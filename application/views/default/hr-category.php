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

$response->title = "Payroll Allowance Types : {$appName}";
$response->scripts = ["assets/js/filters.js"];

$clientId = $session->clientId;
$accessObject->clientId = $clientId;
$accessObject->userId = $session->userId;
$accessObject->userPermits = $defaultUser->user_permissions;

$staff_list = "";

$color = [
    "Allowance" => "success",
    "Deduction" => "danger",
    "teacher" => "warning"
];

$allowance_types = $myClass->pushQuery("*", "payslips_allowance_types", "client_id='{$clientId}' AND status='1' ORDER BY type");

$allowance_array_list = [];
foreach($allowance_types as $key => $each) {
    $allowance_array_list[$each->id] = $each;
    // payslips_allowance_types
    $action = "<a href='#' onclick='return update_allowance(\"{$each->id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->id}\", \"allowance\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";

    $staff_list .= "<tr data-row_id=\"{$each->id}\">";
    $staff_list .= "<td>".($key+1)."</td>";
    $staff_list .= "<td>{$each->name}</td>";
    $staff_list .= "<td>{$each->description}</td>";
    $staff_list .= "<td>{$each->default_amount}</td>";
    $staff_list .= "<td><span class='badge badge-{$color[$each->type]}'>{$each->type}</span></td>";
    $staff_list .= "<td class='text-center'>{$action}</td>";
    $staff_list .= "</tr>";
}

// append the questions list to the array to be returned
$response->array_stream["allowance_array_list"] = $allowance_array_list;

$response->scripts = ["assets/js/payroll.js"];

$response->html = '
    <section class="section">

        <div class="section-header">
            <h1>Allowance Types</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Payroll List</a></div>
                <div class="breadcrumb-item">Allowance Types</div>
            </div>
        </div>
        <form action="'.$baseUrl.'api/payroll/saveallowance" class="ajax-data-form" id="ajax-data-form-content">
            <div data-backdrop="static" data-keyboard="false" class="modal fade" id="allowanceTypesModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Allowance / Deduction Types</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" maxlength="100" placeholder="Type name" name="name" id="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="allowance_type">Type</label>
                                        <select name="type" data-width="100%" id="type" class="form-control selectpicker">
                                            <option value="null">Please select type</option>
                                            <option value="Allowance">Allowance</option>
                                            <option value="Deduction">Deduction</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="default_amount">Default Amount or Percentage</label>
                                        <input type="text" maxlength="20" placeholder="Type default amount or percentage" name="default_amount" id="default_amount" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea placeholder="" maxlength="255" name="description" id="description" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer p-0">
                                <input type="hidden" name="allowance_id">
                                <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                                <button type="button-submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                    <a class="btn btn-sm btn-outline-primary" onclick="return add_allowance();" href="#"><i class="fa fa-plus"></i> Add New</a>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th width="10%">Default Amt OR %</th>
                                        <th>Type</th>
                                        <th width="8%" align="center"></th>
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
    
// print out the response
echo json_encode($response);
?>