<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Bus Details";
$response->title = $pageTitle;

// if the user has the permission
$hasView = $accessObject->hasAccess("view", "buses");

// if the user does not have the required permissions
if(!$hasView || !in_array("bus_manager", $clientFeatures)) {
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

// set the item unique id
$bus_id = confirm_url_id(1) ? $SITEURL[1] : null;

// set the query object parameter
$param = (object)[
    "bus_id" => $bus_id ?? null,
    "clientId" => $clientId,
];

// confirm that the school has the documents manager feature enabled
if(empty($bus_id)) {
    // permission denied
    $response->html = page_not_found("not_found");
} else {

    // permission to modify and validate
    $hasModify = $accessObject->hasAccess("update", "buses");
    $hasCreate = $accessObject->hasAccess("add", "buses");

    // confirm if the user is on the attendance page
    $attendancePage = (bool) isset($SITEURL[2]) && ($SITEURL[2] == "attendance");

    // set the user permissions
    $permissions = [
        "hasView" => $hasView,
        "hasModify" => $hasModify,
        "hasCreate" => $hasCreate,
        "attendancePage" => $attendancePage,
        "markAttendance" => $accessObject->hasAccess("bus_log", "attendance")
    ];

    // create a bus object
    $busObj = load_class("buses", "controllers");

    // get the bus record
    $buses_list = "<div class='buses_list_container'><div class='row'>";
    $buses = $busObj->list($param)["data"];
    $buses_array_list = [];

    // buses list
    if(empty($buses) || !is_array($buses)) {
        // no bus information found
        $buses_list .= "
        <div data-element_type='bus' class='text-center empty_div_container col-lg-12 p-2 text-danger'>
            You have not yet added any buses yet
        </div><div data-element_type='bus'></div>";
        
        // convert the buses_array_list to an object
        $buses_array_list = (object) [];
    } else {
        // loop through the buses list
        foreach($buses as $bus) {
            // append to the array
            $buses_array_list[$bus->item_id] = $bus;
            $pageTitle = $pageTitle;
            // format the bus
            $buses_list .= format_bus_item($bus, false, true, "col-12", $permissions);
        }
    }

    // if the attendance page is active
    if($attendancePage && $permissions["markAttendance"]) {
        // get the attendance history
        $attendanceHistory = $busObj->attendance_history($param);
        
        $bus_attendance = "";
        foreach($attendanceHistory["data"] as $key => $attendance) {
            $bus_attendance .= "<tr>
                <td>".($key + 1)."</td>
                <td>".$attendance->fullname."</td>
                <td>".$attendance->action."</td>
                <td>".$attendance->user_type."</td>
                <td>".$attendance->brand."</td>
                <td>".$attendance->date_created."</td>
            </tr>";
        }
    }

    // also return the buses array list
    $response->array_stream["buses_array_list"] = $buses_array_list;

    $buses_list .= "</div></div>";

    // get the bus form
    $the_bus_form = load_class("forms", "controllers")->bus_form();

    // uploads script
    $response->scripts = ["assets/js/comments.js", "assets/js/buses.js", "assets/js/upload.js"];

    // document information
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-bus"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'buses">Buses List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-4">
                    '.$buses_list.'
                </div>

                <div class="col-md-8">
                    '.($attendancePage ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-outline-success" target="_blank" href="'.$baseUrl.'qr_code/?request=bus&bus_id='.$bus_id.'&client='.$clientId.'"><i class="fa fa-bus"></i> Take Attendance</a>
                    </div>
                    ' : '').'
                    <div class="card">
                        '.($attendancePage ? '
                        <div class="card-header">
                            <h4 class="card-title mb-0">Bus Attendance History</h4>
                        </div>' : '').'
                        <div class="card-body">
                            '.($attendancePage ? '
                            <div class="table-responsive">
                                <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th>Passenger</th>
                                            <th>Action</th>
                                            <th>Type</th>
                                            <th>Details</th>
                                            <th>Date & Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>'.$bus_attendance.'</tbody>
                                </table>
                            </div>' :
                            '<div class="slim-scroll">
                                <div class="p-0 m-0">
                                    '.leave_comments_builder("bus", $bus_id, false).'
                                    <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$bus_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                    <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                                </div>
                            </div>').'
                        </div>
                    </div>
                </div>

            </div>
        </section>'.$the_bus_form;

}

// print out the response
echo json_encode($response);
?>