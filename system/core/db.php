<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		Insuredmyschoolgh Plus
 * @subpackage	Insuredmyschoolgh Plus Super Class
 * @category	Core Functions
 * @author		Analitica Innovare Dev Team
 */

class Db {
	
	private $conn;
	private $dbType;
	private $myschoolgh;
	private $hostname;
	private $username;
	private $password;
	private $database;

	public function __construct() {
		
		$this->hostname = DB_HOST;
		$this->username = DB_USER;
		$this->password = DB_PASS;
		$this->database = DB_NAME;
		$this->dbType = DB_TYPE;
		
		if($this->myschoolgh == null) {
			$this->myschoolgh = $this->db_connect($this->hostname, $this->username, $this->password, $this->database);
		}
	}
	public function get_database(){
		return $this->myschoolgh;
	}

	/**
	 * SQLite Connection
	 * 
	 * Connect to the SQLite database
	 * 
	 * @param string $hostname
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 * @return object
	 */
	private function sqlite_connect($database) {

		// Connect to the SQLite database
		$myschoolgh = new PDO("sqlite:{$database}.db");
		$myschoolgh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$myschoolgh->query("PRAGMA journal_mode = WAL");
		$myschoolgh->query("PRAGMA synchronous = NORMAL");
		$myschoolgh->query("PRAGMA locking_mode = NORMAL");
		$myschoolgh->query("PRAGMA busy_timeout = 5000");
		$myschoolgh->query("PRAGMA cache_size = -16000");

		return $myschoolgh;

	}

	/**
	 * Database Connection
	 * 
	 * Connect to the database
	 * 
	 * @param string $hostname
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 * @return object
	 */
	private function db_connect($hostname, $username, $password, $database) {
		
		try {

			// check the database type
			if($this->dbType == "sqlite") {
				return $this->sqlite_connect($database);
			}

			$this->conn = "mysql:host=$hostname;dbname=$database;charset=utf8mb4";
			
			$myschoolgh = new PDO($this->conn, $username, $password);
			$myschoolgh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$myschoolgh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
			$myschoolgh->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, TRUE);
			
			return $myschoolgh;
			
		} catch(PDOException $e) {
			die("It seems there was an error.  Please refresh your browser and try again. ".$e->getMessage());
		}
		
	}

	/**
	 * Database Query
	 * 
	 * Execute a query
	 * 
	 * @param string $sql
	 * @return array
	 */
	public function query($sql) {
		
		try {
					
			$stmt = $this->myschoolgh->prepare("$sql");
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		
		} catch(PDOException $e) {return 0;}
	}

	/**
	 * Execute a query
	 * 
	 * @param string $sql
	 * @return array
	 */
	public function execute($sql) {
		
		try {
			$stmt = $this->myschoolgh->prepare("$sql");
			$stmt->execute();
		} catch(PDOException $e) {return 0;}
	}
	
}