<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $defaultAcademics, $isWardParent, $isAdmin, $isPayableStaff;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Staff Details";
$response->title = $pageTitle;

// staff id
$user_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(empty($user_id)) {
    $response->html = page_not_found("permission_denied");
} else {
    // set the parameters for the user information
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

        // set the user_id id in the console
        $response->array_stream['user_id'] = $user_id;
        $response->array_stream['url_link'] = "staff/{$user_id}/";
        
        // load the incidents
        $incidents = load_class("incidents", "controllers")->list($staff_param);

        // course listing
        $course_listing = "";

        // set the page title
        $response->title = $data["data"][0]->name;

        // user permissions
        $hasUpdate = $accessObject->hasAccess("update", $data["data"][0]->user_type);
        $addIncident = $accessObject->hasAccess("add", "incident");
        $addCourse = $accessObject->hasAccess("add", "course");
        $updateIncident = $accessObject->hasAccess("update", "incident");
        $deleteIncident = $accessObject->hasAccess("delete", "incident");
        $modifySalaryStructure = $accessObject->hasAccess("modify_payroll", "payslip");

        // has the right to update the user permissions
        $viewPermission = $accessObject->hasAccess("view", "permissions");
        $updatePermission = $accessObject->hasAccess("update", "permissions");

        // confirm that the user is a teacher
        $isTeacher = (bool) ($data["data"][0]->user_type == "teacher");

        // set the start date
        $start_date = date("Y-m-d", strtotime("-1 month"));
        // set the end date
        $end_date = date("Y-m-d");

        // set the url
        $url_link = $SITEURL[2] ?? null;

        // confirm that the user is a teacher
        if($isTeacher) {

            // course list parameter
            $courses_param = (object) [
                "clientId" => $session->clientId,
                "userId" => $defaultUser->user_id,
                "userData" => $defaultUser,
                "course_tutor" => $data["data"][0]->user_id,
                "academic_year" => $defaultAcademics->academic_year,
                "academic_term" => $defaultAcademics->academic_term,
            ];
            $courses_list = load_class("courses", "controllers")->list($courses_param);

            // Subjects List
            if(!empty($courses_list["data"])) {

                $courseDelete = $accessObject->hasAccess("delete", "course");
                $courseUpdate = $accessObject->hasAccess("update", "course");

                // loop through the courses that the teacher handles
                foreach($courses_list["data"] as $key => $each) {

                    $action = "<a href='#' onclick='return load(\"course/{$each->id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

                    if($courseDelete) {
                        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->id}\", \"course\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
                    }

                    $course_listing .= "<tr data-row_id=\"{$each->id}\">";
                    $course_listing .= "<td><span class='user_name' onclick='load(\"course/{$each->item_id}\")'>{$each->name}</span></td>";
                    $course_listing .= "<td>";
                    foreach($each->class_list as $class) {
                        $course_listing .= "<p class='mb-0 pb-0'><span class='user_name' ".(!$isWardParent ? 'onclick="load(\'class/'.$class->id.'\');"' : null).">".$class->name."</span></p>";
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
            $incidents_list = "<div class='row mb-3 pt-2'>";

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
            $incidents_list = "<div class='mt-2'>";
            $incidents_list .= no_record_found("No incident recorded", "No incident has been recorded for this staff member yet.", null, "Incidents", false, "fa fa-ambulance", false);
            $incidents_list .= "</div>";
        }

        // set the first key
        $data = $data["data"][0];

        // create a new object of the forms class
        $formsObj = load_class("forms", "controllers");

        // guardian information
        $user_form = $formsObj->staff_form($clientId, $baseUrl, $data);

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // user  permission information
        $level_data = "<div class='row'>";

        // if the user can update permission
        if(($viewPermission && $defaultUser->user_id == $user_id) || ($updatePermission)) {

            // designation permission
            $role_permission = $myClass->pushQuery("user_permissions", "users_types", "description='{$data->user_type}' LIMIT 1");
            $role_permission = $role_permission[0]->user_permissions ?? [];
            $role_permission = !empty($role_permission) ? json_decode($role_permission, true) : [];

            // convert to an array
            $user_permission = !empty($data->user_permissions) && !is_array($data->user_permissions) ? json_decode($data->user_permissions, true)["permissions"] : $data->user_permissions["permissions"];
            
            // disable the input field if the current user is also logged in
            $isDisabled = ($session->userId == $user_id) ? "disabled='disabled'" : null;
            $isDisabled = $updatePermission ? $isDisabled : "disabled='disabled'";

            // if the permission is not empty
            if(!empty($role_permission)) {
                
                $atLeastOnePermission = false;

                // loop through the list
                foreach ($role_permission["permissions"] as $key => $value) {
                    $header = ucwords(str_replace("_", " ", $key));
                    $level_data .= "<div class='col-lg-12 mb-2 border-bottom border-default'><h6 style='font-weight:bolder'>".$header."</h6>";

                    $level_data .= "<div class='row'>";
                    // loop through the user permissions
                    foreach($value as $nkey => $nvalue) {
                        
                        // confirm the user has the permission					
                        $isPermitted = $user_permission[$key][$nkey] ?? null;

                        // if the user has the permission
                        $valueToSet = $isPermitted && $isDisabled ? $isDisabled : (
                            !$isPermitted && $isDisabled ? '' : $isDisabled
                        );

                        if($isDisabled && !$isPermitted) {
                            // print $key . " --- " . $nkey . " \n\n";
                            $atLeastOnePermission = true;
                        }
                        
                        // if the user access was parsed
                        $level_data .= "<div class='col-lg-3 col-md-6'>";
                        $level_data .= "<input {$valueToSet} ".($isPermitted ? "checked" : null )." type='checkbox' class='brands-checkbox' ".($updatePermission ? "id='access_level[$key][$nkey]' name='access_level[$key][$nkey][]'" : null).">";
                   
                        $level_data .= "<label class='cursor' ".($updatePermission ? "for='access_level[$key][$nkey]'" : null)."> &nbsp; ".ucwords(str_ireplace("_", " ", $nkey))."</label>";
                        $level_data .= "</div>";
                        
                    }
                    $level_data .= "</div>";

                    $level_data .= "</div>";
                }

            }
            
            $level_data .= "</div>";

        }

        // change password url
        $attachment_html = null;
        $change_password_url = null;
        $isCurrentUser = (bool) ($defaultUser->user_id == $user_id);

        // if the user has permission
        if($isAdmin || $isCurrentUser) {
            // set up the information
            if($isAdmin && ($defaultUser->user_id !== $user_id)) {
                $change_password_url = "password_manager?lookup={$data->unique_id}";
            } else {
                $change_password_url = "profile?security";
            }

            // fetch the files again
            $prevData = $myClass->pushQuery("a.id, a.created_by, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
                "users a", "a.item_id='{$user_id}' AND a.client_id='{$clientId}' LIMIT 1");

            // if the data was is not empty
            if(!empty($prevData)) {
                // decode the json string
                $db_attachments = empty($prevData[0]->attachment) ? '' : json_decode($prevData[0]->attachment);
                $attachment_html = $formsObj->list_attachments($db_attachments->files ?? [], $prevData[0]->created_by, "col-lg-4 col-md-6", $isAdmin, false);
            }
        }
        
        // append to the scripts
        $response->scripts = ["assets/js/webcam.js"];

        // set the other parametrs
        if($hasUpdate) {
            // append to the scripts
            $response->scripts = ["assets/js/analitics.js", "assets/js/upload.js", "assets/js/staff.js", "assets/js/webcam.js"];

            // file upload parameter
            $file_params = (object) [
                "module" => "staff_documents_".$user_id,
                "userData" => $defaultUser,
                "ismultiple" => true,
                "accept" => ".doc,.docx,.pdf,.png,.jpg,jpeg"
            ];
        }

        // expected days to be present in school
        $expected_days = $myClass->stringToArray($data->expected_days);

        // set the days of the week
        $daysOfWeek = $myClass->days_of_week;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-user-tie"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    '.($isPayableStaff ? 
                        '<div class="breadcrumb-item active"><a href="'.$baseUrl.'staffs">Staff</a></div>' : 
                        '<div class="breadcrumb-item active"><a href="#" onclick="return false;">Staff</a></div>'
                    ).'
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>';
            // if the term has ended
            if($isAdminAccountant) {
                $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
            }

            $response->html .= '
            <div class="section-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card rounded-2xl hover:scale-105 transition-all duration-300">
                        <div class="card-body text-center bg-gradient-to-br from-amber-200 to-amber-100 rounded-2xl shadow-lg text-white card-type-3">
                            <div class="font-18 text-dark font-weight-bold">STAFF ID</div>
                            <div class="font-18 text-uppercase text-black">'.$data->unique_id.'</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card rounded-2xl hover:scale-105 transition-all duration-300">
                        <div class="card-body text-center bg-gradient-to-br from-blue-200 to-blue-100 rounded-2xl shadow-lg text-white card-type-3">
                            <div class="font-18 text-dark font-weight-bold">POSITION</div>
                            <div class="font-18 text-uppercase text-black">'.($data->position ? $data->position : '-' ).'</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card rounded-2xl hover:scale-105 transition-all duration-300">
                        <div class="card-body text-center bg-gradient-to-br from-green-200 to-green-100 rounded-2xl shadow-lg text-white card-type-3">
                            <div class="font-18 text-dark font-weight-bold">DEPARTMENT</div>
                            <div class="font-18 text-uppercase text-black">
                                '.($data->section_name ? $data->section_name : '-' ).'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card author-box">
                        <div class="card-body">
                            <div class="author-box-center m-0 p-0 flex justify-center">
                                <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                            </div>
                            <div class="author-box-center">
                                <div class="clearfix"></div>
                                <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                                <div class="author-box-job"><strong>'.strtoupper($data->user_type).'</strong></div>
                            </div>
                            <div class="text-center mt-3">
                            '.($modifySalaryStructure ? '<a class="btn mb-1 btn-primary" href="'.$baseUrl.'payroll-view/'.$user_id.'"><i class="fa fa-edit"></i> Salary Structure</a>' : null).'
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">PERSONAL INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-2">
                            <div class="py-2">
                                <p class="clearfix">
                                    <span class="float-left">Date of Employment</span>
                                    <span class="float-right text-muted">'.format_date_to_show($data->enrollment_date).'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Gender</span>
                                    <span class="float-right text-muted">'.$data->gender.'</span>
                                </p>
                                '.(!empty($data->section_name) ? 
                                '<p class="clearfix">
                                    <span class="float-left">Section</span>
                                    <span class="float-right text-muted">'.$data->section_name.'</span>
                                </p>' : '').'
                                <p class="clearfix">
                                    <span class="float-left">Date of Birth</span>
                                    <span class="float-right text-muted">'.format_date_to_show($data->date_of_birth).'</span>
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
                                '.(
                                    !empty($data->blood_group_name) ? '
                                    <p class="clearfix">
                                        <span class="float-left">Blood Group</span>
                                        <span class="float-right text-muted">'.$data->blood_group_name.'</span>
                                    </p>' : null
                                ).'
                                '.(
                                    !empty($data->residence) ? '
                                    <p class="clearfix">
                                        <span class="float-left">Residence</span>
                                        <span class="float-right text-muted">'.$data->residence.'</span>
                                    </p>' : null
                                ).'
                                '.(
                                    !empty($data->country_name) ? '
                                    <p class="clearfix">
                                        <span class="float-left">Country</span>
                                        <span class="float-right text-muted">'.$data->country_name.'</span>
                                    </p>' : null
                                ).'
                                '.$myClass->qr_code_renderer('employee', $data->user_row_id, $clientId, $data->name).'
                            </div>
                            <div class="font-14 text-uppercase mb-0 border-top border-primary pt-3">
                                <div class="font-14 text-uppercase mt-0 mb-2 font-weight-bold mb-0">EXPECTED DAYS</div>
                            '.implode(" ", array_map(function($day) use ($expected_days, $user_id) {
                                return "
                                    <div style='padding-left: 2.5rem;' class='custom-control cursor col-lg-12 custom-switch switch-primary'>
                                        <input onchange='return update_expected_days(\"{$user_id}\", \"users\");' type='checkbox' name='expected_days[]' value='".ucfirst($day)."' class='custom-control-input cursor' id='".$day."' ".(in_array($day, $expected_days) ? "checked='checked'" : null).".>
                                        <label class='custom-control-label cursor text-black' for='".$day."'>".$day."</label>
                                    </div>";
                            }, $daysOfWeek)).'
                            </div>
                        </div>
                    </div>
                    '.($isAdmin || $user_id == $defaultUser->user_id ?         
                        '<div class="card">
                            <div class="card-header">
                                <h4 class="mb-0">LOGIN INFORMATION</h4>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <div class="py-2">
                                    <p class="clearfix">
                                        <span class="float-left">Username</span>
                                        <span class="float-right text-muted">'.$data->username.'</span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">Password</span>
                                        <span class="float-right text-muted">
                                            <button onclick="return load(\''.$change_password_url.'\')" class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-lock"></i> Security Update
                                            </button>
                                        </span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">Last Login</span>
                                        <span class="float-right text-muted">'.$data->last_login.'</span>
                                    </p>
                                </div>
                            </div>
                        </div>' : null
                    ).'
                </div>
                <div class="col-12 col-md-12 col-lg-8">
                    <div class="card stick_to_top">
                    <div class="padding-20">
                        <ul class="nav nav-tabs" id="myTab2" role="tablist">
                        '.($isCurrentUser || $isAdminAccountant ? '
                            <li class="nav-item">
                                <a class="nav-link '.(empty($url_link) || $url_link === "documents" ? "active" : null).'"  onclick="return appendToUrl(\'documents\')" id="documents-tab2" data-toggle="tab" href="#documents" role="tab" aria-selected="true">Documents</a>
                            </li>' : null
                        ).'
                        '.(
                            $isTeacher ? '
                            <li class="nav-item">
                                <a class="nav-link" id="calendar-tab2" data-toggle="tab" href="#course_list" role="tab"
                                aria-selected="true">Subjects</a>
                            </li>' : null
                        ).'
                        <li class="nav-item">
                            <a class="nav-link '.($url_link === "incidents" ? "active" : null).'" onclick="return appendToUrl(\'incidents\')" id="incident-tab2" data-toggle="tab" href="#incident" role="tab"
                            aria-selected="true">Incidents</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link '.($url_link === "attendance" ? "active" : null).'" onclick="return appendToUrl(\'attendance\')" id="attendance-tab2" data-toggle="tab" href="#attendance" role="tab"
                            aria-selected="true">Attendance</a>
                        </li>';

                        if(($viewPermission && $defaultUser->user_id == $user_id) || ($updatePermission)) {
                            $response->html .= '
                            <li class="nav-item">
                                <a class="nav-link '.($url_link === "permissions" ? "active" : null).'" id="permissions-tab2"  onclick="return appendToUrl(\'permissions\')" data-toggle="tab" href="#permissions" role="tab" aria-selected="true">Permissions</a>
                            </li>';
                        }

                        if($hasUpdate) {
                            $response->html .= '
                            <li class="nav-item">
                                <a class="nav-link '.($url_link === "update" ? "active" : null).'" id="profile-tab2" onclick="return appendToUrl(\'update\')" data-toggle="tab" href="#settings" role="tab"
                                aria-selected="false">Edit Record</a>
                            </li>';
                        }
                        
                        $response->html .= '
                        </ul>
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            '.(
                            $isTeacher ? '
                                <div class="tab-pane fade" id="course_list" role="tabpanel" aria-labelledby="course_list-tab2">
                                    <div class="d-flex justify-content-between mb-4">
                                        <div class="mb-2"></div>
                                        '.($addCourse ? '
                                            <div>
                                                <a href="'.$baseUrl.'course_add" class="btn btn-primary"><i class="fa fa-plus"></i> Add Subject</a>
                                            </div>' 
                                        : null ).'
                                    </div>
                                    <div class="table-responsive">
                                        <table data-empty="" class="table table-bordered table-sm table-striped raw_datatable">
                                            <thead>
                                                <tr>
                                                    <th>Subject Title</th>
                                                    <th width="30%">Class</th>
                                                    <th class="text-center" width="15%"></th>
                                                </tr>
                                            </thead>
                                            <tbody>'.$course_listing.'</tbody>
                                        </table>
                                    </div>
                                </div>' : null
                            ).'
                            '.($isCurrentUser || $isAdminAccountant ? '
                                <div class="tab-pane '.(empty($url_link) || $url_link === "documents" ? "show active" : null).' fade" id="documents" role="tabpanel" aria-labelledby="documents-tab2">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div class="mb-2 text-uppercase"></div>
                                        '.($hasUpdate ? "<div><button onclick='return show_eDocuments_Modal();' class='btn btn-outline-primary btn-sm'><i class='fa fa-upload'></i> Upload</button></div>" : null).'
                                    </div>
                                    <div data-ebook_resource_list="'.$user_id.'">
                                        '.($attachment_html ? $attachment_html : no_record_found("No document uploaded", "No document has been uploaded for this staff member yet.", null, "Documents", false, "fa fa-file-alt")).'
                                    </div>
                                </div>' : null
                            ).'
                            <div class="tab-pane '.($url_link === "attendance" ? "show active" : null).' fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab2">
                                <div id="single_user_data" class="row default_period" data-current_period="this_month">
                                    <div id="data-report_stream" data-report_stream="attendance_report&label[staff_id]='.$user_id.'">
                                        <div class="row p-2">
                                            <div class="col-lg-6 col-md-5">
                                                <a target="_blank" data-href_path="attendance_summary" class="btn btn-outline-success" href="'.$baseUrl.'download/attendance?user_id='.$user_id.'&start_date='.$start_date.'&end_date='.$end_date.'&user_type=staff&att_d=true">
                                                    <i class="fa fa-download"></i> Download Attendance Report
                                                </a>
                                            </div>
                                            <div class="col-lg-6 col-md-7 text-right">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                                    </div>
                                                    <input data-item="attendance" data-maxdate="'.$myClass->data_maxdate.'" value="'.$start_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" id="group_start_date">
                                                    <input data-item="attendance" data-maxdate="'.$myClass->data_maxdate.'" value="'.$end_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" id="group_end_date">
                                                    <div class="input-group-append">
                                                        <button style="border-radius:0px" onclick="return filter_Single_UserGroup_Attendance(\'&label[staff_id]='.$user_id.'\',\'user_id='.$user_id.'&user_type='.$data->user_type.'\')" class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-2 quick_loader" id="users_attendance_loader">
                                            <div class="form-content-loader" style="display: flex; position: absolute">
                                                <div class="offline-content text-center">
                                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                                </div>
                                            </div>
                                            <div id="attendance_chart_list"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            '.(
                            $viewPermission ? '
                                <div class="tab-pane '.($url_link === "permissions" ? "show active" : null).' fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab2">
                                    <div class="mb-3 pb-0 border-bottom"><h5>USER PERMISSIONS</h5></div>
                                    '.($updatePermission ? '<form class="ajaxform" id="ajaxform" '.(!$isDisabled || $atLeastOnePermission ? 'action="'.$baseUrl.'api/users/save_permission"' : null).' method="POST">' : null).'
                                        '.$level_data.'
                                        <div class="row">
                                            <input type="hidden" readonly name="user_id" id="user_id" value="'.$user_id.'">
                                            '.($atLeastOnePermission ? '<input type="hidden" name="append_permit" value="1">' : null).'
                                            '.(($updatePermission && !$isDisabled) || $atLeastOnePermission ? 
                                            '<div class="col-lg-12 text-right">
                                                <button type="submit" '.($atLeastOnePermission ? null : $isDisabled).' class="btn btn-success"><i class="fa fa-save"></i> Save Permissions</button>
                                            </div>' : null).'
                                        </div>
                                    '.($updatePermission ? '</form>' : null).'
                                </div>' : null
                            ).'
                            <div class="tab-pane '.($url_link === "incidents" ? "show active" : null).' fade" id="incident" role="tabpanel" aria-labelledby="incident-tab2">
                                <div class="d-flex justify-content-between">
                                    <div class="mb-2"></div>
                                    '.($addIncident ? '
                                        <div>
                                            <button type="button" onclick="return load_quick_form(\'incident_log_form\',\''.$user_id.'\',\''.$data->user_type.'\');" class="btn btn-primary"><i class="fa fa-plus"></i> Log Incident</button>
                                        </div>' 
                                    : null ).'
                                </div>
                                '.$incidents_list.'
                            </div>
                            <div class="tab-pane fade '.($url_link === "update" ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                            
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

        // if the user has permission to upload files
        if($hasUpdate) {

            // append the form
            $response->html .= '
            <div class="modal fade" id="ebook_Resource_Modal_Content" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg" style="width:100%;height:100%;" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><div><strong>Upload E-Resource</strong></div></h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="p-3">
                                '.$formsObj->form_attachment_placeholder($file_params).'
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button data-dismiss="modal" class="btn btn-outline-secondary">CLose</button>
                                    </div>
                                    <div>
                                        <button onclick="return upload_Employee_Documents(\''.$user_id.'\');" class="btn btn-outline-success"><i class="fa fa-upload"></i> Upload Files</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
        
    }

}
// print out the response
echo json_encode($response);
?>