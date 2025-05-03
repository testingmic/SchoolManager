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
     * @param String $action
     * 
     * @return Array
     */
    private function permission_control($resource, $record_id, $userData, $action = "delete") {
        
        // global variable
        global $accessObject;

        // check if the user has the permission to manage the settings
		$isSupport = $accessObject->hasAccess("manage", "settings");

        // set the client_id request
        $whereClause = $isSupport ? null : "AND client_id='{$userData->client_id}'";

        // set the user where clause
        $userWhere = ($action == "restore") ? "AND user_status = 'Deleted'" : "AND user_status = 'Active'";

        // set the user set where clause
        $userSetWhere = ($action == "restore") ? "user_status = 'Active', status ='1', deleted='0'" : "user_status = 'Deleted', status ='0', deleted='1'";
                
        // the list of composite variable to return for each resource
        $resource_list = [
            "event_type" => [
                "table" => "events_types",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM events_types WHERE item_id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "event" => [
                "table" => "events",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM events WHERE item_id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "class" => [
                "table" => "classes",
                "update" => "status='0'",
                "where" => "id='{$record_id}'",
                "query" => "SELECT id FROM classes WHERE id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "department" => [
                "table" => "departments",
                "update" => "status='0'",
                "where" => "id='{$record_id}'",
                "query" => "SELECT id FROM departments WHERE id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "course" => [
                "table" => "courses",
                "update" => "status='0'",
                "where" => "id='{$record_id}'",
                "query" => "SELECT id FROM courses WHERE id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "fees_category" => [
                "table" => "fees_category",
                "update" => "status='0'",
                "where" => "id='{$record_id}'",
                "query" => "SELECT id FROM fees_category WHERE id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "book" => [
                "table" => "books",
                "update" => "status='0', deleted='1'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM books WHERE item_id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "book_category" => [
                "table" => "books_type",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM books_type WHERE item_id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "course_unit" => [
                "table" => "courses_plan",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM courses_plan WHERE plan_type='unit' AND item_id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "course_lesson" => [
                "table" => "courses_plan",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM courses_plan WHERE plan_type='lesson' AND item_id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "incident" => [
                "table" => "incidents",
                "update" => "deleted='0'",
                "where" => "item_id='{$record_id}'",
                "query" => "SELECT id FROM incidents WHERE item_id='{$record_id}' {$whereClause} AND status ='1' LIMIT 1"
            ],
            "class_room" => [
                "table" => "classes_rooms",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}' AND status='1'",
                "query" => "SELECT name FROM classes_rooms WHERE item_id='{$record_id}' AND status ='1' {$whereClause} LIMIT 1"
            ],
            "allowance" => [
                "table" => "payslips_allowance_types",
                "update" => "status='0'",
                "where" => "id='{$record_id}' AND status='1'",
                "query" => "SELECT name FROM payslips_allowance_types WHERE id='{$record_id}' AND status ='1' {$whereClause} LIMIT 1"
            ],
            "timetable" => [
                "table" => "timetables",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}' AND status='1'",
                "query" => "SELECT name FROM timetables WHERE item_id='{$record_id}' AND status ='1' {$whereClause} LIMIT 1"
            ],
            "payslip" => [
                "table" => "payslips",
                "update" => "deleted='1', status='0'",
                "where" => "item_id='{$record_id}' AND validated='0' AND deleted='0'",
                "query" => "SELECT id FROM payslips WHERE item_id='{$record_id}' AND status ='0' {$whereClause} LIMIT 1"
            ],
            "user" => [
                "table" => "users",
                "update" => "{$userSetWhere}",
                "where" => "item_id='{$record_id}' {$userWhere}",
                "query" => "SELECT id FROM users WHERE item_id='{$record_id}' {$whereClause} {$userWhere} LIMIT 1"
            ],
            "leave" => [
                "table" => "leave_requests",
                "update" => "status='Cancelled'",
                "where" => "item_id='{$record_id}' AND status='Pending'",
                "query" => "SELECT id FROM leave_requests WHERE item_id='{$record_id}' AND status ='Pending' {$whereClause} LIMIT 1"
            ],
            "guardian" => [
                "table" => "users",
                "update" => "status='0', deleted='1'",
                "where" => "item_id='{$record_id}' AND user_type='parent'",
                "query" => "SELECT id FROM users WHERE item_id='{$record_id}' {$whereClause} AND user_type='parent' AND status ='1' LIMIT 1"
            ],
            "accounts_type" => [
                "table" => "accounts_type_head",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}' AND status='1'",
                "query" => "SELECT name FROM accounts_type_head WHERE item_id='{$record_id}' AND status ='1' {$whereClause} LIMIT 1"
            ],
            "package" => [
                "table" => "clients_packages",
                "update" => "status='deleted'",
                "where" => "id='{$record_id}' AND status='active'",
                "query" => "SELECT id FROM clients_packages WHERE id='{$record_id}' AND status ='active' LIMIT 1"
            ],
            "daily_report" => [
                "table" => "daily_reports",
                "update" => "deleted='1'",
                "where" => "item_id='{$record_id}' AND is_seen='0'",
                "query" => "SELECT id FROM daily_reports WHERE item_id='{$record_id}' AND is_seen ='0' {$whereClause} LIMIT 1"
            ],
            "template" => [
                "table" => "smsemail_templates",
                "update" => "status='0'",
                "where" => "item_id='{$record_id}' AND status='1'",
                "query" => "SELECT name FROM smsemail_templates WHERE item_id='{$record_id}' AND status ='1' {$whereClause} LIMIT 1"
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

        global $accessObject;

        $code = 203;
        $additional = [];
        $data = "Error processing request!";

        // check if the user has the permission to manage the settings
		$isSupport = $accessObject->hasAccess("manage", "settings");

        // get the query to use
        $featured = $this->permission_control($params->resource, $params->record_id, $params->userData, $params->action);

        // if in preview mode but the user is not a super admin user
        if($this->session->previewMode && empty($this->session->superAdminUser)) {
            return [
                "code" => 203, 
                "data" => "Sorry! You will not be able to perform the delete action since you are in preview mode.",
                "additional" => [
                    "record_id" => $params->record_id, "clear" => true
                ]
            ];
        }

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
                return ["code" => 203, "data" => $e->getMessage() ." Sorry! There was an error while processing the request."];
            }

            // return if no result was found
            if(empty($result)) {
                return ["code" => 203, "data" => "Sorry! There was no record found for the specified id."];
            }

            // payslip modification
            if($params->resource == "payslip") {

                // log the data in the statement account
                $check = $this->pushQuery("item_id, balance", "accounts", "client_id='{$params->clientId}' AND status='1' AND default_account='1'");
                
                // if the account is not empty
                if(!empty($check)) {

                    // transaction record
                    $check_2 = $this->pushQuery("payment_medium, account_id, record_date, item_id, amount, 
                        balance, academic_year, academic_term, description", "accounts_transaction", 
                        "item_id='{$params->record_id}' LIMIT 1");
                                    
                    // if there is an existing payslip record
                    if(!empty($check_2)) {
                        
                        // log the transaction record
                        $stmt = $this->db->prepare("INSERT INTO accounts_transaction SET 
                            item_id = ?, client_id = ?, account_id = ?, account_type = ?, item_type = ?, 
                            reference = ?, amount = ?, created_by = ?, record_date = ?, payment_medium = ?, 
                            description = ?, academic_year = ?, academic_term = ?, balance = ?, state = 'Approved'
                        ");
                        $stmt->execute([
                            $params->record_id, $params->clientId, $check_2[0]->account_id, "payroll", "Deposit", null, 
                            $check_2[0]->amount, $params->userId, $check_2[0]->record_date, $check_2[0]->payment_medium, 
                            "{$check_2[0]->description}: Reversed", $check_2[0]->academic_year, $check_2[0]->academic_term, 
                            ($check[0]->balance + $check_2[0]->amount)
                        ]);

                        // add up to the income
                        $this->db->query("UPDATE accounts SET total_credit = (total_credit + {$check_2[0]->amount}), balance = (balance + {$check_2[0]->amount}) WHERE item_id = '{$check_2[0]->account_id}' LIMIT 1");
                    }
                }
                
            }

            // update the database record
            $this->db->query("UPDATE {$featured["table"]} SET {$featured["update"]} WHERE {$featured["where"]} LIMIT 1");

            // set the action
            $action = ($params->action == "delete" ? "deleted" : "restored");
            
            if($isSupport) {
                /** Log the user activity */
                $this->userLogs("{$params->resource}", $params->record_id, null, "<strong>{$params->userData->name}</strong> {$action} this record from the system.", $params->userData->user_id);
            }

            // return the success response
            $code = 200;
            $data = "Record set successfully {$action}";
            
            // if the action is restore
            if($isSupport) {
                $additional["href"] = $this->session->user_current_url;
            } else {
                // set the additional data
                $additional = ["record_id" => $params->record_id, "clear" => true];
            }

            // set the is support variable
            $additional['is_support'] = $isSupport;

            // if a full result was found
            return [
                "code" => $code,
                "data" => $data, 
                "additional" => $additional
            ];

        } else {
            return ["code" => 203, "data" => "Sorry! There was no record found for the specified id."];
        }

    }

    /**
     * Validate Payslip record
     * 
     * @return Array
     */
    public function validate(stdClass $params) {
        
        // confirm that all required parameters has been parsed
        if(!isset($params->label["record_id"]) || !isset($params->label["record"])) {
            return ["code" => 203, "data" => "Sorry! Please ensure all required parameters has been parsed."];
        }

        // set the record
        $record = $params->label["record"];
        $records_id = $this->stringToArray($params->label["record_id"]);

        // if the record is to validate a payslip
        if($record == "payslip") {

            // loop through the array list
            foreach($records_id as $record_id) {
                $payslip = $this->pushQuery("a.payslip_month, a.id, a.payslip_year, (SELECT b.name FROM users b WHERE b.item_id = a.employee_id ORDER BY b.id DESC LIMIT 1) AS employee_name", 
                "payslips a", "a.client_id='{$params->clientId}' AND a.deleted='0' AND a.item_id='{$record_id}' AND a.validated='0' LIMIT 1");
                if(empty($payslip)) {
                    return ["code" => 203, "data" => "Sorry! An invalid id was supplied or the payslip has already been validated."];
                }
                $this->db->query("UPDATE payslips SET validated='1', validated_date = now(), status='1' WHERE item_id='{$record_id}' LIMIT 1");

                // change the state of the transaction to approved
                $this->db->query("UPDATE accounts_transaction SET state='Approved', validated_date = now(), validated_by = '{$params->userId}' WHERE item_id='{$record_id}' AND state != 'Approved' LIMIT 5");

                // log the user activity
                $this->userLogs("payslip", $record_id, null, "<strong>{$params->userData->name}</strong> validated the payslip: <strong>{$payslip[0]->employee_name}</strong> for the month: <strong>{$payslip[0]->payslip_month} {$payslip[0]->payslip_year}</strong>", $params->userId);

            }

            return ["code" => 200, "data" => "Payslip successfully validated."];

        }

        // if the record is transaction
        elseif($record == "transaction") {

            // loop through the array list
            foreach($records_id as $record_id) {

                // validate the transaction record
                $transaction = $this->pushQuery("item_id", "accounts_transaction", "client_id='{$params->clientId}' AND state='Pending' AND item_id='{$record_id}' LIMIT 1");
                if(empty($transaction)) {
                    return ["code" => 203, "data" => "Sorry! An invalid id was supplied or the transaction has already been validated."];
                }
                $this->db->query("UPDATE accounts_transaction SET state='Approved', validated_date = now(), validated_by = '{$params->userId}' WHERE item_id='{$record_id}' AND state != 'Approved' LIMIT 5");

                // log the user activity
                $this->userLogs("accounts_transaction", $record_id, null, "<strong>{$params->userData->name}</strong> validated the transaction.", $params->userId);
            }

            return ["code" => 200, "data" => "Transaction record successfully validated."];

        }

    }

}
?>