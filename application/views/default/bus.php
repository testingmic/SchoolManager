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
$pageTitle = "Bus Details";
$response->title = $pageTitle;

// if the user has the permission
$hasView = $accessObject->hasAccess("view", "buses");

// if the user does not have the required permissions
if(!$hasView) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// set the item unique id
$bus_id = confirm_url_id(1) ? $SITEURL[1] : null;

// set the query object parameter
$param = (object)[
    "bus_id" => $bus_id ?? null,
    "clientId" => $clientId,
];

// confirm that the school has the documents manager feature enabled
if(!in_array("bus_manager", $clientFeatures) || empty($bus_id)) {
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
        $buses_list .= "
        <div data-element_type='bus' class='text-center empty_div_container col-lg-12 p-2 text-danger'>
            You have not yet added any buses yet
        </div><div data-element_type='bus'></div>";
        
        // convert the buses_array_list to an object
        $buses_array_list = (object) [];
    } else {
        // loop through the buses list
        foreach($buses as $bus) {
            // append to the array
            $buses_array_list[$bus->item_id] = $bus;
            $pageTitle = $pageTitle . ": ".$bus->brand;
            // format the bus
            $buses_list .= format_bus_item($bus, false, true, "col-12", $permissions);
        }
    }

    // also return the buses array list
    $response->array_stream["buses_array_list"] = $buses_array_list;

    $buses_list .= "</div></div>";

    // get the bus form
    $the_bus_form = load_class("forms", "controllers")->bus_form();

    // uploads script
    $response->scripts = ["assets/js/comments.js", "assets/js/buses.js"];

    // document information
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'buses">Buses List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            '.$buses_list.'
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="slim-scroll">
                                <div class="p-0 m-0">
                                    '.leave_comments_builder("bus", $bus_id, false).'
                                    <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$bus_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                    <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>'.$the_bus_form;

}

// print out the response
echo json_encode($response);
?>