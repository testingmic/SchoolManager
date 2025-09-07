<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser, $defaultClientData, $isReadOnly;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);
$pageTitle = "Simple Accounting - Income";
$response->title = $pageTitle;

// add the scripts to load
$response->scripts = ["assets/js/accounting.js", "assets/js/upload.js", "assets/js/object_selector.js"];

// permission to modify and validate
$canDeposit = $accessObject->hasAccess("deposits", "accounting");

// if the user does not have the required permissions
if(!$canDeposit) {
    // unset the page additional information
    $response->page_programming = [];
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// additional permissions
$hasValidate = $accessObject->hasAccess("validate", "accounting");
$canReverse = $accessObject->hasAccess("modify", "accounting");

// date range filter
$date_range = $filter->date_range ?? date("Y-m-d", strtotime("monday this week")).":".date("Y-m-d", strtotime("sunday this week"));

// get the list of all classes
$params = (object)[
    "route" => "deposit",
    "clientId" => $clientId,
    "item_type" => "Deposit",
    "date_range" => $date_range,
    "order_by" => "DESC",
    "userData" => $defaultUser,
    "client_data" => $defaultUser->client,
    "transaction_id" => $filter->transaction_id ?? null,
    "account_id" => $filter->account_id ?? null,
    "account_type" => $filter->account_type ?? null
];

// get the transactions list
$transactions_list = load_class("accounting", "controllers", $params)->list_transactions($params)["data"];

$count = 0;
$total_income = 0;
$list_transactions = "";
$transactions_array_list = [];
foreach($transactions_list as $key => $transaction) {
    $transactions_array_list[$transaction->item_id] = $transaction;
    $total_income += $transaction->amount;
    $count++;

    // view button
    $checkbox = "";
    $action = "<button onclick='return view_transaction(\"{$transaction->item_id}\");' title='Click to view full details of transaction' class='btn btn-outline-success mb-1 btn-sm'><i class='fa fa-eye'></i></button>";

    // if the record is still pending
    if($transaction->state === "Pending") {
        // validate the transaction
        if($hasValidate) {
            $action .= "&nbsp;<button onclick='return validate_transaction(\"{$transaction->item_id}\",\"deposits\")' class=\"btn btn-sm btn-outline-primary mb-1\" title=\"Validate\"><i class='fa fa-check'></i></button>";
        }

        // if the user has permission to modify record
        if($canReverse) {
            $action .= "&nbsp;<button onclick='return reverse_transaction(\"{$transaction->item_id}\",\"{$transaction->account_type_name}\",\"{$transaction->amount}\");' title='Reverse this transaction' class='btn btn-outline-danger mb-1 btn-sm'><i class='fa fa-recycle'></i></button>";
        }
    }

    $list_transactions .= "<tr data-row_id=\"{$transaction->item_id}\">";
    $list_transactions .= "<td>{$count}</td>";
    $list_transactions .= "<td>{$transaction->account_name}</td>";
    $list_transactions .= "<td>{$transaction->account_type_name}</td>";
    $list_transactions .= "<td>".(!empty($transaction->attach_to_object) ? ucwords($transaction->attach_to_object) : "N/A")."</td>";
    $list_transactions .= "<td>".number_format($transaction->amount, 2)."</td>";
    $list_transactions .= "<td>".date("jS M Y", strtotime($transaction->record_date))."</td>";
    $list_transactions .= "<td>{$transaction->state_label}</td>";
    $list_transactions .= "<td class='text-center'><span data-action_id='{$transaction->item_id}'>{$action}</span></td>";
    $list_transactions .= "</tr>";
}
$response->array_stream["transactions_array_list"] = $transactions_array_list;
$response->page_programming["left"] = [
    "Total Income" => number_format($total_income, 2),
    "Average Income" => $total_income > 0 ? number_format(($total_income/count($transactions_list)), 2) : 0
];
// confirm if the account check is empty
if(empty($defaultClientData->default_account_id) || $isReadOnly) {
    // set the content
    $title = $isReadOnly ? "Readonly Mode" : "Payment Account Not Set";
    $link = $isReadOnly ? "readonly_mode" : "account_not_set";

    // message to share
    $the_form = notification_modal($title, $myClass->error_logs[$link]["msg"], $myClass->error_logs[$link]["link"]);
} else {
    // confirm that the user has the required permissions
    $the_form = load_class("forms", "controllers")->transaction_form($params);
}

// page content
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row" id="transactions_list">

            <div class="col-xl-3 col-md-3 col-12 form-group">
                <label>Filter by Account</label>
                <select data-width="100%" class="form-control selectpicker" name="account_id">
                    <option value="">Please Select Account</option>';
                    foreach($myClass->pushQuery("item_id, account_name, account_bank, state", "accounts", "status='1' AND client_id='{$clientId}' LIMIT 5") as $each) {
                        $response->html .= "<option ".(isset($filter->account_id) && ($filter->account_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->account_name)." ({$each->account_bank})</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 col-md-3 col-12 form-group">
                <label>Filter by Type Head</label>
                <select data-width="100%" class="form-control selectpicker" name="account_type">
                    <option value="">Select Type Head</option>
                    <option '.(isset($filter->account_type) && ($filter->account_type == "fees") ? "selected" : "").' value="fees">FEES PAYMENT</option>';
                    foreach($myClass->pushQuery("item_id, name", "accounts_type_head", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->account_type) && ($filter->account_type == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";
                    }
                $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 col-md-4 col-12 form-group">
                <label>Date Range</label>
                <input type="text" name="date_range" id="date_range" value="'.$date_range.'" class="form-control daterange">
            </div>
            <div class="col-xl-2 col-md-2 col-12 form-group">
                <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                <button id="filter_Transaction" data-type="incomes" type="submit" class="btn height-40 btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>

            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="padding-20">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true"><i class="fa fa-list"></i> Income List</a>
                                </li>
                                '.($canDeposit ? '
                                <li class="nav-item">
                                    <a class="nav-link" id="upload_reports-tab2" data-toggle="tab" href="#upload_reports" role="tab" aria-selected="true"><i class="fa fa-edit"></i> Add Income</a>
                                </li> ': null).'
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                    <div>

                                    </div>
                                    <div class="table-responsive trix-slim-scroll">
                                        <table class="table table-bordered table-sm table-striped datatable">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th width="16%">Account Name</th>
                                                    <th width="19%">Account Type Head</th>
                                                    <th>Reference</th>
                                                    <th>Amount</th>
                                                    <th width="12%">Date</th>
                                                    <th>Status</th>
                                                    <th width="13%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>'.$list_transactions.'</tbody>
                                        </table>
                                    </div>
                                </div>
                                '.($canDeposit ? '
                                    <div class="tab-pane fade" id="upload_reports" role="tabpanel" aria-labelledby="upload_reports-tab2">
                                        '.$the_form.'
                                    </div>': null
                                ).'
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