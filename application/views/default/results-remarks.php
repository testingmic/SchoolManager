<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultClientData, $isPayableStaff, $clientFeatures, $isTeacher, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_GET);

$response->title = "Student Remarks";

// access permissions check
if(!$isTeacher && !$isAdmin) {
    $response->html = page_not_found("permission_denied");
} else {

    $response->scripts = ["assets/js/remarks.js"];

    // set the client id
    $filter->clientId = $session->clientId;
    $filter->client_data = $defaultClientData;

    // get the results remarks list
    $results_remarks_array = load_class("terminal_reports", "controllers", $filter)->results_remarks($filter);

    $classes_list = '';

    $results_remarks_list = "
        <div class='col-md-12'>
            <div class='row border-bottom border-gray mb-2'>
                <div class='col-md-4 mb-2'>
                <select name='filter_remarks_class_id' id='filter_remarks_class_id' class='form-control selectpicker' data-width='100%'>
                    <option value=''>Select Class to Filter</option>
                    '.$classes_list.'
                </select>
                </div>
                <div class='col-md-8 mb-2' data-input_item='search'>
                    <input type='text' class='form-control' placeholder='Search remarks' id='search_remarks' onkeyup='return search_remarks()'>
                </div>
            </div>
        </div>";

    if(empty($results_remarks_array['data'])) {
        $results_remarks_list .= no_record_found(
            "No student remarks recorded", 
            "No student remarks have been recorded for the selected class yet. You can add new remarks by clicking the button below.", 
            null, 
            "Student Remarks", 
            false, 
            "fa fa-graduation-cap", 
            false
        );
    }

    else {
        foreach($results_remarks_array['data'] as $key => $remark) {
            $results_remarks_list .= "
            <div class='col-md-6 col-lg-4 remarks_item' data-remarks_id='{$remark->id}' data-student_name='{$remark->student_name}'>
                <div class='card'>
                    <div class='card-header'>
                        <h5 class='card-title mb-0 pb-0'>{$remark->class_name} - {$remark->student_name}</h5>
                    </div>
                    <div class='card-body p-3'>
                        <p>".limit_words($remark->remarks, 20)."...</p>
                        <div class='mt-1 border-top pt-2'>
                            <div><strong>Acadmic Year:</strong> <span class='text-muted'>{$remark->academic_year}</span></div>
                            <div><strong>Acadmic Term:</strong> <span class='text-muted'>{$remark->academic_term}</span></div>
                            <div><strong>Date Created:</strong> <span class='text-muted'>{$remark->created_on}</span></div>
                            <!-- <div><strong>Updated On:</strong> <span class='text-muted'>{$remark->updated_on}</span></div>
                            <div><strong>Created By:</strong> <span class='text-muted'>{$remark->created_by}</span></div> -->
                        </div>
                    </div>
                    <div class='card-footer mt-0 pt-0 pl-3'>
                        <div class='d-flex justify-content-between'>
                            <div>
                                <a href=\"javascript:void(0)\" onclick=\"return edit_student_remarks({$remark->id}, {$remark->class_id}, '{$remark->student_id}')\" class='btn btn-outline-primary'>
                                    <i class='fas fa-edit'></i> Edit
                                </a>
                            </div>
                            <div>
                                <a href=\"javascript:void(0)\" onclick=\"return delete_student_remarks({$remark->id})\" class='btn btn-outline-danger'>
                                    <i class='fas fa-trash'></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>";
        }
    }

    // set the parent menu
    $response->parent_menu = "reports-promotion";

    $userId = $session->userId;
    $clientId = $session->clientId;
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$response->title.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">'.$response->title.'</div>
                </div>
            </div>
            <div class="text-right mb-2">
                <a class="btn btn-outline-success" href="#" onclick="return add_student_remarks();"><i class="fas fa-graduation-cap"></i> Add New Student Remarks</a>
            </div>
            <div class="row">
                '.$results_remarks_list.'
            </div>
        </section>
        <div data-backdrop="static" data-keyboard="false" class="modal fade" id="studentRemarksModal">
            <form autocomplete="Off" action="'.$baseUrl.'api/terminal_reports/save_student_remarks" method="POST" class="ajax-data-form" id="ajax-data-form-content">
                <div class="modal-dialog modal-dialog-top modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Student Remarks</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="class_id">Select Class <span class="required">*</span></label>
                                        <select name="remarks_class_id" id="remarks_class_id" class="form-control selectpicker" data-width="100%">
                                            <option value="">Select Class</option>
                                            '.$classes_list.'
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="remarks_student_id">Select Student <span class="required">*</span></label>
                                        <select name="remarks_student_id" id="remarks_student_id" class="form-control selectpicker" data-width="100%">
                                            <option value="">Select Student</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="remarks">Remarks <span class="required">*</span></label>
                                        <textarea name="remarks" maxlength="260" id="remarks" class="form-control" rows="5"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group text-center font-italic font-bold">
                                        Summary submitted results for the selected student.
                                    </div>
                                    <div id="remarks_summary"></div>
                                    <div id="remarks_summary_list">
                                        <div class="text-center font-italic">
                                            <i class="fa fa-spinner fa-spin"></i> Loading...
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer p-0">
                                <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                                <button data-form_id="ajax-data-form-content" type="button-submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>';

}
    
// print out the response
echo json_encode($response);
?>