<?php 

class Departments extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
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

        $params->query .= !empty($params->q) ? " AND a.name='{$params->q}'" : null;
        $params->query .= !empty($params->department_head) ? " AND a.department_head='{$params->department_head}'" : null;
        $params->query .= !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->department_id) ? " AND a.id='{$params->department_id}'" : null;

        $isMinified = (bool) isset($params->quick_analitics_load);

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' AND b.department = a.id AND b.client_id = a.client_id
                        AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)}
                    ) AS students_count,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' AND b.department = a.id AND b.client_id = a.client_id AND b.gender='Male'
                        AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)}
                    ) AS students_male_count,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' AND b.department = a.id AND b.client_id = a.client_id AND b.gender='Female'
                        AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)}
                    ) AS students_female_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.department_head LIMIT 1) AS department_head_info
                FROM departments a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // if the minified is true
                if($isMinified) {
					$data[] = [
						"name" => $result->name,
						"students_count" => (int) $result->students_count
					];
				} else {
                	// loop through the information
                    foreach(["department_head_info", "created_by_info"] as $each) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
                    }

                    $data[] = $result;
				}

            }

            if($isMinified) {
            	return $data;
			} else {
				return [ "code" => 200, "data" => $data ];
			}

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Add new department record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        // global variable
        global $defaultClientData;
 
        // create a new department code
        if(isset($params->department_code) && !empty($params->department_code)) {
            // replace any empty space with 
            $params->department_code = str_replace("/^[\s]+$/", "", $params->department_code);
            // confirm if the department code already exist
            if(!empty($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$params->clientId}' AND department_code='{$params->department_code}'"))) {
                return ["code" => 400, "data" => "Sorry! There is an existing Department with the same code."];
            }
        } else {
            // generate a new department code
            $counter = $this->append_zeros(($this->itemsCount("departments", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
            $params->department_code = $defaultClientData->client_preferences->labels->{"department_label"}.$counter;
        }
        
        // confirm that a logo was parsed
        if(isset($params->image)) {
            // set the upload directory
            $uploadDir = "assets/img/gallery/";
            // File path config 
            $fileName = basename($params->image["name"]); 
            $targetFilePath = $uploadDir . $fileName; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            
            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg');

            // check if its a valid image
            if(!empty($fileName) && validate_image($params->image["tmp_name"])){
                // set a new filename
                $fileName = $uploadDir . random_string("alnum", RANDOM_STRING)."__{$fileName}";
                // Upload file to the server 
                if(move_uploaded_file($params->image["tmp_name"], $fileName)){}
            } else {
                $fileName = null;
            }
        }

        try {

            // generate a new string
            $item_id = random_string("alnum", RANDOM_STRING);

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO departments SET client_id = ?, created_by = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($fileName) ? ", image='{$fileName}'" : null)."
                ".(!empty($item_id) ? ", item_id='{$item_id}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->department_code) ? ", department_code = '{$params->department_code}'" : null)."
                ".(isset($params->department_head) ? ", department_head = '{$params->department_head}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);
            
            // log the user activity
            $this->userLogs("departments", $item_id, null, "{$params->userData->name} created a new Department: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Department successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update existing department record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        try {

            // get the default variable
            global $defaultClientData;

            // old record
            $prevData = $this->pushQuery("*", "departments", "id='{$params->department_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // create a new class code
            if(isset($params->department_code) && !empty($params->department_code) && ($prevData[0]->department_code !== $params->department_code)) {
                // replace any empty space with 
                $params->department_code = str_replace("/^[\s]+$/", "", $params->department_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$params->clientId}' AND department_code='{$params->department_code}' LIMIT 1"))) {
                    return ["code" => 400, "data" => "Sorry! There is an existing Department with the same code."];
                }
            } elseif(empty($prevData[0]->department_code) || !isset($params->department_code)) {
                // generate a new class code
                $counter = $this->append_zeros(($this->itemsCount("departments", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $params->department_code = $defaultClientData->client_preferences->labels->{"department_label"}.$counter;
            }

            // confirm that a logo was parsed
            if(isset($params->image)) {
                // set the upload directory
                $uploadDir = "assets/img/gallery/";
                // File path config 
                $fileName = basename($params->image["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                
                // Allow certain file formats 
                $allowTypes = array('jpg', 'png', 'jpeg');

                // check if its a valid image
                if(!empty($fileName) && validate_image($params->image["tmp_name"])){
                    // set a new filename
                    $fileName = $uploadDir . random_string("alnum", RANDOM_STRING)."__{$fileName}";
                    // Upload file to the server 
                    if(move_uploaded_file($params->image["tmp_name"], $fileName)){}
                } else {
                    $fileName = null;
                }
            }
            
            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE departments SET date_updated = now()
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($fileName) ? ", image='{$fileName}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->department_code) ? ", department_code = '{$params->department_code}'" : null)."
                ".(isset($params->department_head) ? ", department_head = '{$params->department_head}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
                WHERE id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([$params->department_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("departments", $params->department_id, $prevData[0], "{$params->userData->name} updated the Department: {$prevData[0]->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Department successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}department/{$params->department_id}/update"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Assign Department to Students List
     * 
     * @param Array     $params->data
     * @param Array     $params->data["assign_fees"]
     * @param Array     $params->data["class_id"]
     * @param Array     $params->data["student_id"]
     * 
     * @return Array
     */
    public function assign(stdClass $params) {

        try {

            // confirm that the variable is an array
            if(empty($params->data) && !is_array($params->data)) {
                return ["code" => 400, "data" => "Sorry! The data array must be a valid array."];
            }

            // confirm that the department id was parsed
            if(!isset($params->data["department_id"])) {
                return ["code" => 400, "data" => "Sorry! Ensure that the department id was parsed."];
            }

            // confirm that the class id was parsed
            if(!isset($params->data["class_id"])) {
                return ["code" => 400, "data" => "Sorry! Ensure that the class id was parsed."];
            }

            // confirm that the class is parsed
            $check = $this->pushQuery("id, name", "classes", "id='{$params->data["class_id"]}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            if(empty($check)) {
                return ["code" => 400, "data" => "Sorry! An invalid class id was supplied."];
            }

            // confirm that the class is parsed
            $dcheck = $this->pushQuery("id, name", "departments", "id='{$params->data["department_id"]}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            if(empty($dcheck)) {
                return ["code" => 400, "data" => "Sorry! An invalid department_id was supplied."];
            }

            // update class department
            $update = $this->db->prepare("UPDATE classes SET department_id = ? WHERE id = ? AND client_id = ? LIMIT 1");
            $update->execute([$params->data["department_id"], $params->data["class_id"], $params->clientId]);

            // loop through the students list
            if(isset($params->data["student_id"])) {

                // confirm that the student id is an array
                if(!is_array($params->data["student_id"])) {
                    return ["code" => 400, "data" => "Sorry! Ensure that the student id parsed is a valid array."];
                }

                // update query
                $update = $this->db->prepare("UPDATE users SET department = ? WHERE id = ? AND client_id = ? AND user_type = ? LIMIT 1");

                // loop through the students list
                foreach($params->data["student_id"] as $student) {
                    // execute the update statement
                    $update->execute([$params->data["department_id"], $student, $params->clientId, "student"]);
                }
            }

			// return the output
            return [
                "code" => 200, 
                "data" => "Students of {$check[0]->name} were successfully assigned to {$dcheck[0]->name}."
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 
    }
    
}
?>