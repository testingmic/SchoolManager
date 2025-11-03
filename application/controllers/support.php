<?php 

class Support extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * List Support Tickets
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        $params->parent_id = $params->parent_id ?? 0;

        // additional query parameters
        $params->query .= (isset($params->q)) ? " AND a.subject LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->ticket_id) && !empty($params->ticket_id)) ? " AND a.id='{$params->ticket_id}'" : null;
        $params->query .= isset($params->parent_id) ? " AND a.parent_id='{$params->parent_id}'" : null;

        $order = isset($params->order) ? $params->order : "DESC";

        try {

            // display the replies as well
            $q_param = (object) ["clientId" => $params->clientId];
            $showReplies = (bool) (isset($params->show_all) && !empty($params->show_all));

            // perform the query
            $stmt = $this->db->prepare("
                SELECT a.*, c.client_name, c.client_contact, c.client_secondary_contact, c.client_email, c.client_website, 
                    (SELECT CONCAT(b.item_id,'|',COALESCE(b.name,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_info
                FROM support_tickets a
                LEFT JOIN clients_accounts c ON c.client_id = a.client_id
                WHERE {$params->query} ORDER BY a.id {$order} LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["user_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "email", "image","user_type"]);
                }

                // show the replies
                if($showReplies && ($result->parent_id == 0)) {
                    $q_param->parent_id = $result->id;
                    $q_param->order = "ASC";
                    $result->replies = $this->list($q_param)["data"];
                }

                // clean the content
                $result->content = htmlspecialchars_decode($result->content);

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
     * Create a new support ticket
     * 
     * @param $params->department
     * @param $params->content
     * @param $params->subject
     * 
     * @return Array
     */
    public function create(stdClass $params) {

        try {

            // modify the content variable
            $params->content = custom_clean(htmlspecialchars_decode($params->content));
            $params->content = htmlspecialchars($params->content);

            // set the user type
            $user_type = ($params->userData->user_type === "support") ? "support" : "user";

            // insert the ticket data information
            $stmt = $this->db->prepare("INSERT INTO support_tickets SET client_id =?, content = ?, 
                user_id = ?, subject = ?, department = ?, date_created = now(), date_updated = now(), section = ?, user_type = ?");
            $stmt->execute([$params->clientId, $params->content, $params->userId, $params->subject, 
                $params->department ?? null, $params->section ?? null, $user_type]);

            // return success message
            return ["code" => 200, "data" => "Your ticket was successfully created."];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Share a reply to the ticket
     * 
     * @param $params->ticket_id
     * @param $params->content
     * 
     * @return Array
     */
    public function reply(stdClass $params) {

        try {

            // global variables
            global $noticeClass, $accessObject, $isSupport;

            // set the table
            $params->section = $params->section ?? "ticket";
            $table = ($params->section == "ticket") ? "support_tickets" : "knowledge_base";
            $append = $isSupport ? "" : (($params->section == "ticket") ? " AND client_id='{$params->clientId}'" : null);
            
            // the message to send
            $message = ($params->section == "ticket") ? "Reply was successfully shared." : "Comment was successfully sent.";

            // check the query
            $query = $this->pushQuery("status, user_id, subject, client_id", $table, "id='{$params->ticket_id}' {$append} LIMIT 1");

            // confirm if the ticket exists
            if(empty($query)) {
                return ["code" => 400, "data" => "Sorry! An invalid {$table} was parsed."];
            }

            // set the client id
            $the_client = $isSupport ? $query[0]->client_id : $params->clientId;

            // if the ticket is closed
            if($query[0]->status == "Closed"){
                return ["code" => 400, "data" => "Sorry! The ticket has been closed, hence cannot send any message."];
            }

            // modify the content variable
            $params->content = custom_clean(htmlspecialchars_decode($params->content));
            $params->content = htmlspecialchars($params->content);

            // set the status
            $user_type = ($params->userData->user_type === "support") ? "support" : "user";
            $status = (($query[0]->user_id == $params->userId) || ($user_type !== "support")) ? "Waiting" : "Answered";

            // insert the reply
            $stmt = $this->db->prepare("INSERT INTO {$table} SET client_id = ?, parent_id = ?, content = ?, user_id = ?, user_type = ?");
            $stmt->execute([$the_client, $params->ticket_id, $params->content, $params->userId, $user_type]);

            // send a notice
            if($status === "Answered") {
                // send a notification to the user
                $notice_param = (object) [
                    '_item_id' => $params->ticket_id,
                    'user_id' => $query[0]->user_id,
                    'subject' => "New Ticket Reply",
                    'username' => null,
                    'remote' => false, 
                    'notice_type' => 5,
                    'userId' => $params->userId,
                    'clientId' => $params->clientId,
                    'initiated_by' => 'system'
                ];
                $notice_param->message = "You have a new reply on your ticket: <strong>{$query[0]->subject}.";
                $noticeClass->add($notice_param);
            }

            // update the last_update column of the ticket
            $this->db->query("UPDATE {$table} SET date_updated = now(), status='{$status}' WHERE id='{$params->ticket_id}' LIMIT 1");

            // return success message
            return ["code" => 200, "data" => $message];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
        
    }

    /**
     * Close an existing ticket
     * 
     * @param $params->ticket_id
     * 
     * @return Array
     */
    public function close(stdClass $params) {

        try {

            // global variable
            global $isSupport;

            // append this item
            $append = $isSupport ? "" : " AND client_id='{$params->clientId}'";

            // set the table
            $params->section = $params->section ?? "ticket";
            $table = ($params->section == "ticket") ? "support_tickets" : "knowledge_base";

            // the message to send
            $message = ($params->section == "ticket") ? "Ticket successfully closed." : "Article successfully closed.";

            // confirm if the ticket exists
            if(empty($this->pushQuery("id", $table, "id='{$params->ticket_id}' {$append} LIMIT 1"))) {
                return ["code" => 400, "data" => "Sorry! An invalid ticket id was parsed."];
            }

            // close the ticket
            $this->db->query("UPDATE {$table} SET status = 'Closed' WHERE id='{$params->ticket_id}' LIMIT 1");
            $this->db->query("UPDATE {$table} SET status = 'Closed' WHERE parent_id='{$params->ticket_id}' LIMIT {$this->temporal_maximum}");

            // return success message
            return ["code" => 200, "data" => $message];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
        
    }

    /**
     * Reopen a closed ticket
     * 
     * @param $params->ticket_id
     * 
     * @return Array
     */
    public function reopen(stdClass $params) {

        try {

            // global variable
            global $isSupport;

            // append this item
            $append = $isSupport ? "" : " AND client_id='{$params->clientId}'";

            // confirm if the ticket exists
            if(empty($this->pushQuery("id", "support_tickets", "id='{$params->ticket_id}' {$append} LIMIT 1"))) {
                return ["code" => 400, "data" => "Sorry! An invalid ticket id was parsed."];
            }

            // close the ticket
            $this->db->query("UPDATE support_tickets SET status = 'Reopened' WHERE id='{$params->ticket_id}' LIMIT 1");
            $this->db->query("UPDATE support_tickets SET status = 'Reopened' WHERE parent_id='{$params->ticket_id}' LIMIT {$this->temporal_maximum}");

            // return success message
            return ["code" => 200, "data" => "Ticket successfully Reopened."];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
        
    }

    /**
     * List Knowledge Base Items
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function knowledgebase_list(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        $params->parent_id = $params->parent_id ?? 0;

        // additional query parameters
        $params->query .= (isset($params->q)) ? " AND a.subject LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->item_id) && !empty($params->item_id)) ? " AND a.item_id='{$params->item_id}'" : null;
        $params->query .= (isset($params->knowledge_id) && !empty($params->knowledge_id)) ? " AND (a.id='{$params->knowledge_id}' OR a.item_id='{$params->knowledge_id}')" : null;
        $params->query .= isset($params->parent_id) ? " AND a.parent_id='{$params->parent_id}'" : null;

        $order = isset($params->order) ? $params->order : "DESC";

        try {

            // display the replies as well
            $q_param = (object) ["clientId" => $params->clientId];
            $showReplies = (bool) (isset($params->show_all) && !empty($params->show_all));

            // perform the query
            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT b.description FROM files_attachment b WHERE b.resource='knowledgebase' AND b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_info
                FROM knowledge_base a
                WHERE {$params->query} ORDER BY a.id {$order} LIMIT {$params->limit}
            ");
            $stmt->execute();

            // create a new object
            $filesObj = load_class("forms", "controllers");

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // clean the description
                $result->content = htmlspecialchars_decode($result->content);

                // set the list
                $result->content = $this->replace_placeholder($result->content);

                // if attachment variable was parsed
                $result->attachments_list = json_decode($result->attachment);

                // clean the words
                $result->attachment_html = isset($result->attachments_list->files) ? $filesObj->list_attachments($result->attachments_list->files, $result->user_id, "col-lg-3 col-md-6", false, false) : "";

                // loop through the information
                foreach(["user_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "email", "image","user_type"]);
                }
                // show the replies
                if($showReplies && ($result->parent_id == 0)) {
                    $q_param->parent_id = $result->id;
                    $q_param->order = "ASC";
                    $result->replies = $this->knowledgebase_list($q_param)["data"];
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
     * Create a new support ticket
     * 
     * @param $params->department
     * @param $params->content
     * @param $params->subject
     * 
     * @return Array
     */
    public function knowledgebase_add(stdClass $params) {

        global $isSupport;

        try {

            // only support personnel are allowed to add
            if(!$isSupport) {
                return ["code" => 400, "data" => $this->permission_denied];
            }

            // modify the content variable
            $params->content = custom_clean(htmlspecialchars_decode($params->content));
            $params->content = htmlspecialchars($params->content);

            // create an item id
            $item_id = random_string("alnum", RANDOM_STRING);

            // set the user type
            $user_type = ($params->userData->user_type === "support") ? "support" : "user";

            // insert the article data information
            $stmt = $this->db->prepare("INSERT INTO knowledge_base SET client_id =?, content = ?, 
                user_id = ?, subject = ?, department = ?, date_created = now(), date_updated = now(), 
                section = ?, user_type = ?, item_id = ?, status = ?, video_link = ?");
            $stmt->execute([$params->clientId, $params->content, $params->userId, $params->subject, 
                $params->department ?? null, $params->section ?? null, $user_type, $item_id, "Active", $params->video_link ?? null]);

            // create a new object
            $filesObj = load_class("files", "controllers");

            // attachments
            $attachments = $filesObj->prep_attachments("knowledgebase", $params->userId, $item_id);
            
            // log the user activity
            $this->userLogs("knowledgebase", $item_id, null, "{$params->userData->name} created a new article: {$params->subject}", $params->userId);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
            $files->execute(["knowledgebase", $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"], $params->clientId]);

            // return success message
            return [
                "code" => 200, 
                "data" => "The article was successfully created.",
                "additional" => [
                    "href" => "{$this->baseUrl}knowledgebase"
                ]
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Update a knowledge base article
     * 
     * @param $params->knowledge_id
     * @param $params->content
     * @param $params->subject
     * 
     * @return Array
     */
    public function knowledgebase_update(stdClass $params) {
        
        try {
            
            global $isSupport;

            // only support personnel are allowed to update
            if(!$isSupport) {
                return ["code" => 400, "data" => $this->permission_denied];
            }

            // modify the content variable
            $params->content = custom_clean(htmlspecialchars_decode($params->content));
            $params->content = htmlspecialchars($params->content);

            // update the article data information
            $stmt = $this->db->prepare("UPDATE knowledge_base SET content = ?, subject = ?, date_updated = now(), 
                section = ?, video_link = ? WHERE item_id = ? LIMIT 1");
            $stmt->execute([
                $params->content, $params->subject, $params->section ?? null, $params->video_link ?? null, $params->item_id
            ]);

            // return success message
            return ["code" => 200, "data" => "The article was successfully updated."];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
    }

    /**
     * Save User Access Level Permissions
     *
     * @param $params->data["permission"]
     * @param $params->data["access_id"]
     * 
     * @return Array
     */
    public function access_permission(stdClass $params) {

        // global variable
        global $isSupport, $defaultUser;
        
        // end query is the session access_denied_log is not empty
        if(!empty($this->session->access_denied_log)) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        // only support personnel are allowed to add
        if(!$isSupport) {

            // log the user information
            $this->db->query("INSERT INTO security_logs SET 
                client_id='{$params->clientId}', created_by='{$params->userId}', 
                section='Access Control Manager',
                description='The user attempted to change the user type access permissions on the system.'
            ");
            // set the session record
            $this->session->access_denied_log = true;

            // return an error
            return ["code" => 400, "data" => $this->permission_denied];
        }

        // unset the sessions
        $this->session->remove(["access_denied_log"]);

        // array data check
        if(!is_array($params->data)) {
            return ["code" => 400, "data" => "Sorry! An invalid dataset was parsed."];
        }

        // access permission check
        if(!isset($params->data["access_id"]) || !isset($params->data["permission"])) {
            return ["code" => 400, "data" => "Sorry! An invalid dataset was parsed."];
        }

        // set the access id
        $access_id = $params->data["access_id"];

        // confirm a valid json was parsed
        $permissions = $params->data["permission"];
        $param = json_decode($permissions);

        // if the param is empty
        if(empty($param)) {
           return ["code" => 400, "data" => "Please ensure a valid json data was parsed as permissions."];
        }

        // confirm that the permissions key was also parsed
        if(!isset($param->permissions)) {
            return ["code" => 400, "data" => "Please ensure a valid json data was parsed as permissions."];
        }

        try {

            // get the existing record
            $record = $this->pushQuery("id, description, user_permissions", 
                "users_types", "id='{$access_id}' LIMIT 1");

            // confirm the access permission id
            if(empty($record)) {
                return ["code" => 400, "data" => "Sorry! An invalid access level id was parsed."];
            }

            // update the user permissions
            if($record[0]->description == $defaultUser->user_type) {
                // set the user permissions to the current one
                $this->db->query("UPDATE users_roles SET last_updated=now(), permissions='{$permissions}' WHERE user_id='{$params->userId}' LIMIT 1");
            }

            // if there was a change in the original content
            if($record[0]->user_permissions !== $permissions) {
                // update the database information
                $stmt = $this->db->prepare("UPDATE users_types SET user_permissions=? WHERE id=? LIMIT 1");
                $stmt->execute([$permissions, $access_id]);

                // log the user activity
                $this->userLogs(
                    "permission_log", $access_id, $record[0]->user_permissions, 
                    "{$params->userData->name} updated the user permissions type.", $params->userId
                );
            }

            // get the list of users
            if(in_array($record[0]->description, ["parent", "student"])) {
                // get all the parents list
                $parents_list = $this->pushQuery("id, item_id, name", "users", "status='1' AND user_type = '{$record[0]->description}'");
                $get_ids = array_column($parents_list, "item_id");
                if(!empty($get_ids)) {
                    $this->quickUpdate("permissions='{$permissions}', last_updated=now()", "users_roles", "user_id IN {$this->inList($get_ids)}");
                }
            }

            // return the success message
            return ["data" => "Access permission successfully logged."];

        } catch(PDOException $e) {}

    }

}