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

        $params->query = "1";

        $filesObject = load_class("forms", "controllers");
        $client_data = $this->client_data($params->clientId);

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $client_data = $this->client_data($this->clientId);
        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $client_data->client_preferences->academics->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $client_data->client_preferences->academics->academic_year;
        
        // append the course tutor if the user_type is teacher
        if($params->userData->user_type == "teacher") {
            $params->course_tutor = $params->userId;
        }

        // append the class_id if the user type is student
        if($params->userData->user_type == "student") {
            $params->class_id = $params->userData->class_id;
        }

        $params->query .= " AND a.academic_year='{$params->academic_year}'";
        $params->query .= " AND a.academic_term='{$params->academic_term}'";
        $params->query .= (isset($params->class_id)) ? " AND a.class_id='{$params->class_id}'" : null;
        $params->query .= (isset($params->due_date)) ? " AND a.due_date='{$params->due_date}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->course_id)) ? " AND a.course_id='{$params->course_id}'" : null;
        $params->query .= (isset($params->assignment_id)) ? " AND a.item_id='{$params->assignment_id}'" : null;
        $params->query .= (isset($params->course_tutor)) ? " AND a.course_tutor LIKE '%{$params->course_tutor}%'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*, 
                (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                (SELECT name FROM classes WHERE classes.id = a.class_id LIMIT 1) AS class_name,
                (SELECT name FROM courses WHERE courses.id = a.course_id LIMIT 1) AS course_name,
                (SELECT COUNT(*) FROM users c WHERE c.client_id=a.client_id AND a.class_id=c.class_id AND c.user_type='student' AND c.user_status='Active' AND c.status='1') AS students_assigned,
                (SELECT COUNT(*) FROM assignments_submitted c WHERE c.assignment_id=a.item_id AND c.graded='1') AS students_graded,
                (SELECT COUNT(*) FROM assignments_submitted	c WHERE c.assignment_id=a.item_id AND c.graded='0') AS students_handed_in,
                (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.course_tutor LIMIT 1) AS course_tutor_info,
                (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM assignments a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // clean the assignment description
                $result->assignment_description = custom_clean(htmlspecialchars_decode($result->assignment_description));

                // count the number of students assigned to
                $result->students_assigned = ($result->assigned_to === "selected_students") ? count($this->stringToArray($result->assigned_to_list)) : $result->students_assigned;

                // if attachment variable was parsed
                $result->attachment = json_decode($result->attachment);
                $result->attachment_html = $filesObject->list_attachments($result->attachment->files, $result->created_by, "col-lg-6 col-md-6", false, false);

                // loop through the information
                foreach(["created_by_info", "course_tutor_info"] as $each) {
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
     * Add a new assignment
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {
        /** Confirm the class id */
        if(empty($this->pushQuery("id", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1'"))) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was submitted"];
        }

        /** Confirm the selected course */
        $course_data = $this->pushQuery("id, course_tutor", "courses", "id='{$params->course_id}' AND class_id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1'");
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
        $is_attach = (bool) ($params->question_set_type == "file_attachment");

        try {
            
            /** Move any uploaded files */
            if($is_attach) {
                // unset the session if already set
                $this->session->remove("assignment_uploadID");
                
                // create a new object and prepare/move attachments
                $attachments = load_class("files", "controllers")->prep_attachments("assignments", $params->userId, $item_id);
                
                // insert the record if not already existing
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                $files->execute(["assignments", $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"]]);
            } else {
                // set the assignment id into a session
                $this->session->assignment_uploadID = $item_id;
            }

            /** Insert the record */
            $stmt = $this->db->prepare("
                INSERT INTO assignments SET client_id = ?, created_by = ?, item_id = '{$item_id}'
                ".(isset($params->assignment_type) ? ", assignment_type = '{$params->assignment_type}'" : null)."
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
            ");
            $stmt->execute([$params->clientId, $params->userId]);

            // log the user activity
            $this->userLogs("assignments", $item_id, null, "{$params->userData->name} created a new Assignment: {$params->assignment_title}", $params->userId);

            # set the output to return when successful
            $return = ["code" => 200, "data" => "Assignment successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true];

			// return the output
            return $return;

        } catch(PDOException $e) {}

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
        $result["courses_list"] = $this->pushQuery("id, item_id, name", "courses", "class_id='{$params->class_id}' AND status='1'"); 

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

        // now loop through the list again and insert the user record
        foreach($params->student_list as $student) {
            // explode each student record
            $exp = explode("|", $student);
            $mark = $exp[1];
            $student_id = $exp[0];
            // insert the data into the database
            if(!empty($mark)) {
                // check if the record exits
                $mark_check = $this->confirm_student_marked($params->assignment_id, $student_id);
                if($mark_check) {
                    // update the record if it already exists
                    $stmt = $this->db->prepare("UPDATE assignments_submitted SET score=?, graded=?, date_graded=now() WHERE student_id=? AND assignment_id = ? LIMIT 1");
                    $stmt->execute([$mark, 1, $student_id, $params->assignment_id]);
                    // log the user activity
                    if($mark_check->score !== $mark) {
                        // Record the user activity
                        $this->userLogs("assignment-grade", "{$params->assignment_id}_{$student_id}", null, "{$params->userData->name} graded the student: {$mark}", $params->userId);
                    }
                } else {
                    // insert the new record since it does not exist
                    $stmt = $this->db->prepare("INSERT INTO assignments_submitted SET client_id = ?, score=?, graded=?, date_graded=now(), student_id=?, assignment_id = ?");
                    $stmt->execute([$params->clientId, $mark, 1, $student_id, $params->assignment_id]);
                    // Record the user activity
                    $this->userLogs("assignment-grade", "{$params->assignment_id}_{$student_id}", null, "{$params->userData->name} graded the student: {$mark}", $params->userId);
                }
            }
        }

        return ["data" => "Marks were successfully awarded to the list of students specified."];
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
        $stmt = $this->db->prepare("SELECT description FROM files_attachment WHERE resource = ? AND record_id = ? AND created_by = ? LIMIT 1");
        $stmt->execute(["assignment_doc", $params->assignment_id, $params->student_id]);
        $counter = $stmt->rowCount();

        // data
        $data = "<div class='alert mt-3 alert-info'>No attached files</div>";
        
        // if results was found
        if($counter) {
            // get the result
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

            }
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
		$stmt = $this->db->prepare("SELECT score FROM assignments_submitted WHERE student_id = ? AND assignment_id=? LIMIT 1");
		$stmt->execute([$student_id, $assignmentId]);

		// count the number of rows found
		return ($stmt->rowCount() > 0) ? $stmt->fetch(PDO::FETCH_OBJ) : false;
	}
}
?>