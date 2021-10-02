<?php

class Chats extends Myschoolgh {

    private $message_id;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Load the user chats with the other recipient
     * 
     * @param \stdClass $params
     * @param String    $params->user_id
     * @param Int       $params->limit
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        try {

            // load the chats list that has the seen status as null
            if(!isset($params->apply_seen_status)) {
                // so long as you have clicked to load the chats of the user then it 
                // must taken as you having read the messages sent to you by the user
                $this->db->query("
                    UPDATE users_chat SET seen_status = '1', seen_date = now()
                    WHERE sender_id='{$params->user_id}' AND receiver_id='{$params->userId}' AND seen_status='0'
                ");
            }

            // confirm if the message id is not empty
            $msg_id = isset($params->message_id) ? $params->message_id : null;

            // set additional query to use
            $query = "
                (a.sender_id = '{$params->user_id}' AND a.receiver_id = '{$params->userId}' AND a.receiver_deleted = '0') OR 
                (a.receiver_id = '{$params->user_id}' AND a.sender_id = '{$params->userId}' AND a.sender_deleted = '0')
            ";

            // if the apply seen status is parsed in the query
            if(isset($params->apply_seen_status)) {
                $query = "(a.sender_id = '{$params->user_id}' AND a.receiver_id = '{$params->userId}' AND a.receiver_deleted = '0')
                AND a.seen_status='0' AND a.message_unique_id = '{$msg_id}'";
            }

            // prepare the query and execute it
            $stmt = $this->db->prepare("
                SELECT 
                    a.id AS item_id, a.message_unique_id, a.sender_id, a.receiver_id, a.message AS full_message, 
                    a.date_created, a.seen_status, a.seen_date, DATE(a.date_created) AS raw_date,
                    (SELECT CONCAT(name,'|',COALESCE(phone_number,'NULL'),'|',COALESCE(email,'NULL'),'|',image,'|',COALESCE(last_seen,'NULL'),'|',online) FROM users WHERE users.item_id = a.sender_id LIMIT 1) AS sender_info,
                    (SELECT CONCAT(name,'|',COALESCE(phone_number,'NULL'),'|',COALESCE(email,'NULL'),'|',image,'|',COALESCE(last_seen,'NULL'),'|',online) FROM users WHERE users.item_id = a.receiver_id LIMIT 1) AS receiver_info
                FROM users_chat a WHERE {$query}
                ORDER BY TIMESTAMP(a.date_created) DESC LIMIT 100
            ");
            $stmt->execute();

            $data = [];
            $unread_count = 0;

            // loop through the results list
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // increment the unseen messages count
                if($result->seen_status == 0) {
                    $unread_count++;
                }

                $msg_id = $result->message_unique_id;

                // convert the sender and receiver information into an object
                $result->sender_info = (object) $this->stringToArray($result->sender_info, "|", ["name", "contact", "email", "image","last_seen","online"]);
                $result->receiver_info = (object) $this->stringToArray($result->receiver_info, "|", ["name", "contact", "email", "image","last_seen","online"]);

                // online algorithm (user is online if last activity is at most 5minutes ago)
                $result->sender_info->online = $this->user_is_online($result->sender_info->last_seen);
                $result->receiver_info->online = $this->user_is_online($result->receiver_info->last_seen);

                // convert the seen and sent dates into ago state
                $result->clean_date = date("l, F jS", strtotime($result->date_created));
                $result->sent_time = date("h:i A", strtotime($result->date_created));
                $result->seen_time = !empty($result->seen_date) ? time_diff($result->seen_date) : null;
                $result->seen_timer = !empty($result->seen_date) ? date("h:iA", strtotime($result->seen_date)) : null;
                $result->sent_ago = time_diff($result->date_created);

                // send the raw message
                $result->seen_status = (int) $result->seen_status;
                $result->raw_message = $result->full_message;

                $result->timestamp = strtotime($result->date_created);

                $data[$result->timestamp] = $result;
            }

            // return the messages list
            return [
                "code" => 200,
                "data" => [
                    "messages" => [
                        $msg_id => $data
                    ],
                    "message_id" => $msg_id,
                    "unread_count" => $unread_count
                ]
            ];            

        } catch(PDOException $e) {
            return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
        }
            
    }

