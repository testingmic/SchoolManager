<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $session, $myClass, $accessObject;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Payment Transactions";

$response->title = $pageTitle;

// not found
if(!$isSupport) {
    // end the query here
    $response->html = page_not_found("permission_denied");
    // echo the response
    echo json_encode($response);
    exit;
}

// set the transaction id
$transaction_id = $SITEURL[1] ?? null;

// set the parameters
$parameter = (object)[
    "clientId" => $isSupport ? null : $clientId,
    "userData" => $defaultUser,
    "client_data" => $defaultUser->client,
    "transaction_id" => $transaction_id ?? null
];

// append the limit
if(!empty($transaction_id)) {
    $parameter->limit = 1;
    $pageTitle = 'Transaction Info';
}

// get the list of all the account types
$accountsObject = load_class("accounting", "controllers", $parameter);
$payment_transactions = $accountsObject->epayment_transactions($parameter)["data"] ?? [];

// create a new payment object
$payObject = load_class("payment", "controllers");

// reponse array
$responses = [
    "Invalid" => "danger",
    "Approved" => "success",
    "approved" => "success",
    "Failed" => "danger",
    "Abandoned" => "primary",
    "abandoned" => "danger",
    "success" => "success",
    "action" => "primary"
];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-lock"></i> Payment Transactions</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                '.(!empty($transaction_id) ? '<div class="breadcrumb-item"><a href="'.$baseUrl.'payment_transactions">Transactions List</a></div>' : null).'
                <div class="breadcrumb-item active">'.$pageTitle.'</div>
            </div>
        </div>
        '.(!empty($transaction_id) && $isSupport ? '
            <div>
                <div class="alert alert-warning">
                    Please ensure you are familiar with the paystack payment api before using it.
                </div>
            </div>' : null
        ).'
        <div class="row">';
            // if the transaction id was not parsed
            if(empty($transaction_id)) {
                // show the list of all transaction details
                $response->html .= '
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-striped table-md raw_datatable">
                                <thead>
                                    <th width="5%">#</th>
                                    <th>Amount</th>
                                    <th>Customer</th>
                                    <th>Reference</th>
                                    <th>Channel</th>
                                    <th>Paid On</th>
                                    <th>Status</th>
                                    <th width="10%"></th>
                                </thead>
                                <tbody>';
                                foreach($payment_transactions as $key => $item) {
                                    $payment_data = $item->payment_data->data ?? [];
                                    $response->html .= "
                                    <tr>
                                        <td>".($key+1)."</td>
                                        <td><strong>
                                            ".($payment_data->currency ?? null)." ".($item->amount ?? null)."
                                            </strong>
                                        </td>
                                        <td>
                                            ".($payment_data->customer->email ?? null)."
                                        </td>
                                        <td>{$item->reference_id}</td>
                                        <td class='text-center'>
                                            ".($payment_data->authorization->channel ?? null)."
                                        </td>
                                        <td>
                                            ".(!empty($payment_data->paid_at) ? date("l, M d, Y h:i A", strtotime($payment_data->paid_at)) : null)."
                                        </td>
                                        <td>
                                            ".(!empty($payment_data->gateway_response) ? "<span class='badge badge-{$responses[$payment_data->status]}'>{$payment_data->status}</span>" : "")."
                                        </td>
                                        <td class='text-center'>
                                            <button onclick='load(\"payment_transactions/{$item->reference_id}\")' class='btn btn-outline-primary btn-sm'>
                                                <i class='fa fa-eye'></i> View
                                            </button>
                                        </td>
                                    </tr>";
                                }
                                $response->html .='
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>';
            } else {
                // if the trasaction info was parsed
                if(empty($payment_transactions)) {
                    $response->html = page_not_found();
                } else {
                    // get the transaction data
                    $data = $payment_transactions[0]->payment_data->data ?? [];
                    $state = $payment_transactions[0]->state;

                    // if the data is empty
                    if(empty($data)) {
                        // set the parameters
                        $_param = (object) ["route" => "verify", "reference" => $transaction_id];
                        
                        // confirm the payment
                        $payment_check = $payObject->get($_param);

                        // update the table information if the data is not empty
                        if(!empty($payment_check["data"]) && isset($payment_check["data"]->status) && ($payment_check["data"]->status === true)) {
                            // set the value data
                            $data = $payment_check["data"];

                            // update the database
                            $myschoolgh->query("UPDATE transaction_logs SET state='Processed', payment_data='".json_encode($data)."' WHERE reference_id='{$transaction_id}' LIMIT 3");

                            // set the value data
                            $data = $payment_check["data"]->data;
                        }
                        elseif(!empty($payment_check["data"]) && ($payment_check["data"]->message == "Transaction reference not found")) {
                            // update the database
                            $myschoolgh->query("UPDATE transaction_logs SET state='Invalid', payment_data='".json_encode($payment_check)."' WHERE reference_id='{$transaction_id}' LIMIT 3");
                            // set the new state
                            $state = "Invalid";
                        }
                    }

                    // if the state is invalid then end the query
                    if($state == "Invalid") {

                        $response->html .= "
                        <div class='col-md-12'>
                            <div class='card'>
                                <div class='card-body text-danger'>
                                    Transaction not found.
                                </div>
                            </div>
                        </div>";

                    } else {

                        // load the receipt attached to this transaction
                        $item_param = (object) [
                            "clientId" => $isSupport ? null : $clientId,
                            "reference_id" => $transaction_id,
                            "client_data" => $defaultUser->client,
                            "userData" => $defaultUser,
                            "limit" => 1
                        ];
                        $receipt = load_class("fees", "controllers", $item_param)->list($item_param)["data"];
                        
                        // get the data
                        $record = $receipt[0] ?? [];
                        
                        // display the content
                        $response->html .= "
                        <div class='col-md-6'>
                            <div class='card'>
                                <div class='card-body'>
                                    <table class='table table-bordered table-striped'>
                                        <tr>
                                            <td width='40%'>State</td>
                                            <td align='right' class='font-bold'>
                                                <div>
                                                    ".(!empty($data->gateway_response) ? "<span class='badge badge-{$responses[$data->status]}'>{$data->status}</span>" : "")."
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width='40%'>Reference</td>
                                            <td align='right' class='font-bold'>
                                                {$transaction_id}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Channel</td>
                                            <td align='right' class='font-bold'>
                                                ".ucwords(str_ireplace("_", " ", $data->channel))."
                                            </td>
                                        </tr>
                                        ".($isSupport && !empty($data->fees_split) ?
                                            "<tr>
                                                <td>Your Account</td>
                                                <td align='right' class='font-bold'>
                                                    ".number_format(($data->fees_split->integration / 100), 3)."
                                                </td>
                                            </tr>" : null
                                        )."
                                        ".($isSupport && !empty($data->fees_split) ?
                                            "<tr>
                                                <td>{$data->subaccount->business_name}</td>
                                                <td align='right' class='font-bold'>
                                                    ".number_format(($data->fees_split->subaccount / 100), 3)."
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fees</td>
                                                <td align='right' class='font-bold'>
                                                    ".number_format(($data->fees_split->paystack / 100), 3)."
                                                </td>
                                            </tr>" : null
                                        )."
                                        <tr>
                                            <td>Paid At</td>
                                            <td align='right' class='font-bold'>
                                                ".(!empty($data) && !empty($data->paid_at) ? 
                                                    date("l, M d, Y h:i A", strtotime($data->paid_at)) : 
                                                    null
                                                )."
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Message</td>
                                            <td align='right' class='font-bold'>
                                                {$data->gateway_response}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-6'>
                            <div class='card'>
                                <div class='card-header'>Analytics</div>
                                <div class='card-body pb-0'> 
                                    <div class='row'>
                                        <div class='col-md-6 mb-3'>
                                            <div><strong>Fullname</strong></div>
                                            <div>{$data->customer->first_name} {$data->customer->last_name}</div>
                                        </div>
                                        <div class='col-md-6 mb-3'>
                                            <div><strong>Email Address</strong></div>
                                            <div>{$data->customer->email}</div>
                                        </div>
                                        <div class='col-lg-12 mb-3 border-bottom'></div>
                                        <div class='col-md-6 mb-3'>
                                            <div><strong>Mobile Money Number</strong></div>
                                            <div>
                                            ".(!empty($data->authorization->bin) ? "
                                                {$data->authorization->bin}***
                                                ".strtolower($data->authorization->last4)."" : null
                                            )."
                                            </div>
                                        </div>
                                        ".(!empty($data->authorization->authorization_code) ?
                                            "<div class='col-md-6 mb-3'>
                                                <div><strong>Authorization</strong></div>
                                                <div>{$data->authorization->authorization_code}</div>
                                            </div>" : null
                                        )."
                                        ".(!empty($data->authorization->country_code) ?
                                            "<div class='col-md-6 mb-3'>
                                                <div><strong>Bank and Country</strong></div>
                                                <div>{$data->authorization->bank} ({$data->authorization->country_code})</div>
                                            </div>" : null
                                        )."
                                        <div class='col-md-6 mb-3'>
                                            <div><strong>IP Address</strong></div>
                                            <div>
                                                <a target='_blank' href='https://db-ip.com/{$data->ip_address}'>
                                                    {$data->ip_address}
                                                </a>
                                            </div>
                                        </div>";
                                        // if the logs history is not empty
                                        if(!empty($data->log->history)) {
                                            $response->html .= "
                                            <div class='col-lg-12 mb-3 border-bottom'></div>
                                            <div class='col-md-6 mb-3 font-20 p-3' align='center'>
                                                <div><strong>TIME SPENT</strong></div>
                                                <div>{$data->log->time_spent} seconds</div>
                                            </div>
                                            <div class='col-md-6 mb-3'>
                                                <div><strong class='text-primary'>ATTEMPTS</strong></div>
                                                <div class='mb-3'>{$data->log->attempts} attempt</div>
                                                <div><strong class='text-danger'>ERRORS</strong></div>
                                                <div>{$data->log->errors} errors</div>
                                            </div>
                                            <div class='col-lg-12 mb-3 border-bottom'></div>";
                                            $response->html .= '<div class="activities mb-0">';
                                            // loop through the logs list
                                            foreach($data->log->history as $log) {
                                                $response->html .= '
                                                <div class="activity">
                                                    <div class="activity-icon bg-'.$responses[$log->type].' text-white">
                                                        <i class="fas fa-info"></i>
                                                    </div>
                                                    <div class="activity-detail p-2">
                                                        <div class="mb-0">
                                                            <span class="text-job text-primary">
                                                                00:'.$log->time.'
                                                            </span>
                                                        </div>
                                                        <div class="mb-0">'.$log->message.'</div>
                                                    </div>
                                                </div>';
                                            }
                                            $response->html .= '</div>';
                                        }

                                    $response->html .= "
                                        </div>
                                </div>
                            </div>
                        </div>";

                        // if the record is not empty
                        if(!empty($record)) {
                            // append the receipt information
                            $response->html .= fees_receipt_data($receipt, $record, $record->payment_id, "col-lg-12");
                        }

                    }

                }
            }

    $response->html .= '
        </div>
    </section>';

// print out the response
echo json_encode($response);
?>