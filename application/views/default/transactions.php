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
$pageTitle = "Simple Accounting";
$response->title = "{$pageTitle} : {$appName}";

// add the scripts to load
$response->scripts = ["assets/js/accounting.js"];

// get the list of all classes
$params = (object)[
    "clientId" => $clientId,
    "userData" => $defaultUser,
    "client_data" => $defaultUser->client,
    "transaction_id" => $filter->transaction_id ?? null
];

// get the transactions list
$transactions_list = load_class("accounting", "controllers", $params)->list_transactions($params)["data"];

$count = 0;
$list_transactions = "";
$transactions_array_list = [];
foreach($transactions_list as $key => $transaction) {
    $transactions_array_list[$transaction->item_id] = $transaction;
    $count++;

    $list_transactions .= "<tr>";
    $list_transactions .= "<td>{$count}</td>";
    $list_transactions .= "<td>
        <strong>
            {$transaction->account_name} 
                <br> {$transaction->account_bank}
                <br> {$transaction->account_number}
        </strong>
    </td>";
    $list_transactions .= "<td>{$transaction->account_type_name}</td>";
    $list_transactions .= "<td>{$transaction->reference}</td>";
    $list_transactions .= "<td>".ucfirst($transaction->payment_medium)."</td>";
    $list_transactions .= "<td>".($transaction->item_type == "Expense" ? number_format($transaction->amount, 2) : null)."</td>";
    $list_transactions .= "<td>".($transaction->item_type == "Deposit" ? number_format($transaction->amount, 2) : null)."</td>";
    $list_transactions .= "<td>".number_format($transaction->balance, 2)."</td>";
    $list_transactions .= "<td>".date("d.M.Y", strtotime($transaction->record_date))."</td>";
    $list_transactions .= "</tr>";
}
$response->array_stream["transactions_array_list"] = $transactions_array_list;

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
                                        <table id="tableExport" class="table table-bordered table-striped datatable">
                                            <thead>
                                                <th></th>
                                                <th>Account Name</th>
                                                <th>Type Head</th>
                                                <th>Ref No.</th>
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