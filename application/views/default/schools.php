<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed OR the request_method is not POST
jump_to_main($baseUrl);

// initial variables
global $accessObject, $defaultUser, $isSupport, $defaultClientData, $clientPrefs, $SITEURL, $usersClass;
$appName = config_item("site_name");

$clientId = $session->clientId;
$loggedUserId = $session->userId;

// filters
$response = (object) [];
$response->title = "Manage School : {$appName}";

// client id
$client_id = $SITEURL[1] ?? null;

// if not booking set
if($isSupport && !empty($client_id)) {

    // init values
    $schools_list = "";
    $load_schools_list = $myClass->pushQuery("*", "clients_accounts", "client_id='{$client_id}'");

    // error page
    // if no record was found
    if(empty($load_schools_list)) {
        $response->html = page_not_found("access_denied");
    } else {

        $data = $load_schools_list[0];

        // set the html string
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-landmark"></i> Manage School</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">Manage School</div>
                </div>
            </div>
            <div class="row">
                
                <div class="col-md-4">
                    <div class="card author-box pt-2">
                        <div class="card-body">


                            <div class="author-box-center m-0 p-0">
                                <img alt="image" src="'.$baseUrl.''.$data->client_logo.'" class="profile-picture">
                            </div>
                            <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->client_name.'</div>
                            <div class="text-center border-top mt-0 mb-2">
                                <div class="author-box-description font-22 text-success font-weight-bold">'.$data->client_id.'</div>
                            </div>

                            <div class="text-center border-top">
                                <div class="mt-2">'.(!empty($data->client_email) ? "<i class='fa fa-envelope'></i> {$data->client_email}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_contact) ? "<i class='fa fa-phone'></i> {$data->client_contact} / {$data->client_secondary_contact}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_address) ? "<i class='fa fa-home'></i> {$data->client_address}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_location) ? "<i class='fa fa-map-marked-alt'></i> {$data->client_location}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.(!empty($data->client_website) ? "<i class='fa fa-globe'></i> {$data->client_website}" : "-").'</div>
                            </div>
                            <div class="text-center">
                                <div class="mt-2">'.($myClass->the_status_label($data->client_state)).'</div>
                            </div>
                            <div class="w-100 mt-2 border-top text-center pt-3">
                                <a class="btn btn-dark" href="'.$baseUrl.'dashboard"><i class="fa fa-arrow-circle-left"></i> Go Back</a>
                            </div>







                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            
                        </div>
                    </div>
                </div>
            
            </div>
        </section>';

    }
        
} else {
    $response->html = page_not_found("access_denied");
}

// print out the response
echo json_encode($response);
?>