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
$pageTitle = "Beds List";
$response->title = $pageTitle;

$room_id = $SITEURL[1] ?? null;

$params = (object) [
    "clientId" => $session->clientId,
    "room_id" => $room_id,
    "load_occupants" => true,
    "load_room" => true,
    "load_block" => true,
    "load_building" => true,
    "limit" => $myClass->global_limit
];

$init_param = (object) ["client_data" => $defaultClientData];
$item_list = load_class("Beds", "controllers/housing", $init_param)->list($params);

$hasAdd = $accessObject->hasAccess("create", "housing");
$hasDelete = $accessObject->hasAccess("delete", "housing");
$hasUpdate = $accessObject->hasAccess("update", "housing");

// set the parent menu
$response->parent_menu = "housing";

$count = 0;
$beds_list = "";
foreach($item_list["data"] as $key => $each) {

    $action = "<a title='View Bed record' href='#' onclick='return load(\"bed/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the bed record' href='#' onclick='return load(\"bed/{$each->item_id}/update\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Delete this Bed' onclick='return delete_record(\"{$each->item_id}\", \"housing_bed\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $count++;
    $occupiedBadge = $each->occupied == '1' ? "danger" : "success";
    $studentName = !empty($each->student_info) ? $each->student_info->name : '-';
    
    $beds_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $beds_list .= "<td class='text-center'>".($count)."</td>";
    $beds_list .= "<td>
        <div class='flex items-center space-x-4'>
            <div class='h-12 w-12 bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 rounded-xl flex items-center justify-center shadow-lg'>
                <i class='fa fa-bed text-white text-xl'></i>
            </div>
            <div>
                <span class='bold_cursor text-info' onclick='return load(\"bed/{$each->item_id}\");'>{$each->code}</span>
                <p class='text-xs text-gray-500'>".($each->bed_number ?? '-')."</p>
            </div>
        </div>
    </td>";
    $beds_list .= "<td>".($each->room_info->name ?? '-')."</td>";
    $beds_list .= "<td>".($each->block_info->name ?? '-')."</td>";
    $beds_list .= "<td>".($each->building_info->name ?? '-')."</td>";
    $beds_list .= "<td><span class='badge badge-{$occupiedBadge}'>".($each->occupied == '1' ? 'Occupied' : 'Available')."</span></td>";
    $beds_list .= "<td>".$studentName."</td>";
    $beds_list .= "<td class='text-center'>{$action}</td>";
    $beds_list .= "</tr>";
}

$roomName = !empty($item_list["data"][0]->room_info->name) ? " - " . $item_list["data"][0]->room_info->name : "";

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-bed"></i> '.$pageTitle.$roomName.'</h1>
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
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'add-bed'.(!empty($room_id) ? '/'.$room_id : '').'"><i class="fa fa-plus"></i> Add Bed</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="15%">Bed Code</th>
                                        <th width="15%">Room</th>
                                        <th width="15%">Block</th>
                                        <th width="15%">Building</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Student</th>
                                        <th align="center" width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>'.$beds_list.'</tbody>
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
