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
$item_list = load_class("booking", "controllers", $params)->list($params);

// set the parameters
$params = (object) [];

$count = 0;
$booking_list = "";
$booking_log_array_list = [];

// loop through the list
if(is_array($item_list)) {

    // loopt through the log list
    foreach($item_list["data"]["list"] as $log) {

        $booking_log_array_list[$log->item_id] = $log;
        $count++;

        // set the action button
        $action = "";

        // if still logged
        if($log->state == "Logged") {
            $action .= "&nbsp;<a href='{$baseUrl}booking_log/{$log->item_id}' title='Click to edit this record.' class='btn btn-sm btn-outline-primary mb-1'><i class='fa fa-edit'></i> Edit</a>";
        }

        // members_list processing
        $members_list = "<div class='row'>";

        // loop through the users list
        foreach($log->members_list as $key => $member) {
            // temperature check
            $color = $member["temperature"] > 36.7 ? "danger" : ($member["temperature"] < 34.7 ? "warning" : "success");

            // append to the list
            $members_list .= "<div class='col-lg-6 ".($key !== count($log->members_list) ? "mb-3" : "")."'>";
            $members_list .= "<div><i ".(!empty($member["gender"]) && $member["gender"] == "Female" ? "class='fa fa-user-nurse'"  : (!empty($member["gender"]) && $member["gender"] == "Male" ? "class='fa fa-user-tie'" : "class='fa fa-user'"))."'></i> {$member["fullname"]}</div>";
            $members_list .= !empty($member["contact"]) ? "<div><i class='fa fa-phone'></i> {$member["contact"]}</div>" : null;
            $members_list .= "<div class='text-{$color} font-weight-bold'><i class='fa fa-temperature-high'></i> {$member["temperature"]}</div>";
            $members_list .= !empty($member["residence"]) ? "<div><i class='fa fa-globe'></i> {$member["residence"]}</div>" : null;
            $members_list .= "</div>";
        }

        $members_list .= "</div>";

        // append to the list
        $booking_list .= "<tr data-row_id=\"{$log->item_id}\">";
        $booking_list .= "<td style='vertical-align: top;' align='center'>{$count}</td>";
        $booking_list .= "<td style='vertical-align: top;'>".date("jS M Y", strtotime($log->log_date))."</td>";
        $booking_list .= "<td>{$members_list}</td>";
        $booking_list .= "<td style='vertical-align: top;'>{$log->created_by_info->name}</td>";
        $booking_list .= "<td align='center'>{$action}</td>";
        $booking_list .= "</tr>";
    }
}
$response->array_stream["booking_log_array_list"] = [
    "array_list" => $booking_log_array_list,
    "summary" => $item_list["data"]["summary"]
];

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
                            <a href="'.$baseUrl.'booking_log" class="btn btn-primary" title="Click to log attendance"><i class="fa fa-user"></i> Log Attendance</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="7%" class="text-center">#</th>
                                        <th width="13%">Log Date</th>
                                        <th>Member Details</th>
                                        <th width="12%">Logged By</th>
                                        <th align="center" width="10%"></th>
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