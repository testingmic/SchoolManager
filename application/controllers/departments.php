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
                    $result->{$each} = (object) $this->stringToArray($each, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);    
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

        try {

        } catch(PDOException $e) {

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

        } catch(PDOException $e) {

        } 

    }

    
}
?>