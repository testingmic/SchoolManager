<?php

class Replies extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set the delete button
     * 
     * @param String $item_id
     * @param String $reply_user_id
     * @param String $logged_user_id
     * @param Int $date_created
     * 
     * @return String
     */
    private function is_deletable($item_id, $reply_user_id, $logged_user_id, $date_created) {

        // if the user who shared it is not the same as the person logged in then return
        if($reply_user_id !== $logged_user_id) {
            return "";
        }
        
        // set the delete button
        $delete_button = '<div class="dropdown reply-options" id="reply-option">
                <button class="p-0 btn toggle-reply-options" type="button" id="dropdownMenuButton_'.$item_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i style="font-size:12px" class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                    <a class="dropdown-item delete-reply d-flex align-items-center" data-reply-id="'.$item_id.'" href="javascript:void(0)"><i class="fa fa-trash" class="icon-sm mr-2"></i> &nbsp;<span class="">Delete</span></a>
                </div>
            </div>';

        // if the post is more than 3 hours then it cannot be deleted
        if(raw_time_diff($date_created) < 2.99) {
            return $delete_button;
        }

        return "";

    }

    /**
     * List replies for an item
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        // init the filter
        $where_clause = "";
        $params->query = "1";

        // add some filters
        $params->query .= (isset($params->resource_id)) ? " AND a.resource_id='{$params->resource_id}'" : null;
        $params->query .= (isset($params->user_id)) ? " AND a.user_id='{$params->user_id}'" : null;
        $params->query .= (isset($params->item_id)) ? " AND a.item_id='{$params->item_id}'" : null;
        $params->query .= (isset($params->feedback_type)) ? " AND a.feedback_type='{$params->feedback_type}'" : null;
        $params->query .= (isset($params->resource)) ? " AND a.resource='{$params->resource}'" : null;

        // the number of rows to limit the query
		$params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // check the last item to display
        $params->last_reply_id = isset($params->last_reply_id) && !empty($params->last_reply_id) ? $params->last_reply_id : null;
        if($params->last_reply_id) {
            if($params->last_reply_id == "no_more_record") {
                $where_clause = " AND a.id < '{$params->last_reply_id}'";
            } else {
                $where_clause = " AND a.id < {$params->last_reply_id}";
            }
        }

        //
        try {
            // set initial results
            $results = [];
            $filesObject = load_class("forms", "controllers");

            // load the very first record in the query parsed by the user
            $last_one = $this->db->prepare("SELECT a.id AS first_item FROM users_feedback a WHERE {$params->query} {$where_clause} ORDER BY a.id ASC LIMIT 1");
            $last_one->execute();
            $first_item = $last_one->fetch(PDO::FETCH_OBJ);

            // prepare the statement
            $stmt = $this->db->prepare("SELECT a.id, a.item_id, a.resource, a.user_type, a.message, a.user_id, 
                    a.likes_count, a.comments_count, DATE(a.date_created) AS raw_date,
                    (
                        SELECT b.description FROM files_attachment b 
                        WHERE b.record_id = CONCAT(a.resource_id,'_',a.item_id) 
                        ORDER BY b.id DESC LIMIT 1
                    ) AS attachment, 
                    a.deleted, a.date_created, a.user_agent, a.mentions, a.user_id, a.user_type
                FROM users_feedback a
                WHERE {$params->query} {$where_clause} ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // init the values
            $data = [];
            $resource = "";
            $last_reply_id = "no_more_record";
            // loop through the records
            foreach($results as $key => $result) {
                $key++;
                $result->row_id = $key;
                $resource = $result->resource;

                $result->replied_by = $this->replied_by($result->user_id);
                $result->message = htmlspecialchars_decode($result->message);
                $result->message = custom_clean($result->message);
                
                // delete 
                if(!$result->deleted) {
                    $result->attachment = !empty($result->attachment) ? json_decode($result->attachment) : $this->fake_files;
                }

                $result->time_ago = date("h:iA", strtotime($result->date_created));
                $result->clean_date = date("l, F jS", strtotime($result->date_created));
                
                // if not a remote request
                if((isset($params->remote) && !$params->remote) || !isset($params->remote)) {
                    $result->attachment_html = "";
                    
                    if($result->resource !== "cancel_policy_comments") {
                        $result->delete_button = $result->deleted ? "" : $this->is_deletable($result->item_id, $result->user_id, $params->userId, $result->date_created);
                    } else {
                        $result->delete_button = "";
                    }
                    
                    if(!empty($result->attachment->files) && !$result->deleted) {
                        $result->attachment_html = $filesObject->list_attachments($result->attachment->files, $result->user_id, "col-lg-6 col-md-6", false, false);
                    }
                }

                // if the message has been deleted
                $result->message = $result->deleted ? "<div class=\"font-italic\">This message was deleted</div>" : $result->message;

                // set the attachment to empty if the message is deleted
                if($result->deleted) {
                    $result->attachment = [
                        "files" => [],
                        "files_size" => []
                    ];
                }

                $last_reply_id = $result->id;

                // unset some variables
                unset($result->deleted);
                unset($result->id);

                $result->modified_date = date("l, F jS, Y \a\\t h:i:sA", strtotime($result->date_created));

                $data[] = $result;
            }

            // last reply id
            if(!empty($data)) {
                // $last_id = min(array_column($data, "id"));
                $last_reply_id = $last_reply_id;
            }
            
            // last row id record
            return [
                "replies_list" => $data,
                "replies_resource" => $resource,
                "first_reply_id" => isset($first_item->first_item) ? $first_item->first_item : 0,
                "last_reply_id" => $last_reply_id
            ];
        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Add a new reply
     * 
     * @param \stdClass $params
     * @param String    $params->resource
     * @param String    $params->record_id
     * @param String    $params->message
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        /** Validate the request */
        if(!in_array($params->resource, [
            'claims','licenses','company_policy','complaints','policies', 'adverts',
            'user_policy','reports','payments','announcements','insurance_company'
        ])) {
            return ["code" => 203, "data" => "Invalid request parsed"];
        }

        // table names
        $table_pages = [
            "user_policy" => ["page" => "policies-view"],
            "policies" => ["page" => "policies-view"],
            "claims" => ["page" => "claims-view"],
            "licenses" => ["page" => "licenses-view"],
            "complaints" => ["page" => "complaints-view"],
            "company_policy" => ["page" => "policy-view"],
            "reports" => ["page" => "reports-view"],
            "adverts" => ["page" => "adverts-view"],
            "announcements" => ["page" => "announcements"],
            "insurance_company" => ["page" => "configuration"],
            "payments" => ["page" => "payments-view"],
        ];

        /** Process the data parsed */
        $params->message = addslashes($params->message);

        /** Create a random string */
        $params->_item_id = random_string("alnum", 32);

        // append the attachments
        $filesObj = load_class("files", "controllers");
        $attachments = $filesObj->prep_attachments("{$params->resource}_replies_{$params->record_id}", $params->userData->user_id, $params->record_id);
        
        try {

            // global variable for notice
            global $noticeClass;

            // begin transaction
            $this->db->beginTransaction();

            // clean the content
            $ur_agent = $this->platform .' | '.$this->browser . ' | '.ip_address();
            
            // insert the record
            $stmt = $this->db->prepare("
                INSERT INTO users_feedback SET date_created = now(), user_agent = ?
                ".(isset($params->userId) ? ",user_id='{$params->userId}'" : null)."
                ".(isset($params->record_id) ? ",resource_id='{$params->record_id}'" : null)."
                ".(isset($params->_item_id) ? ",item_id='{$params->_item_id}'" : null)."
                ".(isset($params->resource) ? ",resource='{$params->resource}'" : null)."
                ".(isset($params->subject) ? ",subject='{$params->subject}'" : null)."                
                ".(isset($params->message) ? ",message='{$params->message}'" : null)."
                ".(isset($params->mentions) ? ",mentions='{$params->mentions}'" : null)."
                ".(isset($params->userData->user_type) ? ",user_type='{$params->userData->user_type}'" : null)."
            ");

            // initial
            $replies_count = "";
            
            // execute the prepared statement
            if($stmt->execute([$ur_agent])) {
                
                $code = 200;
                
                // only insert attachment record if there was an attachment to the comment
                if(!empty($attachments["files"])) {
                    // prepare and execute the statement
                    $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                    $files->execute([$params->resource, $params->_item_id, json_encode($attachments), "{$params->record_id}_{$params->_item_id}", $params->userId, $attachments["raw_size_mb"]]);
                }

                // set the table name
                $table_name = $this->resource_parameters[$params->resource]["table"];

                // get the table name
                if(!in_array($params->resource, ['company_policy'])) {

                    // get the one who created the record
                    $creator_id = $this->columnValue("user_id", $table_name, "item_id='{$params->record_id}'")->user_id;
                    $assigned_to = $this->columnValue("assigned_to", $table_name, "item_id='{$params->record_id}'")->assigned_to;

                    // if the one replying is not the creator, then notify the creator of a new reply
                    if($creator_id !== $params->userId) {
                        
                        // form the notification parameters
                        $notice_param = (object) [
                            '_item_id' => random_string("alnum", 32),
                            'user_id' => $creator_id,
                            'subject' => "Thread Reply",
                            'username' => $params->userData->username,
                            'remote' => false, 
                            'message' => "<strong>{$params->userData->name}</strong> left a new reply on your <a title=\"Click to view {$this->resource_parameters[$params->resource]["message"]}\" href=\"{{APPURL}}{$table_pages[$params->resource]["page"]}/{$params->record_id}\">{$this->resource_parameters[$params->resource]["message"]}</a>.",
                            'notice_type' => 5,
                            'userId' => $params->userId,
                            'initiated_by' => 'system'
                        ];
                        // add a new notification
                        $noticeClass->add($notice_param);

                        // if the assigned_to is empty then set the assigned_to to the one replying
                        $assigned_to = empty($assigned_to) ? ", assigned_to='{$params->userId}'" : null;
                        // set the new status
                        if(in_array($table_name, ["users_complaints"])) {
                            $this->db->query("UPDATE {$table_name} SET status='Answered' {$assigned_to} WHERE item_id='{$params->record_id}' LIMIT 1");
                        }
                    } else {
                        // update the status and set it to waiting
                        if(in_array($table_name, ["users_complaints"])) {
                            $this->db->query("UPDATE {$table_name} SET status='Waiting' WHERE item_id='{$params->record_id}' LIMIT 1");
                        }
                    }
                }

                // get the count
                $counter = $this->columnValue("replies_count", $table_name, "item_id='{$params->record_id}'");

                // get the row value
                $replies_count = isset($counter->replies_count) ? ($counter->replies_count+1) : 1;

                // update the replies count for the resource
                $this->db->query("UPDATE {$table_name} SET replies_count=(replies_count+1) WHERE item_id='{$params->record_id}' LIMIT 1");
                
                // add the activity for the activity
                $this->userLogs("Replies Count", $params->record_id, null, "Number of replies is set to {$replies_count}.", $params->userId, "{$this->appName} Calculation<br>Replies count increased by the creation of a new reply</strong>.");

                // log the user activity
                $this->userLogs("Reply", $params->record_id, null, "<strong>{$params->userData->name}</strong> shared a reply on this.", $params->userId, "<strong>{$params->userData->name}</strong><br>Manual reply to the resource.");
                $data = "Reply successfully shared.";


            } else {
                $data = "Sorry! There was an error while processing the request.";
            }

            // commit the transaction
            $this->db->commit();

            return [
                "code" => $code,
                "data" => $data,
                "additional" => [
                    "clear" => true,
                    "record" => [
                        "record_id" => "{$params->record_id}",
                        "resource" => "{$params->resource}",
                        "replies_count" => $replies_count
                    ]
                ]
            ];

        } catch(PDOException $e) {
            // roll back the transaction
            $this->db->rollBack();
            $e->getMessage();
        }

    }

    /**
     * Add a comment
     * 
     * @param \stdClass $params
     * @param String    $params->comment
     * @param String    $params->item_id
     * 
     * @return Array
     */
    public function comment(stdClass $params) {

        /** Process the data parsed */
        $params->comment = addslashes(nl2br($params->comment));
        
        /** Create a random string */
        $params->_item_id = random_string("alnum", 32);

        /** The resource */
        $resource = explode("_", $params->resource)[0];

        /** If the resource is not in the array */
        if(!in_array($resource, ["complaint", "policy", "claim", "cancel", "licenses", "adverts"])) {
            return ["code" => 203, "data" => "Invalid request parsed: complaint, policy, claim, cancel, licenses, adverts"];
        }

        // append the attachments
        $filesObj = load_class("files", "controllers");
        $attachments = $filesObj->prep_attachments("{$params->resource}_{$params->item_id}", $params->userData->user_id, $params->item_id);

        // table names
        $table_name = [
            "policy" => [
                "table" => "users_policy",
                "page" => "policies-view"
            ],
            "claim" => [
                "table" => "users_policy_claims",
                "page" => "claims-view"
            ],
            "licenses" => [
                "table" => "companies_licenses",
                "page" => "licenses-view"
            ],
            "complaint" => [
                "table" => "users_complaints",
                "page" => "complaints-view"
            ],
        ];
        
        try {

            // global variable for notice
            global $noticeClass;

            // initial
            $comments_count = "";
            
            // begin transaction
            $this->db->beginTransaction();

            // clean the content
            $ur_agent = $this->platform .' | '.$this->browser . ' | '.ip_address();

            // insert the record
            $stmt = $this->db->prepare("
                INSERT INTO users_feedback SET date_created = now(), user_agent = ?, feedback_type = 'comment'
                ".(isset($params->userId) ? ",user_id='{$params->userId}'" : null)."
                ".(isset($params->item_id) ? ",resource_id='{$params->item_id}'" : null)."
                ".(isset($params->_item_id) ? ",item_id='{$params->_item_id}'" : null)."
                ".(isset($params->resource) ? ",resource='{$params->resource}'" : null)."               
                ".(isset($params->comment) ? ",message='{$params->comment}'" : null)."
                ".(isset($params->mentions) ? ",mentions='{$params->mentions}'" : null)."
                ".(isset($params->userData->user_type) ? ",user_type='{$params->userData->user_type}'" : null)."
            ");

            // execute the prepared statement
            if($stmt->execute([$ur_agent])) {

                // only insert attachment record if there was an attachment to the comment
                if(!empty($attachments["files"])) {
                    // prepare and execute the statement
                    $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                    $files->execute([$params->resource, $params->item_id, json_encode($attachments), "{$params->item_id}_{$params->_item_id}", $params->userId, $attachments["raw_size_mb"]]);
                }

                // log the user activity
                $this->userLogs("Comment", $params->item_id, null, "<strong>{$params->userData->name}</strong> left a comment on this.", $params->userId, "<strong>{$params->userData->name}</strong><br>Manual reply to the resource.");
                
                $data = "Comment successfully shared.";

                // if the table was found
                if(isset($table_name[$resource]["table"])) {

                    // get the one who created the record
                    $client_id = $this->columnValue("user_id, created_by", $table_name[$resource]["table"], "item_id='{$params->item_id}'");

                    // if the one replying is not the creator, then notify the creator of a new reply
                    if(isset($client_id->user_id) && ($client_id->user_id !== $params->userId)) {
                        
                        // form the notification parameters
                        $notice_param = (object) [
                            '_item_id' => random_string("alnum", 32),
                            'user_id' => $client_id->user_id,
                            'subject' => "Thread Comment",
                            'username' => $params->userData->username,
                            'remote' => false, 
                            'message' => "<strong>{$params->userData->name}</strong> left a comment on your <a title=\"Click to view {$resource}\" href=\"{{APPURL}}{$table_name[$resource]["page"]}/{$params->item_id}\">{$resource}</a>.",
                            'notice_type' => 5,
                            'userId' => $params->userId,
                            'initiated_by' => 'system'
                        ];

                        // add a new notification
                        $noticeClass->add($notice_param);

                        // notify the person who created the item as well (agent, broker or bancassurance)
                        if(isset($client_id->created_by) && ($client_id->created_by !== $params->userId)) {

                            // form the notification parameters
                            $notice_param = (object) [
                                '_item_id' => random_string("alnum", 32),
                                'user_id' => $client_id->created_by,
                                'subject' => "Thread Comment",
                                'username' => $params->userData->username,
                                'remote' => false, 
                                'message' => "<strong>{$params->userData->name}</strong> left a comment on your <a title=\"Click to view {$resource}\" href=\"{{APPURL}}{$table_name[$resource]["page"]}/{$params->item_id}\">{$resource}</a>.",
                                'notice_type' => 5,
                                'userId' => $params->userId,
                                'initiated_by' => 'system'
                            ];

                            // add a new notification
                            $noticeClass->add($notice_param);
                        }

                    }

                    // get the count
                    $counter = $this->columnValue("comments_count", $table_name[$resource]["table"], "item_id='{$params->item_id}'");

                    // get the row value
                    $comments_count = isset($counter->comments_count) ? ($counter->comments_count + 1) : 1;

                    // update the comments count for the resource
                    $this->db->query("UPDATE {$table_name[$resource]["table"]} SET comments_count=(comments_count+1) WHERE item_id='{$params->item_id}' LIMIT 1");
                    
                    // add the activity for the activity
                    $this->userLogs("Comments Count", $params->item_id, null, "Number of comments is set to {$comments_count}.", $params->userId, "{$this->appName} Calculation<br>Replies count increased by the creation of a new comment by <strong>{$params->userData->name}</strong>.");

                }
                
                // if resource is cancel_policy_comments then notify the user
                if($params->resource == "cancel_policy_comments") {
                    // get the one who created the record
                    $client_id = $this->columnValue("user_id, created_by, policy_id", "users_policy_cancellation_request", "slug='{$params->item_id}'");
                    
                    // if the user is not the same as the person submitting the comment
                    if(isset($client_id->user_id) && ($client_id->user_id !== $params->userId)) {
                        
                        // form the notification parameters
                        $notice_param = (object) [
                            '_item_id' => random_string("alnum", 32),
                            'user_id' => $client_id->user_id,
                            'subject' => "Thread Comment",
                            'username' => $params->userData->username,
                            'remote' => false, 
                            'message' => "<strong>{$params->userData->name}</strong> left a comment on your request to cancel the policy <a title=\"Click to view {$resource}\" href=\"{{APPURL}}policies-view/{$params->item_id}\">{$client_id->policy_id}</a>.",
                            'notice_type' => 5,
                            'userId' => $params->userId,
                            'initiated_by' => 'system'
                        ];
                        
                        // add a new notification
                        $noticeClass->add($notice_param);
                    }
                    
                }

            } else {
                $data = "Sorry! There was an error while processing the request.";
            }

            // commit the transaction
            $this->db->commit();

            // load the message just posted
            $data_param = (object) [
                "item_id" => $params->_item_id,
                "userId" => $params->userId,
                "limit" => 1
            ];
            $last_comment = $this->list($data_param)["replies_list"][0];
            
            // response array
            $response_array = ["code" => 200,"data" => $data];
            $response_array["additional"]["record"] = [];
            $response_array["additional"]["data"] = $last_comment;

            // additonal information 
            if($params->resource !== "cancel_policy_comments") {
                $response_array["additional"]["record"] = [
                    "comments_count" => $comments_count
                ];
            }

            return $response_array;            

        } catch(PDOException $e) {
            // roll back the transaction
            $this->db->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * Delete a reply message
     * 
     * @param \stdClass $params
     * @param String    $params->reply_id
     *  
     * @return Array
     */
    public function delete(stdClass $params) {

        /** Load the reply information */
        $replyInfo = $this->pushQuery("user_id", "users_feedback", "item_id = '{$params->reply_id}' AND deleted='0' LIMIT 1");

        /** Return if no data was found */
        if(empty($replyInfo)) {
            return ["code" => 203, "data" => "Sorry! An invalid reply id was parsed"];
        }

        /** Check the user id to the person trying to delete the object */
        if($replyInfo[0]->user_id !== $params->userId) {
            return ["code" => 203, "data" => "Sorry! You are not permitted to delete this reply object"];
        }

        try {

            /** Prepare and execute the statement */
            $stmt = $this->db->prepare("UPDATE users_feedback SET deleted = ? WHERE item_id = ? LIMIT 1");
            $stmt->execute([1, $params->reply_id]);

            /** Log the user activity */
            $this->userLogs("Delete", $params->reply_id, null, "<strong>{$params->userData->name}</strong> deleted the comment.", $params->userId, "<strong>{$params->userData->name}</strong><br>Manual delete of the resource within {$this->allowed_delete_range} hours of posting.");

            /** Return success response */
            return [
                "code" => 200,
                "data" => "The reply was successfully deleted."
            ];

        } catch(PDOException $e) {
            return false;
        }

    }

}
?>