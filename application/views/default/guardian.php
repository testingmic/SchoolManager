<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Guardian Information";
$response->title = $pageTitle;

$response->scripts = [
    "assets/js/index.js"
];

// set the url
$url_link = $SITEURL[2] ?? null;

// student id
$user_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($user_id)) {

    $guardian_param = (object) [
        "limit" => 1,
        "clientId" => $clientId,
        "user_id" => $user_id,
        "append_wards" => true,
        "client_data" => $defaultUser->client
    ];

    $data = load_class("users", "controllers", $guardian_param)->list($guardian_param)["data"];
    
    // if no record was found
    if(empty($data)) {
        $response->html = page_not_found();
    } else {

        // user permissions
        $hasUpdate = $accessObject->hasAccess("update", "guardian");

        // set the first key
        $data = $data[0];

        // set the page title
        $response->title = $data->name;

        // guardian information
        $user_form = load_class("forms", "controllers")->guardian_form($clientId, $baseUrl, $data);

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // wards_list
        $wards_list = "<div class='row mb-3' id='guardian_ward_listing'>";
        // if the wards_list is not empty
        if(!empty($data->wards_list)) {
            // append the guardian wards list
            $wards_list .= $usersClass->guardian_wardlist($data->wards_list, $data->user_id, $hasUpdate);
        }
        $wards_list .= "</div>";

        // set the user_id id in the console
        $response->array_stream['user_id'] = $user_id;
        $response->array_stream['url_link'] = "guardian/{$user_id}/";

        // change password url
        $change_password_url = null;
        if($isAdmin || ($defaultUser->user_id == $user_id)) {
            if($isAdmin && ($defaultUser->user_id !== $user_id)) {
                $change_password_url = "password_manager?lookup={$data->unique_id}";
            } else {
                $change_password_url = "profile?security";
            }
        }
        
        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-user-friends"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'guardians">Guardians</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center m-0 p-0 flex justify-center">
                        <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                    </div>
                    <div class="author-box-center">
                        <div class="clearfix"></div>
                        <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                        <div class="font-22 font-weight-bold text-uppercase text-dark">'.$data->unique_id.'</div>
                    </div>
                    <div class="text-center">
                        <div class="author-box-description">'.$data->description.'</div>
                        <div class="w-100 d-sm-none"></div>
                    </div>
                </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">PERSONAL INFORMATION</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-2">
                            <p class="clearfix">
                                <span class="float-left">Occupation</span>
                                <span class="float-right text-muted">'.$data->occupation.'</span>
                            </p>
                            '.(
                                !empty($data->employer) ? '
                                <p class="clearfix">
                                    <span class="float-left">Employer</span>
                                    <span class="float-right text-muted">'.$data->employer.'</span>
                                </p>' : null
                            ).'
                            '.(
                                !empty($data->employer) ? '
                                <p class="clearfix">
                                    <span class="float-left">Date of Birth</span>
                                    <span class="float-right text-muted">'.$data->date_of_birth.'</span>
                                </p>' : null
                            ).'
                            '.(
                                !empty($data->blood_group_name) ? '
                                <p class="clearfix">
                                    <span class="float-left">Blood Group</span>
                                    <span class="float-right text-muted">'.$data->blood_group_name.'</span>
                                </p>' : null
                            ).'
                            <p class="clearfix">
                                <span class="float-left">Gender</span>
                                <span class="float-right text-muted">'.$data->gender.'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Primary Contact</span>
                                <span class="float-right text-muted">'.$data->phone_number.'</span>
                            </p>
                            '.($data->phone_number_2 ? 
                                '<p class="clearfix">
                                    <span class="float-left">Secondary Contact</span>
                                    <span class="float-right text-muted">'.$data->phone_number_2.'</span>
                                </p>' : null
                            ).'
                            '.(
                                !empty($data->email) ? '
                                <p class="clearfix">
                                    <span class="float-left">E-Mail</span>
                                    <span class="float-right text-muted">'.$data->email.'</span>
                                </p>' : null
                            ).'
                            '.(
                                !empty($data->residence) ? '
                                <p class="clearfix">
                                    <span class="float-left">Residence</span>
                                    <span class="float-right text-muted">'.$data->residence.'</span>
                                </p>' : null
                            ).'
                            '.(
                                !empty($data->country_name) ? '
                                <p class="clearfix">
                                    <span class="float-left">Country</span>
                                    <span class="float-right text-muted">'.$data->country_name.'</span>
                                </p>' : null
                            ).'
                        </div>
                    </div>
                </div>
                '.($isAdmin || $user_id == $defaultUser->user_id ?         
                    '<div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">LOGIN INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-2">
                                <p class="clearfix">
                                    <span class="float-left">Username</span>
                                    <span class="float-right text-muted">'.$data->username.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Password</span>
                                    <span class="float-right text-muted">
                                        <button onclick="return load(\''.$change_password_url.'\')" class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-lock"></i> Security Update
                                        </button>
                                    </span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Last Login</span>
                                    <span class="float-right text-muted">'.$data->last_login.'</span>
                                </p>
                            </div>
                        </div>
                    </div>' : null
                ).'
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(!$updateItem || $url_link === "about" ? "active" : null).'" id="home-tab2" data-toggle="tab" href="#about" role="tab" aria-selected="true">Ward Information</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem || $url_link === "update" ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Update Record</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                            '.($data ? "
                                <div class='mb-3'>
                                    <div class='card-body p-2 pl-0'>
                                        <div class='d-flex mb-3 justify-content-between'>
                                            <div><h5></h5></div>
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