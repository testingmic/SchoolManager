<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser, $accessObject;

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

    // get the list of all classes
    $report_param = (object) [
        "userData" => $defaultUser,
        "client_data" => $defaultUser->client,
        "clientId" => $clientId,
        "report_id" => $report_id,
        "show_scores" => true
    ];

    // scripts to load
    $response->scripts = ["assets/js/results.js"];

    // create new object
    $reportObj = load_class("terminal_reports", "controllers", $report_param);

    // load the reports list
    $reports_list = $reportObj->reports_list($report_param)["data"];

    // return error if the report was not found
    if(empty($reports_list)) {
        $response->html = page_not_found();
    } else {
        // get the first item
        $data = $reports_list[0];

        // scores list
        $scores_list = "";
        $scores_array = [];

        // check the user permissions
        $modifyResult = $accessObject->hasAccess("modify", "results");
        $approveResult = $accessObject->hasAccess("approve", "results");

        // set the scores
        $isApproved = (bool) in_array($data->status, ["Approved", "Cancelled"]);

        // loop through the scores list
        foreach($data->scores_list as $key => $score) {

            // set the scores
            $is_disabled = in_array($score->status, ["Submitted", "Saved"]) && $modifyResult ? null : "disabled='disabled'";
            
            // marks list
            $marks_list = "";

            // loop through the scores list
            foreach($score->scores as $s_key => $s_value) {
                $scores_array[] = $s_key;
                $marks_list .= "
                <td>
                    <input ".(!$is_disabled ? "data-input_type_q='marks' data-input_row_id='{$score->student_item_id}'" : "disabled='disabled'")." type='number' data-input_name='{$s_key}' data-input_type='score' style='width:7rem' value='{$s_value}' class='form-control text-center'>
                </td>";
            }
            // append to the scores
            $scores_list .= "
            <tr data-result_row_id='{$score->report_id}_{$score->student_item_id}'>
                <td>".($key+1)."</td>
                <td>
                    {$score->student_name} <br>
                    <strong class='text-primary'>{$score->student_unique_id}</strong>
                </td>
                ".$marks_list."
                <td>
                    <input type='number' style='width:7rem' value='{$score->total_score}' disabled='disabled' data-input_total_id='{$score->student_item_id}' class='form-control text-center'>
                </td>
                <td>
                    <input {$is_disabled} type='text' data-input_method='remarks' data-input_type='score' style='width:13rem' data-input_row_id='{$score->student_item_id}' class='form-control' value='{$score->class_teacher_remarks}'>
                </td>";
                // if the result has not yet been approved
                if(!$isApproved) {
                    $scores_list .= "<td>
                        ".(!$is_disabled && $modifyResult ? "<span data-input_save_button='{$score->student_item_id}' onclick='return save_result(\"$score->student_item_id\",\"student\");' title='Save Student Marks' class='btn mb-2 hidden btn-sm btn-outline-success'><i class='fa fa-save'></i></span>" : null)."
                        ".(!$is_disabled && $approveResult ? "<span data-input_approve_button='{$score->student_item_id}' onclick='return modify_result(\"approve\",\"{$score->report_id}_{$score->student_item_id}\");' title='Approve this Mark' class='btn btn-sm btn-outline-primary'><i class='fa fa-check-circle'></i></span>" : null)."
                    </td>";
                }
            $scores_list .= "</tr>";
        }
        $scores_array = array_unique($scores_array);
        $scores_header = "";

        foreach($scores_array as $header) {
            $header = ucwords(str_ireplace("_", " ", $header));
            $scores_header .= "<th>{$header}</th>";
        }

        // set the report information
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'results-upload/list">Results List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row" id="books_request_details">
                <div class="col-12 col-md-12 col-lg-3">
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
                <div class="col-12 col-md-12 col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h5>Student Results List</h5>
                                </div>
                                <div>
                                    '.($modifyResult && !$isApproved ? "<span data-input_save_button='{$data->report_id}' onclick='return save_result(\"$data->report_id\",\"results\");' title='Save Student Marks' class='btn btn-outline-success'><i class='fa fa-save'></i> Save</span>" : null).'
                                    '.($approveResult && !$isApproved ? "<span data-input_approve_button='{$data->report_id}' onclick='return save_result(\"{$data->report_id}\",\"approve_results\");' title='Approve this Mark' class='btn btn-outline-primary'><i class='fa fa-check-circle'></i> Approve</span>" : null).'
                                </div>
                            </div>
                            <div class="table-responsive trix-slim-scroll">
                                <table class="table table-bordered datatable">
                                    <thead>
                                        <th width="10%"></th>
                                        <th>Student Name / ID</th>
                                        '.$scores_header.'
                                        <th>Total Score</th>
                                        <th>Remarks</th>
                                        '.(!$isApproved ? "<th></th>" : "").'
                                    </thead>
                                    <tbody>'.$scores_list.'</tbody>
                                </table>
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