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

$clientId = $session->clientId;
$response = (object) [];
$search_term = isset($_GET["term"]) ? xss_clean($_GET["term"]) : null;

$pageTitle = "Fee Payment Log Search";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/payments.js"];

$the_form = "";

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'fees-history">Fee Payment List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row" id="finance_search_field">
            <div class="col-12 col-sm-12 col-md-6">
                <div class="card">
                    <div class="card-body"">
                        <div class="form-group">
                            <label>Enter the Student Name or Receipt ID</label>
                            <input value="'.$search_term.'" type="text" placeholder="Search by Student Name or Receipt ID" name="log_search_term" id="log_search_term" class="form-control">
                        </div>
                        <div align="center" class="form-group mb-2">
                            <button class="btn btn-outline-primary" onclick="return search_Payment_Log()"><i class="fa fa-filter"></i> Search</button>
                        </div>
                        <div class="mt-0 mb-2 border-bottom"></div>
                        <div id="log_search_term_list" class="slim-scroll custom-600px"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6">
                <div class="card">
                    <div class="card-body">'.$the_form.'</div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>