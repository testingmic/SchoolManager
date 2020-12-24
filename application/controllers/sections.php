<?php 

class Sections extends Myschoolgh {

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
        $params->query .= (isset($params->section_leader)) ? " AND a.section_leader='{$params->section_leader}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->section_id)) ? " AND a.id='{$params->section_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status = 'Active' AND b.deleted='0' AND b.user_type='student' AND b.department = a.id AND b.client_id = a.client_id) AS students_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.section_leader LIMIT 1) AS section_leader_info
                FROM sections a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["section_leader_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
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

        // create a new department code
        if(isset($params->section_code) && !empty($params->section_code)) {
            // replace any empty space with 
            $params->section_code = str_replace("/^[\s]+$/", "", $params->section_code);
            // confirm if the department code already exist
            if(!empty($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$params->clientId}' AND section_code='{$params->section_code}'"))) {
                return ["code" => 203, "data" => "Sorry! There is an existing Section with the same code."];
            }
        } else {
            // generate a new department code
            $counter = $this->append_zeros(($this->itemsCount("sections", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
            $params->section_code = $this->client_data($params->clientId)->client_preferences->labels->{"section_label"}.$counter;
        }

        // convert the code to uppercase
        $params->section_code = strtoupper($params->section_code);
        
        // confirm that a logo was parsed
        if(isset($params->image)) {
            // set the upload directory
            $uploadDir = "assets/img/posts/";
            // File path config 
            $fileName = basename($params->image["name"]); 
            $targetFilePath = $uploadDir . $fileName; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg');            
            // check if its a valid image
            if(!empty($fileName) && in_array($fileType, $allowTypes)){
                // set a new filename
                $fileName = $uploadDir . random_string('alnum', 25)."__{$fileName}";
                // Upload file to the server 
                if(move_uploaded_file($params->image["tmp_name"], $fileName)){}
            }
        }

        try {

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO sections SET client_id = ?, created_by = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($fileName) ? ", image='{$fileName}'" : null)."
                ".(isset($params->section_code) ? ", section_code = '{$params->section_code}'" : null)."
                ".(isset($params->section_leader) ? ", section_leader = '{$params->section_leader}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
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

            // old record
            $prevData = $this->pushQuery("*", "sections", "id='{$params->section_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // create a new class code
            if(isset($params->section_code) && !empty($params->section_code) && ($prevData[0]->section_code !== $params->section_code)) {
                // replace any empty space with 
                $params->section_code = str_replace("/^[\s]+$/", "", $params->section_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "sections", "status='1' AND client_id='{$params->clientId}' AND section_code='{$params->section_code}'"))) {
                    return ["code" => 203, "data" => "Sorry! There is an existing Section with the same code."];
                }
            } elseif(empty($prevData[0]->section_code) || !isset($params->section_code)) {
                // generate a new class code
                $counter = $this->append_zeros(($this->itemsCount("sections", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $params->section_code = $this->client_data($params->clientId)->client_preferences->labels->{"section_label"}.$counter;
            }

            // convert the code to uppercase
            $params->section_code = strtoupper($params->section_code);

            // confirm that a logo was parsed
            if(isset($params->image)) {
                // set the upload directory
                $uploadDir = "assets/img/posts/";
                // File path config 
                $fileName = basename($params->image["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                // Allow certain file formats 
                $allowTypes = array('jpg', 'png', 'jpeg');            
                // check if its a valid image
                if(!empty($fileName) && in_array($fileType, $allowTypes)){
                    // set a new filename
                    $fileName = $uploadDir . random_string('alnum', 25)."__{$fileName}";
                    // Upload file to the server 
                    if(move_uploaded_file($params->image["tmp_name"], $fileName)){}
                }
            }

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE sections SET date_updated = now()
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($fileName) ? ", image='{$fileName}'" : null)."
                ".(isset($params->section_code) ? ", section_code = '{$params->section_code}'" : null)."
                ".(isset($params->section_leader) ? ", section_leader = '{$params->section_leader}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
                WHERE id = ? AND client_id = ?
            ");
            $stmt->execute([$params->section_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("sections", $params->section_id, $prevData[0], "{$params->userData->name} updated the Section: {$prevData[0]->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Section successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}update-section/{$params->section_id}/update"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 
        
    }

    
}
?>