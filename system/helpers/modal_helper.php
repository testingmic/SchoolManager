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
    <div class=\"modal fade modal-dialog-right right\" id=\"formsModal\" ".(!$auto_close_modal ? null :  'data-backdrop="static" data-keyboard="false"').">
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
function page_not_found($request = "not_found", $string = "The page you were looking for could not be found.") {
    global $baseUrl, $_SERVER;

    $notFound = (bool) ($request == "not_found");
    $message = $notFound ? $string : "You don't have permission to access the requested object. It is either read-protected or not readable by the server.";
    $title = $notFound ? "404" : "403";

    return '
    <section class="section">
        <div class="container mt-5">
            <div class="page-error">
            <div class="page-inner">
                <h1>'.$title.'</h1>
                <div class="page-description">'.$message.'</div>
                <div class="page-search">
                <form method="GET" class="ajaxform" action="'.$baseUrl.'api/search/list">
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
                    <a href="'.$baseUrl.'"><i class="fa fa-home"></i> Back to Home</a> | 
                    <a href="'.$_SERVER["REQUEST_URI"].'"><i class="fa fa-redo-alt"></i>  Reload Page</a> |
                    <a href="javascript:history.back()" class="anchor"><i class="fa fa-arrow-left"></i> Go Back</a>
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
    <div class=\"btn-group dropdown d-inline mr-2\">
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