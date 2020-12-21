<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL;

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
    "assets/js/upload.js", 
    "assets/js/assignments.js"
];

// the query parameter to load the user information
$i_params = (object) ["limit" => 1, "user_id" => $session->userId, "minified" => "simplified", "userId" => $session->userId];
$userData = $usersClass->list($i_params)["data"][0];

// access permission variables
$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $userData->user_permissions;

// item id
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "assignment_id" => $item_id,
        "userData" => $userData,
        "limit" => 1
    ];

    $data = load_class("assignments", "controllers")->list($item_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];
        $item_param->data = $data;

        // guardian information
        $the_form = load_class("forms", "controllers")->create_assignment($item_param, "update_assignment");
        $hasUpdate = $accessObject->hasAccess("update", "assignments");

        // student update permissions
        $grading_info = "<div class='row'>";

        // get the list of students
        if(in_array($userData->user_type, ["teacher", "admin"])) {
            
            /** Get the students list */
            $students_list = ($data->assigned_to == "selected_students") ? $myClass->stringToArray($data->assigned_to_list) 
                : array_column($myClass->pushQuery("item_id, unique_id, name, email, phone_number, gender", "users", 
                    "client_id='{$clientId}' AND class_id='{$data->class_id}' AND user_type='student' AND user_status='Active' AND status='1'"), "item_id");
            
            /**
             * List the students to whom the assignment has been assigned to
             * 
             * @param 
             * 
             * @return Array
             */
            $the_students_list = $myschoolgh->prepare("
				SELECT item_id, unique_id, name, email, phone_number, gender, image,
                (SELECT score FROM assignments_submitted WHERE assignment_id = '{$data->item_id}' AND student_id = users.item_id) AS score
                FROM users WHERE 
                client_id='{$clientId}' AND class_id='{$data->class_id}' AND user_type='student' 
                AND user_status='Active' AND status='1' AND item_id IN ('".implode("', '", $students_list)."')
			");
			$the_students_list->execute();
            $result = $the_students_list->fetchAll(PDO::FETCH_OBJ);

            // ensure the result is not empty
            if(!empty($result)) {
                $grading_info .= '
                <div class="col-lg-6" id="assignment-content">
                    <div style="margin-top: 10px;margin-bottom: 10px" align="right" class="separator">
                        <button class="btn btn-outline-success save-marks"><i class="fa fa-save"></i> Save</button>
                    </div>
                    <table width="100%" class="table-hover table">
                        <thead>
                            <th>Assigned Students List</th>
                            <th></th>
                        </thead>
                    <tbody>';
                    // loop through the list of students
                    foreach($result as $student) {
                        $grading_info .= '
                            <tr>
                                <td width="65%">
                                    <a style="text-decoration:none" class="anchor" href="javascript:void(0)" onclick="return load_singleStudentData(\''.$student->item_id.'\',\''.$data->grading.'\')" data-value="'.$student->item_id.'" data-function="single-view" data-student_id="'.$data->item_id.'"  data-name="'.$student->name.'" data-score="'.round($student->score,0).'">
                                        <div><img class="rounded-circle cursor author-box-picture" width="40px" src="'.$baseUrl.''.$student->image.'" alt=""> &nbsp; '.$student->name.'</div>
                                    </a>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input name="test_grading" data-value="'.$student->item_id.'" type="number" autocomplete="Off" data-assignment_id="'.$data->item_id.'" maxlength="'.strlen($data->grading).'" min="0" max="'.$data->grading.'" class="form-control"> <span>/ '.$data->grading.'</span>
                                    </div>
                                </td>
                            </tr>';
                    }
                    $grading_info .= '
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-6">
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
                </div>';
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
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-assignments">Assignments List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                    <div class="breadcrumb-item"><a href="'.$baseUrl.'update-assignment/'.$item_id.'/view">Reload</a></div>
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
                        <div class="author-box-job">('.$data->students_assigned.' Students)</div>
                    </div>
                </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Assignment Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Course Name</span>
                                <span class="float-right text-muted">'.($data->course_name ?? null).'</span>
                            </p>
                            '.($hasUpdate ? '
                            <p class="clearfix">
                                <span class="float-left">Assigned To</span>
                                <span class="float-right text-muted">'.($data->assigned_to == "selected_students" ? "{$data->students_assigned} Students" : "Entire Class").'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Handed In</span>
                                <span class="float-right text-muted">'.$data->students_handed_in.' Students</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Marked</span>
                                <span class="float-right text-muted">'.$data->students_graded.' Students</span>
                            </p>
                            ' : null).'
                            <p class="clearfix">
                                <span class="float-left">Submission Date</span>
                                <span class="float-right text-muted">'.date("jS F Y", strtotime($data->due_date)).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Submission Time</span>
                                <span class="float-right text-muted">'.date("h:iA", strtotime($data->due_time)).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Grade</span>
                                <span class="float-right text-muted">'.($data->grading ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Date Created</span>
                                <span class="float-right text-muted">'.date("jS F Y h:iA", strtotime($data->date_created)).'</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Additional Details</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="py-3 pt-0">
                            '.$data->assignment_description.'
                        </div>
                        <div class="py-3 pt-0">
                            '.$data->attachment_html.'
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Course Tutor Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <div class="d-flex justify-content-start">
                                <div class="mr-2">
                                    <img src="'.$baseUrl.''.$data->course_tutor_info->image.'" class="rounded-circle cursor author-box-picture" width="30px">
                                </div>
                                <div>
                                    <div class="clearfix">
                                        <span class="mr-2 float-left">Fullname: </span>
                                        <span class="mr-2 float-right text-muted">'.($data->course_tutor_info->name ?? null).'</span>
                                    </div>
                                    <div class="clearfix">
                                        <span class="mr-2 float-left">Email: </span>
                                        <span class="mr-2 float-right text-muted">'.($data->course_tutor_info->email ?? null).'</span>
                                    </div>
                                    <div class="clearfix">
                                        <span class="mr-2 float-left">Contact: </span>
                                        <span class="mr-2 float-right text-muted">'.($data->course_tutor_info->phone_number ?? null).'</span>
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
                    <li class="nav-item">
                        <a class="nav-link '.(!$updateItem ? "active" : null).'" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">Grade Students</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="students" role="tabpanel" aria-labelledby="students-tab2">
                            '.$grading_info.'
                        </div>
                        <div class="tab-pane fade '.($updateItem ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
                        if($hasUpdate) {
                            $response->html .= $the_form;
                        }

                        $response->html .= '
                        </div>
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