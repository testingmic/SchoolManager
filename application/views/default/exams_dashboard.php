<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$userId = $session->userId;

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];


$pageTitle = "Exams Bank - Summary";
$response->title = $pageTitle;
$response->scripts = [];

$params = (object) ["clientId" => $clientId];

$quick_history = load_class("quiz", "controllers")->quiz_history($params);

$quizModerator = false;

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-book"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        
        <div class="row" id="load_user_quiz_analytics" data-stream="summary,pending_quiz">
            <div class="col-xl-3 col-md-6">
                <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-blue border-left-solid">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-blue mb-1">Quiz Taken</div>
                                <div class="h5" data-count="quiz_taken">N/A</div>
                            </div>
                            <div class="ml-2"><i class="fa fa-book fa-3x text-gray-200"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-top-0 border-bottom-0 border-right-0 border-left-solid border-left-lg border-purple">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-purple mb-1">Quiz Pending</div>
                                <div class="h5" data-count="quiz_pending">N/A</div>
                            </div>
                            <div class="ml-2"><i class="fa fa-tags fa-3x text-gray-200"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-top-0 border-bottom-0 border-right-0 border-left-solid border-left-lg border-green">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-green mb-1">Average Score</div>
                                <div class="h5" data-count="quiz_average_score">N/A</div>
                            </div>
                            <div class="ml-2"><i class="fa fa-chart-bar fa-3x text-gray-200"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-top-0 border-bottom-0 border-right-0 border-left-solid border-left-lg border-yellow">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-yellow mb-1">Total Time Used</div>
                                <div class="h5" data-count="quiz_duration">N/A</div>
                            </div>
                            <div class="ml-2"><i class="fa fa-clock fa-3x text-gray-200"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 mb-4">
                <div class="card">
                    <div class="card-header">Most Recents Tests Undertaken</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        '.($isTeacher || $isAdmin ?
                                            '<th>Student Name</th>' : null
                                         ).'
                                        <th width="22%">Quiz Title</th>
                                        <th>Quiz Marks</th>
                                        <th>Duration</th>
                                        <th width="20%">Date</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    
                                    foreach($quick_history["data"] as $key => $result) {
                                        $response->html .= '
                                        <tr>
                                            <td class="text-center">'.($key+1).'</td>
                                            '.($quizModerator ?
                                                '<th>
                                                    <a data-toggle="tooltip" href="<?= $baseUrl.("tests.performance?uid={$result->user_guid}&rid={$result->record_id}") ?>" title="View detailed records of this test undertaken by <?= $result->name; ?>"><?= $result->name; ?>
                                                    </a>
                                                </th>': null
                                            ).'
                                            <td>
                                                '.$result->test_title.''.(!$result->test_title ? ", {$result->category_name}" : $result->category_name).'
                                            </td>
                                            <td>'.$result->correct.'/<sub class="font-17">
                                                '.($result->correct + $result->wrong).'</sub></td>
                                            <td class="d-none d-sm-table-cell">
                                                '.(secondsToTime($result->duration)).'
                                            </td>
                                            <td>'.date("D, jS F, Y", strtotime($result->test_date)).'</td>
                                            <td><button class="btn btn-sm btn-outline-success" onclick="return (\'exams_result/'.$result->item_id.'"><i class="fa fa-chart-bar"></i> &nbsp; Review</button></td>
                                        </tr>';
                                    }
                $response->html .= '</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>';
// print out the response
echo json_encode($response);
?>