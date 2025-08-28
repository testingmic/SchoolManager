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
$response->title = "Attendance History";

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
    "request" => "daily",
    "client_data" => $defaultUser->client
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
foreach(['bus_id', 'user_id', 'action', 'request'] as $key) {
    if(!empty($filter->$key)) {
        $params->{$key} = $filter->$key;
    }
}

// create a bus object
$busObj = load_class("buses", "controllers");

// append the scripts
$response->scripts = ["assets/js/filters.js"];

// set the attendance history
$attendance_history = "";

$statistics = [
    'total' => 0,
    'checkin' => 0,
    'checkout' => 0,
    'type' => [],
    'days' => [],
    'unique_days' => []
];

$users_ids = [$defaultUser->user_row_id];
if($isWardParent) {
    $params->user_ids = array_column($defaultUser->wards_list, "id");
    $users_ids = array_column($defaultUser->wards_list, "id");
    $users_ids[] = $defaultUser->user_row_id;
}

// set the user ids
$params->user_ids = $notAdminAccountant ? $users_ids : [];

// get the attendance history
$attendanceHistory = $busObj->attendance_history($params);

// loop through the attendance history
foreach($attendanceHistory["data"] as $key => $attendance) {
    // set the total
    $statistics['total']++;
    if($attendance->action == "checkin") {
        $statistics['checkin']++;
    } else {
        $statistics['checkout']++;
    }

    if(!isset($statistics['days'][$attendance->date_logged])) {
        $statistics['days'][$attendance->date_logged] = 0;
    }
    $statistics['days'][$attendance->date_logged]++;

    if(!$notAdminAccountant) {
        // set the color
        $color = $attendance->action == "checkin" ? "text-green-500" : "text-red-500";
        $attendance_history .= "<tr>
            <td>".($key + 1)."</td>
            <td>
                <div>".$attendance->fullname."</div>
                ".(!empty($attendance->class_name) ? "<span class='badge badge-primary p-5px'>".$attendance->class_name."</span>" : "")."
            </td>
            <td><span class='{$color}'>".(!empty($attendance->action) ? ucwords($attendance->action) : "N/A")."</span></td>
            <td>".(!empty($attendance->user_type) ? ucwords($attendance->user_type) : "N/A")."</td>
            <td>".$attendance->date_created."</td>
        </tr>";
    }
}

// get the buses list
$users_list = $isWardParent ? [] : $myClass->pushQuery(
    "id, unique_id, name", 
    "users", 
    "client_id='{$params->clientId}' ".($isWardParent || $isTeacher ? " AND item_id IN ('".implode("','", $users_ids)."')" : "")
);

$simplified_attendance_history = "";
if($notAdminAccountant) {

    if(empty($attendanceHistory["data"])) {
        $simplified_attendance_history = no_record_found("No Attendance History Found", 
        "No attendance history has been for any of ".($isWardParent ? "your wards" : "you")." yet.", null, "Student", false, "fas fa-clock");
    } else {
        foreach($attendanceHistory["data"] as $key => $attendance) {

            // set the location
            $location = empty($attendance->location) ? "School" : $attendance->location;

            $color = $attendance->action == "checkin" ? "text-green-500" : "text-red-500";

            $simplified_attendance_history .= "
            <div class='flex items-center bg-white space-x-3 p-3 mb-2 border rounded-xl'>
                <i class='fas ".($attendance->action == "checkin" ? "fa-check-circle" : "fa-times-circle")." {$color}'></i>
                <div class='flex items-center justify-between w-100'>
                    <div class='flex-1'>
                        <p class='text-sm font-medium text-gray-900'>
                            ".($isParent ? "Your ward {$attendance->fullname}" : "You")." ".($isParent ? "was" : "is")." <span class='{$color}'>".ucwords($attendance->action)."</span> at <strong>".ucwords($location)."</strong> on {$attendance->date_created}
                        </p>
                        <p class='text-xs text-gray-600'><i class={$attendance->date_created}</p>
                        <p class='text-xs text-gray-600'><span class='{$color}'>".ucwords($attendance->action)."</span></p>
                    </div>
                    <div>
                        <span class='badge cursor badge-primary'>{$attendance->location}</span>
                    </div>
                </div>
            </div>"; 
        }
    }
}

