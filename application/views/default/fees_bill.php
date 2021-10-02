<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $defaultAcademics;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "My Bill";
$response->title = "{$pageTitle} : {$appName}";

// set the parameters
$param = (object) [
    "userData" => $defaultUser,
    "student_id" => $SITEURL[1] ?? $defaultUser->user_id,
    "clientId" => $defaultUser->client_id,
    "client_data" => $defaultUser->client,
    "academic_year" => $defaultAcademics->academic_year ?? null,
    "academic_term" => $defaultAcademics->academic_term ?? null
];

// create a new object
$feesObject = load_class("fees", "controllers", $param);

$orientation = "P";
$pages_content = $feesObject->bill($param);

$response->html = '
<section class="section">
    <div class="section-header">
        <h1>'.$pageTitle.'</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item">My Bill</div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    '.$pages_content.'
                    <div class="mt-3" align="center">
                        <a title="Click to Download Bill" class="btn btn-outline-success" target="_blank" href="'.$baseUrl.'download/student_bill/'.$param->student_id.'?download=1">
                            <i class="fa fa-download"></i> DOWNLOAD BILL
                        </a>
                        <a title="Click to Print Bill" class="btn btn-outline-primary" target="_blank" href="'.$baseUrl.'download/student_bill/'.$param->student_id.'?print=1">
                            <i class="fa fa-print"></i> PRINT BILL
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>';

echo json_encode($response);