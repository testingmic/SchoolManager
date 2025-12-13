<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser, $academicSession, $isTeacher, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Upload Results";
$response->title = $pageTitle;

if(!$isTeacher && !$isAdmin) {
    $response->html = page_not_found("permission_denied");
} else {

    // confirm that the user has the required permissions
    $the_form = load_class("forms", "controllers")->terminal_reports($clientId);

    // add the scripts to load
    $response->scripts = ["assets/js/grading.js", "assets/js/results.js"];

    // set the url
    $url_link = $SITEURL[1] ?? null;

    // check if the user has the permission to view the results
    $showResults = isset($_GET["show_results"]) ? $_GET["show_results"] : false;
    $showResults = (bool)($url_link == "results");

    // get the list of all classes
    $report_param = (object) [
        "userData" => $defaultUser,
        "client_data" => $defaultUser->client,
        "clientId" => $clientId,
    ];
    $results_list = load_class("terminal_reports", "controllers", $report_param)->results_list($report_param)["data"];

    $terminal_reports_list = "";

    $response->array_stream['url_link'] = "results-upload/";

    $cards_view = "<div class='row'>";

    foreach($results_list as $key => $report) {

        $action = "<a href='{$baseUrl}results-review/{$report->report_id}?show_results=true' title='Click to view the details of this report' class='btn mb-1 btn-sm btn-outline-primary'>Review <i class='fa fa-eye'></i></a>";
        if((($report->created_by == $defaultUser->user_id) || ($report->teacher_ids == $defaultUser->unique_id)) && ($report->status == "Pending")) {
            $action .= " <a onclick='return modify_report_result(\"Submit\",\"{$report->report_id}\")' href='#' title='Submit this terminal report to Admin for Review and Approval' class='btn btn-sm mb-1 btn-outline-success'>
                <i class='fa fa-check'></i> Submit</a>";
        }
        $terminal_reports_list .= "
        <tr>
            <td>".($key+1)."</td>
            <td width='12%'><span class='user_name' onclick='return load(\"class/{$report->class_id}\");'>".strtoupper($report->class_name)."</span></td>
            <td width='15%'><span class='user_name' onclick='return load(\"course/{$report->course_id}\");'>{$report->course_name} ({$report->course_code})</span></td>
            <td width='15%' class='text-center'>{$report->academic_year}</td>
            <td width='15%' class='text-center'>{$report->academic_term}</td>
            <td width='16%'>
                <div>{$report->fullname}</div>
                <div class='font-weight-bold'>{$report->user_unique_id}</div>
                {$report->date_created}
            </td>
            <td width='10%'>{$myClass->the_status_label($report->status)}</td>
            <td align='center' width='16%'>{$action}</td>
        </tr>";

        $cards_view .= "
        <div class='col-12 col-md-6 col-lg-3 pr-2 pl-2'>
        <div class='card'>
            <div class='card-body p-2'>
                <div><strong>Class:</strong> <span class='float-right text-muted'>{$report->class_name}</span></div>
                <div><strong>Subject:</strong> <span class='float-right text-muted'>{$report->course_name} ({$report->course_code})</span></div>
                <div><strong>Academic Year:</strong> <span class='float-right text-muted'>{$report->academic_year}</span></div>
                <div><strong>Academic Term:</strong> <span class='float-right text-muted'>{$report->academic_term}</span></div>
                <div><strong>Date Created:</strong> <span class='float-right text-muted'>{$report->date_created}</span></div>
                <div><strong>Status:</strong> <span class='float-right text-muted'>{$myClass->the_status_label($report->status)}</span></div>
                <div class='text-center mt-2 border-top border-primary pt-3'>{$action}</div>
            </div>
        </div>
        </div>";
    }

    $cards_view .= "</div>";

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-chart-pie"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body p-1">
                            <div class="padding-20">
                                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link '.(!$showResults ? 'active' : '').'" onclick="return appendToUrl(\'upload\')" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">Upload Result</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link '.($showResults ? 'active' : '').'" onclick="return appendToUrl(\'results\')" id="upload_reports-tab2" data-toggle="tab" href="#upload_reports" role="tab" aria-selected="true">Results List</a>
                                    </li>
                                </ul>
                                <div class="tab-content tab-bordered" id="myTab3Content">
                                    <div class="tab-pane fade '.(!$showResults ? 'show active' : '').'" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                        '.($the_form["general"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade '.($showResults ? 'show active' : '').'" id="upload_reports" role="tabpanel" aria-labelledby="upload_reports-tab2">
                                    '.($isTutor ? $cards_view : 
                                    '<div class="table-responsive trix-slim-scroll">
                                        <table class="table table-sm table-bordered table-striped raw_datatable">
                                            <thead>
                                                <th></th>
                                                <th>Class</th>
                                                <th>Subject</th>
                                                <th class="text-center">Academic Year</th>
                                                <th class="text-center">'.$academicSession.'</th>
                                                <th>Details</th>
                                                <th width="10%">Status</th>
                                                <th></th>
                                            </thead>
                                            <tbody>'.$terminal_reports_list.'</tbody>
                                        </table>
                                    </div>').'
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';
}
    
// print out the response
echo json_encode($response);
?>