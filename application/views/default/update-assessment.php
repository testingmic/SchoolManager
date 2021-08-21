<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $isTutorAdmin, $isWardParent, $isAdmin;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Assignment Details";
$response->title = "{$pageTitle} : {$appName}";

$response->scripts = [
    "assets/js/assignments.js",
    "assets/js/comments.js",
];

// update the assignment permission
$hasUpdate = $accessObject->hasAccess("update", "assignments");

// item id
$item_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "assignment_id" => $item_id,
        "userData" => $defaultUser,
        "limit" => 1
    ];

    $assignmentClass = load_class("assignments", "controllers");
    $data = $assignmentClass->list($item_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];
        $item_param->data = $data;

        // guardian information
        $isGraded = isset($data->awarded_mark) ? true : false;
        $isActive = in_array($data->state, ["Graded", "Pending", "Answered"]);
        $isMultipleChoice =  (bool) ($data->questions_type == "multiple_choice");

        $data->isGraded = $isGraded;
        $data->hasUpdate = $hasUpdate;

        // is manual or auto insertion
        $isAuto = (bool) ($data->insertion_mode === "Auto");

        // get the assignment form
        $the_form = load_class("forms", "controllers")->create_assignment($item_param, "update_assignment");

        // student update permissions
        $grading_info = "<div class='row' id='assignment_question_detail'>";

        // get the list of students
        if($isTutorAdmin) {

            // append the upload script
            if($isActive && !$isMultipleChoice) {
                $response->scripts[] = "assets/js/upload.js";
            }

            // if the question is a multiple choice question set
            if($isMultipleChoice) {

                // parameters to load the assignment information
                $params = (object) [
                    "clientId" => $clientId,
                    "columns" => "a.*",
                    "assignment_id" => $item_id
                ];

                // get the questions list for this assignment
                $questions_list = "<table class='table table-bordered'>";
                $questions_list .= "<thead>";
                $questions_list .= "<tr>";
                $questions_list .= "<th width='5%'>#</th>";
                $questions_list .= "<th width='65%'>Question Content</th>";
                $questions_list .= "<th>Marks</th>";
                $questions_list .= "<th></th>";
                $questions_list .= "</tr>";
                $questions_list .= "</thead>";
                $questions_list .= "<tbody id='added_questions'>";

                // make a query for the questions list
                $questions_query = load_class("assignments", "controllers")->questions_list($params);
                $questions_array = [];

                // loop through the questions list
                if(!empty($questions_query)) {
                    // init the marks
                    $marks = 0;

                    // loop through the questions list
                    foreach($questions_query as $key => $question) {
                        $ii = $key+1;
                        $questions_array[$question->item_id] = $question;
                        $marks += $question->marks;

                        $questions_list .= "
                        <tr data-row_id='{$question->item_id}'>
                            <td>{$ii}</td>
                            <td>{$question->question}</td>
                            <td>{$question->marks}</td>
                            <td align='center'>";
                                if(!$isActive) {
                                    $questions_list .= "<a href='{$baseUrl}add-assessment/add_question?qid={$item_id}&q_id={$question->item_id}' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>&nbsp;";
                                }
                                $questions_list .= "<button class='btn btn-outline-primary btn-sm' onclick='return view_AssignmentQuestion(\"{$question->item_id}\")'><i class='fa fa-eye'></i></button>&nbsp;";
                                if(!$isActive) {
                                    $questions_list .= "<button class='btn btn-outline-danger btn-sm' onclick='return remove_AssignmentQuestion(\"{$item_id}\",\"{$question->item_id}\")'><i class='fa fa-trash'></i></button>";
                                }
                            $questions_list .= "</td>
                        </tr>";
                    }
                    // append the tabulated marks
                    $questions_list .= "<tr><td></td><td align='right'><strong>Total Marks:</strong></td><td><strong>{$marks}</strong></td><td></td></tr>";
                } else {
                    $questions_list .= "<tr>
                        <td colspan='3' class='font-italic text-center'>No questions have been uploaded for this assignment</td>
                    </tr>";
                }
                $questions_list .= "</tbody>";
                $questions_list .= "</table>";
                
                // append the questions list to the array to be returned
                $response->array_stream["questions_array"] = $questions_array;
            }

            // not handed in
            $nothanded_in = false;
            
            /** Get the students list */
            $students_list = ($data->assigned_to == "selected_students") ? $myClass->stringToArray($data->assigned_to_list) 
                : array_column(
                    $myClass->pushQuery(
                        "a.item_id, a.unique_id, a.name, a.email, a.phone_number, a.gender", 
                        "users a LEFT JOIN classes c ON c.id = a.class_id", 
                        "a.client_id='{$clientId}' AND c.item_id='{$data->class_id}' AND a.user_type='student' AND a.user_status='Active' AND a.status='1'"
                ), "item_id");

            /**
             * List the students to whom the assignment has been assigned to
             * 
             * @param 
             * 
             * @return Array
             */
            $the_students_list = $myschoolgh->prepare("
				SELECT item_id, unique_id, name, email, phone_number, gender, image,
                (SELECT score FROM assignments_submitted WHERE assignment_id = '{$data->item_id}' AND student_id = users.item_id) AS score,
                (SELECT b.handed_in FROM assignments_submitted b WHERE b.assignment_id = '{$data->item_id}' AND b.student_id = users.item_id) AS handed_in
                FROM users WHERE 
                client_id='{$clientId}' AND user_type='student' 
                AND user_status='Active' AND status='1' AND item_id IN ('".implode("', '", $students_list)."')
			");
			$the_students_list->execute();
            $result = $the_students_list->fetchAll(PDO::FETCH_OBJ);

            // ensure the result is not empty
            if(!empty($result)) {
                
                // if its a multi choice question or a file upload question set
                $function = $isMultipleChoice ? "review_QuizAssignment" : "load_singleStudentData";

                $grading_info .= '
                <div class="col-lg-'.($isAuto ? 7 : 9).'" id="assignment-content">
                    '.( $isActive ?
                        '<div style="margin-top: 10px;margin-bottom: 10px" align="right" class="initial_assignment_buttons">
                            <button class="btn btn-outline-danger" onclick="return close_Assignment(\''.$data->item_id.'\');"><i class="fa fa-times"></i> Close</button>
                            '.(!$isMultipleChoice ? '<button class="btn btn-outline-success" onclick="return save_AssignmentMarks();"><i class="fa fa-save"></i> Save</button>' : '').'
                        </div>' : (
                            $isAdmin && $isAuto ? '
                                <button class="btn mb-2 btn-outline-danger" onclick="return reopen_Assignment(\''.$data->item_id.'\');"><i class="fa fa-times"></i> Reopen</button>
                            ' : ''
                        )
                    ).'
                    <table width="100%" class="table-hover table mb-0">
                        <thead>
                            <th>Assigned Students List</th>
                            <th></th>
                        </thead>
                    </table>
                    <div class="slim-scroll" style="max-height: 500px; overflow-y:auto;">
                        <table width="100%" class="table-bordered table-stripped table mt-0">
                        <tbody>';
                        // loop through the list of students
                        foreach($result as $student) {
                            
                            $student->handed_in = !empty($student->handed_in) ? $student->handed_in : "Pending";
                            $isSubmitted = (bool) ($student->handed_in == "Submitted");

                            $grading_info .= '
                                <tr>
                                    <td width="65%">
                                        <div class="d-flex justify-content-start">
                                            <div class="mr-2">
                                                '.($isSubmitted ?
                                                    '<a title="Click to view document submitted by '.$student->name.'" style="text-decoration:none" class="anchor" href="javascript:void(0)" '.($isAuto ? 'onclick="return '.$function.'(\''.$student->item_id.'\',\''.$data->grading.'\',\''.$data->item_id.'\')"' : null).' data-assignment_id="'.$data->item_id.'" data-function="single-view" data-student_id="'.$student->item_id.'"  data-name="'.$student->name.'" data-score="'.round($student->score,0).'">
                                                        <img class="rounded-circle cursor author-box-picture" width="40px" src="'.$baseUrl.''.$student->image.'" alt="">
                                                    </a>' : 
                                                    '<img class="rounded-circle cursor author-box-picture" width="40px" src="'.$baseUrl.''.$student->image.'" alt="">'
                                                ).'
                                            </div>
                                            <div>
                                                <p class="p-0 m-0">
                                                    '.($isSubmitted ? '<a style="text-decoration:none" class="anchor" href="javascript:void(0)" '.($isAuto ? 'onclick="return '.$function.'(\''.$student->item_id.'\',\''.$data->grading.'\',\''.$data->item_id.'\')"' : null).' data-assignment_id="'.$data->item_id.'" data-function="single-view" data-student_id="'.$student->item_id.'"  data-name="'.$student->name.'" data-score="'.round($student->score,0).'"><strong>'.$student->name.'</strong></a>' : "<strong>{$student->name}</strong>").'
                                                </p>
                                                <p class="p-0 m-0">'.$myClass->the_status_label($student->handed_in).'</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input '.(!$isActive || $isMultipleChoice ? 'disabled="disabled"' : 'name="test_grading" data-value="'.$student->item_id.'"').' value="'.$student->score.'" type="number" data-assignment_id="'.$data->item_id.'" maxlength="'.strlen($data->grading).'" min="0" max="'.$data->grading.'" class="form-control font-16"> &nbsp; <span class="font-20">/ '.$data->grading.'</span>
                                        </div>
                                    </td>
                                </tr>';
                        }
                        $grading_info .= '
                            </tbody>
                        </table>
                    </div>
                </div>
                '.($isAuto ?
                    '<div class="col-lg-5">
                        <div class="details-content-save"></div>
                        <div class="grading-history-div" style="display:none">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td align="right">
                                            <span data-function="grading-history" class="text-primary cursor" title="View Grading History">Grading History</span>
                                            <input data-grading="'.$data->grading.'" data-assignment_id="'.$data->item_id.'" type="hidden" name="data-student-id" class="data-student-id">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card card-default student-assignment-details"></div>
                    </div>' : 
                    '<input data-grading="'.$data->grading.'" data-assignment_id="'.$data->item_id.'" type="hidden" name="data-student-id" class="data-student-id">'
                );

            } else {
               $grading_info .= '<div style="width:100%;" class="text-center">No student has handed in their answers yet.</div>'; 
            }

        }

        // if student or parent
        elseif($isWardParent) {

            // init
            $preloaded = "";
            $session->attachAssignmentDocs = true;

            // module
            $module = "assignments_handin_{$item_id}";

            // file upload parameter
            $file_params = (object) [
                "module" => $module,
                "userData" => $defaultUser,
                "item_id" => $item_id,
            ];

            // check if the use has handed in the assignment
            $nothanded_in = (bool) (($data->handed_in === "Pending") || empty($data->handed_in));

            if($nothanded_in) {
                // append the upload script
                $response->scripts[] = "assets/js/upload.js";
            }
            
            // display the content if the assignment type is a file_attachment
            if(!$isMultipleChoice && $isActive) {
                
                // display the file upload option
                $grading_info .= 
                ($nothanded_in ?
                    '<div class="col-lg-'.($nothanded_in ? 12 : 4).'" id="handin_upload">
                        <div><h5 class="text-uppercase">Upload Assignment Answer</h5></div>
                        <div class="col-lg-12" id="upload_question_set_template">
                            <div class="form-group text-center mb-1">
                                <div class="row">'.load_class("forms", "controllers")->form_attachment_placeholder($file_params).'</div>
                            </div>
                        </div>
                        <div class="text-right mb-3">
                            <a href="javascript:void(0)" onclick="return submit_Answers(\''.$item_id.'\');" class="btn anchor btn-outline-primary">
                                <i class="fa fa-save"></i> Submit Answer
                            </a>
                        </div>
                    </div>' : ''
                ).'
                <div class="col-lg-'.($nothanded_in ? 4 : 12).'" id="handin_documents">
                    '.$data->attached_attachment_html.'
                </div>';
            }

            // display the questions using an algorithm specified in the assignment class
            elseif($isMultipleChoice) {

                // parameters to load the assignment information
                $params = (object) [
                    "clientId" => $clientId,
                    "columns" => "a.*",
                    "show_answer" => true,
                    "userId" => $session->student_id,
                    "assignment_id" => $item_id
                ];
                
                // if the assignment has not yet been handed in
                if(($data->handed_in === "Pending") || empty($data->handed_in)) {

                    // set the scripts to load for this user
                    $response->scripts = ["assets/js/multichoice.js"];
                    
                    // get the questions array list
                    $questions_array_list = load_class("assignments", "controllers")->questions_list($params);
                    $questions_array = [];

                    // set the previous question id and the current question Id
                    $questions_ids = array_column($questions_array_list, "item_id");
                    $session->previousQuestionId = !empty($session->previousQuestionId) ? $session->previousQuestionId : null;
                    $session->currentQuestionId = !empty($session->currentQuestionId) ? $session->currentQuestionId : $questions_ids[0];

                    $grading_info .= $assignmentClass->current_question($questions_array_list, $session->student_id);
                
                } elseif($data->handed_in === "Submitted") {
                    $params->show_correct_answer = true;
                    $params->student_id = $session->student_id;
                    $grading_info .= $assignmentClass->review_answers($params, "p-3")["data"];

                }

            }

        }

        $grading_info .= '</div>';

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-assessment">Assessments List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center">
                        <div class="clearfix"></div>
                        <div class="author-box-name"><a href="#">'.$data->assignment_title.'</a></div>
                        <div class="author-box-name">'.$data->class_name.'</div>
                        '.(isset($data->students_assigned) ? '<div class="author-box-job">('.$data->students_assigned.' Students)</div>' : null).'
                    </div>
                </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Assignment Details</h4>
                    </div>
                    '.$assignmentClass->quick_data($data).'
                </div>
                <div class="card">
                    <div class="card-header"><h4>Created By Details</h4></div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <div class="d-flex justify-content-start">
                                <div class="mr-0">
                                    '.(isset($data->created_by_info->image) ? '<img src="'.$baseUrl.''.$data->created_by_info->image.'" class="rounded-circle cursor author-box-picture" width="50px">' : null).'
                                </div>
                                <div class="col-11">
                                    <div class="clearfix">
                                        <span class="mr-2 float-left">Fullname: </span>
                                        <span class="mr-2 float-right text-muted">'.($data->created_by_info->name ?? null).'</span>
                                    </div>
                                    <div class="clearfix">
                                        <span class="mr-2 float-left">Email: </span>
                                        <span class="mr-2 float-right text-muted">'.($data->created_by_info->email ?? null).'</span>
                                    </div>
                                    <div class="clearfix">
                                        <span class="mr-2 float-left">Contact: </span>
                                        <span class="mr-2 float-right text-muted">'.($data->created_by_info->phone_number ?? null).'</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    '.($isAuto ?
                        ($isTutorAdmin && $isMultipleChoice ? 
                            '<li class="nav-item">
                                <a class="nav-link '.(!$updateItem ? "active" : null).'" id="questions-tab2" data-toggle="tab" href="#questions" role="tab" aria-selected="true">
                                    Questions Set
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="details-tab2" data-toggle="tab" href="#details" role="tab" aria-selected="true">
                                    Additional Details
                                </a>
                            </li>
                            ' : '
                            <li class="nav-item">
                                <a class="nav-link '.(!$updateItem ? "active" : null).'" id="details-tab2" data-toggle="tab" href="#details" role="tab" aria-selected="true">
                                    Additional Details
                                </a>
                            </li>
                            '    
                        ) : null
                    ).'
                    <li class="nav-item">
                        <a class="nav-link '.(!$isAuto ? "active" : null).'" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">
                            '.($isTutorAdmin ? "Grade Students" : "Handin Assignment").'
                        </a>
                    </li>';

                    if($hasUpdate && $isAuto) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    '.((!$nothanded_in || $isGraded) ? 
                        '<li class="nav-item">
                            <a class="nav-link" id="comments-tab2" data-toggle="tab" href="#comments" role="tab" aria-selected="true">
                                Comments
                            </a>
                        </li>' : ''
                    ).'
                    
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        '.($isTutorAdmin && $isMultipleChoice ? 
                            '<div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="questions" role="tabpanel" aria-labelledby="questions-tab2">
                                <div class="pt-0">
                                    '.(!$isActive ? '
                                    <div class="mb-2 text-right">
                                        <a href="#" onclick="return publish_AssignmentQuestion(\''.$item_id.'\',\''.count($questions_query).'\');" class="anchor btn btn-outline-success"><i class="fa fa-send"></i> Publish Questions</a>
                                        <a href="'.$baseUrl.'add-assessment/add_question?qid='.$item_id.'" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Add Question</a>
                                    </div>' : 
                                    '<div class="mb-2 text-right">
                                        <a href="'.$baseUrl.'add-assessment/add_question?qid='.$item_id.'" class="btn btn-outline-primary"><i class="fa fa-eye"></i> Review Questions</a>
                                    </div>'
                                    ).'
                                    '.$questions_list.'
                                </div>
                            </div>
                            <div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="details-tab2">
                                <div class="pt-0">
                                    <div class="py-3 pt-0">
                                        '.$data->assignment_description.'
                                    </div>
                                    <div class="py-1 pt-0">
                                        '.($data->attachment_html ?? null).'
                                    </div>
                                </div>
                            </div>' : 
                            ($isAuto ?
                                '<div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="details" role="tabpanel" aria-labelledby="details-tab2">
                                    <div class="pt-0">
                                        '.(
                                            $data->assignment_description ? 
                                            '<div class="py-3 pt-0">
                                                '.$data->assignment_description.'
                                            </div>' : null
                                        ).'
                                        '.(
                                            $data->attachment_html ? 
                                            '<div class="py-1 pt-0">
                                                '.$data->attachment_html.'
                                            </div>' : null
                                        ).'
                                    </div>
                                </div>'
                                : null
                            )
                        ).'
                        <div class="tab-pane fade '.(!$isAuto ? "show active" : null).'" id="students" role="tabpanel" aria-labelledby="students-tab2">
                            <div class="trix-slim-scroll">
                            '.(
                                !$isAuto && !empty($data->assignment_description) ?
                                '<div class="cardd">
                                    <div class="card-header p-0">
                                        <h4>Description</h4>
                                    </div>
                                    <div class="mb-4">
                                    '.$data->assignment_description.'
                                    </div>
                                </div>' : null
                            ).'
                                '.$grading_info.'
                            </div>
                        </div>';
                        
                        if($hasUpdate && $isAuto) {
                            $response->html .= '<div class="tab-pane fade '.($updateItem ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                            $response->html .= $the_form;
                            $response->html .= '</div>';
                        }

                        $response->html .= '
                        '.((!$nothanded_in || $isGraded) ? 
                            '<div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab2">
                                '.leave_comments_builder("assignments", $item_id, false).'
                                <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$item_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary">Loading comments</button></div>
                            </div>' : ''
                        ).'
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
?>