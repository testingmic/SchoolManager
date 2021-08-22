<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Log Assessment";
$response->title = "{$pageTitle} : {$appName}";


$assessment_log_id = "";
$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

// include the scripts
$response->scripts = ["assets/js/assessment.js"];

// append the html content
$response->html = '
<section class="section">
    <div class="section-header">
        <h1><i class="fa fa-book"></i> '.$pageTitle.'</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-assessment">List Assessement Logs</a></div>
            <div class="breadcrumb-item">'.$pageTitle.'</div>
        </div>
    </div>
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
                                    <input style="height:50px" class="form-control" value="" name="assessment_title" id="assessment_title">
                                </div>
                                <div class="row">
                                    <div class="col-md-7 form-group">
                                        <label>Select Assignment Category <span class="required">*</span></label>
                                        <select data-width="100%" class="selectpicker form-control" name="assessment_type" id="assessment_type">
                                            <option value="">Select Category</option>';
                                            foreach($myClass->assessment_group as $value) {
                                                $response->html .= "<option ".(isset($assessment_type) && ($assessment_type == $value) ? "selected" : null)." value='{$value}'>{$value}</option>";
                                            }
                                            $response->html .= '
                                        </select>
                                    </div>
                                    <div class="col-md-5 form-group">
                                        <label>Total Marks <span class="required">*</span></label>
                                        <input type="number" min="0" max="100" name="overall_score" id="overall_score" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Select Class <span class="required">*</span></label>
                                    <select data-width="100%" class="selectpicker form-control" name="class_id" id="class_id">
                                        <option value="">Select Class</option>';
                                        foreach($myClass->pushQuery("name, id, item_id", "classes", "client_id='{$clientId}' AND status='1'") as $class) {
                                            $response->html .= "<option ".(isset($class_id) && ($class_id == $class->item_id) ? "selected" : null)." value='{$class->item_id}'>{$class->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Select Course <span class="required">*</span></label>
                                    <select data-width="100%" class="selectpicker form-control" name="course_id" id="course_id">
                                        <option value="">Select Course</option>';
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
                                        <input class="form-control datepicker" value="" name="date_due" id="date_due">
                                    </div>
                                    <div class="col-md-5 form-group">
                                        <label>Submission Time</label>
                                        <input class="form-control" type="time" value="" name="time_due" id="time_due">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Assignment Additional Description</label>
                                    <textarea style="height:60px" class="form-control" name="assessment_description" id="assessment_description"></textarea>
                                </div>
                                <div class="form-group text-right border-top mt-2 pt-4">
                                    <button onclick="return prepare_assessment_log('.$assessment_log_id.')" data-function="save" type="button-submit" class="btn btn-outline-success">Prepare Assessment</button>
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
                            <div class="mb-4 slim-scroll table-responsive" style="max-height: 800px;">
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
                            <div class="row" id="buttons">
                                <div class="col-md-4" align="left">
                                    <button onclick="return cancel_assessment();" class="btn text-uppercase btn-danger">Cancel</button>
                                </div>
                                <div class="col-lg-8" align="right">
                                    <button onclick="return award_marks(\'save\');" class="btn text-uppercase btn-outline-primary">Award Marks & Save</button>
                                    <button onclick="return award_marks(\'close\');" class="btn text-uppercase btn-success">Award Marks & Close</button>
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