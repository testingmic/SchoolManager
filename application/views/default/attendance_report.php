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
$pageTitle = "Attendance Report";
$response->title = $pageTitle;

// end query if the user has no permissions
if($isWardParent || !in_array("attendance", $clientFeatures)) {
    // unset the page additional information
    $response->page_programming = [];
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// permissive users to be created by each access level
$permissions = [
    "teacher" => [
        "student" => "Students"
    ],
    "accountant" => [
        "student" => "Student",
        "staff" => "Employees / Users",
    ],
    "admin" => [
        "student" => "Students",
        "staff" => "Employees / Users",
    ]
];

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "a.id, a.name"
];
// if the class_id is not empty
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

// append the script to load
$response->scripts = ["assets/js/attendance.js"];

// set the html text to display
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-chart-line"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'attendance">Attendance Log</a></div>
                <div class="breadcrumb-item">Report</div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row" id="attendance_report">
                            <div class="col-lg-2 col-md-3">
                                <div class="form-group">
                                    <label>Select Category</label>
                                    <select data-width="100%" class="form-control selectpicker" name="user_type" id="user_type">
                                        <option value="">Please select group</option>';
                                        foreach($permissions[$defaultUser->user_type] as $key => $value) {
                                            $response->html .= "<option value=\"{$key}\">".strtoupper($value)."</option>";
                                        }
                                    $response->html .= '</select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 hidden" id="classes_list">
                                <div class="form-group">
                                    <label>Class <span class="required">*</span></label>
                                    <select data-width="100%" class="form-control selectpicker" name="class_id">
                                        <option value="">Please Select Class</option>';
                                        foreach($class_list as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3">
                                <div class="form-group">
                                    <label>Start Date <span class="required">*</span></label>
                                    <input type="date" value="'.date("Y-m-d", strtotime("-1 month")).'" class="form-control" min="2021-01" max="'.date("Y-m-d").'"  name="start_date">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3">
                                <div class="form-group">
                                    <label>End Date <span class="required">*</span></label>
                                    <input type="date" value="'.date("Y-m-d").'" class="form-control" min="2021-01" max="'.date("Y-m-d").'"  name="end_date">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button onclick="return load_attendance_log()" class="btn btn-outline-success height-40 btn-block"><i class="fa fa-filter"></i> Generate Report</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            
                            <div class="col-lg-7 col-md-5">
                                <a href="" id="download_link" target="_blank" class="btn hidden btn-outline-success">
                                    <i class="fa fa-download"></i> Download Attendance
                                </a>
                            </div>
                            <div class="col-lg-5 col-md-7">
                                <table class="table table-condensed table-bordered text-center">
                                    <tbody>
                                        <tr>
                                            <td style="height:45px"><strong>Present :</strong> <i class="far fa-check-circle text-success"></i></td>
                                            <td style="height:45px"><strong>Absent : </strong> <i class="far fa-times-circle text-danger"></i></td>
                                            <td style="height:45px"><strong>Holiday : </strong> <i class="fas fa-hospital-symbol text-info"></i></td>
                                            <td style="height:45px"><strong>Late : </strong> <i class="fa fa-clock text-warning"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-lg-12">
                                <div class="attendance_log_record">
                                    <div class="text-center font-italic">Attendance record will be displayed here</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';

echo json_encode($response);