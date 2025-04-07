<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser, $accessObject, $defaultClientData, $academicSession;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Review Result";
$response->title = $pageTitle;

// get the report id
$result_id = $SITEURL[1] ?? null;

// return error if the report id was not parsed
if(empty($result_id)) {
    $response->html = page_not_found();
} else {

    // get the list of all classes
    $report_param = (object) [
        "userData" => $defaultUser,
        "client_data" => $defaultUser->client,
        "clientId" => $clientId,
        "result_id" => $result_id,
        "show_scores" => true,
        "limit" => 1
    ];

    // create new object
    $reportObj = load_class("terminal_reports", "controllers", $report_param);

    // load the reports list
    $reports_list = $reportObj->results_list($report_param)["data"];

    // return error if the report was not found
    if(empty($reports_list)) {
        $response->html = page_not_found();
    } else {

        // get the first item
        $data = $reports_list[0];

        // confirm that the teacher's name is empty
        if(empty($data->teachers_name)) {
            
            // quick update if the teacher id is empty
            if(empty($data->teacher_ids)) {
                $myschoolgh->query("UPDATE grading_terminal_logs SET teacher_ids = '{$defaultUser->unique_id}' WHERE report_id='{$result_id}' LIMIT 1");
            }

            // run the cron job
            $reportObj->run_result_cron_job($result_id, $clientId);
            
            // load the reports list
            $reports_list = $reportObj->results_list($report_param)["data"];

            // get the first item
            $data = $reports_list[0];
        }
        
        // scripts to load
        $response->scripts = ["assets/js/results.js"];

        // scores list
        $headers = [];
        $scores_list = "";
        $scores_array = [];

        // check the user permissions
        $hasModify = $accessObject->hasAccess("modify", "results");
        $hasApprove = $accessObject->hasAccess("approve", "results");

        // set the scores
        $isSubmitted = (bool) in_array($data->status, ["Submitted"]);
        $isApproved = (bool) in_array($data->status, ["Approved", "Cancelled"]);
        $isOwner = (bool) ($data->created_by == $defaultUser->user_id) || ($data->teacher_ids == $defaultUser->unique_id);
        
        // set some new variables
        $headers["total_raw_score"] = 0;
        $structured_list = "";

        // grading_structure
        if(!empty($defaultClientData->grading_structure)) {
            // if the columns are not empty
            if(isset($defaultClientData->grading_structure->columns)) {
                // loop through the columns
                foreach($defaultClientData->grading_structure->columns as $key => $column) {
                    $headers["total_raw_score"] += $column->markscap;
                    $headers["column"][$key] = [
                        "raw_score" => $column->markscap,
                        "percentage" => $column->percentage,
                    ];
                    $ikey = strtolower($key == 'School Based Assessment' ? 'sba' : $key);
                    $simplified_sba[$ikey] = $column->percentage;
                    $structured_list .= "
                    <tr>
                        <td width='50%'><label>{$key}</label></td>
                        <td class='text-right p-r-10'>
                            <span class='font-weight-bold font-20' data-grade_name='{$ikey}' data-grade_percentage='{$column->percentage}'>
                                {$column->percentage}%
                            </span>
                        </td>
                    </tr>";
                }
            }
        }

        // get the grading_sba
        $grading_sba = $defaultClientData->grading_sba ?? [];
    
        // restructure the grading
        foreach($grading_sba as $key => $column) {
            $simplified_sba[str_ireplace(" ", "_", strtolower($key))] = $column['percentage'];
        }

        // loop through the scores list
        foreach($data->scores_list as $key => $score) {

            // set the scores
            $is_disabled = in_array($score->status, ["Submitted", "Saved"]) && $hasModify ? null : "disabled='disabled'";
            
            // set the disabled feature
            if($isSubmitted && !$hasApprove) {
                $is_disabled = "disabled='disabled'";
            }

            // marks list
            $marks_list = "";
            $total_percentage_score = 0;

            // loop through the scores list
            foreach($score->scores as $s_key => $marks) {

                if(in_array($s_key, ["sba", "marks"])) continue;

                // append the key to it
                $scores_array[] = $s_key;
                $clean_key = ucwords(str_ireplace("_", " ", $s_key));

                // set the values
                $percent = $headers["column"]["{$clean_key}"]["percentage"] ?? 0;
                $rawscore = $headers["column"]["{$clean_key}"]["raw_score"] ?? 0;

                $simp_value = $simplified_sba[$s_key] ?? 0;
                $marks = $marks > $simp_value ? $simp_value : $marks;

                // append to the item
                $marks_list .= "
                <td align='center'>
                    ".(!$is_disabled ?
                        "<input data-result_student_id='{$score->student_item_id}' min='0' ".(!$is_disabled ? "data-input_type_q='marks' data-max_value='{$simp_value}' data-input_row_id='{$score->student_row_id}'" : "disabled='disabled'")." type='number' data-input_name='{$s_key}' data-input_type='score' style='width:7rem' value='{$marks}' class='form-control text-center'>"
                        : "<span>{$marks}</span>"
                    )."
                </td>";

                // calculate the percentage
                $raw = $headers["column"]["{$clean_key}"]["raw_score"] ?? 0;
                $cap = $headers["column"]["{$clean_key}"]["percentage"] ?? 0;
                $percent = $raw ? round((($marks * $cap) / $raw), 2) : 0;
                $total_percentage_score += $percent;
            }

            $sbaScore = $score->scores["sba"] ?? 0;
            $sbaPercentage = $sbaScore > $simplified_sba["sba"] ? $simplified_sba["sba"] : $sbaScore;

            $examsScore = $score->scores["marks"] ?? 0;
            $examsPercentage = $examsScore > $simplified_sba["examination"] ? $simplified_sba["examination"] : $examsScore;

            // calculate the total percentage
            $totalPercentage = ($sbaPercentage ?? 0) + ($examsPercentage ?? 0);
            $totalPercentage = $totalPercentage > 100 ? 100 : $totalPercentage;

            // append to the scores
            $scores_list .= "
            <tr data-result_row_id='{$score->report_id}_{$score->student_row_id}' data-result_student_id='{$score->student_item_id}'>
                <td width='5%'>".($key+1)."</td>
                <td>
                    ".strtoupper($score->student_name)." <br>
                    <strong class='text-primary'>{$score->student_unique_id}</strong>
                </td>
                ".$marks_list."
                <td align='center'>
                    <span class='font-20' data-result_student_id='{$score->student_item_id}' data-input_row_id='{$score->student_row_id}' data-school_based_assessment='{$sbaPercentage}'>{$sbaPercentage}</span>
                </td>
                <td align='center'>
                    <span class='font-20' data-result_student_id='{$score->student_item_id}' data-input_row_id='{$score->student_row_id}' data-examination='{$examsPercentage}'>{$examsPercentage}</span>
                </td>
                <td align='center'>
                    <span class='font-20' data-result_student_id='{$score->student_item_id}' data-input_row_id='{$score->student_row_id}' data-student_percentage='{$totalPercentage}'>{$totalPercentage}%</span>
                </td>
                <td>
                ".(!$is_disabled ? 
                    "<input {$is_disabled} type='text' data-input_method='remarks' data-input_type='score' style='width:13rem' data-input_row_id='{$score->student_row_id}' class='form-control' value='{$score->class_teacher_remarks}'>"
                    : $score->class_teacher_remarks
                )."
                </td>";
                // if the result has not yet been approved
                if(!$isApproved && !$is_disabled) {
                    $scores_list .= "
                    <td width='100px' class='text-center'>
                        ".(!$is_disabled && $hasModify ? "<span data-input_save_button='{$score->student_row_id}' onclick='return save_result(\"{$score->student_row_id}\",\"student\",\"{$score->report_id}\");' title='Save Student Marks' class='btn mb-2 hidden btn-sm btn-outline-success'><i class='fa fa-save'></i></span>" : null)."
                        ".(!$is_disabled && $hasApprove ? "<span data-input_approve_button='{$score->student_row_id}' onclick='return modify_result(\"approve\",\"{$score->report_id}_{$score->student_row_id}\");' title='Approve this Mark' class='btn btn-sm btn-outline-primary'><i class='fa fa-check-circle'></i></span>" : null)."
                    </td>";
                } else {
                    $scores_list .= "
                    <td class='text-center'>
                        <span title='Save Student Marks' class='badge p-1 badge-success'>Approved</span>
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

        // set the disabled feature
        if(($isSubmitted && !$hasApprove) || ($isApproved && $hasApprove)) {
            $response->scripts = [];
        }

        if($isApproved) {
            $response->scripts = [];
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
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-header pr-2 pl-2">
                            <h4>SUBJECT INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0 pr-2 pl-2">
                            <div class="py-2">
                                <p class="clearfix">
                                    <span class="float-left">Class Name</span>
                                    <span class="float-right user_name" title="View Class Record" onclick="return load(\'class/'.$data->class_id.'\');">'.$data->class_name.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Subject</span>
                                    <span class="float-right user_name" title="View Subject Record" onclick="return load(\'course/'.$data->course_id.'\');">'.$data->course_name.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Subject Code</span>
                                    <span class="float-right text-muted">'.$data->course_code.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Status</span>
                                    <span class="float-right text-muted">'.$myClass->the_status_label($data->status).'</span>
                                </p>
                                <div class="width-100">
                                    <table width="100%" class="table table-bordered">
                                        '.$structured_list.'
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="card">
                        <div class="card-header pr-2 pl-2">
                            <h4>ACADEMIC INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0 pr-2 pl-2">
                            <div class="py-2">
                                <p class="clearfix">
                                    <span class="float-left">Academic Year</span>
                                    <span class="float-right text-muted">'.$data->academic_year.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Academic '.$academicSession.'</span>
                                    <span class="float-right text-muted">'.$data->academic_term.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Teachers Name</span>
                                    <span class="float-right text-muted text-uppercase">'.$data->fullname.' 
                                        <i onclick="return load(\'staff/'.$data->created_by.'/documents\');" class="fa btn btn-outline-success btn-sm fa-eye"></i>
                                    </span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Date Created</span>
                                    <span class="float-right text-muted">'.$data->date_created.'</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-header pr-2 pl-2">
                            <h4>SUMMARY DATA</h4>
                        </div>
                        <div class="card-body pt-0 pb-0 pr-2 pl-2">
                            <div class="pt-3">
                                <p class="clearfix">
                                    <span class="float-left">Students Count</span>
                                    <span class="float-right text-muted">'.$data->students_count.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Average Score</span>
                                    <span class="float-right text-muted">'.number_format($data->overall_score, 2).'</span>
                                </p>
                                '.(!$isApproved && $isOwner && !$isSubmitted ? '
                                <p class="clearfix text-center mt-3 border-top pt-3">
                                    <span onclick="return modify_report_result(\'Submit\',\''.$data->report_id.'\')" class="btn btn-outline-success">SUBMIT RESULT</span>
                                </p>': '').'
                            </div>
                            <div class="mb-3 border-top pt-3 text-center">
                                <a href="'.$baseUrl.'results-upload/list?show_results=true" class="btn btn-outline-primary">
                                    <i class="fa fa-arrow-left"></i> Back to Results List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="books_request_details">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h4>STUDENT RESULTS LIST</h4>
                                </div>
                                <div>
                                    '.($hasModify  && !$isApproved && !$isSubmitted ? "<span data-input_save_button='{$data->report_id}' onclick='return save_result(\"$data->report_id\",\"results\");' title='Save Student Marks' class='btn btn-outline-success'><i class='fa fa-save'></i> Save</span>" : null).'
                                    '.($hasApprove && !$isApproved && $isSubmitted ? "<span data-input_save_button='{$data->report_id}' onclick='return save_result(\"$data->report_id\",\"results\");' title='Save Student Marks' class='btn btn-outline-success'><i class='fa fa-save'></i> Save</span>" : null).'
                                    '.($isSubmitted && $hasApprove && !$isApproved ? "<span data-input_approve_button='{$data->report_id}' onclick='return save_result(\"{$data->report_id}\",\"approve_results\");' title='Approve this Mark' class='btn btn-outline-primary'><i class='fa fa-check-circle'></i> Approve</span>" : null).'
                                </div>
                            </div>
                            <div class="table-responsive trix-slim-scroll">
                                <table width="100%" class="table table-bordered">
                                    <thead>
                                        <th width="5%"></th>
                                        <th width="20%">Student Name / ID</th>
                                        '.$scores_header.'
                                        <th class="text-center">SBA</th>
                                        <th class="text-center">Examination</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Remarks</th>
                                        <th></th>
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