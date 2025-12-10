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
$pageTitle = "Bed Details";
$response->title = $pageTitle;

$item_id = $SITEURL[1] ?? null;

// set the parent menu
$response->parent_menu = "housing";

if(empty($item_id)) {
    $response->html = page_not_found();
} else {

    $init_param = (object) ["client_data" => $defaultClientData];
    $params = (object) [
        "bed_id" => $item_id,
        "clientId" => $clientId,
        "load_occupants" => true,
        "load_room" => true,
        "load_block" => true,
        "load_building" => true
    ];
    $data = load_class("Beds", "controllers/housing", $init_param)->view($params);

    if(empty($data) || isset($data["code"])) {
        $response->html = page_not_found();
    } else {

        $hasUpdate = $accessObject->hasAccess("update", "housing");
        $hasDelete = $accessObject->hasAccess("delete", "housing");

        $actionButtons = "";
        if($hasUpdate) {
            $actionButtons .= "<a href='#' onclick='return load(\"bed/{$data->item_id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i> Update</a> ";
        }
        if($hasDelete) {
            $actionButtons .= "<a href='#' onclick='return delete_record(\"{$data->item_id}\", \"housing_bed\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a>";
        }

        $studentInfo = "";
        if(!empty($data->student_info)) {
            $studentInfo = "
                <tr>
                    <td><strong>Student Name:</strong></td>
                    <td><a href='#' onclick='return load(\"student/{$data->student_info->student_id}\");'>{$data->student_info->name}</a></td>
                </tr>
                <tr>
                    <td><strong>Student ID:</strong></td>
                    <td>{$data->student_info->unique_id}</td>
                </tr>
                <tr>
                    <td><strong>Gender:</strong></td>
                    <td>".ucfirst($data->student_info->gender ?? '-')."</td>
                </tr>
                <tr>
                    <td><strong>Contact:</strong></td>
                    <td>".($data->student_info->phone_number ?? '-')."</td>
                </tr>";
        } else {
            $studentInfo = "<tr><td colspan='2' class='text-center text-muted'>No student assigned</td></tr>";
        }

        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-bed"></i> '.$data->code.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'buildings">Buildings</a></div>
                    <div class="breadcrumb-item">'.$data->code.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Bed Information</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Code:</strong></td>
                                    <td>{$data->code}</td>
                                </tr>
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>".($data->name ?? '-')."</td>
                                </tr>
                                <tr>
                                    <td><strong>Bed Number:</strong></td>
                                    <td>".($data->bed_number ?? '-')."</td>
                                </tr>
                                <tr>
                                    <td><strong>Room:</strong></td>
                                    <td><a href="#" onclick="return load(\"housing-room/".($data->room_info->room_id ?? '')."\");">".($data->room_info->name ?? '-')."</a></td>
                                </tr>
                                <tr>
                                    <td><strong>Block:</strong></td>
                                    <td><a href="#" onclick="return load(\"block/".($data->block_info->block_id ?? '')."\");">".($data->block_info->name ?? '-')."</a></td>
                                </tr>
                                <tr>
                                    <td><strong>Building:</strong></td>
                                    <td><a href="#" onclick="return load(\"building/".($data->building_info->building_id ?? '')."\");">".($data->building_info->name ?? '-')."</a></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class='badge badge-".($data->occupied == '1' ? 'danger' : 'success')."'>".($data->occupied == '1' ? 'Occupied' : 'Available')."</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Active:</strong></td>
                                    <td><span class='badge badge-".($data->status == '1' ? 'success' : 'danger')."'>".($data->status == '1' ? 'Active' : 'Inactive')."</span></td>
                                </tr>
                            </table>
                            <div class="text-center mt-3">
                                '.$actionButtons.'
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Assigned Student</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                '.$studentInfo.'
                            </table>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>Description</h4>
                        </div>
                        <div class="card-body">
                            <p>".nl2br(htmlspecialchars($data->description ?? 'No description provided'))."</p>
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
