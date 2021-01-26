<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed OR the request_method is not POST
jump_to_main($baseUrl);

// initial variables
global $accessObject, $defaultUser, $isAdminAccountant, $isTutorStudent, $isParent, $isStudent;
$appName = config_item("site_name");

// confirm that user id has been parsed
global $SITEURL, $usersClass;
$clientId = $session->clientId;
$loggedUserId = $session->userId;
$cur_user_id = (confirm_url_id(1)) ? xss_clean($SITEURL[1]) : $loggedUserId;

// get the user data
$accessObject->clientId = $clientId;
$accessObject->userId = $loggedUserId;
$accessObject->userPermits = $defaultUser->user_permissions;

// filters
$response = (object) [];
$response->title = "Dashboard : {$appName}";

// create a new events object
$eventClass = load_class("events", "controllers");

// set the parameters
$userData = $defaultUser;
$userData->do_no_encode = true;
$userData->client_id = $clientId;
$userData->mini_description = true;
$userData->the_user_type = $defaultUser->user_type;
$userData->date_range = date("Y-m-d", strtotime("-20 days")).':'.date("Y-m-t", strtotime("+20 days"));

// load the events list
$events_list = $eventClass->events_list($userData);

$wards_list = "";
$assigments_list = "";
$upcoming_events_list = "";
$upcoming_birthday_list = "";

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
    // append to the events list
    $upcoming_events_list .= "
        <li class='media'>
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

        $upcoming_birthday_list .= "
            <li class='media'>
                <img title='Click to view student details' class='rounded-circle cursor author-box-picture' width='40px' src=\"{$baseUrl}{$event["description"]->image}\">
                <div class='media-body' style='flex: 2;'>
                    <div class='media-title'>
                        {$event["description"]->name}<br>
                    </div>
                    
                </div>
                <div class='media-progressbar'>
                    <div class='progress-text'>".date("l, jS M Y", strtotime($clean_date))."</div>
                </div>
            </li>";
    }

}

// load the assignments list
else if($isTutorStudent) {
    // unset the session
    $session->remove("assignment_uploadID");

    // the query parameter to load the user information
    $assignments_param = (object) ["minified" => true, "clientId" => $clientId, "userData" => $defaultUser];
    $assignments_array_list = load_class("assignments", "controllers")->list($assignments_param);

    // can update assignments
    $can_Update_Assign = $accessObject->hasAccess("update", "assignments");

    // unset the sessions if $session->currentQuestionId is not empty
    foreach($assignments_array_list["data"] as $key => $each) {
        
        $action = "<a href='{$baseUrl}update-assignment/{$each->item_id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

        $assigments_list .= "<tr data-row_id=\"{$each->id}\">";
        $assigments_list .= "<td>".($key+1)."</td>";
        $assigments_list .= "<td>{$each->assignment_title} ".(
            $can_Update_Assign ? 
                "<br><strong>Class:</strong> <a href=\"{$baseUrl}update-class/{$each->class_id}/view\">{$each->class_name}</a>
                <br><strong>Course:</strong> <a href=\"{$baseUrl}update-course/{$each->course_id}/view\">{$each->course_name}</a>" : 
                "<br><strong>Course:</strong> <a href=\"{$baseUrl}update-course/{$each->course_id}/view\">{$each->course_name}</a>"
            )." ".($can_Update_Assign ? $myClass->the_status_label($each->state) : $each->handedin_label)."
            </td>";
        $assigments_list .= "<td>{$each->due_date} @ {$each->due_time}</td>";

        // show this section if the user has the necessary permissions
        if($can_Update_Assign) {
            $assigments_list .= "<td>{$each->students_assigned}</td>";
            $assigments_list .= "<td>{$each->students_handed_in}</td>";
            $assigments_list .= "<td>{$each->students_graded}</td>";
        }
        
        if(!$can_Update_Assign) {
            $assigments_list .= "<td>{$each->awarded_mark}</td>";
        }

        $assigments_list .= "<td align='center'>{$action}</td>";
        $assigments_list .= "</tr>";
    }
    
}

// append the events list as part of the results
$response->array_stream["events_array_list"] = $events_list;

// the default data to stream
$data_stream = 'id="data-report_stream" data-report_stream="attendance_report,library_report,departments_report"';

// set the data to stream for an admin user
if($isAdminAccountant) {
    $data_stream = 'id="data-report_stream" data-report_stream="summary_report,revenue_flow,library_report,departments_report"';
}

