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
$response->title = "Sections List : {$appName}";
$response->scripts = [];

$department_param = (object) [
    "clientId" => $session->clientId,
    "limit" => 99999
];

$department_list = load_class("sections", "controllers")->list($department_param);

$hasDelete = $accessObject->hasAccess("delete", "section");
$hasUpdate = $accessObject->hasAccess("update", "section");

$sections = "";
foreach($department_list["data"] as $key => $each) {
    
    $action = "<a title='Click to view section record' href='#' onclick='return loadPage(\"{$baseUrl}update-section/{$each->id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to update section record' href='#' onclick='return loadPage(\"{$baseUrl}update-section/{$each->id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Section' onclick='return delete_record(\"{$each->id}\", \"section\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $sections .= "<tr data-row_id=\"{$each->id}\">";
    $sections .= "<td>".($key+1)."</td>";
    $sections .= "<td><img class='rounded-circle author-box-picture' width='40px' src=\"{$baseUrl}{$each->image}\"> &nbsp; {$each->name}</td>";
    $sections .= "<td>{$each->section_code}</td>";
    $sections .= "<td>{$each->students_count}</td>";
    $sections .= "<td><span class='underline'>".($each->section_leader_info->name ?? null)."</span></td>";
    $sections .= "<td align='center'>{$action}</td>";
    $sections .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Sections List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Sections List</div>
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
                                        <th>Section Name</th>
                                        <th>Section Code</th>
                                        <th width="15%">Students Count</th>
                                        <th>Section Leader</th>
                                        <th align="center" width="13%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$sections.'</tbody>
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