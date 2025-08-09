<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Exeats Dashboard";
$response->title = $pageTitle;
$response->timer = 0;

// end query if the user has no permissions
if(!in_array("exeats", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

// if the client information is not empty
if(!empty($session->clientId)) {

    // convert to lowercase
    $client_id = strtoupper($session->clientId);

    // create new event class
    $data = (object) [];
    
    // set the parameters
    $params = (object) [
        "baseUrl" => $baseUrl,
        "clientId" => $clientId,
        "userId" => $session->userId
    ];

    $hasExeatAdd = $accessObject->hasAccess("add", "exeats");
    $hasExeatDelete = $accessObject->hasAccess("delete", "exeats");
    $hasExeatUpdate = $accessObject->hasAccess("update", "exeats");

    // append the permissions to the default user object
    $defaultUser->hasExeatDelete = $hasExeatDelete;
    $defaultUser->hasExeatUpdate = $hasExeatUpdate;

    // load the Exeats types
    $exeatClass = load_class("exeats", "controllers");
    $exeat_list = $exeatClass->list($params)['data'] ?? [];

    $response->array_stream['exeat_list'] = $exeat_list;

    // load the scripts
    $response->scripts = ["assets/js/exeats.js"];

    $summaryCards = [
        [
            'Title' => 'Total Requests', 'Key' => 'Total', 'Icon' => 'fa-user-graduate', 'Color' => 'blue', 'Border' => 'primary'
        ],
        [
            'Title' => 'Pending Requests', 'Key' => 'Pending', 'Icon' => 'fa-clock', 'Color' => 'yellow', 'Border' => 'warning'
        ],
        [
            'Title' => 'Approved Requests', 'Key' => 'Approved', 'Icon' => 'fa-check', 'Color' => 'green', 'Border' => 'success'
        ],
        [
            'Title' => 'Overdue Returns', 'Key' => 'Overdue', 'Icon' => 'fa-clock', 'Color' => 'red', 'Border' => 'danger'
        ]
    ];

    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-book"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="row" id="exeats_summary_cards">
                    '.implode("", array_map(function($each) use ($exeat_list) {
                        return "
                        <div class='col-12 col-sm-12 col-md-6 col-lg-3'>
                            <div class='card card-statistic-1 border-top-0 border-bottom-0 border-right-0 border-left-lg border-{$each['Border']} border-left-solid bg-gradient-to-br from-{$each['Color']}-200 to-{$each['Color']}-100'>
                                <div class='flex items-center justify-between p-4'>
                                    <div class='w-12 h-12 bg-gradient-to-br from-{$each['Color']}-600 to-{$each['Color']}-600 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-lg'>
                                        <i class='fas {$each['Icon']} text-white text-xl'></i>
                                    </div>
                                    <div class='card-wrap text-right'>
                                        <h3 data-count='{$each['Key']}' class='text-black mb-0'>0</h3>
                                        <span class='text-dark'>{$each['Title']}</span>
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }, $summaryCards)).'
                        <div class="col-12 col-sm-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table data-empty="" class="table table-md table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="22%">Student Name</th>
                                                    <th>Class Name</th>
                                                    <th>Departure Date</th>
                                                    <th>Return Date</th>
                                                    <th>Exeat Type</th>
                                                    <th>Pickup By</th>
                                                    <th>Gender</th>
                                                </tr>
                                            </thead>
                                            <tbody id="exeat_list_table"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-12 col-md-6 col-12 col-sm-12">
                            <div class="row">
                                '.implode("", array_map(function($each) {
                                    return '
                                    <div class="col-lg-4 col-md-6 col-12 col-sm-12" id="exeat_types">
                                        <div class="card card-statistic-1 border">
                                            <div class="flex items-center justify-between p-4">
                                                <div class="w-12 h-12"></div>
                                                <div class="card-wrap text-right">
                                                    <h3 data-count="'.$each.'" class="text-black mb-0">0</h3>
                                                    <span class="text-dark">'.$each.'</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }, ['Day', 'Weekend', 'Emergency'])).'
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                                    <div class="card rounded-2xl">
                                        <div class="card-header pr-0">
                                            <div class="row width-100">
                                                <div class="col-md-7">
                                                    <h4 class="text-uppercase pb-0 mb-0 font-13">Exeats Trends</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body pb-0">
                                            <div data-chart_container="exeats_chart">
                                                <div id="exeats_chart" style="min-height:420px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12 col-sm-12 hidden">
                                    <div class="row">
                                    '.implode("", array_map(function($each) {
                                        return '
                                        <div class="col-lg-6 col-md-6 col-12 col-sm-12" id="exeat_gender">
                                            <div class="card card-statistic-1 border">
                                                <div class="flex items-center justify-between p-4">
                                                    <div class="w-12 h-12"></div>
                                                    <div class="card-wrap text-right">
                                                        <h3 data-count="'.$each.'" class="text-black mb-0">0</h3>
                                                        <span class="text-dark">'.$each.' Students</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                                    }, ['Male', 'Female'])).'
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-12 col-sm-12"></div>
                                
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>';
}

echo json_encode($response);