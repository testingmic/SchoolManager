<?php
 
class Crons {

	private $db;
	private $userAccount;
	private $mailAttachment = array();
	private $rootUrl;
	private $ini_data;
	private $clientId;
	private $limit = 5000;
    private $baseUrl;
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
     * Run the scheduler cron activities
     */
    public function scheduler() {

		try {

            print "Runing Cron Activity @ ".date("Y-m-d h:i:sA")."\n";

			// prepare and execute the statement
			$stmt = $this->db->prepare("SELECT * FROM cron_scheduler WHERE status = ? AND CURRENT_TIME() > TIMESTAMP(active_date) AND cron_type = ? ORDER BY id ASC LIMIT 5");
			$stmt->execute([0, "end_academic_term"]);

			// loop through the result
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                print "Cron JOB ID {$result->item_id} found and currently been processed.\n";

				// if the type is to manage the end of term propagation
                $data_to_import = json_decode($result->query, true);
                $this->end_academic_term_handler($result->item_id, $data_to_import);

				// update the cron status
				$this->db->query("UPDATE cron_scheduler SET date_processed=now(), status='1' WHERE id='{$result->id}' LIMIT 1");
			}
            
            print "Runing Cron Activity Ended @ ".date("Y-m-d h:i:sA")."\n";

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

		try {

			// prepare and execute the statement
			$stmt = $this->db->prepare("SELECT * FROM clients_accounts WHERE client_id = ? AND client_status = ? LIMIT 1");
			$stmt->execute([$clientId, 1]);
			
			// loop through the list
			$result = $stmt->fetch(PDO::FETCH_OBJ);
			
			// loop through the items and convert into an object
			$result->client_preferences = json_decode($result->client_preferences);
			
			// set this value
			$birthday_days_interval = 30;

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
				WHERE a.student_id = ? AND a.status = ? AND a.academic_year = ? AND a.academic_term = ? LIMIT 50
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
                        "exempted" => (int) $value->exempted
					]
				];
			}

			// set the data
			$response = $data;

			return $response;

		} catch(PDOException $e) {}
	}

	/**
	 * Fees History Log Check
	 * 
	 * @param String $clientId
	 * @param String $academic_year
	 * @param String $academic_term
	 * 
	 * @return Bool
	 */
	private function client_fees_history_log_exist($clientId, $academic_year, $academic_term) {

		try {

			// prepare and execute the statement
			$stmt = $this->db->prepare("SELECT id FROM clients_terminal_log WHERE client_id = ? AND academic_year = ? AND academic_term = ? LIMIT 1");
			$stmt->execute([$clientId, $academic_year, $academic_term]);

			return $stmt->rowCount();

		} catch(PDOException $e) {
			return false;
		}
	}

	/**
	 * Fees History Log Check
	 * 
	 * @param String $clientId
	 * @param String $studentId
	 * 
	 * @return mixed
	 */
	private function student_fees_history_log_exist($studentId, $clientId) {

		try {

			// prepare and execute the statement
			$stmt = $this->db->prepare("SELECT arrears_details, arrears_category, arrears_total FROM users_arrears WHERE client_id = ? AND student_id = ? LIMIT 1");
			$stmt->execute([$clientId, $studentId]);

			return $stmt->fetch(PDO::FETCH_OBJ);

		} catch(PDOException $e) {
			return false;
		}
	}

    /**
     * Append Fees Owings
     * 
     * Going to Join the Two Arrays Together
     * 
     * @return Array
     */
    private function append_fees_details($current, $previous) {
        $new_array = [];
        foreach($previous as $key => $value) {
            $new_array[$key] = $value;
        }
        foreach($current as $key => $value) {
            $new_array[$key] = $value;
        }
        return $new_array;
    }

    /**
     * Append Fees Owings
     * 
     * Going to Join the Two Arrays Together
     * 
     * @return Array
     */
    private function append_fees_category($current) {
        $new_array = [];
        foreach($current as $key => $value) {
            foreach($value as $ikey => $ivalue) {
                $new_array[$ikey] = isset($new_array[$ikey]) ? ($new_array[$ikey] + $ivalue) : $ivalue;
            }
        }
        return $new_array;
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

            print "Inside the End Academic Term Handler.\n";
            print "Checking if the school is currently in the propagation state.\n";

            // begin transaction
            $this->db->beginTransaction();

            // confirm that the status is propagation of record
            if($client_data->client_state === "Propagation") {

                // academics information
                $preferences = $client_data->client_preferences;
                $academics = $preferences->academics;

                print "Successful. Assign the current academic year and term variables.\n";
                
                // set variables for the academic year and term
                $academic_year = $academics->academic_year;
                $academic_term = $academics->academic_term;
                $next_academic_year = $academics->next_academic_year;
                $next_academic_term = $academics->next_academic_term;

                print "Load the list of students that were added under the current academic year and term.\n";
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
                                b.academic_year = ? AND b.academic_term = ? AND 
                                a.item_id = b.student_id AND c.status='Processed'
                        ) AS is_promoted
                    FROM 
                        users a 
                    WHERE 
                        a.user_type = ? AND a.user_status IN ('Graduated','Active','Transferred') AND a.client_id = ? AND a.status = ?
                    LIMIT {$this->limit}"
                );
                $list_users->execute([$academic_year, $academic_term, "student", $clientId, 1]);
                $students_list = $list_users->fetchAll(PDO::FETCH_ASSOC);

                // variables
                $students_query_string = "";
                $students_query_array = array();
                $clientDate_Created = date("Y-m-d", strtotime($client_data->date_created));

                print "Loop through the students list and assign new variables for each student.\n";
                print "Replace empty variables with the correct ones eg. Empty date fields with the current date.\n";
                print "Processing of students record began @ ".date("Y-m-d h:i:sA").".\n";
                // loop through the students list
                foreach($students_list as $ikey => $student) {
                    
                    // get the finances owed by the user
                    $student_fees = $this->student_fees($student["item_id"], $academic_year, $academic_term);

                    $students_query_array[] = $student_fees;

                }
                print "Processing of students record @ ".date("Y-m-d h:i:sA").".\n";

                // initial 
                $school_fees = array();
                
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

                print "Get the fees allocated to each student for the current term. Look out for the balance left\n";

                // total balance
                $total_due = array_sum(array_column($school_fees, "due"));
                $total_paid = array_sum(array_column($school_fees, "paid"));
                $total_discount = array_sum(array_column($school_fees, "exempted"));
                $total_balance = array_sum(array_column($school_fees, "balance"));
                $total_actual_balance = $total_balance - $total_discount;

                $school_fees_summary = [
                    "total_due" => number_format($total_due, 2),
                    "total_paid" => number_format($total_paid, 2),
                    "total_balance" => number_format($total_balance, 2),
                    "total_actual_balance" => number_format($total_actual_balance, 2),
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
                            if((round($ivvalue["amount"]["balance"]) > 0) && ($ivvalue["amount"]["exempted"] !== 1)) {
                                $student_ownings[$ikey][$ivvalue["category_id"]] = $ivvalue["amount"]["balance"];
                            }
                        }
                    }
                }

                // get fees category
                $fees_category = $this->db->prepare("SELECT `id`, `name`, `amount`, `code` FROM fees_category WHERE client_id = ? AND status = ? LIMIT 30");
                $fees_category->execute([$clientId, 1]);
                $fees_category_log = $fees_category->fetchAll(PDO::FETCH_OBJ);

                // UPDATE THE STUDENTS FEES DATA FOR THE TERM
                $update_query = $this->db->prepare("UPDATE users_arrears SET arrears_details = ?, arrears_category = ?, arrears_total = ?, last_updated = now(), fees_category_log = ? WHERE student_id = ? AND client_id = ? LIMIT 1");
                $insert_query = $this->db->prepare("INSERT INTO users_arrears SET client_id = ?, student_id = ?, arrears_details = ?, arrears_category = ?, arrears_total = ?, date_created = now(), last_updated = now(), fees_category_log = ?");
                
                $count = 0;
                
                print "For each student insert the fees owned by him/her\n";

                // Loop through the Students Fees Log List
                foreach($students_query_array as $key => $value) {

                    // loop through the students fees payments list
                    foreach($value as $ikey => $ivalue) {
                        
                        // confirm that the owings already exists or not
                        $owing = isset($student_ownings[$ikey]) ? $student_ownings[$ikey] : array();
                        
                        // if the owing is not empty
                        if(!empty($owing)) {

                            // load the student fees arreas
                            $fees_record = $this->student_fees_history_log_exist($ikey, $clientId);

                            // set the arrears total
                            $arrears_total = array_sum($owing);
                            $academic_key = str_ireplace("/", "_", $academic_year)."...{$academic_term}";

                            // confirm that the record already exists or not
                            if(!empty($fees_record)) {
                                // existing arrears
                                $existing = isset($fees_record->arrears_total) ? $fees_record->arrears_total : 0;
                                $old_arrears_details = json_decode($fees_record->arrears_details, true);
                                $old_arrears_category = json_decode($fees_record->arrears_category, true);

                                // format the data
                                $current = [$academic_key => $owing];
                                $arrears_details = $this->append_fees_details($current, $old_arrears_details);
                                $arrears_category = $this->append_fees_category($arrears_details);
                                
                                // arrears total
                                $new_arrears_total = array_sum($arrears_category);

                                // update the existing record
                                $update_query->execute([json_encode($arrears_details), json_encode($arrears_category), $new_arrears_total, json_encode($fees_category_log), $ikey, $clientId]);
                            } else {
                                // format the data
                                $arrears_details = [$academic_key => $owing];
                                $arrears_category = $owing;
                                $new_arrears_total = $arrears_total;

                                // insert the new record
                                $insert_query->execute([$clientId, $ikey, json_encode($arrears_details), json_encode($arrears_category), $new_arrears_total, json_encode($fees_category_log)]);
                            }

                            $count++;
                        }
                    }
                }

                // new query string for a school
                $update_query = $this->db->prepare("UPDATE clients_terminal_log SET fees_log = ?, fees_category_log = ?,
                    year_starts = ?, year_ends = ?, term_starts = ?, term_ends =?, settings = ?
                    WHERE client_id = ? AND academic_year = ? AND academic_term = ? LIMIT 1
                ");
                $insert_query = $this->db->prepare("INSERT INTO clients_terminal_log SET client_id = ?, fees_log = ?, 
                    fees_category_log = ?, academic_year = ?, academic_term = ?, year_starts = ?, year_ends = ?,
                    term_starts = ?, term_ends =?, settings = ?
                ");

                // confirm if the school fees log already exists
                if($this->client_fees_history_log_exist($clientId, $academic_year, $academic_term)) {
                    $update_query->execute([
                        json_encode($school_fees_log), json_encode($fees_category_log), 
                        $preferences->academics->year_starts,
                        $preferences->academics->year_ends,
                        $preferences->academics->term_starts,
                        $preferences->academics->term_ends, 
                        json_encode($original_client->client_preferences),
                        $clientId, $academic_year, $academic_term
                    ]);
                } else {
                    $insert_query->execute([
                        $clientId, json_encode($school_fees_log), json_encode($fees_category_log), 
                        $academic_year, $academic_term,
                        $preferences->academics->year_starts,
                        $preferences->academics->year_ends,
                        $preferences->academics->term_starts,
                        $preferences->academics->term_ends,
                        json_encode($original_client->client_preferences)
                    ]);
                }

                // update the fees collection table
                $no_reversal = $this->db->prepare("UPDATE fees_collection SET has_reversal = ? WHERE academic_year =? AND academic_term = ? AND client_id = ? AND has_reversal = ? LIMIT 5000");
                $no_reversal->execute([0, $academic_year, $academic_term, $clientId, 1]);

                // disallow reversal of all 
                $t_no_reversal = $this->db->prepare("UPDATE accounts_transaction SET state = ? WHERE state = ? AND academic_year =? AND academic_term = ? AND status='1' AND client_id=? LIMIT 5000");
                $t_no_reversal->execute(["Approved", "Pending", $academic_year, $academic_term, $clientId]);

                // set the new term in the clients data table
                $preferences->academics->academic_year = $next_academic_year;
                $preferences->academics->year_starts = $preferences->academics->next_year_starts;
                $preferences->academics->year_ends = $preferences->academics->next_year_ends;
                $preferences->academics->academic_term = $next_academic_term;
                $preferences->academics->term_starts = $preferences->academics->next_term_starts;
                $preferences->academics->term_ends = $preferences->academics->next_term_ends;

                // unset the next academic term and year
                $preferences->academics->next_academic_year = "";
                $preferences->academics->next_year_starts = "";
                $preferences->academics->next_year_ends = "";
                $preferences->academics->next_academic_term = "";
                $preferences->academics->next_term_starts = "";
                $preferences->academics->next_term_ends = "";
                
                // IMPORT THE GRADING SYSTEM LIST
                if(in_array("grading_system", $data_to_import)) {
                    print "Insert the Academic Grading System Structure\n";
                    // GET THE ACTUAL Subjects List
                    $list_grading_structure = $this->db->prepare("SELECT a.*
                        FROM 
                            grading_system a 
                        WHERE 
                            a.academic_year = ? AND a.academic_term = ? AND a.client_id = ?
                        LIMIT 1
                    ");
                    $list_grading_structure->execute([$academic_year, $academic_term, $clientId]);
                    $grading_system = $list_grading_structure->fetchAll(PDO::FETCH_ASSOC);

                    // variables
                    $grading_system_query_string = "";

                    // loop through the Subjects List
                    foreach($grading_system as $ikey => $course) {
                        
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
                        $query_string = "INSERT INTO grading_system SET ";
                        
                        // loop through the columns
                        foreach($columns as $key => $column) {
                            // exempt some data from the query
                            if(!in_array($key, [0, $last_key])) {
                                $query_string .= ''.$column.'="'.(!empty($values[$key]) ? addslashes($values[$key]) : '').'",';
                            }
                        }
                        $grading_system_query_string .= trim($query_string, ",").";";
                    }

                    // check for empty string
                    if(strlen($grading_system_query_string) > 20) {
                        $this->db->query($grading_system_query_string);
                    }
                }

                // variables
                $courses_query_string = "";
                $courses_plan_query_string = "";
                $courses_resource_query_string = "";

                // IMPORT THE Subjects List
                if(in_array("courses", $data_to_import)) {
                    print "Insert the Subjects List\n";
                    // GET THE ACTUAL Subjects List
                    $list_courses = $this->db->prepare("SELECT a.*
                        FROM 
                            courses a 
                        WHERE 
                            a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
                        LIMIT {$this->limit}"
                    );
                    $list_courses->execute([$academic_year, $academic_term, $clientId, 1]);
                    $courses_list = $list_courses->fetchAll(PDO::FETCH_ASSOC);

                    // loop through the Subjects List
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
                                $val = in_array($column, ['weekly_meeting']) && empty($values[$key]) ? 0 : $values[$key];
                                $query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) ? "{$column}=NULL," : ''.$column.'="'.(!empty($values[$key]) ? addslashes($values[$key]) : $val).'",';
                            }
                        }
                        $courses_query_string .= trim($query_string, ",").";";
                    }

                    // check for empty string
                    if(!empty($courses_query_string) && strlen($courses_query_string) > 20) {
                        $this->db->query($courses_query_string);
                    }
                }
                
                // IMPORT THE Subjects List
                if(in_array("courses_plan", $data_to_import)) {
                    print "Insert the courses plan\n";
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
                                $query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : ''.$column.'="'.addslashes($values[$key]).'",';
                            }
                        }
                        $courses_plan_query_string .= trim($query_string, ",").";";
                    }
                    
                    // run the query for the course plan
                    if(!empty($courses_plan_query_string) && strlen($courses_plan_query_string) > 20) {
                        $this->db->query($courses_plan_query_string);
                    }

                }

                // IMPORT THE Subjects List
                if(in_array("courses_resource", $data_to_import)) {
                    print "Insert the course resources\n";
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
                                $query_string .= in_array($column, ["start_date", "end_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : ''.$column.'="'.addslashes($values[$key]).'",';
                            }
                        }
                        $courses_resource_query_string .= trim($query_string, ",").";";
                    }

                    // run the query for the course resources
                    if(!empty($courses_resource_query_string) && strlen($courses_resource_query_string) > 20) {
                        $this->db->query($courses_resource_query_string);
                    }

                }
        
                // IMPORT THE FEES ALLOCATIONS LIST
                if(in_array("fees_allocation", $data_to_import)) {
                    print "Insert the fees allocations list\n";
                    // init variables
                    $fees_allocation_query_string = "";
                    $student_fees_allocation_query_string = "";

                    // LOAD THE COURSE RESOURCES LIST
                    $fees_allocation = $this->db->prepare("SELECT a.*
                        FROM 
                            fees_allocations a 
                        WHERE 
                            a.academic_year = ? AND a.academic_term = ? AND a.client_id = ? AND a.status = ?
                        LIMIT 100"
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
                                $query_string .= ''.$column.'="'.addslashes($values[$key]).'",';
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
                        $allocation["checkout_url"] = $this->random_string(16);

                        // get the new values
                        $values = array_values($allocation);
                        $last_key = count($values)-1;

                        // begin the fees_allocations insert string
                        $query_string = "INSERT INTO fees_payments SET ";
                        
                        // loop through the columns
                        foreach($columns as $key => $column) {
                            // exempt some data from the query
                            if(!in_array($key, [0, $last_key])) {
                                $query_string .= in_array($column, ["last_payment_date", "programme_id"]) && empty($values[$key]) ? "{$column}=NULL," : ''.$column.'="'.addslashes($values[$key]).'",';
                            }
                        }
                        $student_fees_allocation_query_string .= trim($query_string, ",").";";
                    }

                    // check for empty string
                    if(!empty($fees_allocation_query_string) && strlen($fees_allocation_query_string) > 20) {
                        $this->db->query($fees_allocation_query_string);
                    }
                    if(!empty($student_fees_allocation_query_string) && strlen($student_fees_allocation_query_string) > 20) {
                        $this->db->query($student_fees_allocation_query_string);
                    }
                    
                }

                // set the account state to closed
                $this->db->query("UPDATE accounts SET state='Closed' WHERE default_account='0' AND client_id='{$clientId}' AND state='Active' LIMIT 1");

                // load the promotions list
                $promotions_log = $this->db->prepare("SELECT a.*, u.name AS student_name, c.name AS promote_to_class_name,
                		(SELECT b.id FROM classes b WHERE b.item_id = a.promote_from LIMIT 1) AS promote_from_class_id,
                		(SELECT b.id FROM classes b WHERE b.item_id = a.promote_to LIMIT 1) AS promote_to_class_id
                	FROM promotions_log a 
                	LEFT JOIN users u ON u.item_id = a.student_id
                	LEFT JOIN classes c ON c.item_id = a.promote_to
                	WHERE a.academic_year=? AND a.academic_term=? AND a.client_id=? AND a.is_promoted='1' LIMIT 5000"
                );
                $promotions_log->execute([$academic_year, $academic_term, $clientId]);
                $promotions_history = $promotions_log->fetchAll(PDO::FETCH_ASSOC);
                
                // reset the the student class id in the users and payments tables
                $reset_users = $this->db->prepare("UPDATE users SET class_id = ? WHERE item_id = ? AND user_type=? AND client_id = ? LIMIT 1");
                $reset_payments = $this->db->prepare("UPDATE fees_payments SET class_id = ? WHERE student_id = ? AND client_id = ? AND academic_year = ? AND academic_term = ? LIMIT 50");

                // loop through the promotions history list and change the user class id
                foreach($promotions_history as $key => $promote) {
                	// change the student class id in the users table
                	$reset_users->execute([$promote["promote_to_class_id"], $promote["student_id"], 'student', $clientId]);
                	// change the student class id in the payments table
                	$reset_payments->execute([$promote["promote_to_class_id"], $promote["student_id"], $clientId, $next_academic_year, $next_academic_term]);

                	// print the success message
                	print "{$key}. {$promote["student_name"]} successfully promotted to {$promote["promote_to_class_name"]}.\n";
                }

                // set all promotions log to Processed
                $this->db->query("UPDATE promotions_history SET status='Processed' WHERE client_id='{$clientId}' AND status='Pending' LIMIT 100");

                print "Finally update the client preferences\n";

                // update the clients preferences
                $stmt = $this->db->prepare("UPDATE clients_accounts SET client_preferences = ?, client_state = ? WHERE client_id = ? LIMIT 1");
                $stmt->execute([json_encode($preferences), "Complete", $clientId]);

            }

            $this->db->commit();

            print "Processing of Academic Term Data was successful.\n";
            print "Cron Activity ended. Proceeding to the next school.\n\n";

		} catch(PDOException $e) {
			$this->db->rollBack();
			print "{$e->getMessage()}\n";
		}

	}

    /**
	 * Create a "Random" String
	 *
	 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
	 * @param	int	number of characters
	 * @return	string
	 */
	private function random_string($len = 8) {
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
	}

}

// create new object
$jobs = new Crons;
$jobs->scheduler();
?>