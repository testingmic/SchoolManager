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
 * Color code picker
 * 
 * @return array
 */
function color_code_picker($selected = null, $colorsOnly = false) {
    $colors = [
        "black" => "Black",
        "red" => "Red",
        "green" => "Green",
        "blue" => "Blue",
        "yellow" => "Yellow",
        "purple" => "Purple",
        "orange" => "Orange",
        "pink" => "Pink",
    ];
    if($colorsOnly) {
       return array_keys($colors);
    }
    $html = "<option value=\"\">Select Color</option>";
    foreach($colors as $key => $value) {
        $html .= "<option ".($selected == $key ? "selected" : null)." value=\"{$key}\">{$value}</option>";
    }
    return $html;
}

/**
 * No record found
 * 
 * @param String $title
 * @param String $caption
 * @param String $url_link
 * @param String $record
 * 
 * @return String
 */
function no_record_found($title = null, $caption = null, $url_link = null, $record = null, $no_button = false) {
    return "
    <div id='no_record_found_container' class='backdrop-blur-xl ".($no_button ? "mt-2" : null)." backdrop-saturate-150 rounded-2xl border shadow-[0_0_1px_1px_rgba(0,0,0,0.1)] dark:shadow-[0_0_1px_1px_rgba(255,255,255,0.05)] dark:bg-opacity-20 transition-all duration-300 p-6 bg-white dark:bg-gray-900/50 border-white/10 dark:border-gray-700/50'>
        <div class='dark:text-gray-300'>
            <div class='text-center py-12'>
                <div
                    class='w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'
                        fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round'
                        stroke-linejoin='round' class='w-8 h-8 text-gray-400 dark:text-gray-500'>
                        <circle cx='11' cy='11' r='8'></circle>
                        <path d='m21 21-4.3-4.3'></path>
                    </svg>
                </div>
                <h4 class='text-lg font-25 font-medium text-gray-900 dark:text-white mb-2'>{$title}</h4>
                <p class='text-gray-600 dark:text-gray-400 mb-6'>{$caption}</p>
                ".($no_button ? "
                <div class='mt-3'>
                    <span class='btn btn-outline-primary font-13 mb-2' onclick='return loadPage(\"/dashboard\");'><i class='fa fa-home'></i> Back to Home</span> | 
                    <span class='btn btn-outline-primary font-13 mb-2' onclick='return loadPage(\"".$_SERVER['REQUEST_URI']."\");'><i class='fa fa-redo-alt'></i>  Reload Page</span> |
                    <span class='btn btn-outline-primary font-13 mb-2' onclick='javascript:history.back()' class='anchor'><i class='fa fa-arrow-left'></i> Go Back</span>
                </div>
                " : (!empty($url_link) ? "
                    <a href='{$url_link}'>
                        <button
                        class='inline-flex items-center justify-center font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 hover:scale-105 transition-all duration-300 bg-primary-600 hover:bg-primary-700 focus:ring-primary-500 px-4 py-2 text-sm bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-600 text-white shadow-lg'><svg
                            xmlns='http://www.w3.org/2000/svg' width='24' height='24'
                            viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'
                            stroke-linecap='round' stroke-linejoin='round' class='w-5 h-5 mr-2'>
                            <path d='M5 12h14'></path>
                            <path d='M12 5v14'></path>
                        </svg>Add {$record}</button>
                    </a>" : null)
                )."
            </div>
        </div>
    </div>";
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
    $featureDisabled = (bool) ($request == "feature_disabled");
    $message = $notFound ? $string : ($featureDisabled ? "The feature you are trying to access is disabled. Please contact the administrator." : "You don't have permission to access the requested object. It is either read-protected or not readable on this server.");
    $title = $notFound ? "Record Not Found" : ($featureDisabled ? "Feature Disabled" : "Permission Denied");

    return no_record_found($title, $message, $baseUrl."dashboard", "Home", true);
}

