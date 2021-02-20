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
            "module" => "assignments",
            "userData" => $params->userData,
            "item_id" => $params->data->item_id ?? null
        ];

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
            $type = isset($params->data->assignment_type) ? $params->data->assignment_type : null;

            // run this section for only file attachment assignments
            if($type === "file_attachment") {
                // set a new parameter for the items
                $files_param = (object) [
                    "userData" => $params->userData,
                    "label" => "list",
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

        $html_content = "<div class='col-lg-8'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Title <span class='required'>*</span></label>";
        $html_content .= "<input {$disabled} class='form-control' value='".($params->data->assignment_title ?? null)."' name='assignment_title' id='assignment_title'>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-6'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Class <span class='required'>*</span></label>";
        $html_content .= "<select {$disabled} data-width='100%' class='selectpicker form-control' name='class_id' id='class_id'>";
        $html_content .= "<option value='null'>Select Class</option>";
        foreach($this->pushQuery("name, id, item_id", "classes", "client_id='{$params->clientId}' AND status='1'") as $class) {
            $html_content .= "<option ".($class_id == $class->item_id ? "selected" : null)." value='{$class->item_id}'>{$class->name}</option>";
        }
        $html_content .= "</select>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-6'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Course <span class='required'>*</span></label>";
        $html_content .= "<select {$disabled} data-width='100%' class='selectpicker form-control' name='course_id' id='course_id'>";
        $html_content .= "<option value='null'>Select Course</option>";
        
        // display the courses list
        if(isset($ass_data)) {
            foreach($ass_data["courses_list"] as $course) {
                $html_content .= "<option ".($course_id == $course->item_id ? "selected" : null)." value='{$course->item_id}'>{$course->name}</option>";
            }
        }

        $html_content .= "</select>";
        $html_content .= "</div>";
        $html_content .= "</div>";
        $html_content .= "<div class='col-md-6'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Submission Date <span class='required'>*</span></label>";
        $html_content .= "<input {$disabled} type='date' class='form-control' value='".($params->data->due_date ?? date("Y-m-d", strtotime("next week")))."' name='date_due' id='date_due'>";
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
        
        $html_content .= "<div class='form-group text-center mb-1'>{$preloaded_attachments}</div>";

        $html_content .= "<div class='col-md-6'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Grade <span class='required'>*</span></label>";
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

        $html_content .= "<div class='col-lg-12 ".($assigned_to == "selected_students" ? "" : "hidden")."' id='assign_to_students_list'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Students List</label>";
        $html_content .= "<select {$disabled} data-width='100%' multiple class='selectpicker form-control' name='assigned_to_list' id='assigned_to_list'>";
        $html_content .= "<option value=''>Select Students</option>";

        // display the courses list
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
        $type = isset($params->data->assignment_type) ? $params->data->assignment_type : null;

        $html_content = "
        <style>
        #create_assignment trix-editor {
            min-height: 150px;
            max-height: 150px;
        }
        </style>
        <form ".(!$disabled ? "class='ajax-data-form' id='ajax-data-form-content' action='{$this->baseUrl}api/assignments/".(!$params->data ? "add" : "update")."'": "")." method='post'>";
        $html_content .= "<div class='row' id='create_assignment'>";
        
        $html_content .= "<div class='col-lg-4'>";
        $html_content .= "<div class='form-group'>";
        $html_content .= "<label>Select Question Type <span class='required'>*</span></label>";
        $html_content .= "<select {$disabled} data-width='100%' class='selectpicker form-control' name='assignment_type' id='assignment_type'>";
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
                <div class=\"row border-top\">
                    <div class=\"col-md-6 mt-4 text-left\">
                        <button type=\"reset\" class=\"btn btn-outline-danger btn-sm\" class=\"close\" data-dismiss=\"modal\">Cancel</button>
                    </div>
                    <div class=\"col-md-6 mt-4 text-right\">
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
                        <select '.$disabled.' class="form-control selectpicker" name="answer_type" id="answer_type">';

                        foreach($answer_types as $key => $value) {
                            $html_content .= '<option '.(isset($data->answer_type) && ($data->answer_type == $key) ? "selected" : null).' value="'.$key.'">'.$value.'</option>';
                        }

                        $html_content .= '</select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" style="max-width:300px">
                        <label class="custom-label"><strong>Difficulty Level</strong></label>
                        <select '.$disabled.' class="form-control selectpicker" name="difficulty" id="difficulty">';

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
                            <td width="13%"><strong>'.$value.'</strong> '.($i < 3 ? " <span class='required'>*</span>" : "").'</td>
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
    public function list_attachments($attachment_list = null, $user_id = null, $list_class = "col-lg-4 col-md-6", $is_deletable = false, $show_view = false, $show_controls = true) {

        // variables
        $list_class = empty($list_class) ? "col-lg-4 col-md-6" : $list_class;
        
        // images mimetypes for creating thumbnails
        $image_mime = ["jpg", "jpeg", "png", "gif"];
        $docs_mime = ["pdf", "doc", "docx", "txt", "rtf", "jpg", "jpeg", "png", "gif"];
        $video_mime = ["mp4", "mpeg", "movie", "webm", "mov", "mpg", "mpeg", "qt", "flv"];

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

                } else {

                    // if the file exists
                    if(is_file($eachFile->path) && file_exists($eachFile->path)) {

                        // is image check
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
                            <div><span class=\"text text-{$eachFile->color}\"><i class=\"{$eachFile->favicon} fa-6x\"></i></span></div>
                        </div>";
                        $caption = "
                        <div class=\"file_caption text-left\">
                            <span {$preview_link}><strong>{$eachFile->name}</strong></span> <span class=\"text-muted tx-11\">({$eachFile->size})</span>
                            <br><strong class=\"mt-2\">{$eachFile->uploaded_by}</strong>
                        </div>";

                        $view_option = $show_view ? "<a href=\"#\" onclick=\"return loadPage('{$this->baseUrl}{$show_view}/{$record_id}_{$eachFile->unique_id}');\" title=\"Click to view details of file\" class=\"btn btn-sm btn-primary\"><i class=\"fa fa-eye\"></i></a>" : "";
                        $image_desc = "";
                        $delete_btn = "";

                        // show the view of the item
                        if(isset($eachFile->is_editable)) {
                            $view_option .= "&nbsp;<a href=\"#\" onclick=\"return loadPage('{$this->baseUrl}{$eachFile->is_editable}/{$record_id}');\" title=\"Click to edit the material\" class=\"btn btn-sm btn-warning\"><i class=\"fa fa-edit\"></i></a>";
                        }

                        // display this if the object is deletable.
                        if($is_deletable) {
                            $delete_btn = "&nbsp;<a href=\"#\" onclick=\"return delete_existing_file_attachment('{$record_id}_{$eachFile->unique_id}');\" style=\"padding:5px\" class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
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
                                    <a class=\"image-popup\" href=\"{$this->baseUrl}{$eachFile->path}\" title=\"{$eachFile->name} ({$eachFile->size}) on {$eachFile->datetime}\">
                                        <i class=\"fa fa-search\"></i>
                                    </a>
                                </div>";
                        } else if($isVideo) {
                            // get the file name
                            $filename = "{$eachFile->path}";
                            $padding = "style='padding:0px'";
                            // set the video file
                            $thumbnail = "<video ".($show_controls ? "controls='true'" : null)." style='display: block; cursor:pointer; width:100%;' src='{$this->baseUrl}{$filename}#t=1'></video>";
                        }
                        

                        // append to the list
                        $files_list .= "<div data-file_container='{$record_id}_{$eachFile->unique_id}' class=\"{$list_class} attachment-container text-center p-3\">";
                        $files_list .= $isImage ? "<div class=\"gallery-item\">" : null;
                            $files_list .= "
                                <div class=\"col-lg-12 p-0 {$the_class} border\" {$padding} data-attachment_item='{$record_id}_{$eachFile->unique_id}'>
                                    <span style=\"display:none\" class=\"file-options\" data-attachment_options='{$record_id}_{$eachFile->unique_id}'>
                                        {$view_option}
                                        <a title=\"Click to Download\" target=\"_blank\" class=\"btn btn-sm btn-success\" style=\"padding:;\" href=\"{$this->baseUrl}download?file={$file_to_download}\">
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
     * @param \stdClass $params
     * 
     * @return String
     */
    public function form_attachment_placeholder(stdClass $params = null) {
        
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
        <div class=\"col-lg-12\">
            <div class='post-attachment'>
                <div class='row'>
                    <div class=\"col-lg-12\" id=\"".($params->module ?? null)."\">
                        <div class=\"file_attachment_url\" data-url=\"{$this->baseUrl}api/files/attachments\"></div>
                    </div>
                    <div class=\"".(isset($params->class) ? $params->class : "col-md-12")." text-left\">
                        <div class='d-flex justify-content-start'>";
                        if(!isset($params->no_title)) {
                            $html_content .= "<label>Attach a Document <small class='text-danger'>(Maximum size <strong>{$this->max_attachment_size}MB</strong>)</small></label><br>";
                        }
                    $html_content .= "
                            <div class=\"ml-3\">
                                <input ".(isset($params->accept) ? "accept='{$params->accept}'" : null)." class='form-control cursor attachment_file_upload' data-form_item_id=\"".($params->item_id ?? "temp_attachment")."\" data-form_module=\"".($params->module ?? null)."\" type=\"file\" name=\"attachment_file_upload\" id=\"attachment_file_upload\">
                            </div>
                            <div class=\"upload-document-loader hidden\"><span class=\"float-right\">Uploading <i class=\"fa fa-spin fa-spinner\"></i></span></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class=\"col-md-12\">
                <div class=\"file-preview slim-scroll\" preview-id=\"".($params->module ?? null)."\">{$fresh_attachments}</div>
                <div class='form-group text-center mb-1'>{$preloaded_attachments}</div>
            </div>";
            $html_content .= !isset($params->no_footer) ? "<div class=\"col-lg-12 mb-3 border-bottom mt-3\"></div>" : null;
        $html_content .= "</div>";

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
    public function modify_guardian_ward($data) {

        $array = [
            "guardian" => [
                "title" => "Student Name",
                "type" => "student",
                "key" => "unique_id",
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
        $html_content .= "<input type='text' placeholder='Search {$array[$data->user_type]["title"]} name' name='user_name_search' id='user_name_search' class='form-control'>";
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
     * @return String
     */
    public function student_form($clientId, $baseUrl, $userData = null) {

        $isData = !empty($userData) && isset($userData->user_id) ? true : false;

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
        <form class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
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
                                $response .= "<option ".($isData && ($each->name == $userData->blood_group) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                        <input type="hidden" id="user_type" name="user_type" value="'.(!$isData ? "student" : null).'">
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="religion">Religion <span class="required">*</span></label>
                        <input type="text" value="'.($userData->religion ?? null).'" name="religion" id="religion" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea type="text" name="description" id="description" class="form-control">'.($userData->description ?? null).'</textarea>
                    </div>
                </div>
            </div>

            <div class="row mb-3 pb-4">
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
                        <label for="department_id">Department <span class="required">*</span></label>
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
                            <option value="null">Select Student Class</option>';
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
                            <option value="null">Select Student Section</option>';
                            foreach($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->section) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 mt-3"><h5>PREVIOUS SCHOOL DETAILS</h5></div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="previous_school">Previous School</label>
                        <input type="text" value="'.($userData->previous_school ?? null).'" name="previous_school" id="previous_school" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label for="previous_school_qualification">Qualification</label>
                        <input type="text" value="'.($userData->previous_school_qualification ?? null).'" name="previous_school_qualification" id="previous_school_qualification" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="previous_school_remarks">Remarks</label>
                        <textarea type="text" name="previous_school_remarks" id="previous_school_remarks" class="form-control">'.($userData->previous_school_remarks ?? null).'</textarea>
                    </div>
                </div>
            </div>
            <input type="hidden" id="user_id" value="'.($userData->user_id ?? null).'" name="user_id">
            <div class="row">
                <div class="col-lg-12 text-right">
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

        $response = '
        <form class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>BIO INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Guardian Image</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                </div>
                <div class="col-lg-'.(!empty($userData) ? 4 : 4 ).' col-md-6">
                    <div class="form-group">
                        <label for="unique_id">Guardian ID (optional)</label>
                        <input type="text" readonly value="'.($userData->unique_id ?? "").'" name="unique_id" id="unique_id" class="form-control">
                        <input type="text" readonly value="'.($userData->user_id ?? "").'" hidden name="user_id" id="user_id" class="form-control">
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
                            <option value="null">Select Country</option>';
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
                            <option value="null">Select Blood Group</option>';
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
                <div class="col-lg-4 col-md-4">
                    <div class="form-group">
                        <label for="relationship">Relationship</label>
                        <select data-width="100%" name="relationship" id="relationship" class="form-control selectpicker">
                            <option value="null">Select Relation</option>';
                            foreach($this->pushQuery("id, name", "guardian_relation", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".(isset($userData->relationship) && $userData->relationship === $each->name ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                $response .= '</select>
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
        <form class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/departments/'.( $isData ? "update" : "add").'" method="POST">
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
        <form class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/sections/'.( $isData ? "update" : "add").'" method="POST">
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
        <form class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/classes/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12"><h5>CLASS INFORMATION</h5></div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-group">
                        <label for="class_code">Class Code (optional)</label>
                        <input type="text" value="'.($itemData->class_code ?? null).'" name="class_code" id="class_code" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="form-group">
                        <label for="class_size">Class Size (optional)</label>
                        <input type="text" value="'.($itemData->class_size ?? null).'" name="class_size" id="class_size" class="form-control text-uppercase">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="name">Class Name<span class="required">*</span></label>
                        <input type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
                    </div>
                </div>
                <div class="'.($isData ? "col-lg-6 col-md-6" : "col-lg-4 col-md-4").'">
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
                <div class="'.($isData ? "col-lg-6 col-md-6" : "col-lg-4 col-md-4").'">
                    <div class="form-group">
                        <label for="class_teacher">Class Teacher</label>
                        <select data-width="100%" name="class_teacher" id="class_teacher" class="form-control selectpicker">
                            <option value="">Select Class Teacher</option>';
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
                            <option value="">Select Class Assistant</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('student') AND status='1' AND client_id='{$clientId}' ".($isData ? " AND class_id='{$itemData->id}'" : "")."") as $each) {
                                $response .= "<option ".($isData && ($each->item_id == $itemData->class_assistant) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-6">
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

        $response = '
        <form class="ajax-data-form" id="ajax-data-form-content" action="'.$baseUrl.'api/courses/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>COURSE DETAILS</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="course_code">Course Code <span class="required">*</span></label>
                        <input '.$isAdmin.' type="text" maxlength="12" value="'.($itemData->course_code ?? null).'" name="course_code" id="course_code" class="form-control text-uppercase">
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
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="name">Course Title <span class="required">*</span></label>
                        <input type="text" value="'.($itemData->name ?? null).'" name="name" id="name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="class_id">Classes that offer this course <span class="required">*</span></label>
                        <select multiple '.$isAdmin.' data-width="100%" name="class_id[]" id="class_id[]" class="form-control selectpicker">
                            <option value="">Select Class</option>';
                            foreach($this->pushQuery("id, name, item_id", "classes", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && in_array($each->item_id, $itemData->class_ids) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="form-group">
                        <label for="course_tutor">Course Tutors</label>
                        <select multiple data-width="100%" '.$isAdmin.' name="course_tutor[]" id="course_tutor[]" class="form-control selectpicker">
                            <option value="">Select Course Tutor</option>';
                            foreach($this->pushQuery("item_id, name, unique_id", "users", "user_type IN ('teacher') AND user_status='Active' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && in_array($each->item_id, $itemData->course_tutor_ids) ? "selected" : null)." value=\"{$each->item_id}\">{$each->name} ({$each->unique_id})</option>";                            
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

        $guardian = "";

        $response = '
        <form class="ajax-data-form" id="ajax-data-form-content" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
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
                                $response .= "<option ".($isData && ($each->name == $userData->blood_group) ? "selected" : null)." value=\"{$each->name}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="position">Position / Role <span class="required">*</span></label>
                        <input type="text" value="'.($userData->position ?? null).'" name="position" id="position" class="form-control">
                        <input type="hidden" hidden value="'.($userData->user_id ?? null).'" name="user_id" id="user_id" class="form-control">
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
                            <option value="null">Select Section</option>';
                            foreach($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && ($each->id == $userData->section) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                            }
                        $response .= '</select>
                    </div>
                </div>
                <div class="col-lg-12 col-md-6 '.($isData && $userData->user_type !== "teacher" ? "hidden" : "").'" id="course_ids_container">
                    <div class="form-group">
                        <label for="courses_ids">Courses</label>
                        <select multiple data-width="100%" name="courses_ids[]" id="courses_ids" class="form-control selectpicker">
                            <option value="">Select Course</option>';
                            foreach($this->pushQuery("id, name", "courses", "status='1' AND client_id='{$clientId}'") as $each) {
                                $response .= "<option ".($isData && in_array($each->id, $userData->course_ids) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
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
                                $response .= "<option ".($isData && ($key == $userData->user_status) ? "selected" : null)." value=\"{$key}\">{$value}</option>";                            
                            }
                        $response .= '</select>
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
     * Event Forms
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function event_form($data = null) {

        $disabled = isset($data->state) && in_array($data->state, ["Held", "Cancelled"]) ? "disabled='disabled'" : null;

        $html_content = (!$disabled ? '<form enctype="multipart/form-data" class="ajax-data-form" action="'.$this->baseUrl.'api/events/'.(isset($data->item_id) ? "update" : "add").'" method="POST" id="ajax-data-form-content">': '').'
            <div id="modalBody2" class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>Title <span class="required">*</span></label>
                            <input type="text" '.$disabled.' value="'.($data->title ?? null).'" name="title" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type<span class="required">*</span></label>
                            <select '.$disabled.' name="type" id="type" class="form-control '.(!isset($data->item_id) ? "selectpicker" : "").'">
                                <option value="null">Select</option>';
                                if(isset($data->event_types)) {
                                    foreach($data->event_types as $key => $value) {
                                        $html_content .= "<option ".(isset($data->item_id) && ($data->event_type == $value->item_id) ? "selected='selected'" : "")." data-row_id='{$value->item_id}' value='{$value->item_id}'>{$value->name}</option>";
                                    }
                                }
                            $html_content .= '
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Audience<span class="required">*</span></label>
                            <select '.$disabled.' name="audience" id="audience" class="form-control '.(!isset($data->item_id) ? "selectpicker" : "").'">
                                <option value="null">Select</option>';
                                foreach($this->event_audience as $key => $value) {
                                    $html_content .= "<option ".(isset($data->item_id) && ($data->audience == $key) ? "selected='selected'" : "")." value='{$key}'>{$value}</option>";
                                }
                            $html_content .= '</select>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                                <trix-editor '.$disabled.' name="faketext" input="trix-editor-input" class="trix-slim-scroll" id="ajax-form-content"></trix-editor>
                            ' : "<div>{$data->description}</div>").'
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="form-group">
                            <label>Event Image</label>
                            <input type="hidden" hidden name="event_id" class="form-control" value="'.($data->item_id ?? null).'">
                            <input '.$disabled.' type="file" name="event_image" class="form-control" id="event_image">
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
                        <select '.$disabled.' class="form-control selectpicker" id="status" name="status">
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
        <form enctype="multipart/form-data" class="ajax-data-form" action="'.$this->baseUrl.'api/library/'.(isset($data->title) ? "update_book" : "add_book").'" method="POST" id="ajax-data-form-content">    
            <div class="row">
                <div class="col-lg-5 col-md-5">
                    <div class="form-group">
                        <label for="book_image">Cover Image</label>
                        <input type="file" name="book_image" id="book_image" class="form-control">
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
                        <label for="">BOOK CATEGORY <span class="required">*</span></label>
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
        <form class="ajax-data-form" action="'.$this->baseUrl.'api/library/'.(isset($data->name) ? "update_category" : "add_category").'" method="POST" id="ajax-data-form-content">    
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
        <form class="ajax-data-form" action="'.$this->baseUrl.'api/rooms/'.(isset($data->name) ? "update_classroom" : "add_classroom").'" method="POST" id="ajax-data-form-content">
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
            // set the html_content to display
            $html_content .= '
                <div class="form-group">
                    <label>Book Category</label>
                    <select name="category_id" data-width="100%" id="category_id" class="form-control selectpicker">
                        <option value="">Please Select</option>';
                        foreach($this->pushQuery("id, name", "books_type", "status='1' AND client_id='{$data->clientId}'") as $each) {
                            $html_content .= "<option ".(isset($data->category_id) && ($each->id == $data->category_id) ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";                            
                        }
                    $html_content .= '
                    </select>
                </div>
                <div class="form-group">
                    <label>Book Title</label>
                    <select name="book_id" data-width="100%" id="book_id" class="form-control selectpicker">
                        <option value="">Please Select</option>
                    </select>
                </div>';
        }

        // if the request includes the issue_form
        if(isset($data->issue_form)) {
            // set the html_content to display
            $html_content .= '
                <div class="form-group">
                    <label>User Role</label>
                    <select name="user_role" id="user_role" class="form-control selectpicker">
                        <option value="">Please Select</option>';
                        foreach($this->all_user_roles_list as $key => $value) {
                            $html_content .= "<option ".(isset($data->user_role) && ($key == $data->user_role) ? "selected" : null)." value=\"{$key}\">{$value}</option>";                            
                        }
                    $html_content .= '
                    </select>
                </div>
                <div class="form-group">
                    <label>Fullname <span class="required">*</span></label>
                    <select name="user_id" id="user_id" class="form-control selectpicker">
                        <option value="">Please Select</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Return Date <span class="required">*</span></label>
                    <input type="text" name="return_date" id="return_date" value="'.($data->return_date ?? date("Y-m-d", strtotime("+1 week"))).'" class="form-control datepicker">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-6">
                            <label>Overdue Fine</label>
                            <input type="number" value="'.($data->overdue_rate ?? "").'" name="overdue_rate" id="overdue_rate" class="form-control">
                        </div>
                        <div class="col-lg-6">
                            <label>Apply Overdue</label>
                            <select name="overdue_apply" id="overdue_apply" class="form-control selectpicker">
                                <option value="entire">Entire Order</option>
                                <option value="single">Each Book</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <input type="hidden" value="'.($data->issue_id ?? null).'" name="issue_id" readonly>
                    <button onclick="return save_Issue_Request(\''.($data->issue_id ?? null).'\',\'issue\');" type="button-submit" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
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
                    <input type="text" value="'.($data->return_date ?? date("Y-m-d", strtotime("+1 week"))).'" name="return_date" id="return_date" class="form-control datepicker">
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
        <form class="ajax-data-form" id="'.$form_id.'" enctype="multipart/form-data" action="'.$baseUrl.'api/users/'.( $isData ? "update" : "add").'" method="POST">
            <div class="row mb-4 border-bottom pb-3">
                <div class="col-lg-12">
                    <h5>BIO INFORMATION</h5>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" accept=".png,.jpeg,.jpg" name="image" id="image" class="form-control">
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
                        <input type="text" value="'.($userData->date_of_birth ?? null).'" name="date_of_birth" id="date_of_birth" class="form-control _datepicker">
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
                        <input type="hidden" hidden id="trix-editor-input" value="'.($userData->description ?? null).'">
                        <trix-editor name="faketext" input="trix-editor-input" class="trix-slim-scroll" id="ajax-form-content"></trix-editor>
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

        // get the client data
        $client_data = !empty($clientId) ? $this->client_data($clientId) : (object)[];

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

        $general = '
        <form class="ajax-data-form" action="'.$this->baseUrl.'api/account/'.(isset($client_data->client_name) ? "update" : "add").'" method="POST" id="'.$form_id.'">
        <div class="row">
            <div class="col-lg-12"><h5>GENERAL SETTINGS</h5></div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label for="logo">Logo</label>
                    <input type="file" name="logo" id="logo" accept=".png,.jpeg,.jpg" class="form-control">
                </div>
            </div>
            <div class="col-lg-9 col-md-12">
                <div class="form-group">
                    <label for="name">School Name</label>
                    <input type="text" value="'.($client_data->client_name ?? null).'" name="general[name]" class="form-control">
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
            <div class="col-lg-12"><h5>ACADEMIC CALENDAR</h5></div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="academic_year">Academic Year</label>
                    <select data-width="100%" name="general[academics][academic_year]" class="form-control selectpicker">
                        <option value="">Select Academic Year</option>';
                            foreach($this->pushQuery("id, year_group", "academic_years", "1") as $each) {
                                $general .= "<option ".(($client_data && $each->year_group === $prefs->academics->academic_year) ? "selected" : null)." value=\"{$each->year_group}\">{$each->year_group}</option>";                            
                            }
                        $general .= '</select>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="academic_term">Academic Term</label>
                    <select data-width="100%" name="general[academics][academic_term]" class="form-control selectpicker">
                        <option value="">Select Academic Term</option>';
                            foreach($this->pushQuery("id, name, description", "academic_terms","1") as $each) {
                                $general .= "<option ".(($client_data && $each->name === $prefs->academics->academic_term) ? "selected" : null)." value=\"{$each->name}\">{$each->description}</option>";                            
                            }
                        $general .= '</select>
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_starts">Academic Term Start</label>
                    <input type="text" value="'.($prefs->academics->term_starts ?? null).'" name="general[academics][term_starts]" id="term_starts" class="form-control _datepicker">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <label for="term_ends">Academic Term Ends</label>
                    <input type="text" value="'.($prefs->academics->term_ends ?? null).'" name="general[academics][term_ends]" id="term_ends" class="form-control _datepicker">
                </div>
            </div>
            <div class="col-lg-12"><h5>LABELS</h5></div>';
            foreach($labels as $label) {
                $ilabel = "{$label["key"]}_label";
            $general .= '
                <div class="col-lg-2 col-md-3">
                    <div class="form-group">
                        <label for="'.$label["key"].'_label">'.$label["label"].' Label</label>
                        <input type="text" value="'.($prefs->labels->{$ilabel} ?? null).'" maxlength="3" name="general[labels]['.$label["key"].'_label]" id="'.$label["key"].'_label" class="form-control text-uppercase">
                    </div>
                </div>';
            }
        $general .= '
            <div class="col-lg-12"><h5>&nbsp;</h5></div>
            <div class="col-lg-4">
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
            </div>
            <div class="col-md-12 text-right">
                <button type="button-submit" data-form_id="'.$form_id.'" class="btn btn-success"><i class="fa fa-save"></i> Save Record</button>
            </div>
        </form></div>';
        $forms["general"] = $general;

        // create a new account object
        $accountObj = load_class("account", "controllers");
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
                <form method="post" action="'.$this->baseUrl.'api/account/import" class="csvDataImportForm" enctype="multipart/form-data">
                    <div class="row">
                        <div id="dropify-space" class="col-md-8  mt-5 text-center m-auto border pt-4 border-white">
                            <div class="form-content-loader" style="display: none;">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <h2>Upload a CSV to import <strong>'.ucwords($key).' data</strong></h2>
                            <button href="#" class="btn btn-outline-primary" data-download_button="'.$key.'" onclick="return download_sample_csv(\''.$key.'\')"><i class="fa fa-download"></i> Download Sample CSV File</button>
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
                                <option value="null">Please Select</option>';
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

        return $forms;
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

        $bank = '
        <form class="ajax-data-form" action="'.$this->baseUrl.'api/payroll/paymentdetails" method="POST" id="ajax-data-form-content">
            <div class="row">
                <div class="col-lg-12">
                    <h5>BANK DETAILS</h5>
                </div>
                <div class="col-lg-6">
                    <label for="account_name">Account Holder Name</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"></i></span>
                        </div>
                        <input type="text" value="'.($data->account_name ?? null).'" maxlength="255" name="account_name" id="account_name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6">
                    <label for="account_number">Account Number</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-paperclip"></i></span>
                        </div>
                        <input type="text" maxlength="24" value="'.($data->account_number ?? null).'" name="account_number" id="account_number" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6">
                    <label for="bank_name">Bank Name</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-tablet"></i></span>
                        </div>
                        <input type="text" value="'.($data->bank_name ?? null).'" maxlength="40" name="bank_name" id="bank_name" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6">
                    <label for="bank_branch">Bank Branch</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-home"></i></span>
                        </div>
                        <input type="text" value="'.($data->bank_branch ?? null).'" maxlength="40" name="bank_branch" id="bank_branch" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6">
                    <label for="ssnit_number">SSNIT Number</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-tablet"></i></span>
                        </div>
                        <input type="text" value="'.($data->ssnit_number ?? null).'" maxlength="40" name="ssnit_number" id="ssnit_number" class="form-control">
                    </div>
                </div>
                <div class="col-lg-6">
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
                                <option value="null">Please Select</option>';
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
                            <option value="null">Please Select</option>';
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
                                <option value="null">Please Select</option>';
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
                            <option value="null">Please Select</option>';
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
                <div class="col-lg-12"><h5>GROSS SALARY</h5></div>
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
                                            <select name="payment_mode" id="payment_mode" class="form-control selectpicker2">
                                                <option value="null">Select</option>
                                                <option value="Bank">Bank</option>
                                                <option value="Cash">Cash</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 text-right font-weight-bold">Status:</div>
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <select name="payment_status" id="payment_status" class="form-control selectpicker2">
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
            "accept" => ".mp4,.mpg,.mpeg,.flv",
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
            "clientId" => $params->clientId,
            "columns" => "id, item_id, name"
        ];
        $classes_list = load_class("classes", "controllers")->list($classes_param)["data"];

        $html_content = '
        <form class="ajax-data-form" action="'.$this->baseUrl.'api/resources/'.(isset($params->data) ? "update_4elearning" : "upload_4elearning").'" method="POST" id="ajax-data-form-content">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-xl-4 col-md-4 col-12 form-group">
                            <label>Select Class <span class="required">*</span></label>
                            <select class="form-control selectpicker" name="class_id">
                                <option value="null">Please Select Class</option>';
                                foreach($classes_list as $each) {
                                    $html_content .= "<option ".($class === $each->item_id ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
                                }
                                $html_content .= '
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4 col-12 form-group">
                            <label>Select Course <span class="required">*</span></label>
                            <select class="form-control selectpicker" name="course_id">
                                <option value="null">Please Select Course</option>';
                                foreach($courses_list as $each) {
                                    $html_content .= "<option ".($course === $each->item_id ? "selected" : null)." value=\"{$each->id}\">{$each->name}</option>";
                                }
                                $html_content .= '
                            </select>
                        </div>
                        <div class="col-xl-4 col-md-4 col-12 form-group">
                            <label>Select Course Unit</label>
                            <select class="form-control selectpicker" name="unit_id">
                                <option value="null">Please Select Unit</option>';
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
                                <trix-editor style="height:150px;" name="faketext" input="trix-editor-input" class="trix-slim-scroll" id="ajax-form-content"></trix-editor>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group text-center mb-1">
                                <div class="row">'.$this->form_attachment_placeholder($form_params).'</div>
                            </div>
                        </div>  
                        <div class="col-md-12 text-center mb-4">'.$preloaded_attachments.'</div>                          
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        <label>Allow / Disallow Comments</label>
                        <select class="form-control selectpicker" name="allow_comment">
                            <option '.($comment === "allow" ? "selected" : null).' value="allow">Allow Comments</option>
                            <option '.($comment === "disallow" ? "selected" : null).' value="disallow">Disallow Comments</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Material State</label>
                        <select class="form-control selectpicker" name="state">
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

}