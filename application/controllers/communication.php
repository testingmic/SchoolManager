<?php 

class Communication extends Myschoolgh {
    
    public function __construct($params = null) {
        parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;
    }

    /**
     * List Templates
     * 
     * @return Array
     */
    public function list_messages(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->type) ? " AND a.type='{$params->type}'" : null;
        $params->query .= !empty($params->q) ? " AND a.campaign_name LIKE '%{$params->q}%'" : null;
        $params->query .= !empty($params->status) ? " AND a.sent_status='{$params->status}'" : null;
        $params->query .= !empty($params->message_id) ? " AND a.item_id='{$params->message_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.user_type) 
                    FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info
                FROM smsemail_send_list a
                WHERE {$params->query} ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // clean the text
                $result->message = htmlspecialchars_decode($result->message);
                $result->raw_message = htmlspecialchars($result->message);

                $result->recipient_ids = json_decode($result->recipient_ids, true);
                $result->recipient_list = json_decode($result->recipient_list);
                $result->recipient_group = ucwords($result->recipient_group);

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
     * List Templates
     * 
     * @return Array
     */
    public function list_templates(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q) && !empty($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->type) && !empty($params->type)) ? " AND a.type='{$params->type}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->template_id) && !empty($params->template_id)) ? " AND a.item_id='{$params->template_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info
                FROM smsemail_templates a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // clean the text
                $result->message = htmlspecialchars_decode($result->message);
                $result->raw_message = htmlspecialchars($result->message);

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
     * Add a template
     * 
     * @param Object    $params
     * @param String $params->name
     * @param String $params->type
     * @param String $params->message
     * 
     * @return Array
     */
    public function add_template(stdClass $params) {

        try {
            
            // create a new item id
            $item_id = random_string("alnum",  12);

            // clean the template
            $params->message = !empty($params->message) ? custom_clean(htmlspecialchars_decode($params->message)) : null;
            $params->message = !empty($params->message) ? htmlspecialchars($params->message) : null;

            // prepare and execute the statement
            $stmt = $this->db->prepare("INSERT INTO smsemail_templates SET 
                item_id = ?, name = ?, message = ?, type = ?, client_id = ?, created_by = ?,
                academic_year = ?, academic_term = ?, module = ?   
            ");
            $stmt->execute([$item_id, $params->name, $params->message, $params->type, 
                $params->clientId, $params->userId, $params->academic_year, 
                $params->academic_term, $params->module ?? null
            ]);

            // log the user activity
            $this->userLogs("smsemail_template", $item_id, null, "{$params->userData->name} added a {$params->type} template", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Template was successfully added.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}{$params->type}_template"
                ]
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        } 
    }

    /**
     * Update a template
     * 
     * @param Object    $params
     * @param String $params->name
     * @param String $params->message
     * @param String $params->template_id
     * 
     * @return Array
     */
    public function update_template(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "smsemail_templates", "item_id='{$params->template_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($prevData)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // clean the template
            $params->message = custom_clean(htmlspecialchars_decode($params->message));
            $params->message = htmlspecialchars($params->message);
            
            // prepare and execute the statement
            $stmt = $this->db->prepare("UPDATE smsemail_templates SET name = ?, message = ?
                ".(isset($params->module) ? ", module='{$params->module}'" : null)." WHERE item_id = ? AND client_id = ? LIMIT 1");
            $stmt->execute([$params->name, $params->message, $params->template_id, $params->clientId]);

            // log the user activity
            $this->userLogs("smsemail_template", $params->template_id, $prevData[0], "{$params->userData->name} updated the template details.", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Template was successfully updated.", 
                "additional" => [
                    "href" => "{$this->baseUrl}{$params->type}_template"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Send an SMS or Email Message
     * 
     * @param Object    $params
     * @param String    $params->reminder_subject
     * @param String    $params->message
     * @param Array     $params->send_mode
     * @param Array     $params->recipients
     * @param String    $params->class_id
     * @param String    $params->send_later
     * @param Date      $params->schedule_date
     * @param Time      $params->schedule_time
     * 
     * @return Array
     */
    public function send_reminder(stdClass $params) {

        try {

            global $defaultClientData;

            // set the send mode
            $params->send_mode = ['sms']; // disable email for now

            // validate the recipient type
            if(!isset($params->send_mode)) {
                return ["code" => 400, "data" => "Sorry! An invalid sending mode."];
            }

            // get the message to be sent type
            $isSMS = (bool) in_array("sms", $params->send_mode);
            $isEmail = (bool) in_array("email", $params->send_mode);

            // if the recipient array is empty then return error
            if(empty($params->recipients)) {
                return ["code" => 400, "data" => $this->is_required("Reminder Recipient")];
            }

            // recipient list must be a valid array
            if(!is_array($params->recipients)) {
                return ["code" => 400, "data" => "Sorry! The recipient list must be a valid array."];
            }

            // set the init variables
            $units = 0;
            $actual_recipients_array = [];

            // if no class id was selected
            if(empty($params->class_id)) { return ["code" => 400, "data" => $this->is_required("Class ID")]; }

            // old record
            $class_check = $this->pushQuery("id, name", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // return error message
            if(empty($class_check)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

            // get the recipients array list
            $actual_recipients_array = $this->pushQuery(
                "name, unique_id, item_id, phone_number, email", "users", 
                "client_id='{$params->clientId}' AND status='1' AND 
                item_id IN {$this->inList($params->recipients)} AND 
                user_status = 'Active' AND class_id='{$class_check[0]->id}' LIMIT {$this->global_limit}"
            );

            // return false if the recipients list is empty
            if(empty($actual_recipients_array)) {
                return ["code" => 400, "data" => "Sorry! No recipient found."];
            }

            // parameter
            $fees_param = (object) [
                "userData" => $params->userData,
                "clientId" => $params->clientId,
                "client_data" => $defaultClientData,
                "save_bill" => true
            ];

            // create new object
            $feesObj = load_class("fees", "controllers", $fees_param);

            // get the outstanding bill
            $billing = $feesObj->outstanding_bill($params);

            $entireMessage = "";
            $studentMessages = [];
            foreach($actual_recipients_array as $student) {
                $bill = $billing['data'][$student->item_id];

                // replace the placeholders with the actual values
                $theMessage = str_ireplace(["{student_name}", "{fees_balance}"], [$bill->name, $bill->totalArrears], $params->message);

                // append the message to the entire message
                $entireMessage .= $theMessage;

                // set the message to the student
                $student->message_text = $theMessage;

                // append the message to the student messages
                $studentMessages[$student->item_id] = $student;
            }

            // perform this action if the message type is sms
            if($isSMS) {

                // calculate the message text count
                $message_count = ceil(strlen($entireMessage) / $this->sms_text_count);
                
                // get the sms balance
                $balance = $this->pushQuery("sms_balance", "smsemail_balance", "client_id='{$params->clientId}' LIMIT 1");
                $balance = $balance[0]->sms_balance ?? 0;

                // messages to send
                $units = $message_count;

                // return error if the balance is less than the message to send
                if($units > $balance) {
                    if(empty($class_check)) { return ["code" => 400, 
                        "data" => "Sorry! Your SMS Balance is insufficient to send this message.\n
                            You have {$balance} units left. However, you would required {$units} units to send the message.
                        "]; 
                    }
                }

            }

            // convert into json string
            $json = json_encode($actual_recipients_array);
            $actual_recipients_array = json_decode($json, true);

            // get the list of all user ids
            $recipient_ids = array_column($actual_recipients_array, "item_id");

            // set the scheduled date and time
            $params->schedule_time = empty($params->schedule_time) ? date("H:i:s") : $params->schedule_time;
            $params->schedule_date = empty($params->schedule_date) ? date("Y-m-d") : $params->schedule_date;

            // set the time to send the message
            $time_to_send = empty($params->schedule_date) ? date("Y-m-d H:i:s", strtotime("+2 minutes")) : "{$params->schedule_date} {$params->schedule_time}";
            
            // check the time to ensure its not less than current time
            if(strtotime($time_to_send) < time()) {
                return ["code" => 400, "data" => "Sorry! The scheduled time and date must be above current time."];
            }

            // generate the message unique id
            $item_id = random_string("alnum",  12);

            // insert the record
            $stmt = $this->db->prepare("
                INSERT INTO smsemail_send_list SET client_id = ?, item_id = ?, type = ?, campaign_name = ?,
                subject = ?, message = ?, recipient_list = ?, recipient_ids = ?,
                units_used = ?, schedule_time = ?, created_by = ?, send_mode = ?
            ");
            $stmt->execute([
                $params->clientId, $item_id, "reminder", $params->reminder_subject, $params->subject ?? null,
                $params->message, json_encode($actual_recipients_array), json_encode($recipient_ids), 
                $units, $time_to_send, $params->userId, json_encode($params->send_mode)
            ]);

            // if the units to be used is not zero
            if(!empty($units)) {
                // reduce the SMS balance
                $this->db->query("UPDATE smsemail_balance SET sms_balance = (sms_balance - {$units}), sms_sent = (sms_sent + {$units}) WHERE client_id = '{$params->clientId}' LIMIT 1");
            }

            // if the reminder must also be sent via email
            if($isEmail) {

                // generate the email list of the students
                foreach($actual_recipients_array as $each) {
                    // send only when the email is not empty
                    if(!empty($each["email"])) {
                        // generate the bill
                        $fees_param->student_id = $each["item_id"];
                        $fullname = $this->remove_quotes($each["name"]);
                        $fees_param->recipient_list = json_encode([$fullname => trim($each["email"])]);
                        // prepare the bill and insert the log
                        $feesObj->bill($fees_param);
                    }
                }
            }

            // return the success response
            return [
                "data" => "Reminders was successfully sent to the selected recipients.",
                "additional" => [
                    "item_id" => $item_id
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Send an SMS or Email Message
     * 
     * @param Object    $params
     * @param String    $params->campaign_name
     * @param String    $params->template_id
     * @param String    $params->message
     * @param String    $params->recipient_type
     * @param Array     $params->recipients
     * @param String    $params->class_id
     * @param String    $params->send_later
     * @param Date      $params->schedule_date
     * @param Time      $params->schedule_time
     * @param String    $params->type
     * 
     * @return Array
     */
    public function send_smsemail(stdClass $params) {

        try {

            // begin the transaction
            $this->db->beginTransaction();

            // get the message to be sent type
            $isSMS = (bool) ($params->type == "sms");

            // validate the message type (email or sms)
            if(!in_array($params->type, ["email", "sms"])) {
                return ["code" => 400, "data" => "Sorry! An invalid request type was parsed. Must either be 'email' or 'sms'."];
            }

            // validate the recipient type
            if(!in_array($params->recipient_type, ["group", "individual", "class"])) {
                return ["code" => 400, "data" => "Sorry! An invalid recipient group type was parsed. Must either be 'group', 'individual' or 'class'."];
            }

            // if the recipient array is empty then return error
            if(empty($params->recipients)) {
                return ["code" => 400, "data" => $this->is_required("Message Recipient")];
            }

            // recipient list must be a valid array
            if(!is_array($params->recipients)) {
                return ["code" => 400, "data" => "Sorry! The recipient list must be a valid array."];
            }

            // set the init variables
            $units = 0;
            $recipients_array = [];
            $actual_recipients_array = [];
            $column = $isSMS ? "phone_number" : "email";

            // class the class id if it actually exists
            if($params->recipient_type == "class") {

                // if no class id was selected
                if(empty($params->class_id)) { return ["code" => 400, "data" => $this->is_required("Class ID")]; }

                // old record
                $class_check = $this->pushQuery("id, name", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

                // return error message
                if(empty($class_check)) { return ["code" => 400, "data" => "Sorry! An invalid id was supplied."]; }

                // get the recipients array list
                $recipients_array = $this->pushQuery(
                    "name, user_type, unique_id, item_id, {$column}", "users", 
                    "client_id='{$params->clientId}' AND status='1' AND user_status = 'Active'
                        AND item_id IN {$this->inList($params->recipients)}
                        AND class_id='{$class_check[0]->id}' LIMIT {$this->global_limit}"
                );
            }

            // if the recipient_type is individual but no user was selected.
            if($params->recipient_type == "individual") {

                // if the recipients list was not parsed
                if(!isset($params->recipients) || empty($params->recipients)) { 
                    return ["code" => 400, "data" => $this->is_required("Message Recipient")];
                }

                // if the $params->recipients is an array list
                if(is_array($params->recipients)) {

                    // loop through the array list
                    foreach($params->recipients as $recipient) {

                        // get the user info
                        $user = $this->pushQuery(
                            "name, user_type, unique_id, item_id, {$column}", "users", 
                            "client_id='{$params->clientId}' AND status='1' AND 
                            user_status = 'Active' AND item_id = '{$recipient}' LIMIT 1");

                        // append to the list
                        if(!empty($user)) { $recipients_array[] = $user[0]; }
                    }
                }
            }

            // if the recipient_type is group but no role group was selected.
            if($params->recipient_type == "group") {

                // if the role_group was not parsed
                if(!isset($params->role_group) || empty($params->role_group)) { 
                    return ["code" => 400, "data" => $this->is_required("Role Group")];
                }

                // get the recipients array list
                $recipients_array = $this->pushQuery(
                    "name, user_type, unique_id, item_id, {$column}", "users", "
                    client_id='{$params->clientId}' AND
                    user_type IN {$this->inList($params->role_group)} AND 
                    item_id IN {$this->inList($params->recipients)} AND 
                    user_type != 'student' LIMIT {$this->global_limit}"
                );
            }

            // return false if the recipients list is empty
            if(empty($recipients_array)) {
                return ["code" => 400, "data" => "Sorry! No recipient found ."];
            }

            // loop through the recipients array
            foreach($recipients_array as $recipient) {
                if(in_array($recipient->item_id, $params->recipients)) {
                    $actual_recipients_array[] = $recipient;
                }
            }

            // return false if the recipients list is empty
            if(empty($actual_recipients_array)) {
                return ["code" => 400, "data" => "Sorry! No recipient found ."];
            }

            // perform this action if the message type is sms
            if($isSMS) {

                // calculate the message text count
                $chars = strlen($params->message);
                $message_count = ceil($chars / $this->sms_text_count);
                
                // get the sms balance
                $balance = $this->pushQuery("sms_balance", "smsemail_balance", "client_id='{$params->clientId}' LIMIT 1");
                $balance = $balance[0]->sms_balance ?? 0;

                // messages to send
                $units = $message_count * count($actual_recipients_array);

                // return error if the balance is less than the message to send
                if($units > $balance) {
                    if(empty($class_check)) { return ["code" => 400, 
                        "data" => "Sorry! Your SMS Balance is insufficient to send this message. 
                            You have {$balance} units left. However, you would required {$units} units to send the message.
                        "]; 
                    }
                }

            }

            // convert into json string
            $json = json_encode($actual_recipients_array);
            $actual_recipients_array = json_decode($json, true);

            // get the list of all user ids
            $recipient_ids = array_column($actual_recipients_array, "item_id");

            // set the scheduled date and time
            $params->schedule_time = empty($params->schedule_time) ? date("H:i:s") : $params->schedule_time;
            $params->schedule_date = empty($params->schedule_date) ? date("Y-m-d") : $params->schedule_date;

            // set the time to send the message
            $time_to_send = empty($params->schedule_date) ? date("Y-m-d H:i:s", strtotime("+2 minutes")) : "{$params->schedule_date} {$params->schedule_time}";

            // check the time to ensure its not less than current time
            if(strtotime($time_to_send) < time()) {
                return ["code" => 400, "data" => "Sorry! The scheduled time and date must be above current time."];
            }

            // generate the message unique id
            $item_id = random_string("alnum",  12);

            // insert the record
            $stmt = $this->db->prepare("
                INSERT INTO smsemail_send_list SET client_id = ?, item_id = ?, type = ?, campaign_name = ?,
                subject = ?, message = ?, recipient_group = ?, recipient_list = ?, recipient_ids = ?,
                units_used = ?, schedule_time = ?, created_by = ?
            ");
            $stmt->execute([
                $params->clientId, $item_id, $params->type, $params->campaign_name, 
                $params->subject ?? $params->campaign_name,
                $params->message, $params->recipient_type, json_encode($actual_recipients_array),
                json_encode($recipient_ids), $units, $time_to_send, $params->userId
            ]);

            // if the units to be used is not zero
            if(!empty($units)) {
                // reduce the SMS balance
                $this->db->query("UPDATE smsemail_balance SET sms_balance = (sms_balance - {$units}), sms_sent = (sms_sent + {$units}) WHERE client_id = '{$params->clientId}' LIMIT 1");
            }

            // commit the prepared statements
            $this->db->commit();

            // return the success response
            return [
                "data" => "Message was successfully sent to the selected recipients.",
                "additional" => [
                    "item_id" => $item_id
                ]
            ];

        } catch(PDOException $e) {
            $this->db->rollBack();
            return $this->unexpected_error;
        }

    }

    /**
     * Verify Payment and Update the SMS Balance
     * 
     * @param Object    $params
     * @return Array
     */
    public function verify_and_update(stdClass $params) {
        
        try {
            // check if any item is empty
            if(empty($params->package_id) || empty($params->reference_id) || empty($params->transaction_id)) {
                return ["code" => 400, "data" => "Sorry! Ensure all required parameters have been parsed."];
            }

            // check if the transaction id already exits
            $transaction = $this->pushQuery("id", "transaction_logs", "transaction_id='{$params->transaction_id}' AND state='Processed' LIMIT 1");
            if(!empty($transaction)) {
                return ["code" => 400, "data" => "Sorry! This transaction has already been processed."];
            }

            // validate the package
            $sms_package = $this->pushQuery("*", "sms_packages", "item_id='{$params->package_id}' LIMIT 1");
            if(empty($sms_package)) {
                return ["code" => 400, "data" => "Sorry! An invalid package id was parsed."];
            }

            // create a new payment object
            $payObject = load_class("payment", "controllers");

            // set the parameters
            $data = (object) [
                "route" => "verify",
                "reference" => $params->reference_id
            ];

            // confirm the payment
            $payment_check = $payObject->get($data);
            
            // if payment status is true
            if($payment_check["data"]->status === true) {

                // set the amount 
                $amount = (int) ($payment_check["data"]->data->amount / 100);
                $db_amount = (int) $sms_package[0]->amount;
                
                // confirm that the user actually paid the right amount for the package
                if($amount !== $db_amount) {
                    // log the attempt to bypass the system security
                    $this->db->query("INSERT INTO security_logs SET 
                        client_id='{$params->clientId}', created_by='{$params->userId}', section='SMS Topup',
                        description='The user attempted to purchase <strong>{$sms_package[0]->units} units</strong> of SMS\'s
                        with an amount of <strong>{$amount}</strong> which actually costs {$sms_package[0]->amount}'
                    ");
                    // return a warning to the user.
                    return ["code" => 400, "data" => "Sorry! You attempted to purchase a different SMS Package with an amount that is lower/higher than required. Your Account may be terminated should you try this again in future."];
                }
                
                // update the user sms balance
                $this->db->query("UPDATE smsemail_balance SET sms_balance=(sms_balance + {$sms_package[0]->units}) WHERE client_id='{$params->clientId}' LIMIT 1");
                
                // update the total amount purchased on the package
                $this->db->query("UPDATE sms_packages SET amount_purchased=(amount_purchased + {$amount}) WHERE item_id='{$params->package_id}' LIMIT 1");
                
                // log the transaction information
                $this->db->query("
                    INSERT INTO transaction_logs SET client_id = '{$params->clientId}',
                    transaction_id = '{$params->transaction_id}', endpoint = 'sms',
                    reference_id = '{$params->reference_id}', amount='{$amount}',
                    payment_data='".json_encode($payment_check["data"])."', state='Processed'
                ");

                // log the user activity
                $this->userLogs("sms_topup", $params->package_id, null,  "{$params->userData->name} purchased {$sms_package[0]->units} sms units at the rate of {$payment_check["data"]->data->currency}{$amount}.", $params->userId);

            } else {
                return ["code" => 400, "data" => "Sorry! An error was encountered while processing the request."];
            }
            
            // validate the package
            $package = $this->pushQuery("sms_balance", "smsemail_balance", "client_id='{$params->clientId}' LIMIT 1")[0];
            
            // return the response message
            return [ "data" => $package->sms_balance ];
            
        } catch(PDOException $e) {}

    }


    /**
     * Log the information and set the information to return
     * 
     * @param Object    $params
     * @return Array
     */
    public function log(stdClass $params) {

        try {

            // global variables
            global $defaultClientData;

            // get the data parsed
            if(!is_array($params->data)) {
                return ["code" => 400, "data" => "Sorry! An array data is required."];
            }

            // get the email address parsed
            $email = $params->data["email"] ?? null;

            // confirm the email address to be a valid email address
            if(!empty($email)) {
                // validate the email address
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return ["code" => 400, "data" => "Sorry! A valid email address is required."];
                }
            }

            // if email is empty then set it to the client email address
            $email = empty($email) ? $defaultClientData->client_email : $email;

            // check and confirm the package id
            if(!isset($params->data["package_id"])) {
                return ["code" => 400, "data" => "Sorry! The package_id is required."];
            }

            // validate the package
            $sms_package = $this->pushQuery("*", "sms_packages", "item_id='{$params->data["package_id"]}' LIMIT 1");
            if(empty($sms_package)) {
                return ["code" => 400, "data" => "Sorry! An invalid package id was parsed."];
            }

            // return the data 
            return [
                "code" => 200,
                "data" => [
                    "pk_public_key" => $this->pk_public_key,
                    "email" => $email,
                    "amount" => ($sms_package[0]->amount * 100),
                    "currency" => "GHS"
                ]
            ];

        } catch(PDOException $e) {}

    }
}