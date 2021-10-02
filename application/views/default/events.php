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
$pageTitle = "Events Management";
$response->title = "{$pageTitle} : {$appName}";
$response->timer = 0;

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
    $response->scripts = ["assets/js/events.js"];

    if(file_exists("assets/js/scripts/{$client_id}_{$defaultUser->user_type}_events.js")) {
        $response->scripts[] = "assets/js/scripts/{$client_id}_{$defaultUser->user_type}_events.js";
    }

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
                <h1>'.$pageTitle.'</h1>
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