<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultAcademics, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);

$response->title = "Students List";
$response->scripts = ["assets/js/filters.js"];

$clientId = $session->clientId;

$student_param = (object) [
    "clientId" => $clientId,
    "user_type" => "student",
    "userId" => $session->userId, 
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "gender" => $filter->gender ?? null,
    "user_status" => $filter->user_status ?? null,
    "client_data" => $defaultUser->client,
    "academic_year" => $defaultAcademics->academic_year,
    "academic_term" => $defaultAcademics->academic_term,
    "_user_type" => $defaultUser->user_type
];

// if the current user is a parent then append this query
if($defaultUser->user_type === "parent") {
    $student_param->userId = $defaultUser->user_id;
    $student_param->only_wards_list = true;
}

$student_list = load_class("users", "controllers", $student_param)->quick_list($student_param);

// initial permissions
$hasDelete = $accessObject->hasAccess("delete", "student");
$hasUpdate = $accessObject->hasAccess("update", "student");
$hasFiltering = $accessObject->hasAccess("filters", "settings");
$viewAllocation = $accessObject->hasAccess("view_allocation", "fees");

$count = 0;
$students = "";
foreach($student_list["data"] as $key => $each) {
    
    $action = "<span title='View Record' onclick='load(\"student/{$each->user_id}\");' class='btn mb-1 btn-sm btn-outline-primary'><i class='fa fa-eye'></i></span>";

    // if the student status is active
    if( in_array($each->user_status, ["Active"]) ) {
        // if the user has permission to update student information
        if($hasUpdate) {
            $action .= "&nbsp;<span title='Update Record' onclick='load(\"modify-student/{$each->user_id}\");' class='btn mb-1 btn-sm btn-outline-success'><i class='fa fa-edit'></i></span>";
        }
        if($viewAllocation) {
            $action .= "&nbsp;<a href='{$baseUrl}download/student_bill/{$each->user_id}' target='_blank' title='Print Bill' class='btn btn-sm mb-1 btn-outline-warning'><i class='fa fa-print'></i></a>";
        }
        // if the user has permission to delete student information
        if($hasDelete) {
            $action .= "&nbsp;<span title='Delete Student' onclick='return delete_record(\"{$each->user_id}\", \"user\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></span>";
        }
    }

    // set the status
    $t_status = in_array($each->user_status, ["Active"]) ? null : "<br>{$each->the_status_label}";
    $scholarship_status = $each->scholarship_status == 1 ? "<br><span class='badge p-1 badge-success'>Full Scholarship</span>" : null;
    
    $imageToUse = "<img src=\"{$baseUrl}{$each->image}\" class='rounded-2xl cursor author-box-picture' width='50px' height='50px'>";
    if($each->image == "assets/img/avatar.png") {
        $imageToUse = "
        <div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-user h-6 w-6 text-white' aria-hidden='true'><path d='M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2'></path><circle cx='12' cy='7' r='4'></circle></svg>
        </div>";
    }

    $count++;
    $students .= "<tr data-row_id=\"{$each->user_id}\">";
    $students .= "
    <td class='text-center'>{$count}</td>
    <td>
        <div class='flex items-center space-x-4'>
            {$imageToUse}
            <div>
                <span title='View Details' class='user_name' onclick='load(\"student/{$each->user_id}\");'>{$each->name}</span><br>
                {$each->unique_id}{$t_status}{$scholarship_status}
            </div>
        </div>
    </td>";
    $students .= "<td><a class='user_name' href='{$baseUrl}class/{$each->class_id}'>".(!empty($each->class_name) ? ucwords(strtolower($each->class_name)) : 'N/A')."</a></td>";
    $students .= "<td>".(!empty($each->gender) ? ucwords(strtolower($each->gender)) : 'N/A')."</td>";
    $students .= "<td>".(!empty($each->department_name) ? ucwords(strtolower($each->department_name)) : 'N/A')."</td>";

    // if the user has permission to view the student fees allocation
    if($viewAllocation) {
        $students .= "<td class='text-center'>{$defaultCurrency}{$each->debt_formated}</td>";
        $students .= "<td class='text-center'>{$defaultCurrency}{$each->arrears_formated}</td>";
        $students .= "<td class='text-center'>{$defaultCurrency}{$each->total_debt_formated}</td>";
    } else {
        $students .= "<td>{$each->email}</td>";
    }
    $students .= "<td class='text-center'>{$action}</td>";
    $students .= "</tr>";
}

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "a.id, a.name"
];
// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

// if the user is not an admin or accountant
if(!$isAdminAccountant) {
    // unset the page additional information
    $response->page_programming = [];
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-users"></i> Students List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Students</div>
            </div>
        </div>';

        // if the term has ended
        if($isAdminAccountant) {
            $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
        }

        $response->html .= '
        <div class="row" id="filter_Department_Class">
            <div class="col-xl-3 col-md-4 mb-2 form-group">
                <label>Select Department</label>
                <select data-width="100%" class="form-control selectpicker" id="department_id" name="department_id">
                    <option value="">Please Select Department</option>';
                    foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 col-md-4 mb-2 form-group">
                <label>Select Class</label>
                <select data-width="100%" class="form-control selectpicker" name="class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {
                        $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div '.(!$isAdminAccountant ? 'class="col-xl-3 col-md-4 mb-2 form-group"' : 'class="col-xl-2 col-md-4 mb-2 form-group"').'>
                <label>Select Gender</label>
                <select data-width="100%" class="form-control selectpicker" name="gender">
                    <option value="">Please Select Gender</option>';
                    foreach($myClass->pushQuery("*", "users_gender") as $each) {
                        $response->html .= "<option ".(isset($filter->gender) && ($filter->gender == $each->name) ? "selected" : "")." value=\"{$each->name}\">".strtoupper($each->name)."</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div '.(!$isAdminAccountant ? 'hidden' : 'class="col-xl-2 col-md-3 mb-2 form-group"').'>
                <label>Status</label>
                <select data-width="100%" class="form-control selectpicker" name="user_status">
                    <option value="">Please Select Status</option>';
                    foreach($myClass->student_statuses as $status) {
                        $response->html .= "<option ".(isset($filter->user_status) && ($filter->user_status == $status) ? "selected" : "")." value=\"{$status}\">".strtoupper($status)."</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 col-md-3 form-group">
                <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                <button id="filter_Students_List" type="submit" class="btn btn-outline-warning height-40 btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card rounded-2xl">
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Gender</th>
                                        <th>Department</th>
                                        '.($viewAllocation ? 
                                        '<th class="text-center">Term Bill</th>
                                        <th class="text-center">Arrears</th>
                                        <th class="text-center">Total</th>' : '<th>Email</th>').'
                                        <th width="18%"></th>
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