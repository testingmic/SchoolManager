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

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Attendance Log";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/booking_log.js"];

// get the data
$data = null;
$booking_id = $SITEURL[1] ?? null;

// get the record
if(!empty($booking_id)) {
    $params = (object) [
        "clientId" => $clientId,
        "booking_id" => $booking_id
    ];
    $data = load_class("booking", "controllers", $params)->list($params)["data"];

    // if the data is not empty
    if(!empty($data)) {
        $data = $data[0];
    }
}

// set the parameters
$params = (object) [
    "data" => $data,
    "booking_id" => $booking_id
];

$the_form = load_class("forms", "controllers")->booking_form($params);

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'booking_list">Booking Logs</a></div>
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
// print out the response
echo json_encode($response);
?>