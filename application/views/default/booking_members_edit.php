<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);


$response = (object) [];
$filter = (object) $_POST;

$clientId = $session->clientId;
$pageTitle = "Add Member";
$response->scripts = ["assets/js/booking_log.js"];

// student id
$user_id = $SITEURL[1] ?? null;

$params = (object) [
    "limit" => 1,
    "load" => true,
    "clientId" => $clientId,
    "member_id" => $user_id,
    "client_data" => $defaultUser->client
];

// make a request for the logs analitics
$bookingObj = load_class("booking", "controllers");
$item_list = !empty($user_id) ? $bookingObj->list_members($params)["data"]["list"] : null;

$data = "";
if(!empty($item_list)) {
    $data = $item_list[$user_id];
    $pageTitle = "Update Member";
}

// set the parameters
$params = (object) ["data" => $data, "clientId" => $clientId];

$the_form = load_class("forms", "controllers")->members_form($params);
$response->title = "{$pageTitle} : {$appName}";

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'booking_members">Members List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        '.$the_form.'
                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>