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
            "fees_category" => [
                "table" => "courses",
                "update" => "status='0'",
                "where" => "id='{$record_id}'",
                "query" => "SELECT id FROM fees_category WHERE id='{$record_id}' AND client_id='{$userData->client_id}' AND status ='1' LIMIT 1"
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
            "class_room" => [
                "table" => "classes_rooms",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}' AND status='1'",
                "query" => "SELECT name FROM classes_rooms WHERE item_id='{$record_id}' AND status ='1' AND client_id='{$userData->client_id}' LIMIT 1"
            ],
            "allowance" => [
                "table" => "payslips_allowance_types",
                "update" => "status='0'",
                "where" => "id='{$record_id}' AND status='1'",
                "query" => "SELECT name FROM payslips_allowance_types WHERE id='{$record_id}' AND status ='1' AND client_id='{$userData->client_id}' LIMIT 1"
            ],
            "timetable" => [
                "table" => "timetables",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}' AND status='1'",
                "query" => "SELECT name FROM timetables WHERE item_id='{$record_id}' AND status ='1' AND client_id='{$userData->client_id}' LIMIT 1"
            ],
            "payslip" => [
                "table" => "payslips",
                "update" => "deleted='1', status='0'",
                "where" => "item_id='{$record_id}' AND activated='0' AND deleted='0'",
                "query" => "SELECT name FROM payslips WHERE item_id='{$record_id}' AND status ='0' AND client_id='{$userData->client_id}' LIMIT 1"
            ],
            "user" => [
                "table" => "users",
                "update" => "status='0', deleted='1'",
                "where" => "item_id='{$record_id}' AND user_status='Active'",
                "query" => "SELECT id FROM users WHERE item_id='{$record_id}' AND status ='1' AND client_id='{$userData->client_id}' AND user_status='Active' LIMIT 1"
            ],
            "guardian" => [
                "table" => "users",
                "update" => "status='0', deleted='1'",
                "where" => "item_id='{$record_id}' AND user_type='parent'",
                "query" => "SELECT id FROM users WHERE item_id='{$record_id}' AND client_id='{$userData->client_id}' AND user_type='parent' AND status ='1' LIMIT 1"
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
                "event_type", "event", "class", "department", "course", "incident", "user", "guardian", 
                "book", "book_category", "fees_category", "class_room", "timetable", "allowance", "payslip"
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

    /**
     * Validate record
     * 
     * @return Array
     */
    public function validate(stdClass $params) {
        
        // confirm that all required parameters has been parsed
        if(!isset($params->label["record_id"]) || !isset($params->label["record"])) {
            return ["code" => 203, "data" => "Sorry! Please ensure all required parameters has been parsed."];
        }

        $record = $params->label["record"];
        $record_id = $params->label["record_id"];

        // validate the payslip record
        if($record === "payslip") {
            $payslip = $this->pushQuery("a.payslip_month, a.id, a.payslip_year, (SELECT b.name FROM users b WHERE b.item_id = a.employee_id ORDER BY b.id DESC LIMIT 1) AS employee_name", 
            "payslips a", "a.client_id='{$params->clientId}' AND a.deleted='0' AND a.item_id='{$record_id}' AND a.validated='0' LIMIT 1");
            if(empty($payslip)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied or the payslip has already been validated."];
            }
            $this->db->query("UPDATE payslips SET validated='1', validated_date = now(), status='1' WHERE item_id='{$record_id}' LIMIT 1");

            // log the user activity
            $this->userLogs("payslip", $record_id, null, "<strong>{$params->userData->name}</strong> validated the payslip: <strong>{$payslip[0]->employee_name}</strong> for the month: <strong>{$payslip[0]->payslip_month} {$payslip[0]->payslip_year}</strong>", $params->userId);

            return ["code" => 200, "data" => "Payslip successfully validated."];
        }

    }
}
?>