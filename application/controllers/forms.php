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
            if(in_array($the_form, ["incident_log_form", "incident_log_form_view", "incident_log_followup_form"])) {

                // return if no policy id was parsed
                if(!isset($params->module["item_id"])) {
                    return;
                }

                /** Set the course id */
                $query = "";
                $item_id = explode("_", $params->module["item_id"]);

                /** If a second item was parsed then load the lesson unit information */
                if(isset($item_id[1])) {

                    /** If view record */
                    if(in_array($the_form, ["incident_log_form_view", "incident_log_followup_form"])) {

                        /** If view record */
                        $params->view_record = true;

                        // append some query
                        $query = ", (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image,'|',last_seen,'|',online,'|',user_type) FROM users WHERE users.item_id = a.assigned_to LIMIT 1) AS assigned_to_info,
                            (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image,'|',last_seen,'|',online,'|',user_type) FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_information,
                            (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image,'|',last_seen,'|',online,'|',user_type) FROM users WHERE users.item_id = a.user_id LIMIT 1) AS user_information";
                    }

                    $data = $this->pushQuery(
                        "a.*, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment {$query}", 
                        "incidents a", 
                        "a.client_id='{$params->clientId}' AND a.item_id='{$item_id[1]}' AND a.user_id='{$item_id[0]}' AND a.incident_type='incident' LIMIT 1"
                    );

                    if(empty($data)) {
                        return ["code" => 201, "data" => "An invalid id was parsed"];
                    }
                    $params->data = $data[0];
                }
                
                /** Append to parameters */
                $params->incident_log_form = true;
                
                /** Load the function */
                if($the_form == "incident_log_followup_form") {
                    $result = $this->incident_log_followup_form($item_id[1], $params->clientId, $item_id[0]);
                } else {
                    $resources = ["assets/js/upload.js"];
                    $result = $this->incident_log_form($params, $item_id[0]);
                }
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

                /** Load the function */
                $result = $this->course_unit_form($params, $item_id[0]);
            }

            /** Course Unit Lesson Form */
            elseif(in_array($the_form, ["course_lesson_form", "course_lesson_form_view"])) {
                // return if no policy id was parsed
                if(!isset($params->module["item_id"])) {
                    return;
                }

                /** Set the course id */
                $resources = ["assets/js/upload.js"];
                $item_id = explode("_", $params->module["item_id"]);

                /** If a second item was parsed then load the lesson unit information */
                if(isset($item_id[2])) {
                    
                    // make the query
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

                /** Load the function */
                $result = $this->course_lesson_form($params, $item_id[0], $item_id[1]);
            }

            /** Course link and file upload */
            elseif(in_array($the_form, ["course_link_upload", "course_file_upload"])) {
                // return if no policy id was parsed
                if(!isset($params->module["item_id"])) {
                    return;
                }
                /** Set the course id */
                $item_id = explode("_", $params->module["item_id"]);

                // get the course
                $course = $this->pushQuery("id, item_id", "courses", "item_id = '{$item_id[0]}' AND client_id='{$params->clientId}'");
                $course_id = $course[0]->id;

                /** If a second item was parsed then load the lesson unit information */
                if(isset($item_id[1])) {

                    /** Load the course resource  */
                    $data = $this->pushQuery("a.*, (SELECT b.id FROM courses b WHERE b.item_id = a.course_id) AS the_course_id", 
                    "courses_resource_links a", 
                    "a.client_id='{$params->clientId}' AND a.course_id='{$item_id[0]}' AND a.item_id='{$item_id[1]}' LIMIT 1");
                    
                    // if no record was found then end the query
                    if(empty($data)) {
                        return ["code" => 201, "data" => "An invalid id was parsed"];
                    }
                    $course_id = $item_id[0];
                    $params->data = $data[0];
                }

                // load the class
                $result = $this->course_upload_item($params, $course_id, $item_id[0], $the_form);
            }

            /** Modify Guardian Ward */
            elseif($the_form == "modify_guardian_ward") {
                // return if no policy id was parsed
                if(!isset($params->module["item_id"])) {
                    return;
                }
                /** Set the course id */
                $item_id = explode("_", $params->module["item_id"]);

                /** Guardian information paramater */
                $guardian_param = (object) [
                    "limit" => 1,
                    "clientId" => $params->clientId,
                    "guardian_id" => $item_id[0],
                    "append_wards" => true,
                ];

                $data = load_class("users", "controllers")->guardian_list($guardian_param);
                if(empty($data)) {
                    return ["code" => 201, "data" => "An invalid id was parsed"];
                }
                $data = $data[0];

                /** Load the function */
                $result = $this->modify_guardian_ward($params, $data);
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
        $data = str_ireplace("'", "", $data);
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
     * Guardian Ward Modification
     * 
     * @return String
     */
    public function modify_guardian_ward($params, $data) {

        $html_content = "<div class='row'>";
        $html_content .= "<div class='col-md-10'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Student Name</label>";
        $html_content .= "<input type='text' placeholder='Search student name' name='user_name_search' id='user_name_search' class='form-control'>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-2'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Filter</label>";
        $html_content .= "<button onclick='return search_usersList(\"student\")' class='btn btn-outline-success btn-block'><i class='fa fa-filter'></i></button>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-12 mt-2' id='user_search_list'>";
        $html_content .= "</div>";
        $html_content .= "</div>";

        return $html_content;
    }

    /**
     * Course Unit Form
     * 
     * @param stdClass $params
     * @param String $course_id
     * 
     * @return String
     */
    public function course_unit_form(stdClass $params, $course_id) {

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
                        <label>Overview / Objective</label>
                        {$this->textarea_editor($message)}
                    </div>
                </div>
                <div class=\"col-md-6 text-left\">
                    <input type=\"hidden\" name=\"course_id\" id=\"course_id\" value=\"{$course_id}\" hidden class=\"form-control\">
                    <input type=\"hidden\" name=\"unit_id\" id=\"unit_id\" value=\"{$item_id}\" hidden class=\"form-control\">
                    <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Record</button>
                </div>
                <div class=\"col-md-6 text-right\">
                    <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                </div>
            </div>
        </div>";

        return $html_content;

    }

    /**
     * Course Unit Lesson Form
     * 
     * @param stdClass $params
     * @param String $course_id
     * @param String $unit_id
     * 
     * @return String
     */
    public function course_lesson_form(stdClass $params, $course_id, $unit_id) {
        
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
                    <div class='col-md-12 mb-2'>Title: <h5 class=\"text-uppercase\">{$title}</h5></div>
                    <div class='col-md-6 mb-2'><strong>Start Date:</strong> {$params->data->start_date}</div>
                    <div class='col-md-6 mb-2'><strong>End Date:</strong> {$params->data->end_date}</div>
                    <div class='col-md-12 mb-2 border-top pt-3'>{$message}</div>
                    ".(isset($attached) && !empty($attached->files) ? "
                        <div class='col-md-12 border-bottom mb-3 mt-4'><h6>LESSON RESOURCES</h6></div>
                        <div class='col-md-12'>{$attachments}</div>" : ""
                    )."
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
                        <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                    </div>
                </div>
            </div>";
        }

        return $html_content;

    }

    /**
     * Course Link Upload Form
     * 
     * @param stdClass $params
     * @param stdClass $course_id
     * @param stdClass $item_type //- course_file_upload
     * 
     * @return String
     */
    public function course_upload_item(stdClass $params, $course_id = null, $item_id = null, $item_type = "course_link_upload") {

        // description
        $html_content = "";
        $link_file = $item_type == "course_file_upload" ? false : true;
        $lesson_ids = isset($params->data->lesson_id) ? json_decode($params->data->lesson_id, true) : [];
        
        $lessons_list = $this->pushQuery("id, item_id, course_id, unit_id, name", "courses_plan", "course_id = '".($params->data->the_course_id ?? $course_id)."' AND client_id='{$params->clientId}' AND plan_type='lesson'");
        
        $html_content = "
        <form id='ajax-data-form-content' class='ajax-data-form' enctype=\"multipart/form-data\" action=\"{$this->baseUrl}api/resources/upload_4courses\" method=\"POST\">
            <div class=\"row\">
                <div class=\"col-lg-12 pt-0 mt-0\">
                    <div class=\"form-group pb-1 pt-0 mb-2 mt-0\">
                        <label for=\"upload[description]\">Description</label>
                        <textarea name=\"upload[description]\" id=\"upload[description]\" class=\"form-control\">".($params->data->description ?? null)."</textarea>
                    </div>
                </div>
                ".($link_file ? 
                    "<div class=\"col-lg-12 pt-0 mt-0\">
                        <div class=\"form-group pb-1 mb-2 pt-0 mt-0\">
                            <label for=\"upload[link_name]\">Link Name</label>
                            <input name=\"upload[link_name]\" value=\"".($params->data->link_name ?? null)."\" id=\"upload[link_name]\" class=\"form-control\">
                        </div>
                    </div>
                    <div class=\"col-lg-12 pt-0 mt-0\">
                        <div class=\"form-group pb-1 mb-2 pt-0 mt-0\">
                            <label for=\"upload[link_url]\">URL</label>
                            <input name=\"upload[link_url]\" value=\"".($params->data->link_url ?? null)."\" id=\"upload[link_url]\" class=\"form-control\">
                        </div>
                    </div>" : 
                    "
                    <div class=\"col-lg-12 pt-0 mt-0\">
                        <div class=\"form-group pb-1 mb-2 pt-0 mt-0\">
                            <label for=\"upload[file_name]\">File Name</label>
                            <input name=\"upload[file_name]\" value=\"".($params->data->link_name ?? null)."\" id=\"upload[file_name]\" class=\"form-control\">
                        </div>
                    </div>
                    <div class=\"col-lg-12 pt-0 mt-0   \">
                        <div class=\"form-group pt-0 mt-0\">
                            <label for=\"upload[file]\">Select File</label>
                            <input name=\"upload[file]\" type=\"file\" id=\"upload[file]\" class=\"form-control\">
                        </div>
                    </div>"
                )."
                <div class=\"col-lg-12 mb-4\">
                    <label for=\"lesson_id\">Select Lesson</label>
                    <table class=\"table table-bordered\">
                        <thead>
                            <th width=\"8%\"></th>
                            <th>Lesson Title</th>
                        </thead>
                    </table>
                    <div style=\"height:200px; overflow-y:auto;\" class=\"slim-scroll\">
                        <div class=\"form-group pt-0 mt-2\">
                            <table class=\"table table-bordered\">
                                <tbody>";
                                foreach($lessons_list as $each) {
                                    $html_content .= "
                                        <tr class=\"pt-0 pb-0\">
                                            <td style=\"height:40px\">
                                                <input type=\"checkbox\" ".(in_array($each->item_id, $lesson_ids) ? "checked" : "")." class=\"form-control\" value=\"{$each->item_id}\" name=\"upload[lesson_id][]\" id=\"lesson_id[{$each->item_id}][]\">
                                            </td>
                                            <td style=\"height:40px\">
                                                <label for=\"lesson_id[{$each->item_id}][]\">{$each->name}</label>
                                            </td>
                                        </tr>
                                    ";
                                }
                                $html_content .= "</tbody>
                            </table>
                        </div>
                    </div>
                    <input type='hidden' name='upload[upload_type]' value='".($link_file ? "is_link" : "is_file")."' id='upload[upload_type]'>
                    <input type='hidden' name='upload[course_id]' value='{$item_id}' id='upload[course_id]'>
                    <input type='hidden' name='upload[resource_id]' value=\"".($params->data->item_id ?? null)."\" id='upload[resource_id]'>
                </div>
                <div class=\"col-md-6 text-left\">
                    <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Record</button>
                </div>
                <div class=\"col-md-6 text-right\">
                    <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                </div>
            </div>
        </form>";

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
    public function incident_log_form(stdClass $params, $user_id = null) {
        
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

            // loop through the information
            foreach(["created_by_information", "user_information", "assigned_to_info"] as $each) {
                // convert the created by string into an object
                $params->data->{$each} = (object) $this->stringToArray($params->data->$each, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);    
            }

            // load the followup list
            $followups = $this->incident_log_followup_form($item_id, $params->clientId, $user_id, true);

            // show the details
            $html_content = "
                <div class='row'>
                    <div class='col-md-12 mb-2'>Subject: <h5 class=\"text-uppercase\">{$params->data->subject}</h5></div>
                    <div class='col-md-12 mb-2'><strong>Incident Date:</strong> {$params->data->incident_date}</div>
                    <div class='col-md-12 mb-2'><strong>Current State:</strong> {$this->the_status_label($params->data->status)}</div>
                    <div class='col-md-12 mb-2'><strong>Location:</strong> {$params->data->location}</div>
                    <div class='col-md-12 mb-2'><strong>Reported By:</strong> {$params->data->reported_by}</div>
                    ".(
                        !empty($params->data->assigned_to_info->name) ? "
                        <div class='col-md-6 mb-2'>
                            <h6>ASSIGNED TO:</h6>
                            <p><strong>Name:</strong> ".($params->data->assigned_to_info->name ?? null)."</p>
                            <p><strong>Email:</strong> ".($params->data->assigned_to_info->email ?? null)."</p>
                            <p><strong>Contact:</strong> ".($params->data->assigned_to_info->contact ?? null)."</p>
                        </div>" : ""
                    )."
                    <div class='col-md-12 mb-2 border-top pt-3'>{$message}</div>
                    ".(isset($attached) && !empty($attached->files) ? "
                        <div class='col-md-12 border-bottom mb-3 mt-4'><h6>ATTACHMENTS</h6></div>
                        <div class='col-md-12'>{$attachments}</div>" : ""
                    )."
                    ".(!empty($followups) ? "
                        <div class='col-md-12 border-bottom mb-3 mt-4'><h6>FOLLOWUPS</h6></div>
                        <div class='col-md-12'>{$followups}</div>" : ""
                    )."
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
                            <label>Reported By <small>(Name & Contact/ID)</small></label>
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
                    <div class='col-md-".($title ? 8 : 12)."'>
                        <div class='form-group'>
                            <label>Incident Location</label>
                            <input value='".($params->data->location ?? null)."' type='text' name='location' id='location' class='form-control'>
                        </div>
                    </div>";
                    
                    // if the user wants to update
                    if($title) {
                        // display the status
                        $html_content .= '<div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select data-width="100%" name="status" id="status" class="form-control cursor">
                                    <option value="null">Select Status</option>
                                    <option '.($params->data->status == "Pending" ? "selected" : null).' value="Pending">Pending</option>
                                    <option '.($params->data->status == "Processing" ? "selected" : null).' value="Processing">Processing</option>
                                    <option '.($params->data->status == "Cancelled" ? "selected" : null).' value="Cancelled">Cancelled</option>
                                    <option '.($params->data->status == "Solved" ? "selected" : null).' value="Solved">Solved</option>
                                </select>
                            </div>
                        </div>';
                    }

                    $html_content .= "<div class='col-md-12'>
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
                        <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                    </div>
                </div>
            </div>";
        }

        return $html_content;

    }

    /**
     * Followup Thread Messages
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function followup_thread($data) {

        return "
        <div class=\"col-md-12 p-0 grid-margin\" id=\"comment-listing\" data-reply-container=\"{$data->item_id}\">
            <div class=\"card rounded mb-4 replies-item\">
                <div class=\"card-header pb-0 mb-0\">
                    <div class=\"d-flex align-items-center justify-content-between\">
                        <div class=\"d-flex align-items-center\">
                            <img class=\"img-xs rounded-circle\" src=\"{$this->baseUrl}{$data->created_by_information->image}\" alt=\"\">
                            <div class=\"ml-2\">
                                <p class=\"cursor underline m-0\" title=\"Click to view summary information about {$data->created_by_information->name}\" onclick=\"return user_basic_information('{$data->created_by}')\" data-id=\"{$data->created_by}\">{$data->created_by_information->name}</p>
                                <p title=\"{$data->date_created}\" class=\"tx-11 mb-2 replies-timestamp text-muted\">{$data->time_ago}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=\"card-body mt-0 pt-2 pb-2\">
                    <div class=\"tx-14\">{$data->description}</div>
                </div>
            </div>
        </div>";
    }

    /**
     * Incident Followup Form
     * 
     * List the followup details before showing the textarea field to add more information to it
     * 
     * @param String $item_id
     * @param String $clientId
     * @param String $user_id 
     * 
     * @return String
     */
    public function incident_log_followup_form($item_id, $clientId, $user_id = null, $list_only = false) {
        
        /** Initializing */
        $prev_date = null;
        $html_content = "<div id='incident_log_followup_list'>";
        $followups_list = "";

        /** Load the followups for the incident */
        $q_param = (object) [
            "user_id" => $user_id, "incident_type" => "followup", 
            "followup_id" => $item_id, "clientId" => $clientId
        ];
		$followups = load_class("incidents", "controllers")->list($q_param)["data"];

        /** Loop through the followups */
        foreach($followups as $followup) {

            /** Clean date */
            $clean_date = date("l, F Y", strtotime($followup->date_created));
            $raw_date = date("Y-m-d", strtotime($followup->date_created));

            /** If the previous date is not the same as the current date */
            if (!$prev_date || $prev_date !== $raw_date) {
                $followups_list .= "<div class=\"message_list_day_divider_label\"><button class=\"message_list_day_divider_label_pill\">{$clean_date}</button></div>";
            }
            $followups_list .= $this->followup_thread($followup);

            // prepare the previous date
            $prev_date = date("Y-m-d", strtotime($followup->date_created));
            $prev_date = $raw_date;

        }

        $html_content .= !empty($followups_list) ? $followups_list : "<div id=\"no_message_content\" class=\"text-center font-italic\">No followup message available.</div>";
        $html_content .= "</div>";

        $form_content = "
            <div class='mb-4'>
                <div class='form-group'>
                    <label></label>
                    <textarea class='form-control' name='incident_followup' id='incident_followup'></textarea>
                </div>
                <div class='row'>
                    <div class='col-lg-6'><button data-resource_id='{$item_id}' onclick='return post_incident_followup(\"{$user_id}\",\"{$item_id}\")' id='post_incident_followup' class='btn btn-outline-success'>Share Comment</button></div>
                    <div class=\"col-md-6 text-right\">
                        <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Close</button>
                    </div>
                </div>
            </div>";
        
        $close = '<div class="col-md-12 mt-4 border-top pt-4 mb-3 text-center"><span class="btn btn-outline-secondary" data-dismiss="modal">Close Modal</span></div>';

        return !$list_only ? $form_content.$html_content : $html_content.$close;
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
        if(!empty($userData->guardian_list)) {
            // loop through the information
            foreach($userData->guardian_list as $key => $eachItem) {
                $key_id = $key+1;
                $guardian .= '
                <div class="row mb-3 pb-3" data-row="'.$key_id.'">
                    <div class="col-lg-4 col-md-4">
                        <label for="guardian_info[guardian_fullname]['.$key_id.']">Fullname</label>
                        <input type="hidden" name="guardian_info[guardian_id]['.$key_id.']" id="guardian_info[guardian_id]['.$key_id.']" value="'.$eachItem->user_id.'">
                        <input type="text" value="'.$eachItem->fullname.'" name="guardian_info[guardian_fullname]['.$key_id.']" id="guardian_info[guardian_fullname]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <label for="guardian_info[guardian_contact]['.$key_id.']">Contact Number</label>
                        <input type="text" value="'.$eachItem->contact.'" name="guardian_info[guardian_contact]['.$key_id.']" id="guardian_info[guardian_contact]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <label for="guardian_info[guardian_email]['.$key_id.']">Email Address</label>
                        <input type="email" value="'.$eachItem->email.'" name="guardian_info[guardian_email]['.$key_id.']" id="guardian_info[guardian_email]['.$key_id.']" class="form-control">
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
                    <div class="col-lg-4 col-md-4 mt-2">
                        <label for="guardian_info[guardian_relation]['.$key_id.']">Relationship</label>
                        <select data-width="100%" name="guardian_info[guardian_relation]['.$key_id.']" id="guardian_info[guardian_relation]['.$key_id.']" class="form-control selectpicker">
                            <option value="null">Select Relation</option>';
                            foreach($this->pushQuery("id, name", "guardian_relation", "status='1' AND client_id='{$clientId}'") as $each) {
                                $guardian .= "<option ".($each->name == $eachItem->relationship ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                    $guardian .= '</select>
                    </div>
                    <div class="col-lg-8 col-md-8 mt-2">
                        <label for="guardian_info[guardian_address]['.$key_id.']">Address</label>
                        <input type="text" value="'.($eachItem->address ?? null).'" name="guardian_info[guardian_address]['.$key_id.']" id="guardian_info[guardian_address]['.$key_id.']" class="form-control">
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
                        <select data-width="100%" name="gender" id="gender" class="form-control selectpicker">
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
                        <select data-width="100%" name="country" id="country" class="form-control selectpicker">
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
                        <select data-width="100%" name="blood_group" id="blood_group" class="form-control selectpicker">
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
                if($isData && !empty($guardian)) {
                    $response .= $guardian;
                } else {
                    $response .= '
                    <div class="row" data-row="1">
                        <div class="col-lg-4 col-md-4">
                            <label for="guardian_info[guardian_fullname][1]">Fullname</label>
                            <input type="hidden" name="guardian_info[guardian_id][1]" id="guardian_info[guardian_id][1]" value="'.random_string("nozero", 8).'">
                            <input type="text" name="guardian_info[guardian_fullname][1]" id="guardian_info[guardian_fullname][1]" class="form-control">
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
                        <div class="col-lg-4 col-md-4 mt-2">
                            <label for="guardian_info[guardian_relation][1]">Relationship</label>
                            <select data-width="100%" name="guardian_info[guardian_relation][1]" id="guardian_info[guardian_relation][1]" class="form-control selectpicker">
                                <option value="null">Select Relation</option>';
                                foreach($this->pushQuery("id, name", "guardian_relation", "status='1' AND client_id='{$clientId}'") as $each) {
                                    $response .= "<option value=\"{$each->name}\">{$each->name}</option>";                            
                                }
                        $response .= '</select>
                        </div>
                        <div class="col-lg-8 col-md-8 mt-2">
                            <label for="guardian_info[guardian_address][1]">Address</label>
                            <input type="text" name="guardian_info[guardian_address][1]" id="guardian_info[guardian_address][1]" class="form-control">
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
                        <select data-width="100%" name="class_id" id="class_id" class="form-control selectpicker">
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
                        <select data-width="100%" name="department" id="department" class="form-control selectpicker">
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
                        <select data-width="100%" name="section" id="section" class="form-control selectpicker">
                            <option value="null">Select Student Section</option>';
                            foreach($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->section) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
            </div>
            <input type="hidden" id="user_id" value="'.($userData->user_id ?? null).'" name="user_id">
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;

    }

    /**
     * Guardian form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * return String
     */
    public function guardian_form($clientId, $baseUrl, $userData = null) {

        $isData = !empty($userData) && isset($userData->user_id) ? true : false;

        $guardian = "";

        $response = '
        <form class="ajaxform" id="ajaxform" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "guardian_update" : "guardian_add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>BIO INFORMATION</h5>
                </div>
                <div class="col-lg-'.(!empty($userData) ? 6 : 4 ).' col-md-5">
                    <div class="form-group">
                        <label for="image">Guardian Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-'.(!empty($userData) ? 4 : 4 ).' col-md-5">
                    <div class="form-group">
                        <label for="guardian_id">Guardian ID (optional)</label>
                        <input type="text" readonly value="'.($userData->user_id ?? random_string("nozero", 10)).'" name="guardian_id" id="guardian_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select data-width="100%" name="gender" id="gender" class="form-control selectpicker">
                            <option value="null">Select Gender</option>';
                            foreach($this->pushQuery("*", "users_gender") as $each) {
                                $response .= "<option ".($isData && ($each->name == $userData->gender) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                    $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="form-group">
                        <label for="fullname">Fullname <span class="required">*</span></label>
                        <input type="text" value="'.($userData->fullname ?? null).'" name="fullname" id="fullname" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" value="'.($userData->date_of_birth ?? null).'" name="date_of_birth" id="date_of_birth" class="form-control">
                    </div>
                </div>
                <div class="col-lg-8 col-md-6">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" value="'.($userData->email ?? null).'" name="email" id="email" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="contact">Primary Contact</label>
                        <input type="text" name="contact" value="'.($userData->contact ?? null).'" id="contact" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="contact_2">Secondary Contact</label>
                        <input type="text" name="contact_2" value="'.($userData->contact_2 ?? null).'" id="contact_2" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select data-width="100%" name="country" id="country" class="form-control selectpicker">
                            <option value="null">Select Country</option>';
                            foreach($this->pushQuery("*", "country") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->country) ? "selected" : null)." value=\"{$each->id}\">{$each->country_name}</option>";                            
                            }
                    $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="residence">Place of Residence <span class="required">*</span></label>
                        <input type="text" value="'.($userData->residence ?? null).'" name="residence" id="residence" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="address">Postal Address <span class="required">*</span></label>
                        <input type="text" value="'.($userData->address ?? null).'" name="address" id="address" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="employer">Name of Employer</label>
                        <input type="text" value="'.($userData->employer ?? null).'" name="employer" id="employer" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="occupation">Occupation</label>
                        <input type="text" value="'.($userData->occupation ?? null).'" name="occupation" id="occupation" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea type="text" name="description" id="description" class="form-control">'.($userData->description ?? null).'</textarea>
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
                <div class="'.($isData ? "col-lg-6 col-md-6" : "col-lg-4 col-md-4").'">
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
                <div class="'.($isData ? "col-lg-6 col-md-6" : "col-lg-4 col-md-4").'">
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
                <div class="'.($isData ? "col-lg-6 col-md-6" : "col-lg-4 col-md-4").'">
                    <div class="form-group">
                        <label for="class_assistant">Class Assistant</label>
                        <select data-width="100%" name="class_assistant" id="class_assistant" class="form-control selectpicker">
                            <option value="null">Select Class Assistant</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('student') AND status='1' AND client_id='{$clientId}' ".($isData ? " AND class_id='{$itemData->id}'" : "")."") as $each) {
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

    /**
     * Staff form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * return String
     */
    public function staff_form($clientId, $baseUrl, $userData = null) {

        $isData = !empty($userData) && isset($userData->country) ? true : false;

        $guardian = "";

        $response = '
        <form class="ajaxform" id="ajaxform" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>BIO INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Staff Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="unique_id">Staff ID (optional)</label>
                        <input type="text" value="'.($userData->unique_id ?? null).'" name="unique_id" id="unique_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="enrollment_date">Date Employed <span class="required">*</span></label>
                        <input type="date" value="'.($userData->enrollment_date ?? null).'" name="enrollment_date" id="enrollment_date" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select data-width="100%" name="gender" id="gender" class="form-control selectpicker">
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
                        <select data-width="100%" name="country" id="country" class="form-control selectpicker">
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
                        <select data-width="100%" name="blood_group" id="blood_group" class="form-control selectpicker">
                            <option value="null">Select Blood Group</option>';
                            foreach($this->pushQuery("id, name", "blood_groups") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->blood_group) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                        <input type="hidden" id="user_type" name="user_type" value="'.(!$isData ? "student" : null).'">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="position">Position / Role <span class="required">*</span></label>
                        <input type="text" value="'.($userData->position ?? null).'" name="position" id="position" class="form-control">
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
                <div class="col-lg-12"><h5>ACADEMICS</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="department">Department <span class="required">*</span></label>
                        <select data-width="100%" name="department" id="department" class="form-control selectpicker">
                            <option value="">Select Department</option>';
                            foreach($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->department) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="section">Section</label>
                        <select data-width="100%" name="section" id="section" class="form-control selectpicker">
                            <option value="null">Select Section</option>';
                            foreach($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->section) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
            </div>
            <div class="row mb-4 border-bottom pb-4">
                <div class="col-lg-12"><h5>LOGIN INFORMATION</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="user_type">User Permission <span class="required">*</span></label>
                        <select data-width="100%" name="user_type" id="user_type" class="form-control selectpicker">
                            <option value="">Select User Permission</option>';
                            foreach($this->user_roles_list as $key => $value) {
                                $response .= "<option ".($isData && ($key == $userData->user_type) ? "selected" : null)." value=\"{$key}\">{$value}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" value="'.($userData->username ?? null).'" name="username" id="username" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select data-width="100%" name="status" id="status" class="form-control selectpicker">
                            <option value="null">Select Employee Status</option>';
                            foreach($this->user_status_list as $key => $value) {
                                $response .= "<option ".($isData && ($key == $userData->status) ? "selected" : null)." value=\"{$key}\">{$value}</option>";                            
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

}