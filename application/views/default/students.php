<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultAcademics, $defaultCurrency;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$filter = (object) $_POST;

$response->title = "Students List : {$appName}";
$response->scripts = ["assets/js/filters.js"];

$clientId = $session->clientId;

$student_param = (object) [
    "clientId" => $clientId,
    "user_type" => "student",
    "userId" => $session->userId, 
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "gender" => $filter->gender ?? null,
    "client_data" => $defaultUser->client,
    "academic_year" => $defaultAcademics->academic_year,
    "academic_term" => $defaultAcademics->academic_term,
];

// if the current user is a parent then append this query
if($defaultUser->user_type === "parent") {
    $student_param->userId = $defaultUser->user_id;
    $student_param->only_wards_list = true;
}

$student_list = load_class("users", "controllers", $student_param)->quick_list($student_param);

$hasDelete = $accessObject->hasAccess("delete", "student");
$hasUpdate = $accessObject->hasAccess("update", "student");
$hasFiltering = $accessObject->hasAccess("filters", "settings");

$students = "";
foreach($student_list["data"] as $key => $each) {
    
    $action = "<span title='View Record' onclick='load(\"student/{$each->user_id}\");' class='btn mb-1 btn-sm btn-outline-primary'><i class='fa fa-eye'></i></span>";

    if($hasUpdate) {
        $action .= "&nbsp;<span title='Update Record' onclick='load(\"modify-student/{$each->user_id}\");' class='btn mb-1 btn-sm btn-outline-success'><i class='fa fa-edit'></i></span>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<span title='Delete Student' onclick='delete_record(\"{$each->user_id}\", \"user\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></span>";
    }

    $students .= "<tr data-row_id=\"{$each->id}\">";
    $students .= "<td>".($key+1)."</td>";
    $students .= "
    <td>
        <span title='View Details' class='user_name' onclick='load(\"student/{$each->user_id}\");'>{$each->name}</span><br>{$each->unique_id}
    </td>";
    $students .= "<td>{$each->class_name}</td>";
    $students .= "<td>{$each->gender}</td>";
    $students .= "<td>".($each->department_name ?? null)."</td>";
    $students .= "<td>{$defaultCurrency} {$each->debt_formated}</td>";
    $students .= "<td>{$defaultCurrency} {$each->arrears_formated}</td>";
    $students .= "<td align='center'>{$action}</td>";
    $students .= "</tr>";
}

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "id, name"
];
// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-users"></i> Students List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Students</div>
            </div>
        </div>
        <div class="row" id="filter_Department_Class">
            <div class="col-xl-4 '.(!$hasFiltering ? 'hidden': '').' col-md-4 col-12 form-group">
                <label>Select Department</label>
                <select data-width="100%" class="form-control selectpicker" id="department_id" name="department_id">
                    <option value="">Please Select Department</option>';
                    foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Class</label>
                <select data-width="100%" class="form-control selectpicker" name="class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {
                        $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Gender</label>
                <select data-width="100%" class="form-control selectpicker" name="gender">
                    <option value="">Please Select Gender</option>';
                    foreach($myClass->pushQuery("*", "users_gender") as $each) {
                        $response->html .= "<option ".(isset($filter->gender) && ($filter->gender == $each->name) ? "selected" : "")." value=\"{$each->name}\">{$each->name}</option>";                            
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 '.(!$hasFiltering ? 'hidden': '').' col-md-2 col-12 form-group">
                <label for="">&nbsp;</label>
                <button id="filter_Students_List" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Gender</th>
                                        <th>Department</th>
                                        <th>Bill</th>
                                        <th>Arrears</th>
                                        <th width="13%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$students.'</tbody>
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