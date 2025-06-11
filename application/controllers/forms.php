<?php 

class Forms extends Myschoolgh {

    public $thisUser;
    public $hasit;
    public $userPrefs;
    public $default_height = 'height-200';

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
     * @param stdClass $params
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
        $this->userPrefs = (object) [];
        $this_user_id = $params->userData->user_id;

        // set the user's default text edit if not already set
        $this->userPrefs->text_editor = "trix";

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
                    $resources = ["assets/js/upload_2.js"];
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
                $course = $this->pushQuery("id, item_id", "courses", "item_id = '{$item_id[0]}' AND client_id='{$params->clientId}' AND academic_year='{$params->academic_year}' AND academic_term='{$params->academic_term}' LIMIT {$this->temporal_maximum}");
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
            elseif(in_array($the_form, ["modify_guardian_ward", "modify_ward_guardian"])) {

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

                // load this information if the guardian was parsed
                if($the_form == "modify_guardian_ward") {
                    $data = load_class("users", "controllers")->guardian_list($guardian_param);
                } else {
                    $data = $this->pushQuery("guardian_id, item_id AS user_id", "users", "item_id='{$item_id[0]}' LIMIT 1");
                }

                if(empty($data)) {
                    return ["code" => 201, "data" => "An invalid id was parsed"];
                }
                $data = $data[0];

                /** Load the function */
                if($the_form == "modify_guardian_ward") {
                    $data->user_type = "guardian";
                } else {
                    $data->user_type = "student";
                    $data->guardian_id = !empty($data->guardian_id) ? (object) $this->stringToArray($data->guardian_id) : (object) [];
                }
                
                /** Load the form to search for the user */
                $result = $this->modify_guardian_ward($data);
            }
            
            /** Management Assignments */
            elseif(in_array($the_form, ["upload_assignment", "update_assignment"])) {

                /** Assign a variable to the item id */
                $resources = ["assets/js/upload.js", "assets/js/assignments.js"];
                $item_id = isset($params->module["item_id"]) ? $params->module["item_id"] : null;
                $params->data = null;

                /** Make a request for the data is the item_id was parsed */
                if(!empty($item_id)) {
                    // object parameter
                    $assignments_param = (object) [
                        "clientId" => $params->clientId,
                        "assignment_id" => $item_id,
                        "limit" => 1
                    ];
                    $data = load_class("assignments", "controllers")->list($assignments_param);
                    
                    // ensure the request is not empty
                    if(empty($data["data"])) {
                        return ["code" => 201, "data" => "An invalid id was parsed"];
                    }

                    // append the data to the parameters request
                    $params->data = $data[0];
                }

                /** Call the function to process the request */
                $result = $this->create_assignment($params, $the_form);
            }

            /** Document Manager **/
            elseif(in_array($the_form, ["document_create_folder", "document_create_file", "document_upload_files", "document_update_folder", "document_update_file"])) {

                $data = [];
                $resources = ["assets/js/upload.js"];

                // get the record id
                $item_id = isset($params->module["item_id"]) ? $params->module["item_id"] : null;

                // load the information for the file or document
                if(!empty($item_id) && in_array($the_form, ["document_update_folder", "document_update_file", "document_upload_files"])) {

                    // paramters to use
                    $_params = (object) [
                        "unique_id" => $item_id,
                        "limit" => 1,
                        "state" => ["Active", "Trash"],
                        "clientId" => $params->clientId
                    ];
                    
                    // get the data
                    $data = load_class("documents", "controllers")->list($_params)["data"];

                    // confirm that the record is not empty
                    if(!isset($data["directory_list"]) && !isset($data["file_list"])) {
                        return ["code" => 201, "data" => "An invalid document id was parsed"];
                    }

                    // confirm that the record is not empty
                    if(isset($data["directory_list"]) && empty($data["directory_list"])) {
                        return ["code" => 201, "data" => "An invalid folder id was parsed"];
                    }

                    // confirm that the record is not empty
                    if(isset($data["file_list"]) && empty($data["file_list"])) {
                        return ["code" => 201, "data" => "An invalid document id was parsed"];
                    }

                    // get the information
                    $data = $data["directory_list"][0] ?? $data["file_list"][0];
                }

                // call the document_manager model for the information
                $result = $this->document_manager($the_form, $data, $item_id);
            }

            /** Student Assignment Manager **/ 
            elseif(in_array($the_form, ["assignment_detail"])) {

                /** Assign a variable to the item id */
                $resources = ["assets/js/upload.js", "assets/js/assignments.js"];
                $item_id = isset($params->module["item_id"]) ? $params->module["item_id"] : null;
                
                /** Make a request for the data is the item_id was parsed */
                if(empty($item_id)) {
                    return ["code" => 201, "data" => "An invalid id was parsed."];
                }

                /** Parameter breakdown **/
                $split = explode("_", $item_id);

                // return error if the second key (assignment_id) was not parsed
                if(!isset($split[1])) {
                    return ["code" => 201, "data" => "An invalid id was parsed."];
                }                

                // object parameter
                $assignments_param = (object) [
                    "clientId" => $params->clientId,
                    "assignment_id" => $split[1],
                    "student_id" => $split[0],
                    "limit" => 1
                ];
                $data = load_class("assignments", "controllers")->student_info($assignments_param);
                
                // ensure the request is not empty
                if(empty($data["data"])) {
                    return ["code" => 201, "data" => "An invalid id was parsed"];
                }

                /** Call the function to process the request */
                $result = $this->assignment_review_detail($data["data"]);

            }
            
            /** Daily Report Log Form */
            elseif(in_array($the_form, ["daily_report_log_form"])) {

                /** Assign a variable to the item id */
                $resources = ["assets/js/upload.js"];
                $item_id = isset($params->module["item_id"]) ? $params->module["item_id"] : null;
                $params->data = null;

                /** Call the function to process the request */
                $result = $this->daily_report_log_form($params, $the_form);
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
     * This model controls the document management
     * 
     * @param String $the_form
     * @param Object $data
     *
     * @return String
     */
    public function document_manager($the_form, $data = null, $item_id = null) {

        // init value
        $html = "";
        $description = $data->description ?? null;

        // IF THE REQUEST IS TO MANAGE A FOLDER
        if(in_array($the_form, ["document_create_folder", "document_update_folder", "document_update_file"])) {

            // set the url and some few parameters
            $parameters = [
                "document_create_folder" => [
                    "action" => "folders",
                    "label" => "Folder"
                ],
                "document_update_folder" => [
                    "action" => "folders",
                    "label" => "Folder"
                ],
                "document_update_file" => [
                    "action" => "files",
                    "label" => "File"
                ]
            ];


            // set the document information
            if(!empty($data->content) && ($data->mode === "manual")) {
                $description = $data->content;
            }

            // set the html form content
            $html = "
            <form autocomplete='Off' action='{$this->baseUrl}api/documents/{$parameters[$the_form]["action"]}' method='POST' class='ajax-data-form' id='{$the_form}'>
                <div>
                    <div class='form-group'>
                        <label class='font-bold'>{$parameters[$the_form]["label"]} Name</label>
                        <input type='text' value=\"".($data->name ?? null)."\" name='name' id='name' class='form-control'>
                    </div>
                    <div class='form-group'>
                        <label class='font-bold'>{$parameters[$the_form]["label"]} Description</label>
                        {$this->textarea_editor($description, "faketext", "ajax-form-content")}
                    </div>
                    ".(in_array($the_form, ["document_update_file"]) ? 
                        "
                        <div class='form-group mb-0'>
                            <label class='font-bold'>File Type:</label> {$data->file_type}
                        </div>
                        <div class='form-group'>
                            <label class='font-bold'>File Size:</label> ".file_size_convert(round($data->file_size * 1024))."
                        </div>" : null
                    )."
                    <div class='p-0'>
                        <div class='d-flex justify-content-between'>
                            <div>
                                <button type='reset' class='btn btn-light' data-dismiss='modal'>Close</button>
                            </div>
                            <div>                        
                                <input type='hidden' readonly name='unique_id' value='".($item_id ?? null)."'>
                                <input type='hidden' readonly name='request' value='".(!empty($data->unique_id) ? "modify" : "save")."'>
                                <button data-form_id='{$the_form}' type='button-submit' class='btn btn-outline-success'><i class='fa fa-save'></i> Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>";
        }

        // IF THE REQUEST IS TO UPLOAD FILES
        elseif($the_form === "document_upload_files") {

            /** Set parameters for the data to attach */
            $file_params = (object) [
                "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
                "module" => "documents_".(!empty($item_id) ? $item_id : "root"),
                "userData" => $this->thisUser,
                "ismultiple" => true,
                "item_id" => $item_id ?? null
            ];

            // set the html form content
            $html = "
            <form autocomplete='Off' action='{$this->baseUrl}api/documents/upload' method='POST' class='ajax-data-form' id='file_upload'>
                <div>
                    <div class='form-group'>
                        <h4><strong>UPLOAD MULTIPLE FILES INTO \"".(!empty($data) ? $data->name : "MAIN")."\" FOLDER</strong></h4>
                        <div class='font-16 border-top mt-3 pt-3 border-bottom pb-3'>Select the Files that you wish to upload</div>
                    </div>
                    <div class='form-group p-0'>
                        {$this->form_attachment_placeholder($file_params, "")}
                    </div>
                    <div class='d-flex justify-content-between'>
                        <div>
                            <button type='reset' class='btn btn-light' data-dismiss='modal'>Close</button>
                        </div>
                        <div>                        
                            <input type='hidden' readonly name='unique_id' value='".($item_id ?? null)."'>
                            <button data-form_id='file_upload' type='button-submit' class='btn btn-outline-success'><i class='fa fa-upload'></i> Upload Files</button>
                        </div>
                        
                    </div>
                </div>
            </form>";
        }

        // IF THE REQUEST IS TO CREATE OR UPDATE A PDF DOCUMENT
        elseif($the_form === "document_create_file") {
            // set the html form content
            $html = "
            <style>
            .ajax-data-form trix-editor {
                min-height: 300px;
                max-height: 300px;
            }
            </style>
            <form autocomplete='Off' action='{$this->baseUrl}api/documents/files' method='POST' class='ajax-data-form' id='{$the_form}'>
                <div>
                    <div class='form-group'>
                        <label class='font-bold'>File Name <span class='required'>*</span></label>
                        <input type='text' value=\"".($data->name ?? null)."\" name='name' id='name' class='form-control'>
                    </div>
                    <div class='form-group'>
                        <label class='font-bold'>Document Information <span class='required'>*</span></label>
                        {$this->textarea_editor($description, "faketext", "ajax-form-content")}
                    </div>
                    <div class='p-0'>
                        <div class='d-flex justify-content-between'>
                            <div>
                                <button type='reset' class='btn btn-light' data-dismiss='modal'>Close</button>
                            </div>
                            <div>                        
                                <input type='hidden' readonly name='unique_id' value='".($item_id ?? null)."'>
                                <input type='hidden' readonly name='request' value='".(!empty($data->unique_id) ? "modify" : "save")."'>
                                <button data-form_id='{$the_form}' type='button-submit' class='btn btn-outline-success'><i class='fa fa-save'></i> Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>";
        }

        return $html;
    }

    /**
     * Assignment Template
     * 
     * This is a common form fields that are present in all forms of assignments
     * 
     * @param stdClass     $params
     * @param String       $disabled
     * 
     * @return String
     */
    private function assignment_template(stdClass $params, $disabled = null) {
        
        /** Set parameters for the data to attach */
        $file_params = (object) [
            "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
            "module" => "assignments",
            "userData" => $params->userData,
            "item_id" => $params->data->item_id ?? null
        ];

        global $defaultUser, $isTutor;

        $preloaded_attachments = null;
        $description = $params->data->assignment_description ?? null;
        
        $class_id = $params->data->class_id ?? null;
        $assigned_to = $params->data->assigned_to ?? null;

        // if the class id is not empty
        if(!empty($class_id)) {

            // append to the parameters list
            $course_id = $params->data->course_id ?? null;
            $params->data->clientId = $params->clientId;

            // convert the students list into an array list
            $assigned_list = !empty($params->data->assigned_to_list) ? $this->stringToArray($params->data->assigned_to_list) : [];

            // load the courses and students list using the class id as the filter
            $ass_data = load_class("assignments", "controllers")->load_course_students($params->data);

            // get the information list
            if(isset($ass_data["data"])) {
                $ass_data = $ass_data["data"];
            }

            // set the type
            $type = isset($params->data->questions_type) ? $params->data->questions_type : null;

            // run this section for only file attachment assignments
            if($type === "file_attachment") {
                // set a new parameter for the items
                $files_param = (object) [
                    "userData" => $params->userData,
                    "label" => "list",
                    "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
                    "is_deletable" => (bool) !$disabled,
                    "module" => "assignments",
                    "item_id" => $params->data->item_id,
                    "attachments_list" => $params->data->attachment
                ];

                // create a new object
                $attachments = load_class("files", "controllers")->attachments($files_param);
            
                // get the attachments list
                $preloaded_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;
            }

        }
        // class filter
        $classFilter = $isTutor ? "AND item_id IN ".$this->inList($defaultUser->class_ids) : null;

        // get the classes list
        $classes_list = $this->pushQuery("name, id, item_id, payment_module", "classes", "client_id='{$params->clientId}' AND status='1' {$classFilter} LIMIT 100");
        
        $html_content = "<div class='col-lg-8'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Title <span class='required'>*</span></label>";
        $html_content .= "<input {$disabled} class='form-control' value='".($params->data->assignment_title ?? null)."' name='assignment_title' id='assignment_title'>";
        $html_content .= "</div>";
        $html_content .= "</div>";

        $html_content .= "<div class='col-md-4'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Assessment Type <span class='required'>*</span></label>";
        $html_content .= "<select {$disabled} data-width='100%' class='selectpicker form-control' name='assignment_type' id='assignment_type'>";
        $html_content .= "<option value=''>Select Assignment Category</option>";
        foreach($this->assessment_group as $value) {
            $html_content .= "
            <option ".($class_id && $params->data->assignment_type == $value ? "selected" : null)." value='{$value}'>
                ".strtoupper($value)."
            </option>";
        }
        $html_content .= "</select>";
        $html_content .= "</div>";
        $html_content .= "</div>";

        $html_content .= "<div class='col-md-4'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Class <span class='required'>*</span></label>";
        $html_content .= "<select {$disabled} data-width='100%' class='selectpicker form-control' name='class_id' id='class_id'>";
        $html_content .= "<option value=''>Select Class</option>";
        // if the content is an array
        if(is_array($classes_list)) {
            // loop through the results list
            foreach($classes_list as $class) {
                $html_content .= "
                <option data-payment_module='{$class->payment_module}' ".($class_id == $class->item_id ? "selected" : null)." value='{$class->item_id}'>
                    ".strtoupper($class->name)."
                </option>";
            }
        }
        $html_content .= "</select>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-4'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Subject <span class='required'>*</span></label>";
        $html_content .= "<select {$disabled} data-width='100%' class='selectpicker form-control' name='course_id' id='course_id'>";
        $html_content .= "<option value=''>Select Subject</option>";
        
        // display the Subjects List
        if(isset($ass_data)) {
            foreach($ass_data["courses_list"] as $course) {
                $html_content .= "
                <option ".($course_id == $course->item_id ? "selected" : null)." value='{$course->item_id}'>
                    ".strtoupper($course->name)."
                </option>";
            }
        }

        $html_content .= "</select>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-6'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Submission Date <span class='required'>*</span></label>";
        $html_content .= "<input {$disabled} type='text' class='form-control datepicker' data-mindate='".date("Y-m-d", strtotime("today"))."' data-maxdate='".date("Y-m-d", strtotime("today +6 months"))."' value='".($params->data->due_date ?? date("Y-m-d", strtotime("today +3 day")))."' name='date_due' id='date_due'>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-6'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Time Due</label>";
        $html_content .= "<input {$disabled} type='time' class='form-control' value='".($params->data->due_time ?? "09:00")."' name='time_due' id='time_due'>";
        $html_content .= "</div>";
        $html_content .= "</div>";

        $html_content .= "
            <div class='col-md-12'>
                <div class='form-group'>
                    <label>Additional Instructions</label>
                    ".( !$disabled ? $this->textarea_editor($description) : $description )."</div>
            </div>";
        
        // append the assignment id if the data parameter is not empty
        if(!$disabled) {
            // show the file upload content
            $html_content .= "
            <div class='col-lg-12' id='upload_question_set_template'>
                <div class='form-group text-center mb-1'><div class='row'>{$this->form_attachment_placeholder($file_params)}</div></div>
            </div>";
        }
        
        $html_content .= "<div class='col-md-12'>";
        $html_content .= "<div class='form-group text-center mb-1'>{$preloaded_attachments}</div>";
        $html_content .= "</div>";

        $html_content .= "<div class='col-md-12'>";
        $html_content .= "<div class='row'>";
        $html_content .= "<div class='col-md-6'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Grade Scale<span class='required'>*</span></label>";
        $html_content .= "<input {$disabled} type='number' value='".($params->data->grading ?? null)."' min='1' max='100' class='form-control' name='grade' id='grade'>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-6'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Assigned To <span class='required'>*</span></label>";
        $html_content .= "<select {$disabled} data-width='100%' class='selectpicker form-control' name='assigned_to' id='assigned_to'>";
        $html_content .= "<option ".($assigned_to == "all_students" ? "selected" : null)." value='all_students'>All Students</option>";
        $html_content .= "<option ".($assigned_to == "selected_students" ? "selected" : null)." value='selected_students'>Selected Students</option>";
        $html_content .= "</select>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "</div>";

        $html_content .= "<div class='col-lg-12 ".($assigned_to == "selected_students" ? "" : "hidden")."' id='assign_to_students_list'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Students List</label>";
        $html_content .= "<select {$disabled} data-width='100%' multiple class='selectpicker form-control' name='assigned_to_list' id='assigned_to_list'>";
        $html_content .= "<option value=''>Select Students</option>";

        // display the Subjects List
        if(isset($ass_data)) {
            foreach($ass_data["students_list"] as $student) {
                $html_content .= "<option ".(in_array($student->item_id, $assigned_list) ? "selected" : null)." value='{$student->item_id}'>{$student->name}</option>";
            }
        }

        $html_content .= "</select>";
        $html_content .= "</div>";
        $html_content .= "</div>";

        return $html_content;
    }

    /**
     * Create a New Assignment
     * 
     * @param stdClass  $params
     * @param String    $mode       This determines whether to upload_assignment or update_assignment
     * @return String
     */
    public function create_assignment(stdClass $params, $mode = null) {
        
        // readonly state
        $disabled = isset($params->data->state) && (!in_array($params->data->state, ["Pending", "Graded", "Draft"])) ? "disabled='disabled'" : null;
        $type = isset($params->data->questions_type) ? $params->data->questions_type : null;

        $html_content = "
        <style>
        #create_assignment trix-editor {
            min-height: 150px;
            max-height: 150px;
        }
        </style>
        <form autocomplete='Off' ".(!$disabled ? "class='ajax-data-form' id='ajax-data-form-content' action='{$this->baseUrl}api/assignments/".(!$params->data ? "add" : "update")."'": "")." method='post'>";
        $html_content .= "<div class='row' id='create_assignment'>";
        
        $html_content .= "<div class='col-lg-4'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Question Type <span class='required'>*</span></label>";
        $html_content .= "<select {$disabled} data-width='100%' class='selectpicker form-control' name='questions_type' id='questions_type'>";
        $html_content .= "<option value=''>Please Select</option>";
        $html_content .= "<option ".($type == "file_attachment" ? "selected" : null)." value='file_attachment'>Upload Question Set</option>";
        $html_content .= "<option ".($type == "multiple_choice" ? "selected" : null)." value='multiple_choice'>Multiple Choice Questions (Quiz)</option>";
        $html_content .= "</select>";
        $html_content .= "</div>";
        $html_content .= "</div>";

        $html_content .= $this->assignment_template($params, $disabled);

        $html_content .= "</div>";
        
        // show the submit buttons if the assignment is still active
        if(!$disabled) {

            // append the assignment id if the data parameter is not empty
            if($params->data) {
                $html_content .= "<input hidden type='hidden' {$disabled} class='form-control' id='assignment_id' name='assignment_id' value='{$params->data->item_id}'>";
            }
            
            // append the submit and cancel buttons
            $html_content .= "
                <div class=\"d-flex justify-content-between\">
                    <div>
                        <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                    </div>
                    <div>
                        <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Assignment</button>
                    </div>
                </div>";
        }
            
        $html_content .= "</form>";

