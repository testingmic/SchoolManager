<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $isPayableStaff;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Add Staff";
$response->title = $pageTitle;

// If the user is not a teacher, employee, accountant or admin then end the request
if(!$isPayableStaff) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// execute the client limit query
$myClass->clients_accounts_limit($clientId);

// check if the page limit has reached
if($myClass->accountLimit->staff) {
    $response->html = notification_modal("Student Limit Reached", $myClass->error_logs["student_limit"]["msg"], $myClass->error_logs["student_limit"]["link"]);
} else {

    // include the scripts to load for the page
    $response->scripts = ["assets/js/index.js"];

    $the_form = load_class("forms", "controllers")->staff_form($clientId, $baseUrl);

    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-user-shield"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'staffs">Staff</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">'.$the_form.'</div>
                </div>
            </div>
        </div>
    </section>';
}
// print out the response
echo json_encode($response);
?>