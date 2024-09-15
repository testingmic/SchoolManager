<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $academicSession;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$promotion_log_id = $SITEURL[1] ?? null;
$filter = (object) array_map("xss_clean", $_POST);

$response->title = "Promote Students Log ";
$response->scripts = ["assets/js/filters.js", "assets/js/promotion.js"];

$clientId = $session->clientId;

// if the user has the requisite permotions
if(!$accessObject->hasAccess("promote", "promotion") || empty($promotion_log_id)) {
    $response->html = page_not_found("permission_denied");
} else {

    // default class_list
    $classes_param = (object) [
        "clientId" => $clientId,
        "userId" => $defaultUser->user_id,
        "user_type" => $defaultUser->user_type,
        "columns" => "a.id, a.name, a.slug, a.item_id"
    ];
    // if the class_id is not empty=
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // promotion params
    $promotion_params = (object) [
        "client_data" => $defaultUser->client,
        "history_id" => $promotion_log_id,
        "clientId" => $clientId,
        "append_log" => true,
    ];
    $promotion_array_list = load_class("promotion", "controllers")->history($promotion_params)["data"];
    
    // confirm that a record was found
    if(empty($promotion_array_list)) {
        $response->html = page_not_found("not_found");
    } else {

        // approve promotion
        $validatePromotion = $accessObject->hasAccess("approve", "promotion");

        // set a new information
        $data = $promotion_array_list[0];

        // validated
        $isValidated = (bool) ($data->status === "Processed");

        // init
        $promotions_list = null;
        $promoted = 0;
        $count = 0;
        $repeated = 0;

        // confirm that the promotions log is not empty
        if(!empty($data->promotion_log) && is_array($data->promotion_log)) {
            // loop through the promotions log
            foreach($data->promotion_log as $student) {
                
                // increment the promoted & repeated count
                $count++;
                $promoted += $student->is_promoted ? 1 : 0;
                $repeated += !$student->is_promoted ? 1 : 0;

                // append the student list
                $status = ($student->is_promoted == 1) ? "<span class='badge badge-success'>Promoted</span>" : (($student->is_promoted == 2) ? "<span class='badge badge-primary'>On Hold</span>" : (($student->is_promoted == 3) ? "<span class='badge badge-warning'>Cancelled</span>" : "<span class='badge badge-danger'>Repeated</span>"));

                $promotions_list .= "<tr data-row_search='name' data-student_unique_id='{$student->unique_id}' data-student_fullname=\".strip_quotes($student->name).\">";
                $promotions_list .= "<td>{$count}</td>";
                $promotions_list .= "<td>".strtoupper($student->name)."</td>";
                $promotions_list .= "<td>{$student->from_class_name}</td>";
                $promotions_list .= "<td>{$student->to_class_name}</td>";
                $promotions_list .= "<td>{$status}</td>";

                $promotions_list .= !$isValidated ? "<td><input style=\"height:25px\" type='checkbox' name='student_to_promote[]' class='student_to_promote form-control cursor' value='{$student->student_id}'></td>" : null;
                $promotions_list .= "</tr>";
            }
        } 

        // set the data
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-chart-line"></i> Promote Students Log</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'promotions">Promotion History</a></div>
                    <div class="breadcrumb-item">Log</div>
                </div>
            </div>
            <div class="row byPass_Null_Value">

                <div class="col-12 col-md-6 col-lg-5">
                    <div class="card">
                        <div class="card-header pr-2 pl-2">
                            <h4>ACADEMIC INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0 pr-2 pl-2">
                            <div class="py-2">
                                <p class="clearfix">
                                    <span class="float-left">Academic Year</span>
                                    <span class="float-right text-muted">'.$data->academic_year.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Academic '.$academicSession.'</span>
                                    <span class="float-right text-muted">'.$data->academic_term.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Logged By</span>
                                    <span class="float-right text-muted text-uppercase">'.$data->logged_by_data->name.' 
                                        <i onclick="return load(\'staff/'.$data->logged_by_data->user_id.'\');" class="fa btn btn-outline-success btn-sm fa-eye"></i>
                                    </span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Date Created</span>
                                    <span class="float-right text-muted">'.$data->date_created.'</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-header pr-2 pl-2">
                            <h4>PROMOTION INFORMATION</h4>
                        </div>
                        <div class="card-body pt-0 pb-0 pr-2 pl-2">
                            <div class="py-2">
                                <p class="clearfix">
                                    <span class="float-left">Promoted From</span>
                                    <span class="float-right user_name" title="View Class Record" onclick="return load(\'class/'.$data->promote_from.'\');">'.$data->from_class_name.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Promoted To</span>
                                    <span class="float-right user_name" title="View Class Record" onclick="return load(\'class/'.$data->promote_to.'\');">'.$data->to_class_name.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Class Students Count</span>
                                    <span class="float-right user_name">'.$data->students_count.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Status</span>
                                    <span class="float-right text-muted">'.$myClass->the_status_label($data->status).'</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-header pr-2 pl-2">
                            <h4>SUMMARY DATA</h4>
                        </div>
                        <div class="card-body pt-0 pb-0 pr-2 pl-2">
                            <div class="py-3">
                                <p class="clearfix">
                                    <span class="float-left">Students Promoted</span>
                                    <span class="float-right text-muted">'.$promoted.'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Students Repeated</span>
                                    <span class="float-right text-muted">'.$repeated.'</span>
                                </p>
                                '.($validatePromotion && !$isValidated ? '
                                <p class="clearfix text-center mt-3 border-top pt-3">
                                    <span onclick="return validate_Promotion_Log(\''.$promotion_log_id.'\')" class="btn btn-sm btn-outline-success">Validate Promotion</span>
                                    <button title="Click to Cancel this Promotion Log" onclick="return cancel_Promotion_Log(\''.$promotion_log_id.'\')" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> Cancel</button>
                                </p>': '<p class="text-center text-uppercase mt-3 pt-3">'.$myClass->the_status_label($data->status).'</p>').'
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row" id="promote_list">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="'.(!$isValidated ? "col-lg-9 col-md-8" : "col-md-12").'">'.$myClass->quick_student_search_form.'</div>
                                '.(
                                    !$isValidated ?
                                    '<div class="col-lg-3 pl-0 col-md-4">
                                        <div class="d-flex justify-content-between">
                                            <div class="col-8 mb-2">
                                                <label>Select Option</label>
                                                <select disabled name="bulk_modify" class="selectpicker" data-width="100%">
                                                    <option value="">Select Option</option>
                                                    <option value="promote">Promote Student</option>
                                                    <option value="repeat">Repeat Students</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="text-white">.</label>
                                                <button disabled id="bulk_modify" onclick="return modify_Student_Promotion(\''.$promotion_log_id.'\')" class="btn btn-block btn-outline-success"><i class="fa fa-save"></i> Modify</button>
                                            </div>
                                        </div>
                                    </div>' : null
                                ).'
                            </div>
                            <div class="table-responsive trix-slim-scroll">
                                <table width="100%" class="table table-bordered">
                                    <thead>
                                        <th width="7%"></th>
                                        <th width="20%">Student Name / ID</th>
                                        <th>Promoted From</th>
                                        <th>Promoted To</th>
                                        <th>Status</th>
                                        '.(!$isValidated ? '<th>
                                            <input type="checkbox" style="height:25px" title="Promote all students" class="cursor form-control" id="promote_all_student">
                                        </th>' : null).'
                                    </thead>
                                    <tbody>'.$promotions_list.'</tbody>
                                </table>
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