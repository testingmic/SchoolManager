<?php
 
class Alter {

	private $db;
	private $baseUrl;
	private $rootUrl;
	private $clientId;

	public function __construct() {
		$this->baseUrl = "https://app.myschoolgh.com/";
		$this->rootUrl = "/home/mineconr/app.myschoolgh.com/";
		$this->dbConn();

		$this->rootUrl = "C:\/xampp\/htdocs\/myschoolgh/";
	}

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
			'username' => "root",
			'password' => ""
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

    public function execute() {

        $tables_list = $this->db->prepare("SHOW TABLES");
        $tables_list->execute();

        while($table = $tables_list->fetch(PDO::FETCH_ASSOC)) {
            foreach($table as $key => $value) {
                $this->db->query("ALTER TABLE {$value} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                print "{$value} successfully converted.\n";
            }
        }

    }
}
$alter = new Alter;
$alter->execute();