<?php 

class Courses extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
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


        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // append the class_id if the user type is student
        if($params->userData->user_type == "student") {
            $params->class_id = $params->userData->class_id;
        }

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->course_tutor)) ? " AND a.course_tutor='{$params->course_tutor}'" : null;
        $params->query .= (isset($params->created_by)) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->programme_id)) ? " AND a.id='{$params->programme_id}'" : null;
        $params->query .= (isset($params->course_id)) ? " AND a.id='{$params->course_id}'" : null;
        $params->query .= (isset($params->class_id)) ? " AND a.class_id='{$params->class_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT name FROM classes WHERE classes.id = a.class_id LIMIT 1) AS class_name,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.course_tutor LIMIT 1) AS course_tutor_info
                FROM courses a
                WHERE {$params->query} AND a.deleted = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([0]);

            $is_permitted = false;
            $commentsObj = load_class("replies", "controllers");
            $filesObject = load_class("files", "controllers");

            $threadInteraction = (object)[
                "userId" => $params->userId,
                "feedback_type" => "comment"
            ];
            
            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                
                // if the files is set
                if(isset($params->full_attachments)) {
                   $result->attachment = $filesObject->resource_attachments_list("courses_plan", $result->id);
                }

                // load the course links
                $result->resources_list = $this->resources_list($params->clientId, $result->item_id);
                
                // if a request was made for full details
                if(isset($params->full_details)) {
                    
                    // create a new object
                    $threadInteraction->resource_id = $result->id;
                    $result->comments_list = $commentsObj->list($threadInteraction);
                    
                    // if the user is permitted
                    $result->lesson_plan = $this->course_lessons($result->client_id, $result->id);
                }
                
                // loop through the information
                foreach(["course_tutor_info", "created_by_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
                }

                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {} 

    }

    /**
     * List Lessons
     * 
     * @param Int $course_id
     * @param Int $client_id
     * 
     * @return Array
     */
    public function course_lessons($client_id, $course_id, $type = "unit", $unit_id = null) {

        try {

            $query = !empty($unit_id) ? " AND unit_id = '{$unit_id}'" : null;

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM courses_plan a
                WHERE client_id=? AND course_id = ? AND plan_type = ? AND a.status = ? {$query} ORDER BY a.id
            ");
            $stmt->execute([$client_id, $course_id, $type, 1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

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
                    $result->lessons_list = $this->course_lessons($client_id, $course_id, "lesson", $result->id);
                }

                $data[] = $result;
            }

            return $data;


        } catch(PDOException $e) {}
        
    }

    /**
     * Add new Course record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        global $accessObject;

        // check permission
        if(!$accessObject->hasAccess("add", "course")) {
            return ["code" => 203, "data" => $this->permission_denied];
        }
 
        // create a new Course code
        if(isset($params->course_code) && !empty($params->course_code)) {
            // replace any empty space with 
            $params->course_code = str_replace("/^[\s]+$/", "", $params->course_code);
            // confirm if the Course code already exist
            if(!empty($this->pushQuery("id, name", "courses", "status='1' AND client_id='{$params->clientId}' AND course_code='{$params->course_code}'"))) {
                return ["code" => 203, "data" => "Sorry! There is an existing Course with the same code."];
            }
        } else {
            // generate a new Course code
            $counter = $this->append_zeros(($this->itemsCount("courses", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
            $params->course_code = $this->client_data($params->clientId)->client_preferences->labels->{"course_label"}.$counter;
        }
        
        // convert the code to uppercase
        $params->course_code = strtoupper($params->course_code);

        try {

            $item_id = random_string("alnum", 32);

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO courses SET client_id = ?, created_by = ?, item_id = '{$item_id}'
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->credit_hours) ? ", credit_hours = '{$params->credit_hours}'" : null)."
                ".(isset($params->course_code) ? ", course_code = '{$params->course_code}'" : null)."
                ".(isset($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->course_tutor) ? ", course_tutor = '{$params->course_tutor}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);
            
            // log the user activity
            $this->userLogs("courses", $this->lastRowId("courses"), null, "{$params->userData->name} created a new Course: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Course successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true];

			// return the output
            return $return;

        } catch(PDOException $e) {} 

    }

    /**
     * Update existing Course record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        global $accessObject;

        // check permission
        if(!$accessObject->hasAccess("update", "course")) {
            return ["code" => 203, "data" => $this->permission_denied];
        }

        try {

            // old record
            $prevData = $this->pushQuery("*", "courses", "id='{$params->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // create a new class code
            if(isset($params->course_code) && !empty($params->course_code) && ($prevData[0]->course_code !== $params->course_code)) {
                // replace any empty space with 
                $params->course_code = str_replace("/^[\s]+$/", "", $params->course_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "courses", "status='1' AND client_id='{$params->clientId}' AND course_code='{$params->course_code}'"))) {
                    return ["code" => 203, "data" => "Sorry! There is an existing Course with the same code."];
                }
            } elseif(empty($prevData[0]->course_code) || !isset($params->course_code)) {
                // generate a new class code
                $counter = $this->append_zeros(($this->itemsCount("courses", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $params->course_code = $this->client_data($params->clientId)->client_preferences->labels->{"course_label"}.$counter;
            }

            // convert the code to uppercase
            $params->course_code = strtoupper($params->course_code);

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE courses SET date_updated = now()
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->credit_hours) ? ", credit_hours = '{$params->credit_hours}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                ".(isset($params->course_code) ? ", course_code = '{$params->course_code}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->course_tutor) ? ", course_tutor = '{$params->course_tutor}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
                WHERE id = ? AND client_id = ?
            ");
            $stmt->execute([$params->course_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("courses", $params->course_id, $prevData[0], "{$params->userData->name} updated the Course: {$prevData[0]->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Course successfully updated.", "refresh" => 2000];

            if(isset($params->name) && ($prevData[0]->name !== $params->name)) {
                $this->userLogs("courses", $params->course_id, $prevData[0]->name, "Course name was changed from {$prevData[0]->name}", $params->userId);
            }

            if(isset($params->credit_hours) && ($prevData[0]->credit_hours !== $params->credit_hours)) {
                $this->userLogs("courses", $params->course_id, $prevData[0]->credit_hours, "Course credit hours was changed from {$prevData[0]->credit_hours}", $params->userId);
            }

            if(isset($params->description) && ($prevData[0]->description !== $params->description)) {
                $this->userLogs("courses", $params->course_id, $prevData[0]->description, "Course description was changed from {$prevData[0]->description}", $params->userId);
            }

            if(isset($params->course_code) && ($prevData[0]->course_code !== $params->course_code)) {
                $this->userLogs("courses", $params->course_id, $prevData[0]->course_code, "Course code was changed from {$prevData[0]->course_code}", $params->userId);
            }
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}update-course/{$params->course_id}/update"];

			// return the output
            return $return;

        } catch(PDOException $e) {} 

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

            $item_id = random_string("alnum", 32);
            // set the academic_term and the academic_year
            $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->client_data($params->clientId)->client_preferences->academics->academic_term;
            $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->client_data($params->clientId)->client_preferences->academics->academic_year;

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO courses_plan SET client_id = ?, created_by = ?, item_id = '{$item_id}'
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->unit_id) ? ", unit_id = '{$params->unit_id}'" : null)."
                ".(isset($params->course_id) ? ", course_id = '{$params->course_id}'" : null)."
                ".(isset($params->start_date) ? ", start_date = '{$params->start_date}'" : null)."
                ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->end_date) ? ", end_date = '{$params->end_date}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);
            
            // set the last unit id
            $unit_id = $this->lastRowId("courses_plan");
            $this->session->set("thisLast_UnitId", $unit_id);

            // log the user activity
            $this->userLogs("courses_plan", $unit_id, null, "{$params->userData->name} created a new Course Unit: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Unit successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}update-course/{$params->course_id}/view"];

			// return the output
            return $return;

        } catch(PDOException $e) {} 

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
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE courses_plan SET date_updated = now()
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->start_date) ? ", start_date = '{$params->start_date}'" : null)."
                ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->end_date) ? ", end_date = '{$params->end_date}'" : null)."
                WHERE client_id = ? AND course_id = ? AND id = ? LIMIT 1
            ");
            $stmt->execute([$params->clientId, $params->course_id, $params->unit_id]);

            // set the last unit id
            $this->session->set("thisLast_UnitId", $params->unit_id);
            
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
			$return["additional"] = ["href" => "{$this->baseUrl}update-course/{$params->course_id}/view"];

			// return the output
            return $return;

        } catch(PDOException $e) {} 

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

            $item_id = random_string("alnum", 32);

            if(isset($params->unit_id)) {
                $this->session->set("thisLast_UnitId", $params->unit_id);
            }

            // set the academic_term and the academic_year
            $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->client_data($params->clientId)->client_preferences->academics->academic_term;
            $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->client_data($params->clientId)->client_preferences->academics->academic_year;

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO courses_plan SET client_id = ?, created_by = ?, 
                plan_type = 'lesson', item_id = '{$item_id}'
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->unit_id) ? ", unit_id = '{$params->unit_id}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->course_id) ? ", course_id = '{$params->course_id}'" : null)."
                ".(isset($params->start_date) ? ", start_date = '{$params->start_date}'" : null)."
                ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(isset($params->end_date) ? ", end_date = '{$params->end_date}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);

            // append the attachments
            $lesson_id = $this->lastRowId("courses_plan");
            $filesObj = load_class("files", "controllers");

            // attachments
            $attachments = $filesObj->prep_attachments("course_lesson_{$params->unit_id}", $params->userId, $item_id);
            
            // log the user activity
            $this->userLogs("courses_plan", $lesson_id, null, "{$params->userData->name} created a new Course Unit: {$params->name}", $params->userId);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
            $files->execute(["courses_plan", $params->course_id ?? $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"]]);
            
            # set the output to return when successful
			$return = ["code" => 200, "data" => "Lesson successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}update-course/{$params->course_id}/view"];

			// return the output
            return $return;

        } catch(PDOException $e) {} 

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
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
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
                UPDATE courses_plan SET date_updated = now()
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
            
            // update attachment if already existing
            if(isset($db_attachments)) {
                $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? LIMIT 1");
                $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $prevData[0]->item_id]);
            } else {
                // insert the record if not already existing
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                $files->execute(["courses_plan", $params->course_id ?? $prevData[0]->item_id, json_encode($attachments), "{$prevData[0]->item_id}", $params->userId, $attachments["raw_size_mb"]]);
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
			$return["additional"] = ["href" => "{$this->baseUrl}update-course/{$params->course_id}/view"];

			// return the output
            return $return;

        } catch(PDOException $e) {print $e->getMessage();} 

    }

    
}
?>