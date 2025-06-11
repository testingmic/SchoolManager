<?php 

class Incidents extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Update existing incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {


        $filter = "1";

        $params->limit = isset($params->limit) && isset($params->no_limit) ? 1000 : $this->global_limit;
        $params->incident_type = isset($params->incident_type) ? $params->incident_type : "incident";

        if(isset($params->incident_id)) {
            $column = preg_match("/^[0-9]+$/", $params->incident_id) ? "id" : "item_id";
        }

        $filter .= !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
        $filter .= !empty($params->incident_type) ? " AND a.incident_type='{$params->incident_type}'" : null;
        $filter .= !empty($params->incident_date) ? " AND a.incident_date='{$params->incident_date}'" : null;
        $filter .= !empty($params->user_id) ? " AND a.user_id='{$params->user_id}'" : null;
        $filter .= !empty($params->user_role) ? " AND a.user_role='{$params->user_role}'" : null;
        $filter .= !empty($params->client_id) ? " AND a.client_id='{$params->client_id}'" : null;
        $filter .= !empty($params->subject) ? " AND a.subject LIKE '%{$params->subject}%'" : null;
        $filter .= !empty($params->incident_id) ? " AND a.{$column}='{$params->incident_id}'" : null;
        $filter .= !empty($params->followup_id) ? " AND a.incident_id='{$params->followup_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT c.description FROM files_attachment c WHERE c.record_id = a.item_id ORDER BY c.id DESC LIMIT 1) AS attachment,
                    (SELECT CONCAT(b.item_id,'|',b.unique_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.assigned_to LIMIT 1) AS assigned_to_info,
                    (SELECT CONCAT(b.item_id,'|',b.unique_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_information,
                    (SELECT CONCAT(b.item_id,'|',b.unique_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.user_id LIMIT 1) AS user_information
                FROM incidents a
                WHERE {$filter} AND a.deleted = ? AND a.client_id = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([0, $params->clientId]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["created_by_information", "user_information", "assigned_to_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->$each, "|", ["user_id", "unique_id", "name", "phone_number", "email", "image","user_type"]);
                }

                // if attachment variable was parsed
                $result->attachment = !empty($result->attachment) ? json_decode($result->attachment) : null;

                // clean the description attached to the list
                $result->description = htmlspecialchars_decode($result->description);
                $result->description = custom_clean($result->description);
                $result->time_ago = time_diff($result->date_created); 

                // if the files is set
                if(!isset($result->attachment->files)) {
                   $result->attachment = $this->fake_files;
                }

                // if the full_details parameter was parsed
                if(isset($params->full_details)) {
                    // load the incident followups
                    if($result->incident_type == "incident") {
                        // empty followups
                        $result->followups = [];
                        
                        // get the list
                        $the_param = (object) [
                            "clientId" => $params->clientId,
                            "followup_id" => $result->item_id,
                            "incident_type" => "followup"
                        ];
                        // append the followups
                        $result->followups = $this->list($the_param)["data"];
                    }
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
     * View incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function view(stdClass $params) {
        
        if(empty($params->incident_id)) {
            return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
        }

        $params->full_details = true;
        $record = $this->list($params);

        return $record["data"][0] ?? [];
    }

    /**
     * Update existing incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        try {

            // confirm that the request method is POST
            if($params->requestMethod !== 'POST') {
                return ["code" => 400, "data" => "Sorry! An invalid request method was supplied."];
            }

            // generate a unique id
            $item_id = random_string("alnum", RANDOM_STRING);

            // get the user information
            if(isset($params->user_id)) {
                $user = $this->pushQuery("user_type", "users", "item_id='{$params->user_id}' AND client_id='{$params->clientId}' LIMIT 1");
                if(empty($user)) {
                    return ["code" => 400, "data" => "Sorry! An invalid user id was supplied"];
                }
                $user_role = $user[0]->user_type;
            }

            foreach(["subject", "incident_date", "user_id", "description"] as $each) {
                if(empty($params->{$each})) {
                    return ["code" => 400, "data" => "Sorry! The {$each} parameter is required."];
                }
            }

            // set the reported by to be the user id if not set
            $params->reported_by = empty($params->reported_by) ? $params->userId : $params->reported_by;
            $params->assigned_to = empty($params->assigned_to) ? $params->userId : $params->assigned_to;

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO incidents SET client_id = ?, created_by = ?, item_id = '{$item_id}'
                ".(!empty($params->subject) ? ", subject = '{$params->subject}'" : null)."
                ".(!empty($params->incident_date) ? ", incident_date = '{$params->incident_date}'" : null)."
                ".(!empty($params->assigned_to) ? ", assigned_to = '{$params->assigned_to}'" : null)."
                ".(!empty($params->reported_by) ? ", reported_by = '{$params->reported_by}'" : null)."
                ".(!empty($params->location) ? ", location = '{$params->location}'" : null)."
                ".(!empty($params->user_id) ? ", user_id = '{$params->user_id}'" : null)."
                ".(!empty($user_role) ? ", user_role = '{$user_role}'" : null)."
                ".(!empty($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);

            // append the attachments
            $incident_id = $this->lastRowId("incidents");
            $filesObj = load_class("files", "controllers");

            // attachments
            $attachments = $filesObj->prep_attachments("incidents", $params->userId, $item_id);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
            $files->execute(["incidents", $params->incident_id ?? $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"], $params->clientId]);
            
            // log the user activity
            $this->userLogs("incidents", $item_id, null, "{$params->userData->name} created a new Incident: {$params->subject}", $params->userId);

            // set the url link
            $url_link = $user_role == "student" ? "student" : "staff";

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Incident successfully logged.", "refresh" => 2000];
			
            if(!empty($params->remote)) {
                $params->incident_id = $item_id;
                $return["record"] = $this->view($params);
            }

			# append to the response
			$return["additional"] = [
                "clear" => true, 
                "href" => "{$this->baseUrl}{$url_link}/{$params->user_id}/incidents"
            ];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Update existing incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        try {

            if(empty($params->incident_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // old record
            $prevData = $this->pushQuery(
                "a.*, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment",
                "incidents a", 
                "a.item_id = '{$params->incident_id}' ".(!empty($params->user_id) ? "AND a.user_id = '{$params->user_id}'" : null)." AND a.client_id = '{$params->clientId}' AND a.deleted = '0' LIMIT 1"
            );

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // initialize
            $initial_attachment = [];

            /** Confirm that there is an attached document */
            if(!empty($prevData[0]->attachment)) {
                // decode the json string
                $db_attachments = json_decode($prevData[0]->attachment);
                // get the files
                if(isset($db_attachments->files)) {
                    $initial_attachment = $db_attachments->files;
                }
            }

            // append the attachments
            $filesObj = load_class("files", "controllers");
            $module = "incidents";
            $attachments = $filesObj->prep_attachments($module, $params->userId, $prevData[0]->item_id, $initial_attachment);

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE incidents SET date_updated = now()
                    ".(isset($params->subject) ? ", subject = '{$params->subject}'" : null)."
                    ".(isset($params->incident_date) ? ", incident_date = '{$params->incident_date}'" : null)."
                    ".(isset($params->assigned_to) ? ", assigned_to = '{$params->assigned_to}'" : null)."
                    ".(isset($params->reported_by) ? ", reported_by = '{$params->reported_by}'" : null)."
                    ".(isset($params->location) ? ", location = '{$params->location}'" : null)."
                    ".(isset($params->status) ? ", status = '{$params->status}'" : null)."
                    ".(isset($params->user_id) ? ", user_id = '{$params->user_id}'" : null)."
                    ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                WHERE client_id = ? AND item_id = ? LIMIT 1
            ");
            $stmt->execute([$params->clientId, $params->incident_id]);

            // append the attachments
            $filesObj = load_class("files", "controllers");

            // insert the record if not already existing
            $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? AND client_id = ? LIMIT 1");
            $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $prevData[0]->item_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("incidents", $params->incident_id, null, "{$params->userData->name} updated the incident record.", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Incident successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true, "href" => $this->session->user_current_url];

            if(!empty($params->remote)) {
                $return["record"] = $this->view($params);
            }

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Update existing incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add_followup(stdClass $params) {

        // old record
        $prevData = $this->pushQuery(
            "a.*",
            "incidents a", 
            "a.item_id = '{$params->incident_id}' ".(!empty($params->user_id) ? "AND a.user_id = '{$params->user_id}'" : null)." AND a.client_id = '{$params->clientId}' AND a.deleted = '0' LIMIT 1"
        );

        // if empty then return
        if(empty($prevData)) {
            return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
        }

        try {

            // generate a unique id
            $item_id = random_string("alnum", RANDOM_STRING);
            
            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO incidents SET client_id = ?, created_by = ?, incident_type = ?, item_id = ?
                ".(!empty($params->incident_id) ? ", incident_id = '{$params->incident_id}'" : null)."
                ".(!empty($params->status) ? ", status = '{$params->status}'" : null)."
                ".(!empty($params->user_id) ? ", user_id = '{$params->user_id}'" : null)."
                ".(!empty($params->comment) ? ", description = '".addslashes(nl2br($params->comment))."'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId, "followup", $item_id]);

            // log the user activity
            $this->userLogs("incidents", $params->incident_id, null, "{$params->userData->name} added a new comment to the Incident.", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Incident followup comment added successfully."];
            $q_param = (object) ["incident_type" => "followup", "incident_id" => $item_id, "limit" => 1, "clientId" => $params->clientId];
			$return["additional"] = ["data" => $this->list($q_param)["data"][0]];

            if(!empty($params->remote)) {
                $return["record"] = $this->view($params);
            }

            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 
        
    }
    
    /**
     * Update existing incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function report_list(stdClass $params) {

        // global 
        global $defaultUser;

        // filters
        $filter = "1";

        $params->limit = isset($params->limit) && isset($params->no_limit) ? 1000 : $this->global_limit;

        // if the report id was parsed
        if(isset($params->report_id)) {
            $column = preg_match("/^[0-9]+$/", $params->report_id) ? "id" : "item_id";
        }

        // set the created by to the teacher
        if(in_array($defaultUser->user_type, ["teacher", "accountant", "employee"])) {
            $params->created_by = $defaultUser->user_id;
        }

        // set the student id if the user is a student
        if(in_array($defaultUser->user_type, ["student"])) {
            $params->student_id = $defaultUser->user_id;
        }

        // if the user is a parent
        elseif(in_array($defaultUser->user_type, ["parent"])) {
            $params->student_id = $defaultUser->wards_list_ids;
        }

        // set some filters
        $filter .= !empty($params->report_type) ? " AND a.report_type='{$params->report_type}'" : null;
        $filter .= !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
        $filter .= !empty($params->student_id) ? " AND a.student_id IN {$this->inList($params->student_id)}" : null;
        $filter .= !empty($params->client_id) ? " AND a.client_id='{$params->client_id}'" : null;
        $filter .= !empty($params->report_id) ? " AND a.{$column}='{$params->report_id}'" : null;
        $filter .= !empty($params->class_id) ? " AND a.class_id='{$params->class_id}'" : null;
        $filter .= !empty($params->seen_by) ? " AND a.seen_by='{$params->seen_by}'" : null;
        $filter .= !empty($params->date_range) ? $this->dateRange($params->date_range) : null;
        $filter .= !empty($params->followup_id) ? " AND a.report_id='{$params->followup_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*, c.name AS class_name,
                    (SELECT CONCAT(b.item_id,'|',b.unique_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.seen_by LIMIT 1) AS seen_by_info,
                    (SELECT CONCAT(b.item_id,'|',b.unique_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_information,
                    u.name AS student_name, u.unique_id AS student_unique_id, u.image AS student_image
                FROM daily_reports a
                LEFT JOIN users u ON u.item_id = a.student_id
                LEFT JOIN classes c ON c.item_id = a.class_id
                WHERE {$filter} AND a.deleted = ? AND a.client_id = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([0, $params->clientId]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["created_by_information", "seen_by_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->$each, "|", ["user_id", "unique_id", "name", "phone_number", "email", "image","user_type"]);
                }

                // clean the description attached to the list
                $result->description = htmlspecialchars_decode($result->description);
                $result->time_ago = time_diff($result->date_created);

                // if the full_details parameter was parsed
                if(isset($params->full_details)) {
                    // load the report followups
                    if($result->incident_type == "report") {
                        // empty followups
                        $result->followups = [];
                        
                        // get the list
                        $the_param = (object) [
                            "clientId" => $params->clientId,
                            "followup_id" => $result->item_id,
                            "report_type" => "followup",
                            "limit" => 200
                        ];
                        // append the followups
                        $result->followups = $this->report_list($the_param)["data"];
                    }
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
     * Mark the report as seen by the parent
     * 
     * @param String $params->report_id
     * 
     * @return Array
     */
    public function report_is_seen(stdClass $params) {

        // confirm the user type
        if(!in_array($params->userData->user_type, ["parent", "admin"])) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        try {

            // get the record
            $record = $this->pushQuery("is_seen", "daily_reports", "item_id='{$params->report_id}' AND client_id='{$params->clientId}' LIMIT 1");

            // end query if no record was found
            if(empty($record)) {
                return ["code" => 400, "data" => "Sorry! An invalid report id was parsed."];
            }

            // confirm the seen status
            if($record[0]->is_seen) {
               return ["code" => 400, "data" => "Sorry! This record has already been marked as seen."];
            }

            // update the record
            $this->db->query("UPDATE daily_reports SET 
                    is_seen='1', seen_date = now(), is_deletable = '0', seen_by = '{$params->userId}' 
                WHERE item_id='{$params->report_id}' AND client_id='{$params->clientId}' LIMIT 1"
            );

            // return the success message
            return ["code" => 200, "data" => "Daily report was successfully marked as seen."];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Log a daily report
     * 
     * Use this model to either insert or update an existing report log.
     * 
     * @param String $params->class_id
     * @param String $params->student_id
     * @param String $params->report_id
     * @param String $params->description
     * 
     * @return Array
     */
    public function report(stdClass $params) {

        // global variables
        global $isTutorAdmin;

        // confirm the user type
        if(!in_array($params->userData->user_type, ["teacher", "admin"])) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        try {
            // set the current date
            $date = date("l, jS F Y");

            // get the class record
            $class = $this->pushQuery("name", "classes", "item_id='{$params->class_id}' AND client_id='{$params->clientId}' LIMIT 1");

            // confirm the class id
            if(empty($class)) {
                return ["code" => 400, "data" => "Sorry! An invalid class id was parsed."];
            }

            // confirm the student id
            if(!empty($params->student_id)) {
                // get the student name
                $student = $this->pushQuery("name, guardian_id", "users", "item_id='{$params->student_id}' AND user_type='student' AND client_id='{$params->clientId}' LIMIT 1");

                // if the student record is empty
                if(empty($student)) {
                    return ["code" => 400, "data" => "Sorry! An invalid student id was parsed."];
                }
            }

            // clean the description 
            $params->description = custom_clean(htmlspecialchars_decode($params->description));
            $params->description = htmlspecialchars($params->description);

            // confirm if a report id was parsed
            if(!empty($params->report_id)) {
                // get the record
                $record = $this->pushQuery("is_seen, description", "daily_reports", "item_id='{$params->report_id}' AND client_id='{$params->client_id} LIMIT 1");

                // end query if no record was found
                if(empty($record)) {
                    return ["code" => 400, "data" => "Sorry! An invalid report id was parsed."];
                }
                
                // confirm the seen status
                if($record[0]->is_seen) {
                   return ["code" => 400, "data" => "Sorry! This record has already been seen by a parent and cannot be changed."];
                }

                // update the record
                $this->db->query("UPDATE daily_reports SET description='{$params->description}' WHERE item_id='{$params->report_id}' AND client_id='{$params->clientId}' LIMIT 1");

                // log the user activity
                $this->userLogs("daily_report", $params->report_id, $record[0], "{$params->userData->name} updated the logged daily report of <strong>{$date}</strong> for <strong>{$student[0]->name}</strong> in <strong>{$class[0]->name}</strong>", $params->userId);

                // return the success message
                return [
                    "code" => 200, 
                    "data" => "Daily report was successfully updated.",
                    "additional" => [
                        "href" => "{$this->baseUrl}students_daily_reports"
                    ]
                ];
            }

            // set some variables
            $report_id = random_string("alnum", RANDOM_STRING);

            // insert the data
            $stmt = $this->db->prepare("INSERT INTO daily_reports SET 
                client_id = ?, class_id = ?, description = ?, created_by = ?, item_id = '{$report_id}'
                ".(!empty($params->student_id) ? ", student_id='{$params->student_id}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->class_id, $params->description, $params->userId]);

            // alert the guardian
            if(!empty($student) && !empty($student[0]->guardian_id)) {
                
                // convert the guardian_id into an array
                $guardian_ids = $this->stringToArray($student[0]->guardian_id);

                // message
                $message = "Your ward {$student[0]->name}\'s daily report for {$date} as been logged.";
                $content = "Your ward <strong>{$student[0]->name}</strong>\'s daily report for <strong>{$date}</strong> as successfully been logged. Kindly <a href=\"{{APPURL}}students_daily_reports/{$report_id}\">click here</a> to review the report.";

                // loop through the guardian information
                foreach($guardian_ids as $guardian_id) {
                    // log a notification
                    $_item_id = random_string("alnum", RANDOM_STRING);

                    // insert the record
                    $notif = $this->db->prepare("
                        INSERT users_notification SET date_created='{$this->current_timestamp}', 
                            item_id='{$_item_id}',user_id='{$guardian_id}',subject='Daily Report: {$date}',
                            client_id='{$params->clientId}', notice_type='13', message='{$message}',
                            content='{$content}', initiated_by='system', created_by='{$params->userId}'
                    ");
                    $notif->execute();
                }

            }

            // log the user activity
            $this->userLogs("daily_report", $report_id, null, "{$params->userData->name} logged the daily report of <strong>{$date}</strong> for <strong>{$student[0]->name}</strong> in <strong>{$class[0]->name}</strong>", $params->userId);

            // return the success message
            return [
                "code" => 200, 
                "data" => "Daily report was successfully inserted.",
                "additional" => [
                    "href" => "{$this->baseUrl}students_daily_reports"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Draw the Incident Infomation
     * 
     * @return Array
     */
    public function draw($params, $data) {

        // if the data record is empty
        if(empty($data)) {
            return "<div><h2>Sorry! No incidents record was found.</h2></div>";
        }

        // get the client logo content
        if(!empty($params->client->client_logo)) {
            $type = pathinfo($params->client->client_logo, PATHINFO_EXTENSION);
            $logo_data = file_get_contents($params->client->client_logo);
            $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
        }

        // initial variable
        $html_content = "<div style='max-width:95%;margin:auto auto; padding:20px;box-shadow:0px 1px 2px #000;background: #f4f4f4;'>";
        $html_content .= '
            <div align="center" style="margin:20px;">
                '.(isset($client_logo) ? "<img width=\"70px\" src=\"{$client_logo}\">" : "").'
                <h2 style="color:#6777ef;font-family:helvetica;padding:0px;margin:0px;">'.strtoupper($params->client->client_name).'</h2>
                <div>'.$params->client->client_address.'</div>
                '.(!empty($params->client->client_email) ? "<div>{$params->client->client_email}</div>" : "").'
            </div>';
        
        // forms object
        $formsObj = load_class("forms", "controllers");

        $start = 0;
        $count = count($data);

        // loop through the incidents record
        foreach($data as $incident) {

            // increment
            $start++;

            // wipe the message
            $message = isset($incident->description) ? $incident->description : null;

            // load the file attachments
            $attachments = "";
            
            // list the incident attachments
            if(!empty($incident->attachment)) {
                $attached = $incident->attachment;
                if(!empty($attached)) {
                    $attachments = $formsObj->list_attachments($attached->files, $params->userId, "width_250 float_left");
                }
            }

            // load the followup list
            $followups = $this->incident_log_followup_form($incident->item_id, $incident->user_id, $incident->followups);
            
            // get the user image
            $type = pathinfo($incident->user_information->image, PATHINFO_EXTENSION);
            $logo_data = file_get_contents($incident->user_information->image);
            $image = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);

            // append to the content
            $html_content .= "
            <div style='max-width:95%;margin:auto auto; padding:20px;box-shadow:0px 1px 2px #000;background: #fff;'>
            <table style=\"border: 1px solid #dee2e6;\" width=\"100%\" cellpadding=\"5px\">
            <tbody>
            <tr>
                <td colspan='2'>
                    <div style='border-bottom:solid #ccc 1px; padding-bottom:10px; margin-bottom:10px;'>
                        <h3 style=\"padding:0px;margin-top:0px;text-transform:uppercase;margin-bottom:5px;\">{$incident->subject}</h3>
                    </div>
                    <div class='col-md-12'><strong>Incident Date:</strong> {$incident->incident_date}</div>
                    <div class='col-md-12'><strong>Current State:</strong> {$this->the_status_label($incident->status)}</div>
                    <div class='col-md-12'><strong>Location:</strong> {$incident->location}</div>
                    <div class='col-md-12'><strong>Reported By:</strong> {$incident->reported_by}</div>

                </td>
            </tr>
            <tr>
                <td style=\"border: 1px solid #dee2e6;\" width=\"60px\">
                    <img width=\"60px\" style=\"border-radius:50%\" src=\"{$image}\" alt=\"\">
                </td>
                <td style=\"border: 1px solid #dee2e6;\" valign=\"top\">
                    <div style=\"text-transform:uppercase\">{$incident->user_information->name}</div>
                    <div><strong>{$incident->user_information->unique_id}</strong></div>
                    <div><em>{$incident->user_information->user_type}</em></div>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    ".(
                        !empty($incident->assigned_to_info->name) ? "
                        <div>
                            <h4 style=\"padding:0px;margin-top:0px;margin-bottom:5px;\">ASSIGNED TO:</h4>
                            <div><strong>Name:</strong> ".($incident->assigned_to_info->name ?? null)."</div>
                            <div><strong>Email:</strong> ".($incident->assigned_to_info->email ?? null)."</div>
                            ".(!empty($incident->assigned_to_info->contact) ? "<div><strong>Contact:</strong> ".($incident->assigned_to_info->contact ?? null)."</div>" : null)."
                        </div>" : ""
                    )."
                    <div style='border-top:solid #ccc 1px; padding-top:10px; margin-top:10px;'>{$message}</div>
                    ".(isset($attached) && !empty($attached->files) ? "
                        <div style='border-top:solid #ccc 1px; padding-top:10px; margin-top:10px;'><h4>ATTACHMENTS</h4></div>
                        <div></div>" : ""
                    )."                        
                </td>
            </tr>
            <tbody>
            </table>
            ".(!empty($followups) ? "
                <div style='border-top:solid #ccc 1px; padding-top:10px; margin-top:30px;'><h4 style=\"margin-top:0px;margin-bottom:10px;\">FOLLOW UPS</h4></div>
                <div>{$followups}</div>\n" : ""
            )."";
        
            $html_content .= "<div>";

        }
        $html_content .= "</div>";
        $html_content .= "
        <script>
        function print_receipt() {
            window.print();
            window.onfocus = (evt) => {window.close();}
            window.onafterprint = (evt) => { window.close(); }
        }
        print_receipt();
        </script>";

        return $html_content;
    }

    /**
     * Incident Followup Form
     * 
     * List the followup details before showing the textarea field to add more information to it
     * 
     * @param String $item_id
     * @param String $clientId
     * @param String $user_id 
     * 
     * @return String
     */
    public function incident_log_followup_form($item_id, $user_id = null, $followups = []) {
        
        /** Initializing */
        $prev_date = null;
        $html_content = "<table border='0' width=\"100%\" cellpadding=\"2px\">";
        $followups_list = "";

        /** Loop through the followups */
        foreach($followups as $followup) {

            /** Clean date */
            $clean_date = date("l, jS F Y", strtotime($followup->date_created));
            $raw_date = date("Y-m-d", strtotime($followup->date_created));

            /** If the previous date is not the same as the current date */
            if (!$prev_date || $prev_date !== $raw_date) {
                $followups_list .= "<tr><td colspan=\"2\" style='color:#179b17'>{$clean_date}</td></tr>\n";
            }
            $followups_list .= $this->followup_thread($followup);

            // prepare the previous date
            $prev_date = date("Y-m-d", strtotime($followup->date_created));
            $prev_date = $raw_date;

        }

        $html_content .= !empty($followups_list) ? $followups_list : "<tr><td>No followup message available.</td></tr>\n";
        $html_content .= "</table>";
        
        return $html_content;
    }


    /**
     * Followup Thread Messages
     * 
     * @param stdClass $data
     * 
     * @return String
     */
    public function followup_thread($data) {

        // get the user image
        $type = pathinfo($data->created_by_information->image, PATHINFO_EXTENSION);
        $logo_data = file_get_contents($data->created_by_information->image);
        $image = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);

        return "
        <tr>
            <td style=\"border: 1px solid #dee2e6;\" width=\"40px\">
                <img width=\"40px\" style=\"border-radius:50%\" src=\"{$image}\" alt=\"\">
            </td>
            <td style=\"border: 1px solid #dee2e6;\" valign=\"top\">
                <div class=\"cursor underline m-0\">{$data->created_by_information->name}</div>
                <div title=\"{$data->date_created}\">{$data->time_ago}</div>
            </td>
        </tr>
        <tr>
            <td colspan=\"2\" style=\"font-size:16px;padding:10px;\">{$data->description}</td>
        <tr>";
    }
    
}
?>