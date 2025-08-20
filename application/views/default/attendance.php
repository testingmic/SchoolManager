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

    $class_attendance = "";
    $attendance_logs_by_daychart = "";

    // student id
    $isSummary = isset($SITEURL[1]) && ($SITEURL[1] == "summary");
    $class_id = $SITEURL[2] ?? null;
    
    // the default data to stream
    $data_stream = $isAdminAccountant ? "attendance_report,class_attendance_report" : "attendance_report";

    // load if the user is an admin or an accountant
    if($isAdminAccountant) {
        $ana_params = (object)[
            "clientId" => $session->clientId,
            "userData" => $defaultUser,
            "class_id" => $class_id,
            "is_summary" => $isSummary,
            "label" => [
                "stream" => "class_attendance_report"
            ]
        ];
        $attendance_summary = load_class("analitics", "controllers", $ana_params)->generate($ana_params);

        // data to loop through
        $attendance = $attendance_summary['attendance_report']['class_summary'] ?? [];
        $class_summary_attendance = $attendance_summary['attendance_report']['class_summary'] ?? [];

        $i = 0;
        foreach($attendance["attendanceRate"] as $className => $each) {
            
            $i++;

            if(empty($each['totalDays'])) continue;

            $class_attendance .= "
            <tr>
                <td class='3%'>{$i}</td>
                <td>{$className}</td>
                <td class='text-center'>{$each['Size']}</td>
                <td class='text-center'>{$each['totalDays']}</td>
                <td class='text-center text-success'>{$each['Present']}</td>
                <td class='text-center text-danger'>{$each['Absent']}</td>
                <td class='text-center text-success'>{$each['presentRate']}%</td>
                <td class='text-center text-warning'>{$each['absentRate']}%</td>
                <td class='text-center'>
                    <button onclick='return loadPage(\"{$baseUrl}attendance/summary/{$each['Id']}\")' class='btn btn-sm p-1 pr-2 pl-2 btn-outline-success'><i class='fas fa-chart-bar'></i> View</button>
                </td>
            </tr>";
        }

        $attendance_logs_by_daychart .= render_attendance_table($class_summary_attendance);

    }
 
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
                admin_summary_cards() : '
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
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 data-attendance_count="logs_count" class="font-light mb-0">0</h3>
                                    <span class="text-black">Logs Counter</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>'
                ).'
                <div class="'.($isAdminAccountant ? 'col-lg-3' : 'col-lg-3').' col-md-3 col-sm-6">
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
                '.($isAdminAccountant ? '
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="col-lg-8 pl-0 col-md-8">
                                <h4 class="text-uppercase font-13 mb-0">Attendance Performance By Class</h4>
                            </div>
                            <div align="right" class="col-lg-4 pr-0 col-md-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                    </div>
                                    <input data-item="attendance_performance" data-maxdate="'.$myClass->data_maxdate.'" value="'.date("Y-m-d", strtotime("first day of this month")).'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" id="group_start_date">
                                    <input data-item="attendance_performance" data-maxdate="'.$myClass->data_maxdate.'" value="'.date("Y-m-d").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" id="group_end_date">
                                    <div class="input-group-append">
                                        <button style="border-radius:0px" onclick="return filter_ClassGroup_Attendance()" class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
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
                            <div class="table-responsive table-student_staff_list">
                                <table class="table table-sm table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="3%">No.</th>
                                            <th>Class Name</th>
                                            <th class="text-center">Class Size</th>
                                            <th class="text-center">Total Days</th>
                                            <th class="text-center">Present</th>
                                            <th class="text-center">Absent</th>
                                            <th class="text-center">Attendance Rate</th>
                                            <th class="text-center">Absent Rate</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody class="class_summary_attendance_rate">'.$class_attendance.'</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
                        <div class="card-header pr-0">
                            <div class="row width-100 flex align-items-lg-center">
                                <div class="col-lg-8 col-md-8">
                                    <h4 class="text-uppercase font-13 mb-0">Attendance Logs by Day</h4>
                                </div>
                                <div align="right" class="col-lg-4  col-md-4">
                                    <div class="input-group">
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
                        </div>
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
                                </div>' : 
                            '<div id="attendance_chart_list"></div>').'
                        </div>
                    </div>
                ' : null).'
                </div>
            </div>
        </section>';
}

echo json_encode($response);