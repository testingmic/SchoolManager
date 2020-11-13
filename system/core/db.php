<?php
/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		InsuredMedics Plus
 * @subpackage	InsuredMedics Plus Super Class
 * @category	Core Functions
 * @author		Analitica Innovare Dev Team
 */

class Db {
	
	private $medics;
	
	public function __construct() {
		
		$this->hostname = DB_HOST;
		$this->username = DB_USER;
		$this->password = DB_PASS;
		$this->database = DB_NAME;
		
		if($this->medics == null) {
			$this->medics = $this->db_connect($this->hostname, $this->username, $this->password, $this->database);
		}
	}
	public function get_database(){
		return $this->medics;
	}

	private function db_connect($hostname, $username, $password, $database) {
		
		try {
			$this->conn = "mysql:host=$hostname;dbname=$database;charset=utf8mb4";
			
			$medics = new PDO($this->conn, $username, $password);
			$medics->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$medics->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
			$medics->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, TRUE);
			
			return $medics;
			
		} catch(PDOException $e) {
			die("It seems there was an error.  Please refresh your browser and try again. ".$e->getMessage());
		}
		
	}

	public function query($sql) {
		
		try {
					
			$stmt = $this->medics->prepare("$sql");
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $results;
		
		} catch(PDOException $e) {return 0;}
	}

	public function execute($sql) {
		
		try {
			$stmt = $this->medics->prepare("$sql");
			$stmt->execute();
		} catch(PDOException $e) {return 0;}
	}
	
}