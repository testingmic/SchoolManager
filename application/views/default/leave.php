<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $isSupport, $defaultUser, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$data = [];
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Leave Applications";
$response->title = $pageTitle;

// init value for leave applications
$leave_applications = '';

// display error if not admin, accountant, teacher and employee
if($isWardParent) {
    $response->html = page_not_found("permission_denied");
} else {

    // set the application id
    $data = null;
    $isApply = false;
    $application_data = null;
    $application_id = $SITEURL[1] ?? null;

    // set the application id
    if($application_id === 'apply') {
        $isApply = true;
        $pageTitle = "Apply for Leave";
    }

    // load the leave applications
    $param = (object) [
        "userData" => $defaultUser,
        "clientId" => $clientId,
        "application_id" => strlen($application_id) > 6 ? $application_id : null,
        "user_id" => $isAdmin ? null : $defaultUser->user_id
    ];

    // get the reports list
    $leaveObj = !$isApply ? load_class("leave", "controllers") : null;
    $leave_array = !$isApply ? ($leaveObj->list($param)["data"] ?? []) : [];

    // if the request is not to apply
    if(!$isApply && empty($application_id)) {

        // loop through the $leave_array
        foreach($leave_array as $key => $leave) {
            
            // view button
            $action = "<button onclick='load(\"leave/{$leave->item_id}\");' class='btn btn-success btn-sm'><i class='fa fa-eye'></i> View</button>";

            // if the status is still pending
            if($leave->status === 'Pending') {
                $action .= "&nbsp;<button onclick='return delete_record(\"{$leave->item_id}\", \"leave\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></button>";
            }

            // leave status
            $status = $leave->status == 'Pending' ? 'primary' : ($leave->status == 'Approved' ? 'success' : ($leave->status === 'Processing' ? 'warning' : 'danger'));

            $leave_applications .= "
            <tr>
                <td>".($key+1)."</td>
                <td><span onclick='load(\"leave/{$leave->item_id}\");' class='user_name'>{$leave->name}</span></td>
                <td>{$leave->type_name}</td>
                <td><strong>{$leave->leave_from}</strong> to <strong>{$leave->leave_to}</strong></td>
                <td>{$leave->days} days</td>
                <td>{$leave->date_created}</td>
                <td><span class='badge badge-{$status}'>{$leave->status}</span></td>
                <td align='center' width='12%'>{$action}</td>
            </tr>";
        }

    }

    // if the request is not apply
    if(!$isApply && $application_id && !empty($leave_array)) {
        $data = $leave_array[0];
    }

    // set the application form
    $application_form = $isApply ? load_class("forms", "controllers")->leave_form() : null;

    // if the data is not empty
    if(!empty($data)) {

        // uploads script
        $comment_form = null;   
        $response->scripts = ["assets/js/comments.js", "assets/js/frontoffice.js"];

        // include the script if admin
        if($isAdmin) {
            $response->scripts[] = "assets/js/leave.js";
        }

        // leave status
        $status = ($data->status == 'Pending') ? 'primary' : ($data->status == 'Approved' ? 'success' : ($data->status === 'Processing' ? 'warning' : 'danger'));
        $state = $data->status;

        // comment form set
        if(!$isAdmin && in_array($data->status, ["Pending", "Disapproved", "Processing"])) {
            $comment_form = leave_comments_builder("leave", $application_id, false);
        } elseif($isAdmin && !in_array($data->status, ["Cancelled"])) {
            $comment_form = leave_comments_builder("leave", $application_id, false);
        }

        // set the application data
        $application_data = '
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">

                    <div class="font-17 text-uppercase mb-2">
                        <i class="fa fa-user-graduate"></i> 
                        <span onclick="return load(\'staff/'.$data->user_id.'/documents\')" class="user_name">'.$data->name.'</span>
                    </div>
                    <div class="font-15 mb-2">
                        <i class="fa fa-clone"></i> 
                        <strong>'.date("l, jS F Y", strtotime($data->leave_from)).'</strong>
                        to
                        <strong>'.date("l, jS F Y", strtotime($data->leave_to)).'</strong>
                    </div>
                    <div class="font-15 border-bottom pb-2 mb-2">
                        <i class="fa fa-calendar-check"></i> 
                        <strong>'.$data->days.' Days</strong>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">'.$data->reason.'</div>
                    </div>
                    '.($isAdmin ?
                        '<div class="form-group">
                            <label>Leave Status</label>
                            <select data-leave_id="'.$data->item_id.'" '.($data->status === 'Cancelled' ? 'disabled' : 'name="leave_status" id="leave_status"').' class="selectpicker" data-width="100%">
                                <option '.($data->status === 'Pending' ? 'selected' : null).' value="Pending">Pending</option>
                                <option '.($data->status === 'Processing' ? 'selected' : null).' value="Processing">Processing</option>
                                <option '.($data->status === 'Approved' ? 'selected' : null).' value="Approved">Approved</option>
                                <option '.($data->status === 'Disapproved' ? 'selected' : null).' value="Disapproved">Disapproved</option>
                                <option '.($data->status === 'Cancelled' ? 'selected' : null).' value="Cancelled">Cancelled</option>
                            </select>
                        </div>' : "<strong>Status: </strong><span class='badge badge-{$status}'>{$state}</span>"
                    ).'
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="slim-scroll">
                        <div class="p-0 m-0">
                            '.(($data->status !== 'Cancelled') ? $comment_form : null).'
                            <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$application_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                            <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // display the page content
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.' 
                    '.(!$isApply ? '
                        <span onclick="load(\'leave/apply\')" class="btn btn-sm btn-primary">
                            Apply for Leave
                        </span>' : null
                    ).'
                </h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    '.($isApply || !empty($data) ? 
                        '<div class="breadcrumb-item">
                            <a href="'.$baseUrl.'leave">Leave Applications</a>
                        </div>' : null
                    ).'
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                '.(!empty($data) ? $application_data : null).'
                <div '.(!empty($data) ? 'hidden' : null).' class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            '.(!$isApply && !$data ?
                                '<div class="table-responsive table-student_staff_list">
                                    <table class="table table-bordered table-sm table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th>Staff</th>
                                                <th>Leave Type</th>
                                                <th>Leave Date</th>
                                                <th>Days</th>
                                                <th>Date Created</th>
                                                <th>Status</th>
                                                <th width="12%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>'.$leave_applications.'</tbody>
                                    </table>
                                </div>' : null
                            ).'
                            '.($isApply ? $application_form : null).'
                        </div>
                    </div>
                </div>
            </div>
        </section>';

}

// print out the response
echo json_encode($response);
?>