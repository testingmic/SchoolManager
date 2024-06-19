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
$response->title = "Sections List";
$response->scripts = [];

$department_param = (object) [
    "clientId" => $session->clientId
];

$department_list = load_class("sections", "controllers")->list($department_param);

$hasDelete = $accessObject->hasAccess("delete", "section");
$hasUpdate = $accessObject->hasAccess("update", "section");

$sections = "";
foreach($department_list["data"] as $key => $each) {
    
    $action = "<a title='Click to view section record' href='#' onclick='return load(\"section/{$each->id}/view\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Section' onclick='return delete_record(\"{$each->id}\", \"section\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $sections .= "<tr data-row_id=\"{$each->id}\">";
    $sections .= "<td>".($key+1)."</td>";
    $sections .= "<td><a href='#' class='text-uppercase font-weight-bold' onclick='return load(\"section/{$each->id}\");'>{$each->name}</a></td>";
    $sections .= "<td>{$each->section_code}</td>";
    $sections .= "<td>{$each->students_count}</td>";
    $sections .= "<td><span ".(isset($each->section_leader_info->name) ? "onclick='return load(\"student/{$each->section_leader_info->user_id}\")'" : null)." class='user_name'>".($each->section_leader_info->name ?? null)."</span></td>";
    $sections .= "<td align='center'>{$action}</td>";
    $sections .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-school"></i> Sections List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Sections</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Section Name</th>
                                        <th>Section Code</th>
                                        <th width="15%">Students Count</th>
                                        <th>Section Leader</th>
                                        <th align="center" width="12%"></th>
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