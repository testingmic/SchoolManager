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
$response->title = "Assign Student Class";

// end query if the user has no permissions
if(!$accessObject->hasAccess("assign_class", "settings")) {
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// get the class list
$class_list = $myClass->pushQuery("name, id", "classes", "client_id='{$clientId}' AND status='1'", false, "ASSOC");

// get the students list
$students_array_list = $myClass->pushQuery("u.id, u.name, u.unique_id, u.gender, u.residence, u.item_id, u.class_id, u.image, u.class_id", 
"users u", 
"u.client_id='{$clientId}' AND u.user_type='student' AND u.deleted = '0' AND u.status = '1' LIMIT 500");

$students_list = "";

$groupClass = [];
foreach($class_list as $each) {
    $groupClass[$each["id"]] = $each["name"];
}

// if no student was found
if(empty($students_array_list)) {
    // no student found row
    $students_list .= "<tr><td colspan='5' align='center'>No student found </td></tr>";
} else {
    $response->scripts = ["assets/js/bulk_update.js"];
    foreach($students_array_list as $key => $student) {
        $students_list .= "
        <tr data-row_id='{$student->id}' data-row_search='name' data-student_fullname='{$student->name}' data-unique_id='{$student->unique_id}'>
            <td>".($key + 1)."</td>
            <td>
                <label class='cursor' title='Click to select {$student->name}' for='student_id_{$student->item_id}'>
                    ".random_names($student->name)."
                </label>
                <div>
                    <span class='badge badge-primary'>
                        ".($groupClass[$student->class_id] ?? null)."
                    </span>
                </div>
            </td>
            <td>{$student->unique_id}</td>
            <td>{$student->gender}</td>
            <td class='text-center'>
                <input class='student_ids' data-student_name='{$student->name}' name='student_ids[]' value='{$student->id}' id='student_id_{$student->id}' style='width:20px;cursor:pointer;height:20px;' type='checkbox'>
            </td>
        </tr>";
    }
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-graduation-cap"></i> Assign Student Class</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'students">List Students</a></div>
                <div class="breadcrumb-item active">Assign Class to Students</div>
            </div>
        </div>
        <div class="row" id="bulk_assign_class">
            <div class="col-12 col-sm-12 col-md-12 mb-2 text-primary">
                <h4 class="font-italic">
                    Use this panel to assign class to students that has not been set yet.
                    You can only update up to 500 students at a go.
                </h4>
            </div>
            <div class="col-12 col-sm-12 col-md-4">
                <div class="card stick_to_top">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Select Class <span class="required">*</span></label>
                            <select '.(empty($students_array_list) ? "disabled='disabled'" : 'name="class_id"').' data-width="100%" class="form-control selectpicker">
                                <option value="">Please Select Class</option>';
                                foreach($class_list as $each) {
                                    $response->html .= "<option data-class_name='{$each["name"]}' value=\"{$each["id"]}\">".strtoupper($each["name"])."</option>";
                                }
                                $response->html .= '
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Assign Class Fees <span class="required">*</span></label>
                            <select '.(empty($students_array_list) ? "disabled='disabled'" : "disabled='disabled'").' data-width="100%" class="form-control selectpicker">
                                <option value="dont_assign">Do not Assign Class Fees</option>
                                <option value="assign">Assign Class Fees</option>
                            </select>
                        </div>

                        <div class="form-group" align="right" id="allocate_fees_button">
                            <button type="submit" disabled="disabled" '.(empty($students_array_list) ? null : 'onclick="return save_Class_Allocation()"').' class="btn btn-outline-success"><i class="fa fa-save"></i> Assign Class</button>
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
                                        <th>Gender</th>
                                        <td style="background-color: rgba(0,0,0,0.04);" align="center">
                                            <input disabled style="height:23px;width:23px;" id="select_all" type="checkbox" class="cursor">
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>'.$students_list.'</tbody>
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