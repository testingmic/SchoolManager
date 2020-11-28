<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
if(!isset($_SERVER["HTTP_REFERER"])) {
    header("location: {$baseUrl}main");
    exit;
}

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Student Details";
$response->title = "{$pageTitle} : {$appName}";

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

$response->scripts = [
    "assets/js/page/index.js"
];

// student id
$user_id = confirm_url_id(2) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($user_id)) {

    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "no_limit" => 1,
        "user_type" => "student"
    ];

    $data = load_class("users", "controllers")->list($student_param);
    $incidents = load_class("incidents", "controllers")->list($student_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // populate the incidents
        $incidents_list = "";
        if(!empty($incidents["data"])) {
            $incidents_list = "<div class='row mb-3'>";
            // set the header for the list
            $incidents_list .= '<div><h5>INCIDENTS LIST</h5></div>';
            // loop through the list of all incidents
            foreach($incidents["data"] as $each) {
                $incidents_list .= "
                    <div class=\"col-12 col-md-6 load_incident_record col-lg-4\" data-id=\"{$each->item_id}\">
                        <div class=\"card card-success\">
                            <div class=\"card-header\"><h4>{$each->subject}</h4></div>
                            <div class=\"card-body\">{$each->incident}</div>
                        </div>
                    </div>";
            }
            $incidents_list = "</div>";
            $response->client_auto_save = ["incidents_array" => $incidents["data"]];
        }

        // set the first key
        $data = $data["data"][0];

        // guardian information
        $user_form = load_class("forms", "controllers")->student_form($clientId, $baseUrl, $data);
        $hasUpdate = $accessObject->hasAccess("update", "student");

        $guardian = "";
        if(!empty($data->guardian_information)) {
            $guardian .= '<div><h5>GUARDIAN INFORMATION</h5></div>';
            foreach($data->guardian_information as $each) {
                $guardian .= "<div class='row mb-4'>";
                $guardian .= "<div class='col-lg-3'><strong>Fullname:</strong><br> {$each->guardian_fullname}</div>";
                $guardian .= "<div class='col-lg-2'><strong>Relation:</strong><br> {$each->guardian_relation}</div>";
                $guardian .= "<div class='col-lg-3'><strong>Contact:</strong><br> {$each->guardian_contact}</div>";
                $guardian .= "<div class='col-lg-4'><strong>Email:</strong><br> {$each->guardian_email}</div>";
                $guardian .= "</div>";
            }
        }

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
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
                        <p class="clearfix">
                            <span class="float-left">Phone</span>
                            <span class="float-right text-muted">'.$data->phone_number.'</span>
                        </p>
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
                        aria-selected="true">Other Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="calendar-tab2" data-toggle="tab" href="#calendar" role="tab"
                        aria-selected="true">Academic Calendar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="attendance-tab2" data-toggle="tab" href="#attendance" role="tab"
                        aria-selected="true">Student Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="incident-tab2" data-toggle="tab" href="#incident" role="tab"
                        aria-selected="true">Incident Logs</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                            '.$guardian.'
                        </div>
                        <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab2">
                            

                        </div>
                        <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab2">
                            
                            
                        </div>
                        <div class="tab-pane fade" id="incident" role="tabpanel" aria-labelledby="incident-tab2">
                            <div class="text-right">
                                <button type="button" onclick="return load_quick_form(\'incident_log_form\',\''.$user_id.'\');" class="btn btn-primary"><i class="fa fa-plus"></i> Log Incident</button>
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