<?php 

class Forms extends Myschoolgh {

    public function __construct() {
        parent::__construct();

        global $accessObject;
        $this->hasit = $accessObject;
    }

    
    /**
     * Generate a form and return in the response back to the user
     * The variable module will contain all the needed parameters that will be used to generate the form
     * and parse back in the data key of the result set. The process will be very intellegent and take into consideration 
     * everything that should be considered in generating a form for the user.
     * 
     * @param \stdClass $params
     * @param Array $params->module
     * 
     * @return Array
     */
    public function load(stdClass $params) {

        /** Access object */
        global $accessObject, $usersClass;
        
        /** Set parameters */
        $this->thisUser = $params->userData;
        $this->hasit->userId = $params->userData->user_id;
        $this->hasit->userPermits = $params->userData->user_permissions;
        $this->userPrefs = $params->userData->preferences;
        $this_user_id = $params->userData->user_id;

        // set the user's default text edit if not already set
        $this->userPrefs->text_editor = isset($this->userPrefs->text_editor) ? $this->userPrefs->text_editor : "trix";

        /** Test Module Variable */
        if(!isset($params->module)) {
            return ["code" => 201, "data" => "Sorry! The module parameter is required."];
        }

        /** If module not an array */
        if(!is_array($params->module)) {
            return ["code" => 201, "data" => "Sorry! The module parameter is must be an array variable."];
        }
        
        /** If module not an array */
        if(!isset($params->module["label"])) {
            return ["code" => 201, "data" => "Sorry! The label key in the array must be supplied."];
        }

        /** If the label is not in the array list */
        if(!in_array($params->module["label"], array_keys($this->form_modules))) {
            return ["code" => 201, "data" => "Sorry! An invalid label value was parsed."];
        }

        /** Init variables */
        $result = null;
        $resources = [];
        $content = [];
        
        /** Form processing div */
        $the_form = $params->module["label"];

        /** content type */
        if(isset($params->module["content"]) && $params->module["content"] == "form") {
            
            /** The label and method to load */
            if($the_form == "incident_log_form") {

                // return if no policy id was parsed
                if(!isset($params->module["item_id"])) {
                    return;
                }

                // set the user id
                $the_user_id = xss_clean($params->module["item_id"]);
                
                /** Append to parameters */
                $params->incident_log_form = true;
                
                /** Load the policy application form */
                $result = $this->incident_log_form($params, $the_user_id);
            }
            
            /** Course Unit Form */
            elseif($the_form == "course_unit_form") {

                // return if no policy id was parsed
                if(!isset($params->module["item_id"])) {
                    return;
                }
                /** Set the course id */
                $item_id = explode("_", $params->module["item_id"]);

                /** If a second item was parsed then load the lesson unit information */
                if(isset($item_id[1])) {
                    $data = $this->pushQuery("*", "courses_plan", "client_id='{$params->clientId}' AND course_id='{$item_id[0]}' AND id='{$item_id[1]}' AND plan_type='unit' LIMIT 1");
                    if(empty($data)) {
                        return ["code" => 201, "data" => "An invalid id was parsed"];
                    }
                    $params->data = $data[0];
                }

                /** Load the policy application form */
                $result = $this->course_unit_form($params, $item_id[0]);
            }

            /** Course Unit Lesson Form */
            elseif(($the_form == "course_lesson_form") || ($the_form == "course_lesson_form_view")) {
                // return if no policy id was parsed
                if(!isset($params->module["item_id"])) {
                    return;
                }
                /** Set the course id */
                $resources = ["assets/js/upload.js"];
                $item_id = explode("_", $params->module["item_id"]);

                /** If a second item was parsed then load the lesson unit information */
                if(isset($item_id[2])) {
                    $data = $this->pushQuery(
                        "a.*, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
                        "courses_plan a", 
                        "a.client_id='{$params->clientId}' AND a.unit_id='{$item_id[1]}' AND a.id='{$item_id[2]}' AND a.plan_type='lesson' LIMIT 1"
                    );
                    if(empty($data)) {
                        return ["code" => 201, "data" => "An invalid id was parsed"];
                    }
                    $params->data = $data[0];
                }

                /** If view record */
                if($the_form == "course_lesson_form_view") {
                    $params->view_record = true;
                }

                /** Load the policy application form */
                $result = $this->course_lesson_form($params, $item_id[0], $item_id[1]);
            }

        }

        // the result set to return
        $result_set = ["form" => $result];

        // if the content is not empty
        if(!empty($content)) {
            $result_set["content"] = $content;
        }
        // if resources was parsed
        if(!empty($resources)) {
            $result_set["resources"] = $resources;
        }

        // return the result
        return [
            "code" => !empty($result) ? 200 : 201,
            "data" => $result_set
        ];
    }

    /**
     * Text editor to show
     * 
     * @param String $preference
     * @param String $data
     * @param String $name          Default is faketext
     * @param String $id            Default is ajax-form-content
     * 
     * @return String
     */
    public function textarea_editor($data = null, $name = "faketext", $id = "ajax-form-content") {

        // set the form
        $form_content = "<input type='hidden' hidden id='trix-editor-input' value='{$data}'>";
        $form_content .= "<trix-editor name=\"{$name}\" input='trix-editor-input' class=\"trix-slim-scroll\" id=\"{$id}\"></trix-editor>";

        // return the results
        return $form_content;

    }

