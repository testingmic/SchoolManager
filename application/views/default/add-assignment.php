<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Create Assignment";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [
    "assets/js/upload.js", 
    "assets/js/assignments.js"
];

// create a new object
$formClass = load_class("forms", "controllers");
$assignmentClass = load_class("assignments", "controllers");

// the query parameter to load the user information
$item_param = (object) [
    "clientId" => $clientId,
    "assignment_id" => null,
    "userData" => $defaultUser,
    "data" => null,
    "limit" => 1
];

// initials
$the_form = "";

// if the question set id was parsed
if(isset($_GET["qid"]) && !empty($_GET["qid"]) || !empty($session->assignment_uploadID)) {
    // append to the question
    $pageTitle .= " - Review Questions";

    // data object values
    $data = (object) [];

    // assignment id
    $assignment_id = xss_clean($_GET["qid"]);

    // get the assignment details
    $item_param->assignment_id = $assignment_id;
    $ass_data = $assignmentClass->list($item_param);

    // if empty then show the error page
    if(empty($ass_data["data"])) {
        $response->html = page_not_found();
    } else {
        
        // get the item
        $ass_data = $ass_data["data"][0];

        // append to the scripts
        $response->scripts = ["assets/js/add_question.js"];

        // assign it into the array string
        $session->assignment_uploadID = $assignment_id;

        // get the questions list for this assignment
        $questions_list = "<table class='table table-bordered'>";
        $questions_list .= "<thead>";
        $questions_list .= "<tr>";
        $questions_list .= "<th width='8%'>#</th>";
        $questions_list .= "<th width='75%'>Question Content</th>";
        $questions_list .= "<th></th>";
        $questions_list .= "</tr>";
        $questions_list .= "</thead>";
        $questions_list .= "<tbody id='added_questions'>";

        // parameters to load the assignment information
        $params = (object) [
            "clientId" => $clientId,
            "assignment_id" => $assignment_id
        ];

        // make a query for the questions list
        $questions_query = load_class("assignments", "controllers")->questions_list($params);

        // loop through the questions list
        if(!empty($questions_query)) {
            foreach($questions_query as $key => $question) {
                $ii = $key+1;
                $questions_list .= "
                <tr data-row_id='{$question->item_id}'>
                    <td>{$ii}</td>
                    <td>{$question->question}</td>
                    <td>
                        <button class='btn btn-outline-success btn-sm' onclick='return review_AssignmentQuestion(\"{$question->item_id}\")'><i class='fa fa-edit'></i></button>
                        <button class='btn btn-outline-danger btn-sm' onclick='return remove_AssignmentQuestion(\"{$question->item_id}\")'><i class='fa fa-trash'></i></button>
                    </td>                
                </tr>";
            }
        } else {
            $questions_list .= "<tr>
                <td colspan='3' class='font-italic text-center'>No questions have been uploaded for this assignment</td>
            </tr>";
        }
        $questions_list .= "</tbody>";
        $questions_list .= "</table>";

        // the form
        $the_form = '
            <div class="row" id="add_question_container">
                <div class="col-md-5 mb-4 pr-3">
                    <div class="card-body p-0">
                        <div class="pb-0 pt-0">
                            <h6>ASSIGNMENT SUMMARY DETAILS</h6>
                            <p class="clearfix">
                                <span class="float-left">Course Name</span>
                                <span class="float-right text-muted text-right">
                                    '.($ass_data->course_name ?? null).'<br>
                                    '.$ass_data->class_name.'
                                </span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Assigned To</span>
                                <span class="float-right text-muted">'.($ass_data->assigned_to == "selected_students" ? "{$ass_data->students_assigned} Students" : "Entire Class").'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Grade</span>
                                <span class="float-right text-muted">'.($ass_data->grading ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Submission Date</span>
                                <span class="float-right text-muted">'.date("jS F Y", strtotime($ass_data->due_date)).'</span>
                            </p>
                        </div>
                        <div class="text-right mb-4 border-bottom pb-3">
                            <a href="'.$baseUrl.'update-assignment/'.$assignment_id.'/view" class="btn btn-outline-success btn-sm"><i class="fa fa-edit"></i> Update</a>
                        </div>
                    </div>
                    <h6>QUESTIONS LIST</h6>
                    <div id="added_questions_list" class="table-responsive">
                        '.$questions_list.'
                    </div>
                </div>
                <div class="col-md-7">
                    <div id="full_question_detail" data-question_id="'.($data->item_id ?? null).'">
                        '.$formClass->add_question_form($assignment_id, $data).'
                    </div>
                </div>
            </div>';
    }

} else {
    // unset the session
    $session->remove("assignment_uploadID");

    // set the form
    $the_form = $formClass->create_assignment($item_param);
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-assignments">Assignments List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">'.$the_form.'</div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>