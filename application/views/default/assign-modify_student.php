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
$pageTitle = "Modify Students Record";
$response->title = $pageTitle." : {$appName}";

// get the class list
$students_list = "";
$filter = (object) [];
$class_array_list = $myClass->pushQuery("name, item_id, id", "classes", "client_id='{$clientId}' AND status='1'", false, "ASSOC");

// get the list of all other users
$other_users_list = $myClass->pushQuery("
    a.id, a.name, a.user_type, a.unique_id, a.item_id, a.phone_number, a.class_id, 
        a.image, a.date_of_birth, a.enrollment_date, a.username", 
    "users a", "a.client_id='{$clientId}' AND a.user_status='Active' AND a.user_type = 'student'
        AND a.status='1' ORDER BY a.name LIMIT {$myClass->global_limit}");

// get the list of only students
$users_array_list = [];

$response->scripts = ["assets/js/bulk_update.js"];

// get the users list
foreach($other_users_list as $user) {
    $user->class_id = (int) $user->class_id;
    $users_array_list[] = $user;
}

// append to the array list
$response->array_stream["users_array_list"] = $users_array_list;

// set the classes list
$classes_list = "";
foreach($class_array_list as $class) {
    $classes_list .= "<option ".((isset($filter->class_id) && ($class["id"] == $filter->class_id)) ? "selected='selected'" : null)." data-class_id='{$class["item_id"]}' value='{$class["id"]}'>{$class["name"]}</option>";
}

// set the input field
$response->array_stream["form_input_fields"] = [
    "date_of_birth" => [
        "field" => "input",
        "type" => "text",
        "class" => "form-control datepicker"
    ],
    "enrollment_date" => [
        "field" => "input",
        "type" => "text",
        "class" => "form-control datepicker"
    ],
    "image" => [
        "field" => "input",
        "type" => "file",
        "class" => "form-control"
    ]
];

// print the html page
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-user-graduate"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'students">List Students</a></div>
                <div class="breadcrumb-item active">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row send_smsemail">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3" id="class_select">
                                <div class="form-group mb-1">
                                    <label>Class <span class="required">*</span></label>
                                    <select name="class_id" class="form-control selectpicker" data-width="100%">
                                        <option value="">Select</option>
                                        '.$classes_list.'
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-1">
                                    <label>.</label>
                                    <button type="button" id="generate_list_button" onclick="return generate_list()" class="width-150 btn-block btn btn-outline-primary">Load Students <i class="fa fa-download"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="bulk_assign_class">
            <div class="col-12 col-sm-12 col-md-12">
                <div class="card">
                    <form method="post" action="'.$baseUrl.'api/users/bulk_update" class="users_bulk_update" id="users_bulk_update">
                        <div class="card-body">
                            <div class="mb-2 modify_search_input">
                                <div class="mb-2">
                                    <label>Filter by Name or Registration ID</label>
                                    <input type="text" disabled placeholder="Search by fullname" id="student_fullname" class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-9">
                                        <label> Apply to All</label>
                                        <select disabled id="t_column" class="custom_select">
                                            <option value="end">Enrollment Date</option>
                                            <option value="dob">Date of Birth</option>
                                        </select>
                                        <input disabled type="date" id="t_input" class="custom_input">
                                        <button type="button" disabled onclick="return apply_to_all()" class="btn btn-dark">Apply to All</button>
                                    </div>
                                    <div class="col-3 text-right">
                                        <button disabled type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="simple_load_student" class="table table-md table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="35%">Student Name</th>
                                            <th width="20%">Date of Birth</th>
                                            <th width="20%">Enrollment Date</th>
                                            <th width="20%">Image</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list_students_record"></tbody>
                                </table>
                            </div>
                            <div class="mb-2 modify_search_input">
                                <div class="row">
                                    <div class="col-9"></div>
                                    <div class="col-3 text-right">
                                        <button disabled type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>