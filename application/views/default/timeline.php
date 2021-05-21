<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$userId = $session->userId;
$clientId = $session->clientId;

// specify some variables
$response = (object) [];
$pageTitle = "User Activity Timelines";
$response->title = "{$pageTitle} : {$appName}";

// if the user has no permissions
if(!$accessObject->hasAccess("activities", "settings")) {
    // show the error page
    $response->html = page_not_found("permission_denied");
} else {

    $response->scripts = ["assets/js/timeline.js"];

    // get the array list of values
    $activity_list = $myClass->pushQuery("a.*, u.name AS fullname, u.unique_id, u.email, 
        u.phone_number, u.image, u.description AS user_description", 
        "users_activity_logs a LEFT JOIN users u ON u.item_id = a.user_id",
        "(a.client_id='{$clientId}' OR a.user_id='{$userId}') AND a.status = '1' AND a.subject NOT IN ('endpoints') ORDER BY a.id DESC");

    $activities = "";
    $activity_list_array = [];

    $icons = [
        "assignment" => "fa-book-reader",
        "assignments" => "fa-book-reader",
        "attendance_log" => "fa-user-check",
        "guardian_ward" => "fa-user-graduate",
        "student_account" => "fa-user-graduate",
        "parent_account" => "fa-user-clock",
        "employee_account" => "fa-users",
        "accountant_account" => "fa-user-shield",
        "teacher_account" => "fa-user-tie",
        "admin_account" => "fa-user-cog"
    ];
    foreach($activity_list as $activity) {
        
        $activity_list_array[$activity->id] = $activity;
        $time_ago = time_diff($activity->date_recorded);
        
        $icon = isset($icons[$activity->subject]) ? $icons[$activity->subject] : "fa-comment-alt";
        
        $activities .= '
        <div class="activity">
            <div class="activity-icon bg-primary text-white">
                <i class="fas '.$icon.'"></i>
            </div>
            <div class="activity-detail">
                <div class="mb-2">
                    <span class="text-job text-primary">'.$time_ago.'</span>
                    <span class="bullet"></span>
                    <a class="text-job" onclick="return view_activity_log(\''.$activity->id.'\')" href="#">View</a>
                    <div class="float-right dropdown">
                        <a href="#" data-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></a>
                        <div class="dropdown-menu">
                            <div class="dropdown-title">Options</div>
                            <a href="#" onclick="return view_activity_log(\''.$activity->id.'\')" class="dropdown-item has-icon"><i class="fas fa-list"></i> View Details</a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item has-icon text-danger">
                                <i class="fas fa-trash-alt"></i> Archive Record
                            </a>
                        </div>
                    </div>
                </div>
                <div>'.$activity->description.'</div>
            </div>
        </div>';
    }
    $response->array_stream["activity_list_array"] = $activity_list_array;
    $response->array_stream["activity_list_icons"] = $icons;

    // if the activility list
    if(empty($activity_list)) {
        $activities = "No activity has been logged for now. Please check back for more detailed activity logged";
    }

    // set the dates
    $start_date = date("Y-m-d", strtotime("yesterday"));
    $end_date = date("Y-m-d");

    $sections = [
        "admin_account" => "Account Modification",
        "assignments" => "Assignments",
        "assignment-grade" => "Assignment Grading",
        "attendance_log" => "Attendance Logs",
        "bank_details" => "Staff Account Details",
        "courses" => "Courses",
        "courses_plan" => "Course Lesson Plan",
        "course_unit" => "Course Lesson Units",
        "e_learning" => "E-Learning",
        "fees_allocation" => "Fees Allocation",
        "fees_payment" => "Fees Payment",
        "guardian_ward" => "Guardian Ward",
        "payslip" => "Payslips",
        "salary_allowances" => "Salary Allowances"
    ];

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-2 col-md-3">
                    <label>Start Date</label>
                    <input type="text" class="form-control datepicker" value="'.$start_date.'" name="start_date">
                </div>
                <div class="col-lg-2 col-md-3">
                    <label>End Date</label>
                    <input type="text" class="form-control datepicker" value="'.$end_date.'" name="end_date">
                </div>
                <div class="col-lg-3 col-md-3">
                    <label>Section</label>
                    <select class="form-control selectpicker" name="section">
                        <option>All Sections</option>';
                        foreach($sections as $key => $section) {
                            $response->html .= "<option value='{$key}'>{$section}</option>";
                        }
                        $response->html .= '
                    </select>
                </div>
                <div class="col-lg-2 col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-lg-12 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-7 trix-slim-scroll" style="max-height:700px; overflow-y:auto;">
                                    <div class="activities mt-3">
                                    '.$activities.'
                                    </div>
                                </div>
                                <div class="col-lg-5" id="activity_log_detail"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';
}
// print out the response
echo json_encode($response);
?>