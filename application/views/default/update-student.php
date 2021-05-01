<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Student Details";
$response->title = "{$pageTitle} : {$appName}";

$response->scripts = ["assets/js/page/index.js"];

// student id
$user_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($user_id)) {

    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "full_details" => true,
        "no_limit" => 1,
        "user_type" => "student"
    ];

    $data = load_class("users", "controllers")->list($student_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        // load the incidents
        $incidents = load_class("incidents", "controllers")->list($student_param);
        
        // user permissions
        $hasUpdate = $accessObject->hasAccess("update", "guardian");
        $addIncident = $accessObject->hasAccess("add", "incident");
        $updateIncident = $accessObject->hasAccess("update", "incident");
        $deleteIncident = $accessObject->hasAccess("delete", "incident");

        // can recieve
        $canReceive = $accessObject->hasAccess("receive", "fees");
        $viewAllocation = $accessObject->hasAccess("view_allocation", "fees");

        // receive fees payment 
        $receivePayment = !empty($canReceive) ? $canReceive : $isParent;

        // load fees allocation list for class
        $allocation_param = (object) ["clientId" => $clientId, "userData" => $defaultUser, "student_id" => $user_id, "receivePayment" => $receivePayment];
        
        // load the class timetable
        $timetable = load_class("timetable", "controllers")->class_timetable($data->class_guid, $clientId);

        // if the user has permissions to view fees allocation
        if($viewAllocation) {
            // load fees allocation list for the students
            $student_allocation_list = load_class("fees", "controllers", $allocation_param)->student_allocation_array($allocation_param);
        }

        // populate the incidents
        $incidents_list = "";

        // list the user incidents
        if(!empty($incidents["data"])) {
            
            // begin the html contents
            $incidents_list = "<div class='row mb-3'>";

            // loop through the list of all incidents
            foreach($incidents["data"] as $each) {
                // generate the buttons
                $buttons = "<button onclick=\"return load_quick_form('incident_log_form_view','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-primary\" type=\"button\"><i class=\"fa fa-eye\"></i> View</button>&nbsp;";
                
                // is not active
                $isActive = !in_array($each->status, ["Solved", "Cancelled"]);

                // set the update button
                if($updateIncident && $isActive) {
                    $buttons .= "<button onclick=\"return load_quick_form('incident_log_form','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-success\" type=\"button\"><i class=\"fa fa-edit\"></i> Update</button>";
                }

                if($deleteIncident && $isActive) {
                    $buttons .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->item_id}\", \"incident\");' class='btn mb-1 btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a>";
                }

                //append to the list
                $incidents_list .= "
                <div class=\"col-12 col-md-6 load_incident_record col-lg-6\" data-id=\"{$each->item_id}\">
                    <div class=\"card card-success\">
                        <div class=\"card-header pr-2 pl-2\"><h4>{$each->subject}</h4></div>
                        <div class=\"card-body p-2\" style=\"height:150px;max-height:150px;overflow:hidden;\">{$each->description}</div>
                        <div class=\"pl-2 border-top mt-2\"><strong>Status: </strong> {$myClass->the_status_label($each->status)}</div>
                        <div class=\"pl-2\"><strong>Reported By: </strong> {$each->reported_by}</div>
                        ".(!empty($each->assigned_to_info->name) ? 
                            "<div class=\"pl-2\"><strong>Assigned To: </strong> 
                                {$each->assigned_to_info->name}, {$each->assigned_to_info->phone_number}
                            </div>" : null
                        )."
                        ".(($each->created_by === $session->userId) ? 
                            "<div class=\"pl-2\"><strong>Recorded By: </strong> 
                                {$each->created_by_information->name}, {$each->created_by_information->phone_number}
                            </div>" : null
                        )."
                        <div class=\"pl-2 mb-1 mt-2\">
                            <div class=\"d-flex p-2 justify-content-between\">
                                ".($updateIncident && $isActive ? "
                                    <div>
                                        <button onclick=\"return load_quick_form('incident_log_followup_form','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-warning\" type=\"button\"><i class=\"fa fa-list\"></i> Followups</button>
                                    </div>" : "<div>&nbsp;</div>"
                                )."
                                <div>{$buttons}</div>
                            </div>
                        </div>
                        <div class=\"card-footer pr-2 pb-2 pl-2 m-0 pt-1 border-top\">
                            <div class=\"d-flex justify-content-between\">
                                <div><i class=\"fa fa-home\"></i> {$each->location}</div>
                                <div><i class=\"fa fa-calendar-check\"></i> {$each->incident_date}</div>
                            </div>
                        </div>
                    </div>
                </div>";
            }

            $incidents_list .= "</div>";
            $response->client_auto_save = ["incidents_array" => $incidents["data"]];
        }

        // if the incident is empty
        if(empty($incidents_list)) {
            $incidents_list = "<div class='text-center font-italic'>No recorded incidents</div>";
        }

        // guardian information
        $user_form = load_class("forms", "controllers")->student_form($clientId, $baseUrl, $data);

        $guardian = "
            <div class='card-body p-2 pl-0' id='ward_guardian_information'>
                <div class='d-flex justify-content-between'>
                    <div><h5>GUARDIAN INFORMATION</h5></div>
                    ".($hasUpdate ? "<div><button onclick='return load_quick_form(\"modify_ward_guardian\",\"{$data->user_id}\");' class='btn btn-outline-primary btn-sm' type='button'><i class='fa fa-user'></i> Add Guardian</button></div>" : "")."
                </div>";

        // if the guardian information is not empty
        if(!empty($data->guardian_list)) {
            // loop through the guardian list
            foreach($data->guardian_list as $each) {
                $guardian .= "<div class='row mb-3 border-bottom pb-3' data-ward_guardian_id='{$each->user_id}'>";
                $guardian .= "<div class='col-lg-3'><strong>Fullname:</strong><br> {$each->fullname}</div>";
                $guardian .= "<div class='col-lg-2'><strong>Relation:</strong><br> {$each->relationship}</div>";
                $guardian .= "<div class='col-lg-3'><strong>Contact:</strong><br> {$each->contact}</div>";
                $guardian .= "<div class='col-lg-4'><strong>Email:</strong><br> {$each->email}</div>";
                $guardian .= "<div class='col-lg-12'><strong>Address:</strong><br> {$each->address}</div>";

                // if the user has permissions to update the student record
                if($hasUpdate) {
                    $guardian .= "<div class='col-lg-12 text-right'>
                        <a href=\"{$baseUrl}update-guardian/{$each->user_id}/view\" class=\"btn btn-sm btn-outline-success\" title=\"View guardian full details\"><i class=\"fa fa-eye\"></i> View</a>
                        <a onclick=\"return modifyWardGuardian('{$each->user_id}_{$data->user_id}','remove');\" href=\"javascript:void(0);\" class=\"btn btn-outline-danger anchor btn-sm\">Remove</a>
                    </div>";
                }
                $guardian .= "</div>";
            }
        } else {
            $guardian .= "<div class='font-italic'>No guardian has been attached to this Student</div>";
        }

        $guardian .= "</div>";

        // push the guardian ids into an array
        $response->array_stream["student_guardian_array_{$user_id}"] = $data->guardian_id;

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-student">Students List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center">
                        <img alt="image" src="'.$baseUrl.''.$data->image.'" class="rounded-circle author-box-picture">
                        <div class="clearfix"></div>
                        <div class="author-box-name"><a href="#">'.$data->name.'</a></div>
                        <div class="author-box-job">'.$data->class_name.'</div>
                        <div class="author-box-job">('.$data->department_name.')</div>
                    </div>
                    <div class="text-center">
                        <div class="author-box-description">'.$data->description.'</div>
                        <div class="w-100 d-sm-none"></div>
                    </div>
                </div>
                </div>
                <div class="card">
                <div class="card-header">
                    <h4>Personal Details</h4>
                </div>
                <div class="card-body pt-0 pb-0">
                    <div class="py-4">
                        <p class="clearfix">
                            <span class="float-left">Enrollment Date</span>
                            <span class="float-right text-muted">'.$data->enrollment_date.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Gender</span>
                            <span class="float-right text-muted">'.$data->gender.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Section</span>
                            <span class="float-right text-muted">'.$data->section_name.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Birthday</span>
                            <span class="float-right text-muted">'.$data->date_of_birth.'</span>
                        </p>
                        '.($data->phone_number ?
                            '<p class="clearfix">
                                <span class="float-left">Phone</span>
                                <span class="float-right text-muted">'.$data->phone_number.'</span>
                            </p>' : ''
                        ).'
                        '.($data->email ?
                            '<p class="clearfix">
                                <span class="float-left">E-Mail</span>
                                <span class="float-right text-muted">'.$data->email.'</span>
                            </p>' : ''
                        ).'
                        <p class="clearfix">
                            <span class="float-left">Blood Group</span>
                            <span class="float-right text-muted">'.$data->blood_group_name.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Residence</span>
                            <span class="float-right text-muted">'.$data->residence.'</span>
                        </p>
                        '.($data->religion ? 
                            '<p class="clearfix">
                                <span class="float-left">Religion</span>
                                <span class="float-right text-muted">'.$data->religion.'</span>
                            </p>' : ''
                        ).'
                        <p class="clearfix">
                            <span class="float-left">Country</span>
                            <span class="float-right text-muted">'.$data->country_name.'</span>
                        </p>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(!$updateItem ? "active" : null).'" id="home-tab2" data-toggle="tab" href="#about" role="tab"
                        aria-selected="true">Other Information</a>
                    </li>
                    '.($viewAllocation ? 
                    '<li class="nav-item">
                        <a class="nav-link" id="fees-tab2" data-toggle="tab" href="#fees" role="tab"
                        aria-selected="true">Fees Allocation</a>
                    </li>' : '').'
                    <li class="nav-item">
                        <a class="nav-link" id="calendar-tab2" data-toggle="tab" href="#calendar" role="tab"
                        aria-selected="true">Timetable</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="incident-tab2" data-toggle="tab" href="#incident" role="tab"
                        aria-selected="true">Incidents</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Record</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                            '.($data->description ? "
                                <div class='mb-3'>
                                    <div class='card-body p-2 pl-0'>
                                        <div><h5>DESCRIPTION</h5></div>
                                        {$data->description}
                                    </div>
                                </div>
                            " : "").'
                            <div class="mb-3">
                                <div class="card-body p-2 pl-0">
                                    <div><h5>PREVIOUS SCHOOL DETAILS</h5></div>
                                    <table width="100%" class="table-bordered">
                                        <tr>
                                            <td class="p-2" width="20%">School Name</td>
                                            <td class="p-2">'.$data->previous_school.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Qualification</td>
                                            <td class="p-2">'.$data->previous_school_qualification.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Remarks</td>
                                            <td class="p-2">'.$data->previous_school_remarks.'</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            '.$guardian.'
                        </div>
                        '.($viewAllocation ? 
                        '<div class="tab-pane fade" id="fees" role="tabpanel" aria-labelledby="fees-tab2">
                            <div class="table-responsive">
                                <table data-empty="" class="table table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th>Student Name</th>
                                            <th>Category</th>
                                            <th>Due</th>
                                            <th>Paid</th>
                                            '.($receivePayment ? '<th width="10%" align="center"></th>' : '').'
                                        </tr>
                                    </thead>
                                    <tbody>'.$student_allocation_list.'</tbody>
                                </table>
                            </div>
                        </div>':'').'
                        <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab2">
                            <div class="table-responsive trix-slim-scroll">
                                '.$timetable.'
                            </div>
                        </div>
                        <div class="tab-pane fade" id="incident" role="tabpanel" aria-labelledby="incident-tab2">
                            <div class="d-flex justify-content-between">
                                <div class="mb-2"><h5>INCIDENTS LOG</h5></div>
                                '.($addIncident ? '
                                    <div>
                                        <button type="button" onclick="return load_quick_form(\'incident_log_form\',\''.$user_id.'\');" class="btn btn-primary"><i class="fa fa-plus"></i> Log Incident</button>
                                    </div>' 
                                : null ).'
                            </div>
                            '.$incidents_list.'
                        </div>
                        <div class="tab-pane fade '.($updateItem ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
                        if($hasUpdate) {
                            $response->html .= $user_form;
                        }

                        $response->html .= '</div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </section>';
        
    }

}
// print out the response
echo json_encode($response);
?>