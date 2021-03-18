<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Review Result";
$response->title = "{$pageTitle} : {$appName}";

// get the report id
$report_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

// return error if the report id was not parsed
if(empty($report_id)) {
    $response->html = page_not_found();
} else {

    // create new object
    $reportObj = load_class("terminal_reports", "controllers");
        
    // add the scripts to load
    $response->scripts = ["assets/js/grading.js"];

    // get the list of all classes
    $report_param = (object) [
        "userData" => $defaultUser,
        "clientId" => $clientId,
        "report_id" => $report_id,
        "show_scores" => true
    ];
    $reports_list = $reportObj->reports_list($report_param)["data"];

    // return error if the report was not found
    if(empty($reports_list)) {
        $response->html = page_not_found();
    } else {
        // get the first item
        $data = $reports_list[0];
        // print_r($data);

        // set the report information
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'upload-results/list">Results List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row" id="books_request_details">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>RESULT INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-2">
                                <p class="clearfix">
                                    <span class="float-left">Uploaded By</span>
                                    <span class="float-right text-muted">'.$data->fullname.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Date Created</span>
                                    <span class="float-right text-muted">'.$data->date_created.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Class Name</span>
                                    <span class="float-right text-muted">'.$data->class_name.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Course Title</span>
                                    <span class="float-right text-muted">'.$data->course_name.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Course Code</span>
                                    <span class="float-right text-muted">'.$data->course_code.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Academic Year</span>
                                    <span class="float-right text-muted">'.$data->academic_year.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Academic Term</span>
                                    <span class="float-right text-muted">'.$data->academic_term.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Status</span>
                                    <span class="float-right text-muted">'.$myClass->the_status_label($data->status).'</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body pt-0 pb-0">
                            <div class="py-4">
                                <p class="clearfix">
                                    <span class="float-left">Students Count</span>
                                    <span class="float-right text-muted">'.$data->students_count.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Average Score</span>
                                    <span class="float-right text-muted">'.$data->overall_score.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Course Code</span>
                                    <span class="float-right text-muted">'.$data->course_code.'</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>';
        
    }
}

// print out the response
echo json_encode($response);