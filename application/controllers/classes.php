<?php 

class Classes extends Myschoolgh {

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

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->class_teacher)) ? " AND a.class_teacher='{$params->class_teacher}'" : null;
        $params->query .= (isset($params->class_assistant)) ? " AND a.class_assistant='{$params->class_assistant}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->department_id)) ? " AND a.department_id='{$params->department_id}'" : null;
        $params->query .= (isset($params->class_id)) ? " AND a.id='{$params->class_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT ".(isset($params->columns) ? $params->columns : " a.*,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status = 'Active' AND b.deleted='0' AND b.user_type='student' AND b.class_id = a.id AND b.client_id = a.client_id) AS students_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.class_assistant LIMIT 1) AS class_assistant_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.class_teacher LIMIT 1) AS class_teacher_info
                    ")."
                FROM classes a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["class_teacher_info", "class_assistant_info", "created_by_info"] as $each) {
                    // confirm that it is set
                    if(isset($result->{$each})) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
                    }
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

        try {

            // create a new class code
            if(isset($params->class_code) && !empty($params->class_code)) {
                // replace any empty space with 
                $params->class_code = str_replace("/^[\s]+$/", "", $params->class_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$params->clientId}' AND class_code='{$params->class_code}'"))) {
                    return ["code" => 203, "data" => "Sorry! There is an existing Class with the same code."];
                }
            } else {
                // generate a new class code
                $counter = $this->append_zeros(($this->itemsCount("classes", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $params->class_code = $this->client_data($params->clientId)->client_preferences->labels->{"class_label"}.$counter;
            }

            // convert the code to uppercase
            $params->class_code = strtoupper($params->class_code);

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO classes SET client_id = ?, created_by = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->class_code) ? ", class_code = '{$params->class_code}'" : null)."
                ".(isset($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(isset($params->class_teacher) ? ", class_teacher = '{$params->class_teacher}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->class_assistant) ? ", class_assistant = '{$params->class_assistant}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);
            
            // log the user activity
            $this->userLogs("classes", $this->lastRowId("classes"), null, "{$params->userData->name} created a new Class: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Class successfully created.", "refresh" => 2000];
			
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

            // old record
            $prevData = $this->pushQuery("*", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // create a new class code
            if(isset($params->class_code) && !empty($params->class_code) && ($prevData[0]->class_code !== $params->class_code)) {
                // replace any empty space with 
                $params->class_code = str_replace("/^[\s]+$/", "", $params->class_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$params->clientId}' AND class_code='{$params->class_code}'"))) {
                    return ["code" => 203, "data" => "Sorry! There is an existing Class with the same code."];
                }
            } elseif(empty($prevData[0]->class_code) || !isset($params->class_code)) {
                // generate a new class code
                $counter = $this->append_zeros(($prevData[0]->id), $this->append_zeros);
                $params->class_code = $this->client_data($params->clientId)->client_preferences->labels->{"class_label"}.$counter;
            }

            // convert the code to uppercase
            $params->class_code = strtoupper($params->class_code);

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE classes SET date_updated = now()
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->class_code) ? ", class_code = '{$params->class_code}'" : null)."
                ".(isset($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(isset($params->class_teacher) ? ", class_teacher = '{$params->class_teacher}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->class_assistant) ? ", class_assistant = '{$params->class_assistant}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
                WHERE id = ? AND client_id = ?
            ");
            $stmt->execute([$params->class_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("classes", $params->class_id, $prevData[0], "{$params->userData->name} updated the Class: {$prevData[0]->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Class successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}update-class/{$params->class_id}/update"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 
        
    }

    
}
?>