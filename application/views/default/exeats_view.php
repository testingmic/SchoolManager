<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $clientFeatures, $isWardParent, $isWardTutorParent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// set some important variables
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);

// end query if the user has no permissions
if(!in_array("exeats", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["front_office"]);
    echo json_encode($response);
    exit;
}

// set the page tile
$pageTitle = "Exeats";
$response->title = $pageTitle;

$clientId = $session->clientId;

// if the user id is not empty
if(!$accessObject->hasAccess("view", "exeats") && !$isWardParent) {
    // parse error message
    $response->html = page_not_found("permission_denied");
} else {

    // set the first key
    $request_id = $SITEURL[1] ?? null;
    $response->scripts = ["assets/js/exeats.js"];

    // init values
    $data = null;
    $loadForm = false;
    $request_data = null;
    $results_list = null;

    // set the application id
    if($request_id === 'log') {
        $loadForm = true;
    }

    // load the leave applications
    $param = (object) [
        "clientId" => $clientId,
        "userData" => $defaultUser,
        "user_id" => $defaultUser->user_id,
        "exeat_id" => !empty($request_id) && strlen($request_id) > 6 ? $request_id : null,
    ];

    // get the reports list
    $exeatClass = load_class("exeats", "controllers");
    $results_array = $exeatClass->list($param)["data"] ?? [];

    // if the request is not apply
    $data = $results_array[0] ?? [];

    // if the user does not have the required permissions
    if(empty($data)) {
        // unset the page additional information
        $response->page_programming = [];
        // permission denied information
        $response->html = page_not_found("not_found");
        echo json_encode($response);
        exit;
    }

    // if the data is not empty
    if(!empty($data)) {

        // uploads script
        $comment_form = null;   
        $response->scripts = ["assets/js/comments.js", "assets/js/exeats.js"];

        // comment form set
        $comment_form = leave_comments_builder("exeats", $request_id, false);
        $manageExeats = $accessObject->hasAccess("manage", "exeats");

        // check if the exeat is overdue
        if(strtotime($data->return_date) < strtotime(date("Y-m-d")) && $data->status == 'Approved') {
            $status = "Overdue";
        } else {
            $status = $data->status;
        }

        // set the application data
        $request_data = '
        <div class="col-md-5">
            <div class="card stick_to_top">
                <div class="card-body">

                    <div class="font-17 text-uppercase mb-2">
                        <i class="fa fa-user"></i> 
                        <span class="user_name">'.$data->student_name.'</span>
                        '.(!empty($data->class_name) ? "<div class='text-xs text-gray-500'>
                            <span class='badge badge-primary'>{$data->class_name}</span>
                        </div>" : null).'
                    </div>
                    <div class="font-14 border-bottom pb-2 mb-2">
                        <i class="fa fa-list"></i> 
                        <span>'.$data->exeat_type.'</span>
                    </div>
                    <div class="font-14 border-bottom pb-2 mb-2">
                        <div><i class="fa fa-calendar-alt"></i> Departure Date: <span class="text-primary">'.$data->departure_date.'</span></div>
                        <div><i class="fa fa-calendar-check"></i> Return Date: <span class="text-primary">'.$data->return_date.'</span></div>
                    </div>
                    '.(!empty($data->guardian_contact) ? '
                    <div class="font-14 border-bottom pb-2 mb-2">
                        <strong>Pickup Information:</strong>
                    </div>
                    <div class="font-14 border-bottom pb-2 mb-2">
                        <div>Pickup By: <span class="text-primary">'.$data->pickup_by.'</span></div>
                        <div>Guardian Contact: <span class="text-primary">'.$data->guardian_contact.'</span></div>
                    </div>' : null).'
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">
                            <strong>Created On:</strong>
                            <span class="text-primary">'.$data->created_at.'</span>
                        </div>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">
                            <strong>Reason</strong>
                        </div>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">'.$data->reason.'</div>
                    </div>
                    '.($accessObject->hasAccess("update", "exeats") && $manageExeats && !in_array($data->status, ["Deleted", "Cancelled", "Rejected"]) ?
                        '<div class="form-group mb-3">
                            <label for="exeats_status"><strong>Exeat Status:</strong></label>
                            <select data-request_url="exeats" data-request_id="'.$data->item_id.'" name="exeats_status" id="exeats_status" class="selectpicker" data-width="100%">
                                '.implode("", array_map(function($each) use($data) {
                                    return "<option value=\"{$each}\" ".($each == $data->status ? "selected" : "").">{$each}</option>";
                                }, array_keys($exeatClass->exeat_statuses))).'
                            </select>
                        </div>' : "<strong>Status: </strong><span class='badge float-right badge-{$exeatClass->exeat_statuses[$status]}'>{$status}</span>"
                    ).'
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="slim-scroll">
                        <div class="p-0 m-0">
                            '.$comment_form.'
                            <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$request_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0"></div>
                            <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // set the application form
    $request_form = $loadForm ? load_class("forms", "controllers")->enquiry_form() : null;

    // set the html data
    $response->html = '
        <section class="section">

            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="'.$baseUrl.'exeats_log">Exeats Log</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>

            <div class="row">

                '.(!empty($data) ? $request_data : null).'

            </div>

        </section>';

}

// print out the response
echo json_encode($response);
?>