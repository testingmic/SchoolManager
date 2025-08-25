<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $clientFeatures;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$response->title = "Update Book Stock : {$appName}";
$hasAdd = $accessObject->hasAccess("add", "library");

// end query if the user has no permissions
if(!in_array("library", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["library"]);
    echo json_encode($response);
    exit;
}

// confirm if the user has the required permissions
if(!$hasAdd) {
    $response->html = page_not_found("permission_denied");
} else {
    // call the library class
    $response->scripts = ["assets/js/library.js"];

    // get the books list
    $params = (object) [
        "clientId" => $session->clientId,
        "client_data" => $defaultUser->client,
        "minified" => true,
        "limit" => 99999
    ];

    $item_list = load_class("library", "controllers", $params)->list($params);


    $hasDelete = $accessObject->hasAccess("delete", "library");
    $hasUpdate = $accessObject->hasAccess("update", "library");

    $books_listing = "";
    // if the result is an array
    if(is_array($item_list["data"])) {
        // loop through the books list
        foreach($item_list["data"] as $book) {
            $books_listing .= "<option data-books_stock='{$book->books_stock}' value='{$book->item_id}'>{$book->title}</option>";
        }
    }
    
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-book-open"></i> Update Book Stock</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'books">Books List</a></div>
                    <div class="breadcrumb-item">Update Book Stock</div>
                </div>
            </div>
            <div class="books_stock_update">
                

                <div class="row books_content" data-row="1">
                        
                    <div class="col-md-5 mb-3">
                        <div>
                            <label for="book_id_1" class="text-primary">Select Book</label>
                            <select data-row="1" name="book_id_1" id="book_id_1" class="form-control selectpicker">
                                <option value="null">Please Select</option>
                                '.$books_listing.'
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="quantity_1" class="text-primary">Quantity</label>
                        <input data-row="1" type="number" step="1" value="0" class="form-control" min="1" name="quantity_1">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="threshold_1" title="On which stock count do you want to be alerted on shortages" class="text-primary cursor">Threshold</label>
                        <input data-row="1" type="number" step="1" value="0" class="form-control" min="1" name="threshold_1">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="stock_quantity_1" class="text-primary">Available Quantity</label>
                        <input data-row="1" type="number" disabled class="form-control" min="1" name="stock_quantity_1">
                    </div>
                    <div class="col-md-1 text-center">
                        <label class="text-primary">Add</label><br>
                        <button title="Select new Book" type="button" onclick="return append_new_book_row()" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                    </div>
                
                </div>

                <div class="border-top pt-3 text-right">
                    <button class="btn btn-outline-success"><i class="fa fa-save"></i> Update Stock</button>
                </div>


            </div>
        </section>';

    // print out the response
    echo json_encode($response);
}
?>