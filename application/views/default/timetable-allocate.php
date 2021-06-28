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
$pageTitle = "Allocate Timetable";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [];
$response->timer = 350;

// confirm that the user has the required permissions
if(!$accessObject->hasAccess("allocate", "timetable")) {
    // show the error page
    $response->html = page_not_found("permission_denied");
} else {

    // confirm if the user has permission to manage
    $isPermitted = $accessObject->hasAccess("manage", "timetable");

    // set the timetable id
    $timetable_id = $SITEURL[1] ?? $session->last_TimetableId;

    // set the parameters to load
    $params = (object)["clientId" => $clientId, "client_data" => $defaultUser->client];

    $timetableClass = load_class("timetable", "controllers", $params);
    $timetable_list = $timetableClass->list($params);

    // set the timetable key
    $timetable_list = $timetable_list["data"];

    $courses_list = [];
    $rooms_list = [];
    $timetable_allocations = [];
    $response->scripts = ["assets/js/timetable.js", "assets/js/allocate.js"];

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
            $courses_list = load_class("courses", "controllers", $params)->list($params)["data"];

            // load the class rooms available to be used
            $rooms_list = load_class("rooms", "controllers", $params)->list($params)["data"];
            $disabled_inputs = $data->disabled_inputs;

            // load the allocations
            $params->limit = 1;
            $params->full_detail = true;
            $params->timetable_id = $timetable_id;
            $timetable_allocations = $timetableClass->list($params)["data"][$timetable_id]->allocations;
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
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    '.($isPermitted ? '<div class="breadcrumb-item active"><a href="'.$baseUrl.'timetable">Timetables List</a></div>' : null).'
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <input type="hidden" disabled value="'.($data->name ?? null).'" name="t_name">
                        <input type="hidden" disabled value="'.($data->slots ?? null).'" name="t_slots">
                        <input type="hidden" disabled value="'.($data->duration ?? null).'" name="t_duration">
                        <input type="hidden" disabled value="'.($data->days ?? null).'" name="t_days">
                        <input type="hidden" disabled value="'.(isset($data->start_time) ? date("h:i A", strtotime($data->start_time)) : null).'" name="t_start_time">
                        <input type="hidden" disabled value="'.($data->class_id ?? null).'" name="t_class_id">
                        <input type="hidden" disabled value="'.($timetable_id ?? null).'" name="timetable_id">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                    <div class="form-group">
                                        <label>Timetable</label>
                                        <select style="max-width:400px" class="form-control selectpicker" data-url="timetable-allocate" id="current_TimetableId" name="current_TimetableId">';
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
                                <div id="disabledSlots" data-disabled_inputs=\''.json_encode($n_string).'\'>';
                                foreach($disabled_inputs as $input) {
                                    $response->html .= "<input name='{$input}' type='hidden' value='disabled'>";
                                }
                                $response->html .= '
                                </div>
                                <div class="col-lg-12" id="allocate_dynamic_timetable">
                                    <div class="row">
                                        <div class="col-lg-9">
                                            <div class="col-lg-12">
                                                <div class="row">
                                                    <div class="col-lg-12 table-responsive timetable">
                                                        <div id="dynamic_timetable"></div>
                                                    </div>
                                                    <div class="col-lg-12 '.(!$timetable_id ? "hidden" : "").' text-center mt-2">
                                                        <button id="save_TimetableAllocation" onclick="return save_TimetableAllocation()" class="btn btn-outline-success"><i class="fa fa-save"></i> Save Timetable</button>
                                                        <a class="btn btn-outline-primary" target="_blank" href="'.$baseUrl.'download?tb=true&tb_id='.$timetable_id.'&dw=true">
                                                        <i class="fa fa-download"></i> Download Timetable</a>
                                                    </div>
                                                    <div class="col-lg-12 mt-3 text-left">
                                                        <span style="line-height: 25px">
                                                        ● Drag and Drop a course from the right panel to the required slot<br>
                                                        ● Double-click on a slot to clear it<br>
                                                        ● Conflicting Slots are indicated in red and would contain the number of batches affected<br>
                                                        ● A "~" before a course indicates that its conflicts are not considered
                                                        </span>
                                                        <form method="post" class="hidden" action="'.$baseUrl.'api/timetable/allocate" id="courseAlloc">';
                                                            foreach($timetable_allocations as $key => $value) {
                                                                foreach($value as $kkey => $vvalue) {
                                                                    $response->html .= "<input type=\"hidden\" name=\"{$vvalue->day}_{$vvalue->slot}\" value=\"{$vvalue->course_id}:{$vvalue->room_id}\">";
                                                                }
                                                            }
                                                        $response->html .= '
                                                            <button type="submit">Save</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 timetable" id="rightpane">
                                            <h5>Courses List</h5>
                                            <div class="form-group text-center trix-slim-scroll" id="courseScroll">';
                                                if(!empty($courses_list)) {
                                                    foreach($courses_list as $key => $value) {
                                                        $response->html .= "<div class='course p-2' id='{$value->item_id}'>{$value->name} ({$value->course_code})</div>";
                                                    }
                                                } else {
                                                    $response->html .= '<div class="text-warning">You have not started offering any courses.<br>Visit the <b>Lesson Planner</b> section to add courses</div>';
                                                }
                                                $response->html .= '
                                            </div>
                                            <div class="form-group text-center">
                                                <h5>Assign Room</h5>
                                                <div id="default_room_label">Click on a slot to assign room</div>
                                                <div class="hidden" id="default_room_select">
                                                    <select name="t_room_id" class="updateSelect form-control selectpicker" data-placeholder="Choose Room..." required="" onchange="assignRoom(this.value)" tabindex="-1">';
                                                    foreach($rooms_list as $key => $value) {
                                                        $response->html .= "<option value='{$value->item_id}'>{$value->name} - {$value->code}</option>";
                                                    }
                                                $response->html .= '
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <h5>Notifications</h5>
                                                <div class="notices_div"><div class="text-warning">The are no nofications to display currently.</div></div>
                                            </div>
                                        </div>
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