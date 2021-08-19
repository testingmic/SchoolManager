<?php 

class Emails extends Myschoolgh {

    private $labels;
    private $other_labels;
    private $the_user_type;
    private $the_user_id;

    public function __construct()
    {
        parent::__construct();

        $this->labels = ["inbox", "sent", "draft"];
        $this->other_labels = [
            "important" => "important_list",
            "favorite" => "favorite_list",
            "trash" => "trash_list"
        ];
    }

    /**
     * This function will be the main endpoint to process any request to the endpoint
     * 
     * @param stdClass $params
     * 
     * @return Array 
     */
    public function action(stdClass $params) {

        // convert the label into an array
        $params->action = is_array($params->action) ? $params->action : $this->stringToArray($params->action);

        // end query if no action was parsed
        if(!isset($params->action["action"])) {
            return;
        }

        // assign variable
        $action = $params->action["action"];

        // start the filter
        $filter = "";

        // perform some checks
        $this->the_user_type = $params->userData->user_type;
        $this->the_user_id = $params->userData->user_id;

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            // (a.company_id = '{$params->userData->company_id}') OR 
            $filter .= " AND ( (user_id = '{$params->userId}') OR (a.recipient_list LIKE '%{$this->the_user_id}%') OR (a.copy_recipients_list LIKE '%{$this->the_user_id}%') ) AND (a.deleted_list IS NOT NULL AND a.deleted_list NOT LIKE '%{$this->the_user_id}%' AND a.status = '1') AND (a.archive_list IS NOT NULL AND a.archive_list NOT LIKE '%{$this->the_user_id}%' AND a.status = '1')";
        }
        
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ( (user_id = '{$params->userId}') OR (a.recipient_list LIKE '%{$this->the_user_id}%') OR (a.copy_recipients_list LIKE '%{$this->the_user_id}%') ) AND (a.deleted_list IS NOT NULL AND a.deleted_list NOT LIKE '%{$this->the_user_id}%' AND a.status = '1') AND (a.archive_list  IS NOT NULL AND a.archive_list NOT LIKE '%{$this->the_user_id}%' AND a.status = '1')";
        }
        
        // the action to perform
        if($action == "mails_count") {

            // end the query if the request was not parsed
            if(!isset($params->action["labels"])) {
                return;
            }

            // mails counter
            $mails_count = [];
            // convert to array if not an array
            $labels = is_array($params->action["labels"]) ? $params->action["labels"] : $this->stringToArray($params->action["labels"]);

            // if count mails is in the list
            if(in_array("labels_count", $labels)) {

                // append to the filter
                $t_filter = "";
                $t_filter .= " AND (a.trash_list IS NOT NULL AND a.trash_list NOT LIKE '%{$this->the_user_id}%')";

                // loop through each label
                foreach($this->labels as $the_label) {

                    try {

                        // set the each label variable
                        $eachLabel = $the_label;

                        // if sent then show the information
                        if($the_label == "sent") {
                            $the_label = "inbox";
                            $t_filter .= " AND a.user_id = '{$this->the_user_id}'";
                        } else if($the_label == "inbox") {
                            $t_filter .= " AND a.user_id != '{$this->the_user_id}'";
                        }

                        $n_filter = $filter.$t_filter;

                        // run the query
                        $stmt = $this->db->prepare("SELECT COUNT(*) AS mails_count FROM users_emails a WHERE a.label=? {$n_filter}");
                        $stmt->execute([$the_label]);
                        
                        // fetch the results
                        $result = $stmt->fetch(PDO::FETCH_OBJ);

                        // append the the mials count array
                        $mails_count[$eachLabel] = isset($result->mails_count) ? $result->mails_count : 0;

                    } catch(PDOException $e) {}
                }
            }

            // get unread messages
            if(in_array("unread_count", $labels)) {
                $stmt = $this->db->prepare("SELECT COUNT(*) AS unread_count FROM users_emails a WHERE (a.user_id != '{$this->the_user_id}') AND (a.label=? AND a.read_list NOT LIKE '%{$this->the_user_id}%') {$filter}");
                $stmt->execute(['inbox']);
                // fetch the results
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                // append the the mials count array
                $mails_count["unread_count"] = isset($result->unread_count) ? $result->unread_count : 0;
            }
            

            // get read messages
            if(in_array("read_count", $labels)) {
                $stmt = $this->db->prepare("SELECT COUNT(*) AS read_count FROM users_emails a WHERE (a.label=? AND a.read_list LIKE '%{$this->the_user_id}%') {$filter}");
                $stmt->execute(['inbox']);
                // fetch the results
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                // append the the mials count array
                $mails_count["read_count"] = isset($result->read_count) ? $result->read_count : 0;
            }

            // get favourite count
            if(in_array("favorite_count", $labels)) {
                $stmt = $this->db->prepare("SELECT COUNT(*) AS favorite_count FROM users_emails a WHERE (a.label=? AND a.favorite_list LIKE '%{$this->the_user_id}%') {$filter}");
                $stmt->execute(['inbox']);
                // fetch the results
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                // append the the mials count array
                $mails_count["favorite"] = isset($result->favorite_count) ? $result->favorite_count : 0;
            }

            // get important count
            if(in_array("important_count", $labels)) {
                $stmt = $this->db->prepare("SELECT COUNT(*) AS important_count FROM users_emails a WHERE (a.label=? AND a.important_list LIKE '%{$this->the_user_id}%') {$filter}");
                $stmt->execute(['inbox']);
                // fetch the results
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                // append the the mials count array
                $mails_count["important"] = isset($result->important_count) ? $result->important_count : 0;
            }

            // get trash count
            if(in_array("trash_count", $labels)) {
                $stmt = $this->db->prepare("SELECT COUNT(*) AS trash_count FROM users_emails a WHERE (a.label=? AND a.trash_list LIKE '%{$this->the_user_id}%') {$filter}");
                $stmt->execute(['inbox']);
                // fetch the results
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                // append the the mials count array
                $mails_count["trash"] = isset($result->trash_count) ? $result->trash_count : 0;
            }

            // return the success response
            return [
                "code" => 200,
                "data" => $mails_count
            ];

        }

        // list the emails
        elseif($action == "mails_list") {

            // end the query if the request was not parsed
            if(!isset($params->action["labels"])) {
                return;
            }
            
            // assign the label
            $data = (object) [
                "label" => xss_clean($params->action["labels"]),
                "order_by" => isset($action["order_by"]) ? xss_clean($action["order_by"]) : "a.id DESC",
                "thread_id" => isset($action["thread_id"]) ? xss_clean($action["thread_id"]) : null,
                "search" => isset($params->action["q"]) ? xss_clean($params->action["q"]) : null,
                "start_point" => isset($action["start_id"]) ? xss_clean($action["start_id"]) : 0,
                "userData" => $params->userData,
                "filter" => $filter
            ];

            // set the result
            $result = $this->list($data);
            
            // return the success response
            return [
                "code" => empty($result["list"]) ? 203 : 200,
                "data" => $result
            ];
        }

        // move the messages to trash
        elseif($action == "move_to_trash") {
            
            // if not set thread_ids then return 
            if(!isset($params->action["thread_id"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "thread_id" => $this->stringToArray($params->action["thread_id"]),
                "userData" => $params->userData,
                "filter" => $filter
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->move_to_trash($data)
            ];
        }

        // move the messages from trash to inbox
        elseif($action == "move_to_inbox") {
            
            // if not set thread_ids then return 
            if(!isset($params->action["thread_id"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "thread_id" => $this->stringToArray($params->action["thread_id"]),
                "userData" => $params->userData,
                "filter" => $filter
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->move_to_inbox($data)
            ];
        }

        // move the messages to archive list
        elseif($action == "move_to_archive") {
            
            // if not set thread_ids then return 
            if(!isset($params->action["thread_id"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "thread_id" => $this->stringToArray($params->action["thread_id"]),
                "userData" => $params->userData,
                "filter" => $filter
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->toggle_archive($data)
            ];
        }

        // delete the messages
        elseif($action == "delete_message") {
            
            // if not set thread_ids then return 
            if(!isset($params->action["thread_id"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "thread_id" => $this->stringToArray($params->action["thread_id"]),
                "userData" => $params->userData,
                "filter" => $filter
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->delete_message($data)
            ];
        }

        // mark emails as read
        elseif($action == "mark_as_read") {
            
            // if not set thread_ids then return 
            if(!isset($params->action["thread_id"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "thread_id" => $this->stringToArray($params->action["thread_id"]),
                "userData" => $params->userData,
                "filter" => $filter
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->mark_as_read($data)
            ];
        }

        // mark emails as unread
        elseif($action == "mark_as_unread") {
            
            // if not set thread_ids then return 
            if(!isset($params->action["thread_id"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "thread_id" => $this->stringToArray($params->action["thread_id"]),
                "userData" => $params->userData,
                "filter" => $filter
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->mark_as_unread($data)
            ];
        }

        // mark emails as favorite
        elseif($action == "mark_as_favorite") {
            
            // if not set thread_ids then return 
            if(!isset($params->action["thread_id"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "thread_id" => $this->stringToArray($params->action["thread_id"]),
                "userData" => $params->userData,
                "filter" => $filter
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->toggle_favorite($data)
            ];
        }

        // mark emails as important
        elseif($action == "mark_as_important") {
            
            // if not set thread_ids then return 
            if(!isset($params->action["thread_id"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "thread_id" => $this->stringToArray($params->action["thread_id"]),
                "userData" => $params->userData,
                "filter" => $filter
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->toggle_important($data)
            ];
        }

        // discard composing of emails
        elseif($action == "discard_email_composer") {
            
            // end the query if the request was not parsed
            if(!isset($params->action["labels"])) {
                return;
            }

            // assign the label
            $data = (object) [
                "label" => $params->action["labels"],
                "userData" => $params->userData,
                "module" => "emails_{$params->userId}"
            ];
            
            // return the success response
            return [
                "code" => 200,
                "data" => $this->discard_email($data)
            ];
        }

        // send email messages to the list of recipients
        elseif($action == "send_email") {

            // end the query if the request was not parsed
            if(!isset($params->action["labels"])) {
                return;
            }

            // send the message
            $send = $this->send_email($params);
            
            // return the success response
            return [
                "code" => $send == "sent" ? 200 : 203,
                "data" => $send
            ];

        }

    }

    /**
     * Send the email message
     * Decode the labels parameter and get the list of all recipients to receive the message
     * 
     * @param \stdClass $params
     * 
     * @return Bool
     */
    private function send_email(stdClass $params) {

        // convert the labels into an object
        $labels = is_object($params->action["labels"]) ? $params->action["labels"] : json_decode($params->action["labels"]);

        // confirm that the content parameter was parsed
        if(!isset($labels->mail_content)) {
            return "Sorry! The content of the mail.";
        }

        // return if the email subject was not set
        if(!isset($labels->mail_content->subject)) {
            return "Sorry! The subject of this mail was not set.";
        }

        // return if the email subject was not set
        if(empty($labels->mail_content->subject)) {
            return "Sorry! Please enter a subject for this mail.";
        }

        // confirm that the recipients parameter was parsed
        if(!isset($labels->recipients)) {
            return "Sorry! The recipients parameter must be parsed";
        }

        // confirm that the copied list was parsed
        if(!isset($labels->recipients->primary)) {
            return "Sorry! Please enter that the main recipient list is not empty.";
        }

        // init variables
        $cc_list = [];
        $recipient_list = [];

        // loop through the list to properly format it well
        foreach($labels->recipients->primary as $each) {
            $recipient_list[] = [
                "user_id" => $each->user_id,
                "email" => $each->email ?? $each->name,
                "fullname" => $each->name
            ];
        }

        // loop through the copied list if set and a valid array
        if(isset($labels->recipients->copied) && is_object($labels->recipients->copied)) {
            foreach($labels->recipients->copied as $each) {
                $cc_list[] = [
                    "user_id" => $each->user_id,
                    "email" => $each->email ?? $each->name,
                    "fullname" => $each->name
                ];
            }
        }

        // get the user ids of the recipients_list and the copied_list
        $recipient_ids = array_column($recipient_list, "user_id");
        $copied_ids = array_column($cc_list, "user_id");

        // set the subject and content
        $subject = xss_clean($labels->mail_content->subject);
        $content = isset($labels->mail_content->content) ? xss_clean($labels->mail_content->content) : null;
        $label = isset($labels->mail_content->label) ? xss_clean($labels->mail_content->label) : "inbox";
        $scheduler = ($labels->mail_content->scheduler == "send_now") ? "send_now" : date("Y-m-d H:i:s", strtotime($labels->mail_content->scheduler));
        
        // continue processing
        $thread_id = random_string("alnum", 32);

        $module = "emails";

        // the details of the sender
        $sender = [
            "fullname" => $params->userData->name,
            "email" => $params->userData->email,
            "user_id" => $params->userData->user_id,
        ];

        // append the attachments
        $filesObj = load_class("files", "controllers");
        $attachments = $filesObj->prep_attachments("{$module}", $params->userData->user_id, $thread_id);
        
        try {

            // begin the transaction
            $this->db->beginTransaction();

            // prepare the statement
            $stmt = $this->db->prepare("
                INSERT INTO users_emails SET thread_id = ?, company_id = ?, user_id = ?, subject = ?, message = ?, sender_details = ?, 
                recipient_details = ?, recipient_list = ?, copy_recipients = ?, copy_recipients_list = ?, label = ?, attachment_size = ?
                ".(($scheduler !== "send_now") ? ",schedule_send = 'true'" : null)."
                ".(($scheduler !== "send_now") ? ",schedule_date = '{$scheduler}'" : null)."
                , trash_list = ?, deleted_list = ?, archive_list = ?, read_list = ?, favorite_list = ?, important_list = ?, mode = 'sent'
            ");
            $stmt->execute([
                $thread_id, $params->userData->company_id, $params->userId, $subject, $content, json_encode($sender), json_encode($recipient_list), 
                json_encode($recipient_ids), json_encode($cc_list), json_encode($copied_ids), $label, $attachments["raw_size_mb"],
                '["NULL"]', '["NULL"]', '["NULL"]', '["NULL"]', '["NULL"]', '["NULL"]'
            ]);
            
            // only insert attachment record if there was an attachment to the comment
            if(!empty($attachments["files"])) {
                // insert attachment
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                $files->execute(["messaging_emails", $thread_id, json_encode($attachments), "{$thread_id}", $params->userId, $attachments["raw_size_mb"]]);
            }
            
            // notify all persons within the selected group
            $query = json_encode($recipient_list);
            $this->db->query("INSERT INTO cron_scheduler SET client_id = '{$params->clientId}', notice_code='5', active_date=now(), item_id='{$thread_id}', query='{$query}', user_id='{$params->userId}', cron_type='email', subject = 'Email Message'");

            // log the user activity
            $this->userLogs("messaging_emails", $thread_id, null, "<strong>{$params->userData->name}</strong> sent out a mail to: ".count($recipient_ids)." contacts.", $params->userId);

            // commit the transaction
            $this->db->commit();

            // return the success response
            return "sent";

        } catch(PDOException $e) {
            $this->db->rollBack();
        }

    }

    /** 
     * Discard the composing of email content
     * 
     * @return Bool
     */
    private function discard_email(stdClass $params) {

        // create new file object
        return load_class("files", "controllers")->attachments($params);

    }

    /**
     * Move messages from trash to inbox
     * 
     * @return Array
     */
    private function move_to_inbox(stdClass $params) {
        // filter
        $filter = "";

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((company_id = '{$params->userData->company_id}' OR user_id = '{$this->the_user_id}' OR (recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND status = '1'";
        }
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((user_id = '{$this->the_user_id}' AND status = '1') OR (((recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND  status = '1')";
        }

        // regroup the list
        $trash_array = [];

        // loop through the mails thread
        foreach($params->thread_id as $eachThread) {

            // get the email
            $receivers_list = $this->db->prepare("SELECT recipient_list, copy_recipients_list, trash_list FROM users_emails WHERE thread_id = ? {$filter} LIMIT 1");
            $receivers_list->execute([$eachThread]);
            $result = $receivers_list->fetch(PDO::FETCH_OBJ);

            /** return if empty */
            if(empty($result)) {
                return;
            }

            /** Convert the list into an array */
            $recipient_list = !empty($result->recipient_list) ? json_decode($result->recipient_list, true) : [];
            $copy_recipients_list = !empty($result->copy_recipients_list) ? json_decode($result->copy_recipients_list, true) : [];
            
            // the read list in array
            $trash_list = !empty($result->trash_list) ? json_decode($result->trash_list, true) : [];

            // if the recipient list and the copy list are empty the end the query
            if(empty($recipient_list) and empty($copy_recipients_list)) {
                return;
            }

            // loop through the list of the recipients
            $item_found = false;
            $column = null;

            // loop through the recipient list
            foreach($recipient_list as $each) {
                if($each == $this->the_user_id) {
                    $column = "recipient_list";
                    $item_found = true;
                    break;
                }
            }

            // loop through the copied list if the user was not found in the reciepient list
            if(empty($column)) {
                foreach($copy_recipients_list as $each) {
                    if($each == $this->the_user_id) {
                        $column = "copy_recipients_list";
                        $item_found = true;
                        break;
                    }
                }
            }

            // end query if not found in either columns
            if(!$item_found) {
                return;
            }

            // then lets check if the user is in the read list
            if(!empty($trash_list)) {

                // is found is false by default
                $is_found = false;
                $is_found_key = null;

                // loop through the list
                foreach($trash_list as $key => $value) {
                    // if the username is found
                    if($value == $this->the_user_id) {
                        // remove the key from the list
                        $is_found = true;
                        $is_found_key = $key;
                        break;
                    }
                }
                
                // if the key was found
                if($is_found) {

                    // remove the value
                    unset($trash_list[$is_found_key]);

                    // array to the array list
                    $trash_array[] = $eachThread;

                    // update the database table
                    $this->db->query("UPDATE users_emails SET trash_list='".json_encode($trash_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                }
            }

        }

        // return true at the end of the query
        return $trash_array;

    }

    /**
     * Move messages to trash
     * 
     * @return Array
     */
    private function move_to_trash(stdClass $params) {
        // filter
        $filter = "";

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((company_id = '{$params->userData->company_id}' OR user_id = '{$this->the_user_id}' OR (recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND status = '1'";
        }
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((user_id = '{$this->the_user_id}' AND status = '1') OR (((recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND  status = '1')";
        }

        // regroup the list
        $trash_array = [];

        // loop through the mails thread
        foreach($params->thread_id as $eachThread) {

            // get the email
            $receivers_list = $this->db->prepare("SELECT recipient_list, copy_recipients_list, trash_list FROM users_emails WHERE thread_id = ? {$filter} LIMIT 1");
            $receivers_list->execute([$eachThread]);
            $result = $receivers_list->fetch(PDO::FETCH_OBJ);

            /** return if empty */
            if(empty($result)) {
                return;
            }

            /** Convert the list into an array */
            $recipient_list = !empty($result->recipient_list) ? json_decode($result->recipient_list, true) : [];
            $copy_recipients_list = !empty($result->copy_recipients_list) ? json_decode($result->copy_recipients_list, true) : [];
            
            // the read list in array
            $trash_list = !empty($result->trash_list) ? json_decode($result->trash_list, true) : [];

            // if the recipient list and the copy list are empty the end the query
            if(empty($recipient_list) and empty($copy_recipients_list)) {
                return;
            }

            // loop through the list of the recipients
            $item_found = false;
            $column = null;

            // loop through the recipient list
            foreach($recipient_list as $each) {
                if($each == $this->the_user_id) {
                    $column = "recipient_list";
                    $item_found = true;
                    break;
                }
            }

            // loop through the copied list if the user was not found in the reciepient list
            if(empty($column)) {
                foreach($copy_recipients_list as $each) {
                    if($each == $this->the_user_id) {
                        $column = "copy_recipients_list";
                        $item_found = true;
                        break;
                    }
                }
            }

            // end query if not found in either columns
            if(!$item_found) {
                return;
            }

            // then lets check if the user is in the read list
            if(!empty($trash_list)) {

                // is found is false by default
                $is_found = false;

                // loop through the list
                foreach($trash_list as $key => $value) {
                    // if the username is found
                    if($value == $this->the_user_id) {
                        // remove the key from the list
                        $is_found = true;
                        break;
                    }
                }
                
                // if the key was found
                if(!$is_found) {
                    // append the user id
                    array_push($trash_list, $this->the_user_id);

                    // array to the array list
                    $trash_array[] = $eachThread;

                    // update the database table
                    $this->db->query("UPDATE users_emails SET trash_list='".json_encode($trash_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                }
            } else {
                // create a new array list
                $trash_list[] = $this->the_user_id;
                
                // push to the array list
                $trash_array[] = $eachThread;

                // update the database table
                $this->db->query("UPDATE users_emails SET trash_list='".json_encode($trash_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
            }

        }

        // return true at the end of the query
        return $trash_array;

    }

    /**
     * Move messages to trash
     * 
     * @return Array
     */
    private function delete_message(stdClass $params) {
        // filter
        $filter = "";

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((company_id = '{$params->userData->company_id}' OR user_id = '{$this->the_user_id}' OR (recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND status = '1'";
        }
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((user_id = '{$this->the_user_id}' AND status = '1') OR (((recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND  status = '1')";
        }

        // regroup the list
        $trash_array = [];

        // loop through the mails thread
        foreach($params->thread_id as $eachThread) {

            // get the email
            $receivers_list = $this->db->prepare("SELECT recipient_list, copy_recipients_list, deleted_list FROM users_emails WHERE thread_id = ? {$filter} LIMIT 1");
            $receivers_list->execute([$eachThread]);
            $result = $receivers_list->fetch(PDO::FETCH_OBJ);

            /** return if empty */
            if(empty($result)) {
                return;
            }

            /** Convert the list into an array */
            $recipient_list = !empty($result->recipient_list) ? json_decode($result->recipient_list, true) : [];
            $copy_recipients_list = !empty($result->copy_recipients_list) ? json_decode($result->copy_recipients_list, true) : [];
            
            // the read list in array
            $deleted_list = !empty($result->deleted_list) ? json_decode($result->deleted_list, true) : [];

            // if the recipient list and the copy list are empty the end the query
            if(empty($recipient_list) and empty($copy_recipients_list)) {
                return;
            }

            // loop through the list of the recipients
            $item_found = false;
            $column = null;

            // loop through the recipient list
            foreach($recipient_list as $each) {
                if($each == $this->the_user_id) {
                    $column = "recipient_list";
                    $item_found = true;
                    break;
                }
            }

            // loop through the copied list if the user was not found in the reciepient list
            if(empty($column)) {
                foreach($copy_recipients_list as $each) {
                    if($each == $this->the_user_id) {
                        $column = "copy_recipients_list";
                        $item_found = true;
                        break;
                    }
                }
            }

            // end query if not found in either columns
            if(!$item_found) {
                return;
            }

            // then lets check if the user is in the read list
            if(!empty($deleted_list)) {

                // is found is false by default
                $is_found = false;

                // loop through the list
                foreach($deleted_list as $key => $value) {
                    // if the username is found
                    if($value == $this->the_user_id) {
                        // remove the key from the list
                        $is_found = true;
                        break;
                    }
                }
                
                // if the key was found
                if(!$is_found) {
                    // append the user id
                    array_push($deleted_list, $this->the_user_id);

                    // array to the array list
                    $trash_array[] = $eachThread;

                    // update the database table
                    $this->db->query("UPDATE users_emails SET deleted_list='".json_encode($deleted_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                }
            } else {
                // create a new array list
                $deleted_list[] = $this->the_user_id;
                
                // push to the array list
                $trash_array[] = $eachThread;

                // update the database table
                $this->db->query("UPDATE users_emails SET deleted_list='".json_encode($deleted_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
            }

        }

        // return true at the end of the query
        return $trash_array;

    }

    /**
     * Mark emails as important (Toggle the result - Unmark if already in array list)
     * 
     * @param \stdClass $params
     * 
     * @return String
     */
    private function toggle_important(stdClass $params) {

        // filter
        $filter = "";

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((company_id = '{$params->userData->company_id}' OR user_id = '{$this->the_user_id}' OR (recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND status = '1'";
        }
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((user_id = '{$this->the_user_id}' AND status = '1') OR (((recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND  status = '1')";
        }

        // regroup the list
        $important_array = [];

        // loop through the mails thread
        foreach($params->thread_id as $eachThread) {

            // get the email
            $receivers_list = $this->db->prepare("SELECT user_id, recipient_list, copy_recipients_list, important_list FROM users_emails WHERE thread_id = ? {$filter} LIMIT 1");
            $receivers_list->execute([$eachThread]);
            $result = $receivers_list->fetch(PDO::FETCH_OBJ);

            /** return if empty */
            if(empty($result)) {
                return;
            }

            /** Convert the list into an array */
            $recipient_list = !empty($result->recipient_list) ? json_decode($result->recipient_list, true) : [];
            $copy_recipients_list = !empty($result->copy_recipients_list) ? json_decode($result->copy_recipients_list, true) : [];
            
            // the read list in array
            $important_list = !empty($result->important_list) ? json_decode($result->important_list, true) : [];

            // if the recipient list and the copy list are empty the end the query
            if(empty($recipient_list) and empty($copy_recipients_list)) {
                return;
            }

            // loop through the list of the recipients
            $item_found = false;
            $column = null;

            // loop through the recipient list
            foreach($recipient_list as $each) {
                if($each == $this->the_user_id) {
                    $column = "recipient_list";
                    $item_found = true;
                    break;
                }
            }

            // loop through the copied list if the user was not found in the reciepient list
            if(empty($column)) {
                foreach($copy_recipients_list as $each) {
                    if($each == $this->the_user_id) {
                        $column = "copy_recipients_list";
                        $item_found = true;
                        break;
                    }
                }
            }

            // end query if not found in either columns
            if(!$item_found && ($this->the_user_id !== $result->user_id)) {
                return;
            }

            // then lets check if the user is in the read list
            if(!empty($important_list)) {

                // is found is false by default
                $is_found = false;
                $is_found_key = null;

                // loop through the list
                foreach($important_list as $key => $value) {
                    // if the username is found
                    if($value == $this->the_user_id) {
                        // remove the key from the list
                        $is_found = true;
                        $is_found_key = $key;
                        break;
                    }
                }
                
                // if the key was found
                if(!$is_found) {
                    // append the user id
                    array_push($important_list, $this->the_user_id);

                    // array to the array list
                    $important_array[$eachThread] = ["class" => "<span class='txt-10'><i title='Marked as important' class='fa text-warning fa-tags'></i></span>", "is_important" => 1];

                    // update the database table
                    $this->db->query("UPDATE users_emails SET important_list='".json_encode($important_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                } else {
                    // remove the value
                    unset($important_list[$is_found_key]);

                    // push to the array list
                    $important_array[$eachThread] = ["class" => "", "is_important" => 0];

                    // update the database table
                    $this->db->query("UPDATE users_emails SET important_list='".json_encode($important_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                }
            } else {
                // create a new array list
                $important_list[] = $this->the_user_id;
                
                // push to the array list
                $important_array[$eachThread] = ["class" => "<span class='txt-10'><i title='Marked as important' class='fa text-warning fa-tags'></i></span>", "is_important" => 1];

                // update the database table
                $this->db->query("UPDATE users_emails SET important_list='".json_encode($important_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
            }

        }

        // return true at the end of the query
        return $important_array;

    }

    /**
     * Mark emails as favorited
     * 
     * @param \stdClass $params
     * 
     * @return String
     */
    private function toggle_favorite(stdClass $params) {

        // filter
        $filter = "";

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((company_id = '{$params->userData->company_id}' OR user_id = '{$this->the_user_id}' OR (recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND status = '1'";
        }
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((user_id = '{$this->the_user_id}' AND status = '1') OR (((recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND  status = '1')";
        }

        // regroup the list
        $favorite_array = [];

        // loop through the mails thread
        foreach($params->thread_id as $eachThread) {

            // get the email
            $receivers_list = $this->db->prepare("SELECT user_id, recipient_list, copy_recipients_list, favorite_list FROM users_emails WHERE thread_id = ? {$filter} LIMIT 1");
            $receivers_list->execute([$eachThread]);
            $result = $receivers_list->fetch(PDO::FETCH_OBJ);

            /** return if empty */
            if(empty($result)) {
                return;
            }

            /** Convert the list into an array */
            $recipient_list = !empty($result->recipient_list) ? json_decode($result->recipient_list, true) : [];
            $copy_recipients_list = !empty($result->copy_recipients_list) ? json_decode($result->copy_recipients_list, true) : [];
            
            // the read list in array
            $favorite_list = !empty($result->favorite_list) ? json_decode($result->favorite_list, true) : [];

            // if the recipient list and the copy list are empty the end the query
            if(empty($recipient_list) and empty($copy_recipients_list)) {
                return;
            }

            // loop through the list of the recipients
            $item_found = false;
            $column = null;

            // loop through the recipient list
            foreach($recipient_list as $each) {
                if($each == $this->the_user_id) {
                    $column = "recipient_list";
                    $item_found = true;
                    break;
                }
            }

            // loop through the copied list if the user was not found in the reciepient list
            if(empty($column)) {
                foreach($copy_recipients_list as $each) {
                    if($each == $this->the_user_id) {
                        $column = "copy_recipients_list";
                        $item_found = true;
                        break;
                    }
                }
            }

            // end query if not found in either columns
            if(!$item_found && ($this->the_user_id !== $result->user_id)) {
                return;
            }

            // then lets check if the user is in the read list
            if(!empty($favorite_list)) {

                // is found is false by default
                $is_found = false;
                $is_found_key = null;

                // loop through the list
                foreach($favorite_list as $key => $value) {
                    // if the username is found
                    if($value == $this->the_user_id) {
                        // remove the key from the list
                        $is_found = true;
                        $is_found_key = $key;
                        break;
                    }
                }
                
                // if the key was found
                if(!$is_found) {
                    // append the user id
                    array_push($favorite_list, $this->the_user_id);

                    // array to the array list
                    $favorite_array[$eachThread] = ["class" => "text-warning", "is_favorited" => 1];

                    // update the database table
                    $this->db->query("UPDATE users_emails SET favorite_list='".json_encode($favorite_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                } else {
                    // remove the value
                    unset($favorite_list[$is_found_key]);

                    // push to the array list
                    $favorite_array[$eachThread] = ["class" => "text-secondary", "is_favorited" => 0];

                    // update the database table
                    $this->db->query("UPDATE users_emails SET favorite_list='".json_encode($favorite_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                }
            } else {
                // create a new array list
                $favorite_list[] = $this->the_user_id;
                
                // push to the array list
                $favorite_array[$eachThread] = ["class" => "text-warning", "is_favorited" => 1];

                // update the database table
                $this->db->query("UPDATE users_emails SET favorite_list='".json_encode($favorite_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
            }

        }

        // return true at the end of the query
        return $favorite_array;

    }
    
    /**
     * Move the messages to archive (This will toggle between archive state and inbox state)
     * 
     * @param \stdClass $params
     * 
     * @return String
     */
    private function toggle_archive(stdClass $params) {

        // filter
        $filter = "";

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((company_id = '{$params->userData->company_id}' OR user_id = '{$this->the_user_id}' OR (recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND status = '1'";
        }
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((user_id = '{$this->the_user_id}' AND status = '1') OR (((recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND  status = '1')";
        }

        // regroup the list
        $archive_array = [];

        // loop through the mails thread
        foreach($params->thread_id as $eachThread) {

            // get the email
            $receivers_list = $this->db->prepare("SELECT user_id, recipient_list, copy_recipients_list, archive_list FROM users_emails WHERE thread_id = ? {$filter} LIMIT 1");
            $receivers_list->execute([$eachThread]);
            $result = $receivers_list->fetch(PDO::FETCH_OBJ);

            /** return if empty */
            if(empty($result)) {
                return;
            }

            /** Convert the list into an array */
            $recipient_list = !empty($result->recipient_list) ? json_decode($result->recipient_list, true) : [];
            $copy_recipients_list = !empty($result->copy_recipients_list) ? json_decode($result->copy_recipients_list, true) : [];
            
            // the read list in array
            $archive_list = !empty($result->archive_list) ? json_decode($result->archive_list, true) : [];

            // if the recipient list and the copy list are empty the end the query
            if(empty($recipient_list) and empty($copy_recipients_list)) {
                return;
            }

            // loop through the list of the recipients
            $item_found = false;
            $column = null;

            // loop through the recipient list
            foreach($recipient_list as $each) {
                if($each == $this->the_user_id) {
                    $column = "recipient_list";
                    $item_found = true;
                    break;
                }
            }

            // loop through the copied list if the user was not found in the reciepient list
            if(empty($column)) {
                foreach($copy_recipients_list as $each) {
                    if($each == $this->the_user_id) {
                        $column = "copy_recipients_list";
                        $item_found = true;
                        break;
                    }
                }
            }

            // end query if not found in either columns
            if(!$item_found) {
                return;
            }

            // then lets check if the user is in the read list
            if(!empty($archive_list)) {

                // is found is false by default
                $is_found = false;
                $is_found_key = null;

                // loop through the list
                foreach($archive_list as $key => $value) {
                    // if the username is found
                    if($value == $this->the_user_id) {
                        // remove the key from the list
                        $is_found = true;
                        $is_found_key = $key;
                        break;
                    }
                }
                
                // if the key was found
                if(!$is_found) {
                    // append the user id
                    array_push($archive_list, $this->the_user_id);

                    // array to the array list
                    $archive_array[] = $eachThread;

                    // update the database table
                    $this->db->query("UPDATE users_emails SET archive_list='".json_encode($archive_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                } else {
                    // remove the value
                    unset($archive_list[$is_found_key]);

                    // push to the array list
                    $archive_array[] = $eachThread;

                    // update the database table
                    $this->db->query("UPDATE users_emails SET archive_list='".json_encode($archive_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                }
            } else {
                // create a new array list
                $archive_list[] = $this->the_user_id;
                
                // push to the array list
                $archive_array[$eachThread] = $eachThread;

                // update the database table
                $this->db->query("UPDATE users_emails SET archive_list='".json_encode($archive_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
            }

        }

        // return true at the end of the query
        return $archive_array;

    }

    /**
     * Mark emails as read
     * 
     * @param \stdClass $params
     * 
     * @return String
     */
    private function mark_as_read(stdClass $params) {

        // filter
        $filter = "";

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((company_id = '{$params->userData->company_id}' OR user_id = '{$this->the_user_id}' OR (recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND status = '1'";
        }
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((user_id = '{$this->the_user_id}' AND status = '1') OR (((recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND  status = '1')";
        }

        // regroup the list
        $thread_array = [];

        // loop through the mails thread
        foreach($params->thread_id as $eachThread) {

            // get the email
            $receivers_list = $this->db->prepare("SELECT recipient_list, copy_recipients_list, read_list FROM users_emails WHERE thread_id = ? {$filter} LIMIT 1");
            $receivers_list->execute([$eachThread]);
            $result = $receivers_list->fetch(PDO::FETCH_OBJ);

            /** return if empty */
            if(empty($result)) {
                return;
            }

            /** Convert the list into an array */
            $recipient_list = !empty($result->recipient_list) ? json_decode($result->recipient_list, true) : [];
            $copy_recipients_list = !empty($result->copy_recipients_list) ? json_decode($result->copy_recipients_list, true) : [];
            
            // the read list in array
            $read_list = !empty($result->read_list) ? json_decode($result->read_list, true) : [];

            // if the recipient list and the copy list are empty the end the query
            if(empty($recipient_list) and empty($copy_recipients_list)) {
                return;
            }

            // loop through the list of the recipients
            $item_found = false;
            $column = null;

            // loop through the recipient list
            foreach($recipient_list as $each) {
                if($each == $this->the_user_id) {
                    $column = "recipient_list";
                    $item_found = true;
                    break;
                }
            }

            // loop through the copied list if the user was not found in the reciepient list
            if(empty($column)) {
                foreach($copy_recipients_list as $each) {
                    if($each == $this->the_user_id) {
                        $column = "copy_recipients_list";
                        $item_found = true;
                        break;
                    }
                }
            }

            // end query if not found in either columns
            if(!$item_found) {
                return;
            }

            // then lets check if the user is in the read list
            if(!empty($read_list)) {

                // is found is false by default
                $is_found = false;

                // loop through the list
                foreach($read_list as $value) {
                    // if the username is found
                    if($value == $this->the_user_id) {
                        // remove the key from the list
                        $is_found = true;
                        break;
                    }
                }
                
                // if the key was found
                if(!$is_found) {
                    // append the user id
                    array_push($read_list, $this->the_user_id);

                    // append to array list
                    $thread_array[] = $eachThread;

                    // update the database table
                    $this->db->query("UPDATE users_emails SET read_list='".json_encode($read_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                }
            } else {
                // create a new array list
                $read_list[] = $this->the_user_id;

                // append to array list
                $thread_array[] = $eachThread;

                // update the database table
                $this->db->query("UPDATE users_emails SET read_list='".json_encode($read_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
            }

        }

        // return true at the end of the query
        return $thread_array;

    }

    /**
     * Mark emails as read
     * 
     * @param \stdClass $params
     * 
     * @return String
     */
    private function mark_as_unread(stdClass $params) {

        // filter
        $filter = "";

        // go ahead
        if(in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((company_id = '{$params->userData->company_id}' OR user_id = '{$this->the_user_id}' OR (recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND status = '1'";
        }
        // if insurance company user
        elseif(!in_array($this->the_user_type, ["insurance_company"])) {
            $filter .= " AND ((user_id = '{$this->the_user_id}' AND status = '1') OR (((recipient_list LIKE '%{$this->the_user_id}%') OR (copy_recipients_list LIKE '%{$this->the_user_id}%'))) AND  status = '1')";
        }

        // empty array list
        $thread_array = [];

        // loop through the mails thread
        foreach($params->thread_id as $eachThread) {

            // get the email
            $receivers_list = $this->db->prepare("SELECT recipient_list, copy_recipients_list, read_list FROM users_emails WHERE thread_id = ? {$filter} LIMIT 1");
            $receivers_list->execute([$eachThread]);
            $result = $receivers_list->fetch(PDO::FETCH_OBJ);

            /** return if empty */
            if(empty($result)) {
                return;
            }

            /** Convert the list into an array */
            $recipient_list = !empty($result->recipient_list) ? json_decode($result->recipient_list, true) : [];
            $copy_recipients_list = !empty($result->copy_recipients_list) ? json_decode($result->copy_recipients_list, true) : [];
            
            // the read list in array
            $read_list = !empty($result->read_list) ? json_decode($result->read_list, true) : [];

            // if the recipient list and the copy list are empty the end the query
            if(empty($recipient_list) and empty($copy_recipients_list)) {
                return;
            }

            // loop through the list of the recipients
            $item_found = false;
            $column = null;

            // loop through the recipient list
            foreach($recipient_list as $each) {
                if($each == $this->the_user_id) {
                    $column = "recipient_list";
                    $item_found = true;
                    break;
                }
            }

            // loop through the copied list if the user was not found in the reciepient list
            if(empty($column)) {
                foreach($copy_recipients_list as $each) {
                    if($each == $this->the_user_id) {
                        $column = "copy_recipients_list";
                        $item_found = true;
                        break;
                    }
                }
            }

            // end query if not found in either columns
            if(!$item_found) {
                return;
            }

            // then lets check if the user is in the read list
            if(!empty($read_list)) {

                // is found is false by default
                $is_found = false;
                $is_found_key = null;

                // loop through the list
                foreach($read_list as $key => $value) {
                    // if the username is found
                    if($value == $this->the_user_id) {
                        // remove the key from the list
                        $is_found = true;
                        $is_found_key = $key;
                        break;
                    }
                }
                
                // if the key was found
                if($is_found) {
                    // remove the value
                    unset($read_list[$is_found_key]);
                    // append to array list
                    $thread_array[] = $eachThread;
                    // update the database table
                    $this->db->query("UPDATE users_emails SET read_list='".json_encode($read_list)."' WHERE thread_id='{$eachThread}' LIMIT 1");
                }
            }

        }

        // return true at the end of the query
        return $thread_array;

    }

    /**
     * Confirm that the user has read the mail
     * 
     * @param \stdClass $read_list
     * @param String $user_id
     */
    private function is_read($read_list, $user_id) {

        if(empty($read_list)) {
            return false;
        }

        // convert the list into an array
        $read_list = (array) $read_list;

        // return boolean
        return (bool) in_array($user_id, $read_list);
    }

    /**
     * Confirm that the user has favorited the mail
     * 
     * @param \stdClass $favorite_list
     * @param String $user_id
     */
    private function is_favorited($favorite_list, $user_id) {

        if(empty($favorite_list)) {
            return false;
        }

        // convert the list into an array
        $favorite_list = (array) $favorite_list;

        // return boolean
        return (bool) in_array($user_id, $favorite_list);
    }

    /**
     * Confirm that the user has marked the mail as important
     * 
     * @param \stdClass $important_list
     * @param String $user_id
     */
    private function is_important($important_list, $user_id) {

        if(empty($important_list)) {
            return false;
        }

        // convert the list into an array
        $important_list = (array) $important_list;

        // return boolean
        return (bool) in_array($user_id, $important_list);
    }

    /**
     * Confirm that the user has been archived
     * 
     * @param \stdClass $archive_list
     * @param String $user_id
     */
    private function is_archived($archive_list, $user_id) {

        if(empty($archive_list)) {
            return false;
        }

        // convert the list into an array
        $archive_list = (array) $archive_list;

        // return boolean
        return (bool) in_array($user_id, $archive_list);
    }

    /**
     * List emails
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {
        
        // filters
        $query = "1";
        $query .= !empty($params->search) ? " AND (a.subject LIKE '%{$params->search}%' OR a.message LIKE '%{$params->search}%')" : null;
        $query .= !empty($params->thread_id) ? " AND a.thread_id = '{$params->thread_id}'" : null;

        // if the label is inbox, then exempt the user who shared the message from the list
        if($params->label == "inbox") {
            $query .= " AND (user_id != '{$this->the_user_id}') AND (a.trash_list IS NOT NULL AND a.trash_list NOT LIKE '%{$this->the_user_id}%') AND (a.archive_list IS NOT NULL AND a.archive_list NOT LIKE '%{$this->the_user_id}%')";
        }

        // if the label is draft or sent
        elseif(in_array($params->label, ["draft", "sent"])) {
            // form the query string
            $query .= " AND mode='{$params->label}' AND user_id = '{$this->the_user_id}' AND (a.trash_list IS NOT NULL AND a.trash_list NOT LIKE '%{$this->the_user_id}%') AND (a.archive_list IS NOT NULL AND a.archive_list NOT LIKE '%{$this->the_user_id}%')";
            
            // set the label to empty
            $params->label = null;
        }

        // if important or favorite list is requested
        if(in_array($params->label, array_keys($this->other_labels))) {
            $query .= " AND a.{$this->other_labels[$params->label]} LIKE '%{$this->the_user_id}%'";
        }

        // if the label is in the array list
        if(in_array($params->label, $this->labels)) {
            $query .= !empty($params->label) ? " AND a.label = '{$params->label}'" : null;
        }

        try {

            // set the label module
            $limit_count = 25;
            $module = "mails_{$params->label}";
            
            // mails counter
            if(empty($this->session->$module)) {
                
                // get the full list of all items 
                $count_list = $this->db->prepare("SELECT 
                    a.*, u.name AS sender_name, u.image AS sender_image,
                        (SELECT b.description FROM files_attachment b WHERE b.record_id = a.thread_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                        (SELECT b.name FROM companies b WHERE b.item_id = a.company_id LIMIT 1) AS company_name
                    FROM users_emails a
                    LEFT JOIN users u ON u.item_id = a.user_id
                    WHERE {$query} {$params->filter} ORDER BY {$params->order_by}
                ");
                $count_list->execute();

                // set the full list count into the session
                $this->session->$module = $count_list->rowCount();
            }

            // prepare and execute the sql statement
            $stmt = $this->db->prepare("SELECT 
                a.*, u.name AS sender_name, u.image AS sender_image,
                    (SELECT b.description FROM files_attachment b WHERE b.record_id = a.thread_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                    (SELECT b.name FROM companies b WHERE b.item_id = a.company_id LIMIT 1) AS company_name
                FROM users_emails a
                LEFT JOIN users u ON u.item_id = a.user_id
                WHERE {$query} {$params->filter} ORDER BY {$params->order_by} LIMIT {$limit_count}
            ");
            $stmt->execute();

            // create a new object
            $data = [];
            $filesObject = load_class("forms", "controllers");

            // loop through the results list
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // unset the id
                unset($result->id);

                // recipients list
                $result->recipient_list = json_decode($result->recipient_list);
                $result->recipient_details = json_decode($result->recipient_details, true);

                $result->copy_recipients = json_decode($result->copy_recipients);
                $result->copy_recipients_list = json_decode($result->copy_recipients_list);

                $result->read_list = json_decode($result->read_list);
                $result->favorite_list = json_decode($result->favorite_list);
                $result->important_list = json_decode($result->important_list);
                $result->archive_list = json_decode($result->archive_list);

                $result->sender_details = json_decode($result->sender_details, true);

                // clean date
                $result->email_date = date("jS M", strtotime($result->date_created));
                $result->email_fulldate = date("M jS, Y, h:i A", strtotime($result->date_created));
                $result->days_ago = time_diff($result->date_created);
                
                // clean the message
                $result->message = custom_clean(htmlspecialchars_decode($result->message));
                $result->caption = limit_words($result->message, 22)."...";

                // if the user has read the message
                if($this->the_user_id == $result->user_id) {
                    $result->is_read = true;
                } else {
                    $result->is_read = (int) $this->is_read($result->read_list, $this->the_user_id);
                }
                $result->is_favorited = (int) $this->is_favorited($result->favorite_list, $this->the_user_id);
                $result->is_important = (int) $this->is_important($result->important_list, $this->the_user_id);
                $result->is_archived = (int) $this->is_archived($result->archive_list, $this->the_user_id);

                // if attachment variable was parsed
                if(isset($result->attachment)) {
                    $result->attachment_html = "";
                    $result->attachment = json_decode($result->attachment);
                }

                // unset some relevant parameters that must not be seen by the user
                unset($result->read_list);
                unset($result->deleted_list);
                unset($result->trash_list);
                unset($result->favorite_list);
                unset($result->important_list);
                unset($result->archive_list);
                
                // attachment html list
                if(isset($result->attachment->files)) {
                    // format the attachements list
                    $result->attachment_html = $filesObject->list_attachments($result->attachment->files, $this->the_user_id, "col-lg-4 col-md-6", false, false);
                } else {
                    $result->attachment_html = "";
                    $result->attachment = $this->fake_files;
                }
                
                $data[] = $result;
            }

            // listing algorithm
            $total_result = count($data);
            $total_count = $this->session->$module;

            // return the final result
            return [
                "list" => $data,
                "pagination" => [
                    "total_count" => $total_count,
                    "start_point" => !$params->start_point ? 1 : $params->start_point,
                    "end_point" => ($params->start_point + $total_result),
                ]
            ];

        } catch(PDOException $e) {}

    }

}
?>

