<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Accounting Reports";
$response->title = $pageTitle;


// confirm that the user has the required permissions
if(!$accessObject->hasAccess("reports", "accounting")) {
    $response->html = page_not_found("permission_denied");
} else {

    // add the scripts to load
    $response->scripts = ["assets/js/analitics.js", "assets/js/filters.js"];

    // load fees allocation list for the students
    $accounts_list = "";
    $stmt = $myClass->db->prepare("
        SELECT item_id, account_name, account_bank
        FROM accounts
        WHERE client_id = ? AND status = ?
    ");
    $stmt->execute([$clientId, 1]);
    $accounts_array_list = $stmt->fetchAll(PDO::FETCH_OBJ);

    // fees category
    foreach($accounts_array_list as $category) {
        $accounts_list .= "<option value=\"{$category->item_id}\">{$category->account_name} ($category->account_bank)</option>";
    }

    $data_stream = 'id="data-report_stream" data-report_stream="summary_report,transaction_revenue_flow"';

    // set the date
    $start_date = date("Y-m-d", strtotime("monday this week"));
    $end_date = date("Y-m-d", strtotime("sunday this week"));

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-chart-bar"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div data-current_period="this_week" class="row default_period" '.$data_stream.'></div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="summary-tab2" data-toggle="tab" href="#summary" role="tab" aria-selected="true">Revenue Summary</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true"><i class="fa fa-list"></i> Account Statement</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="transaction-tab2" data-toggle="tab" href="#transaction" role="tab" aria-selected="true"><i class="fa fa-list"></i> Transactions Statement</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="transaction_notes-tab2" data-toggle="tab" href="#transaction_notes" role="tab" aria-selected="true"><i class="fa fa-list"></i> Transactions Notes</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="summary" role="tabpanel" aria-labelledby="summary-tab2">
                                    
                                    <div class="row">
                                        
                                        <div class="col-lg-12 col-md-12 col-12 col-sm-12">

                                            <div class="card-header pr-0">
                                                <div class="row width-100">
                                                    <div class="col-md-5 pl-0">
                                                        <h4 class="text-uppercase font-13">Income & Expenditure Summary</h4>
                                                    </div>
                                                    <div align="right" class="col-lg-7 pr-0">
                                                        <div class="btn-group mb-2" role="group" aria-label="Filter Revenue">
                                                            <input data-maxdate="'.date("Y-m-d", strtotime("+2 year")).'" value="'.$start_date.'" style="max-width:150px" type="text" class="form-control text-center ml-2 mr-2 datepicker" name="d_start" id="d_start">
                                                            <input data-maxdate="'.date("Y-m-d", strtotime("+2 year")).'" value="'.$end_date.'" type="text" style="max-width:150px" class="form-control text-center ml-2 mr-2 datepicker" name="d_end" id="d_end">
                                                            <button onclick="return filter_Transaction_Summary(\'summary_report,transaction_revenue_flow\')" type="button" class="btn btn-success"><i class="fa fa-filter"></i> Filter</button>
                                                            <a data-href="summary_link" target="_blank" href="'.$baseUrl.'download/accounting?display=notes&item=summary&start_date='.$start_date.'&end_date='.$end_date.'&group_by=day&breakdown=true" class="btn btn-outline-primary ml-2"><i class="fa fa-print"></i> Print Report</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body mb-0 p-0 pb-3" style="min-height:250px" id="trasaction_container">
                                                <div class="form-content-loader" style="display: flex; position: absolute">
                                                    <div class="offline-content text-center">
                                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                                    </div>
                                                </div>
                                                <table class="table table-bordered table-md" id="transaction_summary" width="100"></table>
                                            </div>
                                                
                                        </div>

                                        <div class="col-lg-12 col-md-12">
                                            <div class="row">
                                                <div class="col-lg-12 border-bottom mb-3 border-primary">
                                                    <h4 class="text-uppercase font-13">BREAKDOWN OF FEES RECEIPTS</h4>
                                                </div>
                                                <div class="col-lg-12" id="revenue_category_counts"></div>
                                            </div>
                                        </div>

                                    </div>
                                    
                                </div>
                                <div class="tab-pane fade" id="transaction_notes" role="tabpanel" aria-labelledby="transaction_notes-tab2">
                                    <div class="row account_note_report">
                                        <div class="col-md-4">
                                            <label>Select Account</label>
                                            <select data-width="100%" id="account_id" name="account_id" class="selectpicker form-control">
                                                <option value="">Select Account</option>
                                                '.$accounts_list.'
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label>Start Date</label>                                
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.date("Y-m-01").'" type="text" class="datepicker form-control text-center" name="start_date" id="start_date">
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label>End Date</label>
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.date("Y-m-t").'" type="text" class="datepicker form-control text-center" name="end_date" id="end_date">
                                        </div>
                                        <div class="col-md-2 col-12 form-group">
                                            <label for="">&nbsp;</label>
                                            <button id="generate_Account_Notes_Report" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> Generate</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                    <div class="row generate_report">
                                        <div class="col-lg-4">
                                            <label>Select Account</label>
                                            <select data-width="100%" id="account_id" name="account_id" class="selectpicker form-control">
                                                <option value="">Select Account</option>
                                                '.$accounts_list.'
                                            </select>
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label>Start Date</label>                                
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.date("Y-m-01").'" type="text" class="datepicker form-control text-center" name="start_date" id="start_date">
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label>End Date</label>
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.date("Y-m-t").'" type="text" class="datepicker form-control text-center" name="end_date" id="end_date">
                                        </div>
                                        <div class="col-md-2 col-12 form-group">
                                            <label for="">&nbsp;</label>
                                            <button id="generate_Account_Statement" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> Generate</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="transaction" role="tabpanel" aria-labelledby="transaction-tab2">
                                    <div class="row transaction_report">
                                        <div class="col-md-3">
                                            <label>Select Account</label>
                                            <select data-width="100%" id="account_id" name="account_id" class="selectpicker form-control">
                                                <option value="">Select Account</option>
                                                '.$accounts_list.'
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Transaction Type</label>
                                            <select data-width="100%" id="item_type" name="item_type" class="selectpicker form-control">
                                                <option value="Deposit">Deposit (Income)</option>
                                                <option value="Expense">Expense</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-1">
                                            <label>Start Date</label>                                
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.date("Y-m-01").'" type="text" class="datepicker form-control text-center" name="start_date" id="start_date">
                                        </div>
                                        <div class="col-md-2 mb-1">
                                            <label>End Date</label>
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.date("Y-m-t").'" type="text" class="datepicker form-control text-center" name="end_date" id="end_date">
                                        </div>
                                        <div class="col-md-2 col-12 form-group">
                                            <label for="">&nbsp;</label>
                                            <button id="generate_Transaction_Report" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> Generate</button>
                                        </div>
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