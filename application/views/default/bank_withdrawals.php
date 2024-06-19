<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) $_GET;
$pageTitle = "Accounting - Bank Withdrawals";
$response->title = $pageTitle;

// permission to modify and validate
$expensePermission = $accessObject->hasAccess("expenditure", "accounting");

// if the user does not have the required permissions
if(!$expensePermission) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// scripts to load into the page
$response->scripts = ["assets/js/accounting.js", "assets/js/upload.js"];

// set the parameters
$parameter = (object)[
    "clientId" => $clientId,
    "type" => "Withdrawal",
    "form_type" => "Withdrawal",
    "form_url" => "bank_withdrawal",
    "form" => [
        "amount" => "Withdrawn"
    ],
    "userData" => $defaultUser,
    "client_data" => $defaultUser->client,
    "transaction_id" => $filter->transaction_id ?? null
];

// get the list of all the account types
$accountsObject = load_class("accounting", "controllers", $parameter);
$list_data = $accountsObject->list_bank_transactions($parameter)["data"];

// append the data to the parameter
if(!empty($list_data) && !empty($parameter->transaction_id)) {
    $parameter->data = $list_data[0];
}

// init value
$type_list = "";
$bank_transactions_array = [];

// values
$total = 0;
$count = 0;
$average = 0;

// if the user has the required permissions
$canDeposit = $accessObject->hasAccess("bank_deposit", "accounting");

// loop through the list of account type heads
foreach($list_data as $key => $each) {
    // increment the values
    $count++;
    $total += $each->amount;

    // append to the array list
    $bank_transactions_array[$each->item_id] = $each;

    // set the action button
    $action = "&nbsp;<button title='Click to view' onclick='return view_bTranc(\"{$each->item_id}\");' class='btn mb-1 btn-sm btn-outline-success'><i class='fa fa-eye'></i></a>";

    // append to the rows
    $type_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $type_list .= "<td>".($key+1)."</td>";
    $type_list .= "<td>{$each->date_created}</td>";
    $type_list .= "<td>
        <strong>BANK:</strong> {$each->bank_name}<br>
        <strong>NAME:</strong> {$each->account_name}<br>
        <strong>NUMBER:</strong> {$each->account_number}<br>
    </td>";
    $type_list .= "<td><span class='badge badge-danger'>".($each->transaction_type)."</span></td>";
    $type_list .= "<td>{$each->amount}</td>";
    $type_list .= "<td align='center'>{$action}</td>";
    $type_list .= "</tr>";
}

// calculate the average
$average = $total > 0 ? round(($total / $count), 2) : null;

// load the form
$the_form = $canDeposit ? load_class("forms", "controllers")->bank_transaction_form($parameter) : null;
$response->array_stream["bank_transactions_array"] = $bank_transactions_array;

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
            '.$the_form.'
            <div class="col-12 '.($canDeposit ? "col-md-7 col-lg-8" : "col-md-12").'">

                <div class="row">

                    <div class="col-lg-4 col-md-6">
                        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-blue mb-3">
                            <div class="card-body pl-2 pr-2 card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-weight-bold mb-0">
                                            COUNT
                                        </h6>
                                        <h3 class="font-weight-bold mb-0">'.$count.'</h3>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-cyan text-white">
                                            <i class="fas fa-coins"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-success mb-3">
                            <div class="card-body pl-2 pr-2 card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-weight-bold mb-0">
                                            TOTAL
                                        </h6>
                                        <h3 class="font-weight-bold mb-0">'.number_format($total, 2).'</h3>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-green text-white">
                                            <i class="fas fa-money-check-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-warning mb-3">
                            <div class="card-body pl-2 pr-2 card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-weight-bold mb-0">
                                            AVERAGE
                                        </h6>
                                        <h3 class="font-weight-bold mb-0">'.number_format($average, 2).'</h3>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-orange text-white">
                                            <i class="fas fa-adjust"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>

                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> &nbsp; Bank Withdrawal List</div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-sm table-striped raw_datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="12%">Date</th>
                                        <th>Bank Name</th>
                                        <th width="14%">Type</th>
                                        <th width="13%">Amount</th>
                                        <th width="10%" align="center"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$type_list.'</tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>