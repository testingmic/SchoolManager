<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser, $defaultAcademics, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];

$response->title = "Fees Payment History ";
$response->scripts = ["assets/js/filters.js", "assets/js/accounting.js"];

$filter = (object) array_map("xss_clean", $_POST);

$userId = $session->userId;

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "a.id, a.name"
];

// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$date_range = $filter->date_range ?? date("Y-m-d", strtotime("-1 month")).":".date("Y-m-d");

// begin the request parameter
$param = (object) [
    "clientId" => $clientId,
    "userData" => $defaultUser,
    "academic_year" => $filter->academic_year ?? null,
    "academic_term" => $filter->academic_term ?? null,
    "client_data" => $defaultUser->client,
    "date_range" => $date_range,
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

$hasReversal = $accessObject->hasAccess("reversal", "fees");
$feesReport = $accessObject->hasAccess("reports", "fees");

// if the user has permission to view fees history
if($feesReport) {

    // initial variables
    $payment_summary = $myClass->pushQuery("
        SUM(amount_due) AS amount_due, SUM(balance) AS balance,
        (
            SELECT SUM(b.amount) FROM fees_collection b WHERE b.client_id='{$clientId}'
            ".(!empty($param->academic_year) ? " AND b.academic_year='{$param->academic_year}'" : null)."
            ".(!empty($param->academic_term) ? " AND b.academic_term='{$param->academic_term}'" : null)."
            AND b.status = '1' AND b.reversed = '0'
        ) AS amount_paid,
        (
            SELECT SUM(arrears_total) FROM users_arrears WHERE client_id='{$clientId}' LIMIT {$myClass->extreme_maximum}
        ) AS total_arrears", 
        "fees_payments a LEFT JOIN users u ON u.item_id = a.student_id", 
        "a.client_id = '{$clientId}' AND a.status = '1' AND a.exempted = '0' AND u.status='1'
        AND u.user_status IN ({$myClass->default_allowed_status_users_list})
        ".(!empty($param->academic_year) ? " AND a.academic_year='{$param->academic_year}'" : null)."
        ".(!empty($param->academic_term) ? " AND a.academic_term='{$param->academic_term}'" : null)."
        ".(!empty($param->class_id) && !is_array($param->class_id) ? " AND a.class_id='{$param->class_id}'" : null)."
        LIMIT {$myClass->extreme_maximum}
    ");

    // set the variables
    $amount_due = $payment_summary[0]->amount_due ?? 0;
    $amount_paid = $payment_summary[0]->amount_paid ?? 0;
    $total_arrears = $payment_summary[0]->total_arrears ?? 0;
    $total_balance = $payment_summary[0]->balance ?? 0;

    $response->page_programming["left"] = [
        "Fees Due" => number_format($amount_due, 2),
        "Term Fees Paid" => number_format($amount_paid, 2),
        "Fees Balance" => number_format($total_balance, 2)
    ];
}

$fees_history = "";
$fees_paid = 0;
$arrears_paid = 0;
$fees_count = 0;

// loop through the fees list
foreach($item_list["data"] as $key => $fees) {

    // increment the values
    $fees_count += 1;
    $fees_paid += $fees->category_id !== "Arrears" ? $fees->amount : 0;
    $arrears_paid += $fees->category_id == "Arrears" ? $fees->amount : 0;

    // set the action button
    $action = "";
    $action = "<a href='#' title='View Receipt' onclick='load(\"fees_view/{$fees->payment_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";
    $action .= "&nbsp;<a title='Print Receipt' target='_blank' href=\"{$baseUrl}receipt/{$fees->payment_id}\" class='btn btn-sm btn-outline-warning'><i class='fa fa-print'></i></a>";
    
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
                    <a href='#' class='user_name' onclick='load(\"student/{$fees->student_info->user_id}\");'>{$fees->student_info->name}</a> <br>
                    <strong>ID:</strong>
                    <strong class='text-success'>{$fees->receipt_id}</strong>
                </div>
            </div>
        </td>";
    $fees_history .= "<td>{$fees->class_name}</td>";
    $fees_history .= "<td>{$fees->currency} ".number_format($fees->amount_paid, 2)."</td>";
    $fees_history .= "<td>".($fees->category_name ? $fees->category_name : $fees->category_id)."</td>";
    $fees_history .= "<td><strong>{$fees->payment_method}</strong>";

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

// get the average fees paid
$average_fees_paid = $fees_count > 0 ? (($fees_paid + $arrears_paid) / $fees_count) : 0;

// if the user is not an admin or accountant
if(!$isAdminAccountant) {
    // unset the page additional information
    $response->page_programming = [];
}

// display the page content
$response->html = '
<section class="section">
    <div class="section-header">
        <h1>Fees Payment History</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item">Fees Payment History</div>
        </div>
    </div>';

    // if the term has ended
    if($isAdminAccountant) {
        $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
    }

    $response->html .= '<div class="row print_Fees_Collection" id="filter_Department_Class">
        <div class="col-xl-3 col-md-3 form-group">
            <label>Select Class</label>
            <select data-width="100%" class="form-control selectpicker" name="class_id">
                <option value="">Please Select Class</option>';
                foreach($class_list as $each) {
                    $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                }
                $response->html .= '
            </select>
        </div>
        <div class="col-xl-3 col-md-3 form-group">
            <label>Select Category</label>
            <select data-width="100%" class="form-control selectpicker" name="category_id">
                <option value="">Please Select Category</option>';
                foreach($myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}'") as $each) {
                    $response->html .= "<option ".(isset($filter->category_id) && ($filter->category_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                }
            $response->html .= '
            </select>
        </div>
        <div class="col-xl-3 col-md-3 form-group">
            <label>Date Range</label>
            <input type="text" name="date_range" id="date_range" value="'.$date_range.'" class="form-control daterange">
        </div>
        <div class="col-xl-3 col-md-3 col-12 form-group">
            <div class="d-flex justify-content-between">
                <div class="col-">
                    <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                    <button id="filter_Fees_Collection" class="btn height-40 btn-outline-warning">
                        <i class="fa fa-filter"></i> FILTER
                    </button>
                </div>
                <div class="col-">
                    <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                    <button id="print_Fees_Collection" class="btn height-40 btn-outline-primary">
                        <i class="fa fa-print"></i> PRINT
                    </button>
                </div>
            </div>
        </div>
        
        '.($feesReport ?
        '<div class="col-xl-3 col-lg-3 col-md-6">
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-blue">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">FEES PAID</h6>
                            <span class="font-bold text-primary font-20 mb-0">'.$defaultCurrency.' '.number_format($fees_paid, 2).'</span>
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
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-success">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">ARREARS PAID</h6>
                            <span class="font-bold text-success text-success font-20 mb-0">'.$defaultCurrency.' '.number_format($arrears_paid, 2).'</span>
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
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-danger">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">AVERAGE PAYMENT</h6>
                            <span class="font-bold text-danger font-20 mb-0">'.$defaultCurrency.' '.number_format($average_fees_paid, 2).'</span>
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
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-warning">
                <div class="card-body pr-2 pl-2 card-type-3">
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
        </div>' : null).'

        <div class="col-12 col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Amount</th>
                                    <th>Fees Type</th>
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