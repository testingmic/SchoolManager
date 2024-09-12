<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		Helpers
 * @subpackage	Page Modals Helper Functions
 * @category	Core Functions
 * @author		Emmallex Technologies Dev Team
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/** 
 * Form loader placeholder 
 * 
 * @return String
 */
function form_loader($position = "absolute") {
  return '
    <div class="form-content-loader" style="display: none; position: '.$position.'">
        <div class="offline-content text-center">
            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
        </div>
    </div>';
}

// set the login window
function page_load_error($title = null, $content = null) {
    
$appName = config_item("site_name");
$baseUrl = config_item("base_url");

$html = "
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'>
  <link rel='stylesheet' href='{$baseUrl}assets/css/app.min.css'>
  <link rel='shortcut icon' type='image/x-icon' href='{$baseUrl}assets/img/favicon.ico' />
  <title>{$title} - {$appName}</title>
  <style>
  .bg {
    background-image: url('{$baseUrl}assets/img/background_2.jpg');
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
  }
  </style>
</head>
<body class='bg'>
    <style>
        .wrapper {
            border: solid 1px #ccc;
            width: 90%;
            margin: auto auto;
            background: #fbfbfb;
            padding: 30px;
        }
    </style>
    <div class='wrapper mt-4'>
        <div align='center'>
            <h1 class='text-danger'>{$appName}</h1>
            <h1>{$title}</h1>
            <p class='font-17'>{$content}</p>
        </div>
    </div>
</body>
</html>";

    return $html;
}

/** 
 * Form loader placeholder 
 * 
 * @return String
 */
function pageoverlay() {
  return '
    <div class="pageoverlay" style="display: none; position: fixed">
        <div class="offline-content text-center">
            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
        </div>
    </div>';
}
/** 
 * Form loader placeholder 
 * 
 * @return String
 */
function absolute_loader() {
  return '
    <div class="absolute-content-loader" style="display: none; position: absolute">
        <div class="offline-content text-center">
            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
        </div>
    </div>';
}

/** 
 * Form overlay
 * 
 * @return String
 */
function form_overlay() {
  return '
    <div class="form-overlay-cover" style="display: none; position: fixed">
        <div class="offline-content text-center"></div>
    </div>';
}

/**
 * Upload overlay
 * 
 * @return String
 */
function upload_overlay() {
  return '
    <div class="upload-overlay-cover" style="display: none; position: fixed">
        <div class="upload-content text-center"></div>
    </div>';
}

/**
 * This is the modal to show and used for loading the ajax forms
 * 
 * @param Bool $auto_close_modal
 * 
 * @return String
 */
function ajax_forms_modal($auto_close_modal) {

    $html = "
    <div class=\"auto_close_modal\" id=\"auto_close_modal\" data-value=\"{$auto_close_modal}\"></div>
    <div class=\"modal fade modal-dialog-right right\" id=\"formsModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-centered modal-lg\" style=\"width:100%;height:100%;\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\"></h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <input hidden class=\"ajax-form-loaded\" value=\"0\" data-form=\"none\">
                <div class=\"modal-body\"></div>
            </div>
        </div>
    </div>";

    return $html;
}


/**
 * This is the modal to show for replies to a specific resource
 * 
 * @param Bool $auto_close_modal
 * 
 * @return String
 */
function replies_modal($auto_close_modal, $modal_id = "repliesModal") {
    $html = "<div class=\"modal fade modal-dialog-right right\" id=\"{$modal_id}\" ".(!$auto_close_modal ? null :  'data-backdrop="static" data-keyboard="false"').">
        <div class=\"modal-dialog modal-dialog-centered modal-md\" style=\"width:100%;height:100%;\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\"></h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <input hidden class=\"ajax-replies-loaded\" value=\"0\" data-form=\"none\">
                <div class=\"slim-scroll modal-body\" data-last_reply_id=\"\" data-scrolling=\"false\" style=\"height:100%; max-height:100%; overflow-y:auto; overflow-x:hidden\"></div>
            </div>
        </div>
    </div>";
    return $html;
}


