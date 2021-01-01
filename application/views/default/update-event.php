<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Event Details";
$response->title = "{$pageTitle} : {$appName}";

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

// item id
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "userData" => $defaultUser,
        "clientId" => $clientId,
        "event_id" => $item_id,
        "userId" => $session->userId,
        "limit" => 1
    ];

    // create a new object
    $eventClass = load_class("events", "controllers");

    // get the event details
    $data = $eventClass->list($item_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // load the scripts
        $response->scripts = [
            "assets/js/events.js",
        ];

        // set the first key
        $data = $data["data"][0];
        $event_types = $eventClass->types_list($item_param);
        $data->event_types = $event_types;

        $formsClass = load_class("forms", "controllers");
        
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-events">Events List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
                <style>
                #ajax-data-form-content trix-editor {
                    min-height: 150px;
                    max-height: 150px;
                }
                </style>
                <div class="row mt-sm-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                '.$formsClass->event_form($data).'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';

    }

}
// print out the response
echo json_encode($response);
?>