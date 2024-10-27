<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $isAdminAccountant, $isTutorAdmin, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "E-Learning";
$response->title = $pageTitle;
$response->scripts = ["assets/js/resources.js"];

// load the classes list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "a.id, a.name, a.item_id",
    "client_data" => $defaultUser->client
];
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-assistive-listening-systems"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row" id="e_resources">';
        
            if($isTutorAdmin || $isAdminAccountant) {
                $response->html .= '<div class="col-xl-4 col-md-4 col-12 form-group">
                    <label>Select Class <span class="required">*</span></label>
                    <select class="form-control selectpicker" name="class_id">
                        <option value="">Please Select Class</option>';
                        foreach($class_list as $each) {
                            $response->html .= "<option value=\"{$each->item_id}\">{$each->name}</option>";
                        }
                        $response->html .= '
                    </select>
                </div>
                <div class="col-xl-4 col-md-4 col-12 form-group">
                    <label>Select Subject <span class="required">*</span></label>
                    <select class="form-control selectpicker" name="course_id">
                        <option value="">Please Select Subject</option>
                    </select>
                </div>
                <div class="col-xl-4 col-md-4 col-12 form-group">
                    <label>Select Subject Unit</label>
                    <select class="form-control selectpicker" name="unit_id">
                        <option value="">Please Select Unit</option>
                    </select>
                </div>';
            }
            $response->html .= '
            <div class="col-sm-12 col-lg-12">
                <div class="row mb-2">
                    <div class="col-md-10 col-lg-10">
                        <input placeholder="Search for a e-learning resource" id="search_term" name="search_term" type="text" class="form-control">
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <button onclick="return search_Resource()" class="btn-block height-40 btn btn-outline-primary">Search <i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-12 mt-2">
                <div class="card">
                    <div class="card-body" id="elearning">
                        <div class="form-content-loader" style="display: none; position: absolute">
                            <div class="offline-content text-center">
                                <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                            </div>
                        </div>
                        <div id="total_count"></div>
                        <div id="elearning_resources_list" style="min-height:100px"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>