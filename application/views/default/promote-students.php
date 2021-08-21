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

// if the user has the requisite permotions
if(!$accessObject->hasAccess("promote", "promotion")) {
    $response->html = page_not_found("permission_denied");
} else {

    // promotion params
    $promotion_params = (object) [
        "client_data" => $defaultUser->client,
        "clientId" => $clientId,
        "append_log" => true,
    ];
    $promotion_array_list = load_class("promotion", "controllers")->history($promotion_params)["data"];
    
    // approve promotion
    $validatePromotion = $accessObject->hasAccess("approve", "promotion");
    
    // append the array list
    $promotion_list = "";
    $promotion_array = [];
    $response->array_stream["class_list"] = $class_list;

    // loop through the list of data
    foreach($promotion_array_list as $key => $each) {

        // append to the array list
        $promotion_array[$each->history_log_id] = $each;

        $status = ($validatePromotion && $each->status === "Pending") ? 
            "<button title='Click to Validate this Promotion Log' onclick='return validate_Promotion_Log(\"{$each->history_log_id}\")' class='btn btn-outline-success'>Validate</button>
            <button title='Click to Cancel this Promotion Log' onclick='return cancel_Promotion_Log(\"{$each->history_log_id}\")' class='btn btn-outline-danger'><i class='fa fa-trash'></i></button>" : null;

        // append to the table list
        $promotion_list .= "<tr data-row_id=\"{$each->history_log_id}\">";
        $promotion_list .= "<td>".($key+1)."</td>";
        $promotion_list .= "<td>{$each->academic_year}</td>";
        $promotion_list .= "<td>{$each->from_class_name}</td>";
        $promotion_list .= "<td>{$each->to_class_name}</td>";
        $promotion_list .= "<td>{$each->logged_by_data->name}<br> <strong>{$each->logged_by_data->unique_id}</strong></td>";
        $promotion_list .= "<td align='center'>{$each->students_count}</td>";
        $promotion_list .= "<td>{$each->date_log}</td>";
        $promotion_list .= "<td>{$myClass->the_status_label($each->status)}</td>";
        $promotion_list .= "<td align='center'><button title='Click to View this Promotion Log' onclick='return view_Promotion_Log(\"{$each->history_log_id}\")' class='btn btn-outline-primary'><i class='fa fa-eye'></i></button> {$status}</td>";
        $promotion_list .= "</tr>";
    }
    $response->array_stream["promotion_list"] = $promotion_array;


    // set the data
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-chart-line"></i> Promote Students</h1>
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
                                <div class="table-responsive table-student_staff_list">
                                    <table data-empty="" class="table table-bordered table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th>Academic Year</th>
                                                <th>Promoted From</th>
                                                <th>Promoted To</th>
                                                <th>Performed By</th>
                                                <th class="text-center">Students Count</th>
                                                <th>Date of Promotion</th>
                                                <th>Status</th>
                                                <th class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>'.$promotion_list.'</tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>';

}
// print out the response
echo json_encode($response);