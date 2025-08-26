<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $session, $myClass, $accessObject, $defaultAcademics;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Assign Student Department ";

// end query if the user has no permissions
if(!$accessObject->hasAccess("assign_department", "settings")) {
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// get the class list
$response->scripts = ["assets/js/bulk_update.js"];
$class_list = $myClass->pushQuery("name, id, department_id", "classes", "client_id='{$clientId}' AND status='1'");

$department_list = $myClass->pushQuery("name, id", "departments", "client_id='{$clientId}' AND status='1'");

$response->html = '
    <section class="section list_Students_By_Class">
        <div class="section-header">
            <h1><i class="fa fa-users"></i> Assign Student Department</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'students">List Students</a></div>
                <div class="breadcrumb-item active">Assign Department to Students</div>
            </div>
        </div>
        <input type="hidden" disabled name="assign_param" value="department">
        <div class="row" id="bulk_assign_department_section">
            <div class="col-12 col-sm-12 col-md-12 mb-2 text-primary">
                <h4 class="font-italic">Use this panel to assign department to a class of students.</h4>
            </div>
            <div class="col-12 col-sm-12 col-md-4">
                <div class="card stick_to_top">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Select Class or Staff Category <span class="required">*</span></label>
                            <select name="class_id" data-width="100%" class="form-control selectpicker">
                                <option value="">Please Select Class</option>';
                                foreach($class_list as $each) {
                                    $response->html .= "<option data-department_id='{$each->department_id}' data-class_name='{$each->name}' value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                }
                                $response->html .= '
                                    <option value="staff_members">ALL STAFF MEMBERS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Department <span class="required">*</span></label>
                            <select name="department_id" data-width="100%" class="form-control selectpicker">
                                <option value="">Please Select Department</option>';
                                foreach($department_list as $each) {
                                    $response->html .= "<option data-department_name='{$each->name}' value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                }
                                $response->html .= '
                            </select>
                        </div>

                        <div class="form-group" align="right" id="allocate_fees_button">
                            <button type="submit" disabled="disabled" onclick="return save_Section_Department_Allocation(\'department\')" class="btn btn-outline-success"><i class="fa fa-save"></i> Assign Department</button>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-8">
                <div class="card">
                    <div class="card-body">
                        '.$myClass->quick_student_search_form.'
                        <div class="table-responsive">
                            <table id="simple_load_student" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="7%" class="text-center">#</th>
                                        <th>Student Name</th>
                                        <th>Reg. ID</th>
                                        <th>Department</th>
                                        <td style="background-color: rgba(0,0,0,0.04);" align="center">
                                            <input disabled style="height:23px;width:23px;" id="select_all" type="checkbox" class="cursor">
                                        </td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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