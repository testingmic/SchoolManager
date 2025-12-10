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
$pageTitle = "Add Building";
$response->title = $pageTitle;
$response->scripts = [
    "assets/js/index.js"
];

// set the parent menu
$response->parent_menu = "housing";

$housingController = load_class("housing", "controllers", (object) ["client_data" => $defaultClientData]);
$housingData = $housingController->housingData;

$buildingTypes = "";
foreach($housingData['buildingType'] as $key => $value) {
    $buildingTypes .= "<option value='{$key}'>{$value}</option>";
}

$genderRestrictions = "";
foreach($housingData['genderRestriction'] as $key => $value) {
    $genderRestrictions .= "<option value='{$key}'>{$value}</option>";
}

$facilities = "";
foreach($housingData['housingFacilities'] as $key => $value) {
    $facilities .= "<div class='form-check form-check-inline'>
        <input class='form-check-input' type='checkbox' name='facilities[]' id='facility_{$key}' value='{$key}'>
        <label class='form-check-label' for='facility_{$key}'>{$value}</label>
    </div>";
}

$the_form = "
<form class='ajax-data-form' id='ajax-data-form-content' action='{$baseUrl}api/housing/buildings/create' method='POST' data-module='housing_building'>
    <div class='row'>
        <div class='col-md-6'>
            <div class='form-group'>
                <label>Building Name <span class='text-danger'>*</span></label>
                <input type='text' name='name' class='form-control' required>
            </div>
        </div>
        <div class='col-md-6'>
            <div class='form-group'>
                <label>Building Code</label>
                <input type='text' name='code' class='form-control' placeholder='Auto-generated if left empty'>
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
                <input type='number' name='capacity' class='form-control' min='1' placeholder='Total capacity'>
            </div>
        </div>
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
    <div class='form-group'>
        <label>Address</label>
        <textarea name='address' class='form-control' rows='2'></textarea>
    </div>
    <div class='form-group'>
        <label>Description</label>
        <textarea name='description' class='form-control' rows='3'></textarea>
    </div>
    <div class='form-group'>
        <label>Facilities</label>
        <div class='border p-3 rounded'>
            {$facilities}
        </div>
    </div>
    <div class='form-group text-right'>
        <button type='button-submit' class='btn btn-primary'><i class='fa fa-save'></i> Save Building</button>
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
// print out the response
echo json_encode($response);
?>
