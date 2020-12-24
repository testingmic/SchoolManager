<?php 

class Announcements extends Myschoolgh {

    /** The recipient group for the announcement */
    private $is_relatedto;

    public function __construct() {
        /** Parent construct */
        parent::__construct();

        /** set the value for the group */
        $this->is_relatedto = [
            "all" => "All Users",
            "user" => "Clients",
            "business" => "Clients",
            "insurance_company" => "Insurance Companies",
            "broker" => "Brokers",
            "agent" => "Agents",
            "bank" => "Banks",
            "client" => "Clients"
        ];

    }

    /**
     * Confirm that the user has read the mail
     * 
     * @param \stdClass $seen_list
     * @param String $user_id
     */
    private function is_seen($seen_list, $user_id) {

        if(empty($seen_list)) {
            return false;
        }

        // convert the list into an array
        $seen_list = (array) $seen_list;

        // return boolean
        return (bool) in_array($user_id, $seen_list);
    }

    /** 
     * List the announcments
     * Apply any filters available and return the results either a minimal content or the full content
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        $filter = "";
        $filter .= isset($params->announcement_id) ? " AND a.item_id='{$params->announcement_id}'" : null;
        $filter .= isset($params->where_clause) ? $params->where_clause : null;

        $params->start_point = !isset($params->start_point) ? 1 : $params->start_point;
        $params->limit = isset($params->limit) ? (int) $params->limit : $this->global_limit;

        try {

            // global variable
            global $accessObject;
            
            // run this section if internal load was not parsed
            if(!isset($params->minimal_load)) {
                
                // list the annoucement item if only the START DATE is beyond the current date
                if(!in_array($params->userData->user_type, ["nic", "admin", "insurance_company"]))  {
                    $user_type = in_array($params->userData->user_type, ["user", "business"]) ? "client" : $params->userData->user_type;
                    $filter .= " AND CURRENT_TIMESTAMP() >= TIMESTAMP(a.start_date) AND (a.recipient_group='{$user_type}' OR a.recipient_group='all')";
                }

                // if an insurance company official is signed in then the company id must match
                if(in_array($params->userData->user_type, ["insurance_company"]))  {
                    $company_id = $params->userData->company_id;
                    $filter .= " AND a.company_id='{$company_id}'";
                }

                // query the data
                $total_ = $this->db->prepare("SELECT a.* FROM announcements a WHERE a.status = ? {$filter} ORDER BY a.id DESC");
                $total_->execute([1]);

                $total_count = $total_->rowCount();
            }

            // prepare the query and execute
            $stmt = $this->db->prepare("
                SELECT a.*, a.item_id AS announcement_id,
                (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment 
                FROM announcements a WHERE a.status = ? {$filter} ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            // has permission to update announcement
            $updateAnnouncement = $accessObject->hasAccess("update", "announcements");

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // start date
                $result->time_ago = time_diff($result->start_date);

                // init the editable
                $result->is_editable = false;

                // is active
                $result->is_active = ((strtotime($result->start_date) < time()) && (strtotime($result->end_date) > time()));

                // if the user has permission to update the announcement
                if($updateAnnouncement) {
                    $result->is_editable = (in_array($params->userData->user_type, [$result->user_type]) && (time() < strtotime($result->start_date)) && $updateAnnouncement);   
                }

                // related to
                $result->related_to = $this->is_relatedto[$result->recipient_group];

                // attachment query
                $result->attachment = json_decode($result->attachment);

                // if clean date was parsed
                if(isset($params->clean_date)) {
                    $result->start_date = date("Y-m-d", strtotime($result->start_date));
                    $result->end_date = date("Y-m-d", strtotime($result->end_date));
                }

                // if the user has read the message
                $result->is_seen = $this->is_seen($result->seen_by, $params->userId);

                // append to the society
                $data[] = $result;
            }

            // if the user request for the data only
            if(isset($params->minimal_load)) {
                return $data;
            }

            // listing algorithm
            $total_result = count($data);

            // return the final result
            return [
                "list" => $data,
                "pagination" => [
                    "total_count" => $total_count,
                    "start_point" => $params->start_point,
                    "end_point" => ($params->start_point + $total_result),
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update the announcement content
     * 
     * Append any attachments to the preexisting on and save
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        // global
        global $accessObject;

        // if the user has permission to update the 
        if(!$accessObject->hasAccess("update", "announcements")) {
            return $this->permission_denied;
        }

        // init
        $group = array_keys($this->is_relatedto);

        // confirm that the recipient group is in the array list
        if(!in_array($params->recipient_group, $group)) {
            return "An invalid recipient group value was parsed. Accepted: ".implode(", ", $group);
        }

        /** Confirm that this claims does not already exist */
        $announcementCheck = $this->pushQuery("a.id, a.start_date, a.end_date, a.recipient_group, a.priority, a.subject, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY a.id DESC LIMIT 1) AS attachment", 
            "announcements a", "a.item_id='{$params->announcement_id}' AND a.status ='1' LIMIT 1");

