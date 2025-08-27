<?php
class Delegates extends Myschoolgh {

    // accepted columns
    public $accepted_column;
    public $readonly_mode;

    public function __construct($params = null) {
        parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;
    }

    /**
     * List Cards
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list($params = null) {
        
        try {

            // append some filters to apply to the query
            $query = !empty($params->guardian_id) ? " AND a.guardian_ids LIKE '%{$params->guardian_id}%'" : "";

            // get the list of users based on the request 
            $stmt = $this->db->prepare("SELECT a.* 
            FROM delegates a 
            WHERE a.client_id='{$params->clientId}' {$query} AND a.status='1' ORDER BY a.id DESC");
			$query = $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Create Delegate
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function create($params) {

        foreach(['firstname', 'lastname', 'phone', 'gender', 'relationship'] as $each) {
            if(empty($params->{$each})) {
                return ["code" => 400, "data" => "{$each} is required"];
            }
        }

        if(strlen($params->phone) > 12) {
            return ["code" => 400, "data" => "Phone number must be less than 12 characters"];
        }

        $delegate = $this->pushQuery("*", "delegates", "client_id='{$params->clientId}' AND phonenumber='{$params->phone}'");
        if(!empty($delegate)) {
            
            // get the guardian ids
            $guardian_ids = $delegate->guardian_ids;

            // append the guardian id to the guardian ids
            if(strpos($guardian_ids, "{$params->guardian_id}") === false) {
                $guardian_ids .= rtrim($guardian_ids, "|") . "|{$params->guardian_id}";
            }

            // update the delegate
            $this->quickUpdate("guardian_ids='{$guardian_ids}'", "delegates", "id='{$delegate->id}'");

            return [
                "code" => 200,
                "data" => "Delegate updated successfully."
            ];

        }
        else {

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO delegates 
                (client_id, firstname, lastname, phonenumber, gender, relationship, guardian_ids, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            // execute the statement
            $stmt->execute([
                $params->clientId, $params->firstname, $params->lastname, 
                $params->phone, $params->gender, $params->relationship, 
                $params->guardian_id, $params->userId
            ]);

            $insertId = $this->db->lastInsertId();
            $delegate_id = "DEL" . $insertId;

            // update the unique id of the delegate
            $this->db->query("UPDATE delegates SET unique_id='{$delegate_id}' WHERE id='{$insertId}' LIMIT 1");

            return [
                "code" => 200,
                "data" => "Delegate created successfully.",
                "additional" => $this->list($params)["data"]
            ];
            
        }

    }

    /**
     * Update Delegate
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update($params) {

        foreach(['firstname', 'lastname', 'phone', 'gender', 'relationship'] as $each) {
            if(empty($params->{$each})) {
                return ["code" => 400, "data" => "{$each} is required"];
            }
        }

        $delegate = $this->pushQuery("*", "delegates", "client_id='{$params->clientId}' AND id='{$params->delegate_id}'");
        if(empty($delegate)) {
            return ["code" => 400, "data" => "Delegate not found."];
        }

        $this->quickUpdate("firstname='{$params->firstname}', lastname='{$params->lastname}', phonenumber='{$params->phone}', gender='{$params->gender}', relationship='{$params->relationship}'", "delegates", "id='{$params->delegate_id}'");

        return [
            "code" => 200,
            "data" => "Delegate updated successfully."
        ];
    
    }
        
}