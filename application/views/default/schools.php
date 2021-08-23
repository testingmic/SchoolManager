<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed OR the request_method is not POST
jump_to_main($baseUrl);

// initial variables
global $accessObject, $defaultUser, $isSupport, $defaultClientData, $clientPrefs, $SITEURL, $usersClass;
$appName = config_item("site_name");

$clientId = $session->clientId;
$loggedUserId = $session->userId;

// filters
$response = (object) [];
$response->title = "Manage School : {$appName}";

// client id
$client_id = $SITEURL[1] ?? $session->clientId;

// if not booking set
if(!empty($client_id)) {

    // init values
    $schools_list = "";
    $load_schools_list = $myClass->pushQuery(
        "*", 
        "clients_accounts", 
        "client_id='{$client_id}' LIMIT 1"
    );

    // error page
    // if no record was found
    if(empty($load_schools_list)) {
        $response->html = page_not_found("access_denied");
    } else {

        // reset the client id
        $client_id = !$isSupport ? $session->clientId : $client_id;

        $params = (object) ["clientId" => $client_id];
        $data = $load_schools_list[0];
        $data->client_preferences = json_decode($data->client_preferences);
        $data->analitics = load_class("account", "controllers")->client($params);
        $clientPref = $data->client_preferences;

        // set is disabled
        $is_disabled = $isSupport ? null : "disabled='disabled'";

        // set variables
        $account = "";
        $academics = "";
        $analitics = "";
        if(isset($clientPref->academics)) {
            foreach($clientPref->academics as $key => $value) {
                $item = ucwords(str_ireplace("_", " ", $key));
                $academics .= "<tr><td class='text-uppercase' width='40%'><strong>{$item}:</strong></td><td class='font-17'>{$value}</td></tr>";
            }
        }

        foreach($data->analitics as $key => $value) {
            $item = ucwords(str_ireplace("_", " ", $key));
            $analitics .= "<tr><td class='text-uppercase' width='40%'><strong>{$item}:</strong></td><td class='font-17'>{$value}</td></tr>";
        }

        if(isset($clientPref->account)) {
            foreach($clientPref->account as $key => $value) {
                $key = ucwords(str_ireplace("_", " ", $key));
                $account .= "<tr><td class='text-uppercase' width='40%'><strong>{$key}:</strong></td><td class='font-17'>".ucwords($value)."</td></tr>";
            }
        }

        // set the html string
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-landmark"></i> Manage School</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Manage School</div>
                </div>
            </div>
            <div class="row">
                
                <div class="col-md-4">
                    <div class="card author-box pt-2">
                        <div class="card-body">


                            <div class="author-box-center m-0 p-0">
                                <img alt="image" src="'.$baseUrl.''.$data->client_logo.'" class="profile-picture">
                            </div>
                            <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->client_name.'</div>
                            <div class="text-center border-top mt-0 mb-2">
                                <div class="author-box-description font-22 text-success font-weight-bold">'.$data->client_id.'</div>
                            </div>

                            <div class="text-center border-top">
                                <div class="mt-2">'.(!empty($data->client_email) ? "<i class='fa fa-envelope'></i> {$data->client_email}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_contact) ? "<i class='fa fa-phone'></i> {$data->client_contact} / {$data->client_secondary_contact}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_address) ? "<i class='fa fa-home'></i> {$data->client_address}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_location) ? "<i class='fa fa-map-marked-alt'></i> {$data->client_location}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_website) ? "<i class='fa fa-globe'></i> {$data->client_website}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_slogan) ? "<i class='fa fa-comments'></i> {$data->client_slogan}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.($myClass->the_status_label($data->client_state)).'</div>
                            </div>
                            <div class="w-100 mt-2 border-top text-center pt-3">
                                <a class="btn btn-dark" href="'.$baseUrl.'dashboard"><i class="fa fa-arrow-circle-left"></i> Go Back</a>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="padding-20">
                                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="account-tab2" data-toggle="tab" href="#account" role="tab" aria-selected="true">ACCOUNT</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="account_analysis-tab2" data-toggle="tab" href="#account_analysis" role="tab" aria-selected="true">ANALYSIS</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">ACADEMIC CALENDAR</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="modify_account-tab2" data-toggle="tab" href="#modify_account" role="tab" aria-selected="true">UPDATE ACCOUNT</a>
                                    </li>
                                    '.(!$is_disabled ? '
                                    <li class="nav-item">
                                        <a class="nav-link" id="sms-tab2" data-toggle="tab" href="#sms" role="tab" aria-selected="true">SMS</a>
                                    </li>' : null).'
                                </ul>
                                <div class="tab-content tab-bordered" id="myTab3Content">
                                    <div class="tab-pane fade show active" id="account" role="tabpanel" aria-labelledby="account-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive">
                                                <table class="table table-success table-striped table-bordered table-md">
                                                    <thead><tr><th colspan="2">CLIENT ACCOUNT INFORMATION</th></tr></thead>
                                                    <tbody>'.$account.'</tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account_analysis" role="tabpanel" aria-labelledby="account_analysis-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive">
                                                <table class="table table-info table-striped table-bordered table-md">
                                                    <thead><tr><th colspan="2">CLIENT DATA ANALYSIS</th></tr></thead>
                                                    <tbody>'.$analitics.'</tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="modify_account" role="tabpanel" aria-labelledby="modify_account-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive">
                                                '.(!$is_disabled ? '<form class="ajax-data-form" action="'.$baseUrl.'api/account/modify" method="POST" id="ajax-data-form-content">' : null).'
                                                    <table class="table table-striped table-bordered table-md">
                                                        <tr>
                                                            <th colspan="2">MODIFY ACCOUNT</th>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%"><strong>PACKAGE</strong></td>
                                                            <td>
                                                                <select '.$is_disabled.' data-width="100%" name="data[account_package]" class="form-control selectpicker">
                                                                    <option '.($clientPref->account->package == "trial" ? "selected" : null).' value="basic">Trial Package</option>
                                                                    <option '.($clientPref->account->package == "basic" ? "selected" : null).' value="basic">Basic Package</option>
                                                                    <option '.($clientPref->account->package == "standard" ? "selected" : null).' value="standard">Standard Package</option>
                                                                    <option '.($clientPref->account->package == "premium" ? "selected" : null).' value="premium">Premium Package</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%"><strong>EXPIRY</strong></td>
                                                            <td>
                                                                <input '.$is_disabled.' data-maxdate="'.date("Y-m-d", strtotime("+5 years")).'" value="'.date("Y-m-d", strtotime($clientPref->account->expiry)).'" name="data[account_expiry]" id="account_expiry" class="form-control datepicker">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%"><strong>STATUS</strong></td>
                                                            <td>
                                                                <select '.$is_disabled.' data-width="100%" name="data[client_state]" class="form-control selectpicker">
                                                                    <option '.($data->client_state == "Active" ? "selected" : null).' value="Active">Active</option>
                                                                    <option '.($data->client_state == "Suspended" ? "selected" : null).' value="Suspended">Suspended</option>
                                                                    <option '.($data->client_state == "Expired" ? "selected" : null).' value="Expired">Expired</option>
                                                                    <option '.($data->client_state == "Activated" ? "selected" : null).' disabled value="Activated">Activated</option>
                                                                    <option '.($data->client_state == "Propagation" ? "selected" : null).' disabled value="Propagation">Propagation</option>
                                                                    <option '.($data->client_state == "Complete" ? "selected" : null).' disabled value="Complete">Complete</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%"><strong>SMS SENDER NAME</strong></td>
                                                            <td>
                                                                <div class="text-danger text-center">Please if you are to change ensure it matches what has been created on <strong>MNotify\'s Senders List</strong>. If not use the Default <strong>'.$myClass->sms_sender.'</strong></div>
                                                                <input '.$is_disabled.' value="'.$data->sms_sender.'" name="data[sms_sender]" id="sms_sender" class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%"><strong>PAYSTACK ACCOUNT ID</strong></td>
                                                            <td>
                                                                <div class="text-danger text-center">Please ensure the same ID below matches the one created within the <strong>PayStack Subaccounts</strong>.</div>
                                                                <input '.$is_disabled.' value="'.$data->client_account.'" name="data[client_account]" id="client_account" class="form-control">
                                                                <input hidden type="hidden" readonly value="'.$data->client_id.'" name="data[client_id]" id="client_id" class="form-control">
                                                            </td>
                                                        </tr>
                                                        '.(!$is_disabled ?
                                                        '<tr>
                                                            <td width="35%"></td>
                                                            <td align="right">
                                                                <button type="button-submit" class="btn btn-outline-success"><i class="fa fa-save"></i> Save</button>
                                                            </td>
                                                        </tr>' : null).'
                                                    </table>
                                                '.(!$is_disabled ? '</form>' : null).'
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive">
                                                <table class="table table-striped table-bordered table-md">
                                                    <tr>
                                                        <th colspan="2">ACADEMIC YEAR INFORMATION</th>
                                                    </tr>
                                                    '.$academics.'
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    '.(!$is_disabled ? '
                                    <div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive">
                                                <table class="table table-bordered table-md">
                                                    <thead><tr><th colspan="2">ACCOUNT SMS DETAILS</th></tr></thead>
                                                    <tbody>
                                                        <tr>
                                                            <td width="40%">ACCOUNT BALANCE</td>
                                                            <td class="font-25">'.$data->analitics->sms_balance.'</td>
                                                        </tr>
                                                        <tr>
                                                            <td width="30%">TOPUP ACCOUNT BALANCE</td>
                                                            <td><input type="number" name="sms_topup" class="form-control font-20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%"></td>
                                                            <td align="right">
                                                                <button onclick="return topup_sms_balance()" class="btn btn-outline-success"><i class="fa fa-save"></i> Topup Account</button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>' : null).'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
        </section>';

    }
        
} else {
    $response->html = page_not_found("access_denied");
}

// print out the response
echo json_encode($response);
?>