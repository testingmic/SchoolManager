<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$filter = (object) $_GET;
$pageTitle = "Simple Accounting - Accounts";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [
    "assets/js/accounting.js"
];

// set the parameters
$params = (object)[
    "clientId" => $clientId,
    "client_data" => $defaultUser->client,
    "account_id" => $filter->account_id ?? null
];
// get the list of all the account types
$accountsObject = load_class("accounting", "controllers", $params);
$list_data = $accountsObject->list_accounts($params)["data"];

// append the data to the params
if(!empty($list_data) && !empty($params->account_id)) {
    $params->data = $list_data[0];
}

// init value
$accounts_list = "";
$bank_accounts_array = [];

// if the user has the required permissions
$hasUpdate = $accessObject->hasAccess("accounts", "accounting");

// loop through the list of account type heads
foreach($list_data as $key => $each) {
    // append to the array list
    $bank_accounts_array[$each->item_id] = $each;

    // set the action button
    $action = "";
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to edit this bank account details' href='#' onclick='return update_bank_account(\"{$each->item_id}\");' class='btn mb-1 btn-sm btn-outline-success'><i class='fa fa-edit'></i> Update Record</a>";
        // $action .= "&nbsp;<a href='#' title='Click to delete this Account' onclick='return delete_record(\"{$each->item_id}\", \"accounts\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    // append to the rows
    $accounts_list .= "
    <div class='col-md-6'>
        <div class='card'>
            <div class='card-body p-2'>
                <h4>{$each->account_name}</h4>
                <table border='1' width='100%' class='table table-bordered table-striped'>
                    <tbody>
                        <tr>
                            <td><strong>Account Number: </strong></td>
                            <td><span class='font-18'>{$each->account_number}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Bank Name: </strong></td>
                            <td>{$each->account_bank}</td>
                        </tr>
                        <tr>
                            <td><strong>Opening Balance: </strong></td>
                            <td>{$each->currency} ".number_format($each->opening_balance, 2)."</td>
                        </tr>
                        <tr>
                            <td><strong>Total Credit: </strong></td>
                            <td>{$each->currency} ".number_format($each->total_credit, 2)."</td>
                        </tr>
                        <tr>
                            <td><strong>Total Debit: </strong></td>
                            <td>{$each->currency} ".number_format($each->total_debit, 2)."</td>
                        </tr>
                        <tr>
                            <td><strong>Current Balance: </strong></td>
                            <td>{$each->currency} ".number_format($each->balance, 2)."</td>
                        </tr>
                        <tr>
                            <td colspan='2'>{$each->description}</td>
                        </tr>
                        ".($hasUpdate ? "<tr><td colspan='2' align='center'>{$action}</td></tr>" : null)."
                    </tbody>
                </table>
            </div>
        </div>
    </div>";
}

// load the form
$the_form = $hasUpdate ? load_class("forms", "controllers")->bank_accounts_form($params) : null;
$response->array_stream["bank_accounts_array"] = $bank_accounts_array;

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
            <div class="col-12 '.($hasUpdate ? "col-md-7 col-lg-8" : "col-md-12").'">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> &nbsp; Accounts List</div>
                    <div class="card-body">

                        <div class="row">
                            '.$accounts_list.'
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>