    /**
     * User recent messages. Properly format the results to return 
     * 
     * @param String $userId
     * 
     * @return Array
     */
    public function recent($userId, $limit = 100) {

        try {

            // prepare the query and execute it
            $stmt = $this->db->prepare("
                SELECT 
                    DISTINCT a.message_unique_id, a.id AS item_id, a.message_unique_id, a.sender_id, a.receiver_id, a.message AS full_message, 
                    a.date_created, a.seen_status, a.seen_date, DATE(a.date_created) AS raw_date,
                    (SELECT CONCAT(name,'|',COALESCE(phone_number,'NULL'),'|',COALESCE(email,'NULL'),'|',image,'|',COALESCE(last_seen,'NULL'),'|',online) FROM users WHERE users.item_id = a.receiver_id LIMIT 1) AS receipient_info,
                    (SELECT CONCAT(name,'|',COALESCE(phone_number,'NULL'),'|',COALESCE(email,'NULL'),'|',image,'|',COALESCE(last_seen,'NULL'),'|',online) FROM users WHERE users.item_id = a.sender_id LIMIT 1) AS sender_info
                FROM users_chat a WHERE 
                    (a.receiver_id = '{$userId}' AND a.receiver_deleted = '0') OR
                    (a.sender_id = '{$userId}' AND a.sender_deleted = '0')
                ORDER BY TIMESTAMP(a.date_created) DESC LIMIT {$limit}
            ");
            $stmt->execute();

            $data = [];
            $msg_id = "";
            $unread_count = 0;

            $unread_countArray = [];

            // loop through the results list
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // variable for the message id
                $msg_id = $result->message_unique_id;

                // increment the unseen messages count
                if(($result->seen_status == 0)) {
                    $unread_count++;

                    // confirm that the key has been set. If set then add 1 to the value
                    if(isset($unread_countArray[$msg_id])) {
                        $unread_countArray[$msg_id] = $unread_countArray[$msg_id]+1;
                    } else {
                        $unread_countArray[$msg_id] = 1;
                    }
                }

                // convert the sender and receiver information into an object
                if($result->sender_id === $userId) {
                    $result->receipient_info = (object) $this->stringToArray($result->receipient_info, "|", ["name", "contact", "email", "image", "last_seen", "online"]);
                    $result->receipient_info->online = $this->user_is_online($result->receipient_info->last_seen);
                    $result->receipient_info->offline_ago = time_diff($result->receipient_info->last_seen);
                } else {
                    // set the information to submit
                    $result->receiver_id = $result->sender_id;
                    $result->receipient_info = (object) $this->stringToArray($result->sender_info, "|", ["name", "contact", "email", "image", "last_seen", "online"]);
                    $result->receipient_info->online = $this->user_is_online($result->receipient_info->last_seen);
                    $result->receipient_info->offline_ago = time_diff($result->receipient_info->last_seen);
                }

                // convert the seen and sent dates into ago state
                $result->clean_date = date("l, F jS", strtotime($result->date_created));
                $result->sent_time = date("h:i A", strtotime($result->date_created));
                $result->seen_time = time_diff($result->seen_date);
                $result->sent_ago = time_diff($result->date_created);

                // send the raw message
                $result->seen_status = (int) $result->seen_status;
                $result->message = limit_words($result->full_message, 10);
                $result->raw_message = strip_tags($result->full_message);

                $result->timestamp = strtotime($result->date_created);

                $data[$msg_id][$result->timestamp] = $result;
            }

            // new parameter
            $messages = [];

            // loop through the list and then get only first item from the list
            foreach($data as $key => $value) {
                // count the number of rows found
                $count = $unread_countArray[$key] ?? 0;
                $arrayKey = array_keys($value)[0];
                $max_key = $value[$arrayKey];

                // append to the messages array
                $messages[$key] = [
                    "unread_count" => $count,
                    "message" => $max_key
                ];
            }

            // return the messages list
            return [
                "messages" => $messages,
                "unread_count" => $unread_count,
                "unread_count_array" => $unread_countArray
            ];            

        } catch(PDOException $e) {
            return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
        }

    }

