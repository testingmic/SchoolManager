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
            "assets/js/comments.js",
            "assets/js/comments_upload.js",
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
                    min-height: 120px;
                    max-height: 120px;
                }
                </style>
                <div class="row mt-sm-4">
                    <div class="'.(isset($data->item_id) ? "col-md-9" : "col-lg-12").'">
                        <div class="card">
                            <div class="card-body p-0">
                                '.$formsClass->event_form($data).'
                            </div>
                        </div>
                    </div>
                    '.(isset($data->item_id) ? '
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div><strong class="text-uppercase">Event Summary Information</strong></div>
                                <div>
                                    <p class="mb-0"><i class="fa fa-user"></i> '.$data->created_by_info->name.'</p>
                                    <p class="mb-0"><i class="fa fa-calendar-check"></i> '.$data->date_created.'</p>
                                </div>
                                <div class="text-left mt-3 mb-3">
                                Click on the button below to upload images with comments for this event.
                                </div>
                                <div class="text-right">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#rightModal_Content">Comments</button>
                                </div>
                            </div>
                        </div>
                    </div>' : "").'

                </div>
            </div>
        </section>';
        if(isset($data->item_id)) {
            $response->html .= '
            <div class="modal fade modal-dialog-right right" id="rightModal_Content" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" style="width:100%;height:100%;" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="p-2 pt-0">
                                <div class="p-2 pt-0">
                                    <div><strong>EVENT COMMENTS</strong></div>
                                    <div>
                                        '.leave_comments_builder("events", $data->item_id, true).'
                                        <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$item_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                        <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary">Loading comments</button></div>    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }

    }

}
// print out the response
echo json_encode($response);
?>