<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Create Timetable";
$response->title = $pageTitle;
$response->scripts = [
    "assets/js/index.js",
    "assets/js/timetable.js"
];

// set the parameter for the classes
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "a.id, a.name, a.item_id"
];
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];


$class_id = null;
$timetable_found = false;
$timetable_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

// if the $timetable_id is not empty
if(!empty($timetable_id)) {
    // set the parameters to load
    $params = (object)[
        "clientId" => $clientId, "client_data" => $defaultUser->client
    ];
    // append some more viables to get the information
    $params->limit = 1;
    $params->timetable_id = $timetable_id;

    $timetable_list = load_class("timetable", "controllers", $params)->list($params);
}

// run this section if $timetable_id is not empty
if(!empty($timetable_id)) {

    // if the table is not empty
    if(!empty($timetable_list["data"])) {
        
        // get the first item
        $data = $timetable_list["data"][$timetable_id] ?? null;
        
        // if the data is not empty
        if(!empty($data)) {
            // set the timetable id in session
            $session->set("last_TimetableId", $timetable_id);

            // set the found variable to true
            $timetable_found = true;
            
            // reassign variables
            $d_name = $data->name;
            $d_time = $data->start_time;
            $d_slots = $data->slots;
            $d_days = $data->days;
            $d_duration = $data->duration;
            $disabled_inputs = $data->disabled_inputs;
            $class_id = $data->class_id;

            $pageTitle = "Modify Timetable";
        }
    }
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-bookmark"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'timetable">Timetable List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                    <a class="btn btn-outline-danger" href="'.$baseUrl.'timetable"><i class="fa fa-arrow-left"></i> Back to Timetable List</a>
                    '.($timetable_found ? '<a class="btn btn-outline-primary" href="'.$baseUrl.'timetable-manage/'.$timetable_id.'"><i class="fa fa-edit"></i> Edit Timetable</a>' : null).'
                </div>';
                if($timetable_id && !$timetable_found) {
                    $response->html .= no_record_found("Record Not Found", "The timetable record you are looking for does not exist.", $baseUrl."timetable-manage", "Timetable");
                } else {
                    $response->html .= '
                    <div class="card">
                        <div class="card-body" id="timetable_form">
                            <div class="row">
                                <div class="col-xl-4 col-md-4 col-12 form-group">
                                    <div class="mb-1">
                                        <select class="form-control selectpicker" data-width="100%" name="class_id">
                                            <option value="">Please Select Class</option>';
                                            foreach($class_list as $each) {
                                                $response->html .= "<option ".($class_id == $each->item_id ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";
                                            }
                                            $response->html .= '
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group mb-3" title="Timetable name eg. JHS 4 Timetable">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Name<span class="required">*</span></span>
                                        </div>
                                        <input autocomplete="Off" type="text" value="'.($d_name ?? null).'" class="form-control" style="border-radius:0px; height:42px;" name="name" id="name">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group mb-3" title="Start time for lesson each day.">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Start Time<span class="required">*</span></span>
                                        </div>
                                        <input max="22:00" type="time" value="'.($d_time ?? "08:00").'" class="form-control" style="border-radius:0px; height:42px;" name="start_time" id="start_time">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group mb-3" title="Number of Slots / Lessons per day">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Slots<span class="required">*</span></span>
                                        </div>
                                        <input type="number" pattern="[0-9]{1,2}" value="'.($d_slots ?? null).'" class="form-control" style="border-radius:0px; height:42px;" name="slots" id="slots">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group mb-3" title="Number of Days in the Week for Class">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Days<span class="required">*</span></span>
                                        </div>
                                        <input type="number" pattern="[0-7]{1,2}" value="'.($d_days ?? null).'" class="form-control" style="border-radius:0px; height:42px;" name="days" id="days">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group mb-3" title="Duration for each lesson in minutes">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Duration<span class="required">*</span></span>
                                        </div>
                                        <input type="number" pattern="[0-9]{2,}" value="'.($d_duration ?? null).'" class="form-control" style="border-radius:0px; height:42px;" name="duration" id="duration">
                                    </div>
                                    <input type="hidden" hidden name="timetable_id" id="timetable_id" value="'.$timetable_id.'">
                                </div>
                                <div class="col-lg-12 text-right">
                                    <button onclick="return save_Timetable_Record()" class="btn btn-outline-success">Save Timetable</button>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                $response->html .= '
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>