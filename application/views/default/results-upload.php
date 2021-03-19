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
$pageTitle = "Upload Results";
$response->title = "{$pageTitle} : {$appName}";

// specify some variables
$accessObject->userId = $session->userId;
$accessObject->clientId = $clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

// confirm that the user has the required permissions
$the_form = load_class("forms", "controllers")->terminal_reports($clientId);

// add the scripts to load
$response->scripts = ["assets/js/grading.js", "assets/js/results.js"];

// get the list of all classes
$report_param = (object) [
    "userData" => $defaultUser,
    "clientId" => $clientId,
];
$reports_list = load_class("terminal_reports", "controllers")->reports_list($report_param)["data"];

$terminal_reports_list = "";
foreach($reports_list as $key => $report) {

    $action = "<a href='{$baseUrl}results-review/{$report->report_id}' title='Click to view the details of this report' class='btn mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    if(($report->created_by == $defaultUser->user_id) && ($report->status == "Pending")) {
        $action .= " <a onclick='return modify_report_result(\"submit\",\"{$report->report_id}\")' href='#' title='Submit this terminal report to Admin for Review and Approval' class='btn mb-1 btn-outline-success'><i class='fa fa-check'></i></a>";
    }
    $terminal_reports_list .= "
    <tr>
        <td>".($key+1)."</td>
        <td>{$report->class_name}</td>
        <td>{$report->course_name} ({$report->course_code})</td>
        <td>{$report->academic_year}</td>
        <td>{$report->academic_term}</td>
        <td>
            <div>{$report->fullname}</div>
            <div class='font-weight-bold'>{$report->user_unique_id}</div>
            {$report->date_created}
        </td>
        <td>{$myClass->the_status_label($report->status)}</td>
        <td align='center'>{$action}</td>
    </tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
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
                                    <a class="nav-link active" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">Upload Report Sheet</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="upload_reports-tab2" data-toggle="tab" href="#upload_reports" role="tab" aria-selected="true">Results List</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                    '.($the_form["general"] ?? null).'
                                </div>
                                <div class="tab-pane fade" id="upload_reports" role="tabpanel" aria-labelledby="upload_reports-tab2">
                                    <div class="table-responsive trix-slim-scroll">
                                        <table class="table table-bordered datatable">
                                            <thead>
                                                <th></th>
                                                <th>Class Name</th>
                                                <th>Course Name / Code</th>
                                                <th>Academic Year</th>
                                                <th>Academic Term</th>
                                                <th>Created Details</th>
                                                <th>Status</th>
                                                <th></th>
                                            </thead>
                                            <tbody>'.$terminal_reports_list.'</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
    
// print out the response
echo json_encode($response);
?>