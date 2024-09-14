<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Settings";
$response->title = $pageTitle;

// unset all existing sessions
$session->remove(["student_csv_file", "course_csv_file", "staff_csv_file", "last_recordUpload"]);

// confirm that the user has the required permissions
if(!$accessObject->hasAccess("manage", "settings")) {
    // show the error page
    $response->html = page_not_found("permission_denied");
} else {

    // get the settings form
    $the_form = load_class("forms", "controllers")->settings_form($clientId);

    // load the scripts
    $response->scripts = ["assets/js/import.js", "assets/js/grading.js", "assets/js/upload.js"];

    // parse the category list in a array
    $response->array_stream["remarks_category_list"] = $the_form["_remarks"]["remarks_category_list"];
    
    // set the url
    $url_link = $SITEURL[1] ?? null;

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
                                        <a class="nav-link '.(empty($url_link) || ($url_link == "_general") ? "active" : null).'" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">General</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link '.($url_link == "_calendar" ? "active" : null).'" id="academic_calendar-tab2" data-toggle="tab" href="#academic_calendar" role="tab" aria-selected="true">Academic Calendar</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="examination-tab2" data-toggle="tab" href="#examination" role="tab" aria-selected="true">Grading</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link '.($url_link == "_grading" ? "active" : null).'" id="results_structure-tab2" data-toggle="tab" href="#results_structure" role="tab" aria-selected="true">Result Settings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link '.($url_link == "_remarks" ? "active" : null).'" id="_remarks-tab2" data-toggle="tab" href="#_remarks" role="tab" aria-selected="true">Remarks</a>
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
                                    <div class="tab-pane fade '.(empty($url_link) || ($url_link == "_general") ? "show active" : null).'" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                        '.($the_form["general"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade '.($url_link == "_calendar" ? "show active" : null).'" id="academic_calendar" role="tabpanel" aria-labelledby="academic_calendar-tab2">
                                        '.($the_form["calendar"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade" id="examination" role="tabpanel" aria-labelledby="examination-tab2">
                                        '.($the_form["examination"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade '.($url_link == "_grading" ? "show active" : null).'" id="results_structure" role="tabpanel" aria-labelledby="results_structure-tab2">
                                        '.($the_form["results_structure"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade '.($url_link == "_remarks" ? "show active" : null).'" id="_remarks" role="tabpanel" aria-labelledby="_remarks-tab2">
                                        '.($the_form["_remarks"]["results_remarks"] ?? null).'
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