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
$pageTitle = "Events Management";
$response->title = $pageTitle;
$response->timer = 0;

// end query if the user has no permissions
if(!in_array("events", $clientFeatures)) {
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
    $formsClass = load_class("forms", "controllers");
    
    // set the parameters
    $params = (object) [
        "baseUrl" => $baseUrl,
        "clientId" => $clientId,
        "userId" => $session->userId
    ];

    // load the event types
    $event_types_list = "";

    $hasEventAdd = $accessObject->hasAccess("add", "events");
    $hasEventDelete = $accessObject->hasAccess("delete", "events");
    $hasEventUpdate = $accessObject->hasAccess("update", "events");

    // append the permissions to the default user object
    $defaultUser->hasEventDelete = $hasEventDelete;
    $defaultUser->hasEventUpdate = $hasEventUpdate;

    // load the events types
    if($hasEventAdd) {
        $eventClass = load_class("events", "controllers");
        $event_types = $eventClass->types_list($params);
        $data->event_types = $event_types;
    }

    // load the scripts
    $response->scripts = ["assets/js/events.js", "assets/js/calendar.js"];

    $response->html = '
        <div id="fullCalModal" class="modal fade" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modalTitle1" class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    </div>
                    <div id="modalBody1" class="modal-body"></div>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-calendar-alt"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Events List</div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="fc-overflow">
                                <div class="table-responsive slim-scroll" id="events_management"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        '.($hasEventAdd ? 
        '<div id="createEventModal" class="modal fade" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Event</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    </div>
                    <style>
                    #ajax-data-form-content trix-editor {
                        min-height: 150px;
                        max-height: 150px;
                    }
                    </style>
                    '.$formsClass->event_form($data).'
                </div>
            </div>
        </div>' : null);
}

echo json_encode($response);