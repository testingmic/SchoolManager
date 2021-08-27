<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultClientData;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();
$clientId = $session->clientId;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$response->scripts = [];
$filter = (object) $_POST;
$response->title = "Class Assessment List : {$appName}";

$response->scripts = ["assets/js/filters.js"];

// the query parameter to load the user information
$assignments_param = (object) [
    "show_marks" => true,
    "clientId" => $clientId,
    "userData" => $defaultUser,
    "client_data" => $defaultClientData,
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

// colors for the list
$color = [
    "Test" => "success",
    "Assignment" => "warning",
    "Quiz" => "primary",
    "Exam" => "dark",
    "Group Work" => "secondary",
];

// unset the sessions if $session->currentQuestionId is not empty
$assignments = "";
$assessment_array = [];
foreach($item_list["data"] as $key => $each) {
    
    $each->assignment_type_label = $color[$each->assignment_type];
    $assessment_array[$each->item_id] = $each;
    $action = "<a title='Click to update assignment record' href='#' onclick='return loadPage(\"{$baseUrl}update-assessment/{$each->item_id}/view\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate && $each->questions_type == "multiple_choice") {
        $action .= "&nbsp;<a title='Click to manage questions for this assignment' href='#' onclick='return loadPage(\"{$baseUrl}add-assessment/add_question?qid={$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-warning' title='Reviews Questions'>Questions</a>";
    }

    // if the state is either closed or graded
    if(in_array($each->state, ["Closed", "Graded"])) {
        $action .= "&nbsp;<a href='#' title='Click to view student marks this Assignment' onclick='return view_AssessmentMarks(\"{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-list'></i></a>";
    }

    if($hasDelete && in_array($each->state, ["Pending", "Draft"])) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Assignment' onclick='return delete_record(\"{$each->id}\", \"assignments\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $assignments .= "<tr data-row_id=\"{$each->id}\">";
    $assignments .= "<td>".($key+1)."</td>";
    $assignments .= "<td>
        <a href='#' onclick='return loadPage(\"{$baseUrl}update-assessment/{$each->item_id}/view\");'>
            {$each->assignment_title}</a> <strong class='badge p-1 pr-2 pl-2 badge-{$color[$each->assignment_type]}'>{$each->assignment_type}</strong>
        ".(
        $hasUpdate ? 
            "<br>Class: <strong>{$each->class_name}</strong>
            <br>Course: <strong>{$each->course_name}</strong>" : 
            "<br>Course:</strong> {$each->course_name}</strong>"
        )."</td>";
    $assignments .= "<td>{$each->due_date} ".(!empty($each->due_time) ? "@ {$each->due_time}" : null)."</td>";

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
$courses_list = [];

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "id, name, item_id"
];
// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

// if the class_id is not empty
if(!empty($filter->class_id)) {
    $courses_param = (object) [
        "clientId" => $clientId,
        "minified" => true,
        "userData" => $defaultUser,
        "class_id" => $filter->class_id,
        "client_data" => $defaultClientData,
    ];
    $courses_list = load_class("courses", "controllers", $courses_param)->list($courses_param)["data"];
}

$response->array_stream["assessment_array"] = $assessment_array;

$response->html = '
    <section class="section">
        <div class="section-header byPass_Null_Value">
            <h1><i class="fa fa-book-reader"></i> Class Assessment List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Class Assessment List</div>
            </div>
        </div>
        <div class="row" id="filter_Department_Class">
            <div class="col-xl-4 '.(!$hasFiltering ? 'hidden': '').' col-md-4 col-12 form-group">
                <label>Select Department</label>
                <select class="form-control selectpicker" data-width="100%" id="department_id" name="department_id">
                    <option value="">Please Select Department</option>';
                    foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Class</label>
                <select class="form-control selectpicker" data-width="100%" name="class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {
                        $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Course</label>
                <select class="form-control selectpicker" data-width="100%" name="course_id">
                    <option value="">Please Select Course</option>';
                    foreach($courses_list as $each) {
                        $response->html .= "<option ".(isset($filter->course_id) && ($filter->course_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">{$each->name}</option>";
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
                            <table data-empty="" class="table table-bordered table-striped datatable">
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
                                        <th align="center" width="10%"></th>
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