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
$response->title = "Guardians List : {$appName}";
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

$guardians = "";
foreach($guardian_list as $kkey => $each) {

    $action = "<a title='Click to view guardian information' href='#' onclick='return loadPage(\"{$baseUrl}update-guardian/{$each->user_id}/view\");' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to update guardian information' href='#' onclick='return loadPage(\"{$baseUrl}update-guardian/{$each->user_id}/update\");' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
    }
    if($hasDelete) {
        $action .= "&nbsp;<a href='#' title='Click to delete guardian record' onclick='return delete_record(\"{$each->user_id}\", \"guardian\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    // load the guardian wards list
    $wards_list = "";
    if(!empty($each->wards_list)) {

        // loop through the list of wards
        foreach($each->wards_list as $key => $ward) {
            // convert to object
            $ward = (object) $ward;

            // append to the list
            $wards_list .= "
                <div class='d-flex justify-content-start".($key+1 !== count($each->wards_list) ? "border-bottom mb-2 pb-2" : "")."'>
                    <div class='mr-2'>
                        <img onclick=\"return loadPage('{$baseUrl}update-student/{$ward->student_guid}/view')\" src=\"{$baseUrl}{$ward->image}\" class='rounded-circle cursor author-box-picture' width='30px'>
                    </div>
                    <div> 
                        <a href='{$baseUrl}update-student/{$ward->student_guid}/view' title='Click to view the full details of {$ward->name}'>{$ward->name} </a>
                        <br>".(!empty($ward->class_name) ? "<span class='font-size:9px!important'><i class='fa fa-home'></i> {$ward->class_name}</span>" : "")."
                    </div>
                </div>
            ";
        } 
    }

    // append to the list of all guardians
    $guardians .= "<tr data-row_id=\"{$each->user_id}\">";
    $guardians .= "<td>".($kkey+1)."</td>";
    $guardians .= "<td><img title=\"Click to view full details\" onclick=\"return loadPage('{$baseUrl}update-guardian/{$each->user_id}/view')\" class='rounded-circle cursor author-box-picture' width='40px' src=\"{$baseUrl}{$each->image}\"> &nbsp; {$each->name}</td>";
    $guardians .= "<td>{$each->relationship}</td>";
    $guardians .= "<td>{$each->email}</td>";
    $guardians .= "<td>{$each->phone_number}</td>";
    $guardians .= "<td>{$wards_list}</td>";
    $guardians .= "<td class='text-center'>{$action}</td>";
    $guardians .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Guardian List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Guardians List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Guardian Name</th>
                                        <th>Relationship</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th width="25%">Wards</th>
                                        <th  align="center" width="13%"></th>
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