// append the scripts to the page
$response->scripts = ["assets/js/analitics.js"];
$timetable = "<div class='text-center'>No timetable record for today was found in the database.</div>";

// if ward/parent/tutor
if($isWardTutorParent) {

    // load the use information
    $data = $defaultUser;
    $timetableClass = load_class("timetable", "controllers");

    // load the wards list
    if($isParent) {
        
        // wards count
        $wards_count = 0;
        $total_expenditure = 0;
        
        // stream nothing if the student id has not been set yet
        if(!empty($session->student_id)) {
            // load the class timetable for student / parent & The lessons if a teacher is logged in
            $timetable = $timetableClass->class_timetable($session->student_class_id, $clientId, "today");    
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
                        <div class='col-lg-3 text-center'>
                            <img src='{$baseUrl}{$ward->image}' width='80px' class='rounded-circle author-box-picture'>
                        </div>
                        <div class='col-lg-9'>
                            <table width='100%'>
                                <tr>
                                    <td class='font-weight-bold p-1' align='right'>Name</td>
                                    <td class='pr-2' align='right'>{$ward->name}</td>
                                </tr>
                                <tr>
                                    <td class='font-weight-bold p-1' align='right'>Gender</td>
                                    <td class='pr-2' align='right'>{$ward->gender}</td>
                                </tr>
                                <tr>
                                    <td class='font-weight-bold p-1' align='right'>Class</td>
                                    <td class='pr-2' align='right'>{$ward->class_name}</td>
                                </tr>
                                <tr>
                                    <td class='font-weight-bold p-1' align='right'>Admission Id</td>
                                    <td class='pr-2' align='right'>{$ward->unique_id}</td>
                                </tr>
                                <tr>
                                    <td class='font-weight-bold p-1' align='right'>Admission Date</td>
                                    <td class='pr-2' align='right'>{$ward->enrollment_date}</td>
                                </tr>
                                <tr>
                                    <td colspan='2' align='right'>
                                        ".($isCurrent ? "<span class='badge mb-2 badge-success'>Selected</span>" : "<button onclick='return set_default_Student(\"{$ward->student_guid}\")' class='btn btn-sm btn-outline-primary mb-2'>Select Student</button>")."
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>";
            }
            
            // begin the request parameter
            $params = (object) [
                "clientId" => $clientId,
                "userData" => $defaultUser,
                "student_array_ids" => $defaultUser->wards_list_ids
            ];
            $item_list = load_class("fees", "controllers", $params)->list($params)["data"];

            $fees_history = "";

            // loop through the list
            foreach($item_list as $key => $each) {
                // add up to the expenses
                $total_expenditure += $each->amount;

                // list the items
                $action = "";
                $action .= "&nbsp;<a href='{$baseUrl}fees-view/{$each->item_id}/print' class='btn btn-sm btn-outline-warning'><i class='fa fa-print'></i></a>";

                $fees_history .= "<tr data-row_id=\"{$each->item_id}\">";
                $fees_history .= "<td>".($key+1)."</td>";
                $fees_history .= "
                    <td>
                        <div class='d-flex justify-content-start'>
                            ".(!empty($each->student_info->image) ? "
                            <div class='mr-2'><img src='{$baseUrl}{$each->student_info->image}' width='40px' height='40px'></div>" : "")."
                            <div>
                                <a href='{$baseUrl}update-student/{$each->student_info->user_id}/'>{$each->student_info->name}</a> <br>
                                <strong>{$each->student_info->unique_id}</strong><br>
                                {$each->class_name}
                            </div>
                        </div>
                    </td>";
                $fees_history .= "<td>{$each->amount}</td>";
                $fees_history .= "<td>{$each->category_name}</td>";
                $fees_history .= "<td>{$each->recorded_date}</td>";
                $fees_history .= "<td width='10%' align='center'>{$action}</td>";
                $fees_history .= "</tr>";
            }

            // assign the assignments list
            $expenses_list = '
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Expenses</h4>
                    </div>
                    <div class="card-body trix-slim-scroll" style="max-height:435px;height:435px;overflow-y:auto;">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
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
            $timetable = $timetableClass->class_timetable($defaultUser->class_guid, $clientId, "today", 90);    
        } else {
            $timetable = $timetableClass->teacher_timetable($defaultUser->course_ids, $clientId);
        }

        // assign the assignments list
        $assignment_list = '
        <div class="col-lg-12 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Assignments</h4>
                </div>
                <div class="card-body trix-slim-scroll" style="max-height:435px;height:435px;overflow-y:auto;">
                    <div class="table-responsive">
                        <table data-empty="" class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th>Title</th>
                                    <th>Due Date</th>
                                    '.($can_Update_Assign ? '
                                        <th width="10%">Assigned</th>
                                        <th>Handed In</th>
                                        <th>Marked</th>' : '<th>Awarded Mark</th>'
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

}

