<?php
 
class Crons {

	private $db;
	private $userAccount;
	private $mailAttachment = array();
	private $rootUrl;
	private $clientId;
	private $ini_data;
	private $baseUrl;
	private $limit = 1000;

	public function __construct() {
		// INI FILE
		$this->ini_data = parse_ini_file(dirname(dirname(__DIR__)) . "/db.ini");

		// set some more variables
		$this->baseUrl = $this->ini_data["base_url"];
		$this->rootUrl = $this->ini_data["root_url"];
		$this->dbConn();
	}
	
	/**
	 * Run the connection to the database
	 * 
	 * @return $this
	 */
	private function dbConn() {
		
		// CONNECT TO THE DATABASE
		$connectionArray = array(
			'hostname' => $this->ini_data['hostname'],
			'database' => $this->ini_data['database'],
			'username' => $this->ini_data['username'],
			'password' => $this->ini_data['password']
		);
		
		// run the database connection
		try {
			$conn = "mysql:host={$connectionArray['hostname']}; dbname={$connectionArray['database']}; charset=utf8";			
			$this->db = new PDO($conn, $connectionArray['username'], $connectionArray['password']);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
		} catch(PDOException $e) {
			die("Database Connection Error: ".$e->getMessage());
		}

		return $this->db;

	}