    /**
     * This method will be used to list attachments onto the page
     * 
     * @param Array $params
     * @param String $user_id
     * @param Bool $is_deletable            This allows the user to delete the file
     * @param Bool $show_view               This when set to false will hide the view icon
     * 
     * @return String
     */
    public function list_attachments($attachment_list = null, $user_id = null, $list_class = "col-lg-4 col-md-6", $is_deletable = false, $show_view = true) {

        // variables
        $list_class = empty($list_class) ? "col-lg-4 col-md-6" : $list_class;
        
        // images mimetypes for creating thumbnails
        $image_mime = ["jpg", "jpeg", "png", "gif"];
        $docs_mime = ["pdf", "doc", "docx", "txt", "rtf", "jpg", "jpeg", "png", "gif"];

        // set the thumbnail path
        $tmp_path = "assets/uploads/{$user_id}/tmp/thumbnail/";

        // create directories if none existent
        if(!is_dir("assets/uploads/{$user_id}")) {
            mkdir("assets/uploads/{$user_id}");
            mkdir("assets/uploads/{$user_id}/tmp/");
            mkdir("assets/uploads/{$user_id}/tmp/thumbnail/");
        }
        // create the tmp directory if non existent
        if(!is_dir("assets/uploads/{$user_id}/tmp/")) {
            mkdir("assets/uploads/{$user_id}/tmp/");
        }
        // create the thumbnail directory
        if(!is_dir("assets/uploads/{$user_id}/tmp/thumbnail/")) {
            mkdir("assets/uploads/{$user_id}/tmp/thumbnail/");
        }

        // confirm if the variable is not empty and an array
        if(!empty($attachment_list) && is_array($attachment_list)) {

            $files_list = "<div class=\"rs-gallery-4 rs-gallery\">";
            $files_list .=  "<div class=\"row\">";

            // loop through the array text
            foreach($attachment_list as $eachFile) {
                
                // if the file is deleted then show a note
                if($eachFile->is_deleted) { 
                    
                    $files_list .= "";

                    // $files_list .= "<div class=\"{$list_class} attachment-container text-center p-3\">
                    //         <div class=\"col-lg-12 p-2 font-italic border\">
                    //             This file is deleted
                    //         </div>
                    //     </div>";

                } else {
                    // if the file exists
                    if(is_file($eachFile->path) && file_exists($eachFile->path)) {

                        // is image check
                        $isImage = in_array($eachFile->type, $image_mime);

                        // set the file to download
                        $record_id = isset($eachFile->record_id) ? $eachFile->record_id : null;

                        // set the file id and append 4 underscores between the names
                        $file_to_download = base64_encode($eachFile->path."{$this->underscores}{$record_id}");

                        // preview link
                        $preview_link = "data-function=\"load-form\" data-resource=\"file_attachments\" data-module-id=\"{$user_id}_{$eachFile->unique_id}\" data-module=\"preview_file_attachment\"";
                        
                        // set init
                        $thumbnail = "
                            <div><span class=\"text text-{$eachFile->color}\"><i class=\"{$eachFile->favicon} fa-6x\"></i></span></div>
                            <div title=\"Click to preview: {$eachFile->name}\" data-toggle=\"tooltip\">
                                <a href=\"javascript:void(0)\" {$preview_link}><strong class=\"text-primary\">{$eachFile->name}</strong></a> <span class=\"text-muted tx-11\">({$eachFile->size})</span>
                            </div>";

                        $view_option = "";
                        $image_desc = "";
                        $delete_btn = "";

                        // if document list the show the view button
                        if($show_view) {
                            // if the type is in the array list
                            if(in_array($eachFile->type, $docs_mime)) {
                                // $view_option .= "
                                // <a title=\"Click to Click\" {$preview_link} class=\"btn btn-sm btn-primary\" style=\"padding:5px\" href=\"javascript:void(0)\">
                                //     <i style=\"font-size:12px\" class=\"fa fa-eye fa-1x\"></i>
                                // </a>";
                            }
                        }

                        // display this if the object is deletable.
                        if($is_deletable) {
                            $delete_btn = "&nbsp;<a href=\"javascript:void(0)\" onclick=\"return delete_existing_file_attachment('{$record_id}_{$eachFile->unique_id}');\" style=\"padding:5px\" class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";

                        }
                        
                        // if the file is an type
                        if($isImage) {

                            // get the file name
                            $filename = "{$tmp_path}{$eachFile->unique_id}.{$eachFile->type}";
                            
                            // if the file does not already exist
                            if(!is_file($filename) && !file_exists($filename)) {
                                
                                // create a new thumbnail of the file
                                create_thumbnail($eachFile->path, $filename);
                            } else {
                                $thumbnail = "<img height=\"100%\" width=\"100%\" src=\"{$this->baseUrl}{$filename}\">";
                            }
                            $image_desc = "
                                <div class=\"gallery-desc\">
                                    <a class=\"image-popup\" href=\"{$this->baseUrl}{$eachFile->path}\" title=\"{$eachFile->name} ({$eachFile->size}) on {$eachFile->datetime}\">
                                        <i class=\"fa fa-search\"></i>
                                    </a>
                                </div>";
                        }

                        // append to the list
                        $files_list .= "<div data-file_container='{$record_id}_{$eachFile->unique_id}' class=\"{$list_class} attachment-container text-center p-3\">";
                        $files_list .= $isImage ? "<div class=\"gallery-item\">" : null;
                            $files_list .= "
                                <div class=\"col-lg-12 attachment-item border\" data-attachment_item='{$record_id}_{$eachFile->unique_id}'>
                                    <span style=\"display:none\" class=\"file-options\" data-attachment_options='{$record_id}_{$eachFile->unique_id}'>
                                        {$view_option}
                                        <a title=\"Click to Download\" target=\"_blank\" class=\"btn btn-sm btn-success\" style=\"padding:5px\" href=\"{$this->baseUrl}download?file={$file_to_download}\">
                                            <i style=\"font-size:12px\" class=\"fa fa-download fa-1x\"></i>
                                        </a>
                                        {$delete_btn}    
                                    </span> {$thumbnail} {$image_desc}
                                </div>
                            </div>";
                        $files_list .= $isImage ? "</div>" : null;
                    }
                }

            }

            $files_list .=  "</div>";
            $files_list .=  "</div>";

            return $files_list;

        }

    }

