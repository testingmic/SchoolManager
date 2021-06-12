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
$pageTitle = "Bulk SMS and Email";
$response->title = "{$pageTitle} : {$appName}";

// add the scripts to load
$response->scripts = ["assets/js/communication.js"];

// set the parameters
$params = (object) [
    "clientId" => $clientId,
    "client_data" => $defaultUser->client
];
// get the list of all the templates
$count["sms"] = 0;
$count["email"] = 0;
$messages_list["email"] = "";
$messages_list["sms"] = "";
$messages_array_list = [];
$array_list = load_class("communication", "controllers", $params)->list_messages($params)["data"];

// append to the list
foreach($array_list as $message) {
    // append the message list
    $messages_array_list[$message->type][$message->item_id] = $message;
    $count[$message->type]++;

    // set the action button
    $action = "<button title='View the details of this message.' onclick='return view_message(\"{$message->type}\",\"{$message->item_id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-eye'></i></button>";

    // get the messages
    $messages_list[$message->type] .= "<tr data-row_id=\"{$message->item_id}\">";
    $messages_list[$message->type] .= "<td>".($count[$message->type])."</td>";
    $messages_list[$message->type] .= "<td>{$message->campaign_name}</td>";
    $messages_list[$message->type] .= "<td>{$message->recipient_group}</td>";
    $messages_list[$message->type] .= "<td>{$message->recieved_count} / ".count($message->recipient_ids)."</td>";
    $messages_list[$message->type] .= "<td>{$message->sent_status}</td>";
    $messages_list[$message->type] .= "<td>{$message->date_created}</td>";
    $messages_list[$message->type] .= "<td align='center'>{$action}</td>";
    $messages_list[$message->type] .= "</tr>";
}

// append to the messages array list
$response->array_stream["messages_array_list"] = $messages_array_list;

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
                                    <a class="nav-link active" id="send_sms-tab2" data-toggle="tab" href="#send_sms" role="tab" aria-selected="true"><i class="fa fa-comment"></i> SMS Report</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="send_email-tab2" data-toggle="tab" href="#send_email" role="tab" aria-selected="true"><i class="fa fa-envelope"></i> Email Messaging Report</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="send_sms" role="tabpanel" aria-labelledby="send_sms-tab2">
                                    <div class="table-responsive trix-slim-scroll">
                                        <table class="table table-bordered table-condensed table-striped datatable">
                                            <thead>
                                                <th></th>
                                                <th>Campaign Name</th>
                                                <th>Recipients Type</th>
                                                <th>Recipients Count</th>
                                                <th>Status</th>
                                                <th>Date Created</th>
                                                <th></th>
                                            </thead>
                                            <tbody>'.$messages_list["sms"].'</tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="send_email" role="tabpanel" aria-labelledby="send_email-tab2">
                                    <div class="table-responsive trix-slim-scroll">
                                        <table class="table table-bordered table-condensed table-striped datatable">
                                            <thead>
                                                <th></th>
                                                <th>Campaign Name</th>
                                                <th>Recipients Type</th>
                                                <th>Recipients Count</th>
                                                <th>Status</th>
                                                <th>Date Created</th>
                                                <th></th>
                                            </thead>
                                            <tbody>'.$messages_list["email"].'</tbody>
                                        </table>
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