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
$hasAdd = $accessObject->hasAccess("add", "department");
$count = 0;
$departments = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "&nbsp;<a title='Click to update department record' href='#' onclick='return load(\"department/{$each->id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    $count++;
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the class record' href='#' onclick='return load(\"department/{$each->id}/update\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Department' onclick='return delete_record(\"{$each->id}\", \"department\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $departments .= "<tr data-row_id=\"{$each->id}\">";
    $departments .= "<td>
    <div class='flex items-center space-x-4'>
        <div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'
                stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'
                class='lucide lucide-tag h-6 w-6 text-white' aria-hidden='true'>
                <path
                    d='M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z'></path>
                <circle cx='7.5' cy='7.5' r='.5' fill='currentColor'></circle>
            </svg>
        </div>
        <div>
            <span class='bold_cursor text-info' onclick='return load(\"department/{$each->id}\");'>{$each->name}</span>
            <p class='text-xs text-gray-500'>{$each->department_code}</p>
        </div>
    </div>
    </td>";
    $departments .= "<td>{$each->department_code}</td>";
    $departments .= "<td>{$each->students_count}</td>";
    $departments .= "<td><span class='underline'>".($each->department_head_info->name ?? null)."</span></td>";
    $departments .= "<td class='text-center'>{$each->reporting_time}</td>";
    $departments .= "<td class='text-center'>{$action}</td>";
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
                <div class="text-right mb-2">
                    '.($hasAdd ? '
                        <a class="btn btn-outline-success" href="'.$baseUrl.'department_add"><i class="fa fa-list"></i> Create New Department</a>' : ''
                    ).'
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" data-rows_count="20" class="table table-bordered table-striped table-sm datatable">
                                <thead>
                                    <tr>
                                        <th>Department Name</th>
                                        <th>Department Code</th>
                                        <th width="15%">Students / Staff Count</th>
                                        <th>Head of Department</th>
                                        <th class="text-center">Latest Reporting Time</th>
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