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

$response->title = "SSNIT Contributions";

// end query if the user has no permissions
if(!in_array("payroll", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["payroll"]);
    echo json_encode($response);
    exit;
}

// access permissions check
if(!$accessObject->hasAccess("reports", "payslip")) {
    $response->html = page_not_found("permission_denied");
}

$ssnit_payments_list = "";

$filter->allowance_name = ['SSNIT', 'TIER 2'];

$ssnit_payments_array = load_class("payroll", "controllers")->employer_contributions($filter);
$ssnit_payments_array = $ssnit_payments_array["data"] ?? [];

if(is_array($ssnit_payments_array)) {
    foreach($ssnit_payments_array as $each) {
        $ssnit_payments_list .= '
            <tr data-row_id="'.$each->id.'">
                <td>'.$each->id.'</td>
                <td>'.$each->employee_name.'</td>
                <td>'.$each->payslip_month.' '.$each->payslip_year.'
                <br>
                <span class="badge badge-primary">'.$each->allowance_name.'</span>
                </td>
                <td>'.$each->employee_contribution.'</td>
                <td>'.$each->employer_contribution.'</td>
                <td>'.($each->employee_contribution + $each->employer_contribution).'</td>
                <td data-row_id_column="paid_status">'.$myClass->the_status_label($each->status).'</td>
                <td>'.$each->created_at.'</td>
                <td class="text-center">
                    '.(in_array($each->status, ['Pending']) ? "
                    <button onclick=\"return mark_contribution_as_paid({$each->id}, true)\" class='btn btn-sm mark_as_paid btn-icon btn-outline-success'>
                        <i class='fas fa-check'></i> Mark as Paid
                    </button>" : null).'
                </td>
            </tr>
        ';
    }
}

$response->scripts = ["assets/js/payroll.js"];

// set the parent menu
$response->parent_menu = "payroll";

$userId = $session->userId;
$clientId = $session->clientId;
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$response->title.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$response->title.'</div>
            </div>
        </div>
        <div class="row">
            
            <div class="col-12 col-sm-12 col-lg-12">
                
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped '.($ssnit_payments_list ? "datatable" : "raw_datatable").'">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Staff Name</th>
                                        <th width="13%">Month / Year</th>
                                        <th>Employee Contribution</th>
                                        <th>Employer Contribution</th>
                                        <th>Total Payable</th>
                                        <th width="10%">Status</th>
                                        <th width="12%">Date Created</th>
                                        <th width="13%" align="center"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$ssnit_payments_list.'</tbody>
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