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
$pageTitle = "E-Learning";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/resources.js"];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row" id="e_resources">
            <div class="col-sm-12 col-lg-12">
                <div class="row mb-2">
                    <div class="col-md-10 col-lg-10">
                        <input placeholder="Search for a e-learning resource" id="search_term" name="search_term" type="text" class="form-control">
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <button class="btn-block btn btn-outline-primary">Search <i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div id="total_count"></div>
                        <div id="elearning_resources_list" style="min-height:100px"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>