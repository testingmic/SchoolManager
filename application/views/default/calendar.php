<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $accessObject, $academicSession;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Academic Calendar";
$response->title = $pageTitle;

// staff id
$user_id = $session->userId;
$response->scripts = ["assets/js/academics.js"];

// confirm the user permissions
$canClose = $accessObject->hasAccess("close", "settings");

// get the file path
$filePath = $_GET["file"] ?? null;

// get the file name
$file_to_download = !empty($filePath) ? xss_clean(base64_decode($filePath)) : null;

// explode the text
$name = !empty($file_to_download) ? explode($myClass->underscores, $file_to_download) : null;

// if no record was found
if(empty($name)) {
    $response->html = page_not_found();
} else {

    // set the filepath in a array stream
    $response->array_stream['calendar']['name'] = $name;
    
    // get the file to display name
    $file = "{$myClass->baseUrl}{$name[0]}";

    // append the html content
    $response->html = '
    <section class="section">
        <div class="section-body">
            <iframe style="width: 100%; height: 87vh; border: none;" src="'.$file.'"></iframe>
        </div>
    </section>';
}

// print out the response
echo json_encode($response);