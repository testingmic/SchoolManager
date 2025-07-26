<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $isTutorAdmin, $isWardParent, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Details";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!in_array("class_assessment", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

$response->scripts = [
    "assets/js/assignments.js",
    "assets/js/comments.js",
    "assets/js/lessons.js"
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
        $isGraded = (bool)isset($data->awarded_mark);
        $isClosed = in_array($data->state, ["Closed"]);
        $isActive = in_array($data->state, ["Graded", "Pending", "Answered"]);
        $isMultipleChoice =  (bool) (($data->questions_type == "multiple_choice")); // || ($data->questions_type == "unassigned")
        $isUnassigned =  (bool) (($data->questions_type == "unassigned"));

        $data->isGraded = $isGraded;
        $data->hasUpdate = $hasUpdate;

        // set the user_id id in the console
        $response->array_stream['url_link'] = "assessment/{$item_id}/";

        // is manual or auto insertion
        $isAuto = (bool) (in_array($data->insertion_mode, ["Auto", "Manual"]));

        // get the assignment form
        $the_form = $hasUpdate ? load_class("forms", "controllers")->create_assignment($item_param, "update_assignment") : null;

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
                $questions_list .= "<th class='text-center'>Marks</th>";
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
                            <td class='text-center'>{$question->marks}</td>
                            <td align='center'>";
                                if(!$isActive && !$isClosed) {
                                    $questions_list .= "<a href='{$baseUrl}add-assessment/add_question?qid={$item_id}&q_id={$question->item_id}' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>&nbsp;";
                                }
                                $questions_list .= "<button class='btn btn-outline-primary btn-sm' onclick='return view_AssignmentQuestion(\"{$question->item_id}\")'><i class='fa fa-eye'></i></button>&nbsp;";
                                if(!$isActive && !$isClosed) {
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
                        "a.client_id='{$clientId}' AND c.item_id='{$data->class_id}' AND a.user_type='student' AND a.user_status IN ({$myClass->default_allowed_status_users_list}) AND a.status='1'"
                ), "item_id");

            /**
             * List the students to whom the assignment has been assigned to
             * 
             * @param 
             * 
             * @return Array
             */
            $the_students_list = $myschoolgh->prepare("
				SELECT id, item_id, unique_id, name, email, phone_number, gender, image,
                (SELECT score FROM assignments_submitted WHERE assignment_id = '{$data->item_id}' AND student_id = users.item_id LIMIT 1) AS score,
                (SELECT CONCAT(b.handed_in,'|',b.is_submitted) FROM assignments_submitted b WHERE b.assignment_id = '{$data->item_id}' AND b.student_id = users.item_id LIMIT 1) AS handed_in_submitted
                FROM users 
                WHERE client_id='{$clientId}' AND user_type='student' 
                AND user_status IN ({$myClass->default_allowed_status_users_list}) AND status='1' AND item_id IN ('".implode("', '", $students_list)."') ORDER BY name ASC
			");
			$the_students_list->execute();
            $result = $the_students_list->fetchAll(PDO::FETCH_OBJ);

            // ensure the result is not empty
            if(!empty($result)) {
                
                // if its a multi choice question or a file upload question set
                $function = $isMultipleChoice ? "review_QuizAssignment" : "load_singleStudentData";

                $grading_info .= '
                <div class="col-lg-'.($isAuto && !$isMultipleChoice ? 12 : ($isUnassigned ? 12 : 12)).'" id="assignment-content">
                    '.( $isActive ?
                        '<div style="margin-top: 10px;margin-bottom: 10px" align="right" class="initial_assignment_buttons">
                            <button class="btn btn-outline-danger" onclick="return close_Assignment(\''.$data->item_id.'\');"><i class="fa fa-times"></i> Mark As Closed</button>
                            '.(!$isMultipleChoice ? '<button class="btn btn-outline-success" onclick="return save_AssignmentMarks();"><i class="fa fa-save"></i> Save</button>' : '').'
                        </div>' : (
                            $isAdmin && $isAuto && !$isClosed ? '
                            <div style="margin-top: 10px;margin-bottom: 10px" align="right" class="initial_assignment_buttons">
                                <button class="btn mb-2 btn-outline-danger" onclick="return reopen_Assignment(\''.$data->item_id.'\');">
                                    <i class="fa fa-times"></i> Reopen Assignment
                                </button>
                            </div>
                        ' : ''
                        )
                    ).'
                    '.$myClass->quick_student_search_form.'
                    <table width="100%" class="table-hover table mb-0">
                        <thead>
                            '.($isUnassigned || $isMultipleChoice ? "<th>#</th>" : null).'
                            <th>Assigned Students List</th>
                            <th></th>
                        </thead>
                    </table>
                    <div class="">
                        <table width="100%" class="table-bordered table-striped table mt-0">
                        <tbody>';
                        $counter = 0;
                        // loop through the list of students
                        foreach($result as $student) {
                            
                            $counter++;
                            // split the varible
                            $split = !empty($student->handed_in_submitted) ? explode("|", $student->handed_in_submitted) : [];

                            // set a new variable for the handed in
                            $student->handed_in = $split[0] ?? "Pending";

                            // set a new variable for the submitted 
                            $student->is_submitted = $split[1] ?? 0;

                            $student->name = random_names($student->name);

                            $student->handed_in = !empty($student->handed_in) ? $student->handed_in : "Pending";
                            $isSubmitted = (bool) (in_array($student->handed_in, ["Submitted", "Graded"]) && $student->is_submitted);

                            $grading_info .= '
                                <tr data-row_search="name" data-student_fullname="'.trim($student->name).'" data-student_unique_id="'.$student->unique_id.'">
                                    '.($isUnassigned || $isMultipleChoice ? "<td width='5%'>{$counter}</td>" : null).'
                                    <td width="70%">
                                        <div class="d-flex justify-content-start items-center">
                                            <div class="mr-2">
                                                <div class="flex items-center space-x-4">
                                                '.($isSubmitted ?
                                                    '<a title="Click to view document submitted by '.$student->name.'" style="text-decoration:none" class="anchor" href="javascript:void(0)" '.($isAuto ? 'onclick="return '.$function.'(\''.$student->item_id.'\',\''.$data->grading.'\',\''.$data->item_id.'\')"' : null).' data-assignment_id="'.$data->item_id.'" data-function="single-view" data-student_id="'.$student->item_id.'"  data-name="'.$student->name.'" data-score="'.round($student->score,0).'">
                                                        <img class="rounded-2xl cursor author-box-picture" height="40px" width="40px" src="'.$baseUrl.''.$student->image.'" alt="">
                                                    </a>' : 
                                                    '<img class="rounded-2xl cursor author-box-picture" height="40px" width="40px" src="'.$baseUrl.''.$student->image.'" alt="">'
                                                ).'
                                                </div>
                                            </div>
                                            <div>
                                                <p class="p-0 text-uppercase m-0">
                                                    '.($isSubmitted ? '<a title="Click to view document submitted by '.$student->name.'" style="text-decoration:none" class="anchor" href="javascript:void(0)" '.($isAuto ? 'onclick="return '.$function.'(\''.$student->item_id.'\',\''.$data->grading.'\',\''.$data->item_id.'\')"' : null).' data-assignment_id="'.$data->item_id.'" data-function="single-view" data-student_id="'.$student->item_id.'"  data-name="'.$student->name.'" data-score="'.round($student->score,0).'"><strong>'.$student->name.'</strong></a>' : "<strong>{$student->name}</strong>").'
                                                </p>
                                                <p class="p-0 m-0">'.$myClass->the_status_label($student->handed_in).'</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group flex items-center">
                                            <input '.(!$isActive || $isMultipleChoice ? 'disabled="disabled"' : 'name="test_grading" data-rvalue="'.$student->id.'" data-value="'.$student->item_id.'"').' value="'.$student->score.'" type="number" data-assignment_id="'.$data->item_id.'" maxlength="'.strlen($data->grading).'" min="0" max="'.$data->grading.'" style="max-width:120px" class="form-control text-center font-20"> &nbsp; <span class="font-25 d-none d-sm-block">/ '.$data->grading.'</span>
                                        </div>
                                    </td>
                                </tr>';
                        }
                        $grading_info .= '
                            </tbody>
                        </table>
                    </div>
                    '.( $isActive ?
                        '<div style="margin-top: 10px;margin-bottom: 10px" align="right" class="initial_assignment_buttons">
                            <button class="btn btn-outline-danger" onclick="return close_Assignment(\''.$data->item_id.'\');"><i class="fa fa-times"></i> Mark As Closed</button>
                            '.(!$isMultipleChoice ? '<button class="btn btn-outline-success" onclick="return save_AssignmentMarks();"><i class="fa fa-save"></i> Save</button>' : '').'
                        </div>' : (
                            $isAdmin && $isAuto && !empty($questions_query) ? '
                                <button class="btn mb-2 btn-outline-danger" onclick="return reopen_Assignment(\''.$data->item_id.'\');"><i class="fa fa-times"></i> Reopen</button>
                            ' : ''
                        )
                    ).'
                </div>
                '.($isAuto ?
                    '<div class="col-lg-5">
                        <div class="details-content-save"></div>
                        <div class="grading-history-div_" style="display:none">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td align="right">
                                            <!--<span data-function="grading-history" class="text-primary cursor" title="View Grading History">Grading History</span>-->
                                            <input data-grading="'.$data->grading.'" data-assignment_id="'.$data->item_id.'" type="hidden" name="data-student-id" class="data-student-id">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="student-assignment-details"></div>
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

            // check if the use has handed in the assignment
            $nothanded_in = (bool) (($data->handed_in === "Pending") || empty($data->handed_in));

            if($nothanded_in) {
                // append the upload script
                $response->scripts[] = "assets/js/upload.js";

                // file upload parameter
                $file_params = (object) [
                    "module" => $module,
                    "item_id" => $item_id,
                    "userData" => $defaultUser,
                    "accept" => ".doc,.docx,.pdf,.png,.jpg,jpeg",
                    "is_deletable" => $nothanded_in,
                    "attachments_list" => $data->attached_document ?? []
                ];
            }
            
            // display the content if the assignment type is a file_attachment
            if((!$isMultipleChoice && $isActive && $isAuto) || (!$isMultipleChoice && $isClosed)) {
                
                // display the file upload option
                $grading_info .= 
                ($nothanded_in ?
                    '<div class="col-lg-12" id="handin_upload">
                        <div><h6 class="text-uppercase">Type your answer in the field provided below.</h6></div>
                        <input type="hidden" hidden id="handin_assignment" value="'.($data->content ?? null).'">
                        <trix-editor class="small-expand-height trix-slim-scroll" input="handin_assignment" id="handin_assignment" name="handin_assignment"></trix-editor>
                        <div class="mt-4"><h6 class="text-uppercase">alt: Upload Assignment Answer</h6></div>
                        <div class="col-lg-12 p-0" id="upload_question_set_template">
                            <div class="form-group text-center mb-1">
                                <div class="row">
                                    '.load_class("forms", "controllers")->form_attachment_placeholder($file_params).'
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="javascript:void(0)" onclick="return submit_Answers(\''.$item_id.'\', \'Pending\');" class="btn anchor btn-outline-success">
                                    <i class="fa fa-save"></i> Save Answer
                                </a>
                            </div>
                            <div>
                                <a href="javascript:void(0)" onclick="return submit_Answers(\''.$item_id.'\', \'Submitted\');" class="btn anchor btn-outline-primary">
                                    <i class="fa fa-save"></i> Submit Answer
                                </a>
                            </div>
                        </div>
                    </div>' : '
                    <div class="col-lg-12" id="handin_documents">
                        '.(!empty($data->content) ? "<div class='mb-3 border-bottom pb-3'>".htmlspecialchars_decode($data->content)."</div>" : null).'
                        '.($data->attached_attachment_html ?? null).'
                    </div>'
                );
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
                    $response->scripts = ["assets/js/multichoice.js", "assets/js/lessons.js"];

                    // append the load
                    $grading_info .= '
                    <div class="form-content-loader" style="display: none; position: absolute">
                        <div class="offline-content text-center">
                            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                        </div>
                    </div>';
                    
                    // get the questions array list
                    $questions_array_list = load_class("assignments", "controllers")->questions_list($params);
                    $questions_array = [];

                    // set the previous question id and the current question Id
                    $questions_ids = array_column($questions_array_list, "item_id");
                    $session->previousQuestionId = !empty($session->previousQuestionId) ? $session->previousQuestionId : null;
                    $session->currentQuestionId = !empty($session->currentQuestionId) ? $session->currentQuestionId : $questions_ids[0];

                    $grading_info .= $assignmentClass->current_question($questions_array_list, $session->student_id, $data->assignment_type);
                
                } elseif($data->handed_in === "Submitted") {
                    $params->show_correct_answer = true;
                    $params->student_id = $session->student_id;
                    $grading_info .= $assignmentClass->review_answers($params, "p-2")["data"];
                }

            }

        }

        $grading_info .= '</div>';

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") || confirm_url_id(2, "details") ? true : false;

        // set the url
        $url_link = $SITEURL[2] ?? null;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-book-reader"></i> '.$data->assignment_type.' Details</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'assessments">Assessments</a></div>
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
                        <div class="author-box-name text-uppercase font-20"><a href="#">'.$data->assignment_title.'</a></div>
                        <div class="author-box-name">'.$data->class_name.'</div>
                        '.(isset($data->students_assigned) ? '<div class="author-box-job">('.$data->students_assigned.' Students)</div>' : null).'
                    </div>
                </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>'.$data->assignment_type.' Details</h4>
                    </div>
                    '.$assignmentClass->quick_data($data, ($isActive && $isTutorAdmin), ($isAdmin && $isAuto), $isClosed && !$data->exported).'
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
                <div class="padding-15">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    '.($isAuto  ?
                        ($isTutorAdmin && $isMultipleChoice ? 
                            '<li class="nav-item">
                                <a class="nav-link '.(empty($url_link) || in_array($url_link, ["questions", "view"]) ? "active" : null).'" onclick="return appendToUrl(\'questions\')" id="questions-tab2" data-toggle="tab" href="#questions" role="tab" aria-selected="true">
                                    Questions Set
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link '.($url_link === "instructions" ? "active" : null).'" onclick="return appendToUrl(\'instructions\')" id="details-tab2" data-toggle="tab" href="#details" role="tab" aria-selected="true">
                                    Instructions
                                </a>
                            </li>
                            ' : '
                            <li class="nav-item">
                                <a class="nav-link '.($url_link === "instructions" ? "active" : null).'" onclick="return appendToUrl(\'instructions\')" id="details-tab2" data-toggle="tab" href="#details" role="tab" aria-selected="true">
                                    Instructions
                                </a>
                            </li>
                            '    
                        ) : null
                    ).'
                    <li class="nav-item">
                        <a class="nav-link '.($url_link === "_grading" ? "active" : null).'" onclick="return appendToUrl(\'_grading\')" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">
                            '.($isTutorAdmin ? "Grading" : "Handin Answers").'
                        </a>
                    </li>';

                    if($hasUpdate && $isAuto) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($url_link === "details" ? "active" : null).'" id="profile-tab2" onclick="return appendToUrl(\'details\')" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    '.((!$nothanded_in || $isGraded) ? 
                        '<li class="nav-item">
                            <a class="nav-link" id="comments-tab2" data-toggle="tab" href="#comments" role="tab" aria-selected="true">
                                Discussions
                            </a>
                        </li>' : ''
                    ).'
                    
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        '.($isTutorAdmin && $isMultipleChoice ? 
                            '<div class="tab-pane fade '.(empty($url_link) || in_array($url_link, ["questions", "view"]) ? "show active" : null).'" id="questions" role="tabpanel" aria-labelledby="questions-tab2">
                                <div class="pt-0">
                                    '.(!$isActive && !$isClosed ? '
                                    <div class="mb-2 text-right">
                                        '.(!empty($questions_query) ? '<a href="#" onclick="return publish_AssignmentQuestion(\''.$item_id.'\',\''.count($questions_query).'\');" class="anchor btn btn-outline-success">
                                            <i class="fa fa-upload"></i> Publish Assignment</a>' : null).'
                                        <a href="'.$baseUrl.'add-assessment/add_question?qid='.$item_id.'" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Add Question</a>
                                    </div>' : 
                                    '<div class="mb-2 text-right">
                                        <a href="'.$baseUrl.'add-assessment/add_question?qid='.$item_id.'" class="btn btn-outline-primary"><i class="fa fa-eye"></i> Review Questions</a>
                                    </div>'
                                    ).'
                                    '.$questions_list.'
                                </div>
                            </div>
                            <div class="tab-pane fade '.($url_link === "instructions" ? "show active" : null).'" id="details" role="tabpanel" aria-labelledby="details-tab2">
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
                                '<div class="tab-pane fade '.($url_link === "instructions" ? "show active" : null).'" id="details" role="tabpanel" aria-labelledby="details-tab2">
                                    <div class="pt-0">
                                        '.(
                                            $data->assignment_description ? 
                                            '<div class="py-3 pt-0">
                                                '.$data->assignment_description.'
                                            </div>' : null
                                        ).'
                                        '.(
                                            !empty($data->attachment_html) ? 
                                            '<div class="py-1 pt-0">
                                                '.$data->attachment_html.'
                                            </div>' : null
                                        ).'
                                    </div>
                                </div>'
                                : null
                            )
                        ).'
                        <div class="tab-pane fade '.($url_link === "_grading" ? "show active" : null).'" id="students" role="tabpanel" aria-labelledby="students-tab2">
                            <div id="handin_assignment_container">
                                <div class="form-content-loader" style="display: none; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
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