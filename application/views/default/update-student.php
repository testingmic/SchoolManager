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
if(!isset($_SERVER["HTTP_REFERER"])) {
    header("location: {$baseUrl}main");
    exit;
}

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Student Details";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [
    "assets/js/page/index.js"
];

// if the user id is not empty
if(!empty($user_id)) {

    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "user_type" => "student"
    ];

    $student_list = load_class("users", "controllers")->list($student_param);

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-student">Students List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                
                </div>
            </div>
        </section>';

}
// print out the response
echo json_encode($response);
?>