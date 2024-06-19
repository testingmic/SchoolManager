<?php
 
class Crons {

	private $db;
	private $userAccount;
	private $mailAttachment = array();
	private $rootUrl;
	private $clientId;
	private $ini_data;
	private $limit = 2000;
	private $siteName = "MySchoolGH - EmmallexTech.Com";

	public function __construct() {
		// INI FILE
		$this->ini_data = parse_ini_file("db.ini");

		// set some more variables
		$this->baseUrl = $this->ini_data["base_url"];
		$this->rootUrl = $this->ini_data["root_url"];
		$this->dbConn();		

		require $this->rootUrl."system/libraries/phpmailer.php";
		require $this->rootUrl."system/libraries/smtp.php";
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
	 * account_status
	 * 
	 * @param String $clientId
	 * 
	 * @return Object
	 */
	public function account_status() {

		try {

			$stmt = $this->db->prepare("SELECT id, client_name, client_preferences FROM clients_accounts WHERE client_status = ? AND client_state = ? AND client_id != ? LIMIT 5");
			$stmt->execute([1, "Active", "MSGH0001"]);
			
			// loop through the list
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				// loop through the items and convert into an object
				$prefs = json_decode($result->client_preferences);

                $expiry = strtotime($prefs->account->expiry);
                $current_time = time();
                
                if($current_time > $expiry) {
                    $this->db->query("UPDATE clients_accounts SET client_state='Expired' WHERE id='{$result->id}' LIMIT 1");
                    print "The Account of {$result->client_name} has Expired\n";
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
		print "Updating the fees payment history.\n";
		// fees payment reversal disallowed after 24 HOURS
        $this->db->query("UPDATE fees_collection SET has_reversal='0' WHERE recorded_date < (NOW() + INTERVAL - 24 HOUR) AND has_reversal='1' LIMIT {$this->limit}");
        	
        // do same to transactions recorded - auto set it to approved after 24 hours
        print "Updating the accounts transaction logs to set the state as approved.\n";
        $this->db->query("UPDATE accounts_transaction SET state='Approved' WHERE date_created < (NOW() + INTERVAL - 24 HOUR) AND state='Pending' AND status='1' LIMIT {$this->limit}");

		// update the status of events
		print "Changing the status of recorded events.\n";
        $this->db->query("UPDATE events SET state='Over' WHERE end_date < CURDATE() AND state='Pending' AND status='1' LIMIT {$this->limit}");

        // users comments is not deletable after 3 hours of posting
        print "Updating the status of user feedbacks.\n";
        $this->db->query("UPDATE users_feedback SET is_deletable='0' WHERE date_created < (NOW() + INTERVAL - 3 HOUR) AND is_deletable='1' LIMIT {$this->limit}");
	}

}

// create new object
$jobs = new Crons;
$jobs->auto_updates();
$jobs->account_status();
?>