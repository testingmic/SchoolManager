<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Section Information";
$response->title = $pageTitle;

$response->scripts = [
    "assets/js/index.js"
];

// item id
$item_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "section_id" => $item_id,
        "limit" => 1
    ];

    $data = load_class("sections", "controllers")->list($item_param);
   
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $amount_paid = 0;
        $amount_due = 0;
        $balance = 0;
        $arrears = 0;

        // set the url
        $url_link = $SITEURL[2] ?? null;

        // set the first key
        $data = $data["data"][0];

        // set the page title
        $response->title = $data->name;

        // guardian information
        $the_form = load_class("forms", "controllers")->section_form($clientId, $baseUrl, $data);
        $hasUpdate = $accessObject->hasAccess("update", "section");
        $viewAllocation = $accessObject->hasAccess("view_allocation", "fees");
        $receivePayment = $accessObject->hasAccess("receive", "fees");

        // load the section students list
        $student_param = (object) ["clientId" => $clientId, "section_id" => $item_id, "user_type" => "student"];
        $student_list = load_class("users", "controllers")->quick_list($student_param);

        // student update permissions
        $students = "";
        $count = 0;
        $studentUpdate = $accessObject->hasAccess("update", "student");

        // set the user_id id in the console
        $response->array_stream['url_link'] = "section/{$item_id}/";

        // loop through the students list
        foreach($student_list["data"] as $student) {

            // if the user has the permission to view fees allocation
            if($viewAllocation) {
                // add up the values
                $amount_due += $student->debt + $student->amount_paid;
                $amount_paid += $student->amount_paid;
                $arrears += $student->arrears;
                $balance += $student->debt;
            }

            // view link
            $count++;
            $action = "<button title='View Student Record' onclick='return load(\"student/{$student->user_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></button>";

            // show the payment button if the user has the permission to receive fees payment
            if($receivePayment && $student->debt > 0) {
                $action .= "&nbsp;<button title='Pay Fees' onclick='return load(\"fees-payment?student_id={$student->user_id}&class_id={$student->class_id}\");' class='btn btn-sm btn-outline-success'><i class='fa fa-adjust'></i> Pay Fees</button>";
            }
            
            $students .= "<tr data-row_id=\"{$student->user_id}\">";
            $students .= "<td>
                <div class='d-flex justify-content-start'>
                    <div>
                        <a href=\"#\" onclick='return load(\"student/{$student->user_id}\");'>
                            <span class='text-uppercase text-primary'>{$student->name}</span>
                        </a>
                    </div>
                </div>
            </td>";
            $students .= "<td>{$student->class_name}</td>";
            $students .= "<td>{$student->gender}</td>";
            $students .= $viewAllocation ? "<td>{$defaultCurrency} {$student->total_debt_formated}</td>" : null;
            $students .= "<td align='center' width='22%'>{$action}</td>";
            $students .= "</tr>";
        }

        // student listing
        $student_listing = '
        <div class="table-responsive table-student_staff_list">
            <table data-empty="" class="table table-sm table-bordered table-striped raw_datatable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Gender</th>
                        '.($viewAllocation ? "<th>DEBT</th>" : null).'
                        <th width="12%"></th>
                    </tr>
                </thead>
                <tbody>'.$students.'</tbody>
            </table>
        </div>';

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-school"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'sections">Sections</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row">

            <div class="col-md-3">
                <div class="card rounded-2xl">
                    <div class="card-body rounded-2xl card-body pt-3 pb-3 pr-2 pl-2 text-center bg-gradient-to-br from-blue-300 via-blue-200 to-blue-100 shadow-lg text-white">
                        <div class="font-23 text-black">SECTION CODE</div>
                        <div class="font-18 font-weight-bold text-uppercase text-black">'.$data->section_code.'</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 hover:scale-105 transition-all duration-300">
                <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-green border-left-solid">
                    <div class="card-body card-type-3">
                        <div class="row">
                            <div class="col">
                                <h6 class="font-14 text-uppercase font-weight-bold mb-0">STUDENTS</h6>
                                <h2 class="mb-0">'.$data->students_count.'</h2>
                            </div>
                            <div class="col-auto">
                                <div class="card-circle l-bg-green text-white">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 hover:scale-105 transition-all duration-300">
                <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-blue border-left-solid">
                    <div class="card-body card-type-3">
                        <div class="row">
                            <div class="col">
                                <h6 class="font-14 text-uppercase font-weight-bold mb-0">BOYS</h6>
                                <h2 class="mb-0">'.$data->students_male_count.'</h2>
                            </div>
                            <div class="col-auto">
                                <div class="card-circle l-bg-cyan text-white">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 hover:scale-105 transition-all duration-300">
                <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-purple border-left-solid">
                    <div class="card-body card-type-3">
                        <div class="row">
                            <div class="col">
                                <h6 class="font-14 text-uppercase font-weight-bold mb-0">GIRLS</h6>
                                <h2 class="mb-0">'.$data->students_female_count.'</h2>
                            </div>
                            <div class="col-auto">
                                <div class="card-circle l-bg-purple-dark text-white">
                                    <i class="fas fa-user-nurse"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center flex justify-center">
                        <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                    </div>
                    <div>
                        <div class="clearfix"></div>
                        <div class="author-box-center mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                    </div>
                </div>
                </div>
                '.(!empty($data->description) ? 
                    '<div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">DESCRIPTION</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="py-2">
                                '.$data->description.'
                            </div>
                        </div>
                    </div>' : null
                ).'
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">SECTION LEADER DETAILS</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-2">
                            <p class="clearfix">
                                <span class="float-left">Fullname</span>
                                <span class="float-right text-muted">'.($data->section_leader_info->name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Email</span>
                                <span class="float-right text-muted">'.($data->section_leader_info->email ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Contact</span>
                                <span class="float-right text-muted">'.($data->section_leader_info->phone_number ?? null).'</span>
                            </p>
                        </div>
                    </div>
                </div>
                '.($viewAllocation ?
                    '<div class="card">
                        <div class="card-header pr-3">
                            <div class="d-flex width-per-100 justify-content-between">
                                <div><h4 class="mb-0">FINANCES</h4></div>
                            </div>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3 border-bottom pb-2">
                                        <div class="col">
                                            <h6 class="font-14 text-uppercase mb-0">TOTAL FEES DUE</h6>
                                            <span class="text-primary font-20 mb-0">'.$defaultCurrency.' '.number_format($amount_due, 2).'</span>
                                        </div>
                                    </div>
                                    <div class="mb-3 border-bottom pb-2">
                                        <div class="col">
                                            <h6 class="font-14 text-uppercase mb-0">TOTAL FEES PAID</h6>
                                            <span class="text-primary font-20 mb-0">'.$defaultCurrency.' '.number_format($amount_paid, 2).'</span>
                                        </div>
                                    </div>
                                    <div class="mb-3 border-bottom pb-2">
                                        <div class="col">
                                            <h6 class="font-14 text-uppercase mb-0">TOTAL BALANCE</h6>
                                            <span class="text-primary font-20 mb-0">'.$defaultCurrency.' '.number_format($balance, 2).'</span>
                                        </div>
                                    </div>
                                    <div class="mb-2 pb-0">
                                        <div class="col">
                                            <h6 class="font-14 text-uppercase mb-0">OUTSTANDING FEES ARREARS</h6>
                                            <span class="text-primary font-20 mb-0">'.$defaultCurrency.' '.number_format($arrears, 2).'</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>' : null
                ).'
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(empty($url_link) || $url_link === "students" ? "active" : null).'" onclick="return appendToUrl(\'students\')" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">Student List</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($url_link === "update" ? "active" : null).'" id="profile-tab2" onclick="return appendToUrl(\'update\')" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(empty($url_link) || $url_link === "students" ? "show active" : null).'" id="students" role="tabpanel" aria-labelledby="students-tab2">
                            '.$student_listing.'
                        </div>
                        <div class="tab-pane fade '.($url_link === "update" ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
                        if($hasUpdate) {
                            $response->html .= $the_form;
                        }

                        $response->html .= '
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