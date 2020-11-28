<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		Helpers
 * @subpackage	Page Modals Helper Functions
 * @category	Core Functions
 * @author		Analitica Innovare Dev Team
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
function replies_modal($auto_close_modal) {
    $html = "<div class=\"modal fade modal-dialog-right right\" id=\"repliesModal\" ".(!$auto_close_modal ? null :  'data-backdrop="static" data-keyboard="false"').">
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

function page_not_found() {
    global $baseUrl;
    return '
    <section class="section">
        <div class="container mt-5">
            <div class="page-error">
            <div class="page-inner">
                <h1>404</h1>
                <div class="page-description">
                The page you were looking for could not be found.
                </div>
                <div class="page-search">
                <form method="GET" class="ajaxform" action="'.$baseUrl.'api/search/list">
                    <div class="form-group floating-addon floating-addon-not-append">
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
                    </div>
                    </div>
                </form>
                <div class="mt-3">
                    <a href="'.$baseUrl.'">Back to Home</a>
                </div>
                </div>
            </div>
            </div>
        </div>
    </section>';
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
