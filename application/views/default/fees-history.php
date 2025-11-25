<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $accessObject, $defaultUser, $defaultAcademics, $defaultCurrency, $isAdminAccountant, $isWardParent, $isParent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];

$response->title = "Fees Payment History ";
$response->scripts = ["assets/js/filters.js", "assets/js/accounting.js"];

// set the parent menu
$response->parent_menu = "fees-payment";

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
            SELECT SUM(arrears_total) 
            FROM users_arrears 
            WHERE client_id='{$clientId}' 
            ".(!empty($defaultUser->wards_list_ids) ? " AND student_id IN {$this->inList($defaultUser->wards_list_ids)}" : null)."
            LIMIT {$myClass->extreme_maximum}
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
    $total_arrears = $isParent && empty($defaultUser->wards_list_ids) ? 0 : ($payment_summary[0]->total_arrears ?? 0);
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

// if the user is not a parent
if(!$isWardParent) {

    // loop through the fees list
    foreach(($item_list["data"] ?? []) as $key => $fees) {

        // increment the values
        $fees_count += 1;
        $fees_paid += $fees->category_id !== "Arrears" ? $fees->amount : 0;
        $arrears_paid += $fees->category_id == "Arrears" ? $fees->amount : 0;

        $fees->amount_paid = $fees->amount_paid ?? $fees->amount;

        // set the action button
        $action = "";
        $action = "<a href='#' title='View Receipt' onclick='load(\"fees_view/{$fees->payment_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";
        $action .= "&nbsp;<a title='Print Receipt' target='_blank' href=\"{$baseUrl}receipt/{$fees->payment_id}\" class='btn btn-sm btn-outline-warning'><i class='fa fa-print'></i></a>";
        
        $fees->student_info->name = random_names($fees->student_info->name);
        
        // add the reversal button key
        if($hasReversal && $fees->has_reversal && !$fees->reversed) {
            $action .= "&nbsp;<a title='Reverse Fees Payment' href='#' onclick=\"reverse_payment('{$fees->payment_id}','{$fees->student_info->name}','{$fees->currency} ".number_format($fees->amount_paid, 2)."')\" class='btn btn-sm btn-outline-danger'><i class='fa fa-recycle'></i></a>";
        }

        if($fees->reversed) {
            $action = "<span class='badge font-bold badge-danger'>REVERSED</span>";
        }

        $useTheImage = strpos($fees->student_info->image, "assets/img/avatar.png") !== false;

        $fees_history .= "<tr data-row_id=\"{$fees->payment_id}\">";
        $fees_history .= "<td class='text-center'>{$fees_count}</td>";
        $fees_history .= "
            <td>
                <div class='d-flex text-uppercase justify-content-start space-x-2'>
                    ".(!$useTheImage ? "
                        <div class='mr-2'><img src='{$baseUrl}{$fees->student_info->image}' width='50px' height='50px'></div>" : "
                        <div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
                            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-user h-6 w-6 text-white' aria-hidden='true'><path d='M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2'></path><circle cx='12' cy='7' r='4'></circle></svg>
                        </div>"
                    )."
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
        $fees_history .= "<td class='text-center'><span data-action_id='{$fees->payment_id}'>{$action}</span></td>";
        $fees_history .= "</tr>";
    }
}

