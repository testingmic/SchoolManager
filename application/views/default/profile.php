<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $isStudent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "My Profile";
$response->title = $pageTitle;

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
    $isSecurity = (bool) isset($_GET["security"]);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the url
        $url_link = $SITEURL[2] ?? null;

        $response->array_stream['url_link'] = "profile/{$user_id}/";

        // set the first key
        $data = $data["data"][0];
        $student_allocation_list = null;

        // guardian information
        $user_form = load_class("forms", "controllers")->profile_form($baseUrl, $data);

        // load this section if a student is logged in
        if($isStudent) {
            
            // load fees allocation list for class
            $allocation_param = (object) [
                "clientId" => $clientId, "userData" => $defaultUser, 
                "student_id" => $user_id, "client_data" => $defaultUser->client, 
                "parse_owning" => true, "show_student" =>  false
            ];

            // create a new object
            $feesObject = load_class("fees", "controllers", $allocation_param);

            // get the student fees allocation
            $student_allocation_list = $feesObject->student_allocation_array($allocation_param);
        }

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-user"></i> '.$pageTitle.'</h1>
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
                            <div class="author-box-center m-0 p-0 flex justify-center">
                                <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                            </div>
                            <div class="author-box-center">
                                <div class="clearfix"></div>
                                <div class="author-box-center mt-2 font-25 mb-0 p-0">'.$data->name.'</div>
                                <div class="author-box-job"><strong>'.ucwords($data->user_type).'</strong></div>
                                '.($data->department_name ? '<div class="author-box-job">('.$data->department_name.')</div>' : '').'
                            </div>
                        </div>
                    </div>
                    <div class="card">
                    <div class="card-header">
                        <h4>PERSONAL INFORMATION</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-2">
                            <p class="clearfix">
                                <span class="float-left">Unique ID</span>
                                <span class="float-right text-muted">'.$data->unique_id.'</span>
                            </p>
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
                                    <a class="nav-link '.(empty($url_link) || $url_link == "summary" ? "active": null).'" onclick="return appendToUrl(\'summary\')" id="home-tab2" data-toggle="tab" href="#about" role="tab"
                                    aria-selected="true">Description</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link '.($url_link == "profile" ? "active": null).'" id="profile-tab2" data-toggle="tab" onclick="return appendToUrl(\'profile\')" href="#profile" role="tab"
                                    aria-selected="false">Update</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link '.($url_link == "settings" ? "active": null).'" onclick="return appendToUrl(\'settings\')" id="settings-tab2" data-toggle="tab" href="#settings" role="tab"
                                    aria-selected="true">Security</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade '.(empty($url_link) || $url_link == "summary" ? "show active": null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                                    '.($data->description ? "
                                        <div class='mb-4 border-bottom'>
                                            <div class='card-body p-2 pl-0'>
                                                <div><h5>DESCRIPTION</h5></div>
                                                {$data->description}
                                            </div>
                                        </div>
                                    " : "").'
                                    '.($isStudent ? '
                                        <div>
                                            <div class="d-flex mb-2 justify-content-between">
                                                <div><h4>MY BILL</h4></div>
                                                <div><a href="'.$baseUrl.'download/student_bill/'.$user_id.'?download=1" target="_blank" class="btn mb-1 btn-outline-danger"><i class="fa fa-file-pdf"></i> Download</a></div>
                                            </div>
                                            <div class="table-responsive">
                                                <table data-empty="" class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%" class="text-center">#</th>
                                                            <th>Category</th>
                                                            <th>Due</th>
                                                            <th>Paid</th>
                                                            <th>Balance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>'.$student_allocation_list["list"].'</tbody>
                                                </table>
                                            </div>
                                        </div>' : null
                                    ).'
                                </div>
                                <div class="tab-pane fade '.($url_link == "profile" ? "show active": null).'" id="profile" role="tabpanel" aria-labelledby="profile-tab2">
                                    '.$user_form.'
                                </div>
                                <div class="tab-pane fade '.($url_link == "settings" ? "show active": null).'" id="settings" role="tabpanel" aria-labelledby="settings-tab2">
                                    <form autocomplete="Off" method="POST" class="ajaxform" id="ajaxform" action="'.$baseUrl.'api/auth/change_password">
                                        <div>
                                            <h5 class="border-bottom pb-2">Change Password</h5>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Username</label>
                                                    <input type="text" title="Username cannot be changed." disabled name="username" id="username" value="'.$data->username.'" class="form-control">
                                                </div>
                                            </div>
                                            '.(
                                                !$updatePermission || $updatePermission ? '
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Current Password <span class="required">*</span></label>
                                                        <input title="Enter the current password" autocomplete="Off" type="password" name="password" id="password" class="form-control">
                                                    </div>
                                                </div>' : ''
                                            ).'
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Password <span class="required">*</span></label>
                                                    <input title="Set the new password." autocomplete="Off" type="password" name="password_1" id="password_1" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label>Confirm Password <span class="required">*</span></label>
                                                    <input title="Confirm the new password." autocomplete="Off" type="password" name="password_2" id="password_2" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="hidden" name="user_id" id="user_id" value="'.$user_id.'">
                                                <button class="btn btn-outline-success" type="submit"><i class="fa fa-lock"></i> Change Password</button>
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