    /**
     * A global space for uploading temporary files
     * 
     * @param \stdClass $params
     * 
     * @return String
     */
    public function form_attachment_placeholder(stdClass $params = null) {
        
        // initialize
        $preloaded_attachments = "";
        
        // set a new parameter for the items
        $files_param = (object) [
            "userData" => $this->thisUser ?? null,
            "label" => "list",
            "module" => $params->module ?? null,
            "item_id" => $params->item_id ?? "temp_attachment",
        ];

        // preload some file attachments
        $attachments = load_class("files", "controllers")->attachments($files_param);
       
        // get the attachments list
        $fresh_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

        // if attachment list was set
        if(isset($params->attachments_list) && !empty($params->attachments_list)) {

            // set a new parameter for the items
            $files_param = (object) [
                "userData" => $params->userData ?? null,
                "label" => "list",
                "is_deletable" => isset($params->is_deletable) ? $params->is_deletable : false,
                "module" => $params->module ?? null,
                "item_id" => $params->item_id ?? null,
                "attachments_list" => $params->attachments_list
            ];

            // create a new object
            $attachments = load_class("files", "controllers")->attachments($files_param);

            // get the attachments list
            $preloaded_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;
        }
        
        // set the file content
        $html_content = "
            <div class='post-attachment'>
                <div class=\"col-lg-12\" id=\"".($params->module ?? null)."\">
                    <div class=\"file_attachment_url\" data-url=\"{$this->baseUrl}api/files/attachments\"></div>
                </div>
                <div class=\"".(isset($params->class) ? $params->class : "col-md-12")." text-left\">
                    <div class='form-group row justify-content-start'>";
                    if(!isset($params->no_title)) {
                        $html_content .= "<label>Attach a Document <small class='text-danger'>(Maximum size <strong>{$this->max_attachment_size}MB</strong>)</small></label><br>";
                    }
                $html_content .= "
                        <div class=\"ml-3\">
                            <input class='form-control cursor attachment_file_upload' data-form_item_id=\"".($params->item_id ?? "temp_attachment")."\" data-form_module=\"".($params->module ?? null)."\" type=\"file\" name=\"attachment_file_upload\" id=\"attachment_file_upload\">
                        </div>
                        <div class=\"upload-document-loader hidden\"><span class=\"float-right\">Uploading <i class=\"fa fa-spin fa-spinner\"></i></span></div>
                    </div>
                </div>
            </div>
            
            <div class=\"col-md-12\">
                <div class=\"file-preview slim-scroll\" preview-id=\"".($params->module ?? null)."\">{$fresh_attachments}</div>
                <div class='form-group text-center mb-1'>{$preloaded_attachments}</div>
            </div>";
            $html_content .= !isset($params->no_footer) ? "<div class=\"col-lg-12 mb-3 border-bottom mt-3\"></div>" : null;

        return $html_content;
        
    }

