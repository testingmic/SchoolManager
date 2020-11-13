<?php
// ensure this file is being included by a parent file
if( !defined( 'BASEPATH' ) ) die( 'Restricted access' );

class Myschoolgh extends Models {

	/* A globl variable to set for the table to query */
	public $tableName;

	/* The edit url variable that will be used in the loadDetails class */
	public $editURL;
	public $permitPage;

	/* This is the global value for the browser and platform to use by all methods */
	public $browser;
	public $platform;
	public $userId;
	public $appName;
	public $start_date;
    public $end_date;

	public function __construct() {

		parent::__construct();
		
		global $myschoolgh, $session;

		$this->db = $myschoolgh;

		$this->session = $session;
		$this->ip_address = ip_address();
		$this->baseUrl = config_item('base_url');

		$this->user_agent = load_class('user_agent', 'libraries');
		$this->appName = config_item("site_name");
		$this->platform = $this->user_agent->platform();
		$this->browser = $this->user_agent->browser();
		$this->agent = $this->user_agent->agent_string();
	}

	/**
	 * @method lastRowId()
	 * @param $tableName The user needs to specify the table name for the query
	 * @return $rowId
	 **/
	final function lastRowId($tableName) {

		$stmt = $this->db->prepare("SELECT id AS rowId FROM {$tableName} ORDER BY id DESC LIMIT 1");
		$stmt->execute();

		return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_OBJ)->rowId : 0;
	}

	/**
	 * @method itemsCount($whereClause)
	 * @desc This method counts the number of rows found
	 * 
	 * @return int
	 *
	 **/
	final function itemsCount($tableName, $whereClause = 1) {
		
		try {

			$stmt = $this->db->prepare("SELECT * FROM {$tableName} WHERE $whereClause");
			$stmt->execute();

			return $stmt->rowCount();

		} catch(PDOException $e) {
			return false;
		}

	}

	/**
	 * @method pushQuery($columns, $table, $whereClause)
	 * @desc Receives user query and returns the full data array
	 * 
	 * @return array
	 **/
	final function pushQuery($columns = "*", $tableName, $whereClause = 1) {

		try {

			$stmt = $this->db->prepare("SELECT {$columns} FROM {$tableName} WHERE $whereClause");
			$stmt->execute();

			return $stmt->fetchAll(PDO::FETCH_OBJ);

		} catch(PDOException $e) {
			return $e->getMessage();
		}

	}

	/**
	 * Get the column value
	 * 
	 * @return Object
	 **/
	final function columnValue($column = "*", $tableName, $whereClause = 1) {

		try {

			$stmt = $this->db->prepare("SELECT {$column} FROM {$tableName} WHERE $whereClause LIMIT 1");
			$stmt->execute();

			return $stmt->fetch(PDO::FETCH_OBJ);

		} catch(PDOException $e) {
			return $e->getMessage();
		}

	}

	/**
	 * @method pushQuery($columns, $table, $whereClause)
	 * @desc Receives user query and returns the full data array
	 * 
	 * @return array
	 **/
	final function prependData($columns = "*", $tableName, $whereClause = 1) {

		try {

			$stmt = $this->db->prepare("SELECT {$columns} FROM {$tableName} WHERE $whereClause");
			$stmt->execute();

			$data = [];
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				
				// if isset an item then set it as key if not then set the id as key
				$key = isset($result->item_id) ? $result->item_id : $result->id;

				// loop through this array list
				foreach(["description", "requirements"] as $eachItem) {
					// if isset the value
					if(isset($result->$eachItem)) {
						// clean the data parsed
						$result->$eachItem = stripslashes(htmlspecialchars_decode($result->$eachItem));
					}
				}
				
				// loop through this array list
				foreach(["attachment", "awards", "managers"] as $eachItem) {
					// if isset the value
					if(isset($result->$eachItem)) {
						// convert the string info an array object
						$result->$eachItem = json_decode($result->$eachItem);
					}
				}

				// clean the category name
				if(isset($result->category)) {
                    $result->category = ucwords(str_replace(["\"","-",","], ["", " ",", "], $result->category));
                }
				$data[$key] = $result;
			}
			return $data;
		} catch(PDOException $e) {
			return [];
		}

	}

	/**
	 * Load the previous record
	 * 
	 * @param String $table		The name of the table to query
	 * @param String $item_id	This is the value of the column value to load
	 * 
	 * @return Object
	 */
	final function prevData($table, $item_id) {
		// query list
		$query = [
			"data" => [
				"table" => "{$table}", "where" => "a.item_id = '{$item_id}'", "columns" => "a.*"
			]
		];

		try {
			// prepare and execute the query
			$stmt = $this->db->prepare("SELECT {$query["data"]["columns"]} 
				FROM {$table} a WHERE {$query["data"]["where"]} LIMIT 1
			");
			$stmt->execute();

			// password removal
			$count = $stmt->rowCount();

			// if result was found
			if($count) {
				// return the json encoded version of the query
				$result = $stmt->fetch(PDO::FETCH_OBJ);
				// remove the password column if in the query set
				if(isset($result->password)) {
					unset($result->password);
				}
				return $result;
			}

		} catch(PDOException $e){}
	}

	/**
	 * @method userLogs
	 * 
	 * @param $page 		This is the page that the user is managing
	 * @param $itemId		This relates to the item that is being managed
	 * @param $description 	This is the full description of what is being done
	 * @param $prevData		This is a previous version of the record that is existing (if the user is updating a record)
	 * 
	 * @return null
	 *
	 **/
	final function userLogs($subject, $itemId, $prevData = null, $description, $userId = null, $source = null) {
		
		try {

			// user agent variable
			$ur_agent = $this->platform .' | '.$this->browser . ' | '.ip_address();
			$prevData = (!empty($prevData) && is_object($prevData)) ? json_encode($prevData) : $prevData;
			$source = !empty($source) ? $source : "{$this->appName} Calculation<br>Property changed by an update from another property.";

			// prepare the statement
			$stmt = $this->db->prepare("
				INSERT INTO users_activity_logs SET user_id = ?, subject = ?, 
				previous_record = ?, item_id = ?, description = ?, user_agent = ?, source = ?
			");
			return $stmt->execute([($userId ?? $this->userId), $subject, $prevData, $itemId, $description, $ur_agent, $source]);

		} catch(PDOException $e) {
			print $e->getMessage();
		}

	}

	/**
	 * @method listDays
	 * @desc It lists dates between two specified dates
	 * @param string $startDate 	This is the date to begin query from
	 * @param string $endDate	This is the date to end the request query
	 * @param string $format 	This is the format that will be applied to the date to be returned
	 * @return array
	 **/
	final function listDays($startDate, $endDate, $format='Y-m-d', $weekends = false) {

		$period = new DatePeriod(
		  new DateTime($startDate),
		  new DateInterval('P1D'),
		  new DateTime(date('Y-m-d',strtotime($endDate. '+1 days')))
		);

		$days = array();
		$sCheck = (array) $period->start;

		// check the date parsed
		if(date("Y-m-d", strtotime($sCheck['date'])) == "1970-01-01") {
			
			// set a new start date and call the function again
			return $this->listDays(date("Y-m-d", strtotime("first day of this month")), date("Y-m-d", strtotime("yesterday")));

			// exit the query
			exit;
		}
		
		// fetch the days to display
		foreach ($period as $key => $value) {

			$days[] = $value->format($format);

		}
		
		return $days;
	}

	/**
	 * @method stringToArray
	 * 
	 * @desc Converts a string to an array
	 * @param $string The string that will be converted to the array
	 * @param $delimeter The character for the separation
	 * 
	 * @return Array
	 */
	final function stringToArray($string, $delimiter = ",", $key_name = [], $allowEmpty = false) {
		// if its already an array then return the data
		if(is_array($string)) {
			return $string;
		}

		$array = [];
		$expl = explode($delimiter, $string);
		foreach($expl as $key => $each) {
			if(!empty($each) || $allowEmpty) {
				if(!empty($key_name)) {
					$array[$key_name[$key]] = trim($each);
				} else{
					$array[] = trim($each, "\"");
				}
			}
		}
		return $array;
	}

	/**
	 * @method cleanLimit
	 * @desc This method takes the limit clause parsed in the query and formats it correctly
	 * @param string $limit 	This is the limit string that has been parsed
	 * @return string
	 **/
	final function cleanLimit($limit) {

		// process the string
		$limit = explode(',', $limit);
		$fPart = (isset($limit[0]) && ($limit[0] > -1)) ? (int) $limit[0] : 0;
		$lPart = (isset($limit[1]) && ($limit[1] > -1)) ? (int) $limit[1] : 25;

		$lPart = ($lPart != 0) ? $lPart : 25;

		$fPart = ($fPart > 500) ? 500 : $fPart;
		$lPart = ($lPart > 500) ? 500 : $lPart;

		return (!isset($limit[1])) ? $fPart : "$fPart,$lPart";
	}

	/**
	 * Verify if a string parsed is a valid date
	 * 
	 * @param string $date 		This is the date string that has been parsed by the user
	 * @param string $format 	This is the format for that date to use
	 * @return bool
	 */
	final function validDate($date, $format = 'Y-m-d') {
		
		$date = date($format, strtotime($date));

	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) === $date;
	}

	/**
	 * This method prepares a string to be used in a query
	 * This will format the user parameters to for a valid IN query
	 * 
	 * @param String $params 	This is the string that the user has parsed
	 * @param Array $compare 	This is the string to test the user's own against
	 * @param String $colum 	This is the column name
	 * 
	 * @return String
	 */
	final function formatInQuery($param, array $compare, $column) {

		$params = (is_array($param)) ? $param : $this->stringToArray($param);

		if(count($params) > count($compare)) {
			return;
		}

		$string = '(';
		foreach($params as $item) {
			if(!in_array($item, $compare)) {
				return null;
				break;
			}

			$string .= "'{$item}',";
		}
		$string = substr($string, 0, -1);
		$string .= ')';

		return " AND $column IN $string"; 
	}

	/**
	 * @method dateRange
	 * @desc This method prepares and submits a clean date for processing
	 * @param string $date This is the date range that has been parsed
	 * @param string $prefix This is the SQL Query placeholder
	 **/
	final function dateRange($date, $prefix, $column = "date_created") {

		// process the string
		$date = explode(':', $date);
		$fPart = (isset($date[0]) && $this->validDate($date[0])) ? $date[0] : '2020-05-01';
		$lPart = (isset($date[1]) && $this->validDate($date[1])) ? $date[1] : date('Y-m-d');

		if(!empty($date[1])) {
			return " AND (DATE($prefix.{$column}) >= '{$fPart}' AND DATE($prefix.{$column}) <= '{$lPart}')";
		} else {
			return " AND (DATE($prefix.{$column}) = '{$fPart}')";
		}
	}

	/**
	 * This logs the user activity for trying to perform a suspected activity
	 *
	 * @param string $endpoint 		This is the activity that the user wants to perform
	 * @param string $tableName 	This is the name of the table that the activity was to be carried on
	 * @param array $invalids		The content of the data to be parsed that does not exist
	 * @param array $itemIds		This is the entire ids that have been parsed.
	 * @return bool
	 **/
	final function deleteBreach($endpoint, $tableName, array $invalids = [], array $itemIds = []) {
		
		try {

			// algorithm for severity
			$itemCount = count($itemIds);
			$invalidCount = count($invalids);

			$diff = $itemCount - $invalidCount;

			// find 30 percent of the entire list
			$thirtyPercent = round($itemCount * 0.3);

			// severity range
			if($diff >= $thirtyPercent) {
				$severity = "high";
			} else {
				$severity = "low";
			}

			// insert the record
			$stmt = $this->db->prepare("
				INSERT INTO breach_notifications
				SET request_method = ?, table_name = ?, severity = ?, suspected_ids = ?
			");
			return $stmt->execute([
				$endpoint, $tableName, $severity, json_encode($invalids)
			]);

		} catch(PDOException $e) {
			return false;
		}

	}

	/**
	 * Compare array and remove item from the list
	 * 
	 * @param String $arrayList		The list to loop through
	 * @param String $item			The value to find in the array list
	 * @param String $delimeter		The delimiter to use for converting the string to array
	 * 
	 * @return Array
	 */
	final function removeArrayValue($arrayList, $item, $delimeter = ",") {

		$arrayVariables = !is_array($arrayList) ? $this->stringToArray($arrayList, $delimeter) : $arrayList;
		$arrayKey = array_search($item, $arrayVariables);

		/** Remove the value from the array list */
		if(!empty($arrayKey) || ($arrayKey == 0)) {
			unset($arrayVariables[$arrayKey]);
		}

		return $arrayVariables;
	}

	/**
	 * Remove a record from the database table
	 * 
	 * @param stdClass 	$params				This object contains the item and its id to delete
	 * 					$params->item 		This refers to either a brand or user or any other item to remove
	 * 					$params->item_id	This is the unique id of the item to remove
	 * 
	 * @return String | Bool
	 */
	final function removeRecord(stdClass $params) {
		/** Process the request */
		if(empty($params->item) || empty($params->item_id)) {
			return "denied";
		}

		try {
			
		} catch(PDOException $e) {
			$this->db->rollBack();
			return false;
		}
	}

	/**
	 * This method prepares a string to be used in a query
	 * This will format the user parameters to for a valid IN query
	 * 
	 * @param String $params 	This is the string that the user has parsed
	 * @param Array $compare 	This is the string to test the user's own against
	 * @param String $colum 	This is the column name
	 * 
	 * @return String
	 */
	final function inList($param) {

		if(empty($param)) {
			return $param;
		}

		$params = (is_array($param)) ? $param : $this->stringToArray($param);

		$string = '(';
		foreach($params as $item) {
			$string .= "'{$item}',";
		}
		$string = substr($string, 0, -1);
		$string .= ')';

		return $string; 
	}

    /**
     * The status labels
	 * 
	 * @param String $status
	 * 
	 * @return String
     */
    public function the_status_label($status) {

        $label = $status;
        if(in_array($status, ["Pending"])) {
            $label = "<span class='badge badge-primary'>{$status}</span>";
        }
        elseif(in_array($status, ["Closed", "Rejected", "Cancelled", "Not Paid", "Unpaid", "Unseen"])) {
            $label = "<span class='badge badge-danger'>{$status}</span>";
        }
        elseif(in_array($status, ["Reopen", "Waiting", "Processing", "In Review", "Confirmed"])) {
            $label = "<span class='badge badge-warning text-white'>{$status}</span>";
        }
        elseif(in_array($status, ["Answered", "Solved", "Enrolled", "Active", "Approved", "Paid", "Running", "Seen"])) {
            $label = "<span class='badge badge-success'>{$status}</span>";
        }

        return $label;
    }

	/**
	 * Order Id format by adding zeros to the begining
	 * 
	 * @param String $requestId		This is the id to format
	 * 
	 * @return String
	 */
	public function append_zeros($requestId, $number = 5) {
		$preOrder = str_pad($requestId, $number, '0', STR_PAD_LEFT);
		return $preOrder;
	}

	/**
     * Get the information of the one who shared the repliy
     * 
     * @param String $user_id
     * 
     * @return Object
     */
    public function replied_by($user_id) {

        try {

            $stmt = $this->db->prepare("SELECT name AS fullname, email, phone_number, image, user_type, position, description FROM users WHERE item_id = ? LIMIT 1");
            $stmt->execute([$user_id]);

            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch(PDOException $e) {
            return false;
        }
    }

	/**
	 * Confirm that the user is online by checking the difference between the last_seen and the current time
	 * If the difference is 5 minutes or less then, the user is online if not then the user is offline
	 */
	public function user_is_online($last_seen) {
		// online algorithm (user is online if last activity is at most 3 minutes ago)
        return (bool) (raw_time_diff($last_seen) < 0.05);
	}

}
?>