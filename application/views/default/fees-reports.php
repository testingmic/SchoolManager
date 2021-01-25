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
$pageTitle = "Fees Reports";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/analitics.js"];

// the default data to stream
$data_stream = 'id="data-report_stream" data-report_stream="summary_report,revenue_flow"';

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'fees-history">Fees Payment List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row" '.$data_stream.'>
            <div class="col-12 col-sm-12 col-lg-12">

                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Revenue Flow Chart</h4>
                            </div>
                            <div class="card-body quick_loader">
                                <div class="form-content-loader" style="display: flex; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
                                <canvas id="revenue_flow_chart" style="width:100%;max-height:405px;height:405px;"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Revenue</h4>
                            </div>
                            <div class="card-body quick_loader" style="max-height:465px;height:465px;">
                                <div class="form-content-loader" style="display: flex; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h3 class="card-title"><i class="fas fa-dollar-sign col-green font-30 p-b-10"></i> <span data-count="total_revenue_received">0.00</span></h3>
                                    </div>
                                    <div style="width:60%">
                                        <div class="d-flex justify-content-between">
                                            <div class="col-6">
                                                <h5>&nbsp;</h5>
                                            </div>
                                            <div class="col-6">
                                                <p class="text-muted text-truncate m-b-5">Revenue <span data-filter="period">Last Week</span></p>
                                                <h5><i class="fas fa-arrow-circle-up col-green m-r-5"></i><span data-count="previous_amount_received">0.00</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="revenue_category_chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Revenue Category</h4>
                            </div>
                            <div class="card-body" data-chart="revenue_category_group">
                                <canvas style="max-height:420px;height:420px;" id="revenue_category_group"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12" id="revenue_category_counts"></div>

                </div>

            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>