<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser, $accessObject;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Bulk SMS";
$response->title = "{$pageTitle} : {$appName}";

// not found
if(!$accessObject->hasAccess("send", "communication")) {
    // end the query here
    $response->html = page_not_found("permission_denied");

    // echo the response
    echo json_encode($response);
    exit;

}

// add the scripts to load
$response->scripts = ["assets/js/communication.js"];

// set the parameters
$route = "sms";
$params = (object) ["clientId" => $clientId, "preferences" => $defaultUser->appPrefs];

// get the data
$templates_array = $myClass->pushQuery("name, id, item_id, type, message", "smsemail_templates", "client_id='{$clientId}' AND status='1'");
$class_array_list = $myClass->pushQuery("name, id, item_id", "classes", "client_id='{$params->clientId}' AND status='1'");
        
// get the list of all other users
$other_users_list = $myClass->pushQuery("name, user_type, unique_id, item_id, phone_number, class_id", "users", "client_id='{$params->clientId}' AND user_status='Active' AND status='1' ORDER BY name LIMIT {$myClass->global_limit}");

// get the list of only students
$users_array_list = [];

// get the users list
foreach($other_users_list as $user) {
    $users_array_list[] = $user;
}

// append to the array list
$response->array_stream["templates_array"] = $templates_array;
$response->array_stream["users_array_list"] = $users_array_list;
$response->array_stream["class_array_list"] = $class_array_list;

// get the smsemail information
$settings = $myClass->pushQuery("*", "smsemail_balance", "client_id='{$clientId}' LIMIT 1");
$settings = !empty($settings) ? $settings[0] : [];

$sms_packages = $myClass->pushQuery("*", "sms_packages", "1");

$response->array_stream["smsemail_settings"] = $settings;
$response->array_stream["sms_packages"] = $sms_packages;

// set the html
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-envelope-open-text"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div></div>
            <div>
                <span class="p-1 mb-2 font-20 bg-amber" id="sms_balance">'.($settings->sms_balance ?? 0).' SMS Units</span>
                <button onclick="return topup_sms()" class="btn mb-2 btn-success"><i class="fa fa-database"></i> Top Up</button>
            </div>
        </div>
        <form method="post" action="'.$baseUrl.'api/communication/send_message" class="form_send_message" id="send_form_'.$route.'" data-route="'.$route.'">
            <div class="row send_smsemail">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <input disabled type="hidden" name="myemail_address" value="'.$defaultUser->email.'">
                            <div class="row">
                                <div class="col-3">
                                    <label>Type:</label>
                                    <select data-route="'.$route.'" type="text" name="recipient_type" data-width="100%" class="form-control selectpicker custom_select">
                                        <option value="">Select Type</option>
                                        <option value="group">Group</option>
                                        <option value="individual">Individual</option>
                                        <option value="class">Class</option>
                                    </select>
                                </div>
                                <div class="col-6 hidden" id="role_group_select_'.$route.'">
                                    <div class="form-group mb-1">
                                        <label>Role <span class="required">*</span></label>
                                        <select data-selectors="'.$route.'" data-route="'.$route.'" name="role_group[]" class="form-control selectpicker" multiple="true" data-width="100%">
                                            <option value="">Select</option>
                                            <option value="admin">Admin</option>
                                            <option value="teacher">Teachers</option>
                                            <option value="student">Students</option>
                                            <option value="accountant">Accountants</option>
                                            <option value="employee">Employees</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4 hidden" id="individual_select_'.$route.'">
                                    <div class="form-group mb-1">
                                        <label>Role <span class="required">*</span></label>
                                        <select data-selectors="'.$route.'" data-route="'.$route.'" name="role_id" class="form-control selectpicker" data-width="100%">
                                            <option value="">Select</option>
                                            <option value="admin">Admins</option>
                                            <option value="teacher">Teachers</option>
                                            <option value="student">Students</option>
                                            <option value="accountant">Accountants</option>
                                            <option value="employee">Employees</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 hidden" id="class_select_'.$route.'">
                                    <div class="form-group mb-1">
                                        <label>Class <span class="required">*</span></label>
                                        <select data-selectors="'.$route.'" data-route="'.$route.'" name="class_id" class="form-control selectpicker" data-width="100%">
                                            <option value="">Select</option>';
                                            foreach($class_array_list as $class) {
                                                $response->html .= "<option data-class_id='{$class->item_id}' value='{$class->id}'>{$class->name}</option>";
                                            }
                                    $response->html .= '</select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-1">
                                        <label>.</label>
                                        <button type="button" id="generate_list_button" onclick="return generate_list(\''.$route.'\')" class="width-150 btn-block btn btn-outline-primary hidden">Generate <i class="fa fa-download"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header text-uppercase">Recipients List</div>
                        <div class="card-body">
                            <table border="1" width="100%" class="table mb-1 table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="10%">#</th>
                                        <th width="50%">FULLNAME</th>
                                        <th width="25%">CONTACT</th>
                                        <td style="border:none;background:rgba(0,0,0,0.04)" align="center">
                                            <input disabled style="height:20px;width:20px;" id="select_all" type="checkbox" class="cursor">
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                            <div style="overflow-y:auto;max-height:500px;">
                                <table border="1" width="100%" class="table pt-0 mt-0 table_list table-bordered table-striped">
                                    <tbody class="receipients_list">
                                        <tr>
                                            <td align="center" colspan="4">No receipient selected at the moment.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-body" id="message_form">
                            <div class="form-content-loader" style="display: none; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>SMS Campaign Name <span class="required">*</span></label>
                                <input type="text" name="campaign_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Template</label>
                                <select type="text" data-route="'.$route.'" data-width="100%" name="template_id" class="form-control selectpicker">
                                    <option value="">Select Template</option>';
                                    foreach($templates_array as $key => $template) {
                                        if($template->type == $route) {
                                            $response->html .= "<option value='{$template->item_id}'>{$template->name}</option>";
                                        }
                                    }
                            $response->html .= '
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label>Message <span class="required">*</span></label>
                                <textarea data-route="'.$route.'" maxlength="480" name="message" style="height:200px" class="form-control"></textarea>
                                <div class="text-right alert-success p-1"> 
                                    <span class="remaining_count p-1">'.$myClass->sms_text_count.' characters remaining</span>
                                    <span id="messages">0 message</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="mb-xs">
                                    <div class="form-group">
                                        <div class="checkbox-replace">
                                            Schedule Message <input class="cursor" style="height:20px;width:20px;" data-route="'.$route.'" type="checkbox" name="send_later">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row p-0">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label">Date</label>
                                        <div class="input-group">
                                            <input data-route="'.$route.'" disabled data-maxdate="'.date("Y-m-d", strtotime("+3 months")).'" type="text" class="form-control datepicker" name="schedule_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Time</label>
                                        <div class="input-group">
                                            <input data-route="'.$route.'" type="time" disabled name="schedule_time" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6" align="left">
                                    <button onclick="return cancel_sending_form()" class="btn btn-dark" type="button"><i class="fa fa-ban"></i> Cancel</button>
                                </div>
                                <input type="hidden" data-route="'.$route.'" readonly name="type" value="'.$route.'">
                                <div class="col-md-6" align="right">
                                    <button class="btn btn-outline-success" type="submit"><i class="fa fa-mail-bulk"></i> Send Message</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>';
    
// print out the response
echo json_encode($response);
?>