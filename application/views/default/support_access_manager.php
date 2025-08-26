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
$pageTitle = "Access Management";

$access_permission_array = [];
$response->title = $pageTitle;

// not found
if(!$isSupport) {
    // end the query here
    $response->html = page_not_found("permission_denied");
    // echo the response
    echo json_encode($response);
    exit;
}

// get the access permission list
$access_permission_list = $myClass->pushQuery("*", "users_types", "1 LIMIT 10");

// loop through the permissions list
foreach($access_permission_list as $role) {
    $role->permissions = json_decode($role->user_permissions, true)["permissions"];
    $access_permission_array[$role->id] = $role;
}

// access permissions
$response->scripts = ["assets/js/access_permission.js"];

// permission 
$response->array_stream["access_permission_array"] = $access_permission_array;

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-lock"></i> User Access Management</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active">'.$pageTitle.'</div>
            </div>
        </div>
        <div>
            <div class="alert alert-warning">
                Please do not modify this content if you do not know how it works!!! NB: Tampering with the data here will hugely affect the performance of the application.
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-5 col-md-5">
                <div class="card">
                    <div class="card-body">
                        <table class="table-bordered table-striped table-md table">
                            <thead>
                                <th width="15%">#</th>
                                <th>GROUP</th>
                                <th width="25%">ACTION</th>
                            </thead>
                            <tbody>';
                            foreach($access_permission_array as $key => $access) {
                                $response->html .= "
                                <tr>
                                    <td>{$key}</td>
                                    <td>{$access->name}</td>
                                    <td class='text-center'>
                                        <button onclick='return access_permission(\"{$key}\")' class='btn btn-sm btn-outline-primary'>
                                            <i class='fa fa-edit'></i> Edit
                                        </button>
                                    </td>
                                </tr>";
                            }
                            $response->html .='
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-7 col-md-7">
                <div class="card" id="access_control">
                    <div class="form-content-loader" style="display: none; position: absolute">
                        <div class="offline-content text-center">
                            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-bold">ACCESS LEVEL</label>
                            <div><span id="access_level"><em>Access Level Appears Here</em></span></div>
                        </div>
                        <div class="form-group">
                            <label class="font-bold">ACCESS PERMISSION</label>
                            <textarea disabled style="height:350px!important" class="form-control custom_form-control" id="access_permission"></textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <button disabled class="btn btn-secondary">Cancel</button>
                                <input type="hidden" hidden class="access_id" disabled>
                            </div>
                            <div>
                                <button disabled class="btn btn-success"><i class="fa fa-save"></i> Save</button>
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