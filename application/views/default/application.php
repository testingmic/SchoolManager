<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $session, $myClass, $isAdminAccountant, $accessObject, $defaultAcademics;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$application_list = "";
$response->title = "Application Form";
$application_id = $SITEURL[1] ?? null;

// end the page if the user is not an admin
if(!$isAdminAccountant || empty($application_id)) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// create a new object
$applicationObj = load_class("applications", "controllers");

// set some parameters
$param = (object) [
    "application_id" => $application_id,
    "clientId" => $clientId
];

// get the list of all applications
$data = $applicationObj->list($param);

// end the page if the user is not an admin
if(empty($data)) {
    $response->html = page_not_found();
    echo json_encode($response);
    exit;
}

// get the class list
$response->scripts = ["assets/js/comments.js"];

// permissions
$hasDelete = $accessObject->hasAccess("delete", "applications");
$hasUpdate = $accessObject->hasAccess("update", "applications");

// get the data
$data = $data["data"][0];
$response->title = "Application - {$data->item_id}";

// load the activity logs
$application_form = load_class("forms", "controllers")->preload_form_data($data, $data->form_answers, false, "user_policy");

// display the form information
$response->html = '
    <section class="section list_Students_By_Class">
        <div class="section-header">
            <h1><i class="fa fa-book-open"></i> '.$response->title.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'applications">Applications List</a></div>
                <div class="breadcrumb-item active">'.$response->title.'</div>
            </div>
        </div>
        <input type="hidden" disabled name="assign_param" value="department">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header pb-0 pl-2 pr-2">APPLICATION INFO</div>
                            <div class="card-body p-2">
                                <div class="mb-2"><span class="text-primary font-bold">ID: </span><strong>'.$data->item_id.'</strong></div>
                                <div class="mb-2"><span class="text-primary font-bold">DATE: </span><strong>'.$data->date_created.'</strong></div>
                                <div class="mb-2"><span class="text-primary font-bold">STATUS: </span>
                                <strong id="the_status_label">'.$myClass->the_status_label($data->state).'</strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header  pl-2 pr-2 pb-0">FORM</div>
                            <div class="card-body p-2">
                                <div class="mb-2"><span class="text-primary font-bold">FORM: </span><strong>'.$data->name.'</strong></div>
                                <div class="mb-2"><span class="text-primary font-bold">FORM ID:</span> <strong>'.$data->form_id.'</strong></div>
                                <div>
                                <select data-width="100%" data-application_id="'.$data->item_id.'" name="application_status" class="selectpicker">
                                    <option value="Pending">Change Application Status</option>
                                    <option '.($data->state == "Pending" ? "selected" : null).' value="Pending">Pending</option>
                                    <option '.($data->state == "Approved" ? "selected" : null).' value="Approved">Approved</option>
                                    <option '.($data->state == "Denied" ? "selected" : null).' value="Denied">Denied</option>
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                '.$application_form.'
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
            <div class="col-12 mb-4 col-sm-12 col-md-6">
                <div class="slim-scroll">
                    <div class="p-0 m-0">
                        '.leave_comments_builder("application", $data->item_id, false).'
                        <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$data->item_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                        <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                    </div>
                </div>
            </div>

        </div>

    </section>';

// print out the response
echo json_encode($response);
?>