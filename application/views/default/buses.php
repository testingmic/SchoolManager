<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Manage Buses";
$response->title = $pageTitle;

// set the item unique id
$bus_id = confirm_url_id(1) ? $SITEURL[1] : null;

// set the query object parameter
$param = (object)[
    "bus_id" => $bus_id ?? null,
    "clientId" => $clientId,
];

// if the user has the permission
$hasView = $accessObject->hasAccess("view", "buses");

// if the user does not have the required permissions
if(!$hasView) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// confirm that the school has the documents manager feature enabled
if(!in_array("bus_manager", $clientFeatures)) {
    // permission denied
    $response->html = page_not_found("not_found");
} else {

    // permission to modify and validate
    $hasModify = $accessObject->hasAccess("update", "buses");
    $hasCreate = $accessObject->hasAccess("add", "buses");

    // set the user permissions
    $permissions = [
        "hasView" => $hasView,
        "hasModify" => $hasModify,
        "hasCreate" => $hasCreate
    ];

    // create a bus object
    $busObj = load_class("buses", "controllers");

    // get the bus record
    $buses_list = "<div class='buses_list_container'><div class='row'>";
    $buses = $busObj->list($param)["data"];
    $buses_array_list = [];

    // buses list
    if(empty($buses) || !is_array($buses)) {
        // no bus information found
        $buses_list .= "<div class='col-lg-12' id='no_record_found_main_container'>";
        $buses_list .= no_record_found("No Bus Found", "No bus has been created yet. Get started by creating your first bus.", null, "Bus");    
        $buses_list .= "</div>";
        // convert the buses_array_list to an object
        $buses_array_list = (object) [];
    } else {
        // loop through the buses list
        foreach($buses as $bus) {
            // append to the array
            $buses_array_list[$bus->item_id] = $bus;
            // format the bus
            $buses_list .= format_bus_item($bus, false, false, "col-lg-4 col-md-6 col-sm-6", $permissions);
        }
    }

    // also return the buses array list
    $response->array_stream["buses_array_list"] = $buses_array_list;

    $buses_list .= "</div></div>";

    // get the bus form
    $the_bus_form = load_class("forms", "controllers")->bus_form();

    // uploads script
    $response->scripts = ["assets/js/buses.js","assets/js/upload.js"];

    // document information
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-bus"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">

                <div class="col-12 col-sm-12 col-md-12">
                    '.($hasCreate ? 
                        '<div class="text-right mb-3">
                            <a class="btn btn-sm btn-outline-primary" onclick="return bus_modal();" href="#">
                                <i class="fa fa-plus"></i> Add New Bus
                            </a>
                        </div>' : null
                    ).'
                    <div class="card-card">
                        '.$buses_list.'
                    </div>
                </div>

            </div>
        </section>'.$the_bus_form;

}

// print out the response
echo json_encode($response);
?>