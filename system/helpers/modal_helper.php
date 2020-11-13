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
 * This is the modal to show for user preferences
 * 
 * @param Bool $auto_close_modal
 * @param String $user_type
 * 
 * @return String
 */
function user_preferences_modal($auto_close_modal, $user_type) {

    // is_user check
    $isUser = in_array($user_type, ["user", "business"]) ? true : false;

    // all variables must be referenced globally
    global $availableQuickLinks, $medicsClass, $sidebar_pref, $auto_close_modal, $baseUrl, $userPrefs, $text_editor, $my_quick_links;
    
    $html = "<div class=\"modal fade\" id=\"userPreferences\" ".(!$auto_close_modal ? null :  'data-backdrop="static" data-keyboard="false"').">
        <div class=\"modal-dialog modal-md\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">User Preferences</h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <div class=\"modal-body\">
                    <form action=\"{$baseUrl}api/users/preference\" method=\"POST\" class=\"app-data-form\">
                        ".form_overlay()."
                        <div class=\"row\">
                            <div class=\"col-md-6\">
                                <label for=\"rows_count\">Table Rows Count</label>
                                <input type=\"number\" value=\"{$userPrefs->list_count}\" name=\"label[list_count]\" id=\"list_count\" class=\"form-control\">
                            </div>
                            <div class=\"col-md-6\">
                                <div class=\"form-group\">
                                    <label for=\"sidebar_nav\">Sidebar Width</label>
                                    <select name=\"label[sidebar_nav]\" id=\"sidebar_nav\" class=\"selectpicker\" data-width=\"100%\">
                                        <option ".($sidebar_pref == "sidebar-opened" ? "selected" : null)." value=\"sidebar-opened\">Opened Sidebar</option>
                                        <option ".($sidebar_pref == "sidebar-folded" ? "selected" : null)." value=\"sidebar-folded\">Closed Sidebar</option>
                                    </select>
                                </div>
                            </div>
                            <div class=\"col-md-6\">
                                <div class=\"form-group\">
                                    <label for=\"rows_count\">Template Theme Color</label>
                                    <select name=\"label[theme_color]\" id=\"theme_color\" class=\"selectpicker\" data-width=\"100%\">
                                        <option ".($userPrefs->theme_color == "sidebar-light" ? "selected" : null)." value=\"sidebar-light\">Light Theme</option>
                                        <option ".($userPrefs->theme_color == "sidebar-dark" ? "selected" : null)." value=\"sidebar-dark\">Dark Theme</option>
                                    </select>
                                </div>
                            </div>
                            <div class=\"col-md-6\">
                                <div class=\"form-group\">
                                    <label for=\"new_policy_notification\">Notify of new Policies</label>
                                    <select name=\"label[new_policy_notification]\" id=\"new_policy_notification\" class=\"selectpicker\" data-width=\"100%\">
                                        <option ".($userPrefs->new_policy_notification == "notify" ? "selected" : null)." value=\"notify\">Yes! Notify me</option>
                                        <option ".($userPrefs->new_policy_notification == "dont-notify" ? "selected" : null)." value=\"1\">No! Donot notify</option>
                                    </select>
                                </div>
                            </div>
                            <div class=\"col-md-6\">
                                <div class=\"form-group\">
                                    <label for=\"auto_close_modal\">Auto Close Modal</label>
                                    <select name=\"label[auto_close_modal]\" id=\"auto_close_modal\" class=\"selectpicker\" data-width=\"100%\">
                                        <option ".(!$auto_close_modal ? "selected" : null)." value=\"allow\">Yes! Allow</option>
                                        <option ".($auto_close_modal ? "selected" : null)." value=\"dont\">No! Do not Allow</option>
                                    </select>
                                </div>
                            </div>                            
                            <div class=\"col-md-6\">
                                <div class=\"form-group\">
                                    <label for=\"text_editor\">Text Editor to Use</label>
                                    <select name=\"label[text_editor]\" id=\"text_editor\" class=\"selectpicker\" data-width=\"100%\">
                                        <option ".($text_editor == "trix" ? "selected" : null)." value=\"trix\">Trix Editor</option>
                                        <!--<option ".($text_editor == "ckeditor" ? "selected" : null)." value=\"ckeditor\">CKEditor</option>-->
                                    </select>
                                </div>
                            </div>
                            <div class=\"col-md-12\">
                                <div class=\"mb-0 pb-0\"><span for=\"quick_links\">Chat Message</span></div>
                            </div>
                            <div class=\"col-md-6\">
                                <div class=\"form-group\">
                                    <select name=\"label[messages][enter_to_send]\" id=\"enter_to_send\" class=\"selectpicker\" data-width=\"100%\">
                                        <option ".($userPrefs->messages->enter_to_send ? "selected" : null)." value=\"1\">Press enter to send</option>
                                        <option ".(!$userPrefs->messages->enter_to_send ? "selected" : null)." value=\"0\">Press send button</option>
                                    </select>
                                </div>
                            </div>";

                            // display this default payment option selection if user/business
                            if($isUser) {
                                $html .= "
                                <div class=\"col-md-6\">
                                    <div class=\"form-group\">
                                        <select name=\"label[payments][default_payment]\" id=\"default_payment\" class=\"selectpicker\" data-width=\"100%\">
                                            <option value=\"null\">Please select option</option>";
                                            foreach($medicsClass->pushQuery("*", "policy_form", "type='payment_options'") as $eachOption) {
                                                $html .= "<option ".(($userPrefs->payments->default_payment == $eachOption->id) ? "selected" : null)." value=\"{$eachOption->id}\">{$eachOption->name}</option>";
                                            }
                                            $html .= "
                                        </select>
                                    </div>
                                </div>";
                            }

                            $html .= "<div class=\"col-md-12\">
                                <div class=\"form-group\">
                                    <div class=\"mb-0 pb-0\"><span for=\"quick_links\">Quick Links</span></div>
                                    <div class=\"row p-2 pt-0 mt-0 justify-content-start\">";
                                        foreach($availableQuickLinks as $key => $quick_link) {
                                            $html .= "<div class=\"form-check form-check-inline\">
                                            <label for=\"label[quick_links][{$key}]\" class=\"form-check-label\">
                                                <input ".(in_array($key, $my_quick_links) ? "checked" : null)." name=\"label[quick_links][{$key}]\" id=\"label[quick_links][{$key}]\" type=\"checkbox\" class=\"form-check-input\">
                                                {$quick_link["label"]}
                                            <i class=\"input-frame\"></i></label>
                                        </div>";
                                        }
                            $html .= "</div>
                                </div>
                            </div>
                            <div class=\"col-lg-12\">
                                <div class=\"form-group text-center\">
                                    <button class=\"btn btn-outline-success\">Save Preferences</button>
                                </div>
                            </div>
                            <div class=\"col-lg-12\">
                                <div class=\"form-group text-center\">
                                    <small><em>Changes will take effect after page refresh</em></small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>";

    return $html;
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
 * This is the modal to show for resource basic information to a specific resource
 * 
 * @param Bool $auto_close_modal
 * 
 * @return String
 */
function resource_information_modal() {
    $html = "<div class=\"modal fade modal-dialog-right right\" id=\"resourceInfoModal\">
        <div class=\"modal-dialog modal-dialog-centered modal-md\" style=\"width:100%;height:100%;\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\"></h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <input hidden class=\"ajax-replies-loaded\" value=\"0\" data-form=\"none\">
                <div class=\"modal-body\" data-last_reply_id=\"\" data-scrolling=\"false\" style=\"height:100%; max-height:100%; overflow-y:auto; overflow-x:hidden\"></div>
            </div>
        </div>
    </div>";

    return $html;
}

/**
 * Save draft item button
 * 
 * @param String $resource
 * @param String $title
 * @param String $recordId
 * 
 * @return String
 */
function submit_draft_button($resource, $title, $recordId) {
    $html = "<div class=\"modal fade\" id=\"submitRecordModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-top modal-md\" style=\"width:100%;\" role=\"document\">
            <div class=\"modal-content\">
                ".form_loader()."
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Submit ".ucwords($resource)."</h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <input hidden class=\"ajax-replies-loaded\" value=\"0\" data-form=\"none\">
                <div class=\"modal-body\" data-scrolling=\"false\" style=\"text-align:left\">
                    Are you sure you want to proceed to submit the {$resource}: <strong>{$title}</strong>. Once submitted you cannot reverse this action.
                </div>
                <div class=\"modal-footer\">
                    <button data-resource=\"".create_slug($resource)."\" class=\"btn btn-outline-success\" data-record-id=\"{$recordId}\">Yes! Proceed</button>
                    <button data-resource=\"".create_slug($resource)."\" class=\"btn btn-outline-danger\" data-dismiss=\"modal\">Cancel</button>
                </div>
            </div>
        </div>
    </div>";
    $html .= "<button class=\"btn btn-outline-success submit-popup-button\" data-record-id=\"{$recordId}\" data-toggle=\"tooltip\" data-msg=\"Are you sure you want to proceed to submit the {$resource}: <strong>{$title}</strong>. Once submitted you cannot reverse this action.\" title=\"Submit this {$resource}\"><i class=\"fa fa-meteor\"></i> Submit</button>";
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
 * Cancel record modal content
 * 
 * @param String $resource
 * @param String $title
 * @param String $recordId
 * 
 * @return String
 */
function cancel_record_button($resource, $recordId, $resource_id) {
    
    global $baseUrl;

    $record = [
        "user_policy" => [
            "title" => "Request Policy Cancellation",
            "hover" => "Policy",
            "label" => "Policy ID",
            "input" => "policy_id"
        ],
        "adverts" => [
            "title" => "Cancel Ad Campaign",
            "label" => "Campaign ID",
            "input" => "advert_id",
            "hover" => "Advert"
        ]
    ];

    $html = "<div class=\"modal fade\" id=\"cancelResourceRecordModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-top modal-md\" style=\"width:100%;\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">{$record[$resource_id]["title"]}</h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <form method=\"POST\" action=\"{$baseUrl}api/{$resource_id}/cancel\" class=\"app-data-form\">
                    ".form_loader()."
                    <div class=\"modal-body\" data-scrolling=\"false\" style=\"text-align:left\">
                        <div class=\"row\">
                            <div class=\"col-lg-12\">
                                <div class=\"form-group\">
                                    <label class=\"font-weight-bold\">{$record[$resource_id]["label"]}</label>
                                    <p><span class=\"font-18px font-weight-bold\">{$recordId}</span></p>
                                    <input class=\"form-control\" hidden readonly=\"readonly\" id=\"{$record[$resource_id]["input"]}\" value=\"{$recordId}\" name=\"{$record[$resource_id]["input"]}\">
                                </div>
                                <div class=\"form-group\">
                                    <label for=\"\">Reason for Cancellation</label>
                                    <textarea maxlength=\"250\" data-toggle=\"maxlength\" required name=\"reason\" class=\"form-control\" rows=\"5\"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=\"modal-footer\">
                        <button data-resource=\"{$resource}\" type=\"submit\" class=\"btn btn-outline-success\" data-record-id=\"{$recordId}\">Yes Submit</button>
                        <button data-resource=\"{$resource}\" type=\"reset\" class=\"btn btn-outline-danger\" data-dismiss=\"modal\">Cancel</button>
                    </div>
                </form>
                <div class=\"font-italic mb-2 border-top pt-2 mt-2 tx-14 text-center\">
                    By submitting, you agree to be contacted for verification of the cancellation request.
                </div>
            </div>
        </div>
    </div>";
    $html .= "<button class=\"btn btn-outline-danger\" data-target=\"#cancelResourceRecordModal\" data-toggle=\"modal\" data-record-id=\"{$recordId}\" title=\"Cancel this {$record[$resource_id]["hover"]}: {$resource}\">Cancel this {$record[$resource_id]["hover"]}</button>";
    return $html;
}

/**
 * Save Information Popup
 * 
 * 
 * @return String
 */
function save_form_button() {
    $html = "<div class=\"modal fade\" id=\"formSubmitModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-top modal-md\" style=\"width:100%;\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Save Form</h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <input hidden class=\"ajax-replies-loaded\" value=\"0\" data-form=\"none\">
                <div class=\"modal-body\" data-scrolling=\"false\" style=\"text-align:left\">
                    Are you sure you want to proceed with this action?
                </div>
                <div class=\"modal-footer\">
                    <button class=\"btn btn-outline-success\">Yes! Proceed</button>
                    <button class=\"btn btn-outline-danger\" data-dismiss=\"modal\">Cancel</button>
                </div>
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
 * Save Information Popup
 * 
 * 
 * @return String
 */
function discard_form($button = "confirm_form_discard", $modal_output = "modal-dialog-right right") {
    $html = "<div class=\"modal fade {$modal_output}\" style=\"z-index: 99999\" id=\"discardFormModal\" data-backdrop=\"static\" data-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-top\" style=\"width:100%;\" role=\"document\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\">Discard</h5>
                    <button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span>&times;</span></button>
                </div>
                <div class=\"modal-body\" data-scrolling=\"false\" style=\"text-align:left\">
                    Are you sure you want to discard this form?
                </div>
                <div class=\"modal-footer\">
                    <button class=\"btn btn-outline-success {$button}\">Yes! Confirm</button>
                    <button class=\"btn btn-outline-danger\" data-dismiss=\"modal\">Cancel</button>
                </div>
            </div>
        </div>
    </div>";
    return $html;
}

/**
 * Form manager select options
 */
function form_manager_options() {
    $html = "<div>
                <div class=\"btn-group dropdown\">
                    <button type=\"button\" class=\"btn btn-secondary dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                        <i style=\"font-size:10px;\" class=\"fa fa-plus\"></i> Add New
                    </button>
                    <div class=\"dropdown-menu\" data-function=\"jsform-module\">
                        <a class=\"dropdown-item\" data-module=\"jsform\" data-field=\"input\" href=\"javascript:void(0)\">Text Input Field</a>
                        <a class=\"dropdown-item\" data-module=\"jsform\" data-field=\"date\" href=\"javascript:void(0)\">Date Input Field</a>
                        <a class=\"dropdown-item\" data-module=\"jsform\" data-field=\"email\" href=\"javascript:void(0)\">Email Field</a>
                        <a class=\"dropdown-item\" data-module=\"jsform\" data-field=\"textarea\" href=\"javascript:void(0)\">Textarea</a>
                        <a class=\"dropdown-item\" data-module=\"jsform\" data-field=\"select\" href=\"javascript:void(0)\">Select Options</a>
                    </div>
                </div>
            </div>";

    return $html;
}

/**
 * Leave comment container
 * 
 * @param Strig $resource           This is the resource name.
 * @param String $recordId          The unique id of the record on which the comment is been shared on
 * 
 * @return String
 */
function leave_comments_builder($resource, $recordId, $comment = null) {
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
    $html = "<div class=\"leave-comment-wrapper\" data-id=\"{$recordId}\">
            ".absolute_loader()."
            <div class=\"form-group mt-3\">
                <label for=\"leave_comment_content\" title=\"Click to display comment form\" class=\"cursor\">
                    ( <i class=\"fa fa-comments\"></i> <strong><span data-id=\"{$recordId}\" data-record=\"comments_count\">0</span> comments</strong> ) ".(!empty($comment) ? $comment : "Leave a comment for the <strong>Client</strong>")." <small class=\"text-danger\">(cannot be modified once posted)</small>
                </label>
            </div>
            <div class=\"hidden\" id=\"leave-comment-content\">
                <div class=\"form-group mb-2\">
                    <trix-editor class=\"slim-scroll\" id=\"leave_comment_content\" name=\"leave_comment_content\"></trix-editor>
                </div>
                <div class=\"form-group mt-0 text-right\">
                    <button type=\"button\" data-resource=\"{$resource}\" data-id=\"{$recordId}\" class=\"btn share-comment btn-outline-success\">Post Comment <i class=\"fa fa-angle-double-right\"></i></button>
                </div>
                <div>{$formsObj->comments_form_attachment_placeholder($form_params)}</div>
            </div>
        </div>";

    return $html;
}