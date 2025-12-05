<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultClientData, $isPayableStaff, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);

$response->title = "Staff Payslips: {$appName}";

// end query if the user has no permissions
if(!in_array("payroll", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["payroll"]);
    echo json_encode($response);
    exit;
}

// set the parent menu
$response->parent_menu = "payroll";

$userId = $session->userId;
$clientId = $session->clientId;

// If the user is not a teacher, employee, accountant or admin then end the request
if(!$isPayableStaff) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

$response->scripts = ["assets/js/filters.js", "assets/js/payroll.js"];

$generatePermission = $accessObject->hasAccess("generate", "payslip");
$validatePayslip = $accessObject->hasAccess("validate", "payslip");

$staff_param = (object) ["clientId" => $clientId, "client_data" => $defaultClientData];
$payslips_array = load_class("payroll", "controllers", $staff_param)->paysliplist($staff_param);

$payslips_list = "";

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

$not_validated = [];

foreach($payslips_array["data"] as $key => $each) {
    
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
            $not_validated[] = [
                'id' => $each->id,
                'unique_id' => $each->emp_unique_id,
                'user' => $each->emp_user_type,
                'item_id' => $each->employee_id,
                'period' => $each->payslip_month . " / " . $each->payslip_year,
                'salary' => [
                    'basic' => $each->basic_salary,
                    'gross' => $each->gross_salary,
                    'allowances' => $each->total_allowance,
                    'deductions' => $each->total_deductions,
                    'net' => $each->net_salary
                ],
                'employee' => [
                    'id' => $each->employee_id,
                    'name' => $each->emp_name,
                    'phone' => $each->emp_phone,
                    'email' => $each->emp_email,
                    'image' => $each->emp_image,
                    'unique_id' => $each->emp_unique_id,
                    'user_type' => $each->emp_user_type
                ]
            ];
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
    $summary .= "<div class='col-lg-6'><strong>Total Earnings</strong>:</div> <div class='col-lg-6'>GH&cent;".number_format($each->total_allowance, 2)."</div>";
    $summary .= "<div class='col-lg-6'><strong>Less Deductions</strong>:</div> <div class='col-lg-6'>GH&cent;".number_format($each->total_deductions, 2)."</div>";
    $summary .= "<div class='col-lg-12'><hr class='mb-1 mt-1'></div><div class='col-lg-6'><strong>Net Salary:</strong></div> <div class='col-lg-6'><strong>GH&cent;".number_format($each->net_salary, 2)."</strong></div>";
    $summary .= "</div>";

    //: Set the new status
	$status = ($each->status == 1) ? "<span class='badge badge-success'><i class=\"fa fa-check-circle\"></i> Paid</span>" : "<span class='badge badge-primary'><i class=\"fa fa-check-circle\"></i> Pending</span>";

    $payslips_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $payslips_list .= "<td>".($key+1)."</td>";
    $payslips_list .= "
        <td>
            <div class='d-flex justify-content-start'>
                <div class='mr-2'>
                    <img class='rounded-2xl author-box-picture' width='40px' src=\"{$baseUrl}{$each->employee_info->image}\"></div>
                <div>
                    <a class='text-uppercase' title='Click to view the details of this employee' href='#' onclick='return load(\"payroll-view/{$each->employee_id}\");'>{$each->employee_info->name}</a> 
                    <br><span class='text-uppercase badge badge-{$color[$each->employee_info->user_type]} p-1'>{$each->employee_info->user_type}</span>
                    <br><span class='pl-0'><i class='fa fa-phone'></i> ".($each->employee_info?->phone_number ?? "N/A")."</span>
                    <br><span class='pl-0'><i class='fa fa-envelope'></i> ".($each->employee_info?->email ?? "N/A")."</span>
                </div>
            </div>
        </td>";
    $payslips_list .= "<td>{$each->payslip_month} {$each->payslip_year}</td>";
    $payslips_list .= "<td>{$summary}</td>";
    $payslips_list .= "<td>{$status}</td>";
    $payslips_list .= "<td class='text-center'>{$action}</td>";
    $payslips_list .= "</tr>";
    
}

$response->array_stream['not_validated'] = $not_validated;

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
                    '.($generatePermission ? '
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'payslip-generate"><i class="fa fa-plus"></i> Generate Payslip</a>
                        <a class="btn btn-sm btn-outline-success" href="'.$baseUrl.'payslip-bulkgenerate"><i class="fa fa-users"></i> Generate Multiple Payslip</a>
                    ' : null).'
                    '.($validatePayslip ? '
                        <a class="btn btn-sm btn-warning" onclick="return bulk_validate_payslip()"><i class="fa fa-check"></i> Validate Payslips</a>
                    ' : null).'
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped '.($payslips_list ? "datatable" : "raw_datatable").'">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Staff Name</th>
                                        <th width="13%">Month / Year</th>
                                        <th width="30%">Summary</th>
                                        <th width="10%">Status</th>
                                        <th width="16%" align="center"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$payslips_list.'</tbody>
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