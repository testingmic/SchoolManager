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
$pageTitle = "Events Category Management";
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
    
    // load section if the user has the right permissions
    if($hasEventAdd) {
        $eventClass = load_class("events", "controllers");
        // load the events types
        $event_types = $eventClass->types_list($params);
        $data->event_types = $event_types;
        // loop through the list
        foreach($event_types as $type) {
            $event_types_list .= "
            <div class='col-lg-4 col-md-6' data-row_id='{$type->item_id}'>
                <div class='card mb-2'>
                    <div class='card-header p-2 text-uppercase'><strong>{$type->name}</strong></div>
                    ".(!empty($type->description) ? "<div class='card-body p-2'>{$type->description}</div>" : "")."
                    <div class='card-footer p-2'>
                        ".($type->slug !== "public-holiday" ? 
                            "<div class='d-flex justify-content-between'>
                                ".($hasEventUpdate ? "<div><button onclick='return update_Event_Type(\"{$type->item_id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i> Edit</button></div>": "")."
                                ".($hasEventDelete ? "<div><button onclick='return delete_record(\"{$type->item_id}\", \"event_type\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></button></div>" : "")."
                            </div>" : ""
                        )."
                    </div>
                </div>
            </div>";
        }

        // append the questions list to the array to be returned
        $response->array_stream["event_types_array"] = $data->event_types;

    }

    // append the permissions to the default user object
    $defaultUser->hasEventDelete = $hasEventDelete;
    $defaultUser->hasEventUpdate = $hasEventUpdate;

    // load the scripts
    $response->scripts = ["assets/js/events.js"];

    $response->html = '
        '.($hasEventAdd ? '
            <div id="createEventTypeModal" class="modal fade" data-backdrop="static" data-keyboard="false">
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
                                    <label>Color Code</label>
                                    <input type="color" class="form-control" id="color_code" name="color_code">
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
            </div>' : ''
        ).'
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'events">Events List</a></div>
                    <div class="breadcrumb-item">Events Category Manager</div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h5><span><button onclick="return add_Event_Type()" class="btn btn-sm btn-outline-primary"><i class="fa fa-plus"></i> Add New</button></span></h5>
                </div>
                <div class="col-sm-12 col-lg-12">
                    <div class="mt-3" id="events_types_list">
                        <div class="row p-0">
                            '.$event_types_list.'
                        </div>
                    </div>
                </div>
            </div>
        </section>';
}

echo json_encode($response);