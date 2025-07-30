<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultClientData, $defaultUser, $isWardTutorParent, $isEmployee;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// init values
$timetable = "<div class='text-center'>No timetable record for today was found in the database.</div>";
$wards_list = "";
$assigments_list = "";
$upcoming_events_list = "";
$upcoming_birthday_list = "";

// if no referer was parsed OR the request_method is not POST
jump_to_main($baseUrl);

$clientId = $session->clientId;
$loggedUserId = $session->userId;
$cur_user_id = (confirm_url_id(1)) ? xss_clean($SITEURL[1]) : $loggedUserId;

// filters
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Dashboard ";

// the default data to stream
$data_stream = 'id="data-report_stream" data-report_stream="attendance_report"';

// set the parameters
$userData = $defaultUser;
$userData->do_not_encode = true;
$userData->client_id = $clientId;
$userData->mini_description = true;
$userData->the_user_type = $defaultUser->user_type;

// set the date to load
$global_period = $isWardParent ? "this_term" : "this_week";

// global params
$global_params = (object) ["client_data" => $defaultUser->client];

// get the academic session
$academicSession = $defaultClientData->client_preferences->sessions->session ?? "Academic Term";

// get the request headers
$reqHeaders = $_GET + $_POST;

// check if the preview mode is set
if(isset($reqHeaders["preview_mode"]) && !empty($reqHeaders["client_id"])) {
    // set the session
    $session->previewMode = true;
    $session->previewClientId = $reqHeaders["client_id"];
    $session->previewSupportClientId = $session->clientId;

    // redirect to the schools page
    $response->redirect = "dashboard";
}

// check if the preview mode is set
if(isset($reqHeaders["preview_exit"])) {
    // set the session
    $session->clientId = $session->previewSupportClientId;
    $session->previewMode = false;
    $session->previewClientId = false;
    $session->previewSupportClientId = false;

    // redirect to the schools page
    $response->redirect = "dashboard";
}

