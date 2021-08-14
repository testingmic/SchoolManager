<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];

$response->title = "Fees Payment History : {$appName}";
$response->scripts = ["assets/js/filters.js"];

$filter = (object) $_POST;

$userId = $session->userId;

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "id, name"
];
// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

// begin the request parameter
$params = (object) [
    "clientId" => $clientId,
    "userData" => $defaultUser,
    "client_data" => $defaultUser->client,
    "student_array_ids" => $defaultUser->wards_list_ids ?? null,
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "category_id" => $filter->category_id ?? null
];

// if the student id is not empty
if(!empty($session->student_id)) {
    $params->student_id = $session->student_id;
}

// load the student fees payment
$item_list = load_class("fees", "controllers", $params)->list($params);

$hasAdd = $accessObject->hasAccess("add", "fees");
$hasUpdate = $accessObject->hasAccess("update", "fees");

$fees_history = "";
foreach($item_list["data"] as $key => $fees) {
    $action = "";
    $action = "<a href='#' title='View receipt details' onclick='loadPage(\"{$baseUrl}fees-view/{$fees->item_id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";
    $action .= "&nbsp;<a title='Click to print this receipt' href='#' onclick=\"return print_receipt('{$fees->item_id}')\" class='btn btn-sm btn-outline-warning'><i class='fa fa-print'></i></a>";
    
    $fees_history .= "<tr data-row_id=\"{$fees->item_id}\">";
    $fees_history .= "<td>".($key+1)."</td>";
    $fees_history .= "
        <td>
            <div class='d-flex justify-content-start'>
                ".(!empty($fees->student_info->image) ? "
                <div class='mr-2'><img src='{$baseUrl}{$fees->student_info->image}' width='40px' height='40px'></div>" : "")."
                <div>
                    <a href='#' onclick='loadPage(\"{$baseUrl}update-student/{$fees->student_info->user_id}\");'>{$fees->student_info->name}</a> <br>
                <strong>{$fees->student_info->unique_id}</strong></div>
            </div>
        </td>";
    $fees_history .= "<td>{$fees->class_name}</td>";
    $fees_history .= "<td>{$fees->category_name}</td>";
    $fees_history .= "<td>{$fees->currency} {$fees->amount}</td><td>";
    $fees_history .= "<strong>{$fees->payment_method}</strong>";

    // if the payment method was a cheque
    if($fees->payment_method === "Cheque") {
        $cheque_bank = explode("::", $fees->cheque_bank)[0];
        $fees_history .= $cheque_bank ? "<br><strong>{$cheque_bank}</strong>" : null;
        $fees_history .= $fees->cheque_number ? "<br><strong>#{$fees->cheque_number}</strong>" : null;
    }
    $fees_history .= "</td><td> ".(isset($fees->created_by_info->name) ? "{$fees->created_by_info->name} <br>" : null)."  <i class='fa fa-calendar'></i> {$fees->recorded_date}</td>";
    $fees_history .= "<td width='10%' align='center'>{$action}</td>";
    $fees_history .= "</tr>";
}

$response->html = '
<section class="section">
    <div class="section-header">
        <h1>Fees Payment History</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item">Fees Payment History</div>
        </div>
    </div>
    <div class="row" id="filter_Department_Class">
        <div class="col-xl-4 col-md-4 col-12 form-group">
            <label>Select Department</label>
            <select data-width="100%" class="form-control selectpicker" id="department_id" name="department_id">
                <option value="">Please Select Department</option>';
                foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                    $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                }
                $response->html .= '
            </select>
        </div>
        <div class="col-xl-3 col-md-3 col-12 form-group">
            <label>Select Class</label>
            <select data-width="100%" class="form-control selectpicker" name="class_id">
                <option value="">Please Select Class</option>';
                foreach($class_list as $each) {
                    $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                }
                $response->html .= '
            </select>
        </div>
        <div class="col-xl-3 col-md-3 col-12 form-group">
            <label>Select Category</label>
            <select data-width="100%" class="form-control selectpicker" name="category_id">
                <option value="">Please Select Category</option>';
                foreach($myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}'") as $each) {
                    $response->html .= "<option ".(isset($filter->category_id) && ($filter->category_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";                            
                }
            $response->html .= '
            </select>
        </div>
        <div class="col-xl-2 col-md-2 col-12 form-group">
            <label for="">&nbsp;</label>
            <button id="filter_Fees_Collection" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
        </div>
        <div class="col-12 col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table data-empty="" class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th width="10%">Fees Type</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Recorded By / Date</th>
                                    <th align="center" width="12%"></th>
                                </tr>
                            </thead>
                            <tbody>'.$fees_history.'</tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>';

// print out the response
echo json_encode($response);
?>