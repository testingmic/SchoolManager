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


        $params->query = "1";

        $params->limit = isset($params->limit) && isset($params->no_limit) ? 9999 : $this->global_limit;
        $params->incident_type = isset($params->incident_type) ? $params->incident_type : "incident";

        $params->query .= (isset($params->created_by)) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= (!empty($params->incident_type)) ? " AND a.incident_type='{$params->incident_type}'" : null;
        $params->query .= (isset($params->incident_date)) ? " AND a.incident_date='{$params->incident_date}'" : null;
        $params->query .= (isset($params->user_id)) ? " AND a.user_id='{$params->user_id}'" : null;
        $params->query .= (isset($params->client_id)) ? " AND a.client_id='{$params->client_id}'" : null;
        $params->query .= (isset($params->incident_id)) ? " AND (a.item_id='{$params->incident_id}' OR a.id='{$params->incident_id}')" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment,
                    (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image,'|',last_seen,'|',online,'|',user_type) FROM users WHERE users.item_id = a.assigned_to LIMIT 1) AS assigned_to_info,
                    (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image,'|',last_seen,'|',online,'|',user_type) FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_information,
                    (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image,'|',last_seen,'|',online,'|',user_type) FROM users WHERE users.item_id = a.user_id LIMIT 1) AS user_information
                FROM incidents a
                WHERE {$params->query} AND a.deleted = ? AND client_id = ? ORDER BY DATE(a.incident_date) LIMIT {$params->limit}
            ");
            $stmt->execute([0, $params->clientId]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["created_by_information", "user_information", "assigned_to_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->$each, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);    
                }

                // if attachment variable was parsed
                $result->attachment = json_decode($result->attachment);

                // clean the description attached to the list
                $result->description = htmlspecialchars_decode($result->description);
                $result->description = custom_clean($result->description);

                // if the files is set
                if(!isset($result->attachment->files)) {
                   $result->attachment = (object) [
                        "files" => [],
                        "files_count" => 0,
                        "files_size" => 0,
                        "raw_size_mb" => 0
                    ];
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
            print $e->getMessage();
        } 

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

            $item_id = random_string("alnum", 32);

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO incidents SET client_id = ?, created_by = ?, item_id = '{$item_id}'
                ".(isset($params->subject) ? ", subject = '{$params->subject}'" : null)."
                ".(isset($params->incident_date) ? ", incident_date = '{$params->incident_date}'" : null)."
                ".(isset($params->assigned_to) ? ", assigned_to = '{$params->assigned_to}'" : null)."
                ".(isset($params->reported_by) ? ", reported_by = '{$params->reported_by}'" : null)."
                ".(isset($params->location) ? ", location = '{$params->location}'" : null)."
                ".(isset($params->user_id) ? ", user_id = '{$params->user_id}'" : null)."
                ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId]);

            // append the attachments
            $incident_id = $this->lastRowId("incidents");
            $filesObj = load_class("files", "controllers");

            // attachments
            $attachments = $filesObj->prep_attachments("incidents", $params->userId, $item_id);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
            $files->execute(["incidents", $params->incident_id ?? $item_id, json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"]]);
            
            // log the user activity
            $this->userLogs("incidents", $item_id, null, "{$params->userData->name} created a new Incident: {$params->subject}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Incident successfully logged.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}update-student/{$params->user_id}/incidents"];

			// return the output
            return $return;

        } catch(PDOException $e) {} 

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

            // old record
            $prevData = $this->pushQuery(
                "a.*, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment",
                "incidents a", 
                "a.item_id = '{$params->incident_id}' AND a.user_id = '{$params->user_id}' AND a.client_id = '{$params->clientId}' AND a.deleted = '0' LIMIT 1"
            );

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
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
            $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? LIMIT 1");
            $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $prevData[0]->item_id]);
            
            // log the user activity
            $this->userLogs("incidents", $params->incident_id, null, "{$params->userData->name} updated the incident record.", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Incident successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}update-student/{$params->user_id}/incidents"];

			// return the output
            return $return;

        } catch(PDOException $e) {} 

    }

    
}
?>