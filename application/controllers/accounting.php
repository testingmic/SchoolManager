<?php

class Accounting extends Myschoolgh {

    public $accepted_column;
    private $iclient;

	public function __construct(stdClass $params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data;
        $this->iclient = $client_data;

	}
    
    /**
     * List Account Type Head
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list_accounttype(stdClass $params) {


        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q) && !empty($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->type) && !empty($params->type)) ? " AND a.type='{$params->type}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->type_id) && !empty($params->type_id)) ? " AND a.item_id='{$params->type_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info
                FROM accounts_type_head a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["createdby_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["name", "phone_number", "email", "image","user_type"]);
                }

                $data[] = $result;
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
     * Add Account Type Head
     * 
     * @param String $params->account_type
     * @param String $params->name
     * @param String $params->description
     *
     * @return Array
     */
    public function add_accounttype(stdClass $params) {

        try {

            // ensure the correct account type is parsed
            if(!in_array(strtolower($params->account_type), ["income", "expense"])) {
                return ["code" => 203, "data" => $this->is_required("Account Type")];
            }

            // create an item_id
            $item_id = random_string("alnum", 15);

            // insert the record
            $stmt = $this->db->prepare("INSERT INTO accounts_type_head SET client_id = ?, name = ?, type = ?,
            description = ?, created_by = ?, item_id = ?");
            $stmt->execute([$params->clientId, $params->name, $params->account_type, $params->description ?? null, $params->userId, $item_id]);

            // log the user activity
            $this->userLogs("accounts_typehead", $item_id, null, "{$params->userData->name} added a new account type head", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account Type Head was successfully created.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}account_type"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update existing Account Type Head
     * 
     * @param String $params->account_type
     * @param String $params->name
     * @param String $params->description
     *
     * @return Array
     */
    public function update_accounttype(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "accounts_type_head", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // ensure the correct account type is parsed
            if(!in_array(strtolower($params->account_type), ["income", "expense"])) {
                return ["code" => 203, "data" => $this->is_required("Account Type")];
            }

            // insert the record
            $stmt = $this->db->prepare("UPDATE accounts_type_head SET name = ?, type = ?,
            description = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
            $stmt->execute([$params->name, $params->account_type, $params->description ?? null, $params->type_id, $params->clientId]);

            // log the user activity
            $this->userLogs("accounts_typehead", $params->type_id, $prevData[0], "{$params->userData->name} updated the existing account type head", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account Type Head was successfully updated.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}account_type/{$params->type_id}"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }
    
    /**
     * List Account Type Head
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list_accounts(stdClass $params) {


        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q) && !empty($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->account_id) && !empty($params->account_id)) ? " AND a.item_id='{$params->account_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info
                FROM accounts a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["createdby_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["name", "phone_number", "email", "image","user_type"]);
                }

                $data[] = $result;
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
     * Add Account
     * 
     * @param String $params->account_number
     * @param String $params->account_name
     * @param String $params->description
     * @param Float  $params->opening_balance
     *
     * @return Array
     */
    public function add_account(stdClass $params) {

        try {

            // create an item_id
            $item_id = random_string("alnum", 15);

            // insert the record
            $stmt = $this->db->prepare("INSERT INTO accounts SET client_id = ?, account_name = ?, account_number = ?,
            description = ?, opening_balance = ?, created_by = ?, item_id = ?");
            $stmt->execute([
                $params->clientId, $params->account_name, $params->account_number, 
                $params->description ?? null, $params->opening_balance, $params->userId, $item_id
            ]);

            // log the user activity
            $this->userLogs("accounts", $item_id, null, "{$params->userData->name} added a new account type head", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account was successfully created.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}account_type"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update Account
     * 
     * @param String $params->account_number
     * @param String $params->account_name
     * @param String $params->description
     * @param Float  $params->opening_balance
     * @param String $params->account_id
     *
     * @return Array
     */
    public function update_account(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "accounts", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // insert the record
            $stmt = $this->db->prepare("UPDATE accounts SET account_name = ?, account_number = ?,
            description = ?, opening_balance = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
            $stmt->execute([
                $params->account_name, $params->account_number, $params->description ?? null, 
                $params->opening_balance, $params->account_id, $params->clientId
            ]);

            // log the user activity
            $this->userLogs("accounts", $params->account_id, $prevData[0], "{$params->userData->name} updated the existing account details", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account was successfully updated.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}accounts/{$params->account_id}"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Add Deposit Record
     * 
     * @param String $params->account_id
     * @param String $params->reference
     * @param String $params->description
     * @param Float  $params->amount
     * @param String $params->deposit_date
     *
     * @return Array
     */
    public function add_deposit(stdClass $params) {

        try {

            // create an item_id
            $item_id = random_string("alnum", 15);

            // insert the record
            $stmt = $this->db->prepare("INSERT INTO accounts_transaction SET 
                item_id = ?, client_id = ?, account_id = ?, account_type = ?, 
                item_type = ?, reference = ?, amount = ?, created_by = ?, date = ?,
                payment_medium = ?, description = ?, academic_year = ?, academic_term = ?
            ");
            $stmt->execute([
                $item_id, $params->clientId, $params->account_id, $params->account_type, 
                'Deposit', $params->reference ?? null, $params->amount, $params->userId, 
                $params->deposit_date, $params->payment_medium, $params->description ?? null,
                $params->academic_year, $params->academic_term
            ]);

            // log the user activity
            $this->userLogs("accounts_deposit", $item_id, null, "{$params->userData->name} added a new deposit", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account deposit was successfully recorded.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}deposit_voucher"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update Deposit Record
     * 
     * @param String $params->account_id
     * @param String $params->reference
     * @param String $params->description
     * @param Float  $params->amount
     * @param String $params->deposit_date
     * @param String $params->deposit_id
     *
     * @return Array
     */
    public function update_deposit(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "accounts_transaction", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND item_type='Deposit' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // insert the record
            $stmt = $this->db->prepare("UPDATE accounts_transaction SET 
                account_id = ?, account_type = ?, item_type = ?, reference = ?, amount = ?, 
                created_by = ?, date = ?, payment_medium = ?, description = ? WHERE item_id = ? AND client_id = ?
            ");
            $stmt->execute([
                $params->account_id, $params->account_type, 
                'Deposit', $params->reference ?? null, $params->amount, $params->userId, 
                $params->deposit_date, $params->payment_medium, $params->description ?? null,
                $params->deposit_id, $params->clientId
            ]);

            // log the user activity
            $this->userLogs("accounts_deposit", $params->deposit_id, null, "{$params->userData->name} updated the existing deposit record", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Deposit record was successfully updated.", 
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

}