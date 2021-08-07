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
                SELECT a.*,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_info
                FROM support_tickets a
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
            $params->content = nl2br($params->content);

            // insert the ticket data information
            $stmt = $this->db->prepare("INSERT INTO support_tickets SET 
                client_id =?, content = ?, user_id = ?, subject = ?, department = ?, date_created = now()");
            $stmt->execute([$params->clientId, $params->content, $params->userId, $params->subject, $params->department]);

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
            global $noticeClass, $accessObject;

            // check the query
            $query = $this->pushQuery("status, user_id, subject", "support_tickets", "id='{$params->ticket_id}' AND client_id='{$params->clientId}' LIMIT 1");

            // confirm if the ticket exists
            if(empty($query)) {
                return ["code" => 203, "data" => "Sorry! An invalid ticket id was parsed."];
            }

            // if the ticket is closed
            if($query[0]->status == "Closed"){
                return ["code" => 203, "data" => "Sorry! The ticket has been closed, hence cannot send any message."];
            }

            // set the status
            $params->content = nl2br($params->content);
            $status = (($query[0]->user_id == $params->userId) || ($params->userData->user_type !== "support")) ? "Waiting" : "Answered";

            // insert the reply
            $stmt = $this->db->prepare("INSERT INTO support_tickets SET client_id = ?, parent_id = ?, content = ?, user_id = ?");
            $stmt->execute([$params->clientId, $params->ticket_id, $params->content, $params->userId]);

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
            $this->db->query("UPDATE support_tickets SET date_updated = now(), status='{$status}' WHERE id='{$params->ticket_id}' LIMIT 1");

            // return success message
            return ["code" => 200, "data" => "Reply was successfully shared."];

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

            // confirm if the ticket exists
            if(empty($this->pushQuery("id", "support_tickets", "id='{$params->ticket_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
                return ["code" => 203, "data" => "Sorry! An invalid ticket id was parsed."];
            }

            // close the ticket
            $this->db->query("UPDATE support_tickets SET status = 'Closed' WHERE id='{$params->ticket_id}' LIMIT 1");

            // return success message
            return ["code" => 200, "data" => "Ticket successfully closed."];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
        
    }

}