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
$pageTitle = "Security Logs";

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
$security_logs_list = $myClass->pushQuery(
    "a.*, c.client_name AS school_name, b.name AS user_name", 
    "security_logs a 
        LEFT JOIN users b ON b.item_id = a.created_by
        LEFT JOIN clients_accounts c ON c.client_id = a.client_id
    ", 
    "1 ORDER BY a.id DESC LIMIT 500"
);

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-lock"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active">'.$pageTitle.'</div>
            </div>
        </div>
        <div>
            <div class="alert alert-warning">
                These are the list of system security logs.
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table-bordered table-striped table-md table datatable">
                            <thead>
                                <th width="7%">#</th>
                                <th>SECTION</th>
                                <th>SCHOOL</th>
                                <th>USER</th>
                                <th>DATE CREATED</th>
                                <th>DESCRIPTION</th>
                            </thead>
                            <tbody>';
                            $count = 0;
                            foreach($security_logs_list as $count => $log) {
                                $count++;
                                $response->html .= "
                                <tr>
                                    <td>{$count}</td>
                                    <td>{$log->section}</td>
                                    <td>{$log->school_name}</td>
                                    <td>{$log->user_name}</td>
                                    <td>{$log->date_created}</td>
                                    <td>{$log->description}</td>
                                </tr>";
                            }
                            $response->html .='
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>';

// print out the response
echo json_encode($response);
?>