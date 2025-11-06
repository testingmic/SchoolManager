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
$pageTitle = "Simple Accounting - Accounts";
$response->title = $pageTitle;
$response->scripts = ["assets/js/accounting.js"];

// set the parent menu
$response->parent_menu = "accounting";

// confirm that the user has the required permissions
if(!$accessObject->hasAccess("accounts", "accounting")) {
    // unset the page additional information
    $response->page_programming = [];
    // permission denied information
    $response->html = page_not_found("permission_denied");
} else {
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
    foreach($list_data as $key => $account) {
        // append to the array list
        $bank_accounts_array[$account->item_id] = $account;

        // set the action button
        $action = "";
        if($hasUpdate) {
            $action .= "&nbsp;<button title='Click to edit this bank account details' onclick='return update_bank_account(\"{$account->item_id}\");' class='btn mb-1 btn-sm btn-outline-success'><i class='fa fa-edit'></i> Update Record</button>";
        }

        // set the default account
        $default_account = $account->default_account ? "<span title='Default Primary Account' class='text-success'><i class='fa fa-check-circle'></i></span>" : null;

        // append to the rows
        $isClosed = (bool) ($account->state === "Closed");

        // append to the rows
        $accounts_list .= "
        <div class='col-lg-6 col-md-12'>
            <div class='card'>
                <div class='card-body p-2'>
                    <h4>{$account->account_name} <span data-account_id='{$account->item_id}' class='default_account'>{$default_account}</span></h4>
                    <div class='table-responsive'>
                    <table border='1' width='100%' class='table table-bordered table-striped'>
                        <tbody>
                            <tr>
                                <td><strong>Account Number: </strong></td>
                                <td><span class='font-18'>{$account->account_number}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Bank Name: </strong></td>
                                <td>{$account->account_bank}</td>
                            </tr>
                            <tr>
                                <td><strong>Opening Balance: </strong></td>
                                <td>{$account->currency} ".number_format($account->opening_balance, 2)."</td>
                            </tr>
                            <tr>
                                <td><strong>Total Credit: </strong></td>
                                <td>{$account->currency} ".number_format($account->total_credit, 2)."</td>
                            </tr>
                            <tr>
                                <td><strong>Total Debit: </strong></td>
                                <td>{$account->currency} ".number_format($account->total_debit, 2)."</td>
                            </tr>
                            <tr>
                                <td><strong>Current Balance: </strong></td>
                                <td>{$account->currency} ".number_format($account->balance, 2)."</td>
                            </tr>
                            <tr>
                                <td colspan='2'>{$account->description}</td>
                            </tr>
                            <tr>
                                <td colspan='2' align='center'>
                                    ".($isClosed ? "<span class='btn btn-danger btn-sm'>Account Closed</span>" : 
                                        "".($hasUpdate ? "{$action}" : null)."
                                        <span data-account_id='{$account->item_id}' class='default_account_button'>".(!$account->default_account ? "<button onclick='return mark_as_default(\"{$account->item_id}\")' data-account_id='{$account->item_id}' class='btn mb-1 btn-primary btn-sm'>Set As Default</button>" : null)."</span>"
                                    )."
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>";
    }

    if(empty($list_data)) {
        $accounts_list .= "<div class='text-danger text-center col-lg-12'>Sorry! No accounts have been added yet.</div>";
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
}

// print out the response
echo json_encode($response);
?>