        return $html_content;
    }

    /**
     * Add Question Form
     * 
     * @param stdClass  $data
     * @param String    $assignment_id
     * 
     * @return String
     */
    public function add_question_form($assignment_id = null, $data = null) {

        // initial variables
        $options_array = [
            "option_a" => "Option A", "option_b" => "Option B",
            "option_c" => "Option C", "option_d" => "Option D",
            "option_e" => "Option E" //, "option_f" => "Option F",
        ];
        $answer_types = [
            "option" => "Single Answer Option",
            "multiple" => "Multiple Select Answers",
            "numeric" => "Numeric",
            // "input" => "Text Input"
        ];
        $difficulty = [
            "easy" => "Easy",
            "medium" => "Medium",
            "advanced" => "Advanced",
        ];

        // convert the answers into an array
        $answer_array = isset($data->correct_answer) ? $this->stringToArray($data->correct_answer) : [];
        $isActive = $data->isActive == "active" ? true : false;
        $disabled = $isActive ? "" : "disabled='disabled'";

        $html_content = '
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <strong>Question</strong> <span class="required">*</span>
                        </div>
                    </div>
                    <input type="hidden" name="question_id" id="question_id" value="'.($data->item_id ?? null).'" class="form-control">
                    <input type="hidden" name="assignment_id" id="assignment_id" value="'.$assignment_id.'" class="form-control">
                    <textarea type="text" '.$disabled.' placeholder="" name="question" id="question" class="form-control">'.($data->question ?? null).'</textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group" style="max-width:300px">
                        <label class="custom-label"><strong>Answer Type</strong> <span class="required">*</span></label>
                        <select '.$disabled.' data-width="100%" class="form-control selectpicker" name="answer_type" id="answer_type">';

                        foreach($answer_types as $key => $value) {
                            $html_content .= '<option '.(isset($data->answer_type) && ($data->answer_type == $key) ? "selected" : null).' value="'.$key.'">'.$value.'</option>';
                        }

                        $html_content .= '</select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" style="max-width:300px">
                        <label class="custom-label"><strong>Difficulty Level</strong></label>
                        <select '.$disabled.' data-width="100%" class="form-control selectpicker" name="difficulty" id="difficulty">';

                        foreach($difficulty as $key => $value) {
                            $html_content .= '<option '.(isset($data->difficulty) && ($data->difficulty == $key) ? "selected" : null).' value="'.$key.'">'.$value.'</option>';
                        }

                        $html_content .= '</select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="marks"><strong>Question Mark</strong> <span class="required">*</span></label>
                        <input '.$disabled.' type="number" min="1" max="100" id="marks" value="'.($data->marks ??  1).'" max="100" name="marks" placeholder="Please enter the mark" step="1" class="form-control">
                    </div>
                </div>
            </div>
            <div class="numeric-answer" style="display:none">
                <div class="form-group">
                    <label for="numeric-answer">Enter the answer <span class="required">*</span></label>
                    <input '.$disabled.' type="number" value="'.($data->correct_answer ??  null).'" name="numeric_answer" placeholder="Please type the answer" step="2" class="form-control">
                </div>
            </div>
            <div class="answers-div">
                <div class="form-group table-responsive trix-slim-scroll">
                    <table class="table">';
                    $i = 0;
                    foreach($options_array as $option => $value) {
                        
                        $i++;

                        $html_content .= '
                        <tr>
                            <td width="16%" class="text-uppercase"><strong>'.$value.'</strong> '.($i < 3 ? " <span class='required'>*</span>" : "").'</td>
                            <td><input '.$disabled.' type="text" name="'.$option.'" id="'.$option.'" value="'.($data->{$option} ?? null).'" class="col-lg-12 objective_question form-control"></td>
                            <td width="7%"><input '.$disabled.' type="checkbox" '.((!empty($data->correct_answer) && in_array($option, $answer_array)) ? "checked" : null).' value="'.$option.'" name="answer_option" style="height:20px; width:20px" class="checkbox cursor"></td>
                        </tr>';
                    }

            $html_content .= '
                    </table>
                </div>                  
            </div>
            '.($isActive ? 
                '<div class="form-group text-center">
                    <button onclick="return cancel_AssignmentQuestion()" class="btn btn-outline-danger btn-sm"><i class="fa fa-save"></i> Cancel</button>
                    <button onclick="return save_AssignmentQuestion(\''.$assignment_id.'\')" class="btn btn-outline-success btn-sm"><i class="fa fa-save"></i> Save Question</button>
                </div>' : ''
            );
        

        return $html_content;

        
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
    public function textarea_editor($data = null, $name = "faketext", $id = "ajax-form-content", $predefined = "description") {

        // set the form
        $data = !empty($data) ? str_ireplace("'", "", $data) : null;
        $name = empty($name) ? "faketext" : $name;
        $form_content = "<input type='hidden' hidden id='trix-editor-input' value='{$data}'>";
        $form_content .= "<trix-editor name=\"{$name}\" data-predefined_name=\"{$predefined}\" input='trix-editor-input' class=\"trix-slim-scroll {$this->default_height}\" id=\"{$id}\"></trix-editor>";

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
    public function list_attachments($attachment_list = null, $user_id = null, $list_class = "col-lg-4 col-md-6", $is_deletable = false, $show_view = false, $show_controls = true) {

        // variables
        $list_class = empty($list_class) ? "col-lg-4 col-md-6" : $list_class;
        
        // images mimetypes for creating thumbnails
        $image_mime = ["jpg", "jpeg", "png", "gif", ".webp"];
        $docs_mime = ["pdf", "doc", "docx", "txt", "rtf", "jpg", "jpeg", "png", "gif"];
        $video_mime = ["mp4", "mpeg", "movie", "webm", "mov", "mpg", "mpeg", "qt", "flv"];

        // set the thumbnail path
        $tmp_path = "assets/uploads/{$user_id}/tmp/thumbnail/";

        // create directories if none existent
        if(!is_dir("assets/uploads/{$user_id}")) {
            mkdir("assets/uploads/{$user_id}/tmp/thumbnail/", 0777, true);
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

                } else {

                    // if the file exists
                    if(is_file($eachFile->path) && file_exists($eachFile->path)) {

                        // is image check
                        $isPDF = in_array($eachFile->type, ["pdf"]);
                        $isImage = in_array($eachFile->type, $image_mime);
                        $isVideo = in_array($eachFile->type, $video_mime);

                        // set the file to download
                        $record_id = isset($eachFile->record_id) ? $eachFile->record_id : null;

                        // set the file id and append 4 underscores between the names
                        $file_to_download = base64_encode($eachFile->path."{$this->underscores}{$record_id}");

                        // preview link
                        $preview_link = "data-function=\"load-form\" data-resource=\"file_attachments\" data-module-id=\"{$user_id}_{$eachFile->unique_id}\" data-module=\"preview_file_attachment\"";
                        
                        // set the new name to push
                        $eachFile->name = isset($eachFile->related_info->name) ? $eachFile->related_info->name : $eachFile->name;

                        // set init
                        $thumbnail = "
                        <div style=\"height:150px\" title=\"Click to preview: {$eachFile->name}\" data-toggle=\"tooltip\">
                            <div><span class=\"text text-{$eachFile->color}\"><i class=\"{$eachFile->favicon} fa-10x\"></i></span></div>
                        </div>";
                        
                        $caption = "
                        <div class=\"file_caption text-left\">
                            <span {$preview_link}><strong>{$eachFile->name}</strong></span>
                            <span class=\"text-muted tx-11\">({$eachFile->size})</span>
                            <br><strong class=\"mt-2\"><i class=\"fa fa-calendar\"></i> ".date("jS F Y", strtotime($eachFile->datetime))."</strong>
                        </div>";

                        $view_option = $show_view ? "<button onclick=\"return load('{$show_view}/{$record_id}_{$eachFile->unique_id}');\" title=\"Click to view details of file\" class=\"btn btn-sm btn-primary\"><i class=\"fa fa-eye\"></i></button>" : "";
                        $image_desc = "";
                        $delete_btn = "";

                        // show the view of the item
                        if(isset($eachFile->is_editable)) {
                            $view_option .= "&nbsp;<button onclick=\"return load('{$eachFile->is_editable}/{$record_id}');\" title=\"Click to edit the material\" class=\"btn btn-sm btn-warning\"><i class=\"fa fa-edit\"></i></button>";
                        }

                        // display this if the object is deletable.
                        if($is_deletable) {
                            $delete_btn = "&nbsp;<button onclick=\"return delete_existing_file_attachment('{$record_id}_{$eachFile->unique_id}');\" class='btn btn-sm anchor btn-outline-danger'><i class='fa fa-trash'></i></button>";
                        }

                        $the_class = "attachment-item";
                        $padding = "style='padding:10px'";
                        
                        // if the file is an type
                        if($isImage) {
                            $list_class = $list_class;
                            // get the file name
                            $filename = "{$tmp_path}{$eachFile->unique_id}.{$eachFile->type}";
                            
                            // if the file does not already exist
                            if(!is_file($filename) && !file_exists($filename)) {
                                // create a new thumbnail of the file
                                create_thumbnail($eachFile->path, $filename);
                            } else {
                                $thumbnail = "<div style=\"height:100%\"><img height=\"100%\" width=\"100%\" src=\"{$this->baseUrl}{$filename}\"></div>";
                            }
                            $image_desc = "
                                <div class=\"gallery-desc\">
                                    <a class=\"image-popup anchor\" href=\"{$this->baseUrl}{$eachFile->path}\" title=\"{$eachFile->name} ({$eachFile->size}) on {$eachFile->datetime}\">
                                        <i class=\"fa fa-search\"></i>
                                    </a>
                                </div>";
                        } else if($isVideo) {
                            // get the file name
                            $filename = "{$eachFile->path}";
                            $padding = "style='padding:0px'";
                            // set the video file
                            $thumbnail = "
                            <div onclick=\"return load('{$show_view}/{$record_id}_{$eachFile->unique_id}');\">
                                <video ".($show_controls ? "controls='true'" : null)." style='display: block; cursor:pointer; width:100%;' src='{$this->baseUrl}{$filename}#t=5'></video>
                            </div>";
                        }
                        
                        // append to the list
                        $files_list .= "<div data-file_container='{$record_id}_{$eachFile->unique_id}' class=\"{$list_class} attachment-container text-center p-1 mb-2\">";
                        $files_list .= $isImage ? "<div class=\"gallery-item\">" : null;
                            $files_list .= "
                                <div class=\"col-lg-12 p-0 {$the_class} border\" {$padding} data-attachment_item='{$record_id}_{$eachFile->unique_id}'>
                                    <span style=\"display:none\" class=\"file-options\" data-attachment_options='{$record_id}_{$eachFile->unique_id}'>
                                        {$view_option}
                                        ".($isPDF ? 
                                            "
                                            <a title=\"Click to view\" onclick=\"return preview_pdf('{$eachFile->path}')\" class=\"btn btn-sm btn-outline-primary\" href=\"#\">
                                                <i style=\"font-size:12px\" class=\"fa fa-eye fa-1x\"></i>
                                            </a>" : null
                                        )."
                                        <a title=\"Click to Download\" target=\"_blank\" class=\"btn btn-sm btn-outline-success\" href=\"{$this->baseUrl}download?file={$file_to_download}\">
                                            <i style=\"font-size:12px\" class=\"fa fa-download fa-1x\"></i>
                                        </a>
                                        {$delete_btn}    
                                    </span> 
                                    {$thumbnail} {$image_desc}
                                </div>
                                {$caption}
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
     * @param stdClass $params
     * 
     * @return String
     */
    public function form_attachment_placeholder($params = null, $width = "col-lg-12") {
        
        // initialize
        $preloaded_attachments = "";
        
        // set a new parameter for the items
        $files_param = (object) [
            "userData" => $params->userData ?? $this->thisUser,
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
        <div class=\"{$width}\">
            <div class='post-attachment'>
                <div class='row'>
                    <div class=\"col-lg-12\" id=\"".($params->module ?? null)."\">
                        <div class=\"file_attachment_url\" data-url=\"{$this->baseUrl}api/files/attachments\"></div>
                    </div>
                    <div class=\"".(isset($params->class) ? $params->class : "col-md-12")." text-left\">
                        <div class='d-flex justify-content-between'>";
                        if(!isset($params->no_title)) {
                            $html_content .= "<label>Attachments ".(empty($params->no_notice) ? "<small class='text-danger d-none d-sm-block'>(Max size <strong>{$this->max_attachment_size}MB</strong>)</small>" : null)."</label><br>";
                        }
                    $html_content .= "
                            <div class=\"ml-3\">
                                <button type='button' id='".($params->input_button ?? "ajax-upload-input")."' class='btn btn-outline-primary'>Select Files</button>
                                <input hidden ".(isset($params->accept) && !empty($params->accept) ? "accept='{$params->accept}'" : null)." ".(isset($params->ismultiple) && !empty($params->ismultiple) ? "multiple" : null)." class='form-control cursor ".($params->form_input_class ?? "attachment_file_upload")."' data-form_item_id=\"".($params->item_id ?? "temp_attachment")."\" data-form_module=\"".($params->module ?? null)."\" type=\"file\" name=\"".($params->form_input_class ?? "attachment_file_upload")."\" id=\"".($params->form_input_class ?? "attachment_file_upload")."\">
                            </div>
                            <div class=\"upload-document-loader hidden\"><span class=\"float-right\">Uploading <i class=\"fa fa-spin fa-spinner\"></i></span></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class=\"col-md-12\">
                <div class=\"file-preview mb-3 slim-scroll\" preview-id=\"".($params->module ?? null)."\">{$fresh_attachments}</div>
                <div class='form-group text-center mb-1'>{$preloaded_attachments}</div>
            </div>";
            $html_content .= !isset($params->no_footer) ? "<div class=\"col-lg-12 mb-3 border-bottom mt-3\"></div>" : null;
        $html_content .= "</div>";

        return $html_content;
        
    }

    /**
     * A global space for uploading temporary files
     * 
     * @param stdClass $params
     * 
     * @return String
     */
    public function comments_form_attachment_placeholder($params = null) {
        
        // existing
        $preloaded_attachments = "";
        
        // set a new parameter for the items
        $files_param = (object) [
            "userData" => $params->userData ?? $this->thisUser,
            "label" => "list",
            "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
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
                "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
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
                        $html_content .= "<label>Attachments <small class='text-danger d-none d-sm-block'>(Max size <strong>{$this->max_attachment_size}MB</strong>)</small></label><br>";
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
    public function modify_guardian_ward($data) {

        $array = [
            "guardian" => [
                "title" => "Student Name",
                "type" => "student",
                "key" => "user_id",
                "attr" => "guardian_id"
            ],
            "student" => [
                "key" => "user_id",
                "title" => "Guardian Name",
                "type" => "guardian",
                "attr" => "student_id"
            ]
        ];

        // set the key for the data to load
        $key = $data->{$array[$data->user_type]["key"]};

        $html_content = "<div class='row'>";
        $html_content .= "<div class='col-md-10'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>{$array[$data->user_type]["title"]}</label>";
        $html_content .= "<input autocomplete='Off' type='text' placeholder='Search {$array[$data->user_type]["title"]} name' name='user_name_search' id='user_name_search' class='form-control'>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-2'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Filter</label>";
        $html_content .= "<button onclick='return search_usersList(\"{$array[$data->user_type]["type"]}\")' class='btn btn-outline-success btn-block'><i class='fa fa-filter'></i></button>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-12 mt-2' data-{$array[$data->user_type]["attr"]}='{$key}' id='user_search_list'>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "
        <script>$(`input[name='user_name_search']`).on('keyup', function(evt) {
            evt.preventDefault();
            if (evt.keyCode == 13 && !evt.shiftKey) {
                search_usersList(\"{$array[$data->user_type]["type"]}\");
            }
        });</script>";

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

        // set the default height
        $this->default_height = 'height-250';

        $html_content = "
        <form action='{$this->baseUrl}api/courses/".(!$title ? "add_unit" : "update_unit")."' autocomplete='Off' method='POST' id='_ajax-data-form-content' class='_ajax-data-form'>
            <div class='row'>
                <div class='col-lg-12'>
                    <div class='form-group'>
                        <label>Unit Title</label>
                        <input value='{$title}' type='text' name='name' id='name' class='form-control'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Week Start Date</label>
                        <input value='".($params->data->start_date ?? null)."' type='text' name='start_date' id='start_date' data-maxdate='".date("Y-m-d", strtotime("+6 months"))."' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Week End Date</label>
                        <input value='".($params->data->end_date ?? null)."' type='text' name='end_date' id='end_date' data-maxdate='".date("Y-m-d", strtotime("+6 months"))."' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Overview / Objective</label>
                        {$this->textarea_editor($message, "faketext", "_ajax-form-content")}
                    </div>
                </div>
                <div class='col-md-12 d-flex justify-content-between'>
                    <div class=\"text-right\">
                        <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                    </div>
                    <div class=\"text-left\">
                        <input type=\"hidden\" name=\"course_id\" id=\"course_id\" value=\"{$course_id}\" hidden class=\"form-control\">
                        <input type=\"hidden\" name=\"unit_id\" id=\"unit_id\" value=\"{$item_id}\" hidden class=\"form-control\">
                        <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Record</button>
                    </div>
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
            "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf", ".ppt", ".pptx"]),
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
                    <div class='col-md-12 mt-4 ".(!empty($message) ? "border-top" : "")." pt-4 mb-3 text-center'><span class='btn btn-outline-secondary' data-dismiss='modal'>Close Modal</span></div>
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
                    "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
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

            // set the default height
            $this->default_height = 'height-250';

            $html_content = "
            <form action='{$this->baseUrl}api/courses/".(!$title ? "add_lesson" : "update_lesson")."' autocomplete='Off' method='POST' id='_ajax-data-form-content' class='_ajax-data-form'>
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
                            <input value='".($params->data->start_date ?? null)."' type='text' name='start_date' id='start_date' data-maxdate='".date("Y-m-d", strtotime("+6 months"))."' class='form-control datepicker'>
                        </div>
                    </div>
                    <div class='col-md-6'>
                        <div class='form-group'>
                            <label>End Date</label>
                            <input value='".($params->data->end_date ?? null)."' type='text' name='end_date' id='end_date' data-maxdate='".date("Y-m-d", strtotime("+6 months"))."' class='form-control datepicker'>
                        </div>
                    </div>
                    <div class='col-md-12'>
                        <div class='form-group'>
                            <label>Description</label>
                            {$this->textarea_editor($message, "faketext", "_ajax-form-content")}
                        </div>
                    </div>";

                    $html_content .= "<div class='col-lg-12'>
                        <div class='form-group text-center mb-1'><div class='row'>{$this->form_attachment_placeholder($form_params)}</div></div>
                    </div>";
                    
                    $html_content .= "<div class='col-md-12 text-center mb-2'>{$preloaded_attachments}</div>";

                    $html_content .= "
                    <div class='col-md-12 d-flex justify-content-between'>
                        <div class=\"text-right\">
                            <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                        </div>
                        <div class=\"text-left\">
                            <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Record</button>
                            <input type=\"hidden\" name=\"course_id\" id=\"course_id\" value=\"{$course_id}\" hidden class=\"form-control\">
                            <input type=\"hidden\" name=\"unit_id\" id=\"unit_id\" value=\"{$unit_id}\" hidden class=\"form-control\">
                            <input type=\"hidden\" name=\"lesson_id\" id=\"lesson_id\" value=\"{$item_id}\" hidden class=\"form-control\">
                        </div>
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
        <form id='_ajax-data-form-content' class='_ajax-data-form' autocomplete='Off' enctype=\"multipart/form-data\" action=\"{$this->baseUrl}api/resources/upload_4courses\" method=\"POST\">
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
                    <div style=\"height:350px; overflow-y:auto;\" class=\"slim-scroll\">
                        <div class=\"form-group pt-0 mt-2\">
                            <table class=\"table table-bordered table-sm\">
                                <tbody>";
                                foreach($lessons_list as $each) {
                                    $html_content .= "
                                        <tr class=\"pt-0 pb-0\">
                                            <td style=\"height:30px\">
                                                <input type=\"checkbox\" ".(in_array($each->item_id, $lesson_ids) ? "checked" : "")." class=\"form-control checkbox-box\" value=\"{$each->item_id}\" name=\"upload[lesson_id][]\" id=\"lesson_id[{$each->item_id}][]\">
                                            </td>
                                            <td style=\"height:30px\">
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
            "form_input_class" => "_attachment_file_upload",
            "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
            "input_button" => "_ajax-upload-input",
            "ismultiple" => true,
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
                    <div class='col-md-12 mb-2'><h5 class=\"text-uppercase\">{$params->data->subject}</h5></div>
                    <div class='col-md-12'><strong>Incident Date:</strong> {$params->data->incident_date}</div>
                    <div class='col-md-12'><strong>Current State:</strong> {$this->the_status_label($params->data->status)}</div>
                    <div class='col-md-12'><strong>Location:</strong> {$params->data->location}</div>
                    <div class='col-md-12'><strong>Reported By:</strong> {$params->data->reported_by}</div>
                    ".(
                        !empty($params->data->assigned_to_info->name) ? "
                        <div class='col-md-6 mb-2 mt-3'>
                            <h6>ASSIGNED TO:</h6>
                            <div><strong>Name:</strong> ".($params->data->assigned_to_info->name ?? null)."</div>
                            <div><strong>Email:</strong> ".($params->data->assigned_to_info->email ?? null)."</div>
                            ".(!empty($params->data->assigned_to_info->contact) ? "<div><strong>Contact:</strong> ".($params->data->assigned_to_info->contact ?? null)."</div>" : null)."
                        </div>" : ""
                    )."
                    <div class='col-md-12 mb-2 border-top pt-3'>{$message}</div>
                    ".(isset($attached) && !empty($attached->files) ? "
                        <div class='col-md-12 border-bottom mb-3 mt-4'><h6>ATTACHMENTS</h6></div>
                        <div class='col-md-12'>{$attachments}</div>" : ""
                    )."
                    ".(!empty($followups) ? "
                        <div class='col-md-12 border-bottom mb-3 mt-2'>
                            <div class='d-flex justify-content-between pb-1'>
                                <div>
                                    <h6>FOLLOWUPS</h6>
                                </div>
                                <div>
                                    <span class='btn btn-outline-secondary' data-dismiss='modal'>Close Modal</span>
                                </div>
                            </div>
                        </div>
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
                    "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
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
            <form autocomplete='Off' action='{$this->baseUrl}api/incidents/".(!$title ? "add" : "update")."' method='POST' id='_ajax-data-form-content' class='_ajax-data-form'>
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
                                foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('employee','teacher','admin','accountant') AND status='1' AND client_id='{$params->clientId}'") as $each) {
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
                                    <option value="">Select Status</option>
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
                            {$this->textarea_editor($message, "faketext", "_ajax-form-content")}
                        </div>
                    </div>";

                    $html_content .= "<div class='col-lg-12'>
                        <div class='form-group text-center mb-1'><div class='row'>{$this->form_attachment_placeholder($form_params)}</div></div>
                    </div>";
                    
                    $html_content .= "<div class='col-md-12 text-center mb-2'>{$preloaded_attachments}</div>";

                    $html_content .= "
                    <div class='col-lg-12'>
                        <div class='d-flex justify-content-between'>
                            <div>
                                <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                            </div>
                            <div>
                                <button class=\"btn btn-outline-success btn-sm\" data-function=\"save\" type=\"button-submit\">Save Record</button>
                                <input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"{$user_id}\" hidden class=\"form-control\">
                                <input type=\"hidden\" name=\"incident_id\" id=\"incident_id\" value=\"{$item_id}\" hidden>
                            </div>
                        </div>
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
                            <img width=\"40px\" class=\"img-xs rounded-circle\" src=\"{$this->baseUrl}{$data->created_by_information->image}\" alt=\"\">
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
    public function incident_log_followup_form($item_id, $clientId, $user_id = null, $list_only = false, $followups = []) {
        
        /** Initializing */
        $prev_date = null;
        $html_content = "<div id='incident_log_followup_list' class='trix-slim-scroll drawer-content'>";
        $followups_list = "";

        /** Load the followups for the incident */
        $q_param = (object) [
            "user_id" => $user_id, "incident_type" => "followup", 
            "followup_id" => $item_id, "clientId" => $clientId
        ];
        $followups = empty($followups) ? load_class("incidents", "controllers")->list($q_param)["data"] : $followups;

        /** Loop through the followups */
        foreach($followups as $followup) {

            /** Clean date */
            $clean_date = date("l, jS F Y", strtotime($followup->date_created));
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
            <div class='mb-4 stick_to_top p-3 bg-blue-grey'>
                <div class='form-group'>
                    <label></label>
                    <textarea class='form-control' placeholder='Add a followup message...' name='incident_followup' id='incident_followup'></textarea>
                </div>
                <div class='d-flex justify-content-between'>
                    <div><button data-resource_id='{$item_id}' onclick='return post_incident_followup(\"{$user_id}\",\"{$item_id}\")' id='post_incident_followup' class='btn btn-outline-success'>Share Comment</button></div>
                    <div>
                        <button type=\"reset\" class=\"btn btn-outline-danger\" class=\"close\" data-dismiss=\"modal\">Close</button>
                    </div>
                </div>
            </div>";
        
        $close = '';

        return !$list_only ? $form_content.$html_content : $html_content.$close;
    }

    /**
     * Students form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * @return String
     */
    public function student_form($clientId, $baseUrl, $userData = null) {

        // global variable
        global $clientPrefs;

        // if data is parsed
        $isData = !empty($userData) && isset($userData->user_id) ? true : false;

        $unique_id = null;
        $guardian = "";
        $guardian_list = [];

        // if the guardian information is parsed
        if(!empty($userData->guardian_list)) {
            
            // loop through the information
            foreach($userData->guardian_list as $key => $eachItem) {
                $key_id = $key+1;
                $guardian .= '
                <div class="row mb-3 pb-3 '.($key_id !== 1 ? "mt-4 border-primary border-top pt-4" : null).'" data-row="'.$key_id.'">
                    <div class="col-lg-4 col-md-4 mb-3">
                        <label for="guardian_info[guardian_fullname]['.$key_id.']">Fullname</label>
                        <input type="hidden" name="guardian_info[guardian_id]['.$key_id.']" id="guardian_info[guardian_id]['.$key_id.']" value="'.$eachItem->user_id.'">
                        <input type="text" value="'.$eachItem->fullname.'" name="guardian_info[guardian_fullname]['.$key_id.']" id="guardian_info[guardian_fullname]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-4 col-md-4 mb-3">
                        <label for="guardian_info[guardian_contact]['.$key_id.']">Primary Contact</label>
                        <input type="text" value="'.$eachItem->contact.'" name="guardian_info[guardian_contact]['.$key_id.']" id="guardian_info[guardian_contact]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-3 mb-3">
                        <label for="guardian_info[guardian_email]['.$key_id.']">Email Address</label>
                        <input type="email" value="'.$eachItem->email.'" name="guardian_info[guardian_email]['.$key_id.']" id="guardian_info[guardian_email]['.$key_id.']" class="form-control">
                    </div>
                    <div class="col-lg-1 col-md-1 text-right">';
                        if($key_id == 1) {
                            $guardian .= '
                            <div class="mr-1 form-group">
                                <label class="text-white"></label>
                                <button data-row="'.$key_id.'" onclick="return append_student_guardian_row()" class="btn form-control btn-primary" type="button"><i class="fa fa-user"></i> Add</button>
                            </div>';
                        } else {
                            $guardian .= '
                            <div class="mr-1"><br>
                                <button data-row="'.$key_id.'" class="btn remove_guardian_row btn-danger" type="button"><i class="fa fa-trash"></i></button>
                            </div>';
                        }
                        $guardian .= '
                    </div>
                    <div class="col-lg-4 col-md-4 mt-2">
                        <label for="guardian_info[guardian_relation]['.$key_id.']">Relationship</label>
                        <select data-width="100%" name="guardian_info[guardian_relation]['.$key_id.']" id="guardian_info[guardian_relation]['.$key_id.']" class="form-control selectpicker">
                            <option value="">Select Relation</option>';
                            foreach($this->pushQuery("id, name", "guardian_relation", "status='1'") as $each) {
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

        // user logo
        $logoUploaded = (bool) ($isData && $userData->image);

        // get the list of all guardians
        if(!$isData) {
            $guardian_list = $this->pushQuery("name, item_id, unique_id, phone_number, email", "users", "client_id='{$clientId}' AND user_type='parent' AND status='1' AND deleted='0'");
        }

        // auto generate the student id
        if(empty($isData)) {
            $counter = $this->append_zeros(($this->itemsCount("users", "client_id = '{$clientId}' AND user_type='student'") + 1), $this->append_zeros);
            $unique_id = $clientPrefs->labels->student_label."/".$counter."/".date("Y");
        }

        $response = '
        <form autocomplete="Off" class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4">
                <div class="col-lg-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">BIO INFORMATION</h5>
                </div>
                <div class="col-md-9">
                    <div class="row mb-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="unique_id">Student ID '.(!$isData ? '<small class="text-danger">Auto generated ID</small>' : null).'</label>
                                <input type="text" readonly value="'.($userData->unique_id ?? $unique_id).'" '.($isData ? "disabled='disabled'" : null).' name="unique_id" id="unique_id" class="form-control text-uppercase">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="enrollment_date">Enrollment Date <span class="required">*</span></label>
                                <input type="text" data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.($userData->enrollment_date ?? null).'" name="enrollment_date" id="enrollment_date" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select data-width="100%" name="gender" id="gender" class="form-control selectpicker">
                                    <option value="">Select Gender</option>';
                                    foreach($this->pushQuery("*", "users_gender") as $each) {
                                        $response .= "<option ".($isData && ($each->name == $userData->gender) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                                    }
                            $response .= '</select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="firstname">Firstname <span class="required">*</span></label>
                                <input type="text" value="'.($userData->firstname ?? null).'" name="firstname" id="firstname" class="form-control">
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="lastname">Lastname <span class="required">*</span></label>
                                <input type="text" value="'.($userData->lastname ?? null).'" name="lastname" id="lastname" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8">
                            <div class="form-group">
                                <label for="othername">Othernames</label>
                                <input type="text" value="'.($userData->othername ?? null).'" name="othername" id="othername" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5">
                            <div class="form-group">
                                <label for="is_bus_user">Is Bus User</label>
                                <select data-width="100%" name="is_bus_user" id="is_bus_user" class="form-control selectpicker">
                                    <option value="">Please Select</option>';
                                    foreach(["Yes", "No"] as $item) {
                                        $response .= "<option ".($isData && ($item == $userData->is_bus_user) ? "selected" : null)." value=\"{$item}\">{$item}</option>";                            
                                    }
                            $response .= '</select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="text" value="'.($userData->date_of_birth ?? null).'" name="date_of_birth" id="date_of_birth" data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-8">
                            <div class="form-group">
                                <label for="alergy">Alergies</label>
                                <input type="text" value="'.($userData->alergy ?? null).'" name="alergy" id="alergy" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-3">
                    '.($logoUploaded ? 
                        '<div class="form-group">
                            <img width="100%" src="'.$this->baseUrl.''.$userData->image.'">
                        </div>' : '
                        <div class="form-group">
                            <img width="100%" src="'.$this->baseUrl.'assets/img/avatar.png">
                        </div>'
                    ).'
                    <div class="form-group">
                        <label for="image">Student Image</label>
                        <input data-student_image="upload" data-student_id="'.($userData->user_id ?? null).'" accept=".jpg,.jpeg,.png,.gif" type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12"><h5 class="text-primary border-bottom pb-2 mb-3">CONTACT INFORMATION</h5></div> 
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
                <div class="col-lg-12 mt-3"><h5 class="text-primary border-bottom pb-2 mb-3">LOCATION & OTHERS</h5></div>  
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select data-width="100%" name="country" id="country" class="form-control selectpicker">
                            <option value="">Select Country</option>';
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
                        <label for="place_of_birth">Place of Birth</label>
                        <input type="text" value="'.($userData->place_of_birth ?? null).'" name="place_of_birth" id="place_of_birth" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="hometown">Hometown</label>
                        <input type="text" value="'.($userData->hometown ?? null).'" name="hometown" id="hometown" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="blood_group">Blood Broup</label>
                        <select data-width="100%" name="blood_group" id="blood_group" class="form-control selectpicker">
                            <option value="">Select Blood Group</option>';
                            foreach($this->pushQuery("id, name", "blood_groups") as $each) {
                                $response .= "<option ".($isData && ($each->name == $userData->blood_group) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                        <input type="hidden" id="user_type" name="user_type" value="'.(!$isData ? "student" : null).'">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="religion">Religion</label>
                        <select data-width="100%" name="religion" id="religion" class="form-control selectpicker">
                            <option value="">Select Region</option>';
                            foreach($this->pushQuery("id, name", "religions") as $each) {
                                $response .= "<option ".($isData && ($each->name == $userData->religion) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                        $response .= '
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea type="text" name="description" id="description" class="form-control height-90px">'.($userData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb-3 pb-4">
                <div class="col-lg-12">
                    <div class="row mb-4">
                        <div class="col-lg-12"><h5 class="text-primary border-bottom pb-2 mb-3">ACADEMICS</h5></div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="department_id">Department</label>
                                <select data-width="100%" name="department_id" id="department_id" class="form-control selectpicker">
                                    <option value="">Select Student Department</option>';
                                    foreach($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                        $response .= "<option ".($isData && ($each->id == $userData->department) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                                    }
                                $response .= '</select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="class_id">Class</label>
                                <select data-width="100%" name="class_id" id="class_id" class="form-control selectpicker">
                                    <option value="">Select Student Class</option>';
                                    foreach($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$clientId}'") as $each) {
                                        $response .= "<option ".($isData && ($each->id == $userData->class_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                                    }
                                $response .= '</select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label for="section">Section</label>
                                <select data-width="100%" name="section" id="section" class="form-control selectpicker">
                                    <option value="">Select Student Section</option>';
                                    foreach($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                        $response .= "<option ".($isData && ($each->id == $userData->section) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                                    }
                                $response .= '</select>
                            </div>
                        </div>
                        <div class="col-lg-12 mt-3"><h5 class="text-primary border-bottom pb-2 mb-3">PREVIOUS SCHOOL INFORMATION</h5></div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="previous_school">Previous School</label>
                                <input type="text" value="'.($userData->previous_school ?? null).'" name="previous_school" id="previous_school" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label for="previous_school_qualification">Previous School Class</label>
                                <input type="text" value="'.($userData->previous_school_qualification ?? null).'" name="previous_school_qualification" id="previous_school_qualification" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label for="previous_school_remarks">Remarks</label>
                                <textarea type="text" name="previous_school_remarks" id="previous_school_remarks" class="form-control height-90px">'.($userData->previous_school_remarks ?? null).'</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mb-4 border-bottom pb-3">
                    <div class="row">
                        <div class="col-lg-8 col-md-8"><h5 class="text-primary mb-0">GUARDIAN INFORMATION</h5></div>
                        '.(
                            !$isData ? '
                            <div class="col-lg-4 col-md-4">
                                <div class="d-flex justify-content-between">
                                    <div class="col-9">
                                        <select id="switch_select" data-width="100%" class="selectpicker form-control">
                                            <option value="add_new">Add New Guardian</option>
                                            <option value="select_existing">Select Existing Guardian</option>
                                        </select>
                                    </div>
                                    <div>
                                        <button onclick="return append_student_guardian_row()" data-row="1" class="btn form-control btn-primary" type="button"><i class="fa fa-user"></i> Add</button>
                                    </div>
                                </div>
                            </div>' : null
                        ).'

                    </div>
                </div>
                <div class="col-lg-12" id="student_guardian_list">';
                
                // if the data
                if($isData && !empty($guardian)) {
                    $response .= $guardian;
                } else {
                    $response .= '
                    <div class="row m-t-20" data-row="1">
                        <div class="col-lg-4 col-md-4">
                            <label for="guardian_info[guardian_fullname][1]">Fullname</label>
                            <input type="hidden" name="guardian_info[guardian_id][1]" id="guardian_info[guardian_id][1]" value="'.random_string("nozero", 8).'">
                            <input type="text" name="guardian_info[guardian_fullname][1]" id="guardian_info[guardian_fullname][1]" class="form-control">
                        </div>                        
                        <div class="col-lg-4 col-md-4">
                            <label for="guardian_info[guardian_contact][1]">Primary Contact</label>
                            <input type="text" name="guardian_info[guardian_contact][1]" id="guardian_info[guardian_contact][1]" class="form-control">
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label for="guardian_info[guardian_contact_2][1]">Secondary Contact</label>
                            <input type="text" name="guardian_info[guardian_contact_2][1]" id="guardian_info[guardian_contact_2][1]" class="form-control">
                        </div>
                        <div class="col-lg-3 col-md-3 mt-2">
                            <label for="guardian_info[guardian_email][1]">Email Address</label>
                            <input type="text" name="guardian_info[guardian_email][1]" id="guardian_info[guardian_email][1]" class="form-control">
                        </div>
                        <div class="col-lg-3 col-md-3 mt-2">
                            <label for="guardian_info[guardian_relation][1]">Relationship</label>
                            <select data-width="100%" name="guardian_info[guardian_relation][1]" id="guardian_info[guardian_relation][1]" class="form-control selectpicker">
                                <option value="">Select Relation</option>';
                                foreach($this->pushQuery("id, name", "guardian_relation", "status='1'") as $each) {
                                    $response .= "<option value=\"{$each->name}\">{$each->name}</option>";                            
                                }
                        $response .= '</select>
                        </div>
                        <div class="col-lg-6 col-md-6 mt-2">
                            <label for="guardian_info[guardian_address][1]">Address</label>
                            <input type="text" name="guardian_info[guardian_address][1]" id="guardian_info[guardian_address][1]" class="form-control">
                        </div>
                    </div>';
                }

                $response .= '</div>';

                // if not data was parsed
                if(!$isData || $isData && empty($guardian)) {

                    // append to the html content
                    $response .= '<div class="col-lg-12" style="display:none" id="student_guardian_list_existing">';
                    $response .= '<div class="form-group">
                        <label>Select Existing Guardian</label>
                        <select name="guardian_id" id="guardian_id" data-width="100%" class="selectpicker form-control">
                            <option value="">Select Guardian</option>';
                    
                    // loop through the guardian list
                    foreach($guardian_list as $guardian) {
                        $response .= "<option value=\"{$guardian->item_id}\">{$guardian->name} ($guardian->unique_id) - {$guardian->phone_number}</option>";
                    }

                        
                    $response .= '</select>';
                    $response .= '</div>';
                    $response .= '</div>';
                }
                $response .= '
            </div>
            <input type="hidden" id="user_id" value="'.($userData->user_id ?? null).'" name="user_id">
            <div class="d-flex justify-content-between">
                <div class="">
                    '.($isData ? 
                        '<button onclick="return load(\'student/'.$userData->user_id.'\');" type="button" class="btn btn-dark"><i class="fa fa-ban"></i> Discard Changes</button>': 
                        'NB: <span class="text-danger">Default password for this account is: <strong class="font-18">'.$this->defaultPassword.'</strong></span>'
                    ).'
                </div>
                <div class=" text-right">
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
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
     * @return String
     */
    public function guardian_form($clientId, $baseUrl, $userData = null) {

        $isData = !empty($userData) && isset($userData->user_id) ? true : false;

        // global variable
        global $clientPrefs;

        // init variable
        $unique_id = null;

        // auto generate the student id
        if(empty($isData)) {
            $counter = $this->append_zeros(($this->itemsCount("users", "client_id = '{$clientId}' AND user_type = 'parent'") + 1), $this->append_zeros);
            $unique_id = $clientPrefs->labels->parent_label."/".$counter."/".date("Y");
        }

        $response = '
        <form autocomplete="Off" class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12"><h5 class="text-primary">BIO INFORMATION</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Guardian Image</label>
                        <input accept=".jpg,.jpeg,.png,.gif" type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-'.(!empty($userData) ? 4 : 4 ).' col-md-6">
                    <div class="form-group">
                        <label for="unique_id">Guardian ID (optional)</label>
                        <input type="text" readonly value="'.($userData->unique_id ?? $unique_id).'" name="unique_id" id="unique_id" class="form-control">
                        <input type="text" readonly value="'.($userData->user_id ?? "").'" hidden name="user_id" id="user_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select data-width="100%" name="gender" id="gender" class="form-control selectpicker">
                            <option value="">Select Gender</option>';
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
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="text" data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.($userData->date_of_birth ?? null).'" name="date_of_birth" id="date_of_birth" class="form-control datepicker">
                    </div>
                </div>
                <div class="col-lg-12"><h5 class="text-primary">CONTACT INFORMATION</h5></div>
                <div class="col-lg-8 col-md-6">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" value="'.($userData->email ?? null).'" name="email" id="email" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="contact">Primary Contact</label>
                        <input type="text" name="phone" value="'.($userData->phone_number ?? null).'" id="phone" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="contact_2">Secondary Contact</label>
                        <input type="text" name="phone_2" value="'.($userData->phone_number_2 ?? null).'" id="phone_2" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select data-width="100%" name="country" id="country" class="form-control selectpicker">
                            <option value="">Select Country</option>';
                            foreach($this->pushQuery("*", "country") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->country) ? "selected" : null)." value=\"{$each->id}\">{$each->country_name}</option>";                            
                            }
                    $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="blood_group">Blood Broup</label>
                        <select data-width="100%" name="blood_group" id="blood_group" class="form-control selectpicker">
                            <option value="">Select Blood Group</option>';
                            foreach($this->pushQuery("id, name", "blood_groups") as $each) {
                                $response .= "<option ".($isData && ($each->name == $userData->blood_group) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
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
                        <label for="address">Postal Address <span class="required">*</span></label>
                        <input type="text" value="'.($userData->address ?? null).'" name="address" id="address" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="relationship">Relationship with Ward</label>
                        <select data-width="100%" name="relationship" id="relationship" class="form-control selectpicker">
                            <option value="">Select Relation</option>';
                            foreach($this->pushQuery("id, name", "guardian_relation", "status='1'") as $each) {
                                $response .= "<option ".(isset($userData->relationship) && $userData->relationship === $each->name ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12"><h5 class="text-primary">EMPLOYMENT DETAILS</h5></div>
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
                    <input type="hidden" hidden name="user_type" value="parent">
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
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
     * @return String
     */
    public function department_form($clientId, $baseUrl, $itemData = null) {

        $isData = !empty($itemData) && isset($itemData->id) ? true : false;

        $guardian = "";

        $response = '
        <form autocomplete="Off" class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/departments/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12"><h5>DEPARTMENT INFORMATION</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Department Image</label>
                        <input accept=".jpg,.jpeg,.png,.gif" type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="department_code">Department Code (optional)</label>
                        <input type="text" value="'.($itemData->department_code ?? null).'" name="department_code" id="department_code" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="department_head">Department Head</label>
                        <select data-width="100%" name="department_head" id="department_head" class="form-control selectpicker">
                            <option value="">Select Department Head</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('employee','teacher') AND status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->item_id == $itemData->department_head) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-6">
                    <div class="form-group">
                        <label for="name">Department Name<span class="required">*</span></label>
                        <input type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
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
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
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
     * @return String
     */
    public function section_form($clientId, $baseUrl, $itemData = null) {

        $isData = !empty($itemData) && isset($itemData->id) ? true : false;

        $response = '
        <form autocomplete="Off" class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/sections/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>SECTION INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Section Image</label>
                        <input accept=".jpg,.jpeg,.png,.gif" type="file" name="image" id="image" class="form-control">
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
                            <option value="">Select Section Leader</option>';
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
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
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
     * @return String
     */
    public function class_form($clientId, $baseUrl, $itemData = null) {

        $isData = !empty($itemData) && isset($itemData->id) ? true : false;

        $guardian = "";

        $response = '
        <form autocomplete="Off" class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/classes/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12"><h5>CLASS INFORMATION</h5></div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="name">Class Name<span class="required">*</span></label>
                        <input type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="form-group">
                        <label for="class_code">Class Code (optional)</label>
                        <input type="text" value="'.($itemData->class_code ?? null).'" name="class_code" id="class_code" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="form-group">
                        <label for="class_size">Class Size (optional)</label>
                        <input type="text" value="'.($itemData->class_size ?? null).'" name="class_size" id="class_size" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="class_teacher">Class Teacher</label>
                        <select data-width="100%" name="class_teacher[]" id="class_teacher" class="form-control selectpicker">
                            <option value="">Select Class Teacher</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('teacher') AND status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && in_array($each->item_id, stringToArray($itemData->class_teacher)) ? "selected" : null)." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="room_id[]">Class Rooms</label>
                        <select data-width="100%" multiple name="room_id[]" id="room_id" class="form-control selectpicker">
                            <option value="">Select Room</option>';
                            foreach($this->pushQuery("item_id, name", "classes_rooms", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && in_array($each->item_id, $itemData->rooms_list) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="'.($isData ? "col-lg-4 col-md-4" : "col-lg-4 col-md-4").'">
                    <div class="form-group">
                        <label for="department_id">Department ID</label>
                        <select data-width="100%" name="department_id" id="department_id" class="form-control selectpicker">
                            <option value="">Select Department</option>';
                            foreach($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $itemData->department_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="'.($isData ? "col-lg-4 col-md-4" : "col-lg-4 col-md-4").'">
                    <div class="form-group">
                        <label for="class_assistant">Class Prefect</label>
                        <select data-width="100%" name="class_assistant" id="class_assistant" class="form-control selectpicker">
                            <option value="">Select Class Prefect</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('student') AND status='1' AND client_id='{$clientId}' ".($isData ? " AND class_id='{$itemData->id}'" : "")."") as $each) {
                                $response .= "<option ".($isData && ($each->item_id == $itemData->class_assistant) ? "selected" : null)." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="'.($isData ? "col-lg-4 col-md-4" : "col-lg-4 col-md-4").'">
                    <div class="form-group">
                        <label for="payment_module">Payment Module</label>
                        <select data-width="100%" name="payment_module" id="payment_module" class="form-control selectpicker">';
                            foreach(["Termly", "Monthly"] as $each) {
                                $response .= "<option ".($isData && $each === $itemData->payment_module ? "selected" : null)." value=\"{$each}\">{$each}</option>";                            
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
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
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
     * @return String
     */
    public function course_form($clientId, $baseUrl, $itemData = null) {

        $isData = !empty($itemData) && isset($itemData->id) ? true : false;
        $isAdmin = !empty($itemData) && !$itemData->isAdmin ? "disabled='disabled'" : "";
        $isAdmin2 = !empty($itemData) && !$itemData->isAdmin ? "readonly='readonly'" : "";

        $response = '
        <form autocomplete="Off" class="ajax-data-form" id="ajax-data-form-content" action="'.$baseUrl.'api/courses/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="course_code">Subject Code</label>
                        <input '.$isAdmin2.' type="text" maxlength="12" value="'.($itemData->course_code ?? null).'" name="course_code" id="course_code" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="credit_hours">Credit Hours</label>
                        <input type="number" value="'.($itemData->credit_hours ?? null).'" name="credit_hours" id="credit_hours" class="form-control text-uppercase">
                    </div>
                </div>                
                <div class="col-lg-4 col-md-4">
                    <div class="form-group">
                        <label for="weekly_meeting">Weekly Meetings</label>
                        <input type="number" min="1" max="30" value="'.($itemData->weekly_meeting ?? null).'" name="weekly_meeting" id="weekly_meeting" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="form-group">
                        <label for="name">Subject Title <span class="required">*</span></label>
                        <input '.$isAdmin2.' type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="class_id">Select subject class <span class="required">*</span></label>
                        <select '.$isAdmin.' '.(!empty($itemData->name) && $isData ? "disabled" : "multiple").' data-width="100%" '.($isData ? 'name="class_id" id="class_id"' : 'name="class_id[]" id="class_id[]"').' class="form-control selectpicker">
                            <option value="">Select Class</option>';
                            foreach($this->pushQuery("id, name, item_id", "classes", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && in_array($each->item_id, $itemData->class_ids) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="course_tutor">Subject Tutors</label>
                        <select data-width="100%" '.$isAdmin.' multiple name="course_tutor[]" id="course_tutor[]" class="form-control selectpicker">';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('teacher') AND user_status='Active' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && in_array($each->item_id, $itemData->course_tutor_ids) ? "selected" : null)." value=\"{$each->item_id}\">
                                    ".ucwords($each->name)."
                                </option>";                            
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
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
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
     * @return String
     */
    public function staff_form($clientId, $baseUrl, $userData = null) {

        $isData = !empty($userData) && isset($userData->name) ? true : false;

        // global variable
        global $clientPrefs;

        // some variables
        $guardian = "";
        $unique_id = null;

        // auto generate the student id
        if(empty($isData)) {
            $counter = $this->append_zeros(($this->itemsCount("users", "client_id = '{$clientId}' AND user_type NOT IN ('student','parent')") + 1), $this->append_zeros);
            $unique_id = $clientPrefs->labels->staff_label."/".$counter."/".date("Y");
        }

        $response = '
        <form class="ajax-data-form" autocomplete="Off" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-3">
                <div class="col-lg-12">
                    <h5 class="text-primary border-bottom pb-2 mb-3">BIO INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Staff Image</label>
                        <input accept=".jpg,.jpeg,.png,.gif" type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="unique_id">Staff ID (optional)</label>
                        <input type="text" readonly value="'.($userData->unique_id ?? $unique_id).'" name="unique_id" id="unique_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="enrollment_date">Date Employed <span class="required">*</span></label>
                        <input type="text" data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.($userData->enrollment_date ?? null).'" name="enrollment_date" id="enrollment_date" class="form-control datepicker">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select data-width="100%" name="gender" id="gender" class="form-control selectpicker">
                            <option value="">Select Gender</option>';
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
                        <input type="text" data-maxdate="'.date("Y-m-d", strtotime("+1 year")).'" value="'.($userData->date_of_birth ?? null).'" name="date_of_birth" id="date_of_birth" class="form-control datepicker">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="position">Position / Role <span class="required">*</span></label>
                        <input type="text" value="'.($userData->position ?? null).'" name="position" id="position" class="form-control">
                        <input type="hidden" hidden value="'.($userData->user_id ?? null).'" name="user_id" id="user_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12"><h5 class="text-primary border-bottom pb-2 mt-3 mb-3">CONTACT INFORMATION</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" data-auto_config="email" value="'.($userData->email ?? null).'" name="email" id="email" class="form-control">
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
                        <label for="residence">Place of Residence <span class="required">*</span></label>
                        <input type="text" value="'.($userData->residence ?? null).'" name="residence" id="residence" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="country">Country</label>
                        <select data-width="100%" name="country" id="country" class="form-control selectpicker">
                            <option value="">Select Country</option>';
                            foreach($this->pushQuery("*", "country") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->country) ? "selected" : null)." value=\"{$each->id}\">{$each->country_name}</option>";                            
                            }
                    $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="blood_group">Blood Broup</label>
                        <select data-width="100%" name="blood_group" id="blood_group" class="form-control selectpicker">
                            <option value="">Select Blood Group</option>';
                            foreach($this->pushQuery("id, name", "blood_groups") as $each) {
                                $response .= "<option ".($isData && ($each->name == $userData->blood_group) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea type="text" name="description" id="description" class="form-control">'.($userData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12"><h5 class="text-primary border-bottom pb-2 mb-3">ACADEMICS</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="user_type">Designation <span class="required">*</span></label>
                        <select data-width="100%" name="user_type" id="user_type" class="form-control selectpicker">
                            <option value="teacher">Select Designation</option>';
                            foreach($this->user_roles_list as $key => $value) {
                                $response .= "<option ".($isData && ($key == $userData->user_type) ? "selected" : null)." value=\"{$key}\">{$value}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="department_id">Department <span class="required">*</span></label>
                        <select data-width="100%" name="department_id" id="department_id" class="form-control selectpicker">
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
                            <option value="">Select Section</option>';
                            foreach($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->section) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-6 '.($isData && $userData->user_type !== "teacher" ? "hidden" : "").'" data-value="course_ids_container">
                    <div class="form-group">
                        <label for="courses_ids">Class & Subjects <span class="text-danger">(Select the class and subjects taught by this teacher)</span></label>
                        <select multiple data-width="100%" name="courses_ids[]" id="courses_ids" class="form-control selectpicker">
                            <option value="">Select Subject</option>';
                            foreach($this->pushQuery("a.id, a.name, a.course_code, b.name AS class_name", "courses a LEFT JOIN classes b ON b.item_id = a.class_id", "a.status='1' AND a.client_id='{$clientId}' AND a.academic_year='{$clientPrefs->academics->academic_year}' AND a.academic_term='{$clientPrefs->academics->academic_term}' ORDER BY b.name LIMIT 100") as $each) {
                                $response .= "<option ".($isData && in_array($each->id, $userData->course_ids) ? "selected" : null)." value=\"{$each->id}\">{$each->class_name} > {$each->course_code}: {$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
            </div>
            <div class="row mb-4 pb-4 hidden">
                <div class="col-lg-12"><h5 class="text-primary border-bottom pb-2 mb-3">LOGIN INFORMATION</h5></div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" readonly data-auto_config="username" value="'.($userData->username ?? null).'" name="username" id="username" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select data-width="100%" name="status" id="status" class="form-control selectpicker">';
                            foreach($this->user_status_list as $key => $value) {
                                $response .= "<option ".($isData && ($key == $userData->user_status) ? "selected" : null)." value=\"{$key}\">{$value}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 '.(!empty($userData->username) ? 'hidden' : '').' text-left">
                    NB: <span class="text-danger">Default password for this account is:
                        <strong class="font-18">'.$this->defaultPassword.'</strong></span>
                </div>
                <div class="col-lg-'.(!empty($userData->username) ? 12 : 6).' text-right">
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;

    }

    /**
     * Event Forms
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function event_form($data = null) {

        $disabled = isset($data->state) && in_array($data->state, ["Held", "Cancelled"]) ? "disabled='disabled'" : null;

        $html_content = (!$disabled ? '<form autocomplete="Off" enctype="multipart/form-data" class="ajax-data-form" action="'.$this->baseUrl.'api/events/'.(isset($data->item_id) ? "update" : "add").'" method="POST" id="ajax-data-form-content">': '').'
            <div id="modalBody2" class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Title <span class="required">*</span></label>
                            <input type="text" '.$disabled.' value="'.($data->title ?? null).'" name="title" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type<span class="required">*</span></label>
                            <select '.$disabled.' data-width="100%" name="type" id="type" class="form-control '.(!isset($data->item_id) ? "selectpicker" : "").'">
                                <option value="">Select</option>';
                                if(isset($data->event_types)) {
                                    foreach($data->event_types as $key => $value) {
                                        $html_content .= "<option ".(isset($data->item_id) && ($data->event_type == $value->item_id) ? "selected='selected'" : "")." data-row_id='{$value->item_id}' value='{$value->item_id}'>{$value->name}</option>";
                                    }
                                }
                            $html_content .= '
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Audience<span class="required">*</span></label>
                            <select '.$disabled.' data-width="100%" name="audience" id="audience" class="form-control '.(!isset($data->item_id) ? "selectpicker" : "").'">
                                <option value="">Select</option>';
                                foreach($this->event_audience as $key => $value) {
                                    $html_content .= "<option ".(isset($data->item_id) && ($data->audience == $key) ? "selected='selected'" : "")." value='{$key}'>{$value}</option>";
                                }
                            $html_content .= '</select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Venue</label>
                            <input '.$disabled.' type="text" value="'.($data->venue ?? null).'" name="venue" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date <span class="required">*</span></label>
                            <input '.$disabled.' type="text" value="'.(isset($data->start_date) ? "{$data->start_date}:{$data->end_date}" : "").'" name="date" class="daterange form-control">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Description</label>
                            '.(!$disabled ? '
                                <input type="hidden" hidden id="trix-editor-input" value="'.($data->description ?? null).'">
                                <trix-editor '.$disabled.' name="faketext" data-predefined_name="description" input="trix-editor-input" class="trix-slim-scroll" id="ajax-form-content"></trix-editor>
                            ' : "<div>{$data->description}</div>").'
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label>Event Image</label>
                            <input type="hidden" hidden name="event_id" class="form-control" value="'.($data->item_id ?? null).'">
                            <input '.$disabled.' accept=".jpg,.jpeg,.png,.gif" type="file" name="event_image" class="form-control" id="event_image">
                        </div>';
                    // confirm that the event cover image was parsed and file is found
                    if(isset($data->event_image) && file_exists($data->event_image)) {
                        $html_content .= "
                        <div class='form-group' id='event_cover_image_{$data->item_id}'>
                            <img src='{$this->baseUrl}{$data->event_image}' width='100%'>
                        </div>
                        ".(!$disabled ? "
                        <div class='form-group text-right'>
                            <button title='Remove event cover image' onclick='return remove_Event_Cover_Image(\"{$data->item_id}\")' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></button>
                        </div>":"");
                    }
                    $html_content .= '</div>
                    <div class="col-lg-3">
                        <div class="form-group pt-4">
                            <input '.$disabled.' type="checkbox" '.(isset($data->is_holiday) && ($data->is_holiday == "on") ? "checked='checked'" : "").' name="holiday" class="checkbox" id="holiday">
                            <label for="holiday">Holiday</label>
                        </div>
                    </div>';
                // show the status modification selector for this event
                $state = isset($data->state) ? $data->state : null;
                $isActive = (bool) ($state == "Pending");

                $html_content .= '
                <div class="col-lg-4">
                    <div class="form-group pt-0">
                        <label for="">Event Status</label>
                        <select '.$disabled.' data-width="100%" class="form-control selectpicker" id="status" name="status">
                            <option '.($state == "Pending" ? "selected" : null).' value="Pending">Pending</option>
                            <option '.($state == "Ongoing" ? "selected" : null).' value="Ongoing">Ongoing</option>
                            <option '.($state == "Held" ? "selected" : null).' value="Held">Held</option>
                            '.($state ? '<option '.($state == "Cancelled" ? "selected" : null).' value="Cancelled">Cancelled</option>' : '').'
                        </select>
                    </div>
                </div>';

                $html_content .= '
                </div>
            </div>
            '.(!$disabled ? '
                <div class="modal-footer">
                    '.(!$state ? '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' : "").'
                    <button type="button-submit" class="btn btn-primary">Save</button>
                </div></form>' : ''
            ).'';

        return $html_content;
    }

    /**
     * Library Book Forms
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function library_book_form($data = null) {

        $html_content = '
        <form autocomplete="Off" enctype="multipart/form-data" class="ajax-data-form" action="'.$this->baseUrl.'api/library/'.(isset($data->title) ? "update_book" : "add_book").'" method="POST" id="ajax-data-form-content">    
            <div class="row">
                <div class="col-lg-5 col-md-5">
                    <div class="form-group">
                        <label for="book_image">Cover Image</label>
                        <input accept=".jpg,.jpeg,.png,.gif" type="file" name="book_image" id="book_image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-7 col-md-7"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">BOOK TITLE <span class="required">*</span></label>
                        <input type="text" placeholder="Book Title" value="'.($data->title ?? null).'" class="form-control" name="title">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">ISBN <span class="required">*</span></label>
                        <input type="text" style="text-transform:uppercase" placeholder="ISBN" value="'.($data->isbn ?? null).'" class="form-control" name="isbn">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">CODE</label>
                        <input type="text" style="text-transform:uppercase" placeholder="Book Code" value="'.($data->code ?? null).'" class="form-control" name="code">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">AUTHOR <span class="required">*</span></label>
                        <input type="text" placeholder="Book Author" value="'.($data->author ?? null).'" class="form-control" name="author">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">RACK NO.</label>
                        <input type="text" placeholder="Rack Number" value="'.($data->rack_no ?? null).'" class="form-control" name="rack_no">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">ROW NO.</label>
                        <input type="text" name="row_no" placeholder="Row Number" value="'.($data->row_no ?? null).'" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">QUANTITY <span class="required">*</span></label>
                        <input type="number" '.(isset($data->quantity) ? "disabled" : "").' max="200" placeholder="Quantity Available" value="'.($data->quantity ?? null).'" name="quantity" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">DEPARTMENT</label>
                        <select id="department_id" data-width="100%" class="form-control selectpicker" name="department_id">
                            <option value="">Please Select</option>';
                            foreach($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$data->clientId}'") as $each) {
                                $html_content .= "<option ".(isset($data->department_id) && ($each->id == $data->department_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $html_content .= '</select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">CLASS</label>
                        <select name="class_id" data-width="100%" id="class_id" class="form-control programme selectpicker">
                            <option value="">Please Select</option>';
                            foreach($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$data->clientId}'") as $each) {
                                $html_content .= "<option ".(isset($data->class_id) && ($each->id == $data->class_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $html_content .= '
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">BOOK CATEGORY</label>
                        <select name="category_id" data-width="100%" id="category" class="form-control category selectpicker">
                            <option value="">Please Select</option>';
                            foreach($this->pushQuery("id, name", "books_type", "status='1' AND client_id='{$data->clientId}'") as $each) {
                                $html_content .= "<option ".(isset($data->category_id) && ($each->id == $data->category_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $html_content .= '
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="">DESCRIPTION</label>
                        <textarea style="height:70px" name="description" placeholder="Book Description" id="description" cols="30" rows="10" class="form-control description">'.($data->description ?? null).'</textarea>
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <input type="hidden" value="'.($data->item_id ?? null).'" name="book_id" readonly>
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $html_content;
    }

    /**
     * Library Book Forms
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function library_category_form($data = null) {

        $html_content = '
        <form autocomplete="Off" class="ajax-data-form" action="'.$this->baseUrl.'api/library/'.(isset($data->name) ? "update_category" : "add_category").'" method="POST" id="ajax-data-form-content">    
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="">CATEGORY NAME <span class="required">*</span></label>
                        <input type="text" placeholder="Category Name" value="'.($data->name ?? null).'" class="form-control" name="name">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">DEPARTMENT</label>
                        <select id="department_id" data-width="100%" class="form-control selectpicker" name="department_id">
                            <option value="">Please Select</option>';
                            foreach($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$data->clientId}'") as $each) {
                                $html_content .= "<option ".(isset($data->department_id) && ($each->id == $data->department_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $html_content .= '</select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">DESCRIPTION</label>
                        <textarea style="height:70px" name="description" placeholder="Book Description" id="description" cols="30" rows="10" class="form-control description">'.($data->description ?? null).'</textarea>
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <input type="hidden" value="'.($data->item_id ?? null).'" name="category_id" readonly>
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $html_content;
    }

    /**
     * Class Room Forms
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function class_room_form($data = null) {

        $html_content = '
        <form autocomplete="Off" class="ajax-data-form" action="'.$this->baseUrl.'api/rooms/'.(isset($data->name) ? "update_classroom" : "add_classroom").'" method="POST" id="ajax-data-form-content">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Room Name <span class="required">*</span></label>
                        <input type="text" placeholder="Enter room name" value="'.($data->name ?? null).'" class="form-control" name="name">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Room Code</label>
                        <input type="text" maxlength="12" placeholder="Room Code" value="'.($data->code ?? null).'" class="form-control" name="code">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Capacity</label>
                        <input type="number" placeholder="Room Capacity" value="'.($data->capacity ?? null).'" class="form-control" name="capacity">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="class_id">Class <span class="required">*</span></label>
                        <select multiple data-width="100%" name="class_id[]" id="class_id[]" class="form-control selectpicker">
                            <option value="">Select Class</option>';
                            foreach($this->pushQuery("id, name, item_id", "classes", "status='1' AND client_id='{$data->clientId}'") as $each) {
                                $html_content .= "<option ".($data && in_array($each->item_id, $data->class_ids) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name}</option>";                            
                            }
                        $html_content .= '</select>
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <input type="hidden" value="'.($data->item_id ?? null).'" name="class_room_id" readonly>
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $html_content;
    }

    /**
     * Library Book Issue Form
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function library_book_issue_form($data = null) {
        
        // init
        $html_content = "";

        // if the request includes search_form
        if(isset($data->search_form)) {
            //get the books category list
            $category_list = $this->pushQuery("id, name", "books_type", "status='1' AND client_id='{$data->clientId}'");
            $books_list = $this->pushQuery("a.item_id, a.isbn, a.author, a.rack_no, a.book_image, a.row_no, a.description, a.class_id, 
                a.title, (SELECT quantity FROM books_stock WHERE books_id = a.item_id) AS books_stock", "books a", "a.status='1' AND a.client_id='{$data->clientId}'");

            // set the html_content to display
            $html_content .= '
                <div class="form-group">
                    <label>Book Category</label>
                    <select name="category_id" data-width="100%" id="category_id" class="form-control selectpicker">
                        <option value="">Please Select</option>';
                        foreach($category_list as $each) {
                            $html_content .= "<option ".(isset($data->category_id) && ($each->id == $data->category_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                        }
                    $html_content .= '
                    </select>
                </div>
                <div class="form-group">
                    <label>Book Title</label>
                    <select name="book_id" data-width="100%" id="book_id" class="form-control selectpicker">
                        <option value="">Please Select</option>';
                        foreach($books_list as $each) {
                            $html_content .= "<option data-in_session='false' data-row_no='{$each->row_no}' data-rack_no='{$each->rack_no}' data-item_id='{$each->item_id}' data-isbn='{$each->isbn}' data-books_stock='{$each->books_stock}' data-book_image='{$each->book_image}' data-book_author='{$each->author}' data-book_title='{$each->title}' value=\"{$each->item_id}\">{$each->title}</option>";                            
                        }
                    $html_content .= '
                    </select>
                </div>';
        }

        // if the request includes the issue_form
        if(isset($data->issue_form)) {
            // set the html_content to display
            $html_content .= '
                <div class="form-group">
                    <label>User Role</label>
                    <select data-width="100%" name="user_role" id="user_role" class="form-control selectpicker">
                        <option value="">Please Select</option>';
                        foreach($this->all_user_roles_list as $key => $value) {
                            $html_content .= "<option ".(isset($data->user_role) && ($key == $data->user_role) ? "selected" : null)." value=\"{$key}\">{$value}</option>";                            
                        }
                    $html_content .= '
                    </select>
                </div>
                <div class="form-group">
                    <label>Fullname <span class="required">*</span></label>
                    <select data-width="100%" name="user_id" id="user_id" class="form-control selectpicker">
                        <option value="">Please Select</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Return Date <span class="required">*</span></label>
                    <input type="text" data-mindate="'.date("Y-m-d").'" data-maxdate="'.date("Y-m-d", strtotime("+3 month")).'" name="return_date" id="return_date" value="'.($data->return_date ?? date("Y-m-d", strtotime("+1 week"))).'" class="form-control datepicker">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-6">
                            <label>Overdue Fine</label>
                            <input type="number" value="'.($data->overdue_rate ?? "").'" name="overdue_rate" id="overdue_rate" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Apply Overdue</label>
                            <select data-width="100%" name="overdue_apply" id="overdue_apply" class="form-control selectpicker">
                                <option value="entire">Entire Order</option>
                                <option value="single">Each Book</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <input type="hidden" value="'.($data->issue_id ?? null).'" name="issue_id" readonly>
                    <button onclick="return save_Issue_Request(\''.($data->issue_id ?? null).'\',\'issued\');" type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>';
        }

        // if the request includes the request_form
        if(isset($data->request_form)) {
            // set the html_content to display
            $html_content .= '
                <input type="hidden" readonly name="user_role" id="user_role" value="'.$data->user_role.'">
                <input type="hidden" readonly name="user_id" id="user_id" value="'.$data->user_id.'">
                <div class="form-group">
                    <label>Return Date <span class="required">*</span></label>
                    <input type="text" data-mindate="'.date("Y-m-d").'" data-maxdate="'.date("Y-m-d", strtotime("+3 month")).'" value="'.($data->return_date ?? date("Y-m-d", strtotime("+1 week"))).'" name="return_date" id="return_date" class="form-control datepicker">
                </div>
                <div class="text-right">
                    <input type="hidden" value="'.($data->issue_id ?? null).'" name="issue_id" readonly>
                    <button onclick="return save_Issue_Request(\''.($data->issue_id ?? null).'\',\'request\');" class="btn btn-success"><i class="fa fa-save"></i> Place Request</button>
                </div>';
        }

        return $html_content;
    }

    /**
     * Profile form
     * 
     * @param String $clientId
     * @param String $baseUrl
     * 
     * @return String
     */
    public function profile_form($baseUrl, $userData = null, $form_id = "ajax-data-form-content") {

        $isData = !empty($userData) && isset($userData->client_id) ? true : false;

        $response = '
        <form autocomplete="Off" class="ajax-data-form" id="'.$form_id.'" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>BIO INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" accept=".jpg,.jpeg,.png,.gif" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="unique_id">Unique ID (optional)</label>
                        <input type="text" disabled value="'.($userData->unique_id ?? null).'" name="unique_id" id="unique_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select data-width="100%" name="gender" id="gender" class="form-control selectpicker">
                            <option value="">Select Gender</option>';
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
                        <input type="text" value="'.($userData->date_of_birth ?? null).'" name="date_of_birth" id="date_of_birth" class="form-control datepicker">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" '.(isset($userData->disabled) ? "readonly" : null).' value="'.($userData->email ?? null).'" name="email" id="email" class="form-control">
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
                            <option value="">Select Country</option>';
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
                            <option value="">Select Blood Group</option>';
                            foreach($this->pushQuery("id, name", "blood_groups") as $each) {
                                $response .= "<option ".($isData && ($each->name == $userData->blood_group) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-8 col-md-6">
                    <div class="form-group">
                        <label for="position">Position / Role <span class="required">*</span></label>
                        <input type="text" value="'.($userData->position ?? null).'" name="position" id="position" class="form-control">
                        <input type="hidden" hidden value="'.($userData->user_id ?? null).'" name="user_id" id="user_id" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" value="'.($userData->address ?? null).'" name="address" id="address" class="form-control">
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
                    <button type="button-submit" data-form_id="'.$form_id.'" href="'.$this->baseUrl.'profile" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $response;

    }

    /**
     * Settings Form
     * 
     * @param String    $clientId
     * 
     * @return Array
     */
    public function settings_form($clientId, $form_id = "ajax-data-form-content") {
        
        // global
        global $defaultUser, $defaultClientData, $academicSession;

        // run the school academic terms query
        $this->academic_terms();
        
        // get the client data
        $client_data = !empty($clientId) ? $defaultClientData : (object)[];

        // run this query
        $prefs = !empty($client_data) ? $client_data->client_preferences : (object)[];
        
        $forms = [];

        $labels = [
            ["key" => "student", "label" => "Student"],
            ["key" => "parent", "label" => "Guardian"],
            ["key" => "teacher", "label" => "Teacher"],
            ["key" => "staff", "label" => "Staff"],
            ["key" => "course", "label" => "Course"],
            ["key" => "book", "label" => "Books"],
            ["key" => "class", "label" => "Class"],
            ["key" => "department", "label" => "Departments"],
            ["key" => "section", "label" => "Section"],
            ["key" => "receipt", "label" => "Receipts"],
        ];

        $unit_labels = [
            ["key" => "unit", "label" => "Unit"],
            ["key" => "lesson", "label" => "Lesson"]
        ];

        $logoUploaded = (bool) ($client_data && $client_data->client_logo);
        $last_date = date("Y-m-d", strtotime("+3 year"));
        $min_date = $this->calendar_minimum_year;

        // attachments
        $filesObject = load_class("files", "controllers");

        $docs = $filesObject->resource_attachments_list("settings_calendar", $client_data->client_id);
        $calendar_docs = !empty($docs) ? json_encode($docs) : [];
        $calendar_docs = !empty($calendar_docs) ? json_decode($calendar_docs) : [];

        if(!empty($calendar_docs)) {
            $calendar_docs->files = $calendar_docs->files_list;
        }

        // set a new parameter for the items
        $files_param = (object) [
            "userData" => $client_data,
            "label" => "list",
            "is_deletable" => true,
            "module" => "settings_calendar",
            "item_id" => $client_data->client_id,
            "accept" => implode(",", [".pdf"]),
            "attachments_list" => $calendar_docs,
            "preview_file" => true
        ];

        /** Set parameters for the data to attach */
        $form_params = (object) [
            "accept" => ".pdf",
            "module" => "settings_calendar",
            "userData" => $client_data,
            "item_id" => $client_data->client_id,
            "preview_file" => true
        ];

        // create a new object
        $attachments = load_class("files", "controllers")->attachments($files_param);

        // get the attachments list
        $preloaded_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

        // GENERAL FORM
        $general = '
        <form autocomplete="Off" class="ajax-data-form" action="'.$this->baseUrl.'api/account/update" method="POST" id="'.$form_id.'">
        <div class="row">
            <div class="col-lg-12"><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">GENERAL SETTINGS</h5></div>
            '.($logoUploaded ? 
            '<div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <img width="150px" src="'.$this->baseUrl.''.$client_data->client_logo.'">
                </div>
            </div>' : '').'
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label for="logo">Logo</label>
                    <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.gif" class="form-control">
                </div>
            </div>
            <div class="col-lg-'.($logoUploaded ? 7 : 9).' col-md-12">
                <div class="form-group">
                    <label for="name">School Name</label>
                    <input type="text" value="'.($client_data->client_name ?? null).'" name="general[name]" class="form-control">
                </div>
            </div>
            <div class="col-lg-12 col-md-12">
                <div class="form-group">
                    <label for="name">Slogan</label>
                    <input type="text" value="'.($client_data->client_slogan ?? null).'" name="general[slogan]" class="form-control">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="website">School Website</label>
                    <input type="text" value="'.($client_data->client_website ?? null).'" name="general[website]" class="form-control">
                </div>
            </div>
            <div class="col-lg-8 col-md-6">
                <div class="form-group">
                    <label for="address">School Address</label>
                    <input type="text" name="general[address]" value="'.($client_data->client_address ?? null).'" class="form-control">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="email">School Email Address</label>
                    <input type="email" value="'.($client_data->client_email ?? null).'" name="general[email]" class="form-control">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="text" name="general[contact]" value="'.($client_data->client_contact ?? null).'" class="form-control">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="location">School Location</label>
                    <input type="text" name="general[location]" value="'.($client_data->client_location ?? null).'" class="form-control">
                </div>
            </div>
            <div class="col-lg-12"><h5 class="border-bottom border-primary text-primary pb-2 pt-3">LABELS</h5></div>';
            foreach($labels as $label) {
                $ilabel = "{$label["key"]}_label";
            $general .= '
                <div class="col-lg-2 col-md-3">
                    <div class="form-group">
                        <label for="'.$label["key"].'_label">'.$label["label"].' Label</label>
                        <input type="text" value="'.($prefs->labels->{$ilabel} ?? null).'" maxlength="4" name="general[labels]['.$label["key"].'_label]" id="'.$label["key"].'_label" class="form-control text-uppercase">
                    </div>
                </div>';
            }
        $general .= '
            <div class="col-lg-12"><h5 class="border-bottom border-primary text-primary pb-2 mb-2 pt-3">SUBJECT LABELS</h5></div>';
            foreach($unit_labels as $label) {
                $ilabel = "{$label["key"]}_label";
            $general .= '
                <div class="col-lg-2 col-md-3">
                    <div class="form-group">
                        <label for="'.$label["key"].'_label">'.$label["label"].' Label</label>
                        <input type="text" value="'.($prefs->labels->{$ilabel} ?? null).'" maxlength="10" name="general[labels]['.$label["key"].'_label]" id="'.$label["key"].'_label" class="form-control">
                    </div>
                </div>';
            }
            $general .= '
            <div class="col-lg-12"><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">FEES & BILLING NOTES</h5></div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label for="name">Bank Account Information</label>
                    <textarea placeholder="Enter the bank account information" name="general[billing][account_info]" class="form-control">'.(!empty($prefs?->billing?->account_info) ? strip_tags($prefs?->billing?->account_info) : null).'</textarea>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label for="name">Fees Mobile Money Numbers</label>
                    <input type="text" placeholder="Enter the mobile money numbers separated by commas" value="'.($prefs?->billing?->mobile_money ?? null).'" name="general[billing][mobile_money]" class="form-control">
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label for="name">Billing Additional Information</label>
                    <textarea placeholder="Enter the billing additional information" name="general[billing][additional_info]" class="form-control">'.(!empty($prefs?->billing?->additional_info) ? strip_tags($prefs?->billing?->additional_info) : null).'</textarea>
                </div>
            </div>
            <div class="col-lg-12"><h5 class="border-bottom border-primary text-primary pb-2 mb-2 pt-3">FINANCE</h5></div>
            <div class="col-lg-4 settings-form">
                <div class="form-group">
                    <label for="opening_days">School Opening Days</label>';
                    // loop through the count 7
                    $openingDays = $prefs->opening_days ?? [];

                    for($i = 0; $i < 7; $i++) {
                        // set the day
                        $today = date("l", strtotime("Monday +$i day"));
                        $general .= '
                            <div style="padding-left: 3.5rem;" class="custom-control col-lg-12 custom-switch switch-primary">
                                <input type="checkbox" name="general[opening_days][]" value="'.ucfirst($today).'" class="custom-control-input" id="'.$today.'" '.(in_array($today, $openingDays) ? "checked=\"checked\"" : null).'>
                                <label class="custom-control-label" for="'.$today.'">'.$today.'</label>
                            </div>';
                    }
                    $general .= '
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="currency">Currency</label>
                    <select data-width="100%" name="general[labels][currency]" id="labels" class="form-control selectpicker">
                        <option value="">Select Currency</option>';
                            foreach($this->pushQuery("id, currency", "currency","1") as $each) {
                                $general .= "<option ".((isset($prefs->labels->currency) && $each->currency === $prefs->labels->currency) ? "selected" : null)." value=\"{$each->currency}\">{$each->currency}</option>";                            
                            }
                        $general .= '</select>
                    </select>
                </div>
                <div class="form-group">
                    <div class="custom-control col-lg-12 custom-switch switch-primary">
                        <input type="checkbox" id="print_receipt" name="general[labels][print_receipt]" value="1" class="custom-control-input" '.((isset($prefs->labels->print_receipt) && $prefs->labels->print_receipt === "1") ? "checked=\"checked\"" : null).'>
                        <label class="custom-control-label" for="print_receipt">Print Receipt After Payment</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="custom-control col-lg-12 custom-switch switch-primary">
                        <input type="checkbox" id="send_receipt" name="general[labels][send_receipt]" value="1" class="custom-control-input" '.((isset($prefs->labels->send_receipt) && $prefs->labels->send_receipt === "1") ? "checked=\"checked\"" : null).'>
                        <label class="custom-control-label" for="send_receipt">Send Receipt After Payment via SMS</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-right">
                <button type="button-submit" data-form_id="'.$form_id.'" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
            </div>
        </form></div>';
        $forms["general"] = $general;

        // ACADEMIC CALENDAR FORM
        $form_id = "_ajax-data-form-content";
        $calendar = '
        <form autocomplete="Off" class="_ajax-data-form" action="'.$this->baseUrl.'api/account/calendar" method="POST" id="'.$form_id.'">
        <div class="row">
            <div class="col-lg-12"><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">ACADEMIC SESSIONS</h5></div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label for="term_ends">Academic Session</label>
                    <select data-width="100%" name="general[sessions][session]" class="form-control selectpicker">
                        <option value="">Select Academic Session</option>';
                            foreach($this->academic_sessions as $_sess => $_session) {
                                $calendar .= "<option ".(($client_data && $_sess === $academicSession) ? "selected" : null)." value=\"{$_sess}\">{$_session}</option>";                            
                            }
                        $calendar .= '
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_ends">Session Name</label>
                    <input type="text" value="'.($prefs->academics->current_term_name ?? 'Term').'" name="general[academics][current_term_name]" id="current_term_name" class="form-control">
                </div>
            </div>
            <div class="col-lg-12"><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">CURRENT ACADEMIC CALENDAR</h5></div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="academic_year">Academic Year</label>
                    <select data-width="100%" name="general[academics][academic_year]" class="form-control selectpicker">
                        <option value="">Select Academic Year</option>';
                            foreach($this->academic_calendar_years as $year_group) {
                                $calendar .= "<option ".(($client_data && $year_group === $prefs->academics->academic_year) ? "selected" : null)." value=\"{$year_group}\">{$year_group}</option>";                            
                            }
                        $calendar .= '
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_starts">Academic Year Start</label>
                    <input type="text" value="'.($prefs->academics->year_starts ?? null).'" name="general[academics][year_starts]" id="year_starts" data-mindate="'.$min_date.'" data-maxdate="'.$last_date.'" class="form-control datepicker">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_ends">Academic Year Ends</label>
                    <input type="text" value="'.($prefs->academics->year_ends ?? null).'" name="general[academics][year_ends]" id="year_ends" data-mindate="'.$min_date.'" data-maxdate="'.$last_date.'" class="form-control datepicker">
                </div>
            </div>
            <div class="col-lg-12"></div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="academic_term">Academic Session</label>
                    <select data-width="100%" name="general[academics][academic_term]" class="form-control selectpicker">
                        <option value="">Select Academic Session</option>';
                            foreach($this->school_academic_terms as $each) {
                                $calendar .= "<option ".(($client_data && $each->name === $prefs->academics->academic_term) ? "selected" : null)." value=\"{$each->name}\">{$each->description}</option>";                            
                            }
                        $calendar .= '</select>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_starts">Academic Session Start</label>
                    <input type="text" value="'.($prefs->academics->term_starts ?? null).'" name="general[academics][term_starts]" id="term_starts" data-mindate="'.$min_date.'" data-maxdate="'.$last_date.'" class="form-control datepicker">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_ends">Academic Session Ends</label>
                    <input type="text" value="'.($prefs->academics->term_ends ?? null).'" name="general[academics][term_ends]" id="term_ends" data-mindate="'.$min_date.'" data-maxdate="'.$last_date.'" class="form-control datepicker">
                </div>
            </div>
            <div class="col-lg-12">
                <div class="form-group text-center mb-1">
                    <div class="row">'.$this->form_attachment_placeholder($form_params).'</div>
                </div>
            </div>  
            <div class="col-md-12 text-center mb-2">'.$preloaded_attachments.'</div>

            <div class="col-lg-12"><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">NEXT ACADEMIC CALENDAR</h5></div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_ends">Session Name</label>
                    <input type="text" value="'.($prefs->academics->next_term_name ?? 'Term').'" name="general[academics][next_term_name]" id="next_term_name" class="form-control">
                </div>
            </div>
            <div class="col-lg-12 border-bottom border-primary text-primary mb-2"></div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="next_academic_year">Next Academic Year</label>
                    <select data-width="100%" name="general[academics][next_academic_year]" class="form-control selectpicker">
                        <option value="">Select Academic Year</option>';
                            foreach($this->academic_calendar_years as $year_group) {
                                $calendar .= "<option ".(($client_data && $year_group === $prefs->academics->next_academic_year) ? "selected" : null)." value=\"{$year_group}\">{$year_group}</option>";                            
                            }
                        $calendar .= '</select>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_starts">Academic Year Start</label>
                    <input type="text" value="'.($prefs->academics->next_year_starts ?? null).'" name="general[academics][next_year_starts]" id="next_year_starts" data-mindate="'.$min_date.'" data-maxdate="'.$last_date.'" class="form-control datepicker">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_ends">Academic Year Ends</label>
                    <input type="text" value="'.($prefs->academics->next_year_ends ?? null).'" name="general[academics][next_year_ends]" id="next_year_ends" data-mindate="'.$min_date.'" data-maxdate="'.$last_date.'" class="form-control datepicker">
                </div>
            </div>
            <div class="col-lg-12"></div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="next_academic_term">Next Academic Session</label>
                    <select data-width="100%" name="general[academics][next_academic_term]" class="form-control selectpicker">
                        <option value="">Select Academic Session</option>';
                            foreach($this->school_academic_terms as $each) {
                                $calendar .= "<option ".(($client_data && $each->name === $prefs->academics->next_academic_term) ? "selected" : null)." value=\"{$each->name}\">{$each->description}</option>";                            
                            }
                        $calendar .= '</select>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="next_term_starts">Next Academic Session Start</label>
                    <input type="text" value="'.($prefs->academics->next_term_starts ?? null).'" name="general[academics][next_term_starts]" data-maxdate="'.$last_date.'" id="next_term_starts" class="form-control datepicker">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="next_term_ends">Next Academic Session Ends</label>
                    <input type="text" value="'.($prefs->academics->next_term_ends ?? null).'" name="general[academics][next_term_ends]" data-maxdate="'.$last_date.'" id="next_term_ends" class="form-control datepicker">
                </div>
            </div>
            <div class="col-md-12 text-right">
                <button type="button-submit" data-form_id="'.$form_id.'" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
            </div>
        </form></div>';
        $forms["calendar"] = $calendar;


        // create a new account object
        $accountObj = load_class("account", "controllers", (object) ["client_data" => $defaultUser->client]);
        $array_columns = $accountObj->accepted_column;

        // forms
        $select = [];
        foreach($array_columns as $key => $value) {
            $select[$key] = $value;
        }

        // loop through the select form
        foreach($select as $key => $import) {
            $form = '
            <div data-csv_import_column="'.$key.'">
                <form autocomplete="Off" method="post" action="'.$this->baseUrl.'api/account/import" class="csvDataImportForm" enctype="multipart/form-data">
                    <div class="row">
                        <div id="dropify-space" class="col-md-8  mt-5 text-center m-auto border pt-4 border-white">
                            <div class="form-content-loader" style="display: none;">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <h2>Upload a CSV to import <strong>'.ucwords($key).' data</strong></h2>
                            <button type="button" class="btn btn-outline-primary" data-download_button="'.$key.'" onclick="return download_sample_csv(\''.$key.'\')"><i class="fa fa-download"></i> Download Sample CSV File</button>
                            <hr>
                            <div class="form-controls col-md-4 m-auto">
                                <div class="form-group text-center">
                                    <input style="height: 50px; line-height: 25px" data-file_unique_id="'.$key.'" accept=".csv" type="file" name="'.$key.'_csv_file" id="'.$key.'_csv_file" class="form-control btn bg-purple text-white no-border text-white cursor">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row justify-content-center p-4 text-center">
                    <div class="col-lg-8 p-3 upload-text hidden">
                        <h2>Upload <strong>'.ucwords($key).'</strong></h2>
                        <div class="csv-rows-counter text-success font-16"></div>
                    </div>
                    <div class="col-lg-8 file-checker"></div>
                </div>                
                <div class="mt-4 csv-rows-content border slim-scroll" style="overflow-x: auto;display: flex; flex: 1; padding-top: 20px; flex-direction: row; max-height: 450px; background: none;">
                    <div class="col-md-4" style="display: none;" data-row="1">
                        <div class="form-row">
                            <select class="form-control">
                                <option value="">Please Select</option>';
                                foreach($import as $value) {
                                    $form .= "<option value='{$value}'>{$value}</option>";
                                }
                            $form .= '</select>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 upload-buttons">
                    <div class="col-lg-12 text-center">
                        <button type="cancel" onclick="return cancel_csv_upload(\''.$key.'\');" class="btn hidden cancel-button btn-outline-danger">
                            Cancel Upload
                        </button>
                        <button onclick="return import_csv_data(\''.$key.'\');" style="display: none;" type="submit" class="btn upload-button btn-outline-success">
                            <i class="fa fa-upload"></i> Continue Data Import
                        </button>
                    </div>
                </div>
            </div>';
            $forms[$key] = $form;
        }

        // set the grading system
        $grading_list = "";
        $grading_system = $client_data->grading_system ?? [];

        // generate the first item for the grading
        if(empty($grading_system)) {
            $grading_list = "
            <div class='row mb-2 grade_item' data-grading_id='1'>
                <div class='col-md-2'>
                    <label>Grade</label>
                    <input type='text' maxlength='5' name='grade_1' data-grading_id='1' class='form-control' width='100px'>
                </div>
                <div class='col-md-3'>
                    <label>Mark Begin(%)</label>
                    <input type='number' min='0' max='100' name='start_1' data-grading_id='1' class='form-control' width='100px'>
                </div>
                <div class='col-md-3'>
                    <label>Marks End Point(%)</label>
                    <input type='number' min='0' max='100' name='end_1' data-grading_id='1' class='form-control' width='100px'>
                </div>
                <div class='col-md-3'>
                    <label>Interpretation</label>
                    <input type='text' min='0' max='100' name='interpretation_1' data-grading_id='1' class='form-control'>
                </div>
                <div class='col-md-1'>
                    <label>&nbsp;</label><br>
                    <button type='button' onclick='return remove_grading_mark(1)' class='btn btn-outline-danger'><i class='fa fa-trash'></i></button>
                </div>
            </div>";
        } else {
            $grading = !is_object($client_data->grading_system) ? json_decode($client_data->grading_system) : $client_data->grading_system;
            foreach($grading as $key => $grade) {
                $grading_list .= "
                    <div class='row mb-2 grade_item' data-grading_id='{$key}'>
                        <div class='col-md-2'>
                            <label>Grade</label>
                            <input type='text' maxlength='5' value='".($grade->grade ?? "")."' name='grade_{$key}' data-grading_id='{$key}' class='form-control' width='100px'>
                        </div>
                        <div class='col-md-3'>
                            <label>Mark Begin(%)</label>
                            <input type='number' min='0' value='".($grade->start ?? "")."' max='100' name='start_{$key}' data-grading_id='{$key}' class='form-control' width='100px'>
                        </div>
                        <div class='col-md-3'>
                            <label>Marks End Point(%)</label>
                            <input type='number' min='0' value='".($grade->end ?? "")."' max='100' name='end_{$key}' data-grading_id='{$key}' class='form-control' width='100px'>
                        </div>
                        <div class='col-md-3'>
                            <label>Interpretation</label>
                            <input type='text' min='0' value='".($grade->interpretation ?? "")."' max='100' name='interpretation_{$key}' data-grading_id='{$key}' class='form-control'>
                        </div>
                        <div class='col-md-1'>
                            <label>&nbsp;</label><br>
                            <button type='button' onclick='return remove_grading_mark({$key})' class='btn btn-outline-danger'><i class='fa fa-trash'></i></button>
                        </div>
                    </div>";
            }
        }

        $columns_listing = "";
        
        if(!empty($client_data->grading_structure)) {
            // get the grading structure
            $columns = !is_object($client_data->grading_structure) ? json_decode($client_data->grading_structure) : $client_data->grading_structure;
            if(!isset($columns->columns)) {
                $columns->columns = json_decode(json_encode([
                    'School Based Assessment' => [
                        "percentage" => 0
                    ],
                    'Examination' => [
                        "percentage" => 0
                    ]
                ]));
            }
            if(isset($columns->columns)) {
                $count = 0;
                foreach($columns->columns as $key => $column) {
                    $count++;
                    $columns_listing .= "
                    <div class='row mb-4 column_item' data-column_id='{$count}'>
                        <div class='col-lg-9 col-md-9'>
                            <label>Name</label>
                            <input type='text' maxlength='100' ".($key == 'Examination' ? "readonly title='This label is automatically generated and not editable.'" : null)." value='{$key}' name='column_name_{$count}' data-column_id='{$count}' class='form-control'>
                        </div>
                        <div class='col-lg-3 col-md-3'>
                            <label>Percentage(%)</label>
                            <input type='number' min='0' value='{$column->percentage}' max='{$column->percentage}' name='column_percentage_{$count}' data-column_id='{$count}' class='text-center form-control'>
                        </div>
                    </div>";
                }
            }
        }

        // examination forms
        $examination = '
        <div class="row">
            <div class="col-lg-12 col-md-12 mb-3">
                <div class="form-group">
                    <div class="d-flex pb-3 justify-content-between">
                        <div><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">GRADING SYSTEM</h5></div>
                        <div><button type="button" title="Add new Grading" onclick="return add_grading_mark()" class="btn btn-outline-primary"><i class="fa fa-plus"></i></button></div>
                    </div>
                    <div id="grading_system_list">'.$grading_list.'</div>
                </div>
            </div>
            <div class="col-lg-12 mb-3 col-md-12">
                <div class="form-group text-center">
                    <button type="button" onclick="return save_grading_mark()" id="save_grading_mark" class="btn btn-outline-success"><i class="fa fa-save"></i> Save Grades</button>
                </div>
            </div>
        </div>';

        $forms["examination"] = $examination;

        
        // set the max with
        $max_width = "col-lg-10";

        // terminal reports columns list
        $default_columns_list[0] = "
            <div class='row mb-2'>
                <div class='col-lg-12'>
                    <input type='text' readonly name='report_columns[course_title]' value='Course / Subject Name' class='form-control'>
                </div>
            </div>";
        $default_columns_list[1] = "
            <div class='row mb-2'>
                <div class='col-lg-12'>
                    <input type='text' readonly name='report_columns[average_score]' value='Average Score' class='form-control'>
                </div>
            </div>";
        $default_columns_list[2] = "
        <div class='row mb-2 mt-1'>
            <div class='col-lg-12'>
                <input type='text' readonly name='report_columns[teacher_comments]' value='Teacher Remarks' class='form-control'>
            </div>
        </div>";

        // examination forms
        $results_structure = '
        <div class="row">
            <div class="'.$max_width.'"><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">GRADEBOOK ASSESSMENT (SBA)</h5></div>
            <div class="'.$max_width.' mb-3 col-md-12" id="term_sba_columns_list">
                <div class="font-16 alert alert-warning text-center">
                    Select the checkbox of the SBA to automatically populate and include in the final report card.
                </div>';
            // init values
            $qu = 0;
            // loop through the assessment test group
            foreach($this->assessment_group as $sba) {
                // increment
                $qu++;
                $sba_mark = $client_data->grading_sba[$sba]["counter"] ?? 0;
                $sba_percent = $client_data->grading_sba[$sba]["percentage"] ?? 0;
                $sba_checkbox = !empty($client_data->grading_sba[$sba]["sba_checkbox"]);
                if(isset($client_data->grading_sba[$sba]["sba_checkbox"])) {
                    $sba_checkbox = $client_data->grading_sba[$sba]["sba_checkbox"] == 'true' ? true : false;
                }
                $name = $sba == "Test" ? "Test or Quiz" : $sba;

                // append to the structure
                $results_structure .= "
                    <div class='row mb-4 sba_item' data-column_id='{$qu}' data-column_name='{$sba}'>
                        <div class='col-lg-4'>
                            <label class='text-white'>...</label>
                            <div><strong>".strtoupper($name)."</strong></div>
                        </div>
                        <div class='col-lg-4'>
                            <label>Least {$sba} Assigned to Students</label>
                            <input type='number' min='0' value='{$sba_mark}' name='sba_least_{$qu}' data-column_id='{$qu}' class='form-control text-center'>
                        </div>
                        <div class='col-lg-2'>
                            <label>Percentage(%)</label>
                            <input type='number' title='The overall percentage that it weighs' min='0' value='{$sba_percent}' name='sba_percentage_{$qu}' data-column_id='{$qu}' class='form-control text-center'>
                        </div>
                        <div class='col-lg-2'>
                            <label>&nbsp;</label><br>
                             <input type=\"checkbox\" ".($sba_checkbox ? "checked" : "")." class=\"form-control checkbox-box\" value=\"{$sba_checkbox}\" name=\"sba_checkbox_{$qu}\" id=\"sba_checkbox_{$qu}\">
                        </div>
                    </div>";
            }
        $results_structure .= "
                <div class='text-center text-danger'>The selected options should sum up to 100%.</div>
            </div>
        </div>";

        // examination forms
        $results_structure .= '
        <div class="row">
            <div class="'.$max_width.'">
                <div class="d-flex pb-3 justify-content-between">
                    <div><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">TERMINAL REPORT STRUCTURE</h5></div>
                    <!--<div><button type="button" title="Add new Column" onclick="return add_report_column()" class="btn btn-outline-primary"><i class="fa fa-plus"></i></button></div>-->
                </div>
            </div>
            <div class="'.$max_width.' mb-3 col-md-12">
                <div class="form-group">
                    '.$default_columns_list[0].'
                    <div class="font-italic text-success">Add to list</div>
                    <div id="term_report_columns_list">'.$columns_listing.'</div>
                    '.$default_columns_list[1].'
                </div>

                <div class="row">
                    <div class="col-lg-4 col-md-12">
                        <div class="form-group">
                            <select class="form-control selectpicker" name="group_sba" data-width="100%">
                                <option '.(!empty($client_data->grading_structure) && $client_data->grading_structure->group_sba == "true" ? "selected" : "").' value="true">Group SBA on Report Card</option>
                                <option '.(!empty($client_data->grading_structure) && $client_data->grading_structure->group_sba == "false" ? "selected" : "").' value="false">Do not group SBA on Report</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="form-group">
                            <select class="form-control selectpicker" name="show_position" data-width="100%">
                                <option '.(isset($client_data->show_position) && $client_data->show_position == "true" ? "selected" : "").' value="true">Show position in class</option>
                                <option '.(isset($client_data->show_position) && $client_data->show_position == "false" ? "selected" : "").' value="false">Do not show position</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="form-group">
                            <select class="form-control selectpicker" name="show_teacher_name" data-width="100%">
                                <option '.(isset($client_data->show_teacher_name) && $client_data->show_teacher_name == "true" ? "selected" : "").' value="true">Show teacher\'s name</option>
                                <option '.(isset($client_data->show_teacher_name) && $client_data->show_teacher_name == "false" ? "selected" : "").' value="false">Do not show teacher\'s name</option>
                            </select>
                        </div>
                    </div>
                </div>
                '.$default_columns_list[2].'
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mt-3">
                            <select class="form-control selectpicker" name="allow_submission" data-width="100%">
                                <option '.(isset($client_data->allow_submission) && $client_data->allow_submission == "true" ? "selected" : "").' value="true">Allow teachers to submit report</option>
                                <option '.(isset($client_data->allow_submission) && $client_data->allow_submission == "false" ? "selected" : "").' value="false">Disallow the submission of reports</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <div class="'.$max_width.' mb-3 col-md-12">
                <div class="form-group text-right">
                    <button type="button" onclick="return save_grading_mark()" id="save_grading_mark" class="btn btn-outline-success"><i class="fa fa-save"></i> Save Grades</button>
                </div>
            </div>
        </div>';

        $forms["results_structure"] = $results_structure;

        // get the remarks list
        $remarks_listing = "";

        // get the user record
        $remarks_list = $this->pushQuery("*", "grading_remarks_list", "client_id='{$clientId}' LIMIT 1");
        $remarks_category_list = $this->pushQuery("*", "grading_remarks_category", "1 LIMIT 10");

        // if the grading information is not empty
        if(!empty($remarks_list)) {
            // get the information
            $remarks_list = $remarks_list[0]->remarks;
            $remarks_list = !empty($remarks_list) ? json_decode($remarks_list) : [];

            $count = 0;
            // loop through the remarks list
            foreach($remarks_list as $key => $remark) {
                $count++;
                $remarks_listing .= "
                <div class='row mb-3 remark_item' data-remark_id='{$key}'>
                    <div class='col-lg-4 mb-1 col-md-5'>
                        ".($key == 1 ? '<label>Category</label>' : null)."
                        <select data-width='100%' name='remarks[{$key}][category_id]' class='form-control selectpicker'>
                            <option value=''>Select Category</option>";
                            foreach($remarks_category_list as $category) {
                                $remarks_listing .= "<option ".($remark->category_id == $category->id ? "selected" : null)." value='{$category->id}'>{$category->name}</option>";
                            }
                $remarks_listing .= "</select>
                    </div>
                    <div class='col-lg-7 mb-1 col-md-6'>
                        ".($key == 1 ? '<label>Remarks</label>' : null)."
                        <input type='text' value='{$remark->remarks}' maxlength='500' name='remarks[{$key}][remarks]' class='form-control'>
                    </div>
                    <div class='col-lg-1 mb-1'>
                        ".($key == 1 ? '<label class="text-white">.</label>' : null)."
                        <button type='button' onclick='return remove_remark_row({$key})' data-remark_id='{$key}' class='btn btn-block btn-outline-danger'><i class='fa fa-trash'></i></button>
                    </div>
                </div>";
            }
        }

        // remarks structure
        $results_remarks = '
        <div class="row">
            <div class="col-lg-11">
                <div class="d-flex pb-3 justify-content-between">
                    <div><h5 class="border-bottom border-primary text-primary pb-2 mb-3 pt-3">EXAMS RESULTS STRUCTURE</h5></div>
                    <div class="mt-2"><button type="button" title="Add new Column" onclick="return add_result_comment()" class="btn btn-outline-primary"><i class="fa fa-plus"></i></button></div>
                </div>
            </div>
            <div class="col-lg-11 mb-3 col-md-12">
                <div class="form-group">
                    <div class="font-italic text-success"></div>
                    <div id="results_comments_list">'.$remarks_listing.'</div>
                </div>
            </div>
            <div class="col-lg-11 pt-3 col-md-12 border-top">
                <div class="form-group text-right">
                    <button type="button" onclick="return save_results_remarks()" id="save_results_remarks" class="btn btn-outline-success"><i class="fa fa-save"></i> Save Remarks</button>
                </div>
            </div>
        </div>';

        $forms["_remarks"] = [
            "remarks_category_list" => $remarks_category_list,
            "results_remarks" => $results_remarks
        ];

        return $forms;
    }

    /**
     * Terminal Reports Form
     * 
     * @param String
     * 
     * @return Array
     */
    public function terminal_reports($clientId) {
        $the_form = [];

        // get the client data
        global $defaultUser, $defaultClientData;
        $client_data = $defaultClientData;

        // get the list of all classes
        $classes_param = (object) [
            "filter" => $defaultUser->user_type,
            "userId" => $defaultUser->user_id,
            "columns" => "a.id, a.item_id, a.name, a.payment_module",
            "user_type" => $defaultUser->user_type,
            "clientId" => $clientId,
            "limit" => 100
        ];
        $classes_list = load_class("classes", "controllers")->list($classes_param)["data"];
        
        // if the submission of report is false
        if(!empty($client_data->allow_submission) && $client_data->allow_submission === "false") {
            $the_form["general"] = "<div class='text-center alert alert-warning'>Sorry! You are not allowed submit a report at this period of the term.</div>";
            return $the_form;
        }

        // if the grading structure columsn is empty then end the query
        if(!isset($client_data->grading_structure->columns) || empty($client_data->grading_structure->columns)) {
            $the_form["general"] = "<div class='text-center alert alert-warning'>Sorry! The <strong>Grading Structure</strong> has not yet been set. Please Visit <strong>Settings > Examination Grading > Terminal Report Structure</strong> section to set this up.</div>";
            return $the_form;
        }
        
        // append the form and display
        $the_form["general"] = "
            <div class='row' id='terminal_reports'>
                <div class='col-lg-3 mb-2'>
                    <select data-width='100%' class='form-control selectpicker' name='class_id' id='class_id'>
                        <option value=''>Select the Class</option>";
                        foreach($classes_list as $class) {
                            $the_form["general"] .= "<option data-payment_module='{$class->payment_module}' value='{$class->item_id}'>".strtoupper($class->name)."</option>";
                        }
        $the_form["general"] .= "</select>
                    </div>
                    <div class='col-lg-4 mb-2'>
                        <select data-width='100%' class='form-control selectpicker' name='course_id' id='course_id'>
                            <option value=''>Select the Subject</option>
                        </select>
                    </div>
                    <div class='col-lg-3 mb-2'>
                        <select data-width='100%' class='form-control selectpicker' name='upload_type' id='upload_type'>
                            <option value='download'>Download CSV File</option>
                            <!--<option value='online'>Input the Data Online</option>-->
                        </select>
                    </div>
                    <div class='col-lg-2' id='download_button'>
                        <button onclick='return download_report_csv()' disabled type='download_csv' class='btn btn-block btn-outline-primary'>Download Sample</button>
                    </div>
                    <div class='col-lg-2 hidden' id='upload_button'>
                        <button onclick='return download_student_list()' disabled type='upload_button' class='btn btn-block btn-outline-primary'>Load Students</button>
                    </div>
                    <div class='col-lg-12 mt-4'></div>
                    <div class='col-lg-12 mt-1 text-center' id='notification'></div>
                    <div class='col-md-4 hidden' id='upload_file'>
                        <div class='form-group'>
                            <label>Select file to upload</label>
                            <input type='file' name='upload_report_file' accept='.csv' class='form-control'>
                        </div>
                    </div>
                    <div class='col-md-3 hidden' id='upload_file'>
                        <div class='form-group'>
                            <label>&nbsp;</label>
                            <button onclick='return upload_csv_file();' class='btn btn-block btn-primary'><i class='fa fa-upload'></i> Click to Upload</button>
                        </div>
                    </div>
                    <div class='col-lg-12 mt-4' id='summary_report_sheet_content'></div>
                </div>";


        return $the_form;
    }

    /**
     * Terminal Reports Form
     * 
     * @param String
     * 
     * @return Array
     */
    public function generate_terminal_reports($clientId, $additional = null) {
        $the_form = [];

        // get the client data
        global $defaultUser, $academicSession;
        $client_data = $additional->client_data;

        // run the school academic terms query
        $this->academic_terms();

        // run this query
        $disabled = in_array($additional->user_type, ["student"]) ? "disabled='disabled'" : null;
        $prefs = !empty($client_data) ? $client_data->client_preferences : (object)[];

        $this_user = "<option value=''>Select Student</option>";
        
        // if the item is not disabled
        if(!empty($disabled)) {
            $this_user = "<option value='{$additional->user->user_id}'>{$additional->user->name}</option>";
        }

        // get the list of all classes
        $classes_param = (object) [
            "filter" => $defaultUser->user_type,
            "userId" => $defaultUser->user_id,
            "columns" => "a.id, a.item_id, a.name, a.payment_module",
            "clientId" => $clientId,
            "limit" => 100
        ];
        $classes_list = load_class("classes", "controllers")->list($classes_param)["data"];
        
        // if the submission of report is false
        $the_form["general"] = "
            <div class='row' id='terminal_reports'>
                <div class='col-md-4 mb-2'>
                    <select data-width='100%' class='form-control selectpicker' name='academic_year' id='academic_year'>
                        <option value=''>Select Academic Year</option>";
                            foreach($this->academic_calendar_years as $year_group) {
                                $the_form["general"] .= "<option ".(($client_data && $year_group === $prefs->academics->academic_year) ? "selected" : null)." value=\"{$year_group}\">{$year_group}</option>";                            
                            }
                        $the_form["general"] .= "</select>
                    </select>
                </div>
                <div class='col-md-4 mb-2'>
                    <select data-width='100%' class='form-control selectpicker' name='academic_term' id='academic_term'>
                        <option value=''>Select Academic {$academicSession}</option>";
                            foreach($this->school_academic_terms as $each) {
                                $the_form["general"] .= "<option ".(($client_data && $each->name === $prefs->academics->academic_term) ? "selected" : null)." value=\"{$each->name}\">{$each->description}</option>";                            
                            }
                        $the_form["general"] .= "</select>
                    </select>
                </div>
                <div class='col-md-4 mb-2'></div>
                <div class='col-lg-4 col-md-4 mb-2'>
                    <select data-width='100%' {$disabled} class='form-control selectpicker' name='class_id' id='class_id'>
                        <option value=''>Select the Class</option>";
                        foreach($classes_list as $class) {
                            $the_form["general"] .= "<option data-payment_module='{$class->payment_module}' ".($additional->class_guid ==  $class->item_id ? "selected" : null)." value='{$class->item_id}'>".strtoupper($class->name)."</option>";
                        }
        $the_form["general"] .= "</select>
                    </div>
                    <div class='col-lg-6 col-md-6 mb-2'>
                        <select data-width='100%' class='form-control selectpicker' name='student_id' id='student_id'>
                            {$this_user}
                        </select>
                    </div>
                    <div class='col-md-2' id='generate_report_button'>
                        <button onclick='return generate_terminal_report()' type='generate_report' class='btn btn-block btn-outline-primary'>Generate</button>
                    </div>
                    <div class='col-lg-12 mt-4'></div>
                    <div class='col-lg-12 mt-1 text-center' id='notification'></div>
                    <div class='col-lg-12 mt-4' id='summary_report_sheet_content'></div>
                </div>";

        return $the_form;
    }

    /**
     * Generate the Payroll Form
     * 
     * @param String    $clientId
     * @param String    $userId
     * 
     * @return Array 
     */
    public function payroll_form($clientId, $userId, $data) {

        $forms = [];

        // get the list of banks
        $banks_list = $this->pushQuery("id, bank_name, phone_number", "banks_list", "1 ORDER BY bank_name");
        
        $bank = '
        <form autocomplete="Off" class="ajax-data-form" action="'.$this->baseUrl.'api/payroll/paymentdetails" method="POST" id="ajax-data-form-content">
            <div class="row">
                <div class="col-lg-12">
                    <h5>BANK DETAILS</h5>
                </div>
                <div class="col-lg-6 col-md-6">
                    <label for="account_name">Account Holder Name</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" value="'.($data->account_name ?? null).'" maxlength="255" name="account_name" id="account_name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <label for="account_number">Account Number</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-paperclip"></i></span>
                        </div>
                        <input type="text" maxlength="24" value="'.($data->account_number ?? null).'" name="account_number" id="account_number" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <label for="bank_name">Bank Name</label>
                    <div class="input-group mb-3">
                        <select data-width="100%" class="form-control selectpicker" id="bank_name" name="bank_name">
                            <option value="">Select Bank Name</option>';
                        foreach($banks_list as $this_bank) {
                            $bank .= "<option value=\"{$this_bank->id}\" ".(isset($data->bank_name) && ($data->bank_name == $this_bank->id) ? "selected" : null).">{$this_bank->bank_name}</option>";
                        }
                        $bank .=
                        '</select>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <label for="bank_branch">Bank Branch</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-home"></i></span>
                        </div>
                        <input type="text" value="'.($data->bank_branch ?? null).'" maxlength="40" name="bank_branch" id="bank_branch" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <label for="ssnit_number">SSNIT Number</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-tablet"></i></span>
                        </div>
                        <input type="text" value="'.($data->ssnit_number ?? null).'" maxlength="40" name="ssnit_number" id="ssnit_number" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <label for="tin_number">Tax Identification Number</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-tablet"></i></span>
                        </div>
                        <input type="text" value="'.($data->tin_number ?? null).'" maxlength="40" name="tin_number" id="tin_number" class="form-control">
                    </div>
                    <input type="hidden" value="'.$userId.'" id="employee_id" name="employee_id" readonly>
                </div>
                <div class="col-md-12 text-right">
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        // fetch the allowances of the employee
        $employeeAllowances = $data->_allowances;
        $employeeDeductions = $data->_deductions;
        
        // fetch all allowances
        $allowances = $this->pushQuery('*', "payslips_allowance_types", "type='Allowance' AND status='1' AND client_id='{$clientId}'");
        $deductions = $this->pushQuery('*', "payslips_allowance_types", "type='Deduction' AND status='1' AND client_id='{$clientId}'");
        
        // initializing
        $ii = 0;
        $allowances_list = "";
        $deductions_list = "";
        
        // count the number of rows found
        if(!empty($employeeAllowances)) {

            // loop through the list of allowances
            foreach($employeeAllowances as $eachAllowance) {
                // Increment 
                $ii++;

                // append to the list
                $allowances_list .= '
                <div class="initial mb-2" data-row="'.$ii.'">
                    <div class="row">
                        <div class="col-lg-'.(($ii == 1) ? 7 : 6).' mb-2 col-md-'.(($ii == 1) ? 7 : 6).'">
                            <select data-width="100%" name="allowance[]" id="allowance_'.$ii.'" class="form-control selectpicker">
                                <option value="">Please Select</option>';
                                foreach($allowances as $each) {
                                    $allowances_list .= "<option data-default_value='{$each->default_amount}' ".(($eachAllowance->allowance_id == $each->id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
                                }
                            $allowances_list .= '
                            </select>
                        </div>
                        <div class="col-lg-5 mb-2 col-md-5">
                            <input value="'.$eachAllowance->amount.'" min="0" max="20000" placeholder="Amount" class="form-control" type="number" name="allowance_amount[]" id="allowance_amount_'.$ii.'">
                        </div>';
                        if($ii > 1) {
                            $allowances_list .= '
                            <div class="text-center">
                                <span class="remove-row cursor btn btn-outline-danger" data-type="allowance" data-value="'.$ii.'"><i class="fa fa-trash"></i></span>
                            </div>';
                        }
                $allowances_list .= '</div></div>';
            }

        } else {
            $allowances_list = '
            <div class="initial mb-2" data-row="1">
                <div class="row">
                    <div class="col-lg-7 mb-2 col-md-7">
                        <select data-width="100%" name="allowance" id="allowance_1" class="form-control selectpicker">
                            <option value="">Please Select</option>';
                            foreach($allowances as $each) {
                                $allowances_list .= "<option data-default_value='{$each->default_amount}' value=\"{$each->id}\">{$each->name}</option>";
                            }
                            $allowances_list .= '
                        </select>
                    </div>
                    <div class="col-lg-5 mb-2 col-md-5">
                        <input placeholder="Amount" min="0" max="20000" class="form-control" type="number" name="allowance_amount_1" id="allowance_amount_1">
                    </div>
                </div>
            </div>';
        }

        // count the number of rows found
        if(!empty($employeeDeductions)) {

            // loop through the list of allowances
            foreach($employeeDeductions as $eachDeduction) {
                // Increment 
                $ii++;

                // append to the list
                $deductions_list .= '
                <div class="initial mb-2" data-row="'.$ii.'">
                    <div class="row">
                        <div class="col-lg-'.(($ii == 1) ? 7 : 6).' mb-2 col-md-'.(($ii == 1) ? 7 : 6).'">
                            <select data-width="100%" name="deductions[]" id="deductions_'.$ii.'" class="form-control selectpicker">
                                <option value="">Please Select</option>';
                                // using foreach loop
                                foreach($deductions as $each) {
                                    // print the list of countries
                                    $deductions_list .= "<option data-default_value='{$each->default_amount}' ".(($eachDeduction->allowance_id == $each->id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
                                }
                            $deductions_list .= '
                            </select>
                        </div>
                        <div class="col-lg-5 mb-2">
                            <input value="'.$eachDeduction->amount.'" min="0" max="20000" placeholder="Amount" class="form-control" type="number" name="deductions_amount[]" id="deductions_amount_'.$ii.'">
                        </div>';
                        if($ii > 1) {
                            $deductions_list .= '
                            <div class="text-center">
                                <span class="remove-row cursor btn btn-outline-danger" data-type="deductions" data-value="'.$ii.'"><i class="fa fa-trash"></i></span>
                            </div>';
                        }
                $deductions_list .= '</div></div>';
            }

        } else {
            $deductions_list = '
            <div class="initial mb-2" data-row="1">
                <div class="row">
                    <div class="col-lg-7 mb-2 col-md-7">
                        <select data-width="100%" name="deductions" id="deductions_1" class="form-control selectpicker">
                            <option value="">Please Select</option>';
                            // using foreach loop
                            foreach($deductions as $each) {
                                // print the list of countries
                                $deductions_list .= "<option data-default_value='{$each->default_amount}' value=\"{$each->id}\">{$each->name}</option>";
                            }
                            $deductions_list .= '
                        </select>
                    </div>
                    <div class="col-lg-5 mb-2 col-md-5">
                        <input placeholder="Amount" min="0" max="20000" class="form-control" type="number" name="deductions_amount_1" id="deductions_amount_1">
                    </div>
                </div>
            </div>';
        }

        $allowance = '
            <div class="row">
                <div class="col-lg-12"><h5>BASIC SALARY</h5></div>
                <div class="col-lg-6">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-american-sign-language-interpreting"></i> '.($data->client->client_preferences->labels->currency ?? null).'</span>
                        </div>
                        <input type="text" value="'.($data->basic_salary ?? null).'" name="basic_salary" id="basic_salary" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12 mt-3 border-top pt-3"><h5>ALLOWANCES</h5></div>
                <div class="col-lg-10">
                    <div width="100%" class="text-right mb-2">
                        <button type="button" class="btn btn-info add-allowance"><i class="fa fa-plus"></i></button>
                    </div>
                    <div class="allowance-div mb-4">
                        '.$allowances_list.'
                    </div>
                </div>
                <div class="col-lg-12 mt-3 border-top pt-3"><h5>DEDUCTIONS</h5></div>
                <div class="col-lg-10">
                    <div width="100%" class="text-right mb-2">
                        <button type="button" class="btn btn-info add-deductions"><i class="fa fa-plus"></i></button>
                    </div>
                    <div class="deductions-div mb-4">
                        '.$deductions_list.'
                    </div>
                </div>
                <div class="col-md-10 text-right">
                    <input type="hidden" value="'.$userId.'" id="employee_id" name="employee_id" readonly>
                    <button onclick="return save_staff_allowances(\''.$userId.'\')" type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>';
        
        $forms["bank_detail"] = $bank;
        $forms["allowance_detail"] = $allowance;

        return $forms;

    }

    /**
     * Generate Payslip form
     * 
     * @return String
     */
    public function payslip_form() {

        $html = '
            <div class="row">                
                <div class="col-lg-6 allowance-div hidden">
                    <div class="card">
                        <div class="text-center mt-2">
                            <h4> Allowances </h4>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-body">
                            <div class="allowances-list">
                                <div class="text-center">Load Employee Allowance Data</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 deductions-div hidden">
                    <div class="card">
                        <div class="text-center mt-2">
                            <h4> Deductions </h4>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-body">
                            <div class="deductions-list">
                                <div class="text-center">Load Employee Deductions Data</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-3"></div>
                <div class="col-lg-6 summary-div hidden">
                    <div class="card">
                        <div class="card-body">
                            <div class="summary-list">

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="text-center">
                                            <h2> Summary </h2>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-right font-weight-bold">Basic:</div>
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">GH&cent;</span>
                                            </div>
                                            <input type="text" name="basic_salary" readonly disabled="disabled" id="basic_salary" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 text-right font-weight-bold">Total Allowances:</div>
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">GH&cent;</span>
                                            </div>
                                            <input type="text" name="total_allowances" id="total_allowances" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 text-right font-weight-bold">Total Deductions:</div>
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">GH&cent;</span>
                                            </div>
                                            <input type="text" name="total_deductions" id="total_deductions" readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 text-right font-weight-bold">Net Salary:</div>
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">GH&cent;</span>
                                            </div>
                                            <input type="text" name="net_salary" id="net_salary" readonly class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-right font-weight-bold">Payment Mode:</div>
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <select name="payment_mode" id="payment_mode" class="form-control selectpicker">
                                                <option value="">Select Payment Mode</option>
                                                <option value="Bank">Bank</option>
                                                <option value="Cash">Cash</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 text-right font-weight-bold">Status:</div>
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <select name="payment_status" id="payment_status" class="form-control selectpicker">
                                                <option value="1">Paid</option>
                                                <option value="0">Unpaid</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 text-right font-weight-bold">Comments:</div>
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <textarea name="comments" id="comments" cols="30" rows="3" style="min-height: 80px;max-height: 80px" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 mt-3">
                                        <div class="allowance-note"></div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-lg-12 mt-3">
                                        <div class="generate-result text-center"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        
        return $html;
    }

    /**
     * E-Learning Upload Form
     * 
     * @return String
     */
    public function elearning_form($params) {

        /** Set parameters for the data to attach */
        $form_params = (object) [
            "accept" => ".mp4,.mpg,.mpeg,.flv,.pdf",
            "module" => "elearning_resource",
            "userData" => $params->thisUser,
            "item_id" => $params->data->item_id ?? null
        ];

        // predefined items
        $courses_list = [];
        $units_list = [];
        $preloaded_attachments = "";
        $comment = $params->data->allow_comments ?? null;
        $state = $params->data->state ?? null;
        $class = $params->data->class_id ?? null;

        // other parameters
        if($class) {

            // set the parameters to load the information
            $course_param = (object) [
                "userData" => $params->thisUser,
                "class_id" => $class,
                "userId" => $params->userId,
                "clientId" => $params->clientId,
                "academic_year" => $params->data->academic_year,
                "academic_term" => $params->data->academic_term
            ];
            $courses_list = load_class("courses", "controllers")->list($course_param)["data"];

            // set the course and unit
            $course = $params->data->course_id;
            $unit = $params->data->unit_id;

            // append the course id
            $course_param->course_id = $params->data->course_row_id;
            $units_list = load_class("courses", "controllers")->course_unit_lessons_list($course_param);
            
            // predefine the file attachments
            // get the attachment list
            if(isset($params->data->attachment)) {

                $attachment = json_encode($params->data->attachment);
                $attachment = json_decode($attachment);

                // set a new parameter for the items
                $files_param = (object) [
                    "userData" => $params->thisUser,
                    "label" => "list",
                    "is_deletable" => true,
                    "show_view" => "e-learning_view",
                    "module" => "elearning_resource",
                    "item_id" => $params->data->item_id,
                    "accept" => implode(",", [".mp4", ".mpg", ".mpeg", ".flv", ".pdf"]),
                    "attachments_list" => $attachment
                ];

                // create a new object
                $attachments = load_class("files", "controllers")->attachments($files_param);
            
                // get the attachments list
                $preloaded_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

            }
        }

        // load the classes list
        $classes_param = (object) [
            "limit" => 100,
            "forceYear" => true,
            "clientId" => $params->clientId,
            "columns" => "a.id, a.item_id, a.name, a.payment_module"
        ];
        $classes_list = load_class("classes", "controllers")->list($classes_param)["data"];

        $html_content = '
        <form autocomplete="Off" class="ajax-data-form" action="'.$this->baseUrl.'api/resources/'.(isset($params->data) ? "update_4elearning" : "upload_4elearning").'" method="POST" id="ajax-data-form-content">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-xl-4 col-md-4 col-12 form-group">
                            <label>Select Class <span class="required">*</span></label>
                            <select class="form-control selectpicker" data-width="100%" name="class_id">
                                <option value="">Please Select Class</option>';
                                foreach($classes_list as $each) {
                                    $html_content .= "<option data-payment_module='{$each->payment_module}' ".($class === $each->item_id ? "selected" : null)." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";
                                }
                                $html_content .= '
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4 col-12 form-group">
                            <label>Select Subject <span class="required">*</span></label>
                            <select class="form-control selectpicker" data-width="100%" name="course_id">
                                <option value="">Please Select Subject</option>';
                                foreach($courses_list as $each) {
                                    $html_content .= "<option ".($course === $each->item_id ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
                                }
                                $html_content .= '
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4 col-12 form-group">
                            <label>Select Subject Unit</label>
                            <select class="form-control selectpicker" data-width="100%" name="unit_id">
                                <option value="">Please Select Unit</option>';
                                foreach($units_list as $each) {
                                    $html_content .= "<option ".($unit === $each->item_id ? "selected" : null)." value=\"{$each->item_id}\">{$each->name}</option>";
                                }
                                $html_content .= '
                            </select>
                        </div>
                        <div class="col-lg-12 col-md-6">
                            <div class="form-group">
                                <label for="subject">Title of the Material <span class="required">*</span></label>
                                <input type="text" name="title" value="'.($params->data->subject ?? null).'" id="title" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label for="description">Summary Description of the Material</label>
                                <input type="hidden" hidden id="trix-editor-input" value="'.($params->data->description ?? null).'">
                                <trix-editor data-predefined_name="description" style="height:150px;" name="faketext" input="trix-editor-input" class="trix-slim-scroll" id="ajax-form-content"></trix-editor>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group text-center mb-1">
                                <div class="row">'.$this->form_attachment_placeholder($form_params).'</div>
                            </div>
                        </div>  
                        <div class="col-md-12 text-center mb-2">'.$preloaded_attachments.'</div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        <label>Allow / Disallow Comments</label>
                        <select class="form-control selectpicker" data-width="100%" name="allow_comment">
                            <option '.($comment === "allow" ? "selected" : null).' value="allow">Allow Comments</option>
                            <option '.($comment === "disallow" ? "selected" : null).' value="disallow">Disallow Comments</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Material State</label>
                        <select class="form-control selectpicker" data-width="100%" name="state">
                            <option '.($state === "Published" ? "selected" : null).' value="Published">Published</option>
                            <option '.($state === "Draft" ? "selected" : null).' value="Draft">Draft</option>
                        </select>
                    </div>
                    '.(isset($params->data->item_id) ? '
                        <div class="form-group">
                            <h6>CREATED BY</h6>
                            <div><i class="fa fa-user"></i> '.$params->data->fullname.'</div>
                            <div><i class="fa fa-phone"></i> '.$params->data->phone_number.'</div>
                            <div><i class="fa fa-envelope"></i> '.$params->data->email.'</div>
                            <div><i class="fa fa-calendar-check"></i> '.$params->data->date_created.'</div>
                        </div>
                        <div class="text-right">
                            <!--<a href="'.$this->baseUrl.'e-learning_view" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i> View Material</a>-->
                        </div>
                    ':'').'
                </div>
                '.(isset($params->data->item_id) ? "<input value='{$params->data->item_id}' type='hidden' id='resource_id' name='resource_id'>" : null).'
                <div class="col-md-12 text-right">
                    <button class="btn btn-success btn-sm" data-function="save" type="button-submit"><i class="fa fa-upload"></i> Upload E-Learning Material</button>
                </div>
            </div>
        </form>';

        return $html_content;

    }

    /**
     * Account Head
     * 
     * @return String
     */
    public function account_type_head(stdClass $params) {

        $data = isset($params->data) && !empty($params->data) ? $params->data : null;

        $html = "";
        $html .= "<div id=\"accounts_form\" class=\"col-12 col-md-5 col-lg-4\">";
        $html .= "<div class=\"card\">";
        $html .= "<div class=\"card-header\">".(empty($data) ? "Add Account Type Head" : "Update Account Type Head")."</div>";
        $html .= "<div class=\"card-body\">";
        $html .= "<form autocomplete=\"Off\" method=\"post\" action=\"{$this->baseUrl}api/accounting/".(!empty($data) ? "update_accounttype" : "add_accounttype")."\" class=\"ajax-data-form\" id=\"ajax-data-form-content\">";
        $html .= "<div class=\"form-group\">";
        $html .= "<label>Name <span class=\"required\">*</span></label>";
        $html .= "<input type=\"text\" name=\"name\" value=\"".($data->name ?? null)."\" class=\"form-control\">";
        $html .= "<input type=\"hidden\" readonly value=\"".($data->item_id ?? null)."\" name=\"type_id\" class=\"form-control\">";
        $html .= "</div>";
        $html .= "<div class=\"form-group\">";
        $html .= "<label>Type <span class=\"required\">*</span></label>";
        $html .= "<select class=\"form-control selectpicker\" name=\"account_type\">";
        $html .= "<option value=\"\">Select Type</option>";
        $html .= "<option ".(!empty($data) && $data->account_type == "Income" ? "selected" : null)." value=\"Income\">Income</option>";
        $html .= "<option ".(!empty($data) && $data->account_type == "Expense" ? "selected" : null)." value=\"Expense\">Expense</option>";
        $html .= "</select>";
        $html .= "</div>";
        $html .= "<div class=\"row\">";
        $html .= "<div class=\"col-md-6\" align=\"left\">";
        $html .= "<button class=\"btn btn-outline-danger\" onclick=\"return reset_account_form('api/accounting/add_accounttype')\" type=\"button\">Cancel</button>";
        $html .= "</div>";
        $html .= "<div class=\"col-md-6\" align=\"right\">";
        $html .= "<button class=\"btn btn-outline-success\" data-function=\"save\" type=\"button-submit\"><i class=\"fa fa-save\"></i> Save</button>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</form>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    /**
     * Account Head
     * 
     * @return String
     */
    public function bank_transaction_form(stdClass $params) {

        // init data
        $data = isset($params->data) && !empty($params->data) ? $params->data : null;

        /** Set parameters for the data to attach */
        $form_params = (object) [
            "module" => "bank_transactions_{$params->form_type}",
            "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
            "ismultiple" => true,
            "no_notice" => true,
            "userData" => $params->userData,
            "item_id" => $data->item_id ?? null
        ];

        // get the list of banks
        $banks_list = $this->pushQuery("id, bank_name, phone_number", "banks_list", "1 ORDER BY bank_name");

        $html = "";
        $html .= "<div id=\"bank_transaction_form\" class=\"col-12 col-md-5 col-lg-4\">";
        $html .= "<div class=\"card\">";
        $html .= "<div class=\"card-header\">".(empty($data) ? "Add {$params->form_type}" : "Update {$params->form_type}")."</div>";
        $html .= "<div class=\"card-body\">";
        $html .= "<form autocomplete=\"Off\" method=\"post\" action=\"{$this->baseUrl}api/accounting/".(!empty($data) ? "update_{$params->form_url}" : "add_{$params->form_url}")."\" class=\"ajax-data-form\" id=\"ajax-data-form-content\">";
        $html .= '<div class="form-group">
            <label>Bank Name <span class="required">*</span></label>
            <select data-width="100%" class="form-control selectpicker" id="bank_id" name="bank_id">
            <option value="">Select Bank</option>';
            foreach($banks_list as $bank) {
                $html .= "<option value=\"{$bank->id}\">{$bank->bank_name}</option>";
            }
        $html .= "</select></div>
            <div class=\"form-group\">
                <label>Bank Account Number <span class=\"required\">*</span></label>
                <input type=\"text\" onkeyup='this.value = this.value.replace(/[^\d.]+/g, \"\");' maxlength='15' name=\"account_number\" value=\"".($data->account_number ?? null)."\" class=\"form-control\">
                <input type=\"hidden\" readonly value=\"".($data->transaction_id ?? null)."\" name=\"transaction_id\" class=\"form-control\">
            </div>
            <div class=\"form-group\">
                <label>Account Name <span class=\"required\">*</span></label>
                <input maxlength='64' type=\"text\" name=\"account_name\" value=\"".($data->account_name ?? null)."\" class=\"form-control\">
            </div>
            <div class=\"form-group\">
                <label>Amount {$params->form["amount"]}<span class=\"required\">*</span></label>
                <input type=\"text\" maxlength='7' onkeyup='this.value = this.value.replace(/[^\d.]+/g, \"\");' name=\"amount\" value=\"".($data->amount ?? null)."\" class=\"form-control\">
            </div>
            <div class=\"form-group\">
                <label>Description</label>
                <textarea maxlength='255' type=\"text\" name=\"description\" class=\"form-control\">".($data->account_name ?? null)."</textarea>
            </div>
            <div class='form-group text-center mb-0'>
                <div class='row'>{$this->form_attachment_placeholder($form_params)}</div>
            </div>
            <div class='form-group text-danger text-center'>
                <em>NB: This action cannot be reversed once confirmed.</em>
            </div><div class=\"d-flex justify-content-between\">
            <div align=\"left\">
                <button class=\"btn btn-outline-danger\" onclick=\"return reset_account_form('api/accounting/add_accounttype')\" type=\"button\">Cancel</button>
            </div>
                <div align=\"right\">
                    <button class=\"btn btn-outline-success\" data-function=\"save\" type=\"button-submit\"><i class=\"fa fa-save\"></i> Save</button>
                    </div>
                </div>
            </form>
            </div>
            </div>
        </div>";

        return $html;
    }

    /**
     * Account Head
     * 
     * @return String
     */
    public function bank_accounts_form(stdClass $params) {

        $data = isset($params->data) && !empty($params->data) ? $params->data : null;

        // get the list of banks
        $banks_list = $this->pushQuery("id, bank_name, phone_number", "banks_list", "1 ORDER BY bank_name");

        $html = "";
        $html .= "<div id=\"accounts_form\" class=\"col-12 col-md-5 col-lg-4\">";
        $html .= "<div class=\"card\">";
        $html .= "<div class=\"card-header\">".(empty($data) ? "Create Account" : "Update Account")."</div>";
        $html .= "<div class=\"card-body\">";
        $html .= "<form autocomplete=\"Off\" method=\"post\" action=\"{$this->baseUrl}api/accounting/".(!empty($data) ? "update_account" : "add_account")."\" class=\"ajax-data-form\" id=\"ajax-data-form-content\">";
        $html .= "<div class=\"form-group\">";
        $html .= "<label>Account Name <span class=\"required\">*</span></label>";
        $html .= "<input type=\"text\" name=\"account_name\" value=\"".($data->account_name ?? null)."\" class=\"form-control\">";
        $html .= "<input type=\"hidden\" readonly value=\"".($data->item_id ?? null)."\" name=\"account_id\" class=\"form-control\">";
        $html .= "</div>";
        $html .= '<div class="form-group">
            <label>Bank Name <span class="required">*</span></label>
            <select data-width="100%" class="form-control selectpicker" id="account_bank" name="account_bank">
            <option value="">Select Bank Name</option>';
            foreach($banks_list as $bank) {
                $html .= "<option value=\"{$bank->bank_name}\">{$bank->bank_name}</option>";
            }
        $html .= '</select></div>';
        $html .= "<div class=\"form-group\">";
        $html .= "<label>Account Number <span class=\"required\">*</span></label>";
        $html .= "<input type=\"text\" name=\"account_number\" value=\"".($data->account_number ?? null)."\" class=\"form-control\">";
        $html .= "</div>";
        $html .= "<div class=\"form-group\">";
        $html .= "<label>Description</label>";
        $html .= "<textarea name=\"description\" class=\"form-control\">".($data->description ?? null)."</textarea>";
        $html .= "</div>";
        $html .= "<div class=\"form-group\">";
        $html .= "<label>Opening Balance <span class=\"required\">*</span></label>";
        $html .= "<input type=\"number\" name=\"opening_balance\" value=\"".($data->opening_balance ?? 0)."\" class=\"form-control\">";
        $html .= "</div>";
        $html .= "<div class=\"d-flex justify-content-between\">";
        $html .= "<div align=\"left\">";
        $html .= "<button class=\"btn btn-outline-danger\" onclick=\"return reset_account_form('api/accounting/add_account', 'Create Account')\" type=\"button\">Cancel</button>";
        $html .= "</div>";
        $html .= "<div align=\"right\">";
        $html .= "<button class=\"btn btn-outline-success\" data-function=\"save\" type=\"button-submit\"><i class=\"fa fa-save\"></i> Save</button>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</form>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    /**
     * Transactions Form
     * 
     * @return String
     */
    public function transaction_form(stdClass $params) {
        
        $data = isset($params->data) && !empty($params->data) ? $params->data : null;

        // the route for the form
        $form_route = [
            "deposit" => [
                "type" => "Income",
                "title" => "Add Income",
                "add" => "{$this->baseUrl}api/accounting/add_deposit",
                "update" => "{$this->baseUrl}api/accounting/update_deposit"
            ],
            "expense" => [
                "type" => "Expense",
                "title" => "Add Expense",
                "add" => "{$this->baseUrl}api/accounting/add_expenditure",
                "update" => "{$this->baseUrl}api/accounting/update_expenditure"
            ]
        ];

        /** init content */
        $preloaded_attachments = "";

        /** Set parameters for the data to attach */
        $form_params = (object) [
            "module" => "accounts_transaction_{$params->route}",
            "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
            "ismultiple" => true,
            "userData" => $params->userData,
            "item_id" => $data->item_id ?? null
        ];
        
        // get the attachment list
        if(isset($params->data->attachment)) {

            // convert to object
            $params->data->attachment = json_decode($params->data->attachment);

            // set a new parameter for the items
            $files_param = (object) [
                "userData" => $params->userData,
                "label" => "list",
                "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf"]),
                "is_deletable" => (bool) ($data->state === "Pending"),
                "module" => "accounts_transaction",
                "item_id" => $data->item_id,
                "attachments_list" => $data->attachment
            ];

            // create a new object
            $attachments = load_class("files", "controllers")->attachments($files_param);
        
            // get the attachments list
            $preloaded_attachments = !empty($attachments) && isset($attachments["data"]) ? $attachments["data"]["files"] : null;

        }

        // load the accounts
        $accounts_list = $this->pushQuery("account_name, item_id, account_number, account_bank, default_account", "accounts", "client_id = '{$params->clientId}' AND state='Active' AND status='1'");
        $accounts_head_list = $this->pushQuery("name, item_id", "accounts_type_head", "client_id = '{$params->clientId}' AND status='1' AND type='{$form_route[$params->route]["type"]}'");

        $html = "
        <div class='row'>
            <div id=\"accounts_form\" class=\"col-md-2\"></div>
            <div id=\"accounts_form\" class=\"col-12 col-md-7 col-lg-7\">
                <div class=\"card\">
                    <div class=\"form-content-loader\" style=\"display: none; position: absolute\">
                        <div class=\"offline-content text-center\">
                            <p><i class=\"fa fa-spin fa-spinner fa-3x\"></i></p>
                        </div>
                    </div>
                    <div class=\"card-header\">".(empty($data) ? $form_route[$params->route]["title"] : $form_route[$params->route]["title"])."</div>
                    <div class=\"card-body\">
                    <form autocomplete=\"Off\" method=\"post\" action=\"".(!empty($data) ? $form_route[$params->route]["update"] : $form_route[$params->route]["add"])."\" class=\"ajax-data-form\" id=\"ajax-data-form-content\">
                        <div class=\"form-group\">
                            <label>Account <span class=\"required\">*</span></label>
                            <select data-width=\"100%\" name=\"account_id\" class=\"form-control selectpicker\">";
                                if(empty($accounts_list) || count($accounts_list) > 1) {
                                    $html .= "<option value=\"\">Select Account</option>";
                                }
                                foreach($accounts_list as $account) {
                                   $html .= "<option ".((!empty($data) && $account->item_id === $data->account_id) || $account->default_account ? "selected" : null)." value=\"{$account->item_id}\">{$account->account_name} - {$account->account_bank}</option>"; 
                                }
                            $html .= "</select>
                            <input type=\"hidden\" readonly value=\"".($data->item_id ?? null)."\" name=\"transaction_id\" class=\"form-control\">
                        </div>
                        <div class=\"form-group\">
                            <label>Account Type Head <span class=\"required\">*</span></label>
                            <select data-width=\"100%\" name=\"account_type\" class=\"form-control selectpicker\">
                                <option value=\"\">Select Account Type Head</option>";
                                foreach($accounts_head_list as $type) {
                                   $html .= "<option ".(!empty($data) && $type->item_id === $data->account_type ? "selected" : null)." value=\"{$type->item_id}\">{$type->name}</option>"; 
                                }
                            $html .= "</select>
                        </div>
                        <div class=\"form-group\">
                            <label>Reference <span class=\"required\">*</span></label>
                            <input type=\"text\" name=\"reference\" value=\"".($data->reference ?? null)."\" class=\"form-control\">
                        </div>
                        <div class=\"form-group\">
                            <label>Amount <span class=\"required\">*</span></label>
                            <input type=\"number\" name=\"amount\" value=\"".($data->amount ?? null)."\" class=\"form-control\">
                        </div>
                        <div class=\"form-group\">
                            <label>Date <span class=\"required\">*</span></label>
                            <input type=\"text\" name=\"date\" readonly value=\"".($data->record_date ?? date("Y-m-d"))."\" class=\"form-control\">
                        </div>
                        <div class=\"form-group\">
                            <label>Pay Via</label>
                            <select data-width=\"100%\" name=\"payment_medium\" class=\"form-control selectpicker\">
                                <option value=\"\">Select Payment Medium</option>";
                                foreach($this->payment_methods as $key => $value) {
                                   $html .= "<option ".(!empty($data) && $key === $data->payment_medium ? "selected" : null)." value=\"{$key}\">{$value}</option>"; 
                                }
                            $html .= "
                            </select>
                        </div>
                        <div class=\"form-group\">
                            <label>Description</label>
                            <textarea name=\"description\" class=\"form-control\">".($data->description ?? null)."</textarea>
                        </div>";
                        $html .= "
                        <div class='col-lg-12'>
                            <div class='form-group text-center mb-1'><div class='row'>{$this->form_attachment_placeholder($form_params)}</div></div>
                        </div>
                        <div class=\"row\">
                            <div class=\"col-md-6\" align=\"left\">
                                <button class=\"btn btn-outline-danger\" onclick=\"return reset_account_form('{$form_route[$params->route]["add"]}', '{$form_route[$params->route]["title"]}')\" type=\"button\">Cancel</button>
                            </div>
                            <div class=\"col-md-6\" align=\"right\">
                                <button class=\"btn btn-outline-success\" data-function=\"save\" type=\"button-submit\"><i class=\"fa fa-save\"></i> Save</button>
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
     * SMS Template Form
     * 
     * @return String
     */
    public function smsemail_template_form(stdClass $params) {

        // the route for the form
        $form_route = [
            "sms" => [
                "type" => "SMS",
                "title" => "Create Template",
                "add" => "{$this->baseUrl}api/communication/add_template",
                "update" => "{$this->baseUrl}api/communication/update_template"
            ],
            "email" => [
                "type" => "Email",
                "title" => "Create Template",
                "add" => "{$this->baseUrl}api/communication/add_template",
                "update" => "{$this->baseUrl}api/communication/update_template"
            ]
        ];
        
        $data = isset($params->data) && !empty($params->data) ? $params->data : null;
        $dynamic_tags = "";

        // loop through the dynamic tags
        foreach(["{name}", "{balance}"] as $tag) {
            $dynamic_tags .= "<span onclick=\"return append_dynamic_tag('{$tag}','{$params->route}')\" class=\"tags mr-2\">{$tag}</span>";
        }
        
        $html = "
        <div class='row'>
            <div class=\"col-md-2\"></div>
            <div id=\"communication_form\" class=\"col-12 col-md-7 col-lg-7\">
                <div class=\"card\">
                    <div class=\"card-header\">".(empty($data) ? $form_route[$params->route]["title"] : $form_route[$params->route]["title"])."</div>
                    <div class=\"card-body\">
                        <form method=\"post\" action=\"".(!empty($data) ? $form_route[$params->route]["update"] : $form_route[$params->route]["add"])."\" class=\"ajax-data-form\" id=\"ajax-data-form-content\">
                            <div class=\"form-group\">
                                <label>Name <span class=\"required\">*</span></label>
                                <input type=\"text\" name=\"name\" value=\"".($data->name ?? null)."\" class=\"form-control\">
                            </div>
                            ".($params->route == "sms" ? 
                            "<div class=\"form-group\">
                                <label>SMS Module</label>
                                <select class=\"selectpicker\" name=\"module\" data-width=\"100%\">
                                    <option>Select Module (Optional)</option>
                                    <option value=\"f\">Fees Payment Reminder</option>
                                </select>
                            </div>"
                            : null)."
                            ".($params->route == "email" ? 
                            "<div class=\"form-group hidden\">
                                <label>Subject</label>
                                <input disabled type=\"text\" name=\"subject\" value=\"".($data->subject ?? null)."\" class=\"form-control\">
                            </div>"
                            : null)."
                            <div class=\"form-group\">
                                <label>Message <span class=\"required\">*</span></label>
                                ".(
                                    ($params->route === "email") ? $this->textarea_editor($data->body ?? null, "faketext", "ajax-form-content", "message") : 
                                    (
                                        ($params->route === "sms") ? "
                                        <textarea maxlength=\"480\" name=\"message\" style=\"height:200px\" class=\"form-control\">".($data->body ?? null)."</textarea>
                                        <div class=\"text-right alert-danger\"> 
                                            <span class=\"remaining_count p-1\">0 characters remaining</span>
                                            <span id=\"messages\">0 message</span>
                                        </div>
                                        " : null
                                    )
                                )."
                            </div>
                            <div class=\"form-group mt-0\">
                                <div class=\"tags-container\">
                                    {$dynamic_tags}
                                </div>
                            </div>
                            <div class=\"form-group\">
                                <div class=\"row\">
                                    <div class=\"col-md-6\" align=\"left\">
                                        <button class=\"btn btn-outline-danger\" onclick=\"return reset_communication_form('{$form_route[$params->route]["add"]}', '{$form_route[$params->route]["title"]}')\" type=\"button\">Cancel</button>
                                    </div>
                                    <input type=\"hidden\" readonly name=\"type\" value=\"{$params->route}\">
                                    <input type=\"hidden\" readonly name=\"template_id\" value=\"".($data->item_id ?? null)."\">
                                    <div class=\"col-md-6\" align=\"right\">
                                        <button class=\"btn btn-outline-success\" data-function=\"save\" type=\"button-submit\"><i class=\"fa fa-save\"></i> Save</button>
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
     * Send SMS & Email Form
     * 
     * @return Array
     */
    public function smsemail_form(stdClass $params) {
        
        $html = [];
        $forms = ["email", "sms"];
        global $usersClass;

        // get the list of all templates
        $templates_array = $this->pushQuery("name, id, item_id, type, message", "smsemail_templates", "client_id='{$params->clientId}' AND status='1'");
        $class_array_list = $this->pushQuery("name, id, item_id", "classes", "client_id='{$params->clientId}' AND status='1'");
        
        // get the list of all other users
        $other_users_list = $this->pushQuery("name, user_type, unique_id, item_id", "users", 
            "client_id='{$params->clientId}' AND status='1' AND user_type != 'student'");

        // get the list of only students
        $students_array_list = $this->pushQuery("name, user_type, unique_id, item_id", "users", 
            "client_id='{$params->clientId}' AND status='1'
            AND academic_year = '{$params->preferences->academics->academic_year}' AND
            academic_term = '{$params->preferences->academics->academic_term}' AND user_type='student'
        ");
        $users_array_list = [];

        // get the users list
        foreach($other_users_list as $user) {
            $users_array_list[] = $user;
        }
        foreach($students_array_list as $user) {
            $users_array_list[] = $user;
        }

        // sms email modules
        $modules = [""];

        // loop through the forms
        foreach($forms as $route) {

            // append to the form
            $html[$route] = "";
            $html[$route] .= "
            <div class=\"send_smsemail\">
                <form method=\"post\" action=\"{$this->baseUrl}api/communication/send_message\" class=\"form_send_message\" id=\"send_form_{$route}\" data-route=\"{$route}\">
                    <div class=\"row\">
                        <div class=\"col-md-6\">
                            <div class=\"form-group mb-1\">
                                <label>Campaign Name <span class=\"required\">*</span></label>
                                <input type=\"text\" name=\"campaign_name\" class=\"form-control\">
                            </div>
                        </div>
                        <div class=\"col-md-6\">
                            <div class=\"form-group mb-1\">
                                <label>Template</label>
                                <select type=\"text\" data-route=\"{$route}\" data-width=\"100%\" name=\"template_id\" class=\"form-control selectpicker\">
                                    <option value=''>Select Template</option>";
                                    foreach($templates_array as $key => $template) {
                                        $templates_array[$key]->message = htmlspecialchars_decode($template->message);
                                        if($template->type == $route) {
                                            $html[$route] .= "<option value='{$template->item_id}'>{$template->name}</option>";
                                        }
                                    }
                        $html[$route] .= "</select>
                            </div>
                        </div>
                        <div class=\"col-md-12\">
                            <div class=\"form-group mb-1\">
                                <label>Message <span class=\"required\">*</span></label>
                                ".(
                                    ($route === "email") ? $this->textarea_editor(null, "faketext", "ajax-form-content", "message") : 
                                    "<textarea data-route=\"{$route}\" maxlength=\"480\" name=\"message\" style=\"height:200px\" class=\"form-control\"></textarea>
                                    <div class=\"text-right alert-danger\"> 
                                        <span class=\"remaining_count p-1\">0 characters remaining</span>
                                        <span id=\"messages\">0 message</span>
                                    </div>"
                                )."
                            </div>
                        </div>
                        <div class=\"col-md-12 hidden\" id=\"class_select_{$route}\">
                            <div class=\"form-group mb-1\">
                                <label>Class <span class=\"required\">*</span></label>
                                <select data-selectors=\"{$route}\" data-route=\"{$route}\" name=\"class_id\" class=\"form-control selectpicker\" data-width=\"100%\">
                                    <option value=''>Select</option>";
                                    foreach($class_array_list as $class) {
                                        $html[$route] .= "<option value='{$class->item_id}'>{$class->name}</option>";
                                    }
                        $html[$route] .= "</select>
                            </div>
                        </div>
                    </div>
                    <div class=\"row mt-3\">
                        <div class=\"col-md-12 mb-xs\">
                            <div class=\"form-group\">
                                <div class=\"checkbox-replace\">
                                    <label class=\"i-checks cursor\"><input data-route=\"{$route}\" type=\"checkbox\" name=\"send_later\"><i></i> Send Later</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-md-8\">
                            <div class=\"form-group\">
                                <label class=\"control-label\">Schedule Date <span class=\"required\">*</span></label>
                                <div class=\"input-group\">
                                    <input data-route=\"{$route}\" disabled data-maxdate=\"".date("Y-m-d", strtotime("+3 months"))."\" type=\"text\" class=\"form-control datepicker\" name=\"schedule_date\">
                                </div>
                                <span class=\"error\"></span>
                            </div>
                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label class=\"control-label\">Schedule Time <span class=\"required\">*</span></label>
                                <div class=\"input-group\">
                                    <input data-route=\"{$route}\" type=\"time\" disabled name=\"schedule_time\" class=\"form-control\" value=\"\">
                                    <span class=\"error\"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=\"row mt-3\">
                        <div class=\"col-md-6\" align=\"left\"></div>
                        <input type=\"hidden\" data-route=\"{$route}\" readonly name=\"type\" value=\"{$route}\">
                        <div class=\"col-md-6\" align=\"right\">
                            <button class=\"btn btn-outline-success\" type=\"submit\"><i class=\"fa fa-mail-bulk\"></i> Send Message</button>
                        </div>
                    </div>
                </form>
            </div>";

        }
        $html["templates_array"] = $templates_array;
        $html["users_array_list"] = $users_array_list;
        $html["class_array_list"] = $class_array_list;

        return $html;
    }

    /**
     * Send SMS & Email Form
     * 
     * @return Array
     */
    public function smsemail_form_old(stdClass $params) {
        
        $html = [];
        $forms = ["email", "sms"];
        global $usersClass;

        // get the list of all templates
        $templates_array = $this->pushQuery("name, id, item_id, type, message", "smsemail_templates", "client_id='{$params->clientId}' AND status='1'");
        $class_array_list = $this->pushQuery("name, id, item_id", "classes", "client_id='{$params->clientId}' AND status='1'");
        
        // get the list of all other users
        $other_users_list = $this->pushQuery("name, user_type, unique_id, item_id", "users", 
            "client_id='{$params->clientId}' AND status='1' AND user_type != 'student'");

        // get the list of only students
        $students_array_list = $this->pushQuery("name, user_type, unique_id, item_id", "users", 
            "client_id='{$params->clientId}' AND status='1'
            AND academic_year = '{$params->preferences->academics->academic_year}' AND
            academic_term = '{$params->preferences->academics->academic_term}' AND user_type='student'
        ");
        $users_array_list = [];

        // get the users list
        foreach($other_users_list as $user) {
            $users_array_list[] = $user;
        }
        foreach($students_array_list as $user) {
            $users_array_list[] = $user;
        }

        // sms email modules
        $modules = [""];

        // loop through the forms
        foreach($forms as $route) {

            // append to the form
            $html[$route] = "";
            $html[$route] .= "
            <div class=\"send_smsemail\">
                <form method=\"post\" action=\"{$this->baseUrl}api/communication/send_message\" class=\"form_send_message\" id=\"send_form_{$route}\" data-route=\"{$route}\">
                    <div class=\"row\">
                        <div class=\"col-md-6\">
                            <div class=\"form-group mb-1\">
                                <label>Campaign Name <span class=\"required\">*</span></label>
                                <input type=\"text\" name=\"campaign_name\" class=\"form-control\">
                            </div>
                        </div>
                        <div class=\"col-md-6\">
                            <div class=\"form-group mb-1\">
                                <label>Template</label>
                                <select type=\"text\" data-route=\"{$route}\" data-width=\"100%\" name=\"template_id\" class=\"form-control selectpicker\">
                                    <option value=''>Select Template</option>";
                                    foreach($templates_array as $key => $template) {
                                        $templates_array[$key]->message = htmlspecialchars_decode($template->message);
                                        if($template->type == $route) {
                                            $html[$route] .= "<option value='{$template->item_id}'>{$template->name}</option>";
                                        }
                                    }
                        $html[$route] .= "</select>
                            </div>
                        </div>
                        <div class=\"col-md-12\">
                            <div class=\"form-group mb-1\">
                                <label>Message <span class=\"required\">*</span></label>
                                ".(
                                    ($route === "email") ? $this->textarea_editor(null, "faketext", "ajax-form-content", "message") : 
                                    "<textarea data-route=\"{$route}\" maxlength=\"480\" name=\"message\" style=\"height:200px\" class=\"form-control\"></textarea>
                                    <div class=\"text-right alert-danger\"> 
                                        <span class=\"remaining_count p-1\">0 characters remaining</span>
                                        <span id=\"messages\">0 message</span>
                                    </div>"
                                )."
                            </div>
                        </div>
                        <div class=\"col-md-6\">
                            <div class=\"form-group mb-1\">
                                <label>Type <span class=\"required\">*</span></label>
                                <select data-route=\"{$route}\" type=\"text\" name=\"recipient_type\" data-width=\"100%\" class=\"form-control selectpicker\">
                                    <option value=''>Select Type</option>
                                    <option value='group'>Group</option>
                                    <option value='individual'>Individual</option>
                                    <option value='class'>Class</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"col-md-12 hidden\" id=\"role_group_select_{$route}\">
                            <div class=\"form-group mb-1\">
                                <label>Role <span class=\"required\">*</span></label>
                                <select data-selectors=\"{$route}\" data-route=\"{$route}\" name=\"role_group[]\" class=\"form-control selectpicker\" multiple=\"true\" data-width=\"100%\">
                                    <option value=''>Select</option>
                                    <option value=\"admin\">Admin</option>
                                    <option value=\"teacher\">Teacher</option>
                                    <option value=\"accountant\">Accountant</option>
                                    <option value=\"employee\">Employees</option>
                                    <option value=\"guardian\">Parent</option>
                                    <option value=\"student\">Student</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"col-md-12 hidden\" id=\"individual_select_{$route}\">
                            <div class=\"form-group mb-1\">
                                <label>Role <span class=\"required\">*</span></label>
                                <select data-selectors=\"{$route}\" data-route=\"{$route}\" name=\"role_id\" class=\"form-control selectpicker\" data-width=\"100%\">
                                    <option value=''>Select</option>
                                    <option value=\"admin\">Admin</option>
                                    <option value=\"teacher\">Teacher</option>
                                    <option value=\"accountant\">Accountant</option>
                                    <option value=\"employee\">Employees</option>
                                    <option value=\"guardian\">Parent</option>
                                    <option value=\"student\">Student</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"col-md-12 hidden\" id=\"individual_select_list_{$route}\">
                            <div class=\"form-group mb-1\">
                                <label>Name <span class=\"required\">*</span></label>
                                <select data-selectors=\"{$route}\" data-route=\"{$route}\" name=\"recipients[]\" multiple class=\"form-control selectpicker\" data-width=\"100%\">
                                    <option value=''>Select</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"col-md-12 hidden\" id=\"class_select_{$route}\">
                            <div class=\"form-group mb-1\">
                                <label>Class <span class=\"required\">*</span></label>
                                <select data-selectors=\"{$route}\" data-route=\"{$route}\" name=\"class_id\" class=\"form-control selectpicker\" data-width=\"100%\">
                                    <option value=''>Select</option>";
                                    foreach($class_array_list as $class) {
                                        $html[$route] .= "<option value='{$class->item_id}'>{$class->name}</option>";
                                    }
                        $html[$route] .= "</select>
                            </div>
                        </div>
                    </div>
                    <div class=\"row mt-3\">
                        <div class=\"col-md-12 mb-xs\">
                            <div class=\"form-group\">
                                <div class=\"checkbox-replace\">
                                    <label class=\"i-checks cursor\"><input data-route=\"{$route}\" type=\"checkbox\" name=\"send_later\"><i></i> Send Later</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=\"row\">
                        <div class=\"col-md-8\">
                            <div class=\"form-group\">
                                <label class=\"control-label\">Schedule Date <span class=\"required\">*</span></label>
                                <div class=\"input-group\">
                                    <input data-route=\"{$route}\" disabled data-maxdate=\"".date("Y-m-d", strtotime("+3 months"))."\" type=\"text\" class=\"form-control datepicker\" name=\"schedule_date\">
                                </div>
                                <span class=\"error\"></span>
                            </div>
                        </div>
                        <div class=\"col-md-4\">
                            <div class=\"form-group\">
                                <label class=\"control-label\">Schedule Time <span class=\"required\">*</span></label>
                                <div class=\"input-group\">
                                    <input data-route=\"{$route}\" type=\"time\" disabled name=\"schedule_time\" class=\"form-control\" value=\"\">
                                    <span class=\"error\"></span>
                                </div>
                                <span class=\"error\"></span>
                            </div>
                        </div>
                    </div>
                    <div class=\"row mt-3\">
                        <div class=\"col-md-6\" align=\"left\"></div>
                        <input type=\"hidden\" data-route=\"{$route}\" readonly name=\"type\" value=\"{$route}\">
                        <div class=\"col-md-6\" align=\"right\">
                            <button class=\"btn btn-outline-success\" type=\"submit\"><i class=\"fa fa-mail-bulk\"></i> Send Message</button>
                        </div>
                    </div>
                </form>
            </div>";

        }
        $html["templates_array"] = $templates_array;
        $html["users_array_list"] = $users_array_list;
        $html["class_array_list"] = $class_array_list;

        return $html;
    }

    /**
     * Generate the Bus Form
     * 
     * @return string
     */
    public function bus_form($data = null) {

        /** Set parameters for the data to attach */
        $file_params = (object) [
            "accept" => implode(",", [".pdf",".jpg",".png",".jpeg",".pjpeg"]),
            "module" => "buses_attachment_".(!empty($data) ? $data->item_id : "root"),
            "userData" => $this->thisUser,
            "ismultiple" => true,
            "item_id" => $data->item_id ?? null
        ];

        $html = '<div data-backdrop="static" data-keyboard="false" class="modal fade" id="busModal">
            <form method="POST" autocomplete="Off" action="'.$this->baseUrl.'api/buses/save" class="ajax-data-form" id="ajax-data-form-content">
                <div class="modal-dialog modal-lg" role="document">
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
                                        <label for="name">Brand Name <span class="required">*</span></label>
                                        <input type="text" maxlength="100" name="brand" id="brand" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="registration_number">Registration Number <span class="required">*</span></label>
                                        <input type="text" maxlength="14" name="registration_number" class="form-control text-uppercase">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="color">Amount Purchased</label>
                                        <input type="number" min="1" maxlength="20" name="amount" id="amount" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Color">Color</label>
                                        <input type="color" maxlength="20" name="color" id="color" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="year_of_purchase">Date / Year Purchased</label>
                                        <input type="text" maxlength="20" name="year_of_purchase" class="form-control datepicker">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="insurance_company">Insurance Company <span class="required">*</span></label>
                                        <input type="text" maxlength="255" name="insurance_company" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="annual_premium">Annual Premium <span class="required">*</span></label>
                                        <input type="number" name="annual_premium" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="insurance_date">Date of Insurance <span class="required">*</span></label>
                                        <input type="text" maxlength="12" name="insurance_date" class="form-control datepicker">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea placeholder="" maxlength="255" name="description" id="description" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        '.$this->form_attachment_placeholder($file_params, "").'
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer p-0">
                                <input type="hidden" name="bus_id">
                                <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                                <button data-form_id="ajax-data-form-content" type="button-submit" class="btn btn-primary">Save Record</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>';

        return $html;

    }


    /**
     * Display the submitted assignment by the student
     * It displays content submitted and also any uploaded file attachment
     * 
     * @param Object $data
     * 
     * @return String
     */
    public function assignment_review_detail($data) {

        // begin the html content
        $html = "";

        // set the content to display
        if(!empty($data["content"])) {
            // is graded
            $graded = $data["content"]->graded ? "Graded" : "Pending";
            $graded_color = $data["content"]->graded ? "success" : "primary";

            // set the content
            $html .= "
            <table class='table table-sm table-bordered' width='100%'>
                <tr>
                    <td colspan='2' align='center'><span class='font-25'>{$data["content"]->assignment_title}</span></td>
                </tr>
                <tr>
                    <td class='text-uppercase text-small'>
                        <div><strong>STUDENT NAME:</strong></div>
                        <div><strong class='text-primary font-20'>{$data["content"]->student_name}</strong></div>
                    </td>
                    <td class='text-uppercase text-small'>
                        <div><strong>REG. ID:</strong></div>
                        <div><strong class='text-primary font-20'>{$data["content"]->unique_id}</strong></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Date Submitted:</strong> {$data["content"]->date_submitted}
                    </td>
                    <td>
                        <span class='badge badge-{$graded_color}'>{$graded}</span>
                    </td>
                </tr>
            </table>";
            $html .= "<div class='col-lg-12 p-0'>{$data["content"]->content}</div>";
            $html .= !empty($data["file"]) ? '<div class="col-lg-12 border-bottom mb-3 pb-3"></div>' : null;
        }

        // set the file content
        $html .= !empty($data["file"]) ? $data["file"] : null;

        // set the close modal
        $html .= '<div class="col-lg-12 border-top mt-3 pt-3" align="center">
                <button type="reset" class="btn btn-light" data-dismiss="modal">Close Modal</button>
            </div>';

        return $html;

    }

    /**
     * Knowledgebase Form
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function knowledgebase_form($data = null) {
        // global variables
        global $defaultUser;

        // file upload parameter
        $file_params = (object) [
            "module" => "knowledgebase",
            "userData" => $defaultUser,
            "accept" => ".png,.jpg,jpeg",
            "is_deletable" => true,
            "attachments_list" => $data->attachments_list ?? []
        ];

        $html_content = '
        <form autocomplete="Off" class="ajax-data-form" action="'.$this->baseUrl.'api/support/'.(isset($data->subject) ? "knowledgebase_update" : "knowledgebase_add").'" method="POST" id="ajax-data-form-content">
            <div class="row">
                <div class="col-md-8">
                    <label for="">Article Title <span class="required">*</span></label>
                    <input type="text" placeholder="Enter Title" value="'.($data->subject ?? null).'" class="form-control" name="subject">
                </div>
                <div class="col-md-4">
                    <label>Section:</label>
                    <select name="section" data-width="100%" class="selectpicker form-control">
                        <option value="">Please Select Section of App</option>';
                        foreach($this->support_sections as $value => $section) {
                            $html_content .= "<option ".(!empty($data->section) && ($data->section == $value) ? "selected" : null)." value='{$value}'>{$section}</option>";
                        }
                    $html_content .= '
                    </select>
                </div>
                <div class="col-md-12 mt-3 mb-3">
                    <label for="">Full Content <span class="required">*</span></label>
                    '.$this->textarea_editor($data->content ?? null, "faketext", "ajax-form-content", "content").'
                </div>
                <div class="col-md-12 p-0">
                    <div class="form-group">
                        '.$this->form_attachment_placeholder($file_params).'
                    </div>
                </div>
                <div class="col-md-12 text-right">
                    <input type="hidden" value="'.($data->item_id ?? null).'" name="item_id" readonly>
                    <button type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
                </div>
            </div>
        </form>';

        return $html_content;
    }

    /**
     * This method is used for display the form however fill in with the answers submitted by the user
     * 
     * @param \stdClass $formData        These are the form fields
     * @param \stdClass $form_answers       These are the answers provided by the user
     * @param Bool  $is_editable            This is to specify whether the user wants to 
     * 
     * @return String 
     */
    public function preload_form_data($formData, $form_answers, $is_editable = true, $form_name = null) {

        $readonly = !$is_editable ? "disabled=\"disabled\"" : null;
        $form_answers = (array) $form_answers;

        /** Form content display */
        $html_content = "<form action='{$this->baseUrl}api/{$form_name}/update' method='POST' id='ajax-data-form-content' class='ajax-data-form'>
            <div class='row'>";
        $html_content .= form_loader();

        // loop throught the list of form fields
        foreach($formData->form_fields as $key => $eachField) {

            // required field
            $required = $eachField->required == "yes" ? "required" : "";

            // if prefill is true
            $value =  "";
            
            // if preload is not null and the value is set
            $value = isset($form_answers[$key]) ? $form_answers[$key] : null;

            // form content
            $html_content .= "<div class=\"col-lg-{$eachField->_width}\">";
            $html_content .= "<div class=\"form-group\">";
            $html_content .= "<label for=\"field[{$key}]\">{$eachField->label} ".(($required) ? " &nbsp;<span class='required'>*</span>" : null)."</label>";

            // if the type is an input, date or email
            if(in_array($eachField->type, ["input", "date", "email"])) {
                
                // set some additional variables
                $type = (in_array($eachField->type, ["input", "date"])) ? "text" : $eachField->type;
                $class = $eachField->type == "date" ? "datepicker" : "";

                $html_content .= "<input {$readonly} value=\"{$value}\" type=\"{$type}\" {$required} class=\"form-control {$class}\" name=\"field[{$key}]\" id=\"field[{$key}]\">";

            }

            // if a textarea was parsed
            if(in_array($eachField->type, ["textarea"])) {
                $html_content .= "<textarea {$readonly} {$required} rows=\"7\" class=\"form-control\" name=\"field[{$key}]\" id=\"field[{$key}]\">{$value}</textarea>";
            }

            // if a select field was parsed
            if(in_array($eachField->type, ["select"])) {
                // show this list
                $html_content .= "<select {$readonly} class=\"form-control selectpicker\" {$required} name=\"field[{$key}]\" id=\"field[{$key}]\" data-width=\"100%\">";

                // show the values for the select fields
                $html_content .= "<option value=\"null\">Please Select:</option>";
                
                // loop through the list
                foreach($eachField->select as $eachItem) {
                    // append to the list
                    $html_content .= "<option ".(($value == $eachItem->value) ? "selected" : "null")." value=\"{$eachItem->value}\">{$eachItem->label}</option>";
                }
                
                $html_content .= "</select>";
            }
            $html_content .= "
                    </div>
                </div>";

        }

        /** Additional html for the form */
        $html_content .= "</div></form>";

        return $html_content;

    }

    /**
     * This method will be enlisting the form
     * It displays both during the updating of the form by Admins or Completing it by the Client.
     * 
     * @param stdClass $params
     * 
     * @param Bool $is_editable         When set to false, the form will be presented in the form that users can fill in
     *                                  A true value will make it editable by an admin
     * 
     * @param Array $data               This holds data that is need for prefilling the form before displaying to the user
     * @param Bool  $preview            This when set to true will make all the fields readonly
     * 
     * @return Array
     */
    public function form_enlisting($params, $is_editable = true, $data = [], $preview = false) {
        
        // assign the data set
        $thisRowId = 0;
        $thisSelectRow = 0;
        $html_content = "";
        $name = isset($params->form_name) ? $params->form_name : null;
        $readonly = $preview ? "disabled=\"disabled\"" : null;
        $form_data = isset($params->fields) ? $params->fields : (object)[];
        $autoComplete = $params->autocomplete ?? "On";

        // loop throught the list of form fields
        foreach($form_data as $key => $eachField) {

            // if the form is editable
            if($is_editable) {

                // append to the list
                $html_content .= "<div class='form-group' data-row=\"{$key}\">
                    <div class=\"input-group\">
                        <div class=\"input-group-prepend\">
                        <div class=\"input-group-text\">".strtoupper($eachField->type)." LABEL &nbsp;</div>
                        </div>
                        <input data-name=\"form[type][{$key}]\" hidden type=\"hidden\" value=\"{$eachField->type}\">
                        <input type=\"text\" value=\"{$eachField->label}\" input-type=\"{$eachField->type}\" data-row=\"{$key}\" required data-name=\"form[label][{$key}]\" id=\"input_label_{$key}\" class=\"form-control\">
                        <div class=\"input-group-prepend\">
                            <select class=\"form-control\" data-role=\"required\" data-row=\"{$key}\" style=\"width:130px\" data-name=\"form[required][{$key}]\">
                                <option ".(($eachField->required == "no") ? "selected" : null)." value=\"no\">Not Required</option>
                                <option ".(($eachField->required == "yes") ? "selected" : null)." value=\"yes\">Required</option>
                            </select>";

                        // show this section for only select fields
                        if(in_array($eachField->type, ["select"])) {
                            // show this list
                            $html_content .= "<select class=\"form-control\" data-role=\"values\" data-row=\"{$key}\" style=\"width:230px\" data-name=\"form[values][{$key}]\">";

                            // if the data is empty
                            if(empty($eachField->select)) {
                                $html_content .= "<option value=\"null\">No values added:</option>";
                            } else {
                                $html_content .= "<option value=\"null\">Values Added:</option>";
                                // loop through the list
                                foreach($eachField->select as $eachItem) {
                                    // append to the list
                                    $html_content .= "<option value=\"{$eachItem->value}\">{$eachItem->label}</option>";
                                }
                            }
                            $html_content .= "</select>";
                            // increment the select rows count
                            $thisSelectRow++;
                        }

                        // if the field type is in the list
                        if(in_array($eachField->type, ["input", "date", "email", "textarea"])) {
                            $html_content .= "
                                <select class=\"form-control\" data-role=\"_width\" data-row=\"{$key}\" style=\"width:230px\" data-name=\"form[_width][{$key}]\">
                                    <option ".(($eachField->_width == "6") ? "selected" : null)." value=\"6\">Field Width: - 50%</option>
                                    <option ".(($eachField->_width == "3") ? "selected" : null)." value=\"3\">Half Width (25%):</option>
                                    <option ".(($eachField->_width == "6") ? "selected" : null)." value=\"6\">Half Width (50%):</option>
                                    <option ".(($eachField->_width == "9") ? "selected" : null)." value=\"9\">Half Width (75%):</option>
                                    <option ".(($eachField->_width == "12") ? "selected" : null)." value=\"12\">Full Width (100%):</option>

                                </select>";
                        }

                        // show the buttons
                        $html_content .= "<div class=\"input-group-text\" style=\"background:none;padding:0px;border:0px; margin-left: 10px;\">";
                        // show the update button for select fields
                        if(in_array($eachField->type, ["select"])) {
                            $html_content .= "<button type=\"button\" data-row=\"{$key}\" class=\"btn update-row btn-outline-success mr-1 btn-sm\"><i class=\"fa fa-edit\"></i></button> &nbsp; ";
                        }                    
                        $html_content .= "<button type=\"button\" data-row=\"{$key}\" class=\"btn remove-row btn-outline-danger btn-sm\"><i class=\"fa fa-times\"></i></button>
                            </div>";
                        

                        $html_content .= "</div>
                    </div>  
                </div>";
                $thisRowId = $key;
                
            }

            // if the form is not editable
            elseif(!$is_editable) {
                
                // required field
                $required = $eachField->required == "yes" ? "required" : "";

                // form content
                $html_content .= "<div class=\"col-lg-12 p-0\">";
                $html_content .= "<div class=\"form-group\">";
                $html_content .= "<label for=\"{$name}field[{$key}]\">{$eachField->label} ".(($required) ? " &nbsp;<span class='required'>*</span>" : null)."</label>";
                
                // init the value
                $value = "";

                // if prefill is true
                $value =  $data[$key] ?? $value;

                // if the type is an input, date or email
                if(in_array($eachField->type, ["input", "date", "email"])) {
                    
                    // set some additional variables
                    $type = (in_array($eachField->type, ["input", "date"])) ? "text" : $eachField->type;
                    $class = $eachField->type == "date" ? "datepicker" : "";

                    $html_content .= "<input autocomplete=\"{$autoComplete}\" {$readonly} value=\"{$value}\" type=\"{$type}\" {$required} class=\"form-control {$class}\" name=\"{$name}field[{$key}]\" id=\"{$name}field[{$key}]\">";

                }

                // if a textarea was parsed
                if(in_array($eachField->type, ["textarea"])) {
                    $html_content .= "<textarea autocomplete=\"{$autoComplete}\" {$readonly} {$required} rows=\"5\" class=\"form-control\" name=\"{$name}field[{$key}]\" id=\"{$name}field[{$key}]\">{$value}</textarea>";
                }

                // if a select field was parsed
                if(in_array($eachField->type, ["select"])) {
                    // show this list
                    $html_content .= "<select autocomplete=\"{$autoComplete}\" {$readonly} class=\"form-control selectpicker\" {$required} name=\"{$name}field[{$key}]\" id=\"{$name}field[{$key}]\" data-width=\"100%\">";

                    // show the values for the select fields
                    $html_content .= "<option value=\"null\">Please Select:</option>";
                    
                    // loop through the list
                    foreach($eachField->select as $eachItem) {
                        // append to the list
                        $html_content .= "<option ".(($value == $eachItem->value) ? "selected" : "null")." value=\"{$eachItem->value}\">{$eachItem->label}</option>";
                    }
                    
                    $html_content .= "</select>";
                }

                $html_content .= "</div>";
                $html_content .= "</div>";

            }

        }

        // return the results
        return [
            "form" => $html_content,
            "thisRowId" => $thisRowId+1,
            "thisSelectRow" => $thisSelectRow+1
        ];

    }

    /**
     * This method will be show the form in an api call
     * It displays both during the updating of the form by Admins or Completing it by the Client.
     * 
     * @param stdClass $params
     * 
     * @param Bool $is_editable         When set to false, the form will be presented in the form that users can fill in
     *                                  A true value will make it editable by an admin
     * 
     * @param Array $data               This holds data that is need for prefilling the form before displaying to the user
     * @param Bool  $preview            This when set to true will make all the fields readonly
     * 
     * @return Array
     */
    public function form_enlisting_api($params, $is_editable = true, $data = [], $preview = false) {
        
        // assign the data set
        $thisRowId = 0;
        $thisSelectRow = 0;
        $html_content = "";
        $name = "mysgh_app_form";
        $readonly = $preview ? "disabled=\"disabled\"" : null;
        $form_data = isset($params->form->fields) ? $params->form->fields : (object)[];
        $autoComplete = $params->autocomplete ?? "On";

        // set additional variables
        $description = htmlspecialchars_decode($params->description);
        $requirements = htmlspecialchars_decode($params->requirements);

        // populate the form
        $html_content .= "<form class='mysgh_app_form' method='POST' id='mysgh_app_form' action='javascript:mysgh_app_form_submit()'>";
        $html_content .= "
        <div class='mysgh_app_form-column_span-12 text-center border-bottom border-primary'>
            <h2>".strtoupper($params->name)."</h2>
        </div>";
        $html_content .= "<div class='mysgh_app_form-column_span-12 text-center border-bottom border-primary pb'>{$description}</div>";
        $html_content .= "<div id='requirements_container' class='mysgh_app_form-column_span-12 border-bottom border-primary'>{$requirements}</div>";
        $html_content .= "<div class='mysgh_app_form-column_row' id='mysgh_app_form-form_content'>";
        // loop throught the list of form fields
        foreach($form_data as $key => $eachField) {

            // required field
            $required = $eachField->required == "yes" ? "required" : "";

            // form content
            $html_content .= "<div class=\"mysgh_app_form-column_span-{$eachField->_width} p-0\">";
            $html_content .= "<div class=\"form-group\">";
            $html_content .= "<label for=\"{$name}_field[{$key}]\">{$eachField->label} ".(($required) ? " &nbsp;<span class='required'>*</span>" : null)."</label>";
            
            // init the value
            $value = "";

            // if prefill is true
            $value =  $data[$key] ?? $value;

            // if the type is an input, date or email
            if(in_array($eachField->type, ["input", "date", "email"])) {
                
                // set some additional variables
                $type = (in_array($eachField->type, ["date"])) ? "date" : $eachField->type;
                $class = $eachField->type == "date" ? "datepicker" : "";

                $html_content .= "<input autocomplete=\"{$autoComplete}\" {$readonly} value=\"{$value}\" type=\"{$type}\" {$required} class=\"mysgh_app_form-input {$class}\" name=\"{$name}_field[{$key}]\" id=\"{$name}_field[{$key}]\">";

            }

            // if a textarea was parsed
            if(in_array($eachField->type, ["textarea"])) {
                $html_content .= "<textarea autocomplete=\"{$autoComplete}\" {$readonly} {$required} rows=\"5\" class=\"mysgh_app_form-textarea\" name=\"{$name}_field[{$key}]\" id=\"{$name}_field[{$key}]\">{$value}</textarea>";
            }

            // if a select field was parsed
            if(in_array($eachField->type, ["select"])) {
                // show this list
                $html_content .= "<select autocomplete=\"{$autoComplete}\" {$readonly} class=\"mysgh_app_form-input selectpicker\" {$required} name=\"{$name}_field[{$key}]\" id=\"{$name}_field[{$key}]\" data-width=\"100%\">";

                // show the values for the select fields
                $html_content .= "<option value=\"null\">Please Select:</option>";
                
                // loop through the list
                foreach($eachField->select as $eachItem) {
                    // append to the list
                    $html_content .= "<option ".(($value == $eachItem->value) ? "selected" : "null")." value=\"{$eachItem->value}\">{$eachItem->label}</option>";
                }
                
                $html_content .= "</select>";
            }

            $html_content .= "</div>";
            $html_content .= "</div>";

        }
        $html_content .= "<div class='mysgh_app_form-column_span-12 pb' align='center'><em>{$params->form->form_footnote}</em></div>";
        $html_content .= "
        <div class='mysgh_app_form-column_span-12 form-group' align='center'>
            <button id='mysgh_app_form-submit_botton' class='submit-button'>Submit Form</button>
        </div>";
        $html_content .= "
        <div id='mysgh_app_form-submit_loader' class='mysgh_app_form-results-container'></div>";
        $html_content .= "</div>";
        $html_content .= "</form>";
        $html_content .= "
        <style>
            .mysgh_app_form {
                max-width: 1040px!important;
                margin: auto auto!important;
                padding: 15px!important;
                font-family: 'Nunito', 'Segoe UI', arial!important;
            }
            .mysgh_app_form .pb1 {
                padding-bottom: 5px!important;
            }
            .mysgh_app_form .pb {
                padding-bottom: 10px!important;
            }
            .mysgh_app_form .text-center {
                text-align: center!important;
            }
            .mysgh_app_form .submit-button {
                box-shadow: 0 2px 6px #acb5f6;
                background-color: #64c909;
                border-color: #64c909;
                color: #fff;
                font-size: 14px;
                padding: 10px;
                border-radius: 7px;
                cursor: pointer;
            }            
            .mysgh_app_form .submit-button:hover {
                background-color: #3f7c08!important;
            }
            .mysgh_app_form .submit-button[disabled] {
                box-shadow: 0 2px 6px #cccccc;
                background-color: #cccccc;
                border-color: #a0a1ab;
            }
            .mysgh_app_form .submit-button[disabled]:hover {
                box-shadow: 0 2px 6px #ababab!important;
                background-color: #ababab!important;
                cursor: not-allowed;
                border-color: #a0a1ab!important;
            }
            .mysgh_app_form-results-container {
                color: #fff;
                width: 100%;
                font-size: 15px;
                border-radius: 7px;
            }
            .mysgh_app_form .error-container {
                background: #dc3545;
                border-radius: 7px;
                font-size: 15px;
                padding: 10px;
            }
            .mysgh_app_form .success-container {
                background: #428109;
                text-align: center;
                border-radius: 7px;
                color: #fff;
                font-size: 25px;
                padding: 20px;
                width: 100%;
            }
            .mysgh_app_form .border-bottom {
                border-bottom: solid 3px #ccc;
                margin-bottom: 15px!important;
            }
            .mysgh_app_form .form-group {
                margin-bottom: 15px!important;
                padding-right: 20px;
                padding-left: 20px;
            }
            .mysgh_app_form .border-primary {
                border-color: #6777ef;
            }
            .mysgh_app_form .mysgh_app_form-column_row {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
            }
            .mysgh_app_form .mysgh_app_form-column_span-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .mysgh_app_form .mysgh_app_form-column_span-9 {
                flex: 0 0 75%;
                max-width: 75%;
            }
            .mysgh_app_form .mysgh_app_form-column_span-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            .mysgh_app_form .mysgh_app_form-column_span-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
            .mysgh_app_form .mysgh_app_form-column_span-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }
            @media only screen and (max-width: 770px) {
                .mysgh_app_form .mysgh_app_form-column_span-6 {
                    flex: 0 0 45%;
                    max-width: 45%;
                    margin-right: 15px;
                }
            }
            @media only screen and (max-width: 425px) {
                .mysgh_app_form .mysgh_app_form-column_span-9,
                .mysgh_app_form .mysgh_app_form-column_span-3 {
                    flex: 0 0 100%;
                    max-width: 100%;
                }
            }
            @media only screen and (max-width: 420px) {
                .mysgh_app_form .mysgh_app_form-column_span-9,
                .mysgh_app_form .mysgh_app_form-column_span-6,
                .mysgh_app_form .mysgh_app_form-column_span-4,
                .mysgh_app_form .mysgh_app_form-column_span-3 {
                    flex: 0 0 100%!important;
                    max-width: 100%!important;
                }
                .mysgh_app_form .form-group {
                    margin-bottom: 15px!important;
                    padding-right: 10px;
                    padding-left: 0px;
                }
            }
            .mysgh_app_form .required {
                color: #ff4000!important;
            }
            .mysgh_app_form .control-label, .mysgh_app_form>label {
                color: #34395e!important;
                font-weight: bold!important;
                font-size: 12px!important;
                letter-spacing: .5px!important;
            }
            .mysgh_app_form label {
                display: inline-block!important;
                font-weight: bold!important;
                font-size: 13px!important;
                margin-bottom: 0.5rem!important;
            }

            .mysgh_app_form .mysgh_app_form-input, .mysgh_app_form .mysgh_app_form-textarea {
                height: 20px;
                display: block!important;
                width: 100%!important;
                padding: 10px!important;
                font-weight: 400!important;
                line-height: 1.5!important;
                background-clip: padding-box!important;
                border: 1px solid #ced4da!important;
                transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out!important;
                border-radius: 5px!important;
            }
            .mysgh_app_form .mysgh_app_form-input {
                height: 20px;
            }
            .mysgh_app_form .mysgh_app_form-textarea {
                height: 70px;
                width: 100%!important;
            }
            .mysgh_app_form .mysgh_app_form-input:focus, 
            .mysgh_app_form .mysgh_app_form-textarea:focus {
                background-color: #fefeff!important;
                border-color: #95a0f4!important;
                box-shadow: none !important!important;
                outline: none!important;
            }
        </style>";
        // return the results
        return $html_content;

    }

    /**
     * Daily Student Report Form
     * 
     * @return String
     */
    public function daily_report_log_form($params) {

        // global variables
        global $defaultUser, $isTutor;

        // variables 
        $class_id = null;

        // class filter
        $classFilter = $isTutor ? "AND item_id IN ".$this->inList($defaultUser->class_ids) : null;

        // get the classes list
        $classes_list = $this->pushQuery("name, id, item_id", "classes", "client_id='{$params->clientId}' AND status='1' {$classFilter} LIMIT 100");

        $html = '
        <form method="POST" autocomplete="Off" action="'.$this->baseUrl.'api/incidents/report" class="ajax-data-form" id="ajax-data-form-content">
            <div class="row">

                <div class="col-md-5">
                    <div class="form-group">
                        <label>Select Student Class <span class="required">*</span></label>
                        <select data-width="100%" class="selectpicker form-control" name="class_id" id="class_id">";
                            <option value="">Select Class</option>';
                            // if the content is an array
                            if(is_array($classes_list)) {
                                // loop through the results list
                                foreach($classes_list as $class) {
                                    $html .= "
                                    <option ".($class_id == $class->item_id ? "selected" : null)." value='{$class->item_id}'>
                                        ".strtoupper($class->name)."
                                    </option>";
                                }
                            }
                        $html .= '
                        </select>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="form-group">
                        <label>Select Student</label>
                        <select data-width="100%" class="selectpicker form-control" name="student_id" id="student_id">";
                            <option value="">Select Student</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Daily Report <span class="required">*</span></label>
                        '.$this->textarea_editor(null, "faketext", "ajax-form-content").'
                    </div>
                </div>
                <div class="col-md-12 form-group text-center text-danger">
                    <em>Logging report for <strong>'.date("l, jS F Y").'</strong></em>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <div>
                    <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
                <div>
                    <button data-form_id="ajax-data-form-content" type="button-submit" class="btn btn-success">Save Record</button>
                </div>
            </div>
        </form>';

        return $html;
    }

    /**
     * Staff Leave Application Form
     * 
     * @return String
     */
    public function leave_form($data = null) {

        $method = !empty($data) ? "PUT" : "POST";
        $reason = !empty($data) ? $data->reason : null;

        global $isAdmin, $defaultUser;

        $html_content = "
        <form method='{$method}' autocomplete='Off' action='{$this->baseUrl}api/leave/apply' class='ajax-data-form' id='ajax-data-form-content'>
            <div class='row'>";
                if($isAdmin) {
                    $html_content .= "
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label>Name <span class='required'>*</span></label>
                            <select class='selectpicker' data-width='100%' name='user_id' id='user_id'>
                                <option value=''>Select Staff</option>";


                        // loop through the staff list
                        foreach($this->pushQuery("item_id, name", "users", "status='1' AND user_type IN ('admin', 'teacher', 'accountant', 'employee') AND client_id='{$defaultUser->client_id}' LIMIT 100") as $type) {
                            $html_content .= "<option value='{$type->item_id}'>".strtoupper($type->name)."</option>";
                        }


                    $html_content .= "
                            </select>
                        </div>
                    </div>";
                } else {
                    $html_content .= "<input type='hidden' readonly name='user_id' value='{$defaultUser->user_id}'>";
                }
                $html_content .= "
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Leave Date <span class='required'>*</span></label>
                        <input type='text' name='leave_from_to' id='leave_from_to' value='' class='form-control daterange'>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Leave Type <span class='required'>*</span></label>
                        <select class='selectpicker' data-width='100%' name='type_id' id='type_id'>
                            <option value=''>Select Type</option>";

                        // loop through the leave types
                        foreach($this->pushQuery("*", "leave_types", "status='1'") as $type) {
                            $html_content .= "<option value='{$type->id}'>{$type->name}</option>";
                        }

                        $html_content .= "
                        </select>
                    </div>
                </div>
                <div class='col-md-8'>
                    <div class='form-group'>
                        <label>Reason <span class='required'>*</span></label>
                        <input type='hidden' hidden id='trix-editor-input' value='{$reason}'>
                        <trix-editor name=\"faketext\" data-predefined_name=\"reason\" input='trix-editor-input' class=\"trix-slim-scroll\" id=\"ajax-form-content\"></trix-editor>
                    </div>
                </div>";
                if($isAdmin) {
                    $html_content .= "
                    <div class='col-md-4'>
                        <div class='form-group'>
                            <label>Leave Status</label>
                            <select class='selectpicker' data-width='100%' name='status' id='status'>
                                <option value='Pending'>Pending</option>
                                <option value='Approved'>Approved</option>
                                <option value='Disapproved'>Disapproved</option>
                            </select>
                        </div>
                    </div>";
                }
                $html_content .= "
                <div class='col-md-".($isAdmin ? 12 : 8)."'>
                    <div class='d-flex justify-content-between'>
                        <div class='mb-2'>
                            <button type='button' class='btn btn-sm btn-danger' onclick='return load(\"leave\");'>Cancel</button>
                        </div>
                        <div class='mb-2'>
                            <button class='btn btn-sm btn-success' type='button-submit'><i class='fa fa-save'></i> Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>";

        return $html_content;

    }

    /**
     * Admission Enquiry Form
     * 
     * @return String
     */
    public function enquiry_form($data = null) {

        $reason = !empty($data) ? $data->reason : null;

        global $isAdmin, $defaultUser;

        $html_content = "
        <form method='POST' autocomplete='Off' action='{$this->baseUrl}api/frontoffice/log' class='ajax-data-form' id='ajax-data-form-content'>
            <div class='row'>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Name <span class='required'>*</span></label>
                        <input type='text' name='data[fullname]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Phone Number <span class='required'>*</span></label>
                        <input type='text' name='data[phone_number]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Email Address</label>
                        <input type='email' name='data[email]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Address</label>
                        <textarea class='form-control' name='data[address]'></textarea>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Description & Note</label>
                        <textarea class='form-control' name='data[description]'></textarea>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Source <span class='required'>*</span></label>
                        <select class='selectpicker' data-width='100%' name='data[source]'>
                            <option value=''>Select Source</option>";
                            // loop through the source
                            foreach(['Parent', 'Online', 'Radio'] as $source) {
                                $html_content .= "<option value='{$source}'>{$source}</option>";
                            }
                        $html_content .= "
                        </select>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Date <span class='required'>*</span></label>
                        <input type='text' name='data[date]' value='' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Followup Date</label>
                        <input type='text' data-maxdate='".date("Y-m-d", strtotime("+3 month"))."' name='data[followup]' value='' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='form-group'>
                        <label>Assigned To</label>
                        <input type='text' name='data[assigned]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='d-flex justify-content-between'>
                        <input type='hidden' name='section' value='admission_enquiry' readonly>
                        <div class='mb-2'>
                            <button type='button' class='btn btn-sm btn-danger' onclick='return load(\"office_enquiry\");'>Cancel</button>
                        </div>
                        <div class='mb-2'>
                            <button class='btn btn-sm btn-success' type='button-submit'><i class='fa fa-save'></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>";

        return $html_content;

    }

    /**
     * Visitors Form
     * 
     * @return String
     */
    public function visitors_form() {

        $html_content = "
        <form method='POST' autocomplete='Off' action='{$this->baseUrl}api/frontoffice/log' class='ajax-data-form' id='ajax-data-form-content'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Purpose <span class='required'>*</span></label>
                        <select class='selectpicker' data-width='100%' name='data[purpose]'>
                            <option value=''>Select Purpose</option>";
                            // loop through the purpose
                            foreach($this->office_purpose as $purpose) {
                                $html_content .= "<option value='{$purpose}'>{$purpose}</option>";
                            }
                        $html_content .= "
                        </select>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Name <span class='required'>*</span></label>
                        <input type='text' name='data[fullname]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Phone Number <span class='required'>*</span></label>
                        <input type='text' name='data[phone_number]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Email Address</label>
                        <input type='email' name='data[email]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Number of Person</label>
                        <input type='number' name='data[number_of_person]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Date <span class='required'>*</span></label>
                        <input type='text' name='data[date]' value='' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Time In</label>
                        <input type='time' name='data[time_in]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-6'>
                    <div class='form-group'>
                        <label>Time Out</label>
                        <input type='time' name='data[time_out]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Note</label>
                        <textarea class='form-control' name='data[note]'></textarea>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='d-flex justify-content-between'>
                        <input type='hidden' name='section' value='visitor' readonly>
                        <div class='mb-2'>
                            <button type='button' class='btn btn-sm btn-danger' onclick='return load(\"office_visitors\");'>Cancel</button>
                        </div>
                        <div class='mb-2'>
                            <button class='btn btn-sm btn-success' type='button-submit'><i class='fa fa-save'></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>";

        return $html_content;

    }

    /**
     * Phonecall Log Form
     * 
     * @return String
     */
    public function phonecall_log_form() {

        $html_content = "
        <form method='POST' autocomplete='Off' action='{$this->baseUrl}api/frontoffice/log' class='ajax-data-form' id='ajax-data-form-content'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Call Type <span class='required'>*</span></label>
                        <select class='selectpicker' data-width='100%' name='data[type]'>
                            <option value=''>Select Call Type</option>";
                            // loop through the type
                            foreach(['Incoming', 'Outgoing'] as $type) {
                                $html_content .= "<option value='{$type}'>{$type}</option>";
                            }
                        $html_content .= "
                        </select>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Name <span class='required'>*</span></label>
                        <input type='text' name='data[fullname]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Phone Number <span class='required'>*</span></label>
                        <input type='text' name='data[phone_number]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Date <span class='required'>*</span></label>
                        <input type='text' name='data[date]' value='' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Call Duration</label>
                        <input type='text' name='data[call_duration]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Next Followup Date</label>
                        <input type='text' data-maxdate='".date("Y-m-d", strtotime("+3 month"))."' name='data[followup]' value='' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Note</label>
                        <textarea class='form-control' name='data[note]'></textarea>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='d-flex justify-content-between'>
                        <input type='hidden' name='section' value='phonecall' readonly>
                        <div class='mb-2'>
                            <button type='button' class='btn btn-sm btn-danger' onclick='return load(\"office_visitors\");'>Cancel</button>
                        </div>
                        <div class='mb-2'>
                            <button class='btn btn-sm btn-success' type='button-submit'><i class='fa fa-save'></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>";

        return $html_content;

    }

    /**
     * Postal Form Form
     * 
     * @return String
     */
    public function postal_form($type = 'dispatch') {

        $types = [
            'dispatch' => [
                'to' => 'To',
                'to_key' => 'to',
                'from' => 'From',
                'from_key' => 'from'
            ],
            'receive' => [
                'to' => 'From',
                'to_key' => 'from',
                'from' => 'To',
                'from_key' => 'to'
            ]
        ];

        /** Set parameters for the data to attach */
        $form_params = (object) [
            "module" => "postal_{$type}",
            "accept" => implode(",", [".doc",".docx",".pdf",".jpg",".png",".jpeg",".pjpeg",".xls",".xlsx",".rtf", ".ppt", ".pptx"]),
            "userData" => $this->thisUser,
            "item_id" => null
        ];

        $html_content = "
        <form method='POST' autocomplete='Off' action='{$this->baseUrl}api/frontoffice/log' class='ajax-data-form' id='ajax-data-form-content'>
            <div class='row'>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>{$types[$type]['to']} <span class='required'>*</span></label>
                        <input type='text' name='data[{$types[$type]['to_key']}]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Reference Number</label>
                        <input type='text' name='data[reference]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Address</label>
                        <textarea class='form-control' name='data[address]'></textarea>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>{$types[$type]['from']} <span class='required'>*</span></label>
                        <input type='text' name='data[{$types[$type]['from_key']}]' value='' class='form-control'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Date</label>
                        <input type='text' name='data[date]' value='' class='form-control datepicker'>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                        <label>Note</label>
                        <textarea class='form-control' name='data[note]'></textarea>
                    </div>
                </div>
                <div class='col-lg-12'>
                    <div class='form-group text-center mb-1'>
                        <div class='row'>
                            ".$this->form_attachment_placeholder($form_params)."
                        </div>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='d-flex justify-content-between'>
                        <input type='hidden' name='section' value='postal_{$type}' readonly>
                        <div class='mb-2'>
                            <button type='button' class='btn btn-sm btn-danger' onclick='return load(\"office_postal{$type}\");'>Cancel</button>
                        </div>
                        <div class='mb-2'>
                            <button class='btn btn-sm btn-success' type='button-submit'><i class='fa fa-save'></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>";

        return $html_content;

    }

}
