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
	public $session;
	public $browser;
	public $platform;
	public $userId;
	public $clientId;
	public $appName;
	public $start_date;
    public $end_date;
    public $defaultUser;
	public $client_data;
	public $birthday_days_interval;
	public $accountLimit = [];
	public $school_academic_terms = [];
	public $academic_calendar_years = [];
	public $pk_public_key = "pk_live_1eca68da1afea8bb9d567a05fe05db1b4297a8c6";
	public $mnotify_key = "3LhA1Cedn4f2qzkTPO3cIkRz8pv0inBl9TWavaoTeEVFe";
	public $default_pay_email = "payments@myschoolgh.com";

	public $db;
	public $ip_address;
	public $baseUrl;
	public $user_agent;
	public $agent;
	public $group_by;
	public $date_format;

    public $academic_term;
    public $academic_year;
	public $iclient;
	public $this_term_starts;
	public $this_term_ends;
	public $last_term_starts;
	public $last_term_ends;
	public $defaultClientData;

	public $thisUser;
	public $color_set = [
		"#007bff", "#6610f2", "#6f42c1", "#e83e8c", "#dc3545", "#fd7e14", 
		"#ffc107", "#28a745", "#20c997", "#17a2b8", "#6c757d", "#343a40", 
		"#007bff", "#6c757d", "#28a745", "#17a2b8", "#ffc107", "#dc3545"
	];
	
	// class opening days
    public $default_opening_days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];

	public function __construct() {

		parent::__construct();
		
		global $myschoolgh, $session;

		$this->db = $myschoolgh;

		$this->session = $session;
		$this->clientId = !empty($session->clientId) ? $session->clientId : null;
		$this->ip_address = ip_address();
		$this->user_agent = load_class('user_agent', 'libraries');
		$this->appName = config_item("site_name");
		$this->platform = $this->user_agent->platform();
		$this->browser = $this->user_agent->browser();
		$this->agent = $this->user_agent->agent_string();

		$this->academic_years();
		$this->menu_content_array();
	}

	/**
	* menu_content_array
	* 
	* @return Array
	*/
	public function menu_content_array() {
        
        global $SITEURL;

        // set the default array list
        $content = [
            "students" => [
                "right" => [
                    "student_add" => ["Add Student", "fa-plus", "btn-warning"],
                    "fees-history" => ["Fees Payment History", "fa-list", "btn-dark"],
                    "fees-allocation" => ["Fees Allocation", "fa-money-bill", "btn-primary"],
                ]
            ],
            "student_add" => [
                "right" => [
                    "students" => ["List Students", "fa-list", "btn-primary"]
                ]
            ],
            "accounts" => [
                "right" => [
                	"transactions" => ["Transactions", "fa-balance-scale", "btn-plain"],
                    "incomes" => ["Incomes", "fa-money-bill-alt", "btn-primary"],
                    "expenses" => ["Expenses", "fa-money-bill", "btn-dark"],
                    "bank_deposits" => ["Bank Deposits", "fa-desktop", "btn-success"],
                    "bank_withdrawals" => ["Bank Withdrawals", "fa-wind", "btn-warning"],
                ]
            ],
            "incomes" => [
                "right" => [
                    "transactions" => ["Transactions", "fa-balance-scale", "btn-primary"],
                    "expenses" => ["Expenses", "fa-money-bill", "btn-dark"],
                    "bank_deposits" => ["Bank Deposits", "fa-desktop", "btn-success"],
                    "bank_withdrawals" => ["Bank Withdrawals", "fa-wind", "btn-warning"]
                ]
            ],
            "expenses" => [
                "right" => [
                    "transactions" => ["Transactions", "fa-balance-scale", "btn-primary"],
                    "incomes" => ["Incomes", "fa-money-bill", "btn-dark"],
                    "bank_deposits" => ["Bank Deposits", "fa-desktop", "btn-success"],
                    "bank_withdrawals" => ["Bank Withdrawals", "fa-wind", "btn-warning"]
                ]
            ],
            "transactions" => [
                "right" => [
                    "incomes" => ["Incomes", "fa-money-bill", "btn-primary"],
                    "expenses" => ["Expenses", "fa-money-bill-alt", "btn-dark"],
                    "bank_deposits" => ["Bank Deposits", "fa-desktop", "btn-success"],
                    "bank_withdrawals" => ["Bank Withdrawals", "fa-wind", "btn-warning"]
                ]
            ],
            "fees-history" => [
                "right" => [
                    "fees-payment" => ["Term Fees Payment", "fa-money-bill", "btn-success"],
                    "arrears/apay" => ["Arrears Payment", "fa-money-bill-alt", "btn-dark"],
                    // "fees-allocation" => ["Fees Allocation", "fa-desktop", "btn-success"],
                    "debtors" => ["Debtors List", "fa-wind", "btn-warning"]
                ]
            ],
        ];

        $this->menu_content_array = $content[$SITEURL[0]] ?? [];

        return $this;
    }

	/**
	 * Check if the user account is activated
	 *
	 * @return String
	 **/
	public function async_notification() {
		global $defaultClientData, $defaultUser;
		
		// if the user has not yet activated the account
		if(
			($defaultClientData->client_state === "Pending") ||
			($defaultUser->user_status == "Pending")
		) {
			return "
			<div class=\"alert alert-danger p-2 mb-2 text-center\">
				Your Account has not yet been activated. Please check your email for the verification link.
			</div>";
		}
	}
	
    /**
     * Replace all placeholders in the message content
     * 
     * @param String $message
     * @param String $page 
     * 
     * @return String 
     */
    public function replace_placeholder($message, $page = null) {

        $content = str_ireplace(["{{APPURL}}", "{{RESOURCE_PAGE}}"], [$this->baseUrl, $page], $message);

        return $content;
    }

	/**
	 * Run the Query to Load the Client Data
	 * Save the results in session and refresh after every 2 minutes
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	final function client_session_data($clientId, $clear = true) {

		// initial client data
		$client_data = (object) [];
		
		// if the clear is true then unset the session variable
		if($clear === true) {
			$this->session->remove("defaultClientData");
		}

		// if the session variable is empty then set a new session
		if(empty($this->defaultClientData)) {
			// load the client data
			$client_data = $this->client_data($clientId);
			$this->defaultClientData = $client_data;

			// set the session variable
			$this->session->set("defaultClientData", ["last_timer" => time(), "data" => $client_data]);
		} else {
			// check timer
			$client_data = $this->defaultClientData;
		}

		return $client_data;

	}

	/**
	 * Get the Grading System
	 * 
	 * @return Object
	 */
	final function grading_system($clientId, $academic_year, $academic_term) {

		// if either the academic term or year are empty
		if(empty($academic_year) || empty($academic_term)) {
			return [];
		}
		
		// prepare and execute the statement
		$stmt = $this->db->prepare("SELECT
				c.grading AS grading_system, c.structure AS grading_structure, 
				c.show_position, c.show_teacher_name, c.allow_submission, sba AS grading_sba
			FROM grading_system c
			WHERE c.client_id = ? AND c.academic_year = ? AND c.academic_term = ? LIMIT 1
		");
		$stmt->execute([$clientId, $academic_year, $academic_term]);

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		// return the result if a result was found
		if(!empty($result)) {
			return $result;
		} else {
			return [];
		}
	}

	/**
	 * client_data
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	final function client_data($clientId = null, $no_grading_system = false) {

		$clientId = !empty($clientId) ? $clientId : (!empty($this->clientId) ? $this->clientId : $this->session->clientId);

		try {

			// prepare and execute the statement
			$stmt = $this->db->prepare("
				SELECT a.*, b.item_id AS default_account_id, b.balance AS default_account_balance
				FROM clients_accounts a
				LEFT JOIN accounts b ON b.client_id = a.client_id AND b.default_account = '1' AND b.status = '1'
				WHERE a.client_id = ? AND a.client_status = ? LIMIT 1
			");
			$stmt->execute([$clientId, 1]);
			
			// loop through the list
			$result = $stmt->fetch(PDO::FETCH_OBJ);
			
			// if the record was found
			if(!empty($result)) {

				// loop through the items and convert into an object
				$result->client_preferences = json_decode($result->client_preferences);
				
				// set this value
				$this->birthday_days_interval = 8;

				// set the defaults
				$academic_year = null;
				$academic_term = null;

				// set the academic year
				if(!empty($result->client_preferences->academics->academic_year)) {
					$academic_year = $result->client_preferences->academics->academic_year;
				}

				// set the academic term
				if(!empty($result->client_preferences->academics->academic_term)) {
					$academic_term = $result->client_preferences->academics->academic_term;
				}

				// if no_grading_system was not parsed
				if(!$no_grading_system) {

					// get the structure
					$structure = $this->grading_system($clientId, $academic_year, $academic_term);

					// convert to an array
					$result = (array) $result;
					$result["birthday_days_interval"] = $this->birthday_days_interval;

					// if the structure is not empty
					if(!empty($structure)) {
						$result = array_merge($result, $structure);
						$result["grading_system"] = json_decode($result["grading_system"]);
						$result["grading_structure"] = json_decode($result["grading_structure"]);
						$result["grading_sba"] = json_decode($result["grading_sba"], true);
					} else {
						$result["grading_sba"] = [];
						$result["grading_system"] = [];
						$result["grading_structure"] = [];
					}
				}

				// convert to object
				$result = (object) $result;
			}
			
			return $result;
			
		} catch(PDOException $e) {
			return (object) $e;
		}
	}

	/**
	 * Get the Account Package Limit
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	final function clients_accounts_limit($clientId = null) {

		try {

			// set the client id
			$clientId = empty($clientId) ? $this->session->clientId : $clientId;

			// get the default account information
			$this->accountLimit = $this->pushQuery("student, staff, fees", "clients_accounts_limit", "client_id='{$clientId}' LIMIT 1")[0];

			return $this;

		} catch(PDOException $e) {
			return [];
		}

	}

	/**
	 * Get the Default Payment Account Set by the School Admin
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	final function default_payment_account($clientId = null) {

		try {

			// get the default account information
			return $this->pushQuery("item_id, balance","accounts","client_id='{$clientId}' AND status='1' AND default_account='1' AND state='Active' LIMIT 1");

		} catch(PDOException $e) {
			return false;
		}

	}

	/**
	 * @method itemById
	 * @param string $table
	 * @param string $field
	 * @param string $value
	 * @param string $column_to_return
	 * @return return the number of rows counted
	 **/
	final function itemById($table, $column, $value, $column_to_return) {
		
		$stmt = $this->db->query("SELECT * FROM $table WHERE $column='{$value}' AND status='1' LIMIT 1");

		if($stmt->rowCount() > 0) {
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				return $result->$column_to_return ?? null;
			}
		}
	}
	
	/**
	 * @method itemByIdNoStatus
	 * @param string $table
	 * @param string $field
	 * @param string $value
	 * @param string $column_to_return
	 * @return return the number of rows counted
	 **/
	final function itemByIdNoStatus($table, $column, $value, $column_to_return) {
		
		$stmt = $this->db->query("SELECT * FROM $table WHERE $column='$value' LIMIT 1");

		if($stmt->rowCount() > 0) {
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				return $result->$column_to_return ?? null;
			}
		}

	}

	/**
	 * The user needs to specify the table name for the query
	 * 
	 * @method lastRowId()
	 * 
	 * @return Int
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
	 * @param String	$columns
	 * @param String	$tableName
	 * @param String	$whereClause
	 * @param Bool		$print
	 * @param String	$query_style
	 * 
	 * @return mixed
	 **/
	final function pushQuery($columns = "*", $tableName = null, $whereClause = 1, $print = false, $query_style = "OBJ") {

		try {

			if(empty($tableName)) { return []; }

			$stmt = $this->db->prepare("SELECT {$columns} FROM {$tableName} WHERE $whereClause");
			$stmt->execute();

			if($print) {
				print "SELECT {$columns} FROM {$tableName} WHERE $whereClause";
			}

			return $query_style === "OBJ" ? $stmt->fetchAll(PDO::FETCH_OBJ) : $stmt->fetchAll(PDO::FETCH_ASSOC);

		} catch(PDOException $e) {
			return $e->getMessage();
		}

	}

	/**
	 * Get the column value
	 * 
	 * @return Object
	 **/
	final function columnValue($column = "*", $tableName = null, $whereClause = 1) {

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
	final function prependData($columns = "*", $tableName = null, $whereClause = 1) {

		try {

			$stmt = $this->db->prepare("SELECT {$columns} FROM {$tableName} WHERE $whereClause");
			$stmt->execute();

			$data = [];
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				
				// if isset an item then set it as key if not then set the id as key
				$key = isset($result->item_id) ? $result->item_id : $result->id;

				// loop through this array list
				foreach(["description", "requirements", "message", "content"] as $eachItem) {
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
	final function userLogs($subject, $itemId, $prevData = null, $description = null, $userId = null, $clientId = null, $source = null) {
		
		try {

			// user agent variable
			$clientId = !empty($clientId) ? $clientId : $this->clientId;
			$ur_agent = $this->platform .' | '.$this->browser . ' | '.ip_address();
			$prevData = (!empty($prevData) && is_object($prevData)) ? json_encode($prevData) : $prevData;
			$source = !empty($source) ? $source : "{$this->appName} Calculation<br>Property changed by an update from another property.";

			// prepare the statement
			$stmt = $this->db->prepare("
				INSERT INTO users_activity_logs SET client_id = ?, user_id = ?, subject = ?, 
				previous_record = ?, item_id = ?, description = ?, user_agent = ?, source = ?
			");
			return $stmt->execute([$clientId, ($userId ?? $this->userId), $subject, $prevData, $itemId, $description, $ur_agent, $source]);

		} catch(PDOException $e) {
			return [];
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
		  new DateTime(date('Y-m-d', strtotime($endDate. '+1 days')))
		);

		$days = array();
		$sCheck = (array) $period->start;

		// check the date parsed
		if(date("Y-m-d", strtotime($sCheck['date'])) == "1970-01-01") {
			
			// set a new start date and call the function again
			return $this->listDays(date("Y-m-d", strtotime("first day of this week")), date("Y-m-d", strtotime("today")));

			// exit the query
			exit;
		}
		
		// fetch the days to display
		foreach ($period as $key => $value) {
			// exempt weekends from the list
			if(!$weekends || !in_array(date("l", strtotime($value->format($format))), ['Sunday', 'Saturday'])) {
				$days[] = $value->format($format);
			}
			
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
	final function stringToArray($string, $delimiter = ",", $key_name = [], $allowEmpty = false, $func = "isNull") {
		
		// if its already an array then return the data
		if(is_array($string) || empty($string)) {
			return $string;
		}
		
		$array = [];
		$expl = explode($delimiter, $string);
		
		foreach($expl as $key => $each) {
			if(!empty($each) || $allowEmpty) {
				if(!empty($key_name)) {
					$array[$key_name[$key]] = trim($each) == "NULL" ? null : $func(trim($each));
				} else{
					$array[] = trim($each) == "NULL" ? null : trim($func($each), "\"");
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

		// if the date equates this, then return false
		if($date === "1970-01-01") { return false; }

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
	final function dateRange($date, $prefix = "a", $column = "date_created") {
		// return if empty
		if(empty($date)) {
			return;
		}
		
		// process the string
		$date = explode(':', $date);
		$fPart = (isset($date[0]) && $this->validDate($date[0])) ? $date[0] : '2020-01-01';
		$lPart = (isset($date[1]) && $this->validDate($date[1])) ? $date[1] : date('Y-m-d');

		// if the prefix is rparts then return the various items
		if($prefix === "rparts") {
			return [
				"start_date" => $fPart,
				"end_date" => $lPart
			];
		}
		
		// format the data to return
		if(!empty($date[1])) {
			return " AND (DATE({$prefix}.{$column}) >= '{$fPart}' AND DATE({$prefix}.{$column}) <= '{$lPart}')";
		} else {
			return " AND (DATE({$prefix}.{$column}) = '{$fPart}')";
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
    final function the_status_label($status, $class = "") {

        $label = $status;
        
        if(in_array($status, ["Pending", "Due Today", "Graduated"])) {
            $label = "<span class='badge badge-primary {$class}'>{$status}</span>";
        }
        elseif(in_array($status, ["Rejected", "Dismissed", "Reversed", "Transferred", "Cancelled", "Not Paid", "Unpaid", "Unseen", "Closed", "Overdue", "Expired", "Suspended", "Denied", "Withdrawn", "Lost"])) {
            $label = "<span class='badge badge-danger {$class}'>{$status}</span>";
        }
        elseif(in_array($status, ["Reopen", "Waiting", "Draft", "Processing", "In Review", "Confirmed", "Graded", "Requested", "Propagation", "Passive"])) {
            $label = "<span class='badge badge-warning text-white {$class}'>{$status}</span>";
        }
        elseif(in_array($status, ["Exported", "Complete", "Answered", "Solved", "Enrolled", "Active", "Approved", "Paid", "Running", "Seen", "Submitted", "Held", "Issued", "Returned", "Processed", "Won"])) {
            $label = "<span class='badge badge-success {$class}'>{$status}</span>";
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
	final function append_zeros($requestId, $number = 5) {
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
    final function replied_by($user_id) {

        try {
            $stmt = $this->db->prepare("SELECT name AS fullname, email, phone_number, image, user_type, position, description, username FROM users WHERE item_id = ? LIMIT 1");
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
	final function user_is_online($last_seen) {
		// online algorithm (user is online if last activity is at most 3 minutes ago)
        return (bool) (raw_time_diff($last_seen) < 0.05);
	}

	/**
	 * @method auto_update
	 * @param associative array()
	 * @param 0 => table_name
	 * @param 1 => columns
	 * @param 2 => where_clause
	 * @param array 3 => where_values
	 * @return json
	 **/
	final function auto_update(array $queryString) {
		
		try {
			
			/** Set the Application Type Header **/
			if(is_array($queryString)) {

				/** assign variable **/
				$tableName = xss_clean($queryString[0]);
				$tableColumns = xss_clean($queryString[1]);
				$whereClause = xss_clean($queryString[2]);
				$whereValues = array_map('xss_clean', $queryString[3]);

				/** check if all the 3 keys are present **/
				if(isset($tableName)) {

					/** Prepare the statement **/
					$stmt = $this->db->prepare("UPDATE $tableName SET $tableColumns WHERE $whereClause");
					
					/** Using for to loop through the values**/
					for($x = 1; $x <= count($whereValues); $x++) {
						$y = $x - 1;
						$stmt->bindParam($x, $whereValues[$y]);
					}
					
					/** Confirm if the transaction was successful **/
					return $stmt->execute();
				}
			}
		} catch(PDOException $e) {
			return false;
		}
	}

	/**
	 * @method auto_insert
	 * @param associative array()
	 * @param 0 => table_name
	 * @param 1 => columns
	 * @param 2 => where_clause
	 * @return json
	 **/
	final function auto_insert(array $queryString) {
		
		try {
			
			/** Set the Application Type Header **/
			if(is_array($queryString)) {

				/** assign variable **/
				$tableName = xss_clean($queryString[0]);
				$tableColumns = xss_clean($queryString[1]);
				$whereValues = array_map('xss_clean', $queryString[2]);

				/** check if all the 3 keys are present **/
				if(isset($tableName)) {

					/** Prepare the statement **/
					$stmt = $this->db->prepare("INSERT INTO $tableName SET $tableColumns");
					
					/** Using for to loop through the values**/
					for($x = 1; $x <= count($whereValues); $x++) {
						$y = $x - 1;
						$stmt->bindParam($x, $whereValues[$y]);
					}
					/** Confirm if the transaction was successful **/
					if($stmt->execute()) {
						/** Return true if all went well **/
						return true;
					} else {
						return false;
					}
					
				}
			}
		} catch(PDOException $e) {
			print $e->getMessage();
			return false;
		}
	}

	/**
	 * Insert a record into the database
	 *
	 * @param String 		$table
	 * @param Array 		$query
	 *
	 * @return Bool
	 **/
	final function _save($table, array $query, array $where = [], $limit = 1) {

		// set the date columns
		$date_columns = ["last_updated"];

		// confirm that the where clause is empty
		if(empty($where)) {
			$sql = "INSERT INTO {$table} SET ";
		} else {
			$sql = "UPDATE {$table} SET ";
		}

		// loop through the query
		foreach($query as $key => $value) {
			// skip empty value columns
			if(!empty($value) || ($value === 0)) {
				$sql .= in_array($key, $date_columns) ? ''.$key.'='.$value.',' : 
					(
						($key === "column_value") ? $value . ',' : 
						(is_array($value) ? ''.$key.'="'.json_encode($value).'",' : ''.$key.'="'.addslashes($value).'",')
					);
			}
		}

		// if the where variable is not empty
		if(!empty($where)) {

			// remove the trailing comma
			$sql = trim($sql, ",");

			// set the where parameter
			$sql .= " WHERE ";

			// loop through the where clause parameter
			foreach($where as $key => $value) {
				$sql .= ' '.$key.'="'.addslashes($value).'" AND ';
			}

			// remove the trailing AND word
			$sql = trim($sql);
			$sql = trim($sql, "AND");
			$sql .= " LIMIT {$limit}";
		}

		$sql = trim($sql, ",");

		try {
			
			// execute the query
			return $this->db->query($sql);

		} catch(PDOException $e) {
			print $e->getMessage();
			return [];
		}

	}

    /**
     * Format the Contact Number properly
     * 
     * @return Array
     */
    final function format_contact($contact) {
        $contact = str_ireplace(" ", "", $contact);
        $contact = "233".substr($contact, -9);
        return $contact;
    }
	
	/**
     * Confirm Last Message sent
     * 
     * Check the last time the user sent a message
     * 
     * @return Bool
     */
    final function check_time($table = "users", $timer = 2, $where = null) {
        
        // get the last date created
        $last_time = $this->columnValue("date_created", $table, "ipaddress='{$this->ip_address}' {$where} ORDER BY id DESC");

        // print_r($last_time);
        // confirm if not empty
        if(empty($last_time)) {
            return true;
        }

        // return false if the column was not found
        if(!isset($last_time->date_created)) {
        	return false;
        }

        // online algorithm (user is online if last activity is at most 3 minutes ago)
        return (bool) (raw_time_diff($last_time->date_created) > $timer);

    }

    /**
     * Remove Quotes
     * 
     * This method removes all single and double quotes from a string
     * 
     * @return String
     */
    final function remove_quotes($string) {
    	return str_ireplace(['"', "'"], [""], trim($string));
    }

	/**
	 * Get the School Academic Terms
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	final function academic_terms($clientId = null) {

		// set the client id
		$clientId = !empty($clientId) ? $clientId : (!empty($this->clientId) ? $this->clientId : $this->session->clientId);

		// get the schools academic years
		$this->school_academic_terms = $this->pushQuery("id, name, description", "academic_terms","1 AND client_id = '{$clientId}' LIMIT {$this->temporal_maximum}");

		return $this;
	}

	/**
	 * Construct the Academic Years to Load
	 * 
	 * @return Array
	 */
	final function academic_years($clientId = null) {
		/** Set the Parameters */
		$previous_year = 2020;
		$next_years = date("Y") + 5;
		
		/** Loop through the list */
		for($i = $previous_year; $i <= $next_years; $i++) {
			$this->academic_calendar_years[] = ($i)."/".($i+1);
		}

		return $this;
	}

	/**
	 * Convert Amount To Words
	 * 
	 * @param $amount
	 * 
	 * @return String
	 */
	final function amount_to_words($number) {

		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = [
			0                   	=> 'Zero',
			1                   	=> 'One',
			2                   	=> 'Two',
			3                   	=> 'Three',
			4                   	=> 'Four',
			5                   	=> 'Five',
			6                   	=> 'Six',
			7                   	=> 'Seven',
			8                   	=> 'Eight',
			9                   	=> 'Nine',
			10                  	=> 'Ten',
			11                  	=> 'Eleven',
			12                  	=> 'Twelve',
			13                  	=> 'Thirteen',
			14                  	=> 'Fourteen',
			15                  	=> 'Fifteen',
			16                  	=> 'Sixteen',
			17                  	=> 'Seventeen',
			18                  	=> 'Eighteen',
			19                  	=> 'Nineteen',
			20                  	=> 'Twenty',
			30                  	=> 'Thirty',
			40                  	=> 'Fourty',
			50                  	=> 'Fifty',
			60                  	=> 'Sixty',
			70                  	=> 'Seventy',
			80                  	=> 'Eighty',
			90                  	=> 'Ninety',
			100                 	=> 'Hundred',
			1000                	=> 'Thousand',
			1000000             	=> 'Million',
			1000000000          	=> 'Billion',
			1000000000000       	=> 'Trillion',
			1000000000000000    	=> 'Quadrillion',
			1000000000000000000 	=> 'Quintillion'
		];
	
		if (!is_numeric($number)) {
			return false;
		}
	
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			return 'This function only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX;
		}
	
		if ($number < 0) {
			return $negative . $this->amount_to_words(abs($number));
		}
	
		$string = null;
		$fraction = null;
	
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}
		
		try {
			switch (true) {
				case $number < 21:
					$string = $dictionary[$number];
					break;
				case $number < 100:
					$tens   = ((int) ($number / 10)) * 10;
					$units  = $number % 10;
					$string = $dictionary[$tens];
					if ($units) {
						$string .= $hyphen . $dictionary[$units];
					}
					break;
				case $number < 1000:
					$hundreds  = $number / 100;
					$remainder = $number % 100;
					$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
					if ($remainder) {
						$string .= $conjunction . $this->amount_to_words($remainder);
					}
					break;
				default:
					$baseUnit = pow(1000, floor(log($number, 1000)));
					$numBaseUnits = (int) ($number / $baseUnit);
					$remainder = $number % $baseUnit;
					$string = $this->amount_to_words($numBaseUnits) . ' ' . ($dictionary[$baseUnit] ?? null);
					if ($remainder) {
						$string .= $remainder < 100 ? $conjunction : $separator;
						$string .= $this->amount_to_words($remainder);
					}
					break;
			}

			if (null !== $fraction && is_numeric($fraction)) {
				$string .= " Cedis";
				// if the strlen is two
				if(strlen($fraction) < 3) {
					// confirm if the number is less than 21 then replace it directly
					if($fraction < 21) {
						$string .= " ".$dictionary[$fraction];
						$string .= " Pesewas";
					} else {
						// clean the number
						$tens   = ((int) ($fraction / 10)) * 10;
						$units  = $fraction % 10;
						$string .= " ".($dictionary[$tens] ?? null);
						if ($units) {
							$string .= $hyphen . $dictionary[$units];
						}
						$string .= " Pesewas";
					}
				} else {
					// split the numbers
					$split = str_split($fraction);
					// mention the numbers as a single integer
					foreach($split as $number) {
						$string .= " ".$dictionary[$number];
					}
					$string .= " Pesewas";
				}
			}
		
		return $string;

		} catch(DivisionByZeroError $e) {
			return "Error: {$e->getMessage()}";
		}
	
	}

    /**
     * Append Fees Owings
     * 
     * Going to Join the Two Arrays Together
     * 
     * @return Array
     */
    public function append_fees_details($current, $previous) {
        $new_array = [];
        foreach($previous as $key => $value) {
            foreach($value as $ikey => $ivalue) {
                $new_array[$key][$ikey] = isset($new_array[$key][$ikey]) ? $ivalue : $ivalue;
            }
        }
        foreach($current as $key => $value) {
            foreach($value as $ikey => $ivalue) {
                $new_array[$key][$ikey] = isset($new_array[$key][$ikey]) ? $ivalue : $ivalue;
            }
        }
        return $new_array;
    }

    /**
     * Append Fees Owings
     * 
     * Going to Join the Two Arrays Together
     * 
     * @return Array
     */
    public function append_fees_category($current) {
        $new_array = [];
        foreach($current as $key => $value) {
            foreach($value as $ikey => $ivalue) {
                $new_array[$ikey] = isset($new_array[$ikey]) ? ($new_array[$ikey] + $ivalue) : $ivalue;
            }
        }
        return $new_array;
    }

}
?>