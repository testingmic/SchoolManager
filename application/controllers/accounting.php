<?php

class Accounting extends Myschoolgh {

	public function __construct(stdClass $params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;

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

        $params->query .= !empty($params->q) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= !empty($params->type) ? " AND a.type='{$params->type}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->type_id) ? " AND a.item_id='{$params->type_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(b.name,'|',COALESCE(b.phone_number,'NULL'),'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info
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
                return ["code" => 400, "data" => $this->is_required("Account Type")];
            }

            // check if the name matches any of these values
            if(in_array(strtolower($params->name), ["payroll", "payslip", "school fees", "fees", "fee"])) {
                return ["code" => 400, "data" => "Sorry! You cannot add any account type head with the names [payroll, payslip, fee or fees]."];
            }

            // create an item_id
            $item_id = random_string("alnum", RANDOM_STRING);

            // insert the record
            $stmt = $this->db->prepare("INSERT INTO accounts_type_head SET client_id = ?, name = ?, type = ?,
            description = ?, created_by = ?, item_id = ?, academic_year = ?, academic_term = ?");
            $stmt->execute([$params->clientId, $params->name, $params->account_type, $params->description ?? null, 
                $params->userId, $item_id, $params->academic_year, $params->academic_term]);

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

            // check if the name matches any of these values
            if(in_array(strtolower($params->name), ["payroll", "payslip", "school fees", "fees", "fee"])) {
                return ["code" => 400, "data" => "Sorry! You cannot change any account type head to the names [payroll, payslip, fee or fees]."];
            }

            // old record
            $prevData = $this->pushQuery("*", "accounts_type_head", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($prevData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // ensure the correct account type is parsed
            if(!in_array(strtolower($params->account_type), ["income", "expense"])) {
                return ["code" => 400, "data" => $this->is_required("Account Type")];
            }

            // insert the record
            $stmt = $this->db->prepare("UPDATE accounts_type_head SET name = ?, type = ?
                ".(isset($params->description) ? ", description='{$params->description}'" : null)."
            WHERE item_id = ? AND client_id = ? AND academic_year = ? AND academic_term = ? LIMIT 1");
            $stmt->execute([
                $params->name, $params->account_type, $params->type_id, 
                $params->clientId, $params->academic_year, $params->academic_term
            ]);

            // log the user activity
            $this->userLogs("accounts_typehead", $params->type_id, $prevData[0], "{$params->userData->name} updated the existing account type head", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account Type Head was successfully updated.", 
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
     * List Accounts
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
        $params->query .= (isset($params->account_id) && !empty($params->account_id)) ? " AND (a.item_id='{$params->account_id}' OR a.account_number='{$params->account_id}')" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(b.name,'|',COALESCE(b.phone_number,'NULL'),'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info
                FROM accounts a
                WHERE {$params->query} AND a.status = ? ORDER BY a.default_account DESC, a.id ASC LIMIT {$params->limit}
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
            $item_id = random_string("alnum", RANDOM_STRING);

            // Verify the account id parsed
            $check = $this->pushQuery("id", "accounts", "client_id='{$params->clientId}' AND status='1' AND state='Active' LIMIT 5");
            $accounts_count = count($check);

            // if empty, end the query
            if($accounts_count > 4) { return ["code" => 400, "data" => "Maximum Accounts Reached! You cannot have create more than 5 active accounts at a time."]; }

            // insert the new account details
            // if there isn't any existing account then, auto set the current account as the default account
            $stmt = $this->db->prepare("INSERT INTO accounts SET client_id = ?, account_name = ?, account_number = ?,
            description = ?, opening_balance = ?, created_by = ?, item_id = ?, balance = ?, total_credit = ?, account_bank = ? ".($accounts_count == 0 ? ", default_account='1'" : null)."
            ");
            $stmt->execute([
                $params->clientId, $params->account_name, $params->account_number, $params->description ?? null, 
                $params->opening_balance ?? 0, $params->userId, $item_id, $params->opening_balance ?? 0, 
                $params->opening_balance ?? 0, $params->account_bank ?? null
            ]);

            // log the user activity
            $this->userLogs("accounts", $item_id, null, "{$params->userData->name} added a new account type head", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account was successfully created.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}accounts"
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
            $prevData = $this->pushQuery("*", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($prevData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // insert the record
            $stmt = $this->db->prepare("UPDATE accounts SET account_name = ?, account_number = ? 
                ".(isset($params->description) ? ", description='{$params->description}'" : null)."
                ".(isset($params->opening_balance) ? ", opening_balance='{$params->opening_balance}'" : null)."
                ".(isset($params->account_bank) ? ", account_bank='{$params->account_bank}'" : null)."
                WHERE item_id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([$params->account_name, $params->account_number, $params->account_id, $params->clientId]);

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
     * List Account Type Head
     * 
     * @param stdClass  $params
     * @param String    $params->q
     * @param String    $params->account_id
     * @param String    $params->account_type
     * @param String    $params->transaction_id
     * @param String    $params->date
     * @param String    $params->date_range
     * 
     * @return Array
     */
    public function list_transactions(stdClass $params, $column = "record_date") {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->q) && !empty($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->item_type) && !empty($params->item_type)) ? " AND a.item_type='{$params->item_type}'" : null;
        $params->query .= (isset($params->account_id) && !empty($params->account_id)) ? " AND a.account_id='{$params->account_id}'" : null;
        $params->query .= (isset($params->account_type) && !empty($params->account_type)) ? " AND a.account_type='{$params->account_type}'" : null;
        $params->query .= (isset($params->transaction_id) && !empty($params->transaction_id)) ? " AND a.item_id='{$params->transaction_id}'" : null;
        $params->query .= isset($params->date) && !empty($params->date) ? " AND DATE(a.record_date) ='{$params->date}'" : "";
        $params->query .= (isset($params->date_range) && !empty($params->date_range)) ? $this->dateRange($params->date_range, "a", $column) : null;

        // return the where clause
        if(isset($params->return_where_clause)){
            return $params->query;
        }

        // append the academic year and term
        // $params->query .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
        // $params->query .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);

        $params->query .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : null;
        $params->query .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : null;

        $order_by = $params->order_by ?? "ASC";

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT c.name FROM accounts_type_head c WHERE c.item_id = a.account_type LIMIT 1) AS account_type_name,
                    c.account_name, c.account_bank, c.account_number,
                    (SELECT CONCAT(b.name,'|',COALESCE(b.phone_number, 'NULL'),'|',COALESCE(b.email, 'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info,
                    (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment
                FROM accounts_transaction a
                LEFT JOIN accounts c ON c.item_id = a.account_id
                WHERE {$params->query} AND a.status = ? ORDER BY a.id {$order_by} LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["createdby_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["name", "phone_number", "email", "image","user_type"]);
                }

                // if attachment variable was parsed
                $result->attachment = empty($result->attachment) ? '' : json_decode($result->attachment);
                $result->state_label = $this->the_status_label($result->state);

                // set the new name if the account type is payroll or fees
                if(empty($result->account_type_name) && in_array($result->account_type, ["payroll", "fees", "arrears"])) {
                    $result->account_type_name = ucfirst($result->account_type);
                }

                // if the files is set
                if(!isset($result->attachment->files)) {
                   $result->attachment = $this->fake_files;
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
     * List Bank Transactions
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list_bank_transactions(stdClass $params) {

        $params->query = "1";
        $params->tranc_id = $params->transaction_id ?? null;

        $params->limit = !empty($params->limit) ? $params->limit : $this->global_limit;
        $params->query .= !empty($params->type) ? " AND a.transaction_type='{$params->type}'" : null;
        $params->query .= !empty($params->bank_id) ? " AND a.bank_id='{$params->bank_id}'" : null;
        $params->query .= !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->tranc_id) ? " AND a.item_id='{$params->tranc_id}'" : null;

        try {

            $filesObject = load_class("forms", "controllers");

            $stmt = $this->db->prepare("
                SELECT a.*, b.bank_name,
                    (SELECT CONCAT(b.name,'|',COALESCE(b.phone_number,'NULL'),'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info,
                (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment
                FROM bank_transactions a
                LEFT JOIN banks_list b ON b.id = a.bank_id
                WHERE {$params->query} AND a.status = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["createdby_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["name", "phone_number", "email", "image","user_type"]);
                }

                // if attachment variable was parsed
                $result->attachment = json_decode($result->attachment);

                $result->attachment_html = isset($result->attachment->files) ? $filesObject->list_attachments($result->attachment->files, $result->created_by, "col-lg-4 col-md-6", false, false) : "";

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
     * List e-Payment Transactions
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function epayment_transactions(stdClass $params) {


        $params->query = "1";
        $params->tranc_id = $params->transaction_id ?? null;

        $params->limit = !empty($params->limit) ? $params->limit : $this->global_limit;
        $params->query .= !empty($params->reference_id) ? " AND a.reference_id='{$params->reference_id}'" : null;
        $params->query .= !empty($params->state) ? " AND a.state='{$params->state}'" : null;
        $params->query .= !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->tranc_id) ? " AND a.transaction_id='{$params->tranc_id}'" : null;

        try {

            $filesObject = load_class("forms", "controllers");

            $stmt = $this->db->prepare("
                SELECT a.*, b.client_name,
                    (SELECT CONCAT(b.name,'|',COALESCE(b.phone_number,'NULL'),'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info
                FROM transaction_logs a
                LEFT JOIN clients_accounts b ON b.client_id = a.client_id
                WHERE {$params->query} ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["createdby_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["name", "phone_number", "email", "image","user_type"]);
                }
                $result->payment_data = !empty($result->payment_data) ? json_decode($result->payment_data) : [];

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
     * Add Deposit Record
     * 
     * @param String $params->transaction_type
     * @param String $params->bank_id
     * @param String $params->account_number
     * @param Float  $params->amount
     * @param String $params->reference_id
     *
     * @return Array
     */
    public function add_bank_deposit(stdClass $params) {

        try {

            // create an item_id
            $item_id = random_string("alnum", RANDOM_STRING);

            // insert the record
            $stmt = $this->db->prepare("INSERT INTO bank_transactions SET 
                item_id = ?, client_id = ?, transaction_type = ?, bank_id = ?, 
                account_number = ?, account_name = ?, amount = ?, created_by = ?, reference_id = ?,
                description = ?, academic_year = ?, academic_term = ?
            ");
            $stmt->execute([
                $item_id, $params->clientId, "Deposit", $params->bank_id, 
                $params->account_number, $params->account_name, $params->amount, 
                $params->userId, $params->reference_id ?? null, $params->description ?? null,
                $params->academic_year, $params->academic_term
            ]);

            // create a new object of the files class
            $filesObj = load_class("files", "controllers");

            // attachments
            $attachments = $filesObj->prep_attachments("bank_transactions_Deposit", $params->userId, $item_id);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
            $files->execute(["bank_transactions", $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"], $params->clientId]);

            // log the user activity
            $this->userLogs("bank_transactions", $item_id, null, "{$params->userData->name} added a new deposit", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account income was successfully recorded.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}bank_deposits"
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
            $item_id = random_string("alnum", RANDOM_STRING);

            // validate the account id
            $accountData = $this->pushQuery("balance", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($accountData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // set the payment medium if not set
            $params->payment_medium = isset($params->payment_medium) ? $params->payment_medium : "cash";

            // insert the record
            $stmt = $this->db->prepare("INSERT INTO accounts_transaction SET 
                item_id = ?, client_id = ?, account_id = ?, account_type = ?, 
                item_type = ?, reference = ?, amount = ?, created_by = ?, record_date = ?,
                payment_medium = ?, description = ?, academic_year = ?, academic_term = ?, balance = ?
            ");
            $stmt->execute([
                $item_id, $params->clientId, $params->account_id, $params->account_type, 
                'Deposit', $params->reference ?? null, $params->amount, $params->userId, 
                $params->date, $params->payment_medium, $params->description ?? null,
                $params->academic_year, $params->academic_term, ($accountData[0]->balance + $params->amount)
            ]);

            // add up to the credit line
            $this->db->query("UPDATE accounts SET total_credit = (total_credit + {$params->amount}), balance = (balance + {$params->amount}) WHERE item_id = '{$params->account_id}' LIMIT 1");

            // create a new object of the files class
            $filesObj = load_class("files", "controllers");

            // attachments
            $attachments = $filesObj->prep_attachments("accounts_transaction_deposit", $params->userId, $item_id);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
            $files->execute(["accounts_transaction", $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"], $params->clientId]);

            // log the user activity
            $this->userLogs("accounts_transaction", $item_id, null, "{$params->userData->name} added a new income", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account income was successfully recorded.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}incomes"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update Income Record
     * 
     * @param String $params->account_id
     * @param String $params->reference
     * @param String $params->description
     * @param Float  $params->amount
     * @param String $params->deposit_date
     * @param String $params->transaction_id
     *
     * @return Array
     */
    public function update_deposit(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "accounts_transaction", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND item_type='Deposit' AND status='1' LIMIT 1");
            if(empty($prevData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // validate the account id
            $accountData = $this->pushQuery("balance", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($accountData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // set the payment medium if not set
            $params->payment_medium = isset($params->payment_medium) ? $params->payment_medium : "cash";

            // insert the record
            $stmt = $this->db->prepare("UPDATE accounts_transaction SET 
                account_id = ?, account_type = ?, reference = ?, amount = ?, created_by = ?, record_date = ?, 
                payment_medium = ?, description = ?, balance = ? WHERE item_id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([
                $params->account_id, $params->account_type, 
                $params->reference ?? null, $params->amount, $params->userId, 
                $params->date, $params->payment_medium, $params->description ?? null,
                ($accountData[0]->balance - $prevData[0]->amount + $params->amount),
                $params->transaction_id, $params->clientId
            ]);

            // update the deposits
            $this->db->query("UPDATE accounts SET total_credit = (total_credit - {$prevData[0]->amount} + {$params->amount}) WHERE item_id = '{$params->account_id}' LIMIT 1");

            // log the user activity
            $this->userLogs("accounts_transaction_deposit", $params->transaction_id, $prevData[0], "{$params->userData->name} updated the existing income record", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Income record was successfully updated.", 
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Add Expenditure Record
     * 
     * @param String $params->account_id
     * @param String $params->reference
     * @param String $params->description
     * @param Float  $params->amount
     * @param String $params->deposit_date
     *
     * @return Array
     */
    public function add_expenditure(stdClass $params) {

        try {

            // create an item_id
            $item_id = random_string("alnum", RANDOM_STRING);

            // validate the account id
            $accountData = $this->pushQuery("balance", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($accountData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // set the payment medium if not set
            $params->payment_medium = isset($params->payment_medium) ? $params->payment_medium : "cash";

            // insert the record
            $stmt = $this->db->prepare("INSERT INTO accounts_transaction SET 
                item_id = ?, client_id = ?, account_id = ?, account_type = ?, item_type = ?, 
                reference = ?, amount = ?, created_by = ?, record_date = ?, payment_medium = ?, 
                description = ?, academic_year = ?, academic_term = ?, balance = ?
            ");
            $stmt->execute([
                $item_id, $params->clientId, $params->account_id, $params->account_type, 
                'Expense', $params->reference ?? null, $params->amount, $params->userId, 
                $params->date, $params->payment_medium, $params->description ?? null,
                $params->academic_year, $params->academic_term, ($accountData[0]->balance - $params->amount)
            ]);

            // add up to the expense
            $this->db->query("UPDATE accounts SET total_debit = (total_debit + {$params->amount}), balance = (balance - {$params->amount}) WHERE item_id = '{$params->account_id}' LIMIT 1");

            // create a new object of the files class
            $filesObj = load_class("files", "controllers");

            // attachments
            $attachments = $filesObj->prep_attachments("accounts_transaction_expense", $params->userId, $item_id);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
            $files->execute(["accounts_transaction", $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"], $params->clientId]);

            // log the user activity
            $this->userLogs("accounts_transaction", $item_id, null, "{$params->userData->name} added a new expense", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account Expense was successfully recorded.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}expenses"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update Expenditure Record
     * 
     * @param String $params->account_id
     * @param String $params->reference
     * @param String $params->description
     * @param Float  $params->amount
     * @param String $params->deposit_date
     * @param String $params->transaction_id
     *
     * @return Array
     */
    public function update_expenditure(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "accounts_transaction", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND item_type='Expense' AND status='1' LIMIT 1");
            if(empty($prevData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // validate the account id
            $accountData = $this->pushQuery("balance", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($accountData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // set the payment medium if not set
            $params->payment_medium = isset($params->payment_medium) ? $params->payment_medium : "cash";
            
            // insert the record
            $stmt = $this->db->prepare("UPDATE accounts_transaction SET 
                account_id = ?, account_type = ?, reference = ?, amount = ?, created_by = ?, record_date = ?, 
                payment_medium = ?, description = ?, balance = ? WHERE item_id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([
                $params->account_id, $params->account_type, 
                $params->reference ?? null, $params->amount, $params->userId, 
                $params->date, $params->payment_medium, $params->description ?? null,
                ($accountData[0]->balance + $prevData[0]->amount - $params->amount),
                $params->transaction_id, $params->clientId
            ]);

            // update the expense
            $this->db->query("UPDATE accounts SET total_debit = (total_debit - {$prevData[0]->amount} + {$params->amount}) WHERE item_id = '{$params->account_id}' LIMIT 1");

            // log the user activity
            $this->userLogs("accounts_transaction_expense", $params->transaction_id, $prevData[0], "{$params->userData->name} updated the existing expense record", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Expense record was successfully updated.", 
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Generate Account Statement
     * 
     * @return String
     */
    public function statement(stdClass $params) {

        // get the accounts list
        $accounts_list = $this->list_accounts($params)["data"];

        if(!isset($params->clientId)) {
            return page_not_found();
        }

        // confirm that the account is not empty
        if(!empty($accounts_list)) {

            $count = count($accounts_list);
            $html_content = "";

            // get the client logo content
            if(!empty($params->client_data->client_logo)) {
                $type = pathinfo($params->client_data->client_logo, PATHINFO_EXTENSION);
                $logo_data = file_get_contents($this->baseUrl . $params->client_data->client_logo);
                $this->client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
            }

            // date range algorithm
            if(isset($params->start_date)) {
                // revert date
                if(strtotime($params->start_date) > strtotime(date("Y-m-d"))) {
                    $params->start_date = date("Y-m-d");
                }
                // algorithm
                if(!isset($params->end_date)) {
                    $params->date_range = "{$params->start_date}:".date("Y-m-t");
                }
                elseif(isset($params->end_date)) {
                    $params->date_range = "{$params->start_date}:{$params->end_date}";
                }
            } else if(isset($params->end_date) && !isset($params->start_date)) {
                $params->start_date = date("Y-m-d");
                $params->date = $params->end_date;
            }

            $start_date = !isset($params->start_date) ? "01-01-2021" : $params->start_date;
            $end_date = !isset($params->end_date) ?  date("t-m-Y") : $params->end_date;       

            // account notes
            $loadNotes = (bool) isset($params->display) && ($params->display == "notes");
            
            // end the query if the user wants notes
            if($loadNotes)  {
                return $this->format_transaction_notes($params);
            }

            // set the date range
            $date_range = "{$start_date} To {$end_date}";

            // loop through the accounts
            foreach($accounts_list as $key => $account) {

                // set the account id for the transaction list
                $params->account_id = $account->item_id;

                // load the transactions for each account
                $transactions = $this->list_transactions($params)["data"];
                
                // count the transactions 
                $transactions_count = count($transactions);

                // algorithm for calculations
                $opening_balance = 0;
                $closing_balance = 0;
                $_transactions = [];

                // credits / debits
                foreach($transactions as $transaction) {
                    $_transactions[$transaction->item_type][] = $transaction;
                }
                
                // counts
                $credits_count = isset($_transactions["Deposit"]) ? count($_transactions["Deposit"]) : 0;
                $debits_count = isset($_transactions["Expense"]) ? count($_transactions["Expense"]) : 0;

                // totals
                $total_credits = isset($_transactions["Deposit"]) ? array_sum(array_column($_transactions["Deposit"], "amount")) : 0;
                $total_debits = isset($_transactions["Expense"]) ? array_sum(array_column($_transactions["Expense"], "amount")) : 0;

                // processing
                if(!empty($transactions)) {
                    $opening_balance = $transactions[0]->balance - $transactions[0]->amount;
                    $closing_balance = $transactions[$transactions_count-1]->balance;    
                } else {
                    $opening_balance = $account->opening_balance;
                    $closing_balance = $account->opening_balance;
                }

                // append to the information details
                $html_content .= "
                <div style='padding:10px'>
                    <table border='0' width='100%' style='border:solid 1px #dee2e6'>
                        <tr>
                            <td align='center' width='25%'>
                                ".(!empty($params->client_data->client_logo) ? "<img width=\"70px\" src=\"{$this->client_logo}\">" : "")."
                                <h4 style=\"color:#6777ef;font-family:helvetica;padding:0px;margin:0px;\">".strtoupper($params->client_data->client_name)."</h4>
                            </td>
                            <td class='text-center'>
                                <div style='padding-bottom:5px;font-size:20px'><strong>STATEMENT OF ACCOUNT</strong></div>
                                <div style='padding-bottom:5px;font-size:13px;'><strong>FOR ACCOUNT NUMBER: {$account->account_number}</strong></div>
                                <div style='padding-bottom:5px;'><strong>{$account->account_name}</strong></div>
                                <div>{$date_range}</div>
                            </td>
                            <td align='right' valign='top' width='27%'>
                                <strong>eStatement</strong>
                                <div>{$account->account_bank}</div>
                                <div style='margin-top:5px;font-size:11px'><em>{$account->description}</em></div>
                            </td>
                        </tr>
                    </table>
                    <div style='margin-top:20px'>
                        <table border='0' width='100%' cellpadding='5px;' style='border:solid 1px #dee2e6;font-size:11px;'>
                            <tr>
                                <td width='50%' valign='top'>
                                    <strong>{$account->account_name}</strong>
                                </td>
                                <td>
                                    <div style='padding-bottom:4px'>&nbsp;</div>
                                    <div style='padding-bottom:4px'>OPENING BALANCE</div>
                                    <div style='padding-bottom:4px'>CLOSING BALANCE</div>
                                    <div style='padding-bottom:4px'>TOTAL DEBITS</div>
                                    <div style='padding-bottom:4px'>TOTAL CREDITS</div>
                                </td>
                                <td valign='top'>
                                    <div style='padding-bottom:4px'><strong>BOOK</strong></div>
                                    <div style='padding-bottom:4px'>".number_format($opening_balance, 2)."</div>
                                    <div style='padding-bottom:4px'>".number_format($closing_balance, 2)."</div>
                                    <div style='padding-bottom:4px'>".$debits_count."</div>
                                    <div style='padding-bottom:4px'>".$credits_count."</div>
                                </td>
                                <td valign='top'>
                                    <div><strong>TOTAL</strong></div>
                                    <div style='padding-bottom:4px'>&nbsp;</div>
                                    <div style='padding-bottom:4px'>&nbsp;</div>
                                    <div style='padding-bottom:4px'>".number_format($total_debits, 2)."</div>
                                    <div style='padding-bottom:4px'>".number_format($total_credits, 2)."</div>
                                </td>
                            </tr>
                        </table>
                        <table border='0' width='100%' cellpadding='5px;' style='border:solid 1px #dee2e6;font-size:11px;'>
                            ".(!$loadNotes ? 
                            "<thead>
                                <tr style='text-transform:uppercase;font-size:10px;'>
                                    <th>Date Created</th>
                                    <th>Item Date</th>
                                    <th>Description</th>
                                    <th align='right'>Debits</th>
                                    <th align='right'>Credits</th>
                                </tr>
                            </thead>" : null);
                        
                        if(!empty($transactions) && !$loadNotes) {
                            foreach ($transactions as $item => $value) {
                                $html_content .= "
                                <tr>
                                    <td width='12%' style='padding:7px;border:solid 1px #dee2e6;'>
                                        ".date("d-m-Y", strtotime($value->date_created))."
                                    </td>
                                    <td width='12%' style='padding:7px;border:solid 1px #dee2e6;'>
                                        ".date("d-m-Y", strtotime($value->record_date))."
                                    </td>
                                    <td  width='40%' style='padding:7px;border:solid 1px #dee2e6;'>
                                        <div>".$value->account_type_name."</div>
                                        <div>".$value->description."</div>
                                        <div>".$value->reference."</div>
                                    </td>
                                    <td align='right' style='padding:7px;border:solid 1px #dee2e6;'>".($value->item_type == "Expense" ? number_format($value->amount, 2) : null)."</td>
                                    <td align='right' style='padding:7px;border:solid 1px #dee2e6;'>".($value->item_type == "Deposit" ? number_format($value->amount, 2) : null)."</td>
                                </tr>";
                            }
                        } else {
                            $html_content .= !$loadNotes ? "<tr><td colspan='5' style='height:50px' align='center'><strong>***NO TRANSACTION FOUND***</strong></td></tr>" : null;
                        }
                $html_content .= (
                    !$loadNotes ? "
                        <tr>
                            <td colspan='3' align='center'><strong>***END OF STATEMENT***</strong></td>
                            <td align='right' style='font-weight:bold;padding:7px;border:solid 1px #dee2e6;height:50px;font-size:15px;'>".number_format($total_debits, 2)."</td>
                            <td align='right' style='font-weight:bold;padding:7px;border:solid 1px #dee2e6;height:50px;font-size:15px;'>".number_format($total_credits, 2)."</td>
                        </tr>" : "")." 
                    </table>
                    </div>
                </div>";

                // display the contact details of the client
                $html_content .= '
                <div class="border-bottom" style="border: 2px solid #2196F3; margin-top:10px"></div>
                <div align="center" style="font-size:13px;padding-top:10px; margin-bottom:20px;">
                    <strong>Location: </strong>'.$params->client_data->client_location.' | 
                    <strong>Contact:</strong> '.$params->client_data->client_contact.'
                    '.(!empty($params->client_data->client_secondary_contact) ? " | {$params->client_data->client_secondary_contact}" : "").'
                    | <strong>Address: </strong> '.(strip_tags($params->client_data->client_address)).'
                </div>';


                $html_content .= $count !== $key + 1 ? "\n<div class=\"page_break\"></div>" : null;
            }
        } else {
            $html_content = "<h3>Sorry! An invalid request was parsed.</h3>";
        }


        return $html_content;
    }

    /**
     * Account Statement Notes
     * 
     * @param String    $params->q
     * @param String    $params->item_type
     * @param String    $params->account_id
     * @param String    $params->account_type
     * @param String    $params->transaction_id
     * @param String    $params->date
     * @param String    $params->date_range
     * 
     * @return String
     */
    public function format_transaction_notes(stdClass $params) {

        try {

            // get the accounts list
            $accounts_list = $this->list_accounts($params)["data"];
            $count = count($accounts_list);

            $html_content = "";

            // confirm that the account is not empty
            if(!empty($accounts_list)) {

                // get the group by
                $group_by = isset($params->group_by) && !empty($params->group_by) ? strtoupper($params->group_by) : "MONTH";
                
                // set the font size
                $default_font = "15px";

                if($group_by === "MONTH") {
                    $default_font .= ";text-transform:uppercase";
                }
                // days 
                if($group_by === "DAY") {

                    // get the days lists
                    $days_list = $this->listDays($params->start_date, $params->end_date);
                    $days_count = count($days_list);

                    if($days_count > 31 && $days_count < 60) {
                        return "<table width='100%'>
                            <tr>
                                <td>Sorry! The days difference must not exceed 31 days.</td>
                            </tr>
                        </table>";
                    }

                    // if the number of days exceed 20 days
                    if($days_count > 2 && $days_count < 15) {
                        $default_font = "15px";
                    } 
                    elseif($days_count >= 15 && $days_count <= 21) {
                        $default_font = "12px";
                    }
                    elseif($days_count >= 21 && $days_count <= 25) {
                        $default_font = "10px";
                    }
                    elseif($days_count >= 25 && $days_count <= 60) {
                        $default_font = "9px";
                    } elseif($days_count > 70) {
                        $default_font = "15px;text-transform:uppercase";
                        $group_by = "MONTH";
                    }
                }

                $breakdown = (bool) (isset($params->breakdown) && !empty($params->breakdown));

                // get the fees category list
                $fees_category_list = $this->pushQuery("id, name", "fees_category", "client_id='{$params->clientId}' AND status='1'");
                
                $query = "1";
                $query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
                $query .= (isset($params->q) && !empty($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
                $query .= (isset($params->item_type) && !empty($params->item_type)) ? " AND a.item_type='{$params->item_type}'" : null;
                $query .= (isset($params->account_id) && !empty($params->account_id)) ? " AND a.account_id='{$params->account_id}'" : null;
                $query .= (isset($params->account_type) && !empty($params->account_type)) ? " AND a.account_type='{$params->account_type}'" : null;
                $query .= (isset($params->transaction_id) && !empty($params->transaction_id)) ? " AND a.item_id='{$params->transaction_id}'" : null;
                $query .= isset($params->academic_year) && !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
                $query .= isset($params->academic_term) && !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);
                $query .= isset($params->date) && !empty($params->date) ? " AND DATE(a.date_created) ='{$params->date}'" : "";
                $query .= (isset($params->date_range) && !empty($params->date_range)) ? $this->dateRange($params->date_range, "a", "date_created") : null;
                
                // loop through the accounts
                foreach($accounts_list as $account_key => $account) {

                    // get the transactions
                    $stmt = $this->db->prepare("
                        SELECT 
                            SUM(a.amount) AS total_sum, DATE(a.date_created) AS record_date, MONTH(a.date_created) AS record_month, a.account_type, a.item_type,
                            (SELECT c.name FROM accounts_type_head c WHERE c.item_id = a.account_type LIMIT 1) AS account_type_name,
                            (SELECT b.account_name FROM accounts b WHERE b.item_id = a.account_id LIMIT 1) AS account_name
                        FROM accounts_transaction a
                        WHERE {$query} AND a.client_id = ? AND a.account_id = ? AND a.reversed = ? GROUP BY a.account_type, {$group_by}(a.date_created)
                    ");
                    $stmt->execute([$params->clientId, $account->item_id, 0]);
                    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                    
                    // group the item types
                    $item_types = [];
                    foreach ($result as $type) {
                        $item_types[$type->item_type][] = $type;
                    }

                    // group by month
                    $months_data = [];

                    // loop through the months of the year
                    foreach($item_types as $item => $record) {
                        // loop through the item types
                        foreach($record as $i_key => $i_value) {
                            // loop through the months
                            $month = $group_by == "MONTH" ? date("M", strtotime("January + ".($i_value->record_month -1)." month")) : $i_value->record_date;
                            // append to the record month
                            $months_data[$item][$month][$i_value->account_type] = $i_value;
                        }
                    }

                    // get the list of income
                    $income_heads["Deposit"] = $this->pushQuery("item_id, name, description", "accounts_type_head", "client_id = '{$params->clientId}' AND status='1' AND type='Income'");
                    $income_heads["Expense"] = $this->pushQuery("item_id, name, description", "accounts_type_head", "client_id = '{$params->clientId}' AND status='1' AND type='Expense'");

                    // get an array of the items
                    $income_array = array_column($income_heads["Deposit"], "item_id");
                    $expense_array = array_column($income_heads["Expense"], "item_id");

                    // add the fees and payroll to the list
                    $income_heads["Deposit"][] = (object) [
                        "item_id" => "fees",
                        "name" => "Fees",
                        "description" => "This is the general category for the fees."
                    ];
                    $income_heads["Deposit"][] = (object) [
                        "item_id" => "arrears",
                        "name" => "Arrears",
                        "description" => "This is the general category for the fees arrears payment."
                    ];
                    $income_heads["Expense"][] = (object) [
                        "item_id" => "payslip",
                        "name" => "Payroll",
                        "description" => "This is the general category for the payroll recording."
                    ];

                    // line totals
                    $line_total = [];

                    $html_content .= "
                        <div style='padding:10px'>
                            <table border='0' width='100%' style='border:solid 1px #dee2e6; margin-bottom:10px'>
                                <tr>
                                    <td align='center' width='15%'>
                                        ".(!empty($params->client_data->client_logo) ? "<img width=\"70px\" src=\"{$this->client_logo}\">" : "")."
                                    </td>
                                    <td class='text-center'>
                                        <h2 style=\"color:#6777ef;font-family:helvetica;padding:0px;margin:0px;\">".strtoupper($params->client_data->client_name)."</h2>
                                        <div style='padding-bottom:5px;font-size:20px'><strong>NOTES OF ACCOUNT</strong></div>
                                        <div style='padding-bottom:5px;font-size:15px;'><strong>FOR ACCOUNT NUMBER: {$account->account_number}</strong></div>
                                        <div style='padding-bottom:5px;'><strong>{$account->account_name}</strong></div>
                                    </td>
                                    <td align='right' valign='top' width='27%'>
                                        <strong>eStatement</strong>
                                        <div>{$account->account_bank}</div>
                                        <div style='margin-top:5px;font-size:11px'><em>{$account->description}</em></div>
                                    </td>
                                </tr>
                            </table>";

                    // begin another data stream
                    $item_content = "";
                    $months_group = [];

                    // loop through the items
                    foreach(["Deposit", "Expense"] as $item) {
                        // set the item name
                        $item_name = $item == "Deposit" ? "Income" : $item;

                        // create the table
                        $item_content .= "
                        <div style='margin-bottom:20px'>
                            <div><strong>{$item_name} Line</strong></div>
                            <table width='100%' cellpadding='5px;' style='border:solid 1px #dee2e6;'>
                                <thead>
                                    <tr>
                                        <th width='15%' align='left' style='font-size:{$default_font};background:#2196F3;color:#fff;'>ITEM</th>";

                                        // list the months of the year if the group by is month
                                        if($group_by === "MONTH") {
                                            for($i = 0; $i < 12; $i++) {
                                                $month = date("M", strtotime("January + {$i} month"));
                                                $item_content .= "<th align='center' style='color:#fff;font-size:{$default_font};background:#2196F3' align='right'>{$month}</th>\n";
                                            }
                                        }
                                        // list the days within the range
                                        else {
                                            foreach($days_list as $day) {
                                                $day = date("jS M", strtotime($day));
                                                $item_content .= "<th align='center' style='color:#fff;font-size:{$default_font};background:#2196F3' align='right'>{$day}</th>\n";
                                            }
                                        }
                            $item_content .= "
                                        <th align='right' style='background:#2196F3;color:#fff;font-size:{$default_font};'>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>";
                                    foreach($income_heads[$item] as $type) {
                                        $item_content .= "<tr>\n";
                                        $item_content .= "<td><span style='font-size:{$default_font};'>{$type->name}</span></td>\n";

                                        // list the months of the year if the group by is month
                                        if($group_by === "MONTH") {

                                            // loop through the months
                                            for($i = 0; $i < 12; $i++) {

                                                $month = date("M", strtotime("January + {$i} month"));
                                                
                                                // get the name 
                                                $item_amount = isset($months_data[$item][$month][$type->item_id]) ? $months_data[$item][$month][$type->item_id]->total_sum : 0;
                                                
                                                if(isset($months_data[$item][$month][$type->item_id])) {
                                                    if($months_data[$item][$month][$type->item_id]->account_type === $type->item_id) {
                                                        $item_content .= "<td style='border:solid 1px #dee2e6;font-size:{$default_font};' align='right'>".number_format($item_amount, 2)."</td>\n";
                                                    } else {
                                                        $item_content .= "<td style='border:solid 1px #dee2e6;'></td>\n";
                                                    }
                                                } else {
                                                    $item_content .= "<td style='border:solid 1px #dee2e6;'></td>\n";
                                                }

                                                // get the line total
                                                $line_total[$item][$type->item_id][] = $item_amount;

                                                // set the month total
                                                $months_group[$item][$month][] = $item_amount;

                                            }

                                        }
                                        // list the days within the range
                                        else {
                                            foreach($days_list as $day) {
                                                // get the name 
                                                $item_amount = isset($months_data[$item][$day][$type->item_id]) ? $months_data[$item][$day][$type->item_id]->total_sum : 0;
                                                
                                                if(isset($months_data[$item][$day][$type->item_id])) {
                                                    if($months_data[$item][$day][$type->item_id]->account_type === $type->item_id) {
                                                        $item_content .= "<td style='border:solid 1px #dee2e6;font-size:{$default_font};' align='right'>".number_format($item_amount, 2)."</td>\n";
                                                    } else {
                                                        $item_content .= "<td style='border:solid 1px #dee2e6;'></td>\n";
                                                    }
                                                } else {
                                                    $item_content .= "<td style='border:solid 1px #dee2e6;'></td>\n";
                                                }

                                                // get the line total
                                                $line_total[$item][$type->item_id][] = $item_amount;

                                                // set the month total
                                                $months_group[$item][$day][] = $item_amount;
                                            }
                                        }

                                        $item_content .= "<td style='font-size:{$default_font};border:solid 1px #dee2e6;' align='right'><strong>".number_format(array_sum($line_total[$item][$type->item_id]), 2)."</strong></td>";
                                        $item_content .= "</tr>\n";
                                    }
                                    $item_content .= "<tr style='border:solid 1px #dee2e6; background:#555758; color: #fff'>";
                                    $item_content .= "<td><strong style='font-size:{$default_font};'>TOTAL</strong></td>";

                                    $summary_total = 0;

                                    if(isset($months_group[$item])) {
                                        foreach($months_group[$item] as $_this => $_that) {
                                            $item_content .= "
                                            <td align='right'>
                                                <strong style='font-size:{$default_font};'>".number_format(array_sum($_that), 2)."</strong>
                                            </td>";
                                        }

                                        foreach($months_group[$item] as $_this => $_that) {
                                            $summary_total += array_sum($_that);
                                        }
                                    }
                        $item_content .= "
                                    <td align='right'>
                                        <strong style='font-size:{$default_font};'>".number_format($summary_total, 2)."</strong>
                                    </td>
                                </tr>";
                        $item_content .= "
                                <tbody>
                            </table>
                        </div>";
                    }

                    // group the total values
                    $total = [];
                    foreach(["Deposit", "Expense"] as $item) {
                        if(isset($line_total[$item])) {
                            foreach($line_total[$item] as $key => $value) {
                                if(isset($total[$item])) {
                                    $total[$item] += array_sum($value);
                                } else {
                                    $total[$item] = array_sum($value);
                                }
                            }
                        }
                    }
                    $total["Expense"] = $total["Expense"] ?? 0;
                    $total["Deposit"] = $total["Deposit"] ?? 0;

                    // summary of each item
                    $html_content .= "
                    <table border='0' width='100%' cellpadding='5px' style='border:solid 1px #dee2e6; margin-bottom:10px'>
                        <tr>
                            <td></td>
                            <td valign='top' align='right' width='20%'>
                                <div style='margin-bottom:5px'><strong>START DATE:</strong> {$params->start_date}</div>
                                <div><strong>END DATE:</strong> {$params->end_date}</div>
                            </td>
                            <td valign='top' align='right' width='20%'>
                                <div style='margin-bottom:5px'><strong>INCOME:</strong> ".number_format($total["Deposit"], 2)."</div>
                                <div><strong>EXPENSE:</strong> ".number_format($total["Expense"], 2)."</div>

                                <div><strong>BALANCE:</strong> ".number_format(($total["Deposit"]-$total["Expense"]), 2)."</div>
                            </td>
                        </tr>
                    </table>";

                    // append the data stream
                    $html_content .= $item_content;

                    // if the show breakdown parameter was parsed
                    if($breakdown) {

                        // header for the breakdown
                        $html_content .= "<div class=\"page_break\"></div>";
                        $html_content .= "<div><strong>Breakdown of Fees Receipt</strong></div>";
                        $filters = "1";
                        $filters .= isset($params->date) && !empty($params->date) ? " AND DATE(a.recorded_date) ='{$params->date}'" : "";
                        $filters .= (isset($params->date_range) && !empty($params->date_range)) ? $this->dateRange($params->date_range, "a", "recorded_date") : null;
                        $filters .= isset($params->academic_year) && !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
                        $filters .= isset($params->academic_term) && !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);

                        // load the fees payent from the fees collection table grouped by the category id and the date recorded
                        $stmt = $this->db->prepare("
                            SELECT 
                                SUM(a.amount) AS amount_paid, a.category_id,
                                fc.name AS category_name, DATE(a.recorded_date) AS recorded_date, {$group_by}(a.recorded_date) AS group_period
                            FROM fees_collection a
                            LEFT JOIN fees_category fc ON fc.id = a.category_id
                            WHERE {$filters} AND a.client_id = ? AND a.reversed = ? AND a.category_id != ?
                            GROUP BY a.category_id, {$group_by}(a.recorded_date)
                        ");
                        $stmt->execute([$params->clientId, 0, "Arrears"]);
                        
                        $fees_payment = [];
                        while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                            $fees_payment[$result->category_name][] = $result;
                        }

                        // group by period set
                        $period_data = [];

                        // loop through the months of the year
                        foreach($fees_payment as $item => $record) {
                            // loop through the item types
                            foreach($record as $i_value) {
                                // loop through the months
                                $month = $group_by == "MONTH" ? date("F", strtotime("January + ".($i_value->group_period -1)." month")) : $i_value->recorded_date;
                                // append to the record month
                                $period_data[$month][$i_value->category_id] = $i_value;
                            }
                        }

                        // a new variable for the breakdown
                        $line_total = [];
                        $breakdown_group = [];

                        $html_content .= "
                        <table width='100%' cellpadding='5px;' style='border:solid 1px #dee2e6;'>
                            <thead>
                                <tr>
                                    <th width='15%' align='left' style='font-size:{$default_font};background:#2196F3;color:#fff;'>ITEM</th>";

                                    // list the months of the year if the group by is month
                                    if($group_by === "MONTH") {
                                        for($i = 0; $i < 12; $i++) {
                                            $month = date("M", strtotime("January + {$i} month"));
                                            $html_content .= "<th align='center' style='color:#fff;font-size:{$default_font};background:#2196F3' align='right'>{$month}</th>\n";
                                        }
                                    }
                                    // list the days within the range
                                    else {
                                        foreach($days_list as $day) {
                                            $day = date("jS M", strtotime($day));
                                            $html_content .= "<th style='color:#fff;font-size:{$default_font};background:#2196F3' align='right'>{$day}</th>\n";
                                        }
                                    }
                        $html_content .= "
                                    <th align='right' style='background:#2196F3;font-size:{$default_font};color:#fff;'>TOTAL</th>
                                </tr>";
    
                                // list through the category list
                                foreach($fees_category_list as $category) {
                                    $html_content .= "<tr>";
                                    $html_content .= "<td><span style='font-size:{$default_font};'>{$category->name}</span></td>\n";

                                    if($group_by === "MONTH") {

                                        // loop through the months
                                        for($i = 0; $i < 12; $i++) {

                                            $month = date("F", strtotime("January + {$i} month"));
                                            
                                            // get the name 
                                            $item_amount = isset($period_data[$month][$category->id]) ? $period_data[$month][$category->id]->amount_paid : 0;
                                            
                                            if(isset($period_data[$month][$category->id])) {
                                                if($period_data[$month][$category->id]->category_id === $category->id) {
                                                    $html_content .= "<td style='border:solid 1px #dee2e6;font-size:{$default_font};' align='right'>".number_format($item_amount, 2)."</td>\n";
                                                } else {
                                                    $html_content .= "<td style='border:solid 1px #dee2e6;'></td>\n";
                                                }
                                            } else {
                                                $html_content .= "<td style='border:solid 1px #dee2e6;'></td>\n";
                                            }

                                            // get the line total
                                            $line_total[$category->id][] = $item_amount;

                                            // set the month total
                                            $breakdown_group[$month][] = $item_amount;

                                        }

                                    }
                                    // list the days within the range
                                    else {
                                        foreach($days_list as $day) {
                                            // get the name 
                                            $item_amount = isset($period_data[$day][$category->id]) ? $period_data[$day][$category->id]->amount_paid : 0;
                                            
                                            if(isset($period_data[$day][$category->id])) {
                                                if($period_data[$day][$category->id]->category_id === $category->id) {
                                                    $html_content .= "<td style='border:solid 1px #dee2e6;font-size:{$default_font};' align='right'>".number_format($item_amount, 2)."</td>\n";
                                                } else {
                                                    $html_content .= "<td style='border:solid 1px #dee2e6;'></td>\n";
                                                }
                                            } else {
                                                $html_content .= "<td style='border:solid 1px #dee2e6;'></td>\n";
                                            }

                                            // get the line total
                                            $line_total[$category->id][] = $item_amount;

                                            // set the month total
                                            $breakdown_group[$day][] = $item_amount;
                                        }
                                    }

                                    $html_content .= "<td style='font-size:{$default_font};border:solid 1px #dee2e6;' align='right'><strong>".number_format(array_sum($line_total[$category->id]), 2)."</strong></td>";
                                    $html_content .= "</tr>\n";

                                    $html_content .= "</tr>";
                                }

                                $html_content .= "<tr style='border:solid 1px #dee2e6; background:#555758; color: #fff'>";
                                $html_content .= "<td><strong style='font-size:{$default_font};'>TOTAL</strong></td>";

                                $summary_total = 0;

                                foreach($breakdown_group as $_this => $_that) {
                                    $html_content .= "
                                    <td align='right'>
                                        <strong style='font-size:{$default_font};'>".number_format(array_sum($_that), 2)."</strong>
                                    </td>";
                                }

                                foreach($breakdown_group as $_this => $_that) {
                                    $summary_total += array_sum($_that);
                                }

                                $html_content .= "
                                    <td align='right'>
                                        <strong style='font-size:{$default_font};'>".number_format($summary_total, 2)."</strong>
                                    </td>
                                </tr>";

                        $html_content .= "
                            </thead>
                        </table>";

                    }

                    $html_content .= "</div>";

                    // display the contact details of the client
                    $html_content .= '
                        <div class="border-bottom" style="border: 2px solid #2196F3; margin-top:0px"></div>
                        <div align="center" style="font-size:13px;padding-top:10px; margin-bottom:20px;">
                            <strong>Location: </strong>'.$params->client_data->client_location.' | 
                            <strong>Contact:</strong> '.$params->client_data->client_contact.'
                            '.(!empty($params->client_data->client_secondary_contact) ? " | {$params->client_data->client_secondary_contact}" : "").'
                            | <strong>Address: </strong> '.(strip_tags($params->client_data->client_address)).'
                        </div>';

                    // append the next page div tag
                    $html_content .= $count !== $account_key + 1 ? "\n<div class=\"page_break\"></div>" : null;
                }
            
            } else {
                $html_content = "<h3>Sorry! An invalid request was parsed.</h3>";
            }           

            return $html_content;

        } catch(PDOException $e) {}

    }

    /**
     * Set The Default Account
     * 
     * @param String $params->account_id
     * 
     * @return Array
     */
    public function set_primary_account(stdClass $params) {

        try {
            
            // Verify the account id parsed
            $check = $this->pushQuery("id", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' AND status='1'");

            // if empty, end the query
            if(empty($check)) { return ["code" => 400, "data" => "Sorry! An invalid account id was parsed."]; }

            // set all accounts default_account column to 0
            $this->db->query("UPDATE accounts SET default_account='0' WHERE client_id='{$params->clientId}' AND status='1' LIMIT 10");

            // change the account default account
            $this->db->query("UPDATE accounts SET default_account='1' WHERE item_id='{$params->account_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // return success message
            return ["code" => 200, "data" => "Account was successfully set as the default primary account."];            
            
        } catch(PDOException $e) {}
    
    }

    /**
     * Reverse Fees Payment
     * 
     * @return Array
     */
    public function reverse(stdClass $params) {

        try {

            // begin transaction
            $this->db->beginTransaction();

            // confirm the payment id
            $payment_check = $this->pushQuery("a.amount, a.balance, a.account_id, a.item_type, a.state,
                (SELECT c.name FROM accounts_type_head c WHERE c.item_id = a.account_type LIMIT 1) AS account_type_name", 
                "accounts_transaction a", "a.item_id='{$params->transaction_id}' AND a.client_id='{$params->clientId}' LIMIT 1");

            // end query if empty
            if(empty($payment_check)) {
                return ["code" => 400, "data" => "Sorry! An invalid Transaction ID was parsed for processing"];
            }

            // confirm if already reversed
            if($payment_check[0]->state == "Reserved") {
                return ["code" => 400, "data" => "Sorry! This transaction has already been reversed"];
            }

            // set the amount
            $amount_paid = $payment_check[0]->amount;

            // reverse the transaction
            $this->db->query("UPDATE accounts_transaction SET state='Reversed', reversed='1' WHERE item_id='{$params->transaction_id}' AND client_id='{$params->clientId}' LIMIT 1");

            // get the account id
            $account_id = $this->pushQuery("account_id", "accounts_transaction", "item_id='{$params->transaction_id}' AND client_id='{$params->clientId}' LIMIT 1");

            // if the account id is not empty
            if(!empty($account_id)) {
                // set the arithmetic sign
                $sign = $payment_check[0]->item_type == "Deposit" ? "-" : "+";
                
                // reduce the account balance
                $this->db->query("UPDATE accounts SET total_credit = (total_credit {$sign} {$amount_paid}), 
                    balance = (balance {$sign} {$amount_paid})
                    WHERE item_id = '{$account_id[0]->account_id}' AND client_id = '{$params->clientId}' LIMIT 1
                ");
            }

            /* Record the user activity log */
            $this->userLogs("transaction_reversal", $params->transaction_id, null, 
                "{$params->userData->name} reversed an amount of <strong>{$amount_paid}</strong> as {$payment_check[0]->item_type} for 
                    <strong>{$payment_check[0]->account_type_name}</strong>.", $params->userId);
            
            // commit the statement
            $this->db->commit();

            // return success message
            return [
                "code" => 200,
                "data" => "Transaction reversal was successful"
            ];

        } catch(PDOException $e) {
            // reverse the db transaction
            $this->db->rollBack();
        }

    }

}