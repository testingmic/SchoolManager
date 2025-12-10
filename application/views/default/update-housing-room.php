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
$pageTitle = "Update Room";
$response->title = $pageTitle;
$response->scripts = [
    "assets/js/index.js"
];

$hasUpdate = $accessObject->hasAccess("update", "housing");
$item_id = $SITEURL[1] ?? null;

// set the parent menu
$response->parent_menu = "housing";

// ensure the the id has been parsed
if(empty($item_id) || !$hasUpdate) {
    $response->html = page_not_found();
} else {

    $init_param = (object) ["client_data" => $defaultClientData];
    $params = (object) [
        "room_id" => $item_id,
        "clientId" => $clientId
    ];
    $data = load_class("Rooms", "controllers/housing", $init_param)->list($params);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $_data = $data["data"][0];
        
        // Get blocks list
        $blocksController = load_class("Blocks", "controllers/housing", $init_param);
        $blocksParams = (object) ["clientId" => $clientId, "limit" => 100];
        $blocksList = $blocksController->list($blocksParams);

        $blocksOptions = "<option value=''>Select Block</option>";
        foreach($blocksList["data"] as $block) {
            $selected = ($_data->block_id == $block->item_id) ? "selected" : "";
            $blocksOptions .= "<option value='{$block->item_id}' {$selected}>{$block->name} ({$block->code})</option>";
        }

        $housingController = load_class("housing", "controllers", (object) ["client_data" => $defaultClientData]);
        $housingData = $housingController->housingData;

        $roomTypes = "";
        foreach($housingData['roomType'] as $key => $value) {
            $selected = ($_data->room_type == $key) ? "selected" : "";
            $roomTypes .= "<option value='{$key}' {$selected}>{$value}</option>";
        }

        $roomConditions = "";
        foreach($housingData['roomCondition'] as $key => $value) {
            $selected = ($_data->room_condition == $key) ? "selected" : "";
            $roomConditions .= "<option value='{$key}' {$selected}>{$value}</option>";
        }

        $facilities = "";
        $existingFacilities = !empty($_data->facilities) ? $_data->facilities : [];
        foreach($housingData['roomFacilities'] as $key => $value) {
            $checked = in_array($key, $existingFacilities) ? "checked" : "";
            $facilities .= "<div class='form-check form-check-inline'>
                <input class='form-check-input' type='checkbox' name='facilities[]' id='facility_{$key}' value='{$key}' {$checked}>
                <label class='form-check-label' for='facility_{$key}'>{$value}</label>
            </div>";
        }

        $the_form = "
        <form class='ajax-data-form' id='ajax-data-form-content' action='{$baseUrl}api/housing/rooms/update' method='PUT' data-module='housing_room'>
            <input type='hidden' name='room_id' value='{$_data->item_id}'>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Room Name <span class='text-danger'>*</span></label>
                        <input type='text' name='name' class='form-control' value='{$_data->name}' required>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Room Code</label>
                        <input type='text' name='code' class='form-control' value='{$_data->code}'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Block <span class='text-danger'>*</span></label>
                        <select name='block_id' class='form-control' required>
                            {$blocksOptions}
                        </select>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Room Type</label>
                        <select name='room_type' class='form-control'>
                            {$roomTypes}
                        </select>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Room Condition</label>
                        <select name='room_condition' class='form-control'>
                            {$roomConditions}
                        </select>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Capacity</label>
                        <input type='number' name='capacity' class='form-control' min='1' value='{$_data->capacity}' placeholder='Number of beds'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Status</label>
                        <select name='status' class='form-control'>
                            <option value='1' ".($_data->status == '1' ? 'selected' : '').">Active</option>
                            <option value='0' ".($_data->status == '0' ? 'selected' : '').">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class='form-group'>
                <label>Description</label>
                <textarea name='description' class='form-control' rows='3'>".htmlspecialchars($_data->description ?? '')."</textarea>
            </div>
            <div class='form-group'>
                <label>Room Facilities</label>
                <div class='border p-3 rounded'>
                    {$facilities}
                </div>
            </div>
            <div class='form-group text-right'>
                <button type='button-submit' class='btn btn-primary'><i class='fa fa-save'></i> Update Room</button>
                <a href='{$baseUrl}housing-rooms' class='btn btn-secondary'>Cancel</a>
            </div>
        </form>";

        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-door-open"></i> '.$pageTitle.'</h1>
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

    }
}
// print out the response
echo json_encode($response);
?>
