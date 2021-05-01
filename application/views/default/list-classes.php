<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$response->title = "Classes List : {$appName}";
$response->scripts = [];

$classes_param = (object) [
    "clientId" => $session->clientId,
    "limit" => 99999
];
$item_list = load_class("classes", "controllers")->list($classes_param);

$hasDelete = $accessObject->hasAccess("delete", "class");
$hasUpdate = $accessObject->hasAccess("update", "class");

$classes = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a title='Click to view the class record' href='#' onclick='return loadPage(\"{$baseUrl}update-class/{$each->id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to update the class record' href='#' onclick='return loadPage(\"{$baseUrl}update-class/{$each->id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete this Class' onclick='return delete_record(\"{$each->id}\", \"class\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $classes .= "<tr data-row_id=\"{$each->id}\">";
    $classes .= "<td>".($key+1)."</td>";
    $classes .= "<td>{$each->name}</td>";
    $classes .= "<td>{$each->class_code}</td>";
    $classes .= "<td>{$each->class_size}</td>";
    $classes .= "<td>{$each->students_count}</td>";
    $classes .= "<td><span class='underline'>".($each->class_teacher_info->name ?? null)."</span></td>";
    $classes .= "<td><span class='underline'>".($each->class_assistant_info->name ?? null)."</span></td>";
    $classes .= "<td align='center'>{$action}</td>";
    $classes .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Classes List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Classes List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Class Name</th>
                                        <th>Class Code</th>
                                        <th>Class Size</th>
                                        <th width="15%">Students Count</th>
                                        <th>Class Teacher</th>
                                        <th>Class Assistant</th>
                                        <th align="center" width="10%"></th>
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