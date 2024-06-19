<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $session, $myClass, $accessObject, $defaultAcademics, $isAdmin, $isSupport;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$application_list = "";
$response->title = "Api Keys";

// end the page if the user is not an admin
if(!$isAdmin && !$isSupport) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// get the class list
$response->scripts = ["assets/js/apis.js"];

// get the list of all applications
$item_list = $myClass->pushQuery("*", "users_api_keys", "client_id='{$clientId}' LIMIT 20");

foreach($item_list as $key => $each) {
    
    // action button
    $action = "";

    // access key
    $access_key = base64_encode("{$each->username}:{$each->access_key}");
    $isExpired = (bool) (time() > strtotime($each->expiry_timestamp));
    $status = $isExpired ? "<span class='badge badge-danger'>Expired</span>" : "<span class='badge badge-success'>Active</span>";

    // append to the list
    $application_list .= "<tr data-row_id=\"{$each->id}\">";
    $application_list .= "<td>".($key+1)."</td>";
    $application_list .= "<td>{$each->username}</td>";
    $application_list .= "<td>{$access_key}</td>";
    $application_list .= "<td>{$each->access_type}</td>";
    $application_list .= "<td>
        <input type='text' ".(!$isExpired ? "style='width:130px' data-api_key_date='{$each->id}' data-mindate='".date("Y-m-d")."' data-maxdate='".date("Y-m-d", strtotime("today +6 months"))."' class='datepicker'" : "disabled style='max-width:150px' class='form-control'")." value='{$each->expiry_date}'>
        ".(!$isExpired ? "<span onclick='return extend_api_expiry(\"{$each->id}\");' title='Update Expiry Date' class='ml-1 cursor btn-outline-success btn btn-sm'><i class='fa fa-save'></i></span>
            <span onclick='return delete_api_key(\"{$each->id}\");' title='Delete API Key' class='ml-1 cursor btn-outline-danger btn btn-sm'><i class='fa fa-trash'></i></span>" : null)."</td>";
    $application_list .= "<td align='center'>{$status}</td>";
    $application_list .= "</tr>";
}

// display the form information
$response->html = '
    <section class="section list_Students_By_Class">
        <div class="section-header">
            <h1><i class="fa fa-book-open"></i> Api Keys</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active">'.$response->title.'</div>
            </div>
        </div>
        <input type="hidden" disabled name="assign_param" value="department">
        <div class="row">
            <div class="text-right mb-3 col-lg-12">
                <button onclick="return create_api_key()" class="btn btn-outline-success">GENERATE API KEY</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Username</th>
                                        <th>Api Key</th>
                                        <th>Type</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>'.$application_list.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';

// print out the response
echo json_encode($response);
?>