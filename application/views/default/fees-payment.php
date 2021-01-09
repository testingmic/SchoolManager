<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $SITEURL;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Fees Payment";
$response->title = "{$pageTitle} : {$appName}";

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

$receivePayment = $accessObject->hasAccess("receive", "fees");

/** confirm that the user has the permission to receive payment */
if(!$receivePayment) {
    $response->html = page_not_found();
} else {

    // disable form inputs
    $disabled = "disabled='disabled'";

    // scripts for the page
    $response->scripts = ["assets/js/filters.js", "assets/js/payments.js"];

    // load the classes list
    $classes_param = (object) ["clientId" => $clientId, "columns" => "id, name"];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // append the html content
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'fees-history">Fees Payment List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row mt-sm-4" id="filter_Department_Class">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Student Details</h4>
                        </div>
                        <div class="card-body pt-0 pb-0">
                            <div class="py-3 pt-0" id="fees_payment_preload">

                                <div class="form-group">
                                    <label>Select Department</label>
                                    <select class="form-control selectpicker" id="department_id" name="department_id">
                                        <option value="">Please Select Department</option>';
                                        foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Select Class</label>
                                    <select class="form-control selectpicker" name="class_id">
                                        <option value="">Please Select Class</option>';
                                        foreach($class_list as $each) {
                                            $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Select Student</label>
                                    <select class="form-control selectpicker" name="student_id">
                                        <option value="">Please Select Student</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Select Category</label>
                                    <select disabled="disabled" class="form-control selectpicker" name="category_id">
                                        <option value="">Please Select Category</option>';
                                        foreach($myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                        }
                                    $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group text-right mb-0 hidden" id="make_payment_button">
                                    <button onclick="return load_Pay_Fees_Form()" class="btn btn-outline-success">Load Form</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="row padding-20" style="min-height:470px">
                            <div class="col-12 col-md-5" id="fees_payment_form">
                                <div class="form-group">
                                    <label>Payment Medium</label>
                                    <select '.$disabled.' class="form-control selectpicker" name="payment_mode" id="payment_mode">
                                        <option value="cash">Cash</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input '.$disabled.' class="form-control" name="amount" id="amount" type="number" min="0">
                                </div>
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea '.$disabled.' class="form-control" name="remarks" id="remarks"></textarea>
                                </div>
                                <div class="form-group text-right">
                                    <div class="d-flex justify-content-between">
                                        <div><button '.$disabled.' id="payment_cancel" onclick="return cancel_Payment_Form();" class="btn hidden btn-outline-danger">Cancel</button></div>
                                        <div><button '.$disabled.' onclick="return save_Receive_Payment();" class="btn btn-outline-success"><i class="fa fa-save"></i> Save</button></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-7" id="fees_payment_history"></div>
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