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

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Timetable";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [
    "assets/js/timetable.js"
];

// load_class("scripts", "controllers")->timetable();

$params = (object)[
    "class_ids" => [],
    "clientId" => $clientId
];
$the_form = load_class("forms", "controllers")->class_room_form($params);

$d_time = "08:00";
$d_slots = 9;
$d_days = 6;
$d_duration = 60;
$timetable_id = "alfjlakjkdajfdlkafd";

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
                            <div class="col-lg-12 mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Start Time</span>
                                            </div>
                                            <input type="time" value="'.$d_time.'" class="form-control" style="border-radius:0px; height:42px;" name="start_time" id="start_time">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">No. of Slots</span>
                                            </div>
                                            <input type="number" pattern="[0-9]{1,2}" value="'.$d_slots.'" class="form-control" style="border-radius:0px; height:42px;" name="slots" id="slots">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">No. of Days</span>
                                            </div>
                                            <input type="number" pattern="[0-7]{1,2}" value="'.$d_days.'" class="form-control" style="border-radius:0px; height:42px;" name="days" id="days">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Duration</span>
                                            </div>
                                            <input type="number" pattern="[0-9]{2,}" value="'.$d_duration.'" class="form-control" style="border-radius:0px; height:42px;" name="duration" id="duration">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12 table-responsive timetable">
                                        <div id="dynamic_timetable"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 hidden mt-3" id="legend">
                                <div class="row">
                                    <div class="col-lg-2 mt-3">
                                        <div class="card mb-3">
                                            <div class="card-body bg-blue text-center">
                                                <strong>Active</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 mt-3">
                                        <div class="card">
                                            <div class="card-body bg-grey text-center">
                                                <strong>Disabled</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 pr-2 pl-2 mt-3">
                                        <div class="card p-0">
                                            <div class="card-body text-center">
                                                <strong>Click on a slot to disable or enable</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right col-lg-5 mt-3">
                                        <input type="hidden" name="timetable_id" id="timetable_id" value="'.$timetable_id.'">
                                        <button onclick="return save_Timetable_Record()" class="btn btn-outline-success">Save Timetable</button>
                                    </div>
                                </div>
                                <div id="disabledSlots"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>