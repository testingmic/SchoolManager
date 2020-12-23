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

    // append to the scripts
    $response->scripts = ["assets/js/add_question.js"];

    // assign it into the array string
    $session->assignment_uploadID = $assignment_id;

    // get the questions list for this assignment
    $questions_list = "<table class='table table-bordered'>";
    $questions_list .= "<thead>";
    $questions_list .= "<tr>";
    $questions_list .= "<th width='5%'>#</th>";
    $questions_list .= "<th width='80%'>Question Content</th>";
    $questions_list .= "<th></th>";
    $questions_list .= "</tr>";
    $questions_list .= "</thead>";
    $questions_list .= "<tbody>";

    // make a query for the questions list
    $questions_query = $myClass->pushQuery("id, item_id, question", "assignments_questions", "assignment_id='{$assignment_id}' AND deleted='0'");

    // loop through the questions list
    if(!empty($questions_query)) {
        foreach($questions_query as $key => $question) {
            $questions_list .= "
            <tr data-row_id='{$question->item_id}'>
                <td>{($key+1)}</td>
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
            <div class="col-md-5">
                <h6>Questions List</h6>
                <div id="added_questions_list">
                    '.$questions_list.'
                </div>
            </div>
            <div class="col-md-7">
                <div id="full_question_detail" data-question_id="'.($data->id ?? null).'">
                    '.$formClass->add_question_form($assignment_id, $data).'
                </div>
            </div>
        </div>';
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