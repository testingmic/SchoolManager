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
$pageTitle = "Building Details";
$response->title = $pageTitle;

$item_id = $SITEURL[1] ?? null;

// set the parent menu
$response->parent_menu = "housing";

if(empty($item_id)) {
    $response->html = page_not_found();
} else {

    $init_param = (object) ["client_data" => $defaultClientData];
    $params = (object) [
        "building_id" => $item_id,
        "clientId" => $clientId,
        "load_blocks" => true
    ];
    $data = load_class("Buildings", "controllers/housing", $init_param)->view($params);
    print_r($data);
    exit;
    if(empty($data)) {
        $response->html = page_not_found();
    } else {

        $hasUpdate = $accessObject->hasAccess("update", "housing");
        $hasDelete = $accessObject->hasAccess("delete", "housing");

        $actionButtons = "";
        if($hasUpdate) {
            $actionButtons .= "<a href='#' onclick='return load(\"building/{$data->item_id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i> Update</a> ";
        }
        if($hasDelete) {
            $actionButtons .= "<a href='#' onclick='return delete_record(\"{$data->item_id}\", \"housing_building\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a>";
        }

        $facilitiesList = "";
        if(!empty($data->facilities) && is_array($data->facilities)) {
            $housingController = load_class("housing", "controllers", (object) ["client_data" => $defaultClientData]);
            $housingData = $housingController->housingData;
            foreach($data->facilities as $facility) {
                $facilityName = $housingData['housingFacilities'][$facility] ?? $facility;
                $facilitiesList .= "<span class='badge badge-info mr-1 mb-1'>{$facilityName}</span>";
            }
        }

        $blocksList = "";
        if(!empty($data->blocks_list)) {
            foreach($data->blocks_list as $block) {
                $blocksList .= "<tr>
                    <td><a href='#' onclick='return load(\"block/{$block->item_id}\");'>{$block->name}</a></td>
                    <td>{$block->code}</td>
                    <td class='text-center'>{$block->rooms_count}</td>
                    <td class='text-center'>{$block->beds_count}</td>
                    <td class='text-center'>
                        <a href='#' onclick='return load(\"block/{$block->item_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>
                    </td>
                </tr>";
            }
        } else {
            $blocksList = "<tr><td colspan='5' class='text-center'>No blocks found</td></tr>";
        }

        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-building"></i> '.$data->name.'</h1>
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
                            <h4>Building Information</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Code:</strong></td>
                                    <td>{$data->code}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td>'.ucfirst(str_replace('_', ' ', $data->building_type)).'</td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td>'.ucfirst(str_replace('_', ' ', $data->gender_restriction)).'</td>
                                </tr>
                                <tr>
                                    <td><strong>Capacity:</strong></td>
                                    <td>'.($data->capacity ?? 'Not set').'</td>
                                </tr>
                                <tr>
                                    <td><strong>Blocks:</strong></td>
                                    <td>'.$data->blocks_count.'</td>
                                </tr>
                                <tr>
                                    <td><strong>Rooms:</strong></td>
                                    <td>'.$data->rooms_count.'</td>
                                </tr>
                                <tr>
                                    <td><strong>Beds:</strong></td>
                                    <td>'.$data->beds_count.'</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge badge-'.($data->status == '1' ? 'success' : 'danger').'">'.($data->status == '1' ? 'Active' : 'Inactive').'</span></td>
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
                            <h4>Address</h4>
                        </div>
                        <div class="card-body">
                            <p>'.nl2br(htmlspecialchars($data->address ?? 'No address provided')).'</p>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Facilities</h4>
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
                            <h4>Blocks in this Building</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Block Name</th>
                                            <th>Code</th>
                                            <th class="text-center">Rooms</th>
                                            <th class="text-center">Beds</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>'.$blocksList.'</tbody>
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
