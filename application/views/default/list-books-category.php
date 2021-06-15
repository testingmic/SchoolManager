<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$pageTitle = "Books Category";
$response->title = "{$pageTitle} : {$appName}";

$params = (object) [
    "clientId" => $session->clientId,
    "client_data" => $defaultUser->client,
    "limit" => 9999
];

$item_list = load_class("library", "controllers", $params)->category_list($params);

$hasAdd = $accessObject->hasAccess("add", "library");
$hasDelete = $accessObject->hasAccess("delete", "library");
$hasUpdate = $accessObject->hasAccess("update", "library");

$category_list = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a title='Click to view the book category record' href='#' onclick='return loadPage(\"{$baseUrl}update-book-category/{$each->item_id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to update book category record' href='#' onclick='return loadPage(\"{$baseUrl}update-book-category/{$each->item_id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Book Category' onclick='return delete_record(\"{$each->item_id}\", \"book_category\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $category_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $category_list .= "<td>".($key+1)."</td>";
    $category_list .= "<td><a href='#' onclick='return loadPage(\"{$baseUrl}update-book-category/{$each->item_id}\");'>{$each->name}</a></td>";
    $category_list .= "<td>{$each->description}</td>";
    $category_list .= "<td>{$each->books_count}</td>";
    $category_list .= "<td align='center'>{$action}</td>";
    $category_list .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.' List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.' List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'add-book-category"><i class="fa fa-plus"></i> Add Category</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="20%">Category Name</th>
                                        <th>Description</th>
                                        <th width="15%">Books Count</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$category_list.'</tbody>
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