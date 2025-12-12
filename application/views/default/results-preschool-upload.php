<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Allow-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultClientData, 
    $isPayableStaff, $clientFeatures, $isEmployee, $isAdmin;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_GET);

$response->title = "Preschool Results Upload";

// access permissions check
if(!$isEmployee && !$isAdmin) {
    $response->html = page_not_found("permission_denied");
} else {

    $response->scripts = ["assets/js/remarks.js"];

    // set the client id
    $filter->clientId = $session->clientId;
    $filter->client_data = $defaultClientData;

    // Load reporting classes setting
    $getSettingsValues = load_class("settings", "controllers")->getsettings((object) [
        "clientId" => $session->clientId, "setting_name" => [
            "preschool_reporting_legend", "preschool_reporting_content", "preschool_reporting_classes"
        ]
    ])["data"] ?? [];

    $reporting_classes = $getSettingsValues["preschool_reporting_classes"]["classes"] ?? [];
    $reporting_template = $getSettingsValues["preschool_reporting_content"] ?? null;
    $reporting_legend = $getSettingsValues["preschool_reporting_legend"] ?? null;
    
    // Get classes that are enabled for preschool reporting
    $classes_array = [];
    if(!empty($reporting_classes)) {
        $classes_array = $myClass->pushQuery("id, name", "classes", "client_id='{$session->clientId}' AND status='1' AND id IN {$myClass->inList($reporting_classes)}", false, "ASSOC");
    }

    $classes_list = "<option value=''>Select Class</option>";
    foreach($classes_array as $each) {
        $classes_list .= "<option value='{$each['id']}'>{$each['name']}</option>";
    }

    // Build legend reference HTML
    $legend_reference_html = '';
    if(!empty($reporting_legend) && !empty($reporting_legend['legend'])) {
        $legend_reference_html = '<div class="row mb-3" id="grading_legend_reference">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> GRADING SYSTEM REFERENCE</h6>
                        <button type="button" class="btn btn-sm btn-light" onclick="toggle_legend_reference();">
                            <i class="fas fa-chevron-up" id="legend_toggle_icon"></i>
                        </button>
                    </div>
                    <div class="card-body p-2" id="legend_reference_body">';
        
        foreach($reporting_legend['legend'] as $key => $legend_item) {
            $legend_key = htmlspecialchars($legend_item['key'] ?? '', ENT_QUOTES, 'UTF-8');
            $legend_value = htmlspecialchars($legend_item['value'] ?? '', ENT_QUOTES, 'UTF-8');
            $legend_reference_html .= '<span class="badge badge-primary p-2 mr-2 mb-2">
                <strong>'.$legend_key.'</strong> = '.$legend_value.'
            </span>';
        }
        
        $legend_reference_html .= '</div>
                </div>
            </div>
        </div>';
    }

    // set the parent menu
    $response->parent_menu = "reports-promotion";

    $userId = $session->userId;
    $clientId = $session->clientId;
    
    // Pass template and legend data to JavaScript
    $template_json = htmlspecialchars(json_encode($reporting_template), ENT_QUOTES, 'UTF-8');
    $legend_json = htmlspecialchars(json_encode($reporting_legend), ENT_QUOTES, 'UTF-8');
    
    $response->html = '
        <script>
            window.preschoolReportingTemplate = '.($reporting_template ? $template_json : 'null').';
            window.preschoolReportingLegend = '.($reporting_legend ? $legend_json : 'null').';
            window.clientId = "'.$clientId.'";
        </script>
        <section class="section">
            <div class="section-header">
                <h1>'.$response->title.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="'.$baseUrl.'results-preschool-setup">Results Setup</a></div>
                    <div class="breadcrumb-item">'.$response->title.'</div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-primary mb-0 pb-0">
                                <i class="fas fa-graduation-cap"></i> SELECT CLASS AND STUDENT
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                    <label>Select Class</label>
                                    <select name="preschool_class_id" id="preschool_class_id" class="form-control selectpicker" data-width="100%">
                                        '.$classes_list.'
                                    </select>
                                </div>
                                <div class="col-12 col-md-5 mb-2 mb-md-0">
                                    <label>Select Student</label>
                                    <select name="preschool_student_id" id="preschool_student_id" class="form-control selectpicker" data-width="100%">
                                        <option value="">Select Class First</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100 w-md-auto" onclick="return load_student_reporting();">
                                        <i class="fas fa-search"></i> Load Student Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            '.$legend_reference_html.'
            <div class="row mt-3" id="student_reporting_container" style="display: none;">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h5 class="card-title text-primary mb-0 pb-0 mr-2">
                                    <i class="fas fa-user"></i> <span id="student_name_display"></span>
                                </h5>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary" onclick="return navigate_student(-1);" id="prev_student_btn">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="return navigate_student(1);" id="next_student_btn">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <span class="text-muted ml-2">
                                        <span id="student_counter">0</span> of <span id="total_students">0</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" id="student_reporting_content">
                            <!-- Reporting template will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </section>';

}
    
// print out the response
echo json_encode($response);
?>
