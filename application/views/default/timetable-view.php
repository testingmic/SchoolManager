<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $accessObject;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$disabled_inputs = [];
$pageTitle = "View Timetable";
$response->title = $pageTitle;
$response->scripts = [];
$response->timer = 0;

// confirm if the user has permission to manage
$isPermitted = $accessObject->hasAccess("manage", "timetable");

// set the id for the timetable id
$timetable_id = $SITEURL[1] ?? $session->last_TimetableId;

// set the parameters to load
$params = (object)["clientId" => $clientId, "client_data" => $defaultUser->client];

// if a student is logged in then show timetables for the class
if(in_array($defaultUser->user_type, ["student", "parent"])) {
    $params->class_id = $session->student_class_id ? $session->student_class_id : $defaultUser->class_guid;
} elseif($defaultUser->user_type === "teacher") {
    $params->class_id = $defaultUser->class_ids;
}

// set the parent menu
$response->parent_menu = "timetable";

// create a new object
$timetableClass = load_class("timetable", "controllers", $params);

// load the timetables list
$timetable_list = $timetableClass->list($params);

// set the timetable key
$timetable_list = $timetable_list["data"];

$rooms_list = [];
$courses_list = [];
$timetable_allocations = [];
$response->scripts = ["assets/js/timetable.js"];
$table = "No timetable record to show at the moment.";

// if the table is not empty
if(!empty($timetable_list)) {
    
    // get the first item
    $data = $timetable_list[$timetable_id] ?? null;
    
    // if the data is not empty
    if(!empty($data)) {

        // set the found variable to true
        $timetable_found = true;
        
        // load the class Subjects List
        $params->class_id = $data->class_id ?? null;

        // load the allocations
        $params->limit = 1;
        $params->timetable_id = $timetable_id;
        
        // draw the timetable to show
        $table = $timetableClass->draw($params);

    } else {
        // once again set the $timetable_id == null even if a session has been set 
        $timetable_id = null;
    }

}

// display the page
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-clock"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                '.($isPermitted ? '<div class="breadcrumb-item active"><a href="'.$baseUrl.'timetable">Timetables List</a></div>' : null).'
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <div class="form-group">
                                    <label><strong>Select Class Timetable</strong></label>
                                    <select data-width="100%" class="form-control selectpicker" data-url="timetable-view" id="change_TimetableViewId" name="change_TimetableViewId">';
                                    if(empty($timetable_id)) {
                                        $response->html .= "<option value='auto_select'>Select Timetable</option>";
                                    }
                                    // if the timetable record is not empty
                                    if(is_array($timetable_list)) {
                                        // loop through the timetable record
                                        foreach($timetable_list as $key => $value) {
                                            $response->html .= "<option ".($timetable_id === $value->item_id ? "selected" : "")." value='{$value->item_id}'>{$value->name} - {$value->class_name}</option>";
                                        }
                                    }
                                    $response->html .= '
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-12" id="timetable_content_loader" style="min-height:200px">
                                        <div class="form-content-loader" style="display: none; position: absolute">
                                            <div class="offline-content text-center">
                                                <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                            </div>
                                        </div>
                                        <div class="table-responsive" id="timetable_content">
                                            '.(isset($table["table"]) ? $table["table"] : "<div class='text-center alert alert-warning'>{$table}</div>").'
                                        </div>
                                        '.(isset($table["table"]) && !empty($table["result"]) ? "<div class='text-center mt-2'><a class='btn btn-outline-success' target='_blank' href='{$baseUrl}download/timetable?tb_id={$timetable_id}&dw=true'>
                                            <i class='fa fa-download'></i> Download Timetable</a></div>" : "").'
                                    </div>
                                </div>
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