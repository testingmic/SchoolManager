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
$pageTitle = "Allocate Timetable";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [];

$timetable_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : $session->last_TimetableId;

// set the parameters to load
$params = (object)["clientId" => $clientId];
$timetable_list = load_class("timetable", "controllers")->list($params);

// set the timetable key
$timetable_list = $timetable_list["data"];

$courses_list = [];
$rooms_list = [];
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
        $courses_list = load_class("courses", "controllers")->list($params)["data"];

        // load the class rooms available to be used
        $rooms_list = [];
    } else {
        // once again set the $timetable_id == null even if a session has been set 
        $timetable_id = null;
    }
}

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
                    <input type="hidden" disabled value="'.($data->name ?? null).'" name="t_name">
                    <input type="hidden" disabled value="'.($data->slots ?? null).'" name="t_slots">
                    <input type="hidden" disabled value="'.($data->duration ?? null).'" name="t_duration">
                    <input type="hidden" disabled value="'.($data->days ?? null).'" name="t_days">
                    <input type="hidden" disabled value="'.($data->start_time ?? null).'" name="t_start_time">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <div class="form-group">
                                    <label>Timetable</label>
                                    <select style="max-width:400px" class="form-control selectpicker" id="current_TimetableId" name="current_TimetableId">';
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
                            <div class="col-lg-12" id="allocate_dynamic_timetable">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-12 table-responsive timetable">
                                                    <div id="dynamic_timetable"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 text-center timetable" id="rightpane">
                                        <div class="form-group" id="courseScroll">
                                            <h5>Courses List</h5>';
                                            foreach($courses_list as $key => $value) {
                                                $response->html .= "<div class='course' id='{$value->item_id}'>{$value->name}</div>";
                                            }
                                            $response->html .= '
                                        </div>
                                        <div class="form-group">
                                            <h5>Assign Room</h5>
                                            <select name="room_name" class="updateSelect form-control selectpicker" data-placeholder="Choose Room..." required="" onchange="assignRoom(this.value)" tabindex="-1">';
                                                foreach($rooms_list as $key => $value) {
                                                    $response->html .= "<option value='{$value->item_id}'>{$value->name} - {$value->code}</option>";
                                                }
                                            $response->html .= '
                                                </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-3 text-left">
                                <span style="line-height: 25px">
                                ● Drag and Drop a course from the right panel to the required slot<br>
                                ● Double-click on a slot to clear it<br>
                                ● Conflicting Slots are indicated in red and would contain the number of batches affected<br>
                                ● A "~" before a course indicates that its conflicts are not considered
                                </span>
                                <form method="post" class="hidden" action="'.$baseUrl.'api/timetable/allocate" id="courseAlloc">
                                    <button type="submit">Save</button>
                                </form>
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