// confirm if the account has been suspended or expired
if(in_array($defaultClientData->client_state, ["Suspended", "Expired"])) {
    // set the content of the message
    $client_state = $defaultClientData->client_state;
    $timer = $client_state == "Expired" ? $clientPrefs->account->expiry : null;

    // message to share
    $response->html = access_denied($client_state, $timer);

} else {

    // set the data to stream for an admin user
    if($isAdminAccountant) {
        
        // create a new events object
        $eventClass = load_class("events", "controllers");

        if($isAdmin) {
            // set the stream
            $data_stream = 'id="data-report_stream" data-report_stream="attendance_report,summary_report,transaction_revenue_flow"';
        } else {
            $data_stream = 'id="data-report_stream" data-report_stream="summary_report,transaction_revenue_flow"';
        }
        // load the events list
        $events_list = $eventClass->events_list($userData);

        // list of summary content
        $summary_list = [
            [
                "label" => "This week", "title" => "Overall Income", "favicon" => "fa-money-bill", 
                "border" => "success", "sum_tag" => "total_income_received", "left-border" => "border-green", 
                "background" => "bg-green-50"
            ],
            [
                "label" => "This week", "title" => "Expenditure", "favicon" => "fa-money-bill-alt", 
                "border" => "danger", "sum_tag" => "total_expenditure", "left-border" => "border-danger",
                "background" => "bg-red-50"
            ],
            [
                "label" => "This week", "title" => "Bank Deposits", "favicon" => "fa-desktop", 
                "border" => "info", "sum_tag" => "Bank_Deposit", "left-border" => "border-blue",
                "background" => "bg-blue-50"
            ],
            [
                "label" => "This week", "title" => "Bank Withdrawals", "favicon" => "fa-wind", 
                "border" => "warning", "sum_tag" => "Bank_Withdrawal", "left-border" => "border-orange", 
                "background" => "bg-orange-50"
            ],
            [
                "label" => "Overall", "title" => "Account Balance", "favicon" => "fa-balance-scale", 
                "border" => "primary", "sum_tag" => "account_balance", "left-border" => "border-purple",
                "background" => "bg-purple-50"
            ]
        ];

        // include the scripts to load
        $response->scripts = ["assets/js/analitics.js", "assets/js/clock.js"];

        // create an init array to use
        $event_array = ["holidays_list", "calendar_events_list"];

        // merge the two array sets
        $events_array_list = array_merge($events_list->holidays_list, $events_list->calendar_events_list);

        // sort the array
        function sort_date($a, $b) {
            return strtotime($a["start"]) - strtotime($b["start"]);
        }

        // order the array set using the date of the event
        usort($events_array_list, "sort_date");
        $reversed_array_list = array_reverse($events_array_list);

        // loop through the array list
        foreach($reversed_array_list as $event) {

            $isHoliday = (bool) ($event["event_group"] === "holidays_list");

            $textStartBg = $isHoliday ? "bg-green-50" : "bg-{$event['backgroundColor']}-50";
            $textFullBg = $isHoliday ? "border-green-500" : "border-{$event['backgroundColor']}-500";
            $textColor = $isHoliday ? "text-green-500" : "text-{$event['backgroundColor']}-500";

            // append to the events list
            $upcoming_events_list .= "
                <div class='flex items-center space-x-3 p-3 {$textStartBg} mb-2 border rounded-xl border-l-4 {$textFullBg}'>
                    <i class='fas fa-check-circle {$textColor}'></i>
                    <div class='flex items-center justify-between w-100'>
                        <div class='flex-1'>
                            <p class='text-sm font-medium text-gray-900'>{$event["title"]}</p>
                            <p class='text-xs text-gray-600'>".date("jS M Y", strtotime($event["start"]))."</p>
                        </div>
                        <div>
                            <span onclick='return view_Event_Details(\"{$event["event_group"]}\", \"{$event["item_id"]}\")' class='badge cursor badge-primary'>Detail</span>
                        </div>
                    </div>
                </div>
                <li class='media hidden'>
                    <div class='media-body' style='flex: 2;'>
                        <div class='media-title'>
                            {$event["title"]} ".($event["event_group"] === "holidays_list" ? "<span class='badge p-1 badge-success'>Holiday</span>" : "")."
                        </div>
                        <div class='text-job text-muted'>{$event["event_type"]}</div>
                    </div>
                    <div class='media-progressbar'>
                        <div class='progress-text'>".date("jS M Y", strtotime($event["start"]))."</div>
                    </div>
                    <div>
                        <span onclick='return view_Event_Details(\"{$event["event_group"]}\", \"{$event["item_id"]}\")' class='badge cursor badge-primary'>Detail</span>
                    </div>
                </li>";
        }

        // if the birthday array is not empty
        if(!empty($events_list->birthday_list) && $isAdminAccountant) {

            // loop through the array list
            foreach($events_list->birthday_list as $event) {
                
                // format the date of birth
                $clean_date = date("Y").'-'.$event["description"]->the_month.'-'.$event["description"]->the_day;

                // format the upcoming birthday list
                $upcoming_birthday_list .= "
                    <li class='media'>
                        <img title='Click to view student details' class='rounded-2xl cursor author-box-picture' width='40px' src=\"{$baseUrl}{$event["description"]->image}\">
                        <div class='media-body ml-2' style='flex: 2;'>
                            <div class='media-title'>
                                <span class='user_name' onclick='return load(\"{$event["link"]}/{$event["description"]->item_id}\");'>
                                ".ucwords($event["description"]->name)."</span><br>
                                ".(
                                    !empty($event["description"]->class_name) ? 
                                        "<small>".ucwords($event["description"]->class_name)."</small><br>" : null
                                )."
                                <span class='badge badge-{$myClass->user_colors[$event["description"]->user_type]} p-1'>".ucwords($event["description"]->user_type)."</span>
                            </div>                    
                        </div>
                        <div class='media-progressbar'>
                            <div class='progress-text md-right text-uppercase'>".date("D, jS M", strtotime($clean_date))."</div>
                        </div>
                    </li>";
            }

        }

        // append the events list as part of the results
        $response->array_stream["events_array_list"] = $events_list;

    }

    // if the user logged in is a tutor or an student
    elseif($isTutorStudent) {
        // unset the session
        $session->remove("assignment_uploadID");

        // the query parameter to load the user information
        $assignments_param = (object) ["minified" => true, "clientId" => $clientId, "userData" => $defaultUser];
        $assignments_array_list = load_class("assignments", "controllers")->list($assignments_param);

        // can update assignments
        $can_Update_Assign = $accessObject->hasAccess("update", "assignments");

        // unset the sessions if $session->currentQuestionId is not empty
        foreach($assignments_array_list["data"] as $key => $each) {
            
            $action = "<a  href='#' onclick='return load(\"assessment/{$each->item_id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

            $assigments_list .= "<tr data-row_id=\"{$each->id}\">";
            $assigments_list .= "<td>".($key+1)."</td>";
            $assigments_list .= "<td width='30%'><a href='#' onclick='return load(\"assessment/{$each->item_id}/view\");'>{$each->assignment_title}</a> ".(
                $can_Update_Assign ? 
                    "<br>Class: <strong>{$each->class_name}</strong>
                    <br>Course: <strong>{$each->course_name}</strong>" : 
                    "<br>Course:</strong> {$each->course_name}</strong>"
                )." ".($can_Update_Assign ? $myClass->the_status_label($each->state) : $each->handedin_label)."</td>";

            
            $assigments_list .= "<td>{$each->due_date} @ {$each->due_time}</td>";

            // show this section if the user has the necessary permissions
            if($can_Update_Assign) {
                $percentage = $each->students_assigned > 0 ? ($each->students_graded / $each->students_assigned) * 100 : 0;
                $assigments_list .= "<td>
                <div>
                    <div class='flex mb-2 justify-between space-x-2'><span>Assigned</span> <span>{$each->students_assigned}</span></div>
                    <div class='w-full mb-2 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700'>
                        <div class='bg-blue-600 h-2.5 rounded-full' style='width: {$percentage}%'></div>
                    </div>
                    <div class='flex justify-between space-x-2'><span class='text-success'>Graded</span> <span>{$each->students_graded}</span></div>
                </div>
                </td>";
                // $assigments_list .= "<td>{$each->students_handed_in}</td>";
                // $assigments_list .= "<td>{$each->students_graded}</td>";
            }
            
            if(!$can_Update_Assign) {
                $assigments_list .= "<td>{$each->awarded_mark}</td>";
            }

            $assigments_list .= "<td align='center'>{$action}</td>";
            $assigments_list .= "</tr>";
        }
        
    }

    // set the data
    $data = $defaultUser;
    $admission_enquiry = null;

    // set a new parameter for the items
    $files_param = (object) [
        "resource" => "settings_calendar",
        "item_id" => $defaultUser->client->client_id
    ];

    // create a new object
    $academi_calendar = load_class("files", "controllers")->list_attachments($files_param);

    // set the academic calendar
    $response->array_stream['academic_calendar'] = $academi_calendar;

    // if ward/parent/tutor
    if($isWardTutorParent) {

        // load the use information
        $expenses_list = null;
        $timetableClass = load_class("timetable", "controllers", $global_params);

        // load the wards list
        if($isParent) {
            
            // wards count
            $wards_count = 0;
            $total_expenditure = 0;
            
            // stream nothing if the student id has not been set yet
            if(!empty($session->student_id)) {
                // load the class timetable for student / parent & The lessons if a teacher is logged in
                $timetable = $timetableClass->class_timetable($session->student_class_id, $clientId, "today", null, "yes");    
            } else {
                // set parameters
                $data_stream = "";
            }


            // if the wards array is not empty
            if(empty($data->wards_list)) {
                $wards_list = "<div class='font-italic'>Sorry! You currently do not have any ward in the school.</div>";
            } else {

                // loop through the wards list
                foreach($data->wards_list as $ward) {

                    // convert to object
                    $ward = (object) $ward;
                    $wards_count++;

                    // set the selected session
                    $isCurrent = (bool) ($session->student_id == $ward->student_guid);

                    $wards_list .= "
                    <div class='mb-3 border-bottom'>
                        <div class='row'>
                            <div class='col-lg-2 text-center'>
                                <img title='Click to view full details of {$ward->name}' onclick='load(\"student/{$ward->student_guid}\");' src='{$baseUrl}{$ward->image}' width='80px' class='author-box-picture cursor'>
                            </div>
                            <div class='col-lg-10'>
                                <table width='100%'>
                                    <tr>
                                        <td class='font-bold p-1' align='right'>Name</td>
                                        <td class='pr-2' align='right'>{$ward->name}</td>
                                    </tr>
                                    <tr>
                                        <td class='font-bold p-1' align='right'>Gender</td>
                                        <td class='pr-2' align='right'>{$ward->gender}</td>
                                    </tr>
                                    <tr>
                                        <td class='font-bold p-1' align='right'>Class</td>
                                        <td class='pr-2' align='right'>{$ward->class_name}</td>
                                    </tr>
                                    <tr>
                                        <td class='font-bold p-1' align='right'>Admission Id</td>
                                        <td class='pr-2' align='right'>{$ward->unique_id}</td>
                                    </tr>
                                    <tr>
                                        <td class='font-bold p-1' align='right'>Admission Date</td>
                                        <td class='pr-2' align='right'>{$ward->enrollment_date}</td>
                                    </tr>
                                    <tr>
                                        <td colspan='2' align='right'>
                                            <button onclick='return load(\"student/{$ward->student_guid}\")' class='btn btn-sm btn-outline-success mb-2'><i class='fa fa-eye'></i> View Record</button>
                                            ".($isCurrent ? "<span class='badge mb-2 badge-success'>SELECTED</span>" : "<button onclick='return set_default_Student(\"{$ward->student_guid}\")' class='btn btn-sm btn-outline-primary mb-2'>Select Student</button>")."
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>";
                }
                
                // begin the request parameter
                $fees_params = (object) [
                    "clientId" => $clientId,
                    "userData" => $defaultUser,
                    "academic_year" => $defaultAcademics->academic_year ?? null,
                    "academic_term" => $defaultAcademics->academic_term ?? null,
                    "client_data" => $defaultUser->client,
                    "student_array_ids" => $defaultUser->wards_list_ids,
                    "group_by" => "GROUP BY a.payment_id"
                ];

                // if the student id is not empty
                if(!empty($session->student_id)) {
                    $fees_params->student_id = $session->student_id;
                }
                
                // load the student fees payment
                $item_list = load_class("fees", "controllers", $fees_params)->list($fees_params)["data"];
                
                // initials
                $fees_history = "";

                // loop through the list
                foreach($item_list as $key => $each) {

                    // add up to the expenses
                    $total_expenditure += $each->amount;

                    // list the items
                    $action = "";
                    $action .= "&nbsp;<a title='Click to print this receipt' href='#' onclick=\"return print_receipt('{$each->payment_id}')\" class='btn btn-sm btn-outline-warning'><i class='fa fa-print'></i></a>";
                    $fees_history .= "<tr data-row_id=\"{$each->payment_id}\">";
                    $fees_history .= "<td>".($key+1)."</td>";
                    $fees_history .= "
                        <td>
                            <div class='d-flex justify-content-start'>
                                ".(!empty($each->student_info->image) ? "
                                <div class='mr-2'><img src='{$baseUrl}{$each->student_info->image}' width='40px' height='40px'></div>" : "")."
                                <div>
                                    <a  href='#' onclick='return load(\"student/{$each->student_info->user_id}\");'>{$each->student_info->name}</a> <br>
                                    
                                    {$each->class_name}
                                </div>
                            </div>
                        </td>";
                    $fees_history .= "<td>{$each->category_name}</td>";
                    $fees_history .= "<td>".number_format($each->amount_paid, 2)."</td>";
                    $fees_history .= "<td>{$each->recorded_date}</td>";
                    $fees_history .= "<td width='10%' align='center'>{$action}</td>";
                    $fees_history .= "</tr>";
                }

                // assign the assignments list
                $expenses_list = '
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Fees Payments History</h4>
                        </div>
                        <div class="card-body trix-slim-scroll">
                            <div class="table-responsive">
                                <table data-rows_count="8" class="table table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th>Student Name</th>
                                            <th width="10%">Category</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th align="center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>'.$fees_history.'</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } else {

            // load the class timetable for student / parent & The lessons if a teacher is logged in
            if($isStudent) {
                $timetable = $timetableClass->class_timetable($defaultUser->class_guid, $clientId, "today", 90, "yes");    
            } else {
                $timetable = $timetableClass->teacher_timetable($defaultUser->user_id, $clientId, "today");
            }

            // load the calendar
            $load_calendar = '';
            foreach($academi_calendar as $each) {

                if($each->is_deleted) continue;
                // get the download link
                $file_to_download = base64_encode($each->path."{$myClass->underscores}{$each->record_id}");

                // get the download path
                $download_path = "{$myClass->baseUrl}download?file={$file_to_download}&preview=1";
                $view_path = "{$myClass->baseUrl}calendar?file={$file_to_download}";

                // load the calendar
                $load_calendar .= "<div class='col-lg-4 col-md-6 mb-4'>";
                $load_calendar .= "<div class='border border-blue border-2px p-2 rounded'>";
                $load_calendar .= "<div class='font-bold uppercase'>{$each->name}</div>";
                $load_calendar .= "<div class='d-flex justify-content-between'>
                    <div>
                        <a class='btn btn-sm btn-outline-primary' title='Click to view {$each->name}' href='{$view_path}'>
                            <i class='fa fa-eye'></i> View Calendar
                        </a>
                    </div>
                    <div>
                        <a class='btn btn-sm btn-outline-danger' target='_blank' title='Click to download {$each->name}' href='{$download_path}'>
                            <i class='fa fa-download'></i> Download
                        </a>
                    </div>
                </div>";
                $load_calendar .= "<div class='relative'>".date("jS F, Y", strtotime($each->datetime))."</div>";
                $load_calendar .= "</div>";
                $load_calendar .= "</div>";
            }

            // assign the assignments list
            $assignment_list = !empty($load_calendar) ? '
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header text-uppercase">
                        <h4>Academic Calendar</h4>
                    </div>
                    <div class="card-body pb-0 trix-slim-scroll">
                        <div class="row">
                            '.$load_calendar.'
                        </div>
                    </div>
                </div>
            </div>' : null;

            $assignment_list .= '
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header text-uppercase">
                        <h4>Class Assessments</h4>
                    </div>
                    <div class="card-body trix-slim-scroll">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped small_datatable" data-rows_count="4">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Title</th>
                                        <th>Due Date</th>
                                        '.($can_Update_Assign ? '
                                            <th width="20%">Participation</th>' : '<th>Awarded Mark</th>'
                                        ).'
                                        <th align="center"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$assigments_list.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>';
        }

        // include the scripts to load
        $response->scripts = ["assets/js/analitics.js"];

    }

    // if the user is an employee
    elseif($isEmployee) {
        // load the leave applications
        $param = (object) [
            "userData" => $defaultUser,
            "clientId" => $clientId,
            "section" => "admission_enquiry",
            "user_id" => $defaultUser->user_id
        ];

        // get the reports list
        $frontObj = load_class("frontoffice", "controllers");
        $results_array = $frontObj->list($param)["data"];

        // results list
        $results_list = "";

        // loop through the results array list
        foreach($results_array as $key => $each) {
    
            $action = "<button title='View Record Details' onclick='return load(\"office_enquiry/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></button>";
            
            $results_list .= "<tr data-row_id=\"{$each->id}\">";
            $results_list .= "<td>".($key+1)."</td>";
            $results_list .= "<td><span class='user_name' onclick='return load(\"office_enquiry/{$each->item_id}\");'>{$each->content->fullname}</span></td>";
            $results_list .= "<td>{$each->content->phone_number}</td>";
            $results_list .= "<td>{$each->source}</td>";
            $results_list .= "<td>{$each->content->date}</td>";
            $results_list .= "<td>".$myClass->the_status_label($each->state)."</td>";
            $results_list .= "<td align='center'>{$action}</td>";
            $results_list .= "</tr>";
        }

        // list the admission enquiry
        $admission_enquiry = '
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                
                <div class="card">
                    <div class="card-header text-uppercase">
                        <h4>Admission Enquiry</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Source</th>
                                        <th>Enquiry Date</th>
                                        <th>Status</th>
                                        <th width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$results_list.'</tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </div>';
    }

    // set the html content
    $response->html = $myClass->async_notification().'
    <section class="section">
        <div class="default_period" data-current_period="'.$global_period.'">
        <div class="d-flex mt-3 justify-content-between" '.$data_stream.'></div>
        <h4 class="border-bottom border-primary mb-3">Hello '.(!empty($defaultUser->name) ? trim($defaultUser->name) : "User").', </h4>';
        
        // load this section for admins and accountants
        if($isAdminAccountant) {

            // if an admin is logged in
            if($isAdmin) {

                // if the term has ended
                $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);

                $response->html .=
                '<div class="row">
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card rounded-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="card-body bg-gradient-to-br from-blue-200 to-blue-100 rounded-2xl shadow-lg text-black card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-bold mb-0">Students Count</h6>
                                        <span  data-count="total_students_count" class="font-25 font-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card rounded-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="card-body bg-gradient-to-br from-green-200 to-green-100 rounded-2xl shadow-lg text-black card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-bold mb-0">Teaching Stafff</h6>
                                        <span data-count="total_teachers_count" class="font-25 font-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle bg-gradient-to-br from-green-500 to-green-600 text-white">
                                            <i class="fas fa-user-secret"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card rounded-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="card-body bg-gradient-to-br from-purple-200 to-purple-100 rounded-2xl shadow-lg text-black card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-bold mb-0">Employees / Users</h6>
                                        <span data-count="total_employees_count" class="font-25 font-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card rounded-2xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="card-body inline-flex justify-center bg-gradient-to-br from-yellow-200 to-orange-100 rounded-2xl shadow-lg text-black card-type-3">
                                <div align="center">
                                    <h6 class="border-bottom font-13 text-uppercase font-bold p-0 m-0">'.date("l, F d, Y").'</h6>
                                    <h3 class="p-0 m-0"><div class="plugin-clock text-black">'.date("h:i A").'</div></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }

            $response->html .= '<div class="row">';
            
            // if the user is an admin
            if($isAdmin) {
                $response->html .= '
                <div class="col-lg-12">
                    <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="card rounded-2xl">
                            <div class="card-body school-details" style="min-height:235px">
                                <div class="row">
                                    '.(
                                        !empty($defaultClientData->client_logo) ?
                                        '<div align="center" class="col-sm-3">
                                            <img width="100%" class="rounded-xl school-logo" src="'.$baseUrl.''.$defaultClientData->client_logo.'">
                                        </div>' : null
                                    ).'
                                    <div align="center" class="p-1 col-sm-'.(!empty($defaultClientData->client_logo) ? 9 : 12).'">
                                        <div style="align-items:center;">
                                            <h3 class="font-30">'.$defaultClientData->client_name.'</h3>
                                            <div class="'.(!empty($defaultClientData->client_slogan) ? "mb-1" : null).' font-15">'.$defaultClientData->client_slogan.'</div>
                                            <div class="font-15 mt-1">'.$defaultClientData->client_email.'</div>
                                            <div class="mt-1 font-17">'.$defaultClientData->client_location.'</div>
                                            <div class="mt-1 font-17">
                                                '.trim($defaultClientData->client_contact).'
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="relative card overflow-hidden rounded-2xl  bg-gradient-to-br from-yellow-200 to-red-50">
                                    <div class="card-body mb-2 pl-2 pr-2 pb-2" align="center">
                                        <p class="font-16 p-0 m-0 text-primary text-uppercase">Academic Year</p>
                                        <h6 class="mt-1 pt-0">'.$defaultAcademics->academic_year.'</h6>
                                        <span class="font-16 font-bold text-black">
                                            '.date("jS M, Y", strtotime($defaultAcademics->year_starts)).' 
                                                &nbsp; <i class="fa fa-arrow-alt-circle-right"></i> &nbsp;
                                            '.date("jS M, Y", strtotime($defaultAcademics->year_ends)).'
                                        </span>
                                        <hr class="my-3">
                                        <p class="font-16 p-0 mt-2 text-uppercase text-primary">Academic '.($academicSession ?? null).'</p>
                                        <h6 class="pt-0 text-uppercase text-primary">'.($defaultAcademics->current_term_name ?? $defaultAcademics->academic_term).'</h6>
                                        <span class="font-16 font-bold text-black">
                                            '.date("jS M, Y", strtotime($defaultAcademics->term_starts)).' 
                                                &nbsp; <i class="fa fa-arrow-alt-circle-right"></i> &nbsp;
                                            '.date("jS M, Y", strtotime($defaultAcademics->term_ends)). '
                                        </span>
                                    </div>
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-400 to-purple-400 opacity-20 rounded-full -translate-y-16 translate-x-16 animate-pulse"></div>
                                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-br from-blue-400 to-cyan-400 opacity-20 rounded-full translate-y-12 -translate-x-12 animate-pulse" style="animation-delay: -2s;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>';
            }

            $financialSummary = '';
            if($isAdminAccountant && !empty($summary_list)) {
                // loop through the summary array listing
                foreach($summary_list as $item) {
                    $financialSummary .= '
                    <div class="">
                        <div class="card border-top-0 hover:scale-105 transition-all duration-300 '.($item["background"] ?? null).' border-bottom-0 border-right-0 border-left-lg border-left-solid '.$item["left-border"].'">
                            <div class="card-header border-'.($item["border"] ?? null).' p-2">
                                <i class="fa text-'.($item["border"] ?? null).' '.$item["favicon"].'"></i> 
                                &nbsp; '.$item["title"].'
                            </div>
                            <div class="card-body pl-2 p-0 font-25"><span data-count="'.$item["sum_tag"].'">0.00</span></div>
                            <div class="card-footer pl-2 pt-0 p-0">
                                <em><span class="text-primary font-14" '.($item["label"] !== "Overall" ? 'data-filter="current_period"' : null).'>
                                    '.$item["label"].'
                                </span></em>
                            </div>
                        </div>
                    </div>';
                }
            }

            // if an account is logged in
            if($isAccountant) {
                $response->html .= '<div class="col-lg-12"><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">';
                $response->html .= $financialSummary;
                $response->html .= '</div></div>';
            }

            $response->html .= '
                <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                    <div class="card rounded-2xl">
                        <div class="card-header pr-2">
                            <div class="row width-per-100">
                                <div class="col-md-9">
                                    <h4 class="text-uppercase font-13">Students Per Class Count</h4>
                                </div>
                                <div class="col-md-3 text-success text-right p-0">
                                    Total: <span data-count="total_students_count" class="font-bold font-25 mb-0">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body trix-slim-scroll quick_loader" id="class_count_list" style="max-height:465px;height:465px;overflow-y:auto;">
                            <div class="form-content-loader" style="display: flex; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="card rounded-2xl">
                        <div class="card-header pr-0">
                            <div class="row width-100">
                                <div align="right" class="col-md-12">
                                    <div class="btn-group" data-filter="quick_summary_filter" id="quick_summary_filter" role="group" aria-label="Filter Revenue">
                                        <button type="button" data-stream="summary_report,transaction_revenue_flow" data-period="yesterday" class="btn sm-hide btn-info">Yesterday</button>
                                        <button type="button" data-stream="summary_report,transaction_revenue_flow" data-period="today" class="btn btn-info">Today</button>
                                        <button type="button" data-stream="summary_report,transaction_revenue_flow" data-period="this_week" class="btn active btn-info">This Week</button>
                                        <button type="button" data-stream="summary_report,transaction_revenue_flow" data-period="last_week" class="btn sm-hide btn-info">Last Week</button>
                                        <button type="button" data-stream="summary_report,transaction_revenue_flow" data-period="this_month" class="btn btn-info">This Month</button>
                                        <button type="button" data-stream="summary_report,transaction_revenue_flow" data-period="last_3months" class="btn btn-info">This Quarter</button>
                                        <button type="button" data-stream="summary_report,transaction_revenue_flow" data-period="this_term" class="btn btn-info">'.$academicSession.'</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body quick_loader dashboard_revenue" style="min-height:465px;">
                            <div class="table-responsive">
                                <div class="form-content-loader" style="display: flex; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2">
                                    <div class="mb-1 amount text-center">
                                        <h4 class="text-primary">
                                            <span data-summary="amount_due">0.00</span>
                                        </h4>
                                        <label>Fees Due</label>
                                    </div>
                                    <div class="mb-1 amount text-center">
                                        <h4 class="text-success">
                                            <span data-summary="amount_paid">0.00</span>
                                        </h4>
                                        <label>Fees Paid</label>
                                    </div>
                                    <div class="mb-1 amount text-center">
                                        <h4 class="text-danger">
                                            <span data-count="total_balance">0.00</span>
                                        </h4>
                                        <label>Fees Balance</label>
                                    </div>
                                    <div class="mb-1 amount text-center">
                                        <h4 class="text-success">
                                            <span '.($isAdmin ? 'data-count="total_expenditure"' : 'data-summary="arrears_paid"').'>0.00</span>
                                        </h4>
                                        <label>'.($isAdmin ? 'Total Expenses' : 'Arrears Paid').'</label>
                                    </div>
                                    <div class="mb-1 amount text-center">
                                        <h4 class="text-warning">
                                            <span data-count="arrears_total">0.00</span>
                                        </h4>
                                        <label>Arrears Balance</label>
                                    </div>
                                </div>
                                <div class="card-body mt-0 pt-2" data-chart="revenue_category_chart">
                                    <div id="revenue_category_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                '.(
                    $isAdmin ?
                    '
                    <div class="col-lg-12"><div class="grid grid-cols-1 hidden md:grid-cols-2 lg:grid-cols-5 gap-4">
                    '.$financialSummary.'
                    </div></div>
                    <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                        <div class="card rounded-2xl">
                            <div class="card-header pr-0">
                                <div class="row width-100">
                                    <div class="col-md-7">
                                        <h4 class="text-uppercase font-13">Attendance Logs</h4>
                                    </div>
                                    <div align="right" class="col-md-5">
                                        <div class="btn-group" data-filter="quick_attendance_filter" id="quick_attendance_filter" role="group" aria-label="Filter Attendance">
                                            <button type="button" data-stream="attendance_report" data-period="last_week" class="btn btn-info">Last Week</button>
                                            <button type="button" data-stream="attendance_report" data-period="this_week" class="btn active btn-info">This Week</button>
                                            <button type="button" data-stream="attendance_report" data-period="this_month" class="btn btn-info">This Month</button>
                                            <button type="button" data-stream="attendance_report" data-period="last_month" class="btn btn-info">Last Month</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <div data-chart_container="attendance_chart">
                                    <div id="attendance_chart" style="min-height:420px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>' : null
                );
            
            // load the class payment information by class only
            if($isAccountant) {
                $response->html .= '
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header pr-0">
                            <div class="row width-100">
                                <div class="col-md-5">
                                    <h4>Fees Payment by Class</h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-0 quick_loader">
                            <div class="form-content-loader" style="display: flex; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <div data-chart="class_fees_payment_chart">
                                <div id="class_fees_payment_chart" style="width:100%;max-height:420px;height:420px;"></div>
                            </div>
                        </div>
                    </div>
                </div>';
            }

            // if the user logged in is an admin
            if($isAdminAccountant) {
                // append the data
                $response->html .= '
                    <div class="col-lg-4 col-md-6 col-12 col-sm-12">
                        <div class="card rounded-2xl">
                            <div class="card-header">
                                <h4 class="text-uppercase font-13 mb-0">Students</h4>
                            </div>
                            <div class="card-body p-0 bg-gradient-to-br from-blue-100 to-blue-50">
                                <div class="card-body pb-2" data-chart="male_female_comparison">
                                    <canvas style="max-height:225px;height:225px;" id="male_female_comparison"></canvas>
                                </div>
                            </div>
                            <div class="card-footer pb-1 mt-1">
                                <div class="student-report">
                                    <div class="student-count pseudo-bg-blue">
                                        <h4 class="item-title text-black mb-1">Female Students</h4>
                                        <div class="font-25 font-bold text-black" data-sex_count="Female"></div>
                                    </div>
                                    <div class="student-count pseudo-bg-yellow">
                                        <h4 class="item-title text-black mb-1">Male Students</h4>
                                        <div class="font-25 font-bold text-black" data-sex_count="Male"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 col-sm-12 d-none d-sm-block">
                        <div class="card rounded-2xl">
                            <div class="card-header">
                                <h4 class="text-uppercase font-13 mb-0">Upcoming Events</h4>
                            </div>
                            <div class="card-body pr-2 pl-2 trix-slim-scroll" style="max-height:355px;height:355px;'.(!empty($upcoming_events_list) ? "overflow-y:auto;" : null).'">
                                <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                                    '.(
                                        !empty($upcoming_events_list) ? 
                                            $upcoming_events_list : 
                                            no_record_found("No Events Found", "No events have been created yet.", null, "Event")
                                    ).'
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 col-sm-12">
                        <div class="card rounded-2xl">
                            <div class="card-header">
                                <h4 class="text-uppercase font-13 mb-0">Upcoming Birthdays</h4>
                            </div>
                            <div class="pl-1 pr-2 trix-slim-scroll" style="max-height:355px;height:355px;overflow-y:auto;">
                                <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                                    '.$upcoming_birthday_list.'
                                </ul>
                            </div>
                        </div>
                    </div>';
            }

        } elseif($isWardTutorParent || $isEmployee) {

            // if the user logged in is a tutor or student
            $response->html .= '
            <div class="row default_period" data-current_period="'.$global_period.'">
                '.(
                    $isTutorStudent || $isEmployee ?
                    ''.($data_stream ? '
                        <div class="col-lg-3 col-md-6 col-sm-12 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="card card-statistic-1 border-top-0 border-bottom-0 border-right-0 border-left-lg border-green border-left-solid bg-gradient-to-br from-green-300 to-green-100">
                                <div class="flex items-center justify-between p-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-green-600 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-lg">
                                        <i class="fas fa-user-check text-white text-xl"></i>
                                    </div>
                                    <div class="card-wrap text-right">
                                        <h3 data-attendance_count="Present" class="font-light text-black mb-0">0</h3>
                                        <span class="text-dark">Days Present</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="card card-statistic-1 border-top-0 border-bottom-0 border-right-0 border-left-lg border-danger border-left-solid bg-gradient-to-br from-red-300 to-red-100">
                                <div class="flex items-center justify-between p-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-600 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-lg">
                                        <i class="fas fa-user-alt-slash text-white text-xl"></i>
                                    </div>
                                    <div class="card-wrap text-right">
                                        <h3 data-attendance_count="Absent" class="font-light text-black mb-0">0</h3>
                                        <span class="text-dark">Days Absent</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="card card-statistic-1 border-top-0 border-bottom-0 border-right-0 border-left-lg border-purple border-left-solid bg-gradient-to-br from-purple-300 to-purple-100">
                                <div class="flex items-center justify-between p-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-purple-600 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-lg">
                                        <i class="fas fa-user-edit text-white text-xl"></i>
                                    </div>
                                    <div class="card-wrap text-right">
                                        <h3 data-attendance_count="logs_count" class="font-light text-black mb-0">0</h3>
                                        <span class="text-dark">Logs Counter</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="card card-statistic-1 border-top-0 border-bottom-0 border-right-0 border-left-lg border-blue border-left-solid bg-gradient-to-br from-blue-300 to-blue-100">
                                <div class="flex items-center justify-between p-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-600 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-lg">
                                        <i class="fas fa-list text-white text-xl"></i>
                                    </div>
                                    <div class="card-wrap text-right">
                                        <h3 data-attendance_count="Term" class="font-light text-black mb-0">0</h3>
                                        <span class="text-dark">'.$academicSession.' Days</span>
                                    </div>
                                </div>
                            </div>
                        </div>' : ''
                    ).'' : null
                ).'
                '.($isWardTutorParent || $isEmployee ?
                '<div class="col-lg-4 col-md-12">
                    '.($isStudent || $isTutor ? '
                        <div class="relative card overflow-hidden rounded-2xl  bg-gradient-to-br from-yellow-200 to-red-50">
                            <div class="card-body mb-2 pl-2 pr-2 pb-2" align="center">
                                <p class="font-16 p-0 m-0 text-primary text-uppercase">Academic Year</p>
                                <h6 class="mt-1 pt-0">'.$defaultAcademics->academic_year.'</h6>
                                <span class="font-16 font-bold text-black">
                                    '.date("jS M, Y", strtotime($defaultAcademics->year_starts)).' 
                                        &nbsp; <i class="fa fa-arrow-alt-circle-right"></i> &nbsp;
                                    '.date("jS M, Y", strtotime($defaultAcademics->year_ends)).'
                                </span>
                                <hr class="my-2">
                                <p class="font-16 p-0 mt-1 text-uppercase text-primary">Academic '.($academicSession ?? null).'</p>
                                <h6 class="mt-1 pt-0 text-uppercase text-primary">'.($defaultAcademics->current_term_name ?? $defaultAcademics->academic_term).'</h6>
                                <span class="font-16 font-bold text-black">
                                    '.date("jS M, Y", strtotime($defaultAcademics->term_starts)).' 
                                        &nbsp; <i class="fa fa-arrow-alt-circle-right"></i> &nbsp;
                                    '.date("jS M, Y", strtotime($defaultAcademics->term_ends)). '
                                </span>
                            </div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-400 to-purple-400 opacity-20 rounded-full -translate-y-16 translate-x-16 animate-pulse"></div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-br from-blue-400 to-cyan-400 opacity-20 rounded-full translate-y-12 -translate-x-12 animate-pulse" style="animation-delay: -2s;"></div>
                        </div>' : null
                    ).'
                    <div class="card">
                        '.($isTutorStudent || $isEmployee ?
                            '<div class="card-header">
                                <h4>ABOUT ME</h4>
                            </div>
                            <div class="card-body mt-0 pt-0 pb-0">
                                <div class="py-1 pt-3">
                                    <p class="clearfix">
                                        <span class="float-left">Name</span>
                                        <span class="float-right text-muted">'.$data->name.'</span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">Gender</span>
                                        <span class="float-right text-muted">'.$data->gender.'</span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">Enrollment Date</span>
                                        <span class="float-right text-muted">'.$data->enrollment_date.'</span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">Class</span>
                                        <span class="float-right text-muted">'.$data->class_name.'</span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">Section</span>
                                        <span class="float-right text-muted">'.$data->section_name.'</span>
                                    </p>
                                    <p class="clearfix">
                                        <span class="float-left">Department</span>
                                        <span class="float-right text-muted">'.$data->department_name.'</span>
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
                            </div>' : '
                            <div class="card-header">
                                <h5 class="pb-0 mb-0">My Wards</h5>
                            </div>
                            <div class="card-body pr-2 trix-slim-scroll mt-0 pt-0 pb-0" style="max-height:575px;min-height:435px;overflow-y:auto;">
                                <div class="py-2" style="width:98%">
                                    '.$wards_list.'
                                </div>
                            </div>'
                        ).'
                    </div>
                    '.($isParent ? 
                    '<div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-uppercase font-13">Upcoming Events</h4>
                            </div>
                            <div class="card-body pr-2 pl-2 trix-slim-scroll" style="max-height:345px;height:345px;overflow-y:auto;">
                                <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                                    '.$upcoming_events_list.'
                                </ul>
                            </div>
                        </div>
                    </div>' : null).'
                </div>
                <div class="col-lg-8 col-md-12">
                    <div class="row">
                        '.($isParent ?                             
                            '<div class="col-lg-4 col-md-6 col-sm-12 transition-all duration-300 transform hover:-translate-y-1">
                                <div class="card card-statistic-1 border-top-0 border-bottom-0 border-right-0 border-left-lg border-green border-left-solid bg-gradient-to-br from-green-300 to-green-100">
                                    <div class="flex items-center justify-between p-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-green-600 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-lg">
                                            <i class="fas fa-users text-white text-xl"></i>
                                        </div>
                                        <div class="card-wrap text-right">
                                            <h3 data-attendance_count="Wards" class="font-light text-black mb-0">'.$wards_count.'</h3>
                                            <span class="text-dark">Wards</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 transition-all duration-300 transform hover:-translate-y-1">
                                <div class="card card-statistic-1 border-top-0 border-bottom-0 border-right-0 border-left-lg border-purple border-left-solid bg-gradient-to-br from-purple-300 to-purple-100">
                                    <div class="flex items-center justify-between p-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-purple-600 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-lg">
                                            <i class="fas fa-money-bill text-white text-xl"></i>
                                        </div>
                                        <div class="card-wrap text-right">
                                            <h3 data-attendance_count="Payments" class="font-light text-black mb-0">'.number_format($total_expenditure, 2).'</h3>
                                            <span class="text-dark">Total Payments</span>
                                        </div>
                                    </div>
                                </div>
                            </div>' : null
                        ).'
                        '.($isTutorStudent ? $assignment_list : ($expenses_list ?? $admission_enquiry)).'
                    </div>
                </div>
                ' : '').'
            </div>
            <div class="row">
                '.(!$isWardTutorParent && !$isEmployee ? 
                    '<div class="col-lg-4 col-md-6 col-12 col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-uppercase font-13">Upcoming Events</h4>
                            </div>
                            <div class="card-body pr-2 pl-2 trix-slim-scroll" style="max-height:345px;height:345px;overflow-y:auto;">
                                <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                                    '.$upcoming_events_list.'
                                </ul>
                            </div>
                        </div>
                    </div>' : null).'
                '.($isAdminAccountant || $isEmployee ? null : '
                    <div class="col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-header width-100-per pr-0 text-uppercase">
                                <div class="row width-per-100">
                                    <div class="col-md-9">
                                        <h4>'.($isWardParent ? "Today's Timetable": "Today's Lessons to Teach").'</h4>
                                    </div>
                                    <div class="col-md-3 p-0">
                                        '.(($isWardParent && $session->student_id) || $isTutor ? '<button onclick="load(\'gradebook\');" class="btn btn-block btn-primary"><i class="fa fa-book-open"></i> Grade Book</button>' : null).'
                                    </div>
                                </div>
                            </div>
                            <div class="card-body trix-slim-scroll table-responsive">
                                '.$timetable.'
                            </div>
                        </div>
                    </div>'
                ).'
            </div>';
        }

        // if a support personnel has been logged in
        else if($isSupport) {

            // init values
            $counter = [
                'Expired' => 0,
                'Pending' => 0,
                'Activated' => 0,
                'Suspended' => 0,
                'Active' => 0,
                'Propagation' => 0,
                'Complete' => 0
            ];
            $schools_list = "";
            $load_schools_list = $myClass->pushQuery("*", "clients_accounts");

            // loop through the list of schools
            foreach($load_schools_list as $key => $school) {
                
                $counter[$school->client_state] += 1;

                $action = null;
                if($school->setup !== "Developer") {
                    $action = "<button title='Manage {$school->client_name} Account Information' onclick='return load(\"schools/{$school->client_id}\")' class='btn btn-outline-success btn-sm'><i class='fa fa-edit'></i></button>";
                    $action .= " <button title='View Update History of {$school->client_name} Account' onclick='return load(\"schools/history/{$school->client_id}\")' class='btn btn-outline-primary btn-sm'><i class='fa fa-comments'></i></button>";
                    $action .= " <a title='Enter Preview Mode for {$school->client_name} Account' href='{$myClass->baseUrl}dashboard?preview_mode=true&client_id={$school->client_id}' class='btn btn-outline-warning btn-sm'><i class='fa fa-eye'></i></a>";
                }

                $schools_list .= "
                    <tr>
                        <td>".($key + 1)."</td>
                        <td>
                            <span class='text-primary hover cursor text-uppercase font-bold' ".(!empty($action) ? "onclick='return load(\"schools/{$school->client_id}\")'" : null).">{$school->client_name}</span>
                            <br> <strong>{$school->client_id}</strong>
                        </td>
                        <td>{$school->client_email}
                        <br>{$school->client_contact}
                        <br>{$school->client_secondary_contact}</td>
                        <td>{$school->client_address}</td>
                        <td>{$myClass->the_status_label($school->client_state)}</td>
                        <td align='center'>{$action}</td>
                    </tr>";
            }

            // set the html string
            $response->html = '
                <div class="row">
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-body pr-3 pl-3 card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-bold mb-0">REGISTERED SCHOOLS</h6>
                                        <span class="font-bold font-25 mb-0">'.count($load_schools_list).'</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-orange text-white">
                                            <i class="fas fa-book-open"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-body card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-bold mb-0">ACTIVE SCHOOLS</h6>
                                        <span class="font-bold font-25 mb-0">'.($counter["Active"] + $counter["Propagation"] + $counter["Complete"] + $counter["Activated"]).'</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-green text-white">
                                            <i class="fas fa-landmark"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-body card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-bold mb-0">INACTIVE SCHOOLS</h6>
                                        <span class="font-bold font-25 mb-0">'.
                                            ($counter["Expired"] + $counter["Pending"] + $counter["Suspended"])
                                        .'</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-orange text-white">
                                            <i class="fas fa-home"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-body card-type-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="font-14 text-uppercase font-bold mb-0">SUPPORT USERS</h6>
                                        <span class="font-bold font-25 mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="card-circle l-bg-orange text-white">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">SCHOOLS LIST</div>
                            <div class="card-body">
                                <div class="table-responsive table-student_staff_list">
                                    <table class="table table-bordered table-sm table-striped raw_datatable">
                                        <thead>
                                            <tr>
                                                <th width="6%" class="text-center">#</th>
                                                <th>SCHOOL NAME</th>
                                                <th>PHONE / EMAIL</th>
                                                <th>ADDRESS</th>
                                                <th>STATUS</th>
                                                <th width="10%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>'.$schools_list.'</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>';
        }
        
        $response->html .= '
        </div>
    </section>';

}

// print out the response
echo json_encode($response);
?>