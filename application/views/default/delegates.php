<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $isWardParent, $isAdminAccountant;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Delegates List";
$response->scripts = [];

// set the parent menu
$response->parent_menu = "guardians";

// end query if the user has no permissions
if(!$accessObject->hasAccess("view", "delegates") && !$isWardParent) {
    // permission denied information
    $response->html = page_not_found("permission_denied", ["delegates"]);
    echo json_encode($response);
    exit;
}

$hasDelete = $accessObject->hasAccess("delete", "delegates") || $isWardParent;
$hasUpdate = $accessObject->hasAccess("update", "delegates") || $isWardParent;
$hasAdd = $accessObject->hasAccess("add", "delegates") || $isWardParent;

$department_param = (object) [
    "clientId" => $session->clientId,
    "userId" => $session->userId,
];

$item_list = load_class("delegates", "controllers", $department_param)->list($department_param);

$count = 0;
$delegates = "";

if($isAdminAccountant) {
    // loop through the item list
    foreach($item_list["data"] as $key => $each) {
        
        $action = "&nbsp;<a title='Click to update delegate record' href='#' onclick='return load(\"delegate/{$each->id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
        $count++;
        if($hasUpdate) {
            $action .= "&nbsp;<a title='Update the delegate record' href='#' onclick='return load(\"delegate/{$each->id}/update\");' class='btn btn-sm mb-1 btn-outline-success'><i class='fa fa-edit'></i></a>";
        }
        if($hasDelete) {
            $action .= "&nbsp;<a href='#' title='Click to delete this Delegate' onclick='return delete_record(\"{$each->id}\", \"delegates\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
        }

        $delegates .= "<tr data-row_id=\"{$each->id}\">";
        $delegates .= "<td>
        <div class='flex items-center space-x-4'>
            <div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
                <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'
                    stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'
                    class='lucide lucide-tag h-6 w-6 text-white' aria-hidden='true'>
                    <path
                        d='M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z'></path>
                    <circle cx='7.5' cy='7.5' r='.5' fill='currentColor'></circle>
                </svg>
            </div>
            <div>
                <span class='bold_cursor text-info' onclick='return load(\"delegate/{$each->id}\");'>{$each->firstname} {$each->lastname}</span>
                <p class='text-xs text-gray-500'>{$each->unique_id}</p>
            </div>
        </div>
        </td>";
        $delegates .= "<td>{$each->gender}</td>";
        $delegates .= "<td>{$each->phonenumber}</td>";
        $delegates .= "<td>{$each->relationship}</td>";
        $delegates .= "<td>{$each->date_created}</td>";
        $delegates .= "<td class='text-center'>{$action}</td>";
        $delegates .= "</tr>";
    }
}

// get the list of delegates for the guardian
if(!empty($item_list["data"])) {
    $delegates_list = "<div class='row mb-3' id='guardian_delegate_listing'>";
    $delegates_list .= $usersClass->guardian_delegatelist($item_list["data"], $session->userId, $hasUpdate, "col-lg-4 col-md-6");
    $delegates_list .= "</div>";
} else {
    $delegates_list = no_record_found("No Delegates Found", "No delegate has been added to your account yet.", null, "Student", false, "fas fa-user-friends");
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-hotel"></i> Delegates List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">Delegates</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                    '.($hasAdd ? '
                        <div><button onclick="return load_quick_form(\'load_delegate_form\', \''.$session->userId.'\');" class="btn btn-outline-primary btn-sm" type="button"><i class="fa fa-user"></i> Create Delegate</button></div>' : ''
                    ).'
                </div>
                '.(!$isAdminAccountant ? $delegates_list : '
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" data-rows_count="20" class="table table-bordered table-striped table-sm datatable">
                                <thead>
                                    <tr>
                                        <th>Fullname</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
                                        <th>Relationship</th>
                                        <th>Date Created</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$delegates.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
                ').'    
            </div>
        </div>
    </section>';
// print out the response
echo json_encode($response);
?>