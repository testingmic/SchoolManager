<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $defaultAcademics;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Exeats Log";
$response->title = $pageTitle;
$response->timer = 0;

// end query if the user has no permissions
if(!in_array("exeats", $clientFeatures)) {
    // permission denied information
    $response->html = page_not_found("feature_disabled");
    echo json_encode($response);
    exit;
}

// if the client information is not empty
if(!empty($session->clientId)) {

    // convert to lowercase
    $client_id = strtoupper($session->clientId);

    // create new event class
    $data = (object) [];
    
    // set the parameters
    $params = (object) [
        "baseUrl" => $baseUrl,
        "clientId" => $clientId,
        "userId" => $session->userId
    ];

    $hasExeatAdd = $accessObject->hasAccess("add", "exeats");
    $hasExeatDelete = $accessObject->hasAccess("delete", "exeats");
    $hasExeatUpdate = $accessObject->hasAccess("update", "exeats");

    // append the permissions to the default user object
    $defaultUser->hasExeatDelete = $hasExeatDelete;
    $defaultUser->hasExeatUpdate = $hasExeatUpdate;

    $student_param = (object) [
        "clientId" => $clientId,
        "user_type" => "student",
        "userId" => $session->userId, 
        "academic_year" => $defaultAcademics->academic_year,
        "academic_term" => $defaultAcademics->academic_term,
        "client_data" => $defaultUser->client,
        "_user_type" => $defaultUser->user_type,
        "minified" => true,
        "quick_list" => true,
    ];

    // get the list of students
    $userClass = load_class("users", "controllers");
    $userList = $userClass->quick_list($student_param)['data'] ?? [];

    // load the Exeats types
    $exeatClass = load_class("exeats", "controllers");

    // get the list of exeat types
    $exeatsList = "";

    // load the scripts
    $response->scripts = ["assets/js/exeats.js"];

    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-book"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'exeats">Exeats Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="text-right mb-2">
                    <a class="btn btn-sm btn-outline-primary"  onclick="return create_exeat();" href="#"><i class="fa fa-plus"></i> Add Exeat</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Student</th>
                                        <th>Exeat Type</th>
                                        <th>Departure & Return Date</th>
                                        <th>Pickup By</th>
                                        <th>Guardian Contact</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$exeatsList.'</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div data-backdrop="static" data-keyboard="false" class="modal fade" id="exeatModal">
        <form autocomplete="Off" action="'.$baseUrl.'api/exeats/create" method="POST" class="ajax-data-form" id="ajax-data-form-content">
            <div class="modal-lg modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Allowance / Deduction Types</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">Student</label>
                                    <select name="student_id" id="student_id" class="form-control selectpicker" data-width="100%">
                                        <option value="">Select Student</option>
                                        '.implode("", array_map(function($each) {
                                            return "<option value=\"{$each->user_id}\">{$each->name}</option>";
                                        }, $userList ?? [])).'
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exeat_type">Exeat Type</label>
                                    <select name="exeat_type" id="exeat_type" class="form-control selectpicker" data-width="100%">
                                        <option value="">Select Exeat Type</option>
                                        '.implode("", array_map(function($each) {
                                            return "<option value=\"{$each}\">{$each}</option>";
                                        }, ['Day', 'Weekend', 'Emergency'])).'
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="departure_date">Departure Date & Time <span class="required">*</span></label>
                                    <input type="text" data-maxdate="'.date("Y-m-d", strtotime("+3 week")).'" maxlength="100" placeholder="Type departure date & time" name="departure_date" id="departure_date" class="form-control datepicker">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="return_date">Return Date & Time</label>
                                    <input type="text" data-maxdate="'.date("Y-m-d", strtotime("+3 week")).'" maxlength="12" placeholder="Type return date & time" name="return_date" id="return_date" class="form-control datepicker">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pickup_by">Pickup By</label>
                                    <select name="pickup_by" id="pickup_by" class="form-control selectpicker" data-width="100%">
                                        <option value="">Select Pickup By</option>
                                        '.implode("", array_map(function($each) {
                                            return "<option value=\"{$each}\">{$each}</option>";
                                        }, ['Self', 'Guardian', 'Other'])).'
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="guardian_contact">Guardian Contact</label>
                                    <input type="text" placeholder="Type guardian contact" name="guardian_contact" id="guardian_contact" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="reason">Reason</label>
                                    <textarea placeholder="" maxlength="255" name="reason" id="reason" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control selectpicker" data-width="100%">
                                        <option value="">Select Status</option>
                                        '.implode("", array_map(function($each) {
                                            return "<option value=\"{$each}\">{$each}</option>";
                                        }, ['Pending', 'Approved', 'Rejected'])).'
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer p-0">
                            <input type="hidden" name="exeat_id">
                            <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button data-form_id="ajax-data-form-content" type="button-submit" class="btn btn-primary">Create Exeat</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>';
}

echo json_encode($response);