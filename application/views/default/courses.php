<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $defaultAcademics, $isWardParent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Subjects List ";
$response->scripts = ["assets/js/filters.js"];

$clientId = $session->clientId;

$filter = (object) array_map("xss_clean", $_POST);

$courses_param = (object) [
    "clientId" => $clientId,
    "userId" => $session->userId,
    "userData" => $defaultUser,
    "department_id" => $filter->department_id ?? null,
    "class_id" => $filter->class_id ?? null,
    "course_tutor" => $filter->course_tutor ?? null,
    "academic_year" => $defaultAcademics->academic_year,
    "academic_term" => $defaultAcademics->academic_term,
];

$item_list = load_class("courses", "controllers")->list($courses_param);

$hasDelete = $accessObject->hasAccess("delete", "course");
$hasUpdate = $accessObject->hasAccess("update", "course");
$hasAdd = $accessObject->hasAccess("add", "course");

$hasFiltering = $accessObject->hasAccess("filters", "settings");

$statistics = [
    'total' => [
        'count' => 0,
        'label' => 'TOTAL SUBJECTS'
    ],
    'with_tutors' => [
        'count' => 0,
        'label' => 'SUBJECTS WITH TUTORS'
    ],
    'no_tutors' => [
        'count' => 0,
        'label' => 'SUBJECTS WITH NO TUTORS'
    ],
];

$courses = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a title='View the course record' href='#' onclick='return load(\"course/{$each->id}\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the course record' href='#' onclick='return load(\"course/{$each->id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Delete this Course' onclick='return delete_record(\"{$each->id}\", \"course\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }


    $statistics['total']['count']++;

    $courses .= "<tr data-row_id=\"{$each->id}\">";
    $courses .= "<td class='text-center'>".($key+1)."</td>";
    $courses .= "<td>
    <div class='flex items-center space-x-4'>
        <div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
            <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-book-open h-6 w-6 text-white' aria-hidden='true'>
                <path d='M12 7v14'></path>
                <path d='M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z'>
                </path>
            </svg>
        </div>
        <div>
            <span onclick='return load(\"course/{$each->id}\");' class='user_name'>{$each->name}</span>
            <p class='text-xs text-gray-500'>{$each->course_code}</p>
        </div>
    </div>
    </td>";
    $courses .= $isAdmin ? "<td>{$each->credit_hours}</td>" : null;
    
    if(!$isWardParent) {
        $courses .= "<td>";
        foreach($each->class_list as $class) {
            $courses .= "<p class='mb-0 pb-0'><span class='user_name' onclick='return load(\"class/{$class->item_id}\");'>".strtoupper($class->name)."</span></p>";
        }
        $courses .= "</td>";
    }

    $courses .= "<td>";

    // loop through the course tutors
    if(!empty($each->course_tutors)) {
        foreach($each->course_tutors as $key => $tutor) {
            $courses .= "
            <div data-record-row_id='{$tutor->item_id}_{$each->id}' class='mb-2 ".($key !== count($each->course_tutors) - 1 ? "border-bottom pb-2" : null)."'>
                <span class='user_name' onclick='return load(\"staff/{$tutor->item_id}/documents\");'>".$tutor->name."</span>
                ".($hasUpdate ? "<span onclick='return delete_record(\"{$each->id}\", \"teacher_course\", \"delete\", \"{$tutor->item_id}\", \"data-record-row_id\");' class='cursor-pointer float-right hover:text-red-500'>
                    <i class='fa fa-trash'></i>
                </span>" : null)."
            </div>";
        }
        $statistics['with_tutors']['count']++;
    } else {
        $statistics['no_tutors']['count']++;
    }

    $courses .= "<td class='text-center'>{$each->date_created}</td>";

    $courses .= "</td><td class='text-center'>{$action}</td>";
    $courses .= "</tr>";
}

// default class_list
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "a.id, a.name, a.item_id"
];
// if the class_id is not empty
$classes_param->department_id = !empty($filter->department_id) ? $filter->department_id : null;
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

$statistics_card = '';

foreach($statistics as $key => $each) {
    $statistics_card .= '
    <div class="col-md-3">
        <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid border-blue">
            <div class="card-body pt-3 pl-3 pr-3 pb-2 card-type-3">
                <div class="row">
                    <div class="col">
                        <h6 class="font-14 text-uppercase font-bold mb-0">'.$each['label'].'</h6>
                        <span class="font-bold text-primary font-20 mb-0">'.$each['count'].'</span>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}

$response->html = '
    <section class="section">
        <div class="section-header byPass_Null_Value">
            <h1><i class="fa fa-book-open"></i> Subjects List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Subjects List</div>
            </div>
        </div>';
        // if the term has ended
        if($isAdminAccountant) {
            $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
        }

        $response->html .= '
        <div class="row" id="filter_Department_Class">
            <div class="col-xl-4 '.(!$hasFiltering ? 'hidden': '').' col-md-4 col-12 form-group">
                <label>Select Department</label>
                <select data-width="100%" class="form-control selectpicker" id="department_id" name="department_id">
                    <option value="">Please Select Department</option>';
                    foreach($myClass->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->department_id) && ($filter->department_id == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Class</label>
                <select data-width="100%" class="form-control selectpicker" name="class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {
                        $response->html .= "<option ".(isset($filter->class_id) && ($filter->class_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-xl-3 '.(!$hasFiltering ? 'hidden': '').' col-md-3 col-12 form-group">
                <label>Select Subject Tutor</label>
                <select data-width="100%" class="form-control selectpicker" name="course_tutor">
                    <option value="">Please Select Tutor</option>';
                    foreach($myClass->pushQuery("item_id, name, unique_id", "users", "user_type IN ('teacher') AND user_status IN ({$myClass->default_allowed_status_users_list}) AND client_id='{$clientId}'") as $each) {
                        $response->html .= "<option ".(isset($filter->course_tutor) && ($filter->course_tutor == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->name)." ({$each->unique_id})</option>";                            
                    }
                $response->html .= '
                </select>
            </div>
            <div class="col-xl-2 '.(!$hasFiltering ? 'hidden': '').' col-md-2 col-12 form-group">
                <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                <button id="filter_Courses_List" type="submit" class="btn height-40 btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="row mb-3">
                    '.$statistics_card.'
                    <div class="col-md-3 text-right">
                        '.($hasAdd ? '
                            <a class="btn btn-outline-success" href="'.$baseUrl.'class_add"><i class="fas fa-graduation-cap"></i> Create New Class</a>' : ''
                        ).'
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Subject Title</th>
                                        '.($isAdmin ? '<th>Credit Hours</th>' : null).'
                                        '.(!$isWardParent ? '<th width="12%">Class Name</th>' : null).'
                                        <th>Subject Tutor</th>
                                        <th class="text-center">Date Created</th>
                                        <th align="center" width="14%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$courses.'</tbody>
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