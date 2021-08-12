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
$filter = (object) $_POST;

$response->title = "Staff Payroll List : {$appName}";
$response->scripts = ["assets/js/filters.js"];

$filter->user_type = !empty($filter->user_type) ? $filter->user_type : "employee,teacher,admin,accountant";

$clientId = $session->clientId;

// access permissions check
if(!$accessObject->hasAccess("modify_payroll", "payslip")) {
    $response->html = page_not_found("permission_denied");
} else {

    $staff_list = "";

    $staff_param = (object) [
        "user_payroll" => true,
        "clientId" => $session->clientId,
        "user_type" => $filter->user_type,
        "department_id" => $filter->department_id ?? null,
        "gender" => $filter->gender ?? null,
        "client_data" => $defaultUser->client
    ];

    $api_staff_list = load_class("users", "controllers", $staff_param)->list($staff_param);


    $color = [
        "admin" => "success",
        "employee" => "primary",
        "accountant" => "danger",
        "teacher" => "warning"
    ];

    $basic_salary = 0;
    $allowances = 0;
    $deductions = 0;
    $net_salary = 0;

    foreach($api_staff_list["data"] as $key => $each) {
        
        $basic_salary += $each->basic_salary;
        $allowances += $each->allowances;
        $deductions += $each->deductions;
        $net_salary += $each->net_salary;

        $action = "<a href='#' onclick='loadPage(\"{$baseUrl}payroll-view/{$each->user_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

        $staff_list .= "<tr data-row_id=\"{$each->user_id}\">";
        $staff_list .= "<td>".($key+1)."</td>";
        $staff_list .= "<td>
            <div class='d-flex justify-content-start'>
                <div class='mr-2'><img class='rounded-circle author-box-picture' width='40px' src=\"{$baseUrl}{$each->image}\"></div>
                <div><a href='#' onclick='return loadPage(\"{$baseUrl}payroll-view/{$each->user_id}\");'>{$each->name}</a> <br><span class='text-uppercase badge badge-{$color[$each->user_type]} p-2'>{$each->user_type}</span></div>
            </div></td>";
        $staff_list .= "<td>{$each->position}</td>";
        $staff_list .= "<td>{$each->enrollment_date}</td>";
        $staff_list .= "<td>{$each->basic_salary}</td>";
        $staff_list .= "<td>{$each->allowances}</td>";
        $staff_list .= "<td>{$each->deductions}</td>";
        $staff_list .= "<td>{$each->net_salary}</td>";
        $staff_list .= "<td class='text-center'>{$action}</td>";
        $staff_list .= "</tr>";
    }

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>Staff Payroll List</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Payroll List</div>
                </div>
            </div>
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
                    <label for="">&nbsp;</label>
                    <button id="filter_Staff_Payroll_List" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
                </div>
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive table-student_staff_list">
                                <table data-empty="" class="table table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th>Staff Name</th>
                                            <th>Staff Role</th>
                                            <th>Appointment Date</th>
                                            <th>Basic Salary</th>
                                            <th>Allowances</th>
                                            <th>Deductions</th>
                                            <th>Net Salary</th>
                                            <th width="5%" align="center"></th>
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
}      
// print out the response
echo json_encode($response);
?>