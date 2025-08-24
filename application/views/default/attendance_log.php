<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $isReadOnly, $isTutorAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Attendance Log Summary";
$response->title = $pageTitle;

// If the user is not a teacher, employee, accountant or admin then end the request
if(!$isTutorAdmin) {
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
        "teacher" => "Teachers",
        "employee" => "Employees",
        "admin" => "Admin Users",
    ],
    "admin" => [
        "student" => "Students",
        "teacher" => "Teachers",
        "employee" => "Employees",
        "accountant" => "Accountants",
        "admin" => "Admin Users",
    ]
];

// get the permissions list
$permissions = [
    "student" => [],
    "teacher" => [
        "student" => "Students"
    ],
    "accountant" => [
        "student" => "Student",
        "staff" => "Staff",
    ],
    "admin" => [
        "student" => "Students",
        "staff" => "Staff",
    ]
];

// client data
$academics = $defaultUser->client->client_preferences->academics;

// ensure the school is not on vacation
if($defaultUser->appPrefs->termEnded) {
    // found
    $response->html = page_not_found("term_ended", "Sorry! The Current Academic Term Ended on <strong>{$academics->term_ends}</strong>.");
} else {
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
                <h1><i class="fa fa-clock"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'attendance">Attendance</a></div>
                    <div class="breadcrumb-item">Attendance Log</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="text-right mb-2">
                        <a class="btn btn-outline-success" href="'.$baseUrl.'attendance_log?date='.$selected_date.'"><i class="fa fa-calendar"></i> Daily Attendance Log</a>
                        <a class="btn btn-outline-success anchor" target="_blank" href="'.$baseUrl.'qr_code?request=daily&client='.$session->clientId.'"><i class="fa fa-qrcode"></i> QR Code Attendance Log</a>
                    </div>';
                    // set the content
                    if($isReadOnly) {
                        $response->html .= notification_modal("Readonly Mode", $myClass->error_logs["readonly_mode"]["msg"], $myClass->error_logs["readonly_mode"]["link"]);
                    } else {
                     $response->html .= '
                     <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Selected Date</label>
                                        <input type="text" value="'.$selected_date.'" class="att_datepicker form-control" name="attendance_date" id="attendance_date">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Select Category</label>
                                        <select data-width="100%" class="form-control selectpicker" name="attendance_category" id="attendance_category">
                                            <option value="null">Please select group</option>';
                                            foreach($permissions[$defaultUser->user_type] as $key => $value) {
                                                $response->html .= "<option value=\"{$key}\">{$value}</option>";
                                            }
                                        $response->html .= '</select>
                                    </div>
                                </div>
                                <div class="col-md-3 form-group attendance_category_list hidden">
                                    <label>Select Class</label>
                                    <select data-width="100%" class="form-control selectpicker" name="attendance_class" id="attendance_class">
                                        <option value="">Please select Class</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group refresh_attendance_list hidden">
                                        <label class="text-white">Select Class</label>
                                        <button onclick="return refresh_AttendanceLog()" class="btn btn-block refresh btn-primary"><i class="fa fa-circle-notch"></i> Load Attendance</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id="attendance">
                                    '.form_loader().'
                                    <div id="attendance_log_list">
                                        <div class="text-center font-italic">
                                        '.no_record_found("Record Attendance", "Select option above to record attendance for a class or staff for any given date.", null, "Event", false, "fa-clock").'
                                        </div>
                                    </div>
                                    <div id="attendance_log_summary"></div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    }
    $response->html .= '</div>
            </div>
        </section>';

}

echo json_encode($response);