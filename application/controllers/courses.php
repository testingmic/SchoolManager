<?php 

class Courses extends Myschoolgh {

    public function __construct($params = null)
    {
        parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;
    }

    /**
     * Load the resources and return
     * 
     * @return Array
     */
    public function resources_list($client_id, $item_id, $resource_id = null) {

        $query = !empty($resource_id) ? " AND a.item_id = '{$resource_id}'" : null;

        $list = $this->pushQuery(
            "a.item_id, a.lesson_id, a.course_id, a.description, a.resource_type, a.link_name, a.link_url, a.date_created", 
            "courses_resource_links a", 
            "a.client_id ='{$client_id}' AND a.course_id='{$item_id}' AND a.status='1' {$query}"
        );
        $the_list = [];
        foreach($list as $each) {
            $the_list[$each->resource_type][] = $each;
        }

        return $the_list;
    }
    
    /**
     * Update existing incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        $_filters = "1";
        
        $params->order_by = $params->order_by ?? "a.name";
        $params->limit = $params->limit ?? $this->global_limit;

        // run this portion if the userData is parsed
        if(isset($params->userData)) {
            // append the class_id if the user type is student
            if(($params->userData->user_type === "student") && !isset($params->bypass)) {
                $params->class_id = $params->userData->class_guid;
            } elseif(($params->userData->user_type === "teacher")) {
                $params->course_tutor = $params->userData->user_id;
            }
        }

        if(!empty($params->class_id) && strlen($params->class_id) < 6) {
            $check = $this->pushQuery("item_id", "classes", "id='{$params->class_id}'");
            if(!empty($check)) {
                $params->class_id = $check[0]->item_id;
            }
        }

        $_filters .= (!empty($params->class_id) && !is_array($params->class_id)) ? 
            " AND a.class_id LIKE '%{$params->class_id}%'" : (
                !empty($params->class_id) && is_array($params->class_id) ? " AND a.class_id IN {$this->inList($params->class_id)} " : null
        );
        $_filters .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
        $_filters .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);
        $_filters .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $_filters .= !empty($params->course_tutor) ? " AND a.course_tutor LIKE '%{$params->course_tutor}%'" : null;
        $_filters .= !empty($params->courses_ids) ? " AND a.id IN {$this->inList($params->courses_ids)}" : null;
        
        if(!isset($params->minified)) {
            $_filters .= !empty($params->q) ? " AND a.name LIKE '%{$params->q}%'" : null;
            $_filters .= !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
            $_filters .= !empty($params->department_id) ? " AND a.department_id='{$params->department_id}'" : null;
            $_filters .= !empty($params->programme_id) ? " AND a.programme_id='{$params->programme_id}'" : null;
            $_filters .= !empty($params->course_id) ? " AND (a.id='{$params->course_id}' OR a.item_id='{$params->course_id}')" : null;
        }

        try {

            $stmt = $this->db->prepare("
                SELECT a.*, b.name AS class_name,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM courses a
                LEFT JOIN classes b ON b.item_id = a.class_id
                WHERE {$_filters} AND a.deleted = ? ORDER BY {$params->order_by} LIMIT {$params->limit}
            ");
            $stmt->execute([0]);

            $minified = isset($params->minified) ? true : false;

            // load class if an only if the minified has not been parsed
            if(!$minified) {
                $commentsObj = load_class("replies", "controllers");
                $filesObject = load_class("files", "controllers");

                // if the user isset
                if(isset($params->userId)) {
                    // set param for the thread interactions
                    $threadInteraction = (object)[
                        "userId" => $params->userId,
                        "feedback_type" => "comment"
                    ];
                }
            } else {
                $filesObject = load_class("files", "controllers");
            }
            
            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // load this section if the minified was not parsed
                if(!$minified) {
                
                    // if the files is set
                    if(isset($params->full_attachments)) {
                        $result->attachment = $filesObject->resource_attachments_list("courses_plan", $result->id);
                    }

                    // load the course links
                    $result->resources_list = $this->resources_list($params->clientId, $result->item_id);
                    
                    // if a request was made for full details
                    if(isset($params->full_details)) {
                        // set aa new param
                        $para = (object) [
                            "clientId" => $params->clientId,
                            "course_id" => $result->id
                        ];

                        // if the user isset
                        if(isset($params->userId)) {
                            // create a new object
                            $threadInteraction->resource_id = $result->id;
                            $result->comments_list = $commentsObj->list($threadInteraction);
                        }
                        
                        // if the user is permitted
                        $result->lesson_plan = $this->course_unit_lessons_list($para);
                    }

                    // set the course tutor into an array
                    $course_tutors = json_decode($result->course_tutor, true);
                    $result->course_tutor_ids = empty($course_tutors) ? [] : $course_tutors;

                    // convert to array
                    $result->class_ids = !empty($result->class_id) ? $this->stringToArray($result->class_id) : [];
                    
                    // load the course tutor details
                    if(!empty($course_tutors)) {
                        // loop through the array list
                        foreach($course_tutors as $tutor) {
                            // get the course tutor information
                            $tutor_info = $this->pushQuery("name, item_id, unique_id, phone_number, email, image", "users", "item_id='{$tutor}' AND user_status='Active' LIMIT 1");
                            if(!empty($tutor_info)) {
                                $result->course_tutors[] = $tutor_info[0];
                            }
                        }
                    } else {
                        $result->course_tutors = [];
                    }

                    // load the course tutor details
                    if(!empty($result->class_ids)) {
                        
                        // loop through the array list
                        foreach($result->class_ids as $class) {
                            // get the course tutor information
                            $class_info = $this->pushQuery("name, id, item_id, class_size, class_code, 
                                (SELECT COUNT(*) FROM users b WHERE b.class_id = a.id AND b.user_type = 'student') AS students_count", 
                                "classes a", "item_id='{$class}' AND status='1' LIMIT 1");
                            if(!empty($class_info)) {
                                $result->class_list[] = $class_info[0];
                            }
                        }
                    } else {
                        $result->class_list = [];
                    }
                    
                    // loop through the information
                    foreach(["created_by_info"] as $each) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
                    }
                }

                if(!empty($params->remote)) {
                    unset($result->course_tutor_ids);
                    unset($result->course_tutor);
                    unset($result->class_ids);
                }

                // if the files is set
                if(isset($params->attachments_only)) {
                    $data[] = $filesObject->resource_attachments_list("courses_plan", $result->id, $params->rq ?? null);
                } else {
                    $data[] = $result;
                }
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * View a course
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function view($params) {

        if(empty($params->course_id)) {
            return ["code" => 400, "data" => "Sorry! An invalid course id was supplied."];
        }
        
        $params->full_details = true;
        $params->full_attachments = true;

        return $this->list($params)["data"][0] ?? [];
    }

    /**
     * List Units / Lessons
     * 
     * @param stdClass $course_id
     * 
     * @return Array
     */
    public function course_unit_lessons_list(stdClass $params, $type = "", $unit_id = "") {

        try {
            
            $query = "";
            $type = (isset($params->type) && !empty($params->type)) ? $params->type : (!empty($type) ? $type : "unit");
            $query .= isset($params->academic_year) && !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
            $query .= isset($params->academic_term) && !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);
            $query .= (isset($params->unit_id) && !empty($params->unit_id)) ? " AND unit_id = '{$params->unit_id}'" : (!empty($unit_id) ? " AND unit_id = '{$unit_id}'" : null);
            $query .= (isset($params->course_id) && !empty($params->course_id)) ? " AND course_id='{$params->course_id}'" : "";

            $isMinified = (bool) isset($params->minified);

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT b.description FROM files_attachment b WHERE b.resource='courses_plan' AND b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM courses_plan a
                WHERE client_id=? AND plan_type = ? AND a.status = ? {$query} ORDER BY a.id LIMIT {$this->global_limit}
            ");
            $stmt->execute([$params->clientId, $type, 1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                
                // if not a minified request
                if(!$isMinified) {

                    // loop through the information
                    foreach(["created_by_info"] as $each) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
                    }

                    // if attachment variable was parsed
                    $result->attachment = json_decode($result->attachment);

                    // if the files is set
                    if(!isset($result->attachment->files)) {
                    $result->attachment = (object) [
                            "files" => [],
                            "files_count" => 0,
                            "files_size" => 0,
                            "raw_size_mb" => 0
                        ];
                    }

                    // clean the description attached to the list
                    $result->description = htmlspecialchars_decode($result->description);
                    $result->description = custom_clean($result->description);
                    
                    if($result->plan_type == "unit") {
                        // load the course links
                        $result->lessons_list = $this->course_unit_lessons_list($params, "lesson", $result->id);
                    }
                }

                $data[] = $result;
            }

            return $data;


        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
        
    }

