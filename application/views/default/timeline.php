<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$userId = $session->userId;
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "User Activity Timelines";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [
    "assets/js/timeline.js"
];

$activity_list = $myClass->pushQuery("a.*, u.name AS fullname, u.unique_id, u.email, 
    u.phone_number, u.image, u.description AS user_description", 
    "users_activity_logs a LEFT JOIN users u ON u.item_id = a.user_id",
    "(a.client_id='{$clientId}' OR a.user_id='{$userId}') AND a.status = '1' AND a.subject NOT IN ('endpoints') ORDER BY a.id DESC");

$activities = "";
$activity_list_array = [];
foreach($activity_list as $activity) {
    
    $activity_list_array[$activity->id] = $activity;
    $time_ago = time_diff($activity->date_recorded);
    
    $activities .= '
    <div class="activity">
        <div class="activity-icon bg-primary text-white">
            <i class="fas fa-comment-alt"></i>
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

if(empty($activity_list)) {
    $activities = "No activity has been logged for now. Please check back for more detailed activity loved";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
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
// print out the response
echo json_encode($response);
?>