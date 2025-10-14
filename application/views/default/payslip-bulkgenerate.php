<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$userId = $SITEURL[1] ?? $session->userId;

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Payslip Bulk Generation";
$response->title = $pageTitle;
$response->scripts = ["assets/js/payroll.js"];

// end query if the user has no permissions
if(!in_array("payroll", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["payroll"]);
    echo json_encode($response);
    exit;
}

// access permissions check
if(!$accessObject->hasAccess("generate", "payslip")) {
    $response->html = page_not_found("permission_denied");
} else {

    // filter
    $filter = (object) array_map("xss_clean", $_GET);
    $selectedYear = $filter->bulk_year_id ?? date("Y");
    $selectedMonth = $filter->bulk_month_id ?? date("F");

    // limit
    $limit = 1000;

    // confirm if the account check is empty
    if(empty($defaultClientData->default_account_id)) {
        // limit
        $limit = 0;
        // message to share
        $payslip_form = notification_modal("Payment Account Not Set", $myClass->error_logs["account_not_set"]["msg"], $myClass->error_logs["account_not_set"]["link"]);
    }

    // get the users
    $stmt = $myClass->db->prepare("
        SELECT u.item_id, u.name, u.unique_id, 
            up.gross_salary, up.net_allowance, up.allowances, up.deductions, up.net_salary, up.basic_salary,
			up.account_name, up.account_number, up.bank_name, up.bank_branch, up.ssnit_number, up.tin_number
        FROM users u
        LEFT JOIN payslips_employees_payroll up ON up.employee_id = u.item_id
        WHERE u.client_id = ? AND u.status = ? AND u.user_type NOT IN('parent','student') AND u.user_status IN ('Active')
        ORDER BY name LIMIT {$limit}
    ");
    $stmt->execute([$clientId, 1]);
    $users_array_list = $stmt->fetchAll(PDO::FETCH_OBJ);

    $users_list = "";

    // user payload for the salary information
    $userPayload = (object)[
        'month_id' => $selectedMonth,
        'year_id' => $selectedYear,
        'clientId' => $clientId,
        'simple_data' => true,
        'limit' => 1,
    ];

    // payroll object
    $payrollObject = load_class("payroll", "controllers");

    // loop through the users
    foreach($users_array_list as $each) {

        // set the user payload
        $basic_salary = !empty($each->basic_salary) ? $each->basic_salary : 0;
        $allowances = !empty($each->allowances) ? $each->allowances : 0;
        $deductions = !empty($each->deductions) ? $each->deductions : 0;

        $userPayload->employee_id = $each->item_id;
        $salaryInfo = $payrollObject->paysliplist($userPayload)["data"] ?? [];

        $validatedSalary = false;
        $salaryCreated = false;
        $isDisabled = false;

        $row_id = "data-staff_id='{$each->item_id}'";
        $append_color = "";
        $inputValue = "value='{$each->item_id}'";
        $row_class = empty($each->basic_salary) ? "text-white bg-danger-light" : "";

        // if the salary info is not empty
        if(!empty($salaryInfo)) {
            $validatedSalary = $salaryInfo[0]->validated;
            $salaryCreated = $salaryInfo[0]->date_log;

            if($validatedSalary) {
                $inputValue = "";
                $row_id .= " title='Payslip Created on {$salaryCreated} and validated on {$salaryInfo[0]->validated_date}'";
                $append_color = "text-white";
                $row_class = "text-white bg-success";
                $isDisabled = "disabled checked";
            }
        }

        $users_list .= "
        <tr {$row_id} class='{$row_class}'>
            <td>
                <div style='padding-left: 2.5rem;' class='custom-control cursor col-lg-12 custom-switch switch-primary'>
                    <input {$isDisabled} data-item='staff_checkbox' data-user_name='{$each->name}' type='checkbox' value='{$each->item_id}' name='user_ids[]' class='custom-control-input cursor' id='user_id_{$each->item_id}' checked='checked'>
                    <label class='custom-control-label {$append_color} cursor' for='user_id_{$each->item_id}'>{$each->name} 
                        <br><strong>{$each->unique_id}</strong>
                    </label>
                </div>
            </td>
            <td>
                <input type='text' {$isDisabled} {$row_id} name='basic_salary' value='{$basic_salary}' class='form-control text-center font-20 w-[150px]'>
            </td>
            <td class='text-center font-18'>
                <span class='allowances'>{$allowances}</span>
            </td>
            <td class='text-center font-18'>
                <span class='deductions'>{$deductions}</span>
            </td>
            <td class='text-center font-20'>
                <span class='net_salary'>".(!empty($each->net_salary) ? number_format($each->net_salary, 2) : "0.00")."</span>
            </td>
        </tr>";
    }

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'payslips">List Payslips</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12" id="payslip_container">

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <label for="bulk_year_id">Select Year</label>
                                    <select name="bulk_year_id" data-width="100%" class="form-control selectpicker">
                                        <option value="">Please Select </option>';
                                        for($i = date("Y") - 2; $i < date("Y") + 2; $i++) {
                                            $response->html .= "<option ".(($i == $selectedYear) ? "selected" : "")." value=\"{$i}\">Year - {$i}</option>";                            
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <label for="bulk_month_id">Select Month</label>
                                    <select name="bulk_month_id" data-width="100%" class="form-control selectpicker">
                                        <option value="">Please Select </option>';
                                        for($i = 0; $i < 12; $i++) {
                                            $month = date("F", strtotime("December +{$i} month 3 day"));
                                            $response->html .= "<option ".(($month == $selectedMonth) ? "selected" : "")." value=\"{$month}\">{$month}</option>";                            
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label for="reload_employee_payslips">&nbsp;</label>
                                        <button onclick="return reload_employee_payslips()" id="reload_employee_payslips" class="btn-block btn btn-outline-success">Load Record</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive table-student_staff_list">
                                <table data-empty="" class="table table-bordered table-md table-striped">
                                    <thead>
                                        <tr class="font-17">
                                            <th width="30%">Staff Name</th>
                                            <th>Basic Salary</th>
                                            <th class="text-center">Allowances</th>
                                            <th class="text-center">Deductions</th>
                                            <th class="text-center">Net Salary</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        '.$users_list.'
                                        <tr id="total_row" class="text-white bg-success font-25 font-bold">
                                            <td></td>
                                            <td>
                                                <span class="total_net_salary">0</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="total_allowances">0</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="total_deductions">0</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="total_net_salary">0</span>
                                            </td>   
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-1">
                                <button onclick="return generate_payslips()" id="generate_payslips" class="btn py-3 font-17 btn-outline-success">
                                    <i class="fas fa-project-diagram"></i> Generate Multiple Payslips
                                </button>
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