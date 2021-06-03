<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $defaultAcademics;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Staff Details";
$response->title = "{$pageTitle} : {$appName}";

$response->scripts = [
    "assets/js/page/index.js"
];

// staff id
$user_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($user_id)) {

    $staff_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "full_details" => true,
        "no_limit" => 1,
        "user_type" => "employee,teacher,admin,accountant",
        "client_data" => $defaultUser->client
    ];

    // bypass check if the user is a student or parent
    if(!empty($session->student_id)) {
        $staff_param->bypass = true;
        $staff_param->user_type = "teacher,accountant";
    }

    $data = load_class("users", "controllers", $staff_param)->list($staff_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {
        
        // load the incidents
        $incidents = load_class("incidents", "controllers")->list($staff_param);

        // course listing
        $course_listing = "";

        // user permissions
        $hasUpdate = $accessObject->hasAccess("update", $data["data"][0]->user_type);
        $addIncident = $accessObject->hasAccess("add", "incident");
        $addCourse = $accessObject->hasAccess("add", "course");
        $updateIncident = $accessObject->hasAccess("update", "incident");
        $deleteIncident = $accessObject->hasAccess("delete", "incident");

        // has the right to update the user permissions
        $updatePermission = $accessObject->hasAccess("update", "permissions");

        // confirm that the user is a teacher
        $isTeacher = (bool) ($data["data"][0]->user_type == "teacher");

        // confirm that the user is a teacher
        if($isTeacher) {

            // course list parameter
            $courses_param = (object) [
                "clientId" => $session->clientId,
                "userId" => $defaultUser->user_id,
                "userData" => $defaultUser,
                "course_tutor" => $data["data"][0]->user_id,
                "limit" => 99999,
                "academic_year" => $defaultAcademics->academic_year,
                "academic_term" => $defaultAcademics->academic_term,
            ];
            $courses_list = load_class("courses", "controllers")->list($courses_param);

            // courses list
            if(!empty($courses_list["data"])) {

                $courseDelete = $accessObject->hasAccess("delete", "course");
                $courseUpdate = $accessObject->hasAccess("update", "course");

                // loop through the courses that the teacher handles
                foreach($courses_list["data"] as $key => $each) {

                    $action = "<a href='#' onclick='return loadPage(\"{$baseUrl}update-course/{$each->id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

                    if($courseUpdate) {
                        $action .= "&nbsp;<a href='#' onclick='return loadPage(\"{$baseUrl}update-course/{$each->id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
                    }
                    if($courseDelete) {
                        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->id}\", \"course\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
                    }

                    $course_listing .= "<tr data-row_id=\"{$each->id}\">";
                    $course_listing .= "<td>&nbsp; {$each->name}</td>";
                    $course_listing .= "<td>{$each->course_code}</td>";
                    $course_listing .= "<td>{$each->credit_hours}</td>";
                    $course_listing .= "<td>";
                    foreach($each->class_list as $class) {
                        $course_listing .= "<p class='mb-0 pb-0'><span>".$class->name."</span></p>";
                    }
                    $course_listing .= "</td><td class='text-center'>{$action}</td>";
                    $course_listing .= "</tr>";
                }
            }
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

        // set the first key
        $data = $data["data"][0];

        // guardian information
        $user_form = load_class("forms", "controllers")->staff_form($clientId, $baseUrl, $data);

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // user  permission information
        $level_data = "<div class='row'>";

        // if the user can update permission
        if($updatePermission) {

            // convert to an array
            $user_permission = json_decode($data->user_permissions, true)["permissions"];
            
            // disable the input field if the current user is also logged in
            $isDisabled = ($session->userId == $user_id) ? "disabled='disabled'" : null;

            // if the permission is not empty
            if(!empty($user_permission)) {
                // loop through the list
                foreach ($user_permission as $key => $value) {
                    $header = ucwords(str_replace("_", " ", $key));
                    $level_data .= "<div class='".(isset($thisUserAccess) ? "col-lg-4 col-md-4" : "col-lg-12")." mb-2 border-bottom border-default'><h6 style='font-weight:bolder'>".$header."</h6>";
                    
                    if(!isset($thisUserAccess)) {
                        $level_data .= "<div class='row'>";
                    }
                    
                    // loop through the user permissions
                    foreach($value as $nkey => $nvalue) {						
                        
                        // if the user access was parsed
                        if(isset($thisUserAccess)) {
                            $level_data .= "<div class='col-lg-12'>";
                            $level_data .= "<input {$isDisabled} ".(isset($thisUserAccess[$key][$nkey]) && ($thisUserAccess[$key][$nkey] == 1) ? "checked" : null )." type='checkbox' id='access_level[$key][$nkey]' class='brands-checkbox' name='access_level[$key][$nkey][]'>";
                        } else {
                            $level_data .= "<div class='col-lg-3 col-md-4'>";
                            $level_data .= "<input {$isDisabled} checked='checked' type='checkbox' id='access_level[$key][$nkey]' class='brands-checkbox' name='access_level[$key][$nkey][]'>";
                        }
                        $level_data .= "<label class='cursor' for='access_level[$key][$nkey]'> &nbsp; ".ucfirst($nkey)."</label>";
                        $level_data .= "</div>";
                        
                    }

                    if(!isset($thisUserAccess)) {
                        $level_data .= "</div>";
                    }
                    $level_data .= "</div>";
                }
            }
            
            $level_data .= "</div>";

        }

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-staff">List Staff</a></div>
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
                        '.($data->class_name ? '<div class="author-box-job">'.$data->class_name.'</div>' : '').'
                        <div class="author-box-job"><strong>'.strtoupper($data->user_type).'</strong></div>
                        '.($data->department_name ? '<div class="author-box-job">('.$data->department_name.')</div>' : '').'
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
                            <span class="float-left">Date of Employment</span>
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
                        <p class="clearfix">
                            <span class="float-left">Primary Contact</span>
                            <span class="float-right text-muted">'.$data->phone_number.'</span>
                        </p>
                        '.(!empty($data->phone_number_2) ? 
                        '<p class="clearfix">
                            <span class="float-left">Secondary Contact</span>
                            <span class="float-right text-muted">'.$data->phone_number_2.'</span>
                        </p>' : '').'
                        '.(!empty($data->position) ? 
                        '<p class="clearfix">
                            <span class="float-left">Position</span>
                            <span class="float-right text-muted">'.$data->position.'</span>
                        </p>' : '').'
                        <p class="clearfix">
                            <span class="float-left">E-Mail</span>
                            <span class="float-right text-muted">'.$data->email.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Blood Group</span>
                            <span class="float-right text-muted">'.$data->blood_group_name.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Residence</span>
                            <span class="float-right text-muted">'.$data->residence.'</span>
                        </p>
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
                        aria-selected="true">Summary</a>
                    </li>
                    '.(
                        $isTeacher ? '
                        <li class="nav-item">
                            <a class="nav-link" id="calendar-tab2" data-toggle="tab" href="#course_list" role="tab"
                            aria-selected="true">Course List</a>
                        </li>' : null
                    ).'
                    <li class="nav-item">
                        <a class="nav-link" id="attendance-tab2" data-toggle="tab" href="#attendance" role="tab"
                        aria-selected="true">Attendance</a>
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

                    if($updatePermission) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link" id="permissions-tab2" data-toggle="tab" href="#permissions" role="tab"
                            aria-selected="true">Permissions</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                            '.($data->description ? "
                                <div class='mb-3 border-bottom_'>
                                    <div class='card-body p-2 pl-0'>
                                        <div><h5>DESCRIPTION</h5></div>
                                        {$data->description}
                                    </div>
                                </div>
                            " : "").'
                        </div>
                        '.(
                        $isTeacher ? '
                            <div class="tab-pane fade" id="course_list" role="tabpanel" aria-labelledby="course_list-tab2">
                                <div class="d-flex justify-content-between mb-4">
                                    <div class="mb-2"><h5>COURSES LIST</h5></div>
                                    '.($addCourse ? '
                                        <div>
                                            <a href="'.$baseUrl.'add-course" class="btn btn-primary"><i class="fa fa-plus"></i> Add Course</a>
                                        </div>' 
                                    : null ).'
                                </div>
                                <div class="table-responsive">
                                    <table data-empty="" class="table table-bordered table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th>Course Title</th>
                                                <th>Course Code</th>
                                                <th>Credit Hours</th>
                                                <th width="15%">Class</th>
                                                <th class="text-center" width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>'.$course_listing.'</tbody>
                                    </table>
                                </div>
                            </div>' : null
                        ).'
                        '.(
                        $updatePermission ? '
                            <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab2">
                                <div class="mb-3 pb-0 border-bottom"><h5>USER PERMISSIONS</h5></div>
                                <form class="ajaxform" id="ajaxform" action="'.$baseUrl.'api/users/save_permission" method="POST">
                                    '.$level_data.'
                                    <div class="row">
                                        <input type="hidden" readonly name="user_id" id="user_id" value="'.$user_id.'">
                                        <div class="col-lg-12 text-right">
                                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Permissions</button>
                                        </div>
                                    </div>
                                </form>
                            </div>' : null
                        ).'
                        <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab2">
                            
                            
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