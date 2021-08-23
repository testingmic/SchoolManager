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
     * Run the scheduler cron activities
     */
    public function scheduler() {

		try {

			// prepare and execute the statement
			$stmt = $this->db->prepare("SELECT * FROM cron_scheduler WHERE status = ? AND CURRENT_TIME() > TIMESTAMP(active_date) AND cron_type = ? ORDER BY id ASC LIMIT 5");
			$stmt->execute([0, "end_academic_term"]);

			// loop through the result
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

				// if the type is to manage the end of term propagation
                $data_to_import = json_decode($result->query, true);
                $this->end_academic_term_handler($result->item_id, $data_to_import);

				// update the cron status
				$this->db->query("UPDATE cron_scheduler SET date_processed=now(), status='1' WHERE id='{$result->id}' LIMIT 1");

			}

		} catch(PDOException $e) {
			print $e->getMessage();
		}

    }

	/**
	 * Get the Grading System
	 * 
	 * @return Object
	 */
	public function grading_system($clientId, $academic_year, $academic_term) {

		// if either the academic term or year are empty
		if(empty($academic_year) || empty($academic_term)) {
			return [];
		}
		
		// prepare and execute the statement
		$stmt = $this->db->prepare("SELECT
				c.grading AS grading_system, c.structure AS grading_structure, 
				c.show_position, c.show_teacher_name, c.allow_submission
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
	public function client_data($clientId = null) {

		$clientId = !empty($clientId) ? $clientId : (!empty($this->clientId) ? $this->clientId : $this->session->clientId);

		try {

			// prepare and execute the statement
			$stmt = $this->db->prepare("SELECT * FROM clients_accounts WHERE client_id = ? AND client_status = ? LIMIT 1");
			$stmt->execute([$clientId, 1]);
			
			// loop through the list
			$result = $stmt->fetch(PDO::FETCH_OBJ);
			
			// loop through the items and convert into an object
			$result->client_preferences = json_decode($result->client_preferences);
			
			// set this value
			$this->birthday_days_interval = 30;

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

			// get the structure
			$structure = $this->grading_system($clientId, $academic_year, $academic_term);

			// convert to an array
			$result = (array) $result;

			// if the structure is not empty
			if(!empty($structure)) {
				$result = array_merge($result, $structure);
				$result["grading_system"] = json_decode($result["grading_system"]);
				$result["grading_structure"] = json_decode($result["grading_structure"]);
			} else {
				$result["grading_system"] = [];
				$result["grading_structure"] = [];
			}

			// convert to object
			$result = (object) $result;

			return $result;
			
		} catch(PDOException $e) {
			return (object) $e;
		}
	}

	/**
	 * Get the Student Fees
	 * 
	 * @param String $studentId 
	 * @param String $academic_year
	 * @param String $academic_term
	 * 
	 * @return Array
	 */
	private function student_fees($studentId, $academic_year, $academic_term) {

		try {

			$stmt = $this->db->prepare("SELECT a.category_id, a.amount_due, a.amount_paid, a.balance, a.exempted,
					b.name AS category_name, b.amount AS category_default_amount
				FROM fees_payments a 
				LEFT JOIN fees_category b ON b.id = a.category_id
				WHERE a.student_id = ? AND a.status = ? AND a.academic_year = ? AND a.academic_term = ?
			");
			$stmt->execute([$studentId, 1, $academic_year, $academic_term]);
			$result = $stmt->fetchAll(PDO::FETCH_OBJ);

			$data = array();

			// loop through the results list
			foreach($result as $key => $value) {
				// append the list
				$data[$studentId][$value->category_id] = [
					"category_id" => $value->category_id,
					"amount" => [
						"due" => $value->amount_due,
						"paid" => $value->amount_paid,
						"balance" => $value->balance,
                        "exempted" => $value->exempted
					]
				];
			}

			// set the data
			$response = $data;

			return $response;

		} catch(PDOException $e) {}
	}

	/**
	 * Close Academic Term
	 * 
	 * Process the Populating of Data for a new Term
	 * 
	 * @param String $recordId
	 * @param Array $data_to_import
	 * 
	 * @return Bool
	 */
	private function end_academic_term_handler($recordId, $data_to_import) {

		// split the record id for the client id and the record id
		$clientId = explode("_", $recordId)[1];

		// load client data
		$client_data = $this->client_data($clientId);
		$original_client = $client_data;

		try {

            $this->db->beginTransaction();

            // confirm that the status is propagation of record
            if($client_data->client_state === "Propagation") {

                // academics information
                $preferences = $client_data->client_preferences;
                $academics = $preferences->academics;
                
                // set variables for the academic year and term
                $academic_year = $academics->academic_year;
                $academic_term = $academics->academic_term;
                $next_academic_year = $academics->next_academic_year;
                $next_academic_term = $academics->next_academic_term;

                /**
                 * STEP ONE:: 
                 * 
                 * GET THE LIST OF ALL STUDENTS FOR THE CURRENT ACADEMIC YEAR / TERM
                 */
                $list_users = $this->db->prepare("SELECT a.*,
                        (
                            SELECT 
                                CONCAT (
                                    b.is_promoted,'_',b.promote_to,'_',
                                    (
                                        SELECT d.id FROM classes d WHERE d.item_id = b.promote_to LIMIT 1
                                    )
                                )
                            FROM 
                                promotions_log b 
                            LEFT JOIN promotions_history c ON c.history_log_id = b.history_log_id
                            WHERE 
                                b.academic_year = a.academic_year AND b.academic_term = a.academic_term AND 
                                a.item_id = b.student_id AND c.status='Processed'
                        ) AS is_promoted
                    FROM 
                        users a 
                    WHERE 
                        a.academic_year = ? AND a.academic_term = ? AND a.user_type = ? AND a.user_status = ? AND a.client_id = ?
                    LIMIT {$this->limit}"
                );
                $list_users->execute([$academic_year, $academic_term, "student", "Active", $clientId]);
                $students_list = $list_users->fetchAll(PDO::FETCH_ASSOC);

                // variables
                $students_query_string = "";
                $students_query_array = array();

                // loop through the students list
                foreach($students_list as $ikey => $student) {
                    
                    // get the keys
                    $columns = array_keys($student);

                    // format the student promotion information
                    $is_promoted = !empty($student["is_promoted"]) ? explode("_", $student["is_promoted"]) : null;
                    
                    // append new variables
                    $student["class_id"] = (!empty($is_promoted) && ($is_promoted[0] == 1)) ? $is_promoted[2] : $student["class_id"];
                    $student["academic_year"] = $next_academic_year;
                    $student["academic_term"] = $next_academic_term;
                    $student["date_created"] = date("Y-m-d H:i:s");
                    $student["last_login"] = date("Y-m-d H:i:s");
                    $student["verified_date"] = !empty($student["verified_date"]) ? $student["verified_date"] : date("Y-m-d H:i:s");
                    $student["last_visited_page"] = "{{APPURL}}dashboard";

                    // get the new values
                    $values = array_values($student);
                    $last_key = count($values)-1;

                    // get the finances owed by the user
                    $student_fees = $this->student_fees($student["item_id"], $academic_year, $academic_term);

                    // begin the student insert string
                    $query_string = "INSERT INTO users SET ";
                    
                    // loop through the columns
                    foreach($columns as $key => $column) {
                        // exempt some data from the query
                        if(!in_array($key, [0, $last_key])) {
                            $query_string .= "{$column}='{$values[$key]}',";
                        }
                    }
                    $students_query_string .= trim($query_string, ",").";";

                    $students_query_array[] = $student_fees;

                }

                // initial 
                $school_fees = array();
                $count = 0;

                // algorithm to calculate how much money the school should have received 
                foreach($students_query_array as $key => $value) {
                    foreach($value as $ikey => $ivalue) {
                        foreach($ivalue as $ivkey => $ivvalue) {
                            
                            // if the fee is exempted
                            if($ivvalue["amount"]["exempted"] == 1) {
                                $school_fees[$ivvalue["category_id"]]["exempted"] = isset($school_fees[$ivvalue["category_id"]]["exempted"]) ? ($school_fees[$ivvalue["category_id"]]["exempted"] + $ivvalue["amount"]["balance"]) : $ivvalue["amount"]["balance"];
                            }
                            $school_fees[$ivvalue["category_id"]]["due"] = isset($school_fees[$ivvalue["category_id"]]["due"]) ? ($school_fees[$ivvalue["category_id"]]["due"] + $ivvalue["amount"]["due"]) : $ivvalue["amount"]["due"];
                            $school_fees[$ivvalue["category_id"]]["paid"] = isset($school_fees[$ivvalue["category_id"]]["paid"]) ? ($school_fees[$ivvalue["category_id"]]["paid"] + $ivvalue["amount"]["paid"]) : $ivvalue["amount"]["paid"];
                            $school_fees[$ivvalue["category_id"]]["balance"] = isset($school_fees[$ivvalue["category_id"]]["balance"]) ? ($school_fees[$ivvalue["category_id"]]["balance"] + $ivvalue["amount"]["balance"]) : $ivvalue["amount"]["balance"];
                        }
                    }
                }

                // total balance
                $total_due = array_sum(array_column($school_fees, "due"));
                $total_paid = array_sum(array_column($school_fees, "paid"));
                $total_discount = array_sum(array_column($school_fees, "exempted"));
                $total_balance = array_sum(array_column($school_fees, "balance"));
                $total_actual_balance = $total_balance - $total_discount;

                // print "AMOUNT DUE: " . $total_due . "\n";
                // print "AMOUNT PAID: " . $total_paid . "\n";
                // print "AMOUNT DISCOUNT: " . $total_discount . "\n";
                // print "AMOUNT BALANCE: " . $total_balance . "\n";
                // print "AMOUNT ACTUAL BALANCE: " . $total_actual_balance . "\n";
                // exit;

                $school_fees_summary = [
                    "total_due" => $total_due,
                    "total_paid" => $total_paid,
                    "total_balance" => $total_balance
                ];

                // school fees log information
                $school_fees_log = [
                    "fees_log" => $school_fees,
                    "summary" => $school_fees_summary
                ];

                // get only fees in the array list that the student has a balance
                $student_ownings = array();

                // algorithm to calculate how much money the school should have received 
                foreach($students_query_array as $key => $value) {
                    foreach($value as $ikey => $ivalue) {
                        foreach($ivalue as $ivkey => $ivvalue) {
                            if(round($ivvalue["amount"]["balance"]) > 0) {
                                $student_ownings[$ikey][$ivvalue["category_id"]] = $ivvalue["amount"]["balance"];
                            }
                        }
                    }
                }

                // get fees category
                $fees_category = $this->db->prepare("SELECT * FROM fees_category WHERE client_id = ? AND status = ? LIMIT 30");
                $fees_category->execute([$clientId, 1]);
                $fees_category_log = $fees_category->fetchAll(PDO::FETCH_OBJ);

                // EXECUTE THE STUDENTS LIST
                if(in_array("students", $data_to_import)) {
                    $this->db->query($students_query_string);
                }

                // UPDATE THE STUDENTS FEES DATA FOR THE TERM
                $update_query = $this->db->prepare("UPDATE clients_terminal_log SET fees_log = ?, arrears_log = ?, fees_category_log = ? WHERE student_id = ? AND academic_year = ? AND academic_term = ? LIMIT 1");
                $insert_query = $this->db->prepare("INSERT INTO clients_terminal_log SET client_id = ?, fees_log = ?, arrears_log = ?, fees_category_log = ?, student_id = ?, academic_year = ?, academic_term = ?");
                
                // Loop through the Students Fees Log List
                foreach($students_query_array as $key => $value) {
                    // loop through the students fees payments list
                    foreach($value as $ikey => $ivalue) {
                        // confirm that the owings already exists or not
                        $owing = isset($student_ownings[$ikey]) ? $student_ownings[$ikey] : (object) array();
                        // confirm that the record already exists or not
                        if($this->fees_history_log_exist($ikey, $academic_year, $academic_term)) {
                            $update_query->execute([json_encode($value), json_encode($owing), json_encode($fees_category_log), $ikey, $academic_year, $academic_term]);
                        } else {
                            $insert_query->execute([$clientId, json_encode($value), json_encode($owing), json_encode($fees_category_log), $ikey, $academic_year, $academic_term]);
                        }
                    }
                }

                // new query string for a school
                $update_query = $this->db->prepare("UPDATE clients_terminal_log SET fees_log = ?, fees_category_log = ? WHERE client_id = ? AND academic_year = ? AND academic_term = ? AND log_type = ? LIMIT 1");
                $insert_query = $this->db->prepare("INSERT INTO clients_terminal_log SET client_id = ?, fees_log = ?, fees_category_log = ?, academic_year = ?, academic_term = ?, log_type = ?");

                // confirm if the school fees log already exists
                if($this->fees_history_log_exist($clientId, $academic_year, $academic_term, "school")) {
                    $update_query->execute([json_encode($school_fees_log), json_encode($fees_category_log), $clientId, $academic_year, $academic_term, "school"]);
                } else {
                    $insert_query->execute([$clientId, json_encode($school_fees_log), json_encode($fees_category_log), $academic_year, $academic_term, "school"]);
                }

                // set the new term in the clients data table
                $preferences->academics->academic_year = $next_academic_year;
                $preferences->academics->academic_term = $next_academic_term;
                $preferences->academics->term_starts = $preferences->academics->next_term_starts;
                $preferences->academics->term_ends = $preferences->academics->next_term_ends;

                // unset the next academic term and year
                $preferences->academics->next_academic_year = "";
                $preferences->academics->next_academic_term = "";
                $preferences->academics->next_term_starts = "";
                $preferences->academics->next_term_ends = "";

                // IMPORT THE COURSES LIST
                if(in_array("courses", $data_to_import)) {

                    // GET THE ACTUAL COURSES LIST
                    $list_courses = $this->db->prepare("SELECT a.*
                        FROM 
                            courses a 
                        WHERE 
                            a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
                        LIMIT {$this->limit}"
                    );
                    $list_courses->execute([$academic_year, $academic_term, $clientId, 1]);
                    $courses_list = $list_courses->fetchAll(PDO::FETCH_ASSOC);

                    // variables
                    $courses_query_string = "";
                    $courses_plan_query_string = "";
                    $courses_resource_query_string = "";

                    // loop through the courses list
                    foreach($courses_list as $ikey => $course) {
                        
                        // get the keys
                        $columns = array_keys($course);
                        
                        // append new variables
                        $course["academic_year"] = $next_academic_year;
                        $course["academic_term"] = $next_academic_term;
                        $course["date_created"] = date("Y-m-d H:i:s");
                        $course["date_updated"] = date("Y-m-d H:i:s");

                        // get the new values
                        $values = array_values($course);
                        $last_key = count($values)-1;

                        // begin the student insert string
                        $query_string = "INSERT INTO courses SET ";
                        
                        // loop through the columns
                        foreach($columns as $key => $column) {
                            // exempt some data from the query
                            if(!in_array($key, [0, $last_key])) {
                                $query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) ? "{$column}=NULL," : "{$column}='{$values[$key]}',";
                            }
                        }
                        $courses_query_string .= trim($query_string, ",").";";
                    }
                    
                    // check for empty string
                    if(strlen($courses_query_string) > 20) {
                        $this->db->query($courses_query_string);
                    }
                }
                
                // IMPORT THE COURSES LIST
                if(in_array("courses_plan", $data_to_import)) {

                    // GET THE LIST OF COURSE PLAN
                    $course_plan = $this->db->prepare("SELECT a.*
                        FROM 
                            courses_plan a 
                        WHERE 
                            a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND status = ?
                        LIMIT {$this->limit}"
                    );
                    $course_plan->execute([$academic_year, $academic_term, $clientId, 1]);
                    $course_plan_list = $course_plan->fetchAll(PDO::FETCH_ASSOC);

                    // loop through the course plans list
                    foreach($course_plan_list as $ikey => $course_plan_item) {
                        
                        // get the keys
                        $columns = array_keys($course_plan_item);
                        
                        // append new variables
                        $course_plan_item["start_date"] = NULL;
                        $course_plan_item["end_date"] = NULL;
                        $course_plan_item["academic_year"] = $next_academic_year;
                        $course_plan_item["academic_term"] = $next_academic_term;
                        $course_plan_item["date_created"] = date("Y-m-d");
                        $course_plan_item["date_updated"] = date("Y-m-d H:i:s");

                        // get the new values
                        $values = array_values($course_plan_item);
                        $last_key = count($values)-1;

                        // begin the student insert string
                        $query_string = "INSERT INTO courses_plan SET ";
                        
                        // loop through the columns
                        foreach($columns as $key => $column) {
                            // exempt some data from the query
                            if(!in_array($key, [0, $last_key])) {
                                $query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : "{$column}='{$values[$key]}',";
                            }
                        }
                        $courses_plan_query_string .= trim($query_string, ",").";";
                    }
                    
                    // run the query for the course plan
                    if(strlen($courses_plan_query_string) > 20) {
                        $this->db->query($courses_plan_query_string);
                    }

                }

                // IMPORT THE COURSES LIST
                if(in_array("courses_resource", $data_to_import)) {

                    // LOAD THE COURSE RESOURCES LIST
                    $list_course_resources = $this->db->prepare("SELECT a.*
                        FROM 
                            courses_resource_links a 
                        WHERE 
                            a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
                        LIMIT {$this->limit}"
                    );
                    $list_course_resources->execute([$academic_year, $academic_term, $clientId, 1]);
                    $course_resources_list = $list_course_resources->fetchAll(PDO::FETCH_ASSOC);

                    // loop through the course resources list
                    foreach($course_resources_list as $ikey => $course_resource) {
                        
                        // get the keys
                        $columns = array_keys($course_resource);
                        
                        // append new variables
                        $course_resource["academic_year"] = $next_academic_year;
                        $course_resource["academic_term"] = $next_academic_term;
                        $course_resource["date_created"] = date("Y-m-d");

                        // get the new values
                        $values = array_values($course_resource);
                        $last_key = count($values)-1;

                        // begin the student insert string
                        $query_string = "INSERT INTO courses_resource_links SET ";
                        
                        // loop through the columns
                        foreach($columns as $key => $column) {
                            // exempt some data from the query
                            if(!in_array($key, [0, $last_key])) {
                                $query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : "{$column}='{$values[$key]}',";
                            }
                        }
                        $courses_resource_query_string .= trim($query_string, ",").";";
                    }

                    // run the query for the course resources
                    if(strlen($courses_resource_query_string) > 20) {
                        $this->db->query($courses_resource_query_string);
                    }

                }
        
                // IMPORT THE FEES ALLOCATIONS LIST
                if(in_array("fees_allocation", $data_to_import)) {

                    // init variables
                    $fees_allocation_query_string = "";
                    $student_fees_allocation_query_string = "";

                    // LOAD THE COURSE RESOURCES LIST
                    $fees_allocation = $this->db->prepare("SELECT a.*
                        FROM 
                            fees_allocations a 
                        WHERE 
                            a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
                        LIMIT {$this->limit}"
                    );
                    $fees_allocation->execute([$academic_year, $academic_term, $clientId, 1]);
                    $fees_allocation_list = $fees_allocation->fetchAll(PDO::FETCH_ASSOC);

                    // loop through the course resources list
                    foreach($fees_allocation_list as $ikey => $allocation) {
                        
                        // get the keys
                        $columns = array_keys($allocation);
                        
                        // append new variables
                        $allocation["academic_year"] = $next_academic_year;
                        $allocation["academic_term"] = $next_academic_term;
                        $allocation["date_created"] = date("Y-m-d H:i:s");

                        // get the new values
                        $values = array_values($allocation);
                        $last_key = count($values)-1;

                        // begin the fees_allocations insert string
                        $query_string = "INSERT INTO fees_allocations SET ";
                        
                        // loop through the columns
                        foreach($columns as $key => $column) {
                            // exempt some data from the query
                            if(!in_array($key, [0, $last_key])) {
                                $query_string .= "{$column}='{$values[$key]}',";
                            }
                        }
                        $fees_allocation_query_string .= trim($query_string, ",").";";
                    }

                    // LOAD THE COURSE RESOURCES LIST
                    $fees_allocation = $this->db->prepare("SELECT a.*
                        FROM 
                            fees_payments a 
                        WHERE 
                            a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
                        LIMIT {$this->limit}"
                    );
                    $fees_allocation->execute([$academic_year, $academic_term, $clientId, 1]);
                    $fees_allocation_list = $fees_allocation->fetchAll(PDO::FETCH_ASSOC);

                    // loop through the course resources list
                    foreach($fees_allocation_list as $ikey => $allocation) {
                        
                        // get the keys
                        $columns = array_keys($allocation);
                        
                        // append new variables
                        $allocation["academic_year"] = $next_academic_year;
                        $allocation["academic_term"] = $next_academic_term;
                        $allocation["paid_status"] = 0;
                        $allocation["editable"] = 0;
                        $allocation["amount_paid"] = 0.00;
                        $allocation["last_payment_id"] = NULL;
                        $allocation["last_payment_date"] = NULL;
                        $allocation["balance"] = $allocation["amount_due"];
                        $allocation["date_created"] = date("Y-m-d H:i:s");
                        $allocation["checkout_url"] = $this->random_string(22);

                        // get the new values
                        $values = array_values($allocation);
                        $last_key = count($values)-1;

                        // begin the fees_allocations insert string
                        $query_string = "INSERT INTO fees_payments SET ";
                        
                        // loop through the columns
                        foreach($columns as $key => $column) {
                            // exempt some data from the query
                            if(!in_array($key, [0, $last_key])) {
                                $query_string .= in_array($column, ["last_payment_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : "{$column}='{$values[$key]}',";
                            }
                        }
                        $student_fees_allocation_query_string .= trim($query_string, ",").";";
                    }

                    // check for empty string
                    if(strlen($fees_allocation_query_string) > 20) {
                        $this->db->query($fees_allocation_query_string);
                    }
                    if(strlen($student_fees_allocation_query_string) > 20) {
                        $this->db->query($student_fees_allocation_query_string);
                    }
                    
                }

                // update the clients preferences
                $stmt = $this->db->prepare("UPDATE clients_accounts SET client_preferences = ?, client_state = ? WHERE client_id = ? LIMIT 1");
                $stmt->execute([json_encode($preferences), "Complete", $clientId]);

            }

            // $this->db->commit();

            print "Processing of Academic Term Data was successful.\n";

		} catch(PDOException $e) {
			$this->db->rollBack();
			print "{$e->getMessage()}\n";
		}

	}

}

// create new object
$jobs = new Crons;
$jobs->scheduler();
?>