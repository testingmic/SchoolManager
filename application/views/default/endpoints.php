<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser, $isSupport;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// set the title
$page_title = "Api Endpoints";

// not found
if(!$isSupport) {
    // end the query here
    $response->html = page_not_found("permission_denied");
    // echo the response
    echo json_encode($response);
    exit;
}

// load the api endpoints
$api = load_class("endpoints", "controllers");

// the query parameter to load the user information
$i_params = (object) [
    "limit" => 1,
    "user_id" => $session->userId
];

// preset
$method = isset($_GET["method"]) ? strtolower(xss_clean($_GET["method"])) : null;
$resource = isset($_GET["resource"]) ? strtolower(xss_clean($_GET["resource"])) : null;
$end_id = isset($_GET["end_id"]) ? strtolower(xss_clean($_GET["end_id"])) : null;

$endpoint_id = null;

// if the endpoint id and a label was parsed
// confirm that the label is either an update or delete
if(isset($_POST["label"], $_POST["endpoint_id"]) && in_array($_POST["label"], ["save", "deprecate", "delete", "fetch", "restore"])) {
    $endpoint_id = !empty($_POST["endpoint_id"]) ? xss_clean($_POST["endpoint_id"]) : null;
}

// set the parameters to push
$params = (object) [
    "method" => $method,
    "userId" => $session->userId,
    "resource" => $resource,
    "defaultUser" => $defaultUser,
    "endpoint_id" => $endpoint_id
];

// get the list
$list = $api->list($params);

// if the update was parsed
if(isset($_POST["label"], $_POST["endpoint_id"])) {
    // if label is update
    if(in_array($_POST["label"], ["fetch"])) {
        // return the endpoint information
        print !empty($list) ? json_encode($list["data"][$endpoint_id]) : null;
    }
    // if label is update
    elseif(in_array($_POST["label"], ["deprecate"])) {
        $params->label = "deprecate";
        // return the endpoint information
        print !empty($list) ? json_encode($api->update($params)) : "bugged";
    }
    // if label is update
    elseif(in_array($_POST["label"], ["delete"])) {
        $params->label = "delete";
        // return the endpoint information
        print !empty($list) ? json_encode($api->update($params)) : "bugged";
    }
    // if label is update
    elseif(in_array($_POST["label"], ["restore"])) {
        $params->label = "restore";
        // return the endpoint information
        print !empty($list) ? json_encode($api->update($params)) : "bugged";
    }
    // save a post
    elseif(in_array($_POST["label"], ["save"])) {
        // process this record
        $data = (object) array_map("xss_clean", $_POST);

        // append the user id
        $data->userId = $session->userId;
        $data->defaultUser = $defaultUser;

        // if the request was not parsed
        if(!isset($data->request)) {
            print "Sorry! An invalid request was parsed.";
        }
        // if the request is update but no record was found
        elseif(($data->request == "update") && (empty($list) || isset($list[1]->resource))) {
            print "Sorry! An invalid endpoint request id was parsed.";
        }
        // submit the information for update
        elseif($data->request == "update") {
            print json_encode($api->update($data));
        }
        // submit the information for saving a new endpoint
        elseif($data->request == "add") {
            print json_encode($api->add($data));
        }

    }
    exit;
}

// resource only list
$rec_only = (object) [
    "resource_only" => true
];
$resources = $api->list($rec_only);

// set the initial response
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];

// set the title
$response->title = "API Endpoints";
$response->scripts = ["assets/js/endpoints.js"];

