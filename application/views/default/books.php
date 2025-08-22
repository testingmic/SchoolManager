<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $clientFeatures, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Books List ";

// end query if the user has no permissions
if(!in_array("library", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

$params = (object) [
    "clientId" => $session->clientId,
    "client_data" => $defaultUser->client
];

$item_list = load_class("library", "controllers", $params)->list($params);

$hasAdd = $accessObject->hasAccess("add", "library");
$hasDelete = $accessObject->hasAccess("delete", "library");
$hasUpdate = $accessObject->hasAccess("update", "library");

$books_list = "";
foreach($item_list["data"] as $key => $each) {
    $action = "<a title='View this book record' href='#' onclick='return load(\"book/{$each->item_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update this book record' href='#' onclick='return load(\"book/{$each->item_id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Delete this Book' onclick='return delete_record(\"{$each->item_id}\", \"book\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $books_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $books_list .= "<td>".($key+1)."</td>";
    $books_list .= "<td>
    <div class='flex items-center space-x-4'>
        ".(!empty($each->book_image) ? "<img src='{$baseUrl}{$each->book_image}' width='50px' height='40px'>" : "")."
        <div>
            <span title='View Details' class='user_name' onclick='load(\"book/{$each->item_id}\");'>{$each->title}</span><br>
            {$each->code}
        </div>
    </div>
    </td>";
    $books_list .= "<td>{$each->author}</td>";
    $books_list .= "<td>{$each->books_stock}</td>";
    $books_list .= "<td><a href='#' class='text-primary' onclick='return load(\"book_category/{$each->category_item_id}\");'>".($each->category_name ?? null)."</a></td>";
    $books_list .= "<td>".($each->isbn ?? null)."</td>";
    $books_list .= "<td>".($defaultCurrency ?? null).' '.number_format($each->price ?? 0, 2)."</td>";
    $books_list .= "<td align='center'>{$action}</td>";
    $books_list .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-book-open"></i> Books List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Books List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-outline-success" href="'.$baseUrl.'books_stock/add"><i class="fa fa-book"></i> Update Books Stock</a>
                        <a class="btn btn-outline-primary" href="'.$baseUrl.'book_add"><i class="fa fa-plus"></i> Add Book</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Book Title</th>
                                        <th>Author</th>
                                        <th width="13%">Stock Quantity</th>
                                        <th>Collection</th>
                                        <th>ISBN</th>
                                        <th>Price</th>
                                        <th align="center" width="13%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$books_list.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>