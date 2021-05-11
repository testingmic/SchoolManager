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

$receivePayment = $accessObject->hasAccess("receive", "fees");

/** confirm that the user has the permission to receive payment */
if(!$receivePayment) {
    $response->html = page_not_found();
} else {
    /** Preset */
    $department_id = null;
    $category_id = null;
    $students_list = [];
    $payment_form = "";
    $student_id = null;
    $class_id = null;

    // disable form inputs
    $search_disabled = null;
    $disabled = "disabled='disabled'";

    /** Confirm if some items has already been selected */
    if(isset($_GET["checkout_url"])) {
        /** Clean the checkout url parsed */
        $checkout_url = xss_clean($_GET["checkout_url"]);

        /** Create a parameter */
        $params = (object) [
            "clientId" => $clientId,
            "client_data" => $defaultUser->client,
            "checkout_url" => $checkout_url
        ];
        /** Create a new object */
        $feesClass = load_class("fees", "controllers", $params);

        /** Get the student fees allocation */
        $data = $feesClass->confirm_student_payment_record($params);
        
        /** End the query if the data is empty */
        if(empty($data)) {
            // set the html to show
            $response->html = page_not_found();
            // print the error page
            echo json_encode($response);
            // end the query
            exit;
        }

        // set the information
        $amount = $data->balance;
        $class_id = $data->class_id;
        $student_id = $data->student_id;
        $category_id = $data->category_id;
        $department_id = $data->department_id;
        $disabled = $data->paid_status === 1 ? "disabled='disabled'" : null;
        $search_disabled = $data->paid_status === 1 ? null : "disabled='disabled'";

        // append the allocation information to the parameters before fetching the payment form
        $params->allocation_info = $data;
        $params->client = $defaultUser->client;

        // load the last payment information
        $payment_form = $feesClass->payment_form($params)["data"];
        $payment_form = $payment_form["form"];

        // append to the params
        $params->class_id = $class_id;
        $params->user_type = "student";
        $params->minified = "simplified";

        // load the students list
        $students_list = load_class("users", "controllers")->list($params)["data"];
    }

    // scripts for the page
    $response->scripts = ["assets/js/filters.js", "assets/js/payments.js"];

    // load the classes list
    $classes_param = (object) ["clientId" => $clientId, "columns" => "id, name"];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // get the list of banks
    $banks_list = $myClass->pushQuery("id, bank_name, phone_number", "fees_collection_banks", "1 ORDER BY bank_name");

    // append the html content
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
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
                                    <select '.$search_disabled.' data-width="100%" class="form-control selectpicker" id="department_id" name="department_id">
                                        <option value="">Please Select Department</option>';
                                        foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option ".(($department_id === $each->id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Select Class</label>
                                    <select '.$search_disabled.' data-width="100%" class="form-control selectpicker" name="class_id">
                                        <option value="">Please Select Class</option>';
                                        foreach($class_list as $each) {
                                            $response->html .= "<option ".(($class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Select Student</label>
                                    <select '.$search_disabled.' data-width="100%" class="form-control selectpicker" name="student_id">
                                        <option value="">Please Select Student</option>';
                                        foreach($students_list as $each) {
                                            $response->html .= "<option ".(($student_id == $each->user_id) ? "selected" : "")." value=\"{$each->user_id}\">{$each->name}</option>";
                                        }
                                        $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Select Category</label>
                                    <select '.$search_disabled.' data-width="100%" class="form-control selectpicker" name="category_id">
                                        <option value="">Please Select Category</option>';
                                        foreach($myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}'") as $each) {
                                            $response->html .= "<option ".(($category_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";                            
                                        }
                                    $response->html .= '
                                    </select>
                                </div>

                                <div class="form-group text-right mb-0 '.($category_id ? null : 'hidden').'" id="make_payment_button">
                                    <button '.$search_disabled.' onclick="return load_Pay_Fees_Form()" class="btn btn-outline-success">Load Form</button>
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
                                    <select '.$disabled.' data-width="100%" class="form-control selectpicker" name="payment_method" id="payment_method">';
                                        foreach($myClass->payment_methods as $key => $value) {
                                            $response->html .= "<option value=\"{$key}\">{$value}</option>";
                                        }
                                        $response->html .='
                                    </select>
                                </div>
                                <div class="form-group hidden" id="cheque_payment_filter">
                                    <label>Bank Name</label>
                                    <select '.$disabled.' data-width="100%" class="form-control selectpicker" id="bank_id" name="bank_id">
                                        <option value="">Select Bank Name</option>';
                                    foreach($banks_list as $bank) {
                                        $response->html .= "<option value=\"{$bank->bank_name}::{$bank->id}\">{$bank->bank_name}</option>";
                                    }
                                    $response->html .=
                                    '</select>
                                </div>
                                <div class="form-group hidden" id="cheque_payment_filter">
                                    <label>Cheque Number</label>
                                    <input '.$disabled.' class="form-control text-uppercase" name="cheque_number" id="cheque_number" type="number" min="0">
                                </div>
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input '.$disabled.' value="'.($amount ?? null).'" class="form-control" name="amount" id="amount" type="number" min="0">
                                </div>
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea '.$disabled.' class="form-control" name="description" id="description"></textarea>
                                </div>
                                <div class="form-group text-right">
                                    <div class="d-flex justify-content-between">
                                        <div><button '.$disabled.' id="payment_cancel" onclick="return cancel_Payment_Form();" class="btn '.($category_id ? null : 'hidden').' btn-outline-danger">Cancel</button></div>
                                        <div><button '.$disabled.' onclick="return save_Receive_Payment();" class="btn btn-outline-success"><i class="fa fa-save"></i> Save</button></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-7" id="fees_payment_history">'.$payment_form.'</div>
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