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

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$filter = (object) $_POST;

$response->title = "Staff Payslips: {$appName}";
$response->scripts = ["assets/js/filters.js"];

$userId = $session->userId;
$clientId = $session->clientId;

$generatePermission = $accessObject->hasAccess("generate", "payslip");
$validatePayslip = $accessObject->hasAccess("validate", "payslip");

$staff_param = (object) ["clientId" => $clientId, "client_data" => $defaultClientData];
$payslips_list = load_class("payroll", "controllers", $staff_param)->paysliplist($staff_param);

$staff_list = "";

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

foreach($payslips_list["data"] as $key => $each) {
    
    $basic_salary += $each->basic_salary;
    $allowances += $each->total_allowance;
    $deductions += $each->total_deductions;
    $net_salary += $each->net_salary;
    
    $action = "";
    $validated = true;

    // if the payslip has not yet been validated
    if(!$each->validated && !$each->status) {
        // if the user has the permission to validate a payslip
        if($validatePayslip) {
            $validated = false;
            $action .= "&nbsp;<a onclick='return validate_payslip(\"{$each->item_id}\",\"{$baseUrl}payslips\")' class=\"btn btn-sm btn-outline-success mb-1\" title=\"Validate Payslip\" href=\"#\"><i class='fa fa-check'></i></a>";
        }
    }
    
    $action .= "&nbsp; <a href=\"{$baseUrl}download/payslip?pay_id={$each->item_id}&dw=true\" target=\"_blank\" class=\"btn mb-1 btn-sm btn-outline-warning\"><i class='fa fa-download'></i></a>&nbsp; 
            <a href=\"{$baseUrl}download/payslip?pay_id={$each->item_id}\" target=\"_blank\" class=\"btn mb-1 btn-sm btn-outline-primary\"><i class='fa fa-print'></i> </a>";
    
    if($generatePermission && !$each->status) {
        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->item_id}\", \"payslip\");' class='btn mb-1 btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }
    // set the summary data
    $summary = "<div class='row'>";
    $summary .= "<div class='col-lg-6'><strong>Basic Salary</strong>:</div> <div class='col-lg-6'>GH&cent;".number_format($each->basic_salary, 2)."</div>";
    $summary .= "<div class='col-lg-6'><strong>Total Allowances</strong>:</div> <div class='col-lg-6'>GH&cent;".number_format($each->total_allowance, 2)."</div>";
    $summary .= "<div class='col-lg-6'><strong>Less Deductions</strong>:</div> <div class='col-lg-6'>GH&cent;".number_format($each->total_deductions, 2)."</div>";
    $summary .= "<div class='col-lg-12'><hr class='mb-1 mt-1'></div><div class='col-lg-6'><strong>Net Salary:</strong></div> <div class='col-lg-6'><strong>GH&cent;".number_format($each->net_salary, 2)."</strong></div>";
    $summary .= "</div>";

    //: Set the new status
	$status = ($each->status == 1) ? "<span class='badge badge-success'><i class=\"fa fa-check-circle\"></i> Paid</span>" : "<span class='badge badge-primary'><i class=\"fa fa-check-circle\"></i> Pending</span>";

    $staff_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $staff_list .= "<td>".($key+1)."</td>";
    $staff_list .= "
        <td>
            <div class='d-flex justify-content-start'>
                <div class='mr-2'>
                    <!--".(!$validated ? "<input name='selected' type='checkbox' value='{$each->id}' class='form-control cursor' style='height:20px'>" : "")."-->
                    <img class='rounded-circle author-box-picture' width='40px' src=\"{$baseUrl}{$each->employee_info->image}\"></div>
                <div>
                    <a title='Click to view the details of this employee' href='#' onclick='return loadPage(\"{$baseUrl}payroll-view/{$each->employee_id}\");'>{$each->employee_info->name}</a> 
                    <span class='text-uppercase badge badge-{$color[$each->employee_info->user_type]} p-1'>{$each->employee_info->user_type}</span>
                    <br><span class='p-2'><i class='fa fa-phone'></i> {$each->employee_info->phone_number}</span>
                    <br><span class='p-2'><i class='fa fa-envelope'></i> {$each->employee_info->email}</span>
                </div>
            </div>
        </td>";
    $staff_list .= "<td>{$each->payslip_month} {$each->payslip_year}</td>";
    $staff_list .= "<td>{$summary}</td>";
    $staff_list .= "<td>{$status}</td>";
    $staff_list .= "<td class='text-center'>{$action}</td>";
    $staff_list .= "</tr>";
    
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Staff Payslips</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Payslips</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                    <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'payslip-generate"><i class="fa fa-plus"></i> Generate Payslip</a>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Staff Name</th>
                                        <th width="13%">Month / Year</th>
                                        <th width="30%">Summary</th>
                                        <th width="10%">Status</th>
                                        <th width="14%" align="center"></th>
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
    
// print out the response
echo json_encode($response);
?>