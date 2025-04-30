<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global variables
global $myClass, $accessObject, $defaultUser, $isPayableStaff;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);

$response->title = "Staff List";

// If the user is not a teacher, employee, accountant or admin then end the request
if(!$isPayableStaff) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// include the scripts to load for the page
$response->scripts = ["assets/js/filters.js"];

// add additional filters
$filter->user_type = !empty($filter->user_type) ? $filter->user_type : "employee,teacher,admin,accountant";

// set the parameters
$staff_param = (object) [
    "clientId" => $session->clientId,
    "user_type" => $filter->user_type,
    "department_id" => $filter->department_id ?? null,
    "gender" => $filter->gender ?? null,
    "client_data" => $defaultUser->client
];

$api_staff_list = load_class("users", "controllers", $staff_param)->quick_list($staff_param);

$clientId = $session->clientId;

$staff_list = "";
$counter = 0;

// check if the user has permission to change the password
$canChangePassword = $accessObject->hasAccess("change_password", "permissions");

// loop through the staff list
foreach($api_staff_list["data"] as $i => $each) {
    
    $counter++;
    $userName = ucwords(strtolower($each->name));
    $action = "<span title='View staff record' onclick='return load(\"staff/{$each->user_id}/documents\");' class='btn mb-1 btn-sm btn-outline-primary'><i class='fa fa-eye'></i></span>";

    if($accessObject->hasAccess("update", $each->user_type)) {
        $action .= "&nbsp;<a title='Update Staff Record' href=\"staff/{$each->user_id}/update\" class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($canChangePassword) {
        $action .= "&nbsp;<button title='Reset User Password' onclick=\"return modal_popup('reset_password_mod', '{$each->user_id}', 'Reset Password - {$userName}', {$counter})\" 
            class=\"btn btn-sm mb-1 btn-outline-warning\"><i class=\"fa fa-lock\"></i></button>";
    }

    if($accessObject->hasAccess("delete", $each->user_type) && ($each->user_id !== $defaultUser->user_id)) {
        $action .= "&nbsp;<span title='Delete Staff Record' onclick='return delete_record(\"{$each->user_id}\", \"user\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></span>";
    }


    $staff_list .= "<tr data-row_id=\"{$each->user_id}\">";
    $staff_list .= "<td>{$counter}</td>";
    $staff_list .= "<td>
        <div class='d-flex justify-content-start'>
            <div class='mr-2'><img class='author-box-picture' width='40px' src=\"{$baseUrl}{$each->image}\"></div>
            <div>
                <span class='user_name' onclick='return load(\"staff/{$each->user_id}/documents\");'>{$each->name}</span>
                <br><span class='badge badge-{$myClass->user_colors[$each->user_type]} p-1'>".strtoupper($each->user_type)."</span>
                <br><span class='font-17'>".strtoupper($each->unique_id)."</span>
            </div>
        </div></td>";
    $staff_list .= "<td>{$each->position}</td>";
    $staff_list .= "<td>{$each->gender}</td>";
    $staff_list .= "<td>{$each->date_of_birth}</td>";
    $staff_list .= "<td>{$each->enrollment_date}</td>";
    $staff_list .= "<td>{$each->department_name}</td>";
    $staff_list .= "<td align='center'>{$action}</td>";
    $staff_list .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-user-tie"></i> Staff List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Staff List</div>
            </div>
        </div>';
        // if the term has ended
        if($defaultUser->appPrefs->termEnded && $isAdminAccountant) {
            $response->html .= academic_term_ended_dashboard_modal($defaultAcademics, $baseUrl);
        }

        $response->html .= '
        <div class="row">
            <div class="col-xl-4 col-md-4 col-12 form-group">
                <label>Select Department</label>
                <select data-width="100%" class="form-control selectpicker" id="department_id" name="department_id">
                    <option value="">Please Select Department</option>';
                    foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 col-md-3 col-12 form-group">
                <label>Select Role</label>
                <select data-width="100%" class="form-control selectpicker" name="user_type">
                    <option value="">Please Select Role</option>';
                    foreach($myClass->user_roles_list as $key => $value) {
                        $response->html .= "<option ".(isset($filter->user_type) && ($filter->user_type == $key) ? "selected" : "")." value=\"{$key}\">{$value}</option>";                            
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 col-md-3 col-12 form-group">
                <label>Select Gender</label>
                <select data-width="100%" class="form-control selectpicker" name="gender">
                    <option value="">Please Select Gender</option>';
                    foreach($myClass->pushQuery("*", "users_gender") as $each) {
                        $response->html .= "<option ".(isset($filter->gender) && ($filter->gender == $each->name) ? "selected" : "")." value=\"{$each->name}\">{$each->name}</option>";                            
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 col-md-2 col-12 form-group">
                <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                <button id="filter_Staff_List" type="submit" class="btn btn-outline-warning height-40 btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Staff Name</th>
                                        <th>Staff Role</th>
                                        <th>Gender</th>
                                        <th>Date of Birth</th>
                                        <th>Date Employed</th>
                                        <th>Department</th>
                                        <th width="13%" align="center"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$staff_list.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';

    // append the reset password modal
    $response->html .= $canChangePassword ? reset_password_modal() : null;

// print out the response
echo json_encode($response);
?>