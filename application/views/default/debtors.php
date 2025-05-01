<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $session, $myClass, $accessObject, $defaultAcademics, $defaultCurrency, $academicSession;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);
$response->title = "Class Debtors ";

$amount = 0;
$class_summary = [];
$class_debtors_list = null;

// if the user has the required permissions
if(!$accessObject->hasAccess("view", "fees")) {
    $response->html = page_not_found("permission_denied");
} else {
    // default class_list
    $classes_param = (object) [
        "clientId" => $clientId,
        "columns" => "a.id, a.name"
    ];
    // if the class_id is not empty
    $classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    $start = 100;
    $from = $filter->percentage_from ?? null;
    $percentage_from = null;
    for($i = 0; $i <= 100; $i--) {
        $value = $start - $i;
        $percentage_from .= "<option ".($from == $value ? "selected" : null)." value='{$value}'>{$value}%</option>";
        $i += 11;
        if($value < 20) {
            break;
        }
    }

    $start = 100;
    $to = $filter->percentage_to ?? 100;
    $percentage_to = null;
    for($i = 0; $i <= 100; $i--) {
        $value = $start - $i;
        $percentage_to .= "<option ".($to == $value ? "selected" : null)." value='{$value}'>{$value}%</option>";
        $i += 11;
        if($value < 20) {
            break;
        }
    }

    // permission to receive payment
    $receivePayment = $accessObject->hasAccess("receive", "fees");

    // total breakdown
    $total_term_bill = 0;
    $total_term_bill_paid = 0;
    $total_term_bill_balance = 0;
    $total_term_bill_arrears = 0;
    $total_term_bill_total = 0;


    // set the user status
    $user_status = $filter->user_status ?? "Active";

    // set the parameters
    $param = (object) [
        "academics" => $defaultAcademics,
        "clientId" => $clientId, 
        "academic_year" => $defaultAcademics->academic_year ?? null,
        "academic_term" => $defaultAcademics->academic_term ?? null,
        "limit" => $myClass->global_limit,
        // "client_data" => $defaultClientData, 
        "class_id" => $filter->class_id ?? null,
        "user_status" => $user_status
    ];
    // create a new arrears object
    $arrearsObj = load_class("arrears", "controllers", $param);

    // get the list of all debtors
    $arrears_array = $arrearsObj->list($param);
    $students_ids = [];

    // initial value
    $count = 0;

    // if the fees arrears not empty
    if(!empty($arrears_array["data"])) {

        // loop through the arrears details
        foreach($arrears_array["data"] as $key => $student) {

            // append to the array list
            $students_ids[] = $student->_student_id;

            // if the total is more than 1
            if(($student->arrears_total > 0) || ($student->debt > 0)) {

                // append to the class summary information
                $total = $student->debt + $student->arrears_total;

                // calculate the percentage
                $percentage = $student->amount_paid > 0 ? round((100 - ($student->amount_paid / $student->term_bill) * 100), 2) : 100;

                // percentage range
                if(($percentage >= $from) && ($percentage <= $to)) {

                    // increment the total breakdown
                    $total_term_bill += $student->term_bill;
                    $total_term_bill_paid += $student->amount_paid;
                    $total_term_bill_balance += $student->debt;
                    $total_term_bill_arrears += $student->arrears_total;
                    $total_term_bill_total += $total; 

                    // set the button
                    $action = "";
                    if($receivePayment) {
                        $action = "<td align='center'>";
                        $action .= "<span title='Click to pay current fees balance' onclick='load(\"fees-payment?student_id={$student->student_id}&class_id={$student->class_id}\");' class='btn mb-1 btn-sm btn-outline-success'>Pay Fees</span>";

                        if($student->arrears_total > 0) {
                            $action .= "&nbsp;<span title='Click to pay previous fees arrears' onclick='load(\"arrears/{$student->student_id}\");' class='btn mb-1 btn-sm btn-outline-primary'>Pay Arrears</span>";
                        }
                        
                        $action .= "</td>";
                    }
                    $count++;

                    // append to the list
                    $class_debtors_list .= "
                    <tr>
                        <td>{$count}</td>
                        <td>
                            <div class='d-flex text-uppercase justify-content-start'>
                                <div>
                                    <span class='user_name' onclick='load(\"student/{$student->student_id}\");'>".strtoupper($student->student_info->name)."</span> <br>
                                    <strong>{$student->class_name}</strong><br>
                                    REG. ID: <strong>{$student->student_info->unique_id}</strong><br>
                                    ".$myClass->the_status_label($student->user_status)."
                                </div>
                            </div>
                        </td>
                        <td>{$defaultCurrency} {$student->term_bill}</td>
                        <td>{$defaultCurrency} {$student->amount_paid}</td>
                        <td>{$defaultCurrency} {$student->debt}</td>
                        <td>{$defaultCurrency} {$student->arrears_total}</td>
                        <td>{$defaultCurrency} {$total}</td>
                        <td align='center'>{$percentage}%</td>
                        {$action}
                    </tr>";

                }

            }

        }

    }

    // get the list of students who do not appear in the list
    $other_students = $myClass->pushQuery(
        "a.name, c.id AS class_id, c.name AS class_name, a.unique_id, a.image, a.item_id, a.user_status,
            (
                SELECT sum(b.amount_due) FROM fees_payments b 
                WHERE b.student_id = a.item_id AND b.academic_term = '{$defaultAcademics->academic_term}'
                    AND b.academic_year = '{$defaultAcademics->academic_year}' AND b.exempted='0'
            ) AS term_bill,
            (
                SELECT sum(b.amount_paid) FROM fees_payments b 
                WHERE b.student_id = a.item_id AND b.academic_term = '{$defaultAcademics->academic_term}'
                    AND b.academic_year = '{$defaultAcademics->academic_year}' AND b.exempted='0'
            ) AS amount_paid,
            (
                SELECT sum(b.balance) FROM fees_payments b 
                WHERE b.student_id = a.item_id AND b.academic_term = '{$defaultAcademics->academic_term}'
                    AND b.academic_year = '{$defaultAcademics->academic_year}' AND b.exempted='0'
            ) AS debt
        ", 
        "users a LEFT JOIN classes c ON c.id = a.class_id", 
        "a.client_id='{$clientId}' ".(!empty($students_ids) ? "AND a.id NOT IN {$myClass->inList($students_ids)}" : null)."
            ".(!empty($filter->class_id) ? " AND c.id IN {$myClass->inList($filter->class_id)}" : "")."
            AND a.user_type='student' AND a.status='1' AND a.user_status IN {$myClass->inList($user_status)}");

    // loop through the students list
    foreach($other_students as $student) {

        if($student->debt > 0) {
            // append to the class summary information
            $total = $student->debt;

            $percentage = $student->amount_paid > 0 ? round((100 - ($student->amount_paid / $student->term_bill) * 100), 2) : 100;

            // percentage range
            if(($percentage >= $from) && ($percentage <= $to)) {

                // increment the total breakdown
                $total_term_bill += $student->term_bill;
                $total_term_bill_paid += $student->amount_paid;
                $total_term_bill_balance += $student->debt;
                $total_term_bill_total += $total;
                
                // set the button
                $action = "";
                if($receivePayment) {
                    $action = "<td align='center'>";
                    $action .= "&nbsp;<button title='Click to pay current fees balance' onclick='load(\"fees-payment?student_id={$student->item_id}&class_id={$student->class_id}\");' class='btn btn-sm btn-outline-primary'>Pay Fees</button>";
                    $action .= "</td>";
                }
                
                $count++;
            
                // append to the list
                $class_debtors_list .= "
                <tr>
                    <td>{$count}</td>
                    <td>
                        <div class='d-flex text-uppercase justify-content-start'>
                            <div>
                                <span class='user_name' onclick='load(\"student/{$student->item_id}\");'>".strtoupper($student->name)."</span> <br>
                                <strong>{$student->class_name}</strong><br>
                                <strong>{$student->unique_id}</strong><br>
                                ".$myClass->the_status_label($student->user_status)."
                            </div>
                        </div>
                    </td>
                    <td>{$defaultCurrency} {$student->term_bill}</td>
                    <td>{$defaultCurrency} {$student->amount_paid}</td>
                    <td>{$defaultCurrency} {$student->debt}</td>
                    <td>{$defaultCurrency} 0</td>
                    <td>{$defaultCurrency} {$total}</td>
                    <td align='center'>{$percentage}%</td>
                    {$action}
                </tr>";
            }

        }
    }

    // if the class debtors list is not empty
    if(!empty($class_debtors_list)) {

        // calculate the percentage
        $percentage = $total_term_bill_paid > 0 ? round((100 - ($total_term_bill_paid / $total_term_bill) * 100), 2) : 100;
        
        // perpare the final row for summation
        $class_debtors_list .= "
            <tr class='font-bold font-14'>
                <td class='text-white'>".($count+1)."</td>
                <td>SUMMARY TOTAL</td>
                <td>{$defaultCurrency} ".number_format($total_term_bill, 2)."</td>
                <td>{$defaultCurrency} ".number_format($total_term_bill_paid, 2)."</td>
                <td>{$defaultCurrency} ".number_format($total_term_bill_balance, 2)."</td>
                <td>{$defaultCurrency} ".number_format($total_term_bill_arrears, 2)."</td>
                <td>{$defaultCurrency} ".number_format($total_term_bill_total, 2)."</td>
                <td align='center'>{$percentage}%</td>
                <td align='center'></td>
            </tr>";
    }

    $response->scripts = ["assets/js/debtors.js"];

    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-street-view"></i> Debtors List</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="'.$baseUrl.'fees-history">Fees Payments</a></div>
                    <div class="breadcrumb-item active">Debtors</div>
                </div>
            </div>
            <div class="row">';

            $response->html .= '
            
                <div class="col-lg-4 col-md-6 col-12 form-group mb-2">
                    <label>Multiple Select Class to Filter</label>
                    <select data-width="100%" multiple class="form-control selectpicker" name="class_id">';
                        if(!empty($class_list) && is_array($class_list)) {
                            foreach($class_list as $each) {
                                $response->html .= "<option ".(isset($filter->class_id) && (in_array($each->id, $filter->class_id)) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                            }
                        }
                        $response->html .= '
                    </select>
                </div>
                <div class="col-lg-2 col-sm-6 col-md-3 col-12 form-group mb-2">
                    <label>Debt Percentage From</label>
                    <select name="percentage_from" class="form-control">
                        '.$percentage_from.'
                        <option '.(empty($from) || ($from == 5) ? "selected" : null).' value="5">5%</option>
                        <option '.(empty($from) || ($from == 1) ? "selected" : null).' value="1">1%</option>
                        <option '.(($from == 0) ? "selected" : null).' value="0">0%</option>
                    </select>
                </div>
                <div class="col-lg-2 col-sm-6 col-md-3 col-12 form-group mb-2">
                    <label>To</label>
                    <select name="percentage_to" class="form-control">
                        '.$percentage_to.'
                        <option '.(($to == 5) ? "selected" : null).' value="5">5%</option>
                        <option '.(($to == 1) ? "selected" : null).' value="1">1%</option>
                        <option '.(($to == 0) ? "selected" : null).' value="0">0%</option>
                    </select>
                </div>
                <div class="col-lg-2 col-sm-6 col-md-3 col-12 form-group mb-2">
                    <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                    <button id="filter_Fees_Arrears" type="submit" class="btn btn-outline-warning height-40 btn-block"><i class="fa fa-filter"></i> FILTER</button>
                </div>
                <div class="col-lg-2 col-sm-6 col-md-3 col-12 form-group">
                    <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                    <button onclick="return load(\'arrears/add\');" class="btn height-40 btn-outline-primary btn-block">
                        <i class="fa fa-plus"></i> MODIFY RECORD
                    </button>
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

        $response->html .='
            <div class="col-12 mt-2 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Student Name / Class</th>
                                        <th>'.$academicSession.' Bill</th>
                                        <th>Total Paid</th>
                                        <th>'.$academicSession.' Arrears</th>
                                        <th>Previous Arrears</th>
                                        <th>Total Debt</th>
                                        <th align="center">Debt %</th>
                                        '.($class_debtors_list && $receivePayment ? "<th></th>" : null).'
                                    </tr>
                                </thead>
                                <tbody>'.$class_debtors_list.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>';

    $response->html .= '</div></section>';

}
// print out the response
echo json_encode($response);
?>