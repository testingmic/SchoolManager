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

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Payroll Report";
$response->title = $pageTitle;
$response->scripts = ["assets/js/analitics.js"];

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

    $response->html = '
    <section class="section">
        <div class="section-header" id="data-report_stream" data-report_stream="salary_report">
            <h1><i class="fa fa-chart-pie"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'payslips">List Payslips</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row default_period" data-current_period="this_year">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="row">
                    <div class="col-xl-3 col-lg-6">
                        <div class="card">
                            <div class="card-body card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted mb-0">Basic Salary</h6>
                                        <span data-count="basic_salary" class="font-weight-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-orange text-white">
                                            <i class="fas fa-money-check"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div class="card">
                            <div class="card-body card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted mb-0">Allowances</h6>
                                        <span data-count="total_allowance" class="font-weight-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-purple text-white">
                                            <i class="fas fa-scroll"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div class="card">
                            <div class="card-body card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted mb-0">Deductions</h6>
                                        <span data-count="total_deductions" class="font-weight-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle bg-danger text-white">
                                            <i class="fas fa-window-restore"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6">
                        <div class="card">
                            <div class="card-body card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="text-muted mb-0">Net Salary</h6>
                                        <span data-count="net_salary" class="font-weight-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-green text-white">
                                            <i class="fas fa-money-bill"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>  
                <div class="card">
                    <div class="card-header">
                        <h4>Salary Payment Chart</h4>
                    </div>
                    <div class="card-body quick_loader">
                        <div class="card-body p-0" data-chart="salary_flow_chart">
                            <canvas id="salary_flow_chart" style="width:100%;max-height:355px;height:355px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Allowance / Deductions Breakdown</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-body p-0 table-responsive" data-chart="full_breakdown_chart"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>';

}
// print out the response
echo json_encode($response);
?>