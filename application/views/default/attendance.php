<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Attendance Log";
$response->title = "{$pageTitle} : {$appName}";

// if the client information is not empty
if(!empty($session->clientId)) {
    // convert to lowercase
    $client_id = strtolower($session->clientId);
    
    // set the parameters
    $params = (object) [
        "baseUrl" => $baseUrl,
        "clientId" => $clientId,
        "userId" => $session->userId
    ];

    // generate a new script for this client
    $filename = "assets/js/scripts/{$client_id}_attendance.js";
    
    // get the data
    $data = load_class("scripts", "controllers")->attendance($params);
    
    // create a new file handler
    $file = fopen($filename, "w");
    
    // write the content to the file
    fwrite($file, $data);
    
    // close the opened file
    fclose($file);

    // load the scripts
    $response->scripts = [
        "assets/js/scripts/{$client_id}_attendance.js"
    ];

    $response->html = '
        <div class="modal globalModalAction fade" id="pickCalendarActionModal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-md" style="width:100%;height:100%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <input hidden class="ajax-form-loaded" value="0" data-form="none">
                    <div class="modal-body"></div>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item">Attendance Log</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="fc-overflow">
                                <div id="attendance_calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';
    // print out the response
}

echo json_encode($response);