    /**
     * Unread Messages List
     * 
     * @return Array
     */
    public function unread_list($userId, $senderId) {

        $list = $this->pushQuery(
            "a.id, a.sender_id, c.last_seen, c.image, c.name, a.message_unique_id, a.message, a.date_created",
            "users_chat a LEFT JOIN users c ON c.item_id = a.sender_id", 
            "a.receiver_id='{$userId}' AND a.sender_id = '{$senderId}' AND a.seen_status='0' LIMIT 20"
        );
        $chats = [];
        foreach($list as $chat) {
            $chat->sent_time = date("h:i A", strtotime($chat->date_created));
            $chats[] = $chat;
        }
        return $chats;
    }

    /**
     * Chat Alerts
     * 
     * Get the list of messages from various users
     * 
     * @return Array
     */
    public function alerts(stdClass $params) {
        
        try {

            $stmt = $this->db->prepare("SELECT 
                    COUNT(*) AS chats_count, a.sender_id, 
                    c.last_seen, c.image, c.name, a.message_unique_id
                FROM users_chat a
                LEFT JOIN users c ON c.item_id = a.sender_id
                WHERE a.receiver_id=? AND a.seen_status = ? GROUP BY a.sender_id LIMIT 20   
            ");
            $stmt->execute([$params->userId, 0]);

            $data = [];
            // loop through the list of alerts
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // set some more parameters
                $result->online = $this->user_is_online($result->last_seen);
                $result->offline_ago = time_diff($result->last_seen);
                $data[$result->sender_id]["count"] = $result;
                $data[$result->sender_id]["message_id"] = $result->message_unique_id;
                $data[$result->sender_id]["messages_list"] = $this->unread_list($params->userId, $result->sender_id);
            }


            return $data;

        } catch(PDOException $e) {}
    }

