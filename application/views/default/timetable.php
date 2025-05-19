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
$pageTitle = "Timetable";
$response->title = $pageTitle;

// confirm that the user has the required permissions
if(!$accessObject->hasAccess("manage", "timetable") || !in_array("timetable", $clientFeatures)) {
    // show the error page
    $response->html = page_not_found("permission_denied");
} else {

    // set some scripts to load
    $response->scripts = ["assets/js/timetable.js"];

    // confirm if the user has permission to manage
    $isPermitted = $accessObject->hasAccess("allocate", "timetable");

    // assign more variables
    $class_id = null;
    $disabled_inputs = [];
    $timetable_found = false;
    $timetable_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

    // set the parameter for the classes
    $classes_param = (object) [
        "clientId" => $clientId,
        "columns" => "a.id, a.name, a.item_id"
    ];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // set the parameters to load
    $params = (object)[
        "clientId" => $clientId, "client_data" => $defaultUser->client
    ];

    // if the $timetable_id is not empty
    if(!empty($timetable_id)) {
        // append some more viables to get the information
        $params->limit = 1;
        $params->timetable_id = $timetable_id;
    }
    $timetable_list = load_class("timetable", "controllers", $params)->list($params);

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
            }
        }
    } else {
        // set the timetable key
        $timetable_list = $timetable_list["data"];
    }

    $n_string = $disabled_inputs;

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    '.($timetable_found ? '<div class="breadcrumb-item"><a href="'.$baseUrl.'timetable">Timetable List</a></div>' : null).'
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row" id="timetable_form">';
                                if(!$timetable_found) {
                                    $response->html .= '
                                    <div class="col-lg-3 p-0">
                                        <div class="form-group p-2 mb-0">
                                            <h5 class="badge-success p-2">CREATED TIMETABLES LIST</h5>
                                        </div>
                                        <div class="trix-slim-scroll p-2" style="max-height:500px; overflow-y:auto;">';

                                    if(empty($timetable_list)) {
                                        $response->html .= "
                                        <div class='text-center text-danger font-italic'>
                                            Sorry! You have currently not added any timetable yet.
                                            Kindly complete the form to add a new timetable.
                                        </div>";
                                    } else {
                                        foreach($timetable_list as $key => $value) {
                                            $response->html .= "
                                                <div data-row_id=\"{$value->item_id}\" class='form-group mb-3 border-bottom timetable-item'>
                                                    <p class='clearfix pb-0 mb-0'>
                                                        <span class='float-left font-weight-bolder'>Name</span>
                                                        <span class='float-right'>{$value->name}</span>
                                                    </p>
                                                    ".($value->class_name ? 
                                                        "<p class='clearfix pb-0 mb-0'>
                                                            <span class='float-left font-weight-bolder'>Class</span>
                                                            <span class='float-right'>{$value->class_name}</span>
                                                        </p>" : ""
                                                    )."
                                                    <p class='clearfix pb-0 mb-0'>
                                                        <span class='float-left font-weight-bolder'>Slots</span>
                                                        <span class='float-right'>{$value->slots}</span>
                                                    </p>
                                                    <p class='clearfix pb-0 mb-0'>
                                                        <span class='float-left font-weight-bolder'>Days</span>
                                                        <span class='float-right'>{$value->days}</span>
                                                    </p>
                                                    <p class='clearfix pb-0 mb-0'>
                                                        <span class='float-left font-weight-bolder'>Start Time</span>
                                                        <span class='float-right'>{$value->start_time}</span>
                                                    </p>
                                                    <p class='clearfix pb-0 mb-0'>
                                                        <span class='float-left font-weight-bolder'>Duration</span>
                                                        <span class='float-right'>{$value->duration} minutes</span>
                                                    </p>
                                                    <p class='clearfix pb-0 mb-2 text-right'>
                                                        <a href='#' onclick='return delete_record(\"{$value->item_id}\", \"timetable\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a>
                                                        ".($isPermitted ? "<a href='{$baseUrl}timetable-allocate/{$value->item_id}' title='Allocate subjects / courses to each time.' class='btn btn-outline-warning btn-sm'><i class='fa fa-copy'></i> Allocate</a>" : null)."
                                                        <a class='btn btn-outline-primary btn-sm' href='{$baseUrl}timetable/{$value->item_id}' title='Modify the timetable structure.'><i class='fa fa-edit'></i> Modify</a>
                                                    </p>
                                                </div>
                                            ";
                                        }
                                    }
                                    $response->html .= '
                                        </div>
                                    </div>';
                                }
                                $response->html .= '
                                <div class="col-lg-'.($timetable_found ? 12 : 9).' mb-3">
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
                                        </div>
                                        '.(!$timetable_found ? 
                                            '<div class="col-lg-12 text-right">
                                                <button onclick="return save_Timetable_Record()" class="btn btn-outline-success">Save Timetable</button>
                                            </div>' : ''
                                        ).'
                                    </div>
                                </div>';
                                if($timetable_found) {
                                    $response->html .= '
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive timetable">
                                                <div id="dynamic_timetable"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mt-3" id="legend">
                                        <div class="row">
                                            <div class="col-lg-2 mt-3">
                                                <div title="Click on a slot to disable or enable" class="card mb-3">
                                                    <div class="card-body bg-blue text-center">
                                                        <strong>Active</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mt-3">
                                                <div class="card">
                                                    <div title="Click on a slot to disable or enable" class="card-body bg-grey text-center">
                                                        <strong>Disabled</strong>
                                                        <input type="hidden" hidden name="timetable_id" id="timetable_id" value="'.$timetable_id.'">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center col-lg-8 mt-3">
                                                <div class="d-flex justify-content-around">
                                                    '.($isPermitted ? '<div><a href="'.$baseUrl.'timetable-allocate/'.$timetable_id.'" class="btn btn-outline-warning pt-3 pb-3"><i class="fa fa-copy"></i> Allocate Timetable</a></div>' : null).'
                                                    <div><button onclick="return save_Timetable_Record()" class="btn btn-outline-success pt-3 pb-3"><i class="fa fa-save"></i> Update Timetable</button></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="disabledSlots" data-disabled_inputs=\''.json_encode($n_string).'\'>';
                                        foreach($disabled_inputs as $input) {
                                            $response->html .= "<input name='{$input}' type='hidden' value='disabled'>";
                                        }
                                }
                                $response->html .= '
                                    </div>
                                </div>
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