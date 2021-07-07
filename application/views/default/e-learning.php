<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $isAdminAccountant, $isTutorAdmin, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "E-Learning";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = ["assets/js/resources.js"];

// load the classes list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "id, name",
    "client_data" => $defaultUser->client
];
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
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
                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";
                        }
                        $response->html .= '
                    </select>
                </div>
                <div class="col-xl-4 col-md-4 col-12 form-group">
                    <label>Select Course <span class="required">*</span></label>
                    <select class="form-control selectpicker" name="course_id">
                        <option value="">Please Select Course</option>
                    </select>
                </div>
                <div class="col-xl-4 col-md-4 col-12 form-group">
                    <label>Select Course Unit</label>
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
                        <button onclick="return search_Resource()" class="btn-block btn btn-outline-primary">Search <i class="fa fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
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