<?php

class Records extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Permission Control
     * 
     * @param String $resource
     * @param String $resource_id
     * @param stdClass $userData
     * 
     * @return Array
     */
    private function permission_control($resource, $record_id, $userData) {
        
        // global variable
        global $accessObject;
                
        // the list of composite variable to return for each resource
        $resource_list = [
            "event_type" => [
                "table" => "events_types",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM events_types WHERE item_id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ],
            "event" => [
                "table" => "events",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM events WHERE item_id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ],
            "class" => [
                "table" => "classes",
                "update" => "status='0'",
                "where" => "id='{$record_id}'",
                "query" => "SELECT id FROM classes WHERE id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ],
            "department" => [
                "table" => "departments",
                "update" => "status='0'",
                "where" => "id='{$record_id}'",
                "query" => "SELECT id FROM departments WHERE id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ],
            "course" => [
                "table" => "courses",
                "update" => "status='0'",
                "where" => "id='{$record_id}'",
                "query" => "SELECT id FROM courses WHERE id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ],
            "book" => [
                "table" => "books",
                "update" => "status='0', deleted='1'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM books WHERE item_id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ],
            "book_category" => [
                "table" => "books_type",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM books_type WHERE item_id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ],
            "incident" => [
                "table" => "incidents",
                "update" => "deleted='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM incidents WHERE item_id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ],
            "user" => [
                "table" => "users",
                "update" => "status='0', deleted='1'",
                "where" => "item_id='{$record_id}' AND user_status='Active'",
                "query" => "SELECT id FROM users WHERE item_id='{$record_id}' AND status ='1' AND client_id='{$userData->client_id}' AND user_status='Active' LIMIT 1"
            ],
            "guardian" => [
                "table" => "users_guardian",
                "update" => "status='0'",
                "where" => "user_id='{$record_id}'",
                "query" => "SELECT id FROM users_guardian WHERE user_id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
            ]
        ];

        // return the information for the specified resource
        return $resource_list[$resource] ?? null;

    }

    /**
     * Delete a record from the system
     * 
     * @param String $resource
     * @param String $record_id
     * @param \stdClass $params->userData
     * 
     * @return Array
     */
    public function remove(stdClass $params) {

        $code = 203;
        $additional = [];
        $data = "Error processing request!";

        // get the query to use
        $featured = $this->permission_control($params->resource, $params->record_id, $params->userData);

        // run the query
        if(!empty($featured)) {

            // try and catch all errors in the statement
            try {
                // perform the query
                $stmt = $this->db->prepare($featured["query"]);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_OBJ);
            } catch(PDOException $e) {
                // quit the execution of the file
                // return ["code" => 203, "data" => "Sorry! There was an error while processing the request."];
                return ["code" => 203, "data" => $e->getMessage()];
            }

            // return if no result was found
            if(empty($result)) {
                return ["code" => 203, "data" => "Sorry! There was no record found for the specified id."];
            }

            // if the result is in this list
            if(in_array($params->resource, [
                "event_type", "event", "class", "department", "course", "incident", "user", "guardian", "book", "book_category"
            ])) {
                // update the database record
                $this->db->query("UPDATE {$featured["table"]} SET {$featured["update"]} WHERE {$featured["where"]} LIMIT 1");
                
                /** Log the user activity */
                $this->userLogs("{$params->resource}", $params->record_id, null, "<strong>{$params->userData->name}</strong> deleted this record from the system.", $params->userData->user_id);

                // return the success response
                $code = 200;
                $data = "Record set successfully deleted";
            }

            // if a full result was found
            return [
                "code" => $code,
                "data" => $data, 
                "additional" => [
                    "record_id" => $params->record_id
                ]
            ];

        } else {
            return ["code" => 203, "data" => "Sorry! There was no record found for the specified id."];
        }

    }
}
?>