<?php 

class Sections extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * List Sections
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {


        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= !empty($params->q) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= !empty($params->section_leader) ? " AND a.section_leader='{$params->section_leader}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->section_id) ? " AND a.id='{$params->section_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' AND b.section = a.id AND b.client_id = a.client_id AND b.user_type = 'student' 
                        AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)}
                    ) AS students_count,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' AND b.section = a.id AND b.client_id = a.client_id AND b.gender='Male'
                        AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)}
                    ) AS students_male_count,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' AND b.section = a.id AND b.client_id = a.client_id AND b.gender='Female'
                        AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)}
                    ) AS students_female_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.section_leader LIMIT 1) AS section_leader_info
                FROM sections a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                $result->description = clean_html($result->description);
                
                // loop through the information
                foreach(["section_leader_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","user_type"]);
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
     * Add new department record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        // get the default client data
        global $defaultClientData;

        // create a new department code
        if(isset($params->section_code) && !empty($params->section_code)) {
            // replace any empty space with 
            $params->section_code = str_replace("/^[\s]+$/", "", $params->section_code);
            // confirm if the department code already exist
            if(!empty($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$params->clientId}' AND section_code='{$params->section_code}'"))) {
                return ["code" => 400, "data" => "Sorry! There is an existing Section with the same code."];
            }
        } else {
            // generate a new department code
            $counter = $this->append_zeros(($this->itemsCount("sections", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
            $params->section_code = $defaultClientData->client_preferences->labels->{"section_label"}.$counter;
        }

        // confirm that the color code is valid
        if(!empty($params->color_code) && !in_array($params->color_code, color_code_picker(null, true))) {
            return ["code" => 400, "data" => "Sorry! An invalid color code was supplied."];
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

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO sections SET client_id = ?, created_by = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($fileName) ? ", image='{$fileName}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->section_code) ? ", section_code = '{$params->section_code}'" : null)."
                ".(isset($params->section_leader) ? ", section_leader = '{$params->section_leader}'" : null)."
                ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(!empty($params->color_code) ? ", color_code = '{$params->color_code}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);
            
            // log the user activity
            $this->userLogs("sections", $this->lastRowId("sections"), null, "{$params->userData->name} created a new Section: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Section successfully created.", "refresh" => 2000];
			
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

            // get global variable data
            global $defaultClientData;

            // old record
            $prevData = $this->pushQuery("*", "sections", "id='{$params->section_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // create a new class code
            if(isset($params->section_code) && !empty($params->section_code) && ($prevData[0]->section_code !== $params->section_code)) {
                // replace any empty space with 
                $params->section_code = str_replace("/^[\s]+$/", "", $params->section_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$params->clientId}' AND section_code='{$params->section_code}' LIMIT 1"))) {
                    return ["code" => 400, "data" => "Sorry! There is an existing Section with the same code."];
                }
            } elseif(empty($prevData[0]->section_code) || !isset($params->section_code)) {
                // generate a new class code
                $counter = $this->append_zeros(($this->itemsCount("sections", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $params->section_code = $defaultClientData->client_preferences->labels->{"section_label"}.$counter;
            }

            // confirm that the color code is valid
            if(!empty($params->color_code) && !in_array($params->color_code, color_code_picker(null, true))) {
                return ["code" => 400, "data" => "Sorry! An invalid color code was supplied."];
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
                UPDATE sections SET date_updated = now()
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($fileName) ? ", image='{$fileName}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->section_code) ? ", section_code = '{$params->section_code}'" : null)."
                ".(isset($params->section_leader) ? ", section_leader = '{$params->section_leader}'" : null)."
                ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(!empty($params->color_code) ? ", color_code = '{$params->color_code}'" : null)."
                WHERE id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([$params->section_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("sections", $params->section_id, $prevData[0], "{$params->userData->name} updated the Section: {$prevData[0]->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Section successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}section/{$params->section_id}/update"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 
        
    }
    

    /**
     * Assign Section to Students List
     * 
     * @param Array     $params->data
     * @param Array     $params->data["assign_fees"]
     * @param Array     $params->data["class_id"]
     * @param String    $params->data["section_id"]
     * @param Array     $params->data["student_id"]
     * 
     * @return Array
     */
    public function assign(stdClass $params) {

        try {

            global $accessObject;

            if(!$accessObject->hasAccess("assign_section", "settings")) {
                return ["code" => 400, "data" => $this->permission_denied];
            }

            // confirm that the variable is an array
            if(empty($params->data) && !is_array($params->data)) {
                return ["code" => 400, "data" => "Sorry! The data array must be a valid array."];
            }

            // confirm that the section id was parsed
            if(!isset($params->data["section_id"])) {
                return ["code" => 400, "data" => "Sorry! Ensure that the section id was parsed."];
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
            $dcheck = $this->pushQuery("id, name", "sections", "id='{$params->data["section_id"]}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            if(empty($dcheck)) {
                return ["code" => 400, "data" => "Sorry! An invalid section_id was supplied."];
            }

            // update class department
            $update = $this->db->prepare("UPDATE classes SET section_id = ? WHERE id = ? AND client_id = ? LIMIT 1");
            $update->execute([$params->data["section_id"], $params->data["class_id"], $params->clientId]);

            // loop through the students list
            if(isset($params->data["student_id"])) {

                // confirm that the student id is an array
                if(!is_array($params->data["student_id"])) {
                    return ["code" => 400, "data" => "Sorry! Ensure that the student id parsed is a valid array."];
                }

                // update query
                $update = $this->db->prepare("UPDATE users SET section = ? WHERE id = ? AND client_id = ? AND user_type = ? LIMIT 1");

                // loop through the students list
                foreach($params->data["student_id"] as $student) {
                    // execute the update statement
                    $update->execute([$params->data["section_id"], $student, $params->clientId, "student"]);
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