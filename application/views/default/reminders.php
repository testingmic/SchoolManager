<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $myschoolgh, $defaultUser, $accessObject, $defaultAcademics;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$filters = (object) $_GET;
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Reminders";
$response->title = $pageTitle;

// not found
if(!$accessObject->hasAccess("send", "communication")) {
    // end the query here
    $response->html = page_not_found("permission_denied");

    // echo the response
    echo json_encode($response);
    exit;
}

// add the scripts to load
$response->scripts = ["assets/js/communication.js", "assets/js/reminders.js"];

// set the parameters
$route = "reminder";
$params = (object) ["clientId" => $clientId, "preferences" => $defaultUser->appPrefs];

// set the parent menu
$response->parent_menu = "fees-payment";

// get the data
$class_array_list = $myClass->pushQuery("name, id, item_id", "classes", "client_id='{$params->clientId}' AND status='1'");
        
// get the list of all other users
$other_users_list = $myClass->pushQuery("
    a.name, a.user_type, a.unique_id, a.item_id, a.phone_number, 
        a.class_id, a.image, (
            SELECT sum(b.balance) FROM fees_payments b 
            WHERE b.student_id = a.item_id AND b.academic_term = '{$defaultAcademics->academic_term}'
                AND b.academic_year = '{$defaultAcademics->academic_year}'
        ) AS student_debt, ar.arrears_total AS arrears", 
    "users a LEFT JOIN users_arrears ar ON ar.student_id = a.item_id", 
    "a.client_id='{$params->clientId}' AND a.user_status IN {$myClass->inList($myClass->student_statuses)} AND a.user_type = 'student'
        AND a.status='1' ORDER BY a.name LIMIT {$myClass->global_limit}");

// get the list of only students
$users_array_list = [];

// get the users list
foreach($other_users_list as $user) {
    $user->class_id = (int) $user->class_id;
    $user->student_debt = (float) $user->student_debt;
    $user->arrears = (float) $user->arrears;
    $users_array_list[] = $user;
}

// append to the array list
$response->array_stream["users_array_list"] = $users_array_list;
$response->array_stream["class_array_list"] = $class_array_list;

// get the smsemail information
$settings = $myClass->pushQuery("*", "smsemail_balance", "client_id='{$clientId}' LIMIT 1");
$settings = !empty($settings) ? $settings[0] : [];
$response->array_stream["smsemail_settings"] = $settings;

// set the classes list
$classes_list = "";
foreach($class_array_list as $class) {
    $classes_list .= "<option ".(!empty($filters->cid) && ($class->id === $filters->cid) ? "selected" : null)." data-class_id='{$class->item_id}' value='{$class->id}'>".strtoupper($class->name)."</option>";
}
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
                <span class="p-1 mb-2 font-20 bg-amber badge badge-warning p-2" id="sms_balance">'.($settings->sms_balance ?? 0).' SMS Units</span>
                <button onclick="return topup_sms()" class="btn mb-2 btn-success"><i class="fa fa-database"></i> Top Up</button>
            </div>
        </div>
        '.(confirm_url_id(1, "send") ?
        '<form method="post" action="'.$baseUrl.'api/communication/send_reminder" class="form_send_reminder" id="send_form_'.$route.'" data-route="'.$route.'">
            <div class="row send_smsemail">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <input disabled type="hidden" name="myemail_address" value="'.$defaultUser->email.'">
                            <div class="row">
                                <div class="col-3">
                                    <label>Reminder Type:</label>
                                    <select data-route="'.$route.'" type="text" id="recipient_type" data-width="100%" class="form-control selectpicker">
                                        <option selected value="fees">Fees</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="class_select_'.$route.'">
                                    <div class="form-group mb-1">
                                        <label>Class <span class="required">*</span></label>
                                        <select data-selectors="'.$route.'" data-route="'.$route.'" name="class_id" class="form-control selectpicker" data-width="100%">
                                            <option value="">Select Select to Generate</option>
                                            '.$classes_list.'
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group mb-1">
                                        <label>.</label>
                                        '.(!empty($filters->cid) ? "<input hidden id='preload_students_list' value='".($filters->sid ?? null)."'>" : null).'
                                        <button type="button" id="generate_list_button" onclick="return generate_list(\''.$route.'\')" class="width-150 btn-block btn btn-outline-primary">Generate <i class="fa fa-download"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header text-uppercase">Students List</div>
                        <div class="card-body">
                            <table border="1" width="100%" class="table mb-1 table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="10%">#</th>
                                        <th width="70%">FULLNAME</th>
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
                                <label>Reminder Subject <span class="required">*</span></label>
                                <input value="Fees Payment" type="text" name="reminder_subject" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Mode of Message <span class="required">*</span></label>
                                <select data-width="100%" data-route="'.$route.'" multiple name="send_mode[]" class="form-control selectpicker">
                                    <option selected value="sms">SMS</option>
                                    <option selected value="email">Email</option>
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label>Message to Attached <span class="required">*</span></label>
                                <textarea data-route="'.$route.'" maxlength="480" id="reminder_textarea" name="message" style="height:200px" class="form-control"></textarea>
                                <div class="text-right alert-success p-1"> 
                                    <span class="remaining_count p-1">'.$myClass->sms_text_count.' characters remaining</span>
                                    <span id="messages">0 message</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="mb-xs">
                                    <div class="form-group">
                                        <div class="checkbox-replace">
                                            Schedule Reminder <input class="cursor" style="height:20px;width:20px;" data-route="'.$route.'" type="checkbox" name="send_later">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row p-0">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label">Date</label>
                                        <div class="input-group">
                                            <input data-route="'.$route.'" disabled data-mindate="'.date("Y-m-d").'" data-maxdate="'.date("Y-m-d", strtotime("+3 months")).'" type="text" class="form-control datepicker" name="schedule_date">
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
                                <input type="hidden" data-route="'.$route.'" readonly id="type" value="'.$route.'">
                                <div class="col-md-6" align="right">
                                    <button class="btn btn-outline-success" type="submit"><i class="fa fa-mail-bulk"></i> Send Message</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>' : ''
        ).'
    </section>';
    
// print out the response
echo json_encode($response);
?>