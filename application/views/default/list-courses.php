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
$response->title = "Courses List : {$appName}";
$response->scripts = [];

$courses_param = (object) [
    "clientId" => $session->clientId,
    "userId" => $session->userId,
    "limit" => 99999
];

$item_list = load_class("courses", "controllers")->list($courses_param);

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$hasDelete = $accessObject->hasAccess("delete", "course");
$hasUpdate = $accessObject->hasAccess("update", "course");

$courses = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a href='{$baseUrl}update-course/{$each->id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a href='{$baseUrl}update-course/{$each->id}/update' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->id}\", \"course\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $courses .= "<tr data-row_id=\"{$each->id}\">";
    $courses .= "<td>".($key+1)."</td>";
    $courses .= "<td>&nbsp; {$each->name}</td>";
    $courses .= "<td>{$each->course_code}</td>";
    $courses .= "<td>{$each->credit_hours}</td>";
    $courses .= "<td>{$each->class_name}</td>";
    $courses .= "<td><span class='underline'>".($each->course_tutor_info->name ?? null)."</span></td>";
    $courses .= "<td>{$action}</td>";
    $courses .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Courses List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item">Courses List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Course Title</th>
                                        <th>Course Code</th>
                                        <th>Credit Hours</th>
                                        <th width="15%">Class</th>
                                        <th>Course Tutor</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>'.$courses.'</tbody>
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