<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Exeats Dashboard";
$response->title = $pageTitle;
$response->timer = 0;

// end query if the user has no permissions
if(!in_array("exeats", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

// if the client information is not empty
if(!empty($session->clientId)) {

    // convert to lowercase
    $client_id = strtoupper($session->clientId);

    // create new event class
    $data = (object) [];
    
    // set the parameters
    $params = (object) [
        "baseUrl" => $baseUrl,
        "clientId" => $clientId,
        "userId" => $session->userId
    ];

    $hasExeatAdd = $accessObject->hasAccess("add", "exeats");
    $hasExeatDelete = $accessObject->hasAccess("delete", "exeats");
    $hasExeatUpdate = $accessObject->hasAccess("update", "exeats");

    // append the permissions to the default user object
    $defaultUser->hasExeatDelete = $hasExeatDelete;
    $defaultUser->hasExeatUpdate = $hasExeatUpdate;

    // load the Exeats types
    $exeatClass = load_class("exeats", "controllers");
    $exeat_list = $exeatClass->list($params)['data'] ?? [];

    $response->array_stream['exeat_list'] = $exeat_list;

    // load the scripts
    $response->scripts = ["assets/js/exeats.js"];

    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-book"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body"></div>
                </div>
            </div>
        </div>
    </section>';
}

echo json_encode($response);