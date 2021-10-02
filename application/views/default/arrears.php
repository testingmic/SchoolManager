<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $defaultClientData, $defaultCurrency, $defaultAcademics;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$filter = (object) $_POST;
$pageTitle = "Student Fees Arrears";
$response->title = "{$pageTitle} : {$appName}";

// student id
$user_id = $SITEURL[1] ?? null;

// set the page header
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="'.$baseUrl.'students">Students</a></div>';

// if not user id was parsed then list the full arrears list
if(empty($user_id)) {

    // set the parameters
    $param = (object) [
        "academics" => $defaultAcademics,
        "clientId" => $clientId, 
        "limit" => $myClass->global_limit,
        "client_data" => $defaultClientData, 
        "class_id" => $filter->class_id ?? null,
        "department_id" => $filter->department_id ?? null,
    ];

    // set the script to run
    $response->scripts = ["assets/js/filters.js"];

    // create a new arrears object
    $arrearsObj = load_class("arrears", "controllers", $param);

    // set the response header
    $response->html .= '<div class="breadcrumb-item">Fees Arrears</div>
                </div>
            </div>
            <div class="section-body">';

    // get the list of all debtors
    $arrears_array = $arrearsObj->list($param);

    // set the fees arrears list
    $students_arrears_list = "";

    // initial variables
    $class_summary = [];

    // if the fees arrears not empty
    if(!empty($arrears_array["data"])) {
        // initial value
        $count = 0;
        $students_ids = [];
        $receivePayment = $accessObject->hasAccess("receive", "fees");

        // loop through the arrears details
        foreach($arrears_array["data"] as $key => $student) {

            // append to the array list
            $students_ids[] = $student->_student_id;

            // if the total is more than 1
            if(($student->arrears_total > 0) || ($student->debt > 0)) {

                // append to the class summary information
                $total = $student->debt + $student->arrears_total;
                // $class_summary[$student->class_name] = isset($class_summary[$student->class_name]) ? ($class_summary[$student->class_name] + $total) : $total;

                // set the button
                $action = "";
                if($receivePayment) {
                    $arrears = "<button title='Click to pay previous fees arrears' onclick='load(\"arrears/{$student->student_id}\");' class='btn btn-sm btn-outline-success'><i class='fa fa-money-bill-alt'></i> PAY</button>";
                    $fees = "&nbsp;<button title='Click to pay current fees balance' onclick='load(\"fees-payment?student_id={$student->student_id}&class_id={$student->class_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-money-bill'></i> PAY</button>";
                }
                $action .= "&nbsp;<button onclick='load(\"student/{$student->student_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i> VIEW</button>";
                $count++;
                // append to the list
                $students_arrears_list .= "
                <tr>
                    <td>{$count}</td>
                    <td>
                        <div class='d-flex text-uppercase justify-content-start'>
                            ".(!empty($student->student_info->image) ? "
                            <div class='mr-2'><img src='{$baseUrl}{$student->student_info->image}' width='40px' height='40px'></div>" : "")."
                            <div>
                                <span class='user_name' onclick='load(\"student/{$student->student_id}\");'>".strtoupper($student->student_info->name)."</span> <br>
                            <strong>{$student->student_info->unique_id}</strong></div>
                        </div>
                    </td>
                    <td>{$student->class_name}</td>
                    <td>{$defaultCurrency} {$student->debt} ".($receivePayment && ($student->debt > 0) ? $fees : null)."</td>
                    <td>{$defaultCurrency} {$student->arrears_total} ".($receivePayment && ($student->arrears_total > 0) ? $arrears : null)."</td>
                    <td>{$defaultCurrency} {$total}</td>
                    <td align='center'>{$action}</td>
                </tr>";
            }

        }

        // get the list of students who do not appear in the list
        $other_students = $myClass->pushQuery(
            "a.name, c.id AS class_id, c.name AS class_name, a.unique_id, a.image, a.item_id,
                (
                    SELECT sum(b.balance) FROM fees_payments b 
                    WHERE b.student_id = a.item_id AND b.academic_term = '{$defaultAcademics->academic_term}'
                        AND b.academic_year = '{$defaultAcademics->academic_year}' AND b.exempted='0'
                ) AS debt
            ", 
            "users a LEFT JOIN classes c ON c.id = a.class_id", 
            "a.client_id='{$clientId}' AND a.id NOT IN {$myClass->inList($students_ids)}
                ".(!empty($filter->class_id) ? " AND c.id IN {$myClass->inList($filter->class_id)}" : "")."
                AND a.user_type='student' AND a.status='1' AND a.user_status='Active'");

        // loop through the students list
        foreach($other_students as $student) {
            if($student->debt > 0) {
                // append to the class summary information
                $total = $student->debt;
                // $class_summary[$student->class_name] = isset($class_summary[$student->class_name]) ? ($class_summary[$student->class_name] + $total) : $total;

                // set the button
                $action = "";
                if($receivePayment) {
                    $fees = "&nbsp;<button title='Click to pay current fees balance' onclick='load(\"fees-payment?student_id={$student->item_id}&class_id={$student->class_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-money-bill'></i> PAY</button>";
                }
                $action .= "&nbsp;<button onclick='load(\"arrears/add?sid={$student->item_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i> VIEW</button>";
                $count++;
                // append to the list
                $students_arrears_list .= "
                <tr>
                    <td>{$count}</td>
                    <td>
                        <div class='d-flex text-uppercase justify-content-start'>
                            ".(!empty($student->image) ? "
                            <div class='mr-2'><img src='{$baseUrl}{$student->image}' width='40px' height='40px'></div>" : "")."
                            <div>
                                <span class='user_name' onclick='load(\"student/{$student->item_id}\");'>".strtoupper($student->name)."</span> <br>
                            <strong>{$student->unique_id}</strong></div>
                        </div>
                    </td>
                    <td>{$student->class_name}</td>
                    <td>{$defaultCurrency} {$student->debt} ".($receivePayment && ($student->debt > 0) ? $fees : null)."</td>
                    <td>{$defaultCurrency} 0.00</td>
                    <td>{$defaultCurrency} {$total}</td>
                    <td align='center'>{$action}</td>
                </tr>";
            }
        }
    }

    // default class_list
    $classes_param = (object) [
        "clientId" => $clientId,
        "columns" => "id, name"
    ];
    // if the class_id is not empty
    $classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    $response->html .= '
        <div class="row" id="filter_Department_Class">
            <div class="col-lg-8 col-md-6 col-12 form-group">
                <label>Multiple Select Class to Filter</label>
                <select data-width="100%" multiple class="form-control selectpicker" name="class_id">';
                    foreach($class_list as $each) {
                        $response->html .= "<option ".(isset($filter->class_id) && (in_array($each->id, $filter->class_id)) ? "selected" : "")." value=\"{$each->id}\">{$each->name}</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-lg-2 col-sm-6 col-md-3 col-12 form-group">
                <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                <button id="filter_Fees_Arrears" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-lg-2 col-sm-6 col-md-3 col-12 form-group">
                <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                <button onclick="return load(\'arrears/add\');" class="btn btn-outline-primary btn-block"><i class="fa fa-plus"></i> ADD RECORD</button>
            </div>
            <div class="col-lg-12"></div>';
            foreach($class_summary as $name => $amount) {
                $response->html .= "
                <div class='col-lg-2 col-md-3 col-sm-6 mb-1'>
                    <div class='card mb-1'>
                        <div style='line-height:12px' class='card-header bg-info text-white font-bold text-uppercase p-2 pb-0 pt-0'>{$name}</div>
                        <div class='card-body font-18 mb-0 pb-1 pt-1 pl-2'>
                            {$defaultCurrency} ".number_format($amount, 2)."
                        </div>
                    </div>
                </div>";
            }
            $response->html .=
            '<div class="col-12 mt-2 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Term Bill</th>
                                        <th>Arrears</th>
                                        <th>Total</th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$students_arrears_list.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

}

// if the user id is not empty
elseif(!empty($user_id) && ($user_id !== "add")) {

    // set the student parameter
    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "minified" => "simplified",
        "user_type" => "student"
    ];

    // set the script to run
    $response->scripts = ["assets/js/fees_allocation.js"];

    $data = load_class("users", "controllers", $student_param)->list($student_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {
        
        // set the first key
        $data = $data["data"][0];

        // get the list of banks
        $banks_list = $myClass->pushQuery("id, bank_name, phone_number", "banks_list", "1 ORDER BY bank_name");
        $arrears_array = $myClass->pushQuery("arrears_details, arrears_category, fees_category_log, arrears_total", "users_arrears", "student_id='{$data->user_id}' AND client_id='{$clientId}' LIMIT 1");

        // get the class and student fees allocation
        $student_fees_arrears = "";
        $disabled = null;

        // if the fees arrears not empty
        if(!empty($arrears_array)) {
                
            // include the array helper
            load_helpers(['array_helper']);
            
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
                    $student_fees_arrears .= "<td><strong>Term: </strong> {$split[1]}</td></tr>";
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

        } else { 
            $disabled = "disabled";
            $student_fees_arrears = "<div class='col-md-12 font-20 text-center text-success'><strong>{$data->name}</strong> currently has no fees arrears.</div>";
        }

        // scripts for the page
        $response->scripts = ["assets/js/arrears.js"];

        // get the list of all debtors
        $arrears_debtors_list = $myClass->pushQuery(
            "a.arrears_total, a.student_id, UPPER(b.name) AS fullname",
            "users_arrears a LEFT JOIN users b ON b.item_id = a.student_id", 
            "ROUND(a.arrears_total) > 0 AND b.client_id='{$clientId}' ORDER BY fullname LIMIT 500");
        
        // append the html content
        $response->html .= '
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'arrears">Arrears List</a></div>
                <div class="breadcrumb-item">'.$data->name.'</div>
            </div>
        </div>
        <div class="section-body">
        <div class="row">
            <div class="col-12 col-md-5 col-lg-4">
                <div class="form-group mb-2">
                    <div class="col-md-12"><label>Filter by Student name</label></div>
                    <div class="row">
                        <div class="col-md-9 col-sm-8 mb-1">
                            <select data-width="100%" class="form-control selectpicker" name="debtor_id" id="debtor_id">';
                                foreach($arrears_debtors_list as $value) {
                                    $response->html .= "<option ".($user_id == $value->student_id ? "selected" : null)." value=\"{$value->student_id}\">{$value->fullname} ({$defaultCurrency} {$value->arrears_total})</option>";
                                }
                                $response->html .='
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-4 mb-1">
                            <button onclick="return load_debtor_details()" class="btn btn-block btn-outline-primary">Load</button>
                        </div>
                    </div>
                </div>
                <div class="card author-box pt-2">
                    <div class="card-body pl-1 pr-1">
                        <div class="author-box-center m-0 p-0">
                            <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                        </div>
                        <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                        <div class="text-center border-top mt-0">
                            <div class="author-box-description font-22 text-success font-weight-bold">REG. ID: '.$data->unique_id.'</div>
                            <div title="Date of Birth" class="author-box-description font-22 font-weight-bold mt-1"><i class="fa fa-calendar"></i> '.$data->date_of_birth.'</div>
                            <div title="Class Name" class="author-box-description font-22 text-info font-weight-bold mt-1"><i class="fa fa-house-damage"></i> '.$data->class_name.'</div>
                            <div title="Department Name" class="author-box-description font-22 text-info font-weight-bold mt-1">'.$data->department_name.'</div>
                            <div class="w-100 mt-2 border-top pt-3">
                                <a class="btn btn-dark" href="'.$baseUrl.'student/'.$user_id.'"><i class="fa fa-arrow-circle-left"></i> View Record</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-7 col-lg-8">
                <div class="card">
                    <div class="card-header text-uppercase">Fees Arrears Payment</div>
                    <div class="card-body" id="fees_arrears_payment">
                        <div class="form-content-loader" style="display: none; position: absolute">
                            <div class="offline-content text-center">
                                <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                            </div>
                        </div>
                        <div class="row" id="arrears_payment_form">

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
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

    }

}

// if the user id is not empty
elseif(!empty($user_id) && ($user_id === "add")) {

    // run the academic terms query
    $myClass->academic_terms($clientId);

    // add filters
    $response->scripts = ["assets/js/filters.js", "assets/js/arrears.js"];

    // default class_list
    $classes_param = (object) [
        "clientId" => $clientId,
        "columns" => "id, name"
    ];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // append the html content
    $response->html .= '<div class="breadcrumb-item active"><a href="'.$baseUrl.'arrears">Arrears List</a></div>
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
                                                $response->html .= "<option value=\"{$each->id}\">{$each->name}</option>";
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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" data-target="quickStudentAdd" class="btn btn-outline-primary">Add</button>
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
                </div>

                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-2 text-right"><button class="btn hidden btn-outline-primary" data-target="addStudentArrears"><i class="fa fa-plus"></i> Add Arrears</button></div>
                            <table id="student_Fees_Arrears" class="table table-bordered table-md">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Academic Year / Term</th>
                                        <th>Amount</th>
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
                <div class="modal-header">
                    <h5 class="modal-title">Log Arrears</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <input hidden class="ajax-replies-loaded" value="0" data-form="none">
                <div class="modal-body" style="text-align:left">
                    <div class="form-content-loader" style="display: none; position: absolute">
                        <div class="offline-content text-center">
                            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                        </div>
                    </div>
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
                                <label>Academic Term <span class="required">*</span></label>
                                <select data-width="100%" class="form-control selectpicker" name="academic_term" id="academic_term">
                                    <option value="">Select Term</option>';
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
                                <input type="number" name="amount" id="amount" class="form-control">
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