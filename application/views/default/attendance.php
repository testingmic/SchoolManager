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
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item">Attendance Log</div>
                </div>
            </div>
            <div class="row">
            '.($isAdminAccountant ? 
                '<div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-hiking card-icon col-green"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="student_count" class="font-light mb-0">0</h3>
                                    <span class="text-muted">Students</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user card-icon col-orange"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="teacher_count" class="font-light mb-0">0</h3>
                                    <span class="text-muted">Teachers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-users card-icon col-cyan"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="all_employees" class="font-light mb-0">0</h3>
                                    <span class="text-muted">Employees</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>' : '
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user card-icon col-orange"></i>
                        <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="Present" class="font-light mb-0">0</h3>
                                    <span class="text-muted">Days Present</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user card-icon col-red"></i>
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
                        <i class="fas fa-user card-icon col-blue"></i>
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
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-wrap">
                            <div class="padding-20 pt-2 quick_loader pb-1" style="height:100px">
                                <div class="form-content-loader" style="display: flex; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span class="text-muted" data-section="chart_summary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                '.(
                    $isAdminAccountant ? '
                    <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between" style="width:100%">
                                    <div><h4>Class Attendance</h4></div>
                                    <div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                            </div>
                                            <input type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="class_date_select" id="class_date_select">
                                            <div class="input-group-append">
                                                <button style="border-radius:0px" onclick="return filter_Class_Calender()" class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                                            </div>
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
                <div class="col-lg-12 col-md-12 col-12 col-sm-12" id="data-report_stream" data-report_stream="'.$data_stream.'">
                    <div class="card">
                        <div class="card-header"><h4>Attendance Record</h4></div>
                        <div class="card-body quick_loader">
                            <div class="form-content-loader" style="display: flex; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            '.($isAdminAccountant ? '<div style="width:100%;height:345px;" id="attendance_chart"></div>' : 
                            '<div id="attendance_chart_list"></div>').'
                        </div>
                    </div>
                </div>

            </div>
        </section>';
}

echo json_encode($response);