<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

$hasIssue = $accessObject->hasAccess("issue", "library");

$pageTitle = $hasIssue ? "Issued Books List" : "My Books List";

$response->title = "{$pageTitle} : {$appName}";

$params = (object)[
    "clientId" => $clientId
];
$the_form = load_class("forms", "controllers")->library_category_form($params);

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                '.($hasIssue ? '<a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'issue-book"><i class="fa fa-arrow-circle-right"></i> Issue Book</a>' 
                    : '<a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'request-book"><i class="fa fa-american-sign-language-interpreting"></i> Request Book</a>'
                ).'
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Book Title</th>
                                        '.($hasIssue ? '<th>Role</th>' : '').'
                                        '.($hasIssue ? '<th>Fullname</th>' : '').'
                                        <th>Date of Issue</th>
                                        <th>Date of Expiry</th>
                                        <th width="10%">Fine</th>
                                        <th width="10%">Status</th>
                                        <th align="center" width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
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