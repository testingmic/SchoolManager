<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$filter = (object) $_POST;

$user_id = $SITEURL[1] ?? null;
$response->title = "Update Student Record : {$appName}";

$clientId = $session->clientId;

// if the user id is not empty
if(empty($user_id)) {
    // parse error message
    $response->html = page_not_found();
} else {

    // set the student parameter
    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "full_details" => true,
        "no_limit" => 1,
        "user_type" => "student",
        "client_data" => $defaultUser->client
    ];

    // get the student data
    $data = load_class("users", "controllers", $student_param)->list($student_param);
    
    // if no record was found
    if(empty($data["data"])) {
        // parse error message
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        // guardian information
        $user_form = load_class("forms", "controllers")->student_form($clientId, $baseUrl, $data);

        // set the html data
        $response->html = '
            <section class="section">

                <div class="section-header">
                    <h1>Edit Student</h1>
                    <div class="section-header-breadcrumb">
                        <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                        <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-student">Students List</a></div>
                        <div class="breadcrumb-item">'.$data->name.'</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                '.$user_form.'
                            </div>
                        </div>
                    </div>
                </div>

            </section>';
    }

}

// print out the response
echo json_encode($response);
?>