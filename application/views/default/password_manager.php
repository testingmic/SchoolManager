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
$search_term = isset($_GET["lookup"]) ? xss_clean($_GET["lookup"]) : null;

// load the list of all reset requests
$password_requests = $myClass->pushQuery(
    "a.*, 
    (SELECT b.name FROM users b WHERE b.item_id = a.changed_by LIMIT 1) AS changed_by_name,
    (SELECT b.name FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS fullname,
    (SELECT b.user_type FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_role,
    (SELECT b.unique_id FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_unique_id", 
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
            <td>{$request->fullname} 
                <span class='badge p-1 badge-{$color[$request->user_role]}'>".ucwords($request->user_role)."</span>
                <div><strong>{$request->user_unique_id}</strong></div>
            </td>
            <td><i class='fa fa-calendar'></i> {$request->request_date}</td>
            <td>{$request->reset_agent}</td>
            <td>
                <span id='change_status_{$request->item_id}' class='font-weight-bold text-{$color[$request->token_status]}'>{$request->token_status}</span>
                <div>".(!empty($request->reset_date) ? "<i class='fa fa-calendar'></i> {$request->reset_date}" : null)."</div>
            </td>
            <td align='center'>
                ".(in_array($request->token_status, ["PENDING"]) ? 
                    "<div class='change_password_{$request->item_id}'>
                        <button onclick='return show_ChangePasword_Form(\"{$request->item_id}\", \"{$request->request_token}\")' class='btn btn-outline-success p-1 pl-2 pr-2 mr-1 mb-1'><i class='fa fa-lock'></i> Change</outline>
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
                                    <a class="nav-link" id="change_password_form-tab2" data-toggle="tab" href="#change_password_form" role="tab" aria-selected="true"><i class="fa fa-list"></i> Change Username And/Or Password</a>
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
                                                        <th align="center" width="10%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>'.$change_requests.'</tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="change_password_form" role="tabpanel" aria-labelledby="change_password_form-tab2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    SEARCH USER
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label>Enter Fullname or Unique ID</label>
                                                        <input value="'.$search_term.'" type="text" placeholder="Search by Name or UNIQUE ID" name="search_user_term" id="search_user_term" class="form-control">
                                                    </div>
                                                    <div align="center" class="form-group mb-2">
                                                        <button class="btn btn-outline-primary" onclick="return search_By_Fullname_Unique_ID()"><i class="fa fa-filter"></i> Search</button>
                                                    </div>
                                                    <div class="mt-0 mb-2 border-bottom"></div>
                                                    <div id="search_user_term_list" class="slim-scroll custom-600px"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">

                                            <div class="card hidden" id="change_Username_Password">
                                                <div class="card-body">

                                                    <div class="form-group mb-1">
                                                        <label>Username</label>
                                                        <input autocomplete="Off" type="text" name="username" id="username" class="form-control">
                                                    </div>
                                                    <div class="form-group mb-1">
                                                        <label>Password</label>
                                                        <input autocomplete="Off" type="text" name="passwd" id="passwd" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Confirm Password</label>
                                                        <input autocomplete="Off" type="text" name="passwd_2" id="passwd_2" class="form-control">
                                                    </div>
                                                    <div class="form-group text-right">
                                                        <input type="hidden" name="user_id" id="user_id">
                                                        <button onclick="return change_Username_Password_Form();" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                                                        <button onclick="return change_Username_Password();" class="btn btn-outline-success"><i class="fa fa-lock"></i> Change Password</button>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="change_Password" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-top modal-md" style="width:100%;height:100%;" role="document">
            <div class="modal-content">
                <div class="form-content-loader" style="display: none; position: absolute">
                    <div class="offline-content text-center">
                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                    </div>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body mb-0 pb-0">
                    <div class="form-group mb-1">
                        <label>Password</label>
                        <input autocomplete="Off" type="text" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group pb-0 mb-0">
                        <label>Confirm Password</label>
                        <input autocomplete="Off" type="text" name="password_2" id="password_2" class="form-control">
                    </div>      
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="token">
                    <input type="hidden" name="request_id">
                    <button onclick="return change_Password();" class="btn btn-outline-success"><i class="fa fa-lock"></i> Change Password</button>
                    <button onclick="return cancel_ChangePasword_Form();" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>';
    
// print out the response
echo json_encode($response);
?>