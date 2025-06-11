<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $isTutor, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Create Record";
$response->title = $pageTitle;


$assessment_log_id = "";
$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

// if the user has the permission to allocate fees
$createAssessmentTest = $accessObject->hasAccess("add", "assignments");

/** confirm that the user has the permission to add assessment */
if(!$createAssessmentTest) {
    $response->html = page_not_found("permission_denied");
} else {

    // include the scripts
    $response->scripts = ["assets/js/assessment.js", "assets/js/upload.js"];

    $classFilter = $isTutor ? "AND item_id IN ".$myClass->inList($defaultUser->class_ids) : null;
    $classes_list = $myClass->pushQuery("name, id, item_id", "classes", "client_id='{$clientId}' AND status='1' {$classFilter}");

    // file upload parameter
    $file_params = (object) [
        "module" => "assessments_".$defaultUser->user_id,
        "userData" => $defaultUser,
        "accept" => ".doc,.docx,.pdf,.png,.jpg,jpeg"
    ];

    // append the html content
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-book"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'assessments">Assessements</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>';
        // if the term has ended
        if(($isAdminAccountant || $isTutorAdmin)) {
            $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
        }
        $response->html .= '
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h4>Assessment Content Details</h4>
                        </div>
                        <div class="card-body pt-0 pb-0" id="log_assessment">
                            <div class="py-3 pt-0">
                                <div class="prepare_assessment_log">
                                    <div class="form-group">
                                        <label>Title <span class="required">*</span></label>
                                        <input style="height:50px" autocomplete="Off" class="form-control" value="" name="assessment_title" id="assessment_title">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7 form-group">
                                            <label>Select Assignment Category <span class="required">*</span></label>
                                            <select data-width="100%" class="selectpicker form-control" name="assessment_type" id="assessment_type">
                                                <option value="">Select Category</option>';
                                                foreach($myClass->assessment_group as $value) {
                                                    $response->html .= "<option ".(isset($assessment_type) && ($assessment_type == $value) ? "selected" : null)." value='{$value}'>".strtoupper($value)."</option>";
                                                }
                                                $response->html .= '
                                            </select>
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label>Grade Scale <span class="required">*</span></label>
                                            <input type="number" autocomplete="Off" min="0" max="100" name="overall_score" id="overall_score" class="form-control text-center">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Select Class <span class="required">*</span></label>
                                        <select data-width="100%" class="selectpicker form-control" name="class_id" id="class_id">
                                            <option value="">Select Class</option>';
                                            foreach($classes_list as $class) {
                                                $response->html .= "<option ".(isset($class_id) && ($class_id == $class->item_id) ? "selected" : null)." value='{$class->item_id}'>".strtoupper($class->name)."</option>";
                                            }
                                            $response->html .= '
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Select Subject <span class="required">*</span></label>
                                        <select data-width="100%" class="selectpicker form-control" name="course_id" id="course_id">
                                            <option value="">Select Subject</option>';
                                            if(isset($ass_data)) {
                                                foreach($ass_data["courses_list"] as $course) {
                                                    $response->html .= "<option ".($course_id == $course->item_id ? "selected" : null)." value='{$course->item_id}'>{$course->name}</option>";
                                                }
                                            }
                                            $response->html .= '
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7 form-group">
                                            <label>Submission Date <span class="required">*</span></label>
                                            <input data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" class="form-control datepicker" value="" name="date_due" id="date_due">
                                        </div>
                                        <div class="col-md-5 form-group">
                                            <label>Submission Time</label>
                                            <input autocomplete="Off" class="form-control" type="time" value="" name="time_due" id="time_due">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Assignment Additional Description</label>
                                        <trix-editor class="trix-medium-height" name="assessment_description" id="assessment_description"></trix-editor>
                                    </div>
                                    <div class="col-lg-12 p-0" id="upload_question_set_template">
                                        <div class="form-group text-center mb-1">
                                            <div class="row">
                                                '.load_class("forms", "controllers")->form_attachment_placeholder($file_params).'
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group text-right mt-2 pt-4">
                                        <button onclick="return prepare_assessment_log('.$assessment_log_id.')" data-function="save" type="button-submit" class="btn btn-outline-success">
                                        <i class="fa fa-download"></i> Load Students
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-7">
                    <div class="card">
                        <div class="padding-20">
                            <div class="text-center" id="init_data">The students list for gradding will appear here.</div>
                            <div id="award_marks" class="hidden">
                                '.$myClass->quick_student_search_form.'
                                <div class="mb-4 slim-scroll">
                                    <table data-empty="" id="student_staff_list" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th>Student Name</th>
                                                <th width="30%">Awarded Mark</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>                         
                                </div>
                                <div class="d-flex justify-content-between" id="buttons">
                                    <div>
                                        <button onclick="return cancel_assessment();" class="btn text-uppercase btn-danger mb-1">Discard</button>
                                    </div>
                                    <div class="text-right">
                                        <button onclick="return award_marks(\'save\');" class="btn text-uppercase btn-outline-primary mb-1">Award Marks & Save</button>
                                        <button onclick="return award_marks(\'close\');" class="btn text-uppercase btn-success mb-1">Award Marks & Close</button>
                                    </div>
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