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
$pageTitle = "Account Type Head";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [
    "assets/js/accounting.js"
];

$params = (object)[
    "clientId" => $clientId
];
$the_form = load_class("forms", "controllers")->account_type_head($params);

$data = null;

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
            '.$the_form.'
            <div class="col-12 col-md-7 col-lg-8">
                <div class="card">
                    <div class="card-header">Account Type Head List</div>
                    <div class="card-body">

                        <div class="table-responsive table-student_staff_list">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Branch</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th width="13%" align="center"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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