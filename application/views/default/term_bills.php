<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $session, $defaultAcademics, $academicSession, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Manage {$academicSession} Bills";
$response->title = $pageTitle;

// set the parent menu
$response->parent_menu = "fees-setup";

// if the filter is set
$filter = (object) array_map("xss_clean", $_POST);

// if the filter is set
$allocationTab = (bool) (!empty($filter->filter) && $filter->filter == "student");

// list the academic years
$academic_calendar_years = null;

// confirm if the user has the required permissions
if(!$accessObject->hasAccess("allocation", "fees")) {
    // permission denied
    $response->html = page_not_found("permission_denied");
} else {

    // current url
    $isFound = false;
    $currentUrl = $SITEURL[1] ?? null;
    $currentAccYearTerm = $SITEURL[2] ?? ($_GET['period'] ?? false);
    $termIsSet = (bool) (!empty($currentUrl) && in_array($currentUrl, ["set", "review"]));
    $isReview =(bool) (!empty($currentUrl) && $currentUrl !== "review");

    // load the academic terms
    $myClass->academic_terms();
    $_previous_academic_years = [];

    // check if the current academic year and term has been logged as completed
    $_academic_check = $myClass->pushQuery("academic_year, academic_term", "clients_terminal_log", "client_id='{$clientId}' LIMIT 50");

    // loop through the previous academic years and terms
    foreach($_academic_check as $_acc_years) {
        $_previous_academic_years[] = str_ireplace("/", ".", "{$_acc_years->academic_year}_{$_acc_years->academic_term}");
    }

    // if the admin has selected to manage an academic year 
    if($termIsSet) {
        // if the academic year and term is not set
        if(!$currentAccYearTerm) {
            // error found page
            $academic_calendar_years = "<div class='col-lg-12'>".page_not_found()."</div>";
        } else {
            // split the item
            $split = explode("_", $currentAccYearTerm);
            $_term = $split[1] ?? null;
            $_year = str_ireplace(".", "/", $split[0]);
            $setNewBill = (bool) ($SITEURL[1] == "set");

            // if the term is empty
            if(empty($_term)) {
                $academic_calendar_years = "<div class='col-lg-12'>".page_not_found()."</div>";
            } else {

                // set new variable
                $isFound = true;
                $class_allocation_list = '';
                $student_allocation_list = '';
                $pageTitle = strtoupper($academicSession)." BILL: ".(empty($_term) ? null : strtoupper($_term))." ".strtoupper($academicSession)." - {$_year}";

                // scripts for the page
                $response->scripts = ["assets/js/term_bills.js", "assets/js/filters.js"];

                // load the classes list
                $classes_param = (object) ["clientId" => $clientId, "columns" => "a.id, a.name, a.payment_module"];
                $class_list = load_class("classes", "controllers")->list($classes_param)["data"];
                
                // load fees allocation list for class
                $allocation_param = (object) [
                    "group_by_student" => "group_by", 
                    "clientId" => $clientId, 
                    "userData" => $defaultUser, 
                    "receivePayment" => false, 
                    "canAllocate" => true, 
                    "showPrintButton" => true, 
                    "currentTerm" => true,
                    "showOutstanding" => true,
                    "academic_year" => $_year, 
                    "academic_term" => $_term
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

                // get the category list
                $category_list = $myClass->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$clientId}'");

                // include the fees_helper file
                load_helpers(['fees_helper']);

                // info
                $info = "Use this form to assign fees to a class or to a particular student. Leave the student id field blank if you want to set for the entire class.";

                // call the class fees allocation form
                $class_fees_allocation_form = fees_allocation_form($info, $class_list, $category_list, $clientId, $_year, $_term, $isReview);

                // set the content to display
                $academic_calendar_years = '
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
                                <div class="tab-pane fade '.(!$allocationTab ? "show active" : null).'" id="allocation_form" role="tabpanel" aria-labelledby="allocation_form-tab2">
                                    '.$class_fees_allocation_form.'
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
                                    '.display_class_filter(true, $class_list, $currentAccYearTerm, $filter->class_id ?? 0).'
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
                </div>';

            }
        }
    } else {
        // curent academic year and term
        $current_yt =  str_ireplace("/", ".", "{$defaultAcademics->academic_year}_{$defaultAcademics->academic_term}");
        $next_yt =  str_ireplace("/", ".", "{$defaultAcademics->next_academic_year}_{$defaultAcademics->next_academic_term}");

        // loop through the academic years
        foreach($myClass->academic_calendar_years as $acc_year) {

            // loop through the academic terms
            foreach($myClass->school_academic_terms as $acc_term) {
                // this item
                $term_year = str_ireplace("/", ".", $acc_year)."_".$acc_term->name;
                $isCurrent = (bool) ($current_yt == $term_year);
                $_accepted_academic_terms[] = $term_year;


                
                // if the term_year in in the previous academic callendar year or the current academic calendar year
                // or is equal to the next academic year/term
                if(in_array($term_year, $_previous_academic_years) || in_array($term_year, [$current_yt, $next_yt])) {

                    // add the checked sign for the current academic year and term
                    $checked = $isCurrent ? "<span title='Current Academic Year & Term' class='checked'><i class='fa fa-check-circle'></i></span>" : null;
                    
                    // set the caption
                    $caption = in_array($term_year, $_previous_academic_years) ? 
                        "<a class='btn btn-warning btn-sm' href='{$baseUrl}term_bills/review?period={$term_year}'>Review</a>" : 
                        ($isCurrent ? "<a class='btn btn-success btn-sm' href='{$baseUrl}fees-allocation'><i class='fa fa-edit'></i> Manage</a>" :
                            "<a class='btn btn-primary btn-sm' href='{$baseUrl}term_bills/set?period={$term_year}'><i class='fa fa-ankh'></i> Allocate</a>"
                        );

                    // display the record
                    $academic_calendar_years .= "
                        <div class=\"col-12 col-sm-6 col-md-6 col-lg-4\">
                            <div class=\"card hover:scale-105 transition-all duration-300 border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid ".($isCurrent ? 
                                    "border-success" : (!in_array($term_year, $_previous_academic_years) ? "border-primary" : "border-warning")
                                )."\">
                                <div class=\"card-body pr-2 pl-3 card-type-3\">
                                    <div class=\"card-header font-20 ".($isCurrent ? "text-primary" : null)." p-2\">
                                        {$acc_year} - YEAR
                                        {$checked}
                                    </div>
                                    <div class=\"card-body p-2\">
                                        <div class='d-flex justify-content-between'>
                                            <div class='".($isCurrent ? "text-primary" : null)." font-20 text-uppercase'>{$acc_term->name} {$academicSession}</div>
                                            <div>{$caption}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                }
            }
        }
    }
    $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-bell"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    '.($isFound ? '<div class="breadcrumb-item active"><a href="'.$baseUrl.'term_bills">Manage '.$academicSession.' Bills</a></div>' : null).'
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="row mt-sm-4" id="filter_Department_Class">
                '.$academic_calendar_years.'
            </div>
        </section>';
}
// print out the response
echo json_encode($response);
?>