    /**
     * A global space for uploading temporary files
     * 
     * @param \stdClass $params
     * 
     * @return String
     */
    public function comments_form_attachment_placeholder(stdClass $params = null) {
        
        // existing
        $preloaded_attachments = "";
        
        // set a new parameter for the items
        $files_param = (object) [
            "userData" => $this->thisUser ?? null,
            "label" => "list",
            "module" => $params->module ?? null,
            "item_id" => $params->item_id ?? "temp_attachment",
        ];

        // preload some file attachments
        $attachments = load_class("files", "controllers")->attachments($files_param);
       
        // get the attachments list
        $fresh_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

        // if attachment list was set
        if(isset($params->attachments_list) && !empty($params->attachments_list)) {
 
            // set a new parameter for the items
            $files_param = (object) [
                "userData" => $this->thisUser ?? null,
                "label" => "list",
                "is_deletable" => isset($params->is_deletable) ? $params->is_deletable : false,
                "module" => $params->module ?? null,
                "item_id" => $params->item_id ?? null,
                "attachments_list" => $params->attachments_list
            ];

            // create a new object
            $attachments = load_class("files", "controllers")->attachments($files_param);

            // get the attachments list
            $preloaded_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

        }

        // set the file content
        $html_content = "
            <div class='post-attachment'>
                <div class=\"col-lg-12\" id=\"".($params->module ?? null)."\">
                    <div class=\"file_attachment_url\" data-url=\"{$this->baseUrl}api/files/attachments\"></div>
                </div>
                ".upload_overlay()."
                <div class=\"".(isset($params->class) ? $params->class : "col-md-12")." text-left\">
                    <div class='form-group row justify-content-start'>";
                    if(!isset($params->no_title)) {
                        $html_content .= "<label>Attach a Document <small class='text-danger'>(Maximum size <strong>{$this->max_attachment_size}MB</strong>)</small></label><br>";
                    }
                $html_content .= "
                        <div class=\"ml-3\">
                            <input multiple accept=\"".($params->accept ?? "")."\" class='form-control cursor comment_attachment_file_upload' data-form_item_id=\"".($params->item_id ?? "temp_attachment")."\" data-form_module=\"".($params->module ?? null)."\" type=\"file\" name=\"comment_attachment_file_upload\" id=\"comment_attachment_file_upload\">
                        </div>
                        <div class=\"upload-document-loader hidden\"><span class=\"float-right\">Uploading <i class=\"fa fa-spin fa-spinner\"></i></span></div>
                    </div>
                </div>
            </div>
            
            <div class=\"col-md-12 ".(isset($params->no_padding) ? "p-0": "")."\">
                <div class=\"file-preview slim-scroll\" preview-id=\"".($params->module ?? null)."\">{$fresh_attachments}</div>
            </div>";
            // list the attached documents
            $html_content .= "<div class='form-group text-center mb-1'>{$preloaded_attachments}</div>";
            $html_content .= !isset($params->no_footer) ? "<div class=\"col-lg-12 mb-3 border-bottom mt-3\"></div>" : null;

        return $html_content;
        
    }

    /**
     * Course Unit Form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * @return String
     */
    public function course_unit_form($params, $course_id) {

        // description
        $html_content = "";
        $message = isset($params->data->description) ? htmlspecialchars_decode($params->data->description) : null;
        $item_id = isset($params->data->id) ? $params->data->id : null;
        $title = isset($params->data->name) ? $params->data->name : null;

        $html_content = "
        <form action='{$this->baseUrl}api/courses/".(!$title ? "add_unit" : "update_unit")."' method='POST' id='ajax-data-form-content' class='ajax-data-form'>
            <div class='row'>
                <div class='col-lg-12'>
                    <div class='form-group'>
                        <label>Unit Title</label>
                        <input value='{$title}' type='text' name='name' id='name' class='form-control'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Start Date</label>
                        <input value='".($params->data->start_date ?? null)."' type='text' name='start_date' id='start_date' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>End Date</label>
                        <input value='".($params->data->end_date ?? null)."' type='text' name='end_date' id='end_date' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Description</label>
                        {$this->textarea_editor($message)}
                    </div>
                </div>
                <div class=\"col-md-6 text-left\">
                    <input type=\"hidden\" name=\"course_id\" id=\"course_id\" value=\"{$course_id}\" hidden class=\"form-control\">
                    <input type=\"hidden\" name=\"unit_id\" id=\"unit_id\" value=\"{$item_id}\" hidden class=\"form-control\">
                    <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Record</button>
                </div>
                <div class=\"col-md-6 text-right\">
                    <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Close</button>
                </div>
            </div>
        </div>";

        return $html_content;

    }

