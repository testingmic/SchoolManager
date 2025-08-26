<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser, $isSupport;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Password Manager";
$response->title = $pageTitle;

// confirm that the user has the required permissions
if(!$accessObject->hasAccess("change_password", "permissions")) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// add the scripts to load
$response->scripts = ["assets/js/password.js"];

$change_requests = "";
$search_term = isset($_GET["lookup"]) ? xss_clean($_GET["lookup"]) : null;
$client = $isSupport ? "1" : "a.client_id = '{$clientId}'";

// load the list of all reset requests
$password_requests = $myClass->pushQuery(
    "a.*, 
    (SELECT b.name FROM users b WHERE b.item_id = a.changed_by LIMIT 1) AS changed_by_name,
    (SELECT b.name FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS fullname,
    (SELECT b.user_type FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_role,
    (SELECT b.unique_id FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_unique_id", 
    "users_reset_request a", "{$client} ORDER BY a.id DESC LIMIT 500");

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
    "student" => "primary",
    "support" => "success"
];

// loop through the requests
foreach($password_requests as $key => $request) {
    $change_requests .= "
        <tr data-row_id='{$request->item_id}'>
            <td>".($key+1)."</td>
            <td>{$request->fullname} 
                <span class='badge p-1 badge-{$color[$request->user_role]}'>".ucwords($request->user_role)."</span>
                <div><strong>{$request->user_unique_id}</strong></div>
            </td>
            <td>
                <i class='fa fa-calendar'></i> {$request->request_date}
                <div>{$request->reset_agent}</div>
                <div><span id='change_status_{$request->item_id}' class='font-weight-bold text-{$color[$request->token_status]}'>{$request->token_status}</span></div>
            </td>
            <td class='text-center'>
                ".(!in_array($request->token_status, ["PENDING"]) ? 
                    "<div class='change_password_{$request->item_id}'>
                        <button onclick='return show_ChangePasword_Form(\"{$request->item_id}\", \"{$request->request_token}\")' class='btn btn-outline-success btn-sm mr-1 mb-1'><i class='fa fa-edit'></i></outline>
                        <button onclick='return cancel_ChangePassword(\"{$request->item_id}\")' class='btn btn-outline-danger btn-sm mb-1'><i class='fa fa-trash'></i></button>
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

            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-sm table-striped raw_datatable">
                                <thead>
                                    <tr>
                                        <th width="4%" class="text-center">#</th>
                                        <th>Fullname</th>
                                        <th>Request Date & State</th>
                                        <th align="center"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$change_requests.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">

                <div class="card">
                    <div class="card-header">
                        SEARCH USER
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Enter Fullname or Unique ID</label>
                            <input autocomplete="Off" value="'.$search_term.'" type="text" placeholder="Search by Name or UNIQUE ID" name="search_user_term" id="search_user_term" class="form-control">
                        </div>
                        <div align="center" class="form-group mb-2">
                            <button class="btn btn-outline-primary" onclick="return search_By_Fullname_Unique_ID()"><i class="fa fa-filter"></i> Search</button>
                        </div>
                        <div class="mt-0 mb-2 border-bottom"></div>
                        <div id="search_user_term_list" class="slim-scroll custom-600px"></div>
                    </div>
                </div>

            </div>

            <div class="col-12 col-lg-7">

                <div class="card hidden" id="change_Username_Password">
                    <div class="card-body">

                        <div class="form-group mb-1">
                            <label>Username</label>
                            <input autocomplete="Off" name="username" id="username" type="text" '.(($isAdmin || $isSupport) ? null : "disabled='disabled'").' class="form-control">
                        </div>
                        <div class="form-group mb-1">
                            <label>Password</label>
                            <input autocomplete="off" type="password" name="passwd" id="passwd" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input autocomplete="off" type="password" name="passwd_2" id="passwd_2" class="form-control">
                        </div>
                        <div class="form-group text-right">
                            <input type="hidden" name="user_id" id="user_id">
                            <button type="button" onclick="return cancel_ChangePasword_Form();" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                            <button type="button" onclick="return change_Username_Password();" class="btn btn-outline-success"><i class="fa fa-lock"></i> Change Password</button>
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
                        <input autocomplete="off" type="password" name="password" id="password" class="form-control">
                    </div>
                    <div class="form-group pb-0 mb-0">
                        <label>Confirm Password</label>
                        <input autocomplete="off" type="password" name="password_2" id="password_2" class="form-control">
                    </div>      
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="token">
                    <input type="hidden" name="request_id">
                    <button type="button" onclick="return cancel_ChangePasword_Form();" class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" onclick="return change_Password();" class="btn btn-outline-success"><i class="fa fa-lock"></i> Change Password</button>
                </div>
            </div>
        </div>
    </div>';
    
// print out the response
echo json_encode($response);
?>