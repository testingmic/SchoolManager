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
$response->scripts = ["assets/js/accounting.js", "assets/js/upload.js"];

// get the list of all classes
$params = (object)[
    "route" => "expense",
    "clientId" => $clientId,
    "item_type" => "Expense",
    "userData" => $defaultUser,
    "client_data" => $defaultUser->client,
    "transaction_id" => $filter->transaction_id ?? null
];

// get the transactions list
$transactions_list = load_class("accounting", "controllers", $params)->list_transactions($params)["data"];

// confirm that the user has the required permissions
$the_form = load_class("forms", "controllers")->transaction_form($params);

// permission to modify and validate
$hasValidate = $accessObject->hasAccess("validate", "accounting");
$hasModify = $accessObject->hasAccess("modify", "accounting");

$count = 0;
$list_transactions = "";
$transactions_array_list = [];
foreach($transactions_list as $key => $transaction) {
    $transactions_array_list[$transaction->item_id] = $transaction;
    $count++;

    // view button
    $action = "<button onclick='return view_transaction(\"{$transaction->item_id}\");' title='Click to view full details of transaction' class='btn btn-outline-success mb-1 btn-sm'><i class='fa fa-eye'></i></button>";

    // if the record is still pending
    if($transaction->state === "Pending") {
        // validate the transaction
        $action .= "&nbsp;<button onclick='return validate_transaction(\"{$transaction->item_id}\",\"{$baseUrl}expenses\")' class=\"btn btn-sm btn-outline-primary mb-1\" title=\"Validate Transaction\"><i class='fa fa-check'></i></button>";

        // if the user has permission to modify record
        if($hasModify) {
            $action .= "&nbsp;<button onclick='return reverse_transaction(\"{$transaction->item_id}\");' title='Click to reverse this transaction' class='btn btn-outline-danger mb-1 btn-sm'><i class='fa fa-recycle'></i></button>";
        }
    }

    $list_transactions .= "<tr data-row_id=\"{$transaction->item_id}\">";
    $list_transactions .= "<td>{$count}</td>";
    $list_transactions .= "<td>{$transaction->account_name}</td>";
    $list_transactions .= "<td>{$transaction->account_type_name}</td>";
    $list_transactions .= "<td>{$transaction->reference}</td>";
    $list_transactions .= "<td>".ucfirst($transaction->payment_medium)."</td>";
    $list_transactions .= "<td>".number_format($transaction->amount, 2)."</td>";
    $list_transactions .= "<td>".date("jS M Y", strtotime($transaction->record_date))."</td>";
    $list_transactions .= "<td align='center'>{$action}</td>";
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
                                    <a class="nav-link active" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true"><i class="fa fa-list"></i> Expense List</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="upload_reports-tab2" data-toggle="tab" href="#upload_reports" role="tab" aria-selected="true"><i class="fa fa-edit"></i> Add Expense</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                    <div class="table-responsive trix-slim-scroll">
                                        <table class="table table-bordered table-striped datatable">
                                            <thead>
                                                <th></th>
                                                <th>Account Name</th>
                                                <th>Account Type Head</th>
                                                <th>Ref No.</th>
                                                <th>Pay Via</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th></th>
                                            </thead>
                                            <tbody>'.$list_transactions.'</tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="upload_reports" role="tabpanel" aria-labelledby="upload_reports-tab2">
                                    '.$the_form.'
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