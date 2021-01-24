<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Settings";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [];

// specify some variables
$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

// confirm that the user has the required permissions
if(!$accessObject->hasAccess("manage", "settings")) {
    // show the error page
    $response->html = page_not_found();
} else {

    $the_form = load_class("forms", "controllers")->settings_form($clientId);

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
                    <div class="card">
                        <div class="card-body">
                            <div class="padding-20">
                                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">General</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="calendar-tab2" data-toggle="tab" href="#calendar" role="tab"
                                        aria-selected="true">Timetable</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="courses-tab2" data-toggle="tab" href="#courses" role="tab" aria-selected="true">Courses List</a>
                                    </li>
                                </ul>
                                <div class="tab-content tab-bordered" id="myTab3Content">
                                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                        '.($the_form["general"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab2">
                                        <div class="col-lg-12 pl-0"><h5>ACCOUNTING SETTINGS</h5></div>
                                        '.($the_form["accounting"] ?? null).'
                                    </div>
                                    <div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab2">
                                        <div class="col-lg-12 pl-0"><h5>OTHERS</h5></div>
                                        <div class="col-lg-12">
                                            '.($the_form["accounting"] ?? null).'
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="profile-tab2">

                                    </div>
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
?>