        // if the claims check is not empty
        if(empty($announcementCheck)) {
            return ["data" => "Sorry! The record was not found"];
        }

        // initialize
        $initial_attachment = [];

        // define the module
        $module = "announcements_{$params->announcement_id}";

        // policy data
        $announcementData = $announcementCheck[0];

        /** Confirm that there is an attached document */
        if(!empty($announcementData->attachment)) {
            // decode the json string
            $db_attachments = json_decode($announcementData->attachment);
            // get the files
            if(isset($db_attachments->files)) {
                $initial_attachment = $db_attachments->files;
            }
        }

        $params->message = custom_clean(htmlspecialchars_decode($params->message));

        // form the post modal information
        $modal = "
        <div class=\"modal announcementModal_{$params->announcement_id} fade\" data-backdrop=\"static\" data-keyboard=\"false\" id=\"announcementModal_{$params->announcement_id}\" data-announcement-id=\"{$params->announcement_id}\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">
            <div class=\"modal-dialog\" role=\"document\" style=\"min-width:300px; bottom: 0px\">
                <div class=\"modal-content\">
                    <div class=\"modal-header\">
                        <h4><small class=\"tx-11\"><i class=\"fas fa-info\"></i></small> {$params->subject}</h4>
                    </div>
                    <div class=\"modal-body\">
                        <div class=\"notice\">
                            {$params->message}
                        </div>
                    </div>
                    <div class=\"modal-footer text-right\">
                        <button data-announcement-id=\"{$params->announcement_id}\" type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                    </div>
                </div>
            </div>
        </div>";
        
        // announcement start and end dates
        $start_date = isset($params->start_date) ? date("Y-m-d H:i", strtotime($params->start_date)) : null;
        $end_date = isset($params->end_date) ? date("Y-m-d 23:59", strtotime($params->end_date)) : null;

        // append the attachments
        $filesObj = load_class("files", "controllers");
        $attachments = $filesObj->prep_attachments("announcements", $params->userData->user_id, $params->announcement_id, $initial_attachment);

        try {
            // begin transaction
            $this->db->beginTransaction();
            
            // prepare and execute the statement
            $stmt = $this->db->prepare("
                UPDATE announcements SET 
                    last_updated_by = ?, last_updated_date = now(), recipient_group = ?, subject = ?, message = ?, content = ?, user_type = ? 
                    ".(!empty($start_date) ? ",start_date='{$start_date}'" : null)."
                    ".(isset($params->priority) ? ",priority='{$params->priority}'" : null)."
                    ".(!empty($end_date) ? ",end_date='{$end_date}'" : null)."
                WHERE item_id = ? LIMIT 1
            ");
            $stmt->execute([$params->userId, $params->recipient_group, $params->subject, $params->message, $modal, $params->userData->user_type, $params->announcement_id]);

            // update attachment if already existing
            if(isset($db_attachments)) {
                $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? LIMIT 1");
                $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $params->announcement_id]);
            } else {
                // insert the record if not already existing
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                $files->execute(["announcements", $params->announcement_id, json_encode($attachments), "{$params->announcement_id}", $params->userId, $attachments["raw_size_mb"]]);
            }

            // if the previous is not the same as the current
            if(isset($params->subject) && ($announcementData->subject !== $params->subject)) {
                // save the form change information
                $this->userLogs("announcements", $params->announcement_id, $announcementData->subject, "The subject was changed from <strong>{$announcementData->subject}</strong>", $params->userId);
            }

            // if the previous is not the same as the current
            if(isset($params->start_date) && ($announcementData->start_date !== $params->start_date)) {
                // save the form change information
                $this->userLogs("announcements", $params->announcement_id, $announcementData->start_date, "The start date  was changed from <strong>{$announcementData->start_date}</strong> to <strong>{$params->start_date}</strong>", $params->userId);
            }

