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

            // get the data
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            if(!empty($data)) {
                // get the guardian ids
                $guardian_ids = !empty($data) ? array_column($data, "guardian_ids") : [];
                $guardian_ids = !empty($guardian_ids) ? array_filter($guardian_ids, function($each) {
                    return !empty($each);
                }) : [];
                $guardian_ids = !empty($guardian_ids) ? array_unique($guardian_ids) : [];

                // get the list of guardians
                $guardians = !empty($guardian_ids) ? $this->pushQuery("id, item_id, unique_id, phone_number, firstname, lastname", "users", "client_id='{$params->clientId}' AND item_id IN {$this->inList($guardian_ids)} AND user_type='parent' AND status = '1'") : [];

                $regroup = [];
                foreach($guardians as $each) {
                    $regroup[$each->item_id][] = $each;
                }
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

        $params->phone = trim($params->phone);
        if(strlen($params->phone) > 12) {
            return ["code" => 400, "data" => "Phone number must be less than 12 characters"];
        }

        $delegate = $this->pushQuery("*", "delegates", "client_id='{$params->clientId}' AND phonenumber='{$params->phone}' AND status = '1'");
        if(!empty($delegate)) {
            
            // get the guardian ids
            $guardian_ids = $delegate[0]->guardian_ids;

            // append the guardian id to the guardian ids
            if(!empty($guardian_ids) && strpos($guardian_ids, "{$params->guardian_id}") === false) {
                $guardian_ids = rtrim($guardian_ids, "|") . "|{$params->guardian_id}";
            }

            // update the delegate
            $this->quickUpdate("guardian_ids='{$guardian_ids}'", "delegates", "id='{$delegate[0]->id}'");

            return [
                "code" => 200,
                "data" => "Delegate updated successfully.",
                "additional" => [
                    "href" => $this->baseUrl . "guardian/{$params->guardian_id}/delegates"
                ]
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
                $params->guardian_id ?? null, $params->userId
            ]);

            $insertId = $this->db->lastInsertId();
            $delegate_id = "DEL" . $insertId;

            // update the unique id of the delegate
            $this->db->query("UPDATE delegates SET unique_id='{$delegate_id}' WHERE id='{$insertId}' LIMIT 1");

            $href = !empty($params->guardian_id) ? "guardian/{$params->guardian_id}/delegates" : "delegates";

            return [
                "code" => 200,
                "data" => "Delegate created successfully.",
                "additional" => [
                    "href" => $this->baseUrl . $href
                ]
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