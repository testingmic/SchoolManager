<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $clientFeatures, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Timetable";
$response->title = $pageTitle;

// set the parent menu
$response->parent_menu = "timetable";

// confirm that the user has the required permissions
if(!$accessObject->hasAccess("manage", "timetable") || !in_array("timetable", $clientFeatures)) {
    // show the error page
    $response->html = page_not_found("permission_denied");
} else {

    // set some scripts to load
    $response->scripts = ["assets/js/timetable.js"];

    // confirm if the user has permission to manage
    $isPermitted = $accessObject->hasAccess("allocate", "timetable");

    // assign more variables
    $class_id = null;
    $disabled_inputs = [];
    $timetable_found = false;
    $timetable_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;

    // set the parameter for the classes
    $classes_param = (object) [
        "clientId" => $clientId,
        "columns" => "a.id, a.name, a.item_id"
    ];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // set the parameters to load
    $params = (object)[
        "clientId" => $clientId, "client_data" => $defaultUser->client
    ];

    // if the $timetable_id is not empty
    if(!empty($timetable_id)) {
        // append some more viables to get the information
        $params->limit = 1;
        $params->timetable_id = $timetable_id;
    }
    $timetable_list = load_class("timetable", "controllers", $params)->list($params);

    // set the timetable key
    $timetable_list = $timetable_list["data"];
    $columns_to_show = ['slots', 'days', 'start_time', 'duration', 'first_break_starts', 'first_break_ends', 'second_break_starts', 'second_break_ends'];

    $n_string = $disabled_inputs;

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-clock"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    '.($timetable_found ? '<div class="breadcrumb-item"><a href="'.$baseUrl.'timetable">Timetable List</a></div>' : null).'
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="text-right mb-2">
                        <a class="btn btn-outline-primary" href="'.$baseUrl.'timetable-manage"><i class="fa fa-plus"></i> Create New Timetable</a>
                    </div>
                    <div class="cared">
                        <div class="row" id="timetable_form">';
                            if(!$timetable_found) {
                                $response->html .= '
                                <div class="col-lg-12">';

                                if(empty($timetable_list)) {
                                    $response->html .= no_record_found("No Timetable Found", "No timetable has been created yet. Get started by creating your first timetable.", $baseUrl."timetable-manage", "New Timetable");
                                } else {
                                    $response->html .= '<div class="row">';
                                    foreach($timetable_list as $key => $value) {

                                        // expected days
                                        $expected_days = json_decode($value->expected_days, true);

                                        $response->html .= "
                                        <div data-row_id=\"{$value->item_id}\" class='col-lg-4 col-md-4 col-sm-6 col-12 rounded-2xl transition-all duration-300 hover:-translate-y-1'>
                                            <div class='card'>
                                                <div class='card-header'>
                                                    <h5 class='card-title font-weight-normal mb-0 font-17'>{$value->name}</h5>
                                                </div>
                                                <div class='card-body pt-1 pb-1'>
                                                    <div data-row_id=\"{$value->item_id}\">
                                                        ".($value->class_name ? 
                                                            "<p class='clearfix pb-0 mb-0'>
                                                                <span class='float-left font-weight-bolder'>Class</span>
                                                                <span class='float-right'>{$value->class_name}</span>
                                                            </p>" : ""
                                                        );
                                                        foreach($columns_to_show as $key) {
                                                            $key_name = ucfirst(str_ireplace("_", " ", $key));
                                                            $response->html .= "
                                                            <p class='clearfix pb-0 mb-0'>
                                                                <span class='float-left font-weight-bolder'>{$key_name}</span>
                                                                <span class='float-right'>{$value->{$key}}</span>
                                                            </p>";
                                                        }
                                                        $response->html .= "
                                                        <p class='clearfix pb-0 mb-0'>
                                                            <span class='float-left font-weight-bolder'>Expected Days</span>
                                                            <span class='float-right'>".( !empty($expected_days) ? implode("<br> ", $expected_days) : "N/A")."</span>
                                                        </p>
                                                        <p class='clearfix pb-0 mb-2 mt-2 text-right'>
                                                            <a href='#' onclick='return delete_record(\"{$value->item_id}\", \"timetable\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a>
                                                            ".($isPermitted ? "<a href='{$baseUrl}timetable-allocate/{$value->item_id}' title='Allocate subjects / courses to each time.' class='btn btn-outline-warning btn-sm'><i class='fa fa-copy'></i> Allocate</a>" : null)."
                                                            <a class='btn btn-outline-primary btn-sm' href='{$baseUrl}timetable-manage/{$value->item_id}' title='Modify the timetable structure.'><i class='fa fa-edit'></i> Modify</a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                                    }
                                    $response->html .= '</div>';
                                }
                                $response->html .= '
                                </div>';
                            }
                            $response->html .= '
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
?>