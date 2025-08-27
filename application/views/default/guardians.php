<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Guardians List";
$response->scripts = [];

$guardian_param = (object) [
    "append_wards" => true,
    "user_type" => "parent",
    "clientId" => $session->clientId,
    "client_data" => $defaultUser->client
];

$guardian_list = load_class("users", "controllers", $guardian_param)->list($guardian_param)["data"];

$hasDelete = $accessObject->hasAccess("delete", "guardian");
$hasUpdate = $accessObject->hasAccess("update", "guardian");
$hasAdd = $accessObject->hasAccess("add", "guardian");

$viewDelegates = $accessObject->hasAccess("view", "delegates");

$guardians = "";
foreach($guardian_list as $kkey => $each) {

    $action = "<a title='Click to view guardian information' href='#' onclick='return load(\"guardian/{$each->user_id}/view\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
    
    if($accessObject->hasAccess("update", "guardian")) {
        $action .= "&nbsp;<a title='Update Staff Record' href=\"guardian/{$each->user_id}/update\" class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
    }

    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete guardian record' onclick='return delete_record(\"{$each->user_id}\", \"guardian\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    // load the guardian wards list
    $wards_list = "";
    if(!empty($each->wards_list)) {

        $processedIds = [];
        // loop through the list of wards
        foreach($each->wards_list as $key => $ward) {

            // convert to object
            $ward = (object) $ward;

            if(in_array($ward->student_guid, $processedIds)) {
                continue;
            }

            $imageToUse = "";
            $processedIds[] = $ward->student_guid;

            // append to the list
            $wards_list .= "
                <div class='d-flex justify-content-start".($key+1 !== count($each->wards_list) ? "border-bottom mb-2 pb-2" : "")."'>
                    <div class='mr-2'>
                        {$imageToUse}
                    </div>
                    <div> 
                        <a href='{$baseUrl}student/{$ward->student_guid}' class='user_name' title='View the details of {$ward->name}'>
                            ".(!empty($ward->name) ? ucwords(strtolower($ward->name)) : null)."
                        </a>
                        <br>".(!empty($ward->class_name) ? "<i class='fa fa-home'></i> ".ucwords(strtolower($ward->class_name)) : "")."
                    </div>
                </div>
            ";
        } 
    }

    // append to the list of all guardians
    $guardians .= "<tr data-row_id=\"{$each->user_id}\">";
    $guardians .= "<td>".($kkey+1)."</td>";
    $guardians .= "<td>
        <div class='d-flex justify-content-start'>
            <div class='mr-1'>
                <img title='View guardian details' onclick=\"return load('guardian/{$each->user_id}')\" class='author-box-picture' width='40px' src=\"{$baseUrl}{$each->image}\">
            </div>
            <div>
                <span class='user_name' title='View guardian record' onclick='return load(\"guardian/{$each->user_id}\");'>
                {$each->name} <br>
                </span>
                <strong>{$each->unique_id}</strong>
            </div>
        </div>
    </td>";
    $guardians .= "<td>".(!empty($each->email) ? strtolower($each->email) : null)."</td>";
    $guardians .= "<td>{$each->phone_number}</td>";
    $guardians .= "<td>{$wards_list}</td>";
    $guardians .= "<td width='13%' class='text-center'>{$action}</td>";
    $guardians .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-user-friends"></i> Guardian List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Guardians</div>
            </div>
        </div>';
        // if the term has ended
        if($isAdminAccountant) {
            $response->html .= top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl);
        }
        $response->html .= '
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                    '.($hasAdd ? '
                        <a class="btn btn-outline-success" href="'.$baseUrl.'guardian_add"><i class="fas fa-user-clock"></i> Add Guardian</a>' : ''
                    ).'
                    '.($viewDelegates ? '
                        <a class="btn btn-primary" href="'.$baseUrl.'delegates"><i class="fas fa-user-friends"></i> Manage Delegates</a>' : ''
                    ).'
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Guardian Name</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th width="25%">Wards</th>
                                        <th width="13%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$guardians.'</tbody>
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