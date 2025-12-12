<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultClientData, $isPayableStaff, $clientFeatures, $isTeacher, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_GET);

$response->title = "Student Remarks";

// access permissions check
if(!$isTeacher && !$isAdmin) {
    $response->html = page_not_found("permission_denied");
} else {

    $response->scripts = ["assets/js/remarks.js"];

    // set the client id
    $filter->clientId = $session->clientId;
    $filter->client_data = $defaultClientData;

    $results_remarks_list = "
    <div class='col-md-12'>
        <div class='row border-bottom border-gray mb-2'>
            <div class='col-md-4 mb-2'>
            <select name='filter_remarks_class_id' id='filter_remarks_class_id' class='form-control selectpicker' data-width='100%'>
                <option value=''>Select Class to Filter</option>
                '.$classes_list.'
            </select>
            </div>
            <div class='col-md-8 mb-2' data-input_item='search'>
                <input type='text' class='form-control' placeholder='Search remarks' id='search_remarks' onkeyup='return search_remarks()'>
            </div>
        </div>
    </div>";

    // load the form
    $settings = load_class("settings", "controllers")->getsettings((object) [
        "clientId" => $session->clientId, "setting_name" => "preschool_reporting_legend"
    ])["data"] ?? [];

    $legend_html = '<div class="d-flex gap-2 justify-content-between mb-2" data-legend_item="1">
        <div class="w-[5%] font-20 text-danger">
            <br>1
        </div>
        <div class="w-[35%]">
            <div><strong>KEY:</strong></div>
            <div>
                <input maxlength="5" data-item="legend_key" type="text" name="legend_key[1]" class="form-control text-uppercase" placeholder="e.g. A">
            </div>
        </div>
        <div class="w-[58%]">
            <div><strong>INTERPRETATION:</strong></div>
            <div>
                <input maxlength="32" data-item="legend_value" type="text" name="legend_value[1]" class="form-control" placeholder="e.g. Excellent">
            </div>
        </div>
    </div>';

    if(!empty($settings)) {
        $legend_html = "";
        $count = 1;
        foreach($settings['legend'] as $key => $value) {
            $legend_html .= '<div class="d-flex gap-2 justify-content-between mb-2" data-legend_item="'.$count.'">
                <div class="w-[5%] font-20 text-danger">
                    '.($key == 1 ? "<br>{$count}" : $count).'
                </div>
                <div class="w-['.($key == 1 ? 35 : 30).'%]">
                    '.($key == 1 ? '<div><strong>KEY:</strong></div>' : '').'
                    <div>
                        <input maxlength="5" data-item="legend_key" type="text" value="'.($value['key'] ?? '').'" name="legend_key['.$count.']" class="form-control text-uppercase" placeholder="e.g. A">
                    </div>
                </div>
                <div class="w-['.($key == 1 ? 58 : 56).'%]">
                    '.($key == 1 ? '<div><strong>INTERPRETATION:</strong></div>' : '').'
                    <div>
                        <input maxlength="32" data-item="legend_value" type="text" value="'.($value['value'] ?? '').'" name="legend_value['.$count.']" class="form-control" placeholder="e.g. Excellent">
                    </div>
                </div>
                '.(
                    $key !== 1 ? '
                    <div class="w-[8%]">
                        <div>
                            <button type="button" class="btn btn-outline-danger" onclick="return delete_legend_item('.$count.');"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>' : ''
                ).'
            </div>';
            $count++;
        }
    }

    // set the parent menu
    $response->parent_menu = "reports-promotion";

    $userId = $session->userId;
    $clientId = $session->clientId;
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$response->title.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item">'.$response->title.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5 col-md-6">
                    <div class="row">
                        <div class="col-md-12 text-right mb-2">
                            <a class="btn btn-outline-success" href="#" onclick="return add_preschool_reporting();">
                                <i class="fas fa-plus"></i> Add New Legend
                            </a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-primary mb-0 pb-0">
                                <i class="fas fa-info-circle"></i> REPORTING LEGEND
                            </h5>
                        </div>
                        <div class="card-body pt-2 pb-2" id="preschool_reporting_legend">
                            '.$legend_html.'
                        </div>
                    </div>
                </div>
            </div>
        </section>';

}
    
// print out the response
echo json_encode($response);
?>