/**
 * Modal for general purpose
 * 
 * @return String
 */
function general_modal() {
    return "<div class=\"modal fade\" id=\"generalModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-top modal-md\" style=\"width:100%;\" role=\"document\">
            <div class=\"modal-content\">
                ".form_loader()."
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\"></h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <input hidden class=\"ajax-replies-loaded\" value=\"0\" data-form=\"none\">
                <div class=\"modal-body\" data-scrolling=\"false\" style=\"text-align:left\"></div>
                <div class=\"modal-footer\">
                    <button data-resource=\"\" class=\"btn btn-outline-success\" data-record-id=\"\">Yes! Proceed</button>
                    <button class=\"btn btn-outline-danger\" data-dismiss=\"modal\">Cancel</button>
                </div>
            </div>
        </div>
    </div>";
}


/**
 * Save ajax form notification
 * 
 * @return String
 */
function save_form_data() {
    return "<div class=\"modal fade\" id=\"saveGeneralFormModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-top modal-md\" style=\"width:100%;\" role=\"document\">
            <div class=\"modal-content\">
                ".form_loader()."
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\"></h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <input hidden class=\"ajax-replies-loaded\" value=\"0\" data-form=\"none\">
                <div class=\"modal-body\" data-scrolling=\"false\" style=\"text-align:left\"></div>
                <div class=\"modal-footer\">
                    <button data-resource=\"\" class=\"btn btn-outline-success\">Yes! Proceed</button>
                    <button class=\"btn btn-outline-danger\" data-dismiss=\"modal\">Cancel</button>
                </div>
            </div>
        </div>
    </div>";
}

/**
 * Page not found html
 * 
 * @return String
 */
function page_not_found($request = "not_found", $string = "The resource you trying to access for could not be found.") {
    global $baseUrl, $_SERVER;

    $notFound = (bool) ($request == "not_found");
    $message = $notFound ? $string : "You don't have permission to access the requested object. It is either read-protected or not readable on this server.";
    $title = $notFound ? "404" : "403";

    return '
    <section class="section">
        <div class="container bg-white border-2px mt-3">
            <div class="page-error">
            <div class="page-inner">
                <h1 class="text-warning">'.$title.'</h1>
                <div class="page-description text-danger">'.$message.'</div>
                <div class="page-search">
                <form method="GET" autocomplete="Off" class="ajaxform" action="'.$baseUrl.'api/search/list">
                    <div class="form-group floating-addon floating-addon-not-append">
                    '.($notFound ? '
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fas fa-search"></i>
                            </div>
                            </div>
                            <input type="text" name="q" class="form-control" placeholder="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-lg" type="submit">Search</button>
                            </div>
                        </div>' : ''
                    ).'
                    </div>
                </form>
                <div class="mt-3">
                    <span class="user_name font-13" onclick="return load(\'dashboard\');"><i class="fa fa-home"></i> Back to Home</span> | 
                    <span class="user_name font-13" onclick="return loadPage(\''.$_SERVER["REQUEST_URI"].'\');"><i class="fa fa-redo-alt"></i>  Reload Page</span> |
                    <span class="user_name font-13" onclick="javascript:history.back()" class="anchor"><i class="fa fa-arrow-left"></i> Go Back</span>
                </div>
                </div>
            </div>
            </div>
        </div>
    </section>';
}

/**
 * Page not found html
 * 
 * @param StdClass $clientData
 * 
 * @return String
 */
function propagating_data($clientData) {
    return '
    <section class="section">
        <div class="container">
            <div class="mt-3 text-center font-18">
                <div class="page-inner">
                    <div class="page-description">
                        <h3>Propagating Data</h3>
                    </div>
                    <div class="text-danger">
                        Please wait while the process completes <i class="fa fa-spin fa-spinner"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>';
}

/**
 * Session Logged Out
 * 
 * @return String
 */