    /**
     * Update Chat Seen Status
     * 
     * @param String    $params->message_id
     * 
     * @return Bool
     */
    public function read(stdClass $params) {

        $stmt = $this->db->prepare("UPDATE users_chat SET seen_status = ?, seen_date = now()
            WHERE receiver_id = ? AND message_unique_id = ? ORDER BY id DESC LIMIT 10
        ");
        return $stmt->execute([1, $params->userId, $params->message_id]);

    }

    /**
     * Search User
     * 
     * Return the list of users that matches the search term
     * 
     * @return Array
     */
    public function search_user(stdClass $params) {

        try {

            $stmt = $this->db->prepare("SELECT a.name, a.item_id AS user_id, a.unique_id, a.image, a.last_seen, 
                    (
                        SELECT c.message_unique_id 
                        FROM users_chat c 
                        WHERE   (c.sender_id = '{$params->userId}' AND c.receiver_id=a.item_id) OR
                                (c.receiver_id = '{$params->userId}' AND c.sender_id=a.item_id)
                        ORDER BY c.id DESC LIMIT 1
                    ) AS message_unique_id
                FROM users a
                WHERE a.client_id = ? AND a.user_status = ? AND a.item_id != ? AND
                    (a.name LIKE '%{$params->q}%' OR a.unique_id='{$params->q}')
            ");
            $stmt->execute([$params->clientId, 'Active', $params->userId]);

            $data = [];
            // loop through the results list
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // online algorithm (user is online if last activity is at most 5minutes ago)
                $result->online = $this->user_is_online($result->last_seen);
                $result->offline_ago = time_diff($result->last_seen);
                $result->message_unique_id = empty($result->message_unique_id) ? strtoupper(random_string('alnum', 16)) : $result->message_unique_id;

                $data[] = $result;
            }

            return $data;


        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Delete a message/conversation
     * 
     * @param \stdClass $params
     * @param String    $params->msg_id     The id of the delete to delete
     * @param String    $params->action       This denote how the delete should be executed
     */
    public function delete(stdClass $params) {

        // confirm that the action is either message or conversation
        if(!in_array($params->action, ["delete_message", "delete_conversation"])) {
            return;
        }

        // if the request is a message
        if($params->action == "delete_message") {
            // load the message
            $query = $this->pushQuery("id", "users_chat", "item_id='{$params->msg_id}' AND sender_id='{$params->userId}' LIMIT 1");
            
            // if the search is empty then end execution
            if(empty($query)) {
                return;
            }

            // delete the message
            $this->db->query("UPDATE users_chat SET sender_deleted='1' WHERE item_id='{$params->msg_id}' AND sender_id='{$params->userId}' LIMIT 1");

            // return success
            return ["code" =>  200, "data" => "Message successfully deleted"];
        }

        // if the user requests to delete a conversation
        elseif($params->action == "delete_conversation") {
            // load the messages with thax  t unique id
            $query = $this->pushQuery("id", "users_chat", "(message_unique_id='{$params->msg_id}' AND sender_id='{$params->userId}') OR (message_unique_id='{$params->msg_id}' AND receiver_id='{$params->userId}') LIMIT 1");
            
        }
    }

    /**
     * Load the Recent Read Messages
     * 
     * @param String    $userId
     * 
     * @return Array
     */
    public function recent_read($userId) {

        // get the list of read messages
        $list = $this->pushQuery(
            "message_unique_id, seen_date", "users_chat", 
            "sender_id='{$userId}' AND seen_status='1' ORDER BY id DESC LIMIT 10"
        );
        $chats = [];
        // loop through the results list
        foreach($list as $chat) {
            $chats[$chat->message_unique_id] = date("h:iA", strtotime($chat->seen_date));
        }
        return $chats;

    }

    /**
     * Send a Message to the User
     * 
     * @param
     */
    public function send(stdClass $params) {

        /** Generate a new id if the message id is empty */
		$params->message_id = (empty($params->message_id) || $params->message_id == "null") ? strtoupper(random_string("alnum", 24)) : $params->message_id;
		$this->message_id = $params->message_id;

        // initiate a connection and append the messages
        $last_insert_id = $this->save_message($params);

        return [
            "code" => 200,
            "data" => [
                "message_id" => $this->message_id,
                "last_insert_id" => $last_insert_id,
                "recent_read" => $this->recent_read($params->userId)
            ]
        ];

    }

    /**
	 * Save the chat message, if the message id is null then generate a new 
     * id and save it as part of the message
	 * 
	 * @param Object $chatMsg
	 * 
	 * @return Bool
	 */
	private function save_message($data) {

		try {
			/** Save the message log */
			$stmt = $this->db->prepare("INSERT INTO users_chat SET message_unique_id = ?, sender_id = ?, receiver_id = ?, message = ?, user_agent = ?");
			$stmt->execute([$this->message_id, $data->sender_id, $data->receiver_id, $data->message, $this->agent]);
            
            return $this->db->lastInsertId();

		} catch(\PDOException $e) {}
	}

    /**
	 * Get the user information using the ID
	 * 
	 * @param String $userId
	 * 
	 * @return Object
	 */
	private function userInfo($userId) {

		try {

            // set the messages to load
			$stmt = $this->db->prepare("SELECT firstname, name, email, item_id, image, phone_number, last_seen FROM users WHERE item_id = ? LIMIT 1");
			$stmt->execute([$userId]);
			$result = $stmt->fetch(\PDO::FETCH_OBJ);

            // set some more parameters
            $result->online = $this->user_is_online($result->last_seen);
            $result->offline_ago = time_diff($result->last_seen);

            return $result;

		} catch(\PDOException $e) {}
	}

    /**
     * Load unsed messages from the sender
     * 
     * @param String    $params->message_id
     * @param String    $params->sender_id
     * 
     * @return Object
     */
    public function unread(stdClass $params) {

        /** Load user chats that has not yet been seen */
        $data = (object) [
            "apply_seen_status" => true,
            "userId" => $params->userId,
            "message_id" => $params->message_id,
            "user_id" => $params->sender_id
        ];
        $prev_messages = [];
        // $prev_messages = $this->list($data)["data"];

        /** Update the seeen status for messages between these two users */
        // $s_stmt = $this->db->prepare("UPDATE users_chat SET seen_status = ?, seen_date=now() WHERE receiver_id = ? AND sender_id = ? AND seen_status = ? LIMIT 20");
        // $s_stmt->execute([1, $params->sender_id, $params->userId, 0]);
        
        /** Return the results */
        return [
            "code" => 200,
            "data" => [
                "message_id" => $params->message_id,
                "prev_list" => $prev_messages
            ]
        ];
    }

}

?>