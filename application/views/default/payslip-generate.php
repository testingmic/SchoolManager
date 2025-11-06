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
$pageTitle = "Payslip Generation";
$response->title = $pageTitle;
$response->scripts = ["assets/js/payroll.js"];

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
if(!$accessObject->hasAccess("generate", "payslip")) {
    $response->html = page_not_found("permission_denied");
} else {    

    // confirm if the account check is empty
    if(empty($defaultClientData->default_account_id)) {
        // limit
        $limit = 0;
        // message to share
        $payslip_form = notification_modal("Payment Account Not Set", $myClass->error_logs["account_not_set"]["msg"], $myClass->error_logs["account_not_set"]["link"]);
    } else {
        // limit
        $limit = 1000;
        // load the form
        $payslip_form = load_class("forms", "controllers")->payslip_form($clientId);
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
                <div class="col-12 col-sm-12 col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="row" id="payslip_container">
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <label>Select Employee</label>
                                    <select '.(!$limit ? "disabled" : 'name="employee_id"').' data-width="100%" class="form-control selectpicker">
                                        <option value="">Please Select </option>';
                                        foreach($myClass->pushQuery("name, unique_id, item_id", "users", "user_type NOT IN('parent','student') AND user_status IN ({$myClass->default_allowed_status_users_list}) AND client_id='{$clientId}' ORDER BY name LIMIT {$limit}") as $each) {
                                            $response->html .= "<option ".(($userId == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">
                                            ".ucwords($each->name)."
                                            </option>";                            
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <label>Select Year</label>
                                    <select '.(!$limit ? "disabled" : 'name="year_id"').' data-width="100%" class="form-control selectpicker">
                                        <option value="">Please Select </option>';
                                        for($i = date("Y") - 2; $i < date("Y") + 1; $i++) {
                                            $response->html .= "<option ".(($i == date("Y")) ? "selected" : "")." value=\"{$i}\">{$i}</option>";                            
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-2">
                                    <label>Select Month</label>
                                    <select '.(!$limit ? "disabled" : 'name="month_id"').' data-width="100%" class="form-control selectpicker">
                                        <option value="">Please Select </option>';
                                        for($i = 0; $i < 12; $i++) {
                                            $month = date("F", strtotime("December +{$i} month 3 day"));
                                            $response->html .= "<option ".(($month == date("F")) ? "selected" : "")." value=\"{$month}\">{$month}</option>";                            
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-6 mb-2">
                                    <div class="form-group">
                                        <label for="submit">&nbsp;</label>
                                        <button '.(!$limit ? "disabled" : 'onclick="return load_employee_payslip()"').' class="btn-block btn btn-outline-success">Load Record</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    '.$payslip_form.'                
                </div>
            </div>
        </section>';
}
// print out the response
echo json_encode($response);
?>