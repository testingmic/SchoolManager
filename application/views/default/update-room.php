<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Update Class Room";
$response->title = $pageTitle;
$response->scripts = [
    "assets/js/index.js"
];

$hasAdd = $accessObject->hasAccess("add", "class");
$hasDelete = $accessObject->hasAccess("delete", "class");
$hasUpdate = $accessObject->hasAccess("update", "class");

$item_id = $SITEURL[1] ?? null;

// set the parent menu
$response->parent_menu = "timetable";

// ensure the the id has been parsed
if(empty($item_id) || !$hasUpdate) {
    $response->html = page_not_found();
} else {

    $params = (object)[
        "room_id" => $item_id,
        "clientId" => $clientId
    ];
    $data = load_class("rooms", "controllers")->list($params);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $_data = $data["data"][0];
        $params = $_data;
        $params->clientId = $clientId;

        $the_form = load_class("forms", "controllers")->class_room_form($params);

        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-rooms">Class Rooms List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">'.$the_form.'</div>
                    </div>
                </div>
            </div>
        </section>';

    }
}
// print out the response
echo json_encode($response);
?>