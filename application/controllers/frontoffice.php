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
                $result->attachment = !empty($result->attachment) ? json_decode($result->attachment) : null;
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
	 * @param object 	$params
	 * @param object 	$params->section
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

			$href = str_ireplace("/log", "", $this->session->user_current_url);
			$href = $href . "/{$item_id}";

            // return the success message
            return [
                'code' => 200,
                'data' => 'Request was successfully processed.',
                'additional' => [
                    'href' => $href
                ]
            ];


		} catch(PDOException $e) {}

	}

	/**
	 * Process the Admission Enquiry Request
	 * 
	 * @param stdClass $params
	 * 
	 * @return Array
	 */
	public function enquiry(stdClass $params) {
		return $this->admission($params);
	}

	/**
	 * Process the Admission Request
	 * 
	 * @param stdClass $params
	 * 
	 * @return Array
	 */
	public function admission(stdClass $params) {

		$section = "admission_enquiry";

		// confirm that a valid client id was parsed
		if(empty($params->clientId)) {
			return [
				'code' => 400,
				'data' => 'Sorry! An invalid record id or client id was parsed.'
			];
		}

		$clientInfo = $this->clients_list($params->clientId);
		if(empty($clientInfo)) {
			return [
				'code' => 400,
				'data' => 'Sorry! An invalid client id was parsed.'
			];
		}
		$clientInfo = $clientInfo[0];

		// set the item id
		$item_id = random_string("alnum", RANDOM_STRING);

		$classList = [
			'creche' => 'Creche',
		];
		for($i = 1; $i <= 6; $i++) {
			$classList['p'.$i] = "Class {$i}";
			$classList['class'.$i] = "Class {$i}";
			$classList['class '.$i] = "Class {$i}";
		}

		// add the junior high classes
		for($i = 1; $i <= 3; $i++) {
			$classList['jh'.$i] = "Junior High {$i}";
			$classList['jhs '.$i] = "Junior High {$i}";

			$classList['nursery'.$i] = "Nursery {$i}";
			$classList['nursery '.$i] = "Nursery {$i}";
			
			$classList['kg'.$i] = "Kindergarten {$i}";
			$classList['kg'.$i] = "Kindergarten {$i}";

			$classList['kindergarten'.$i] = "Kindergarten {$i}";
			$classList['kindergarten '.$i] = "Kindergarten {$i}";
		}

		// confirm that a valid parent name, child first name and child gender was parsed
		if(empty($params->parentName) || empty($params->childFirstName) || empty($params->childGender)) {
			return [
				'code' => 400,
				'data' => 'Sorry! An invalid parent name, child first name or child gender was parsed.'
			];
		}

		/** Prepare the data to be inserted */
		$data = [
			'fullname' => $params->parentName,
			'phone_number' => $params->phone ?? null,
			'email' => $params->email ?? null,
			'address' => $params->address ?? null,
			'date' => date("Y-m-d"),
			'clientId' => $params->clientId,
			'description' => $params->message ?? 'This is an admission enquiry from the website for my child ' . $params->childFirstName . ' ' . $params->childLastName . '.',
			'wardInformation' => [
				'childFirstName' => $params->childFirstName,
				'childLastName' => $params->childLastName,
				'childDob' => $params->childDob,
				'childGender' => $params->childGender,
				'applyingFor' => !empty($params->applyingFor) ? $classList[strtolower($params->applyingFor)] : 'Unknown',
				'parentName' => $params->parentName,
				'relationship' => !empty($params->relationship) ? $params->relationship : 'Parent',
				'phone' => $params->phone ?? null,
				'email' => $params->email ?? null,
			]
		];

		return $data;

		// insert the request
		$stmt = $this->db->prepare("INSERT INTO frontoffice SET client_id = ?, item_id = ?, created_by = ?, section = ?, source = ?, content = ?");

		// execute the statement
		$stmt->execute([
			$params->clientId, $item_id, 0, $section, 'website', json_encode($data)
		]);

		return [
			'code' => 200,
			'result' => [
				'record_id' => $item_id,
			],
			'data' => 'Admission request processed successfully.'
		];
	}

	/**
	 * Search for an admission request
	 * 
	 * @param stdClass $params
	 * 
	 * @return Array
	 */
	public function search(stdClass $params) {

		try {

			if(empty($params->record_id) || empty($params->clientId)) {
				return [
					'code' => 400,
					'data' => 'Sorry! An invalid record id or client id was parsed.'
				];
			}

			$stmt = $this->db->prepare("SELECT * FROM frontoffice WHERE client_id = ? AND item_id = ? LIMIT 1");
			$stmt->execute([$params->clientId, $params->record_id]);
			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if(empty($result)) {
				return [
					'code' => 400,
					'data' => 'Sorry! An invalid record id was parsed.'
				];
			}

			$result['content'] = json_decode($result['content'], true);

			return [
				'code' => 200,
				'result' => $result
			];

		} catch(PDOException $e) {
			return [
				'code' => 400,
				'data' => 'Sorry! There was an error while processing the request.'
			];
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

            // check if the user has a pending leave application
            if(empty($this->pushQuery("id", "frontoffice", 
                "client_id = '{$params->clientId}' AND item_id='{$params->request_id}' LIMIT 1"))) {
                return ["code" => 400, "data" => "Sorry! An invalid leave id was parsed."];
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