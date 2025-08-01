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
			
			// perform the query
			$stmt = $this->db->prepare("SELECT a.*, u.name AS fullname, u.email, u.username,
					(
                        SELECT b.description FROM files_attachment b 
                        WHERE b.resource='buses' AND b.record_id = a.item_id 
                        ORDER BY b.id DESC LIMIT 1
                    ) AS attachment
				FROM buses a LEFT JOIN users u ON u.item_id = a.created_by
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
				"brand" => $params->brand, "item_id" => $bus_id, "client_id" => $params->clientId, 
				"reg_number" => $reg_number, "created_by" => $params->userId,
				"insurance_company" => $params->insurance_company,
				"annual_premium" => $params->annual_premium ?? 0,
				"insurance_date" => date("Y-m-d", strtotime($params->insurance_date))
			];

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

}
?>