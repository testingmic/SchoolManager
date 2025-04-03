<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $SITEURL, $defaultAcademics;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Fees Allocation";
$response->title = $pageTitle;

// if the filter is set
$filter = (object) array_map("xss_clean", $_POST);

// fees allocation mandate
$canAllocate = $accessObject->hasAccess("allocation", "fees");
$receivePayment = $accessObject->hasAccess("receive", "fees");

// if the filter is set
$allocationTab = (bool) (!empty($filter->filter) && $filter->filter == "student");

/** confirm that the user has the permission to receive payment */
if(!$canAllocate) {
    $response->html = page_not_found("permission_denied");
} else {

    // scripts for the page
    $response->scripts = ["assets/js/filters.js", "assets/js/payments.js"];

    // load the classes list
    $classes_param = (object) ["limit" => $myClass->temporal_maximum, "clientId" => $clientId, "columns" => "a.id, a.name, a.payment_module"];
    $class_list = load_class("classes", "controllers")->list($classes_param)["data"];

    // get the category list
    $category_list = $myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}' LIMIT {$myClass->temporal_maximum}");

    // load fees allocation list for class
    $allocation_param = (object) [
        "limit" => $myClass->maximum_class_count,
        "group_by_student" => "group_by", "clientId" => $clientId, 
        "userData" => $defaultUser, "receivePayment" => $receivePayment, 
        "canAllocate" => $canAllocate, "showPrintButton" => true,
        "academic_year" => $defaultAcademics->academic_year ?? null, 
        "academic_term" => $defaultAcademics->academic_term ?? null
    ];
    $allocation_param->client_data = $defaultUser->client;
    
    // create a new object
    $feesObject = load_class("fees", "controllers", $allocation_param);

    // load the class allocation list    
    $class_allocation_list = $feesObject->class_allocation_array($allocation_param);

    // load fees allocation list for the students
    $allocation_param->class_id = $filter->class_id ?? 0;
    $student_allocation_list = $feesObject->student_allocation_array($allocation_param);

    // set the total due, paid and balance
    $totalDue = $feesObject->allocationSummary['totalDue'];
    $totalPaid = $feesObject->allocationSummary['totalPaid'];
    $totalBalance = $feesObject->allocationSummary['totalBalance'];

    // info
    $info = "Use this form to assign fees to a class or to a particular student. Leave the student id field blank if you want to set for the entire class.";

    // include the fees_helper file
    load_helpers(['fees_helper']);

    // call the class fees allocation form
    $class_fees_allocation_form = fees_allocation_form($info, $class_list, $category_list, $clientId);

    // append the html content
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'  - '.$allocation_param->academic_year.': '.$allocation_param->academic_term.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'fees-history">Fees Payment List</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row mt-sm-4" id="filter_Department_Class">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link '.(!$allocationTab ? "active" : null).'" id="allocation_form-tab2" data-toggle="tab" href="#allocation_form" role="tab" aria-selected="'.(!$allocationTab ? "true" : null).'">Bulk Fees Allocation</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="classes-tab2" data-toggle="tab" href="#classes" role="tab" aria-selected="true">Class Fees Allocation</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link '.($allocationTab ? "active" : null).'" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="'.($allocationTab ? "true" : null).'">Student Fees Allocation</a>
                                </li>
                            </ul>

                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade '.(!$allocationTab ? "show active" : null).'" id="allocation_form" role="tabpanel" aria-labelledby="allocation_form-tab2">';
                                
                                // display class not set error
                                if(empty($class_list)) {
                                    // no class added error
                                    $response->html .= notification_modal("Class Not Set", $myClass->error_logs["class_not_set"]["msg"], $myClass->error_logs["class_not_set"]["link"]);
                                } elseif(empty($category_list)) {
                                    // fees category not added error
                                    $response->html .= notification_modal("Fees Category Not Set", $myClass->error_logs["fees_category_not_set"]["msg"], $myClass->error_logs["fees_category_not_set"]["link"]);
                                } else {
                                    $response->html .= '
                                        '.$class_fees_allocation_form.'
                                    ';
                                }
                            $response->html .= '
                                </div>
                                <div class="tab-pane fade" id="classes" role="tabpanel" aria-labelledby="classes-tab2">
                                    <div class="table-responsive">
                                        <table data-empty="" class="table table-bordered table-sm table-striped raw_datatable">
                                            <thead>
                                                <tr>
                                                    <th width="7%" class="text-center">#</th>
                                                    <th>Class</th>
                                                    <th>Fees Category</th>
                                                    <th>Amount</th>
                                                    <th align="center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>'.$class_allocation_list.'</tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade '.($allocationTab ? "show active" : null).'" id="students" role="tabpanel" aria-labelledby="students-tab2">
                                    '.display_class_filter(false, $class_list, $defaultAcademics->academic_year ?? null, $filter->class_id ?? 0).'
                                    '.fees_allocation_summary($totalDue, $totalPaid, $totalBalance, $defaultCurrency).'
                                    <div class="table-responsive">
                                        <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                            <thead>
                                                <tr>
                                                    <th width="7%" class="text-center">#</th>
                                                    <th>Student Name</th>
                                                    <th>Due</th>
                                                    <th>Paid</th>
                                                    <th width="12%">Balance</th>
                                                    <th align="center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>'.$student_allocation_list.'</tbody>
                                        </table>
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

// print out the response
echo json_encode($response);
?>