<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed OR the request_method is not POST
jump_to_main($baseUrl);

// initial variables
global $accessObject, $defaultUser, $isSupport, $defaultClientData, $clientPrefs, $SITEURL, $usersClass, $defaultAcademics;
$appName = $myClass->appName;

$clientId = $session->clientId;
$loggedUserId = $session->userId;

// filters
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Package Account Manager ";

// client id
$client_id = $SITEURL[1] ?? $session->clientId;

// check the user permission
if(!$isSupport && !$accessObject->hasAccess("manage", "settings")) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// if not booking set
if(!empty($client_id)) {

    // reset the client id
    $client_id = !$isSupport ? $session->clientId : $client_id;

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

        // include scripts
        $response->scripts = ["assets/js/academics.js"];

        $params = (object) ["clientId" => $client_id, "limit" => 1];
        $data = $load_schools_list[0];
        $data->client_preferences = json_decode($data->client_preferences);
        $data->analitics = load_class("account", "controllers")->client($params);
        $thisClientPref = $data->client_preferences;

        // set the features
        $schoolFeatures = !empty($thisClientPref->features_list) ? (array) $thisClientPref->features_list : [];
        $academicSession = $thisClientPref->sessions->session ?? "Term";
        
        // set is disabled
        $is_disabled = $isSupport ? null : "disabled='disabled'";

        // disable all items if the account has expired or is suspended
        if(!$isSupport) {
            if(in_array($data->client_state, ["Suspended", "Expired"])) {
                $is_disabled = "disabled='disabled'";
            } else {
                $is_disabled = null;
            }
        }

        // set variables
        $account = "";
        $academics = "";
        $analitics = "";
        $features_list = "";

        if(isset($thisClientPref->academics)) {
            foreach($thisClientPref->academics as $key => $value) {
                $item = ucwords(str_ireplace(["_", "Term", "Semester"], [" ", $academicSession, $academicSession], $key));
                $academics .= "<tr><td class='text-uppercase' width='40%'><strong>{$item}:</strong></td><td class='font-17'>".strtoupper($value)."</td></tr>";
            }
        }

        foreach($data->analitics as $key => $value) {
            $item = ucwords(str_ireplace("_", " ", $key));
            $analitics .= "<tr><td class='text-uppercase' width='40%'><strong>{$item}:</strong></td><td class='font-17'>{$value}</td></tr>";
        }

        if(isset($thisClientPref->account)) {
            foreach($thisClientPref->account as $key => $value) {
                $key = ucwords(str_ireplace("_", " ", $key));
                $account .= "<tr><td class='text-uppercase' width='40%'><strong>{$key}:</strong></td><td class='font-17'>".(
                    !empty($value) ? ucwords($value) : null
                )."</td></tr>";
            }
        }

        // import list
        $features_list = "<table class='table table-bordered table-md table-striped'>";

        // append to the features list if the user is an admin
        if($isSupport || in_array("e_payments", $schoolFeatures)) {
            $myClass->features_list["e_payments"] = "Card MoMo Payments";
        }

        // set the error message 
        $error_message = in_array("e_payments", $schoolFeatures) && empty($data->client_account) ? 
            "<tr><th colspan='2'><div class='text-danger text-center'>PayStack account to help receive e-Payments has not been created and set for the school.</div></th></tr>" : null;

        // list the features to show
        foreach($myClass->features_list as $key => $feature) {
            $features_list .= "<tr>";
            $features_list .= "<td width='50%' class='text-uppercase'><label class='cursor' title='Click to select this feature' for='features[$key]'>{$feature}</label></td>";
            $features_list .= "<td><input ".($key == "e_payments" && in_array($key, $schoolFeatures) && !$isSupport ? "disabled" : null)." {$is_disabled} ".(in_array($key, $schoolFeatures) ? "checked" : null)." style='width:20px;height:20px' title='Click to select this feature' data-menu_item='{$key}' class='cursor' id='features[$key]' type='checkbox' name='features[$key]' ></td>";
            $features_list .= "</tr>";
        }
        $features_list .= !$is_disabled ? 
            '<tr>
                <td width="50%"></td>
                <td align="left">
                    <button onclick="return save_client_features(\''.$client_id.'\')" class="btn btn-outline-success"><i class="fa fa-save"></i> Save Record</button>
                </td>
            </tr>' : null;

        $features_list .= "</table>";


        // if not a support admin logged in
        if(!$isSupport) {
            // import list
            $import_list = [
                "grading_system" => "Grading System",
                "courses" => "Subjects",
                "courses_plan" => "Lesson Plan",
                "courses_resource" => "Subject Resources"
            ];
            
            // import div
            $import_div = "";
            $count = 0;
            $disabled = !empty($session->is_only_readable_app) ? "disabled" : null;

            foreach($import_list as $key => $value) {
                $count++;
                $import_div .= "
                <div class=\"col-md-6\">
                    <div class=\"form-group\">
                        <label style=\"font-size:13px\" class=\"cursor\" for=\"{$key}_{$count}\">{$value}</label>
                        <input {$disabled} ".($key === "students" ? "checked disabled='disabled'" : "checked")." id=\"{$key}_{$count}\" style=\"height:20px;width:20px;\" class=\"cursor data_to_import\" name=\"data_to_import[]\" value=\"{$key}\" type=\"checkbox\">
                    </div>
                </div>";
            }

            // check the promotions log to ensure none is left ideal
            $promotion_log = $myClass->pushQuery("count(*) AS list_count", "promotions_history", "client_id='{$clientId}' AND status='Pending' LIMIT {$myClass->temporal_maximum}");
        }

        // init
        $limits_analysis = "";

        // load the client limits
        $limit = $myClass->pushQuery("*", "clients_accounts_limit", "client_id='{$client_id}' LIMIT 1")[0] ?? [];

        // loop through the list
        if(!empty($limit)) {
            $limits_analysis = "
            <tr class='font-bold'>
                <td>ITEM</td>
                <td>STATUS</td>
            </tr>
            <tr class='font-bold'>
                <td>STUDENT</td>
                <td><span class='badge badge-".($limit->student ? "danger" : "success")."'>
                    ".($limit->student ? "Limit Reached" : "Active")."
                    </span>
                </td>
            </tr>
            <tr class='font-bold'>
                <td>STAFF</td>
                <td><span class='badge badge-".($limit->staff ? "danger" : "success")."'>
                    ".($limit->staff ? "Limit Reached" : "Active")."
                    </span>
                </td>
            </tr>
            <tr class='font-bold'>
                <td>FEES</td>
                <td><span class='badge badge-".($limit->fees ? "danger" : "success")."'>
                    ".($limit->fees ? "Limit Reached" : "Active")."
                    </span>
                </td>
            </tr>";
        }


        // set the html string
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-landmark"></i> Package Account Manager</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Package Account Manager</div>
                </div>
            </div>
            <div class="row">
                
                <div class="col-md-4">
                    <div class="card author-box pt-2">
                        <div class="card-body">
                            '.(!empty($data->client_logo) ? 
                                '<div class="author-box-center m-0 p-0">
                                    <img alt="image" src="'.$baseUrl.''.$data->client_logo.'" class="profile-picture">
                                </div>' : null
                            ).'
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
                                    '.(!$is_disabled  ? 
                                    '<li class="nav-item">
                                        <a class="nav-link" id="features-tab2" data-toggle="tab" href="#features" role="tab" aria-selected="true">FEATURES</a>
                                    </li>' : null).'
                                    '.(!$is_disabled && $isSupport ? '
                                    <li class="nav-item">
                                        <a class="nav-link" id="modify_account-tab2" data-toggle="tab" href="#modify_account" role="tab" aria-selected="true">EDIT ACCOUNT</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="sms-tab2" data-toggle="tab" href="#sms" role="tab" aria-selected="true">SMS</a>
                                    </li>' : null).'
                                    '.($accessObject->hasAccess("close", "settings") && !$isSupport  ? 
                                    '<li class="nav-item">
                                        <a class="nav-link" id="close_session-tab2" data-toggle="tab" href="#close_session" role="tab" aria-selected="true">CLOSE '.$academicSession.'</a>
                                    </li>' : null).'
                                </ul>
                                <div class="tab-content tab-bordered" id="myTab3Content">
                                    <div class="tab-pane fade show active" id="account" role="tabpanel" aria-labelledby="account-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive">
                                                <table class="table table-striped table-bordered table-md">
                                                    <thead><tr><th colspan="2">CLIENT ACCOUNT INFORMATION</th></tr></thead>
                                                    <tbody>'.$account.'</tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account_analysis" role="tabpanel" aria-labelledby="account_analysis-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive">
                                                <table class="table table-striped table-bordered table-md">
                                                    <thead><tr><th colspan="2">CLIENT DATA ANALYSIS</th></tr></thead>
                                                    <tbody>'.$analitics.'</tbody>
                                                </table>
                                                <table class="table table-striped table-bordered table-md">
                                                    <thead><tr><th colspan="2">CLIENT LIMITS ANALYSIS</th></tr></thead>
                                                    <tbody>'.$limits_analysis.'</tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    '.($isSupport ? '
                                    <div class="tab-pane fade" id="modify_account" role="tabpanel" aria-labelledby="modify_account-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive">
                                                <form class="ajax-data-form" action="'.$baseUrl.'api/account/modify" method="POST" id="ajax-data-form-content">
                                                    <table class="table table-striped table-bordered table-md">
                                                        <tr>
                                                            <th colspan="2">MODIFY ACCOUNT</th>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%"><strong>PACKAGE</strong></td>
                                                            <td>
                                                                <select '.$is_disabled.' data-width="100%" name="data[account_package]" class="form-control selectpicker">
                                                                    <option '.($thisClientPref->account->package == "trial" ? "selected" : null).' value="basic">Trial Package</option>
                                                                    <option '.($thisClientPref->account->package == "basic" ? "selected" : null).' value="basic">Basic Package</option>
                                                                    <option '.($thisClientPref->account->package == "standard" ? "selected" : null).' value="standard">Standard Package</option>
                                                                    <option '.($thisClientPref->account->package == "premium" ? "selected" : null).' value="premium">Premium Package</option>
                                                                    <option '.($thisClientPref->account->package == "custom" ? "selected" : null).' value="premium">Custom Package</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="35%"><strong>EXPIRY</strong></td>
                                                            <td>
                                                                <input '.$is_disabled.' data-maxdate="'.date("Y-m-d", strtotime("+5 years")).'" value="'.date("Y-m-d", strtotime($thisClientPref->account->expiry)).'" name="data[account_expiry]" id="account_expiry" class="form-control datepicker">
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
                                                                <button data-form_id="ajax-data-form-content" type="button-submit" class="btn btn-outline-success"><i class="fa fa-save"></i> Save</button>
                                                            </td>
                                                        </tr>' : null).'
                                                    </table>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    ' : '
                                    <div class="tab-pane fade" id="close_session" role="tabpanel" aria-labelledby="close_session-tab2">


                                        <div class="row">
                                            '.(!empty($promotion_log) && ($promotion_log[0]->list_count > 0) ? 
                                                "<div class='col-lg-12 bg-danger text-white text-center mb-3 font-17 p-2'>
                                                    There are <strong>{$promotion_log[0]->list_count} Promotions Log</strong> that is yet to be validated.
                                                    Please visit <a href='{$baseUrl}promote-students?history' class='text-white font-bold'>Promotions History Page</a> to rectify this issue before proceeding to close this {$academicSession}.
                                                </div>" : null
                                            ).'
                                            <div class="col-12 col-md-8">
                                                <div class="card">
                                                    <div class="card-body font-14">
                                                        <div class="mb-2 row">
                                                            <div class="col-lg-6"><span class="font-weight-bold text-uppercase">Academic Year</span></div>
                                                            <div class="col-lg-6"><span>'.$defaultAcademics->academic_year.'</span></div>
                                                        </div>
                                                        <div class="mb-2 row">
                                                            <div class="col-lg-6"><span class="font-weight-bold text-uppercase">Academic '.$academicSession.'</span></div>
                                                            <div class="col-lg-6"><span>'.$defaultAcademics->academic_term.'</span></div>
                                                        </div>
                                                        <div class="mb-2 row">
                                                            <div class="col-lg-6"><span class="font-weight-bold text-uppercase">This '.$academicSession.' Began On</span></div>
                                                            <div class="col-lg-6"><span>'.date("jS F Y", strtotime($defaultAcademics->term_starts)).'</span></div>
                                                        </div>
                                                        <div class="mb-2 row">
                                                            <div class="col-lg-6"><span class="font-weight-bold text-uppercase">This '.$academicSession.' '.($defaultUser->appPrefs->termEnded ? "Ended On" : "Ends On").'</span></div>
                                                            <div class="col-lg-6">
                                                            <span>
                                                                '.date("jS F Y", strtotime($defaultAcademics->term_ends)).'
                                                                '.($defaultUser->appPrefs->termEnded ? "<span class='badge badge-danger'>Already Ended</span>" : "<span class='badge badge-success'>Active</span>").'
                                                            </span>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2 row">
                                                            <div class="col-lg-6"><span class="font-weight-bold text-uppercase">Next '.$academicSession.' Begins</span></div>
                                                            <div class="col-lg-6"><span>'.date("jS F Y", strtotime($defaultAcademics->next_term_starts)).'</span></div>
                                                        </div>
                                                        <div class="mb-2 row">
                                                            <div class="col-lg-6"><span class="font-weight-bold text-uppercase">Next '.$academicSession.' Ends On</span></div>
                                                            <div class="col-lg-6"><span>'.date("jS F Y", strtotime($defaultAcademics->next_term_ends)).'</span></div>
                                                        </div>
                                                        <div class="mb-2 mt-3 border-top pt-3"></div>
                                                        <div class="mb-2 row">
                                                            <div class="col-lg-12 text-primary mb-3"><strong>SELECT DATA TO IMPORT</strong></div>
                                                            '.$import_div.'
                                                        </div>
                                                        <div class="mb-2 mt-3 border-top pt-3"></div>
                                                        '.(empty($session->is_only_readable_app) ?
                                                            '<div class="d-flex  justify-content-between mb-2">
                                                                <div>
                                                                    <a href="'.$baseUrl.'settings" class="btn btn-outline-primary"><i class="fa fa-edit"></i> Update</a>
                                                                </div>
                                                                <div>
                                                                    <button onclick="return end_Academic_Term(\'begin\');" class="btn btn-outline-danger"><i class="fa fa-american-sign-language-interpreting"></i> End Academic '.$academicSession.'</button>
                                                                </div>
                                                            </div>' : null
                                                        ).'
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4" id="academic_Term_Processing"></div>
                                        </div>

                                    </div>
                                    ').'
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
                                                            <td class="font-25">'.($data->analitics->sms_balance ?? 0).'</td>
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
                                    <div class="tab-pane fade" id="features" role="tabpanel" aria-labelledby="features-tab2">
                                        <div class="row">
                                            <div class="col-lg-12 table-responsive" id="account_features">
                                                <table class="table table-striped table-bordered table-md">
                                                    <tr>
                                                        <th colspan="2">ACCOUNT FEATURES PREFERENCE</th>
                                                    </tr>
                                                    '.$error_message.'
                                                    '.$features_list.'
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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