<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $isSupport, $SITEURL;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];

// not found
if(!$isSupport) {
    // end the query here
    $response->html = page_not_found("permission_denied");
    // echo the response
    echo json_encode($response);
    exit;
}

// get the filter values
$filter = (object) array_map("xss_clean", $_POST);
$package_id = isset($SITEURL[2]) && in_array($SITEURL[2], ["view", "update"]) ? $SITEURL[1] : 0;

// set the item found
$item_found = false;
$isCreate = false;
$client_packages = "";

// get the list of packages
$packagesList = $myClass->pushQuery("*", "clients_packages", !empty($package_id) ? "id = {$package_id}" : 1);

// if no package id was parsed
if(!$package_id) {

    // loop through the list
    foreach($packagesList as $key => $package) {

        $isActive = ($package->status == 'active');
        $status = !$isActive ? "<span class='badge badge-danger'>{$package->status}</span>" : "<span class='badge badge-success'>Active</span>";
        $action = $isActive ? "&nbsp;<span title='Delete Package' onclick='delete_record(\"{$package->id}\", \"package\");' class='btn btn-sm cursor mb-1 btn-outline-danger'><i class='fa cursor fa-trash'></i></span>" : null;

        $client_packages .= '<tr data-row_id="'.$package->id.'">
            <td>'.$package->id.'</td>
            <td>'.ucwords($package->package).'</td>
            <td class="text-center">'.$package->student.'</td>
            <td class="text-center">'.$package->staff.'</td>
            <td class="text-center">'.$package->admin.'</td>
            <td class="text-center">'.$package->monthly_sms.'</td>
            <td class="text-center">'.$package->fees.'</td>
            <td class="text-center">'.$package->pricing.'</td>
            <td class="text-center">'.$status.'</td>
            <td class="text-center">
                <a href="'.$baseUrl.'packages/'.$package->id.'/update" class="btn btn-sm mb-1 btn-outline-success"><i class="fa fa-edit"></i> Edit</a>
                '.$action.'
            </td>
        </tr>';
    }
}

// set the item found
$item_found = !empty($package_id) && !empty($packagesList);

// set a create option
$isCreate = isset($SITEURL[1]) && in_array($SITEURL[1], ["create", "add"]);
$packageData = !$isCreate && $item_found ? $packagesList[0] : (object) [];

// set the page title
$response->title = "Client Packages";
$response->scripts = ["assets/js/support.js"];

// access permissions check
$response->html = '
<section class="section">
    <div class="section-header">
        <h1>Client Packages</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            '.($package_id || $isCreate ? '<div class="breadcrumb-item"><a href="'.$baseUrl.'packages">Client Packages</a></div>' : '<div class="breadcrumb-item">Client Packages</div>').'
            '.($package_id && !empty($package_array) ? '<div class="breadcrumb-item active">Package# '.$package_id.'</div>' : null).'
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-12 col-lg-12">
            '.(!$item_found && !$isCreate ? '
            <div class="text-right mb-2">
                <a href="'.$baseUrl.'packages/create" class="btn btn-sm btn-outline-success"><i class="fa fa-plus"></i> Create New Package</a>
            </div>' : null);
            if(!$item_found && !$isCreate) {
                $response->html .= '
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-student_staff_list">
                            <table data-empty="" data-order_item="desc" class="table table-bordered table-sm table-striped raw_datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th width="15%">Title</th>
                                        <th class="text-center">Students</th>
                                        <th class="text-center">Staff</th>
                                        <th class="text-center">Admin</th>
                                        <th class="text-center">Monthly SMS</th>
                                        <th class="text-center">Fees Cap</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center" width="10%">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>'.$client_packages.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>';
            }
            if($isCreate || $item_found) {
                $response->html .= '
                <div class="card">
                    <div class="card-header">
                        <h4>'.($isCreate ? 'Create New Package' : 'Update Package').'</h4>
                    </div>
                    <div class="card-body">
                        <form class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/account/'.($isCreate ? 'create_package' : 'update_package').'" method="post">
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label for="package">Package Titlw</label>
                                        <input type="text" class="form-control" id="package" name="package" value="'.($packageData->package ?? null).'">
                                    </div>
                                </div>';
                                foreach(['student', 'staff', 'admin', 'monthly_sms', 'fees', 'pricing'] as $key => $value) {
                                    $response->html .= '
                                    <div class="col-sm-12 col-md-6">
                                        <div class="form-group">
                                            <label for="'.$value.'">'.ucwords($value).'</label>
                                            <input type="number" class="form-control" id="'.$value.'" name="'.$value.'" value="'.($packageData->$value ?? null).'">
                                        </div>
                                    </div>';
                                }
                                $response->html .= '
                                '.(!$isCreate ? "<input type='hidden' name='package_id' readonly value='{$packageData->id}'>" : null).'
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select data-width="100%" name="status" id="status" class="form-control selectpicker">
                                            <option '.(!empty($packageData->status) && $packageData->status == "active" ? "selected" : null).' value="active">Active</option>
                                            <option '.(!empty($packageData->status) && $packageData->status == "inactive" ? "selected" : null).' value="inactive">Inactive</option>
                                            <option '.(!empty($packageData->status) && $packageData->status == "deleted" ? "selected" : null).' value="deleted">Deleted</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 text-center">
                                    <button type="button-submit" class="btn btn-outline-success"><i class="fa fa-save"></i> Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>';
            }
    $response->html .='
    </div>
</section>';

// print out the response
echo json_encode($response);