<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Department Details";
$response->title = "{$pageTitle} : {$appName}";

$response->scripts = [
    "assets/js/page/index.js"
];

// item id
$item_id = $SITEURL[1] ?? null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "department_id" => $item_id,
        "limit" => 1
    ];

    $data = load_class("departments", "controllers")->list($item_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        // guardian information
        $the_form = load_class("forms", "controllers")->department_form($clientId, $baseUrl, $data);
        $hasUpdate = $accessObject->hasAccess("update", "department");

        // load the section students list
        $student_param = (object) ["clientId" => $clientId, "department_id" => $item_id, "user_type" => "student"];
        $student_list = load_class("users", "controllers")->list($student_param);

        // student update permissions
        $students = "";
        $studentUpdate = $accessObject->hasAccess("update", "student");

        // loop through the students list
        foreach($student_list["data"] as $key => $student) {
            // view link
            $action = "<a href='#' onclick='return loadPage(\"{$baseUrl}update-student/{$student->user_id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";
            if($studentUpdate) {
                $action .= "&nbsp;<a href='#' onclick='return loadPage(\"{$baseUrl}update-student/{$student->user_id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
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
            $students .= "<td>{$student->class_name}</td>";
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
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Gender</th>
                        <th width="13%">Action</th>
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
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-departments">Departments</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center">
                        <img alt="image" src="'.$baseUrl.''.$data->image.'" class="author-box-picture">
                        <div class="clearfix"></div>
                        <div class="author-box-name"><a href="#">'.$data->name.'</a></div>
                        <div class="author-box-job">'.$data->department_code.'</div>
                        <div class="author-box-job">('.$data->students_count.' Students)</div>
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
                        <h4>Department Head</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            '.(!empty($data->department_head_info->name) ?
                                '<p class="clearfix">
                                    <span class="float-left">Fullname</span>
                                    <span class="float-right text-muted">'.($data->department_head_info->name ?? null).'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Email</span>
                                    <span class="float-right text-muted">'.($data->department_head_info->email ?? null).'</span>
                                </p>
                                <p class="clearfix">
                                    <span class="float-left">Contact</span>
                                    <span class="float-right text-muted">'.($data->department_head_info->phone_number ?? null).'</span>
                                </p>' : '<div class="text-center">Department Head Not Set</div>'
                            ).'
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