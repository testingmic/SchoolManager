<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Attendance Log";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!in_array("attendance", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// if the client information is not empty
if(!empty($clientId)) {
    // convert to lowercase
    $client_id = strtolower($clientId);
    
    // load the scripts
    $response->scripts = ["assets/js/analitics.js"];
    
    // the default data to stream
    $data_stream = $isAdminAccountant ? "attendance_report,class_attendance_report" : "attendance_report";
 
    // set the html text to display
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-clock"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Attendance Log</div>
                </div>
            </div>
            <div class="row default_period" data-current_period="this_month">
            '.($isAdminAccountant ? 
                '<div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user-graduate card-icon col-green"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="Student" class="font-light mb-0">0</h3>
                                    <span class="text-black">Students</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user-tie card-icon col-orange"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="Staff" class="font-light mb-0">0</h3>
                                    <span class="text-black">Teachers, Employees & Users</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>' : '
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user-check card-icon col-green"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="Present" class="font-light mb-0">0</h3>
                                    <span class="text-black">Days Present</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user-alt-slash card-icon col-red"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="Absent" class="font-light mb-0">0</h3>
                                    <span class="text-muted">Days Absent</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user-edit card-icon col-blue"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="logs_count" class="font-light mb-0">0</h3>
                                    <span class="text-muted">Logs Counter</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
                ).'
                <div class="'.($isAdminAccountant ? 'col-lg-4' : 'col-lg-3').' col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-wrap">
                            <div class="padding-20 pt-2 quick_loader pb-1" style="height:100px">
                                <div class="form-content-loader" style="display: flex; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span class="text-black" data-section="chart_summary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-12 col-sm-12" id="data-report_stream" data-report_stream="'.$data_stream.'">
                    <div class="card">
                        <div class="row p-4 pb-0">
                            <div class="col-lg-7 col-md-5">
                                <h4 class="font-20">Attendance Logs by Day</h4>
                            </div>
                            <div class="col-lg-5 col-md-7 text-right">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                    </div>
                                    <input data-item="attendance" data-maxdate="'.$myClass->data_maxdate.'" value="'.date("Y-m-d", strtotime("first day of this month")).'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" id="group_start_date">
                                    <input data-item="attendance" data-maxdate="'.$myClass->data_maxdate.'" value="'.date("Y-m-d").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" id="group_end_date">
                                    <div class="input-group-append">
                                        <button style="border-radius:0px" onclick="return filter_UserGroup_Attendance()" class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body quick_loader" id="users_attendance_loader">
                            <div class="form-content-loader" style="display: flex; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            '.($isAdminAccountant ? '
                                <div data-chart_container="users_attendance_chart">
                                    <div style="width:100%;height:345px;" id="attendance_chart"></div>
                                </div>' : 
                            '<div id="attendance_chart_list"></div>').'
                        </div>
                    </div>
                </div>
                '.($isAdminAccountant ? '
                    <div class="col-lg-12 col-md-12 col-12 col-sm-12 hidden">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="font-20">Attendance Logs by Day</h4>
                            </div>
                            <div class="card-body pb-0">
                                <div data-chart_container="attendance_log_chart">
                                    <div id="attendance_log_chart" style="min-height:350px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                ' : null).'
                '.(
                    $isAdminAccountant ? '
                    <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                        <div class="card">
                            <div class="row p-4 pb-0">
                                <div class="col-lg-8 col-md-6">
                                    <h4 class="font-20">Attendance Logs By Class</h4>
                                </div>
                                <div class="col-lg-4 col-md-6 text-right">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                        </div>
                                        <input type="text" data-maxdate="'.$myClass->data_maxdate.'" class="datepicker form-control" style="border-radius:0px; height:42px;" name="class_date_select" id="class_date_select">
                                        <div class="input-group-append">
                                            <button style="border-radius:0px" onclick="return filter_Class_Attendance()" class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body quick_loader" id="class_attendance_loader">
                                <div class="form-content-loader" style="display: flex; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
                                <div data-chart_container="class_attendance_chart">
                                    <div style="width:100%;height:345px;" id="class_attendance_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ' : ''
                ).'
            </div>
        </section>';
}

echo json_encode($response);