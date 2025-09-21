<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $clientFeatures;

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
    "client_data" => $defaultUser->client,
    "transaction_id" => $filter->transaction_id ?? null,
    "account_id" => $filter->account_id ?? null,
    "account_type" => $filter->account_type ?? null
];

// get the transactions list
$transactions_list = load_class("accounting", "controllers", $params)->list_transactions($params)["data"];

$statistics = [
    'count' => 0,
    'summation' => 0,
    'summation_by_type' => [
        'Deposit' => 0,
        'Expense' => 0
    ],
    'type' => [],
    'days_chart' => []
];

$count = 0;
$list_transactions = "";
$transactions_array_list = [];
foreach($transactions_list as $key => $transaction) {
    // set the transaction array list
    $transactions_array_list[$transaction->item_id] = $transaction;
    $count++;

    // set the statistics
    $statistics['count']++;
    $statistics['summation'] += $transaction->amount;
    $statistics['type'][$transaction->item_type] = isset($statistics['type'][$transaction->item_type]) ? $statistics['type'][$transaction->item_type] + 1 : 1;

    // set the summation by type
    $statistics['summation_by_type'][$transaction->item_type] += $transaction->amount;

    // set the days chart
    if(!isset($statistics['days_chart'][$transaction->record_date])) {
        $statistics['days_chart'][$transaction->record_date] =  [
            'transactions' => 0,
            'labels' => [
                'Deposit' => 0,
                'Expense' => 0
            ],
            'data' => [
                'Deposit' => 0,
                'Expense' => 0
            ]
        ];
    }
    $statistics['days_chart'][$transaction->record_date]['transactions']++;

    // set the labels and data
    if(!isset($statistics['days_chart'][$transaction->record_date]['labels'][$transaction->item_type])) {
        $statistics['days_chart'][$transaction->record_date]['labels'][$transaction->item_type] = 0;
    }
    $statistics['days_chart'][$transaction->record_date]['labels'][$transaction->item_type]++;
    $statistics['days_chart'][$transaction->record_date]['data'][$transaction->item_type] += $transaction->amount;

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

    $reference = !empty($transaction->attach_to_object) ? ucwords($transaction->attach_to_object) : "N/A";

    if(!empty($transaction->record_object) && $transaction->record_object !== 'null') {
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
}

$response->scripts = ["assets/js/accounting.js", "assets/js/upload.js"];
$response->array_stream["transactions_array_list"] = $transactions_array_list;
$response->array_stream["statistics"] = $statistics;

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
    <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No.</th>
                                <th width="22%">Bus</th>
                                <th>Driver</th>
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