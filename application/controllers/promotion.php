<?php
class Promotion extends Myschoolgh {

    public function __construct(stdClass $params = null) {
        parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? null;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;
    }

    /**
     * List all the promotions history
     * 
     * 
     * @return Array
     */
    public function history(stdClass $params) {

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query = "";
        $params->query .= (isset($params->status) && !empty($params->status)) ? " AND a.status = '{$params->status}'" : null;
        $params->query .= (isset($params->clientId) && !empty($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->history_id) && !empty($params->history_id)) ? " AND a.history_log_id = '{$params->history_id}'" : null;
        $params->query .= (isset($params->promote_to) && !empty($params->promote_to)) ? " AND a.promote_to = '{$params->promote_to}'" : null;
        $params->query .= (isset($params->promote_from) && !empty($params->promote_from)) ? " AND a.promote_from = '{$params->promote_from}'" : null;
        $params->query .= isset($params->academic_year) && !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
        $params->query .= isset($params->academic_term) && !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);

        try {

            // prepare and execute the request for the history log
            $stmt = $this->db->prepare("SELECT a.*,
                    (SELECT name FROM classes WHERE classes.item_id = a.promote_from LIMIT 1) AS from_class_name,
                    (SELECT name FROM classes WHERE classes.item_id = a.promote_to LIMIT 1) AS to_class_name,
                    (SELECT COUNT(*) FROM users WHERE users.class_id = c.id) AS students_count,
                    (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.logged_by LIMIT 1) AS logged_by_data
                FROM promotions_history a
                LEFT JOIN classes c ON c.item_id = a.promote_from
                WHERE 1 {$params->query} LIMIT {$params->limit}
            ");
            $stmt->execute();

            // append log
            $appendLog = (bool) isset($params->append_log);

            // perform a query query
            $params->quick_query = true;

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // convert the created by string into an object
                $result->logged_by_data = (object) $this->stringToArray($result->logged_by_data, "|", ["unique_id", "user_id", "name", "image", "user_type"]);

                // if append log was parsed
                if($appendLog) {
                    $params->history_id = $result->history_log_id;
                    $result->promotion_log = $this->log($params)["data"];
                }
                $data[] = $result;
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
     * List all the promotions log
     * 
     * This relates to all students that have been promoted for the entire academic year as parsed
     * 
     * @return Array
     */
    public function log(stdClass $params) {

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query = "";
        $params->query .= (isset($params->history_id) && !empty($params->history_id)) ? " AND a.history_log_id = '{$params->history_id}'" : null;

        // run this section if the quick_query parameter was not parsed
        if(!isset($params->quick_query)) {
            $params->query .= (isset($params->clientId) && !empty($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
            $params->query .= (isset($params->student_id) && !empty($params->student_id)) ? " AND a.student_id LIKE '%{$params->student_id}%'" : null;
            $params->query .= (isset($params->promote_to) && !empty($params->promote_to)) ? " AND a.promote_to = '{$params->promote_to}'" : null;
            $params->query .= (isset($params->promote_from) && !empty($params->promote_from)) ? " AND a.promote_from = '{$params->promote_from}'" : null;
            $params->query .= isset($params->academic_year) && !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
            $params->query .= isset($params->academic_term) && !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);
        }

        try {
            
            // prepare and execute the request for the students promotion log
            $stmt = $this->db->prepare("SELECT a.*,
                    u.unique_id, u.firstname, u.lastname, u.name, 
                    u.image, u.gender, u.enrollment_date, u.email, u.date_of_birth,
                    (SELECT name FROM classes WHERE classes.item_id = a.promote_from LIMIT 1) AS from_class_name,
                    (SELECT name FROM classes WHERE classes.item_id = a.promote_to LIMIT 1) AS to_class_name,
                (SELECT CONCAT(b.unique_id,'|',b.item_id,'|',b.name,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.promoted_by LIMIT 1) AS logged_by_data
                FROM promotions_log a
                LEFT JOIN users u ON u.item_id = a.student_id
                WHERE 1 {$params->query} LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // confirm if the student was promoted
                $result->is_promoted = (int) $result->is_promoted;

                // convert the created by string into an object
                $result->logged_by_data = (object) $this->stringToArray($result->logged_by_data, "|", ["unique_id", "user_id", "name", "image", "user_type"]);
                
                // append the array
                $data[] = $result;
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
     * List Students
     * 
     * @return Array
     */
    public function students(stdClass $params) {

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        
        // if the class id is empty
        if(empty($params->class_id)) {
            return ["code" => 201,"data" => $this->is_required("Class")];
        }

        // if the client data is parsed
        $academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;
        $academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;

        // if the client data is parsed
        $params->query = "";
        $params->query .= isset($params->clientId) && !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->class_id) ? " AND c.item_id = '{$params->class_id}'" : null;
        $params->query .= isset($params->student_id) && !empty($params->student_id) ? " AND a.item_id = '{$params->student_id}'" : null;
        $params->query .= !empty($academic_year) ? " AND a.academic_year='{$academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
        $params->query .= !empty($academic_term) ? " AND a.academic_term='{$academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);

        try {
            
            // prepare and execute the request for the students list
            $stmt = $this->db->prepare("SELECT a.item_id, a.unique_id, a.firstname, a.lastname, a.name, 
                    a.image, a.gender, a.enrollment_date, a.email, a.date_of_birth, a.class_id, c.name AS class_name,
                    (
                        SELECT b.is_promoted FROM promotions_log b 
                        WHERE 
                            b.student_id = a.item_id AND 
                            b.academic_term = '{$academic_year}' AND b.academic_year = '{$academic_term}'
                        LIMIT 1
                    ) AS is_promoted
                FROM users a
                LEFT JOIN classes c ON c.id = a.class_id
                WHERE 1 {$params->query} AND a.user_type = ? LIMIT {$params->limit}
            ");
            $stmt->execute(["student"]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                $result->is_promoted = empty($result->is_promoted) ? 0 : (int) $result->is_promoted;
                $data[$result->item_id] = $result;
            }

            // confirm if the promotions list was parsed
            $params->limit = 1;

            // set the students list in an array
            $response["students_list"] = $data;

            // run this section if the no_promotion_log was parsed
            if(!isset($params->no_promotion_log)) {
                $params->promote_from = $params->class_id;
                $params->status = "Processed";
                $response["promotion_log"] = (bool) !empty($this->history($params)["data"]);
            }

            return [
                "code" => 200,
                "data" => $response
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
    }

    /**
     * Promote Students
     * 
     * @return Array
     */
    public function promote(stdClass $params) {

        try {

            // if the class id is empty
            if(empty($params->promote_from) || empty($params->promote_to)) {
                return ["code" => 203, "data" => $this->is_required("Class to Promote From and To")];
            }
            // convert the students list into an array list
            $students_list = $this->stringToArray($params->students_list);

            // confirm that the class selected exists
            $from_class = $this->pushQuery("name", "classes", "item_id='{$params->promote_from}' AND client_id='{$params->clientId}' LIMIT 1");
            $to_class = $this->pushQuery("name", "classes", "item_id='{$params->promote_to}' AND client_id='{$params->clientId}' LIMIT 1");

            // if the class id is invalid
            if(empty($from_class) || empty($to_class)) {
                return ["code" => 203,"data" => "Please ensure a valid class id was supplied"];
            }

            // check if there is an existing record already
            $existing_record = $this->pushQuery("history_log_id, status", "promotions_history", 
                "academic_year='{$params->academic_year}' AND academic_term='{$params->academic_term}' AND 
                promote_from='{$params->promote_from}' AND promote_to='{$params->promote_to}' AND 
                client_id='{$params->clientId}' LIMIT 1
            ");

            // append some additional parameters
            $params->class_id = $params->promote_from;

            // get the students list from the database for the selected class id
            $students_data = $this->students($params)["data"];
            $class_students_array = $students_data["students_list"];

            // if the class has already been promoted
            if(!empty($existing_record) && ($existing_record[0]->status === "Processed")) {
                return ["code" => 203,"data" => "Sorry! This class has already been promoted. Hence cannot repeat the action."];
            }

            // get the existing promotion_log_id
            $promotion_log_id = !empty($existing_record) ? $existing_record[0]->history_log_id : null;
            
            // new students array list
            $query_list = "INSERT INTO `promotions_log` (`client_id`, `student_id`, `history_log_id`, `academic_year`, `academic_term`, `promote_from`, `promote_to`, `is_promoted`, `date_log`, `promoted_by`) VALUES";
            $not_found = false;
            $update_query = null;
            $students_array = [];
            $history_log_id = !empty($promotion_log_id) ? $promotion_log_id : random_string("alnum", 13);

            // compare the students list
            foreach($class_students_array as $student) {

                // push the user unique id in the array list
                array_push($students_array, $student->item_id);
                
                // set the is promoted data
                $is_promoted = in_array($student->item_id, $students_list) ? 1 : 0;

                // if there is an existing record then update the set
                $update_query .= "UPDATE `promotions_log` SET `promote_from`='{$params->promote_from}', `is_promoted`='{$is_promoted}', `promote_to`='".($is_promoted ? $params->promote_to : $params->promote_from)."' WHERE `history_log_id`='{$history_log_id}' AND `student_id`='{$student->item_id}' LIMIT 1;";

                // append to the query list
                $query_list .= "('{$params->clientId}', '{$student->item_id}', '{$history_log_id}', '{$params->academic_year}', '{$params->academic_term}', '{$params->promote_from}', '".($is_promoted ? $params->promote_to : $params->promote_from)."', '{$is_promoted}', now(), '{$params->userId}'),";
            }

            // loop through the students list to be promoted
            foreach($students_list as $student) {
                // if the student id was not in the array list
                if(!in_array($student, $students_array)) {
                    $not_found = true;
                    break;
                }
            }
            
            // return false if an invalid student id was parsed
            if($not_found) {
                return ["code" => 203, "data" => "Sorry! Ensure all students id parsed is valid."];
            }

            // trim the end
            $query_list = !empty($update_query) ? $update_query : trim($query_list, ",");

            // execute the query
            if($this->db->query($query_list)) {

                // log the user activity
                $this->userLogs("promotion", $history_log_id, null, "{$params->userData->name} ".(!empty($update_query) ? "Updated the promotion of students list." : "Promoted a list of students.")."", $params->userId);

                // log the history item
                if(!empty($update_query)) {
                    $this->db->query("UPDATE promotions_history SET 
                        `promote_from`='{$params->promote_from}', `promote_to`='{$params->promote_to}', `date_log` = now() 
                        WHERE `client_id`='{$params->clientId}' AND `history_log_id`='{$history_log_id}' LIMIT 1
                    ");
                } else {
                    $this->db->query("INSERT INTO promotions_history SET
                        `client_id`='{$params->clientId}', `history_log_id`='{$history_log_id}', 
                        `academic_year`='{$params->academic_year}', `academic_term`='{$params->academic_term}', 
                        `promote_from`='{$params->promote_from}', `promote_to`='{$params->promote_to}', `date_log` = now(), 
                        `logged_by`='{$params->userId}'
                    ");
                }

                // return the success response
                return "Students were successfully promoted.";
            } else {
                return ["code" => 203, "data" => "Sorry! There was an error while processing the request."];
            }
            
        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Cancel Promotion
     * 
     * @return Array
     */
    public function validate(stdClass $params) {

        try {

            // confirm that the record actually exists
            $history_log = $this->pushQuery("id", "promotions_history", "history_log_id='{$params->history_id}' AND client_id='{$params->clientId}' LIMIT 1");

            // if the class id is invalid
            if(empty($history_log)) {
                return ["code" => 203,"data" => "Sorry! An invalid promotion history log id was supplied"];
            }

            // update the log history record
            $stmt = $this->db->prepare("UPDATE promotions_history SET status = ? WHERE history_log_id = ? AND client_id = ? LIMIT 1");
            $stmt->execute(["Processed", $params->history_id, $params->clientId]);

            // log the user activity
            $this->userLogs("promotion", $params->history_id, null, "{$params->userData->name} validated the Student's Promotion Record", $params->userId);

            return [
                "data" => "The promotion log was successfully validated."
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
    }

    /**
     * Cancel Promotion
     * 
     * @return Array
     */
    public function cancel(stdClass $params) {

        try {

            // confirm that the record actually exists
            $history_log = $this->pushQuery("id", "promotions_history", "history_log_id='{$params->history_id}' AND client_id='{$params->clientId}' LIMIT 1");

            // if the class id is invalid
            if(empty($history_log)) {
                return ["code" => 203,"data" => "Sorry! An invalid promotion history log id was supplied"];
            }
            
            // update the students promotion list record
            $stmt = $this->db->prepare("UPDATE promotions_log SET is_promoted = ? WHERE history_log_id = ? AND client_id = ?");
            $stmt->execute([3, $params->history_id, $params->clientId]);

            // update the log history record
            $stmt = $this->db->prepare("UPDATE promotions_history SET status = ? WHERE history_log_id = ? AND client_id = ? LIMIT 1");
            $stmt->execute(["Cancelled", $params->history_id, $params->clientId]);

            // log the user activity
            $this->userLogs("promotion", $params->history_id, null, "{$params->userData->name} cancelled the Student's Promotion Record", $params->userId);

            return [
                "data" => "The promotion log was successfully cancelled."
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
    }

}