    /**
     * Course Unit Lesson Form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * @return String
     */
    public function course_lesson_form($params, $course_id, $unit_id) {
        
        // description
        $html_content = "";
        $message = isset($params->data->description) ? htmlspecialchars_decode($params->data->description) : null;
        $item_id = isset($params->data->id) ? $params->data->id : null;
        $unit_id = isset($params->data->unit_id) ? $params->data->unit_id : $unit_id;
        $title = isset($params->data->name) ? $params->data->name : null;
        
        /** Confirm the user has the permission to perform this action */
        $hasAccess = $this->hasit->hasAccess("lesson", "course");

        /** init content */
        $preloaded_attachments = "";

        /** Set parameters for the data to attach */
        $form_params = (object) [
            "module" => "course_lesson_{$unit_id}",
            "userData" => $this->thisUser,
            "item_id" => $item_id ?? null
        ];
        
        if((!$hasAccess && $item_id) || isset($params->view_record)) {
            // load the file attachments
            $attachments = "";

            if(!empty($params->data->attachment)) {
                $attached = json_decode($params->data->attachment);
                if(!empty($attached)) {
                    $attachments = $this->list_attachments($attached->files, $params->userId, "col-lg-6");
                }
            }

            // show the details
            $html_content = "
                <div class='row'>
                    <div class='col-md-12 mb-2'><strong>Title:</strong> {$title}</div>
                    <div class='col-md-6 mb-2'><strong>Start Date:</strong> {$params->data->start_date}</div>
                    <div class='col-md-6 mb-2'><strong>End Date:</strong> {$params->data->end_date}</div>
                    <div class='col-md-12 mb-2 border-top pt-3'>{$message}</div>
                    <div class='col-md-12 border-bottom mb-3 mt-4'><h6>LESSON RESOURCES</h6></div>
                    <div class='col-md-12'>{$attachments}</div>
                </div>";

        }

        // if the user has the required permissions
        elseif($hasAccess) {

            
            // get the attachment list
            if(isset($params->data->attachment)) {

                // convert to object
                $params->data->attachment = json_decode($params->data->attachment);

                // set a new parameter for the items
                $files_param = (object) [
                    "userData" => $this->thisUser,
                    "label" => "list",
                    "is_deletable" => $hasAccess,
                    "module" => "course_lesson_{$unit_id}",
                    "item_id" => $item_id,
                    "attachments_list" => $params->data->attachment
                ];

                // create a new object
                $attachments = load_class("files", "controllers")->attachments($files_param);
            
                // get the attachments list
                $preloaded_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

            }

            $html_content = "
            <form action='{$this->baseUrl}api/courses/".(!$title ? "add_lesson" : "update_lesson")."' method='POST' id='ajax-data-form-content' class='ajax-data-form'>
                <div class='row'>
                    <div class='col-lg-12'>
                        <div class='form-group'>
                            <label>Lesson Title</label>
                            <input value='{$title}' type='text' name='name' id='name' class='form-control'>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group'>
                            <label>Start Date</label>
                            <input value='".($params->data->start_date ?? null)."' type='text' name='start_date' id='start_date' class='form-control datepicker'>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group'>
                            <label>End Date</label>
                            <input value='".($params->data->end_date ?? null)."' type='text' name='end_date' id='end_date' class='form-control datepicker'>
                        </div>
                    </div>
                    <div class='col-md-12'>
                        <div class='form-group'>
                            <label>Description</label>
                            {$this->textarea_editor($message)}
                        </div>
                    </div>";

                    $html_content .= "<div class='col-lg-12'>
                        <div class='form-group text-center mb-1'><div class='row'>{$this->form_attachment_placeholder($form_params)}</div></div>
                    </div>";
                    
                    $html_content .= "<div class='col-md-12 text-center mb-4'>{$preloaded_attachments}</div>";

                    $html_content .= "<div class=\"col-md-6 text-left\">
                        <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Record</button>
                        <input type=\"hidden\" name=\"course_id\" id=\"course_id\" value=\"{$course_id}\" hidden class=\"form-control\">
                        <input type=\"hidden\" name=\"unit_id\" id=\"unit_id\" value=\"{$unit_id}\" hidden class=\"form-control\">
                        <input type=\"hidden\" name=\"lesson_id\" id=\"lesson_id\" value=\"{$item_id}\" hidden class=\"form-control\">
                    </div>
                    <div class=\"col-md-6 text-right\">
                        <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Close</button>
                    </div>
                </div>
            </div>";
        }

        return $html_content;

    }

