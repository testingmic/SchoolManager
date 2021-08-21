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
$response->title = "Departments List : {$appName}";
$response->scripts = [];

$department_param = (object) [
    "clientId" => $session->clientId,
    "limit" => 99999
];

$item_list = load_class("departments", "controllers")->list($department_param);

$hasDelete = $accessObject->hasAccess("delete", "department");
$hasUpdate = $accessObject->hasAccess("update", "department");

$departments = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "&nbsp;<a title='Click to update department record' href='#' onclick='return loadPage(\"{$baseUrl}update-department/{$each->id}\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Department' onclick='return delete_record(\"{$each->id}\", \"department\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $departments .= "<tr data-row_id=\"{$each->id}\">";
    $departments .= "<td>".($key+1)."</td>";
    $departments .= "<td><a href='#' class='text-uppercase font-weight-bold' onclick='return loadPage(\"{$baseUrl}update-department/{$each->id}\");'>{$each->name}</a></td>";
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
                            <table data-empty="" class="table table-bordered table-striped datatable">
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