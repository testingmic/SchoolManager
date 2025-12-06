<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Staff Payroll Details";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!in_array("payroll", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["payroll"]);
    echo json_encode($response);
    exit;
}

// set the parent menu
$response->parent_menu = "payroll";

// access permissions check
if(!$accessObject->hasAccess("modify_payroll", "payslip")) {
    $response->html = page_not_found("permission_denied");
} else {
    // staff id
    $userId = $SITEURL[1] ?? $session->userId;
    $pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

    // if the user id is not empty
    if(!empty($userId)) {

        $staff_param = (object) [
            "clientId" => $clientId,
            "user_id" => $userId,
            "limit" => 1,
            "user_payroll" => true,
            "no_limit" => 1,
            "client_data" => $defaultUser->client
        ];

        $data = load_class("users", "controllers", $staff_param)->list($staff_param);
        $taxCalculator = load_class("taxcalculator", "controllers");

        // if no record was found
        if(empty($data["data"])) {
            $response->html = page_not_found();
        } else {

            $response->array_stream['url_link'] = "payroll-view/{$userId}/";

            // set the first key
            $data = $data["data"][0];

            // leave days
            $leave_days = $data->leave_days ?? 0;

            // expected days to be present in school
            $expected_days = $myClass->stringToArray($data->expected_days);

            // set the days of the week
            $daysOfWeek = $myClass->days_of_week;

            // user id
            $user_id = $userId;

            $response->scripts = ["assets/js/payroll.js"];

            // if the request is to view the student information
            $updateItem = confirm_url_id(2, "update") ? true : false;

            $payroll_form = load_class("forms", "controllers")->payroll_form($clientId, $userId, $data);

            // get the list of employee payslips
            $payslips = "";

            $staff_param = (object) ["clientId" => $clientId, "employee_id" => $userId, "client_data" => $defaultUser->client];
            $payslips_list = load_class("payroll", "controllers", $staff_param)->paysliplist($staff_param)["data"];
            
            $response->array_stream["dataset"] = $payroll_form["dataset"];

            // if the payslip information is not empty
            if(!empty($payslips_list)) {
                $basic_salary = 0;
                $allowances = 0;
                $deductions = 0;
                $net_salary = 0;

                foreach($payslips_list as $key => $each) {
                    
                    $basic_salary += $each->basic_salary;
                    $allowances += $each->total_allowance;
                    $deductions += $each->total_deductions;
                    $net_salary += $each->net_salary;

                    $action = "&nbsp; <a href=\"{$baseUrl}download?pay_id={$each->item_id}&dw=true\" target=\"_blank\" class=\"btn mb-1 btn-sm btn-outline-warning\"><i class='fa fa-download'></i></a>&nbsp; 
                        <a href=\"{$baseUrl}download?pay_id={$each->item_id}\" target=\"_blank\" class=\"btn mb-1 btn-sm btn-outline-primary\"><i class='fa fa-print'></i> </a>";
                    
                    // set the summary data
                    $summary = "<div class='row'>";
                    $summary .= "<div class='col-lg-6'><strong>Basic Salary</strong>:</div> <div class='col-lg-6'>GH&cent;".number_format($each->basic_salary, 2)."</div>";
                    $summary .= "<div class='col-lg-6'><strong>Total Earnings</strong>:</div> <div class='col-lg-6'>GH&cent;".number_format($each->total_allowance, 2)."</div>";
                    $summary .= "<div class='col-lg-6'><strong>Less Deductions</strong>:</div> <div class='col-lg-6'>GH&cent;".number_format($each->total_deductions, 2)."</div>";
                    $summary .= "<div class='col-lg-12'><hr class='mb-0 mt-0'></div><div class='col-lg-6'><strong>Net Salary:</strong></div> <div class='col-lg-6'><strong>GH&cent;".number_format($each->net_salary, 2)."</strong></div>";
                    $summary .= "</div>";

                    //: Set the new status
                    $status = ($each->status == 1) ? "<span class='badge badge-success'><i class=\"fa fa-check-circle\"></i> Paid</span>" : "<span class='badge badge-primary'><i class=\"fa fa-check-circle\"></i> Pending</span>";

                    $payslips .= "<tr data-row_id=\"{$each->id}\">";
                    $payslips .= "<td>".($key+1)."</td>";
                    $payslips .= "<td>{$each->payslip_month} {$each->payslip_year} <br>{$status}</td>";
                    $payslips .= "<td width=\"50%\">{$summary}</td>";
                    $payslips .= "<td width=\"15%\">{$action}</td>";
                    $payslips .= "</tr>";
                    
                }
            }

            $allowancesList = [];
            foreach($data->_allowances as $each) {
                $allowancesList[$each->name] = $each->amount;
            }

            $deductionsList = [];
            $taxRatings = [];
            // loop through the deductions
            foreach($data->_deductions as $each) {
                // set the deductions list
                $deductionsList[$each->name] = $each->amount;

                // set the tax ratings
                if($each->name == "SSNIT") {
                    $taxRatings["tier1"] = $each->calculation_value;
                }

                // set the tax ratings
                if($each->name == "TIER 2") {
                    $taxRatings["tier2"] = $each->calculation_value;
                }

                if($each->name == "PAYE") {
                    $taxRatings["paye"] = 'calculate';
                }
            }

            $salaryCalculation = $taxCalculator->calculateWithPensions($data->basic_salary, 0, $allowancesList, $taxRatings);

            $response->array_stream["salary_calculation"] = $salaryCalculation;

            // user  permission information
            $level_data = "<div class='row'>";

            // append the html content
            $response->html = '
            <section class="section">
                <div class="section-header">
                    <h1>'.$pageTitle.'</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                        <div class="breadcrumb-item active"><a href="'.$baseUrl.'payroll">Staff Payroll List</a></div>
                        <div class="breadcrumb-item">'.$pageTitle.'</div>
                    </div>
                </div>
                <div class="section-body">
                <div class="row mt-sm-4">
                    <div class="col-md-3">
                        <div class="card rounded-2xl hover:scale-105 transition-all duration-300">
                            <div class="card-body text-center bg-gradient-to-br from-amber-500 to-amber-300 rounded-2xl shadow-lg text-white card-type-3">
                                <div class="font-18 text-white">STAFF ID</div>
                                <div class="font-20 font-weight-bold text-uppercase text-white">'.$data->unique_id.'</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card rounded-2xl hover:scale-105 transition-all duration-300">
                            <div class="card-body text-center bg-gradient-to-br from-pink-500 to-pink-300 rounded-2xl shadow-lg text-white card-type-3">
                                <div class="font-18 text-white">APPOINTMENT DATE</div>
                                <div class="font-20 font-weight-bold text-uppercase text-white">
                                    '.($data->enrollment_date ? $data->enrollment_date : '-' ).'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card rounded-2xl hover:scale-105 transition-all duration-300">
                            <div class="card-body text-center bg-gradient-to-br pr-2 pl-2 from-blue-500 to-purple-600 rounded-2xl shadow-lg text-white card-type-3">
                                <div class="font-18 text-white">POSITION</div>
                                <div class="font-20 font-weight-bold text-uppercase text-white">'.($data->position ? $data->position : '-' ).'</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card rounded-2xl hover:scale-105 transition-all duration-300">
                            <div class="card-body text-center bg-gradient-to-br from-green-500 to-green-300 rounded-2xl shadow-lg text-white card-type-3">
                                <div class="font-18 text-white">DEPARTMENT</div>
                                <div class="font-20 font-weight-bold text-uppercase text-white">
                                    '.($data->section_name ? $data->section_name : '-' ).'
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4">
                        <div class="card author-box">
                        <div class="card-body">
                            <div class="author-box-center">
                                <div class="author-box-center m-0 p-0 flex justify-center">
                                    <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                                </div>
                                <div class="clearfix"></div>
                                <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                                '.($data->class_name ? '<div class="author-box-job">'.$data->class_name.'</div>' : '').'
                                '.($data->phone_number ? '<div class="author-box-job"><i class="fa fa-phone"></i> '.$data->phone_number.'</div>' : '').'
                                '.($data->email ? '<div class="author-box-job"><i class="fa fa-envelope"></i> '.$data->email.'</div>' : '').'
                                '.($data->residence ? '<div class="author-box-job"><i class="fa fa-globe"></i> '.$data->residence.'</div>' : '').'
                            </div>
                            <div class="w-100 mt-2 text-center border-top pt-3">
                                <a class="btn btn-dark" href="'.$baseUrl.'staff/'.$data->user_id.'/documents"><i class="fa fa-arrow-circle-left"></i> VIEW DETAILS</a>
                            </div>
                        </div>
                        </div>
                        <div class="card">
                            <div class="card-header pb-0">
                                <h4 class="pb-0">Salary Details</h4>
                            </div>
                            <div class="card-body pt-0 pb-0 mb-0">
                                <div class="py-4">
                                    <p class="clearfix font-weight-bold">
                                        <span class="float-left">Basic Salary</span>
                                        <span class="float-right text-muted">'.$salaryCalculation['basic_salary'].'</span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">Earnings</span>
                                        <span class="float-right text-muted">'.$salaryCalculation['total_allowances'].'</span>
                                    </p>
                                    <p class="clearfix font-weight-bold">
                                        <span class="float-left">Gross Salary</span>
                                        <span class="float-right text-muted">'.$salaryCalculation['gross_income'].'</span>
                                    </p>
                                    '.(in_array("PAYE", array_keys($deductionsList)) ? '
                                    <p class="clearfix">
                                        <span class="float-left">--- PAYE Tax</span>
                                        <span class="float-right text-muted">'.$salaryCalculation['paye_tax'].'</span>
                                    </p>' : '').'
                                    '.(in_array("SSNIT", array_keys($deductionsList)) ? '
                                    <p class="clearfix">
                                        <span class="float-left">--- Tier 1 Pension</span>
                                        <span class="float-right text-muted">'.$salaryCalculation['pensions']['tier1']['employee_contribution'].'</span>
                                    </p>' : '').'
                                    '.(in_array("TIER 2", array_keys($deductionsList)) ? '
                                    <p class="clearfix">
                                        <span class="float-left">--- Tier 2 Pension</span>
                                        <span class="float-right text-muted">'.$salaryCalculation['pensions']['tier2']['contribution'].'</span>
                                    </p>' : '').'
                                    <p class="clearfix">
                                        <span class="float-left">Deductions</span>
                                        <span class="float-right text-muted">'.$salaryCalculation['total_deductions'].'</span>
                                    </p>
                                    <p class="clearfix font-weight-bold">
                                        <span class="float-left">Net Salary</span>
                                        <span class="float-right text-muted">'.$salaryCalculation['net_income'].'</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <div class="card">
                            <div class="padding-20">
                                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" onclick="return appendToUrl(\'bank_details\')" id="bank_details-tab2" data-toggle="tab" href="#bank_details" role="tab" aria-selected="false">
                                            Bank Information
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="allowances-tab2" onclick="return appendToUrl(\'salary\')" data-toggle="tab" href="#allowances" role="tab" aria-selected="true">Salary Structure</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="payslips-tab2" onclick="return appendToUrl(\'payslips\')" data-toggle="tab" href="#payslips" role="tab" aria-selected="true">Payslips</a>
                                    </li>
                                </ul>
                                <div class="tab-content tab-bordered" id="myTab3Content">
                                    <div class="tab-pane fade show active" id="bank_details" role="tabpanel" aria-labelledby="bank_details-tab2">

                                        '.$payroll_form["bank_detail"].'
                                        
                                        <div class="row">
                                        '.div_labels("ADDITIONAL INFORMATION").'
                                            <div class="col-lg-6">
                                                <div class="font-14 text-uppercase">
                                                    <div class="font-14 text-uppercase mt-0 mb-2 font-weight-bold mb-0">EXPECTED DAYS</div>
                                                '.implode(" ", array_map(function($day) use ($expected_days, $user_id) {
                                                    return "
                                                        <div style='padding-left: 2.5rem;' class='custom-control cursor col-lg-12 custom-switch switch-primary'>
                                                            <input onchange='return update_expected_days(\"{$user_id}\", \"users\");' type='checkbox' name='expected_days[]' value='".ucfirst($day)."' class='custom-control-input cursor' id='".$day."' ".(in_array($day, $expected_days) ? "checked='checked'" : null).".>
                                                            <label class='custom-control-label cursor text-black' for='".$day."'>".$day."</label>
                                                        </div>";
                                                }, $daysOfWeek)).'
                                                </div>
                                            </div>
                                            <div class="col-lg-6">

                                                <div class="font-14">
                                                    <div class="font-14 mt-0 mb-2 font-weight-bold">ANNUAL LEAVE DAYS</div>
                                                    <select name="leave_days" onchange="return update_leave_days(\''.$user_id.'\', \'users\');" id="leave_days" class="form-control mt-2 selectpicker" data-width="100%">
                                                        <option value="">Select Leave Days</option>
                                                        '.implode(" ", array_map(function($day) use ($leave_days, $user_id) {
                                                            return "<option value='".$day."' ".($day == $leave_days ? "selected='selected'" : null).">".$day." Working Days</option>";
                                                        }, $myClass->leave_days)).'
                                                    </select>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="allowances" role="tabpanel" aria-labelledby="allowances-tab2">
                                        '.$payroll_form["allowance_detail"].'
                                    </div>
                                    <div class="tab-pane fade" id="payslips" role="tabpanel" aria-labelledby="payslips-tab2">
                                        <div class="d-flex justify-content-between">
                                            <div><h5>EMPLOYEE PAYSLIPS</h5></div>
                                            <div><a class="btn btn-outline-primary" href="'.$baseUrl.'payslip-generate/'.$userId.'"><i class="fa fa-plus"></i> Create Payslip</a></div>
                                        </div>
                                        <div class="mt-3 border-top table-responsive pt-3">
                                            <table width="100%" class="table table-bordered table-sm table-striped raw_datatable">
                                                <thead>
                                                    <tr>
                                                        <th width="5%" class="text-center">#</th>
                                                        <th>Month / Year</th>
                                                        <th>Summary</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>'.$payslips.'</tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </section>';
            
        }
    }
}
// print out the response
echo json_encode($response);
?>