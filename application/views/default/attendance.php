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
// jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Attendance Log";
$response->title = "{$pageTitle} : {$appName}";

// if the client information is not empty
if(!empty($clientId)) {
    // convert to lowercase
    $client_id = strtolower($clientId);
    
    // load the scripts
    $response->scripts = [
        "assets/js/attendance_log.js"
    ];

    // get the attendance log for the day
    $days = [
        "today" => date("Y-m-d"),
        "yesterday" => date("Y-m-d", strtotime("yesterday"))
    ];
    $users = ["student", "teacher", "admin"];

    // users counter
    $users_count = [];

    // attendance log algo
    // loop through the days for the record
    foreach($days as $key => $day) {
        // loop through the users for each day
        foreach($users as $user) {
            // set a parameter for the user_type
            $user_type = ($user == "admin") ? "('admin','accountant','employee')" : "('{$user}')";
            
            // run a query for the information
            $theQuery = $myClass->pushQuery("users_list", "users_attendance_log", "log_date='{$day}' AND user_type IN {$user_type} AND client_id='{$clientId}'");
            
            // if the query is not empty
            if(!empty($theQuery)) {
                // convert the users list into an array
                $present = json_decode($theQuery[0]->users_list, true);
                $users_count[$key][$user] = isset($users_count[$key][$user]) ? ($users_count[$key][$user] + count($present)) : count($present);
            }
        }
    }

    // set the html text to display
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item">Attendance Log</div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <i class="fas fa-hiking card-icon col-green"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> 0
                                    </h3>
                                    <span class="text-muted">Students</span>
                                </div>
                                <div class="mb-0 text-right text-muted text-sm">
                                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 0%</span>
                                    <span class="text-nowrap">Since Yesterday</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user card-icon col-orange"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> 0
                                    </h3>
                                    <span class="text-muted">Teachers</span>
                                </div>
                                <div class="mb-0 text-right text-muted text-sm">
                                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 0%</span>
                                    <span class="text-nowrap">Since Yesterday</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <i class="fas fa-users card-icon col-cyan"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> 0
                                    </h3>
                                    <span class="text-muted">Employees</span>
                                </div>
                                <div class="mb-0 text-right text-muted text-sm">
                                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 0%</span>
                                    <span class="text-nowrap">Since Yesterday</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <i class="fas fa-chart-line card-icon col-orange"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">
                                        <i class="ti-arrow-up text-success"></i> 0
                                    </h3>
                                    <span class="text-muted">All Logs</span>
                                </div>
                                <div class="mb-0 text-right text-muted text-sm">
                                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 0%</span>
                                    <span class="text-nowrap">Since Yesterday</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header"><h4>Attendance Record</h4></div>
                        <div class="card-body">
                            <div id="attendance_chart"></div>
                        </div>
                    </div>
                </div>

            </div>
        </section>';
}

echo json_encode($response);