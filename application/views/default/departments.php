<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Departments List";
$response->scripts = [];

$department_param = (object) [
    "clientId" => $session->clientId
];

$item_list = load_class("departments", "controllers")->list($department_param);

$hasDelete = $accessObject->hasAccess("delete", "department");
$hasUpdate = $accessObject->hasAccess("update", "department");
$count = 0;
$departments = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "&nbsp;<a title='Click to update department record' href='#' onclick='return load(\"department/{$each->id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    $count++;
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Department' onclick='return delete_record(\"{$each->id}\", \"department\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $departments .= "<tr data-row_id=\"{$each->id}\">";
    $departments .= "<td>".($count)."</td>";
    $departments .= "<td><a href='#' class='text-uppercase font-weight-bold' onclick='return load(\"department/{$each->id}\");'>{$each->name}</a></td>";
    $departments .= "<td>{$each->department_code}</td>";
    $departments .= "<td>{$each->students_count}</td>";
    $departments .= "<td><span class='underline'>".($each->department_head_info->name ?? null)."</span></td>";
    $departments .= "<td align='center'>{$action}</td>";
    $departments .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-hotel"></i> Departments List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Departments</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" data-rows_count="20" class="table table-bordered table-striped table-sm datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Department Name</th>
                                        <th>Department Code</th>
                                        <th width="15%">Students Count</th>
                                        <th>Head of Department</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$departments.'</tbody>
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