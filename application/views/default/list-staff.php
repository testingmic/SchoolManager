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
jump_to_main($baseUrl);

$response = (object) [];
$response->title = "Staff List : {$appName}";
$response->scripts = [];

$staff_param = (object) [
    "clientId" => $session->clientId,
    "user_type" => "employee,teacher,admin,accountant"
];

$api_staff_list = load_class("users", "controllers")->list($staff_param);

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

// the query parameter to load the user information
$i_params = (object) ["limit" => 1, "user_id" => $session->userId, "minified" => "simplified", "userId" => $session->userId];

// get the user data
$userData = $usersClass->list($i_params)["data"][0];
$accessObject->userPermits = $userData->user_permissions;

$staff_list = "";

$color = [
    "admin" => "success",
    "employee" => "primary",
    "accountant" => "danger",
    "teacher" => "warning"
];

foreach($api_staff_list["data"] as $key => $each) {
    
    $action = "<a href='{$baseUrl}update-staff/{$each->user_id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($accessObject->hasAccess("update", $each->user_type)) {
        $action .= "&nbsp;<a href='{$baseUrl}update-staff/{$each->user_id}/update' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($accessObject->hasAccess("delete", $each->user_type)) {
        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->user_id}\", \"user\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $staff_list .= "<tr data-row_id=\"{$each->user_id}\">";
    $staff_list .= "<td>".($key+1)."</td>";
    $staff_list .= "<td>
        <div class='d-flex justify-content-start'>
            <div class='mr-2'><img class='rounded-circle author-box-picture' width='40px' src=\"{$baseUrl}{$each->image}\"></div>
            <div>{$each->name} <br><span class='text-uppercase badge badge-{$color[$each->user_type]} p-2'>{$each->user_type}</span></div>
        </div></td>";
    $staff_list .= "<td>{$each->position}</td>";
    $staff_list .= "<td>{$each->gender}</td>";
    $staff_list .= "<td>{$each->date_of_birth}</td>";
    $staff_list .= "<td>{$each->enrollment_date}</td>";
    $staff_list .= "<td>{$each->department_name}</td>";
    $staff_list .= "<td class='text-center'>{$action}</td>";
    $staff_list .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Staff List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item">Staff List</div>
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
                                        <th>Staff Name</th>
                                        <th>Staff Role</th>
                                        <th>Gender</th>
                                        <th>Date of Birth</th>
                                        <th>Date Employed</th>
                                        <th>Department</th>
                                        <th width="10%" align="center"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$staff_list.'</tbody>
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