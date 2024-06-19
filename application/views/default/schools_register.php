<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed OR the request_method is not POST
jump_to_main($baseUrl);

// initial variables
global $accessObject, $defaultUser, $isSupport, $defaultClientData, $clientPrefs, $SITEURL, $usersClass;
$appName = $myClass->appName;

// confirm that user id has been parsed
$clientId = $session->clientId;
$loggedUserId = $session->userId;

// filters
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Register School ";

// if not booking set
if($isSupport) {

    // set the html string
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-landmark"></i> Register New School</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Register School</div>
            </div>
        </div>
        <div class="row">
            
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-body">
                        
                    <form method="POST" action="'.$baseUrl.'api/auth" id="auth-form" class="needs-validation" novalidate="">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="school_name">School Name</label>
                                    <input id="school_name" type="text" class="form-control" name="school_name" tabindex="1" required autofocus>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="school_address">Address</label>
                                    <input id="school_address" type="text" class="form-control" name="school_address" tabindex="1" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="school_contact">Primary Contact</label>
                                    <input id="school_contact" type="text" class="form-control" name="school_contact" tabindex="1" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="school_contact_2">Secondary Contact</label>
                                    <input id="school_contact_2" type="text" class="form-control" name="school_contact_2" tabindex="1">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input id="email" type="email" class="form-control" name="email" tabindex="1" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="d-block">
                                        <label for="password" class="control-label">Password</label>
                                    </div>
                                    <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                                    <input id="plan" value="basic" type="hidden" class="form-control" name="plan">
                                </div>
                            </div>
                            <div class="col-lg-12">
                            <input type="hidden" name="portal_registration" value="true" id="portal_registration" hidden>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                Create Account
                                </button>
                            </div>
                            </div>
                        </div>
                    </form>

                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-4">
                <div class="card">
                    <div class="card-body">


                    </div>
                </div>
            </div>

        </div>
    </section>';

} else {
    $response->html = page_not_found("access_denied");
}

// print out the response
echo json_encode($response);
?>