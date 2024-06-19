<?php

class Frontoffice extends Myschoolgh {

	private $frontoffice_data;

	public function __construct() {
		parent::__construct();

		$this->frontoffice_data = [
			'visitor' => [
				'keys' => ['purpose', 'fullname', 'phone_number', 'email', 'number_of_person', 'date', 'time_in', 'time_out', 'note']
			],
			'admission_enquiry' => [
				'keys' => ['fullname', 'phone_number', 'email', 'address', 'date', 'source', 'followup', 'assigned', 'description']
			], 
			'postal_dispatch' => [
				'keys' => ['to', 'reference', 'from', 'date', 'note', 'address']
			],
			'postal_receive' => [
				'keys' => ['to', 'reference', 'from', 'date', 'note', 'address']
			],
			'phonecall' => [
				'keys' => ['type', 'fullname', 'phone_number', 'call_duration', 'followup', 'note', 'date']
			]
		];

	}
	
	/**
	 * Load the Leave Applications List
	 * 
	 * @param Object $params
	 * 
	 * @return Array
	 */
	public function list(stdClass $params) {

		$params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= !empty($params->source) ? " AND a.source='{$params->source}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->request_id) ? " AND a.item_id='{$params->request_id}'" : null;
        $params->query .= !empty($params->section) ? " AND a.section='{$params->section}'" : null;

        try {

        	// if is the request is to load the replies
            $loadReplies = (bool) isset($params->load_replies);

            $stmt = $this->db->prepare("
                SELECT 
                	a.*, u.name, u.email, u.phone_number, u.image,
                	(SELECT b.description FROM files_attachment b WHERE b.resource=a.section AND b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment
                FROM frontoffice a
                    LEFT JOIN users u ON u.item_id = a.created_by
                WHERE {$params->query}
                ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute();

            $filesObject = load_class("forms", "controllers");

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

                // decode the attachments as well
                $result->attachment = json_decode($result->attachment);
                $result->attachment_html = isset($result->attachment->files) ? $filesObject->list_attachments($result->attachment->files, $result->created_by, "col-lg-6 col-md-6", false, false) : "";

                // convert the content into an object
                $result->content = json_decode($result->content);

				$data[] = $result;
                
            }

			return [ "code" => 200, "data" => $data ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

	}

	/**
	 * Process the user request.
	 * 
	 * Validate the information and insert the data into the database
	 * 
	 * @param Array 	$params->data
	 * @param Strign 	$params->section
	 * 
	 * @return Array
	 */
	public function log(stdClass $params) {

		// confirm if the data parsed is a valid array
		if(!is_array($params->data)) {
			return ['code' => 203, 'data' => 'Sorry! The data parameter must be a valid array.'];
		}

		// validate the section
		if(!in_array($params->section, array_keys($this->frontoffice_data))) {
			return ['code' => 203, 'data' => 'Sorry! An invalid section was parsed.'];
		}

		// get the 
		foreach(array_keys($params->data) as $key) {
			// confirm that all keys parsed are required
			if(!in_array($key, $this->frontoffice_data[$params->section]['keys'])) {
				return ['code' => 203, 'data' => 'Sorry! An invalid param was submitted.'];
			}

			// ensure the value is not empty
			if(in_array($key, ['purpose', 'name', 'phone_number', 'from', 'top'])) {
				if(empty($params->data[$key])) {
					return ['code' => 203, 'data' => 'Sorry! Ensure all required fields have been completed.'];
				}
			}

			// confirm if a valid email was parsed
			if(in_array($key, ['email']) && !empty($params->data['email'])) {
				if(!filter_var($params->data['email'], FILTER_VALIDATE_EMAIL)) {
					return ['code' => 203, 'data' => 'Sorry! Ensure all a valid email address was entered.'];		
				}
			}

		}

		// insert the record into the database
		try {

			// set the item id
            $item_id = random_string("alnum", RANDOM_STRING);

            // insert the request
            $stmt = $this->db->prepare("INSERT INTO frontoffice SET client_id = ?, item_id = ?, created_by = ?, section = ?, source = ?, content = ?");

            // execute the statement
            $stmt->execute([
                $params->clientId, $item_id, $params->userId, $params->section, 
                $params->data['source'] ?? null, json_encode($params->data)
            ]);

            // set the section
            $section = $params->section;

            // upload the files if neccessary
            if(!empty($this->session->{$section})) {
                
                // create a new object and prepare/move attachments
                $attachments = load_class("files", "controllers")->prep_attachments($section, $params->userId, $item_id);
                
                // insert the record if not already existing
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
                $files->execute([$section, $item_id, json_encode($attachments), $item_id, $params->userId, $attachments["raw_size_mb"]]);

            }

            // return the success message
            return [
                'code' => 200,
                'data' => 'Request was successfully processed.',
                'additional' => [
                    'href' => $this->session->user_current_url
                ]
            ];


		} catch(PDOException $e) {}

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

            // check if the user has a pending leave application
            if(empty($this->pushQuery("id", "frontoffice", 
                "client_id = '{$params->clientId}' AND item_id='{$params->request_id}' LIMIT 1"))) {
                return ["code" => 203, "data" => "Sorry! An invalid leave id was parsed."];
            }

            // update the status
            $this->db->query("UPDATE frontoffice SET state='{$params->status}' WHERE item_id='{$params->request_id}' LIMIT 1");

            // return the success message
            return [
                'code' => 200,
                'data' => 'Request status was successfully updated.'
            ];

        } catch(PDOException $e) {}

    }

}
?>