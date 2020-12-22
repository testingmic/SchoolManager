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
$response->title = "Assignments List : {$appName}";
$response->scripts = [];

// the query parameter to load the user information
$i_params = (object) ["limit" => 1, "user_id" => $session->userId, "minified" => "simplified", "userId" => $session->userId];
$userData = $usersClass->list($i_params)["data"][0];

$assignments_param = (object) [
    "clientId" => $session->clientId,
    "userData" => $userData,
    "limit" => 99999
];

$item_list = load_class("assignments", "controllers")->list($assignments_param);

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $userData->user_permissions;
$hasDelete = $accessObject->hasAccess("delete", "assignments");
$hasUpdate = $accessObject->hasAccess("update", "assignments");

$assignments = "";
foreach($item_list["data"] as $key => $each) {
    
    $action = "<a href='{$baseUrl}update-assignment/{$each->item_id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->id}\", \"assignments\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $assignments .= "<tr data-row_id=\"{$each->id}\">";
    $assignments .= "<td>".($key+1)."</td>";
    $assignments .= "<td>{$each->assignment_title}</td>";
    $assignments .= "<td>{$each->due_date} @ {$each->due_time}</td>";
    // show this section if the user has the necessary permissions
    if($hasUpdate) {
        $assignments .= "<td>{$each->students_assigned}</td>";
        $assignments .= "<td>{$each->students_handed_in}</td>";
        $assignments .= "<td>{$each->students_graded}</td>";
    }
    
    if(!$hasUpdate) {
        $assignments .= "<td>{$each->awarded_mark}</td>";
    }

    $assignments .= "<td>{$each->date_created}</td>";
    $assignments .= "<td>".($hasUpdate ? $each->state : $each->handedin_label)."</td>";
    $assignments .= "<td align='center'>{$action}</td>";
    $assignments .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Assignments List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item">Assignments List</div>
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
                                        <th>Title</th>
                                        <th>Due Date</th>
                                        '.($hasUpdate ? '
                                            <th width="10%">Assigned</th>
                                            <th>Handed In</th>
                                            <th>Marked</th>' : '<th>Awarded Mark</th>'
                                        ).'
                                        <th>Date Created</th>
                                        <th>Status</th>
                                        <th align="center" width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$assignments.'</tbody>
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