    /**
     * Incident Form
     * 
     * @param stdClass $params
     * @param String $user_id
     * 
     * @return String
     */
    public function incident_log_form($params, $user_id = null) {
        
        // description
        $html_content = "";
        $message = isset($params->data->description) ? htmlspecialchars_decode($params->data->description) : null;
        $item_id = isset($params->data->item_id) ? $params->data->item_id : null;
        $title = isset($params->data->subject) ? $params->data->subject : null;
        
        /** Confirm the user has the permission to perform this action */
        $hasAccess = $this->hasit->hasAccess("add", "incident");

        /** init content */
        $preloaded_attachments = "";

        /** Set parameters for the data to attach */
        $form_params = (object) [
            "module" => "incidents",
            "userData" => $this->thisUser,
            "item_id" => $item_id ?? null
        ];
        
        // if the user has the required permissions
        if((!$hasAccess && $item_id) || isset($params->view_record)) {
            // load the file attachments
            $attachments = "";

            if(!empty($params->data->attachment)) {
                $attached = json_decode($params->data->attachment);
                if(!empty($attached)) {
                    $attachments = $this->list_attachments($attached->files, $params->userId, "col-lg-6");
                }
            }

            // show the details
            $html_content = "
                <div class='row'>
                    <div class='col-md-12 mb-2'><strong>Subject:</strong> {$params->data->subject}</div>
                    <div class='col-md-6 mb-2'><strong>Incident Date:</strong> {$params->data->incident_date}</div>
                    <div class='col-md-6 mb-2'><strong>Current State:</strong> {$params->data->status}</div>
                    <div class='col-md-12 mb-2'><strong>Location:</strong> {$params->data->location}</div>
                    <div class='col-md-12 mb-2'><strong>Reported By:</strong> {$params->data->reported_by}</div>
                    <div class='col-md-6 mb-2'>
                        <h5>Assigned To:</h5>
                        <p><strong>Name:</strong> {$params->data->assigned_to_info->name}</p>
                        <p><strong>Email:</strong> {$params->data->assigned_to_info->email}</p>
                        <p><strong>Contact:</strong> {$params->data->assigned_to_info->contact}</p>
                    </div>
                    <div class='col-md-12 mb-2 border-top pt-3'>{$message}</div>
                    <div class='col-md-12 border-bottom mb-3 mt-4'><h6>ATTACHMENTS</h6></div>
                    <div class='col-md-12'>{$attachments}</div>
                </div>";

        }

        // if the user has the required permissions
        elseif($hasAccess) {

            
            // get the attachment list
            if(isset($params->data->attachment)) {

                // convert to object
                $params->data->attachment = json_decode($params->data->attachment);

                // set a new parameter for the items
                $files_param = (object) [
                    "userData" => $this->thisUser,
                    "label" => "list",
                    "is_deletable" => $hasAccess,
                    "module" => "incidents",
                    "item_id" => $item_id,
                    "attachments_list" => $params->data->attachment
                ];

                // create a new object
                $attachments = load_class("files", "controllers")->attachments($files_param);
            
                // get the attachments list
                $preloaded_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

            }

            $html_content = "
            <form action='{$this->baseUrl}api/incidents/".(!$title ? "add" : "update")."' method='POST' id='ajax-data-form-content' class='ajax-data-form'>
                <div class='row'>
                    <div class='col-lg-12'>
                        <div class='form-group'>
                            <label>Subject <span class='required'>*</span></label>
                            <input value='{$title}' type='text' name='subject' id='subject' class='form-control'>
                        </div>
                    </div>
                    <div class='col-lg-12'>
                        <div class='form-group'>
                            <label>Reported By</label>
                            <input value='".($params->data->reported_by ?? null)."' type='text' name='reported_by' id='reported_by' class='form-control'>
                        </div>
                    </div>
                    <div class='col-md-5'>
                        <div class='form-group'>
                            <label>Incident Date <span class='required'>*</span></label>
                            <input value='".($params->data->incident_date ?? null)."' type='text' name='incident_date' id='incident_date' class='form-control datepicker'>
                        </div>
                    </div>
                    <div class=\"col-md-7\">
                        <div class=\"form-group\">
                            <label for=\"assigned_to\">Assigned To</label>
                            <select data-width=\"100%\" name=\"assigned_to\" id=\"assigned_to\" class=\"form-control selectpicker\">
                                <option value=\"null\">Select User</option>";
                                foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('employee','teacher') AND status='1' AND client_id='{$params->clientId}'") as $each) {
                                    $html_content .= "<option ".(($title && ($each->item_id == $params->data->assigned_to) || (!$title && ($each->item_id == $params->userId))) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                                }
                            $html_content .= "</select>
                        </div>
                    </div>    
                    <div class='col-md-12'>
                        <div class='form-group'>
                            <label>Incident Location</label>
                            <input value='".($params->data->location?? null)."' type='text' name='location' id='location' class='form-control'>
                        </div>
                    </div>
                    <div class='col-md-12'>
                        <div class='form-group'>
                            <label>Description</label>
                            {$this->textarea_editor($message)}
                        </div>
                    </div>";

                    $html_content .= "<div class='col-lg-12'>
                        <div class='form-group text-center mb-1'><div class='row'>{$this->form_attachment_placeholder($form_params)}</div></div>
                    </div>";
                    
                    $html_content .= "<div class='col-md-12 text-center mb-4'>{$preloaded_attachments}</div>";

                    $html_content .= "<div class=\"col-md-6 text-left\">
                        <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Record</button>
                        <input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"{$user_id}\" hidden class=\"form-control\">
                        <input type=\"hidden\" name=\"incident_id\" id=\"incident_id\" value=\"{$item_id}\" hidden class=\"form-control\">
                    </div>
                    <div class=\"col-md-6 text-right\">
                        <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Close</button>
                    </div>
                </div>
            </div>";
        }

        return $html_content;


    }

