<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $defaultClientData, $defaultCurrency, $defaultAcademics, $clientFeatures, $academicSession;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];

// end query if the user has no permissions
if(!$accessObject->hasAccess("receive", "fees")) {
    // permission denied information
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

$filter = (object) array_map("xss_clean", $_POST);
$pageTitle = "Previous Term Arrears Payment";
$response->title = $pageTitle;

// student id
$user_id = $SITEURL[1] ?? null;

// set the page header
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-street-view"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'students">Students</a></div>';

// if the user id is not empty
if(!empty($user_id) && ($user_id !== "add")) {

    // execute the client limit query
    $myClass->clients_accounts_limit($clientId);

    // check if the page limit has reached
    if($myClass->accountLimit->fees) {
        $response->html = notification_modal("Fees / Arrears Limit Reached", $myClass->error_logs["fees_limit"]["msg"], $myClass->error_logs["fees_limit"]["link"]);
    } else {

        // remove the session if existing
        $session->remove(["e_payment_transaction_id"]);

        // init variable
        $data = [];
        $disabled = true;
        $student_fees_arrears = null;
        $arrears_array = [];
        $isLoadAll = (bool) ($user_id === "apay");

        // confirm if the account check is empty
        if(empty($defaultClientData->default_account_id)) {
            // message to share
            $response->html = notification_modal("Payment Account Not Set", $myClass->error_logs["account_not_set"]["msg"], $myClass->error_logs["account_not_set"]["link"]);
        } else {

            // load the page if the user_id is not pay
            if(!$isLoadAll) {
                // set the student parameter
                $student_param = (object) [
                    "clientId" => $clientId,
                    "user_id" => $user_id,
                    "limit" => 1,
                    "user_status" => $myClass->student_statuses,
                    "minified" => "simplified",
                    "user_type" => "student"
                ];

                // set the script to run
                $response->scripts = ["assets/js/fees_allocation.js"];

                $data = load_class("users", "controllers", $student_param)->list($student_param);
            }

            // if no record was found
            if(!$isLoadAll && empty($data["data"])) {
                $response->html = page_not_found();
                echo json_encode($response);
                exit;
            }

            // scripts for the page
            $response->scripts = ["assets/js/debtors.js"];

            // get the list of banks
            $banks_list = $myClass->pushQuery("id, bank_name, phone_number", "banks_list", "1 ORDER BY bank_name");

            // load this page if not load all
            if(!$isLoadAll) {

                // set the first key
                $data = $data["data"][0];

                // get the student arrears record
                $arrears_array = $myClass->pushQuery("arrears_details, arrears_category, fees_category_log, arrears_total", "users_arrears", "student_id='{$data->user_id}' AND client_id='{$clientId}' LIMIT 1");

                // get the class and student fees allocation
                $student_fees_arrears = "";
                $disabled = null;

                // if the fees arrears not empty
                if(!empty($arrears_array)) {
                                    
                    // set a new item for the arrears
                    $arrears = $arrears_array[0];
                    $outstanding = 0;

                    // convert the item to array
                    $arrears_details = json_decode($arrears->arrears_details, true);
                    $arrears_category = json_decode($arrears->arrears_category, true);
                    $fees_category_log = json_decode($arrears->fees_category_log, true);
                    
                    // set the arrears_total
                    if(round($arrears->arrears_total) > 0) {

                        // set the table head
                        $student_fees_arrears .= "<table class='table table-md table-bordered'>";
                        $students_fees_category_array = filter_fees_category($fees_category_log);

                        // loop through the arrears details
                        foreach($arrears_details as $year => $categories) {

                            // clean the year term
                            $split = explode("...", $year);
                            
                            // set the academic year header
                            $student_fees_arrears .= "<thead>";

                            $student_fees_arrears .= "<tr class='font-20'><td><strong>Year: </strong>".str_ireplace("_", "/", $split[0])."</td>";
                            $student_fees_arrears .= "<td><strong>{$academicSession}: </strong> {$split[1]}</td></tr>";
                            $student_fees_arrears .= "<tr><th>DESCRIPTION</th><th>BALANCE</th></tr>";
                            $student_fees_arrears .= "</thead>";
                            $student_fees_arrears .= "<tbody>";
                            $total = 0;
                            // loop through the items for each academic year
                            foreach($categories as $cat => $value) {
                                // add the sum
                                $total += $value;
                                $outstanding += $value;
                                // display the category name and the value
                                $student_fees_arrears .= "<tr><td>{$students_fees_category_array[$cat]["name"]}</td><td>{$value}</td></tr>";
                            }
                            $student_fees_arrears .= "<tr><td></td>
                                    <td class='font-20 font-bold'>".number_format($total, 2)."</div>
                                    </td>
                                </tr>";
                            $student_fees_arrears .= "</tbody>";
                        }

                        $student_fees_arrears .= "</table>";
                    } else {
                        $disabled = "disabled";
                        $student_fees_arrears = "<div class='col-md-12 font-20 text-center text-success'><strong>{$data->name}</strong> currently has no fees arrears.</div>";
                    }

                }

            }

            // if no fees arrears details was found
            if(!$isLoadAll && empty($arrears_array)) {
                $disabled = "disabled";
                $student_fees_arrears = "<div class='col-md-12 font-18 text-center text-success'><strong>{$data->name}</strong> currently has no fees arrears.</div>";
            } elseif($isLoadAll) {
                $student_fees_arrears = "<div class='col-md-12 font-18 text-center text-danger'>You have not selected any student yet.</div>";
            }

            // get the client payment methods
            if(in_array("e_payments", $clientFeatures)) {
                $myClass->payment_methods["momo_card"] = "Mobile Money / Card Payment";
            }

            // get the list of all debtors
            $arrears_debtors_list = $myClass->pushQuery(
                "a.arrears_total, a.student_id, UPPER(b.name) AS fullname",
                "users_arrears a LEFT JOIN users b ON b.item_id = a.student_id", 
                "ROUND(a.arrears_total) > 0 AND b.client_id='{$clientId}' ORDER BY fullname LIMIT {$myClass->temporal_maximum}");
            
            // append the html content
            $response->html .= '
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'debtors">Debtors List</a></div>
                    '.(!empty($data) ? '<div class="breadcrumb-item">'.strtoupper($data->name).'</div>' : null).'
                </div>
            </div>
            <div class="section-body">
            <div class="row">
                <div id="fees_arrears_list" class="col-12 col-md-5 col-lg-4">
                    <div class="form-group mb-2">
                        <div class="col-md-12 p-0"><label>Filter by Student name</label></div>
                        <div class="row">
                            <div class="col-md-9 col-sm-8 mb-1">
                                <select data-width="100%" class="form-control selectpicker" name="debtor_id" id="debtor_id">
                                    <option value="">Select Student</option>';
                                    foreach($arrears_debtors_list as $value) {
                                        $response->html .= "<option ".($user_id == $value->student_id ? "selected" : null)." value=\"{$value->student_id}\">{$value->fullname} ({$defaultCurrency} {$value->arrears_total})</option>";
                                    }
                                    $response->html .='
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-4 mb-1">
                                <button onclick="return load_debtor_details()" class="btn height-40 btn-block btn-outline-primary">Load</button>
                            </div>
                        </div>
                    </div>
                    <div class="card author-box pt-2">
                        <div class="card-body pl-1 pr-1">
                            '.(!empty($data) ? '
                                <div class="author-box-center m-0 p-0 flex justify-center">
                                    <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                                </div>
                                <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                                <div class="text-center border-top mt-0">
                                    <div class="author-box-description font-22 text-success font-weight-bold">REG. ID: '.$data->unique_id.'</div>
                                    <div title="Date of Birth" class="author-box-description font-22 font-weight-bold mt-1">
                                        <i class="fa fa-calendar"></i> '.format_date_of_birth($data->date_of_birth).'
                                    </div>
                                    <div title="Class Name" class="author-box-description font-22 text-info font-weight-bold mt-1"><i class="fa fa-house-damage"></i> '.$data->class_name.'</div>
                                    <div title="Department Name" class="author-box-description font-22 text-info font-weight-bold mt-1">'.$data->department_name.'</div>
                                    <div class="w-100 mt-2 border-top pt-3">
                                        <a class="btn btn-dark" href="'.$baseUrl.'student/'.$user_id.'"><i class="fa fa-arrow-circle-left"></i> View Record</a>
                                    </div>
                                </div>' : '
                                <div class="text-center mt-0">Student Record Appears Here</div>
                                '
                            ).'
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-8">
                    <div class="card">
                        <div class="card-header text-uppercase">Fees Arrears Payment</div>
                        <div id="change_notification" class="font-18 hidden p-3 text-center text-danger">Click on load to display the arrears form here.</div>
                        <div class="card-body" id="fees_arrears_payment">
                            <div class="form-content-loader" style="display: none; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <div class="row" id="arrears_payment_form">
                                <div class="form-content-loader" style="display: none; position: absolute">
                                    <div class="offline-content text-center">
                                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                    </div>
                                </div>
                                <div class="col-lg-'.($disabled ? 12 : 6).'">
                                    '.$student_fees_arrears.'
                                </div>
                                <div class="col-lg-6 '.($disabled ? "hidden" : null).'">
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
                                        <label>Bank Name <span class="required">*</span></label>
                                        <select '.$disabled.' data-width="100%" class="form-control selectpicker" id="bank_id" name="bank_id">
                                            <option value="">Select Bank Name</option>';
                                            foreach($banks_list as $bank) {
                                                $response->html .= "<option value=\"{$bank->bank_name}\">{$bank->bank_name}</option>";
                                            }
                                        $response->html .=
                                        '</select>
                                    </div>
                                    '.(!empty($data) ? '
                                        <div class="form-group hidden" id="cheque_payment_filter">
                                            <label>Cheque Number <span class="required">*</span></label>
                                            <input '.$disabled.' class="form-control text-uppercase" name="cheque_number" id="cheque_number" type="number" min="0">
                                        </div>
                                        <div class="form-group hidden" id="cheque_payment_filter">
                                            <label>Cheque Security Code <span class="required">*</span></label>
                                            <input '.$disabled.' class="form-control text-uppercase" name="cheque_security" id="cheque_security" type="text" min="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Contact Number</label>
                                            <input '.$disabled.' value="'.$data->phone_number.'" class="form-control" name="contact_number" id="contact_number" type="text">
                                        </div>
                                        <div class="form-group">
                                            <label class="email_label">Email Address</label>
                                            <input '.$disabled.' value="'.$data->email.'" class="form-control" name="email_address" id="email_address" type="email">
                                        </div>
                                        <div class="form-group">
                                            <label>Amount <span class="required">*</span></label>
                                            <input '.$disabled.' class="form-control" name="amount" id="amount" type="number" min="0">
                                        </div>
                                        <div class="text-right">
                                            '.($disabled ? null : '
                                                <input type="hidden" name="arrears_student_id" id="arrears_student_id" disabled value="'.$user_id.'">
                                                <input type="hidden" name="outstanding" id="outstanding" disabled value="'.$outstanding.'">
                                                <button '.$disabled.' id="default_payment_button" onclick="return save_Receive_Payment();" class="btn btn-outline-success"><i class="fa fa-money-check-alt"></i> Make Payment</button>
                                                <button '.$disabled.' id="momocard_payment_button" onclick="return receive_Momo_Card_Payment();" class="btn hidden btn-outline-success"><i class="fa fa-money-check-alt"></i> Pay via MoMo/Card</button>
                                                <input type="hidden" hidden id="client_subaccount" name="client_subaccount" disabled value="'.$defaultClientData->client_account.'">'
                                            ).'
                                        </div>' : '<input hidden class="form-control" name="amount" id="amount" type="number" min="0">' 
                                        ).'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';

        }

    }
    
}

