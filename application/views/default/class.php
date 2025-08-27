<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $defaultCurrency;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Class Information";
$response->title = $pageTitle;


// item id
$item_id = $SITEURL[1] ?? null;

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "class_id" => $item_id,
        "load_courses" => true,
        "client_data" => $defaultUser->client,
        "limit" => 1
    ];

    $data = load_class("classes", "controllers", $item_param)->list($item_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the url
        $url_link = $SITEURL[2] ?? null;

        // set the first key
        $amount_paid = 0;
        $amount_due = 0;
        $balance = 0;
        $arrears = 0;

        $timetable = "";
        $data = $data["data"][0];

        // set the page title
        $response->title = $data->name;

        // set the graduation year
        $isGraduationYear = (bool) ($data->is_graduation_level == "Yes");

        // set the user_id id in the console
        $response->array_stream['url_link'] = "class/{$item_id}/";

        // guardian information
        $the_form = load_class("forms", "controllers")->class_form($clientId, $baseUrl, $data);
        $hasUpdate = $accessObject->hasAccess("update", "class");
        $viewAllocation = $accessObject->hasAccess("view_allocation", "fees");
        $receivePayment = $accessObject->hasAccess("receive", "fees");

        // load the section students list
        $student_param = (object) ["clientId" => $clientId, "client_data" => $defaultUser->client, "class_id" => $data->id, "user_type" => "student", "bypass" => true];
        $student_list = load_class("users", "controllers", $student_param)->quick_list($student_param);

        // student update permissions
        $students = "";
        $studentUpdate = $accessObject->hasAccess("update", "student");

        // load the class timetable
        $timetable = load_class("timetable", "controllers", $item_param)->class_timetable($data->item_id, $clientId);
        $count = 0;

        $t_status = "";

        // loop through the students list
        foreach($student_list["data"] as $key => $student) {

            // if the user has the permission to view fees allocation
            if($viewAllocation) {
                // set the debt
                $debt = $student->debt ?? 0;

                // add up the values
                $amount_due += $debt + $student->amount_paid;
                $amount_paid += $student->amount_paid ?? 0;
                $arrears += $student->arrears ?? 0;
                $balance += $debt;
            }

            $debt_formated = $student->total_debt_formated ?? 0;

            // view link
            $count++;
            $action = "<button title='View Student Record' onclick='return load(\"student/{$student->user_id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></button>";

            // set the scholarship status
            $scholarship_status = $student->scholarship_status == 1 ? "<div><span class='badge p-1 badge-success'>Full Scholarship</span></div>" : null;

            // show the payment button if the user has the permission to receive fees payment
            if($receivePayment && $student->debt > 0) {
                $action .= "&nbsp;<button title='Pay Fees' onclick='return load(\"fees-payment?student_id={$student->user_id}&class_id={$student->class_id}\");' class='btn btn-sm btn-outline-success'><i class='fa fa-adjust'></i> Pay Fees</button>";
            }

            $imageToUse = "<img src=\"{$baseUrl}{$student->image}\" class='rounded-2xl cursor author-box-picture' width='50px' height='50px'>";
			if($student->image == "assets/img/avatar.png") {
				$imageToUse = "
				<div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-user h-6 w-6 text-white' aria-hidden='true'><path d='M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2'></path><circle cx='12' cy='7' r='4'></circle></svg>
                </div>";
			}

            $students .= "<tr data-row_id=\"{$student->user_id}\">";
            $students .= "<td>
                <div class='flex items-center space-x-4'>
                    {$imageToUse}
                    <div>
                        <span title='View Details' class='user_name' onclick='load(\"student/{$student->user_id}\");'>{$student->name}</span>
                        <br>{$student->unique_id}{$t_status}{$scholarship_status}
                    </div>
                </div>
            </td>";
            $students .= "<td>{$student->gender}</td>";
            $students .= $viewAllocation ? "<td>{$defaultCurrency} {$debt_formated}</td>" : null;
            $students .= "<td align='center' width='24%'>{$action}</td>";
            $students .= "</tr>";
        }

        // student listing
        $student_listing = '
        <div class="table-responsive table-student_staff_list">
            <table data-empty="" class="table table-sm table-bordered table-striped raw_datatable">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>GENDER</th>
                        '.($viewAllocation ? "<th>DEBT</th>" : null).'
                        <th width="24%"></th>
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
                <h1><i class="fa fa-house-damage"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'classes">Classes</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-lg-3 col-md-6">
                <div class="card rounded-2xl">
                    <div class="card-body pt-3 pb-3 pr-2 pl-2 text-center bg-gradient-to-br from-blue-200 to-blue-100 rounded-2xl shadow-lg text-white card-type-3">
                        <div class="font-22 text-black">'.$data->name.'</div>
                        <div class="font-18 font-weight-bold text-uppercase text-black">'.$data->class_code.'</div>
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
                <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-danger border-left-solid">
                    <div class="card-body card-type-3">
                        <div class="row">
                            <div class="col">
                                <h6 class="font-14 text-uppercase font-weight-bold mb-0">GIRLS</h6>
                                <h2 class="mb-0">'.$data->students_female_count.'</h2>
                            </div>
                            <div class="col-auto">
                                <div class="card-circle l-bg-red text-white">
                                    <i class="fas fa-user-nurse"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                '.(!empty($data->description) ? 
                    '<div class="card">
                        <div class="card-header">
                            <h4>DESCRIPTION</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="pt-3">
                                '.$data->description.'
                            </div>
                        </div>
                    </div>' : null
                ).'
                <div class="card pb-0 stick_to_top">
                    <div class="card-header">
                        <h4 class="mb-0">CLASS TEACHER</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                    '.(empty($data->class_teacher_info) ? '<div class="py-4 pt-0 text-center">No Class Teacher Set</div>' : 
                        '<div class="pb-0 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Fullname</span>
                                <span class="float-right text-muted">'.(
                                    !empty($data->class_teacher_info->name) ? ucwords($data->class_teacher_info->name) : null
                                ).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Email</span>
                                <span class="float-right text-muted">'.($data->class_teacher_info->email ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Contact</span>
                                <span class="float-right text-muted">'.($data->class_teacher_info->phone_number ?? null).'</span>
                            </p>
                        </div>' ).'
                    </div>
                </div>
                <div class="card pb-0 stick_to_top d-none d-sm-block">
                    <div class="card-header">
                        <h4 class="mb-0">CLASS PREFECT</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                    '.(empty($data->class_assistant_info) ? '<div class="py-4 pt-0 text-center">No Class Prefect Set</div>' : 
                        '<div class="pt-3">
                            <p class="clearfix">
                                <span class="float-left">Fullname</span>
                                <span class="float-right text-muted">'.($data->class_assistant_info->name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Email</span>
                                <span class="float-right text-muted">'.($data->class_assistant_info->email ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Contact</span>
                                <span class="float-right text-muted">'.($data->class_assistant_info->phone_number ?? null).'</span>
                            </p>
                        </div>
                        ' ).' 
                    </div>
                </div>
                '.($viewAllocation ?
                    '<div class="card stick_to_top">
                        <div class="card-header pr-3">
                            <div class="d-flex width-per-100 justify-content-between">
                                <div><h4>FINANCES</h4></div>
                                <div><a title="Print entire Class Bill" target="_blank" class="btn btn-outline-primary" href="'.$baseUrl.'download/student_bill?class_id='.$item_id.'&isPDF=true"><i class="fa fa-print"></i> Print Class Bill</a></div>
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
                <div class="card stick_to_top">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(empty($url_link) || $url_link === "students" ? "active" : null).'" onclick="return appendToUrl(\'students\')" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">Student List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link '.($url_link === "courses" ? "active" : null).'" onclick="return appendToUrl(\'courses\')" id="courses-tab2" data-toggle="tab" href="#courses" role="tab" aria-selected="true">Subjects List</a>
                    </li>
                    <li class="nav-item '.(!in_array("timetable", $clientFeatures) ? "hidden" : null).'">
                        <a class="nav-link '.($url_link === "timetable" ? "active" : null).'" onclick="return appendToUrl(\'timetable\')" id="calendar-tab2" data-toggle="tab" href="#calendar" role="tab"
                        aria-selected="true">Timetable</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem || $url_link === "update" ? "active" : null).'" id="profile-tab2" onclick="return appendToUrl(\'update\')" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(empty($url_link) || $url_link === "students" ? "show active" : null).'" id="students" role="tabpanel" aria-labelledby="students-tab2">
                            '.$student_listing.'
                        </div>
                        <div class="tab-pane fade '.($url_link === "timetable" ? "show active" : null).'" id="calendar" role="tabpanel" aria-labelledby="calendar-tab2">
                            <div class="table-responsive trix-slim-scroll">
                                '.$timetable.'
                            </div>
                        </div>
                        <div class="tab-pane fade '.($url_link === "courses" ? "show active" : null).'" id="courses" role="tabpanel" aria-labelledby="courses-tab2">
                            <div class="row">';

                            // if the class list is not empty
                            if(!empty($data->class_courses_list)) {
                                
                                // loop throught the classes list
                                foreach($data->class_courses_list as $course) {
                                    $response->html .= '
                                    <div class="col-lg-6 col-md-6 p-2">
                                        <div class="card">
                                            <div class="card-body pr-2 pl-2 pt-0 pb-0">
                                                <div class="pb-0 pt-3">
                                                    <p class="clearfix mb-2">
                                                        <span class="float-left bold">Name</span>
                                                        <span class="float-right text-muted">
                                                            <span class="user_name" '.(!$isWardParent && ($isTutor && in_array($course->id, $defaultUser->course_ids)) ? 'onclick="load(\'course/'.$course->item_id.'\');"' : null).'>
                                                                '.($isAdmin ? "<a href='{$myClass->baseUrl}course/{$course->id}/lesson'>" : null).'
                                                                    '.$course->name.'
                                                                '.($isAdmin ? "</a>" : null).'
                                                            </span>
                                                        </span>
                                                    </p>
                                                    <p class="clearfix">
                                                        <span class="float-left bold">Code</span>
                                                        <span class="float-right text-muted">'.$course->course_code.'</span>
                                                    </p>
                                                    <p class="clearfix">
                                                        <span class="float-left bold">Credit Hours</span>
                                                        <span class="float-right text-muted">'.$course->credit_hours.'</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }
                            } else {
                                $response->html .= '<div class="col-lg-12 font-italic">Sorry there are no subjects assigned to this class.</div>';
                            }
                            
                            $response->html .= ' 
                            </div>
                        </div>
                        <div class="tab-pane fade '.($updateItem || $url_link === "update" ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
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