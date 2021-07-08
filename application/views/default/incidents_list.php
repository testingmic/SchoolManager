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

$filter = (object) $_POST;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) [];
$response->title = "Incidents List : {$appName}";
$response->scripts = ["assets/js/filters.js"];

$params = (object) [
    "clientId" => $session->clientId,
    "subject" => $filter->subject ?? null,
    "user_role" => $filter->user_role ?? null,
    "limit" => 99999
];

$item_list = load_class("incidents", "controllers")->list($params);

$deleteIncident = $accessObject->hasAccess("delete", "incident");
$updateIncident = $accessObject->hasAccess("update", "incident");

$incidents = "";

$color = [
    "student" => "secondary",
    "admin" => "success",
    "employee" => "primary",
    "accountant" => "danger",
    "teacher" => "warning"
];
foreach($item_list["data"] as $key => $each) {
    
    // generate the action buttons
    $action = "<button onclick=\"return load_quick_form('incident_log_form_view','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-primary\" type=\"button\"><i class=\"fa fa-eye\"></i></button>&nbsp;";
                
    // is not active
    $isActive = !in_array($each->status, ["Solved", "Cancelled"]);

    // set the update button
    if($updateIncident && $isActive) {
        $action .= "<button title='Click to update this record' onclick=\"return load_quick_form('incident_log_form','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-success\" type=\"button\"><i class=\"fa fa-edit\"></i></button>";
    }

    if($deleteIncident && $isActive) {
        $action .= "&nbsp;<a href='#' title='Click to delete this record' onclick='return delete_record(\"{$each->item_id}\", \"incident\");' class='btn mb-1 btn-sm btn-outline-danger'><i class='fa fa-trash'></i> </a>";
    }

    $action .= "&nbsp;<a target='_blank' href='{$baseUrl}download/incident?incident_id={$each->item_id}' title='Click to download this incident' class='btn mb-1 btn-sm btn-outline-warning'><i class='fa fa-download'></i> </a>";
    
    $incidents .= "<tr data-row_id=\"{$each->id}\">";
    $incidents .= "<td>".($key+1)."</td>";
    $incidents .= "<td>
        <div class='d-flex justify-content-start'>
            <div class='mr-2'>
                <img class='rounded-circle author-box-picture' width='40px' src=\"{$baseUrl}{$each->user_information->image}\">
            </div>
            <div>
                <a href='{$baseUrl}update-student/{$each->user_information->user_id}'>{$each->user_information->name}</a><br>
                <span class='text-uppercase badge badge-{$color[$each->user_information->user_type]} p-1'>
                    {$each->user_information->user_type}
                </span>
            </div>
    </td>";
    $incidents .= "<td>{$each->subject}</td>";
    $incidents .= "<td>{$each->reported_by}</td>";
    $incidents .= "<td>{$each->incident_date}</td>";
    $incidents .= "<td>".$myClass->the_status_label($each->status)."</td>";
    $incidents .= "<td align='center'>{$action}</td>";
    $incidents .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Incidents List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Incidents List</div>
            </div>
        </div>
        <div class="row">
            
            <div class="col-md-3 col-12 form-group">
                <label>Select Role</label>
                <select data-width="100%" class="form-control selectpicker" name="user_role" id="user_role">
                    <option value="">Please Select Role</option>';
                    foreach($myClass->incident_user_role as $key => $value) {
                        $response->html .= "<option ".(isset($filter->user_role) && ($filter->user_role == $key) ? "selected" : "")." value=\"{$key}\">{$value}</option>";                            
                    }
                    $response->html .= '
                </select>
            </div>
            <div class="col-md-7 col-12 form-group">
                <label>Search with Subject</label>
                <input type="text" class="form-control" name="subject" id="subject" value="'.($filter->subject ?? null).'">
            </div>
            <div class="col-md-2 col-12 form-group">
                <label for="">&nbsp;</label>
                <button id="filter_Incidents_List" type="submit" class="btn btn-outline-warning btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Fullname</th>
                                        <th>Subject</th>
                                        <th>Reported By</th>
                                        <th>Incident Date</th>
                                        <th>Status</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$incidents.'</tbody>
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