            // if the previous is not the same as the current
            if(isset($params->end_date) && ($announcementData->end_date !== $params->end_date)) {
                // save the form change information
                $this->userLogs("announcements", $params->announcement_id, $announcementData->end_date, "The end date for the announcement was changed from <strong>{$announcementData->end_date}</strong> to <strong>{$params->end_date}</strong>", $params->userId);
            }

            // if the previous is not the same as the current
            if(isset($params->priority) && ($announcementData->priority !== $params->priority)) {
                // save the form change information
                $this->userLogs("announcements", $params->announcement_id, $announcementData->priority, "The priority was changed from <strong>{$announcementData->priority}</strong> to <strong>{$params->priority}</strong>", $params->userId);
            }          

            // if the previous is not the same as the current
            if(isset($params->recipient_group) && ($announcementData->recipient_group !== $params->recipient_group)) {
                // save the form change information
                $this->userLogs("announcements", $params->announcement_id, $announcementData->recipient_group, "The recipient group was changed to <strong>{$params->recipient_group}</strong>", $params->userId);
            }

            // log the user activity
            // $this->userLogs("announcements", $params->announcement_id, json_encode($announcementData), "<strong>{$params->userData->name}</strong> updated the announcement information.", $params->userId);
            
            // commit the transaction
            $this->db->commit();