// set the response dataset
$response->html = '
    <section class="section">
        <div class="d-flex mt-3 justify-content-between" '.$data_stream.'>
            <div class="section-header">
                <h1>Dashboard</h1>
            </div>
        </div>
        '.($isAdminAccountant ?
            '<div class="row">
                <div class="col-xl-3 col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Total Students</h6>
                                    <span data-count="total_students_count" class="font-weight-bold mb-0">0</span>
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
                <div class="col-xl-3 col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Teaching Stafff</h6>
                                    <span data-count="total_teachers_count" class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle l-bg-cyan text-white">
                                        <i class="fas fa-user-secret"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Employees / Users</h6>
                                    <span data-count="total_employees_count" class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle l-bg-green text-white">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Parents</h6>
                                    <span data-count="total_parents_count" class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle l-bg-yellow text-white">
                                        <i class="fas fa-anchor"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 hidden col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Attendance</h6>
                                    <span class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle l-bg-purple text-white">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>' : ''
        ).'
        <div class="row default_period" data-current_period="last_14days">
            '.($isAdminAccountant ?
            '<div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Revenue Flow Chart</h4>
                    </div>
                    <div class="card-body quick_loader">
                        <div class="form-content-loader" style="display: flex; position: absolute">
                            <div class="offline-content text-center">
                                <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                            </div>
                        </div>
                        <div class="card-body" data-chart="revenue_flow_chart">
                            <canvas id="revenue_flow_chart" style="width:100%;max-height:405px;height:405px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>' : '').'
            '.($isWardTutorParent ?
            '<div class="col-lg-4 col-md-12">
                <div class="card">
                    '.($isTutorStudent ?
                        '<div class="card-header">
                            <h5 class="pb-0 mb-0">About Me</h5>
                        </div>
                        <div class="card-body mt-0 pt-0 pb-0">
                            <div class="py-4">
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
                            <h5 class="pb-0 mb-0">My Kids</h5>
                        </div>
                        <div class="card-body pr-2 trix-slim-scroll mt-0 pt-0 pb-0" style="max-height:575px;min-height:435px;overflow-y:auto;">
                            <div class="py-2" style="width:98%">
                                '.$wards_list.'
                            </div>
                        </div>'
                    ).'
                </div>
            </div>
            <div class="col-lg-8 col-md-12">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <i class="fas fa-list-alt card-icon col-blue"></i>
                            <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">0</h3>
                                    <span class="text-muted">Notifications</span>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    '.($isTutorStudent ?
                    '<div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <i class="fas fa-book-open card-icon col-red"></i>
                            <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">0</h3>
                                    <span class="text-muted">Assignments</span>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <i class="fas fa-percentage card-icon col-green"></i>
                            <div class="card-wrap">
                            <div class="padding-20">
                                <div class="text-right">
                                    <h3 class="font-light mb-0">0</h3>
                                    <span class="text-muted">Attendance</span>
                                </div>
                            </div>
                            </div>
                        </div>':'
                        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <i class="fas fa-users card-icon col-red"></i>
                                <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">'.$wards_count.'</h3>
                                        <span class="text-muted">Wards</span>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <i class="fas fa-money-alt card-icon col-green"></i>
                                <div class="card-wrap">
                                <div class="padding-20">
                                    <div class="text-right">
                                        <h3 class="font-light mb-0">'.number_format($total_expenditure, 2).'</h3>
                                        <span class="text-muted">Total Expenditure</span>
                                    </div>
                                </div>
                                </div>
                            </div>'
                        ).'
                    </div>
                    '.($isTutorStudent ? $assignment_list : $expenses_list).'
                </div>
            </div>
            ' : '').'
            <div class="col-md-6 hidden">
                <div class="card">
                <div class="card-header">
                    <h4>Attendance Chart</h4>
                </div>
                <div class="card-body quick_loader" style="max-height:405px;height:405px;">
                    <div class="form-content-loader" style="display: flex; position: absolute">
                        <div class="offline-content text-center">
                            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                        </div>
                    </div>
                    <canvas id="myChart"></canvas>
                </div>
                </div>
            </div>
        </div>
        '.($isAdminAccountant ?
            '<div class="row">
                <div class="col-xl-3 col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Books Category</h6>
                                    <span data-count="library_category_count" class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle l-bg-purple-dark text-white">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Books Available</h6>
                                    <span data-count="library_books_count" class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle bg-green text-white">
                                        <i class="fas fa-book-reader"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Departments</h6>
                                    <span data-count="departments_count" class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle bg-pink text-white">
                                        <i class="fas fa-bookmark"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <div class="card">
                        <div class="card-body card-type-3">
                            <div class="row">
                                <div class="col">
                                    <h6 class="text-muted mb-0">Classes</h6>
                                    <span data-count="total_classes_count" class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle bg-red text-white">
                                        <i class="fas fa-home"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>' : ''
        ).'
        <div class="row">
            '.($isAdminAccountant ? 
                '<div class="col-lg-4 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                        <h4>Class Count</h4>
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
                <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Revenue</h4>
                        </div>
                        <div class="card-body quick_loader" style="max-height:465px;height:465px;">
                            <div class="form-content-loader" style="display: flex; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="card-title"><i class="fas fa-dollar-sign col-green font-30 p-b-10"></i> <span data-count="total_revenue_received">0.00</span></h3>
                                </div>
                                <div style="width:60%">
                                    <div class="d-flex justify-content-between">
                                        <div class="col-6">
                                            <h5>&nbsp;</h5>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted text-truncate m-b-5">Revenue <span data-filter="period">Last Week</span></p>
                                            <h5><i class="fas fa-arrow-circle-up col-green m-r-5"></i><span data-count="previous_amount_received">0.00</span></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" data-chart="revenue_category_chart">
                                <div id="revenue_category_chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Students</h4>
                        </div>
                        <div class="card-body" data-chart="male_female_comparison">
                            <canvas style="max-height:225px;height:225px;" id="male_female_comparison"></canvas>
                        </div>
                        <div class="card-footer">
                            <div class="student-report">
                                <div class="student-count pseudo-bg-blue">
                                    <h4 class="item-title">Female Students</h4>
                                    <div class="item-number" data-sex_count="Female"></div>
                                </div>
                                <div class="student-count pseudo-bg-yellow">
                                    <h4 class="item-title">Male Students</h4>
                                    <div class="item-number" data-sex_count="Male"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>' : ''
            ).'
            <div class="col-lg-4 col-md-6 col-12 col-sm-12">
                <div class="card">
                    <div class="card-header pl-2">
                        <h4>Upcoming Events</h4>
                    </div>
                    <div class="card-body pr-2 pl-2 trix-slim-scroll" style="max-height:345px;height:345px;overflow-y:auto;">
                        <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                            '.$upcoming_events_list.'
                        </ul>
                    </div>
                </div>
            </div>
            '.($isAdminAccountant ? 
                '<div class="col-lg-4 col-md-6 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Upcoming Birthdays</h4>
                        </div>
                        <div class="card-body trix-slim-scroll" style="max-height:345px;height:345px;overflow-y:auto;">
                            <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                                '.$upcoming_birthday_list.'
                            </ul>
                        </div>
                    </div>
                </div>' : '
                <div class="col-lg-8">
                    '.($data_stream ? 
                        '<div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="card card-statistic-1">
                                    <i class="fas fa-user-check card-icon col-green"></i>
                                    <div class="card-wrap">
                                        <div class="padding-20">
                                            <div class="text-right">
                                                <h3 data-attendance_count="Present" class="font-light mb-0">0</h3>
                                                <span class="text-muted">Days Present</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="card card-statistic-1">
                                    <i class="fas fa-user-alt-slash card-icon col-red"></i>
                                    <div class="card-wrap">
                                        <div class="padding-20">
                                            <div class="text-right">
                                                <h3 data-attendance_count="Absent" class="font-light mb-0">0</h3>
                                                <span class="text-muted">Days Absent</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="card card-statistic-1">
                                    <i class="fas fa-user-edit card-icon col-blue"></i>
                                    <div class="card-wrap">
                                        <div class="padding-20">
                                            <div class="text-right">
                                                <h3 data-attendance_count="logs_count" class="font-light mb-0">0</h3>
                                                <span class="text-muted">Logs Counter</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>' : ''
                    ).'
                </div>
                <div class="col-lg-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>'.($isWardParent ? "Today's Timetable": "Today's Lessons to Teach").'</h4>
                        </div>
                        <div class="card-body pt-2 trix-slim-scroll table-responsive">
                            '.$timetable.'
                        </div>
                    </div>
                </div>'
            ).'
        </div>
    </section>';

// print out the response
echo json_encode($response);
?>