    /**
     * Students form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * return String
     */
    public function student_form($clientId, $baseUrl, $userData = null) {

        $isData = !empty($userData) && isset($userData->country) ? true : false;

        $guardian = "";

        // if the guardian information is parsed
        if(!empty($userData->guardian_information)) {
            // user id
            $guardian .= '<input type="hidden" id="user_id" value="'.$userData->user_id.'" name="user_id" value="student">';
            // loop through the information
            foreach($userData->guardian_information as $key => $eachItem) {
                $key_id = $key;
                $guardian .= '
                <div class="row" data-row="'.$key_id.'">
                    <div class="col-lg-4 col-md-4">
                        <label for="guardian_info[guardian_fullname]['.$key_id.']">Fullname</label>
                        <input type="text" value="'.$eachItem->guardian_fullname.'" name="guardian_info[guardian_fullname]['.$key_id.']" id="guardian_info[guardian_fullname]['.$key_id.']" class="form-control">
                        <div class="col-lg-12 col-md-12 pl-0 mt-2">
                            <label for="guardian_info[guardian_relation]['.$key_id.']">Relationship</label>
                            <select name="guardian_info[guardian_relation]['.$key_id.']" id="guardian_info[guardian_relation]['.$key_id.']" class="form-control selectpicker">
                                <option value="null">Select Relation</option>';
                                foreach($this->pushQuery("id, name", "guardian_relation", "status='1' AND client_id='{$clientId}'") as $each) {
                                    $guardian .= "<option ".($each->name == $eachItem->guardian_relation ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                                }
                        $guardian .= '</select>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-4">
                        <label for="guardian_info[guardian_contact]['.$key_id.']">Contact Number</label>
                        <input type="text" value="'.$eachItem->guardian_contact.'" name="guardian_info[guardian_contact]['.$key_id.']" id="guardian_info[guardian_contact]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <label for="guardian_info[guardian_email]['.$key_id.']">Email Address</label>
                        <input type="text" value="'.$eachItem->guardian_email.'" name="guardian_info[guardian_email]['.$key_id.']" id="guardian_info[guardian_email]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-1 col-md-1 text-right">
                        <div class="d-flex justify-content-end">';
                        if($key_id == 1) {
                            $guardian .= '
                            <div class="mr-1"><br>
                                <button data-row="'.$key_id.'" class="btn append-row btn-primary" type="button"><i class="fa fa-plus"></i></button>
                            </div>';
                        } else {
                            $guardian .= '
                            <div class="mr-1"><br>
                                <button data-row="'.$key_id.'" class="btn remove_guardian_row btn-danger" type="button"><i class="fa fa-trash"></i></button>
                            </div>';
                        }
                        $guardian .= '
                            </div>
                    </div>
                </div>';
            }
        }

        $response = '
        <form class="ajaxform" id="ajaxform" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>BIO INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Student Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="unique_id">Student ID (optional)</label>
                        <input type="text" value="'.($userData->unique_id ?? null).'" name="unique_id" id="unique_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="enrollment_date">Enrollment Date <span class="required">*</span></label>
                        <input type="date" value="'.($userData->enrollment_date ?? null).'" name="enrollment_date" id="enrollment_date" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" class="form-control selectpicker">
                            <option value="null">Select Gender</option>';
                            foreach($this->pushQuery("*", "users_gender") as $each) {
                                $response .= "<option ".($isData && ($each->name == $userData->gender) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                    $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="firstname">Firstname <span class="required">*</span></label>
                        <input type="text" value="'.($userData->firstname ?? null).'" name="firstname" id="firstname" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="lastname">Lastname <span class="required">*</span></label>
                        <input type="text" value="'.($userData->lastname ?? null).'" name="lastname" id="lastname" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="othername">Othernames</label>
                        <input type="text" value="'.($userData->othername ?? null).'" name="othername" id="othername" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth <span class="required">*</span></label>
                        <input type="date" value="'.($userData->date_of_birth ?? null).'" name="date_of_birth" id="date_of_birth" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" value="'.($userData->email ?? null).'" name="email" id="email" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="phone">Primary Contact</label>
                        <input type="text" name="phone" value="'.($userData->phone_number ?? null).'" id="phone" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="phone_2">Secondary Contact</label>
                        <input type="text" name="phone_2" value="'.($userData->phone_number_2 ?? null).'" id="phone_2" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select name="country" id="country" class="form-control selectpicker">
                            <option value="null">Select Country</option>';
                            foreach($this->pushQuery("*", "country") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->country) ? "selected" : null)." value=\"{$each->id}\">{$each->country_name}</option>";                            
                            }
                    $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="residence">Place of Residence <span class="required">*</span></label>
                        <input type="text" value="'.($userData->residence ?? null).'" name="residence" id="residence" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="blood_group">Blood Broup</label>
                        <select name="blood_group" id="blood_group" class="form-control selectpicker">
                            <option value="null">Select Blood Group</option>';
                            foreach($this->pushQuery("id, name", "blood_groups") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->blood_group) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                        <input type="hidden" id="user_type" name="user_type" value="'.(!$isData ? "student" : null).'">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea type="text" name="description" id="description" class="form-control">'.($userData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>

            <div class="row mb-4 border-bottom pb-4">
                <div class="col-lg-12"><h5>GUARDIAN INFORMATION</h5></div>
                <div class="col-lg-12" id="student_guardian_list">';
                
                // if the data
                if($isData) {
                    $response .= $guardian;
                } else {
                    $response .= '
                    <div class="row" data-row="1">
                        <div class="col-lg-4 col-md-4">
                            <label for="guardian_info[guardian_fullname][1]">Fullname</label>
                            <input type="text" name="guardian_info[guardian_fullname][1]" id="guardian_info[guardian_fullname][1]" class="form-control">
                            <div class="col-lg-12 col-md-12 pl-0 mt-2">
                                <label for="guardian_info[guardian_relation][1]">Relationship</label>
                                <select name="guardian_info[guardian_relation][1]" id="guardian_info[guardian_relation][1]" class="form-control selectpicker">
                                    <option value="null">Select Relation</option>';
                                    foreach($this->pushQuery("id, name", "guardian_relation", "status='1' AND client_id='{$clientId}'") as $each) {
                                        $response .= "<option value=\"{$each->name}\">{$each->name}</option>";                            
                                    }
                            $response .= '</select>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-4">
                            <label for="guardian_info[guardian_contact][1]">Contact Number</label>
                            <input type="text" name="guardian_info[guardian_contact][1]" id="guardian_info[guardian_contact][1]" class="form-control">
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <label for="guardian_info[guardian_email][1]">Email Address</label>
                            <input type="text" name="guardian_info[guardian_email][1]" id="guardian_info[guardian_email][1]" class="form-control">
                        </div>
                        <div class="col-lg-1 col-md-1 text-right">
                            <div class="d-flex justify-content-end">
                                <div class="mr-1">
                                    <br>
                                    <button data-row="1" class="btn append-row btn-primary" type="button"><i class="fa fa-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </div>';
                }

                $response .= '</div>
            </div>
            <div class="row mb-4 border-bottom pb-4">
                <div class="col-lg-12"><h5>ACADEMICS</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="class_id">Class</label>
                        <select name="class_id" id="class_id" class="form-control selectpicker">
                            <option value="null">Select Student Class</option>';
                            foreach($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->class_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="department">Department <span class="required">*</span></label>
                        <select name="department" id="department" class="form-control selectpicker">
                            <option value="">Select Student Department</option>';
                            foreach($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->department) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="section">Section</label>
                        <select name="section" id="section" class="form-control selectpicker">
                            <option value="null">Select Student Section</option>';
                            foreach($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->section) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;

    }

    /**
     * Department form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * return String
     */
    public function department_form($clientId, $baseUrl, $itemData = null) {

        $isData = !empty($itemData) && isset($itemData->id) ? true : false;

        $guardian = "";

        $response = '
        <form class="ajaxform" id="ajaxform" enctype="multipart/form-data" action="'.$baseUrl.'api/departments/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12"><h5>DEPARTMENT INFORMATION</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Department Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="department_code">Department Code (optional)</label>
                        <input type="text" value="'.($itemData->department_code ?? null).'" name="department_code" id="department_code" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-8 col-md-6">
                    <div class="form-group">
                        <label for="name">Department Name<span class="required">*</span></label>
                        <input type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="department_head">Department Head</label>
                        <select data-width="100%" name="department_head" id="department_head" class="form-control selectpicker">
                            <option value="null">Select Department Head</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('employee','teacher') AND status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->item_id == $itemData->department_head) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <input type="hidden" readonly name="department_id" id="department_id" value="'.($itemData->id ?? null).'">
                        <label for="description">Description</label>
                        <textarea type="text" rows="5" name="description" id="description" class="form-control">'.($itemData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;

    }
    
    /**
     * Section form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * return String
     */
    public function section_form($clientId, $baseUrl, $itemData = null) {

        $isData = !empty($itemData) && isset($itemData->id) ? true : false;

        $response = '
        <form class="ajaxform" id="ajaxform" enctype="multipart/form-data" action="'.$baseUrl.'api/sections/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>SECTION INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Section Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="section_code">Section Code (optional)</label>
                        <input type="text" value="'.($itemData->section_code ?? null).'" name="section_code" id="section_code" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="form-group">
                        <label for="name">Section Name<span class="required">*</span></label>
                        <input type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="section_leader">Section Leader</label>
                        <select data-width="100%" name="section_leader" id="section_leader" class="form-control selectpicker">
                            <option value="null">Select Section Leader</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('student','teacher') AND status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->item_id == $itemData->section_leader) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <input type="hidden" readonly name="section_id" id="section_id" value="'.($itemData->id ?? null).'">
                        <label for="description">Description</label>
                        <textarea type="text" rows="5" name="description" id="description" class="form-control">'.($itemData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;

    }

