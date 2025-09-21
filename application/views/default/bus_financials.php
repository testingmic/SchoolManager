<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $clientFeatures, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Bus Financials";
$response->title = $pageTitle;

// if the user has the permission
$hasView = $accessObject->hasAccess("financials", "buses");

// if the user does not have the required permissions
if(!in_array("bus_manager", $clientFeatures)) {
    $response->html = page_not_found("feature_disabled", ["bus_manager"]);
    echo json_encode($response);
    exit;
}

// if the user does not have the required permissions
if(!$hasView) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// date range filter
$date_range = $filter->date_range ?? date("Y-m-d", strtotime("-1 month")).":".date("Y-m-d");

// get the list of all classes
$params = (object)[
    "route" => "deposit",
    "clientId" => $clientId,
    "date_range" => $date_range,
    "order_by" => "DESC",
    "attach_to_object" => "bus",
    "userData" => $defaultUser,
    "busFinancials" => true,
    "client_data" => $defaultUser->client,
    "transaction_id" => $filter->transaction_id ?? null,
    "account_id" => $filter->account_id ?? null,
    "account_type" => $filter->account_type ?? null
];

// get the transactions list
$transactions_list = load_class("accounting", "controllers", $params)->list_transactions($params)["data"];

$count = 0;
$list_transactions = "";
$transactions_array_list = [];
foreach($transactions_list as $key => $transaction) {
    // set the transaction array list
    $count++;

    // view button
    $checkbox = "";
    $action = "<button onclick='return view_transaction(\"{$transaction->item_id}\");' title='Click to view full details of transaction' class='btn btn-outline-success mb-1 btn-sm'><i class='fa fa-eye'></i></button>";

    // if the record is still pending
    if($transaction->state === "Pending") {
        // validate the transaction
        if($hasValidate) {
            $action .= "&nbsp;<button onclick='return validate_transaction(\"{$transaction->item_id}\",\"{$baseUrl}incomes\")' class=\"btn btn-sm btn-outline-primary mb-1\" title=\"Validate Transaction\"><i class='fa fa-check'></i></button>";
        }

        // if the user has permission to modify record
        if($hasModify) {
            $action .= "&nbsp;<button onclick='return reverse_transaction(\"{$transaction->item_id}\",\"{$transaction->account_type_name}\",\"{$transaction->amount}\");' title='Click to reverse this transaction' class='btn btn-outline-danger mb-1 btn-sm'><i class='fa fa-recycle'></i></button>";
        }
    }

    $reference = !empty($transaction->bus_name) ? ucwords($transaction->bus_name) : "N/A";

    if(!empty($transaction->bus_name) && $transaction->bus_name !== 'null') {
        $reference = "<a class='text-primary' href='{$baseUrl}{$transaction->attach_to_object}/{$transaction->record_object}'>{$reference} Object</a>";
    }

    $list_transactions .= "<tr data-row_id=\"{$transaction->item_id}\">";
    $list_transactions .= "<td class='text-center'>{$count}</td>";
    $list_transactions .= "<td>{$reference}</td>";
    $list_transactions .= "<td>{$transaction->account_type_name}</td>";
    $list_transactions .= "<td>".date("jS M Y", strtotime($transaction->record_date))."</td>";
    $list_transactions .= "<td>".number_format($transaction->amount, 2)."</td>";
    $list_transactions .= "<td>{$transaction->item_type}</td>";
    $list_transactions .= "<td class='text-center'>{$transaction->state_label}</td>";
    $list_transactions .= "<td class='text-center'><span data-action_id='{$transaction->item_id}'>{$action}</span></td>";
    $list_transactions .= "</tr>";

    $transactions_array_list[$transaction->item_id] = filterAccountingObject($transaction);
}

$response->scripts = ["assets/js/accounting.js", "assets/js/upload.js"];
$response->array_stream["transactions_array_list"] = $transactions_array_list;

$charts = '';
$chartContent = [
    [
        'label' => 'Income',
        'color' => 'blue',
        'type' => 'Deposit',
        'icon' => 'fas fa-money-bill-alt'
    ],
    [
        'label' => 'Expense',
        'color' => 'red',
        'type' => 'Expense',
        'icon' => 'fas fa-money-check-alt'
    ],
    [
        'label' => 'Balance',
        'color' => 'green',
        'type' => 'Balance',
        'icon' => 'fas fa-balance-scale'
    ]
];

foreach($chartContent as $each) {
$charts .= '
    <div class="col-xl-3 col-lg-3 col-md-6 hover:scale-105 transition-all duration-300">
        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-'.$each['color'].'">
            <div class="card-body pr-2 pl-3 card-type-3">
                <div class="row">
                    <div class="col">
                        <h6 class="font-14 text-uppercase font-bold mb-0">'.$each['label'].'</h6>
                        <span class="font-bold font-20 mb-0" data-summary="'.$each['type'].'">'.$defaultCurrency.' '.number_format(0, 2).'</span>
                    </div>
                    <div class="col-auto">
                        <div class="bg-'.$each['color'].' text-white card-circle">
                            <i class="fas '.$each['icon'].'"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

// document information
$response->html = '
<section class="section">
    <div class="section-header">
        <h1><i class="fa fa-bus"></i> '.$pageTitle.'</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'buses">Buses</a></div>
            <div class="breadcrumb-item">'.$pageTitle.'</div>
        </div>
    </div>
    <div class="row" data-summary="bus_financials">
        '.$charts.'
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No.</th>
                                    <th width="22%">Bus</th>
                                    <th>Account</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th width="8%">Type</th>
                                    <th class="text-center">Status</th>
                                    <th width="12%" class="text-center">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                '.$list_transactions.'
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>';

// print out the response
echo json_encode($response);