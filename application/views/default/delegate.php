<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $isAdmin, $isWardParent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Delegate Information";
$response->title = $pageTitle;

// end query if the user has no permissions
if(!$accessObject->hasAccess("view", "delegates") && !$isWardParent) {
    // permission denied information
    $response->html = page_not_found("permission_denied", ["delegates"]);
    echo json_encode($response);
    exit;
}

$response->scripts = [
    "assets/js/index.js"
];

// set the url
$url_link = $SITEURL[2] ?? null;

// student id
$delegate_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($delegate_id)) {

    $delegate_param = (object) [
        "limit" => 1,
        "clientId" => $clientId,
        "delegate_id" => $delegate_id,
        "append_guardian" => true,
        "client_data" => $defaultUser->client
    ];

    // load the delegate object
    $delegateObject = load_class("delegates", "controllers", $delegate_param);

    // get the data
    $data = $delegateObject->list($delegate_param)["data"];
    
    // if no record was found
    if(empty($data)) {
        $response->html = page_not_found();
    } else {

        // user permissions
        $hasUpdate = $accessObject->hasAccess("update", "guardian") || $isWardParent;
        $addDelegate = $accessObject->hasAccess("add", "delegates") || $isWardParent;
        $updateDelegate = $accessObject->hasAccess("update", "delegates") || $isWardParent;

        // set the first key
        $data = $data[0];

        // set the page title
        $response->title = $data->firstname." ".$data->lastname;

        $students_list = no_record_found("Students", "This feature is not available yet. We are currently working on it.", null, false, false, "fa fa-graduation-cap");

        // guardian information
        $user_form = load_class("forms", "controllers")->load_delegate_form($clientId, $baseUrl, $data);

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // set the user_id id in the console
        $response->array_stream['user_id'] = $delegate_id;
        $response->array_stream['url_link'] = "delegate/{$delegate_id}/";

        $guardians_list = $delegateObject->delegate_guardians_list($data->guardians_list, $updateDelegate);

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-user-friends"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'delegates">Delegates</a></div>
                    <div class="breadcrumb-item">'.$data->firstname.' '.$data->lastname.'</div>
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
                        <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->firstname.' '.$data->lastname.'</div>
                        <div class="font-22 font-weight-bold text-uppercase text-dark">'.$data->unique_id.'</div>
                    </div>
                    <div class="text-center">
                        <div class="author-box-description">
                            <span class="badge badge-primary">'.$data->relationship.'</span>
                        </div>
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
                            '.(
                                !empty($data->gender) ? '
                                <p class="clearfix">
                                    <span class="float-left">Gender</span>
                                    <span class="float-right text-muted">'.$data->gender.'</span>
                                </p>' : null
                            ).'
                            '.(
                                !empty($data->phonenumber) ? '
                                <p class="clearfix">
                                    <span class="float-left">Phone Number</span>
                                    <span class="float-right text-muted">'.$data->phonenumber.'</span>
                                </p>' : null
                            ).'
                            '.(
                                !empty($data->relationship) ? '
                                <p class="clearfix">
                                    <span class="float-left">Phone Number</span>
                                    <span class="float-right text-muted">'.$data->relationship.'</span>
                                </p>' : null
                            ).'
                            '.$myClass->qr_code_renderer("delegate", $delegate_id, $clientId, $data->firstname." ".$data->lastname).'
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(in_array($url_link, ["about", "view"]) || empty($url_link) ? "active" : null).'" id="home-tab2" data-toggle="tab" href="#about" role="tab" aria-selected="true">Guardians</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link '.($url_link === "delegates" ? "active" : null).'" id="home-tab2" data-toggle="tab" href="#delegates" role="tab" aria-selected="true">Students</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($url_link === "update" ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Update</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(in_array($url_link, ["about", "view"]) || empty($url_link) ? "show active" : null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                            '.($data ? "
                                <div>
                                    <div class='card-body p-2 pl-0'>
                                        {$guardians_list}
                                    </div>
                                </div>
                            " : "").'
                        </div>
                        <div class="tab-pane fade '.($url_link === "delegates" ? "show active" : null).'" id="delegates" role="tabpanel" aria-labelledby="home-tab2">
                            '.($data ? "
                                <div>
                                    <div class='card-body p-2 pl-0'>
                                        {$students_list}
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