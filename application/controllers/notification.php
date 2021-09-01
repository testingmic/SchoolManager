<?php 
// ensure this file is being included by a parent file
if( !defined( 'BASEPATH' ) ) die( 'Restricted access' );

class Notification extends Myschoolgh {

    public function __construct() {
        parent::__construct();
    }
	
    /**
     * Replace all placeholders in the message content
     * 
     * @param String $message
     * @param String $page 
     * 
     * @return String 
     */
    public function replace_placeholder($message, $page) {

        $content = str_ireplace(["{{APPURL}}", "{{RESOURCE_PAGE}}"], [$this->baseUrl, $page], $message);

        return $content;
    }

	/**
	 * Global function to search for item based on the predefined columns and values parsed
	 * 
	 * @param \stdClass $params
	 * @param String $params->user_id
	 * @param String $params->notice_id  
	 * @param String $params->company_id
	 * @param String $params->date
	 * 
	 * @return Object
	 */
	public function list(stdClass $params = null) {

		$params->query = "1";

        // if the user id parameter was not parsed
		if(!isset($params->user_id)) {
			// perform some checks
			$params->user_id = !empty($params->user_id) ? $params->user_id : $params->userId;
		}

		// if the field is null
		$params->query .= (isset($params->notice_id) && !empty($params->notice_id)) ? (preg_match("/^[0-9]+$/", $params->notice_id) ? " AND a.id='{$params->notice_id}'" : " AND a.item_id='{$params->notice_id}'") : null;
		$params->query .= (isset($params->status) && !empty($params->status)) ? " AND a.seen_status='{$params->status}'" : null;
        $params->query .= (isset($params->user_id) && !empty($params->user_id)) ? " AND a.user_id='{$params->user_id}'" : null;
        $params->query .= (isset($params->initiated_by) && !empty($params->initiated_by)) ? " AND a.initiated_by='{$params->initiated_by}'" : null;
		$params->query .= (isset($params->date) && !empty($params->date)) ? " AND DATE(a.date_created) ='{$params->date_created}'" : null;
        $params->query .= (isset($params->date_range) && !empty($params->date_range)) ? $this->dateRange($params->date_range, "a") : null;

		// the number of rows to limit the query
		$params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        try {
            // make the request for the record from the model
            $stmt = $this->db->prepare("
                SELECT 
                    a.*, u.name AS user_fullname, t.favicon, t.favicon_color,
                    (SELECT CONCAT(item_id,'|',name,'|',phone_number,'|',email,'|',image) FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM users_notification a
                LEFT JOIN users u ON u.item_id = a.user_id
                LEFT JOIN users_notification_types t  ON t.id = a.notice_type
                WHERE {$params->query} ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute();

            $row = 0;
            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {                
                // replace all placeholders
                $result->message = $this->replace_placeholder($result->message, $result->resource_page);

                // convert the created by string into an object
                $result->created_by_info = (object) $this->stringToArray($result->created_by_info, "|", ["user_id", "name", "phone_number", "email", "image"]);

                // append more
                $result->status = $this->the_status_label($result->seen_status);
                $result->time_to_ago = time_diff($result->date_created);

                // append to the list and return
                $data[] = $result;
            }

            // return the data
            return [
                "data" => $data,
                "code" => !empty($data) ? 200 : 201
            ];
        } catch(PDOException $e) {
            return $e->getMessage();
        }

	}

    /**
     * Add a new notification
     * 
     * @param \stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        // predefine some variables
        $params->_item_id = isset($params->_item_id) ? $params->_item_id : random_string("alnum", "32");
        $params->notice_type = isset($params->notice_type) ? $params->notice_type : 3;
        $params->initiated_by = isset($params->initiated_by) ? $params->initiated_by : "user";
        
        try {
            // insert the record
            $stmt = $this->db->prepare("
                INSERT users_notification SET date_created=now()
                ".(isset($params->_item_id) ? ", item_id='{$params->_item_id}'" : null)."
                ".(isset($params->user_id) ? ", user_id='{$params->user_id}'" : null)."
                ".(isset($params->subject) ? ", subject='".addslashes($params->subject)."'" : null)."
                ".(isset($params->clientId) ? ", client_id='{$params->clientId}'" : null)."
                ".(isset($params->message) ? ", message='".addslashes($params->message)."'" : null)."
                ".(isset($params->initiated_by) ? ", initiated_by='{$params->initiated_by}'" : null)."
                ".(isset($params->notice_type) ? ", notice_type='{$params->notice_type}'" : null)."
                ".(isset($params->userId) ? ", created_by='{$params->userId}'" : null)."
            ");
            $stmt->execute();

            return [
                "code" => 200,
                "data" => "Notification was successfully record"
            ];

        } catch(PDOException $e) {
            return;
        }

    }

    /**
     * Mark Notification as Read
     * 
     * @param stdClass $params->notification_id
     * 
     * @return Array
     */
    public function mark_as_read(stdClass $params) {

        try {

            // mark all as read
            if($params->notification_id == "mark_all_as_read") {
                // prepare and execute the statement
                $stmt = $this->db->prepare("UPDATE users_notification SET seen_status = ?, seen_date = now() 
                    WHERE user_id = ? AND client_id = ? AND seen_status = ? LIMIT 100");
                $stmt->execute(["Seen", $params->userId, $params->clientId, "Unseen"]);
            } else {
                // prepare and execute the statement
                $stmt = $this->db->prepare("UPDATE users_notification SET seen_status = ?, seen_date = now() 
                    WHERE item_id = ? AND client_id = ? LIMIT 1");
                $stmt->execute(["Seen", $params->notification_id, $params->clientId]);
            }

            // return true for success
            return true;

        } catch(PDOException $e) {
            return [];
        }

    }

}
?>