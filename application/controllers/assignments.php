<?php 

class Assignments extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List assignments list
     * 
     * User the usertype to ascertain which information to display
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        // global variable
        global $defaultUser, $defaultClientData;
        
        $query = "";
        $params->query = "1";

        $filesObject = load_class("forms", "controllers");

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // set some default client data
        $client_data = $defaultClientData;
        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $client_data->client_preferences->academics->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $client_data->client_preferences->academics->academic_year;
        
        // variables
        global $isTutorAdmin, $isWardParent, $isParent;

        // if the user is a parent or student
        if($isWardParent) {

            // set the user id
            $the_user_id = $isParent ? $this->session->student_id : $params->userData->user_id;

            // run the query
            $query = ",(SELECT handed_in FROM assignments_submitted c WHERE c.assignment_id=a.item_id AND c.student_id='{$the_user_id}') AS handed_in,
                (SELECT score FROM assignments_submitted c WHERE c.assignment_id=a.item_id AND c.student_id='{$the_user_id}') AS awarded_mark, 
                (SELECT b.description FROM files_attachment b WHERE b.resource='assignment_doc' AND b.record_id = a.item_id AND b.created_by = '{$the_user_id}' ORDER BY b.id DESC LIMIT 1) AS attached_document";
        }

        // if the user type is an admin or teacher
        if($isTutorAdmin) {
            $query = ",
            (SELECT COUNT(*) FROM assignments_submitted c WHERE c.assignment_id=a.item_id AND c.graded='1') AS students_graded,
            (SELECT COUNT(*) FROM assignments_submitted	c WHERE c.assignment_id=a.item_id AND c.handed_in='Submitted') AS students_handed_in,
            (SELECT COUNT(*) FROM users c WHERE c.client_id=a.client_id AND c.class_id = cl.id AND c.user_type='student' AND c.user_status='Active' AND c.status='1') AS students_assigned
            ";
        }

        // append the course tutor if the user_type is teacher
        if($params->userData->user_type == "teacher") {
            $params->course_tutor = $params->userData->user_id;
        }

        // append the class_id if the user type is student
        if($params->userData->user_type == "student") {
            $params->class_id = $params->userData->class_guid;
            $params->query .= " AND a.state NOT IN ('Cancelled', 'Draft')";
        }

        // is student 
        $isAStudent = (bool) ($params->userData->user_type == "student");

        $params->query .= " AND a.academic_year='{$params->academic_year}'";
        $params->query .= " AND a.academic_term='{$params->academic_term}'";

        $params->query .= (isset($params->class_id) && !empty($params->class_id)) ? " AND a.class_id='{$params->class_id}'" : null;
        $params->query .= (isset($params->due_date)) ? " AND a.due_date='{$params->due_date}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->course_id) && !empty($params->course_id)) ? " AND a.course_id='{$params->course_id}'" : null;
        $params->query .= (isset($params->assignment_id) && !empty($params->assignment_id)) ? " AND a.item_id='{$params->assignment_id}'" : null;
        $params->query .= (isset($params->course_tutor)) ? " AND a.course_tutor LIKE '%{$params->course_tutor}%'" : null;
        
        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                cl.name AS class_name,
                (SELECT name FROM courses WHERE courses.item_id = a.course_id LIMIT 1) AS course_name,
                (SELECT b.description FROM files_attachment b WHERE b.resource='assignments' AND b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                {$query} FROM assignments a
                LEFT JOIN classes cl ON cl.item_id = a.class_id
                WHERE {$params->query} AND a.status = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $isMinified = isset($params->minified) ? true : false;
            $showMarks = isset($params->show_marks) ? true : false;

            $data = [];

            // loop through the results list
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // convert the assigned to list into an array
                $assigned_to_list = !empty($result->assigned_to_list) ? json_decode($result->assigned_to_list, true) : [];
                $result->assigned_to_list = ($result->assigned_to == "selected_students" && empty($assigned_to_list)) ? $result->assigned_to_list : $assigned_to_list;
                
                // if the assigned to is not selected students
                if(
                    ($result->assigned_to !== "selected_students") || (
                        ($result->assigned_to === "selected_students") && 
                        in_array($defaultUser->user_id, $assigned_to_list)
                    ) || !$isAStudent
                ) {
                    // labels
                    $result->handedin_label = $result->state !== "Closed" ? $this->the_status_label("Pending") : $this->the_status_label($result->state);

                    // handedin label
                    if(isset($result->handed_in)) {
                        $result->handedin_label = $this->the_status_label($result->handed_in);
                    }

                    // if not minified request
                    if(!$isMinified) {

                        // is an attachment assignment
                        $isAttachment = (bool) ($result->assignment_type == "file_attachment");

                        // clean the assignment description
                        $result->assignment_description = custom_clean(htmlspecialchars_decode($result->assignment_description));

                        // count the number of students assigned to
                        if(isset($result->students_assigned)) {
                            $result->students_assigned = ($result->assigned_to === "selected_students") ? count($this->stringToArray($result->assigned_to_list)) : $result->students_assigned;
                        }

                        // set the course tutor into an array
                        $result->course_tutor = json_decode($result->course_tutor, true);

                        // loop through the array list
                        if(!empty($result->course_tutor)) {
                            foreach($result->course_tutor as $tutor) {
                                // get the course tutor information
                                $tutor_info = $this->pushQuery("name, item_id, unique_id, phone_number, email, image", "users", "item_id='{$tutor}' AND user_status='Active' LIMIT 1");
                                if(!empty($tutor_info)) {
                                    $result->course_tutors[] = $tutor_info[0];
                                }
                            }
                        }

                        // if attachment variable was parsed
                        if($isAttachment) {
                            // if the assignment is an attachment type
                            $result->attached_document = isset($result->attached_document) ? json_decode($result->attached_document) : [];
                            $result->attached_attachment_html = isset($result->attached_document->files) ? $filesObject->list_attachments($result->attached_document->files, $result->created_by, "col-lg-4 col-md-6", false, false) : null;

                            // decode the attachments as well
                            $result->attachment = json_decode($result->attachment);
                            $result->attachment_html = isset($result->attachment->files) ? $filesObject->list_attachments($result->attachment->files, $result->created_by, "col-lg-4 col-md-6", false, false) : "";
                        }

                        // loop through the information
                        foreach(["created_by_info"] as $each) {
                            // convert the created by string into an object
                            $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
                        }
                    }

                    if($showMarks && in_array($result->state, ["Closed", "Graded"])) {
                        $result->marks_list = $this->marks_list($result->item_id);
                    }

                    $data[] = $result;
                }
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {} 

    }    

    /**
     * Return the Marks Obtained by Students 
     * 
     * @param String    $assignment_id
     * 
     * @return Array
     */
    public function marks_list($assignment_id) {

        // make the request
        $marks_list = $this->pushQuery(
            "a.student_id, u.name AS student_name, u.image AS student_image, u.unique_id,
                a.score, a.handed_in, a.graded, a.date_submitted", 
            "assignments_submitted a LEFT JOIN users u ON u.item_id = a.student_id", 
            "a.assignment_id='{$assignment_id}'"
        );

        return $marks_list;

    }

    /**
     * Add a new assignment
     * 
     *@param stdClass $params
     * 
     * @return Array 
     */
    public function add(stdClass $params) {

        /** Confirm that the assignment_type is valid */
        if(!in_array($params->assignment_type, ["file_attachment", "multiple_choice"])) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment type was parsed."];
        }

        /** Run a class check */
        $classCheck = $this->pushQuery("id, department_id", "classes", "item_id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

        /** Confirm the class id */
        if(empty($classCheck)) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was submitted"];
        }

        /** Confirm the selected course */
        $course_data = $this->pushQuery("id, course_tutor", "courses", "item_id='{$params->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        if(empty($course_data)) {
            return ["code" => 203, "data" => "Sorry! An invalid course id was submitted"];
        }

        /** Confirm if the submission date is valid */
        if(!$this->validDate($params->date_due)) {
            return ["code" => 203, "data" => "Sorry! A valid submission date is required"];
        }

        /** The due date must not be lesser than today */
        if(strtotime($params->date_due) < strtotime(date("Y-m-d"))) {
            return ["code" => 203, "data" => "Sorry! The submission date must not be less than current date"];
        }

        /** Confirm the grade */
        if($params->grade < 1) {
            return ["code" => 203, "data" => "Sorry! The grade's value must be at least '1'"];
        }

        /** Confirm that the user is using the file attachment module */
        $item_id = random_string("alnum", 32);
        $is_attached = (bool) ($params->assignment_type == "file_attachment");

        try {
            
            /** Move any uploaded files */
            if($is_attached) {
                // state
                $state = "Pending";

                // unset the session if already set
                $this->session->remove("assignment_uploadID");
                
                // create a new object and prepare/move attachments
                $attachments = load_class("files", "controllers")->prep_attachments("assignments", $params->userId, $item_id);
                
                // insert the record if not already existing
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                $files->execute(["assignments", $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"]]);
            } else {
                $state = "Draft";
                // set the assignment id into a session
                $this->session->assignment_uploadID = $item_id;
            }

            /** Insert the record */
            $stmt = $this->db->prepare("
                INSERT INTO assignments SET client_id = ?, created_by = ?, item_id = '{$item_id}', state = '{$state}'
                ".(isset($params->assignment_type) ? ", assignment_type = '{$params->assignment_type}'" : null)."
                ".(isset($params->assignment_group) ? ", assignment_group = '{$params->assignment_group}'" : null)."
                ".(isset($params->assignment_title) ? ", assignment_title = '{$params->assignment_title}'" : null)."
                ".(isset($params->course_id) ? ", course_id = '{$params->course_id}'" : null)."
                ".(isset($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                ".(!empty($classCheck[0]->department_id) ? ", department_id = '{$classCheck[0]->department_id}'" : null)."
                ".(isset($params->grade) ? ", grading = '{$params->grade}'" : null)."
                ".(isset($course_data[0]->course_tutor) ? ", course_tutor = '{$course_data[0]->course_tutor}'" : null)."
                ".(isset($params->date_due) ? ", due_date = '{$params->date_due}'" : null)."
                ".(isset($params->time_due) ? ", due_time = '{$params->time_due}'" : null)."
                ".(isset($params->assigned_to) ? ", assigned_to = '{$params->assigned_to}'" : null)."
                ".(isset($params->assigned_to_list) ? ", assigned_to_list = '{$params->assigned_to_list}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->description) ? ", assignment_description = '".addslashes($params->description)."'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);

            // log the user activity
            $this->userLogs("assignments", $item_id, null, "{$params->userData->name} created a new Assignment: {$params->assignment_title}", $params->userId);

            // set the output to return when successful
            $return = ["code" => 200, "data" => "Assignment successfully created.", "refresh" => 2000];
			
			// append to the response
			$return["additional"] = ["clear" => true];

            // if the request is to add a quiz
            if($is_attached) {
                $return["data"] = "Assignment successfully created. Proceeding to add the questions";
                $return["additional"]["href"] = "{$this->baseUrl}add-assignment/add_question?qid={$item_id}";
            }

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update the assignment details
     * 
     * @param stdClass $params
     * 
     * @return Array 
     */
    public function update(stdClass $params) {

        /** Confirm the assignment id */
        $prevData = $this->pushQuery("a.*, (SELECT b.description FROM files_attachment b WHERE b.resource='assignments' AND b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
            "assignments a", "a.item_id='{$params->assignment_id}' AND a.client_id='{$params->clientId}' AND a.status='1'");

        if(empty($prevData)) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment id was submitted"];
        }

        /** Confirm the class id */
        if(empty($this->pushQuery("id", "classes", "item_id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1'"))) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was submitted"];
        }

        /** Confirm the selected course */
        $course_data = $this->pushQuery("id, course_tutor", "courses", "item_id='{$params->course_id}' AND client_id='{$params->clientId}' AND status='1'");
        if(empty($course_data)) {
            return ["code" => 203, "data" => "Sorry! An invalid course id was submitted"];
        }

        /** Confirm if the submission date is valid */
        if(!$this->validDate($params->date_due)) {
            return ["code" => 203, "data" => "Sorry! A valid submission date is required"];
        }

        /** Confirm the grade */
        if($params->grade < 1) {
            return ["code" => 203, "data" => "Sorry! The grade's value must be at least '1'"];
        }

        /** Append to the previous assignment documents */
        $prevData = $prevData[0];
        $initial_attachment = [];

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
        $module = "assignments";
        $attachments = $filesObj->prep_attachments($module, $params->userId, $prevData->item_id, $initial_attachment);

        // update the assignment attachments
        $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? AND resource='assignments' LIMIT 1");
        $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $prevData->item_id]);

        try {
            /** Insert the record */
            $stmt = $this->db->prepare("
                UPDATE assignments SET date_updated = now()
                ".(isset($params->assignment_type) ? ", assignment_type = '{$params->assignment_type}'" : null)."
                ".(isset($params->assignment_group) ? ", assignment_group = '{$params->assignment_group}'" : null)."
                ".(isset($params->assignment_title) ? ", assignment_title = '{$params->assignment_title}'" : null)."
                ".(isset($params->course_id) ? ", course_id = '{$params->course_id}'" : null)."
                ".(isset($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                ".(isset($params->grade) ? ", grading = '{$params->grade}'" : null)."
                ".(isset($course_data[0]->course_tutor) ? ", course_tutor = '{$course_data[0]->course_tutor}'" : null)."
                ".(isset($params->date_due) ? ", due_date = '{$params->date_due}'" : null)."
                ".(isset($params->time_due) ? ", due_time = '{$params->time_due}'" : null)."
                ".(isset($params->assigned_to) ? ", assigned_to = '{$params->assigned_to}'" : null)."
                ".(isset($params->assigned_to_list) ? ", assigned_to_list = '{$params->assigned_to_list}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->description) ? ", assignment_description = '".addslashes($params->description)."'" : null)."
                WHERE client_id = ? AND item_id = ? LIMIT 1
            ");
            $stmt->execute([$params->clientId, $params->assignment_id]);

            // log the user activity
            $this->userLogs("assignments", $params->assignment_id, null, "{$params->userData->name} updated the assignment details", $params->userId);

            if(isset($params->assignment_title) && ($prevData->assignment_title !== $params->assignment_title)) {
                $this->userLogs("assignments", $params->assignment_id, $prevData->assignment_title, "Assignment Title has been changed.", $params->userId);
            }

            if(isset($params->date_due) && ($prevData->due_date !== $params->date_due)) {
                $this->userLogs("assignments", $params->assignment_id, $prevData->due_date, "Due Date has been changed.", $params->userId);
            }

            if(isset($params->time_due) && ($prevData->due_time !== $params->time_due)) {
                $this->userLogs("assignments", $params->assignment_id, $prevData->due_time, "Due Time has been changed.", $params->userId);
            }

            if(isset($params->grading) && ($prevData->grading !== $params->grading)) {
                $this->userLogs("assignments", $params->assignment_id, $prevData->grading, "Assignment Grade has been changed.", $params->userId);
            }

            if(isset($params->description) && ($prevData->assignment_description !== $params->description)) {
                $this->userLogs("assignments", $params->assignment_id, $prevData->assignment_description, "Assignment description has been changed.", $params->userId);
            }

            if(isset($params->assigned_to) && ($prevData->assigned_to !== $params->assigned_to)) {
                $this->userLogs("assignments", $params->assignment_id, $prevData->assigned_to, "Assignment assigned to has been changed.", $params->userId);
            }

            // set the value
			$additional = ["href" => "{$this->baseUrl}update-assessment/{$params->assignment_id}/view"];

            # set the output to return when successful
            $return = ["code" => 200, "data" => "Assignment successfully updated.", "refresh" => 2000, "additional" => $additional];
			
			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
    }

    /**
     * List the Courses and Students List
     * 
     * This uses the class_id as a filter for the results to be returned in the query
     * 
     * @param stdClass $params->class_id
     * 
     * @return Array
     */
    public function load_course_students(stdClass $params) {

        /** Load the Students List */
        $result["students_list"] = $this->pushQuery("item_id, unique_id, name, email, phone_number, gender", "users", 
            "client_id='{$params->clientId}' AND class_id='{$params->class_id}' AND user_type='student' AND user_status='Active' AND status='1'");
        
        /** Load the courses list */
        $result["courses_list"] = $this->pushQuery("id, item_id, name, course_code", "courses", "class_id LIKE '%{$params->class_id}%' AND status='1'"); 

        /** Return the results */
        return [
            "data" => $result
        ];
        
    }

    /**
     * Award students marks
     * 
     * @return Array
     */
    public function award_marks(stdClass $params) {
        // global variable
        global $accessObject;

        // ensure the user has the necessary permissions
        if(!$accessObject->hasAccess("update", "assignments")) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        // get the assignment information
        $the_data = $this->pushQuery("id, grading", "assignments", "client_id='{$params->clientId}' AND item_id='{$params->assignment_id}' LIMIT 1");

        // validate the record
        if(empty($the_data)) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment id was parsed."];
        }
        $bug = false;
        $data = $the_data[0];

        // validate the record parsed
        if(!is_array($params->student_list)) {
            return ["code" => 203, "data" => "Sorry! The student_list variable accepts an array value."];
        }

        // loop through the list
        foreach($params->student_list as $student) {
            // explode each student record
            $exp = explode("|", $student);

            // break the loop if error found
            if(!isset($exp[1])) { $bug = true; break; }
            
            // check the grading marks
            if(($exp[1] > $data->grading) || ($exp[1] < 0)) { $bug = true; break; }

        }

        // return error if a bug was found
        if($bug) {
            return ["code" => 203, "data" => "Sorry! Please ensure that the marks assigned student does not exceed the grading value of: {$data->grading}."];
        }

        $graded_count = 0;
        $student_marks = [];

        // now loop through the list again and insert the user record
        foreach($params->student_list as $student) {
            // explode each student record
            $exp = explode("|", $student);
            $mark = $exp[1];
            $student_id = $exp[0];

            // insert the data into the database
            if(!empty($mark)) {
                // push into the array list
                $graded_count += 1;
                $student_marks[$student_id] = $mark;

                // check if the record exits
                $mark_check = $this->confirm_student_marked($params->assignment_id, $student_id);

                if($mark_check) {
                    // log the user activity
                    if($mark_check->score !== $mark) {
                        // update the record if it already exists
                        $stmt = $this->db->prepare("UPDATE assignments_submitted SET score=?, graded=?, date_graded=now() WHERE student_id=? AND assignment_id = ? LIMIT 1");
                        $stmt->execute([$mark, 1, $student_id, $params->assignment_id]);

                        // Record the user activity
                        $this->userLogs("assignments", "{$params->assignment_id}_{$student_id}", null, "{$params->userData->name} graded the student: {$mark}", $params->userId);
                    }
                } else {
                    // insert the new record since it does not exist
                    $stmt = $this->db->prepare("INSERT INTO assignments_submitted SET client_id = ?, score=?, graded=?, date_graded=now(), student_id=?, assignment_id = ?");
                    $stmt->execute([$params->clientId, $mark, 1, $student_id, $params->assignment_id]);
                    // Record the user activity
                    $this->userLogs("assignments", "{$params->assignment_id}_{$student_id}", null, "{$params->userData->name} graded the student: {$mark}", $params->userId);
                }
            }
        }

        // update the assignment state
        $this->db->query("UPDATE assignments SET state='Graded' WHERE item_id='{$params->assignment_id}' AND client_id='{$params->clientId}' LIMIT 1");

        // return the success response
        return [
            "data" => "Marks were successfully awarded to the list of students specified.",
            "additional" => [
                "marks" => $student_marks,
                "graded_count" => $graded_count
            ]
        ];
    }
    
    /**
     * Close Assignment
     * 
     * @param String        $params->assignment_id
     * 
     * @return Array
     */
    public function close(stdClass $params) {
        // get the assignment information
        $the_data = $this->pushQuery("id, grading", "assignments", "client_id='{$params->clientId}' AND item_id='{$params->assignment_id}' LIMIT 1");

        // validate the record
        if(empty($the_data)) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment id was parsed."];
        }

        // update the status of the assignment
        $this->db->query("UPDATE assignments SET state='Closed', date_closed=now() WHERE item_id='{$params->assignment_id}' AND client_id='{$params->clientId}' LIMIT 1");

        // Record the user activity
        $this->userLogs("assignment", "{$params->assignment_id}", null, "{$params->userData->name} closed the assignment thus prohibiting grading.", $params->userId);

        return [
            "data" => "Assignment was successfully closed."
        ];
    }
    
    /**
     * Reopen a closed Assignment
     * 
     * @param String        $params->assignment_id
     * 
     * @return Array
     */
    public function reopen(stdClass $params) {
        // get the assignment information
        $the_data = $this->pushQuery("id, grading", "assignments", "client_id='{$params->clientId}' AND item_id='{$params->assignment_id}' LIMIT 1");

        // validate the record
        if(empty($the_data)) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment id was parsed."];
        }

        // update the status of the assignment
        $this->db->query("UPDATE assignments SET state='Graded', date_closed=NULL WHERE item_id='{$params->assignment_id}' AND client_id='{$params->clientId}' LIMIT 1");

        // Record the user activity
        $this->userLogs("assignment", "{$params->assignment_id}", null, "{$params->userData->name} reopened the closed assignment for grading.", $params->userId);

        return [
            "data" => "Assignment was successfully reopened for grading."
        ];
    }
    
    /**
     * Publish an Assignment
     * 
     * @param String        $params->assignment_id
     * 
     * @return Array
     */
    public function publish(stdClass $params) {
        // get the assignment information
        $the_data = $this->pushQuery("id, grading", "assignments", "client_id='{$params->clientId}' AND item_id='{$params->assignment_id}' LIMIT 1");

        // validate the record
        if(empty($the_data)) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment id was parsed."];
        }

        // update the status of the assignment
        $this->db->query("UPDATE assignments SET state='Pending', date_published=now() WHERE item_id='{$params->assignment_id}' AND client_id='{$params->clientId}' LIMIT 1");

        // Record the user activity
        $this->userLogs("assignment", "{$params->assignment_id}", null, "{$params->userData->name} published the assignment.", $params->userId);

        return [
            "data" => "Assignment was successfully published."
        ];
    }

    /**
     * Handin Assignment
     * 
     * Upload the assignment data
     * 
     * @param String        $params->assignment_id
     * 
     * @return Array
     */
    public function handin(stdClass $params) {

        // get the assignment information
        $the_data = $this->pushQuery("id, grading", "assignments", "client_id='{$params->clientId}' AND item_id='{$params->assignment_id}' LIMIT 1");

        // validate the record
        if(empty($the_data)) {
            return ["code" => 203, "data" => "Sorry! An invalid assignment id was parsed."];
        }

        // initial values
        $files = "";
        $item_id = $params->assignment_id;
        $module = "assignments_handin_{$item_id}";
        $data = "Sorry! You have already handed in your assignment.";

        // catch all errors
        try {

            // run this section if the session is not empty
            if(!empty($this->session->{$module})) {
            
                // prepare the user documents
                $attachments = load_class("files", "controllers")->prep_attachments($module, $params->userId, $item_id);
                
                // insert the record if not already existing
                $stmt = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                $stmt->execute(["assignment_doc", "{$item_id}_{$params->userId}", json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"]]);

                // change the user information detail
                $check = $this->confirm_student_marked($params->assignment_id, $params->userId);
                
                // insert a record if empty
                if(empty($check) || (isset($check->handed_in) && ($check->handed_in === "Pending"))) {

                    // insert the new record since it does not exist
                    if(empty($check)) {
                        $stmt = $this->db->prepare("INSERT INTO assignments_submitted SET client_id = ?, student_id=?, assignment_id = ?, handed_in = ?");
                        $stmt->execute([$params->clientId, $params->userId, $item_id, "Submitted"]);
                    }
                    // update the record if the handed in is still pending
                    else {
                        $this->db->query("UPDATE assignments_submitted SET handed_in = 'Submitted' WHERE student_id='{$params->userId}' AND assignment_id = '{$item_id}' LIMIT 1");
                    }

                    // Record the user activity
                    $this->userLogs("assignment", "{$item_id}_{$params->userId}", null, "{$params->userData->name} handed in the assignment for grading.", $params->userId);

                    // load the attachments
                    $code = 200;
                    $data = "Congrats, your assignment was successfully submitted.";

                    // load the file attachments
                    $stmt = $this->db->prepare("SELECT description, created_by FROM files_attachment WHERE resource='assignment_doc' AND record_id = ? AND created_by = ? ORDER BY id DESC LIMIT 1");
                    $stmt->execute([$item_id, $params->userId]);
                    $result = $stmt->fetch(PDO::FETCH_OBJ);

                    $the_files = json_decode($result->description);
                    $files = isset($the_files->files) ? load_class("forms", "controllers")->list_attachments($the_files->files, $result->created_by, "col-lg-6", false, false) : null;
                }

                // remove the session variable
                $this->session->remove("attachAssignmentDocs");

            }

            // handin assignment for quiz like mode
            elseif(!empty($this->session->currentQuestionId) && $this->session->showSubmitButton) {

                // only save if the question id and answers were parsed
                if(isset($params->question_id) && isset($params->answers)) {
                    // save the answer for the last question
                    $params->save_answer_only = true;
                    $this->save_answer($params);
                }
                
                // load the user answers list
                $answer_info = $this->pushQuery(
                    "*", "assignments_answers", 
                    "assignment_id='{$item_id}' AND student_id = '{$params->userId}' AND client_id='{$params->clientId}' LIMIT 1"
                );

                // check if the answers list is empty
                if(empty($answer_info)) {
                    $data = "Sorry! You have not yet attempted to solve the questions";
                } else {

                    // get the answers parameter
                    $the_answers = json_decode($answer_info[0]->answers);
                    $score = 0;

                    // get the total marks obtained by the user
                    foreach($the_answers as $answer) {
                        if($answer->status == "correct") {
                            $score += $answer->assigned_mark;
                        }
                    }

                    // update the score for the user
                    $this->db->query("UPDATE assignments_answers SET scores = '{$score}' WHERE assignment_id='{$item_id}' AND student_id = '{$params->userId}' LIMIT 1");
                    $this->db->query("UPDATE assignments SET state = 'Answered' WHERE assignment_id='{$item_id}' AND client_id = '{$params->clientId}' LIMIT 1");
                    
                    // insert the record into the assignments_submitted table
                    $stmt = $this->db->prepare("INSERT INTO assignments_submitted SET client_id = ?, assignment_id = ?, student_id = ?, score = ?, graded = ?, handed_in = ?, date_graded = now()");
                    $stmt->execute([$params->clientId, $item_id, $params->userId, $score, 1, "Submitted"]);

                    // Record the user activity
                    $this->userLogs("assignment", "{$item_id}_{$params->userId}", null, "{$params->userData->name} handed in the assignment for auto grading by the system.", $params->userId);

                    // set the success message
                    $code = 200;
                    $data = "Congrats, your assignment was successfully submitted.";

                    // unset all the sessions that are not needed
                    $this->session->remove(["currentQuestionId","previousQuestionId","showSubmitButton", "attachAssignmentDocs", "nextQuestionId", "questionNumber"]);
                }

            }

            // set the data to error 
            else {
                $code = 203;
                $data = "Sorry! You have not attached any document. Please upload a document before submitting.";
            }

            return [
                "code" => $code,
                "data" => $data,
                "additional" => $files
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
    }

    /**
     * Display student assignment submission information
     * 
     * @param String        $params->student_id
     * @param String        $params->assignment_id
     * @param String        $params->preview
     */
    public function student_info(stdClass $params) {

        // load the file attachments
        $stmt = $this->db->prepare("SELECT description, created_by FROM files_attachment WHERE resource='assignment_doc' AND record_id = ? AND created_by = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$params->assignment_id, $params->student_id]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        // initial value
        $data = "<div class='alert mt-3 alert-info'>No attached files</div>";

        // if the description parameter is not empty
        if(isset($result->description)) {
            $the_files = json_decode($result->description);
            $data = isset($the_files->files) ? load_class("forms", "controllers")->list_attachments($the_files->files, $result->created_by, "col-lg-12", false, false) : null;
        }

        return [
            "data" => $data
        ];
    }

    /**
     * Confirm if the student record already exists
     * 
     * @param String        $assignmentId
     * @param String        $student_id
     * 
     * @return Object
     */
	public function confirm_student_marked($assignmentId, $student_id) {
		
        // execute the statement by making the query
		$stmt = $this->db->prepare("SELECT score, handed_in FROM assignments_submitted WHERE student_id = ? AND assignment_id=? LIMIT 1");
		$stmt->execute([$student_id, $assignmentId]);

		// count the number of rows found
		return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_OBJ) : false;
	}

    /**
     * Return the list of Assignment Questions
     * 
     * @param String    $params->assignment_id
     * @param String    $params->clientId
     * 
     * @return Array
     */
    public function questions_list(stdClass $params) {

        // columns to load
        $columns = isset($params->columns)  ? $params->columns : "a.id, a.item_id, a.marks, a.question";

        // make the request
        $questions = $this->pushQuery(
            $columns, "assignments_questions a", 
            "a.assignment_id='{$params->assignment_id}' AND a.client_id = '{$params->clientId}' AND a.deleted='0'"
        );

        return $questions;

    }

    /**
     * Add Assignment Question
     * 
     * This method first checks if the question id was parsed. If so then the question will 
     * be updated. If not then a new record will be inserted into the table
     * 
     * As part of the success response, parse the full list of all questions under this assignment record
     * 
     * @return Array
     */
    public function add_question(stdClass $params) {
        
        // confirm if the answer_type is in the array
        if(!in_array($params->answer_type, ["option", "multiple", "numeric", "input"])) {
            return ["code" => 203, "data" => "Sorry! An invalid answer type was parsed. Accepted values are: option, multiple, numeric, input"];
        }

        try {

            // get the assignment information
            $the_data = $this->pushQuery("id, grading", "assignments", "client_id='{$params->clientId}' AND item_id='{$params->assignment_id}' LIMIT 1");

            // validate the record
            if(empty($the_data)) {
                return ["code" => 203, "data" => "Sorry! An invalid assignment id was parsed."];
            }

            $found = false;
            $data = $the_data[0];
            $answers = isset($params->answers) && is_array($params->answers) ? implode(",", $params->answers) : "";

            // ensure that the answers parameter is not empty
            if(empty($answers) && in_array($params->answer_type, ["option", "multiple"])) {
                return ["code" => 203, "data" => "Sorry! Please select at least one option as the answer."];
            }

            // if the answer type is a numeric variable
            if(in_array($params->answer_type, ["numeric"])) {
                if(!isset($params->numeric_answer)) {
                    return ["code" => 203, "data" => "Sorry! Please enter the answer for this question in the provided space."];
                }
                $params->answers = $params->numeric_answer;
            }

            $append_msg = "";

            // get the assignment_id marks already in the database
            $assignment_marks = $this->pushQuery("SUM(marks) AS total_marks", 
                "assignments_questions", 
                (isset($params->question_id) ? "item_id != '{$params->question_id}' AND " : "")." 
                assignment_id='{$params->assignment_id}' AND client_id='{$params->clientId}'");

            if(!empty($assignment_marks)) {
                $grading = $data->grading;
                $the_marks = $assignment_marks[0]->total_marks;
                $total_mark = isset($params->marks) ? ($params->marks + $the_marks) : $the_marks;

                if($total_mark > $grading) {
                    return ["code" => 203, "data" => "Sorry! Adding this question with the assign marks would result in a grade of: {$total_mark} which is more than: {$grading}."];
                } elseif($total_mark == $grading) {
                    $append_msg = "This should be your last question. Since the marking scheme matches the grade set.";
                }
            }

            // get the question information
            if(isset($params->question_id)) {
                // get the assignment information
                $the_data = $this->pushQuery("id", "assignments_questions", "assignment_id='{$params->assignment_id}' AND item_id='{$params->question_id}' LIMIT 1");
                // validate the record
                if(empty($the_data)) {
                    return ["code" => 203, "data" => "Sorry! An invalid question id was parsed."];
                }
                $found = true;
            }

            // insert the record if not existing
            if(!$found) {
                // create a new unique id
                $item_id = random_string("alnum", 32);

                // statement to be executed
                $stmt = $this->db->prepare("
                    INSERT INTO assignments_questions SET item_id = ?, assignment_id = ?, 
                    question = ?, correct_answer = ?, created_by = ?, client_id = ?
                    ".(isset($params->marks) ? ",marks='{$params->marks}'" : null)."
                    ".(isset($params->option_a) ? ",option_a='{$params->option_a}'" : null)."
                    ".(isset($params->option_b) ? ",option_b='{$params->option_b}'" : null)."
                    ".(isset($params->option_c) ? ",option_c='{$params->option_c}'" : null)."
                    ".(isset($params->option_d) ? ",option_d='{$params->option_d}'" : null)."
                    ".(isset($params->option_e) ? ",option_e='{$params->option_e}'" : null)."
                    ".(isset($params->option_f) ? ",option_f='{$params->option_f}'" : null)."
                    ".(isset($params->answer_type) ? ",answer_type='{$params->answer_type}'" : null)."
                    ".(isset($params->difficulty) ? ",difficulty='{$params->difficulty}'" : null)."
                    ".(isset($params->correct_answer_description) ? ",correct_answer_description='{$params->correct_answer_description}'" : null)."
                ");
                $stmt->execute([$item_id, $params->assignment_id, $params->question, $answers, $params->userId, $params->clientId]);

                return [
                    "data" => "Assignment Question successfully created. {$append_msg}",
                    "additional" => [
                        "questions" => $this->questions_list($params)
                    ]
                ];

            } else {
                
                // statement to be executed
                $stmt = $this->db->prepare("
                    UPDATE assignments_questions SET question = ?, correct_answer = ?
                    ".(isset($params->option_a) ? ",option_a='{$params->option_a}'" : null)."
                    ".(isset($params->marks) ? ",marks='{$params->marks}'" : null)."
                    ".(isset($params->option_b) ? ",option_b='{$params->option_b}'" : null)."
                    ".(isset($params->option_c) ? ",option_c='{$params->option_c}'" : null)."
                    ".(isset($params->option_d) ? ",option_d='{$params->option_d}'" : null)."
                    ".(isset($params->option_e) ? ",option_e='{$params->option_e}'" : null)."
                    ".(isset($params->option_f) ? ",option_f='{$params->option_f}'" : null)."
                    ".(isset($params->answer_type) ? ",answer_type='{$params->answer_type}'" : null)."
                    ".(isset($params->difficulty) ? ",difficulty='{$params->difficulty}'" : null)."
                    ".(isset($params->correct_answer_description) ? ",correct_answer_description='{$params->correct_answer_description}'" : null)."
                    WHERE item_id = ? AND assignment_id = ? AND client_id = ? LIMIT 1
                ");
                $stmt->execute([$params->question, $answers, $params->question_id, $params->assignment_id, $params->clientId]);

                return [
                    "data" => "Assignment Question successfully updated",
                    "additional" => [
                        "questions" => $this->questions_list($params)
                    ]
                ];
            }

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Quick assignment data
     * 
     * @return String
     */
    public function quick_data(stdClass $data) {
        $html_content = '
        <div class="card-body pt-0 pb-0">
            <div class="py-3 pt-0">
                <p class="clearfix">
                    <span class="float-left">Course Name</span>
                    <span class="float-right text-muted">'.($data->course_name ?? null).'</span>
                </p>
                '.($data->hasUpdate ? '
                <p class="clearfix">
                    <span class="float-left">Assigned To</span>
                    <span class="float-right text-muted">'.($data->assigned_to == "selected_students" ? "{$data->students_assigned} Students" : "Entire Class").'</span>
                </p>
                <p class="clearfix">
                    <span class="float-left">Handed In</span>
                    <span class="float-right text-muted">'.$data->students_handed_in . ($data->students_handed_in > 1 ? " Students" : " Student" ).'</span>
                </p>
                <p class="clearfix">
                    <span class="float-left">Marked</span>
                    <span class="float-right text-muted"><span class="graded_count">'.$data->students_graded.' Students</span>
                </p>
                <p class="clearfix">
                    <span class="float-left">Grade</span>
                    <span class="float-right text-primary"><span style="font-size:30px">'.($data->grading ?? null).'</span>marks</span>
                </p>
                ' : null).'
                <p class="clearfix">
                    <span class="float-left">Submission Date</span>
                    <span class="float-right text-muted">'.date("jS F Y", strtotime($data->due_date)).'</span>
                </p>
                <p class="clearfix">
                    <span class="float-left">Submission Time</span>
                    <span class="float-right text-muted">'.date("h:iA", strtotime($data->due_time)).'</span>
                </p>
                <p class="clearfix">
                    <span class="float-left">Date Created</span>
                    <span class="float-right text-muted">'.date("jS F Y h:iA", strtotime($data->date_created)).'</span>
                </p>
                <p class="clearfix">
                    <span class="float-left">Status</span>
                    <span class="float-right text-muted" id="assignment_state">'.$this->the_status_label($data->state).'</span>
                </p>

                '.($data->isGraded ? 
                    '<p class="clearfix">
                        <span class="float-left font-weight-bold">Awarded Mark:</span>
                        <span class="float-right text-primary"><span style="font-size:30px">'.$data->awarded_mark.'</span>/<sub style="font-size:30px">'.$data->grading.'</sub></span>
                    </p>' : ''
                ).'
            </div>
        </div>';

        return $html_content;

    }

    /**
     * Modify an existing question details
     * 
     * @param String        $params->question_id
     * @param String        $params->assignment_id
     * 
     * @return String
     */
    public function review_question(stdClass $params) {

        // get the question data
        $question_info = $this->pushQuery(
            "a.*, (SELECT b.state FROM assignments b WHERE b.item_id = '{$params->assignment_id}' LIMIT 1) AS theState", 
            "assignments_questions a", 
            "a.item_id='{$params->question_id}' AND a.assignment_id='{$params->assignment_id}' LIMIT 1"
        );

        // if the question information is not empty
        if(empty($question_info)) {
           return ["code" => 203, "data" => "Sorry! An invalid question/assignment id was parsed."]; 
        }
        $data = $question_info[0];
        $isActive = (bool) ($data->theState == "Draft");

        $data->isActive = $isActive ? "active" : "not_active";

        $form = load_class("forms", "controllers")->add_question_form($params->assignment_id, $data);

        return [
            "data" => $form
        ];

    }

    /**
     * Get the Current Question to Display
     * 
     * Format the question data and submit the html content for display
     * 
     * @param Array     $questions_list
     * @param String    $userId
     * 
     * @return String
     */
    public function current_question(array $questions_list, $userId = null) {
        global $session;

        $questions_ids = array_column($questions_list, "item_id");
        $questions_count = count($questions_ids);
        $data = $this->question_content($questions_list, $session->currentQuestionId);
        
        // if the question data is not empty
        if(!empty($data)) {

            // unset the show submit button
            $session->showSubmitButton = false;
            $question = $data["question"];
            
            $question_id = $question->item_id;
            $answer_type = $question->answer_type;

            // if the answer_type is multiple or option then the default value must be an array
            $this_answer = in_array($answer_type, ["multiple", "option"]) ? [] : "";

            // load the existing user record (if any)
            $answer_info = $this->pushQuery(
                "*", "assignments_answers", 
                "assignment_id='{$question->assignment_id}' AND student_id = '{$userId}' LIMIT 1"
            );

            // get the list of answers submitted by the user if not empty
            if(!empty($answer_info)) {
                // convert the answers into an array
                $existing_answer = json_decode($answer_info[0]->answers, true);
                // loop through the array list for the question id
                if(!empty($existing_answer)) {
                    // init variable
                    $quest_key = "not_found";
                    // loop through the existing answers list
                    foreach($existing_answer as $qkey => $answer) {
                        if($answer["question_id"] == $question_id) {
                            $quest_key = $qkey;
                            break;
                        }
                    }
                    if($quest_key !== "not_found") {
                        $this_answer = $existing_answer[$quest_key]["answer"];
                        $this_answer = in_array($answer_type, ["multiple", "option"]) ? $this->stringToArray($this_answer) : $this_answer;
                        
                        // if the answer is empty but yet a multiple or option then replace with empty array
                        if(in_array($answer_type, ["multiple", "option"]) && empty($this_answer)) {
                            $this_answer = [];
                        }
                    }
                }
            }

            $number = $data["key"] + 1;
            
            // set the previous question id
            if($data["key"] == 0 && $questions_count > 1) {
                $session->previousQuestionId = null;
                $session->nextQuestionId = $questions_list[$data["key"]+1]->item_id;
            }
            elseif($data["key"] == ($questions_count -1)) {
                // set the previous and current question ids
                $session->previousQuestionId = $questions_list[$data["key"]-1]->item_id;
                $session->nextQuestionId = $questions_list[$questions_count-1]->item_id;
                // show the submit button
                $session->showSubmitButton = true;
            }
            else {
                // set the previous question id to the current key minus one
                $session->previousQuestionId = isset($questions_list[$data["key"]-1]) ? $questions_list[$data["key"]-1]->item_id : $questions_list[0]->item_id;

                // confirm that there is a next question after this question
                if(isset($questions_list[$data["key"]+1])) {
                    $session->nextQuestionId = $questions_list[$data["key"]+1]->item_id;
                } else {
                    // set the current question id to the last question
                    $session->nextQuestionId = $questions_list[$questions_count-1]->item_id;
                    // show the submit button
                    $session->showSubmitButton = true;
                }
            }

            // options array list
            $options_array = ["option_a" => "A", "option_b" => "B", "option_c" => "C","option_d" => "D", "option_e" => "E", "option_f" => "F"];

            // process the question data and return the processed information
            $question_html = "
            <div class='col-lg-12'>
                <table class='table table-bordered' id='multichoice_question' data-answer_type='{$answer_type}' data-question_id='{$question_id}'>
                <tr>
                    <td colspan='2'><h6 class='mb-0 pb-0'>{$questions_count} Objective test questions was found under this Assignment.</h6></td>
                </tr>
                <tr>
                    <td width='5%'>{$number}</td>
                    <td>{$question->question}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>";
                    // if the answer type is option or multiple
                    if(in_array($answer_type, ["multiple", "option"])) {
                        // loop through the array list
                        foreach($options_array as $key => $option) {
                            if(isset($question->{$key}) && !empty($question->{$key})) {
                                $question_html .= "
                                <div class='pt-2'>
                                    <input ".(in_array($key, $this_answer) ? "checked" : null)." name='answer_option' value='{$key}' id='{$key}' class='cursor checkbox' type='checkbox' style='height:20px; width:20px'>
                                    <label class='cursor' for='{$key}'><strong>{$option}.</strong> {$question->{$key}}</label>
                                </div>";
                            }
                        }
                    }
                    // if the answer must be a numeric value
                    elseif($answer_type == "numeric") {
                        $question_html .= "
                        <div class='pt-2'>
                            <label>Provide Correct Answer</label>
                            <input value='{$this_answer}' name='answer_option' id='answer_option' class='form-control' type='number'>
                        </div>";
                    }
                    // if the answer requires a text
                    elseif($answer_type == "input") {
                        $question_html .= "
                        <div class='pt-2'>
                            <label>Provide Correct Answer</label>
                            <textarea name='answer_option' id='answer_option' class='form-control'>{$this_answer}</textarea>
                        </div>";
                    }
                $question_html .= "
                    </td>
                </tr>";
                $question_html .= "
                </table>
                <div class='d-flex justify-content-between'>
                    <div>
                        ".($session->previousQuestionId && $number !== 1 ? "<button onclick='return loadQuestionInfo(\"{$session->previousQuestionId}\");' class='btn-sm btn-outline-primary btn'><i class='fa fa-fast-backward'></i> Previous Question</button>" : "")."
                    </div>
                    <div>
                        ".($session->showSubmitButton && $number == $questions_count ? "
                            <button onclick='return reviewQuizAssignment(\"{$question->assignment_id}\", \"{$session->previousQuestionId}\")' class='btn btn-sm btn-outline-primary'><i class='fa fa-eye'></i> Review Answers</button>
                            <button onclick='return submitQuizAssignment(\"{$question->assignment_id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-save'></i> Sumit Assignment</button>
                        " : "<button onclick='return loadQuestionInfo();' class='btn-sm btn-outline-primary btn'>Next Question <i class='fa fa-fast-forward'></i></button>")."
                    </div>
                </div>
            </div>";

            // add one to the question number
            $session->questionNumber = $number;

            return $question_html;
            
        }
    }

    /**
     * Review Answers
     * 
     * This method loads all the questions for the specified assignment id and then appends the user
     * answers to it
     * 
     * @param String        $params->assignment_id
     * @param String        $params->show_correct_answer
     * @param String        $params->student_id
     * 
     * @return
     */
    public function review_answers($params, $class = "p-0 mb-0") {

        // get the questions array list
        $params->columns = "a.*";
        $questions_array_list = $this->questions_list($params);

        // only save if the question id and answers were parsed
        if(isset($params->question_id) && isset($params->answers)) {
            // save the answer for the last question
            $params->save_answer_only = true;
            $this->save_answer($params);
        }

        $showAnswer = (bool) isset($params->show_answer);
        $t_student_id = isset($params->student_id) ? $params->student_id : $params->userId;

        // load the existing user record (if any)
        $answer_info = $this->pushQuery(
            "*", "assignments_answers", 
            "assignment_id='{$params->assignment_id}' AND student_id = '{$t_student_id}' AND client_id='{$params->clientId}' LIMIT 1"
        );
        // convert to array
        $answers_list = !empty($answer_info) ? json_decode($answer_info[0]->answers, true) : [];

        // options array list
        $options_array = ["option_a" => "A", "option_b" => "B", "option_c" => "C","option_d" => "D", "option_e" => "E", "option_f" => "F"];

        // init the information to parse
        $correct = null;
        $question_html = "
        <div class='col-lg-12 {$class}'>
        <table class='table table-bordered'>";

        // if no result was found
        if(empty($questions_array_list)) {
            // return error
            $question_html .= "<tr><td align='center'>Sorry! No result was found for the request.</td></tr>";
        } else {

            // loop through the questions list
            foreach($questions_array_list as $qkey => $question) {
                
                // set the answer type and the number
                $number = $qkey + 1;
                $answer_type = $question->answer_type;

                // answer mechanism
                if(isset($answers_list[$qkey])) {
                    $correct = (bool) ($answers_list[$qkey]["status"] === "correct");
                }

                $this_answer = $answers_list[$qkey]["answer"] ?? null;
                $this_answer = in_array($answer_type, ["multiple", "option"]) ? $this->stringToArray($this_answer) : $this_answer;
                
                // if the answer is empty but yet a multiple or option then replace with empty array
                if(in_array($answer_type, ["multiple", "option"]) && empty($this_answer)) {
                    $this_answer = [];
                }

                // correct answer
                $correctAnswer = $this->stringToArray($question->correct_answer);

                // process the question data and return the processed information
                $question_html .= "
                    <tr>
                        <td width='5%'>{$number}</td>
                        <td>{$question->question}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>";
                        // if the answer type is option or multiple
                        if(in_array($answer_type, ["multiple", "option"])) {
                            // start the html content
                            $question_html .= "<div style='width:100%;'>";

                            // if the show_answer parameter was parsed
                            if($showAnswer) {
                                // if the answer is correct
                                $question_html .= "
                                    <span style='float:right;right:0px;margin-top:10px;'>
                                        ".($correct ? "<span class='badge badge-success'>Correct</span>" : "<span class='badge badge-danger'>Wrong</span>")."
                                    </span>";
                            }
                            // loop through the array list
                            foreach($options_array as $key => $option) {
                                if(isset($question->{$key})) {
                                    $question_html .= "
                                    <div class='pt-2'>
                                        <input disabled='disabled' ".(in_array($key, $this_answer) ? "checked" : null)." name='answer_option' value='{$key}' id='{$key}' class='cursor checkbox' type='checkbox' style='height:20px; width:20px'>
                                        <label ".(in_array($key, $this_answer) ? "style='color:#2196F3 !important'" : null)." class='cursor' for='{$key}'>
                                            <strong>{$option}.</strong> {$question->{$key}} ".(in_array($key, $correctAnswer) ? "<span class='text-success'><i class='fa fa-check'></i></span>" : "")."
                                        </label>
                                    </div>";
                                }
                            }
                            $question_html .= "</div>";
                        }
                        // if the answer must be a numeric value
                        elseif($answer_type == "numeric") {
                            $question_html .= "
                            <div class='pt-2'>
                                <label>Provide Correct Answer</label>
                                <input disabled='disabled' value='{$this_answer}' name='answer_option' id='answer_option' class='form-control' type='number'>
                            </div>";
                        }
                        // if the answer requires a text
                        elseif($answer_type == "input") {
                            $question_html .= "
                            <div class='pt-2'>
                                <label>Provide Correct Answer</label>
                                <textarea disabled='disabled' name='answer_option' id='answer_option' class='form-control'>{$this_answer}</textarea>
                            </div>";
                        }
                    $question_html .= "
                        </td>
                    </tr>";
            }
            
        }
        $question_html .= "</table></div>";

        return ["data" => $question_html];

    }

    /**
     * Question Content
     * 
     * Loop through the questions array and confirm which one matches the id parsed
     * 
     * Return the array key and question details in the response
     * 
     * @param Array     $questions_list
     * @param String    $question_id
     * 
     * @return Array
     */
    public function question_content(array $questions_list, $question_id) {

        if(empty($questions_list)) {
            return;
        }

        $data = [];

        foreach($questions_list as $key => $question) {
            if($question->item_id == $question_id) {
                $data = [
                    "key" => $key,
                    "question" => $question
                ];
                break;
            }
        }

        return $data;
    }

    /**
     * Save the user answer and get the next question
     * 
     * Load the existing information (if any) in assignments_answers table. Append to the array list
     * 
     * Set the $session->nextQuestionId as the $session->currentQuestionId
     * 
     * Once done, get the details of the next question to display on the page.
     * 
     * @param String    $params->question_id
     * @param Array     $answers
     * 
     * @return Array
     */
    public function save_answer(stdClass $params) {
        
        // get the question data
        $question_info = $this->pushQuery(
            "a.*", "assignments_questions a", 
            "a.item_id='{$params->question_id}' AND a.client_id='{$params->clientId}' LIMIT 1"
        );

        // if the question information is not empty
        if(empty($question_info)) {
           return ["code" => 203, "data" => "Sorry! An invalid question id was parsed."];
        }
        $data = $question_info[0];

        // jump if no answer was supplied
        if(isset($params->answers)) {

            // convert the answers into an array
            $answers = $this->stringToArray($params->answers);

            // get the answer type
            if($data->answer_type == "option" && count($answers) > 1) {
                return ["code" => 203, "data" => "Sorry! This question requires a single answer. Multiple answers were given"];
            }

            // append to the correct answer if the answer_type is multiple
            if($data->answer_type == "multiple") {
                $data->correct_answer = "match::{$data->correct_answer}";
            }

            // load the existing user record (if any)
            $answer_info = $this->pushQuery(
                "*", "assignments_answers", 
                "assignment_id='{$data->assignment_id}' AND student_id = '{$params->userId}' AND client_id='{$params->clientId}' LIMIT 1"
            );

            // create a new object of the answers mechanism
            $answerClass = load_class("answers", "controllers");
            $clean_answer = implode(",", $answers);

            // if empty the form a new string of data
            if(empty($answer_info)) {

                // check the answer
                $checker = $answerClass->answerMechanism($clean_answer, $data->correct_answer);
                $score = $checker == "correct" ? $data->marks : 0;

                // generate a array of the question
                $the_answer = [
                    [
                        "question_id" => $params->question_id,
                        "answer" => $clean_answer,
                        "date_answered" => date("Y-m-d h:iA"),
                        "assigned_mark" => $data->marks,
                        "status" => $answerClass->answerMechanism($clean_answer, $data->correct_answer)
                    ]
                ];

                // insert the user answer information
                $stmt = $this->db->prepare("INSERT INTO assignments_answers SET client_id =?, assignment_id = ?, student_id = ?, answers = ?, scores = ?");
                $stmt->execute([$params->clientId, $data->assignment_id, $params->userId, json_encode($the_answer), $score]);

            } else {
                // convert the answers into an array
                $existing_answer = json_decode($answer_info[0]->answers, true);

                // check the answer
                $checker = $answerClass->answerMechanism($clean_answer, $data->correct_answer);

                // add to the existing scroe
                $score = $checker == "correct" ? ($answer_info[0]->scores + $data->marks) : $answer_info[0]->scores;

                // generate a array of the question
                $the_answer = [
                    "question_id" => $params->question_id,
                    "answer" => $clean_answer,
                    "assigned_mark" => $data->marks,
                    "date_answered" => date("Y-m-d h:iA"),
                    "status" => $answerClass->answerMechanism($clean_answer, $data->correct_answer)
                ];

                // init
                $the_key = null;

                // loop through the record to ascertain whether the record already exist
                // replace it if it exists
                foreach($existing_answer as $key => $this_answer) {
                    if($this_answer["question_id"] == $params->question_id) {
                        $the_key = [
                            "key" => $key
                        ];
                        break;
                    }
                }
                // append to the array list if $the_key value is empty
                if(empty($the_key)) {
                    array_push($existing_answer, $the_answer);
                } else {
                    // replace the value for the specific key
                    $existing_answer[$the_key["key"]] = $the_answer;
                }

                // update the existing record
                $stmt = $this->db->prepare("UPDATE assignments_answers SET answers = ? WHERE client_id =? AND assignment_id = ? AND student_id = ? LIMIT 1");
                $stmt->execute([json_encode($existing_answer), $params->clientId, $data->assignment_id, $params->userId]);
            }

        }

        // end the query if save only is set
        if(isset($params->save_answer_only)) {
            return "Yes it was saved only";
        }
        
        // set the nextQuestionId as the currentQuestionId
        $this->session->currentQuestionId = isset($params->previous_id) ? $params->previous_id : $this->session->nextQuestionId;

        // parameters to load the assignment information
        $para = (object) [
            "clientId" => $params->clientId,
            "columns" => "a.*",
            "assignment_id" => $data->assignment_id
        ];
        
        // get the questions array list
        $questions_array_list = $this->questions_list($para);
        
        // return the response
        return [
            "data" => $this->current_question($questions_array_list, $params->userId)
        ];
        
    }

    /**
     * Log The Assessment Record of An Outstanding Assessment
     * 
     * @return Array
     */
    public function prepare_assessment(stdClass $params) {

        try {

            // global variable
            global $usersClass;

            // check the overall_score
            if(isset($params->overall_score) && !preg_match("/^[0-9]+$/", $params->overall_score)) {
                return ["code" => 203, "data" => "Sorry! The overall score must be a numeric integer."];
            }

            // get the id equivalent of the class id
            if(isset($params->class_id) && !preg_match("/^[0-9]+$/", $params->class_id)) {
                $params->class_id = $this->pushQuery("id", "classes", "item_id='{$params->class_id}' LIMIT 1")[0]->id ?? null;
            }

            // confirm the course information
            $course_id = $this->pushQuery("id", "courses", "item_id='{$params->course_id}' LIMIT 1")[0]->id ?? null;

            // if the course id is empty
            if(empty($course_id)) {
                return ["code" => 203, "data" => "Sorry! An invalid course id was submitted."];
            }

            // load the students list based on the class id parsed
            $users_list = $this->pushQuery("item_id, name, image, unique_id","users", 
                "client_id='{$params->clientId}' AND class_id='{$params->class_id}' AND academic_term='{$params->academic_term}' AND
                academic_year='{$params->academic_year}' AND user_type='student' AND user_status='Active'"
            );

            return [
                "students_list" => $users_list,
                "parsed_data" => [
                    "assessment_title" => $params->assessment_title,
                    "assessment_type" => $params->assessment_type,
                    "class_id" => $params->class_id,
                    "course_id" => $params->course_id,
                    "overall_score" => $params->overall_score,
                    "date_due" => $params->date_due ?? date("Y-m-d"),
                    "time_due" => $params->time_due ?? date("H:i")
                ]
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Save the Marks Obtained by Each Student
     * 
     * @param Array $params->data           An array of the assessment log data
     * @param Array $params->students_list  An array of the list of students with their marks
     * 
     * @return Array
     */
    public function save_assessment(stdClass $params) {

        // confirm that the data is an array
        if(!is_array($params->data)) {
            return ["code" => 203, "data" => "Sorry! The data parameter must be an array."];
        }

        // confirm that the students_list is an array
        if(!is_array($params->students_list)) {
            return ["code" => 203, "data" => "Sorry! The students_list parameter must be an array."];
        }

        // confirm that all the parameters are extent
        $required = ["assessment_title", "assessment_type", "class_id", "course_id", "date_due", "time_due", "overall_score", "mode"];
        $parsed = array_keys($params->data);
        $not_found = [];

        // confirm if all the keys required was parsed
        foreach($required as $key) {
            if(!in_array($key, $parsed)) {
                $not_found[] = $key;
            }
        }

        // assigned_to
        $item_id = isset($data["assignment_id"]) ? $data["assignment_id"] : random_string("alnum", 32);
        $assigned_to = "selected_students";
        $students_list = $params->students_list;
        $data = $params->data;
        $update_log = $data["assignment_id"] ?? null;

        $state = $data["mode"] == "close" ? "Closed" : "Graded";

        // return error
        if(!empty($not_found)) {
            return ["code" => 203, "data" => "Sorry! The variables ".implode(" | ", $not_found)." was not parsed."];
        }

        try {

            // insert the record into the database
            $stmt = $this->db->prepare("INSERT INTO assignments SET
               item_id = ?, client_id = ?, assignment_group = ?, assigned_to = ?, assigned_to_list = ?, course_tutor = ?,
               course_id = ?, class_id = ?, grading = ?, assignment_title = ?, assignment_description = ?,
               insertion_mode = ?, created_by = ?, due_date = ?, due_time = ?, state = ?, date_published = now(), 
               academic_year = ?, academic_term = ?
            ");
            $stmt->execute([
                $item_id, $params->clientId, $data["assessment_type"], $assigned_to, 
                json_encode(array_keys($students_list)),
                $params->userId, $data["course_id"], $data["class_id"], 
                $data["overall_score"], $data["assessment_title"], $data["assessment_description"] ?? null, 
                "Manual", $params->userId, $data["date_due"], $data["time_due"],
                $state, $params->academic_year, $params->academic_term
            ]);

            // set the query string
            $insert = $this->db->prepare("INSERT INTO assignments_submitted SET client_id = ?, assignment_id = ?,
            student_id = ?, score = ?, graded = ?, handed_in = ?, date_graded = now()");

            $update = $this->db->prepare("UPDATE assignments_submitted SET score = ?, graded = ?, 
                handed_in = ?, date_graded = now()
                WHERE client_id = ? AND assignment_id = ? AND student_id = ? LIMIT 1
            ");

            // insert the student marks data
            foreach($students_list as $student_id => $student) {
                if($update_log) {
                    $update->execute([$student["score"], 1, "Submitted", $params->clientId, $item_id, $student_id]);
                } else {
                    $insert->execute([$params->clientId, $item_id, $student_id, $student["score"], 1, "Submitted"]);
                }
            }

            // set the output to return when successful
            $return = ["code" => 200, "data" => "Assignment successfully created.", "refresh" => 2000];
			
			// append to the response
			$return["additional"] = ["clear" => true];

            // if the request is to add a quiz
            $return["additional"]["href"] = "{$this->baseUrl}update-assessment/{$item_id}";

			// return the output
            return $return;

        } catch(PDOException $e) {}

    }
    
}
?>