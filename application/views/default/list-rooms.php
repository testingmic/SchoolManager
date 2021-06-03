<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$pageTitle = "Class Rooms";
$response->title = "{$pageTitle} : {$appName}";

$params = (object) [
    "clientId" => $session->clientId,
    "limit" => 9999
];

$item_list = load_class("rooms", "controllers")->list($params);

$hasAdd = $accessObject->hasAccess("add", "library");
$hasDelete = $accessObject->hasAccess("delete", "library");
$hasUpdate = $accessObject->hasAccess("update", "library");

$rooms_list = "";
foreach($item_list["data"] as $key => $each) {

    $action = "";
    
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to update class room record' href='#' onclick='return loadPage(\"{$baseUrl}update-room/{$each->item_id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this class room' onclick='return delete_record(\"{$each->item_id}\", \"class_room\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $rooms_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $rooms_list .= "<td>".($key+1)."</td>";
    $rooms_list .= "<td>{$each->name}</td>";
    $rooms_list .= "<td>{$each->code}</td>";
    $rooms_list .= "<td>{$each->capacity}</td>";
    $rooms_list .= "<td align='center'>{$action}</td>";
    $rooms_list .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.' List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.' List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'add-room"><i class="fa fa-plus"></i> Add Class Room</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="40%">Name</th>
                                        <th>Code</th>
                                        <th width="15%">Capacity</th>
                                        <th align="center" width="13%"></th>
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