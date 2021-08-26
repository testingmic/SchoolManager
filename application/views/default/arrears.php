<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $defaultClientData;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Fees Arrears";
$response->title = "{$pageTitle} : {$appName}";

$response->scripts = ["assets/js/fees_allocation.js"];

// student id
$user_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($user_id)) {

    // set the student parameter
    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "minified" => "simplified",
        "user_type" => "student"
    ];

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
            }

        } else { 
            $disabled = "disabled";
            $student_fees_arrears = "<div class='col-md-12 font-20 text-success'><strong>{$data->name}</strong> currently has no fees arrears.</div>";
        }

        // scripts for the page
        $response->scripts = ["assets/js/arrears.js"];
        
        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-student">Students</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-5 col-lg-4">
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
                                        <a class="btn btn-dark" href="'.$baseUrl.'update-student/'.$user_id.'"><i class="fa fa-arrow-circle-left"></i> Go Back</a>
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

                                    <div class="col-lg-6">
                                        '.$student_fees_arrears.'
                                    </div>
                                    <div class="col-lg-6">
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
                                        <div class="col-md-6 form-group">
                                            <label>Contact Number</label>
                                            <input '.$disabled.' class="form-control" name="contact_number" id="contact_number" type="text">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="email_label">Email Address</label>
                                            <input '.$disabled.' class="form-control" name="email_address" id="email_address" type="email">
                                        </div>
                                        <div class="form-group">
                                            <label>Amount <span class="required">*</span></label>
                                            <input '.$disabled.' class="form-control" name="amount" id="amount" type="number" min="0">
                                        </div>
                                        <div class="text-right">
                                            <input type="hidden" name="student_id" id="student_id" disabled value="'.$user_id.'">
                                            <button '.$disabled.' id="default_payment_button" onclick="return save_Receive_Payment();" class="btn btn-outline-success"><i class="fa fa-money-check-alt"></i> Make Payment</button>
                                            <button '.$disabled.' id="momocard_payment_button" onclick="return receive_Momo_Card_Payment();" class="btn hidden btn-outline-success"><i class="fa fa-money-check-alt"></i> Pay via MoMo/Card</button>
                                            <input type="hidden" hidden id="client_subaccount" name="client_subaccount" disabled value="'.$defaultClientData->client_account.'">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>';
    }

}
// print out the response
echo json_encode($response);
?>