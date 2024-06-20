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
		
		if($this->myschoolgh == null) {
			$this->myschoolgh = $this->db_connect($this->hostname, $this->username, $this->password, $this->database);
		}
	}
	public function get_database(){
		return $this->myschoolgh;
	}

	private function db_connect($hostname, $username, $password, $database) {
		
		try {
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

	public function query($sql) {
		
		try {
					
			$stmt = $this->myschoolgh->prepare("$sql");
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		
		} catch(PDOException $e) {return 0;}
	}

	public function execute($sql) {
		
		try {
			$stmt = $this->myschoolgh->prepare("$sql");
			$stmt->execute();
		} catch(PDOException $e) {return 0;}
	}
	
}