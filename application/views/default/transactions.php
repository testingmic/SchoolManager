<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Simple Accounting - Transactions";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!$accessObject->hasAccess("accounts", "accounting")) {
    // unset the page additional information
    $response->page_programming = [];
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// set the parent menu
$response->parent_menu = "accounting";


// add the scripts to load
$response->scripts = ["assets/js/accounting.js"];

// get the list of all classes
$params = (object)[
    "clientId" => $clientId,
    "userData" => $defaultUser,
    "order_by" => "DESC",
    "academic_year" => $defaultAcademics->academic_year,
    "academic_term" => $defaultAcademics->academic_term,
    "client_data" => $defaultUser->client,
    "transaction_id" => $filter->transaction_id ?? null
];

// get the transactions list
$transactions_list = load_class("accounting", "controllers", $params)->list_transactions($params)["data"];

$count = 0;
$total_income = 0;
$total_expense = 0;
$account_balance = 0;
$list_transactions = "";
$transactions_array_list = [];
foreach($transactions_list as $key => $transaction) {
    $transactions_array_list[$transaction->item_id] = $transaction;
    $count++;

    $isIncome = (bool) ($transaction->item_type == "Deposit");

    $total_income += $isIncome ? $transaction->amount : 0;
    $total_expense += !$isIncome ? $transaction->amount : 0; 

    $list_transactions .= "<tr>";
    $list_transactions .= "<td>{$count}</td>";
    $list_transactions .= "<td>
        <strong>
            {$transaction->account_name} <!--<br> {$transaction->account_bank}<br> {$transaction->account_number}-->
        </strong>
    </td>";
    $list_transactions .= "<td>{$transaction->account_type_name}</td>";
    $list_transactions .= "<td>".(!empty($transaction->attach_to_object) ? ucwords($transaction->attach_to_object) : (
        !empty($transaction->reference) ? $transaction->reference : (
            !empty($transaction->description) ? $transaction->description : "N/A"
        )
    ))."</td>";
    $list_transactions .= "<td>".ucfirst($transaction->payment_medium)."</td>";
    $list_transactions .= "<td>".(!$isIncome ? number_format($transaction->amount, 2) : null)."</td>";
    $list_transactions .= "<td>".($isIncome ? number_format($transaction->amount, 2) : null)."</td>";
    $list_transactions .= "<td>".number_format($transaction->balance, 2)."</td>";
    $list_transactions .= "<td>".date("d.M.Y", strtotime($transaction->date_created))."</td>";
    $list_transactions .= "</tr>";
}
$response->array_stream["transactions_array_list"] = $transactions_array_list;
$response->page_programming["left"] = [
    "Income" => number_format($total_income, 2),
    "Expenses" => number_format($total_expense, 2),
    "Account Balance" => number_format(($total_income - $total_expense), 2)
];
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
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="padding-20">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true"><i class="fa fa-list"></i> Transactions List</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                    <div class="table-responsive trix-slim-scroll">
                                        <table id="tableExport" class="table table-sm table-bordered table-striped datatable">
                                            <thead>
                                                <th></th>
                                                <th>Account Name</th>
                                                <th>Type Head</th>
                                                <th>Reference</th>
                                                <th>Pay Via</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Balance</th>
                                                <th>Date</th>
                                            </thead>
                                            <tbody>'.$list_transactions.'</tbody>
                                        </table>
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