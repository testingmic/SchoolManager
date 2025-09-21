<?php

class Buses extends Myschoolgh {
	
	public function __construct() {
		parent::__construct();
	}

	/**
     * Get the list of all buses
     * 
     * @param $params
     * 
     * @return mixed
     */
	public function list($params = null) {

		try {

			// limit to query
			$limit = $params->limit ?? $this->global_limit;

			// parameters to use in filtering
			$filters = 1;

			// add more filters
			$filters .= !empty($params->driver_id) ? " AND a.driver_id IN {$this->inList($params->driver_id)}" : null;
			$filters .= !empty($params->bus_id) ? " AND a.item_id IN {$this->inList($params->bus_id)}" : null;
			$filters .= !empty($params->reg_number) ? " AND a.reg_number = '{$params->reg_number}'" : null;
			$filters .= !empty($params->clientId) ? " AND a.client_id = '{$params->clientId}'" : null;
			$filters .= !empty($params->q) ? " AND (a.brand LIKE '%{$params->q}%') " : null;
		
			$selectSummary = "";
			
			if(!empty($params->account_summary)) {
				$selectSummary = ",
				(
					SELECT SUM(ac.amount) FROM accounts_transaction ac 
					WHERE ac.item_type = 'Deposit' AND ac.status = '1' AND ac.attach_to_object = 'bus'  AND a.item_id = ac.record_object 
				) AS income,
				(
					SELECT SUM(ac.amount) FROM accounts_transaction ac 
					WHERE ac.item_type = 'Expense' AND ac.status = '1' AND ac.attach_to_object = 'bus'  AND a.item_id = ac.record_object 
				) AS expense";
			}

			// perform the query
			$stmt = $this->db->prepare("SELECT a.*, u.name AS fullname, u.email, u.username,
					(
                        SELECT b.description FROM files_attachment b 
                        WHERE b.resource='buses' AND b.record_id = a.item_id 
                        ORDER BY b.id DESC LIMIT 1
                    ) AS attachment, ud.name AS driver_name {$selectSummary}
				FROM buses a 
				LEFT JOIN users u ON u.item_id = a.created_by
				LEFT JOIN users ud ON ud.item_id = a.driver_id
				WHERE {$filters} AND a.status = ? ORDER BY a.id DESC LIMIT {$limit}"
			);
			$stmt->execute([1]);

			$data = [];
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

				$result->attachment = !empty($result->attachment) ? json_decode($result->attachment, true) : [];
				$data[] = $result;

			}

			return [
				"data" => $data,
				"code" => 200
			];

		} catch(PDOException $e) {
			return $this->unexpected_error;
		}

	}

	/**
     * Add or Update an existing bus record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
	public function save(stdClass $params) {

		// initial variables
		$bus_id = !empty($params->bus_id) ? $params->bus_id : null;
		$reg_number = strtoupper($params->registration_number);

		// create a new files object
		$filesObj = load_class("files", "controllers");

		// if the bus id is empty then insert a new record
		if(empty($bus_id)) {

			// confirm that the bus id is valid
			if(!empty($this->pushQuery("id", "buses", "reg_number='{$reg_number}' AND client_id='{$params->clientId}' LIMIT 1"))) {
				return ["code" => 400, "data" => "Sorry! This bus record already exists in the database."];
			}

			// generate a new string
			$bus_id = random_string("alnum", RANDOM_STRING);

			// set the data to update
			$data = [
				"brand" => $params->brand, 
				"item_id" => $bus_id, 
				"client_id" => $params->clientId, 
				"reg_number" => $reg_number,
				"created_by" => $params->userId,
				"insurance_company" => $params->insurance_company,
				"annual_premium" => $params->annual_premium ?? 0,
				"insurance_date" => date("Y-m-d", strtotime($params->insurance_date))
			];

			if(!empty($params->driver_id)) {
				$data["driver_id"] = $params->driver_id;
			}

			// set the insurance expiry date
			$data["expiry_date"] = date("Y-m-d", strtotime("{$data["insurance_date"]} +1 year"));

			// append to the data if the year_of_purchase was parsed
			if(!empty($params->year_of_purchase)) {
				$data["year_of_purchase"] = date("Y-m-d", strtotime($params->year_of_purchase));
			}

			// append to the data if the color was parsed
			if(!empty($params->color)) {
				$data["color"] = $params->color;
			}

			// append to the data if the year_of_purchase was parsed
			if(!empty($params->amount)) {
				$data["purchase_price"] = $params->amount;
			}

			// append to the data if the description was parsed
			if(!empty($params->description)) {
				$data["description"] = $params->description;
			}

			// attachments
			if(!empty($this->session->buses_attachment_root)) {

				// prepare the attachments
            	$attachments = $filesObj->prep_attachments("buses_attachment_root", $params->userId, $bus_id);

	            // insert the record if not already existing
	            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
	            $files->execute(["buses",$bus_id,json_encode($attachments),$bus_id, $params->userId,$attachments["raw_size_mb"],$params->clientId]);

	        }

			// insert the record
			if(!$this->_save("buses", $data)) {
				return ["code" => 400, "data" => "Sorry! There was an error while processing the request."];
			}

            // log the user activity
            $this->userLogs("buses", $bus_id, null, "{$params->userData->name} added the Bus: {$params->brand} with the registration number <strong>{$reg_number}</strong>", $params->userId);

            // load the record
            $par = (object) ["bus_id" => $bus_id, "limit" => 1];
			$record = $this->list($par)["data"][0];

			// return the success message
			return [
				"data" => "Bus successfully added.",
				"additional" => [
					"href" => "{$this->baseUrl}/bus/{$bus_id}",
					"append_data" => [
						"container" => "div[data-element_type='bus']:last",
						"data" => format_bus_item($record)
					],
					"array_stream" => [
						"buses_array_list" => [
							$bus_id => $record
						]
					],
					"delete_divs" => [
						"div[class~='empty_div_container'][data-element_type='bus']"
					],
					"clear" => true
				]
			];

		}
		// else if the record id was parsed
		else {

			// get the previous record
			$prevData = $this->pushQuery(
				"a.id, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
				"buses a", "a.item_id='{$params->bus_id}' AND a.client_id='{$params->clientId}' LIMIT 1"
			);

			// confirm that the bus id is valid
			if(empty($prevData)) {
				return ["code" => 400, "data" => "Sorry! An invalid bus id was parsed."];
			}

			// set the data to update
			$data = [
				"brand" => $params->brand, 
				"reg_number" => $reg_number, 
				"annual_premium" => $params->annual_premium ?? 0,
				"insurance_company" => $params->insurance_company, 
				"insurance_date" => date("Y-m-d", strtotime($params->insurance_date))
			];

			if(!empty($params->driver_id)) {
				$data["driver_id"] = $params->driver_id;
			}

			// set the insurance expiry date
			$data["expiry_date"] = date("Y-m-d", strtotime("{$data["insurance_date"]} +1 year"));

			// append to the data if the year_of_purchase was parsed
			if(!empty($params->year_of_purchase)) {
				$data["year_of_purchase"] = date("Y-m-d", strtotime($params->year_of_purchase));
			}

			// append to the data if the year_of_purchase was parsed
			if(!empty($params->amount)) {
				$data["purchase_price"] = $params->amount;
			}

			// append to the data if the color was parsed
			if(!empty($params->color)) {
				$data["color"] = $params->color;
			}

			// append to the data if the description was parsed
			if(!empty($params->description)) {
				$data["description"] = $params->description;
			}

			// update the record
			if(!$this->_save("buses", $data, ["item_id" => $params->bus_id, "client_id" => $params->clientId])) {
				return ["code" => 400, "data" => "Sorry! There was an error while processing the request."];
			}

			// attachments
			if(!empty($this->session->buses_attachment_root)) {

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
	            $module = "buses_attachment_root";
	            $attachments = $filesObj->prep_attachments($module, $params->userId, $params->bus_id, $initial_attachment);

	            // update attachment if already existing
	            if(isset($db_attachments)) {
	                $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? LIMIT 1");
	                $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $params->bus_id]);
	            } else {
	                // insert the record if not already existing
	                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
	                $files->execute(["buses", $params->bus_id, json_encode($attachments), $params->bus_id, $params->userId, $attachments["raw_size_mb"], $params->clientId]);
	            }

	        }

			// log the user activity
            $this->userLogs("buses", $params->bus_id, null, "{$params->userData->name} updated the Bus details with the registration number <strong>{$reg_number}</strong>", $params->userId);

            // load the record
			$par = (object) ["bus_id" => $params->bus_id, "limit" => 1];
			$record = $this->list($par)["data"][0];

			// return the success message
			return [
				"data" => "Bus information successfully modified.",
				"additional" => [
					"href" => "{$this->baseUrl}/bus/{$params->bus_id}",
					"replace_data" => [
						"container" => "div[data-element_type='bus'][data-element_id='{$params->bus_id}']",
						"data" => format_bus_item($record, true)
					],
					"array_stream" => [
						"buses_array_list" => [
							$params->bus_id => $record
						]
					],
					"delete_divs" => [
						"div[class~='empty_div_container'][data-element_type='bus']"
					]
				]
			];

		}

	}

	/**
     * Get the financials of a bus
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
	public function financials(stdClass $params) {
		
		global $accessObject;

		// set the permissions
		$a = "financials";

		// if the user does not have the required permissions
		if(!$accessObject->hasAccess($a, "buses")) {
			return ["code" => 403, "data" => $this->permission_denied];
		}

		// date range filter
		$date_range = $params->date_range ?? date("Y-m-d", strtotime("-1 month")).":".date("Y-m-d");
		$start_date = explode(":", $date_range)[0];
		$end_date = explode(":", $date_range)[1];

		// get the list of all classes
		$params = (object)[
			"route" => "deposit",
			"clientId" => $params->clientId,
			"date_range" => $date_range,
			"order_by" => "DESC",
			"busFinancials" => true,
			"attach_to_object" => "bus",
			"userData" => $params->userData
		];

		// get the transactions list
		$financials = load_class("accounting", "controllers", $params)->list_transactions($params)["data"];

		$statistics = [
			'count' => 0,
			'summation' => 0,
			'summation_by_type' => [
				'Deposit' => 0,
				'Expense' => 0,
				'Balance' => 0
			],
			'type' => [],
			'buses' => [],
			'charts' => [
				'labels' => [],
				'data' => [
					0 => [],
					1 => []
				]
			],
			'days_chart' => [],
		];
		
		$list_days = $this->listDays($start_date, $end_date, "Y-m-d");
		foreach($list_days as $day) {
			$statistics['charts']['labels'][] = date("jS M Y", strtotime($day));
			$statistics['days_chart'][$day] =  [
				'transactions' => 0,
				'labels' => [
					'Deposit' => 0,
					'Expense' => 0
				],
				'data' => [
					'Deposit' => 0,
					'Expense' => 0
				]
			];
		}

		// loop through the financials
		foreach($financials as $transaction) {

			// if the transaction is reversed, then skip
			if($transaction->reversed == 1) continue;

			// set the statistics
			$statistics['count']++;
			$statistics['summation'] += $transaction->amount;
			$statistics['type'][$transaction->item_type] = isset($statistics['type'][$transaction->item_type]) ? $statistics['type'][$transaction->item_type] + 1 : 1;
		
			// set the summation by type
			$statistics['summation_by_type'][$transaction->item_type] += $transaction->amount;
		
			// set the days chart
			if(!isset($statistics['days_chart'][$transaction->record_date])) {
				$statistics['days_chart'][$transaction->record_date] =  [
					'transactions' => 0,
					'labels' => [
						'Deposit' => 0,
						'Expense' => 0
					],
					'data' => [
						'Deposit' => 0,
						'Expense' => 0
					]
				];
			}
			$statistics['days_chart'][$transaction->record_date]['transactions']++;

			// set the buses
			$bus_name = !empty($transaction->bus_name) ? $transaction->bus_name : "N/A";
			if(!isset($statistics['buses'][$bus_name])) {
				$statistics['buses'][$bus_name] = [
					'count' => 0,
					'Deposit' => 0,
					'Expense' => 0
				];
			}
			$statistics['buses'][$bus_name]['count']++;
			$statistics['buses'][$bus_name][$transaction->item_type] += $transaction->amount;
		
			// set the labels and data
			if(!isset($statistics['days_chart'][$transaction->record_date]['labels'][$transaction->item_type])) {
				$statistics['days_chart'][$transaction->record_date]['labels'][$transaction->item_type] = 0;
			}
			$statistics['days_chart'][$transaction->record_date]['labels'][$transaction->item_type]++;
			$statistics['days_chart'][$transaction->record_date]['data'][$transaction->item_type] += $transaction->amount;
		}

		// set the balance
		$statistics['summation_by_type']['Balance'] = $statistics['summation_by_type']['Deposit'] - $statistics['summation_by_type']['Expense'];

		foreach($statistics['days_chart'] as $day => $data) {
			$statistics['charts']['data'][0][] = $data['data']['Deposit'];
			$statistics['charts']['data'][1][] = $data['data']['Expense'];
		}

		return [
			"code" => 200,
			"data" => [
				'statistics' => $statistics,
				'list_days' => $list_days
			]
		];

	}

	/**
     * Delete a bus record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
	public function delete(stdClass $params) {

		try {

			// get the previous record
			$check = $this->pushQuery("id, reg_number", "buses", "item_id='{$params->bus_id}' AND client_id='{$params->clientId}' LIMIT 1");

			// confirm that the bus id is valid
			if(empty($check)) {
				return ["code" => 400, "data" => "Sorry! An invalid bus id was parsed."];
			}

			// update the status of the bus
			$this->_save("buses", ["status" => 0], ["client_id" => $params->clientId, "item_id" => $params->bus_id]);

			// log the user activity
            $this->userLogs("buses", $params->bus_id, null, "{$params->userData->name} deleted the Bus record with the registration number: <strong>{$check[0]->reg_number}</strong>", $params->userId);

			// return the success message
			return [
				"code" => 200,
				"data" => "Bus record successfully deleted.",
			];

		} catch(PDOException $e) {}

	}

	/**
     * Lookup the user by the user id
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function user_lookup($params = null) {

        if(empty($params->user_id)) {
            return ["code" => 400, "data" => "User ID is required"];
        }

		global $isSupport;

        $explodedUserId = explode(":", $params->user_id);

		// set the where clause
		$whereClause = $isSupport ? "" : "u.client_id='{$params->clientId}' AND ";

        // search for the user
        $users = $this->pushQuery(
            "u.id, u.item_id, u.name, u.gender, u.class_id, u.day_boarder, u.unique_id, u.date_of_birth, u.user_type, u.enrollment_date, c.name AS class_name", 
            "users u LEFT JOIN classes c ON u.class_id=c.id", 
            "{$whereClause} u.id='{$explodedUserId[1]}' AND (u.user_status='active' OR u.user_status='Active')"
        );

        if(empty($users)) {
            return ["code" => 404, "data" => "User not found"];
        }

		// if the user type is parent, then get the children of the parent
		if(in_array($users[0]->user_type, ["parent", "guardian"])) {
			$users = $this->pushQuery(
				"u.id, u.item_id, u.name, u.gender, u.class_id, u.day_boarder, u.unique_id, u.date_of_birth, u.user_type, u.enrollment_date, c.name AS class_name", 
				"users u LEFT JOIN classes c ON u.class_id=c.id", 
				"{$whereClause} u.guardian_id LIKE '%{$users[0]->item_id}%' AND u.user_status='active' AND u.user_type='student'"
			);
		}

        return [
            "code" => 200,
            "data" => $users
        ];
    }

    /**
     * Save the attendance
     * 
     * @param Object $params
     * 
     * @return Array
     */
    public function log_attendance($params = null) {

        // check if the request is empty
        if(empty($params->request)) {
            return ["code" => 400, "data" => "Request is required"];
        }

        // check if the request is valid
        if(!in_array($params->request, ["bus", "daily"])) {
            return ["code" => 400, "data" => "Invalid request"];
        }

		// if the request is daily, then set the user id to the user id with the prefix userId
		if(!empty($params->request) && !empty($params->action)) {
			$params->user_id = "userId:{$params->user_id}";
		}

        // lookup the user
        $user = $this->user_lookup($params);

        if($user['code'] == 404) {
            return ["code" => 404, "data" => "User not found"];
		}

        // get the user data
        $user = $user['data'][0];

        // set the date logged and bus id
        $date_logged = date("Y-m-d");
        $bus_id = $params->bus_id ?? null;
		$action = $params->action ?? "checkin";
		$request = $params->request ?? "bus";

		// set the longitude and latitude
		$longitude = substr(($params->longitude ?? 0), 0, 12);
		$latitude = substr(($params->latitude ?? 0), 0, 12);

		// get the user id if the user id is not parsed
		$userId = !empty($params->userId) ? $params->userId : ($this->session->userId ?? 0);

		// handle the daily attendance if the request is daily and the action is checkin
		if($request == "daily" && $action == "checkin") {
			$bus_id = null;
			$attendanceObject = load_class("attendance", "controllers");

			// set the parameters
			$param = (object) [
				"date" => $date_logged,
				"clientId" => $params->clientId,
				"userId" => $params->userId ?? 0,
				"appendExisting" => true,
				"academic_year" => $params->academic_year ?? null,
				"academic_term" => $params->academic_term ?? null,
				"user_type" => $user->user_type == "student" ? "student" : "staff",
				"class_id" => !empty($user->class_id) ? $user->class_id : 0,
				"attendance" => (array)[
					"{$user->item_id}" => [
						"status" => "present",
						"comments" => "",
					]
				]
			];
			// log the attendance
			$attendanceObject->log($param);
		}

        // log the bus attendance for the user
        $this->db->query("INSERT INTO buses_attendance 
            (client_id, user_id, bus_id, date_logged, request, action, created_by, longitude, latitude) VALUES 
            ('{$params->clientId}', '{$user->id}', '{$bus_id}', '{$date_logged}', '{$request}', '{$action}', '{$userId}', '{$longitude}', '{$latitude}')"
        );

		$message = $action == "checkin" ? "{$user->name} has <span class='text-green-500 text-underline'>Checked In</span> to the bus" : "{$user->name} has <span class='text-red-500 text-underline'>Checked Out</span> of the bus";

        return [
            "code" => 200,
            "data" => $message
        ];
    }

	/**
     * Get the attendance list
     * 
     * @param Object $params
     * 
     * @return Array
     */
	public function attendance_history($params = null) {
		
		try {

			// append some filters to apply to the query
			$query = !empty($params->user_id) ? " AND a.user_id IN ('{$params->user_id}')" : "";
			$query .= !empty($params->bus_id) ? " AND a.bus_id IN ('{$params->bus_id}')" : "";
			$query .= !empty($params->date_logged) ? " AND a.date_logged = '{$params->date_logged}'" : "";
			$query .= !empty($params->request) ? " AND a.request = '{$params->request}'" : "";
			$query .= !empty($params->action) ? " AND a.action = '{$params->action}'" : "";
			$query .= !empty($params->user_ids) ? " AND a.user_id IN (".implode(",", $params->user_ids).")" : "";

			if(!empty($params->date_range)) {
				$split = explode(":", $params->date_range);
				
				$query .= !empty($split[0]) && !empty($split[1]) ? " AND a.date_logged BETWEEN '{$split[0]}' AND '{$split[1]}'" : "";
			}

			// get the list of users based on the request 
			$stmt = $this->db->prepare("SELECT 
					a.*, b.reg_number, u.name AS fullname, u.gender, u.day_boarder, u.unique_id, 
					u.date_of_birth, u.user_type, b.brand, b.insurance_company, b.insurance_date,
					b.driver_id AS driver_id, d.name AS driver_name, d.unique_id AS driver_unique_id, c.name AS class_name
				FROM buses_attendance a 
					LEFT JOIN buses b ON a.bus_id = b.item_id
					LEFT JOIN users u ON a.user_id = u.id
					LEFT JOIN classes c ON u.class_id = c.id
					LEFT JOIN users d ON b.driver_id = d.item_id
				WHERE a.client_id='{$params->clientId}' {$query}
				ORDER BY a.id DESC");
			$query = $stmt->execute();

			$data = $stmt->fetchAll(PDO::FETCH_OBJ);

			return [
				"code" => 200,
				"data" => $data
			];
		} catch(PDOException $e) {
			return [];
		}

	}

}
?>