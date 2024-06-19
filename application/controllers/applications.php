<?php 

class Applications extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load and Form the Application Form
     * 
     * @param stdClass $params
     * 
     * @return mixed
     */
    public function load_form(stdClass $params) {

        // load the application form
        $param = (object) [
            "application_id" => $params->form_id,
            "clientId" => $params->clientId,
            "state" => "Enrolled",
            "limit" => 1
        ];
        $form = $this->forms($param);

        // return error if no data was found
        if(empty($form["data"])) {
            return ["code" => 203, "data" => "Sorry! An invalid form id was parsed."];
        }

        // get the first key of the result returned
        $data = $form["data"][0];
        $data->autocomplete = "Off";

        if(isset($params->only_load)) {
            return $data;
        }

        // set the form content
        $form_content = load_class("forms", "controllers")->form_enlisting_api($data);

        return $form_content;
    }

    /**
     * Process the Form Submitted to Apply for Admission
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function apply(stdClass $params) {

        try {

            // check the last time for applying
            if(!$this->check_time("users_applications")) {
                return ["code" => 203, "data" => "Sorry! You cannot submit another application within 2 minutes."];
            }

            // load the application form
            $params->form_id = $params->mysgh_app_form_id;
            $params->only_load = true;

            // get the data
            $data = $this->load_form($params);

            // get the form fields
            $the_form = (array) $data->form->fields;

            // get bugs
            $bugs = [];
            $today = strtotime(date("Y-m-d"));

            // clean the parsed user data
            $parsed_data = array_map("xss_clean", $params->mysgh_app_form_field);

            // process the form
            foreach($parsed_data as $key => $value) {
                if(!isset($the_form[$key])) {
                    $bugs[] = "An invalid data field was supplied.";
                }
                else {
                    if(($the_form[$key]->required == "yes") && empty($value)) {
                        $bugs[] = "{$the_form[$key]->label} is required.";
                    } elseif(($the_form[$key]->required == "yes") && ($the_form[$key]->type == "email")) {
                        if(!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $bugs[] = "{$the_form[$key]->label} must be a valid email.";
                        }
                    } elseif(($the_form[$key]->type == "date") && !empty($value)) {
                        if(!$this->validDate($value)) {
                            $bugs[] = "{$the_form[$key]->label} must be a valid date.";
                        } elseif(strtotime($value) >= $today) {
                            $bugs[] = "{$the_form[$key]->label} must not be either today or above today's date.";
                        }
                    }
                }
            }

            // if the bugs is not empty
            if(!empty($bugs)) {

                // bug list 
                $bugs_list = "";

                // loop through the bugs
                foreach($bugs as $key => $bug) {
                    $bugs_list .= "<div class='pb1'>".($key + 1).". {$bug}</div>";
                }

                // return the error list
                return ["code" => 203, "data" => $bugs_list];
            }

            // generate a new application id
            $item_id = "APP".$this->append_zeros(($this->lastRowId("users_applications") + 1), 5);

            // insert the record
            $stmt = $this->db->prepare("INSERT INTO users_applications 
                SET client_id = ?, form_id = ?, form_fields = ?, form_answers = ?, ipaddress = ?, item_id = ?");
            $stmt->execute([
                $params->clientId, $params->mysgh_app_form_id, json_encode($the_form), 
                json_encode($parsed_data), ip_address(), $item_id
            ]);

            // notify the user of the success
            return ["code" => 200, "data" => "Congrats! Your application was successfully submitted. One of our personnel will contact you for further discussion.<br>Your Application ID is: <strong>{$item_id}</strong>"];

        } catch(PDOException $e) {}

    }

    /**
     * List Application Forms
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function forms(stdClass $params) {

        // global variable
        global $defaultUser, $defaultClientData, $defaultAcademics;
        
        $query = "";
        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $filtering = (!empty($params->application_id)) ? " AND a.item_id='{$params->application_id}'" : null;
        $filtering .= (!empty($params->state)) ? " AND a.state='{$params->state}'" : null;
        $filtering .= (isset($params->q)) ? " AND a.name LIKE %{$params->q}%" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT b.description FROM files_attachment b WHERE b.resource='application_form' AND b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                	(SELECT COUNT(*) FROM users_applications b WHERE b.form_id = a.item_id LIMIT 2000) AS applications_count
                FROM users_application_forms a
                WHERE a.client_id='{$params->clientId}' {$filtering} AND a.status = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            // init data variable
            $data = [];

            // loop through the results list
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
              	// convert the form to an array
              	$result->form = json_decode($result->form);
                $result->attachment = json_decode($result->attachment);

                // push to the array
                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {} 

    }

    /**
     * List Applications
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        // global variable
        global $defaultUser, $defaultClientData, $defaultAcademics;
        
        $query = "";
        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $filtering = (!empty($params->application_id)) ? " AND a.item_id='{$params->application_id}'" : null;
        $filtering .= (!empty($params->state)) ? " AND a.state='{$params->state}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*, b.name, b.description, b.requirements, b.year_enrolled
                FROM users_applications a
                LEFT JOIN users_application_forms b ON b.item_id = a.form_id
                WHERE a.client_id='{$params->clientId}' {$filtering} ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            // init data variable
            $data = [];

            // loop through the results list
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // convert the form to an array
                $result->form_fields = json_decode($result->form_fields);
                $result->form_answers = json_decode($result->form_answers);

                // push to the array
                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Add Application Form
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        /** Confirm the user has the required permissions */
        global $accessObject;

        /** confirm that the form is not empty  */
        if(!isset($params->form)) {
            return ["data" => "Sorry! The application form cannot be empty", "code" => 203];
        }

        /** Convert the form data into an array json */
        $params->form = json_decode($params->form, true);
        
        if(!is_array($params->form)) {
            return ["data" => "Sorry! The application form must be an array", "code" => 203];
        }

        /** Loop through the form and rearrange the data */
        $formArray = $params->form;
        
        /** Validate the user variables parsed */
        $params->_item_id = random_string("alnum", RANDOM_STRING);

        /** Clean the text */
        $params->description = htmlspecialchars(custom_clean(htmlspecialchars_decode($params->description)));
        $params->requirements = htmlspecialchars(custom_clean(htmlspecialchars_decode($params->requirements)));
        $params->form_footnote = htmlspecialchars(custom_clean(htmlspecialchars_decode($params->form_footnote)));
        $params->allow_attachment = isset($params->allow_attachment) ? $params->allow_attachment : "yes";

        /** Policy form data */
        $formData = [
            "fields" => $formArray,
            "form_footnote" => $params->form_footnote,
            "allow_attachment" => $params->allow_attachment
        ];

        // append the attachments
        $filesObj = load_class("files", "controllers");
        $attachments = $filesObj->prep_attachments("application_forms", $params->userData->user_id, $params->_item_id);

        /** Insert the record */
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO users_application_forms SET created_by = ?, date_created = now(), client_id = ?
                ".(!empty($params->_item_id) ? ",item_id='{$params->_item_id}'" : null)."
                ".(!empty($params->description) ? ",description='{$params->description}'" : null)."
                ".(!empty($params->form_title) ? ",name='{$params->form_title}'" : null)."
                ".(!empty($formData) ? ",form='".json_encode($formData)."'" : null)."
                ".(!empty($params->status) ? ",state='{$params->status}'" : null)."
                ".(!empty($params->requirements) ? ",requirements='{$params->requirements}'" : null)."
                ".(isset($params->year_enrolled) ? ",year_enrolled='{$params->year_enrolled}'" : null)."
            ");
            $stmt->execute([$params->userId, $params->clientId]);

            // insert attachment
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, 
                record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
            $files->execute(["application_form", $params->_item_id, json_encode($attachments), "{$params->_item_id}", $params->userId, 
                $attachments["raw_size_mb"], $params->clientId
            ]);

            // log the user activity
            $this->userLogs("application_form", $params->_item_id, null, "<strong>{$params->userData->name}</strong> created a new Application Form: {$params->form_title}.", $params->userId);

            // commit the transaction
            $this->db->commit();

            // return the success response
            return [
                "code" => 200,
                "data" => "Application Form has been successfully saved!",
                "additional" => [
                    "clear" => true,
                    "href" => "{$this->baseUrl}application_forms",
                ],
                "record_id" => $params->_item_id
            ];

        } catch(PDOException $e) {
            $this->db->rollBack();
            return $this->unexpected_error;
        }

    }

    /**
     * Update Application Form
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        /** Confirm the user has the required permissions */
        global $accessObject;

        /** Confirm the forms application id */
        $prevData = $this->pushQuery("a.*, (SELECT b.description FROM files_attachment b WHERE 
                b.resource='application_forms' AND b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
            "users_application_forms a", "a.item_id='{$params->application_id}' AND a.client_id='{$params->clientId}' LIMIT 1");

        if(empty($prevData)) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment id was submitted"];
        }

        /** confirm that the form is not empty  */
        if(!isset($params->form)) {
            return ["data" => "Sorry! The application form cannot be empty", "code" => 203];
        }

        /** Convert the form data into an array json */
        $params->form = json_decode($params->form, true);
        
        if(!is_array($params->form)) {
            return ["data" => "Sorry! The application form must be an array", "code" => 203];
        }

        /** Loop through the form and rearrange the data */
        $formArray = $params->form;

        /** Clean the text */
        $params->description = htmlspecialchars(custom_clean(htmlspecialchars_decode($params->description)));
        $params->requirements = htmlspecialchars(custom_clean(htmlspecialchars_decode($params->requirements)));
        $params->form_footnote = htmlspecialchars(custom_clean(htmlspecialchars_decode($params->form_footnote)));
        $params->allow_attachment = isset($params->allow_attachment) ? $params->allow_attachment : "yes";

        /** Policy form data */
        $formData = [
            "fields" => $formArray,
            "form_footnote" => $params->form_footnote,
            "allow_attachment" => $params->allow_attachment
        ];

        /** Append to the previous assignment documents */
        $attachments = [];
        $prevData = $prevData[0];
        $initial_attachment = [];
        $module = "application_forms";

        // if the attachment is not empty
        if(!empty($this->session->{$module})) {
            /** Confirm that there is an attached document */
            if(!empty($prevData->attachment)) {
                // decode the json string
                $db_attachments = json_decode($prevData->attachment);
                // get the files
                if(isset($db_attachments->files)) {
                    $initial_attachment = $db_attachments->files;
                }
            }

            // append the attachments
            $filesObj = load_class("files", "controllers");
            $attachments = $filesObj->prep_attachments($module, $params->userId, $params->application_id, $initial_attachment);
        }

        /** Insert the record */
        try {
            // begin transaction
            $this->db->beginTransaction();

            // prepare the statement
            $stmt = $this->db->prepare("
                UPDATE users_application_forms SET description='{$params->description}'
                ".(!empty($params->form_title) ? ",name='{$params->form_title}'" : null)."
                ".(!empty($formData) ? ",form='".json_encode($formData)."'" : null)."
                ".(!empty($params->status) ? ",state='{$params->status}'" : null)."
                ".(!empty($params->requirements) ? ",requirements='{$params->requirements}'" : null)."
                ".(isset($params->year_enrolled) ? ",year_enrolled='{$params->year_enrolled}'" : null)."
                WHERE item_id = '{$params->application_id}' AND client_id = '{$params->clientId}' LIMIT 1
            ");
            $stmt->execute();

            // if the attachment is not empty
            if(!empty($attachments)) {
                // update application attachment
                $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? AND resource='application_form' LIMIT 1");
                $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $params->application_id]);
            }

            // commit the transaction
            $this->db->commit();

            // return the success response
            return [
                "code" => 200,
                "data" => "Application Form has been successfully updated!",
                "additional" => [
                    "href" => "{$this->baseUrl}application_forms/modify/{$params->application_id}",
                    "reload" => "{$this->baseUrl}application_forms/modify/{$params->application_id}"
                ]
            ];

        } catch(PDOException $e) {
            $this->db->rollBack();
            return $this->unexpected_error;
        }

    }

    /**
     * Change the Status of an application
     * 
     * @return Array
     *
     */
    public function status(stdClass $params) {
        
        try {

            $stmt = $this->db->prepare("UPDATE users_applications SET state = ? WHERE client_id = ? AND item_id = ? LIMIT 1");
            $stmt->execute([$params->status, $params->clientId, $params->application_id]);

            return [
                "code" => 200,
                "data" => "Application status successfully changed",
                "additional" => [
                    "data" => $this->the_status_label($params->status)
                ]
            ];

        } catch(PDOException $e) {}

    }
}
?>