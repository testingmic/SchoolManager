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
$pageTitle = "Attendance Booking List";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/booking_log.js"];

$params = (object) [
    "clientId" => $clientId,
    "client_data" => $defaultUser->client,
    "log_date" => $filter->log_date ?? null
];
$item_list = load_class("booking", "controllers", $params)->list($params)["data"];

// set the parameters
$params = (object) [];

$count = 0;
$booking_list = "";
$booking_log_array_list = [];

// loop through the list
if(is_array($item_list)) {

    // loopt through the log list
    foreach($item_list as $log) {

        $booking_log_array_list[$log->item_id] = $log;
        $count++;

        // set the action button
        $action = "<button title='View the details of this log.' onclick='return view_message(\"{$log->item_id}\")' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></button>";
        $action .= "&nbsp;<a href='{$baseUrl}booking_log/{$log->item_id}' title='Click to edit this record.' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i> Edit</a>";

        $booking_list .= "<tr data-row_id=\"{$log->item_id}\">";
        $booking_list .= "<td>{$count}</td>";
        $booking_list .= "<td>{$log->log_date}</td>";
        $booking_list .= "<td>{$log->log_date}</td>";
        $booking_list .= "<td>{$log->created_by_info->name}</td>";
        $booking_list .= "<td align='center'>{$action}</td>";
        $booking_list .= "</tr>";
    }
}
$response->array_stream["booking_log_array_list"] = $booking_log_array_list;

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
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="7%" class="text-center">#</th>
                                        <th width="13%">Log Date</th>
                                        <th>Member Details</th>
                                        <th width="18%">Created By</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$booking_list.'</tbody>
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