    /**
     * Add new Course record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        global $accessObject, $defaultClientData;

        // check permission
        if(!$accessObject->hasAccess("add", "course")) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        // ensure at least one class has been selected
        if(empty($params->class_id)) {
            return ["code" => 400, "data" => "Sorry! Select at least one class to continue."];   
        }

        // create a new Course code
        if(empty($params->course_code)) {
            // generate a new Course code
            $counter = $this->append_zeros(($this->itemsCount("courses", "client_id = '{$params->clientId}'") + 1), 3);
            $params->course_code = $defaultClientData->client_preferences->labels->{"course_label"}.$counter;
        }
        
        // convert the code to uppercase
        $params->course_code = strtoupper($params->course_code);

        try {

            // init
			$tutor_ids = $params->course_tutor ?? [];
            $class_ids = [];

            // loop through the classes that offers this course and insert the record
            foreach($params->class_id as $class_id) {

                // generate a unique subject id
                $item_id = random_string("alnum", RANDOM_STRING);

                // execute the statement
                $stmt = $this->db->prepare("
                    INSERT INTO courses SET course_tutor = ?, class_id = ?, client_id = ?, created_by = ?, item_id = '{$item_id}'
                    ".(!empty($params->name) ? ", name = '{$params->name}'" : null)."
                    ".(!empty($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                    ".(!empty($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                    ".(!empty($params->credit_hours) ? ", credit_hours = '{$params->credit_hours}'" : null)."
                    ".(!empty($params->weekly_meeting) ? ", weekly_meeting = '{$params->weekly_meeting}'" : null)."
                    ".(!empty($params->course_code) ? ", course_code = '{$params->course_code}'" : null)."
                    ".(!empty($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                    ".(!empty($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                    ".(!empty($params->description) ? ", description = '{$params->description}'" : null)."
                ");
                $stmt->execute([json_encode($tutor_ids), $class_id, $params->clientId, $params->userId]);
                
                // set the course id
                $course_id = $this->lastRowId("courses");

                // append the course tutors
                $this->append_course_tutors($tutor_ids, $course_id, $params->clientId);

                // log the user activity
                $this->userLogs("courses", $item_id, null, "{$params->userData->name} created a new Subject: {$params->name} for the Class with ID::{$class_id}", $params->userId);
            }

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Subject successfully created.", "refresh" => 2000];
			
			# append to the response
            $return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}courses"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Update existing Course record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        global $accessObject, $defaultClientData;

        // check permission
        if(!$accessObject->hasAccess("update", "course")) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        try {

            // old record
            $prevData = $this->pushQuery("*", "courses", "id='{$params->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // init
			$tutor_ids = !empty($params->course_tutor) ? $this->stringToArray($params->course_tutor) : [];
            $class_ids = !empty($params->class_id) ? $params->class_id : null;

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE courses SET date_updated = '{$this->current_timestamp}'
                ".(!empty($class_ids) ? ", class_id = '{$class_ids}'" : null)."
                ".(!empty($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($tutor_ids) ? ", course_tutor = '".json_encode($tutor_ids)."'" : null)."
                ".(!empty($params->credit_hours) ? ", credit_hours = '{$params->credit_hours}'" : null)."
                ".(!empty($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(!empty($params->course_code) ? ", course_code = '{$params->course_code}'" : null)."
                ".(!empty($params->weekly_meeting) ? ", weekly_meeting = '{$params->weekly_meeting}'" : null)."
                ".(!empty($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(!empty($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(!empty($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(!empty($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                WHERE id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([$params->course_id, $params->clientId]);
            
            // append the course tutors
            $this->append_course_tutors($tutor_ids, $params->course_id, $params->clientId);

            // log the user activity
            $this->userLogs("courses", $prevData[0]->item_id, $prevData[0], "{$params->userData->name} updated the Course: {$prevData[0]->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Subject successfully updated.", "refresh" => 2000];

            if(isset($params->name) && ($prevData[0]->name !== $params->name)) {
                $this->userLogs("courses", $prevData[0]->item_id, $prevData[0]->name, "Subject name was changed from {$prevData[0]->name}", $params->userId);
            }

            if(isset($params->credit_hours) && ($prevData[0]->credit_hours !== $params->credit_hours)) {
                $this->userLogs("courses", $prevData[0]->item_id, $prevData[0]->credit_hours, "Subject credit hours was changed from {$prevData[0]->credit_hours}", $params->userId);
            }

            if(isset($params->course_code) && ($prevData[0]->course_code !== $params->course_code)) {
                $this->userLogs("courses", $prevData[0]->item_id, $prevData[0]->course_code, "Subject code was changed from {$prevData[0]->course_code}", $params->userId);
            }

            if(isset($params->weekly_meeting) && ($prevData[0]->weekly_meeting !== $params->weekly_meeting)) {
                $this->userLogs("courses", $prevData[0]->item_id, $prevData[0]->weekly_meeting, "Weekly Meetings was changed from {$prevData[0]->weekly_meeting}", $params->userId);
            }
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}course/{$params->course_id}/update"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Add new Course Unit record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add_unit(stdClass $params) {

        try {
            // get & set some default variables
            global $defaultClientData;
            $item_id = random_string("alnum", RANDOM_STRING);
            
            // set the academic_term and the academic_year
            $params->academic_term = isset($params->academic_term) ? $params->academic_term : $defaultClientData->client_preferences->academics->academic_term;
            $params->academic_year = isset($params->academic_year) ? $params->academic_year : $defaultClientData->client_preferences->academics->academic_year;

            // validate the date
            if(!empty($params->start_date) && !isvalid_date($params->start_date)) {
                return ["code" => 400, "data" => "Sorry! The start date is invalid."];
            }

            // validate the end date
            if(!empty($params->end_date) && !isvalid_date($params->end_date)) {
                return ["code" => 400, "data" => "Sorry! The end date is invalid."];
            }

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO courses_plan SET client_id = ?, created_by = ?, item_id = '{$item_id}'
                ".(!empty($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($params->unit_id) ? ", unit_id = '{$params->unit_id}'" : null)."
                ".(!empty($params->course_id) ? ", course_id = '{$params->course_id}'" : null)."
                ".(!empty($params->start_date) ? ", start_date = '{$params->start_date}'" : null)."
                ".(!empty($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(!empty($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(!empty($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(!empty($params->end_date) ? ", end_date = '{$params->end_date}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);
            
            // update the course date
            $this->db->query("UPDATE courses SET date_updated='{$this->current_timestamp}', units_count=(units_count+1) WHERE id='{$params->course_id}' LIMIT 1");

            // set the last unit id
            $unit_id = $this->lastRowId("courses_plan");
            $this->session->set("thisLast_UnitId", $unit_id);

            // log the user activity
            $this->userLogs("courses_plan", $unit_id, null, "{$params->userData->name} created a new Course Unit: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Unit successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}course/{$params->course_id}/lesson"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Update Course Unit record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update_unit(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "courses_plan", "id='{$params->unit_id}' AND course_id='{$params->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE courses_plan SET date_updated = '{$this->current_timestamp}'
                ".(!empty($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($params->start_date) ? ", start_date = '{$params->start_date}'" : null)."
                ".(!empty($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(!empty($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(!empty($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(!empty($params->end_date) ? ", end_date = '{$params->end_date}'" : null)."
                WHERE client_id = ? AND course_id = ? AND id = ? LIMIT 1
            ");
            $stmt->execute([$params->clientId, $params->course_id, $params->unit_id]);

            // set the last unit id
            $this->session->set("thisLast_UnitId", $params->unit_id);

            // update the course date
            $this->db->query("UPDATE courses SET date_updated='{$this->current_timestamp}' WHERE id='{$params->course_id}' LIMIT 1");
            
            // log the user activity
            $this->userLogs("courses_plan", $params->unit_id, $prevData[0], "{$params->userData->name} Updated Course Unit: {$params->name}", $params->userId);

            if(isset($params->name) && ($prevData[0]->name !== $params->name)) {
                $this->userLogs("courses_plan", $params->unit_id, $prevData[0]->name, "Unit Name was changed from {$prevData[0]->name}", $params->userId);
            }

            if(isset($params->start_date) && ($prevData[0]->start_date !== $params->start_date)) {
                $this->userLogs("courses_plan", $params->unit_id, $prevData[0]->start_date, "Unit Start Date was changed from {$prevData[0]->start_date}", $params->userId);
            }

            if(isset($params->end_date) && ($prevData[0]->end_date !== $params->end_date)) {
                $this->userLogs("courses_plan", $params->unit_id, $prevData[0]->end_date, "Unit End Date was changed from {$prevData[0]->end_date}", $params->userId);
            }

            if(isset($params->description) && ($prevData[0]->description !== $params->description)) {
                $this->userLogs("courses_plan", $params->unit_id, $prevData[0]->description, "Unit description was changed from {$prevData[0]->description}", $params->userId);
            }

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Unit successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}course/{$params->course_id}/lesson"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    
    /**
     * Add new Course Unit Lesson record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add_lesson(stdClass $params) {

        try {

            // get and set some default data
            global $defaultClientData;
            $item_id = random_string("alnum", RANDOM_STRING);

            if(isset($params->unit_id)) {
                $this->session->set("thisLast_UnitId", $params->unit_id);
            }

            // set the academic_term and the academic_year
            $params->academic_term = isset($params->academic_term) ? $params->academic_term : $defaultClientData->client_preferences->academics->academic_term;
            $params->academic_year = isset($params->academic_year) ? $params->academic_year : $defaultClientData->client_preferences->academics->academic_year;

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO courses_plan SET client_id = ?, created_by = ?, 
                plan_type = 'lesson', item_id = '{$item_id}'
                ".(!empty($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($params->unit_id) ? ", unit_id = '{$params->unit_id}'" : null)."
                ".(!empty($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(!empty($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(!empty($params->course_id) ? ", course_id = '{$params->course_id}'" : null)."
                ".(!empty($params->start_date) ? ", start_date = '{$params->start_date}'" : null)."
                ".(!empty($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(!empty($params->end_date) ? ", end_date = '{$params->end_date}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);

            // append the attachments
            $lesson_id = $this->lastRowId("courses_plan");
            $filesObj = load_class("files", "controllers");

            // update the course date
            $this->db->query("UPDATE courses SET date_updated='{$this->current_timestamp}', lessons_count=(lessons_count+1) WHERE id='{$params->course_id}' LIMIT 1");

            // attachments
            $attachments = $filesObj->prep_attachments("course_lesson_{$params->unit_id}", $params->userId, $item_id);
            
            // log the user activity
            $this->userLogs("courses_plan", $lesson_id, null, "{$params->userData->name} created a new Course Unit: {$params->name}", $params->userId);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
            $files->execute(["courses_plan", $params->course_id ?? $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"], $params->clientId]);
            
            # set the output to return when successful
			$return = ["code" => 200, "data" => "Lesson successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}course/{$params->course_id}/lesson"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Update Course Unit Lesson record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update_lesson(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery(
                "a.*, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment",
                "courses_plan a", 
                "a.id='{$params->lesson_id}' AND a.course_id='{$params->course_id}' AND a.client_id='{$params->clientId}' AND a.status='1' LIMIT 1"
            );

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // initialize
            $initial_attachment = [];

            /** Confirm that there is an attached document */
            if(!empty($prevData[0]->attachment)) {
                // decode the json string
                $db_attachments = json_decode($prevData[0]->attachment);
                // get the files
                if(isset($db_attachments->files)) {
                    $initial_attachment = $db_attachments->files;
                }
            }

