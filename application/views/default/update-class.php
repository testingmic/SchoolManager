<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Class Details";
$response->title = "{$pageTitle} : {$appName}";

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

// item id
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "class_id" => $item_id,
        "load_courses" => true,
        "limit" => 1
    ];

    $data = load_class("classes", "controllers")->list($item_param);
    
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
        $student_param = (object) ["clientId" => $clientId, "class_id" => $item_id, "user_type" => "student"];
        $student_list = load_class("users", "controllers")->list($student_param);

        // student update permissions
        $students = "";
        $studentUpdate = $accessObject->hasAccess("update", "student");

        // load the class timetable
        $timetable = load_class("timetable", "controllers")->class_timetable($data->item_id, $clientId);

        // loop through the students list
        foreach($student_list["data"] as $key => $student) {
            // view link
            $action = "<a href='{$baseUrl}update-student/{$student->user_id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";
            if($studentUpdate) {
                $action .= "&nbsp;<a href='{$baseUrl}update-student/{$student->user_id}/update' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
            }

            $students .= "<tr data-row_id=\"{$student->user_id}\">";
            $students .= "<td>".($key+1)."</td>";
            $students .= "<td><img class='rounded-circle author-box-picture' width='40px' src=\"{$baseUrl}{$student->image}\"> &nbsp; {$student->name}</td>";
            $students .= "<td>{$student->class_name}</td>";
            $students .= "<td>{$student->gender}</td>";
            $students .= "<td>{$action}</td>";
            $students .= "</tr>";
        }

        // student listing
        $student_listing = '
        <div class="table-responsive table-student_staff_list">
            <table data-empty="" class="table table-striped datatable">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Gender</th>
                        <th width="13%"></th>
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
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-classes">Class List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center">
                        <div class="clearfix"></div>
                        <div class="author-box-name"><a href="#">'.$data->name.'</a></div>
                        <div class="author-box-job">'.$data->class_code.'</div>
                        <div class="author-box-job">('.$data->students_count.' Students)</div>
                    </div>
                </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Description</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="py-3 pt-0">
                            '.$data->description.'
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Class Teacher Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
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
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Class Assistant Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
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
                        <a class="nav-link" id="calendar-tab2" data-toggle="tab" href="#calendar" role="tab"
                        aria-selected="true">Timetable</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="courses-tab2" data-toggle="tab" href="#courses" role="tab" aria-selected="true">Courses List</a>
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
                            <div class="col-lg-12 pl-0"><h5>CLASS STUDENTS LIST</h5></div>
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