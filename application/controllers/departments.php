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

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->department_head)) ? " AND a.department_head='{$params->department_head}'" : null;
        $params->query .= (isset($params->created_by)) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->department_id)) ? " AND a.id='{$params->department_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status = 'Active' AND b.deleted='0' AND b.user_type='student' AND b.department = a.id AND b.client_id = a.client_id) AS students_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.department_head LIMIT 1) AS department_head_info
                FROM departments a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["department_head_info", "created_by_info"] as $each) {
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
     * Add new department record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {
 
        // create a new department code
        if(isset($params->department_code) && !empty($params->department_code)) {
            // replace any empty space with 
            $params->department_code = str_replace("/^[\s]+$/", "", $params->department_code);
            // confirm if the department code already exist
            if(!empty($this->pushQuery("id, name", "departments", "status='1' AND client_id='{$params->clientId}' AND department_code='{$params->department_code}'"))) {
                return ["code" => 203, "data" => "Sorry! There is an existing Department with the same code."];
            }
        } else {
            // generate a new department code
            $counter = $this->append_zeros(($this->itemsCount("departments", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
            $params->department_code = $this->client_data($params->clientId)->client_preferences->labels->{"department_label"}.$counter;
        }

        // convert the code to uppercase
        $params->department_code = strtoupper($params->department_code);
        
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
                INSERT INTO departments SET client_id = ?, created_by = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($fileName) ? ", image='{$fileName}'" : null)."
                ".(isset($params->department_code) ? ", department_code = '{$params->department_code}'" : null)."
                ".(isset($params->department_head) ? ", department_head = '{$params->department_head}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);
            
            // log the user activity
            $this->userLogs("departments", $this->lastRowId("departments"), null, "{$params->userData->name} created a new Department: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Department successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true];

			// return the output
            return $return;

        } catch(PDOException $e) {} 

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

        } catch(PDOException $e) {

        } 

    }

    
}
?>