	/**
	 * Push a query to the database
	 * 
	 * @param String $columns
	 * @param String $table
	 * @param String $where
	 * 
	 * @return Array
	 */
	private function pushQuery($columns, $table, $where) {
		try {
			$stmt = $this->db->prepare("SELECT {$columns} FROM {$table} WHERE {$where}");
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_OBJ);
		} catch(PDOException $e) {
			return [];
		}
	}

	/**
	 * account_status
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	public function account_status() {

		try {

			$stmt = $this->db->prepare("SELECT a.id, a.client_name, a.client_preferences, a.client_id,
				(SELECT COUNT(DISTINCT b.item_id) FROM users b WHERE b.client_id = a.client_id AND b.user_type IN ('admin')) AS admins_count,
                (SELECT COUNT(DISTINCT b.item_id) FROM users b WHERE b.client_id = a.client_id AND b.user_type='student') AS students_count,
                (SELECT COUNT(DISTINCT b.item_id) FROM users b WHERE b.client_id = a.client_id AND b.user_type IN ('teacher','employee','accountant')) AS staff_count
				FROM clients_accounts a
				WHERE a.client_status = ? AND a.client_state = ? AND a.client_id != ? LIMIT 10"
			);
			$stmt->execute([1, "Active", "MSGH00002"]);
			
			// loop through the list
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				// loop through the items and convert into an object
				$prefs = json_decode($result->client_preferences);

				// set the expiry
                $expiry = strtotime($prefs->account->expiry);
                $current_time = time();
                $clientId = $result->client_id;

                // get the fees received
                $fees = $this->db->prepare("SELECT SUM(amount) AS fees_received
                	FROM fees_collection 
                	WHERE 
                		reversed='0' AND status='1' AND client_id='{$clientId}' AND
                		academic_term='{$prefs->academics->academic_term}' AND academic_year='{$prefs->academics->academic_year}'
                	LIMIT 5000
                ");
                $fees->execute();

                // data to use
                $fees_received = $fees->fetch(PDO::FETCH_OBJ)->fees_received;
                
                if($current_time > $expiry) {
                    $this->db->query("UPDATE clients_accounts SET client_state='Expired' WHERE id='{$result->id}' LIMIT 1");
                    print "The Account of {$result->client_name} has Expired\n";
                }

                // add and remove limits on accounts
                $query = null;
                if($result->students_count >= $prefs->account->student) {
                	$query .= ",student='1'";
                }
                elseif($result->students_count < $prefs->account->student) {
                	$query .= ",student='0'";
                }

                // set the admin and staff
                if(($result->admins_count + $result->staff_count) >= ($prefs->account->staff + $prefs->account->admin)) {
                	$query .= ",staff='1'";
                }
                elseif(($result->admins_count + $result->staff_count) < ($prefs->account->staff + $prefs->account->admin)) {
                	$query .= ",staff='0'";
                }

                // set for fees payment
                if(isset($prefs->account->fees)) {
					$prefs->account->fees = trim($prefs->account->fees);
					if(!empty($prefs->account->fees) && !is_null($prefs->account->fees)) {
						if(!empty($fees_received) && round($fees_received) >= round($prefs->account->fees)) {
							$query .= ",fees='1'";
						}
						elseif(!empty($fees_received) && round($fees_received) < round($prefs->account->fees)) {
							$query .= ",fees='0'";
						}
					} else {
						$query .= ",fees='0'";
					}
	            }
	            $iqr = "UPDATE clients_accounts_limit SET last_updated=now() {$query} WHERE client_id='{$clientId}' LIMIT 1";

	            // execute the query
	            $this->db->query($iqr);

				// get the payroll settings
				$settings = $this->pushQuery("*", "settings", "client_id = '{$clientId}' AND setting_name = 'payroll_settings'");
				if(!empty($settings)) {
					$settings = json_decode($settings[0]->setting_value, true);

					if(!empty($settings['auto_validate_payslips'])) {
						// get the payslip record
						$payslip = $this->pushQuery("a.payslip_month, a.date_log, a.item_id, a.id, a.payslip_year, (SELECT b.name FROM users b WHERE b.item_id = a.employee_id ORDER BY b.id DESC LIMIT 1) AS employee_name, (SELECT b.name FROM users b WHERE b.item_id = a.created_by ORDER BY b.id DESC LIMIT 1) AS created_by", 
						"payslips a", "a.client_id='{$clientId}' AND a.deleted='0' AND a.validated='0' AND a.status='0' AND a.date_log > (NOW() + INTERVAL - 24 HOUR) LIMIT 1");
					
						if(!empty($payslip)) {
							foreach($payslip as $each) {
								$this->db->query("UPDATE payslips SET validated='1', validated_date = now(), status='1' WHERE id='{$each->id}' LIMIT 1");
								$this->db->query("UPDATE accounts_transaction SET state='Approved', validated_date = NOW() WHERE item_id='{$each->item_id}' AND state != 'Approved' LIMIT 3");
							}
						}
					}
				}
			}
			
		} catch(PDOException $e) {
			print $e->getMessage()."\n";
		}
	}

	/**
	 * Automatically update the status of transactions, fees collection and events
	 * 
	 * Disable reversal after every 30 hours
	 * 
	 * @return Bool
	 */
	public function auto_updates() {
		// fees payment reversal disallowed after 12 HOURS
		print "Updating the fees payment history.\n";
        $this->db->query("UPDATE fees_collection SET has_reversal='0' WHERE recorded_date < (NOW() + INTERVAL - 12 HOUR) AND has_reversal='1' ORDER BY id DESC LIMIT {$this->limit}");
        	
        // do same to transactions recorded - auto set it to approved after 12 hours
        print "Updating the accounts transaction logs to set the state as approved.\n";
        $this->db->query("UPDATE accounts_transaction SET state='Approved' WHERE date_created < (NOW() + INTERVAL - 12 HOUR) AND state='Pending' AND status='1' ORDER BY id DESC LIMIT {$this->limit}");

		// update the status of events
		print "Changing the status of recorded events.\n";
        $this->db->query("UPDATE events SET state='Over' WHERE end_date < CURDATE() AND state='Pending' AND status='1' ORDER BY id DESC LIMIT {$this->limit}");

        // users comments is not deletable after 3 hours of posting
        print "Updating the status of user feedbacks.\n";
        $this->db->query("UPDATE users_feedback SET is_deletable='0' WHERE date_created < (NOW() + INTERVAL - 3 HOUR) AND is_deletable='1' ORDER BY id DESC LIMIT {$this->limit}");

        // update the teacher's daily report set. change the status after 24 hours of posting
       	print "Updating the status of user student daily reports.\n";
        $this->db->query("UPDATE daily_reports SET is_deletable='0' WHERE date_created < (NOW() + INTERVAL - 24 HOUR) AND is_deletable='1' ORDER BY id DESC LIMIT {$this->limit}"); 
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
	public function inList($param) {

		if(empty($param)) {
			return $param;
		}

		$params = (is_array($param)) ? $param : [];

		$string = '(';
		foreach($params as $item) {
			$string .= "'{$item}',";
		}
		$string = substr($string, 0, -1);
		$string .= ')';

		return $string; 
	}

	/**
	* Modify the Teacher Class IDs
	* 
	* Get the equivalent class id from the courses ids list and update the teacher class_ids column
	* 
	* @return Bool
	*/
	public function update_teacher_class_ids() {

		try {

			$stmt = $this->db->prepare("SELECT item_id, course_ids, class_ids, name
				FROM users 
				WHERE user_type='teacher' AND user_status='Active' 
				AND LENGTH(course_ids) > 2 AND status='1' 
				AND class_ids IS NULL
				LIMIT 2000
			");
			$stmt->execute();

			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				
				// convert the course id to an array
				$course_id = json_decode($result->course_ids, true);

				// get the class ids 
				$classes = $this->db->query("SELECT b.id, b.item_id, b.name AS class_name, a.name 
					FROM courses a LEFT JOIN classes b ON b.item_id = a.class_id
					WHERE a.id IN {$this->inList($course_id)} LIMIT 100");
				$classes_result = $classes->fetchAll(PDO::FETCH_ASSOC);

				// get the class ids only
				$classes_ids = array_column($classes_result, "item_id");
				$classes_ids = array_unique($classes_ids);

				// update the teacher columns
				$sm = $this->db->prepare("UPDATE users SET class_ids = ? WHERE item_id = ? LIMIT 1");
				$sm->execute([json_encode($classes_ids), $result->item_id]);

				// print a success message information
				print $result->name." class id information was successfully updated.\n";
			}


		} catch(PDOException $e) {}

	}

}

// create new object
$jobs = new Crons;
$jobs->account_status();
$jobs->update_teacher_class_ids();
$jobs->auto_updates();
?>