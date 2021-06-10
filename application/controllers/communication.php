<?php 

class Communication extends Myschoolgh {

    private $iclient = [];

	public function __construct(stdClass $params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data;
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term;
        $this->academic_year = $client_data->client_preferences->academics->academic_year;
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
     * @param String $params->name
     * @param String $params->type
     * @param String $params->message
     * 
     * @return Array
     */
    public function add_template(stdClass $params) {

        try {
            
            // create a new item id
            $item_id = random_string("alnum", 15);

            // clean the template
            $params->message = custom_clean(htmlspecialchars_decode($params->message));
            $params->message = htmlspecialchars($params->message);

            // prepare and execute the statement
            $stmt = $this->db->prepare("INSERT INTO smsemail_templates SET 
                item_id = ?, name = ?, message = ?, type = ?, client_id = ?, created_by = ?,
                academic_year = ?, academic_term = ?    
            ");
            $stmt->execute([$item_id, $params->name, $params->message, $params->type, 
                $params->clientId, $params->userId, $params->academic_year, $params->academic_term
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
            return $this->unexpected_error;
        } 
    }

    /**
     * Update a template
     * 
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
            if(empty($prevData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

            // clean the template
            $params->message = custom_clean(htmlspecialchars_decode($params->message));
            $params->message = htmlspecialchars($params->message);
            
            // prepare and execute the statement
            $stmt = $this->db->prepare("UPDATE smsemail_templates SET name = ?, message = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
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

            // get the message to be sent type
            $isSMS = (bool) ($params->type == "sms");

            // validate the message type (email or sms)
            if(!in_array($params->type, ["email", "sms"])) {
                return ["code" => 203, "data" => "Sorry! An invalid request type was parsed. Must either be 'email' or 'sms'."];
            }

            // validate the recipient type
            if(!in_array($params->recipient_type, ["group", "individual", "class"])) {
                return ["code" => 203, "data" => "Sorry! An invalid recipient group type was parsed. Must either be 'group', 'individual' or 'class'."];
            }

            // set the init variables
            $recipients_array = [];
            $column = $isSMS ? "phone_number" : "email";

            // class the class id if it actually exists
            if($params->recipient_type == "class") {
                // if no class id was selected
                if(empty($params->class_id)) { return ["code" => 203, "data" => $this->is_required("Class ID")]; }

                // old record
                $class_check = $this->pushQuery("id, name", "classes", "item_id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
                if(empty($class_check)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

                // get the recipients array list
                $recipients_array = $this->pushQuery(
                    "name, user_type, unique_id, item_id, {$column}", "users", 
                    "client_id='{$params->clientId}' AND status='1' AND 
                    academic_year='{$this->academic_year}' AND user_status = 'Active' AND
                    academic_term='{$this->academic_term}' AND class_id='{$class_check[0]->id}'"
                );
            }

            // if the recipient_type is individual but no user was selected.
            if($params->recipient_type == "individual") {
                // if the recipients list was not parsed
                if(!isset($params->recipients) || empty($params->recipients)) { 
                    return ["code" => 203, "data" => $this->is_required("Message Recipient")];
                }
                // if the $params->recipients is an array list
                if(is_array($params->recipients)) {
                    // loop through the array list
                    foreach($params->recipients as $recipient) {
                        $recipients_array[] = $this->pushQuery(
                            "name, user_type, unique_id, item_id, {$column}", "users", 
                            "client_id='{$params->clientId}' AND status='1' AND user_status = 'Active' AND
                            academic_year='{$this->academic_year}' AND item_id = '{$recipient}' AND
                            academic_term='{$this->academic_term}' LIMIT 1"
                        )[0];
                    }
                }
            }

            // if the recipient_type is group but no role group was selected.
            if($params->recipient_type == "group") {

                // if the role_group was not parsed
                if(!isset($params->role_group) || empty($params->role_group)) { 
                    return ["code" => 203, "data" => $this->is_required("Role Group")]; 
                }
                // get the recipients array list
                $recipients_array = $this->pushQuery(
                    "name, user_type, unique_id, item_id, {$column}", "users", 
                    "client_id='{$params->clientId}' AND
                        user_type IN {$this->inList($params->role_group)} AND user_type != 'student'"
                );
                // get the list of students only
                $students_list = $this->pushQuery(
                    "name, user_type, unique_id, item_id, {$column}", "users", 
                    "client_id='{$params->clientId}' AND status='1' AND 
                    academic_year='{$this->academic_year}' AND user_status = 'Active' AND
                    academic_term='{$this->academic_term}' AND user_type = 'student'"
                );

                // merge the two results set
                $recipients_array = array_merge($recipients_array, $students_list);
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
                $units = $message_count * count($recipients_array);

                // return error if the balance is less than the message to send
                if($units > $balance) {
                    if(empty($class_check)) { return ["code" => 203, 
                        "data" => "Sorry! Your SMS Balance is insufficient to send this message. 
                            You have {$balance} units left. However, you would required {$units} units to send the message.
                        "]; 
                    }
                }

            }

            // set the scheduled date and time
            $params->schedule_time = empty($params->schedule_time) ? date("H:i:s") : $params->schedule_time;
            $params->schedule_date = empty($params->schedule_date) ? date("Y-m-d") : $params->schedule_date;

            // set the time to send the message
            $time_to_send = empty($params->send_later) ? date("Y-m-d H:i:s", strtotime("+2 minutes")) : "{$params->schedule_date} {$params->schedule_time}";

            
            // return the success response
            return $recipients_array;

            
        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

}