$response->html = '
<section class="section">
    <div class="section-header">
        <h1>API Endpoints</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
            <div class="breadcrumb-item">API Endpoints</div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 mb-2 form-group">
            <div class="page-content">
                <div class="row">
                    <div class="col-lg-5 p-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Api Endpoint List</div>
                            </div>
                            <div class="card-body p-1">
                                <div class="filters">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <select data-width="100%" name="resource" id="resource" class="form-control selectpicker">
                                                    <option value="">Select Resource</option>';
                                                    foreach($resources["data"] as $key => $value) {
                                                        $response->html .= '<option '.(($resource == $value->resource) ? "selected" : null).' value="'.$value->resource.'">'.ucfirst($value->resource).'</option>';
                                                    }
                                                $response->html .= '
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <select data-width="100%" name="method" id="method" class="form-control selectpicker">
                                                    <option value="">Select Method</option>';
                                                    $response->html .= '<option '.(($method == "get") ? "selected" : null).' value="get">GET</option>';
                                                    $response->html .= '<option '.(($method == "post") ? "selected" : null).' value="post">POST</option>';
                                                    $response->html .= '<option '.(($method == "put") ? "selected" : null).' value="put">PUT</option>';
                                                    $response->html .= '<option '.(($method == "delete") ? "selected" : null).' value="delete">DELETE</option>';
                                                $response->html .= '
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <button type="refresh" class="btn btn-block btn-outline-primary btn-sm"><i class="fa fa-filter"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="endpoint-list">
                                    <table class="table pb-0 mb-0 table-hover " width="100%">
                                        <th width="6%">#</th>
                                        <th align="left">Endpoint</th>
                                        <th width="15%">Method</th>
                                        <th width="20%"></th>
                                    </table>
                                    <div class="slim-scroll" style="overflow-y:auto;height:650px">
                                        <table class="table pt-0 mt-0 table-hover data-table">
                                            <tbody>';
                                            $key = 0; 
                                            foreach($list["data"] as $value) { 
                                                $key++;
                                                $response->html .= '<tr data-item="'.$value->item_id.'">
                                                    <td width="6%">'.$key.'</td>
                                                    <td>'.ucfirst($value->endpoint).'';
                                                        if($value->deprecated && !$value->deleted) {
                                                            $response->html .= '<br><span class="badge badge-warning">Deprecated</span>';
                                                        }
                                                        if($value->deleted) {
                                                            $response->html .= '<br><span class="badge badge-danger">Deleted</span>';
                                                        }
                                                    $response->html .= '</td>
                                                    <td width="15%">'.strtoupper($value->method).'</td>
                                                    <td width="20%" align="center">';
                                                        if(!$value->deprecated) {
                                                            $response->html .= '<button data-function="update" title="Update this Endpoint" data-item="'.$value->item_id.'" class="btn btn-sm mb-1 btn-outline-success"><i class="fa fa-edit"></i></button>
                                                        <button data-function="delete" data-label="deprecate" data-msg="Are you want you want to deprecate this endpoint?" title="Deprecate this Endpoint" data-item="'.$value->item_id.'" class="btn mb-1 btn-sm btn-outline-danger"><i class="fa fa-stop"></i></button>';
                                                        } elseif($value->deprecated && !$value->deleted) {
                                                            $response->html .= '<button data-function="delete" data-label="restore" data-msg="Are you sure you want to restore this endpoint to active state?" title="Set this endpoint as active" data-item="'.$value->item_id.'" class="btn mb-1 btn-sm btn-outline-warning"><i class="fa fa-play"></i></button>
                                                        <button data-function="delete" data-label="delete" data-msg="Are you want you want to permanently delete this endpoint?" title="Delete endpoint" data-item="'.$value->item_id.'" class="btn mb-1 btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>';
                                                        }
                                                    $response->html .= '</td>
                                                </tr>';
                                            }
                                            $response->html .= '
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 p-2">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title width-100">
                                    Endpoint Content &nbsp;
                                    <span class="float-right">
                                        <a href="'.$myClass->baseUrl.'endpoints" class="btn btn-sm hidden refresh-button btn-outline-success"><i class="fa fa-random"></i> Refresh</a>
                                        <button class="btn btn-outline-primary btn-sm cursor" type="add"><i class="fa fa-plus"></i> Add New</button>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="endpoint-content">
                                    <form action="'.$myClass->baseUrl.'endpoints/save" autocomplete="Off" class="endpoint-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="resource">Resource Name</label>
                                                    <input type="text" name="resource" id="resource" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="endpoint">Endpoint URL</label>
                                                    <input type="text" name="endpoint" id="endpoint" class="form-control">
                                                    <input type="text" name="request" readonly hidden id="request" value="add" class="form-control">
                                                    <input type="hidden" readonly hidden value="'.$end_id.'" name="endpoint_id" id="endpoint_id" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="parameter">Parameters <small>(Must be a valid json formatted string)</small></label>
                                                    <textarea name="parameter" id="parameter" rows="7" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="description">Description <small>(A little description of this endpoint)</small></label>
                                                    <textarea name="description" id="description" rows="7" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="status">Status</label>
                                                    <select name="status" id="status" class="form-control selectpicker">
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                        <option value="dormant">Dormant</option>
                                                        <option value="overloaded">Overloaded</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="method">Method</label>
                                                    <select name="method" id="method" class="form-control selectpicker">
                                                        <option value="">Select Method</option>
                                                        <option value="get">GET</option>
                                                        <option value="post">POST</option>
                                                        <option value="put">PUT</option>
                                                        <option value="delete">DELETE</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group text-center">
                                                    <label for="">_______________</label>
                                                    <button type="submit" class="btn btn-block btn-outline-success btn-sm"><i class="fa fa-save"></i> Save</button>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="endpoint-info hidden">
                                                    <div><strong>Date Created:</strong> <span class="date_created"></span></div>
                                                    <div><strong>Last Updated:</strong> <span class="last_updated"></span></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-center">
                                                <div class="form-results"></div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>';

// print out the response
echo json_encode($response);
?>