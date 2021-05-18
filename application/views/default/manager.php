<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $accessObject;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "App Manager";
$response->title = "{$pageTitle} : {$appName}";

// staff id
$user_id = $session->userId;


// confirm the user permissions
if(empty($accessObject->hasAccess("close", "settings"))) {
    $response->html = page_not_found();
} else {
    // append the html content
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body font-16">
                            <div class="mb-2">
                                <span class="font-weight-bold text-uppercase">Academic Year</span>
                                <span class="pull-right">'.$defaultUser->appPrefs->academics->academic_year.'</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-weight-bold text-uppercase">Academic Term</span>
                                <span class="pull-right">'.$defaultUser->appPrefs->academics->academic_term.'</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-weight-bold text-uppercase">This Term Began On</span>
                                <span class="pull-right">'.date("jS F Y", strtotime($defaultUser->appPrefs->academics->term_starts)).'</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-weight-bold text-uppercase">This Term '.($defaultUser->appPrefs->termEnded ? "Ended On" : "Ends On").'</span>
                                <span class="pull-right">
                                    '.date("jS F Y", strtotime($defaultUser->appPrefs->academics->term_ends)).'
                                    '.($defaultUser->appPrefs->termEnded ? "<span class='badge badge-danger'>Already Ended</span>" : "<span class='badge badge-success'>Active</span>").'
                                </span>
                            </div>
                            <div class="mb-2">
                                <span class="font-weight-bold text-uppercase">Next Term Begins</span>
                                <span class="pull-right">'.date("jS F Y", strtotime($defaultUser->appPrefs->academics->next_term_starts)).'</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-weight-bold text-uppercase">Next Term Ends On</span>
                                <span class="pull-right">'.date("jS F Y", strtotime($defaultUser->appPrefs->academics->next_term_ends)).'</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>';
}


// print out the response
echo json_encode($response);