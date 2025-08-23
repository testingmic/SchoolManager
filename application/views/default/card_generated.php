<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultClientData, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Generated ID Cards";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!$accessObject->hasAccess("view", "id_cards")) {
    // unset the page additional information
    $response->page_programming = [];
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// get the permissions list
$permissions = [
    "student" => [],
    "teacher" => [
        "student" => "Students"
    ],
    "accountant" => [
        "student" => "Student",
        "staff" => "Staff",
    ],
    "admin" => [
        "student" => "Students",
        "staff" => "Staff",
    ]
];

$response->scripts = ["assets/js/settings.js"];

// get the card form
$the_card_form = load_class("forms", "controllers")->id_card_form($defaultUser, $permissions);

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-user-plus"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'guardians">Guardians</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-3">
                    <a class="btn btn-sm btn-outline-primary" onclick="return id_card_modal();" href="#">
                        <i class="fa fa-plus"></i> Generate ID Cards
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th>Name</th>
                                        <th>Admission Number</th>
                                        <th>Gender</th>
                                        <th>Category</th>
                                        <th>Issued On</th>
                                        <th align="center" width="18%"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>' . $the_card_form;
// print out the response
echo json_encode($response);
?>