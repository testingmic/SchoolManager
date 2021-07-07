<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);


$response = (object) [];
$filter = (object) $_POST;

$clientId = $session->clientId;
$pageTitle = "Members List";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/booking_log.js"];

$params = (object) [
    "clientId" => $clientId,
    "client_data" => $defaultUser->client,
    "log_date" => $filter->log_date ?? null
];

// make a request for the logs analitics
$bookingObj = load_class("booking", "controllers");
$item_list = $bookingObj->list_members($params);

// set the parameters
$params = (object) [];

$count = 0;
$members_list = "";
$booking_log_array_list = [];

// loop through the list
if(is_array($item_list)) {

    // loopt through the log list
    foreach($item_list["data"]["list"] as $member) {

        // set the action
        $action = "<a class='btn btn-outline-success' href='{$baseUrl}booking_members_edit/{$member->item_id}'><i class='fa fa-edit'></i> Edit</a>";
        $count++;

        // append to the list
        $members_list .= "<tr data-row_id=\"{$member->item_id}\">";
        $members_list .= "<td align='center'>{$count}</td>";
        $members_list .= "<td>{$member->fullname}</td>";
        $members_list .= "<td>{$member->contact}</td>";
        $members_list .= "<td>{$member->gender}</td>";
        $members_list .= "<td>{$member->residence}</td>";
        $members_list .= "<td>{$member->bible_class_name}</td>";
        $members_list .= "<td>{$member->organizations}</td>";
        $members_list .= "<td align='center'>{$action}</td>";
        $members_list .= "</tr>";
    }

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
                    <div class="row p-3">
                        <div class="col-md-6 col-sm-12 text-left">
                            <h5></h5>
                        </div>
                        <div class="col-md-6 col-sm-12 text-right">
                            <a href="'.$baseUrl.'booking_members_edit" class="btn btn-primary" title="Click to add new member"><i class="fa fa-user"></i> Add Member</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="8%" class="text-center">#</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Gender</th>
                                        <th>Residence</th>
                                        <th>Bible Class</th>
                                        <th>Organizations</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$members_list.'</tbody>
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