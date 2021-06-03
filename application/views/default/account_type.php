<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$filter = (object) $_GET;
$pageTitle = "Simple Accounting";
$response->title = "{$pageTitle} : {$appName}";
$response->scripts = [
    "assets/js/accounting.js"
];

// set the parameters
$params = (object)[
    "clientId" => $clientId,
    "client_data" => $defaultUser->client,
    "type_id" => $filter->type_id ?? null
];
// get the list of all the account types
$accountsObject = load_class("accounting", "controllers", $params);
$list_data = $accountsObject->list_accounttype($params)["data"];

// append the data to the params
if(!empty($list_data) && !empty($params->type_id)) {
    $params->data = $list_data[0];
}

// init value
$type_list = "";
$account_headtype_array = [];

// if the user has the required permissions
$hasUpdate = $accessObject->hasAccess("account_type_head", "accounting");

// loop through the list of account type heads
foreach($list_data as $key => $each) {
    // append to the array list
    $account_headtype_array[$each->item_id] = $each;

    // set the action button
    $action = "";
    if($hasUpdate) {
        $action .= "&nbsp;<a title='Click to delete record' href='#' onclick='return update_account_type(\"{$each->item_id}\");' class='btn mb-1 btn-sm btn-outline-success'><i class='fa fa-edit'></i></a>";
        $action .= "&nbsp;<a href='#' title='Click to delete this Account Type Head' onclick='return delete_record(\"{$each->item_id}\", \"accounts_type\");' class='btn btn-sm mb-1 btn-outline-danger'><i class='fa fa-trash'></i></a>";
    }

    // append to the rows
    $type_list .= "<tr data-row_id=\"{$each->item_id}\">";
    $type_list .= "<td>".($key+1)."</td>";
    $type_list .= "<td>{$each->name}</td>";
    $type_list .= "<td>{$each->type}</td>";
    $type_list .= $hasUpdate ? "<td>{$action}</td>" : null;
    $type_list .= "</tr>";
}

// load the form
$the_form = $hasUpdate ? load_class("forms", "controllers")->account_type_head($params) : null;
$response->array_stream["account_headtype_array"] = $account_headtype_array;

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            '.$the_form.'
            <div class="col-12 '.($hasUpdate ? "col-md-7 col-lg-8" : "col-md-12").'">
                <div class="card">
                    <div class="card-header"><i class="fa fa-list"></i> &nbsp; Account Type Head List</div>
                    <div class="card-body">

                        <div class="table-responsive">
                            <table data-empty="" class="table table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        '.($hasUpdate ? '<th width="13%" align="center"></th>' : null).'
                                    </tr>
                                </thead>
                                <tbody>'.$type_list.'</tbody>
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