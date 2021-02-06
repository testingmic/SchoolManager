<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$userId = $session->userId;
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Upload E-Learning Material";
$response->title = "{$pageTitle} : {$appName}";

// load the resource
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

// if the item id is not empty
if(empty($item_id)) {
    $response->html = page_not_found();
} else {
    // make a request for the form
    $params = (object)[
        "limit" => 1,
        "resource_id" => $item_id,
        "userData" => $defaultUser,
        "thisUser" => $defaultUser,
        "clientId" => $clientId,
        "userId" => $userId
    ];

    // request for the data
    $resourceObj = load_class("resources", "controllers");
    $data = $resourceObj->load_resources($params);

    // if the material was not found
    if(empty($data)) {
        $response->html = page_not_found();
    } else {
        
        // get the first key of the array data
        $data = $data[0];

        // append the data when loading the form
        $params->data = $data;
    
        // append the scripts to load as part of this request
        $response->scripts = ["assets/js/resources.js", "assets/js/upload.js"];

        // load the form
        $the_form = load_class("forms", "controllers")->elearning_form($params);

        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'e-learning">E-Learning</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            '.$the_form.'
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