<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $clientFeatures, $isAdmin, $isAdminAccountant;

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
    $response->html = page_not_found("permission_denied", ["attendance"]);
    echo json_encode($response);
    exit;
}

// if the client information is not empty
if(!empty($clientId)) {
    // convert to lowercase
    $client_id = strtolower($clientId);
    
    // load the scripts
    $response->scripts = ["assets/js/analitics.js"];

    $class_attendance = "";
    $class_student_attendance = "";
    $attendance_logs_by_daychart = "";

    // student id
    $isSummary = isset($SITEURL[1]) && ($SITEURL[1] == "summary");
    $class_id = $SITEURL[2] ?? 0;
    
    // the default data to stream
    $data_stream = $isAdminAccountant ? "attendance_report,class_attendance_report" : "attendance_report";
    
    $start_date = date("Y-m-d", strtotime("-1 month"));
    $end_date = date("Y-m-d");

    $hasNoRecords = $isWardParent && empty($defaultUser->wards_list);

    // load if the user is an admin or an accountant
    if($isAdminAccountant) {

        // load the attendance summary
        $ana_params = (object)[
            "clientId" => $session->clientId,
            "userData" => $defaultUser,
            "class_id" => $class_id,
            "is_summary" => $isSummary,
            "label" => [
                "start_date" => $start_date,
                "end_date" => $end_date,
                "stream" => "class_attendance_report"
            ]
        ];
        $attendance_summary = load_class("analitics", "controllers", $ana_params)->generate($ana_params);

        // data to loop through
        $attendance = $attendance_summary['attendance_report']['class_summary'] ?? [];
        $class_summary_attendance = $attendance_summary['attendance_report']['class_summary'] ?? [];

        // render the class attendance
        $class_attendance = render_class_attendance($attendance, $class_id, $baseUrl);
        $attendance_logs_by_daychart .= render_attendance_table($class_summary_attendance);

        if(!empty($class_id)) {
            $class_student_attendance = class_student_attendance($attendance_summary['attendance_report']['attendance']->students_dataset, $class_id, $baseUrl);
        }

    }

    $chart_card = $hasNoRecords ? no_record_found("No Records Found", "You do not have any wards assigned to you yet hence unable to view attendance logs.", null, "Class", false, "fas fa-clock") : 
        '<div id="attendance_chart_list"></div>';
 
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
            <div class="row default_period" data-current_period="last_1month">
            '.($isAdminAccountant ? 
                admin_summary_cards() : '
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user-check card-icon col-green"></i>
                        <div class="card-wrap">
                            <div class="stats-card">
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
                            <div class="stats-card">
                                <div class="text-right">
                                    <h3 data-attendance_count="Absent" class="font-light mb-0">0</h3>
                                    <span class="text-black">Days Absent</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-statistic-1">
                        <i class="fas fa-user-edit card-icon col-blue"></i>
                        <div class="card-wrap">
                            <div class="stats-card">
                                <div class="text-right">
                                    <h3 data-attendance_count="logs_count" class="font-light mb-0">0</h3>
                                    <span class="text-black">Logs Counter</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
                ).'
                <div class="'.($isAdminAccountant ? 'col-lg-3' : 'col-lg-3').' col-md-6 col-sm-6">
                    <div class="card">
                        <div class="card-wrap">
                            <div class="pt-2 quick_loader pb-1" style="height:100px">
                                <div class="form-content-loader" style="display: flex; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
                                <div class="text-left stats-cards">
                                    <span class="text-black" data-section="chart_summary"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                '.($isAdminAccountant ? '
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="col-lg-6 pl-0 col-md-5">
                                <h4 class="text-uppercase font-13 mb-0">Attendance Performance By Class</h4>
                            </div>
                            <div align="right" class="col-lg-6 pr-0 col-md-7">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                    </div>
                                    <input data-item="attendance_performance" data-maxdate="'.$myClass->data_maxdate.'" value="'.$start_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" id="group_start_date">
                                    <input data-item="attendance_performance" data-maxdate="'.$myClass->data_maxdate.'" value="'.$end_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" id="group_end_date">
                                    <div class="input-group-append">
                                        <button style="border-radius:0px" onclick="return filter_ClassGroup_Attendance()" class="btn btn-outline-primary"><i class="fa fa-filter"></i> Filter</button>
                                        '.(!empty($class_id) ? '<button class="btn btn-outline-primary" onclick="return loadPage(\''.$baseUrl.'attendance\')">
                                            <i class="fa fa-arrow-left"></i> Go Back</button>' : '<button onclick="return loadPage(\''.$baseUrl.'attendance_history\')" class="btn btn-outline-success"><i class="fa fa-history"></i> Daily Attendance History</button>'
                                        ).'
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body quick_loader" id="class_summary_attendance_loader">
                            <div class="form-content-loader" style="display: flex; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <input type="hidden" name="filtered_class_id" readonly id="filtered_class_id" value="'.$class_id.'">
                            <div class="table-responsive table-student_staff_list">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="3%">No.</th>
                                            <th>Class Name</th>
                                            <th class="text-center text-black">Class Size</th>
                                            <th class="text-center text-black">Total Days</th>
                                            <th class="text-center text-black">Present</th>
                                            <th class="text-center text-black">Absent</th>
                                            <th class="text-center text-black">Attendance Rate</th>
                                            <th class="text-center text-black">Absent Rate</th>
                                            '.(empty($class_id) ? "<th></th>" : null).'
                                        </tr>
                                    </thead>
                                    <tbody class="class_summary_attendance_rate">'.$class_attendance.'</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    '.(!empty($class_id) ? '
                    <div class="card">
                        <div class="card-header">
                            <div class="col-lg-8 pl-0 col-md-8">
                                <h4 class="text-uppercase font-13 mb-0">Attendance Performance By Class</h4>
                            </div>
                        </div>
                        <div class="card-body quick_loader" id="class_summary_attendance_loader">
                            <div class="form-content-loader" style="display: flex; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <div class="table-responsive table-student_staff_list">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="3%">No.</th>
                                            <th>Student Name</th>
                                            <th class="text-center text-black">Total Days</th>
                                            <th class="text-center text-black">Present</th>
                                            <th class="text-center text-black">Absent</th>
                                            <th class="text-center text-black">Attendance Rate</th>
                                            <th class="text-center text-black">Absent Rate</th>
                                            '.(empty($class_id) ? "<th></th>" : null).'
                                        </tr>
                                    </thead>
                                    <tbody class="class_students_attendance_rate">'.$class_student_attendance.'</tbody>
                                </table>
                            </div>
                        </div>
                    </div>' : null).'
                </div>' : null).'
                '.($isAdmin && !$isSummary ? '
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card rounded-2xl">
                        <div class="card-header pr-0">
                            <div class="row width-100">
                                <div class="col-md-4 flex align-items-lg-center">
                                    <h4 class="text-uppercase font-13 mb-0">Student vs Staff Attendance Count</h4>
                                </div>
                                <div align="right" class="col-md-8">
                                    <div class="btn-group" data-filter="quick_attendance_filter" id="quick_attendance_filter" role="group" aria-label="Filter Attendance">
                                        <button type="button" data-stream="attendance_report" data-period="last_week" class="btn btn-info">Last Week</button>
                                        <button type="button" data-stream="attendance_report" data-period="this_week" class="btn btn-info">This Week</button>
                                        <button type="button" data-stream="attendance_report" data-period="this_month" class="btn active btn-info">This Month</button>
                                        <button type="button" data-stream="attendance_report" data-period="last_month" class="btn btn-info">Last Month</button>
                                        <button type="button" data-stream="attendance_report" data-period="last_30days" class="btn btn-info">Last 30 Days</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-0">
                            <div data-chart_container="attendance_chart">
                                <div id="attendance_chart" style="min-height:420px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                ' : null).'
                <div class="col-lg-12 col-md-12 col-12 col-sm-12" id="data-report_stream" data-report_stream="'.$data_stream.'">
                '.((!$isSummary && !$class_id ) || !$isAdminAccountant ? '
                    <div class="card">
                        '.(!$hasNoRecords ?
                        '<div class="card-header pr-0">
                            <div class="row width-100 flex align-items-lg-center">
                                <div class="col-lg-5 col-md-5">
                                    <h4 class="text-uppercase font-13 mb-0">Attendance Logs by Day</h4>
                                </div>
                                <div align="right" class="col-lg-7 col-md-7">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                        </div>
                                        <input data-item="attendance" data-maxdate="'.$myClass->data_maxdate.'" value="'.$start_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" id="group_start_date">
                                        <input data-item="attendance" data-maxdate="'.$myClass->data_maxdate.'" value="'.$end_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" id="group_end_date">
                                        <div class="input-group-append">
                                            <button style="border-radius: 0px; min-height: 40px" onclick="return filter_UserGroup_Attendance()" class="btn btn-outline-primary"><i class="fa fa-filter"></i> Filter</button>
                                            <button onclick="return loadPage(\''.$baseUrl.'attendance_history\')" class="btn btn-success"><i class="fa fa-history"></i> View Attendance History</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>' : null).'
                        <div class="card-body quick_loader" id="users_attendance_loader">
                            <div class="form-content-loader" style="display: flex; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            '.($isAdminAccountant ? '
                                <div data-chart_container="users_attendance_chart">
                                    <div class="table-responsive w-100" data-chart_container="attendance_logs_by_daychart">
                                        <table class="table table-sm table-striped table-bordered" id="attendance_logs_by_daychart">
                                            '.$attendance_logs_by_daychart.'
                                        </table>
                                    </div>
                                </div>' : $chart_card).'
                        </div>
                    </div>
                ' : null).'
                </div>
            </div>
        </section>';
}

echo json_encode($response);