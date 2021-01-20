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
global $accessObject, $defaultUser, $isAdminAccountant;
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
$filters = [
    "this_week" => [
        "title" => "This Week",
        "alt" => [
            "key" => "last_week",
            "value" => "Last Week"
        ]
    ],
    "last_14days" => [
        "title" => "Last 2 Weeks",
        "alt" => [
            "key" => "last_14days",
            "value" => "Last 28 Days"
        ]
    ],
    "this_month" => [
        "title" => "This Month",
        "alt" => [
            "key" => "last_month",
            "value" => "Last Month"
        ]
    ],
    "last_30days" => [
        "title" => "Last 30 Days",
        "alt" => [
            "key" => "last_30days",
            "value" => "Previous 30 Days"
        ]
    ],
    "last_month" => [
        "title" => "Last Month",
        "alt" => [
            "key" => "last_month",
            "value" => "Last 2 Months"
        ]
    ],
    "last_3months" => [
        "title" => "Last 3 Month",
        "alt" => [
            "key" => "last_month",
            "value" => "Last 6 Months"
        ]
    ]
];

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
if(!empty($events_list->birthday_list)) {
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
else {
    // unset the session
    $session->remove("assignment_uploadID");

    // the query parameter to load the user information
    $assignments_param = (object) [
        "minified" => true,
        "clientId" => $clientId,
        "userData" => $defaultUser
    ];
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

// append the scripts to the page
if($isAdminAccountant) {
    $response->scripts = ["assets/js/analitics.js"];
}

// set the response dataset
$response->html = '
    <section class="section">
        <div class="d-flex mt-3 justify-content-between">
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
                                    <h6 class="text-muted mb-0">Teaching Stafff</h6>
                                    <span data-count="total_teachers_count" class="font-weight-bold mb-0">0</span>
                                </div>
                                <div class="col-auto">
                                    <div class="card-circle l-bg-cyan text-white">
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
                                        <i class="fas fa-user"></i>
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
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                <div class="card-header">
                    <h4>Revenue Flow Chart</h4>
                </div>
                <div class="card-body">
                    <div class="form-content-loader" style="display: none; position: absolute">
                        <div class="offline-content text-center">
                            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                        </div>
                    </div>
                    <div id="revenue_flow_chart"></div>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                <div class="card-header">
                    <h4>Attendance Chart</h4>
                </div>
                <div class="card-body">
                    <div class="form-content-loader" style="display: none; position: absolute">
                        <div class="offline-content text-center">
                            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                        </div>
                    </div>
                    <canvas id="myChart"></canvas>
                </div>
                </div>
            </div>
        </div>
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
                                <div style="width:50%">
                                    <div class="d-flex justify-content-between">
                                        <div class="col-6">
                                            <p class="text-muted font-15 text-truncate m-b-5">Tuition Fees</p>
                                            <h5><i class="fas fa-arrow-circle-up col-green m-r-5"></i><span data-count="total_fees_received">0.00</span></h5>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted text-truncate m-b-5">Revenue <span data-filter="period">Last Week</span></p>
                                            <h5><i class="fas fa-arrow-circle-up col-green m-r-5"></i><span data-count="previous_amount_received">0.00</span></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="revenue_category_chart"></div>
                        </div>
                    </div>
                </div>' : ''
            ).'
            <div class="col-lg-5 col-md-6 col-12 col-sm-12">
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
            '.(!empty($upcoming_birthday_list) ? 
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
                <div class="col-lg-7 col-md-6 col-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Assignments</h4>
                        </div>
                        <div class="card-body trix-slim-scroll" style="max-height:345px;height:345px;overflow-y:auto;">
                            <div class="table-responsive">
                                <table data-empty="" class="table slim-scrdddoll table-striped datatable">
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
                </div>
                '
            ).'
        </div>
    </section>';

// print out the response
echo json_encode($response);
?>