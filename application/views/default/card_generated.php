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
$preview_card_form = load_class("forms", "controllers")->preview_card_form();

$params = (object) [
    "clientId" => $clientId
];

// get the list of cards
$list_cards = load_class("cards", "controllers")->list($params);

$cards_listing = "";

// loop through the list of cards
foreach($list_cards["data"] as $key => $each) {
    
    $key = $key + 1;
    $cards_listing .= "<tr data-row_id=\"{$each->id}\">";
    $cards_listing .= "<td>{$key}</td>";
    $cards_listing .= "<td>{$each->name}</td>";
    $cards_listing .= "<td>{$each->unique_id}</td>";
    $cards_listing .= "<td>{$each->gender}</td>";
    $cards_listing .= "<td>".ucwords($each->user_type)."</td>";
    $cards_listing .= "<td>{$each->class_name}</td>";
    $cards_listing .= "<td>{$each->issue_date}</td>";
    $cards_listing .= "<td>{$each->expiry_date}</td>";
    $cards_listing .= "<td class='text-center'>
        <button onclick='return card_preview({$each->id});' class='btn btn-sm btn-outline-success'>
            <i class='fa fa-eye'></i> Preview
        </button>
    </td>";
    $cards_listing .= "</tr>";

}

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
                    <a class="btn btn-sm btn-outline-success" target="_blank" href="'.$baseUrl.'download/idcard">
                        <i class="fa fa-qrcode"></i> Preview Cards
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th>Name</th>
                                        <th>Card Number</th>
                                        <th>Gender</th>
                                        <th>Category</th>
                                        <th>Level</th>
                                        <th>Issued On</th>
                                        <th>Expiry On</th>
                                        <th align="center" width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$cards_listing.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>' . $the_card_form . $preview_card_form;
// print out the response
echo json_encode($response);
?>