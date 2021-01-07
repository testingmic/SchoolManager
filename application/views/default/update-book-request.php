<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Summary Requestion Information";

// access permissions    
$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;
$hasIssue = $accessObject->hasAccess("issue", "library");

$tTitle = $hasIssue ? "Issued Books List" : "My Books List";

$response->title = "{$pageTitle} : {$appName}";
// item id
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

// if the user id is not empty
if(!empty($item_id)) {

    // parameters for the category
    $params = (object) ["clientId" => $session->clientId, "show_list" => true, "borrowed_id" => $item_id, "limit" => 1, "userData" => $defaultUser];
    $data = load_class("library", "controllers")->issued_request_list($params);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {
        
        // set the first key
        $data = $data["data"][0];
        $item_param = $data;
        
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'issued-books">'.$tTitle.'</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row" id="library_form">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>USER INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-3 pt-0">
                                <div class="d-flex justify-content-start">
                                    <div class="mr-2">
                                        <img src="'.$baseUrl.''.$data->user_info->image.'" width="60px">
                                    </div>
                                    <div style="width:100%">
                                        <p class="clearfix">
                                            <span class="float-left">Fullname:</span>
                                            <span class="float-right text-muted">'.($data->user_info->name).'</span>
                                        </p>
                                        <p class="clearfix">
                                            <span class="float-left">Unique ID:</span>
                                            <span class="float-right text-muted">'.($data->user_info->unique_id).'</span>
                                        </p>
                                        <p class="clearfix">
                                            <span class="float-left">Contact:</span>
                                            <span class="float-right text-muted">'.($data->user_info->phone_number).'</span>
                                        </p>
                                        <p class="clearfix">
                                            <span class="float-left">Email:</span>
                                            <span class="float-right text-muted">'.($data->user_info->email).'</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>REQUEST DETAILS</h4>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-3 pt-0">
                                <p class="clearfix">
                                    <span class="float-left">Issued Date:</span>
                                    <span class="float-right text-muted">'.($data->issued_date ?? null).'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Return Date:</span>
                                    <span class="float-right text-muted">'.($data->return_date ?? null).'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Overdue Fine:</span>
                                    <span class="float-right text-muted">'.($data->fine ?? null).'</span>
                                </p>
                                '.( 
                                    $data->state == "Overdue" ? '
                                        <p class="clearfix">
                                            <span class="float-left">Fine Paid:</span>
                                            <span class="float-right text-muted">'.($data->actual_paid ?? null).'</span>
                                        </p>
                                    ' : ''
                                ).'
                                <p class="clearfix">
                                    <span class="float-left">Current State:</span>
                                    <span class="float-right text-muted">'.$myClass->the_status_label($data->state).'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Date:</span>
                                    <span class="float-right text-muted">'.($data->updated_at ?? null).'</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-12 col-lg-8">

                </div>
            </div>
        </section>';

    }


}
// print out the response
echo json_encode($response);