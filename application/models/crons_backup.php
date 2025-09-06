<?php

class Backup {

    // global tables
    private $ini_data;
    protected $global_tables;
    public $baseUrl;
    public $rootUrl;
    public $systemRoot;
    public $db;

    // construct
    public function __construct() {

        // INI FILE
        $this->ini_data = parse_ini_file(dirname(dirname(__DIR__)) . "/db.ini");

        // set some more variables
        $this->baseUrl = $this->ini_data["base_url"];
        $this->rootUrl = $this->ini_data["root_url"];
        $this->systemRoot = $this->ini_data["system_root"];
        
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
        		if( (strpos($value, "wn_") === false) && (!in_array($value, $this->global_tables))) {
		            $tables_array[] = $value;
		        }
            }
        }

        print_r($tables_array);
        die();

    	// initialize the client data content
    	$clients_db = [];

        try {
            foreach($tables_array as $table) {
                try {
                    $stmt = $this->db->query("SELECT * FROM {$table}");
                    while($result = $stmt->fetch_array(MYSQLI_ASSOC)) {
                        $key = isset($result["id"]) ? $result["id"] : (
                            isset($result["item_id"]) ? $result["item_id"] : (
                                $result["unique_id"] ?? null
                            )
                        );
                        $clients_db[$table][$key] = $result;
                    }
                } catch(\Exception $e) {
                    print $e->getMessage()."\n";
                }
            }
        } catch(\Exception $e) {
            print $e->getMessage()."\n";
        }

        // loop through each client data
        $today_file = "{$this->systemRoot}backups/myschool/myschoolgh_".date("Y-m-d_H").".json";
        $ft = fopen($today_file, "w");
        fwrite($ft, json_encode($clients_db));

    }

}

$backup = new Backup;
$backup->run();