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
$pageTitle = "Add Bed";
$response->title = $pageTitle;
$response->scripts = [
    "assets/js/index.js"
];

$room_id = $SITEURL[1] ?? null;

// set the parent menu
$response->parent_menu = "housing";

// Get rooms list
$init_param = (object) ["client_data" => $defaultClientData];
$roomsController = load_class("Rooms", "controllers/housing", $init_param);
$roomsParams = (object) ["clientId" => $clientId, "limit" => 100];
$roomsList = $roomsController->list($roomsParams);

$roomsOptions = "<option value=''>Select Room</option>";
foreach($roomsList["data"] as $room) {
    $selected = ($room_id == $room->item_id) ? "selected" : "";
    $roomsOptions .= "<option value='{$room->item_id}' {$selected}>{$room->name} ({$room->code})</option>";
}

$the_form = "
<form class='ajax-data-form' id='ajax-data-form-content' action='{$baseUrl}api/housing/beds/create' method='POST' data-module='housing_bed'>
    <div class='row'>
        <div class='col-md-6'>
            <div class='form-group'>
                <label>Bed Name</label>
                <input type='text' name='name' class='form-control' placeholder='Bed name'>
            </div>
        </div>
        <div class='col-md-6'>
            <div class='form-group'>
                <label>Bed Code</label>
                <input type='text' name='code' class='form-control' placeholder='Auto-generated if left empty'>
            </div>
        </div>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <div class='form-group'>
                <label>Room <span class='text-danger'>*</span></label>
                <select name='room_id' class='form-control' required>
                    {$roomsOptions}
                </select>
            </div>
        </div>
        <div class='col-md-6'>
            <div class='form-group'>
                <label>Bed Number</label>
                <input type='text' name='bed_number' class='form-control' placeholder='Bed number'>
            </div>
        </div>
    </div>
    <div class='form-group'>
        <label>Description</label>
        <textarea name='description' class='form-control' rows='3'></textarea>
    </div>
    <div class='row'>
        <div class='col-md-6'>
            <div class='form-group'>
                <label>Status</label>
                <select name='status' class='form-control'>
                    <option value='1'>Active</option>
                    <option value='0'>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class='form-group text-right'>
        <button type='button-submit' class='btn btn-primary'><i class='fa fa-save'></i> Save Bed</button>
        <a href='{$baseUrl}beds".(!empty($room_id) ? "/{$room_id}" : "")."' class='btn btn-secondary'>Cancel</a>
    </div>
</form>";

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-bed"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'buildings">Buildings</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">'.$the_form.'</div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>
