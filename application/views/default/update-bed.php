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
$pageTitle = "Update Bed";
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
        "bed_id" => $item_id,
        "clientId" => $clientId,
        "load_occupants" => true
    ];
    $data = load_class("Beds", "controllers/housing", $init_param)->list($params);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $_data = $data["data"][0];
        
        // Get rooms list
        $roomsController = load_class("Rooms", "controllers/housing", $init_param);
        $roomsParams = (object) ["clientId" => $clientId, "limit" => 100];
        $roomsList = $roomsController->list($roomsParams);

        $roomsOptions = "<option value=''>Select Room</option>";
        foreach($roomsList["data"] as $room) {
            $selected = ($_data->room_id == $room->item_id) ? "selected" : "";
            $roomsOptions .= "<option value='{$room->item_id}' {$selected}>{$room->name} ({$room->code})</option>";
        }

        // Get students list for assignment
        $usersController = load_class("users", "controllers", $init_param);
        $studentsParams = (object) [
            "clientId" => $clientId,
            "user_type" => "student",
            "user_status" => "Active",
            "limit" => 500
        ];
        $studentsList = $usersController->list($studentsParams);

        $studentsOptions = "<option value=''>No Student Assigned</option>";
        foreach($studentsList["data"] ?? [] as $student) {
            $selected = (!empty($_data->student_id) && $_data->student_id == $student->user_id) ? "selected" : "";
            $studentsOptions .= "<option value='{$student->user_id}' {$selected}>{$student->name} ({$student->unique_id})</option>";
        }

        $the_form = "
        <form class='ajax-data-form' id='ajax-data-form-content' action='{$baseUrl}api/housing/beds/update' method='PUT' data-module='housing_bed'>
            <input type='hidden' name='bed_id' value='{$_data->item_id}'>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Bed Name</label>
                        <input type='text' name='name' class='form-control' value='{$_data->name}'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Bed Code</label>
                        <input type='text' name='code' class='form-control' value='{$_data->code}'>
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
                        <input type='text' name='bed_number' class='form-control' value='{$_data->bed_number}'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Assign Student</label>
                        <select name='student_id' class='form-control'>
                            {$studentsOptions}
                        </select>
                        <small class='form-text text-muted'>Leave empty to unassign student</small>
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
                <label>Description</label>
                <textarea name='description' class='form-control' rows='3'>".htmlspecialchars($_data->description ?? '')."</textarea>
            </div>
            <div class='form-group text-right'>
                <button type='button-submit' class='btn btn-primary'><i class='fa fa-save'></i> Update Bed</button>
                <a href='{$baseUrl}beds' class='btn btn-secondary'>Cancel</a>
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

    }
}
// print out the response
echo json_encode($response);
?>
