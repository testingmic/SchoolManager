<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $isSupport, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$data = [];
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Add Article";
$response->title = $pageTitle;
$response->scripts = ["assets/js/upload.js"];

// not found
if(confirm_url_id(1, "modify") && empty(confirm_url_id(2)) || !$isSupport) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// get the information
elseif(confirm_url_id(2)) {
    // get the value
    $knowledge_id = $SITEURL[2];

    // load the record
    $item_param = (object) [
        "clientId" => $clientId,
        "knowledge_id" => $knowledge_id,
        "client_data" => $defaultUser->client
    ];

    // get the list of all the templates
    $support_array = load_class("support", "controllers", $item_param)->knowledgebase_list($item_param)["data"];

    // if the data is empty
    if(empty($support_array)) {
        $response->html = page_not_found("not_found");
        echo json_encode($response);
        exit;
    }

    // get the data
    $data = $support_array[0];
    $pageTitle = "Modify Article";
}
$the_form = load_class("forms", "controllers")->knowledgebase_form($data);


$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'knowledgebase">Knowledgebase</a></div>
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