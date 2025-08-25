<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $clientFeatures, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Bus Attendance";

// end query if the user has no permissions
if(!in_array("bus_manager", $clientFeatures) || !in_array("qr_code_scanner", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["bus_manager", "qr_code_scanner"]);
    echo json_encode($response);
    exit;
}

// if the user has no permission to log bus attendance
if(!$accessObject->hasAccess("bus_log", "attendance")) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// get the filter parameters
$filter = (object) array_map("xss_clean", $_POST);

// set the parameters
$params = (object) [
    "clientId" => $session->clientId,
    "request" => "bus",
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
foreach(['bus_id', 'user_id', 'action'] as $key) {
    if(!empty($filter->$key)) {
        $params->$key = $filter->$key;
    }
}

// create a bus object
$busObj = load_class("buses", "controllers");

// append the scripts
$response->scripts = ["assets/js/filters.js"];

// get the attendance history
$attendanceHistory = $busObj->attendance_history($params);

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

    // set the color
    $color = $attendance->action == "checkin" ? "text-green-500" : "text-red-500";
    $attendance_history .= "<tr>
        <td>".($key + 1)."</td>
        <td>
            <div>".$attendance->driver_name."</div>
            ".(!empty($attendance->driver_unique_id) ? "<div><strong>Employee ID:</strong> ".$attendance->driver_unique_id."</div>" : "")."
        </td>
        <td>
            <div><a class='text-blue-500' href='{$baseUrl}bus/{$attendance->bus_id}/attendance'>".$attendance->brand."</a></div>
            <div><strong>Reg Number:</strong> ".$attendance->reg_number."</div>
        </td>
        <td>
            <div>".$attendance->fullname."</div>
            ".(!empty($attendance->class_name) ? "<span class='badge badge-primary p-5px'>".$attendance->class_name."</span>" : "")."
        </td>
        <td><span class='{$color}'>".(!empty($attendance->action) ? ucwords($attendance->action) : "N/A")."</span></td>
        <td>".(!empty($attendance->user_type) ? ucwords($attendance->user_type) : "N/A")."</td>
        <td>".$attendance->date_created."</td>
    </tr>";
}

// get the buses list
$buses_list = $myClass->bus_list($session->clientId);
$users_list = $myClass->pushQuery("id, unique_id, name", "users", "client_id='{$params->clientId}' AND user_type NOT IN ('parent', 'guardian')");

// set the html content
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-bus"></i> Bus Attendance Logs</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Bus Attendance</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                    <a class="btn btn-outline-success anchor" target="_blank" href="'.$baseUrl.'qr_code?request=bus&client='.$session->clientId.'">
                        <i class="fa fa-qrcode"></i> Take Attendance - QR Code
                    </a>
                </div>
                <div class="row" id="filter_Bus_Driver">
                    <div class="col-xl-3 col-md-6 mb-2 form-group">
                        <label>Select Bus</label>
                        <select data-width="100%" class="form-control selectpicker" id="bus_id" name="bus_id">
                            <option value="">Please Select Bus</option>
                            '.implode("", array_map(function($bus) use ($filter) {
                                return "<option ".(!empty($filter->bus_id) && $filter->bus_id == $bus->item_id ? "selected" : "")." value='{$bus->item_id}'>{$bus->brand} ({$bus->reg_number})</option>";
                            }, $buses_list)).'
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-2 form-group">
                        <label>Select Passenger</label>
                        <select data-width="100%" class="form-control selectpicker" id="user_id" name="user_id">
                            <option value="">Please Select Passenger</option>
                            '.implode("", array_map(function($user) use ($filter) {
                                return "<option ".(!empty($filter->user_id) && $filter->user_id == $user->id ? "selected" : "")." value='{$user->id}'>{$user->name} ({$user->unique_id})</option>";
                            }, $users_list)).'
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-4 mb-2 form-group">
                        <label>Select Action</label>
                        <select data-width="100%" class="form-control selectpicker" id="action" name="action">
                            <option value="">Please Select Action</option>
                            <option '.(!empty($filter->action) && $filter->action == "checkin" ? "selected" : "").' value="checkin">Check In</option>
                            <option '.(!empty($filter->action) && $filter->action == "checkout" ? "selected" : "").' value="checkout">Check Out</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-md-4 mb-2 form-group">
                        <label>Select Date</label>
                        <input type="text" class="form-control daterange" placeholder="Select Date Range" id="date_logged" name="date_logged" value="'.$date_range.'">
                    </div>
                    <div class="col-xl-2 col-md-4 form-group">
                        <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                        <button id="filter_Bus_Attendance" type="submit" class="btn btn-outline-warning height-40 btn-block"><i class="fa fa-filter"></i> FILTER</button>
                    </div>
                </div>
                <div class="row">
                    '.render_summary_card($statistics["total"], "Total Records", "fa fa-bus", "orange", "col-lg-3").'
                    '.render_summary_card($statistics["checkin"], "Total Checkins", "fa fa-check", "green", "col-lg-3").'
                    '.render_summary_card($statistics["checkout"], "Total Checkouts", "fa fa-check", "red", "col-lg-3").'
                    '.render_summary_card(count(array_keys($statistics["days"])), "Total Days", "fa-calendar", "cyan", "col-lg-3").'
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Driver</th>
                                        <th>Bus</th>
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
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>