<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $session, $myClass, $accessObject, $defaultAcademics;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$response->title = "Assign Student Class : {$appName}";
$response->scripts = ["assets/js/bulk_update.js"];

// get the class list
$class_list = $myClass->pushQuery("name, id", "classes", "client_id='{$clientId}' AND status='1'", false, "ASSOC");
$class_id_list = !empty($class_list) ? array_column($class_list, "id") : [];

// get the students list
$students_array_list = $myClass->pushQuery("name, unique_id, gender, residence, item_id, class_id", "users", 
    "academic_year = '{$defaultAcademics->academic_year}' AND academic_term = '{$defaultAcademics->academic_term}'
    AND client_id='{$clientId}' AND user_type='student' AND class_id NOT IN {$myClass->inList($class_id_list)}");

$students_list = "";
foreach($students_array_list as $key => $student) {

    $students_list .= "
    <tr data-row_id='{$student->item_id}'>
        <td>".($key + 1)."</td>
        <td><label class='cursor' for='student_id_{$student->item_id}'>".ucwords($student->name)."</label></td>
        <td>{$student->unique_id}</td>
        <td>{$student->gender}</td>
        <td align='center'>
            <input class='student_ids' data-student_name='{$student->name}' disabled name='student_ids[]' value='{$student->item_id}' id='student_id_{$student->item_id}' style='width:20px;cursor:pointer;height:20px;' type='checkbox'>
        </td>
    </tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Assign Student Class</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'list-student">List Students</a></div>
                <div class="breadcrumb-item active">Assign Class to Students</div>
            </div>
        </div>
        <div class="row" id="bulk_assign_class">
            <div class="col-12 col-sm-12 col-md-12 mb-2 text-primary">
                <h4 class="font-italic">Use this panel to assign class to students that has not been set yet.</h4>
            </div>
            <div class="col-12 col-sm-12 col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Select Class <span class="required">*</span></label>
                            <select data-width="100%" class="form-control selectpicker" name="class_id">
                                <option value="">Please Select Class</option>';
                                foreach($class_list as $each) {
                                    $response->html .= "<option data-class_name='{$each["name"]}' value=\"{$each["id"]}\">{$each["name"]}</option>";
                                }
                                $response->html .= '
                            </select>
                        </div>

                        <div class="form-group" align="right" id="allocate_fees_button">
                            <button type="submit" disabled="disabled" onclick="return save_Class_Allocation()" class="btn btn-outline-success"><i class="fa fa-save"></i> Assign Class</button>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-8">
                <div class="card">
                    <div class="card-body">
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