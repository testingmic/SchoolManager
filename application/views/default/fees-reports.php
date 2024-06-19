<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultAcademics, $defaultCurrency, $session, $accessObject;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);
$pageTitle = "Fees Reports";
$response->title = $pageTitle;
$response->scripts = ["assets/js/analitics.js", "assets/js/filters.js"];

// the default data to stream
$data_stream = 'id="data-report_stream" data-report_stream="summary_report,fees_revenue_flow"';

$hasFiltering = $accessObject->hasAccess("filters", "settings");
$feesReport = $accessObject->hasAccess("reports", "fees");

// if the user does not the request permission
if(!$feesReport) {
    // end the page and return the access denied content
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// run the school academic terms
$myClass->academic_terms();
$session->reportPeriod = "this_week";

// if the class_id is not empty
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "id, name",
    "limit" => 100,
    "client_data" => $defaultUser->client
];
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$students_list = [];

// load fees allocation list for the students
$fees_category_list = "";
$feesObject = load_class("fees", "controllers", $classes_param);
$fees_category_array = $feesObject->category_list($classes_param)["data"];

// set the academic year and term
$academic_year_term = $defaultAcademics->academic_year."_".$defaultAcademics->academic_term;

// fees category
foreach($fees_category_array as $category) {
    $fees_category_list .= "<option value=\"{$category->id}\">".strtoupper($category->name)."</option>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-chart-line"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'fees-history">Fees Payment History</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        
        <div data-current_period="'.($session->reportPeriod ? $session->reportPeriod : "this_week").'" class="row default_period" '.$data_stream.'>

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
                                        <input type="hidden" hidden name="no_status_filters" value="not_filtered">
                                        <div class="col-lg-3 hidden col-md-4 col-12 form-group">
                                            <label>Academic Year & Term</label>
                                            <select data-width="100%" class="form-control selectpicker" name="academic_year_term">
                                                <option value="">Select Academic Year</option>';
                                                foreach($myClass->academic_calendar_years as $year) {
                                                    // print the academic year
                                                    $response->html .= "<option disabled>Academic Year - {$year}</option>";
                                                    // loop through the academic term
                                                    foreach($myClass->school_academic_terms as $term) {
                                                        $response->html .= "<option ".(($academic_year_term === "{$year}_{$term->name}") ? "selected" : null)." value=\"{$year}_{$term->name}\">{$year} - {$term->name}</option>";
                                                    }
                                                }
                                                $response->html .= '
                                            </select>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-12 form-group">
                                            <label>Select Class</label>
                                            <select data-width="100%" class="form-control selectpicker" name="class_id">
                                                <option value="">Please Select Class</option>';
                                                foreach($class_list as $each) {
                                                    $response->html .= "<option value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                                }
                                                $response->html .= '
                                            </select>
                                        </div>

                                        <div class="col-lg-4 col-md-4 col-12 form-group">
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
                                            <button id="filter_Fees_Report"  class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
                                        </div>
                            
                                        <div class="col-12 col-sm-12 col-lg-12">
                            
                                            <div class="row">

                                                <div class="col-lg-3 col-md-4">
                                                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-blue">
                                                        <div class="card-body card-type-3">
                                                            <div class="row">
                                                                <div class="col pr-0">
                                                                    <h6 class="font-14 text-uppercase font-bold mb-0">TOTAL FEES DUE</h6>
                                                                    <span data-summary="amount_due" class="font-bold text-primary font-17 mb-0">'.$defaultCurrency.' '.number_format(0, 2).'</span>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="bg-info text-white card-circle">
                                                                        <i class="fas fa-money-bill-alt"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                
                                                <div class="col-lg-3 col-md-4">
                                                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-success">
                                                        <div class="card-body card-type-3">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <h6 class="font-14 text-uppercase font-bold mb-0">TOTAL FEES PAID</h6>
                                                                    <span data-summary="amount_paid" class="font-bold text-success font-17 mb-0">'.$defaultCurrency.' '.number_format(0, 2).'</span>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="bg-success text-white card-circle">
                                                                        <i class="fas fa-money-bill-wave-alt"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                
                                                <div class="col-lg-3 col-md-4">
                                                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-danger">
                                                        <div class="card-body card-type-3">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <h6 class="font-14 text-uppercase font-bold mb-0">BALANCE</h6>
                                                                    <span data-summary="balance" class="font-bold text-danger font-17 mb-0">'.$defaultCurrency.' '.number_format(0, 2).'</span>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="bg-danger text-white card-circle">
                                                                        <i class="fas fa-money-bill"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 col-md-4">
                                                    <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-warning">
                                                        <div class="card-body card-type-3">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <h6 class="font-14 text-uppercase font-bold mb-0">ARREARS PAID</h6>
                                                                    <span data-summary="arrears_paid" class="font-bold text-warning font-17 mb-0">'.$defaultCurrency.' '.number_format(0, 2).'</span>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="bg-warning text-white card-circle">
                                                                        <i class="fas fa-money-bill-wave-alt"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        
                                                <div class="col-xl-3 hidden col-lg-3 col-md-4">
                                                    <div class="card">
                                                        <div class="card-body card-type-3">
                                                            <div class="row">
                                                                <div class="col">
                                                                    <h6 class="font-14 text-uppercase font-bold mb-0">FEES ARREARS</h6>
                                                                    <span data-summary="arrears_total" class="font-bold font-17 mb-0">'.$defaultCurrency.''.number_format(0, 2).'</span>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <div class="bg-amber text-white card-circle">
                                                                        <i class="fas fa-money-check"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                                                    <div class="card">
                                                        <div class="card-header pr-0">
                                                            <div class="row width-100">
                                                                <div class="col-md-3">
                                                                    <h4>Revenue</h4>
                                                                </div>
                                                                <div align="right" class="col-md-9">
                                                                    <div class="btn-group" data-filter="quick_summary_filter" role="group" aria-label="Filter Attendance">
                                                                        <button type="button" data-stream="summary_report" data-period="today" class="btn '.($session->reportPeriod == "today" ? "active" : null).' btn-info">Today</button>
                                                                        <button type="button" data-stream="summary_report" data-period="last_week" class="btn '.($session->reportPeriod == "last_week" ? "active" : null).' btn-info">Last Week</button>
                                                                        <button type="button" data-stream="summary_report" data-period="this_week" class="btn '.($session->reportPeriod == "this_week" ? "active" : null).' btn-info">This Week</button>
                                                                        <button type="button" data-stream="summary_report" data-period="this_month" class="btn '.($session->reportPeriod == "this_month" ? "active" : null).' btn-info">This Month</button>
                                                                        <button type="button" data-stream="summary_report" data-period="last_month" class="btn '.($session->reportPeriod == "last_month" ? "active" : null).' btn-info">Last Month</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body quick_loader" style="max-height:465px;height:465px;">
                                                            <div class="form-content-loader" style="display: flex; position: absolute">
                                                                <div class="offline-content text-center">
                                                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <div>
                                                                    <h3 class="card-title"><span>'.$defaultCurrency.'</span> <span data-count="total_revenue_received">0.00</span></h3>
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
                                                <div class="col-lg-4 hidden col-md-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4>Fees Payment Method</h4>
                                                        </div>
                                                        <div class="card-body" data-chart="revenue_payment_category">
                                                            <canvas style="max-height:420px;height:420px;" id="revenue_payment_category"></canvas>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-header pr-0">
                                                            <div class="row width-100">
                                                                <div class="col-md-5">
                                                                    <h4>Current Fees + Arrears Payments Flow Chart</h4>
                                                                </div>
                                                                <div align="right" class="col-md-7">
                                                                    <div class="btn-group" data-filter="quick_revenue_filter" role="group" aria-label="Filter Attendance">
                                                                        <button type="button" data-stream="fees_revenue_flow" data-period="today" class="btn '.($session->reportPeriod == "today" ? "active" : null).' btn-info">Today</button>
                                                                        <button type="button" data-stream="fees_revenue_flow" data-period="last_week" class="btn '.($session->reportPeriod == "last_week" ? "active" : null).' btn-info">Last Week</button>
                                                                        <button type="button" data-stream="fees_revenue_flow" data-period="this_week" class="btn '.($session->reportPeriod == "this_week" ? "active" : null).'  btn-info">This Week</button>
                                                                        <button type="button" data-stream="fees_revenue_flow" data-period="this_month" class="btn '.($session->reportPeriod == "this_month" ? "active" : null).' btn-info">This Month</button>
                                                                        <button type="button" data-stream="fees_revenue_flow" data-period="last_month" class="btn '.($session->reportPeriod == "last_month" ? "active" : null).' btn-info">Last Month</button>
                                                                        <button type="button" data-stream="fees_revenue_flow" data-period="this_year" class="btn '.($session->reportPeriod == "this_year" ? "active" : null).' btn-info">This Year</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body quick_loader">
                                                            <div class="form-content-loader" style="display: flex; position: absolute">
                                                                <div class="offline-content text-center">
                                                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                                                </div>
                                                            </div>
                                                            <div class="card-body" data-chart="revenue_flow_chart">
                                                                <div id="revenue_flow_chart" style="width:100%;max-height:405px;height:405px;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-header pr-0">
                                                            <div class="row width-100">
                                                                <div class="col-md-5">
                                                                    <h4>Fees Payment by Class</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body pb-0 quick_loader">
                                                            <div class="form-content-loader" style="display: flex; position: absolute">
                                                                <div class="offline-content text-center">
                                                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                                                </div>
                                                            </div>
                                                            <div data-chart="class_fees_payment_chart">
                                                                <div id="class_fees_payment_chart" style="width:100%;max-height:420px;height:420px;"></div>
                                                            </div>
                                                        </div>
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
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.date("Y-m-d").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="start_date" id="start_date">
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label>End Date</label>
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.date("Y-m-t").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="end_date" id="end_date">
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
                                                    $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                                }
                                                $response->html .= '
                                            </select>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label>Select Student <span class="required">*</span></label>
                                            <select data-width="100%" class="form-control selectpicker" name="student_id">
                                                <option value="">Please Select Student</option>';
                                                foreach($students_list as $each) {
                                                    $response->html .= "<option ".(($student_id == $each->user_id) ? "selected" : "")." value=\"{$each->user_id}\">".strtoupper($each->name)."</option>";
                                                }
                                                $response->html .= '
                                            </select>
                                        </div>
                                        <div class="'.(!$hasFiltering ? 'hidden': '').' col-md-2 col-12 form-group">
                                            <label for="">&nbsp;</label>
                                            <button id="generate_Fees_Report"  class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> Generate Report</button>
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