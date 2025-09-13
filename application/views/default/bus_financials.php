<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Bus Financials";
$response->title = $pageTitle;

// if the user has the permission
$hasView = $accessObject->hasAccess("financials", "buses");

// if the user does not have the required permissions
if(!in_array("bus_manager", $clientFeatures)) {
    $response->html = page_not_found("feature_disabled", ["bus_manager"]);
    echo json_encode($response);
    exit;
}

// if the user does not have the required permissions
if(!$hasView) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

 // document information
 $response->html = '
 <section class="section">
     <div class="section-header">
         <h1><i class="fa fa-bus"></i> '.$pageTitle.'</h1>
         <div class="section-header-breadcrumb">
             <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
             <div class="breadcrumb-item active"><a href="'.$baseUrl.'buses">Buses</a></div>
             <div class="breadcrumb-item">'.$pageTitle.'</div>
         </div>
     </div>
     <div class="row">
        <div class="col-12">
            '.no_record_found("Coming soon", "This feature is coming soon. Stay tuned for updates.", null, "Bus Financials", false, "fa fa-chart-line").'
        </div>
     </div>
</section>';

// print out the response
echo json_encode($response);