// if the user id is not empty
elseif(!empty($user_id) && ($user_id === "add")) {

    // run the academic terms query
    $myClass->academic_terms($clientId);

    // add filters
    $response->scripts = ["assets/js/filters.js", "assets/js/debtors.js"];

    // default class_list
    $classes_param = (object) [
        "clientId" => $clientId,
        "columns" => "a.id, a.name"
    ];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // append the html content
    $response->html .= '<div class="breadcrumb-item active"><a href="'.$baseUrl.'debtors">Debtors List</a></div>
                <div class="breadcrumb-item">Add Record</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row" id="filter_Department_Class">

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="row" id="arrearsForm">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label>Class</label>
                                        <select data-width="100%" class="form-control selectpicker" name="class_id">
                                            <option value="">Select Class</option>';
                                            foreach($class_list as $each) {
                                                $response->html .= "<option value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                                            }
                                            $response->html .= '
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="form-group">
                                        <label>Select Student</label>
                                        <select data-width="100%" class="form-control selectpicker" name="student_id">
                                            <option value="">Select Student</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 border-top pt-3">
                                    <div class="form-group mb-0 text-right">
                                        <button onclick="return load_Student_Arrears()" class="btn btn-outline-success"><i class="fa fa-filter"></i> Load Record</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="change_history"></div>
                </div>

                <div class="col-md-7">
                    <div class="form-content-loader" style="display: none; position: absolute">
                        <div class="offline-content text-center">
                            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-2 text-right"><button class="btn hidden btn-outline-primary" data-target="addStudentArrears"><i class="fa fa-plus"></i> Add Arrears</button></div>
                            <table border="1" id="student_Fees_Arrears" width="100%" class="table table-bordered table-md">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Academic Year / '.$academicSession.'</th>
                                        <th align="left">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td colspan="3" align="center">Student Record Appears Here</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>';
}

// set the footer
$response->html .= '</div></section>';

if($user_id === "add") {
    $response->html .= '
    <div class="modal fade" id="addStudentArrears" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-top modal-md" style="width:100%;" role="document">
            <div class="modal-content">
                <div class="form-content-loader" style="display: none; position: absolute">
                    <div class="offline-content text-center">
                        <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                    </div>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title">Log Arrears</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <input hidden class="ajax-replies-loaded" value="0" data-form="none">
                <div class="modal-body" style="text-align:left">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>Academic Year <span class="required">*</span></label>
                                <select data-width="100%" class="form-control selectpicker" name="academic_year" id="academic_year">
                                    <option value=\'\'>Select Year</option>';
                                        foreach($myClass->academic_calendar_years as $year_group) {
                                            $response->html .= "<option value=\"{$year_group}\">{$year_group}</option>";                            
                                        }
                                    $response->html .= '
                                        </select>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label>Academic '.$academicSession.' <span class="required">*</span></label>
                                <select data-width="100%" class="form-control selectpicker" name="academic_term" id="academic_term">
                                    <option value="">Select '.$academicSession.'</option>';
                                        foreach($myClass->school_academic_terms as $each) {
                                            $response->html .= "<option value=\"{$each->name}\">{$each->description}</option>";                            
                                        }
                                    $response->html .= '
                                        </select>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="category_id">Category ID <span class="required">*</span></label>
                                <select data-width="100%" name="category_id" id="category_id" class="form-control selectpicker">
                                    <option value="">Select Category</option>';
                                    foreach($myClass->pushQuery("id, name, amount", "fees_category", "status='1' AND client_id='{$clientId}'") as $each) {
                                        $response->html .= "<option data-amount=\"{$each->amount}\" value=\"{$each->id}\">{$each->name}</option>";                            
                                    }
                                $response->html .= '</select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-0 pb-0">
                                <label for="amount">Enter the Amount <span class="required">*</span></label>
                                <input type="number" min="0" onkeyup="this.value = this.value.replace(/[^\d.]+/g, \'\');" name="amount" id="amount" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    <button onclick="return quick_Add_Arrears()" class="btn btn-outline-success">Log Record</button>
                </div>
            </div>
        </div>
    </div>';
}

// print out the response
echo json_encode($response);
?>