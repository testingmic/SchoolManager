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

    $allowance_types = $myClass->pushQuery("*", "payslips_allowance_types", "client_id='{$clientId}' AND status='1' ORDER BY type");

    $allowance_array_list = [];

    // load the form
    $category_form = load_class("forms", "controllers")->payroll_category_form($clientId, $baseUrl);

    // loop through the payslip category list
    foreach($allowance_types as $key => $each) {
        $allowance_array_list[$each->id] = $each;
        // payslips_allowance_types
        $action = "<a href='#' title='Click to update this category' onclick='return update_allowance(\"{$each->id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
        $action .= "&nbsp;<a title='Click to delete this Allowance Category' href='#' onclick='return delete_record(\"{$each->id}\", \"allowance\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";

        $staff_list .= "<tr data-row_id=\"{$each->id}\">";
        $staff_list .= "<td>".($key+1)."</td>";
        $staff_list .= "<td>{$each->name}</td>";
        $staff_list .= "<td><span class='badge badge-{$color[$each->type]}'>{$each->type}</span></td>";
        $staff_list .= "<td>{$each->description}</td>";
        $staff_list .= "<td>{$each->is_statutory}</td>";
        $staff_list .= "<td class='text-center'>{$action}</td>";
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
            <div data-backdrop="static" data-keyboard="false" class="modal fade" id="allowanceTypesModal">
                '.$category_form.'
            </div>

            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="text-right mb-2">
                        <a class="btn btn-outline-primary" onclick="return add_allowance();" href="#"><i class="fa fa-plus"></i> Add New</a>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table data-empty="" class="table table-bordered table-sm table-striped raw_datatable">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Is Statutory</th>
                                            <th width="12%" align="center"></th>
                                        </tr>
                                    </thead>
                                    <tbody>'.$staff_list.'</tbody>
                                </table>
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