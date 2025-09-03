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
$hasAdd = $accessObject->hasAccess("add", "section");

$sections = "";
foreach($department_list["data"] as $key => $each) {
    
    $action = "<a title='Click to view section record' href='#' onclick='return load(\"section/{$each->id}/students\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the section record' href='#' onclick='return load(\"section/{$each->id}/update\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Section' onclick='return delete_record(\"{$each->id}\", \"section\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $color = !empty($each->color_code) ? $each->color_code : "blue";

    $sections .= "<tr data-row_id=\"{$each->id}\">";
    $sections .= "<td>
    <div class='flex items-center space-x-4'>
        <div class='h-12 w-12 bg-gradient-to-br from-{$color}-500 via-{$color}-600 to-{$color}-300 rounded-xl flex items-center justify-center shadow-lg'>
            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-users h-6 w-6 text-white' aria-hidden='true'><path d='M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2'></path><path d='M16 3.128a4 4 0 0 1 0 7.744'></path><path d='M22 21v-2a4 4 0 0 0-3-3.87'></path><circle cx='9' cy='7' r='4'></circle></svg>
        </div>
        <div>
            <span onclick='return load(\"section/{$each->id}\");' class='user_name'>{$each->name}</span>
            <p class='text-xs text-gray-500'>{$each->section_code}</p>
        </div>
    </div>
    </td>";
    $sections .= "<td>{$each->section_code}</td>";
    $sections .= "<td>{$each->students_count}</td>";
    $sections .= "<td><span ".(isset($each->section_leader_info->name) ? "onclick='return load(\"student/{$each->section_leader_info->user_id}\")'" : null)." class='user_name'>".($each->section_leader_info->name ?? null)."</span></td>";
    $sections .= "<td class='text-center'>{$action}</td>";
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
                <div class="text-right mb-2">
                    '.($hasAdd ? '
                        <a class="btn btn-outline-success" href="'.$baseUrl.'sections_add"><i class="fa fa-plus"></i> Create New Section</a>' : ''
                    ).'
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Section Name</th>
                                        <th>Section Code</th>
                                        <th width="15%">Students Count</th>
                                        <th>Section Leader</th>
                                        <th align="center" width="14%"></th>
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