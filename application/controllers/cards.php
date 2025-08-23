<?php
class Cards extends Myschoolgh {

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
            $query = !empty($params->class_id) ? " AND a.class_id IN ({$params->class_id})" : "";
            $query .= !empty($params->user_type) ? " AND a.user_type IN ({$params->user_type})" : "";
            $query .= !empty($params->issue_date) ? " AND a.issue_date = '{$params->issue_date}'" : "";
            $query .= !empty($params->day_boarder) ? " AND a.day_boarder = '{$params->day_boarder}'" : "";
            $query .= !empty($params->card_preview_id) ? " AND a.id = '{$params->card_preview_id}'" : "";

            // get the list of users based on the request 
            $stmt = $this->db->prepare("SELECT a.*, b.name AS class_name 
            FROM generated_cards a 
            LEFT JOIN classes b ON a.class_id = b.id 
            WHERE a.client_id='{$params->clientId}' {$query}");
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
     * Preview Card
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function preview(stdClass $params) {
        

        $data = $this->list($params)['data'];

        if(empty($data)) {
            return ["code" => 400, "data" => "Sorry! No card found."];
        }

        $cardSettings = $this->iclient->client_preferences->id_card ?? [];
        $defaultClientData = $this->iclient;

        // set the base url for the client
        $defaultClientData->baseUrl = $this->baseUrl;

        // get the first item in the array
        $userData = $data[0];

        // get the type of the user
        $type = $userData->user_type == "student" ? "studentId" : "employeeId";

        $qr_string = "{$type}:[{$userData->user_id}]/admissionId:[{$userData->unique_id}]/userType:[{$userData->user_type}]";

        // create new qr code object
        $qrObject = load_class("Qr", "controllers");
        $qr_code = $qrObject->generate(["filename" => "{$userData->user_id}.png", "client_id" => $userData->client_id, "text" => $qr_string]);

        // append some more variables to the card settings
        foreach($userData as $key => $value) {
            $cardSettings->{$key} = $value;
        }

        $cardSettings->qr_code = rtrim($this->baseUrl, "/") . $qr_code;

        return [
            "code" => 200,
            "data" => [
                "user" => $userData,
                "idcard" => render_card_preview($cardSettings, $defaultClientData, true),
                "qr_code" => $cardSettings->qr_code
            ]
        ];

    }

    /**
     * Generate Cards
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function generate(stdClass $params) {
        
        try {

            // check if the user category is empty
            if(empty($params->user_category)) {
                return ["code" => 400, "data" => "Sorry! The category to generate the id cards for is required."];
            }

            $issue_date = !empty($params->issue_date) ? $params->issue_date : date("Y-m-d");
            $expiry_date = !empty($params->expiry_date) ? $params->expiry_date : date("Y-m-d", strtotime("+3 years"));
            
            // check if the expiry date is the same as the issue date
            if(strtotime($params->expiry_date) == strtotime(date("Y-m-d"))) {
                return ["code" => 400, "data" => "Sorry! The expiry date cannot be the same as today's date."];
            }

            if(strtotime($issue_date) > strtotime($expiry_date)) {
                return ["code" => 400, "data" => "Sorry! The issue date cannot be greater than the expiry date."];
            }

            // expiry should not be more than 6 years from the issue date
            if(strtotime($expiry_date) > strtotime(date('Y-m-d', strtotime("+6 years")))) {
                return ["code" => 400, "data" => "Sorry! The expiry date cannot be more than 6 years from the issue date."];
            }

            if(strtotime($issue_date) > strtotime(date('Y-m-d'))) {
                return ["code" => 400, "data" => "Sorry! The issue date cannot be in the future."];
            }

            $query = !empty($params->user_category_list) && $params->user_category_list !== 'null' ? " AND class_id IN ({$params->user_category_list})" : "";

            $user_category = $params->user_category == "student" ? "'student'" : "'admin', 'teacher', 'employee', 'accountant'";

            // get the list of users based on the request payload
            $users = $this->pushQuery(
                "id, name, gender, class_id, day_boarder, unique_id, date_of_birth, user_type, enrollment_date", 
                "users", 
                "client_id='{$params->clientId}' AND user_type IN ({$user_category}) {$query} AND user_status='active'"
            );

            // get the list of users that have already been generated
            $generatedList = $this->pushQuery("user_id, name", "generated_cards", "client_id='{$params->clientId}'");

            // get the list that has not been generated yet
            $notGenerated = array_diff(array_column($users, 'id'), array_column($generatedList, 'user_id'));

            // loop through the users and insert the record into the database
            foreach($users as $user) {

                // if the user has already been generated, skip
                if(!in_array($user->id, $notGenerated)) {
                    continue;
                }

                // insert the record into the database
                $this->db->query("INSERT INTO generated_cards SET 
                    client_id='{$params->clientId}', 
                    user_id='{$user->id}', name='{$user->name}',
                    user_type='{$user->user_type}',
                    issue_date='{$issue_date}',
                    expiry_date='{$expiry_date}',
                    class_id='{$user->class_id}', 
                    gender='{$user->gender}',
                    enrollment_date='{$user->enrollment_date}',
                    day_boarder='{$user->day_boarder}', 
                    unique_id='{$user->unique_id}'
                    ".(!empty($user->date_of_birth) ? ", date_of_birth='{$user->date_of_birth}'" : "")."
                ");
            }

            return [
                "code" => 200,
                "data" => "Cards successfully generated.",
                "additional" => [
                    "clear" => true,
                    "count_popup" => count($notGenerated),
                    "href" => "{$this->baseUrl}card_generated"
                ]
            ];
        
        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 
        
    }

}