// if the user is a parent
if($isWardParent) {
    if(empty($item_list["data"])) {
        $simplified_fees_history = no_record_found("No Fees Payment Found", "No fees payment has been for any of your wards yet.", null, "Student", false, "fas fa-money-bill");
    } else {
        $simplified_fees_history = "";

        // loop through the fees list
        foreach(($item_list["data"] ?? []) as $key => $fees) {
            
            $fees_count += 1;
            $fees_paid += $fees->category_id !== "Arrears" ? $fees->amount : 0;
            $arrears_paid += $fees->category_id == "Arrears" ? $fees->amount : 0;
            
            $simplified_fees_history .= "
            <div class='flex items-center bg-white space-x-3 p-3 mb-2 border rounded-xl'>
                <i class='fas fa-check-circle text-green-500'></i>
                <div class='flex items-center justify-between w-100'>
                    <div class='flex-1'>
                        <div class='text-sm font-medium text-gray-900'>
                            <div class='font-15 font-bold'>{$fees->student_info->name}</div>
                            <div><strong>ID:</strong><strong class='text-success'> {$fees->receipt_id}</strong></div>
                            <div class='font-bold'>{$fees->currency} {$fees->amount_paid}</div>
                            <div><i class='fa fa-home'></i> {$fees->class_name}</div>
                        </div>
                        <p class='text-gray-600'><i class='fa fa-calendar'></i> {$fees->recorded_date}</p>
                        <div><span class='badge cursor badge-primary'>{$fees->category_name}</span></div>
                    </div>
                    <div>
                        <a href='#' title='View Receipt' onclick='load(\"fees_view/{$fees->payment_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i> View</a>
                    </div>
                </div>
            </div>";  

        }

    }
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
        <h1><i class="fa fa-money-bill"></i> Fees Payment History</h1>
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
        <div class="col-xl-3 col-md-3 form-group '.($isWardParent ? 'hidden' : '').'">
            <label>Select Class</label>
            <select data-width="100%" class="form-control selectpicker" name="class_id">
                <option value="">Please Select Class</option>';
                foreach($class_list as $each) {
                    $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                }
                $response->html .= '
            </select>
        </div>
        <div class="col-xl-3 col-md-3 form-group '.($isWardParent ? 'hidden' : '').'">
            <label>Select Category</label>
            <select data-width="100%" class="form-control selectpicker" name="category_id">
                <option value="">Please Select Category</option>';
                foreach($myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}'") as $each) {
                    $response->html .= "<option ".(isset($filter->category_id) && ($filter->category_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                }
            $response->html .= '
            </select>
        </div>
        <div class="col-xl-3 col-md-3 form-group '.($isWardParent ? 'hidden' : '').'">
            <label>Date Range</label>
            <input type="text" name="date_range" id="date_range" value="'.$date_range.'" class="form-control daterange">
        </div>
        <div class="col-xl-3 col-md-3 col-12 form-group '.($isWardParent ? 'hidden' : '').'">
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
        
        '.($feesReport || $isWardParent ?
        '<div class="'.($isWardParent ? 'col-xl-4 col-lg-4' : 'col-xl-3 col-lg-3').' col-md-6 hover:scale-105 transition-all duration-300">
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-blue">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">FEES PAID</h6>
                            <span class="font-bold text-primary font-20 mb-0">'.$defaultCurrency.' '.(!empty($fees_paid) ? number_format($fees_paid, 2) : 0).'</span>
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

        <div class="'.($isWardParent ? 'col-xl-4 col-lg-4' : 'col-xl-3 col-lg-3').' col-md-6 '.(empty($arrears_paid) && $isWardParent ? 'hidden' : '').' hover:scale-105 transition-all duration-300">
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-success">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">ARREARS PAID</h6>
                            <span class="font-bold text-success text-success font-20 mb-0">'.$defaultCurrency.' '.(!empty($arrears_paid) ? number_format($arrears_paid, 2) : 0).'</span>
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

        <div class="'.($isWardParent ? 'col-xl-4 col-lg-4' : 'col-xl-3 col-lg-3').' col-md-6 '.($isWardParent ? 'hidden' : '').' hover:scale-105 transition-all duration-300">
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-danger">
                <div class="card-body pr-2 pl-3 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">AVERAGE PAYMENT</h6>
                            <span class="font-bold text-danger font-20 mb-0">'.$defaultCurrency.' '.(!empty($average_fees_paid) ? number_format($average_fees_paid, 2) : 0).'</span>
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

        <div class="'.($isWardParent ? 'col-xl-4 col-lg-4' : 'col-xl-3 col-lg-3').' col-md-6 hover:scale-105 transition-all duration-300">
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-warning">
                <div class="card-body pr-2 pl-2 card-type-3">
                    <div class="row">
                        <div class="col">
                            <h6 class="font-14 text-uppercase font-bold mb-0">ARREARS</h6>
                            <span class="font-bold text-warning font-20 mb-0">'.$defaultCurrency.' '.(!empty($total_arrears) ? number_format($total_arrears, 2) : 0).'</span>
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
            '.($isWardParent ? $simplified_fees_history : '
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">ID</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Amount</th>
                                    <th width="12%">Fees Type</th>
                                    <th>Method</th>
                                    <th>Recorded By</th>
                                    <th align="center" width="13%"></th>
                                </tr>
                            </thead>
                            <tbody>'.$fees_history.'</tbody>
                        </table>
                    </div>
                </div>
            </div>').'
        </div>
    </div>
</section>';

// print out the response
echo json_encode($response);
?>