            if(isset($params->unit_id)) {
                $this->session->set("thisLast_UnitId", $prevData[0]->unit_id);
            }

            // append the attachments
            $filesObj = load_class("files", "controllers");
            $module = "course_lesson_{$prevData[0]->unit_id}";
            $attachments = $filesObj->prep_attachments($module, $params->userId, $prevData[0]->item_id, $initial_attachment);

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE courses_plan SET date_updated = '{$this->current_timestamp}'
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->unit_id) ? ", unit_id = '{$params->unit_id}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->start_date) ? ", start_date = '{$params->start_date}'" : null)."
                ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(isset($params->end_date) ? ", end_date = '{$params->end_date}'" : null)."
                WHERE client_id = ? AND course_id = ? AND id = ? LIMIT 1
            ");
            $stmt->execute([$params->clientId, $params->course_id, $params->lesson_id]);

            // update the course date
            $this->db->query("UPDATE courses SET date_updated='{$this->current_timestamp}' WHERE id='{$params->course_id}' LIMIT 1");

            // update attachment if already existing
            if(isset($db_attachments)) {
                $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? AND client_id = ? LIMIT 1");
                $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $prevData[0]->item_id, $params->clientId]);
            } else {
                // insert the record if not already existing
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
                $files->execute(["courses_plan", $params->course_id ?? $prevData[0]->item_id, json_encode($attachments), "{$prevData[0]->item_id}", $params->userId, $attachments["raw_size_mb"], $params->clientId]);
            }

            // log the user activity
            $this->userLogs("courses_plan", $params->unit_id, $prevData[0], "{$params->userData->name} Updated Course Unit: {$params->name}", $params->userId);
            
            if(isset($params->name) && ($prevData[0]->name !== $params->name)) {
                $this->userLogs("courses_plan", $params->lesson_id, $prevData[0]->name, "Lesson Name was changed from {$prevData[0]->name}", $params->userId);
            }

            if(isset($params->start_date) && ($prevData[0]->start_date !== $params->start_date)) {
                $this->userLogs("courses_plan", $params->lesson_id, $prevData[0]->start_date, "Lesson Start Date was changed from {$prevData[0]->start_date}", $params->userId);
            }

            if(isset($params->end_date) && ($prevData[0]->end_date !== $params->end_date)) {
                $this->userLogs("courses_plan", $params->lesson_id, $prevData[0]->end_date, "Lesson End Date was changed from {$prevData[0]->end_date}", $params->userId);
            }

            if(isset($params->description) && ($prevData[0]->description !== $params->description)) {
                $this->userLogs("courses_plan", $params->lesson_id, $prevData[0]->description, "Lesson description was changed from {$prevData[0]->description}", $params->userId);
            }

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Lesson successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}course/{$params->course_id}/lesson"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
	 * Append Courses Classes
	 * 
	 * Loop through the courses that the user has been attached to 
	 * If not in the courses tutors list then append the user_id to it
	 * 
	 * @return Bool
	 */
	public function append_class_courses($course_class, $course_id, $client_id) {

		$course_classes = $this->stringToArray($course_class);
		$valid_ids = [];

		foreach($course_classes as $class) {
			$query = $this->pushQuery("courses_list", "classes", "item_id='{$class}' AND client_id='{$client_id}' AND status='1' LIMIT 1");
			if(!empty($query)) {
				$valid_ids[] = $class;
				if(!empty($query[0]->courses_list)) {
					$result = json_decode($query[0]->courses_list, true);
					if(!in_array($course_id, $result)) {
						array_push($result, $course_id);
						$this->db->query("UPDATE classes SET courses_list = '".json_encode($result)."' WHERE item_id='{$class}' AND status='1' LIMIT 1");
					}
				} else {
					$classes = [$course_id];
					$this->db->query("UPDATE classes SET courses_list = '".json_encode($classes)."' WHERE item_id='{$class}' AND status='1' LIMIT 1");
				}
			}
		}
		return $valid_ids;

	}
    

	/**
	 * Unattach a class from a course
	 * 
	 * @return Bool
	 */
	public function remove_class_course($class_ids, $course_id, $client_id) {

		$class_ids = $this->stringToArray($class_ids);
		
		foreach($class_ids as $class) {
			$query = $this->pushQuery("courses_list", "classes", "item_id='{$class}' AND client_id='{$client_id}' AND status='1' LIMIT 1");
			if(!empty($query)) {
				if(!empty($query[0]->courses_list)) {
					$result = json_decode($query[0]->courses_list, true);
					if(in_array($course_id, $result)) {
						$key = array_search($course_id, $result);
						unset($result[$key]);
						$this->db->query("UPDATE classes SET courses_list = '".json_encode($result)."' WHERE item_id='{$class}' LIMIT 1");
					}
				}
			}
		}
	}

	/**
	 * Remove All Course Tutors
	 * 
	 * Loop through the courses that the user has been attached to 
	 * If not in the courses tutors list then append the user_id to it
	 * 
	 * @return Bool
	 */
	public function remove_all_class_courses(stdClass $params, $item_id) {

		$class_ids = $this->pushQuery("courses_list, id", "classes", "client_id='{$params->clientId}' AND status='1' LIMIT {$this->temporal_maximum}");

		foreach($class_ids as $class) {
			if(!empty($class->courses_list)) {
				$result = json_decode($class->courses_list, true);
				if(in_array($item_id, $result)) {
					$key = array_search($item_id, $result);
					if($key !== FALSE) {
						unset($result[$key]);
						$this->db->query("UPDATE classes SET courses_list = '".json_encode($result)."' WHERE id='{$class->id}' LIMIT 1");
					}
				}
			}
		}
		return true;

	}

    /**
	 * Append Courses Tutors
	 * 
	 * Loop through the courses that the user has been attached to 
	 * If not in the courses tutors list then append the user_id to it
	 * 
	 * @return Bool
	 */
	public function append_course_tutors($course_tutor, $course_id, $client_id) {

		$course_tutors = $this->stringToArray($course_tutor);
		$valid_ids = [];

		foreach($course_tutors as $tutor) {
			$query = $this->pushQuery("course_ids", "users", "item_id='{$tutor}' AND client_id='{$client_id}' LIMIT 1");
			if(!empty($query)) {
				$valid_ids[] = $tutor;
				if(!empty($query[0]->course_ids)) {
					$result = json_decode($query[0]->course_ids, true);
					if(!in_array($course_id, $result)) {
						array_push($result, $course_id);
						$this->db->query("UPDATE users SET 
                            course_ids = '".json_encode($result)."', class_ids = NULL
                            WHERE item_id='{$tutor}' LIMIT 1"
                        );
					}
				} else {
					$tutors = [$course_id];
					$this->db->query("UPDATE users SET 
                        course_ids = '".json_encode($tutors)."', class_ids = NULL 
                        WHERE item_id='{$tutor}' LIMIT 1"
                    );
				}
			}
		}
		return $valid_ids;

	}
    

	/**
	 * Unattach a tutor from a course
	 * 
	 * @return Bool
	 */
	public function remove_course_tutor($tutor_ids, $course_id, $client_id) {

		$tutor_ids = $this->stringToArray($tutor_ids);
		
		foreach($tutor_ids as $tutor) {
			$query = $this->pushQuery("course_ids", "users", "item_id='{$tutor}' AND client_id='{$client_id}' LIMIT 1");
			if(!empty($query)) {
				if(!empty($query[0]->course_ids)) {
					$result = json_decode($query[0]->course_ids, true);
					if(in_array($course_id, $result)) {
						$key = array_search($course_id, $result);
						unset($result[$key]);
						$this->db->query("UPDATE users SET course_ids = '".json_encode($result)."' WHERE item_id='{$tutor}' LIMIT 1");
					}
				}
			}
		}
	}

	/**
	 * Remove All Course Tutors
	 * 
	 * Loop through the courses that the user has been attached to 
	 * If not in the courses tutors list then append the user_id to it
	 * 
	 * @return Bool
	 */
	public function remove_all_course_tutors(stdClass $params) {

		$tutor_ids = $this->pushQuery("course_ids, id", "users", "client_id='{$params->clientId}' AND academic_year='{$params->academic_year}' AND academic_term='{$params->academic_term}' AND user_type='teacher' AND user_status='Active' LIMIT {$this->temporal_maximum}");

		foreach($tutor_ids as $course) {
			if(!empty($course->course_ids)) {
				$result = json_decode($course->course_ids, true);
				if(in_array($params->course_id, $result)) {
					$key = array_search($params->course_id, $result);
					if($key !== FALSE) {
						unset($result[$key]);
						$this->db->query("UPDATE users SET course_ids = '".json_encode($result)."' WHERE id='{$course->id}' AND user_status='Active' LIMIT 1");
					}
				}
			}
		}
		return true;

	}

    /**
     * Prepare the Course Material for Download
     * 
     * @param stdClass $content
     * 
     * @return String
     */
    public function draw($content) {

        // if empty the client data then return false
        if(empty($this->iclient)) {
            return;
        }

        // get the client logo content
        if(!empty($this->iclient->client_logo)) {
            $type = pathinfo($this->iclient->client_logo, PATHINFO_EXTENSION);
            $logo_data = file_get_contents($this->iclient->client_logo);
            $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
        }

        // set the address and the other information
        $html = "<table cellpadding=\"5\" width=\"100%\">
            <tr>
                <td align=\"center\">
                    ".(isset($client_logo) ? "<img src=\"{$client_logo}\" width=\"80px\"><br>" : "")."
                    <span style=\"padding:0px; font-weight:bold; font-size:20px; margin:0px;\">".strtoupper($this->iclient->client_name)."</span><br>
                    <span style=\"padding:0px; font-weight:bold; margin:0px;\">{$this->iclient->client_address}</span><br>
                    <span style=\"padding:0px; font-weight:bold; margin:0px;\">{$this->iclient->client_contact} ".(!$this->iclient->client_secondary_contact ? " / {$this->iclient->client_secondary_contact}" : null)."</span>
                </td>
            </tr>
        </table>";

        $html .= "<table cellpadding=\"6px\" cellspacing=\"1px\" width=\"100%\">\n";
        $html .= "
            <tr>
                <td width=\"30%\" style=\"border:solid 1px #ccc\">
                    <span style=\"font-size:13px\">Academic Year: ".strtoupper($content->academic_year)."</span><br>
                    <span style=\"font-size:13px\">Academic Term: ".strtoupper($content->academic_term)."</span><br>
                </td>
                <td width=\"70%\" style=\"border:solid 1px #ccc\">
                    <span style=\"font-size:24px\">".strtoupper($content->name)."</span><br>
                    <span style=\"font-size:13px;padding-right:40px;\"><strong>CODE:</strong> {$content->course_code}</span><br>
                    <span style=\"font-size:13px\"><strong>WEEKLY MEETINGS:</strong> {$content->weekly_meeting}</span><br>
                    <span style=\"font-size:13px\"><strong>CREDIT HOURS:</strong> {$content->credit_hours}</span>
                </td>
            </tr>
            </table>
            <table cellpadding=\"6px\" cellspacing=\"1px\" width=\"100%\">
            <tr><td colspan=\"2\">{$content->description}</td></tr>";
            $html .= "<tr><td colspan=\"2\"><span style=\"font-size:24px\">LESSON PLAN</span></td></tr>";
        foreach($content->lesson_plan as $key => $plan) {
            $html .= "<tr>";
            $html .= "<td width=\"70%\" style=\"border:solid 1px #ccc; font-size:16px; color:#fff; background-color:#2196f3;\">Unit ".($key+1).". {$plan->name}</td>";
            $html .= "<td width=\"30%\" style=\"border:solid 1px #ccc; color:#fff; background-color:#2196f3;\"><strong>{$plan->start_date}</strong> to <strong>{$plan->end_date}</strong></td>";
            $html .= "</tr>";
            $html .= "<tr>";
            $html .= "<td colspan=\"2\">{$plan->description}</td>";
            $html .= "</tr>";
            $html .= "<tr>";
            $html .= "<td colspan=\"2\">";
            foreach($plan->lessons_list as $lkey => $lesson) {
                $html .= "<table width=\"100%\" style=\"padding:0px;margin:0px;\">";
                $html .= "<tr>";
                $html .= "<td width=\"70%\" style=\"background-color:#ff9800;color:#fff;line-height:30px;height:30px;\">&nbsp;&nbsp;Lesson ".($lkey+1).". <strong>{$lesson->name}</strong></td>";
                $html .= "<td width=\"30%\" style=\"line-height:30px;height:30px;border:solid 1px #ccc; color:#fff; background-color:#ff9800;\">&nbsp;&nbsp;<strong>{$lesson->start_date}</strong> to <strong>{$lesson->end_date}</strong></td>";
                $html .= "</tr>";
                $html .= "<tr>";
                $html .= "<td colspan=\"2\">".(empty($lesson->description) ? "No content under this lesson": strip_tags($lesson->description, "<br><strong>"))."</td>";
                $html .= "</tr>";
                $html .= "<tr><td></td></tr>";
                $html .= "</table>";
            }
            $html .= "</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        $html .= "<table width=\"100%\">";
        // run this section if there are any resource links
        if(isset($content->resources_list["link"])) {
            // header
            $html .= "<tr><td style=\"line-height:30px;height:30px;border:solid 1px #ccc; font-size:18px; color:#fff; background-color:#607d8b;\"><span>&nbsp;ADDITIONAL SUBJECT RESOURCES</span></td></tr>";
            // loop through the links list
            foreach($content->resources_list["link"] as $key => $resource) {
                $html .= "<tr>";
                $html .= "<td>&nbsp;<span>".($key+1).". <strong>{$resource->link_name}</strong></span><br>";
                $html .= "&nbsp;<span>{$resource->description}</span><br>";
                $html .= "&nbsp;<a title=\"Click to visit resource\" href=\"{$resource->link_url}\" targe=\"_blank\">{$resource->link_url}</a><br>";
                $html .= "</td>";
                $html .= "</tr>";
            }
        }
        // load this section if the course tutors are not empty
        if(!empty($content->course_tutors)) {
            $html .= "<tr><td style=\"border:solid 1px #ccc; font-size:18px; color:#fff; background-color:#607d8b;\"><span>&nbsp;SUBJECT TUTORS</span></td></tr>";
        }
        $html .= "</table>";
        // load this section if the course tutors are not empty
        if(!empty($content->course_tutors)) {
            $html .= "
            <table width=\"100%\">
                <tr>
                    <td>
                        <span style=\"font-size:13px\"><strong>Fullname:</strong></span><br>
                        <span style=\"font-size:13px\"><strong>Employee ID:</strong></span><br>
                        <span style=\"font-size:13px\"><strong>Phone Number:</strong></span><br>
                        <span style=\"font-size:13px\"><strong>Email Address:</strong></span><br>
                    </td>";
            foreach($content->course_tutors as $key => $value) {
                $html .= "
                <td>
                    <span style=\"font-size:13px\">".$value->name."</span><br>
                    <span style=\"font-size:13px\">".$value->unique_id."</span><br>
                    <span style=\"font-size:13px\">".$value->phone_number."</span><br>
                    <span style=\"font-size:13px\">".$value->email."</span><br>
                </td>";
            }
            $html .= "</tr>";
            $html .= "</table>";
        }

        return $html;
    }

}
?>