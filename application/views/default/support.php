<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];

$response->title = "Support Tickets : {$appName}";
$response->scripts = ["assets/js/support.js"];

$support_tickets = "";
$ticket_id = (confirm_url_id(1, "ticket") && confirm_url_id(2)) ? $SITEURL[2] : null;

// set the parameters
$item_param = (object) [
    "clientId" => $clientId,
    "ticket_id" => $ticket_id,
    "client_data" => $defaultUser->client
];

// if the ticket id is not empty
if(!empty($ticket_id)) {
    // then set to load the replies as well
    $item_param->show_all = true;
}

// get the list of all the templates
$support_array = load_class("support", "controllers", $item_param)->list($item_param)["data"];

// init variables
$count = 0;
$item_found = false;
$support_tickets = "";

// loop through the templates list
if((count($support_array) > 1) || empty($ticket_id)) {
    foreach($support_array as $key => $ticket) {
        $count++;

        // view button
        $checkbox = "";
        $ticket->department = str_ireplace("_", " ", $ticket->department);

        // if the record is still pending
        $action = "{$baseUrl}support/ticket/{$ticket->id}";

        $support_tickets .= "<tr class=\"cursor clickable-row\" data-href=\"{$action}\" data-row_id=\"{$ticket->id}\">";
        $support_tickets .= "<td>{$ticket->id}</td>";
        $support_tickets .= "<td><a class=\"text-success\" href=\"{$action}\">{$ticket->subject}</a></td>";
        $support_tickets .= "<td>{$ticket->department}</td>";
        $support_tickets .= "<td>".$myClass->the_status_label($ticket->status)."</td>";
        $support_tickets .= "<td>".date("jS M Y", strtotime($ticket->date_created))."</td>";
        $support_tickets .= "</tr>";
    }
}

// not found
if(!empty($ticket_id) && empty($support_array)) {
    // end the query here
    $response->html = page_not_found();

    // echo the response
    echo json_encode($response);
    exit;
} elseif($ticket_id && !empty($support_array)) {
    $data = $support_array[0];
    $item_found = true;
    $disabled = ($data->status == "Closed") ? "disabled='disabled'" : null;
}

