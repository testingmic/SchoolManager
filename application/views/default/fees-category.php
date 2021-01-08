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
$response->title = "Fees Category List : {$appName}";
$response->scripts = [];

$department_param = (object) [
    "clientId" => $session->clientId
];
$department_list = load_class("fees", "controllers")->category_list($department_param);

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;
$accessObject->userPermits = $defaultUser->user_permissions;

$hasAdd = $accessObject->hasAccess("add", "fees_category");
$hasUpdate = $accessObject->hasAccess("update", "fees_category");

$sections = "";
foreach($department_list["data"] as $key => $each) {
    
    $action = "<a href='{$baseUrl}update-fees-category/{$each->id}/view' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i></a>";

    if($hasUpdate) {
        $action .= "&nbsp;<a href='{$baseUrl}update-fees-category/{$each->id}/update' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$each->id}\", \"fees_category\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    $sections .= "<tr data-row_id=\"{$each->id}\">";
    $sections .= "<td>".($key+1)."</td>";
    $sections .= "<td>{$each->name}</td>";
    $sections .= "<td>{$each->code}</td>";
    $sections .= "<td>{$each->amount}</td>";
    $sections .= "<td>{$each->fees_count}</td>";
    $sections .= "<td align='center'>{$action}</td>";
    $sections .= "</tr>";
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>Fees Category List</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                <div class="breadcrumb-item">Fees Category List</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                '.($hasAdd ? '
                    <div class="text-right mb-2">
                        <a class="btn btn-sm btn-outline-primary" href="'.$baseUrl.'add-fees-category"><i class="fa fa-plus"></i> Add Category</a>
                    </div>' : ''
                ).'
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Amount</th>
                                        <th>Fees Count</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$sections.'</tbody>
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