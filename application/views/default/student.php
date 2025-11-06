<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $defaultAcademics, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Student Information";
$response->title = $pageTitle;

// set the parent menu
$response->parent_menu = "students";

// student id
$user_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($user_id)) {

    // set the student parameter
    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "full_details" => true,
        "no_limit" => 1,
        "user_status" => $myClass->default_statuses_list,
        "user_type" => "student",
        "return_password" => true,
        "client_data" => $defaultUser->client
    ];

    $data = load_class("users", "controllers", $student_param)->list($student_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found('Student');
    } else {

        // load the scripts
        $response->scripts = ["assets/js/analitics.js", "assets/js/index.js", "assets/js/staff.js", "assets/js/upload.js", "assets/js/webcam.js"];

        // set the first key
        $data = $data["data"][0];

        // set the page title
        $response->title = $data->name;

        // load the incidents
        $incidents = in_array("incidents", $clientFeatures) ? load_class("incidents", "controllers")->list($student_param) : [];

        // set the user_id id in the console
        $response->array_stream['user_id'] = $user_id;
        $response->array_stream['url_link'] = "student/{$user_id}/";

        $start_date = date("Y-m-d", strtotime("-1 month"));
        $end_date = date("Y-m-d");
        
        // user permissions
        $hasUpdate = $accessObject->hasAccess("update", "student");
        $addIncident = $accessObject->hasAccess("add", "incident");
        $updateIncident = $accessObject->hasAccess("update", "incident");
        $deleteIncident = $accessObject->hasAccess("delete", "incident");

        // can recieve
        $canReceive = $accessObject->hasAccess("receive", "fees");
        $canAllocate = $accessObject->hasAccess("allocation", "fees");
        $viewAllocation = $isParent || $accessObject->hasAccess("view_allocation", "fees");

        // receive fees payment 
        $receivePayment = !empty($canReceive) ? $canReceive : $isParent;

        // load fees allocation list for class
        $allocation_param = (object) [
            "clientId" => $clientId, "userData" => $defaultUser, "student_id" => $user_id, "receivePayment" => $receivePayment, 
            "client_data" => $defaultUser->client, "parse_owning" => true, "show_student" =>  false, "group_by" => "GROUP BY a.payment_id"
        ];
        
        // load the class timetable
        $timetable = load_class("timetable", "controllers", $allocation_param)->class_timetable($data->class_guid, $clientId);

        // attachment information
        $attachment_html = null;
        $student_fees_payments = "";
        $student_fees_list = [];
        $amount = 0;

        // set the url
        $payment_module_url = ($data->payment_module === "Monthly") ? "&payment_module={$data->payment_module}" : null;

        // if the user has permissions to view fees allocation
        if($viewAllocation) {

            // append the academic year and term
            $allocation_param->academic_year = $defaultAcademics->academic_year;
            $allocation_param->academic_term = $defaultAcademics->academic_term;
            $allocation_param->onScholarship = $data->scholarship_status;

            // create a new object
            $feesObject = load_class("fees", "controllers", $allocation_param);
                        
            // load fees allocation list for the students
            $fees_category_list = "";
            $student_fees_list = $feesObject->list($allocation_param)["data"] ?? [];
            $allocation_param->limit = 200;
            $student_allocation_list = $feesObject->student_allocation_array($allocation_param);
            $fees_category_array = $feesObject->category_list($allocation_param)["data"] ?? [];

            // fees category
            foreach($fees_category_array as $category) {
                $fees_category_list .= "<option value=\"{$category->id}\">{$category->name}</option>";
            }

            if(!empty($student_fees_list) && is_array($student_fees_list)) {
                // loop through the list of all fees payment
                foreach($student_fees_list as $key => $record) {

                    // add up the amount to be paid
                    $amount += $record->amount;
                    $record->amount_paid = $record->amount_paid ?? 0;

                    // append to the fees allocation list
                    $student_fees_payments .='
                    <tr>
                        <td>'.($key+1).'</td>
                        <td>
                            '.($record->category_name ? $record->category_name : $record->category_id).'
                        </td>
                        <td>'.$record->payment_method.'</td>
                        <td>'.($record->description ? $record->description : null).'</td>
                        <td>'.$record->recorded_date.'</td>
                        <td>'.number_format($record->amount_paid, 2).'</td>
                        <td class="text-center">'.($record->reversed ? "<span class='badge p-1 badge-danger'>Reversed</span>" : 
                            '<a href="'.$myClass->baseUrl.'receipt/'.$record->payment_id.'" target="_blank" title="Click to print Receipt" class="btn btn-sm btn-outline-warning"><i class="fa fa-print"></i></a></td>').'
                    </tr>';
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
                    $buttons .= "<button title='Click to update this record' onclick=\"return load_quick_form('incident_log_form','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-success\" type=\"button\"><i class=\"fa fa-edit\"></i> Update</button>";
                }

                if($deleteIncident && $isActive) {
                    $buttons .= "&nbsp;<a href='#' title='Click to delete this record' onclick='return delete_record(\"{$each->item_id}\", \"incident\");' class='btn mb-1 btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a>";
                }
                $buttons .= "&nbsp;<a href='{$baseUrl}download/incident?incident_id={$each->item_id}' target='_blank' title='Click to download this record' class='btn mb-1 btn-sm btn-outline-warning'><i class='fa fa-download'></i> Download</a>";

                //append to the list
                $incidents_list .= "
                <div class=\"col-12 col-md-6 load_incident_record col-lg-6\" data-id=\"{$each->item_id}\">
                    <div class=\"card card-success\">
                        <div class=\"card-header pr-2 pl-2\"><h4>{$each->subject}</h4></div>
                        <div class=\"card-body p-2\" style=\"height:150px;max-height:150px;overflow:hidden;\">{$each->description}</div>
                        <div class=\"pl-2 border-top mt-1 pt-2\"><strong>Status: </strong> {$myClass->the_status_label($each->status)}</div>
                        <div class=\"pl-2\"><strong>Reported By: </strong> {$each->reported_by}</div>
                        ".(!empty($each->assigned_to_info->name) ? 
                            "<div class=\"pl-2\"><strong>Assigned To: </strong> 
                                {$each->assigned_to_info->name}
                            </div>" : null
                        )."
                        ".(($each->created_by === $session->userId) ? 
                            "<div class=\"pl-2\"><strong>Recorded By: </strong> 
                                {$each->created_by_information->name} ".(!empty($each->created_by_information->phone_number) ? ", {$each->created_by_information->phone_number}" : null)."
                            </div>" : null
                        )."
                        <div class=\"pl-2 mb-1 mt-2\">
                            ".($updateIncident && $isActive ? "
                                <div class=\"text-center border-bottom pb-2 mb-2\">
                                    <button onclick=\"return load_quick_form('incident_log_followup_form','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-warning\" type=\"button\"><i class=\"fa fa-list\"></i> Followups</button>
                                </div>" : "<div>&nbsp;</div>"
                            )."
                            <div class=\"text-center\">{$buttons}</div>
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
            $incidents_list .= no_record_found("No incident recorded", "No incident has been recorded for this student yet.", null, "Incidents", false, "fa fa-ambulance", false);
        }

        // get the student arrears
        $student_fees_arrears = null;
        $arrears_array = $myClass->pushQuery("arrears_details, arrears_category, fees_category_log, arrears_total", "users_arrears", "student_id='{$data->user_id}' AND client_id='{$clientId}' LIMIT 1");

        // if the user has permission to view the fees allocation
        if($viewAllocation) {

            // if the fees arrears not empty
            if(!empty($arrears_array)) {
                                
                // set a new item for the arrears
                $arrears = $arrears_array[0];

                // convert the item to array
                $arrears_details = json_decode($arrears->arrears_details, true);
                $arrears_category = json_decode($arrears->arrears_category, true);
                $fees_category_log = json_decode($arrears->fees_category_log, true);
                
                // set the arrears_total
                if(round($arrears->arrears_total) > 0) {

                    // set the table head
                    $student_fees_arrears .= "<table class='table table-md table-bordered'>";
                    $students_fees_category_array = filter_fees_category($fees_category_log);

                    // loop through the arrears details
                    foreach($arrears_details as $year => $categories) {
                        // clean the year term
                        $split = explode("...", $year);
                        
                        // set the academic year header
                        $student_fees_arrears .= "<thead>";

                        $student_fees_arrears .= "<tr class='font-20'><td><strong>Academic Year: </strong>".str_ireplace("_", "/", $split[0])."</td>";
                        $student_fees_arrears .= "<td><strong>Academic Term: </strong> {$split[1]}</td></tr>";
                        $student_fees_arrears .= "<tr><th>DESCRIPTION</th><th>BALANCE</th></tr>";
                        $student_fees_arrears .= "</thead>";
                        $student_fees_arrears .= "<tbody>";
                        $total = 0;
                        // loop through the items for each academic year
                        foreach($categories as $cat => $value) {
                            // add the sum
                            $total += $value;
                            $category_name = $students_fees_category_array[$cat]["name"] ?? null;
                            // display the category name and the value
                            $student_fees_arrears .= "<tr><td>{$category_name}</td><td>{$value}</td></tr>";
                        }
                        $student_fees_arrears .= 
                            !$isParent && $canReceive ? 
                            "<tr><td></td>
                                <td class='font-20 font-bold'>
                                    <div class='mb-3'>".number_format($total, 2)."</div>
                                    <button onclick='return load(\"arrears/{$user_id}\")' class='btn text-uppercase btn-sm btn-outline-success'><i class='fa fa-money-bill-alt'></i> Pay Arrears</button>
                                </td>
                            </tr>" : null;
                        $student_fees_arrears .= "</tbody>";
                    }
                    $student_fees_arrears .= "</table>";
                } else {
                    $disabled = "disabled";
                }
            }

        }

        // set the url
        $url_link = $SITEURL[2] ?? null;

        // create a new form object
        $formObject = load_class("forms", "controllers");

        // guardian information
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
                $guardian .= !empty($each->address) ? "<div class='col-lg-12'><strong>Address:</strong><br> {$each->address}</div>" : null;

                // if the user has permissions to update the student record
                if($hasUpdate) {
                    $guardian .= "<div class='col-lg-12 mt-2 text-right'>
                        <a href=\"{$baseUrl}guardian/{$each->user_id}/view\" class=\"btn btn-sm btn-outline-success\" title=\"View guardian full details\"><i class=\"fa fa-eye\"></i> View</a>
                        <a onclick=\"return modifyWardGuardian('{$each->user_id}_{$data->user_id}','remove');\" href=\"javascript:void(0);\" class=\"btn btn-outline-danger anchor btn-sm\">Remove</a>
                    </div>";
                }
                $guardian .= "</div>";
            }
        } else {
            $guardian .= "<div class='font-italic'>No guardian has been attached to this Student.</div>";
        }

        $guardian .= "</div>";

        // push the guardian ids into an array
        $response->array_stream["student_guardian_array_{$user_id}"] = $data->guardian_id;

        // if the request is to view the student information
        $updateItem = false;

        // change password url
        $change_password_url = null;
        if($isAdmin || ($defaultUser->user_id == $user_id)) {
            if($isAdmin && ($defaultUser->user_id !== $user_id)) {
                $change_password_url = "password_manager?lookup={$data->unique_id}";
            } else {
                $change_password_url = "profile?security";
            }
        }

        // if the user is an admin or accountant
        if($isAdminAccountant) {
            // fetch the files again
            $prevData = $myClass->pushQuery("a.id, a.created_by, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
                "users a", "a.item_id='{$user_id}' AND a.client_id='{$clientId}' LIMIT 1");

            // if the data was is not empty
            if(!empty($prevData)) {
                // decode the json string
                $db_attachments = !empty($prevData[0]->attachment) ? json_decode($prevData[0]->attachment) : '';
                $attachment_html = $formObject->list_attachments($db_attachments->files ?? [], $prevData[0]->created_by, "col-lg-4 col-md-6", $isAdmin, false);
            }

            // file upload parameter
            $file_params = (object) [
                "module" => "staff_documents_".$user_id,
                "userData" => $defaultUser,
                "ismultiple" => true,
                "accept" => ".doc,.docx,.pdf,.png,.jpg,jpeg"
            ];
        }

        // set the default image if the user's image was not found
        $data->image = is_file($data->image) && file_exists($data->image) ? $data->image : (
            "assets/img/avatar.png"
        );

        // set the scholarship status
        $scholarship_status = $data->scholarship_status == 1 ? 0 : 1;
        $scholarship_class = !$scholarship_status ? "btn-outline-danger" : "btn-outline-success";
        $scholarship_title = !$scholarship_status ? "<i class='fa fa-ankh'></i> Remove Scholarship" : "<i class='fa fa-ankh'></i> Scholarship";

        // the default data to stream
        $data_stream = "attendance_report&label[student_id]={$user_id}";

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-user-graduate"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'students">Students</a></div>
                    <div class="breadcrumb-item">'.ucwords(explode(" ", strtolower($data->name))[0]).' Details</div>
                </div>
            </div>';
            // if the term has ended
            if($isAdminAccountant) {
                $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
            }

            $response->html .= '
            <div class="section-body">
            <div class="row">
            <div class="col-md-3">
                <div class="card rounded-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="card-body text-center bg-gradient-to-br from-amber-200 to-amber-100 rounded-2xl shadow-lg text-white card-type-3">
                        <div class="font-18 text-dark font-bold">STUDENT ID</div>
                        <div class="font-18 text-uppercase text-black">'.$data->unique_id.'</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="card-body text-center bg-gradient-to-br from-blue-200 to-blue-100 rounded-2xl shadow-lg text-white card-type-3">
                        <div class="font-18 text-dark font-bold">CLASS</div>
                        <div class="font-18 text-uppercase text-black">
                            '.($data->class_name ? $data->class_name : '-' ).'
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card rounded-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="card-body pl-0 pr-0 text-center bg-gradient-to-br from-pink-200 to-pink-100 rounded-2xl shadow-lg text-white card-type-3">
                        <div class="font-18 text-dark font-bold">DEPARTMENT</div>
                        <div class="font-18 text-uppercase text-black">
                            '.($data->department_name ? $data->department_name : '-' ).'
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-none d-sm-block">
                <div class="card rounded-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="card-body pl-0 pr-0 text-center bg-gradient-to-br from-green-200 to-green-100 rounded-2xl shadow-lg text-white card-type-3">
                        <div class="font-18 text-dark font-bold">SECTION</div>
                        <div class="font-18 text-uppercase text-black">
                            '.($data->section_name ? $data->section_name : '-' ).'
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body pr-0 pl-0">
                    <div class="author-box-center m-0 p-0 flex justify-center">
                        <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                    </div>
                    <div class="author-box-center mt-2 text-uppercase font-20 mb-0 p-0">'.$data->name.'</div>
                    <div class="text-center">
                        <div class="author-box-description mt-0">'.$data->the_status_label.'</div>
                        <div class="w-100 mt-2">
                            '.($hasUpdate  ? '
                                '.(in_array($data->user_status, ["Active"]) ? '
                                    <a class="btn mb-1 btn-primary" href="'.$baseUrl.'modify-student/'.$user_id.'"><i class="fa fa-edit"></i> Edit</a>': null
                                ).'
                                <button onclick="return modal_popup(\'change_user_Status\')" class="btn mb-1 btn-outline-dark"><i class="fa fa-assistive-listening-systems"></i> Change State</button>' : null
                            ).'
                            '.(
                                isset($student_allocation_list) && !$student_allocation_list["allocated"] && $canAllocate ? 
                                '<a class="btn mb-1 btn-outline-warning" href="'.$baseUrl.'fees-allocate/'.$user_id.'?set"><i class="fa fa-ankh"></i> Set Student Bill</a>'
                                : null
                            ).'
                            '.($hasUpdate ? '<a target="_blank" title="Export Student Record" class="btn mb-1 hidden btn-outline-danger" href="'.$baseUrl.'download/export/users/'.$user_id.'"><i class="fa fa-download"></i> Export</a>' : null).'
                            '.(
                                $hasUpdate && $isAdminAccountant ? '<span id="scholarship_status" class="text-right"><span class="btn mb-1 '.$scholarship_class.'" onclick="return full_scholarship(\''.$user_id.'\', '.$scholarship_status.')">'.$scholarship_title.'</span>' : null    
                            ).'
                        </div>
                    </div>
                </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="pb-0 mb-0">PERSONAL INFORMATION</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-2">
                            <p class="clearfix">
                                <span class="float-left">Enrollment Date</span>
                                <span class="float-right text-muted">'.format_date_to_show($data->enrollment_date).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Boarding Status</span>
                                <span class="float-right badge badge-'.($data->boarding_status == "Day" ? "primary" : "success").'">'.$data->boarding_status.'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Student Type</span>
                                <span class="float-right badge badge-'.($data->student_type == "Weekend" ? "danger" : "success").'">
                                    '.(!empty($data->student_type) ? $data->student_type : "Weekday").'
                                </span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Gender</span>
                                <span class="float-right text-muted">'.$data->gender.'</span>
                            </p>
                            '.($data->section_name ?
                                '<p class="clearfix">
                                    <span class="float-left">Section</span>
                                    <span class="float-right text-muted">'.$data->section_name.'</span>
                                </p>' : ''
                            ).'
                            <p class="clearfix">
                                <span class="float-left">Birthday</span>
                                <span class="float-right text-muted">'.format_date_to_show($data->date_of_birth).'</span>
                            </p>
                            '.(!empty($data->phone_number) ?
                                '<p class="clearfix">
                                    <span class="float-left">Primary Contact</span>
                                    <span class="float-right text-muted">'.$data->phone_number.'</span>
                                </p>' : ''
                            ).'
                            '.(!empty($data->phone_number_2) ?
                                '<p class="clearfix">
                                    <span class="float-left">Secondary Contact</span>
                                    <span class="float-right text-muted">'.$data->phone_number_2.'</span>
                                </p>' : ''
                            ).'
                            '.(!empty($data->email) ?
                                '<p class="clearfix">
                                    <span class="float-left">E-Mail</span>
                                    <span class="float-right text-muted">'.$data->email.'</span>
                                </p>' : ''
                            ).'
                            '.(!empty($data->blood_group_name) ?
                                '<p class="clearfix">
                                    <span class="float-left">Blood Group</span>
                                    <span class="float-right text-muted">'.$data->blood_group_name.'</span>
                                </p>' : ''
                            ).'
                            <p class="clearfix">
                                <span class="float-left">Residence</span>
                                <span class="float-right text-muted">'.$data->residence.'</span>
                            </p>
                            '.($data->hometown ? 
                                '<p class="clearfix">
                                    <span class="float-left">Hometown</span>
                                    <span class="float-right text-muted">'.$data->hometown.'</span>
                                </p>' : ''
                            ).'
                            '.($data->place_of_birth ? 
                                '<p class="clearfix">
                                    <span class="float-left">Place of Birth</span>
                                    <span class="float-right text-muted">'.$data->place_of_birth.'</span>
                                </p>' : ''
                            ).'
                            '.($data->religion ? 
                                '<p class="clearfix">
                                    <span class="float-left">Religion</span>
                                    <span class="float-right text-muted">'.$data->religion.'</span>
                                </p>' : ''
                            ).'
                            '.($data->country_name ? 
                                '<p class="clearfix">
                                    <span class="float-left">Country</span>
                                    <span class="float-right text-muted">'.$data->country_name.'</span>
                                </p>' : ''
                            ).'
                            '.$myClass->qr_code_renderer("student", $data->user_row_id, $clientId, $data->name).'
                        </div>
                    </div>
                </div>
                '.($isAdmin || $user_id == $defaultUser->user_id ?         
                    '<div class="card stick_to_top">
                        <div class="card-header">
                            <h4 class="mb-0">LOGIN INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-2">
                                <p class="clearfix">
                                    <span class="float-left">Username</span>
                                    <span class="float-right text-muted">'.$data->username.'</span>
                                </p>
                                '.(
                                    !empty($data->pass_word) && password_verify(DEFAULT_PASS, $data->pass_word) ? 
                                    "<p class='clearfix'>
                                        <span class='float-left'>Default Password</span>
                                        <span class='float-right text-muted cursor' id='default_password' title='Click to toggle password' onclick='return show_defaultPassword()'>
                                            **************
                                        </span>
                                    </p>" : null
                                ).'
                                <p class="clearfix">
                                    <span class="float-left">Change Password</span>
                                    <span class="float-right text-muted">
                                        <button onclick="return load(\''.$change_password_url.'\')" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-lock"></i> Reset Password
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
            <div class="col-12 col-md-12 col-lg-8" id="single_user_data">
                <div class="card stick_to_top">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(empty($url_link) || $url_link === "about" ? "active" : null).'" onclick="return appendToUrl(\'about\')" id="home-tab2" data-toggle="tab" href="#about" role="tab" aria-selected="true">Data</a>
                    </li>
                    '.($viewAllocation ? 
                    '<li class="nav-item">
                        <a class="nav-link '.($url_link === "fees" ? "active" : null).'" onclick="return appendToUrl(\'fees\')" id="fees-tab2" data-toggle="tab" href="#fees" role="tab" aria-selected="true">Fees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link '.($url_link === "payments" ? "active" : null).' '.($isWardParent ? "hidden" : null).'" onclick="return appendToUrl(\'payments\')" id="fees_payments-tab2" data-toggle="tab" href="#fees_payments" role="tab" aria-selected="true">Payments</a>
                    </li>' : '').'
                    <li class="nav-item d-none d-sm-block">
                        <a class="nav-link '.($url_link === "documents" ? "active" : null).'" onclick="return appendToUrl(\'documents\')" id="documents-tab2" data-toggle="tab" href="#documents" role="tab" aria-selected="true">Documents</a>
                    </li>
                    <li class="nav-item '.(!in_array("attendance", $clientFeatures) || $isWardParent ? "hidden" : null).'">
                        <a class="nav-link '.($url_link === "attendance" ? "active" : null).'" onclick="return appendToUrl(\'attendance\')" id="attendance-tab2" data-toggle="tab" href="#attendance" role="tab" aria-selected="true">Attendance</a>
                    </li>
                    <li class="nav-item '.(!in_array("incidents", $clientFeatures) ? "hidden" : null).'">
                        <a class="nav-link '.($url_link === "incidents" ? "active" : null).'" onclick="return appendToUrl(\'incidents\')" id="incident-tab2" data-toggle="tab" href="#incident" role="tab" aria-selected="true">Incidents</a>
                    </li>
                    <li class="nav-item '.(!in_array("timetable", $clientFeatures) ? "hidden" : null).'">
                        <a class="nav-link '.($url_link === "timetable" ? "active" : null).'" onclick="return appendToUrl(\'timetable\')" id="timetable-tab2" data-toggle="tab" href="#timetable" role="tab" aria-selected="true">Timetable</a>
                    </li>';

                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(empty($url_link) || $url_link === "about" ? "show active" : null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                            '.($data->description ? "
                                <div class='mb-3'>
                                    <div class='card-body p-2 pl-0'>
                                        <div><h5>DESCRIPTION</h5></div>
                                        {$data->description}
                                    </div>
                                </div>
                            " : "").'
                            <div class="mb-2">
                                <div class="card-body p-2 pl-0">
                                    <div><h5>PREVIOUS SCHOOL DETAILS</h5></div>
                                    <table width="100%" class="table-bordered">
                                        <tr>
                                            <td class="p-2" width="20%">School Name</td>
                                            <td class="p-2">'.$data->previous_school.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Previous Class</td>
                                            <td class="p-2">'.$data->previous_school_qualification.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Remarks</td>
                                            <td class="p-2">'.$data->previous_school_remarks.'</td>
                                        </tr>
                                    </table>
                                    <div class="mt-4"><h5>Allergies</h5></div>
                                    <table width="100%" class="table-bordered">
                                        <tr>
                                            <td class="p-2">'.(!empty($data->alergy) ? $data->alergy : "NIL").'</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            '.$guardian.'
                        </div>
                        '.($viewAllocation ? 
                        '<div class="tab-pane '.($url_link === "fees" ? "show active" : null).' fade" id="fees" role="tabpanel" aria-labelledby="fees-tab2">
                            <div class="row mb-3 pb-2 border-bottom">
                                <div class="col-md-12 text-right">
                                '.(
                                    ((!empty($student_allocation_list["owning"]) && $canReceive) || (!empty($student_allocation_list["owning"]) && $isParent)) ? 
                                        '<span class="text-right '.($data->scholarship_status ? 'hidden' : null).'" id="make_payment_button">
                                            <a '.($isParent ? "target='_blank' href='{$myClass->baseUrl}pay/{$defaultUser->client_id}/fees/{$user_id}'" : 'href="'.$myClass->baseUrl.'fees-payment?student_id='.$user_id.'&class_id='.$data->class_id.$payment_module_url.'"').' class="btn mb-2 btn-outline-success"><i class="fa fa-adjust"></i> MAKE PAYMENT</a>
                                        </span>
                                        '.($canAllocate && in_array($data->user_status, ["Active"]) && ($data->payment_module !== "Monthly") ? '
                                        <button id="modify_bill_button" title="Click to Modify Fees Allocated to Student" onclick="return load(\'fees-allocate/'.$user_id.'\')" class="btn text-uppercase mb-2 btn-outline-primary '.($data->scholarship_status ? 'hidden' : null).'">
                                            <i class="fa fa-edit"></i> Modify Bill
                                        </button>' : null).'
                                        <span>
                                        <button id="student_on_scholarship" class="btn text-uppercase mb-2 btn-info '.(!$data->scholarship_status ? 'hidden' : null).'">
                                            <i class="fa fa-ankh"></i> On Scholarship
                                        </button>
                                        '
                                    : ($student_allocation_list["allocated"] && empty($student_allocation_list["owning"]) ? 
                                        '<div class="btn mb-2 btn-success text-uppercase">Fees Fully Paid</div>' :
                                        (
                                            $canAllocate ? '
                                            <span>
                                                <button title="Click to Allocate Fees to Student" onclick="return load(\'fees-allocate/'.$user_id.'\')" class="btn text-uppercase mb-2 btn-primary">
                                                    <i class="fa fa-ankh"></i> Set Student Bill
                                                </button>
                                            </span>' : null
                                        )
                                    )
                                ).'
                                '.(
                                    $student_allocation_list["allocated"] ? '
                                        <span><a href="'.$baseUrl.'download/student_bill/'.$user_id.'?print=1" target="_blank" class="btn mb-2 btn-outline-warning text-uppercase"><i class="fa fa-print"></i> Print Bill</a></span>
                                        <span><a href="'.$baseUrl.'download/student_bill/'.$user_id.'?download=1" target="_blank" class="btn mb-2 btn-outline-danger hidden text-uppercase"><i class="fa fa-download"></i> Download</a></span>
                                    ' : null
                                ).'
                                '.(
                                    !empty($student_allocation_list["owning"]) && $canReceive && $accessObject->hasAccess("send", "communication") ? 
                                        '<span class="text-right">
                                        <button onclick="return modal_popup(\'send_Fees_Reminder\');" class="btn mb-2 btn-info"><i class="fa fa-envelope"></i> SEND REMINDER</a>
                                    </span>' : null
                                ).'
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table data-empty="" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th>Category</th>
                                            <th>Due</th>
                                            <th>Paid</th>
                                            <th>Balance</th>
                                            '.($receivePayment ? '<th width="20%" align="center"></th>' : '').'
                                        </tr>
                                    </thead>
                                    <tbody>'.$student_allocation_list["list"].'</tbody>
                                </table>
                            </div>
                            '.(!empty($student_fees_arrears) ? "<div class=\"col-md-4 mt-5\"><h5>FEES ARREARS</h5></div> {$student_fees_arrears}" : null).'
                        </div>
                        <div class="tab-pane '.($url_link === "payments" ? "show active" : null).' fade" id="fees_payments" role="tabpanel" aria-labelledby="fees_payments-tab2">
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label>Filter by Category</label>
                                    <select data-width="100%" id="category_id" class="selectpicker form-control">
                                        <option value="">Select Category</option>
                                        '.$fees_category_list.'
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Start Date</label>                                
                                    <input value="'.$start_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" id="group_start_date">
                                </div>
                                <div class="col-lg-3">
                                    <label>End Date</label>
                                    <input value="'.$end_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" id="group_end_date">
                                </div>
                                <div class="col-lg-2">
                                    <label>&nbsp;<br></label>
                                    <button type="button" onclick="return generate_payment_report(\''.$user_id.'\');" class="btn btn-block btn-primary">Generate</button>
                                </div>

                                <div class="border-top pt-3 col-lg-12 mt-3">
                                    <div class="table-responsive">
                                        <table width="100%" class="table table-striped table-sm table-bordered raw_datatable">
                                            <thead>
                                                <tr>
                                                    <th data-width="40" style="width: 40px;">#</th>
                                                    <th>Item</th>
                                                    <th>Method</th>
                                                    <th>Description</th>
                                                    <th>Record Date</th>
                                                    <th align="right">Amount</th>
                                                    <th align="center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>'.$student_fees_payments.'</tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>':'').'
                        <div class="tab-pane '.($url_link === "attendance" ? "show active" : null).' fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab2">
                            <div id="single_user_data" class="row default_period" data-current_period="this_month">
                                <div id="data-report_stream" class="width-100" data-report_stream="attendance_report&label[student_id]='.$user_id.'">
                                    <div class="row p-2">
                                        <div class="col-lg-6 col-md-5">
                                            <a target="_blank" data-href_path="attendance_summary" class="btn btn-outline-success" href="'.$baseUrl.'download/attendance?user_id='.$user_id.'&class_id='.$data->class_id.'&start_date='.$start_date.'&end_date='.$end_date.'&user_type='.$data->user_type.'&att_d=true">
                                                <i class="fa fa-download"></i> Download Attendance Report
                                            </a>
                                        </div>
                                        <div class="col-lg-6 col-md-7 text-right">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-calendar-check"></i></span>
                                                </div>
                                                <input data-maxdate="'.$myClass->data_maxdate.'" value="'.$start_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" data-item="attendance" id="group_start_date">
                                                <input data-maxdate="'.$myClass->data_maxdate.'" value="'.$end_date.'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" data-item="attendance" id="group_end_date">
                                                <div class="input-group-append">
                                                    <button style="border-radius:0px" onclick="return filter_Single_UserGroup_Attendance(\'&label[student_id]='.$user_id.'\',\'user_id='.$user_id.'&class_id='.$data->class_id.'&user_type='.$data->user_type.'\')" class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
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
                        <div class="tab-pane '.($url_link === "documents" ? "show active" : null).' fade" id="documents" role="tabpanel" aria-labelledby="documents-tab2">
                            <div class="mb-2 w-100">
                                <div class="d-flex justify-content-between">
                                    <div><h5>STUDENT DOCUMENTS</h5></div>
                                    '.($isAdminAccountant ? 
                                        "<div><button onclick='return show_eDocuments_Modal();' class='btn btn-outline-primary btn-sm'><i class='fa fa-upload'></i> Upload</button></div>" : null
                                    ).'
                                </div>
                            </div>
                            <div data-ebook_resource_list="'.$user_id.'">
                                '.($attachment_html ? $attachment_html : no_record_found("No Documents Uploaded", "No documents uploaded yet.", null, "Documents", false, "fa fa-file-alt")).'
                            </div>
                        </div>
                        <div class="tab-pane fade '.($url_link === "timetable" ? "show active" : null).'" id="timetable" role="tabpanel" aria-labelledby="timetable-tab2">
                            <div class="table-responsive trix-slim-scroll">
                                '.$timetable.'
                            </div>
                        </div>
                        <div class="tab-pane fade '.($url_link === "incidents" ? "show active" : null).'" id="incident" role="tabpanel" aria-labelledby="incident-tab2">
                            <div class="d-flex justify-content-between mb-3 w-100">
                                <div class="mb-2"><h5></h5></div>
                                '.($addIncident ? '
                                    <div>
                                        <button type="button" onclick="return load_quick_form(\'incident_log_form\',\''.$user_id.'\',\''.$data->user_type.'\');" class="btn btn-primary"><i class="fa fa-plus"></i> Log Incident</button>
                                    </div>' 
                                : null ).'
                            </div>
                            '.$incidents_list.'
                        </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </section>';

        // if the user has the permission to receive fees payment
        if($hasUpdate) {

            // append the reminder form
            $response->html .= change_status_modal($data->user_row_id, $data->user_status);
        }

        // if the user has the permission to receive fees payment
        if($canReceive && $accessObject->hasAccess("send", "communication")) {

            // fees helper
            load_helpers(["fees_helper"]);

            // append the reminder form
            $response->html .= fees_payment_reminder_form($data->name, $user_id);
        }

        // if the user has permission to upload files
        if($isAdminAccountant) {

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
                                '.$formObject->form_attachment_placeholder($file_params).'
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