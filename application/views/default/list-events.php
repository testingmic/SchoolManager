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

// if the client information is not empty
if(!empty($session->clientId)) {

    // convert to lowercase
    $client_id = strtolower($session->clientId);

    // create new event class
    $data = (object) [];
    $eventClass = load_class("events", "controllers");
    $formsClass = load_class("forms", "controllers");

    // set the parameters
    $params = (object) [
        "baseUrl" => $baseUrl,
        "clientId" => $clientId,
        "userId" => $session->userId
    ];

    // load the event types
    $event_types_list = "";
    $event_types_array = [];
    $event_types = $eventClass->types_list($params);

    $accessObject->userId = $session->userId;
    $accessObject->clientId = $session->clientId;
    $accessObject->userPermits = $defaultUser->user_permissions;
    $hasDelete = $accessObject->hasAccess("delete", "events");
    $hasUpdate = $accessObject->hasAccess("update", "events");


    // loop through the list
    foreach($event_types as $type) {
        $event_types_array[$type->item_id] = $type;
        $event_types_list .= "
            <div class='card mb-2'>
                <div class='card-header p-2 text-uppercase'>{$type->name}</div>
                ".(!empty($type->description) ? "<div class='card-body p-2'>{$type->description}</div>" : "")."
                <div class='card-footer p-2'>
                    <div class='d-flex justify-content-between'>
                        ".($hasUpdate ? "<div><button onclick='return update_Event_Type(\"{$type->item_id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i> Edit</button></div>": "")."
                        ".($hasDelete ? "<div><a href='#' onclick='return delete_record(\"{$type->item_id}\", \"event_type\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a></div>" : "")."
                    </div>
                </div>
            </div>";
    }

    // append the permissions to the default user object
    $defaultUser->hasDelete = $hasDelete;
    $defaultUser->hasUpdate = $hasUpdate;

    $params = (object) [
        "container" => "events_management",
        "events_list" => $eventClass->events_list($defaultUser),
        "event_Sources" => "birthdayEvents,holidayEvents,calendarEvents"
    ];

    // append the questions list to the array to be returned
    $response->array_stream["event_types_array"] = $event_types_array;
    
    // generate a new script for this client
    $filename = "assets/js/scripts/{$client_id}_{$defaultUser->user_type}_events.js";
    $data = load_class("scripts", "controllers")->attendance($params);
    $file = fopen($filename, "w");
    fwrite($file, $data);
    fclose($file);

    // load the scripts
    $response->scripts = [
        "assets/js/scripts/{$client_id}_{$defaultUser->user_type}_events.js",
        "assets/js/events.js",
    ];

    $response->html = '
        <div id="fullCalModal" class="modal fade">
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
        <div id="createEventModal" class="modal fade" data-backdrop="static" data-keyboard="false">
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
        </div>
        <div id="createEventTypeModal" class="modal fade">
            <div class="modal-dialog modal-dialog-top">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modalTitle2" class="modal-title">Add Event Type</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    </div>
                    <div id="modalBody2" class="modal-body">
                        <form>
                            <div class="form-group">
                                <label>Event Type Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="name">
                                <input type="hidden" class="form-control" id="type_id" hidden name="type_id">
                            </div>
                            <div class="form-group">
                                <label for="formGroupExampleInput2">Description</label>
                                <textarea id="description" name="description" class="form-control"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button onclick="return save_Event_Type()" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </div>        
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item">Attendance Log</div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="fc-overflow">
                                <div id="events_management"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-3">
                    <h5>EVENT TYPES <span class="float-right"><button onclick="return add_Event_Type()" class="btn btn-sm btn-outline-primary"><i class="fa fa-plus"></i> Add New</button></span></h5>
                    <div class="mt-3" id="events_types_list">'.$event_types_list.'</div>
                </div>
            </div>
        </section>';
}

echo json_encode($response);