<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$disabled_inputs = [];
$pageTitle = "View Timetable";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [];
$response->timer = 0;

$timetable_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : $session->last_TimetableId;

// set the parameters to load
$params = (object)["clientId" => $clientId];

$timetableClass = load_class("timetable", "controllers");
$timetable_list = $timetableClass->list($params);

// set the timetable key
$timetable_list = $timetable_list["data"];

$courses_list = [];
$rooms_list = [];
$timetable_allocations = [];
$response->scripts = ["assets/js/timetable.js"];

// if the table is not empty
if(!empty($timetable_list)) {
    
    // get the first item
    $data = $timetable_list[$timetable_id] ?? null;
    
    // if the data is not empty
    if(!empty($data)) {

        // set the found variable to true
        $timetable_found = true;
        
        // load the class courses list
        $params->minified = true;
        $params->userData = $defaultUser;
        $params->class_id = $data->class_id;
        $courses_list = load_class("courses", "controllers")->list($params)["data"];

        // load the class rooms available to be used
        $rooms_list = load_class("rooms", "controllers")->list($params)["data"];
        $disabled_inputs = $data->disabled_inputs;

        // load the allocations
        $params->limit = 1;
        $params->timetable_id = $timetable_id;
        $table = $timetableClass->draw($params);
    } else {
        // once again set the $timetable_id == null even if a session has been set 
        $timetable_id = null;
    }
}

// set the disabled slots
$n_string = $disabled_inputs;

// display the page
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'timetable">Timetables List</a></div>
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
                                    <label>Timetable</label>
                                    <select style="max-width:400px" class="form-control selectpicker" data-url="timetable-view" id="change_TimetableViewId" name="change_TimetableViewId">';
                                    if(empty($timetable_id)) {
                                        $response->html .= "<option value='auto_select'>Select Timetable</option>";
                                    }
                                    foreach($timetable_list as $key => $value) {
                                        $response->html .= "<option ".($timetable_id === $value->item_id ? "selected" : "")." value='{$value->item_id}'>{$value->name} - {$value->class_name}</option>";
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
                                            '.($table["table"] ?? "<div class='text-center alert alert-warning'>{$table}</div>").'
                                        </div>
                                        '.(isset($table["table"]) ? "<div class='text-center mt-2'><a class='btn btn-outline-success' target='_blank' href='{$baseUrl}download?tb=true&tb_id={$timetable_id}&dw=true'>
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