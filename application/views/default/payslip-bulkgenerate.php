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

    $limit = 1000;
    // confirm if the account check is empty
    if(empty($defaultClientData->default_account_id)) {
        // limit
        $limit = 0;
        // message to share
        $payslip_form = notification_modal("Payment Account Not Set", $myClass->error_logs["account_not_set"]["msg"], $myClass->error_logs["account_not_set"]["link"]);
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
                            <div class="row">
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <label>Select Employee</label>
                                    <select '.(!$limit ? "disabled" : 'name="employee_id"').' data-width="100%" class="form-control selectpicker">
                                        <option value="">Please select staff</option>';
                                        foreach($myClass->pushQuery("name, unique_id, item_id", "users", "user_type NOT IN('parent','student') AND user_status IN ({$myClass->default_allowed_status_users_list}) AND client_id='{$clientId}' ORDER BY name LIMIT {$limit}") as $each) {
                                            $response->html .= "<option value=\"{$each->item_id}\">
                                            ".ucwords($each->name)."
                                            </option>";                            
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 mb-2">
                                    <div class="form-group">
                                        <label for="submit">&nbsp;</label>
                                        <button onclick="return append_employee_to_list()" class="btn-block btn btn-outline-success">Add to List</button>
                                    </div>
                                </div>
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