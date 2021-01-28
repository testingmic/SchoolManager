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

// permissive users to be created by each access level
$permissions = [
    "teacher" => [
        "student" => "Students"
    ],
    "accountant" => [
        "student" => "Student",
        "business" => "Business Firm",
    ],
    "admin" => [
        "student" => "Students",
        "teacher" => "Teachers",
        "employee" => "Employees",
        "accountant" => "Accountants",
        "admin" => "Admin Users",
    ]
];

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

    // load the scripts
    $response->scripts = [
        "assets/js/attendance.js"
    ];

    // set the selected date
    $selected_date = isset($_GET["date"]) && $myClass->validDate($_GET["date"]) ? xss_clean($_GET["date"]) : date("Y-m-d");

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'attendance">Attendance</a></div>
                    <div class="breadcrumb-item">Attendance Log</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Selected Date</label>
                                        <input type="text" value="'.$selected_date.'" class="att_datepicker form-control" name="attendance_date" id="attendance_date">
                                    </div>
                                    <div class="form-group">
                                        <label>Select Category</label>
                                        <select data-width="100%" class="form-control selectpicker" name="attendance_category" id="attendance_category">
                                            <option value="null">Please select group</option>';
                                            foreach($permissions[$defaultUser->user_type] as $key => $value) {
                                                $response->html .= "<option value=\"{$key}\">{$value}</option>";
                                            }
                                        $response->html .= '</select>
                                    </div>
                                    <div class="form-group attendance_category_list hidden">
                                        <label>Select Class</label>
                                        <select data-width="100%" class="form-control selectpicker" name="attendance_class" id="attendance_class">
                                            <option value="">Please select Class</option>
                                        </select>
                                    </div>
                                    <div class="form-group refresh_attendance_list text-right hidden">
                                        <button onclick="return refresh_AttendanceLog()" class="btn refresh btn-sm btn-outline-primary"><i class="fa fa-circle-notch"></i> Refresh</button>
                                    </div>
                                </div>
                                <div class="col-md-9" id="attendance">
                                    '.form_loader().'
                                    <div id="attendance_log_list">
                                        <div class="text-center font-italic">Users list is displayed here.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';
    // print out the response
}

echo json_encode($response);