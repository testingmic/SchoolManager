<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser, $defaultAcademics, $defaultCurrency;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];

$response->title = "Fees Payment History : {$appName}";
$response->scripts = ["assets/js/filters.js", "assets/js/reversals.js"];

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
$param = (object) [
    "clientId" => $clientId,
    "userData" => $defaultUser,
    "academic_year" => $defaultAcademics->academic_year ?? null,
    "academic_term" => $defaultAcademics->academic_term ?? null,
    "client_data" => $defaultUser->client,
    "student_array_ids" => $defaultUser->wards_list_ids ?? null,
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "category_id" => $filter->category_id ?? null,
    "group_by" => "GROUP BY a.payment_id"
];

// if the student id is not empty
if(!empty($session->student_id)) {
    $param->student_id = $session->student_id;
}

// load the student fees payment
$item_list = load_class("fees", "controllers", $param)->list($param);

$hasAdd = $accessObject->hasAccess("add", "fees");
$hasUpdate = $accessObject->hasAccess("update", "fees");
$hasReversal = $accessObject->hasAccess("reversal", "fees");

// initial variables
$payment_summary = $myClass->pushQuery("
        SUM(amount_due) AS amount_due,
        (
            SELECT SUM(b.amount) FROM fees_collection b WHERE b.client_id='{$clientId}'
            ".(!empty($param->academic_year) ? " AND b.academic_year='{$param->academic_year}'" : null)."
            ".(!empty($param->academic_term) ? " AND b.academic_term='{$param->academic_term}'" : null)."
            AND b.status = '1' AND b.reversed = '0'
        ) AS amount_paid,
        SUM(balance) AS total_balance,
        (
            SELECT SUM(arrears_total) FROM users_arrears WHERE client_id='{$clientId}'
        ) AS total_arrears", 
        "fees_payments", 
        "client_id = '{$clientId}' AND status = '1'
    ".(!empty($param->academic_year) ? " AND academic_year='{$param->academic_year}'" : null)."
    ".(!empty($param->academic_term) ? " AND academic_term='{$param->academic_term}'" : null)."
    ".(!empty($param->class_id) ? " AND class_id='{$param->class_id}'" : null)."
");

$amount_due = $payment_summary[0]->amount_due ?? 0;
$amount_paid = $payment_summary[0]->amount_paid ?? 0;
$total_balance = $payment_summary[0]->total_balance ?? 0;
$total_arrears = $payment_summary[0]->total_arrears ?? 0;

$fees_history = "";

// loop through the fees list
foreach($item_list["data"] as $key => $fees) {

    // set the action button
    $action = "";
    $action = "<a href='#' title='View Receipt' onclick='load(\"fees_view/{$fees->payment_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";
    $action .= "&nbsp;<a title='Print Receipt' href='#' onclick=\"print_receipt('{$fees->payment_id}')\" class='btn btn-sm btn-outline-warning'><i class='fa fa-print'></i></a>";
    
    // add the reversal button key
    if($hasReversal && $fees->has_reversal && !$fees->reversed) {
        $action .= "&nbsp;<a title='Reverse Fees Payment' href='#' onclick=\"reverse_payment('{$fees->payment_id}','{$fees->student_info->name}','{$fees->currency} ".number_format($fees->amount_paid, 2)."')\" class='btn btn-sm btn-outline-danger'><i class='fa fa-recycle'></i></a>";
    }

    if($fees->reversed) {
        $action = "<span class='badge font-bold badge-danger'>REVERSED</span>";
    }

    $fees_history .= "<tr data-row_id=\"{$fees->payment_id}\">";
    $fees_history .= "<td>".($key+1)."</td>";
    $fees_history .= "
        <td>
            <div class='d-flex text-uppercase justify-content-start'>
                ".(!empty($fees->student_info->image) ? "
                <div class='mr-2'><img src='{$baseUrl}{$fees->student_info->image}' width='40px' height='40px'></div>" : "")."
                <div>
                    <a href='#' onclick='load(\"student/{$fees->student_info->user_id}\");'>{$fees->student_info->name}</a> <br>
                    <strong>ID: </strong>
                    <strong class='text-success'>{$fees->receipt_id}</strong>
                </div>
            </div>
        </td>";
    $fees_history .= "<td>{$fees->class_name}</td>";
    $fees_history .= "<td>{$fees->currency} ".number_format($fees->amount_paid, 2)."</td><td>";
    $fees_history .= "<strong>{$fees->payment_method}</strong>";

    // if the payment method was a cheque
    if($fees->payment_method === "Cheque") {
        $cheque_bank = explode("::", $fees->cheque_bank)[0];
        $fees_history .= $cheque_bank ? "<br><strong>{$cheque_bank}</strong>" : null;
        $fees_history .= $fees->cheque_number ? "<br><strong>#{$fees->cheque_number}</strong>" : null;
    }
    $fees_history .= "</td><td> ".(isset($fees->created_by_info->name) ? "{$fees->created_by_info->name} <br>" : null)."  <i class='fa fa-calendar'></i> {$fees->recorded_date}</td>";
    $fees_history .= "<td align='center'><span data-action_id='{$fees->payment_id}'>{$action}</span></td>";
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
            <label class="d-sm-none d-md-block" for="">&nbsp;</label>
            <button id="filter_Fees_Collection" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">TOTAL FEES DUE</h6>
                            <span class="font-bold text-primary font-20 mb-0">'.$defaultCurrency.' '.number_format($amount_due, 2).'</span>
                        </div>
                        <div class="col-auto">
                            <div class="bg-info text-white card-circle">
                                <i class="fas fa-money-bill-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">FEES + ARREARS PAID</h6>
                            <span class="font-bold text-success text-success font-20 mb-0">'.$defaultCurrency.' '.number_format($amount_paid, 2).'</span>
                        </div>
                        <div class="col-auto">
                            <div class="bg-success text-white card-circle">
                                <i class="fas fa-money-bill-wave-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">FEES OUTSTANDING</h6>
                            <span class="font-bold text-danger font-20 mb-0">'.$defaultCurrency.' '.number_format($total_balance, 2).'</span>
                        </div>
                        <div class="col-auto">
                            <div class="bg-danger text-white card-circle">
                                <i class="fas fa-money-bill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">ARREARS OUTSTANDING</h6>
                            <span class="font-bold text-warning font-20 mb-0">'.$defaultCurrency.' '.number_format($total_arrears, 2).'</span>
                        </div>
                        <div class="col-auto">
                            <div class="bg-amber text-white card-circle">
                                <i class="fas fa-money-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Recorded By</th>
                                    <th align="center" width="13%"></th>
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