function session_logout() {
    global $baseUrl, $_SERVER;
    return '
        <div class="row">
            <div class="col-lg-3 col-md-3"></div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-header">
                    <h4>Session Expired</h4>
                    </div>
                    <div class="card-body">
                    <div class="empty-state" data-height="400">
                        <div class="empty-state-icon bg-danger">
                        <i class="fas fa-lock"></i>
                        </div>
                        <h2>Current Session Expired</h2>
                        <p class="lead">
                            Sorry! You have been logged out of the system due to inactivity.
                        </p>
                        <a href="'.$baseUrl.'login" class="btn anchor btn-warning mt-4">Login</a>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    ';
}

/**
 * Change Default Password
 * 
 * @return String
 */
function changed_password($title = null, $timer = null) {
    global $baseUrl, $defaultUser;
    return '
        <div class="row">
            <div class="col-lg-3 col-md-3"></div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="p-2 border-bottom border-3px text-center text-uppercase"><h4>'.$title.'</h4></div>
                    <div class="card-body">
                        <div class="empty-state pt-0" data-height="400">
                            <div class="empty-state-icon bg-danger"><i class="fas fa-lock"></i></div>
                            <form autocomplete="Off" method="POST" class="ajaxform" id="ajaxform" action="'.$baseUrl.'api/auth/change_password">
                                <div>
                                    <h5 class="border-bottom pb-2 pt-3">Complete the form below to change the default password.</h5>
                                </div>
                                <div class="row" align="left">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Password <span class="required">*</span></label>
                                            <input title="Set the new password." autocomplete="Off" type="password" name="password_1" id="password_1" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Confirm Password <span class="required">*</span></label>
                                            <input title="Confirm the new password." autocomplete="Off" type="password" name="password_2" id="password_2" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-12" align="right">
                                        <input type="hidden" name="user_id" id="user_id" value="'.$defaultUser->user_id.'">
                                        <button class="btn btn-outline-success" type="submit"><i class="fa fa-lock"></i> Change Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
}

/**
 * Account Expired or Suspended
 * 
 * @return String
 */
function access_denied($state = null, $timer = null) {
    global $baseUrl, $_SERVER;
    return '
        <div class="row">
            <div class="col-lg-3 col-md-3"></div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="p-2 border-bottom border-3px text-center text-uppercase"><h4>'.$state.' Account</h4></div>
                    <div class="card-body">
                        <div class="empty-state" data-height="400">
                            <div class="empty-state-icon bg-danger"><i class="fas fa-lock"></i></div>
                            <h2>Your Account '.($state == "Expired" ? "Expired on {$timer}" : "is {$state}").'</h2>
                            <p class="lead">
                                Sorry! You have been denied access to the system
                                because your account has '.($state == "Suspended" ? "been {$state}" : $state).'
                            </p>
                            <button onclick="return loadPage(\''.$baseUrl.'support\');" class="btn anchor btn-warning mt-4">Visit Support Section</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
}

/**
 * Global Sample Page for Content Display
 *
 * @return String
 **/
function notification_modal($title = null, $caption = null, $url_link = null) {
    
    // global variable data
    global $defaultClientData, $baseUrl;

    // return the content
    return '
        <div class="row">
            <div class="col-lg-3 col-md-3"></div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="p-2 border-bottom border-3px text-center text-uppercase"><h4>'.$title.'</h4></div>
                    <div class="card-body">
                        <div class="empty-state" data-height="400">
                            <div class="empty-state-icon bg-danger"><i class="fas fa-bell"></i></div>
                            <p class="mt-3 font-17 no-weight">'.$caption.'</p> '.$url_link.'
                        </div>
                    </div>
                </div>
            </div>
        </div>';
}

/**
 * Save Information Popup
 * 
 * 
 * @return String
 */
function ajax_form_button() {
    $html = "<div class=\"modal fade modal-dialog-right right\" id=\"ajaxFormSubmitModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-top modal-md\" style=\"width:100%;height:100%;\" role=\"document\">
            <div class=\"modal-content\">
                ".form_loader()."
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Submit Form</h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <input hidden class=\"ajax-replies-loaded\" value=\"0\" data-form=\"none\">
                <div class=\"modal-body\" data-scrolling=\"false\" style=\"text-align:left\">
                    Are you sure you want to submit the form?
                </div>
                <div class=\"modal-footer\">
                    <button class=\"btn btn-outline-success\">Yes! Submit</button>
                    <button class=\"btn btn-outline-danger\" data-dismiss=\"modal\">Cancel</button>
                </div>
            </div>
        </div>
    </div>";
    return $html;
}

