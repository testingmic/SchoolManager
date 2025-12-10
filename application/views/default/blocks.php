<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultClientData;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Blocks List";
$response->title = $pageTitle;

$building_id = $SITEURL[1] ?? null;

$params = (object) [
    "clientId" => $session->clientId,
    "building_id" => $building_id,
    "load_rooms" => false,
    "load_building" => true,
    "limit" => $myClass->global_limit
];

$init_param = (object) ["client_data" => $defaultClientData];
$item_list = load_class("Blocks", "controllers/housing", $init_param)->list($params);

$hasAdd = $accessObject->hasAccess("create", "housing");
$hasDelete = $accessObject->hasAccess("delete", "housing");
$hasUpdate = $accessObject->hasAccess("update", "housing");

// set the parent menu
$response->parent_menu = "housing";

$count = 0;
$blocks_list = "";
foreach($item_list["data"] as $key => $each) {

    $action = "<a title='View Block record' href='#' onclick='return load(\"block/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the block record' href='#' onclick='return load(\"block/{$each->item_id}/update\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Delete this Block' onclick='return delete_record(\"{$each->item_id}\", \"housing_block\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $count++;
    $blocks_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $blocks_list .= "<td class='text-center'>".($count)."</td>";
    $blocks_list .= "<td>
        <div class='flex items-center space-x-4'>
            <div class='h-12 w-12 bg-gradient-to-br from-green-500 via-green-600 to-teal-600 rounded-xl flex items-center justify-center shadow-lg'>
                <i class='fa fa-layer-group text-white text-xl'></i>
            </div>
            <div>
                <span class='bold_cursor text-info' onclick='return load(\"block/{$each->item_id}\");'>{$each->name}</span>
                <p class='text-xs text-gray-500'>{$each->code}</p>
            </div>
        </div>
    </td>";
    $blocks_list .= "<td>".($each->building_info->name ?? '-')."</td>";
    $blocks_list .= "<td class='text-center'>".($each->floor_number ?? '-')."</td>";
    $blocks_list .= "<td class='text-center'>{$each->rooms_count}</td>";
    $blocks_list .= "<td class='text-center'>{$each->beds_count}</td>";
    $blocks_list .= "<td class='text-center'>".($each->capacity ?? '-')."</td>";
    $blocks_list .= "<td class='text-center'>{$action}</td>";
    $blocks_list .= "</tr>";
}

$buildingName = !empty($item_list["data"][0]->building_info->name) ? " - " . $item_list["data"][0]->building_info->name : "";

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-layer-group"></i> '.$pageTitle.$buildingName.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'buildings">Buildings</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'add-block'.(!empty($building_id) ? '/'.$building_id : '').'"><i class="fa fa-plus"></i> Add Block</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="25%">Block Name</th>
                                        <th width="20%">Building</th>
                                        <th width="10%" class="text-center">Floor</th>
                                        <th width="10%" class="text-center">Rooms</th>
                                        <th width="10%" class="text-center">Beds</th>
                                        <th width="10%" class="text-center">Capacity</th>
                                        <th align="center" width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>'.$blocks_list.'</tbody>
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
