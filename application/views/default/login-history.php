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
$accessObject->userId = $userId;
$accessObject->clientId = $clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

$response = (object) [];
$pageTitle = "User Login History";
$response->title = "{$pageTitle} : {$appName}";

// if the user has no permissions
if(!$accessObject->hasAccess("login_history", "settings")) {
    // show the error page
    $response->html = page_not_found();
} else {

    $response->scripts = ["assets/js/timeline.js"];

    // get the array list of values
    $activity_list = $myClass->pushQuery(
        "a.*, u.name AS fullname, u.unique_id, u.email, u.phone_number, u.image, u.description, u.user_type", 
        "users_login_history a LEFT JOIN users u ON u.item_id = a.user_id",
        "(a.client_id='{$clientId}' OR a.user_id='{$userId}') ORDER BY a.id DESC");
    
    $activities = "";
    $user_login_history = [];

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
        
        $user_login_history[$activity->id] = $activity;
        $time_ago = time_diff($activity->lastlogin);
        
        $activities .= '
        <div class="activity">
            <div class="activity-icon bg-primary text-white">
                <i class="fas fa-lock"></i>
            </div>
            <div class="activity-detail">
                <div class="mb-2">
                    <span class="text-job text-primary">'.$time_ago.'</span>
                    <span class="bullet"></span>
                    <a class="text-job" onclick="return view_login_history_log(\''.$activity->id.'\')" href="#">View</a>
                </div>
                <div><i class="fa fa-user mr-2 text-danger"></i> '.$activity->fullname.'</div>
                <div><i class="fa fa-globe mr-2 text-primary"></i> '.$activity->log_browser.'</div>
                <div class="mb-2"><i class="fa mr-2 text-success fa-broadcast-tower"></i> '.$activity->log_ipaddress.'</div>
                <div>'.$activity->log_platform.'</div>
            </div>
        </div>';
    }
    $response->array_stream["user_login_history"] = $user_login_history;

    // if the activility list
    if(empty($activity_list)) {
        $activities = "No login history has been logged for now. Please check back for more detailed activity logged";
    }

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
                <div class="col-12 col-sm-12 col-lg-12">
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