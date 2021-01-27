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
$pageTitle = "Staff Payroll Details";
$response->title = "{$pageTitle} : {$appName}";

// the query parameter to load the user information
$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

// staff id
$userId = $session->userId;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($userId)) {

    $staff_param = (object) [
        "clientId" => $clientId,
        "user_id" => $userId,
        "limit" => 1,
        "user_payroll" => true,
        "no_limit" => 1,
        "user_type" => $defaultUser->user_type
    ];

    $data = load_class("users", "controllers")->list($staff_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        $response->scripts = ["assets/js/payroll.js"];

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        $payroll_form = load_class("forms", "controllers")->payroll_form($clientId, $userId, $data);

        // user  permission information
        $level_data = "<div class='row'>";

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'hr-payroll">Staff List</a></div>
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
                            <div class="author-box-name"><a href="#">'.$data->name.'</a></div>
                            '.($data->class_name ? '<div class="author-box-job">'.$data->class_name.'</div>' : '').'
                            '.($data->department_name ? '<div class="author-box-job">('.$data->department_name.')</div>' : '').'
                        </div>
                    </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4>Personal Details</h4>
                        </div>
                        <div class="card-body pt-0 pb-0 mb-0">
                            <div class="py-4">
                                <p class="clearfix">
                                    <span class="float-left">Gross Salary</span>
                                    <span class="float-right text-muted">'.$data->gross_salary.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Allowances</span>
                                    <span class="float-right text-muted">'.$data->allowances.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Deductions</span>
                                    <span class="float-right text-muted">'.$data->deductions.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Basic Salary</span>
                                    <span class="float-right text-muted">'.$data->basic_salary.'</span>
                                </p>
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
                                    <span class="float-left">Appointment Date</span>
                                    <span class="float-right text-muted">'.$data->enrollment_date.'</span>
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
                                    <a class="nav-link active" id="bank_details-tab2" data-toggle="tab" href="#bank_details" role="tab" aria-selected="false">Bank Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="allowances-tab2" data-toggle="tab" href="#allowances" role="tab" aria-selected="true">Allowances</a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade show active" id="bank_details" role="tabpanel" aria-labelledby="bank_details-tab2">
                                    '.$payroll_form["bank_detail"].'
                                </div>
                                <div class="tab-pane fade" id="allowances" role="tabpanel" aria-labelledby="allowances-tab2">
                                    '.$payroll_form["allowance_detail"].'
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