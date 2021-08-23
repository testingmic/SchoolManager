<?php
 
class Crons {

	private $db;
	private $userAccount;
	private $mailAttachment = array();
	private $rootUrl;
	private $clientId;
	private $limit = 2000;
	private $siteName = "MySchoolGH - EmmallexTech.Com";

	public function __construct() {
		$this->baseUrl = "https://app.myschoolgh.com/";
		$this->rootUrl = "/home/mineconr/app.myschoolgh.com/";
		$this->dbConn();

		$this->rootUrl = "/var/www/html/myschool_gh/";

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
			'hostname' => "localhost",
			'database' => "mineconr_school",
			'username' => "mineconr_school",
			'password' => "YdwQLVx4vKU_"
		);

		$connectionArray = array(
			'hostname' => "localhost",
			'database' => "myschoolgh",
			'username' => "newuser",
			'password' => "password"
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
			print $e->getMessage();
		}
	}

}

// create new object
$jobs = new Crons;
$jobs->account_status();
?>