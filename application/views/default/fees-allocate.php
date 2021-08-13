<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Allocate Student Fees";
$response->title = "{$pageTitle} : {$appName}";

$response->scripts = ["assets/js/page/index.js"];

// student id
$user_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($user_id)) {

    // set the student parameter
    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "minified" => "simplified",
        "no_limit" => 1,
        "user_type" => "student"
    ];

    $data = load_class("users", "controllers", $student_param)->list($student_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {
        
        // set the first key
        $data = $data["data"][0];


        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-student">Students</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'update-student/'.$user_id.'">Student Data</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="card author-box pt-2">
                            <div class="card-body pl-1 pr-1">
                                <div class="author-box-center m-0 p-0">
                                    <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                                </div>
                                <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                                <div class="text-center border-top mt-0">
                                    <div class="author-box-description font-22 text-success font-weight-bold">'.$data->unique_id.'</div>
                                    <div class="author-box-description font-22 text-info font-weight-bold mt-1">'.$data->class_name.'</div>
                                    <div class="w-100 mt-2 border-top pt-3">
                                        <a class="btn btn-dark" href="'.$baseUrl.'update-student/'.$user_id.'"><i class="fa fa-arrow-circle-left"></i> Go Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7 col-lg-8">
                        <div class="card author-box">
                            <div class="card-header text-uppercase">Fees Allocation Table</div>
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    <button title="Click to add new fee item row." class="btn btn-primary"><i class="fa fa-plus"></i> Add Fee Item</button>
                                </div>
                            </div>
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