/** Add new button */
function add_new_item($item_id = null) {
	global $accessObject;
	$buttons = "
    <div class=\"btn-group dropdown d-inline\">
		<button type=\"button\" class=\"btn btn-outline-info btn-icon-text dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
			Add New
		</button>
		<div class=\"dropdown-menu\" style=\"width:160px\" data-function=\"add-item-module\">
			<a href=\"#\" onclick=\"return load_quick_form('course_link_upload','{$item_id}');\" class=\"dropdown-item btn-sm\" type=\"button\">New Link</a>
            <!--<a href=\"#\" onclick=\"return load_quick_form('course_file_upload','{$item_id}');\" class=\"dropdown-item btn-sm\" type=\"button\">New File</a>-->
	    </div>
    </div>";

	return $buttons;
}


/**
 * Leave comment container
 * 
 * @param Strig $resource           This is the resource name.
 * @param String $recordId          The unique id of the record on which the comment is been shared on
 * @param String $comment           The default comment to show on the form.
 * 
 * @return String
 */
function leave_comments_builder($resource, $recordId, $upload = true, $comment = null) {
    // global variable
    global $userData;

    // create a new object of the forms class
    $formsObj = load_class("forms", "controllers");
    
    /** Set parameters for the data to attach */
    $form_params = (object) [
        "module" => "{$resource}_{$recordId}",
        "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg"]),
        "userData" => $userData,
        "item_id" => $recordId
    ];

    // create the html form
    $html = "
        <style>
        .leave-comment-wrapper trix-editor {
            min-height: 100px;
            max-height: 100px;
        }
        </style>
        <div class=\"leave-comment-wrapper\" data-id=\"{$recordId}\">
            <div class='form-content-loader' style='display: none; position: absolute;'>
                <div class='offline-content text-center'>
                    <p><i class='fa fa-spin fa-spinner fa-3x'></i></p>
                </div>
            </div>
            ".absolute_loader()."
            <div class=\"form-group mt-1\">
                <label for=\"leave_comment_content\" title=\"Click to display comment form\" class=\"cursor\">
                    ( <i class=\"fa fa-comments\"></i> <strong><span data-id=\"{$recordId}\" data-record=\"comments_count\">0</span> comments</strong> ) ".(!empty($comment) ? $comment : "Leave a comment below")." <small class=\"text-danger\">(cannot be modified once posted)</small>
                </label>
            </div>
            <div class=\"hidden_\" id=\"leave-comment-content\">
                <div class=\"form-group mb-2\">
                    <trix-editor class=\"slim-scroll\" id=\"leave_comment_content\" name=\"leave_comment_content\"></trix-editor>
                </div>
                <div class=\"form-group mt-0 text-right\">
                    <button type=\"button\" onclick=\"return share_Comment('{$resource}', '{$recordId}')\" class=\"btn share-comment btn-sm btn-outline-success\">Post Comment <i class=\"fa fa-angle-double-right\"></i></button>
                </div>
                ".($upload ? "<div>{$formsObj->comments_form_attachment_placeholder($form_params)}</div>" : "")."
            </div>
        </div>";

    return $html;
}

