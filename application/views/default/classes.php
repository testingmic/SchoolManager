<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultClientData, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Class List";
$response->scripts = [];

$classes_param = (object) [
    "clientId" => $session->clientId,
    "academic_year" => $defaultAcademics->academic_year,
    "academic_term" => $defaultAcademics->academic_term,
    "limit" => 200
];
$item_list = load_class("classes", "controllers", $classes_param)->list($classes_param);

$hasDelete = $accessObject->hasAccess("delete", "class");
$hasUpdate = $accessObject->hasAccess("update", "class");
$viewAllocation = $accessObject->hasAccess("view_allocation", "fees");
$classes = "";

foreach($item_list["data"] as $key => $each) {
    
    $action = "<a title='View Class record' href='#' onclick='return load(\"class/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Update the class record' href='#' onclick='return load(\"class/{$each->id}/update\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($viewAllocation) {
        $action .= "&nbsp;<a target='_blank' href='{$baseUrl}download/student_bill?class_id={$each->id}&isPDF=true' title='Download Class Bill' class='btn btn-sm mb-1 btn-outline-warning'><i class='fa fa-download'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Delete this Class' onclick='return delete_record(\"{$each->id}\", \"class\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $classes .= "<tr data-row_id=\"{$each->id}\">";
    $classes .= "<td>".($key+1)."</td>";
    $classes .= "<td><span class='bold_cursor text-uppercase underline text-info' onclick='return load(\"class/{$each->item_id}\");'>{$each->name}</span></td>";
    $classes .= "<td>{$each->class_code}</td>";
    $classes .= "<td>{$each->department_name}</td>";
    $classes .= "<td>{$each->class_size}</td>";
    $classes .= "<td>{$each->students_count}</td>";
    $classes .= "<td><span class='user_name'>".($each->class_teacher_info->name ?? null)."</span></td>";
    $classes .= "<td><span class='user_name'>".($each->class_assistant_info->name ?? null)."</span></td>";
    $classes .= "<td align='center'>{$action}</td>";
    $classes .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-house-damage"></i> '.$response->title.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Classes</div>
            </div>
        </div>';
        // if the term has ended
        $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl, true);
        $response->html .= '
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Class Name</th>
                                        <th>Code</th>
                                        <th>Department</th>
                                        <th>Class Size</th>
                                        <th width="15%">Students Count</th>
                                        <th>Class Teacher</th>
                                        <th>Class Prefect</th>
                                        <th align="center" width="13%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$classes.'</tbody>
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