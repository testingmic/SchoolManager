<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $session, $myClass, $accessObject, $defaultAcademics, $isAdminAccountant;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$application_list = "";
$response->title = "Applications List";

// end the page if the user is not an admin
if(!$isAdminAccountant) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// create a new object
$applicationObj = load_class("applications", "controllers");

// set some parameters
$param = (object) [
    "clientId" => $clientId
];


// get the list of all applications
$item_list = $applicationObj->list($param);

$hasDelete = $accessObject->hasAccess("delete", "applications");
$hasUpdate = $accessObject->hasAccess("update", "applications");

foreach($item_list["data"] as $key => $each) {
    
    $action = "<button title='View Application Record' onclick='return load(\"application/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></button>";
    
    $application_list .= "<tr data-row_id=\"{$each->id}\">";
    $application_list .= "<td>".($key+1)."</td>";
    $application_list .= "<td><span class='user_name' onclick='return load(\"application/{$each->item_id}\");'>{$each->item_id}</span></td>";
    $application_list .= "<td>{$each->date_created}</td>";
    $application_list .= "<td>
        <div><span class='user_name' onclick='return load(\"application_forms/modify/{$each->form_id}?view=1\");' title='Click to view full details'>".strtoupper($each->name)."</span></div>
        <div><i class='fa fa-calendar'></i> {$each->year_enrolled}</div>
        <div><strong>FORM ID:</strong> <span class='text-info'>{$each->form_id}</span></div>
    </td>";
    $application_list .= "<td></td>";
    $application_list .= "<td>".$myClass->the_status_label($each->state)."</td>";
    $application_list .= "<td class='text-center'>{$action}</td>";
    $application_list .= "</tr>";
}

// display the form information
$response->html = '
    <section class="section list_Students_By_Class">
        <div class="section-header">
            <h1><i class="fa fa-book-open"></i> List: Applications</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active">List: Applications</div>
            </div>
        </div>
        <input type="hidden" disabled name="assign_param" value="department">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="15%">Application ID</th>
                                        <th width="15%">Date Applied</th>
                                        <th>Form Details</th>
                                        <th>Enrolled Student(s)</th>
                                        <th width="12%">State</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$application_list.'</tbody>
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