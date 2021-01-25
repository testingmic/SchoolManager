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
$response->title = "Books List : {$appName}";

$department_param = (object) [
    "clientId" => $session->clientId,
    "limit" => 99999
];

$item_list = load_class("library", "controllers")->list($department_param);

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

$hasAdd = $accessObject->hasAccess("add", "library");
$hasDelete = $accessObject->hasAccess("delete", "library");
$hasUpdate = $accessObject->hasAccess("update", "library");

$books_list = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a href='{$baseUrl}update-book/{$each->item_id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a href='{$baseUrl}update-book/{$each->item_id}/update' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->item_id}\", \"book\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $books_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $books_list .= "<td>".($key+1)."</td>";
    $books_list .= "<td><a href='{$baseUrl}update-book/{$each->item_id}'>".(!empty($each->book_image) ? "<img src='{$baseUrl}{$each->book_image}' width='50px' height='40px'>" : "")." {$each->title}</a></td>";
    $books_list .= "<td>{$each->author}</td>";
    $books_list .= "<td>{$each->books_stock}</td>";
    $books_list .= "<td><span class='underline'>".($each->category_name ?? null)."</span></td>";
    $books_list .= "<td><span class='underline'>".($each->isbn ?? null)."</span></td>";
    $books_list .= "<td align='center'>{$action}</td>";
    $books_list .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Books List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item">Books List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'add-book"><i class="fa fa-plus"></i> Add Book</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Book Title</th>
                                        <th>Author</th>
                                        <th width="13%">Stock Quantity</th>
                                        <th>Category</th>
                                        <th>ISBN</th>
                                        <th align="center" width="10%"></th>
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