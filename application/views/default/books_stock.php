<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Update Book Stock ";
$hasAdd = $accessObject->hasAccess("add", "library");

// confirm if the user has the required permissions
if(!$hasAdd) {
    $response->html = page_not_found("permission_denied");
} else {
    // get the books list
    $params = (object) [
        "clientId" => $session->clientId,
        "client_data" => $defaultUser->client,
        "minified" => true
    ];

    // call the library class
    $libraryObj = load_class("library", "controllers", $params);
    $response->scripts = ["assets/js/library.js"];

    // if the user has the permission to add a new item
    $isAddURL = (bool) confirm_url_id(1, "add");
    $hasDelete = $accessObject->hasAccess("delete", "library");
    $hasUpdate = $accessObject->hasAccess("update", "library");

    // begin the page html content
    $response->html = '
    <section class="section">
    <div class="section-header">
        <h1><i class="fa fa-book-open"></i> '.($isAddURL ? 'Update Book Stock' : 'Stock Update History').'</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'books">Books List</a></div>
            '.($isAddURL ? '<div class="breadcrumb-item active"><a href="'.$baseUrl.'books_stock/list">Stock Update History</a></div>' : '').'
            <div class="breadcrumb-item">'.($isAddURL ? 'Update Book Stock' : 'Stock Update History').'</div>
        </div>
    </div>';

    // get the url parsed
    if(!$isAddURL) {

        // initial count
        $count = 0;
        $stock_update_history = "";

        // get the list
        $stocks_list = $libraryObj->stock_update_list($params);

        // if the record is not empty
        if(!empty($stocks_list["data"]) && is_array($stocks_list["data"])) {

            // loop through the record
            foreach($stocks_list["data"] as $stock) {
                $count++;

                // format the books list
                $books_list = "";

                foreach($stock->books_list as $book) {
                    $books_list .= "
                    <div>
                        <p class='mb-0'>
                            <strong>Book Title:</strong>
                            <a href='{$baseUrl}book/{$book["stock"]->book_id}'>
                                <strong>{$book["data"]->title}</strong> (ISBN: {$book["data"]->isbn})
                            </a> 
                            <strong>Quantity:</strong> {$book["stock"]->quantity}
                        </p>
                    </div>";
                }

                // append to the query list
                $stock_update_history .= "
                <tr>
                    <td>{$count}</td>
                    <td>{$stock->date_created}</td>
                    <td>{$books_list}</td>
                    <td></td>
                    <td>".($stock->reversed ? "<span class='badge badge-danger'>Reversed</span>" : "<span class='badge badge-success'>Active</span>")."</td>
                    <td></td>
                </tr>";
            }

        }

        // get the list of books to add to stock
        $response->html .= '
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-outline-success" href="'.$baseUrl.'books_stock/add"><i class="fa fa-book"></i> Update Books Stock</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="15%">Date</th>
                                        <th>Books List</th>
                                        <th width="15%">Created By</th>
                                        <th width="10%">Status</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$stock_update_history.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

    }

    // if the user wants to add the stocks
    elseif($isAddURL) {

        // get the books list
        $item_list = $libraryObj->list($params);

        $books_listing = "";
        // if the result is an array
        if(is_array($item_list["data"])) {
            // loop through the books list
            foreach($item_list["data"] as $book) {
                $books_listing .= "<option data-book_title='{$book->title}' data-books_stock='{$book->books_stock}' value='{$book->item_id}'>{$book->title}</option>";
            }
        }
        
        $response->html .= '
        <div class="mt-4">
            <div class="books_stock_update">

                <div class="row books_content" data-row="1">
                        
                    <div class="col-md-5 mb-3">
                        <div>
                            <label for="book_id_1" class="text-primary">Select Book</label>
                            <select data-row="1" name="book_id_1" id="book_id_1" class="form-control selectpicker">
                                <option value="">Please Select</option>
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
                        <input data-row="1" name="book_title_1" type="hidden">
                        <input data-row="1" type="number" disabled class="form-control" min="1" name="stock_quantity_1">
                    </div>
                    <div class="col-md-1 text-center">
                        <label class="text-primary">Add</label><br>
                        <button title="Select new Book" type="button" onclick="return append_new_book_row()" class="btn btn-primary"><i class="fa fa-plus"></i></button>
                    </div>
                
                </div>

                <div class="border-top pt-3 text-right">
                    <button onclick="return reset_book_stock()" class="btn btn-outline-danger">Cancel</button>
                    <button onclick="return update_book_stock()" class="btn btn-outline-success"><i class="fa fa-save"></i> Update Stock</button>
                </div>

            </div>
        </div>';

    }

    $response->html .= '</section>';

}
// print out the response
echo json_encode($response);
?>