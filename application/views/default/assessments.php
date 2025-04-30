<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultClientData, $clientFeatures, $isTutorAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;
$clientId = $session->clientId;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->scripts = [];
$filter = (object) array_map("xss_clean", $_POST);
$response->title = "School Based Assessment List ";

// end query if the user has no permissions
if(!in_array("class_assessment", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

$response->scripts = ["assets/js/filters.js", "assets/js/lessons.js"];

// the query parameter to load the user information
$assignments_param = (object) [
    "show_marks" => true,
    "clientId" => $clientId,
    "userData" => $defaultUser,
    "client_data" => $defaultClientData,
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "course_id" => $filter->course_id ?? null,
    "assessment_group" => $filter->assessment_group ?? null
];

// unset the session
$session->remove("assignment_uploadID");
$assessmentObj = load_class("assignments", "controllers");

// permissions
$hasDelete = $accessObject->hasAccess("delete", "assignments");
$hasUpdate = $accessObject->hasAccess("update", "assignments");

$hasFiltering = $isTutorAdmin;

$item_list = $assessmentObj->list($assignments_param);

$assignments = "";
$formated_content = $assessmentObj->format_list($item_list, true);

// new items list
$assessment_array = $formated_content["array_list"];
$assignments = $formated_content["assignments_list"];

// default class_list and courses_list
$courses_list = [];

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "a.id, a.name, a.item_id"
];
// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

// if the class_id is not empty
if(!empty($filter->class_id)) {
    // set the params
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
            <h1><i class="fa fa-book-reader"></i> School Based Assessment List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">School Based Assessment List</div>
            </div>
        </div>';
        // if the term has ended
        if($defaultUser->appPrefs->termEnded && ($isAdminAccountant || $isTutorAdmin)) {
            $response->html .= academic_term_ended_dashboard_modal($defaultAcademics, $baseUrl);
        }
        $response->html .= '
        <div class="row" id="filter_Department_Class">
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Department</label>
                <select class="form-control selectpicker" data-width="100%" id="department_id" name="department_id">
                    <option value="">Please Select Department</option>';
                    foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 col-md-2 col-12 form-group">
                <label>Select Class</label>
                <select class="form-control selectpicker" data-width="100%" name="class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {
                        $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 col-md-3 col-12 form-group">
                <label>Select Subject</label>
                <select class="form-control selectpicker" data-width="100%" name="course_id">
                    <option value="">Please Select Subject</option>';
                    foreach($courses_list as $each) {
                        $response->html .= "<option ".(isset($filter->course_id) && ($filter->course_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-lg-2 col-md-2 col-12 form-group">
                <label>Select SBA Type</label>
                <select class="form-control selectpicker" data-width="100%" name="assessment_group">
                    <option value="">Please Select SBA Type</option>';
                    foreach($assessmentObj->assessment_group as $each) {
                        $response->html .= "<option ".(isset($filter->assessment_group) && ($filter->assessment_group == $each) ? "selected" : "")." value=\"{$each}\">{$each}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 col-md-2 col-12 form-group">
                <label for="">&nbsp;</label>
                <button id="filter_Assignments_List" type="submit" class="btn height-40 btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Title</th>
                                        <th>Due Date</th>
                                        '.($hasUpdate ? '
                                            <th class="text-center" width="10%">Assigned</th>
                                            <th class="text-center">Handed In</th>
                                            <th class="text-center">Marked</th>' : '
                                            <th class="text-center">Total Score</th>
                                            <th class="text-center">Awarded Mark</th>
                                            '
                                        ).'
                                        <th>Date Created</th>
                                        <th class="text-center">Status</th>
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