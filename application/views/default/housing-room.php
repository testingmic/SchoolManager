<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultClientData;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Room Details";
$response->title = $pageTitle;

$item_id = $SITEURL[1] ?? null;

// set the parent menu
$response->parent_menu = "housing";

if(empty($item_id)) {
    $response->html = page_not_found();
} else {

    $init_param = (object) ["client_data" => $defaultClientData];
    $params = (object) [
        "room_id" => $item_id,
        "clientId" => $clientId,
        "load_beds" => true,
        "load_block" => true,
        "load_building" => true
    ];
    $data = load_class("Rooms", "controllers/housing", $init_param)->view($params);

    if(empty($data) || isset($data["code"])) {
        $response->html = page_not_found();
    } else {

        $hasUpdate = $accessObject->hasAccess("update", "housing");
        $hasDelete = $accessObject->hasAccess("delete", "housing");

        $actionButtons = "";
        if($hasUpdate) {
            $actionButtons .= "<a href='#' onclick='return load(\"housing-room/{$data->item_id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i> Update</a> ";
        }
        if($hasDelete) {
            $actionButtons .= "<a href='#' onclick='return delete_record(\"{$data->item_id}\", \"housing_room\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a>";
        }

        $facilitiesList = "";
        if(!empty($data->facilities) && is_array($data->facilities)) {
            $housingController = load_class("housing", "controllers", (object) ["client_data" => $defaultClientData]);
            $housingData = $housingController->housingData;
            foreach($data->facilities as $facility) {
                $facilityName = $housingData['roomFacilities'][$facility] ?? $facility;
                $facilitiesList .= "<span class='badge badge-info mr-1 mb-1'>{$facilityName}</span>";
            }
        }

        $bedsList = "";
        if(!empty($data->beds_list)) {
            foreach($data->beds_list as $bed) {
                $occupiedBadge = $bed->occupied == '1' ? "danger" : "success";
                $studentName = !empty($bed->student_info) ? $bed->student_info->name : '-';
                $bedsList .= "<tr>
                    <td><a href='#' onclick='return load(\"bed/{$bed->item_id}\");'>{$bed->code}</a></td>
                    <td>".($bed->bed_number ?? '-')."</td>
                    <td><span class='badge badge-{$occupiedBadge}'>".($bed->occupied == '1' ? 'Occupied' : 'Available')."</span></td>
                    <td>{$studentName}</td>
                    <td class='text-center'>
                        <a href='#' onclick='return load(\"bed/{$bed->item_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>
                    </td>
                </tr>";
            }
        } else {
            $bedsList = "<tr><td colspan='5' class='text-center'>No beds found</td></tr>";
        }

        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-door-open"></i> '.$data->name.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'buildings">Buildings</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Room Information</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Code:</strong></td>
                                    <td>{$data->code}</td>
                                </tr>
                                <tr>
                                    <td><strong>Block:</strong></td>
                                    <td><a href="#" onclick="return load(\'block/'.($data->block_info->block_id ?? '').'\');">'.($data->block_info->name ?? '-').'</a></td>
                                </tr>
                                <tr>
                                    <td><strong>Building:</strong></td>
                                    <td><a href="#" onclick="return load(\'building/'.($data->building_info->building_id ?? '').'\');">'.($data->building_info->name ?? '-').'</a></td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>".ucfirst($data->room_type)."</td>
                                </tr>
                                <tr>
                                    <td><strong>Condition:</strong></td>
                                    <td><span class=\'badge badge-'.($data->room_condition == 'excellent' || $data->room_condition == 'good' ? 'success' : ($data->room_condition == 'fair' ? 'warning' : 'danger')).'\'>'.ucfirst(str_replace('_', ' ', $data->room_condition)).'</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Capacity:</strong></td>
                                    <td>{$data->capacity}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Beds:</strong></td>
                                    <td>{$data->beds_count}</td>
                                </tr>
                                <tr>
                                    <td><strong>Occupied:</strong></td>
                                    <td>{$data->occupied_beds_count}</td>
                                </tr>
                                <tr>
                                    <td><strong>Available:</strong></td>
                                    <td>{$data->available_beds}</td>
                                </tr>
                                <tr>
                                    <td><strong>Occupancy:</strong></td>
                                    <td><span class=\'badge badge-'.($data->occupancy_rate >= 100 ? 'danger' : ($data->occupancy_rate >= 75 ? 'warning' : 'success')).'\'>{$data->occupancy_rate}%</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class=\'badge badge-'.($data->status == '1' ? 'success' : 'danger').'\'>'.($data->status == '1' ? 'Active' : 'Inactive').'</span></td>
                                </tr>
                            </table>
                            <div class="text-center mt-3">
                                '.$actionButtons.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Description</h4>
                        </div>
                        <div class="card-body">
                            <p>'.nl2br(htmlspecialchars($data->description ?? 'No description provided')).'</p>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Room Facilities</h4>
                        </div>
                        <div class="card-body">
                            '.($facilitiesList ? $facilitiesList : '<p class="text-muted">No facilities listed</p>').'
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Beds in this Room</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Bed Code</th>
                                            <th>Bed Number</th>
                                            <th>Status</th>
                                            <th>Student</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>'.$bedsList.'</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';

    }
}
// print out the response
echo json_encode($response);
?>
