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
$hasAdd = $accessObject->hasAccess("add", "class");
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
    $classes .= "<td class='text-center'>".($key+1)."</td>";
    $classes .= "<td>
    <div class='flex items-center space-x-4'>
        <div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
                <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'
                    stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'
                    class='lucide lucide-clipboard-list h-6 w-6 text-white' aria-hidden='true'>
                    <rect width='8' height='4' x='8' y='2' rx='1' ry='1'></rect>
                    <path d='M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2'></path>
                    <path d='M12 11h4'></path>
                    <path d='M12 16h4'></path>
                    <path d='M8 11h.01'></path>
                    <path d='M8 16h.01'></path>
                </svg>
            </div>
            <div>
                <span class='bold_cursor text-info' onclick='return load(\"class/{$each->item_id}\");'>{$each->name}</span>
                <p class='text-xs text-gray-500'>{$each->class_code}</p>
            </div>
        </div>
    </td>";
    $classes .= "<td>{$each->department_name}</td>";
    $classes .= "<td>{$each->class_size}</td>";
    $classes .= "<td>{$each->students_count}</td>";
    $classes .= "<td><span class='user_name'>".($each->class_teacher_info->name ?? null)."</span></td>";
    $classes .= "<td class='text-center'><span class='user_name' title='".($each->is_graduation_level == "Yes" ? "Yes" : "No")."'>".($each->is_graduation_level == "Yes" ? "<i class='fa fa-check-circle text-success'></i>" : "<i class='fa fa-times-circle text-danger'></i>")."</span></td>";
    $classes .= "<td class='text-center'>{$action}</td>";
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
                <div class="text-right mb-2">
                    '.($hasAdd ? '
                        <a class="btn btn-outline-success" href="'.$baseUrl.'class_add"><i class="fas fa-graduation-cap"></i> Create New Class</a>' : ''
                    ).'
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-sm table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th width="22%">Class Name</th>
                                        <th>Department</th>
                                        <th>Class Size</th>
                                        <th width="15%">Students Count</th>
                                        <th>Class Teacher</th>
                                        <th class="text-center">Graduation Level</th>
                                        <th align="center" width="18%"></th>
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