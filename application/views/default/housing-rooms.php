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
$pageTitle = "Rooms List";
$response->title = $pageTitle;

$block_id = $SITEURL[1] ?? null;

$params = (object) [
    "clientId" => $session->clientId,
    "block_id" => $block_id,
    "load_beds" => false,
    "load_block" => true,
    "load_building" => true,
    "limit" => $myClass->global_limit
];

$init_param = (object) ["client_data" => $defaultClientData];
$item_list = load_class("Rooms", "controllers/housing", $init_param)->list($params);

$hasAdd = $accessObject->hasAccess("create", "housing");
$hasDelete = $accessObject->hasAccess("delete", "housing");
$hasUpdate = $accessObject->hasAccess("update", "housing");

// set the parent menu
$response->parent_menu = "housing";

$count = 0;
$rooms_list = "";
foreach($item_list["data"] as $key => $each) {

    $action = "<a title='View Room record' href='#' onclick='return load(\"housing-room/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the room record' href='#' onclick='return load(\"housing-room/{$each->item_id}/update\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Delete this Room' onclick='return delete_record(\"{$each->item_id}\", \"housing_room\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $count++;
    $occupancyBadge = $each->occupancy_rate >= 100 ? "danger" : ($each->occupancy_rate >= 75 ? "warning" : "success");
    
    $rooms_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $rooms_list .= "<td class='text-center'>".($count)."</td>";
    $rooms_list .= "<td>
        <div class='flex items-center space-x-4'>
            <div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 rounded-xl flex items-center justify-center shadow-lg'>
                <i class='fa fa-door-open text-white text-xl'></i>
            </div>
            <div>
                <span class='bold_cursor text-info' onclick='return load(\"housing-room/{$each->item_id}\");'>{$each->name}</span>
                <p class='text-xs text-gray-500'>{$each->code}</p>
            </div>
        </div>
    </td>";
    $rooms_list .= "<td>".($each->block_info->name ?? '-')."</td>";
    $rooms_list .= "<td>".ucfirst($each->room_type)."</td>";
    $rooms_list .= "<td><span class='badge badge-".($each->room_condition == 'excellent' || $each->room_condition == 'good' ? 'success' : ($each->room_condition == 'fair' ? 'warning' : 'danger'))."'>".ucfirst(str_replace('_', ' ', $each->room_condition))."</span></td>";
    $rooms_list .= "<td class='text-center'>{$each->beds_count}</td>";
    $rooms_list .= "<td class='text-center'>{$each->occupied_beds_count}</td>";
    $rooms_list .= "<td class='text-center'>{$each->available_beds}</td>";
    $rooms_list .= "<td class='text-center'><span class='badge badge-{$occupancyBadge}'>{$each->occupancy_rate}%</span></td>";
    $rooms_list .= "<td class='text-center'>{$action}</td>";
    $rooms_list .= "</tr>";
}

$blockName = !empty($item_list["data"][0]->block_info->name) ? " - " . $item_list["data"][0]->block_info->name : "";

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-door-open"></i> '.$pageTitle.$blockName.'</h1>
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
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'add-housing-room'.(!empty($block_id) ? '/'.$block_id : '').'"><i class="fa fa-plus"></i> Add Room</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="20%">Room Name</th>
                                        <th width="15%">Block</th>
                                        <th width="10%">Type</th>
                                        <th width="12%">Condition</th>
                                        <th width="8%" class="text-center">Total Beds</th>
                                        <th width="8%" class="text-center">Occupied</th>
                                        <th width="8%" class="text-center">Available</th>
                                        <th width="8%" class="text-center">Occupancy</th>
                                        <th align="center" width="6%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>'.$rooms_list.'</tbody>
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
