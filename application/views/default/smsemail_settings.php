<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Bulk SMS & Email Settings";
$response->title = "{$pageTitle} : {$appName}";

// get the smsemail information
$settings = $myClass->pushQuery("*", "smsemail_balance", "client_id='{$clientId}' LIMIT 1");
$sms_packages = $myClass->pushQuery("*", "sms_packages", "1");
$settings = !empty($settings) ? $settings[0] : [];

$response->array_stream["smsemail_settings"] = $settings;
$response->array_stream["sms_packages"] = $sms_packages;

// add the scripts to load
$response->scripts = ["assets/js/communication.js"];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" name="myemail_address" value="'.$defaultUser->email.'">
                        <div class="d-flex mb-2 justify-content-between">
                            <div></div>
                            <div>
                                <button onclick="return topup_sms()" class="btn btn-success"><i class="fa fa-database"></i> Top Up</button>
                            </div>
                        </div>
                        <table class="table font-18 table-bordered table-condensed table-striped">
                            <tbody>
                                <tr>
                                    <td><strong>Units Balance</strong></td>
                                    <td><span id="sms_balance">'.($settings->sms_balance ?? 0).' SMS Units</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Units Used</strong></td>
                                    <td>'.($settings->sms_sent ?? 0).'</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>';
    
// print out the response
echo json_encode($response);
?>