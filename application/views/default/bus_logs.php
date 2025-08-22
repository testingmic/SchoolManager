<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $clientFeatures, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Bus Attendance";

// end query if the user has no permissions
if(!in_array("attendance", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

$params = (object) [
    "clientId" => $session->clientId,
    "client_data" => $defaultUser->client
];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-bus"></i> Bus Attendance Logs</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Bus Attendance</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                    <a class="btn btn-outline-success" href="'.$baseUrl.'bus_logs/add"><i class="fa fa-bus"></i> Log Attendance</a>
                    <a class="btn btn-primary anchor" href="'.$baseUrl.'qr_code?request=bus&client='.$session->clientId.'"><i class="fa fa-qrcode"></i> Scan QR Code</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Bus Information</th>
                                        <th>Driver</th>
                                        <th>Date & Time</th>
                                        <th>Teachers Count</th>
                                        <th>Students Count</th>
                                        <th>Details</th>
                                        <th align="center" width="13%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$books_list.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>