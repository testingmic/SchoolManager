<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Bulk SMS and Email";
$response->title = "{$pageTitle} : {$appName}";

// add the scripts to load
$response->scripts = ["assets/js/communication.js"];

// set the parameters
$params = (object) [
    "clientId" => $clientId
];

// confirm that the user has the required permissions
$the_form = load_class("forms", "controllers")->smsemail_form($params);

$response->array_stream["templates_array"] = $the_form["templates_array"];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="padding-20">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="send_sms-tab2" data-toggle="tab" href="#send_sms" role="tab" aria-selected="true"><i class="fa fa-comment"></i> Send SMS</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="send_email-tab2" data-toggle="tab" href="#send_email" role="tab" aria-selected="true"><i class="fa fa-envelope"></i> Send Email</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="send_sms" role="tabpanel" aria-labelledby="send_sms-tab2">
                                    '.$the_form["sms"].'
                                </div>
                                <div class="tab-pane fade" id="send_email" role="tabpanel" aria-labelledby="send_email-tab2">
                                    '.$the_form["email"].'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
    
// print out the response
echo json_encode($response);
?>