    /**
     * Class form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * return String
     */
    public function class_form($clientId, $baseUrl, $itemData = null) {

        $isData = !empty($itemData) && isset($itemData->id) ? true : false;

        $guardian = "";

        $response = '
        <form class="ajaxform" id="ajaxform" enctype="multipart/form-data" action="'.$baseUrl.'api/classes/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12"><h5>CLASS INFORMATION</h5></div>
                <div class="col-lg-4 col-md-5">
                    <div class="form-group">
                        <label for="class_code">Class Code (optional)</label>
                        <input type="text" value="'.($itemData->class_code ?? null).'" name="class_code" id="class_code" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="form-group">
                        <label for="name">Class Name<span class="required">*</span></label>
                        <input type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="department_id">Department ID</label>
                        <select data-width="100%" name="department_id" id="department_id" class="form-control selectpicker">
                            <option value="null">Select Department</option>';
                            foreach($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $itemData->department_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="class_teacher">Class Teacher</label>
                        <select data-width="100%" name="class_teacher" id="class_teacher" class="form-control selectpicker">
                            <option value="null">Select Class Teacher</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('teacher') AND status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->item_id == $itemData->class_teacher) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="class_assistant">Class Assistant</label>
                        <select data-width="100%" name="class_assistant" id="class_assistant" class="form-control selectpicker">
                            <option value="null">Select Class Assistant</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('student') AND status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->item_id == $itemData->class_assistant) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <input type="hidden" readonly name="class_id" id="class_id" value="'.($itemData->id ?? null).'">
                        <label for="description">Description</label>
                        <textarea type="text" rows="5" name="description" id="description" class="form-control">'.($itemData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;

    }

    /**
     * Course form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * return String
     */
    public function course_form($clientId, $baseUrl, $itemData = null) {

        $isData = !empty($itemData) && isset($itemData->id) ? true : false;

        $response = '
        <form class="ajaxform" id="ajaxform" action="'.$baseUrl.'api/courses/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>COURSE DETAILS</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="course_code">Course Code (optional)</label>
                        <input type="text" value="'.($itemData->course_code ?? null).'" name="course_code" id="course_code" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="credit_hours">Credit Hours</label>
                        <input type="number" value="'.($itemData->credit_hours ?? null).'" name="credit_hours" id="credit_hours" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="class_id">Class</label>
                        <select data-width="100%" name="class_id" id="class_id" class="form-control selectpicker">
                            <option value="null">Select Class</option>';
                            foreach($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $itemData->class_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="form-group">
                        <label for="name">Course Title<span class="required">*</span></label>
                        <input type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="course_tutor">Course Tutor</label>
                        <select data-width="100%" name="course_tutor" id="course_tutor" class="form-control selectpicker">
                            <option value="null">Select Section Leader</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('teacher') AND status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->item_id == $itemData->course_tutor) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <input type="hidden" readonly name="course_id" id="course_id" value="'.($itemData->id ?? null).'">
                        <label for="description">Description</label>
                        <textarea type="text" rows="5" name="description" id="description" class="form-control">'.($itemData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;
    }

}