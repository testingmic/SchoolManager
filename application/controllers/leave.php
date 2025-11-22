<?php 

class Leave extends Myschoolgh {

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Load the Leave Applications List
	 * 
	 * @param Object $params
	 * 
	 * @return Array
	 */
	public function list(stdClass $params) {

        global $isAdmin, $defaultUser;

		$params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        if(!$isAdmin) {
            $params->user_id = $defaultUser->user_id;
        }

        $params->query .= !empty($params->q) ? " AND a.name='{$params->q}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->application_id) ? " AND a.item_id='{$params->application_id}'" : null;
        $params->query .= !empty($params->user_id) ? " AND a.user_id LIKE '%{$params->user_id}%'" : null;
        $params->query .= !empty($params->type_id) ? " AND a.type_id='{$params->type_id}'" : null;

        try {

        	// if is the request is to load the replies
            $loadReplies = (bool) isset($params->load_replies);

            $stmt = $this->db->prepare("
                SELECT 
                	a.*, u.name, u.email, u.phone_number, l.name AS type_name
                FROM leave_requests a
                    LEFT JOIN users u ON u.item_id = a.user_id
                    LEFT JOIN leave_types l ON l.id = a.type_id
                WHERE {$params->query}
                ORDER BY DATE(a.leave_from) LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // if the user also requested to load the courses
                if($loadReplies) {
                    $result->replies_list = $this->pushQuery(
                    	"item_id, message, user_id, date_created", 
                    	"users_feedback", 
                    	"resource_id='{$result->item_id}' AND deleted='0' ORDER BY id DESC LIMIT 500"
                    );
                }

                // clean the leave reason
                $result->reason = htmlspecialchars_decode($result->reason);

                if(!empty($params->mobileapp)) {
                    $result->reason = strip_tags($result->reason);
                }

                $result->leave_type = $result->type_name;

                // set the leave date start and end
                $result->leave_date_start = $result->leave_from;
                $result->leave_date_end = $result->leave_to;

                // set the leave from and to
                $result->leave_from_to = "{$result->leave_from}:{$result->leave_to}";

				$data[] = $result;
                
            }

			return [ "code" => 200, "data" => $data ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

	}

    /**
     * View a leave application
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function view(stdClass $params) {
        try {

            global $isAdmin, $defaultUser;

            // if the user is not admin, then set the user id to the default user id
            if(!$isAdmin) {
                $params->user_id = $defaultUser->user_id;
            }

            $leaveRecord = $this->list($params);
            if(empty($leaveRecord["data"])) {
                return ["code" => 400, "data" => "Sorry! An invalid leave id was parsed."];
            }
            $leaveRecord = $leaveRecord["data"][0];
            return [ "code" => 200, "data" => $leaveRecord ];
        } catch(PDOException $e) {
            return $this->unexpected_error;
        }
    }

    /**
     * Create a leave application
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function create(stdClass $params) {
        
        global $isAdmin, $defaultUser;

        // if the user is not admin, then set the user id to the default user id
        if(!$isAdmin) {
            $params->user_id = $defaultUser->user_id;
        }

        // if the user id is not set, then set it to the staff id
        if(empty($params->user_id) && !empty($params->staff_id)) {
            $params->user_id = $params->staff_id;
        }

        // if the leave from to is not set, then set it to the leave date start and end
        if(empty($params->leave_from_to) && !empty($params->leave_date_start) && !empty($params->leave_date_end)) {
            $params->leave_from_to = "{$params->leave_date_start}:{$params->leave_date_end}";
        }

        return $this->apply($params);

    }

    /**
     * Process a leave application
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function apply(stdClass $params) {

        // get some global variables
        global $isAdmin, $defaultUser;

        // leave date format
        $split = explode(":", $params->leave_from_to);
        
        // get the from and to dates
        $leave_from = $split[0];
        $leave_to = $split[1] ?? null;

        // from must not be greater than to
        if(!empty($leave_to) && strtotime($leave_from) > strtotime($leave_to)) {
            return "The starting date must not be greater than the ending date.";
        }

        // count the number of days
        $days_count = $this->listDays($leave_from, $leave_to);

        // return error if the leave days is more than 60 days
        if(count($days_count) > 60) {
            return ["code" => 400, "data" => "Sorry! The leave days must not exceed 60 days."];
        }

        // error message
        if(!$isAdmin && $defaultUser->user_id !== $params->user_id) {
            return ["code" => 400, "data" => "Sorry! An invalid user id was parsed"];
        }

        // get the leave type
        $getType = !empty($params->type_id) ? $this->pushQuery("*", "leave_types", "status='1' AND id='{$params->type_id}' LIMIT 20") : null;
        if(empty($getType)) {
            $getTypeByName = $this->pushQuery("*", "leave_types", "status='1' AND name='{$params->leave_type}' LIMIT 20");
            if(!empty($getTypeByName)) {
                $getType = $getTypeByName[0];
                $params->type_id = $getType->id;
            }
        }

        // confirm that a valid leave type was parsed
        if(empty($getType)) {
            return ["code" => 400, "data" => "Sorry! An invalid leave type was parsed."];   
        }

        // check if the user has a pending leave application
        if(!empty($this->pushQuery("id", "leave_requests", 
            "client_id = '{$params->clientId}' AND status='Pending' AND user_id='{$params->user_id}' AND DATE(leave_from) BETWEEN '{$leave_from}' AND '{$leave_to}' LIMIT 1"))) {
            return ["code" => 400, "data" => "Sorry! There is a pending leave application from this staff."];   
        }

        // proceed
        try {

            // if the request method is POST
            if($params->requestMethod === 'POST') {

                // set the item id
                $item_id = random_string("alnum", RANDOM_STRING);

                // insert the request
                $stmt = $this->db->prepare("INSERT INTO leave_requests SET client_id = ?, item_id = ?, user_id = ?, type_id = ?, leave_from = ?, leave_to = ?, days = ?, reason = ?, status = ?");
                $stmt->execute([
                    $params->clientId, $item_id, $params->user_id, $params->type_id, 
                    $leave_from, $leave_to, count($days_count), $params->reason, 
                    $params->status ?? "Pending"
                ]);

            }

            // return the success message
            return [
                'code' => 200,
                'data' => 'Leave application was successfully processed.',
                'additional' => [
                    'href' => "{$this->baseUrl}leave/{$item_id}"
                ]
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Update the status of a leave application
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function status(stdClass $params) {

        try {

            global $isAdmin;
            if(!$isAdmin) {
                return ["code" => 403, "data" => "Sorry! You are not authorized to update the status of a leave application."];
            }

            // check if the user has a pending leave application
            if(empty($this->pushQuery("id", "leave_requests", 
                "client_id = '{$params->clientId}' AND item_id='{$params->leave_id}' LIMIT 1"))) {
                return ["code" => 400, "data" => "Sorry! An invalid leave id was parsed."];
            }

            // update the status
            $this->db->query("UPDATE leave_requests SET status='{$params->status}' WHERE item_id='{$params->leave_id}' LIMIT 1");

            // return the success message
            return [
                'code' => 200,
                'data' => 'Leave application status was successfully updated.',
                'additional' => [
                    'href' => "{$this->baseUrl}leave/{$params->leave_id}"
                ]
            ];

        } catch(PDOException $e) {}

    }


}