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
$pageTitle = "Update Building";
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
        "building_id" => $item_id,
        "clientId" => $clientId
    ];
    $data = load_class("Buildings", "controllers/housing", $init_param)->list($params);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $_data = $data["data"][0];
        
        $housingController = load_class("housing", "controllers", (object) ["client_data" => $defaultClientData]);
        $housingData = $housingController->housingData;

        $buildingTypes = "";
        foreach($housingData['buildingType'] as $key => $value) {
            $selected = ($_data->building_type == $key) ? "selected" : "";
            $buildingTypes .= "<option value='{$key}' {$selected}>{$value}</option>";
        }

        $genderRestrictions = "";
        foreach($housingData['genderRestriction'] as $key => $value) {
            $selected = ($_data->gender_restriction == $key) ? "selected" : "";
            $genderRestrictions .= "<option value='{$key}' {$selected}>{$value}</option>";
        }

        $facilities = "";
        $existingFacilities = !empty($_data->facilities) ? $_data->facilities : [];
        foreach($housingData['housingFacilities'] as $key => $value) {
            $checked = in_array($key, $existingFacilities) ? "checked" : "";
            $facilities .= "<div class='form-check form-check-inline'>
                <input class='form-check-input' type='checkbox' name='facilities[]' id='facility_{$key}' value='{$key}' {$checked}>
                <label class='form-check-label' for='facility_{$key}'>{$value}</label>
            </div>";
        }

        $the_form = "
        <form class='ajax-data-form' id='ajax-data-form-content' action='{$baseUrl}api/housing/buildings/update' method='PUT' data-module='housing_building'>
            <input type='hidden' name='building_id' value='{$_data->item_id}'>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Building Name <span class='text-danger'>*</span></label>
                        <input type='text' name='name' class='form-control' value='{$_data->name}' required>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Building Code</label>
                        <input type='text' name='code' class='form-control' value='{$_data->code}'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Building Type <span class='text-danger'>*</span></label>
                        <select name='building_type' class='form-control' required>
                            <option value=''>Select Type</option>
                            {$buildingTypes}
                        </select>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Gender Restriction</label>
                        <select name='gender_restriction' class='form-control'>
                            {$genderRestrictions}
                        </select>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Capacity</label>
                        <input type='number' name='capacity' class='form-control' min='1' value='{$_data->capacity}' placeholder='Total capacity'>
                    </div>
                </div>
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
                <label>Address</label>
                <textarea name='address' class='form-control' rows='2'>".htmlspecialchars($_data->address ?? '')."</textarea>
            </div>
            <div class='form-group'>
                <label>Description</label>
                <textarea name='description' class='form-control' rows='3'>".htmlspecialchars($_data->description ?? '')."</textarea>
            </div>
            <div class='form-group'>
                <label>Facilities</label>
                <div class='border p-3 rounded'>
                    {$facilities}
                </div>
            </div>
            <div class='form-group text-right'>
                <button type='button-submit' class='btn btn-primary'><i class='fa fa-save'></i> Update Building</button>
                <a href='{$baseUrl}buildings' class='btn btn-secondary'>Cancel</a>
            </div>
        </form>";

        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-building"></i> '.$pageTitle.'</h1>
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
