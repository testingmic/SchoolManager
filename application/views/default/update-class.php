<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Class Information";
$response->title = "{$pageTitle} : {$appName}";


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

        // set the first key
        $timetable = "";
        $data = $data["data"][0];

        // guardian information
        $the_form = load_class("forms", "controllers")->class_form($clientId, $baseUrl, $data);
        $hasUpdate = $accessObject->hasAccess("update", "class");

        // load the section students list
        $student_param = (object) ["clientId" => $clientId, "client_data" => $defaultUser->client, "class_id" => $item_id, "user_type" => "student", "bypass" => true];
        $student_list = load_class("users", "controllers", $student_param)->list($student_param);

        // student update permissions
        $students = "";
        $studentUpdate = $accessObject->hasAccess("update", "student");

        // load the class timetable
        $timetable = load_class("timetable", "controllers", $item_param)->class_timetable($data->item_id, $clientId);

        // loop through the students list
        foreach($student_list["data"] as $key => $student) {

            // view link
            $action = "<a href='#' onclick='return loadPage(\"{$baseUrl}update-student/{$student->user_id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

            if($studentUpdate) {
                $action .= "&nbsp;<a href='#' title='Click to delete this Class' onclick='return delete_record(\"{$student->user_id}\", \"class\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
            }

            $students .= "<tr data-row_id=\"{$student->user_id}\">";
            $students .= "<td>".($key+1)."</td>";
            $students .= "<td>
            <div class='d-flex justify-content-start'>
                <div class='mr-1'>
                <img onclick='return loadPage(\"{$baseUrl}update-student/{$student->user_id}\");' class='cursor author-box-picture' width='40px' src=\"{$baseUrl}{$student->image}\"> &nbsp; 
                </div>
                <div>
                    <a href=\"#\" onclick='return loadPage(\"{$baseUrl}update-student/{$student->user_id}\");'>
                        <span class='text-uppercase font-weight-bold text-primary'>{$student->name}</span>
                    </a>
                </div>
            </div>
            </td>";
            $students .= "<td>{$student->unique_id}</td>";
            $students .= "<td>{$student->gender}</td>";
            $students .= "<td>{$action}</td>";
            $students .= "</tr>";
        }

        // student listing
        $student_listing = '
        <div class="table-responsive table-student_staff_list">
            <table data-empty="" class="table table-bordered table-striped raw_datatable">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th>NAME</th>
                        <th>REG. ID</th>
                        <th>GENDER</th>
                        <th width="14%"></th>
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
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-classes">Classes</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-3 text-center bg-info">
                        <div class="font-25 font-weight-bolder text-white">'.$data->name.'</div>
                        <div class="font-18 font-weight-bold text-uppercase text-white">'.$data->class_code.'</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-body card-type-3">
                        <div class="row">
                            <div class="col">
                                <h6 class="font-14 text-uppercase font-weight-bold mb-0">STUDENTS</h6>
                                <h2 class="font-weight-bold mb-0">'.$data->students_count.'</h2>
                            </div>
                            <div class="col-auto">
                                <div class="card-circle l-bg-orange text-white">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-body card-type-3">
                        <div class="row">
                            <div class="col">
                                <h6 class="font-14 text-uppercase font-weight-bold mb-0">BOYS</h6>
                                <h2 class="font-weight-bold mb-0">'.$data->students_male_count.'</h2>
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
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-body card-type-3">
                        <div class="row">
                            <div class="col">
                                <h6 class="font-14 text-uppercase font-weight-bold mb-0">GIRLS</h6>
                                <h2 class="font-weight-bold mb-0">'.$data->students_female_count.'</h2>
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
                    <div class="author-box-center">
                        <div class="clearfix"></div>
                        <div class="author-box-center font-weight-bold mt-2 text-uppercase font-25 mb-0 p-0">'.$data->name.'</div>
                        <div class="author-box-job font-16">'.$data->class_code.'</div>
                    </div>
                </div>
                </div>
                '.(!empty($data->description) ? 
                    '<div class="card">
                        <div class="card-header">
                            <h4>Description</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="py-3 pt-0">
                                '.$data->description.'
                            </div>
                        </div>
                    </div>' : null
                ).'
                <div class="card">
                    <div class="card-header">
                        <h4>CLASS TEACHER</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                    '.(empty($data->class_teacher_info) ? '<div class="py-4 pt-0 text-center">No Class Teacher Set</div>' : 
                        '<div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Fullname</span>
                                <span class="float-right text-muted">'.($data->class_teacher_info->name ?? null).'</span>
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
                <div class="card">
                    <div class="card-header">
                        <h4>CLASS PREFECT</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                    '.(empty($data->class_assistant_info) ? '<div class="py-4 pt-0 text-center">No Class Prefect Set</div>' : 
                        '<div class="py-3 pt-0">
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
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(!$updateItem ? "active" : null).'" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">Student List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="courses-tab2" data-toggle="tab" href="#courses" role="tab" aria-selected="true">Courses List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="calendar-tab2" data-toggle="tab" href="#calendar" role="tab"
                        aria-selected="true">Timetable</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="students" role="tabpanel" aria-labelledby="students-tab2">
                            '.$student_listing.'
                        </div>
                        <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab2">
                            <div class="table-responsive trix-slim-scroll">
                                '.$timetable.'
                            </div>
                        </div>
                        <div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab2">
                            <div class="col-lg-12 pl-0"><h5>Class Courses List</h5></div>
                                <div class="row">';

                            // if the class list is not empty
                            if(!empty($data->class_courses_list)) {
                                
                                // loop throught the classes list
                                foreach($data->class_courses_list as $course) {
                                    $response->html .= '
                                    <div class="col-lg-6 col-md-6">
                                        <div class="card">
                                            <div class="card-body pt-0 pb-0">
                                                <div class="pb-2 pt-3 border-bottom">
                                                    <p class="clearfix mb-2">
                                                        <span class="float-left">Name</span>
                                                        <span class="float-right text-muted"><a href="'.$baseUrl.'update-course/'.$course->id.'/view">'.$course->name.'</a></span>
                                                    </p>
                                                    <p class="clearfix">
                                                        <span class="float-left">Code</span>
                                                        <span class="float-right text-muted">'.$course->course_code.'</span>
                                                    </p>
                                                    <p class="clearfix">
                                                        <span class="float-left">Credit Hours</span>
                                                        <span class="float-right text-muted">'.$course->credit_hours.'</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }
                            } else {
                                $response->html .= '<div class="col-lg-12 font-italic">No class is currently offering this course.</div>';
                            }
                            
                            $response->html .= ' 
                            </div>
                        </div>
                        <div class="tab-pane fade '.($updateItem ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
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