function quick_add_student() {
    global $myClass, $defaultClientId;

    $response = '
    <div class="modal fade" id="quickStudentAdd" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-top modal-md" style="width:100%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Student</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <input hidden class="ajax-replies-loaded" value="0" data-form="none">
                <div class="modal-body" style="text-align:left">
                    <div class="form-content-loader" style="display: none; position: absolute">
                        <div class="offline-content text-center">
                            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Fullname <span class="required">*</span></label>
                        <input type="text" class="form-control" name="fullname">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="text" name="date_of_birth" id="date_of_birth" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="gender">Gender <span class="required">*</span></label>
                                <select data-width="100%" name="gender" id="gender" class="form-control selectpicker">
                                    <option value="null">Select Gender</option>';
                                    foreach($myClass->pushQuery("*", "users_gender") as $each) {
                                        $response .= "<option value=\"{$each->name}\">{$each->name}</option>";                            
                                    }
                            $response .= '
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="residence">Place of Residence</label>
                                <input type="text" name="residence" id="residence" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label for="class_id">Class <span class="required">*</span></label>
                                <select data-width="100%" name="class_id" id="class_id" class="form-control selectpicker">
                                    <option value="null">Select Student Class</option>';
                                    foreach($myClass->pushQuery("id, name", "classes", "status='1' AND client_id='{$defaultClientId}'") as $each) {
                                        $response .= "<option value=\"{$each->id}\">{$each->name}</option>";                            
                                    }
                                $response .= '</select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-danger" data-dismiss="modal">Cancel</button>
                    <button onclick="return quick_Add_Student()" class="btn btn-outline-success">Save</button>
                </div>
            </div>
        </div>
    </div>';

    return $response;
}

/**
 * Change Status Modal
 * 
 * @used
 * 
 * @return String
 */
function change_status_modal($user_id = null, $status = "Active") {
    
    // global variable
    global $baseUrl, $myClass;

    $html = '
    <div data-backdrop="static" data-keyboard="false" class="modal fade" id="change_user_Status">
        <form action="'.$baseUrl.'api/users/change_status" class="ajax-data-form" id="change_status_modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change User Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Status <span class="required">*</span></label>
                                    <select name="user_status" class="selectpicker" data-width="100%" id="user_status">';
                                    foreach($myClass->student_statuses as $_status) {
                                        $html .= "<option ".($status == $_status ? "selected='selected'" : null)." value='{$_status}'>{$_status}</option>";
                                    }
                                    $html .= '
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Additional Message</label>
                                    <textarea placeholder="" maxlength="255" name="description" id="description" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer p-0">
                            <input type="hidden" readonly name="user_id[]" value="'.$user_id.'">
                            <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button data-form_id="change_status_modal" type="button-submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>';

    return $html;
}

/**
 * Form manager select options
 */
function form_manager_options() {
    $html = "
        <div>
            <div class=\"btn-group dropdown\">
                <button type=\"button\" class=\"btn btn-primary dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                    <i style=\"font-size:10px;\" class=\"fa fa-plus\"></i> Add New
                </button>
                <div class=\"dropdown-menu\" data-function=\"jsform-module\">
                    <a onclic=\"return false;\" class=\"dropdown-item\" data-module=\"jsform\" data-field=\"input\" href=\"#\">Text Input Field</a>
                    <a onclic=\"return false;\" class=\"dropdown-item\" data-module=\"jsform\" data-field=\"date\" href=\"#\">Date Input Field</a>
                    <a onclic=\"return false;\" class=\"dropdown-item\" data-module=\"jsform\" data-field=\"email\" href=\"#\">Email Field</a>
                    <a onclic=\"return false;\" class=\"dropdown-item\" data-module=\"jsform\" data-field=\"textarea\" href=\"#\">Textarea</a>
                    <a onclic=\"return false;\" class=\"dropdown-item\" data-module=\"jsform\" data-field=\"select\" href=\"#\">Select Options</a>
                </div>
            </div>
        </div>";

    return $html;
}

/**
 * Select Field Popup
 * 
 * 
 * @return String
 */
function select_field_modal() {
    $html = "<div class=\"modal fade\" id=\"selectFieldModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-top modal-md\" style=\"width:100%;\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Update Select</h5>
                    <button type=\"button\" class=\"add-row btn btn-outline-primary btn-sm\">Add Option</span></button>
                </div>
                <div class=\"modal-body\" data-scrolling=\"false\" style=\"text-align:left\">
                    <table class=\"table table-bordered\">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <!--<th>Value</th>-->
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class=\"modal-footer\">
                    <button class=\"btn btn-outline-success\">Save</button>
                </div>
            </div>
        </div>
    </div>";
    return $html;
}