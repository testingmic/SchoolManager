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
global $accessObject, $defaultUser;
$appName = config_item("site_name");

// confirm that user id has been parsed
global $SITEURL, $usersClass;
$loggedUserId = $session->userId;
$cur_user_id = (confirm_url_id(1)) ? xss_clean($SITEURL[1]) : $loggedUserId;

// get the user data
$accessObject->userId = $loggedUserId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

$hasDelete = $accessObject->hasAccess("delete", "student");
$hasUpdate = $accessObject->hasAccess("update", "student");


// filters
$filters = [
    "this_week" => [
        "title" => "This Week",
        "alt" => [
            "key" => "last_week",
            "value" => "Last Week"
        ]
    ],
    "this_month" => [
        "title" => "This Month",
        "alt" => [
            "key" => "last_month",
            "value" => "Last Month"
        ]
    ],
    "this_quarter" => [
        "title" => "This Quarter",
        "alt" => [
            "key" => "last_quarter",
            "value" => "Last Quarter"
        ]
    ]
];

$response = (object) [];
$response->title = "Dashboard : {$appName}";
$response->scripts = [
    "assets/js/page/index.js"
];

// get the list of users
$student_param = (object) ["clientId" => $session->clientId,"user_type" => "student"];

$students = "";
$viewStudents = (bool) in_array($defaultUser->user_type, ["teacher", "admin"]);
// get the list of students
if($viewStudents) {

    // get the list of students
    $student_list = load_class("users", "controllers")->list($student_param);

    // loop through the students list
    foreach($student_list["data"] as $key => $each) {
        
        $action = "<a href='{$baseUrl}update-student/{$each->user_id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

        if($hasUpdate) {
            $action .= "&nbsp;<a href='{$baseUrl}update-student/{$each->user_id}/update' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
        }
        if($hasDelete) {
            $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->user_id}\", \"user\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
        }

        $students .= "<tr data-row_id=\"{$each->user_id}\">";
        $students .= "<td>".($key+1)."</td>";
        $students .= "<td><img class='rounded-circle author-box-picture' width='40px' src=\"{$baseUrl}{$each->image}\"> &nbsp; {$each->name}</td>";
        $students .= "<td>{$each->class_name}</td>";
        $students .= "<td>{$each->gender}</td>";
        $students .= "<td>{$each->date_of_birth}</td>";
        $students .= "<td>{$each->department_name}</td>";
        $students .= "<td class='text-center'>{$action}</td>";
        $students .= "</tr>";
    }
}

// set the response dataset
$response->html = '
    <section class="section">
        <div class="d-flex mt-3 justify-content-between">
            <div class="section-header">
                <h1>Dashboard</h1>
            </div>
            <div>
                <div class="form-group">
                    <select style="width:300px" class="selectpicker form-control" id="filter-dashboard" data-width="100%">';
                    foreach($filters as $key => $value) {
                        $response->html .= "<option data-select_option='".json_encode($value["alt"])."' value='{$key}'>{$value["title"]}</option>";
                    }
$response->html .= '</select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-6">
                <div class="card">
                <div class="card-body card-type-3">
                    <div class="row">
                    <div class="col">
                        <h6 class="text-muted mb-0">Total Students</h6>
                        <span class="font-weight-bold mb-0">0</span>
                    </div>
                    <div class="col-auto">
                        <div class="card-circle l-bg-orange text-white">
                        <i class="fas fa-book-open"></i>
                        </div>
                    </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 0%</span>
                    <span class="text-nowrap">Since last month</span>
                    </p>
                </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card">
                <div class="card-body card-type-3">
                    <div class="row">
                    <div class="col">
                        <h6 class="text-muted mb-0">Teaching Stafff</h6>
                        <span class="font-weight-bold mb-0">0</span>
                    </div>
                    <div class="col-auto">
                        <div class="card-circle l-bg-cyan text-white">
                        <i class="fas fa-users"></i>
                        </div>
                    </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 0%</span>
                    <span class="text-nowrap">Since last month</span>
                    </p>
                </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
              <div class="card">
                <div class="card-body card-type-3">
                  <div class="row">
                    <div class="col">
                      <h6 class="text-muted mb-0">Employees</h6>
                      <span class="font-weight-bold mb-0">0</span>
                    </div>
                    <div class="col-auto">
                      <div class="card-circle l-bg-green text-white">
                        <i class="fas fa-user"></i>
                      </div>
                    </div>
                  </div>
                  <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 0%</span>
                    <span class="text-nowrap">Since last month</span>
                  </p>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-lg-6">
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
                  <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 0%</span>
                    <span class="text-nowrap">Since last month</span>
                  </p>
                </div>
              </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                <div class="card-header">
                    <h4>Revenue Chart</h4>
                </div>
                <div class="card-body">
                    <canvas id="chart-1"></canvas>
                </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                <div class="card-header">
                    <h4>Attendance Chart</h4>
                </div>
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
                </div>
            </div>
        </div>
        <div class="row">
        '.($viewStudents ? '
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="mt-3 pr-3 pl-3">
                        <div class="d-flex justify-content-between">
                            <div><h4>Students List</h4></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Gender</th>
                                        <th>Date of Birth</th>
                                        <th>Department</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>'.$students.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>' : ''
            ).'
            </div>
        </div>

    </section>';

// print out the response
echo json_encode($response);
?>