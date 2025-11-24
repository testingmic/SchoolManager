<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultClientData, $clientFeatures, $isTutorAdmin, $isWardParent, $isWardTutorParent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;
$clientId = $session->clientId;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->scripts = [];
$filter = (object) array_map("xss_clean", $_POST);
$response->title = "Assessments List";

// end query if the user has no permissions
if(!in_array("class_assessment", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["class_assessment"]);
    echo json_encode($response);
    exit;
}

// set the parent menu
$response->parent_menu = "assessments";

$response->scripts = ["assets/js/filters.js", "assets/js/lessons.js"];

// if the class_id is not empty
if($isWardParent) {
    $filter->class_id = empty($filter->class_id) ? $session->student_class_id : $filter->class_id;
    $filter->class_id = !empty($defaultUser->wards_list) ? array_unique(array_column($defaultUser->wards_list, "class_guid")) : [];
}

// the query parameter to load the user information
$assignments_param = (object) [
    "show_marks" => !$isWardParent,
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

$emptyDataSet = false;
$simplified_assessments_history = "";
if(!$isWardTutorParent) {

    // format the list
    $formated_content = $assessmentObj->format_list($item_list, true);

    // new items list
    $assignments = $formated_content["assignments_list"];

    // filter the array list
    $response->array_stream["assessment_array"] = $formated_content["array_list"] ?? [];
}

else {
    
    if(empty($item_list["data"])) {

        $emptyDataSet = true;
        $simplified_assessments_history = no_record_found(
            "No Assessment Found", 
            ($isWardParent ? "No assessment has been created for any of your wards" : "No assessment has been created for any of your students")." yet.", 
            null, 
            "Student", 
            false, 
            "fas fa-book-reader"
        );
    
    } else {

        $simplified_assessments_history .= "<div class='row'>";

        $export_array = $myClass->append_groupwork_to_assessment ? ["Homework", "Classwork", "Quiz", "GroupWork"] : $myClass->assessment_group;

        foreach($item_list["data"] as $record) {

            // print_r($record);

            $action = "<a title='View Assessment record' href='#' onclick='return load(\"assessment/{$record->item_id}/instructions\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i> View</a>";
        
            // manage questions button
            if($hasUpdate && $record->questions_type == "multiple_choice") {
                $action .= "&nbsp;<a title='Manage questions for this Assessment' href='#' onclick='return load(\"add-assessment/add_question/{$record->item_id}?qid={$record->item_id}\");' class='btn btn-sm mb-1 btn-outline-warning' title='Reviews Questions'>
                    <i class='fa fa-edit'></i> Update
                </a>";
            }
    
            // if the state is either closed or graded
            if(in_array($record->state, ["Closed", "Graded"]) && $hasUpdate) {
                $action .= "&nbsp;<a href='#' title='View student marks this Assessment' onclick='return view_AssessmentMarks(\"{$record->item_id}\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-list'></i> Marks</a>";
            }
    
            if($hasDelete && in_array($record->state, ["Pending", "Draft"])) {
                $action .= "&nbsp;<button title='Delete this Assessment' onclick='return delete_record(\"{$record->id}\", \"assignments\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i> Delete</button>";
            }
            // if the item has not yet been exported
            if(!$record->exported) {
                // if in array
                if(in_array($record->assignment_type, $export_array)) {
                    // append the export marks button if the creator of the question is the same person logged in
                    if(($record->created_by === $defaultUser->user_id || $isAdmin) && in_array($record->state, ["Closed"])) {
                        $action .= " <button onclick='return export_Assessment_Marks(\"{$record->item_id}\",\"{$record->assignment_type}\",\"{$record->class_id}\",\"{$record->course_id}\")' class='btn btn-sm mb-1 btn-outline-warning' title='Export Marks'><i class='fa fa-reply-all'></i> Export</button>";
                    }
                }
            } elseif($record->exported) {
                $record->state = "Exported";
            }

            $simplified_assessments_history .= '
                <div class="col-12 col-lg-4 col-md-6 load_assessment_record" data-id="'.$record->item_id.'">
                    <div class="card card-success">
                        <div class="card-header pr-2 pl-2 pb-0"><h4>'.$record->assignment_title.'</h4></div>
                        <div class="card-body p-2" style="height:160px;max-height:160px;overflow:hidden;">
                            '.$record->assignment_description.'
                        </div>
                        <div class="pl-2 border-top p-2">
                            <strong>Status: </strong> <span class="badge float-right badge-'.($myClass->assessment_color_group[$record->assignment_type] ?? "primary").' ">
                                '.$record->assignment_type.'
                            </span>
                        </div>
                        <div class="pl-2 p-2">
                            <strong>Grading: </strong><span class="float-right">'.$record->grading.'</span>
                        </div>
                        <div class="pl-2 p-2">
                            <strong>Awarded Mark: </strong><span class="float-right">'.$record->awarded_mark.'</span>
                        </div>
                        <div class="pl-2 p-2">
                            <strong>Class Average: </strong><span class="float-right">'.$record->class_average.'</span>
                        </div>
                        <div class="pl-2 p-2">
                            <strong>Class: </strong><span class="float-right">'.$record->class_name.'</span>
                        </div>
                        <div class="pl-2 p-2">
                            <strong>Subject: </strong><span class="float-right">'.$record->course_name.'</span>
                        </div>
                        <div class="pl-2 p-2">
                            <strong>Due Date: </strong><span class="float-right">'.$record->due_date.' '.$record->due_time.'</span>
                        </div>
                        <div class="pl-2 mb-1 mt-2">
                            <div class="text-center">
                                '.$action.'
                            </div>
                        </div>
                    </div>
                </div>';

        }

        $simplified_assessments_history .= "</div>";
    }

}

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
    $courses_list = load_class("subjects", "controllers", $courses_param)->list($courses_param)["data"];
}



$response->html = '
    <section class="section">
        <div class="section-header byPass_Null_Value">
            <h1><i class="fa fa-book-reader"></i> School Based Assessment</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Assessments List</div>
            </div>
        </div>';
        // if the term has ended
        if(($isAdminAccountant || $isTutorAdmin)) {
            $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
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
            <div class="'.($isWardParent ? "col-md-3 hidden" : "col-xl-2 col-md-2").' col-12 form-group">
                <label>Select Class</label>
                <select class="form-control selectpicker" data-width="100%" name="class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {

                        // if the class is not the student class and the user is a ward parent
                        if($isWardParent && $each->item_id !== $session->student_class_id) {
                            continue;
                        }

                        $response->html .= "<option ".(isset($filter->class_id) && (in_array($each->item_id, $filter->class_id)) ? "selected" : "")." value=\"{$each->item_id}\">{$each->name}</option>";
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
            <div class="'.($isWardParent ? "col-md-3" : "col-xl-2 col-md-2").' col-12 form-group">
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
                <label for="filter_Assignments_List" class="d-none d-sm-block">&nbsp;</label>
                <button id="filter_Assignments_List" id="filter_Assignments_List" type="submit" class="btn height-40 btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
            '.($isWardTutorParent ? $simplified_assessments_history : '
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="25%">Title</th>
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
                                        <th align="center" width="14%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$assignments.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>').'
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>