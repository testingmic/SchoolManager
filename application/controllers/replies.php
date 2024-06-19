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
        $params->query .= (!empty($params->resource_id)) ? " AND a.resource_id='{$params->resource_id}'" : null;
        $params->query .= (!empty($params->user_id)) ? " AND a.user_id='{$params->user_id}'" : null;
        $params->query .= (!empty($params->item_id)) ? " AND a.item_id='{$params->item_id}'" : null;
        $params->query .= (!empty($params->feedback_type)) ? " AND a.feedback_type='{$params->feedback_type}'" : null;
        $params->query .= (!empty($params->resource)) ? " AND a.resource='{$params->resource}'" : null;

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

        // get the entire comments list counter
        $comments_count = $this->pushQuery("COUNT(*) AS comments_count", "users_feedback a", "{$params->query} {$where_clause}");

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
                    a.likes_count, a.comments_count, DATE(a.date_created) AS raw_date, a.is_deletable,
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
                    $result->delete_button = "";
                    
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
                "comments_count" => $comments_count[0]->comments_count ?? 0,
                "replies_list" => $data,
                "replies_resource" => $resource,
                "first_reply_id" => isset($first_item->first_item) ? $first_item->first_item : 0,
                "last_reply_id" => $last_reply_id
            ];
        } catch(PDOException $e) {
            return [];
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
        if(!in_array($params->resource, ['assignments', 'document', 'daily_report', 'leave'])) {
            return ["code" => 203, "data" => "Invalid request parsed"];
        }

        // clean the description 
        $params->message = !empty($params->message) ? custom_clean(htmlspecialchars_decode($params->message)) : null;
        $params->message = htmlspecialchars($params->message);

        /** Create a random string */
        $params->_item_id = random_string("alnum", RANDOM_STRING);

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

        // clean the description 
        $params->comment = !empty($params->comment) ? custom_clean(htmlspecialchars_decode($params->comment)) : null;
        $params->comment = htmlspecialchars($params->comment);
        
        /** Create a random string */
        $params->_item_id = random_string("alnum", RANDOM_STRING);

        /** The resource */
        $resource = $params->resource;

        /** If the resource is not in the array */
        if(!in_array($resource, ["assignments", "events", "ebook", "books_request", "document", "bus", "application", "daily_report", "leave", "frontoffice"])) {
            return ["code" => 203, "data" => "Invalid request parsed"];
        }

        // append the attachments
        $filesObj = load_class("files", "controllers");
        $attachments = $filesObj->prep_attachments("{$params->resource}_{$params->item_id}", $params->userData->user_id, $params->item_id);

        // table names
        $table_name = [
            "assignments" => [
                "table" => "assignments",
                "page" => "assessment"
            ], "events" => [
                "table" => "events",
                "page" => "update-event"
            ], "document" => [
                "table" => "documents",
                "page" => "document"
            ], "bus" => [
                "table" => "buses",
                "page" => "bus"
            ], "daily_report" => [
                "table" => "daily_reports",
                "page" => "students_daily_reports"
            ], "frontoffice" => [
                "table" => "frontoffice",
                "page" => "frontoffice"
            ]
        ];
        
        try {

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
                ".(isset($params->resource) ? ',resource="'.$params->resource.'"' : null)."
                ".(isset($params->comment) ? ',message="'.$params->comment.'"' : null)."
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

                    
                    // get the count
                    $counter = $this->columnValue("comments_count", $table_name[$resource]["table"], "item_id='{$params->item_id}'");

                    // get the row value
                    $comments_count = isset($counter->comments_count) ? ($counter->comments_count + 1) : 1;

                    // update the comments count for the resource
                    $this->db->query("UPDATE {$table_name[$resource]["table"]} SET comments_count=(comments_count+1) WHERE item_id='{$params->item_id}' LIMIT 1");
                    
                    // add the activity for the activity
                    $this->userLogs("Comments Count", $params->item_id, null, "Number of comments is set to {$comments_count}.", $params->userId, "{$this->appName} Calculation<br>Replies count increased by the creation of a new comment by <strong>{$params->userData->name}</strong>.");

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
            $response_array["additional"]["record"] = [
                "comments_count" => $comments_count
            ];

            return $response_array;            

        } catch(PDOException $e) {
            // roll back the transaction
            $this->db->rollBack();
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

    /**
     * Share a comment
     * 
     * @return Array
     */
    public function share(stdClass $params) {

        // clean the string 
        $comment = (htmlspecialchars_decode($params->comment));
        $comment = str_ireplace("<div>", "<br>", $comment);
        $comment = strip_tags(custom_clean($comment), "<br>");
        $comment = htmlspecialchars($comment);

        // get the type of message to push
        $type = (isset($params->comment_id) && !empty($params->comment_id)) ? "reply" : "comment";

        // insert the comments into the table
        try {
            
            $stmt = $this->db->prepare("INSERT INTO e_learning_comments SET type = ?, comment_id = ?,
                comment = ?, user_id = ?, record_id = ?, ipaddress = ?, user_agent = ?");
            $stmt->execute([
                $type, $params->comment_id ?? NULL, $comment, $params->userId, $params->record_id, 
                $this->ip_address, $this->agent
            ]);
            
            // get the comment id
            $comment_id = $this->lastRowId("e_learning_comments");
            
            // create an object
            $resourceObj = load_class("resources", "controllers");

            // save the video time if parsed
            if(isset($params->video_time)) {
                // set the video id
                $params->video_id = $params->record_id;
                
                // save the time for this video 
                $resourceObj->save_time($params);
            }

            // add comments
            $this->db->query("UPDATE e_learning_views SET comments=(comments+1) WHERE video_id='{$params->record_id}' LIMIT 1");
            
            // add the activity for the activity
            $this->userLogs("e_learning_video", $params->record_id, null, "<strong>{$params->userData->name}</strong> Shared the comment: <em>{$comment}</em> on the Video.", $params->userId, "{$this->appName} Calculation<br>Replies count increased by the creation of a new comment by <strong>{$params->userData->name}</strong>.");

            // get the last comment information
            $params->comment_id = $comment_id;
            $params->limit = 1;
            $comment = $resourceObj->comments_list($params)["comments_list"];
            $comment = !empty($comment) ? $comment[0] : [];

            return [
                "data" => $comment
            ];

        } catch(PDOException $e) {}
    }

}
?>