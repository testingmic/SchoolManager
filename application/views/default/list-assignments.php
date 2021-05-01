<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();
$clientId = $session->clientId;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$response->scripts = [];
$filter = (object) $_POST;
$response->title = "Assignments List : {$appName}";

$response->scripts = ["assets/js/filters.js"];

// the query parameter to load the user information
$assignments_param = (object) [
    "clientId" => $clientId,
    "userData" => $defaultUser,
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "course_id" => $filter->course_id ?? null
];

// unset the session
$session->remove("assignment_uploadID");

$item_list = load_class("assignments", "controllers")->list($assignments_param);

$hasDelete = $accessObject->hasAccess("delete", "assignments");
$hasUpdate = $accessObject->hasAccess("update", "assignments");

$hasFiltering = $accessObject->hasAccess("filters", "settings");

// unset the sessions if $session->currentQuestionId is not empty
$assignments = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a title='Click to update assignment record' href='#' onclick='return loadPage(\"{$baseUrl}update-assignment/{$each->item_id}/view\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate && $each->assignment_type == "multiple_choice") {
        $action .= "&nbsp;<a title='Click to manage questions for this assignment' href='#' onclick='return loadPage(\"{$baseUrl}add-assignment/add_question?qid={$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-warning' title='Reviews Questions'>Questions</a>";
    }

    if($hasDelete && in_array($each->state, ["Pending", "Draft"])) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Assignment' onclick='return delete_record(\"{$each->id}\", \"assignments\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $assignments .= "<tr data-row_id=\"{$each->id}\">";
    $assignments .= "<td>".($key+1)."</td>";
    $assignments .= "<td><a href='#' onclick='return loadPage(\"{$baseUrl}update-assignment/{$each->item_id}/view\");'>{$each->assignment_title}</a> ".(
        $hasUpdate ? 
            "<br>Class: <strong>{$each->class_name}</strong>
            <br>Course: <strong>{$each->course_name}</strong>" : 
            "<br>Course:</strong> {$each->course_name}</strong>"
        )."</td>";
    $assignments .= "<td>{$each->due_date} @ {$each->due_time}</td>";

    // show this section if the user has the necessary permissions
    if($hasUpdate) {
        $assignments .= "<td>{$each->students_assigned}</td>";
        $assignments .= "<td>{$each->students_handed_in}</td>";
        $assignments .= "<td>{$each->students_graded}</td>";
    }
    
    if(!$hasUpdate) {
        $assignments .= "<td>{$each->awarded_mark}</td>";
    }

    $assignments .= "<td>{$each->date_created}</td>";
    $assignments .= "<td>".($hasUpdate ? $myClass->the_status_label($each->state) : $each->handedin_label)."</td>";
    $assignments .= "<td align='center'>{$action}</td>";
    $assignments .= "</tr>";
}

// default class_list and courses_list
$class_list = [];
$courses_list = [];

// if the class_id is not empty
if(!empty($filter->department_id)) {
    $classes_param = (object) [
        "clientId" => $clientId,
        "columns" => "id, name",
        "department_id" => $filter->department_id
    ];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];
}

// if the class_id is not empty
if(!empty($filter->class_id)) {
    $courses_param = (object) [
        "clientId" => $clientId,
        "minified" => true,
        "userData" => $defaultUser,
        "class_id" => $filter->class_id
    ];
    $courses_list = load_class("courses", "controllers")->list($courses_param)["data"];
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Assignments List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Assignments List</div>
            </div>
        </div>
        <div class="row" id="filter_Department_Class">
            <div class="col-xl-4 '.(!$hasFiltering ? 'hidden': '').' col-md-4 col-12 form-group">
                <label>Select Department</label>
                <select class="form-control selectpicker" id="department_id" name="department_id">
                    <option value="">Please Select Department</option>';
                    foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Class</label>
                <select class="form-control selectpicker" name="class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {
                        $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Course</label>
                <select class="form-control selectpicker" name="course_id">
                    <option value="">Please Select Course</option>';
                    foreach($courses_list as $each) {
                        $response->html .= "<option ".(isset($filter->course_id) && ($filter->course_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 '.(!$hasFiltering ? 'hidden': '').' col-md-2 col-12 form-group">
                <label for="">&nbsp;</label>
                <button id="filter_Assignments_List" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Title</th>
                                        <th>Due Date</th>
                                        '.($hasUpdate ? '
                                            <th width="10%">Assigned</th>
                                            <th>Handed In</th>
                                            <th>Marked</th>' : '<th>Awarded Mark</th>'
                                        ).'
                                        <th>Date Created</th>
                                        <th>Status</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$assignments.'</tbody>
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