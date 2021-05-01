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

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$response->title = "Courses List : {$appName}";
$response->scripts = ["assets/js/filters.js"];

$clientId = $session->clientId;

$filter = (object) $_POST;

$courses_param = (object) [
    "clientId" => $clientId,
    "userId" => $session->userId,
    "userData" => $defaultUser,
    "limit" => 99999,
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "course_tutor" => $filter->course_tutor ?? null
];

$item_list = load_class("courses", "controllers")->list($courses_param);

$hasDelete = $accessObject->hasAccess("delete", "course");
$hasUpdate = $accessObject->hasAccess("update", "course");

$hasFiltering = $accessObject->hasAccess("filters", "settings");

$courses = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a title='Click to view the course record' href='#' onclick='return loadPage(\"{$baseUrl}update-course/{$each->id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to update course record' href='#' onclick='return loadPage(\"{$baseUrl}update-course/{$each->id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Course' onclick='return delete_record(\"{$each->id}\", \"course\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $courses .= "<tr data-row_id=\"{$each->id}\">";
    $courses .= "<td>".($key+1)."</td>";
    $courses .= "<td>&nbsp; {$each->name}</td>";
    $courses .= "<td>{$each->course_code}</td>";
    $courses .= "<td>{$each->credit_hours}</td><td>";
    
    foreach($each->class_list as $class) {
        $courses .= "<p class='mb-0 pb-0'><a href='#' onclick='return loadPage(\"{$baseUrl}update-class/{$class->id}/view\");'><span class='underline'>".$class->name."</span></a></p>";
    }

    $courses .= "</td><td>";

    foreach($each->course_tutors as $tutor) {
        $courses .= "<p class='mb-0 pb-0'><a href='#' onclick='return loadPage(\"{$baseUrl}update-staff/{$tutor->item_id}/view\");'><span class='underline'>".$tutor->name."</span></a></p>";
    }

    $courses .= "</td><td class='text-center'>{$action}</td>";
    $courses .= "</tr>";
}

// default class_list
$class_list = [];
// if the class_id is not empty
if(!empty($filter->department_id)) {
    $classes_param = (object) [
        "clientId" => $clientId,
        "columns" => "id, name",
        "department_id" => $filter->department_id
    ];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Courses List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Courses List</div>
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
                <label>Select Course Tutor</label>
                <select class="form-control selectpicker" name="course_tutor">
                    <option value="">Please Select Tutor</option>';
                    foreach($myClass->pushQuery("item_id, name, unique_id", "users", "user_type IN ('teacher') AND user_status='Active' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->course_tutor) && ($filter->course_tutor == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                    }
                $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 '.(!$hasFiltering ? 'hidden': '').' col-md-2 col-12 form-group">
                <label for="">&nbsp;</label>
                <button id="filter_Courses_List" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Course Title</th>
                                        <th>Course Code</th>
                                        <th>Credit Hours</th>
                                        <th width="15%">Classes</th>
                                        <th>Course Tutor</th>
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