/**
 * Page not found html
 * 
 * @param StdClass $clientData
 * 
 * @return String
 */
function propagating_data($clientData, $session = null) {
    return '
    <section class="section">
        <div class="container">
            <div class="mt-3 text-center font-18">
                <div class="page-inner" id="transitioning_data">
                    <div class="page-description">
                        <h3>ARCHIVAL & SETUP</h3>
                    </div>
                    <div class="text-danger">
                        The previous academic term has been closed. 
                        Please wait while the system processes the data and forwards it to the next academic year and term.
                        This <strong>Transitioning process</strong> may take at most <strong>15 minutes</strong> to complete.
                        <i class="fa fa-spin fa-spinner"></i>
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
 * Academic Term Ended Notification
 * 
 * @param StdClass $defaultAcademics
 * @param String $baseUrl
 * 
 * @return String
 */
function academic_term_ended_notification($defaultAcademics, $baseUrl) {
    return '
    <div class="flex p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800 mb-3 text-center font-19">
        <div class="flex-shrink-0 mr-3">
            <svg class="w-10 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        <p class="text-red-700 dark:text-red-300">
            The current Academic Term ended on <strong>'.date("jS F, Y", strtotime($defaultAcademics->term_ends)).'</strong>.
            Click Here to <a href="'.$baseUrl.'schools/close_term"><strong>End the Academic Term</strong></a>. 
            <br><strong>DO NOT</strong> change the <strong>Academic Year</strong> or <strong>Term</strong>
            to reflect what you want. Only do so if the date was inserted incorrectly.
        </p>
    </div>';
}

/**
 * Academic Term Ended Notification Modal
 * 
 * @param StdClass $defaultAcademics
 * @param String $baseUrl
 * 
 * @return String
 */
function top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl, $previewOnly = false) {
    $html = '';
    
    // notification engine is not required
    if(empty($defaultUser) && !isset($defaultUser->appPrefs)) return $html;

    // check if the term has ended
    if(!empty($defaultUser->appPrefs->termEnded) && !$previewOnly) {
        $html .= '
        <div class="flex p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800 mb-3 text-center font-19">
            <div class="flex-shrink-0 mr-3">
                <svg class="w-10 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <p class="text-red-700 dark:text-red-300">
                The current Academic Term ended on <strong>'.date("jS F, Y", strtotime($defaultAcademics->term_ends)).'</strong>.
                Go to settings to review the academic calendar or 
                <a href="'.$baseUrl.'schools/close_term"><strong>End the Academic Term Now</strong></a>
            </p>
        </div>';
    }
    
    // check if the user is in preview mode
    if(!empty($defaultUser->isPreviewMode)) {
        $html .= '
        <div class="notification-engine">
            <div class="alert alert-warning text-center font-19">
                Hello '.$defaultUser->name.' you are currently in preview mode. Changes made will not be saved - 
                <button class="btn btn-outline-danger" onclick="return loadPage(\''.$baseUrl.'dashboard?preview_exit=true\');">Exit Preview Mode</button>
            </div>
        </div>';
    }

    return $html;
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

/**
 * Add new button
 * 
 * @param string $item_id
 * @param string $label
 * 
 * @return string
 */
function add_new_item($item_id = null, $label = null) {
	global $accessObject;
	$buttons = "
    <div class=\"btn-group dropdown d-inline\">
		<button type=\"button\" class=\"btn btn-outline-info btn-icon-text dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
			Add New {$label}
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

/**
 * Quick Add Student Modal
 * 
 * @return string
 */
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
 * @param string $user_id
 * @param string $status
 * 
 * @return string
 */
function change_status_modal($user_id = null, $status = "Active") {
    
    // global variable
    global $baseUrl, $myClass;

    $html = '
    <div data-backdrop="static" data-keyboard="false" class="modal fade" id="change_user_Status">
        <form action="'.$baseUrl.'api/users/change_status" method="POST" class="ajax-data-form" id="change_status_modal">
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
 * Reset Password Modal
 * 
 * @param string $user_id
 * 
 * @return string
 */
function reset_password_modal($user_id = null) {
    global $baseUrl;
    $html = '
    <div data-backdrop="static" data-keyboard="false" class="modal fade" id="reset_password_mod">
        <form action="'.$baseUrl.'api/users/reset_password" method="POST" class="ajax-data-form" id="reset_password_modal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reset User Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Set Password <span class="required">*</span></label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                                    <input type="password" name="confirm_password" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer p-0">
                            <input type="hidden" readonly name="user_id" value="'.$user_id.'">
                            <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button data-form_id="reset_password_modal" type="button-submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
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

/**
 * Display the class filter
 * 
 * @param bool $isReview
 * @param array $class_list
 * @param string $currentAccYearTerm
 * @param int $selectedClassId
 * 
 * @return string
 */
function display_class_filter($isReview, $class_list, $currentAccYearTerm, $selectedClassId) {
    $display = '';
    $urlPath = $isReview ? "data-location='term_bills/set?period={$currentAccYearTerm}'" : "data-location='fees-allocation'";
    $display .= '<div class="row border-bottom mb-2" id="filter_Department_Class">
            <div class="col-xl-4 col-md-4 mb-2 form-group">
                <label>Select Class</label>
                <select data-width="100%" class="form-control selectpicker" name="list_class_id">
                    <option value="">Please Select Class</option>';
                    foreach($class_list as $each) {
                        $display .= "<option ".(isset($selectedClassId) && ($selectedClassId == $each->id) ? "selected" : "")." value=\"{$each->id}\">".strtoupper($each->name)."</option>";
                    }
                    $display .= '
                </select>
            </div>
            <div class="col-xl-2 col-md-3 form-group">
                <label class="d-sm-none d-md-block" for="">&nbsp;</label>
                <button id="filter_Fees_Allocation_List" '.$urlPath.' type="submit" class="btn btn-outline-warning height-40 btn-block"><i class="fa fa-filter"></i> FILTER</button>
            </div>
        </div>';

    return $display;
}

/**
 * Fees Allocation Summary
 * 
 * @param float $totalDue
 * @param float $totalPaid
 * @param float $totalBalance
 * @param string $defaultCurrency
 * 
 * @return string
 */
function fees_allocation_summary($totalDue, $totalPaid, $totalBalance, $defaultCurrency) {

    $summaryDetails = [
        'Total Fees Due' => [
            'amount' => $totalDue,
            'color' => 'bg-info',
            'border-color' => 'border-info',
            'text-color' => 'text-info',
            'currency' => $defaultCurrency
        ],
        'Total Fees Paid' => [
            'amount' => $totalPaid,
            'color' => 'bg-success',
            'border-color' => 'border-success',
            'text-color' => 'text-success',
            'currency' => $defaultCurrency
        ],
        'Total Fees Balance' => [
            'amount' => $totalBalance,
            'color' => 'bg-danger',
            'border-color' => 'border-danger',
            'text-color' => 'text-danger',
            'currency' => $defaultCurrency
        ]
    ];

    $html = '<div class="row">';
    foreach($summaryDetails as $label => $each) {
        $html .= '
        <div class="col-md-4">
            <div class="card border-top-0 border-bottom-0 border-right-0 border-left-lg border-left-solid '.$each['border-color'].'">
                <div class="card-body card-type-3">
                    <div class="row">
                        <div class="col pr-0">
                            <h6 class="font-14 text-uppercase font-bold mb-0">'.$label.'</h6>
                            <span data-summary="amount_due" class="font-bold '.$each['text-color'].' font-23 mb-0">'.$each['currency'].' '.number_format($each['amount'], 2).'</span>
                        </div>
                        <div class="col-auto">
                            <div class="'.$each['color'].' text-white card-circle">
                                <i class="fas fa-money-bill-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';    
    }
    $html .= '</div>';
    return $html;

}
