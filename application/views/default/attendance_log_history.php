<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $clientFeatures, $defaultCurrency, $isWardParent, $notAdminAccountant, $isParent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Attendance Log Report";

// end query if the user has no permissions
if(!in_array("attendance", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["attendance"]);
    echo json_encode($response);
    exit;
}

// get the filter parameters
$filter = (object) array_map("xss_clean", $_POST);

// set the parameters
$params = (object) [
    "clientId" => $session->clientId,
    "request" => $notAdminAccountant ? "" : "daily",
    "client_data" => $defaultUser->client,
    "start_date" => $filter->start_date ?? date("Y-m-d", strtotime("-3 month")),
    "end_date" => $filter->end_date ?? date("Y-m-d")
];

// if the date_logged is not empty
if(!empty($filter->date_logged)) {
    $date_range = $filter->date_logged;
} else {
    // set the date range
    $date_range = date("Y-m-d", strtotime("-1 month")).":".date("Y-m-d");
}

// set the date range
$params->date_range = $date_range;

// loop through the filter parameters
foreach(['user_id', 'class_id', 'user_type'] as $key) {
    if(!empty($filter->$key)) {
        $params->{$key} = $filter->$key;
    }
}

// if the user has the permission to allocate fees
$createAssessmentTest = $accessObject->hasAccess("review", "attendance");
if(!$createAssessmentTest) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

$attendanceHistory = load_class("attendance", "controllers")->list($params);
if($attendanceHistory["code"] == 200) {
    $attendanceHistory = $attendanceHistory["data"];
} else {
    $attendanceHistory = [];
}

$attendanceLog = "";
foreach($attendanceHistory as $key => $attendance) {

    $users_data = json_decode($attendance->users_data, true);
    
    $presentCount = array_filter($users_data, function($user) {
        return $user["state"] == "present";
    });

    $absentCount = array_filter($users_data, function($user) {
        return $user["state"] == "absent";
    });

    $lateCount = array_filter($users_data, function($user) {
        return $user["state"] == "late";
    });

    $holidayCount = array_filter($users_data, function($user) {
        return $user["state"] == "holiday";
    });

    $actions = "";

    $status = $attendance->finalize ? "<span class='badge badge-success'>Finalized</span>" : "<span class='badge badge-danger'>Not Finalized</span>";

    // if the attendance is not finalized, show the finalize button
    if(!$attendance->finalize) {
        $actions .= "<button data-row_id='{$attendance->id}' type='button' class='btn btn-sm btn-outline-primary finalize' onclick='return mark_as_finalized({$attendance->id})'>
            <i class='fa fa-check'></i> Finalize
        </button>";
    }

    $attendanceLog .= "<tr data-row_id='{$attendance->id}'>
        <td class='text-center'>".($key + 1)."</td>
        <td>".date("jS M, Y", strtotime($attendance->log_date))."</td>
        <td>".ucwords($attendance->user_type)."</td>
        <td>{$attendance->class_name}</td>
        <td>
            <div>
                <div class='border-bottom mb-1 pb-1'>
                    <span>Present:</span>
                    <span class='text-success float-right font-14'>".count($presentCount)."</span>
                </div>
                <div class='border-bottom mb-1 pb-1'>
                    <span>Absent:</span>
                    <span class='text-danger float-right font-14'>".count($absentCount)."</span>
                </div>
                <div>
                    <span>Late:</span>
                    <span class='text-warning float-right font-14'>".count($lateCount)."</span>
                </div>
            </div>
        </td>
        <td class='text-center'><span class='finalized_status_{$attendance->id}'>{$status}</span></td>
        <td>{$attendance->created_by_name}</td>
        <td class='text-center'>{$actions}</td>
    </tr>";
}

// load the scripts
$response->scripts = [
    "assets/js/attendance.js"
];

// display the form information
$response->html = '
<section class="section attendance_log_history">
    <div class="section-header">
        <h1><i class="fa fa-book-open"></i> '.$response->title.'</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="'.$baseUrl.'attendance">Attendance</a></div>
            <div class="breadcrumb-item active">'.$response->title.'</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th width="10%">Date</th>
                                    <th>User Type</th>
                                    <th>Class</th>
                                    <th width="18%">Users Data</th>
                                    <th width="10%" class="text-center">Status</th>
                                    <th width="15%" align="center">Created By</th>
                                    <th align="center" width="10%"></th>
                                </tr>
                            </thead>
                            <tbody>'.$attendanceLog.'</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>';

// print out the response
echo json_encode($response);
?>
