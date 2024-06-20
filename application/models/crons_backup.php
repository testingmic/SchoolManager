<?php

class Backup {

    // global tables
    private $ini_data;
    protected $global_tables;
    public $baseUrl;
    public $rootUrl;
    public $db;

    // construct
    public function __construct() {

        // INI FILE
        $this->ini_data = parse_ini_file("db.ini");

        // set some more variables
        $this->baseUrl = $this->ini_data["base_url"];
        $this->rootUrl = $this->ini_data["root_url"];
        
    	$this->global_tables = [
    		"banks_list", "blood_groups", "clients_accounts", "clients_packages", "contact", "country", 
    		"currency", "cron_scheduler", "knowledge_base", "sms_packages", "users_api_endpoints", 
    		"users_api_keys", "users_api_queries", "users_api_requests", "users_types", "guardian_relation",
    		"users_access_attempt", "users_notification_types", "religions", "users_gender",
    		"e_learning_comments", "e_learning_timer", "e_learning_views", "grading_remarks_category",
            "users_activity_logs"
    	];

        // CONNECT TO THE DATABASE
    	$HOST = $this->ini_data['hostname'];
    	$USER = $this->ini_data['username'];
    	$PASSWORD = $this->ini_data['password'];
    	$DB = $this->ini_data['database'];

    	$this->db = new mysqli($HOST, $USER, $PASSWORD, $DB);
    }

    /**
     * Run the Query to Get the Tables
     *
     * Create the Backup of the tables
     **/
    public function run() {

    	// init values
    	$clients_db = array();
    	$tables_array = array();
        $clients_db = "";

    	// get the list of all tables
        $query_list = $this->db->query("SHOW TABLES");

        // loop through the tables list
        while($table = $query_list->fetch_row()) {
        	foreach($table as $key => $value) {
        		// dont add tables that starts with wn
        		if( (strpos($value, "wn_") === false) && (!in_array($value, $this->global_tables))) {
		            // append to the tables list
		            $tables_array[] = $value;
		        }
            }
        }

    	// initialize the client data content
    	$clients_db = [];

        // loop through the tables client tables
        $clients_list = $this->db->query("SELECT * FROM clients_accounts WHERE client_state = 'Active'");

        // loop through the clients list
        try {

            while($client = $clients_list->fetch_array(MYSQLI_ASSOC)) {

                // loop through the tables list
                foreach($tables_array as $table) {
                    try {
                        // select from all tables where the client_id matches the existing one
                        $stmt = $this->db->query("SELECT * FROM {$table} WHERE client_id='{$client["client_id"]}' LIMIT 25000");

                        while($result = $stmt->fetch_array(MYSQLI_ASSOC)) {
                            $key = isset($result["id"]) ? $result["id"] : $result["item_id"];
                            $clients_db[$client["client_id"]][$table][$key] = $result;
                        }
                    } catch(\Exception $e) {}
                }

            }
            
        } catch(\Exception $e) {}

        // loop through each client data
        foreach($clients_db as $client => $data) {
            $today_file = "{$this->rootUrl}application/logs/json/{$client}_".date("Y-m-d").".json";
            $ft = fopen($today_file, "w");
            fwrite($ft, json_encode($data));
        }

    }

}

$backup = new Backup;
$backup->run();