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
$filter = (object) $_POST;
$pageTitle = "Fees Reports";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/analitics.js", "assets/js/filters.js"];

// the default data to stream
$data_stream = 'id="data-report_stream" data-report_stream="summary_report,revenue_flow"';

$hasFiltering = $accessObject->hasAccess("filters", "settings");

// if the class_id is not empty
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "id, name",
    "client_data" => $defaultUser->client
];
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$students_list = [];

// load fees allocation list for the students
$fees_category_list = "";
$feesObject = load_class("fees", "controllers", $classes_param);
$fees_category_array = $feesObject->category_list($classes_param)["data"];

// fees category
foreach($fees_category_array as $category) {
    $fees_category_list .= "<option value=\"{$category->id}\">{$category->name}</option>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'fees-history">Fees Payment History</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        
        <div data-current_period="'.($session->reportPeriod ? $session->reportPeriod : "last_14days").'" class="row default_period" '.$data_stream.'>

            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">Fees Report Summary</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="generate-tab2" data-toggle="tab" href="#generate" role="tab" aria-selected="true">Generate Report</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                    
                                    <div class="row" id="reports_insight">

                                        <div class="col-md-4 col-12 form-group">
                                            <label>Select Class</label>
                                            <select data-width="100%" class="form-control selectpicker" name="class_id">
                                                <option value="null">Please Select Class</option>';
                                                foreach($class_list as $each) {
                                                    $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";
                                                }
                                                $response->html .= '
                                            </select>
                                        </div>

                                        <div class="col-md-4 col-12 form-group">
                                            <label>Period Filter</label>
                                            <select data-width="100%" class="form-control selectpicker" id="filter-dashboard" name="period">
                                                <option value="">Please Select Period</option>';
                                                foreach($myClass->accepted_period as $key => $value) {
                                                    $response->html .= "<option ".($session->reportPeriod === $key ? "selected" : "")." value=\"{$key}\">{$value["title"]}</option>";                            
                                                }
                                            $response->html .= '
                                            </select>
                                        </div>

                                        <div class="col-xl-2 col-md-2 col-12 form-group">
                                            <label for="">&nbsp;</label>
                                            <button id="filter_Fees_Report" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
                                        </div>
                            
                                        <div class="col-12 col-sm-12 col-lg-12">
                            
                                            <div class="row">
                            
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
                                                            <div class="card-body" data-chart="revenue_category_chart">
                                                                <div id="revenue_category_chart"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4>Revenue Category</h4>
                                                        </div>
                                                        <div class="card-body" data-chart="revenue_category_group">
                                                            <canvas style="max-height:420px;height:420px;" id="revenue_category_group"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
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
                                                            <div class="card-body" data-chart="revenue_flow_chart">
                                                                <canvas id="revenue_flow_chart" style="width:100%;max-height:405px;height:405px;"></canvas>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4>Fees Payment Method</h4>
                                                        </div>
                                                        <div class="card-body" data-chart="revenue_payment_category">
                                                            <canvas style="max-height:420px;height:420px;" id="revenue_payment_category"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-8 col-md-12">
                                                    <div class="row">
                                                        <div class="col-lg-12" id="revenue_category_counts"></div>
                                                    </div>
                                                </div>
                            
                                            </div>
                            
                                        </div>

                                    </div>

                                </div>
                                <div class="tab-pane fade" id="generate" role="tabpanel" aria-labelledby="generate-tab2">
                                    
                                   
                                    <div class="row generate_report" id="filter_Department_Class">
                                        
                                        <div class="col-md-3 mb-1">
                                            <label>Start Date</label>                                
                                            <input value="'.date("Y-m-01").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="start_date" id="start_date">
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label>End Date</label>
                                            <input value="'.date("Y-m-t").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="end_date" id="end_date">
                                        </div>
                                        <div class="col-md-6 mb-1"></div>
                                        <div class="col-md-3 form-group">
                                            <label>Filter by Category</label>
                                            <select data-width="100%" name="category_id" class="selectpicker form-control">
                                                <option value="">Select Category</option>
                                                '.$fees_category_list.'
                                            </select>
                                        </div>
                                        <div class="'.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                                            <label>Select Class</label>
                                            <select data-width="100%" class="form-control selectpicker" name="class_id">
                                                <option value="">Please Select Class</option>';
                                                foreach($class_list as $each) {
                                                    $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                                                }
                                                $response->html .= '
                                            </select>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Select Student <span class="required">*</span></label>
                                            <select data-width="100%" class="form-control selectpicker" name="student_id">
                                                <option value="">Please Select Student</option>';
                                                foreach($students_list as $each) {
                                                    $response->html .= "<option ".(($student_id == $each->user_id) ? "selected" : "")." value=\"{$each->user_id}\">{$each->name}</option>";
                                                }
                                                $response->html .= '
                                            </select>
                                        </div>
                                        <div class="'.(!$hasFiltering ? 'hidden': '').' col-md-2 col-12 form-group">
                                            <label for="">&nbsp;</label>
                                            <button id="generate_Fees_Report" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> Generate Report</button>
                                        </div>
                                        
                                    </div>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
    
// print out the response
echo json_encode($response);
?>