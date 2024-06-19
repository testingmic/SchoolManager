<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];

$hasRequest = $accessObject->hasAccess("request", "library");

$pageTitle = "Request Book";

$response->title = $pageTitle;

if(!$hasRequest) {
    $response->html = page_not_found("permission_denied");
} else {

    $userId = !empty($session->student_id) ? $session->student_id : $session->userId;
    $user_role = !empty($session->student_id) ? "student" : $defaultUser->user_type;

    $response->scripts = ["assets/js/library.js"];
    $search = (object)["search_form" => true, "clientId" => $clientId];
    $search_form = load_class("forms", "controllers")->library_book_issue_form($search);
    
    $form = (object)["request_form" => true, "user_id" => $userId, "clientId" => $clientId, "user_role" => $user_role];
    $request_form = load_class("forms", "controllers")->library_book_issue_form($form);

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'books_issued">Requested Books List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row" id="library_form">
                <div class="col-12 col-sm-12 col-md-5">
                    <div class="card mb-3">
                        <div class="card-body">
                            '.$search_form.'
                        </div>
                    </div>
                    <div class="card hidden mb-3" id="selected_book_details" data-mode="request"></div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>SELECTED BOOKS LIST</h6>
                            <div id="selected_book_list">
                                <div class="font-italic">No books has been selected yet.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-7">
                    <div class="card">
                        <div class="card-body">
                            '.$request_form.'
                        </div>
                    </div>
                </div>
            </div>
        </section>';
}

// print out the response
echo json_encode($response);
?>