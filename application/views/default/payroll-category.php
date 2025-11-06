<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);

$response->title = "Payslip Setup";

// end query if the user has no permissions
if(!in_array("payroll", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled", ["payroll"]);
    echo json_encode($response);
    exit;
}

$clientId = $session->clientId;

$staff_list = "";

// access permissions check
if(!$accessObject->hasAccess("modify_payroll", "payslip")) {
    $response->html = page_not_found("permission_denied");
} else {

    $color = [
        "Allowance" => "success",
        "Deduction" => "danger",
        "teacher" => "warning"
    ];

    // student id
    $isPreviewMode = $SITEURL[1] ?? false;
    $recordId = $SITEURL[2] ?? false;

    $idWhere = "";
    $isSingleRecord = false;
    if($isPreviewMode && $recordId) {
        $idWhere = "AND id='{$recordId}'";
        $isSingleRecord = true;
    }

    // get the allowance types
    $allowanceData = $myClass->pushQuery("*", "payslips_allowance_types", "client_id='{$clientId}' AND status='1' {$idWhere} ORDER BY type");

    // if the record is not found
    if($isPreviewMode && $recordId && empty($allowanceData)) {
        $response->html = page_not_found("record_not_found");
        echo json_encode($response);
        exit;
    }

    $allowance_array_list = [];

    // load the form
    $category_form = load_class("forms", "controllers")->payroll_category_form($clientId, $baseUrl, $isSingleRecord, $allowanceData[0] ?? (object) []);
    $settings = load_class("settings", "controllers")->getsettings((object) [
        "clientId" => $clientId, "setting_name" => "payroll_settings"
    ])["data"];

    // loop through the payslip category list
    foreach($allowanceData as $key => $each) {
        
        $allowance_array_list[$each->id] = $each;

        $action = "<a href='#' title='Click to update this category' onclick='return update_allowance(\"{$each->id}\")' class='btn btn-sm btn-outline-success'>
            <i class='fa fa-edit'></i>
        </a>";
        $action .= "&nbsp;<a title='Click to delete this Allowance Category' href='#' onclick='return delete_record(\"{$each->id}\", \"allowance\");' class='btn btn-sm btn-outline-danger'>
            <i class='fa fa-trash'></i>
        </a>";

        $extras = "";
        if($each->type == "Allowance") {
            $extras .= $each->subject_to_paye == "Yes" ? "<span class='badge badge-primary p-1'>Subject to PAYE</span> " : "";
            $extras .= $each->subject_to_ssnit == "Yes" ? "<span class='badge badge-primary p-1'>Subject to SSNIT</span>" : "";
        }

        if($each->type == "Deduction") {
            $method = str_ireplace("_", " ", $each->calculation_method);
            $extras .= "<span class='badge badge-info p-1'>".ucwords($method)."</span>";
            $extras .= $each->pre_tax_deduction == "Yes" ? "<span class='badge badge-danger p-1'>Pre Tax Deduction</span>" : "";
        }

        $staff_list .= "<tr data-row_id=\"{$each->id}\">";
        $staff_list .= "<td>".($key+1)."</td>";
        $staff_list .= "<td>
            <strong>{$each->name}</strong>
            <div class='mb-1'>
                ".($each->is_statutory == "Yes" ? "<span class='badge badge-primary p-1'>Statutory</span>" : "<span class='badge badge-warning p-1'>Non-Statutory</span>")."
            </div>
            <div class='mb-1'>{$extras}</div>
        </td>";
        $staff_list .= "<td><span class='badge badge-{$color[$each->type]}'>{$each->type}</span></td>";
        $staff_list .= "<td>{$each->description}</td>";
        $staff_list .= $isSingleRecord ? '' : "<td class='text-center'>{$action}</td>";
        $staff_list .= "</tr>";

    }

    // append the questions list to the array to be returned
    $response->array_stream["allowance_array_list"] = $allowance_array_list;

    $response->scripts = ["assets/js/payroll.js"];

    $response->html = '
        <section class="section">

            <div class="section-header">
                <h1>'.$response->title.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'payroll">Payroll</a></div>
                    <div class="breadcrumb-item">'.$response->title.'</div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-12 '.($isSingleRecord ? 'col-lg-6' : 'col-lg-8').'">
                    <div class="text-right mb-2">
                        '.($isSingleRecord ? '' : '<a class="btn btn-outline-primary" onclick="return add_allowance();" href="#"><i class="fa fa-plus"></i> Add New</a>').'
                        '.($isSingleRecord ? '<a class="btn btn-outline-warning" href="'.$baseUrl.'payroll-category"><i class="fa fa-arrow-left"></i> Go Back</a>' : '').'
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-lg-4"></div>
                <div class="col-12 col-sm-12 '.($isSingleRecord ? 'col-lg-6' : 'col-lg-8').'">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table data-empty="" class="table table-bordered table-sm table-striped raw_datatable">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th>Name</th>
                                            <th width="15%">Type</th>
                                            <th>Description</th>
                                            '.($isSingleRecord ? '' : '<th width="15%" align="center"></th>').'
                                        </tr>
                                    </thead>
                                    <tbody>'.$staff_list.'</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-4 '.($isSingleRecord ? 'hidden' : '').'">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Payroll Frequency & Setup</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3 col-sm-12 col-lg-12">
                                    <div style="padding-left: 2.5rem;" class="custom-control cursor col-lg-12 custom-switch switch-primary">
                                        <input '.(!empty($settings['auto_generate_payslip']) ? 'checked' : '').' type="checkbox" value="1" name="auto_generate_payslip" id="auto_generate_payslip" class="custom-control-input cursor">
                                        <label class="custom-control-label  cursor" for="auto_generate_payslip">Auto Generate Payslips</label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-lg-12">
                                    <div class="form-group">
                                        <select data-width="100%" name="payroll_frequency" class="form-control selectpicker">
                                            <option value="">Payroll Frequency</option>
                                            '.implode("", array_map(function($each) use ($settings) {
                                                return "<option value=\"{$each}\" ".($settings['payroll_frequency'] == $each ? 'selected' : '').">{$each}</option>";
                                            }, $myClass->payroll_frequency_list)).'
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-lg-12">
                                    <div class="form-group">
                                        <input type="number" min="1" maxlength="20" value="'.(!empty($settings['payment_day']) ? $settings['payment_day'] : '1').'" placeholder="Payment Day" name="payment_day" id="payment_day" class="form-control">
                                        <span class="text-muted">Day of month for salary payment.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Tax Calculation Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3 col-sm-12 col-lg-12">
                                    <div style="padding-left: 2.5rem;" class="custom-control cursor col-lg-12 custom-switch switch-primary">
                                        <input '.(!empty($settings['auto_calculate_paye']) ? 'checked' : '').' type="checkbox" value="1" name="auto_calculate_paye" id="auto_calculate_paye" class="custom-control-input cursor">
                                        <label class="custom-control-label  cursor" for="auto_calculate_paye">Auto Calculate PAYE</label>
                                    </div>
                                </div>
                                <div class="col-12  mb-3 col-sm-12 col-lg-12">
                                    <div style="padding-left: 2.5rem;" class="custom-control cursor col-lg-12 custom-switch switch-primary">
                                        <input '.(!empty($settings['auto_calculate_ssnit']) ? 'checked' : '').' type="checkbox" value="1" name="auto_calculate_ssnit" id="auto_calculate_ssnit" class="custom-control-input cursor">
                                        <label class="custom-control-label  cursor" for="auto_calculate_ssnit">Auto Calculate SSNIT</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3 col-sm-12 col-lg-12">
                                    <div style="padding-left: 2.5rem;" class="custom-control cursor col-lg-12 custom-switch switch-primary">
                                        <input '.(!empty($settings['auto_calculate_tier_2']) ? 'checked' : '').' type="checkbox" value="1" name="auto_calculate_tier_2" id="auto_calculate_tier_2" class="custom-control-input cursor">
                                        <label class="custom-control-label  cursor" for="auto_calculate_tier_2">Auto Calculate Tier 2</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <button onclick="return save_payroll_settings();" class="btn btn-block btn-outline-success"><i class="fa fa-save"></i> Save Changes</button>
                        </div>
                    </div>

                </div>
                '.($isSingleRecord ? '
                <div class="col-12 col-sm-12 col-lg-6">
                    '.$category_form.'
                </div>
                ' : '').'
            </div>

        </section>
        <div data-backdrop="static" data-keyboard="false" class="modal fade" id="allowanceTypesModal">
            '.$category_form.'
        </div>';
}

// print out the response
echo json_encode($response);
?>