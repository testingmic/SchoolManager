<?php
// set the title
$page_title = "Api Endpoints";

// load the api endpoints
$api = load_class("endpoints", "controllers");

// the query parameter to load the user information
$i_params = (object) [
    "limit" => 1,
    "user_id" => $session->userId
];

// load the user data
$userData = $usersClass->list($i_params)["data"][0];

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
    "userData" => $userData,
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
        $data->userData = $userData;

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

// test store
$test = [
    "persist:imp-client-{$session->userId}" => [
        "policy" => [],
        "policy_form" => [],
        "policy_types" => [],
        "chats" => [],
        "payment_history" => [],
        "complaints" => [],
        "user_activity" => [],
        "notifications" => [],
        "endpoints" => [],
        "insurance_companies" => [],
        "insurance_agents" => [],
        "insurance_brokers" => [],
        "insurance_agents" => [],
    ]
];

// loaded js
$loadedJS = [
    "assets/js/endpoints.js"
];

// require the headtags
require "headtags.php";

?>
<div class="main-content">
    <div class="page-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
            <div>
                <h4 class="mb-3 mb-md-0">Api Endpoints</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">

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
                                                <select name="resource" id="resource" class="form-control selectpicker">
                                                    <option value="">Select Resource</option>
                                                    <?php foreach($resources["data"] as $key => $value) { ?>
                                                    <option <?= ($resource == $value->resource) ? "selected" : null; ?> value="<?= $value->resource ?>"><?= ucfirst($value->resource) ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <select name="method" id="method" class="form-control selectpicker">
                                                    <option value="">Select Method</option>
                                                    <option <?= ($method == "get") ? "selected" : null; ?> value="get">GET</option>
                                                    <option <?= ($method == "post") ? "selected" : null; ?> value="post">POST</option>
                                                    <option <?= ($method == "put") ? "selected" : null; ?> value="put">PUT</option>
                                                    <option <?= ($method == "delete") ? "selected" : null; ?> value="delete">DELETE</option>
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
                                    <div class="slim-scroll" style="overflow-y:auto;height:430px">
                                        <table class="table pt-0 mt-0 table-hover data-table">
                                            <tbody>
                                            <?php $key = 0; foreach($list["data"] as $value) { $key++; ?>
                                                <tr data-item="<?= $value->item_id ?>">
                                                    <td width="6%"><?= $key ?></td>
                                                    <td><?= ucfirst($value->endpoint) ?>
                                                        <?php if($value->deprecated && !$value->deleted) { ?>
                                                        <br><span class="badge badge-warning">Deprecated</span>
                                                        <?php } ?>
                                                        <?php if($value->deleted) { ?>
                                                        <br><span class="badge badge-danger">Deleted</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td width="15%"><?= strtoupper($value->method) ?></td>
                                                    <td width="20%" align="center">
                                                        <?php if(!$value->deprecated) { ?>
                                                        <button data-function="update" title="Update this Endpoint" data-item="<?= $value->item_id ?>" class="btn btn-sm mb-1 btn-outline-success"><i class="fa fa-edit"></i></button>
                                                        <button data-function="delete" data-label="deprecate" data-msg="Are you want you want to deprecate this endpoint?" title="Deprecate this Endpoint" data-item="<?= $value->item_id ?>" class="btn mb-1 btn-sm btn-outline-danger"><i class="fa fa-stop"></i></button>
                                                        <?php } elseif($value->deprecated && !$value->deleted) { ?>
                                                        <button data-function="delete" data-label="restore" data-msg="Are you sure you want to restore this endpoint to active state?" title="Set this endpoint as active" data-item="<?= $value->item_id ?>" class="btn mb-1 btn-sm btn-outline-warning"><i class="fa fa-play"></i></button>
                                                        <button data-function="delete" data-label="delete" data-msg="Are you want you want to permanently delete this endpoint?" title="Delete endpoint" data-item="<?= $value->item_id ?>" class="btn mb-1 btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    Endpoint Content &nbsp;
                                    <span class="float-right">
                                        <a href="<?= $baseUrl ?>endpoints" class="btn btn-sm hidden refresh-button btn-outline-success"><i class="fa fa-random"></i> Refresh</a>
                                        <button class="btn btn-outline-primary btn-sm cursor" type="add"><i class="fa fa-plus"></i> Add New</button>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="endpoint-content">
                                    <form action="<?= $baseUrl ?>endpoints/save" autocomplete="Off" class="endpoint-form" method="POST">
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
                                                    <input type="hidden" readonly hidden value="<?= $end_id ?>" name="endpoint_id" id="endpoint_id" class="form-control">
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
</div>

<?php require "foottags.php"; ?>