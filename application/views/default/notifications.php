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
$pageTitle = "Notifications";
$response->title = $pageTitle;

$params = (object) [
    "clientId" => $session->clientId,
    "user_id" => $session->userId,
    "limit" => $myClass->global_limit
];

$item_list = load_class("notification", "controllers")->list($params);

$hasAdd = $accessObject->hasAccess("add", "library");
$hasDelete = $accessObject->hasAccess("delete", "library");
$hasUpdate = $accessObject->hasAccess("update", "library");

$count = 0;
$notifications_list = "";
foreach($item_list["data"] as $key => $each) {

    $count++;
    $each->content = !empty($each->content) ? $each->content : $each->message;
    $notifications_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $notifications_list .= "<td>".($count)."</td>";
    $notifications_list .= "<td>{$each->subject}</td>";
    $notifications_list .= "<td>{$each->content}</td>";
    $notifications_list .= "<td>{$each->date_created}</td>";
    $notifications_list .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-bell"></i> '.$pageTitle.' List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.' List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="25%">Subject</th>
                                        <th>Content</th>
                                        <th width="15%">Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>'.$notifications_list.'</tbody>
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