<?php 

class Incidents extends Myschoolgh {

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

        $params->limit = isset($params->limit) && isset($params->no_limit) ? 9999 : $params->limit;
        $params->incident_type = isset($params->incident_type) ? $params->incident_type : "incident";

        $params->query .= (isset($params->created_by)) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= (!empty($params->incident_type)) ? " AND a.incident_type='{$params->incident_type}'" : null;
        $params->query .= (isset($params->incident_date)) ? " AND a.incident_date='{$params->incident_date}'" : null;
        $params->query .= (isset($params->user_id)) ? " AND a.user_id='{$params->user_id}'" : null;
        $params->query .= (isset($params->client_id)) ? " AND a.client_id='{$params->client_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image,'|',last_seen,'|',online,'|',user_type) FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_information,
                    (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image,'|',last_seen,'|',online,'|',user_type) FROM users WHERE users.item_id = a.user_id LIMIT 1) AS user_information
                FROM incidents a
                WHERE a.deleted = ? {$params->query} AND client_id = ? ORDER BY DATE(a.incident_date) LIMIT {$params->limit}
            ");
            $stmt->execute([0, $params->clientId]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["created_by_information", "user_information"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($each, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);    
                }

                // load the incident followups
                if($result->incident_type == "incident") {
                    // empty followups
                    $result->followups = [];
                    
                    // get the list
                    $the_param = (object) [
                        "clientId" => $params->clientId,
                        "incident_type" => "followup"
                    ];
                    // append the followups
                    $result->followups = $this->list($the_param)["data"];
                }
                
                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {

        } 

    }

    /**
     * Update existing incident record
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
     * Update existing incident record
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