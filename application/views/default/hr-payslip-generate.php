<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$userId = confirm_url_id(1) ? xss_clean($SITEURL[1]) : $userId;

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Payslip Generation";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/payroll.js"];

$payslip_form = load_class("forms", "controllers")->payslip_form($clientId);

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'hr-payslip">List Payslips</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row" id="payslip_container">
                            <div class="col-lg-4">
                                <label>Select Employee</label>
                                <select class="form-control selectpicker" name="employee_id">
                                    <option value="">Please Select </option>';
                                    foreach($myClass->pushQuery("name, unique_id, item_id", "users", "user_type NOT IN('parent','student') AND user_status='Active' AND client_id='{$clientId}' ORDER BY name") as $each) {
                                        $response->html .= "<option ".(($userId == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">{$each->name}</option>";                            
                                    }
                                    $response->html .= '
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Select Year</label>
                                <select class="form-control selectpicker" name="year_id">
                                    <option value="">Please Select </option>';
                                    for($i = date("Y")-2; $i < date("Y")+3; $i++) {
                                        $response->html .= "<option ".(($i == date("Y")) ? "selected" : "")." value=\"{$i}\">{$i}</option>";                            
                                    }
                                    $response->html .= '
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label>Select Month</label>
                                <select class="form-control selectpicker" name="month_id">
                                    <option value="">Please Select </option>';
                                    for($i = 0; $i < 12; $i++) {
                                        $month = date("F", strtotime("January +{$i} month"));
                                        $response->html .= "<option ".(($month == date("F")) ? "selected" : "")." value=\"{$month}\">{$month}</option>";                            
                                    }
                                    $response->html .= '
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="submit">&nbsp;</label>
                                    <button onclick="return load_employee_payslip(\''.$userId.'\')" class="btn-block btn btn-outline-success">Load Record</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                '.$payslip_form.'                
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>