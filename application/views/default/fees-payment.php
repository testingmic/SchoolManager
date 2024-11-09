<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $SITEURL, $defaultClientData, $defaultCurrency, $clientFeatures;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$getObject = (object) array_map("xss_clean", $_GET);
$pageTitle = "Pay Fees";
$response->title = $pageTitle;

// if the user has the permission to allocate fees
$receivePayment = $accessObject->hasAccess("receive", "fees");

/** confirm that the user has the permission to receive payment */
if(!$receivePayment) {
    $response->html = page_not_found("permission_denied");
} else {

    // execute the client limit query
    $myClass->clients_accounts_limit($clientId);

    // remove the session if existing
    $session->remove(["e_payment_transaction_id"]);

    // check if the page limit has reached
    if($myClass->accountLimit->fees) {
        $response->html = notification_modal("Fees / Arrears Limit Reached", $myClass->error_logs["fees_limit"]["msg"], $myClass->error_logs["fees_limit"]["link"]);
    } else {

        /** Preset */
        $department_id = null;
        $category_id = null;
        $students_list = [];
        $payment_form = "";
        $student_id = $getObject->student_id ?? null;
        $class_id = $getObject->class_id ?? null;

        // confirm if the account check is empty
        if(empty($defaultClientData->default_account_id)) {
            // message to share
            $response->html = notification_modal("Payment Account Not Set", $myClass->error_logs["account_not_set"]["msg"], $myClass->error_logs["account_not_set"]["link"]);
        } else {

        /** Create a parameter */
        $params = (object) [
            "clientId" => $clientId,
            "user_status" => ["Active", "Graduated", "Transferred", "Suspended", "Dismissed"],
            "client_data" => $defaultUser->client
        ];
        $params->class_id = $class_id;

        // disable form inputs
        $student_info = [];
        $search_disabled = null;
        $disabled = "disabled='disabled'";
        $isPaymentByMonth = (bool) (!empty($getObject->payment_month) || !empty($getObject->payment_module));
        $getPaymentMonth = !empty($getObject->payment_month) ? $getObject->payment_month : null;

        /** Confirm if some items has already been selected */
        if(isset($getObject->checkout_url)) {

            /** Clean the checkout url parsed */
            $checkout_url = $getObject->checkout_url;
            $params->checkout_url = $checkout_url;

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
            $data = is_array($data) ? $data[0] : $data;
            $amount = $data->balance;
            $class_id = $data->class_id;
            $student_id = $data->student_id;
            $category_id = $data->category_id;
            $department_id = $data->department_id ?? null;
            $disabled = (($data->paid_status == 1) || ($data->paid_status == '1')) ? "disabled='disabled'" : null;
            $search_disabled = ($data->paid_status == 1) ? null : "disabled='disabled'";

            print_r($data);exit;
            // set teh student information
            $student_info = [
                "name" => $data->student_details["student_name"],
                "unique_id" => $data->student_details["unique_id"],
                "phone_number" => $data->student_details["phone_number"],
                "image" => $data->student_details["image"],
                "debt" => $data->student_details["debt"],
                "arrears" => $data->student_details["arrears"],
                "total" => $data->student_details["debt"]
            ];

            // set the student id
            $response->array_stream["student_id"] = $student_id;
            
            // append the allocation information to the parameters before fetching the payment form
            $params->allocation_info = $data;
            $params->client = $defaultUser->client;
            $params->category_id = $data->category_id;

            // load the last payment information
            $payment_form = $feesClass->payment_form($params)["data"];
            $payment_form = $payment_form["form"];

            // append to the params
            $params->class_id = $class_id;
        }
        
        // load only students
        $params->user_type = "student";
        $params->set_id_as_key = true;

        // load the students list
        $students_list = [];
        
        // if the student id was parsed
        if(isset($getObject->student_id) || isset($checkout_url)) {
            $students_list = load_class("users", "controllers")->quick_list($params)["data"];
        }

        // load the class students list
        $response->array_stream["class_students_list"] = $students_list;

        // scripts for the page
        $response->scripts = ["assets/js/payments.js", "assets/js/filters.js"];

        // load the classes list
        $classes_param = (object) ["clientId" => $clientId, "columns" => "a.id, a.name, a.payment_module"];
        $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

        // get the list of banks
        $banks_list = $myClass->pushQuery("id, bank_name, phone_number", "banks_list", "1 ORDER BY bank_name");

        // set the client subaccount in a session
        $session->client_subaccount = $defaultClientData->client_account;

        // get the client payment methods
        if(in_array("e_payments", $clientFeatures)) {
            $myClass->payment_methods["momo_card"] = "Mobile Money / Card Payment";
        }

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
                '.(!empty($student_id) && !empty($class_id) ? "<input id='auto_load_form' hidden type='hidden'>" : null).'
                <div class="row mt-sm-4" id="filter_Department_Class">
                    <div class="col-12 col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h4>Student Details</h4>
                            </div>
                            <div class="card-body pt-0 pb-0">
                                <div class="py-3 pt-0" id="fees_payment_preload">
                                    <div class="byPass_Null_Value"></div>
                                    <input type="hidden" hidden name="no_status_filters" value="not_filtered">
                                    <input type="hidden" name="client_email_address" value="'.$defaultUser->client->client_email.'">
                                    <div class="form-group">
                                        <label>Select Class <span class="required">*</span></label>
                                        <select '.$search_disabled.' data-width="100%" class="form-control selectpicker" name="class_id">
                                            <option value="">Please Select Class</option>';
                                            foreach($class_list as $each) {
                                                $response->html .= "<option data-payment_module='{$each->payment_module}' ".(($class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                            }
                                            $response->html .= '
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Select Student <span class="required">*</span></label>
                                        <select '.$search_disabled.' data-width="100%" class="form-control selectpicker" name="student_id">
                                            <option value="">Please Select Student</option>';
                                            foreach($students_list as $each) {
                                                if($student_id == $each->user_id) {
                                                    $student_info = [
                                                        "user_id" => $each->user_id,
                                                        "name" => $each->name,
                                                        "unique_id" => $each->unique_id,
                                                        "image" => $each->image,
                                                        "phone_number" => $each->phone_number,
                                                        "debt" => $each->debt,
                                                        "arrears" => $each->arrears,
                                                        "total" => $each->total_debt_formated
                                                    ];
                                                }
                                                $response->html .= "<option data-arrears_formated=\"{$each->arrears_formated}\" data-total_debt_formated=\"{$each->total_debt_formated}\" data-debt_formated=\"{$each->debt_formated}\" data-name=\"{$each->name}\" data-image=\"{$each->image}\" data-phone_number=\"{$each->phone_number}\" data-unique_id=\"{$each->unique_id}\" ".(($student_id == $each->user_id) ? "selected" : "")." value=\"{$each->user_id}\">".strtoupper($each->name)."</option>";
                                            }
                                            $response->html .= '
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Select Category <small>(Optional)</small></label>
                                        <select '.$search_disabled.' data-width="100%" class="form-control selectpicker" name="category_id">
                                            <option value="">Please Select Category</option>';
                                            foreach($myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}'") as $each) {
                                                $response->html .= "<option ".(($category_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                            }
                                        $response->html .= '
                                        </select>
                                    </div>

                                    <div class="form-group '.(!$isPaymentByMonth ? "hidden" : null).'" id="payment_month">
                                        <label>Select Month to Assign <span class="required">*</span></label>
                                        <select data-width="100%" disabled class="form-control selectpicker" name="payment_month">
                                            <option value="">Please Select The Month</option>';
                                            foreach(range(0, 11, 1) as $period) {
                                                $month = date('F_Y', strtotime("January +$period month"));
                                                $name = str_ireplace("_", " ", $month);
                                                $response->html .= "<option ".($isPaymentByMonth && ($getPaymentMonth == $month) ? "selected" : null)." data-month_id=\"{$month}\" value=\"{$month}\">".strtoupper($name)."</option>";                            
                                            }
                                        $response->html .= '
                                        </select>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <div class="mb-1">
                                            <a class="btn btn-dark" data-link_item="student_go_back" href="#" onclick="return load(\''.(!empty($getObject->student_id) ? "student/{$getObject->student_id}" : ($student_id ? $student_id : "fees-history")).'\');">
                                                <i class="fa fa-arrow-circle-left"></i> Go Back
                                            </a>
                                        </div>
                                        <div class="mb-1">
                                            <div class="form-group mb-0 '.($category_id || $class_id ? null : 'hidden').'" id="make_payment_button">
                                                <button '.$search_disabled.' onclick="return load_Pay_Fees_Form()" class="btn btn-outline-success"><i class="fa fa-filter"></i> Load Form</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>    
                        </div>
                        <div id="student_information">
                        '.(!empty($student_info) ?
                            '<div class="card">
                                <div class="card-body p-3 pb-3 shadow-style">
                                    <div class="d-flex justify content-start">
                                        <div class="mr-2">
                                            <img onclick="return load(\'student/'.$student_info["user_id"].'\')" width="60px" class="img-shadow cursor" title="Click to view record of '.$student_info["name"].'" src="'.$baseUrl.$student_info["image"].'">
                                        </div>
                                        <div>
                                            <div class="font-18 user_name" title="Click to view record of '.$student_info["name"].'" onclick="return load(\'student/'.$student_info["user_id"].'\')">'.$student_info["name"].'</div>
                                            <div><strong>STUDENT ID:</strong> '.$student_info["unique_id"].'</div>
                                            <div><strong>FEES ARREARS:</strong> <span class="fees_arrears">'.$defaultCurrency.''.number_format($student_info["debt"], 2).'</span></div>
                                            <div><strong>PREVIOUS ARREARS:</strong> '.$defaultCurrency.''.number_format($student_info["arrears"], 2).'</div>
                                            <div><strong>BALANCE OUTSTANDING:</strong> <span class="balance_outstanding">'.$defaultCurrency.''.$student_info["total"].'</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>'
                        : null).'
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <input type="hidden" hidden id="client_subaccount" name="client_subaccount" disabled value="'.$defaultClientData->client_account.'">
                        <div class="card" id="fees_payment_form">
                            <div class="form-content-loader" style="display: none; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <div class="row padding-20" style="min-height:470px">
                                <div class="col-12 col-md-12">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Payment Medium</label>
                                            <select '.$disabled.' data-width="100%" class="form-control selectpicker" name="payment_method" id="payment_method">';
                                                foreach($myClass->payment_methods as $key => $value) {
                                                    $response->html .= "<option value=\"{$key}\">{$value}</option>";
                                                }
                                                $response->html .='
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group hidden" id="cheque_payment_filter">
                                            <label>Bank Name <span class="required">*</span></label>
                                            <select '.$disabled.' data-width="100%" class="form-control selectpicker" id="bank_id" name="bank_id">
                                                <option value="">Select Bank Name</option>';
                                            foreach($banks_list as $bank) {
                                                $response->html .= "<option value=\"{$bank->bank_name}::{$bank->id}\">".strtoupper($bank->bank_name)."</option>";
                                            }
                                            $response->html .=
                                            '</select>
                                        </div>
                                        <div class="col-md-4 form-group hidden" id="cheque_payment_filter">
                                            <label>Cheque Number <span class="required">*</span></label>
                                            <input '.$disabled.' onkeyup="this.value = this.value.replace(/[^\d.]+/g, \'\');" class="form-control text-uppercase" name="cheque_number" id="cheque_number" type="text" min="0">
                                        </div>
                                        <div class="col-md-4 form-group hidden" id="cheque_payment_filter">
                                            <label>Cheque Security Code <span class="required">*</span></label>
                                            <input '.$disabled.' onkeyup="this.value = this.value.replace(/[^\d.]+/g, \'\');" class="form-control text-uppercase" name="cheque_security" id="cheque_security" type="text" min="0">
                                        </div>
                                        <div class="col-md-6 form-group" id="payment_amount_input">
                                            <label>Amount <span class="required">*</span></label>
                                            <input onkeyup="this.value = this.value.replace(/[^\d.]+/g, \'\');" '.$disabled.' class="form-control" name="amount" id="amount" type="text">
                                        </div>
                                        <div class="col-md-12 mt-0 mb-0 form-group"></div>
                                        <div class="col-md-6 hidden form-group">
                                            <input '.$disabled.' class="form-control" name="contact_number" id="contact_number" type="text" value="">
                                        </div>
                                        <div class="col-md-6 hidden form-group">
                                            <input '.$disabled.' class="form-control" name="email_address" id="email_address" type="email">
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label>Remarks</label>
                                            <textarea '.$disabled.' class="form-control" name="description" style="height:100px" id="description"></textarea>
                                        </div>
                                        <div class="col-md-12 form-group text-right">
                                            <div class="d-flex justify-content-between">
                                            <div><button '.$disabled.' id="payment_cancel" onclick="return cancel_Payment_Form();" class="btn '.($category_id ? null : 'hidsden').' btn-dark"><i class="fa fa-ban"></i> Discard</button></div>
                                                <div>
                                                    <button '.$disabled.' id="default_payment_button" onclick="return save_Receive_Payment();" class="btn text-uppercase btn-outline-success"><i class="fa fa-money-check-alt"></i> Pay Fee</button>
                                                    <button '.$disabled.' id="momocard_payment_button" onclick="return receive_Momo_Card_Payment();" class="btn hidden btn-outline-success"><i class="fa fa-money-check-alt"></i> Pay via MoMo/Card</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-3 border-top pt-4 col-md-12" id="fees_payment_history">'.$payment_form.'</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';

        }
    
    }

}

// print out the response
echo json_encode($response);
?>
