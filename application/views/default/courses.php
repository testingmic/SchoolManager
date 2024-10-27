<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultAcademics, $isWardParent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Subjects List ";
$response->scripts = ["assets/js/filters.js"];

$clientId = $session->clientId;

$filter = (object) array_map("xss_clean", $_POST);

$courses_param = (object) [
    "clientId" => $clientId,
    "userId" => $session->userId,
    "userData" => $defaultUser,
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "course_tutor" => $filter->course_tutor ?? null,
    "academic_year" => $defaultAcademics->academic_year,
    "academic_term" => $defaultAcademics->academic_term,
];

$item_list = load_class("courses", "controllers")->list($courses_param);

$hasDelete = $accessObject->hasAccess("delete", "course");
$hasUpdate = $accessObject->hasAccess("update", "course");

$hasFiltering = $accessObject->hasAccess("filters", "settings");

$courses = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a title='View the course record' href='#' onclick='return load(\"course/{$each->id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the course record' href='#' onclick='return load(\"course/{$each->id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Delete this Course' onclick='return delete_record(\"{$each->id}\", \"course\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $courses .= "<tr data-row_id=\"{$each->id}\">";
    $courses .= "<td>".($key+1)."</td>";
    $courses .= "<td><span onclick='return load(\"course/{$each->id}\");' class='user_name'>{$each->name}</span></td>";
    $courses .= "<td>{$each->course_code}</td>";
    $courses .= "<td>{$each->credit_hours}</td>";
    
    if(!$isWardParent) {
        $courses .= "<td>";
        foreach($each->class_list as $class) {
            $courses .= "<p class='mb-0 pb-0'><span class='user_name' onclick='return load(\"class/{$class->item_id}\");'>".strtoupper($class->name)."</span></p>";
        }
        $courses .= "</td>";
    }

    $courses .= "<td>";

    // loop through the course tutors
    if(!empty($each->course_tutors)) {
        foreach($each->course_tutors as $tutor) {
            $courses .= "<p class='mb-0 pb-0'><span class='user_name' onclick='return load(\"staff/{$tutor->item_id}/documents\");'>".$tutor->name."</a></p>";
        }
    }

    $courses .= "</td><td align='center'>{$action}</td>";
    $courses .= "</tr>";
}

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "a.id, a.name, a.item_id"
];
// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$response->html = '
    <section class="section">
        <div class="section-header byPass_Null_Value">
            <h1><i class="fa fa-book-open"></i> Subjects List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Subjects List</div>
            </div>
        </div>
        <div class="row" id="filter_Department_Class">
            <div class="col-xl-4 '.(!$hasFiltering ? 'hidden': '').' col-md-4 col-12 form-group">
                <label>Select Department</label>
                <select data-width="100%" class="form-control selectpicker" id="department_id" name="department_id">
                    <option value="">Please Select Department</option>';
                    foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Class</label>
                <select data-width="100%" class="form-control selectpicker" name="class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {
                        $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Subject Tutor</label>
                <select data-width="100%" class="form-control selectpicker" name="course_tutor">
                    <option value="">Please Select Tutor</option>';
                    foreach($myClass->pushQuery("item_id, name, unique_id", "users", "user_type IN ('teacher') AND user_status IN ({$myClass->default_allowed_status_users_list}) AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->course_tutor) && ($filter->course_tutor == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->name)." ({$each->unique_id})</option>";                            
                    }
                $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 '.(!$hasFiltering ? 'hidden': '').' col-md-2 col-12 form-group">
                <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                <button id="filter_Courses_List" type="submit" class="btn height-40 btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Subject Title</th>
                                        <th>Subject Code</th>
                                        <th>Credit Hours</th>
                                        '.(!$isWardParent ? '<th width="12%">Class Name</th>' : null).'
                                        <th>Subject Tutor</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$courses.'</tbody>
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