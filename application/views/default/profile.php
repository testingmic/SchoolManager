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
$pageTitle = "My Profile";
$response->title = "{$pageTitle} : {$appName}";

// staff id
$user_id = $session->userId;

// if the user id is not empty
if(!empty($user_id)) {

    $staff_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "full_details" => true,
        "no_limit" => 1,
        "user_type" => $defaultUser->user_type
    ];

    $data = load_class("users", "controllers")->list($staff_param);

    // has the right to update the user permissions
    $updatePermission = $accessObject->hasAccess("update", "permissions");
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        // guardian information
        $user_form = load_class("forms", "controllers")->profile_form($baseUrl, $data);

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // user  permission information
        $level_data = "<div class='row'>";

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-user-friends"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="card author-box">
                        <div class="card-body">
                            <div class="author-box-center m-0 p-0">
                                <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                            </div>
                            <div class="author-box-center">
                                <div class="clearfix"></div>
                                <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                                <div class="author-box-job"><strong>'.strtoupper($data->user_type).'</strong></div>
                                '.($data->department_name ? '<div class="author-box-job">('.$data->department_name.')</div>' : '').'
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
                                <span class="float-left">Gender</span>
                                <span class="float-right text-muted">'.$data->gender.'</span>
                            </p>
                            '.(!empty($data->section_name) ? 
                            '<p class="clearfix">
                                <span class="float-left">Section</span>
                                <span class="float-right text-muted">'.$data->section_name.'</span>
                            </p>' : '').'
                            <p class="clearfix">
                                <span class="float-left">Birthday</span>
                                <span class="float-right text-muted">'.$data->date_of_birth.'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Primary Contact</span>
                                <span class="float-right text-muted">'.$data->phone_number.'</span>
                            </p>
                            '.(!empty($data->phone_number_2) ? 
                            '<p class="clearfix">
                                <span class="float-left">Secondary Contact</span>
                                <span class="float-right text-muted">'.$data->phone_number_2.'</span>
                            </p>' : '').'
                            <p class="clearfix">
                                <span class="float-left">E-Mail</span>
                                <span class="float-right text-muted">'.$data->email.'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Blood Group</span>
                                <span class="float-right text-muted">'.$data->blood_group_name.'</span>
                            </p>
                            '.(!empty($data->position) ? 
                            '<p class="clearfix">
                                <span class="float-left">Position</span>
                                <span class="float-right text-muted">'.$data->position.'</span>
                            </p>' : '').'
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
                                    <a class="nav-link active" id="home-tab2" data-toggle="tab" href="#about" role="tab"
                                    aria-selected="true">Summary Description</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab2" data-toggle="tab" href="#profile" role="tab"
                                    aria-selected="false">Update Record</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="settings-tab2" data-toggle="tab" href="#settings" role="tab"
                                    aria-selected="true">Security / Settings</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="about" role="tabpanel" aria-labelledby="home-tab2">
                                    '.($data->description ? "
                                        <div class='mb-3 border-bottom_'>
                                            <div class='card-body p-2 pl-0'>
                                                <div><h5>DESCRIPTION</h5></div>
                                                {$data->description}
                                            </div>
                                        </div>
                                    " : "").'
                                </div>
                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab2">
                                    '.$user_form.'
                                </div>
                                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab2">
                                    <form autocomplete="Off" method="POST" class="ajaxform" id="ajaxform" action="'.$baseUrl.'api/auth/change_password">
                                        <div>
                                            <h5 class="border-bottom pb-2">Change Password</h5>
                                        </div>
                                        <div class="row">
                                            '.(
                                                !$updatePermission || $updatePermission ? '
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Current Password</label>
                                                        <input type="password" name="password" id="password" class="form-control">
                                                    </div>
                                                </div>' : ''
                                            ).'
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Password</label>
                                                    <input type="password" name="password_1" id="password_1" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Confirm Password</label>
                                                    <input type="password" name="password_2" id="password_2" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="hidden" name="user_id" id="user_id" value="'.$user_id.'">
                                                <button class="btn btn-outline-success" type="submit">Change Password</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
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