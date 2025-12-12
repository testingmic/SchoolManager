<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

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

$response->title = "Preschool Reporting Setup";

// access permissions check
if(!$isEmployee && !$isAdmin) {
    $response->html = page_not_found("permission_denied");
} else {

    $response->scripts = ["assets/js/remarks.js"];

    // set the client id
    $filter->clientId = $session->clientId;
    $filter->client_data = $defaultClientData;

    $getSettingsValues = load_class("settings", "controllers")->getsettings((object) [
        "clientId" => $session->clientId, "setting_name" => [
            "preschool_reporting_legend", "preschool_reporting_content", "preschool_reporting_classes"
        ]
    ])["data"] ?? [];

    // load the form
    $settings = $getSettingsValues["preschool_reporting_legend"] ?? [];

    // load the reporting content
    $reporting_content = $getSettingsValues["preschool_reporting_content"] ?? [];

    // load the reporting classes
    $reporting_classes = $getSettingsValues["preschool_reporting_classes"] ?? [];

    // build the reporting content HTML
    $reporting_content_html = '';
    if(!empty($reporting_content) && !empty($reporting_content['sections'])) {
        $section_counter = 1;
        foreach($reporting_content['sections'] as $section_index => $section) {
            // Ensure we have a valid section ID
            $section_id = !empty($section['id']) ? intval($section['id']) : (time() + $section_counter);
            $section_title = htmlspecialchars($section['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $questionnaires = $section['questionnaires'] ?? [];
            
            $reporting_content_html .= '<div class="mb-3 border rounded p-3" data-section_id="'.$section_id.'">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 font-weight-bold text-primary">'.($section_title ?: 'Untitled Section').'</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="return add_questionnaire('.$section_id.');" title="Add Questionnaire">
                            <i class="fas fa-plus"></i> Add Row
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="return delete_reporting_section('.$section_id.');" title="Delete Section">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-2">
                    <input type="text" class="form-control text-uppercase section-title-input" data-section_id="'.$section_id.'" value="'.$section_title.'" placeholder="Enter section title (e.g. Communication Skills)" maxlength="100">
                </div>
                <div class="questionnaires-list" data-section_id="'.$section_id.'">';
            
            if(!empty($questionnaires)) {
                $q_counter = 1;
                foreach($questionnaires as $q_index => $questionnaire) {
                    // Ensure we have a valid questionnaire ID
                    $q_id = !empty($questionnaire['id']) ? intval($questionnaire['id']) : ($section_id * 1000 + $q_counter);
                    $q_text = htmlspecialchars($questionnaire['text'] ?? '', ENT_QUOTES, 'UTF-8');
                    $reporting_content_html .= '<div class="d-flex align-items-center mb-2" data-questionnaire_id="'.$q_id.'">
                        <div class="flex-grow-1 mr-2">
                            <input type="text" class="form-control form-control-sm questionnaire-input" data-section_id="'.$section_id.'" data-questionnaire_id="'.$q_id.'" value="'.$q_text.'" placeholder="Enter questionnaire item" maxlength="200">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="return delete_questionnaire('.$section_id.', '.$q_id.');" title="Delete">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>';
                    $q_counter++;
                }
            }
            
            $reporting_content_html .= '</div>
            </div>';
            $section_counter++;
        }
    }

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

    $classes_array = $myClass->pushQuery("id, name", "classes", "client_id='{$session->clientId}' AND status='1'");

    $classes_list = "";
    $reporting_classes = $reporting_classes['classes'] ?? [];
    foreach($classes_array as $each) {
        $selected = in_array($each->id, $reporting_classes) ? "selected" : null;
        $classes_list .= "<option value='{$each->id}' {$selected}>{$each->name}</option>";
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
                <div class="col-lg-7 col-md-6">
                    <div class="row">
                        <div class="col-md-12 text-right mb-2">
                            <a class="btn btn-outline-success" href="#" onclick="return add_reporting_section();">
                                <i class="fas fa-plus"></i> Add New Section
                            </a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-primary mb-0 pb-0">
                                <i class="fas fa-file-alt"></i> REPORTING TEMPLATE
                            </h5>
                        </div>
                        <div class="card-body pt-2 pb-2" id="preschool_reporting_content">
                            '.($reporting_content_html ?: '<div class="text-muted text-center py-3">No sections added yet. Click "Add New Section" to get started.</div>').'
                        </div>
                    </div>
                </div>
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
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title text-primary mb-0 pb-0">
                                <i class="fas fa-info-circle"></i> CLASSES TO APPLY
                            </h5>
                        </div>
                        <div class="card-body pt-2 pb-2" id="preschool_reporting_classes">
                            <select name="reporting_classes[]" id="reporting_classes[]" onchange="return save_reporting_classes();" class="form-control selectpicker" multiple data-width="100%">
                                '.$classes_list.'
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>';

}
    
// print out the response
echo json_encode($response);
?>