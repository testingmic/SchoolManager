<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
if(!isset($_SERVER["HTTP_REFERER"])) {
    header("location: {$baseUrl}main");
    exit;
}

$response = (object) [];
$response->title = "Students List : {$appName}";
$response->scripts = [];

$student_param = (object) [
    "clientId" => $session->clientId,
    "user_type" => "student"
];

$student_list = load_class("users", "controllers")->list($student_param);

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$hasDelete = $accessObject->hasAccess("delete", "student");
$hasUpdate = $accessObject->hasAccess("update", "student");

$students = "";
foreach($student_list["data"] as $key => $each) {
    
    $action = "<a href='{$baseUrl}update-student/{$each->user_id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a href='{$baseUrl}update-student/{$each->user_id}/update' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' data-record_id='{$each->user_id}' data-record_type='user' class='btn btn-sm delete_record btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $students .= "<tr data-row_id=\"{$each->user_id}\">";
    $students .= "<td>".($key+1)."</td>";
    $students .= "<td><img class='rounded-circle author-box-picture' width='40px' src=\"{$baseUrl}{$each->image}\"> &nbsp; {$each->name}</td>";
    $students .= "<td>{$each->class_name}</td>";
    $students .= "<td>{$each->gender}</td>";
    $students .= "<td>{$each->department_name}</td>";
    $students .= "<td>{$action}</td>";
    $students .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Students List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item">Students List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Gender</th>
                                        <th>Department</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>'.$students.'</tbody>
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