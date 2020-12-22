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
$pageTitle = "Guardian Details";
$response->title = "{$pageTitle} : {$appName}";

// the query parameter to load the user information
$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

$response->scripts = [
    "assets/js/page/index.js"
];

// student id
$user_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($user_id)) {

    $guardian_param = (object) [
        "limit" => 1,
        "clientId" => $clientId,
        "guardian_id" => $user_id,
        "append_wards" => true,
    ];

    $data = load_class("users", "controllers")->guardian_list($guardian_param);
    
    // if no record was found
    if(empty($data)) {
        $response->html = page_not_found();
    } else {

        // user permissions
        $hasUpdate = $accessObject->hasAccess("update", "guardian");

        // set the first key
        $data = $data[0];

        // guardian information
        $user_form = load_class("forms", "controllers")->guardian_form($clientId, $baseUrl, $data);

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // wards_list
        $wards_list = "<div class='row mb-3' id='guardian_ward_listing'>";
        // if the wards_list is not empty
        if(!empty($data->wards_list)) {
            // append the guardian wards list
            $wards_list .= $usersClass->guardian_wardlist($data->wards_list, $data->user_id);
        }
        $wards_list .= "</div>";
        
        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-guardian">Guardian List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center">
                        <img alt="image" src="'.$baseUrl.''.$data->image.'" class="rounded-circle author-box-picture">
                        <div class="clearfix"></div>
                        <div class="author-box-name"><a href="#">'.$data->fullname.'</a></div>
                        <div class="author-box-job">'.$data->residence.'</div>
                    </div>
                    <div class="text-center">
                        <div class="author-box-description">'.$data->description.'</div>
                        <div class="w-100 d-sm-none"></div>
                    </div>
                </div>
                </div>
                <div class="card">
                <div class="card-header">
                    <h4>Personal Details</h4>
                </div>
                <div class="card-body pt-0 pb-0">
                    <div class="py-4">
                        <p class="clearfix">
                            <span class="float-left">Occupation</span>
                            <span class="float-right text-muted">'.$data->occupation.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Employer</span>
                            <span class="float-right text-muted">'.$data->employer.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Date of Birth</span>
                            <span class="float-right text-muted">'.$data->date_of_birth.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Gender</span>
                            <span class="float-right text-muted">'.$data->gender.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Primary Contact</span>
                            <span class="float-right text-muted">'.$data->contact.'</span>
                        </p>
                        '.($data->contact_2 ? 
                            '<p class="clearfix">
                                <span class="float-left">Secondary Contact</span>
                                <span class="float-right text-muted">'.$data->contact_2.'</span>
                            </p>
                            ' : ''
                        ).'
                        <p class="clearfix">
                            <span class="float-left">E-Mail</span>
                            <span class="float-right text-muted">'.$data->email.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Residence</span>
                            <span class="float-right text-muted">'.$data->residence.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Country</span>
                            <span class="float-right text-muted">'.$data->country_name.'</span>
                        </p>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(!$updateItem ? "active" : null).'" id="home-tab2" data-toggle="tab" href="#about" role="tab"
                        aria-selected="true">Other Information</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Record</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                            '.($data ? "
                                <div class='mb-3'>
                                    <div class='card-body p-2 pl-0'>
                                        <div class='d-flex justify-content-between'>
                                            <div><h5>WARDS LIST</h5></div>
                                            ".($hasUpdate ? "<div><button onclick='return load_quick_form(\"modify_guardian_ward\",\"{$data->user_id}\");' class='btn btn-outline-primary btn-sm' type='button'><i class='fa fa-user'></i> Add Ward</button></div>" : "")."
                                        </div>
                                        {$wards_list}
                                    </div>
                                </div>
                            " : "").'
                        </div>
                        <div class="tab-pane fade '.($updateItem ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
                        if($hasUpdate) {
                            $response->html .= $user_form;
                        }

                        $response->html .= '</div>
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