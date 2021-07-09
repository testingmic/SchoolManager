<?php

class Accounting extends Myschoolgh {

    private $iclient;

	public function __construct(stdClass $params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data;
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
            if(empty($prevData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

            // ensure the correct account type is parsed
            if(!in_array(strtolower($params->account_type), ["income", "expense"])) {
                return ["code" => 203, "data" => $this->is_required("Account Type")];
            }

            // insert the record
            $stmt = $this->db->prepare("UPDATE accounts_type_head SET name = ?, type = ?
                ".(isset($params->description) ? ", description='{$params->description}'" : null)."
            WHERE item_id = ? AND client_id = ? LIMIT 1");
            $stmt->execute([$params->name, $params->account_type, $params->type_id, $params->clientId]);

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
            description = ?, opening_balance = ?, created_by = ?, item_id = ?, balance = ?, total_credit = ?, account_bank = ?");
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
            if(empty($prevData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

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
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list_transactions(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->q) && !empty($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->item_type) && !empty($params->item_type)) ? " AND a.item_type='{$params->item_type}'" : null;
        $params->query .= (isset($params->account_id) && !empty($params->account_id)) ? " AND a.account_id='{$params->account_id}'" : null;
        $params->query .= (isset($params->account_type) && !empty($params->account_type)) ? " AND a.account_type='{$params->account_type}'" : null;
        $params->query .= (isset($params->transaction_id) && !empty($params->transaction_id)) ? " AND a.item_id='{$params->transaction_id}'" : null;
        $params->query .= isset($params->academic_year) && !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : ($this->academic_year ? " AND a.academic_year='{$this->academic_year}'" : null);
        $params->query .= isset($params->academic_term) && !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : ($this->academic_term ? " AND a.academic_term='{$this->academic_term}'" : null);
        $params->query .= isset($params->date) && !empty($params->date) ? " AND DATE(a.record_date) ='{$params->date}'" : "";
        $params->query .= (isset($params->date_range) && !empty($params->date_range)) ? $this->dateRange($params->date_range, "a", "record_date") : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT c.name FROM accounts_type_head c WHERE c.item_id = a.account_type LIMIT 1) AS account_type_name,
                    (SELECT b.account_name FROM accounts b WHERE b.item_id = a.account_id LIMIT 1) AS account_name,
                    (SELECT CONCAT(b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info,
                    (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment
                FROM accounts_transaction a
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

                // if attachment variable was parsed
                $result->attachment = json_decode($result->attachment);
                $result->state_label = $this->the_status_label($result->state);

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

            // validate the account id
            $accountData = $this->pushQuery("balance", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($accountData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

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
            $attachments = $filesObj->prep_attachments("accounts_transaction", $params->userId, $item_id);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
            $files->execute(["accounts_transaction", $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"]]);

            // log the user activity
            $this->userLogs("accounts_transaction", $item_id, null, "{$params->userData->name} added a new deposit", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Account deposit was successfully recorded.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}deposits"
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
     * @param String $params->transaction_id
     *
     * @return Array
     */
    public function update_deposit(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "accounts_transaction", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND item_type='Deposit' AND status='1' LIMIT 1");
            if(empty($prevData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

            // validate the account id
            $accountData = $this->pushQuery("balance", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($accountData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

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
            $this->userLogs("accounts_transaction", $params->transaction_id, $prevData[0], "{$params->userData->name} updated the existing deposit record", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Deposit record was successfully updated.", 
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
            $item_id = random_string("alnum", 15);

            // validate the account id
            $accountData = $this->pushQuery("balance", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($accountData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

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
            $attachments = $filesObj->prep_attachments("accounts_transaction", $params->userId, $item_id);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
            $files->execute(["accounts_transaction", $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"]]);

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
            if(empty($prevData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

            // validate the account id
            $accountData = $this->pushQuery("balance", "accounts", "item_id='{$params->account_id}' AND client_id='{$params->clientId}' LIMIT 1");
            if(empty($accountData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

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
            $this->userLogs("accounts_transaction", $params->transaction_id, $prevData[0], "{$params->userData->name} updated the existing expense record", $params->userId);

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

        // confirm that the account is not empty
        if(!empty($accounts_list)) {

            $count = count($accounts_list);
            $html_content = "";

            // get the client logo content
            if(!empty($params->client_data->client_logo)) {
                $type = pathinfo($params->client_data->client_logo, PATHINFO_EXTENSION);
                $logo_data = file_get_contents($params->client_data->client_logo);
                $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
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
                                ".(!empty($params->client_data->client_logo) ? "<img width=\"70px\" src=\"{$client_logo}\">" : "")."
                                <h4 style=\"color:#6777ef;font-family:helvetica;padding:0px;margin:0px;\">".strtoupper($params->client_data->client_name)."</h4>
                            </td>
                            <td align='center'>
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
                            <thead>
                                <tr style='text-transform:uppercase;font-size:10px;'>
                                    <th>Date Created</th>
                                    <th>Item Date</th>
                                    <th>Description</th>
                                    <th>Debits</th>
                                    <th>Credits</th>
                                </tr>
                            </thead>";
                        
                        if(!empty($transactions)) {
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
                                    <td style='padding:7px;border:solid 1px #dee2e6;'>".($value->item_type == "Expense" ? number_format($value->amount, 2) : null)."</td>
                                    <td style='padding:7px;border:solid 1px #dee2e6;'>".($value->item_type == "Deposit" ? number_format($value->amount, 2) : null)."</td>
                                </tr>";
                            }
                        } else {
                            $html_content .= "
                            <tr>
                                <td colspan='5' style='height:50px' align='center'><strong>***NO TRANSACTION FOUND***</strong></td>
                            </tr>";
                        }
                $html_content .= "
                            <tr>
                                <td colspan='3' align='center'><strong>***END OF STATEMENT***</strong></td>
                                <td style='padding:7px;border:solid 1px #dee2e6;height:50px'>".number_format($total_debits, 2)."</td>
                                <td style='padding:7px;border:solid 1px #dee2e6;height:50px'>".number_format($total_credits, 2)."</td>
                            </tr>
                    </table>
                    </div>
                </div>";


                $html_content .= $count !== $key + 1 ? "\n<div class=\"page_break\"></div>" : null;
            }
        } else {
            $html_content = "<h3>Sorry! An invalid request was parsed.</h3>";
        }


        return $html_content;
    }

}