// access permissions check
$response->html = '
<section class="section">
    <div class="section-header">
        <h1>Support Tickets '.(empty($ticket_id)  ? '<small>Need an answer? We are here to help</small>' : null).'</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            '.($ticket_id ? '<div class="breadcrumb-item"><a href="'.$baseUrl.'support">Support Tickets</a></div>' : '<div class="breadcrumb-item">Support Tickets</div>').'
            '.($ticket_id && !empty($support_array) ? '<div class="breadcrumb-item active">Ticket# '.$ticket_id.'</div>' : '<div class="breadcrumb-item active">Ticket Not Found</div>').'
        </div>
    </div>
    <div class="row">
        
        <div class="col-12 col-sm-12 col-lg-12">
            '.(!$item_found ? '
            <div class="text-right mb-2">
                <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#tickets" href="#"><i class="fa fa-plus"></i> Submit New Ticket</a>
            </div>' : null).'
            '.(!$item_found ? '
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive table-student_staff_list">
                        <table data-empty="" data-order_item="desc" class="table table-bordered table-striped raw_datatable">
                            <thead>
                                <tr>
                                    <th width="8%" class="text-center">#</th>
                                    <th>Subject</th>
                                    <th width="20%">Department</th>
                                    <th width="12%">Status</th>
                                    <th width="15%">Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>'.$support_tickets.'</tbody>
                        </table>
                    </div>
                </div>
            </div>
            ' : 
            '<div class="card mb-2">
                <div class="card-header bg-teal">
                    <h4 class="card-title text-white">Ticket #'.$ticket_id.' - '.$data->subject.'</h4>
                    <div class="card-tools"></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-lg-3 text-center my-2 my-md-0">
                            <div>
                                <i class="font-20 fa fa-info-circle text-success"></i> 
                                <h5 class="t-font-boldest text-15 mt-1 mb-0">
                                '.$data->status.'
                                </h5>
                                <span>Status</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3 my-2 text-center my-md-0">
                            <div>
                                <i class="font-20 fa fa-phone text-primary"></i>
                                <h5 class="t-font-boldest text-15 mt-1 mb-0">'.$data->department.'</h5>
                                <span>Department</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3 my-2 text-center my-md-0">
                            <div>
                                <i class="font-20 far fa-clock text-warning"></i>
                                <h5 class="t-font-boldest text-15 mt-1 mb-0">'.time_diff($data->date_updated).'</h5>
                                <span>Last Updated</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3 my-2 text-center my-md-0">
                            <div>
                                <i class="font-20 fa fa-calendar text-info"></i>
                                <h5 class="t-font-boldest text-15 mt-1 mb-0">'.$data->date_created.'</h5>
                                <span>Submitted On</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <button '.$disabled.' class="btn bg-teal"><i></i> Reply to Ticket</button>
                <button '.$disabled.' onclick="return close_ticket(\''.$ticket_id.'\')" class="btn btn-danger">Close</button>
            </div>
            <div class="mt-4">
                <div class="activities">
                    <div class="activity">
                        <div class="activity-icon bg-primary text-white">
                            <img class="rounded-circle author-box-picture" width="55px" src="'.$baseUrl.''.$data->user_info->image.'">
                        </div>
                        <div class="activity-detail" style="width:100%">
                            <div>
                                <div class="d-flex justify-content-between">
                                    <div class="font-weight-bold text-primary">'.$data->user_info->name.'</div>
                                    <div>
                                        <span class="text-job text-primary">'.$data->date_created.'</span>
                                    </div>
                                </div>
                            </div>
                            <div>'.$data->content.'</div>
                        </div>
                    </div>
                </div>
            </div>');

            
            // if a single reply id was parsed
            if($item_found) {
                $response->html .= '<div class="activities">';
                // loop through the replies list
                foreach($data->replies as $reply) {

                    // append to the replies list   
                    $content = '<div class="activity">';

                        // run this section if the user_type is a normal user
                        if($reply->user_type === "user") {
                            $content .= '
                            <div class="activity-icon bg-primary text-white">
                                <img class="rounded-circle author-box-picture" width="55px" src="'.$baseUrl.''.$reply->user_info->image.'">
                            </div>
                            <div class="activity-detail" style="width:100%">
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <div class="font-weight-bold text-primary">'.$reply->user_info->name.'</div>
                                        <div>
                                            <span class="text-job text-primary">'.$reply->date_created.'</span>
                                        </div>
                                    </div>
                                </div>
                                <div>'.$reply->content.'</div>
                            </div>';
                        } else {
                            // if the user_type is an support admin
                            $content .= '
                            <div class="d-flex justify-content-between" style="width:100%">
                                <div class="card" style="width:100%">
                                    <div class="card-body p-3 bg-green">
                                        <div class="d-flex justify-content-between">
                                            <div class="font-weight-bold text-white">'.$reply->user_info->name.'</div>
                                            <div>
                                                <span class="text-job text-white">'.$reply->date_created.'</span>
                                            </div>
                                        </div>
                                        <div class="mt-2">'.$reply->content.'</div>
                                    </div>
                                </div>
                                <div>
                                    <img class="rounded-circle author-box-picture" width="55px" src="'.$baseUrl.''.$reply->user_info->image.'">
                                </div>
                            </div>';
                        }

                    $content .= '</div>';

                    $response->html .= $content;
                }
                $response->html .= '</div>';
            }

            $response->html .= $item_found && !$disabled ? '
            <div class="mb-4">
                <div id="ticket_form" class="mt-4 p-0">
                    <div class="form-group mb-0">
                        <label>Message</label>
                        <span class="d-inline-block badge badge-primary p-2 text-11"><i class="fas fa-info-circle"></i>
                        To best assist you, we request that you be specific and detailed</span>
                        <input type="hidden" hidden name="ticket_id" id="ticket_id" value="'.$ticket_id.'">
                        <textarea placeholder="Write here..." name="content" id="content" class="form-control"></textarea>
                    </div>      
                </div>
                <div class="mt-2">
                    <button onclick="return reply_ticket(\''.$ticket_id.'\')" class="btn btn-success"><i class="fa fa-reply-all"></i> Send</button>
                </div>
            </div>' : null;

            $response->html .= '
            </div>
    </div>
</section>';
// show this section if a ticket id has not been parsed
if(empty($item_found)) {
    // append the modal window
    $response->html .=  '
    <div class="modal fade" id="tickets" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-top modal-md" style="width:100%;height:100%;" role="document">
            <div class="modal-content" id="ticket_form">
                <div class="form-content-loader" style="display: none; position: absolute">
                    <div class="offline-content text-center">
                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                    </div>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title">Submit new support ticket</h5>
                </div>
                <div class="modal-body mb-0 pb-0">
                    <div class="form-group">
                        <label>Help desk:</label>
                        <select name="department" data-width="100%" class="selectpicker form-control">
                            <option value="Support">Support</option>
                            <option value="Sales_Billing">Sales / Billing</option>
                            <option value="Usage_Problem">Usage Problem</option>
                            <option value="Abuse">Abuse</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control">
                    </div>
                    <div class="form-group mb-0">
                        <label>Message</label>
                        <span class="d-inline-block badge badge-primary p-2 text-11"><i class="fas fa-info-circle"></i>
                        To best assist you, we request that you be specific and detailed</span>
                        <textarea name="content" placeholder="Write here..." id="content" class="form-control"></textarea>
                    </div>      
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="token">
                    <input type="hidden" name="request_id">
                    <button type="reset" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    <button type="submit" onclick="return submit_ticket()" class="btn btn-outline-success"><i class="fa fa-reply-all"></i> Submit</button>
                </div>
            </div>
        </div>
    </div>';
}

// print out the response
echo json_encode($response);
?>