            // return success
            return [
                "code" => 200,
                "data" => "Announcement has succesfully been updated."
            ];

        } catch(PDOException $e) {
            $this->db->rollBack();
            return $this->unexpected_error;
        }
    }

    /**
     * Share a new announcement
     * Set the recipient group and confirm that a valid group was parsed. Format the Modal content to save in the database
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function post(stdClass $params) {
        
        // init
        $group = array_keys($this->is_relatedto);

        // confirm that the recipient group is in the array list
        if(!in_array($params->recipient_group, $group)) {
            return "An invalid recipient group value was parsed. Accepted: ".implode(", ", $group);
        }

        // clean the message
        $params->_item_id = random_string("alnum", 32);
        $params->message = custom_clean(htmlspecialchars_decode($params->message));

        // form the post modal information
        $modal = "
        <div class=\"modal announcementModal_{$params->_item_id} fade\" data-backdrop=\"static\" data-keyboard=\"false\" id=\"announcementModal_{$params->_item_id}\" data-announcement-id=\"{$params->_item_id}\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">
            <div class=\"modal-dialog\" role=\"document\" style=\"min-width:300px; bottom: 0px\">
                <div class=\"modal-content\">
                    <div class=\"modal-header\">
                        <h4><small class=\"tx-11\"><i class=\"fas fa-info\"></i></small> {$params->subject}</h4>
                    </div>
                    <div class=\"modal-body\">
                        <div class=\"notice\">
                            {$params->message}
                        </div>
                    </div>
                    <div class=\"modal-footer text-right\">
                        <button data-announcement-id=\"{$params->_item_id}\" type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                    </div>
                </div>
            </div>
        </div>";
        
        // append the attachments
        $filesObj = load_class("files", "controllers");
        $attachments = $filesObj->prep_attachments("announcements", $params->userData->user_id, $params->_item_id);

        // announcement start and end dates
        $start_date = date("Y-m-d H:i", strtotime($params->start_date));
        $end_date = isset($params->end_date) ? date("Y-m-d 23:59", strtotime($params->end_date)) : date("Y-m-d 23:59", strtotime("next week"));
        try {

            $this->db->beginTransaction();
            
            // prepare and execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO announcements SET item_id =?, user_id = ?, recipient_group = ?, modal_function = ?, subject = ?, 
                message = ?, content = ?, start_date = ?, end_date = ?, user_type = ?, company_id = ?, seen_by = '[\"NULL\"]'
            ");
            $stmt->execute([
                $params->_item_id, $params->userId, $params->recipient_group, "ajaxNotice_{$params->_item_id}", $params->subject, 
                $params->message, $modal, $start_date, $end_date, $params->userData->user_type, $params->userData->company_id
            ]);

            // only insert attachment record if there was an attachment to the comment
            if(!empty($attachments["files"])) {
                // insert attachment
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                $files->execute(["announcements", $params->_item_id, json_encode($attachments), "{$params->_item_id}", $params->userId, $attachments["raw_size_mb"]]);
            }

            // notify all persons within the selected group
            $query = "SELECT item_id AS user_id, name FROM users WHERE (".(($params->recipient_group == "client") ? "user_type=\"user\" OR user_type=\"business\"" : "user_type=\"{$params->recipient_group}\"").") AND deleted=\"0\" AND status=\"1\"";
            $this->db->query("INSERT INTO cron_scheduler SET notice_code='12', active_date='{$start_date}', item_id='{$params->_item_id}', query='{$query}', user_id='{$params->userId}', cron_type='notification', subject = 'Announcement'");

            // log the user activity
            $this->userLogs("announcements", $params->_item_id, null, "<strong>{$params->userData->name}</strong> shared an announcement.", $params->userId);

            // query the 
            $param = (object) [
                "limit" => 1,
                "minimal_load" => true,
                "userId" => $params->userId,
                "userData" => $params->userData,
                "announcement_id" => $params->_item_id
            ];

            $this->db->commit();

            // return success
            return [
                "code" => 200,
                "data" => "Announcement has succesfully been shared.",
                "additional" => [
                    "clear" => true,
                    "append" => [
                        "div_id" => "announcements_list",
                        "data" => $this->list($param)[0]
                    ]
                ]
            ];

        } catch(PDOException $e) {
            $this->db->rollBack();  
            return $this->unexpected_error;
        }

    }

    /**
     * Show the notification to display
     * One at a time with its function to manage the notification
     */
    public function notice(stdClass $userData) {
        // set the user_type
        $user_type = in_array($userData->user_type, ["user", "business"]) ? "client" : $userData->user_type;

        // query the parameter
        $param = (object) [
            "limit" => 1,
            "minimal_load" => true,
            "where_clause" => "AND CURRENT_TIMESTAMP() >= TIMESTAMP(a.start_date) AND (a.recipient_group='{$user_type}' OR a.recipient_group='all') AND a.seen_by NOT LIKE '%{$userData->user_id}%'",
            "userData" => $userData,
            "userId" => $userData->user_id,
        ];

        // get the new information
        $data = $this->list($param);
        if(empty($data)) {
            return [];
        }
        // get the first item in the array tree
        $data = $data[0];

        // append the function
        $data->modal_function_script = $this->announcement_function($data->modal_function, $data->item_id, $userData->user_id);

        return $data;

    }

    /**
     * Generate the modal function and send to the user
     * 
     * @param String $function_name
     * 
     * @return String
     */
    public function announcement_function($function_name, $item_id, $user_id) {
        
        $html_content = "\n";
        $html_content .= "\t\tvar {$function_name} = () => {\n";
        $html_content .= "\t\t\t$(`div[class~=\"announcementModal_{$item_id}\"] button[data-dismiss=\"modal\"]`).on(\"click\", function() {\n";
        $html_content .= "\t\t\t\t\tlet announcement_id = $(this).data(\"announcement-id\");\n";
        $html_content .= "\t\t\t\t\t$.post(`\${baseUrl}api/announcements/viewed`, {announcement_id: announcement_id}).then((response) => {\n";
        $html_content .= "\t\t\t\t\t\tif(response.code == 200) {\n";
        $html_content .= "\t\t\t\t\t\t\t$(`div[class~=\"announcementModal_{$item_id}\"]`).modal(\"hide\");\n";
        $html_content .= "\t\t\t\t\t\t\tsetTimeout(() => {\$(`div[class~=\"announcementModal_{$item_id}\"]`).remove(); }, 1000)\n";
        $html_content .= "\t\t\t\t\t\t}\n";
        $html_content .= "\t\t\t\t\t});\n";
        $html_content .= "\t\t\t});\n";
        $html_content .= "\t\t}\n";

        return $html_content;
    }

    /**
     * Log the viewed announcement by a user
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    public function viewed(stdClass $params) {

        /** Confirm that this claims does not already exist */
        $announcementCheck = $this->pushQuery("a.id, a.seen_by", 
            "announcements a", "a.item_id='{$params->announcement_id}' AND a.status ='1' LIMIT 1");

        // if the claims check is not empty
        if(empty($announcementCheck)) {
            return ["data" => "Sorry! The record was not found"];
        }

        // policy data
        $data = $announcementCheck[0];
        $seen_by = json_decode($data->seen_by, true);

        // if not in array
        if(!in_array($params->userId, $seen_by)) {
            // push into the array list
            array_push($seen_by, $params->userId);

            // update the database record
            $this->db->query("UPDATE announcements SET seen_by='".json_encode($seen_by)."' WHERE item_id='{$params->announcement_id}' LIMIT 1");
            
            // return success
            return "Viewed!";
        } else {
            return "Already viewed!";
        }

    }

}

?>