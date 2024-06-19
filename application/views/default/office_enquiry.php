<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// set some important variables
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);

// set the page tile
$pageTitle = "Admission Enquiry";
$response->title = $pageTitle;

$clientId = $session->clientId;

// if the user id is not empty
if(!$accessObject->hasAccess("view", "admission_enquiry")) {
    // parse error message
    $response->html = page_not_found("permission_denied");
} else {

    // set the first key
    $request_id = $SITEURL[1] ?? null;
    $response->scripts = ["assets/js/frontoffice.js"];

    // init values
    $data = null;
    $loadForm = false;
    $request_data = null;
    $results_list = null;

    // set the application id
    if($request_id === 'log') {
        $loadForm = true;
    }

    // load the leave applications
    $param = (object) [
        "userData" => $defaultUser,
        "clientId" => $clientId,
        "section" => "admission_enquiry",
        "request_id" => strlen($request_id) > 6 ? $request_id : null,
        "user_id" => $defaultUser->user_id
    ];

    // get the reports list
    $frontObj = !$loadForm ? load_class("frontoffice", "controllers") : null;
    $results_array = !$loadForm ? ($frontObj->list($param)["data"] ?? []) : [];

    // if the request is not apply
    if($request_id && !empty($results_array)) {
        $singleData = true;
        $data = $results_array[0];
    }

    if(!$loadForm) {

        // loop through the results array list
        foreach($results_array as $key => $each) {
    
            $action = "<button title='View Record Details' onclick='return load(\"office_enquiry/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></button>";
            
            $results_list .= "<tr data-row_id=\"{$each->id}\">";
            $results_list .= "<td>".($key+1)."</td>";
            $results_list .= "<td><span class='user_name' onclick='return load(\"office_enquiry/{$each->item_id}\");'>{$each->content->fullname}</span></td>";
            $results_list .= "<td>{$each->content->phone_number}</td>";
            $results_list .= "<td>{$each->source}</td>";
            $results_list .= "<td>{$each->content->date}</td>";
            $results_list .= "<td>{$each->content->followup}</td>";
            $results_list .= "<td>".$myClass->the_status_label($each->state)."</td>";
            $results_list .= "<td align='center'>{$action}</td>";
            $results_list .= "</tr>";
        }
        
    }

    // if the data is not empty
    if(!empty($data)) {

        // uploads script
        $comment_form = null;   
        $response->scripts = ["assets/js/comments.js", "assets/js/frontoffice.js"];

        // comment form set
        $comment_form = leave_comments_builder("frontoffice", $request_id, false);

        // leave status
        $status = ($data->state == 'Pending') ? 'primary' : ($data->state == 'Won' ? 'success' : ($data->state === 'Passive' ? 'warning' : 'danger'));
        $state = $data->state;

        // set the application data
        $request_data = '
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">

                    <div class="font-17 text-uppercase mb-2">
                        <i class="fa fa-user"></i> 
                        <span class="user_name">'.$data->content->fullname.'</span>
                    </div>
                    <div class="font-14 mb-2">
                        <i class="fa fa-envelope"></i> 
                        <span>'.$data->content->email.'</span>
                    </div>
                    <div class="font-14 border-bottom pb-2 mb-2">
                        <i class="fa fa-phone"></i> 
                        <span>'.$data->content->phone_number.'</span>
                    </div>
                    <div class="font-14 border-bottom pb-2 mb-2">
                        <i class="fa fa-map"></i> 
                        <strong>'.$data->content->address.'</strong>
                    </div>
                    <div class="font-14 border-bottom pb-2 mb-2">
                        <i class="fa fa-calendar-check"></i> 
                        <strong>'.$data->content->date.'</strong>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">
                            <strong>Source</strong>:
                            '.$data->source.'
                        </div>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">
                            <strong>Followup Date</strong> :
                            '.date("jS F Y", strtotime($data->content->followup)).'
                        </div>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">'.$data->content->description.'</div>
                    </div>
                    '.($accessObject->hasAccess("update", "admission_enquiry") ?
                        '<div class="form-group">
                            <label>Enquiry Status</label>
                            <select data-request_url="office_enquiry" data-request_id="'.$data->item_id.'" name="enquiry_status" id="enquiry_status" class="selectpicker" data-width="100%">
                                <option '.($data->state === 'Pending' ? 'selected' : null).' value="Pending">Pending</option>
                                <option '.($data->state === 'Passive' ? 'selected' : null).' value="Passive">Passive</option>
                                <option '.($data->state === 'Dead' ? 'selected' : null).' value="Dead">Dead</option>
                                <option '.($data->state === 'Won' ? 'selected' : null).' value="Won">Won</option>
                                <option '.($data->state === 'Lost' ? 'selected' : null).' value="Lost">Lost</option>
                            </select>
                        </div>' : "<strong>Status: </strong><span class='badge badge-{$status}'>{$state}</span>"
                    ).'
                    <div class="font-15  pb-2 mb-2">
                        <div>
                            <i class="fa fa-user"></i>
                            <span onclick="return load(\'staff/'.$data->created_by.'/documents\')" class="user_name">
                            '.$data->name.'
                            </span>
                        </div>
                        <div>
                            <i class="fa fa-phone"></i> 
                            '.$data->phone_number.'
                        </div>
                        <div>
                            <i class="fa fa-envelope"></i> 
                            '.$data->email.'
                        </div>
                        <div>
                            <i class="fa fa-calendar-check"></i> 
                            '.$data->date_created.'
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="slim-scroll">
                        <div class="p-0 m-0">
                            '.$comment_form.'
                            <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$request_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                            <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // set the application form
    $request_form = $loadForm ? load_class("forms", "controllers")->enquiry_form() : null;

    // set the html data
    $response->html = '
        <section class="section">

            <div class="section-header">
                <h1>'.$pageTitle.' 
                    '.(!$loadForm ? '
                        <span onclick="load(\'office_enquiry/log\')" class="btn btn-sm btn-primary">
                            Log Enquiry
                        </span>' : null
                    ).'
                </h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    '.($loadForm || !empty($data) ? 
                        '<div class="breadcrumb-item">
                            <a href="'.$baseUrl.'office_enquiry">Admission Enquiry</a>
                        </div>' : null
                    ).'
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>

            <div class="row">

                '.(!empty($data) ? $request_data : null).'

                <div '.(!empty($data) ? 'hidden' : null).' class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            
                            '.(!$loadForm && !$data ?
                                '
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Source</th>
                                                <th>Enquiry Date</th>
                                                <th>Followup Date</th>
                                                <th>Status</th>
                                                <th width="12%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>'.$results_list.'</tbody>
                                    </table>
                                </div>' : null
                            ).'
                            
                            '.($loadForm ? $request_form : null).'

                        </div>
                    </div>
                </div>
            </div>

        </section>';

}

// print out the response
echo json_encode($response);
?>