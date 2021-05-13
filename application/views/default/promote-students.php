<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$filter = (object) $_POST;

$response->title = "Promote Students : {$appName}";
$response->scripts = ["assets/js/filters.js", "assets/js/promotion.js"];

$clientId = $session->clientId;
$hasFiltering = $accessObject->hasAccess("filters", "settings");

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "userId" => $defaultUser->user_id,
    "user_type" => $defaultUser->user_type,
    "columns" => "id, name, slug, item_id"
];
// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$response->html = '
<section class="section">
    <div class="section-header">
        <h1>Promote Students</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item">Promote Students</div>
        </div>
    </div>
    <div class="row byPass_Null_Value" id="filter_Department_Class">
        <div class="col-12 col-sm-12 col-lg-12">
            <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="promotion-tab2" data-toggle="tab" href="#promotion" role="tab" aria-selected="true">Promote Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="promotion_history-tab2" data-toggle="tab" href="#promotion_history" role="tab" aria-selected="true">Promotion History</a>
                        </li>
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">

                        <div class="tab-pane fade show active" id="promotion" role="tabpanel" aria-labelledby="promotion-tab2">
                            
                            <div class="row">
                                <div class="col-xl-4 '.(!$hasFiltering ? 'hidden': '').' col-md-4 col-12 form-group">
                                    <label>Select Department</label>
                                    <select data-width="100%" class="form-control selectpicker" id="department_id" name="department_id">
                                        <option value="">Please Select Department</option>';
                                        foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                                    <label>Promote Students From</label>
                                    <select data-width="100%" class="form-control selectpicker" name="class_id">
                                        <option value="">Please Select Class</option>';
                                        foreach($class_list as $each) {
                                            $response->html .= "<option value=\"{$each->item_id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                                    <label>Promote Students To</label>
                                    <select data-width="100%" disabled class="form-control selectpicker" name="promote_to">
                                        <option value="">Promote To</option>';
                                        foreach($class_list as $each) {
                                            $response->html .= "<option value=\"{$each->item_id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>
                                <div class="col-xl-2 '.(!$hasFiltering ? 'hidden': '').' col-md-2 col-12 form-group">
                                    <label for="">&nbsp;</label>
                                    <button id="filter_Promotion_Students_List" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
                                </div>
                            </div>

                            <div class="row mt-4 table-responsive" id="promote_Student_Display"></div>                            

                        </div>

                        <div class="tab-pane fade" id="promotion_history" role="tabpanel" aria-labelledby="promotion_history-tab2">
                            Promotion History
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>';

// print out the response
echo json_encode($response);