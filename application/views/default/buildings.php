<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Buildings List";
$response->title = $pageTitle;

$params = (object) [
    "clientId" => $session->clientId,
    "load_blocks" => false,
    "limit" => $myClass->global_limit
];

$init_param = (object) ["client_data" => $defaultClientData];
$item_list = load_class("Buildings", "controllers/housing", $init_param)->list($params);

$hasAdd = $accessObject->hasAccess("create", "housing");
$hasDelete = $accessObject->hasAccess("delete", "housing");
$hasUpdate = $accessObject->hasAccess("update", "housing");

// set the parent menu
$response->parent_menu = "housing";

$count = 0;
$buildings_list = "";
foreach($item_list["data"] as $key => $each) {

    $action = "<a title='View Building record' href='#' onclick='return load(\"building/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the building record' href='#' onclick='return load(\"building/{$each->item_id}/update\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Delete this Building' onclick='return delete_record(\"{$each->item_id}\", \"housing_building\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $count++;
    $buildings_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $buildings_list .= "<td class='text-center'>".($count)."</td>";
    $buildings_list .= "<td>
        <div class='flex items-center space-x-4'>
            <div class='h-12 w-12 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg'>
                <i class='fa fa-building text-white text-xl'></i>
            </div>
            <div>
                <span class='bold_cursor text-info' onclick='return load(\"building/{$each->item_id}\");'>{$each->name}</span>
                <p class='text-xs text-gray-500'>{$each->code}</p>
            </div>
        </div>
    </td>";
    $buildings_list .= "<td>".ucfirst(str_replace('_', ' ', $each->building_type))."</td>";
    $buildings_list .= "<td>".ucfirst(str_replace('_', ' ', $each->gender_restriction))."</td>";
    $buildings_list .= "<td class='text-center'>{$each->blocks_count}</td>";
    $buildings_list .= "<td class='text-center'>{$each->rooms_count}</td>";
    $buildings_list .= "<td class='text-center'>{$each->beds_count}</td>";
    $buildings_list .= "<td class='text-center'>".($each->capacity ?? '-')."</td>";
    $buildings_list .= "<td class='text-center'>{$action}</td>";
    $buildings_list .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-building"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Housing</div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'add-building"><i class="fa fa-plus"></i> Add Building</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="25%">Building Name</th>
                                        <th width="12%">Type</th>
                                        <th width="12%">Gender</th>
                                        <th width="10%" class="text-center">Blocks</th>
                                        <th width="10%" class="text-center">Rooms</th>
                                        <th width="10%" class="text-center">Beds</th>
                                        <th width="10%" class="text-center">Capacity</th>
                                        <th align="center" width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>'.$buildings_list.'</tbody>
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
