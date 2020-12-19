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

        $client_data = $this->client_data($params->clientId);

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $client_data->client_preferences->academics->academic_year;
        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $client_data->client_preferences->academics->academic_term;

        $params->query .= " AND a.academic_year='{$params->academic_year}'";
        $params->query .= " AND a.academic_term='{$params->academic_term}'";
        $params->query .= (isset($params->year_id)) ? " AND a.year_id='{$params->year_id}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->deadline_date)) ? " AND a.deadline_date='{$params->deadline_date}'" : null;
        $params->query .= (isset($params->assignment_id)) ? " AND a.assignment_id='{$params->assignment_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*, (SELECT name FROM classes WHERE classes.id = a.class_id LIMIT 1) AS class_name,
                (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM assignments a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["created_by_info"] as $each) {
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
}
?>