$buttons = "";
$buttons_color = "bg-blue-500 hover:bg-blue-600";
$currentTimeHour = date("H");
if($currentTimeHour < 12) {
    $count = $isParent ? count($defaultUser->wards_list) : 1;
    $buttons = $isParent ? "Drop off {$count} Child".($count > 1 ? "ren" : "") : "Report for School";
}
elseif($currentTimeHour >= 14) {
    $buttons_color = "bg-green-500 hover:bg-green-600";
    $buttons = $isParent ? "Pick up Child" : "Checkout from School";
}

// set the html content
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fas fa-ticket-alt"></i> Daily Attendance History</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'attendance">Attendance</a></div>
                <div class="breadcrumb-item">Attendance History</div>
            </div>
        </div>
        <div class="row" id="daily_attendance_history">
            <div class="col-12 col-sm-12 col-lg-12">
                '.(
                    $isParent && !empty($defaultUser->wards_list) ? '
                    <div class="rounded-pill '.$buttons_color.' mb-2 mb-3 cursor-pointer text-center font-25 text-white p-6 btn-block">
                        <div class="font-18">Tap to</div>
                        <strong>'.$buttons.'</strong>
                    </div>' : ''
                ).'
                '.(!$isWardParent ? '
                <div class="text-right mb-2">
                    <a class="btn btn-outline-success anchor" target="_blank" href="'.$baseUrl.'qr_code?request=daily&client='.$session->clientId.'">
                        <i class="fa fa-qrcode"></i> Take Attendance - QR Code
                    </a>
                </div>
                ' : null).'
                <div class="row" id="filter_Daily_Attendance">
                    <div class="col-xl-3 col-md-6 mb-2 form-group '.($isWardParent ? "d-none" : "").'">
                        <label>Select User</label>
                        <select data-width="100%" class="form-control selectpicker" id="user_id" name="user_id">
                            <option value="">Please Select User</option>
                            '.implode("", array_map(function($user) use ($filter) {
                                return "<option ".(!empty($filter->user_id) && $filter->user_id == $user->id ? "selected" : "")." value='{$user->id}'>{$user->name} ({$user->unique_id})</option>";
                            }, $users_list)).'
                        </select>
                    </div>
                    <div class="'.($isWardParent ? "col-lg-4 col-md-4 hidden" : "col-xl-3 col-md-6").' mb-2 form-group">
                        <label>Select Action</label>
                        <select data-width="100%" class="form-control selectpicker" id="action" name="action">
                            <option value="">Please Select Action</option>
                            <option '.(!empty($filter->action) && $filter->action == "checkin" ? "selected" : "").' value="checkin">Check In</option>
                            <option '.(!empty($filter->action) && $filter->action == "checkout" ? "selected" : "").' value="checkout">Check Out</option>
                        </select>
                    </div>
                    <div class="'.($isWardParent ? "col-lg-4 col-md-4" : "col-xl-3 col-md-6").' '.($isParent && empty($defaultUser->wards_list) ? "hidden" : "").' mb-2 form-group">
                        <label>Select Date</label>
                        <input type="text" class="form-control daterange" placeholder="Select Date Range" id="date_logged" name="date_logged" value="'.$date_range.'">
                    </div>
                    <div class="col-xl-3 hidden '.($isWardParent ? "col-lg-4 col-md-4" : "col-md-6").' form-group">
                        <button id="filter_Daily_Attendance" type="submit" class="btn btn-outline-warning height-40 btn-block"><i class="fa fa-filter"></i> FILTER</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 mt-1 sm:grid-cols-2 lg:grid-cols-4 md:grid-cols-3 gap-2 mb-2">
                    '.render_summary_card($statistics["total"], "Total Records", "fa fa-bus", "orange", "null", "attendance-icon").'
                    '.render_summary_card($statistics["checkin"], "Total Checkins", "fa fa-check", "green", "null", "attendance-icon").'
                    '.render_summary_card($statistics["checkout"], "Total Checkouts", "fa fa-arrow-circle-left", "red", "null", "attendance-icon").'
                    '.render_summary_card(count(array_keys($statistics["days"])), "Total Days", "fa-calendar", "cyan", "null", "attendance-icon").'
                </div>
                '.($notAdminAccountant ? $simplified_attendance_history : '
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Passenger</th>
                                        <th>Action</th>
                                        <th>Type</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody>'.$attendance_history.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>'
            ).'
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>