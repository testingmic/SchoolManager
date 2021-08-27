<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Settings";
$response->title = "{$pageTitle} : {$appName}";

// unset all existing sessions
$session->remove(["student_csv_file", "course_csv_file", "staff_csv_file", "last_recordUpload"]);

// confirm that the user has the required permissions
if(!$accessObject->hasAccess("manage", "settings")) {
    // show the error page
    $response->html = page_not_found();
} else {

    // get the settings form
    $the_form = load_class("forms", "controllers")->settings_form($clientId);

    $response->scripts = ["assets/js/import.js", "assets/js/grading.js"];

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="padding-20">
                                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">General</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="academic_calendar-tab2" data-toggle="tab" href="#academic_calendar" role="tab" aria-selected="true">Academic Calendar</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="examination-tab2" data-toggle="tab" href="#examination" role="tab" aria-selected="true">Grading</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="results_structure-tab2" data-toggle="tab" href="#results_structure" role="tab" aria-selected="true">Result Structure & Settings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="import_students-tab2" data-toggle="tab" href="#import_students" role="tab" aria-selected="true">Import Student</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="staff-tab2" data-toggle="tab" href="#staff" role="tab" aria-selected="true">Import Staff</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="parent-tab2" data-toggle="tab" href="#parent" role="tab" aria-selected="true">Import Parent</a>
                                    </li>
                                </ul>
                                <div class="tab-content tab-bordered" id="myTab3Content">
                                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                        '.($the_form["general"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade" id="academic_calendar" role="tabpanel" aria-labelledby="academic_calendar-tab2">
                                        '.($the_form["calendar"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade" id="examination" role="tabpanel" aria-labelledby="examination-tab2">
                                        '.($the_form["examination"] ?? null).'
                                    </div>                                    
                                    <div class="tab-pane fade" id="results_structure" role="tabpanel" aria-labelledby="results_structure-tab2">
                                        '.($the_form["results_structure"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade" id="import_students" role="tabpanel" aria-labelledby="import_students-tab2">
                                        '.($the_form["student"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade" id="staff" role="tabpanel" aria-labelledby="staff-tab2">
                                        <div class="col-lg-12">
                                            '.($the_form["staff"] ?? null).'
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="parent" role="tabpanel" aria-labelledby="parenttab2">
                                        <div class="col-lg-12">
                                            '.($the_form["parent"] ?? null).'
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';
    
    // unset the session if existing
    $session->remove("last_uploadId");

}
// print out the response
echo json_encode($response);
?>