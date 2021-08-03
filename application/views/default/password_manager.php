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
$pageTitle = "Password Manager";
$response->title = "{$pageTitle} : {$appName}";

// add the scripts to load
$response->scripts = ["assets/js/password.js"];

$change_requests = "";

// load the list of all reset requests
$password_requests = $myClass->pushQuery(
    "a.*, (SELECT b.name FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS fullname,
    (SELECT b.user_type FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_role", 
    "users_reset_request a", "a.client_id = '{$clientId}' ORDER BY a.id DESC");

// colors
$color = [
    "USED" => "success",
    "EXPIRED" => "warning",
    "PENDING" => "primary",
    "ANNULED" => "danger",

    "admin" => "success",
    "employee" => "primary",
    "accountant" => "danger",
    "teacher" => "warning",
    "parent" => "secondary",
    "student" => "primary"
];

// loop through the requests
foreach($password_requests as $key => $request) {
    $change_requests .= "
        <tr>
            <td>".($key+1)."</td>
            <td>{$request->fullname} <span class='badge p-1 badge-{$color[$request->token_status]}'>{$request->user_role}</span></td>
            <td>{$request->request_date}</td>
            <td>{$request->reset_agent}</td>
            <td><span class='font-weight-bold text-{$color[$request->token_status]}'>{$request->token_status}</span></td>
            <td align='center'>
                ".(in_array($request->token_status, ["PENDING"]) ? 
                    "<div class='change_password_{$request->item_id}'>
                        <button class='btn btn-outline-success p-1 pl-2 pr-2 mr-1 mb-1'><i class='fa fa-lock'></i> Change</outline>
                        <button onclick='return cancel_ChangePassword(\"{$request->item_id}\")' class='btn btn-outline-danger p-1 pl-2 pr-2 mb-1'><i class='fa fa-trash'></i> Cancel</button>
                    </div>" : 
                    null
                )."
            </td>
        </tr>";
}


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
                                    <a class="nav-link active" id="change_requests-tab2" data-toggle="tab" href="#change_requests" role="tab" aria-selected="true"><i class="fa fa-list"></i> Password Change Requests</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="change_password-tab2" data-toggle="tab" href="#change_password" role="tab" aria-selected="true"><i class="fa fa-list"></i> Change Password</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="change_requests" role="tabpanel" aria-labelledby="change_requests-tab2">
                                    <div class="row generate_report">
                                        <div class="table-responsive">
                                            <table data-empty="" class="table table-bordered table-striped raw_datatable">
                                                <thead>
                                                    <tr>
                                                        <th width="5%" class="text-center">#</th>
                                                        <th>Fullname</th>
                                                        <th>Request Date</th>
                                                        <th>User Agent</th>
                                                        <th>Status</th>
                                                        <th align="center" width="18%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>'.$change_requests.'</tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="change_password" role="tabpanel" aria-labelledby="change_password-tab2">
                                    <div class="row account_note_report">
                                        
                                    
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