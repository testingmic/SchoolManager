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
$pageTitle = "Simple Accounting Reports";
$response->title = "{$pageTitle} : {$appName}";

// add the scripts to load
$response->scripts = ["assets/js/filters.js"];

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

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-chart-bar"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="padding-20">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true"><i class="fa fa-list"></i> Account Statement</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="transaction-tab2" data-toggle="tab" href="#transaction" role="tab" aria-selected="true"><i class="fa fa-list"></i> Transactions Statement</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="transaction_notes-tab2" data-toggle="tab" href="#transaction_notes" role="tab" aria-selected="true"><i class="fa fa-list"></i> Transactions Notes</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
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
                                            <input value="'.date("Y-m-01").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="start_date" id="start_date">
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label>End Date</label>
                                            <input value="'.date("Y-m-t").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="end_date" id="end_date">
                                        </div>
                                        <div class="col-md-2 col-12 form-group">
                                            <label for="">&nbsp;</label>
                                            <button id="generate_Account_Notes_Report" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> Generate</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab2">
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
                                            <input value="'.date("Y-m-01").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="start_date" id="start_date">
                                        </div>
                                        <div class="col-md-3 mb-1">
                                            <label>End Date</label>
                                            <input value="'.date("Y-m-t").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="end_date" id="end_date">
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
                                            <input value="'.date("Y-m-01").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="start_date" id="start_date">
                                        </div>
                                        <div class="col-md-2 mb-1">
                                            <label>End Date</label>
                                            <input value="'.date("Y-m-t").'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="end_date" id="end_date">
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
        </div>
    </section>';
    
// print out the response
echo json_encode($response);
?>