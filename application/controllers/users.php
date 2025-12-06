<?php
// ensure this file is being included by a parent file
if( !defined( 'BASEPATH' ) ) die( 'Restricted access' );

class Users extends Myschoolgh {

	public $password_ErrorMessage;
	public $fees_category_count;
	
	# start the construct
	public function __construct($data = null) {
		parent::__construct();

		$this->permission_denied = "Sorry! You do not have the required permission to perform this action.";
		$this->password_ErrorMessage = "<div style='width:100%'>Sorry! Please use a stronger password. <br><strong>Password Format</strong><br><ul>
			<li style='padding-left:15px;'>Password should be at least 8 characters long</li>
			<li style='padding-left:15px;'>At least 1 Uppercase</li>
			<li style='padding-left:15px;'>At least 1 Lowercase</li>
			<li style='padding-left:15px;'>At least 1 Numeric</li>
			<li style='padding-left:15px;'>At least 1 Special Character</li></ul></div>";

		
		$this->iclient = $data->client_data ?? [];
		$this->defaultClientData = $this->iclient;
		$this->fees_category_count = 20;
        $this->academic_term = $data->client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $data->client_data->client_preferences->academics->academic_year ?? null;
	}

	/**
	 * Confirm that the user is currently logged in
	 * 
	 * @return Bool
	 */
	public function loggedIn() {
		return true;
	}

	/**
	 * Perform checks to handle logged in
	 * 
	 * @return bool
	 */
	public function onlineCheck() {
		
		return true;

	}

	/**
	 * Quick List of Users
	 * 
	 * @return Array
	 */
	public function quick_list(stdClass $params) {
		
		try {

			global $defaultUser, $accessObject;

			$manageExeats = $accessObject->hasAccess("manage", "exeats");

			// set the user data
			if(!empty($defaultUser)) {
				$params->userData = $defaultUser;
			}

			// the number of rows to limit the query
			$params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

			// if the client data is parsed
			$academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;
			$academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;

			if(empty($params->verify_email)) {
				// get the class ids that this teacher is allowed to teach
				if($params->userData->user_type == "teacher") {
					// use this section if the class id was not parsed
					if(empty($params->class_id)) {
						// load the list of students
						$params->class_ids = $params->userData->class_ids;
					}
				}
				// if the user logged in is a student
				elseif($params->userData->user_type === "student") {
					$params->user_id = $params->userData->user_id;
					$params->class_id = $params->userData->class_id;
				}
			}

			// look up query
			$params->query = " 1 ";
			$params->query .= !empty($params->clientId) ? " AND a.client_id ='{$params->clientId}'" : null;
			$params->query .= !empty($params->unique_id) ? " AND a.unique_id IN {$this->inList($params->unique_id)}" : null;
			$params->query .= !empty($params->q) ? " AND a.name LIKE '%{$params->q}%'" : null;
			$params->query .= !empty($params->lookup) ? " AND ((a.name LIKE '%{$params->lookup}%') OR (a.unique_id LIKE '%{$params->lookup}%'))" : null;
			$params->query .= !empty($params->user_id) ? " AND a.item_id IN {$this->inList($params->user_id)}" : null;
			$params->query .= !empty($params->gender) ? " AND a.gender IN {$this->inList($params->gender)}" : null;
				
			if(!empty($defaultUser->wards_list_ids)) {
				$params->query .= " AND a.item_id IN {$this->inList($defaultUser->wards_list_ids)}";
			}

			if(!empty($params->class_id) && ($params->class_id === "staff_members")) {
				$params->query .= " AND a.user_type NOT IN {$this->inList(["student", "parent", "guardian"])}";
				$params->user_type = null;
			}

			// if the item quick_user_search was not parsed
			if(empty($params->quick_user_search)) {

				// append addition query filters
				$params->query .= !empty($params->department_id) ? " AND a.department='{$params->department_id}'" : null;
				$params->query .= !empty($params->email) ? " AND a.email ='{$params->email}'" : null;
				$params->query .= !empty($params->user_type) ? " AND a.user_type IN {$this->inList($params->user_type)}" : null;
				
				$params->query .= !empty($params->section_id) ? " AND a.section='{$params->section_id}'" : null;
				
				// if the user is a parent
				if(isset($params->only_wards_list)) {
					$params->query .= " AND a.guardian_id LIKE '%{$params->userId}%'";
				}
			}

			// if the class was parsed and also not an array list
			if(!empty($params->class_id) && !is_array($params->class_id)) {
				// if the preg matches numbers only
				$params->query .= preg_match("/^[0-9]+$/", $params->class_id) ? " AND a.class_id IN {$this->inList($params->class_id)}" : null;
				// if the preg matches string and numbers
				$params->query .= (strlen($params->class_id) > 5) && preg_match("/^[0-9a-zA-Z]+$/", $params->class_id) ? " AND cl.item_id IN {$this->inList($params->class_id)}" : null;
			}
			// if the class was parsed and also not an array list
			elseif(!empty($params->class_id) && is_array($params->class_id)) {
				// add the class id filter to the query
				$params->query .= " AND a.class_id IN {$this->inList($params->class_id)}";
			}

			// if the class was parsed and also not an array list
			if(!empty($params->class_ids) && is_array($params->class_ids) && !$manageExeats) {
				// add the class id filter to the query
				$params->query .= " AND cl.item_id IN {$this->inList($params->class_ids)}";
			}

			// if no status filter was parsed
			if(!isset($params->minified) || (isset($params->minified) && $params->minified !== "no_status_filters")) {
				// append the user status query
				$params->query .= (isset($params->user_status) && !empty($params->user_status)) ? " AND a.user_status IN {$this->inList($params->user_status)}" : " AND a.user_status NOT IN ({$this->default_not_allowed_status_users_list})";
			}

			// set the columns to load
			$params->columns = "a.id, a.client_id, a.unique_id, a.item_id AS user_id, a.name, a.scholarship_status,
				a.user_type, a.phone_number, a.class_id, a.email, a.image, a.gender, a.user_status, a.can_change_status, 
				cl.payment_module, cl.name class_name, dp.id AS department_id,
				dp.name AS department_name, sc.id AS section_id, sc.name AS section_name, 
				a.enrollment_date, a.position, a.date_of_birth, a.expected_days";
			
			if(!isset($params->minified) || (isset($params->minified) && $params->minified === "no_status_filters")) {
				$params->columns .= ", (
					SELECT CONCAT(
						COALESCE(SUM(b.amount_paid), '0'), '|',
						COALESCE(SUM(b.balance), '0'), '|',
						CONCAT(
							GROUP_CONCAT(b.category_id),'/',
							GROUP_CONCAT(COALESCE(b.payment_module, 'NONE')),'/',
							GROUP_CONCAT(COALESCE(b.payment_month, 'NONE')),'/',
							GROUP_CONCAT(COALESCE(b.balance, '0'))
						)
					)
					FROM fees_payments b 
					WHERE b.student_id = a.item_id AND b.academic_term = '{$academic_term}'
						AND b.academic_year = '{$academic_year}' AND b.exempted = '0' LIMIT 50
				) AS payments_data, ar.arrears_total AS arrears";

				$params->set_id_as_key = $params->set_id_as_key ?? true;
			}
			
			// additional filters
			$conter = 0;
			$idAsKey = $params->set_id_as_key ?? false;
			$groupByUserType = $params->group_by_user_type ?? false;

			// advanced search
			if($groupByUserType) {
				// set the user status
				$params->query .= !empty($params->user_status) ? " AND a.user_status IN {$this->inList($params->user_status)}" : null;
				// set the user type
				$params->query .= !empty($params->user_type) ? " AND a.user_type IN {$this->inList($params->user_type)}" : null;
			}

			// append the join to the query
			$appendJoin = "LEFT JOIN departments dp ON dp.id = a.department
				LEFT JOIN sections sc ON sc.id = a.section
				LEFT JOIN users_arrears ar ON ar.student_id = a.item_id";

			if(!empty($params->quick_list)) {
				$params->columns = "a.id, a.unique_id, a.item_id AS user_id, a.name, a.class_id, cl.name AS class, a.user_type AS type, a.date_of_birth AS dob, a.gender";
				$appendJoin = "";
			}

			// if the quick list was parsed then set the minor dataset to true
			$minorDataset = !empty($params->quick_list);

			// if the mobile app was parsed and the limit is greater than 100 then set the limit to 100
			if(!empty($params->mobileapp) && $params->limit > $this->mobile_app_limit) {
				$params->limit = $this->mobile_app_limit;
			}
			
			// prepare and execute the statement
			$sql = $this->db->prepare("SELECT {$params->columns}, a.student_type, a.boarding_status
				FROM users a
				LEFT JOIN classes cl ON cl.id = a.class_id
				{$appendJoin}
				WHERE {$params->query} AND a.deleted = '0' AND a.status = '1' ORDER BY a.name LIMIT {$params->limit}
			");
			$sql->execute();

			$data = [];

			// loop through the students list
			while($result = $sql->fetch(PDO::FETCH_OBJ)) {

				if(!empty($result->name)) {
					$result->name = random_names($result->name);
				}

				// if the user_type is a student
				if(!empty($result->user_type) && (($result->user_type === "student") && !$groupByUserType) && !$minorDataset) {
				    // set the init values
				    $result->debt = 0.00;
				    $result->term_bill = 0.00;
				    $result->amount_paid = 0.00;
				    $result->debt_formated = 0.00;
				    $result->arrears_formated = 0.00;
				    $result->total_debt_formated = 0.00;
				    $result->arrears = $result->arrears ?? 0;
				    
					// run this section if the user is a student
					if(isset($result->payments_data)) {
						$payments = explode("|", $result->payments_data);
						$result->debt = $payments[1];
						$result->amount_paid = $payments[0];
						$result->term_bill = $payments[0] + $result->debt;
						
						// format the payment data
						$result->payments_data = format_payment_data($payments);
					}

					// set the scholarship status
					$result->scholarship_status = (int) $result->scholarship_status;

					if(($result->user_type === "student")) {
						// set the additional data
						$result->debt_formated = empty($result->debt) ? 0 : number_format($result->debt, 2);
						$result->arrears_formated = empty($result->arrears) ? 0 : number_format($result->arrears, 2);
						$result->total_debt_formated = empty($result->debt) ? 0 : number_format(($result->debt + $result->arrears), 2);
					}
				}

				// append the was present
				if(isset($params->append_waspresent)) {
					$result->is_present = false;
				}

				if(!$minorDataset) {
					$result->can_change_status = (int) $result->can_change_status;
					$result->the_status_label = $this->the_status_label($result->user_status, "p-1");
				}

				if($groupByUserType) {
					$data[$result->user_type][] = $result;
				} else {
					$data[($idAsKey ? $result->user_id : $conter)] = $result;
				}

				$conter++;
			}

			// return the data"Sorry! There was an error while processing the request."
			return [
				"data" => $data,
				"code" => 200
			];

		} catch(PDOException $e) {
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
		}

	}

	/**
	 * Global function to search for item based on the predefined columns and values parsed
	 * 
	 * @param StdClass $params
	 * @param String $params->q		The search query
	 * @param String $params->user_type		The type of the user to load the result
	 * 
	 * @return Object
	 */
	public function search($params = null) {
		$params->minified = true;
		$params->quick_list = true;
		return $this->quick_list($params);
	}

	/**
	 * Global function to search for item based on the predefined columns and values parsed
	 * 
	 * @param StdClass $params
	 * @param String $params->user_type		The type of the user to load the result
	 * 
	 * @return Object
	 */
	public function searches($params = null) {

		try {

			// set the client id
			$params->clientId = $params->clientId ?? $this->clientId;
			
			// set the query
			$query = "a.client_id = '{$params->clientId}'";
			$query .= !empty($params->user_type) ? " AND a.user_type = '{$params->user_type}'" : null;
			$query .= !empty($params->q) ? " AND (a.name LIKE '%{$params->q}%' OR a.email LIKE '%{$params->q}%' OR a.phone_number LIKE '%{$params->q}%')" : null;

			// prepare and execute the statement
			$sql = $this->db->prepare("SELECT a.item_id AS user_id, a.name, 
					a.email, a.phone_number, a.image, 
					a.class_id, a.image, a.unique_id,
					cl.name AS class_name, a.section, 
					se.name AS section_name, 
					a.department, dept.name AS department_name
				FROM users a
				LEFT JOIN classes cl ON cl.id = a.class_id
				LEFT JOIN sections se ON se.id = a.section
				LEFT JOIN departments dept ON dept.id = a.department
				WHERE {$query} AND a.deleted = '0' AND a.status = '1' ORDER BY a.name LIMIT {$params->limit}
			");
			$sql->execute();

			$data = $sql->fetchAll(PDO::FETCH_OBJ);

			// return the data
			return [
				"data" => $data,
				"code" => 200
			];

		} catch(PDOException $e) {
			return ["code" => 400, "data" => $e->getMessage()];
		}
	}
	
	/**
	 * Global function to search for item based on the predefined columns and values parsed
	 * 
	 * @param StdClass $params
	 * @param String $params->user_id 		The unique user id to load the results
	 * @param String $params->user_type		The type of the user to load the result
	 * @param String $params->gender		The gender of the user
	 * 
	 * @return Object
	 */
	public function list($params = []) {

		$params->query = " 1 ";
		
		global $defaultUser, $isWardParent;

		// load the informatin per the user permissions
		if(isset($params->userData) || !empty($defaultUser)) {
			
			// set the user type
			$d_data = isset($params->userData) ? $params->userData : $defaultUser;
			$user_type = $d_data->user_type ?? null;

			// loop through the query
			if(in_array($user_type, ["employee", "student"]) && !isset($params->bypass)) {
				$params->user_id = $d_data->user_id;
			}

		}

		// boolean value
        $params->remote = (bool) (isset($params->remote) && $params->remote);

		// get the id equivalent of the class id
		if(isset($params->class_id) && !preg_match("/^[0-9]+$/", $params->class_id)) {
			$params->minified = "simplified";
			$params->class_id = $this->pushQuery("id", "classes", "item_id='{$params->class_id}' LIMIT 1")[0]->id ?? null;
		}

		if($isWardParent && !empty($params->user_type)) {
			if($params->user_type === "parent") {
				$params->user_id = $defaultUser->user_id;
			}
			elseif($params->user_type === "student" && empty($defaultUser->wards_list)) {
				return [];
			}
		}

		// set more parameters
		$params->query .= !empty($params->user_id) ? " AND a.item_id IN {$this->inList($params->user_id)}" : "";
		$params->query .= !empty($params->unique_id)? " AND a.unique_id IN {$this->inList($params->unique_id)}" : "";
		$params->query .= !empty($params->class_id) ? " AND a.class_id='{$params->class_id}'" : null;
		$params->query .= !empty($params->user_type) ? " AND a.user_type IN {$this->inList($params->user_type)}" : null;

		// if the unique or item id was parsed
		if(!empty($params->unique_or_item_id)) {
			$params->query .= " AND (a.unique_id='{$params->unique_or_item_id}' OR a.item_id='{$params->unique_or_item_id}')";
		}

		// if the activated parameter was not and not equal to pending then append this section
		if((isset($this->session->activated) && ($this->session->activated !== "Pending")) || (!isset($this->session->activated))) {
			$params->query .= (isset($params->user_status) && !empty($params->user_status)) ? " AND a.user_status IN {$this->inList($params->user_status)}" : " AND a.user_status NOT IN ({$this->default_not_allowed_status_users_list})";
		}
		
		// bypass the academic year checker
		$params->query .= (isset($params->enrolment_academic_year) && !empty($params->enrolment_academic_year)) ? " AND a.enrolment_academic_year='{$params->enrolment_academic_year}'" : null;
		$params->query .= (isset($params->enrolment_academic_term) && !empty($params->enrolment_academic_term)) ? " AND a.enrolment_academic_term='{$params->enrolment_academic_term}'" : null;
		
		// if the field is null (dont perform all these checks if minified was parsed)
		if(!isset($params->minified) || (isset($params->minified) && isset($params->reporting))) {

			// run this section and leave the rest if reporting was parsed
			if(isset($params->reporting)) {
				$params->query .= (isset($params->or_clause) && !empty($params->or_clause)) ? $params->or_clause : null;
				$params->query .= (isset($params->date_range)) ? $this->dateRange($params->date_range) : null;
				$params->query .= (isset($params->gender) && !empty($params->gender)) ? " AND a.gender='{$params->gender}'" : null;
			} else {
				$params->query .= (isset($params->email)) ? " AND a.email='{$params->email}'" : null;
				$params->query .= (isset($params->or_clause) && !empty($params->or_clause)) ? $params->or_clause : null;
				$params->query .= (isset($params->date_of_birth) && !empty($params->date_of_birth)) ? " AND a.date_of_birth='{$params->date_of_birth}'" : null;
				$params->query .= (isset($params->created_by) && !empty($params->created_by)) ? " AND a.created_by='{$params->created_by}'" : null;
				$params->query .= (isset($params->firstname) && !empty($params->firstname)) ? " AND a.firstname LIKE '%{$params->firstname}%'" : null;
				$params->query .= (isset($params->lastname) && !empty($params->lastname)) ? " AND a.lastname LIKE '%{$params->lastname}%'" : null;
				$params->query .= (isset($params->department_id) && !empty($params->department_id)) ? " AND a.department='{$params->department_id}'" : null;
				$params->query .= (isset($params->section_id) && !empty($params->section_id)) ? " AND a.section='{$params->section_id}'" : null;
				$params->query .= (isset($params->username)) ? " AND a.username='{$params->username}'" : null;
				$params->query .= (isset($params->gender) && !empty($params->gender)) ? " AND a.gender='{$params->gender}'" : null;
			}

		}
		
		// if the user is a parent
		if(isset($params->only_wards_list)) {
			$params->query .= " AND a.guardian_id LIKE '%{$params->userId}%'";
		}

		$params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;

		// if a search parameter was parsed in the request
		$order_by = "ORDER BY a.name ASC";
		$params->query .= (isset($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
		$params->query .= (isset($params->lookup)) ? " AND a.name LIKE '%{$params->lookup}%'" : null;

		// the number of rows to limit the query
		$params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

		// if the mobile app was parsed and the limit is greater than 100 then set the limit to 100
		if(!empty($params->mobileapp) && $params->limit > $this->mobile_app_limit) {
			$params->limit = $this->mobile_app_limit;
		}

		// make the request for the record from the model
		try {

			// if minified list was requested
			if(!empty($params->minified)) {

				// set the columns to load
				$params->columns = "
					a.id AS user_row_id, a.client_id, a.guardian_id, a.item_id AS user_id, a.name, a.preferences, a.description,
					a.unique_id, a.email, a.image, a.phone_number, a.user_type, a.class_id, a.account_balance, a.changed_password,
					a.gender, a.enrollment_date, a.residence, a.religion, a.date_of_birth, a.last_visited_page, a.fees_is_set,
					a.scholarship_status, a.occupation, employer, a.alergy,
					(SELECT b.description FROM users_types b WHERE b.id = a.access_level) AS user_type_description, c.country_name, a.username,
					(SELECT name FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_name,
					dept.name AS department_name, a.address,
					se.name AS section_name, a.user_status,
					(SELECT name FROM blood_groups WHERE blood_groups.id = a.blood_group LIMIT 1) AS blood_group_name";

				// exempt current user
				if(($params->minified === "chat_list_users")) {
					// set the order
					$order_by = "ORDER BY a.name ASC";
					
					// set the columns to load
					$params->columns .= ", a.occupation, a.online, a.last_seen, (
						SELECT b.message_unique_id FROM users_chat b WHERE 
							(b.sender_id = a.item_id AND b.receiver_id = '{$params->userId}' AND b.receiver_deleted = '0') OR 
                    		(b.receiver_id = '{$params->userId}' AND b.sender_id = a.item_id AND b.sender_deleted = '0')
						LIMIT 1
					) AS msg_id";
					$params->query .= " AND a.item_id != '{$params->userId}' ";
				}

				if($params->minified === "simplied_load_withclass") {

					// if the user type was parsed and the type is guardian
					if(isset($params->user_type) && ($params->user_type == "guardian")) {
						// make a query for the guardian list
						$query = $this->pushQuery("a.name, a.item_id AS user_id, a.unique_id, a.image, 
							a.email, a.phone_number, a.residence, a.relationship, a.fees_is_set,
							(SELECT b.country_name FROM country b WHERE b.id = a.country LIMIT 1) AS country_name", 
							"users a", "a.status='1' AND a.client_id='{$params->clientId}' AND a.user_type='parent' AND a.name LIKE '%{$params->q}%'");
						
						// return the response
						return ["data" => $query];

					} else {
						$params->columns .= ", a.date_of_birth, a.guardian_id, cl.name AS class_name";
					}
				}

			}

			// if the request is to return the where clause parameter only
			if(isset($params->return_where_clause)) {
				
				// remove user data was parsed
				if(isset($params->remove_user_data)) {
					unset($params->userData);
				}

				return $params->query;
			}

			$loadWards = !empty($params->append_wards) ? true : false;
			$noKeyLoad = empty($params->key_data_load) ? true : false;
			$appendClient = !empty($params->append_client) ? true : false;

			// if the client data is parsed
			$academic_year = !empty($params->academic_year) ? $params->academic_year : $this->academic_year;
			$academic_term = !empty($params->academic_term) ? $params->academic_term : $this->academic_term;
			
			$leftJoin = !empty($params->user_payroll) ? "LEFT JOIN payslips_employees_payroll up ON up.employee_id = a.item_id" : null;
			$leftJoinQuery = !empty($leftJoin) ? ", 
				up.gross_salary, up.net_allowance, up.allowances, up.deductions, up.net_salary, up.basic_salary,
				up.account_name, up.account_number, up.bank_name, up.bank_branch, up.ssnit_number, up.tin_number" : null;

			// prepare and execute the statement
			$sql = $this->db->prepare("SELECT 
				".((isset($params->columns) ? $params->columns : "
					a.*, a.item_id AS user_id,
					ut.description AS user_type_description, c.country_name,
					(SELECT COUNT(*) FROM users b WHERE (b.created_by = a.item_id) AND a.deleted='0') AS clients_count,
					(SELECT name FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_name,
					dept.name AS department_name, se.name AS section_name, a.blood_group AS blood_group_name,
					(SELECT phone_number FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_phone
				")).", a.id AS user_row_id, a.class_ids, a.changed_password, (SELECT b.permissions FROM users_roles b WHERE b.user_id = a.item_id AND b.client_id = a.client_id LIMIT 1) AS user_permissions, 
					a.course_ids, cl.name AS class_name, cl.item_id AS class_guid,
					(
						SELECT SUM(p.balance) FROM fees_payments p 
						WHERE p.student_id = a.item_id AND p.exempted = '0' 
							AND p.academic_year = '{$academic_year}' AND p.academic_term = '{$academic_term}'
					) AS debt, a.last_visited_page,
					(
						SELECT ar.arrears_total FROM users_arrears ar WHERE ar.student_id = a.item_id LIMIT 1
					) AS arrears, cl.payment_module {$leftJoinQuery}
				FROM users a 
				LEFT JOIN country c ON c.id = a.country
				LEFT JOIN classes cl ON cl.id = a.class_id
				LEFT JOIN sections se ON se.id = a.section
				LEFT JOIN departments dept ON dept.id = a.department
				LEFT JOIN users_types ut ON ut.id = a.access_level
				{$leftJoin}
				WHERE {$params->query} AND a.deleted = ? AND a.status = ? {$order_by} LIMIT {$params->limit}
			");
			$sql->execute([0, 1]);

			// init
			$row = 0;
			$data = [];
			$users_group = [];

			$parentKeys = [
				'department_name', 'class_name', 'scholarship_status', 'fees_is_set', 'debt', 'arrears', 'section_name',
				'course_ids', 'class_ids', 'class_guid', 'class_id', 'guardian_id', 'payment_module', 'account_balance'
			];

			$staffKeys = [
				'scholarship_status', 'fees_is_set', 'debt', 'arrears', 'employer', 'occupation',
				'class_guid', 'class_id', 'guardian_id', 'payment_module'
			];
			
			// loop through the results
			while($result = $sql->fetch(PDO::FETCH_OBJ)) {

				if(!empty($params->return_password)) {
					$result->pass_word = $result->password;
				}

				if(!empty($result->name)) {
					$result->name = random_names($result->name);
				}
				
				// if the preference is set
				if(isset($result->preferences)) {
					# return an empty result
					unset($result->password);
					unset($result->item_id);
					$result->preferences = !empty($result->preferences) ? json_decode($result->preferences) : (object) [];
				}

				$result->course_ids = !empty($result->course_ids) ? json_decode($result->course_ids, true) : [];
				$result->class_ids = !empty($result->class_id) ? json_decode($result->class_id, true) : (
					!empty($result->class_ids) ? json_decode($result->class_ids, true) : []
				);
				$result->changed_password = $result->changed_password;

				// if not a minified suggestion list
				if(!isset($params->minified)) {

					// unset the id
					unset($result->id);
					$result->action = "";

					if($result->user_type == "student") {
						$result->guardian_list = $this->guardian_list($result->guardian_id, $result->client_id, true);
					}

					// if not a remote 
					if(!$params->remote) {

						// contact details
						$result->contact_details = "<i style=\"font-size:10px\" class=\"fa fa-envelope\"></i> {$result->email}<br><i style=\"font-size:10px\" class=\"fa fa-phone\"></i> {$result->phone_number}";
						
						// set the label for the policy
						$result->the_status_label = $this->the_status_label($result->user_status);

						// action buttons
						$result->action .= " &nbsp; <a class='btn p-1 btn-outline-success m-0 btn-sm' title='Click to view details of this profile' href='{$this->baseUrl}profile/{$result->user_id}'><i class='fa fa-eye'></i></a>";
					}
					
					// append to the list and return
					$row++;
					$result->row_id = $row;

				}

				// if the user type is a parent
				if($result->user_type === "parent") {
					foreach($parentKeys as $item) {
						unset($result->{$item});
					}
				}

				// if the user type is a staff
				if(in_array($result->user_type, ['employee', 'accountant', 'admin', 'teacher'])) {
					foreach($staffKeys as $item) {
						unset($result->{$item});
					}
				}

				// if the guardian id was parsed
				$result->guardian_id = isset($result->guardian_id) && !empty($result->guardian_id) ? $this->stringToArray($result->guardian_id) : [];

				// clean date of birth
				if(isset($result->date_of_birth)) {
					$result->dob_clean = date("jS F Y", strtotime($result->date_of_birth));
				}

				$result->last_visited_page = str_ireplace(
					["{{APPURL}}"], [$this->baseUrl],
					$result->last_visited_page
				);

				// unset the permissions
				if(isset($params->no_permissions)) {
					unset($result->user_permissions);
				} else {
					$result->user_permissions = !empty($result->user_permissions) ? 
						(!is_array($result->user_permissions) ? json_decode($result->user_permissions, true) : $result->user_permissions) : [];
				}

				// run this section if the user is a student
				if($result->user_type === "student") {
					$result->debt_formated = empty($result->debt) ? 0 : number_format($result->debt, 2);
					$result->arrears_formated = empty($result->arrears) ? 0 : number_format($result->arrears, 2);
					$result->total_debt_formated = empty($result->debt) ? 0 : number_format(($result->debt + $result->arrears), 2);
				}

				// append the was present
				if(isset($params->append_waspresent)) {
					$result->is_present = false;
				}

				if(isset($result->description)) {
					$result->description = custom_clean(htmlspecialchars_decode($result->description));
				}
				
				// online algorithm (user is online if last activity is at most 5minutes ago)
				if(isset($result->online)) {
					$result->online = $this->user_is_online($result->last_seen);
					$result->last_seen = time_diff($result->last_seen);
				}
				
				// if the message id was queried but empty then generate a new id
				if(isset($result->msg_id) && empty($result->msg_id)) {
					// set the new message id
					$result->msg_id = strtoupper(random_string("alnum", RANDOM_STRING));
				}

				// if the left join is parsed
				if($leftJoin) {
					// get the allowances of the user
					$userAllowances = $this->pushQuery("a.*, at.name, at.calculation_method, at.calculation_value, at.pre_tax_deduction, at.subject_to_ssnit", 
					"payslips_employees_allowances a LEFT JOIN payslips_allowance_types at ON at.id = a.allowance_id", 
					"a.employee_id='{$result->user_id}' AND a.client_id='{$result->client_id}'");

					// filter the allowances
					$result->_allowances = array_filter($userAllowances, function($each) {
						return $each->type == 'Allowance';
					});

					// get the deductions of the user
					$result->_deductions = array_filter($userAllowances, function($each) {
						return $each->type == 'Deduction';
					});
				}

				// if the user wants to load wards as well
				if($loadWards && ($result->user_type === "parent")) {
					$qr = $this->db->prepare("SELECT 
							a.id, a.item_id AS student_guid, a.unique_id, a.firstname, a.lastname, a.othername,
							a.name, a.image, a.guardian_id, a.date_of_birth, a.blood_group, a.gender, a.email,
							c.name AS class_name, c.item_id AS class_guid, a.enrollment_date,
							(SELECT b.name FROM departments b WHERE b.id = a.department LIMIT 1) AS department_name
						FROM users a 
						LEFT JOIN classes c ON c.id = a.class_id
						WHERE a.status='1' AND a.client_id='{$result->client_id}' AND a.guardian_id LIKE '%{$result->user_id}%' AND a.user_type='student' LIMIT 20
					");
					$qr->execute();
					$result->wards_list = $qr->fetchAll(PDO::FETCH_ASSOC);
					$result->wards_list_ids = array_column($result->wards_list, "student_guid");
				}

				// append the client details in the request
				if($appendClient) {
					$result->client = $this->defaultClientData;
				}

				if($params->limit && (int)$params->limit !== 1) {
					$result->user_permissions = [];
				}

				// append to the results set to return
				if($noKeyLoad) {
					$data[] = $result;
				} else {
					$data[$result->user_id] = $result;
				}

				// if minified
				if(isset($params->minified)) {
					$users_group[$result->user_type][] = $result;
				}

			}

			// exempt current user
			if(isset($params->minified) && ($params->minified == "chat_list_users")) {
				// recent chats list
				$chatsObj = load_class("chats", "controllers");
				$chats_list = isset($params->userId) ? $chatsObj->recent($params->userId) : [];

				// set the data to return
				$data = [
					"users_list" => $data,
					"chats_list" => $chats_list
				];
			}

			// the reports list
			if(isset($params->minified) && ($params->minified == "reporting_list")) {

				// array loop
				$array_list = $this->array_keys_count($users_group);

				$data_set = [
					"users_list" => [
						"list" => $data,
						"performance" => $this->user_performance_group($users_group),
					],
					"users_group" => $array_list["list"],
					"clients_count" => $array_list["clients_count"],
					"total_count" => count($data)
				];

				return $data_set;

			}

			// return the data
			return [
				"data" => $data,
				"code" => 200
			];

		} catch(PDOException $e) {
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
		}

	}

	/**
	 * Minimal list of users
	 * 
	 * @return Array
	 */
	public function minimal(stdClass $params) {

		try {

			global $accessObject;

			$params->query = " 1 ";
			$params->query .= !empty($params->user_type) ? " AND a.user_type IN {$this->inList($params->user_type)}" : null;
			$params->query .= !empty($params->lookup) ? " AND ((a.name LIKE '%{$params->lookup}%') OR (a.unique_id LIKE '%{$params->lookup}%'))" : null;
			$params->query .= !empty($params->q) ? " AND ((a.name LIKE '%{$params->q}%') OR (a.unique_id LIKE '%{$params->q}%'))" : null;

			$column = "id";
			// get the id equivalent of the class id
			if(!empty($params->class_id) && !preg_match("/^[0-9]+$/", $params->class_id)) {
				$column = "item_id";
			}
			
			if(!empty($params->class_id)) {
				$class = $this->pushQuery("id, name", "classes", "{$column} = '{$params->class_id}' LIMIT 1")[0] ?? null;
				if(empty($class)) {
					return ["code" => 400, "data" => "Sorry! The class was not found."];
				}

				$params->class_id = (int) $class->id;
				$params->class_name = $class->name;

				$params->query .= !empty($params->class_id) ? " AND a.class_id = '{$params->class_id}'" : null;
			}

			/** Set the columns to load */
			$params->columns = "a.id, a.item_id AS user_id, a.unique_id, a.firstname, a.lastname, a.othername, a.name";
			$params->columns .= empty($params->user_type) ? ", a.user_type" : null;
			$params->columns .= empty($params->class_id) ? ", cl.name AS class_name" : null;

			/** Prepare and execute the statement */
			$sql = $this->db->prepare("SELECT {$params->columns} 
			FROM users a 
			".(empty($params->class_id) ? "LEFT JOIN classes cl ON cl.id = a.class_id" : null)."
			WHERE {$params->query} AND a.deleted = ? AND a.status = ? AND a.client_id='{$params->clientId}'
			LIMIT 500");
			$sql->execute([0, 1]);

			$data = [];
			while($result = $sql->fetch(PDO::FETCH_OBJ)) {
				$result->id = (int) $result->id;
				$data[] = $result;
			}

			$canUpdate = true;

			/** If the resource parameter was parsed then get the attendance log for the day */
			if(!empty($params->resource) && ($params->resource === "attendance")) {
				
				/** Set the query to get the attendance log for the day */
				$query = !empty($params->class_id) ? " AND a.class_id = '{$params->class_id}'" : null;
				$query .= !empty($params->user_type) ? " AND a.user_type = '{$params->user_type}'" : null;
				
				$selected_date = date("Y-m-d", strtotime($params->selected_date ?? date("Y-m-d")));
				
				/** Get the attendance log for the day */
				$check = $this->pushQuery(
					"a.id, a.users_list, a.users_data, a.user_type, a.class_id, a.finalize, a.date_finalized", "users_attendance_log a", "a.log_date='{$selected_date}' {$query} LIMIT 1"
				);

				$users_list = !empty($check) ? json_decode($check[0]->users_data, true) : [];

				$canUpdate = !empty($check) ? ((int)$check[0]->finalize !== 1) : true;
				foreach($data as $key => $user) {
					$data[$key]->status = $users_list[$user->user_id]['state'] ?? '';
					$data[$key]->comments = $users_list[$user->user_id]['comments'] ?? '';
				}
			}

			// if the class id was not parsed and the resource was not parsed then return the data
			if(empty($params->class_id) && empty($params->resource)) {
				return [ "code" => 200, "data" => $data ];
			}

			return [
				"data" => [
					'finalized' => !$canUpdate,
					'record_exist' => !empty($check),
					'class_name' => $params->class_name,
					'record_id' => !empty($check) ? (int)$check[0]->id : 0,
					'canFinalize' => $accessObject->hasAccess("finalize", "attendance"),
					'finalizedDate' => !empty($check) ? $check[0]->date_finalized : null,
					'users' => $data,
				],
				"code" => 200
			];

		} catch(PDOException $e) {
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
		}
	}

	/**
	 * Quick Search
	 * 
	 * @return Array
	 */
	public function quick_search(stdClass $params) {

		global $isSupport;
		 
		try {

			// look up query
			$params->query = " 1 ";
			$params->query .= (isset($params->lookup)) ? " AND ((a.name LIKE '%{$params->lookup}%') OR (a.unique_id LIKE '%{$params->lookup}%'))" : null;

			// if not support admin then 
			if(!$isSupport) {
				$params->query .= " AND a.client_id='{$params->clientId}'";
			}

			$addQuery = $isSupport ? ", (SELECT b.client_name FROM clients_accounts b WHERE b.client_id = a.client_id LIMIT 1) AS client_name" : null;

			// set the columns to load
			$params->columns = "
				a.client_id, a.guardian_id, a.item_id AS user_id, a.name, a.preferences,	
				cl.name AS class_name, a.username, a.last_password_change,
				a.unique_id, a.email, a.image, a.phone_number, a.user_type, a.class_id, a.account_balance,
				a.gender, a.enrollment_date, a.residence, a.religion, a.date_of_birth, a.last_visited_page,
				(SELECT b.description FROM users_types b WHERE b.id = a.access_level) AS user_type_description, c.country_name,
				(SELECT name FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_name,
				(SELECT name FROM departments WHERE departments.id = a.department LIMIT 1) AS department_name,
				(SELECT name FROM sections WHERE sections.id = a.section LIMIT 1) AS section_name,
				(SELECT name FROM blood_groups WHERE blood_groups.id = a.blood_group LIMIT 1) AS blood_group_name
				{$addQuery}";

			// if the mobile app was parsed and the limit is greater than 100 then set the limit to 100
			if(!empty($params->mobileapp) && $params->limit > $this->mobile_app_limit) {
				$params->limit = $this->mobile_app_limit;
			}
				
			// prepare and execute the statement
			$sql = $this->db->prepare("SELECT {$params->columns} FROM users a
				LEFT JOIN country c ON c.id = a.country
				LEFT JOIN classes cl ON cl.id = a.class_id
				WHERE {$params->query} AND a.deleted = ? AND a.status = ? LIMIT {$params->limit}
			");
			$sql->execute([0, 1]);

			$data = [];
			while($result = $sql->fetch(PDO::FETCH_OBJ)) {
				$result->name = $this->remove_quotes($result->name);
				$data[] = $result;
			}

			// return the data"Sorry! There was an error while processing the request."
			return [
				"data" => $data,
				"additional" => [
					"isSupport" => $isSupport
				],
				"code" => 200
			];

		} catch(PDOException $e) {
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
		}

	}

	/**
	 * array_keys_count
	 * 
	 * This loops through the array list and gets the count
	 * 
	 * @param Array $array
	 * 
	 * @return Array
	 */
	public function array_keys_count($array) {

		if(!is_array($array)) {
			return;
		}

		$list = [];
		$clients_count = 0;

		// loop through the list of array values
		foreach($array as $key => $value) {
			
			// append to the clients count
			if(in_array($key, ["user", "business"])) {
				$clients_count += count($value);
			} 
			$list[$key] = [
				"count" => is_array($array[$key]) ? count($value) : 0,
				"title" => $this->the_user_roles[$key]["_role_title"]
			];
		}
		return [
			"list" => $list,
			"clients_count" => $clients_count
		];
	}

	/**
	 * array_keys_count
	 * 
	 * This loops through the array list and gets the count
	 * 
	 * @param Array $array
	 * 
	 * @return Array
	 */
	public function user_performance_group($array) {

		if(!is_array($array)) {
			return;
		}

		$list = [];
		$types = [
			"student" => [
				"role" => "students_report",
				"title" => "Student"
			],
			"teacher" => [
				"role" => "teachers_report",
				"title" => "Broker"
			],
			"accountant" => [
				"role" => "accountants_report",
				"title" => "Accountant"
			],
			"parent" => [
				"role" => "parents_report",
				"title" => "Parent"
			],
			"employee" => [
				"role" => "employees_report",
				"title" => "Employee"
			],
			"admin" => [
				"role" => "admin_report",
				"title" => "Admin User's"
			]
		];

		// loop through the list of array values
		foreach($array as $key => $value) {
			
			// append to the clients count
			$list[$types[$key]["role"]][] = $value;
		}

		return $list;
	}

	/**
	 * Update the expected days
	 * 
	 * @param stdClass $params
	 * 
	 * @return Array
	 */
	public function expected_days(stdClass $params) {

		try {

			if(empty($params->expected_days)) {
				return ["code" => 400, "data" => "Sorry! Ensure that the expected days was parsed."];
			}

			if(empty($params->user_id)) {
				return ["code" => 400, "data" => "Sorry! Ensure that the user id was parsed."];
			}

			// convert the expected days to an array
			$params->expected_days = $this->stringToArray($params->expected_days);
			
			// update the expected days
			$stmt = $this->db->prepare("UPDATE users SET expected_days = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
			$stmt->execute([implode(",", $params->expected_days), $params->user_id, $params->clientId]);
			
			// return the success response
			return [
				"code" => 200,
				"data" => "Expected days were successfully updated"
			];
		} catch(PDOException $e) {
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
		}
	}

	/**
	 * Update the expected days
	 * 
	 * @param stdClass $params
	 * 
	 * @return Array
	 */
	public function leave_days(stdClass $params) {

		try {

			if(empty($params->leave_days)) {
				return ["code" => 400, "data" => "Sorry! Ensure that the leave days was parsed."];
			}

			if(empty($params->user_id)) {
				return ["code" => 400, "data" => "Sorry! Ensure that the user id was parsed."];
			}

			if(!in_array($params->leave_days, $this->leave_days)) {
				return ["code" => 400, "data" => "Sorry! An invalid leave days was parsed."];
			}
			
			// update the expected days
			$stmt = $this->db->prepare("UPDATE users SET leave_days = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
			$stmt->execute([$params->leave_days, $params->user_id, $params->clientId]);
			
			// return the success response
			return [
				"code" => 200,
				"data" => "Leave days were successfully updated"
			];
		} catch(PDOException $e) {
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
		}
	}

	/**
	 * Get the list of guardian information
	 * 
	 * @param Mixed $guardian_ids
	 * @param String $client_id
	 * 
	 * @return Array
	 */
	public function guardian_list($guardian_ids = null, $client_id = null, $force_search = false) {
		
		// assign a variable
		$data = [];
		$guardian_id = !empty($guardian_ids) && !isset($guardian_ids->clientId) ? $this->stringToArray($guardian_ids) : [];

		
		// if the guardian id is not empty
		if(!empty($guardian_id) || $force_search) {
			// loop through the guardian ids submitted
			foreach($guardian_id as $user_id) {
				$query = $this->pushQuery("item_id AS user_id, unique_id, image, name AS fullname, 
					phone_number AS contact, phone_number_2, email, relationship, address", 
					"users", "status='1' AND user_status='Active' AND (item_id='{$user_id}' OR unique_id='{$user_id}') AND 
					client_id='{$client_id}' LIMIT 1");
				if(!empty($query)) {
					$data[] = $query[0];
				}
			}
		}
		// load all the guardian list and submit in the response
		else {
			// set the client id and confirm load wards
			$guardianId = isset($guardian_ids->guardian_id) ? "AND (a.item_id='{$guardian_ids->guardian_id}' OR unique_id='{$guardian_ids->guardian_id}') AND user_type='parent' LIMIT 1" : null; 
			$clientId = isset($guardian_ids->clientId) ? $guardian_ids->clientId : $client_id;
			$loadWards = isset($guardian_ids->append_wards) ? true : false;

			$query = $this->pushQuery("a.item_id AS user_id, a.unique_id, a.name, a.phone_number, a.phone_number_2, a.residence, 
				a.email, a.username, a.blood_group, a.nationality, a.occupation, a.employer, a.relationship,
				(SELECT b.country_name FROM country b WHERE b.id = a.country LIMIT 1) AS country_name", 
				"users a", "a.status='1' AND a.client_id='{$clientId}' {$guardianId}");

			// loop through the users list
			foreach($query as $value) {
				// unset the id
				unset($value->id);
				unset($value->status);

				// if the user wants to load wards as well
				if($loadWards) {
					$qr = $this->db->prepare("
						SELECT 
							a.item_id AS student_guid, a.unique_id, a.firstname, a.lastname, a.othername,
							a.name, a.image, a.guardian_id, a.date_of_birth, a.blood_group, a.gender, a.email,
							c.name AS class_name, b.name AS department_name
						FROM users a 
						LEFT JOIN classes c ON c.id = a.class_id
						LEFT JOIN departments b ON b.id = a.department
						WHERE a.status='1' AND a.user_type = 'student' AND a.guardian_id LIKE '%{$value->user_id}%'
					");
					$qr->execute();
					$value->wards_list = $qr->fetchAll(PDO::FETCH_OBJ);
				}
				$data[] = $value;
			}
			
		}

		return $data;
	}

	/**
	 * Guardian Wards Listing
	 * 
	 * @param Array 	$wards
	 * @param String 	$guardian_id
	 * @param Bool		$canupdate
	 * 
	 * @return String
	 */
	public function guardian_wardlist(array $wards, $guardian_id, $canupdate = false) {

		// initialize
		$wards_list = "";

		// loop through the array list
		foreach($wards as $ward) {
			// convert to object
            $ward = (object) $ward;

			$imageToUse = "<img src=\"{$this->baseUrl}{$ward->image}\" class='rounded-2xl cursor author-box-picture' width='50px'>";
			if($ward->image == "assets/img/avatar.png") {
				$imageToUse = "
				<div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
					<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-user h-6 w-6 text-white' aria-hidden='true'><path d='M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2'></path><circle cx='12' cy='7' r='4'></circle></svg>
				</div>";
			}

			// append to the list
			$wards_list .= "
				<div class=\"col-12 col-md-6 load_ward_information col-lg-6\" data-id=\"{$ward->student_guid}\">
					<div class=\"card card-success\">
						<div class=\"card-header pr-2 pl-2\" style=\"border-bottom:0px;\">
							<div class=\"d-flex gap-4 justify-content-start\">
								<div class='mr-2'>{$imageToUse}</div>
								<div>
									<h4 class=\"mb-0 pb-0 font-16 pr-0 mr-0 text-uppercase\">".limit_words($ward->name, 3)."</h4>
									<span class=\"text-primary\">{$ward->unique_id}</span><br>
									".(!empty($ward->class_name) ? "<p class=\"mb-0 pb-0\"><i class='fa fa-home'></i> ".ucwords(strtolower($ward->class_name))."</p>" : "")."
									".(!empty($ward->gender) ? "<p class=\"mb-0 pb-0\"><i class='fa fa-user'></i> {$ward->gender}</p>" : "")."
									<p class=\"mb-0 pb-0\"><i class='fa fa-calendar-check'></i>".(!empty($ward->date_of_birth) ? " {$ward->date_of_birth} " : " N/A ")."</p>
								</div>
							</div>
						</div>
						".($canupdate ? 
							"<div class=\"border-top p-2\">
								<div class=\"d-flex justify-content-between\">
									<div>
										<button onclick=\"return load('student/{$ward->student_guid}')\" class=\"btn btn-sm btn-outline-success\" title=\"View ward details\"><i class=\"fa fa-eye\"></i> View</button>
									</div>
									<div>
										<a href=\"#\" onclick='return modifyGuardianWard(\"{$guardian_id}_{$ward->student_guid}\", \"remove\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Remove</a>
									</div>
								</div>
							</div>" : ""
						)."
					</div>
				</div>
			";
		}

		return $wards_list;
	}

	/**
	 * Guardian Delegates Listing
	 * 
	 * @param Array 	$delegates
	 * @param String 	$guardian_id
	 * @param Bool		$canupdate
	 * 
	 * @return String
	 */
	public function guardian_delegatelist(array $delegates, $guardian_id, $canupdate = false, $width = "col-md-6") {

		// initialize
		$delegates_list = "";

		// loop through the array list
		foreach($delegates as $delegate) {
			// convert to object
            $delegate = (object) $delegate;

			$imageToUse = "<img src=\"{$this->baseUrl}{$delegate->image}\" class='rounded-2xl cursor author-box-picture' width='50px'>";
			if($delegate->image == "assets/img/avatar.png") {
				$imageToUse = "
				<div class='h-12 w-12 bg-gradient-to-br from-purple-500 via-purple-600 to-blue-600 rounded-xl flex items-center justify-center shadow-lg'>
					<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-user h-6 w-6 text-white' aria-hidden='true'><path d='M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2'></path><circle cx='12' cy='7' r='4'></circle></svg>
				</div>";
			}

			// append to the list
			$delegates_list .= "
				<div class=\"col-12 {$width} load_delegate_information\" data-delegate_id=\"{$delegate->id}\">
					<div class=\"card card-success\">
						<div class=\"card-header pr-2 pl-2\" style=\"border-bottom:0px;\">
							<div class=\"d-flex gap-4 justify-content-start\">
								<div class='mr-2'>{$imageToUse}</div>
								<div>
									<h4 class=\"mb-0 pb-0 font-16 pr-0 mr-0 text-uppercase\">".limit_words($delegate->firstname . " " . $delegate->lastname, 3)."</h4>
									<span class=\"text-primary\">{$delegate->unique_id}</span><br>
									".(!empty($delegate->relationship) ? "<p class=\"mb-0 pb-0\"><i class='fa fa-user'></i> {$delegate->relationship}</p>" : "")."
									".(!empty($delegate->gender) ? "<p class=\"mb-0 pb-0\"><i class='fa fa-user'></i> {$delegate->gender}</p>" : "")."
									<p class=\"mb-0 pb-0\"><i class='fa fa-phone'></i>".(!empty($delegate->phonenumber) ? " {$delegate->phonenumber} " : " N/A ")."</p>
								</div>
							</div>
						</div>
						".($canupdate ? 
							"<div class=\"border-top p-2\">
								<div class=\"d-flex justify-content-between\">
									<div>
										<button onclick=\"return load('delegate/{$delegate->id}')\" class=\"btn btn-sm btn-outline-success\" title=\"View delegate details\"><i class=\"fa fa-eye\"></i> View</button>
									</div>
									<div>
										<a href=\"#\" onclick='return modifyGuardianWard(\"{$guardian_id}_{$delegate->id}\", \"remove\", \"delegate\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Remove</a>
									</div>
								</div>
							</div>" : ""
						)."
					</div>
				</div>
			";
		}

		return $delegates_list;
	}

	/**
	 * Full Scholarship
	 * 
	 * @param String $student_id
	 * @param String $status
	 * 
	 * @return Array
	 */
	public function full_scholarship($params) {

		try {

			// update the user scholarship information
			$stmt = $this->db->prepare("UPDATE users SET scholarship_status = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
			$stmt->execute([!empty($params->status) ? 1 : 0, $params->student_id, $params->clientId]);

			// get the user scholarship status
			$status = !empty($params->status) ? 0 : 1;
			$color = !empty($params->status) ? "danger" : "success";
			$title = !empty($params->status) ? "Remove Scholarship" : "Award Full Scholarship";

			// return the success response
			return [
				"code" => 200,
				"data" => "Scholarship status was successfully updated",
				"additional" => [
					'html' => '<span class="btn mb-1 btn-outline-'.$color.'" onclick="return full_scholarship(\''.$params->student_id.'\', '.$status.')"><i class="fa fa-ankh"></i> '.$title.'</span></span>',
					'status' => $status,
				]
			];
		} catch(PDOException $e) {
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
		}
	}	

	/**
	 * Append/Remove a student to the Guardian
	 * 
	 * @param object $params
	 * @param String $params->user_id		This is a combination of the guardian id and the student id
	 * @param String $params->todo			This is the action to perform (append / remove)
	 * 
	 * @return Array
	 */
	public function modify_guardianward(stdClass $params) {

		// split the user id
		$expl = explode("_", $params->user_id);
		
		// if there is no second key then end the query
		if(!isset($expl[1])) {
			return ["code" => "Sorry! The student id is required."];
		}
		
		// confirm that a valid parent id was parsed
		$p_data = $this->pushQuery("a.id, a.name", "users a", "a.status='1' AND a.client_id='{$params->clientId}' AND (a.item_id = '{$expl[0]}' OR a.unique_id = '{$expl[0]}') LIMIT 1");
		if(empty($p_data)) {
			return ["code" => 400, "data" => "Sorry! An invalid guardian id was parsed"];
		}

		// confirm that a valid student id was parsed
		$u_data = $this->pushQuery("a.guardian_id, a.name", "users a", "a.status='1' AND a.client_id='{$params->clientId}' AND a.item_id = '{$expl[1]}' AND user_type='student' LIMIT 1");
		if(empty($u_data)) {
			return ["code" => 400, "data" => "Sorry! An invalid student id was parsed"];
		}

		// convert the guardian id into an array
		$guardian_id = !empty($u_data[0]->guardian_id) ? $this->stringToArray($u_data[0]->guardian_id) : [];

		// if in the array then remove the value
		if(in_array($expl[0], $guardian_id)) {
			foreach($guardian_id as $key => $value) {
				if($value == $expl[0]) {
					unset($guardian_id[$key]);
					break;
				}
			}
		}
		
		// append to the array list
		else {
			array_push($guardian_id, $expl[0]);
		}

		// update the user guardian id information
		$stmt = $this->db->prepare("UPDATE users SET guardian_id = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
		$stmt->execute([implode(",", $guardian_id), $expl[1], $params->clientId]);

		// return the success response
		if($params->todo == "remove") {

			// log the user activity
			$this->userLogs("guardian_ward", $expl[0], null, "{$params->userData->name} removed <strong>{$u_data[0]->name}</strong> as the ward of <strong>{$p_data[0]->name}</strong>.", $params->userId);

			return [
				"data" => [
					"info" => "Guardian ward was successfully removed",
					"removed_list" => [$expl[1]]
				],
				"code" => 200
			];
		} else if($params->todo == "append") {
			// get the list of guardian wards
			$guardian_param = (object) [
				"limit" => 1,
				"append_wards" => true,
				"guardian_id" => $expl[0],
				"clientId" => $params->clientId,
			];
			$data = $this->guardian_list($guardian_param);

			// format the list
			$wards_list = $this->guardian_wardlist($data[0]->wards_list, $expl[0], true);
			
			// log the user activity
			$this->userLogs("guardian_ward", $expl[0], null, "{$params->userData->name} appended <strong>{$u_data[0]->name}</strong> as a ward to <strong>{$p_data[0]->name}</strong>.", $params->userId);

			// return the results
			return [
				"data" => [
					"info" => "Student successfully appended to the Guardian Ward's List.",
					"wards_list" => $wards_list
				]
			];
		}

	}

	/**
	 * Append/Remove a delegate to the Guardian
	 * 
	 * @param object $params
	 * @param String $params->user_id		This is a combination of the guardian id and the delegate id
	 * @param String $params->todo			This is the action to perform (append / remove)
	 * 
	 * @return Array
	 */
	public function modify_guardiandelegate(stdClass $params) {

		global $isWardParent, $defaultUser;

		// split the user id
		$expl = explode("_", $params->user_id);
		
		// if there is no second key then end the query
		if(!isset($expl[1])) {
			return ["code" => "Sorry! The delegate id is required."];
		}

		$delegate = $this->pushQuery("*", "delegates", "client_id='{$params->clientId}' AND id='{$expl[1]}' LIMIT 1");
		if(empty($delegate)) {
			return ["code" => 400, "data" => "Sorry! An invalid delegate id was parsed"];
		}

		if($params->todo == "remove") {
			$guardians = $this->stringToArray($delegate[0]->guardian_ids);
			foreach($guardians as $key => $value) {
				if($value == $expl[0]) {
					unset($guardians[$key]);
					break;
				}
			}

			// if the delegate was created by the user, then delete the delegate
			if(($delegate[0]->created_by == $defaultUser->user_id) || $isWardParent) {
				$this->quickUpdate("status='0'", "delegates", "id='{$expl[1]}' AND client_id='{$params->clientId}' LIMIT 1");
			} else {
				$stmt = $this->db->prepare("UPDATE delegates SET guardian_ids = ? WHERE id = ? AND client_id = ? LIMIT 1");
				$stmt->execute([implode(",", $guardians), $expl[1], $params->clientId]);
			}

			return [
				"code" => 200,
				"data" => "Delegate was successfully removed",
				"additional" => [
					"removed_id" => $expl[1]
				]
			];
		}
			
	}

	/**
	 * Append/Remove a Guardian attached to a ward
	 * 
	 * @param object $params
	 * @param String $params->user_id		This is a combination of the guardian id and the student id
	 * @param String $params->todo			This is the action to perform (append / remove)
	 * 
	 * @return Array
	 */
	public function modify_wardguardian(stdClass $params) {

		// split the user id
		$expl = explode("_", $params->user_id);
		
		// if there is no second key then end the query
		if(!isset($expl[1])) {
			return ["code" => "Sorry! The student id is required."];
		}
		
		// confirm that a valid parent id was parsed
		$p_data = $this->pushQuery("a.id, a.name", "users a", "a.status='1' AND a.client_id='{$params->clientId}' AND a.item_id = '{$expl[0]}' LIMIT 1");
		if(empty($p_data)) {
			return ["code" => 400, "data" => "Sorry! An invalid guardian id was parsed"];
		}

		// confirm that a valid student id was parsed
		$u_data = $this->pushQuery("a.guardian_id, a.name", "users a", "a.status='1' AND a.client_id='{$params->clientId}' AND a.item_id = '{$expl[1]}' LIMIT 1");
		if(empty($u_data)) {
			return ["code" => 400, "data" => "Sorry! An invalid student id was parsed"];
		}

		// convert the guardian id into an array
		$guardian_id = !empty($u_data[0]->guardian_id) ? $this->stringToArray($u_data[0]->guardian_id) : [];

		// if in the array then remove the value
		if(in_array($expl[0], $guardian_id)) {
			foreach($guardian_id as $key => $value) {
				if($value == $expl[0]) {
					unset($guardian_id[$key]);
					break;
				}
			}
		}
		
		// append to the array list
		else {
			array_push($guardian_id, $expl[0]);
		}

		// update the user guardian id information
		$stmt = $this->db->prepare("UPDATE users SET guardian_id = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
		$stmt->execute([implode(",", $guardian_id), $expl[1], $params->clientId]);


		// return the success response
		if($params->todo == "remove") {
			// log the user activity
			$this->userLogs("guardian_ward", $expl[0], null, "{$params->userData->name} removed <strong>{$u_data[0]->name}</strong> as the ward of <strong>{$p_data[0]->name}</strong>.", $params->userId);
			
			return [
				"data" => [
					"info" => "Ward Guardian was successfully removed",
					"removed_list" => [$expl[0]]
				],
				"code" => 200
			];
		} else if($params->todo == "append") {
			// log the user activity
			$this->userLogs("guardian_ward", $expl[0], null, "{$params->userData->name} appended <strong>{$u_data[0]->name}</strong> as a ward to <strong>{$p_data[0]->name}</strong>.", $params->userId);

			// return the results
			return [
				"data" => [
					"info" => "Guardian successfully appended to the Student Guardian's List.",
					"user_id" => $expl[1]
				]
			];
		}

	}

	/**
	 * Register new account
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function add(stdClass $params) {
		
		global $accessObject, $clientPrefs;
		
		// clean the contact number
		$params->phone = isset($params->phone) ? str_ireplace(["(", ")", "-", "_"], "", $params->phone) : null;
		$params->phone = !empty($params->phone) ? preg_replace("/[\s]/", "", $params->phone) : null;

		// client id
		$params->email = $params->email ?? null;
		$params->client_id = isset($params->clientId) ? strtoupper($params->clientId) : null;

		/** Check the email address if not empty */
		if(!empty($params->email) && !filter_var($params->email, FILTER_VALIDATE_EMAIL)) {
			return ["code" => 400, "data" => "Sorry! Provide a valid email address."];
		}

		/** If the user is logged in */
		$loggedInAccount = (bool) isset($params->userData->user_id);

		/** Set the changed password value */
		$params->changed_password = 1;

		if(!in_array($params->user_type, array_keys($this->the_user_roles))) {
			return ["code" => 400, "data" => "Sorry! An invalid user_type was provided for processing :- accepted values are ".implode(", ", array_keys($this->the_user_roles))];
		}

		if(!empty($params->gender) && !in_array(strtolower($params->gender), ["male", "female"])) {
			return ["code" => 400, "data" => "Sorry! An invalid gender was provided for processing :- accepted values are Male and Female"];
		}
		
		/** Run this section if the user is logged in */
		if($loggedInAccount || (isset($params->remote) && $params->remote)) {
			/** The user types */
			$access = ($params->user_type === "student") ? "student" : ($params->user_type === "parent" ? "guardian" : $params->user_type);
			
			/** If not permitted */
			if(!$accessObject->hasAccess("add", $access)) {
				return ["code" => 401, "data" => $this->permission_denied];
			}

			/** Generate a random password */
			$params->password = $this->defaultPassword;

			/** Set the changed password value */
			$params->changed_password = 0;

			// this user created the account
			$params->created_by = $params->userData->user_id;

			/** Check the username if not empty */
			if(!isset($params->lastname)) {
				return ["code" => 400, "data" => "Sorry! The lastname cannot be empty"];
			}
			
		}

		$fileName = null;

		// confirm that a logo was parsed
        if(isset($params->image)) {
            // set the upload directory
            $uploadDir = "assets/img/users/";
            // File path config 
            $fileName = basename($params->image["name"]);

            // check if its a valid image
            if(!empty($fileName) && validate_image($params->image["tmp_name"])){
                // set a new filename
                $fileName = $uploadDir . random_string('alnum', 10)."__{$fileName}";
                // Upload file to the server 
                if(move_uploaded_file($params->image["tmp_name"], $fileName)){}
            } else {
            	$fileName = null;
            }
        }

		// if the date of birth is today then set it to null
		if(!empty($params->date_of_birth) && strtotime($params->date_of_birth) == strtotime(date("Y-m-d"))) {
			$params->date_of_birth = null;
		}

		// generate a new unique user id
		$theClientData = !empty($this->iclient) ? $this->iclient : $this->client_data($params->clientId);

		// set the label and generate a new unique id
		$label = $params->user_type === "student" ? "student_label" : ($params->user_type === "parent" ? "parent_label" : "staff_label");
		$counter = $this->append_zeros(($this->itemsCount("users", "client_id = '{$params->clientId}' AND user_type='{$params->user_type}'") + 1), $this->append_zeros);
        
		// set the label
		$ilabel = $clientPrefs->labels->{$label};
		$unique_id = (!empty($ilabel) ? $ilabel : "MSGH")."/".$counter."/".date("Y");

		// get the last user id
		$last_id = $this->lastRowId("users") + 1;
        
		// set the username to the unique_id if the username is empty
		$params->username = "MSGH".$this->append_zeros($last_id, 8);

		/** Check the contact number if not empty */
		if((isset($params->phone) && !empty($params->phone) && !preg_match("/^[0-9+]+$/", $params->phone))) {
			return ["code" => 400, "data" => "Sorry! Provide a valid contact number."];
		}

		/** Check the contact number if not empty */
		if(isset($params->portal_registration) && !passwordTest($params->password)) {
			return ["code" => 400, "data" => $this->password_ErrorMessage];
		}

		// set the user type
		$params->user_type = isset($params->user_type) && !empty($params->user_type) ? $params->user_type : "student";
		
		// convert the user type to lowercase
		$params->user_type = strtolower($params->user_type);

		// get the user permissions
		$accessPermissions = $accessObject->getPermissions($params->user_type);

		// if the permission is empty
		if(empty($accessPermissions)) {
			return ["code" => 400, "data" => "Sorry! An invalid user_type was provided for processing."];
		}

		// if the email address is not empty
		if(!empty($params->email)) {

			// confirm that the email does not already exist
			$i_params = (object) ["limit" => 1, "email" => $params->email, "verify_email" => true];
			
			// get the user data
			if(!empty($this->quick_list($i_params)["data"])) {
				return ["code" => 400, "data" => "Sorry! The email is already in use."];
			}
		}

		// set the unique id
		$params->unique_id = isset($params->unique_id) && !empty($params->unique_id) ? $params->unique_id : $unique_id;
		$params->unique_id = strtoupper($params->unique_id);
		
		// grouping guardian
		$guardian = [];

		/** Convert the email address to lowercase */
		$params->email = !empty($params->email) ? strtolower($params->email) : null;
		
		// insert the user information
		try {

			// if the guardian id is not empty then discard the guardian id parsed
			if(isset($params->guardian_id) && !empty($params->guardian_id)) {
				// set the new id
				$guardian_ids = [$params->guardian_id];
			} else {
				// if the guardian info is not empty and is an array
				if(isset($params->guardian_info) && is_array($params->guardian_info)) {
					// loop through the array list and append the key and values
					foreach($params->guardian_info as $key => $value) {
						// loop through the list again for efficient grouping
						foreach($value as $kk => $vv) {
							$guardian[$kk][$key] = $vv;
						}
					}
				}
			}

			// begin transaction
			$this->db->beginTransaction();

			// variables
			$params->user_id = random_string("alnum", RANDOM_STRING);

			#set the token expiry time to 6 hours from the moment of request
			$token = random_string("alnum", mt_rand(15, 30));
            $token_expiry = time()+(60*60*6);

			// set a default password for students and parents
			if(in_array($params->user_type, ["student", "parent", "employee", "teacher", "admin"]) || !isset($params->email)) {
				// set this as a default password if no email was parsed or the usertype is student, parent and employee
				$params->password = DEFAULT_PASS;

				// encrypt the password sent
				$encrypt_password = password_hash($params->password, PASSWORD_DEFAULT);
			}
			
			// usertype and fullname
			$params->fullname = $params->firstname. " " . ($params->othername ?? null) . " " .( $params->lastname ?? null);
			$params->created_by = $params->created_by ?? $params->user_id;

			// load the access level permissions
			$permissions = $accessPermissions[0]->user_permissions;
			$access_level = $accessPermissions[0]->id;

			// init course ids
			$course_ids = [];
			$add_message = "";

			// append tutor to Subjects List
			if(isset($params->courses_ids)) {
				$course_ids = $this->append_user_courses($params->courses_ids, $params->user_id, $params->clientId);
			}

			// set the redirect url
			$redirect = ($params->user_type === "student") ? "student" : ($params->user_type === "parent" ? "guardian" : "staff");

			// get the class id
			$params->class_id = isset($params->class_id) ? $params->class_id : null;

			// set the enrollment academic year and term
			if($redirect === "student") {
				$params->enrolment_academic_year = $params->enrolment_academic_year ?? $this->academic_year;
				$params->enrolment_academic_term = $params->enrolment_academic_term ?? $this->academic_term;
			}

			// convert to arrary if the class id is an array variable
			if(is_array($params->class_id)) {
				$params->class_id = json_encode($params->class_id);
			}

			// format the date of birth
			$params->date_of_birth = isset($params->date_of_birth) && strtotime($params->date_of_birth) == strtotime(date("Y-m-d")) ? null : ($params->date_of_birth ?? null);
			
			// default password for the users
			$defaultPass = password_hash(DEFAULT_PASS, PASSWORD_DEFAULT);

			// insert the user information
			$stmt = $this->db->prepare("
				INSERT INTO users SET item_id = ?, user_type = ?, access_level = ?,
				verify_token = ?, token_expiry = ?, changed_password = ?, status = '1'
				".(isset($params->unique_id) ? ", unique_id='{$params->unique_id}'" : null)."
				".(!empty($params->clientId) ? ", client_id='{$params->clientId}'" : null)."
				".(isset($params->firstname) ? ", firstname='{$params->firstname}'" : null)."
				".(isset($params->lastname) ? ", lastname='{$params->lastname}'" : null)."
				".(isset($params->othername) ? ", othername='{$params->othername}'" : null)."
				".(isset($params->fullname) ? ", name='{$params->fullname}'" : null)."
				".(isset($params->email) ? ", email='{$params->email}'" : null)."
				".(isset($params->gender) ? ", gender='{$params->gender}'" : null)."
				".(isset($params->username) ? ", username='{$params->username}'" : null)."
				".(isset($params->position) ? ", position='{$params->position}'" : null)."

				".(isset($params->hometown) ? ", hometown='{$params->hometown}'" : null)."
				".(isset($params->place_of_birth) ? ", place_of_birth='{$params->place_of_birth}'" : null)."
				".(isset($params->alergy) ? ", alergy='{$params->alergy}'" : null)."
				
				".(isset($params->relationship) ? ", relationship='{$params->relationship}'" : null)."

				".(!empty($params->enrolment_academic_year) ? ", enrolment_academic_year='{$params->enrolment_academic_year}'" : null)."
				".(!empty($params->enrolment_academic_term) ? ", enrolment_academic_term='{$params->enrolment_academic_term}'" : null)."

				".(isset($params->status) ? ", status='{$params->status}'" : null)."
				".(isset($encrypt_password) ? ", password='{$encrypt_password}'" : ", password='{$defaultPass}'")."

				".(!empty($params->boarding_status) ? ", boarding_status='{$params->boarding_status}'" : null)."
				".(!empty($params->student_type) ? ", student_type='{$params->student_type}'" : null)."

				".(!empty($course_ids) ? ", course_ids='".json_encode($course_ids)."'" : "")."

				".(!empty($fileName) ? ", image='{$fileName}'" : null)."
				".(isset($params->previous_school) ? ", previous_school='{$params->previous_school}'" : null)."
				".(isset($params->previous_school_remarks) ? ", previous_school_remarks='{$params->previous_school_remarks}'" : null)."
				".(isset($params->previous_school_qualification) ? ", previous_school_qualification='{$params->previous_school_qualification}'" : null)."

				".(isset($params->enrollment_date) ? ", enrollment_date='{$params->enrollment_date}'" : null)."
				".(isset($params->class_id) ? ", class_id='{$params->class_id}'" : null)."
				".(isset($params->blood_group) ? ", blood_group='{$params->blood_group}'" : null)."
				".(isset($params->religion) ? ", religion='{$params->religion}'" : null)."
				".(isset($params->section) ? ", section='{$params->section}'" : null)."
				".(isset($params->programme) ? ", programme='{$params->programme}'" : null)."
				".(isset($params->department_id) ? ", department='{$params->department_id}'" : null)."

				".(isset($params->residence) ? ", residence='{$params->residence}'" : null)."
				".(isset($params->phone) ? ", phone_number='{$params->phone}'" : null)."
				".(isset($params->phone_2) ? ", phone_number_2='{$params->phone_2}'" : null)."
				".(isset($params->description) ? ", description='{$params->description}'" : null)."
				".(isset($params->address) ? ", address='{$params->address}'" : null)."
				".(isset($params->employer) ? ", employer='{$params->employer}'" : null)."
				".(isset($params->occupation) ? ", occupation='{$params->occupation}'" : null)."
				".(isset($params->postal_code) ? ", postal_code='{$params->postal_code}'" : null)."
				".(isset($params->nationality) ? ", nationality='{$params->nationality}'" : null)."
				".(isset($params->country) ? ", country='{$params->country}'" : null)."
				".(isset($params->city) ? ", city='{$params->city}'" : null)."
				".(!empty($params->date_of_birth) ? ", date_of_birth='{$params->date_of_birth}'" : null)."
			");
			
			// execute the insert user data
			$stmt->execute([$params->user_id, $params->user_type, $access_level, $token, $token_expiry, $params->changed_password]);

			// log the user access level
			$stmt2 = $this->db->prepare("INSERT INTO users_roles SET user_id = ?, client_id = ?, permissions = ?");
			$stmt2->execute([$params->user_id, $params->clientId, $permissions]);

			// insert the user guardian information
			if(!empty($guardian)) {
				// init the guardian id
				$guardian_ids = [];
				$last_user = $last_id;

				// loop through the guardian array list
				foreach($guardian as $key => $value) {

					// process if the fullname is not empty
					if(!empty($value["guardian_fullname"])) {

						// increment the last user id
						$last_user++;

						// generate a new unique user id
						$counter = $this->append_zeros(($this->itemsCount("users", "client_id = '{$params->clientId}' AND user_type='parent'") + 1), $this->append_zeros);
						$unique_id = strtoupper($theClientData->client_preferences->labels->parent_label.$counter.date("Y"));

						// explode the first and lastnames
						$expl = explode(" ", $value["guardian_fullname"]);
						// use the first and second index as the names
						$firstname = $expl[0];
						$lastname = isset($expl[2]) ? $expl[2] : ($expl[1] ?? null);
						$othername = isset($expl[2]) ? $expl[2] : null;

						// create a new random string
						$guardian_id = random_string("alnum", RANDOM_STRING);

						// join the names as the fullname
						$fullname = "{$firstname} {$othername} {$lastname}";
						$p_username = "MSGH".$this->append_zeros($last_user, 8);
						
						// insert the name of the guardian
						$stmt = $this->db->prepare("INSERT INTO users SET 
							firstname = ?, lastname = ?, othername = ?, name = ?, username = ?,
							phone_number = ?, `email` = ?, `relationship` = ?, user_type = ?,
							`address` = ?, `unique_id` = ?, `client_id` = ?, item_id = ?
						");
						$stmt->execute([
							$firstname, $lastname, $othername, $fullname, $p_username, $value["guardian_contact"], 
							$value["guardian_email"], $value["guardian_relation"], "parent", 
							$value["guardian_address"], $unique_id, $params->clientId, $guardian_id 
						]);
						$guardian_ids[] = $guardian_id;
					}
					
				}
			}

			// update the user information if the guardian_ids is not empty
			if(!empty($guardian_ids)) {
				$this->db->query("UPDATE users SET guardian_id='".implode(",", $guardian_ids)."' WHERE item_id='{$params->user_id}' AND user_type = 'student' LIMIT 1");
			}
			
			// if the email address was parsed
			if(isset($params->email) && filter_var($params->email, FILTER_VALIDATE_EMAIL)) {

				// email comfirmation link
				$message = "Hello {$params->firstname},";

				$message .= '<a class="alert alert-success" href="'.$this->baseUrl.'verify?dw=user&token='.$token.'">Verify your account</a>';
				$message .= '<br><br>If it does not work please copy this link and place it in your browser url.<br><br>';
				$message .= $this->baseUrl.'verify?dw=user&token='.$token;

				// recipient list
				$reciepient = ["recipients_list" => [["fullname" => $params->fullname,"email" => $params->email,"customer_id" => $params->user_id]]];

				// insert the email content to be processed by the cron job
				$stmt = $this->db->prepare("INSERT INTO users_messaging_list SET template_type = ?, item_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?, users_id = ?");
				$stmt->execute(['account-verify', random_string("alnum", RANDOM_STRING), json_encode($reciepient),
					$params->created_by, "[{$this->appName}] Account Verification", $message, $params->user_id
				]);

				// add message
				$add_message = "A verification link has been sent via email.";

			}
			
			// insert the user activity
			$this->userLogs("account-verify", $params->user_id, null, "{$params->fullname} - verify account by clicking on the link sent to the provided email address.", $params->created_by, null);

			// commit all opened transactions
			$this->db->commit();

			# set the output to return when successful
			$return = ["code" => 200, "data" => ucfirst($redirect)." account successfully created. {$add_message}", "refresh" => 2000];
			
			# append to the response
			if($loggedInAccount) {

				// set the url
				if(($redirect == "student")) {
					$canAllocate = $accessObject->hasAccess("allocation", "fees");
					$url_link = $canAllocate ? "{$this->baseUrl}fees-allocate/{$params->user_id}?is_new_admission=1" : "{$this->baseUrl}student/{$params->user_id}";
				} elseif(in_array($params->user_type, ["admin", "teacher", "employee", "accountant"])) {
					$url_link = "{$this->baseUrl}payroll-view/{$params->user_id}?new_staff";
				} else {
					$url_link = "{$this->baseUrl}{$redirect}/{$params->user_id}";
				}

				// append the redirection url
				$return["additional"] = [
					"clear" => true,
					"href" => $url_link
				];
			}

			if(!empty($params->remote)) {
				$return["record"] = $this->view((object) [
					"clientId" => $params->clientId,
					"user_id" => $params->user_id,
					"limit" => 1,
					"full_details" => true,
					"no_limit" => 1,
				]);
			}

			// return the output
            return $return;

		} catch(PDOException $e) {
			$this->db->rollBack();
			return false;
		}

	}

	/**
	 * Generate a user record
	 * 
	 * @param \stdClass $params
	 * 
	 * @return mixed
	 */
	public function generate_user_record(stdClass $params) {
		// global variable
		global $academicSession;

		// get the student information
		$userRecord = $this->pushQuery("
			a.*, (SELECT b.name FROM classes b WHERE b.id = a.class_id LIMIT 1) AS class_name,
			us.arrears_details, us.arrears_category, us.fees_category_log, us.arrears_total",
			"users a LEFT JOIN users_arrears us ON us.student_id = a.item_id", 
			"a.client_id='{$params->clientId}' AND a.item_id='{$params->user_id}'  AND a.status = '1'
			LIMIT 1"
		);

		if(empty($userRecord)) {
			return false;
		}

		$userRecord = $userRecord[0];

		// academic year and term
		$params->academic_year = $params->academic_year ?? $this->academic_year;
		$params->academic_term = $params->academic_term ?? $this->academic_term;

		// header("Content-Type: application/json");
		// echo json_encode($userRecord);exit;

		// generate the pdf header
		$html_string = generate_pdf_header($params->client_data, $this->baseUrl, $params->isPDF, false);

		$html_string .= '
			<div style="margin-top:0px;">
				<table border="0" width="100%" cellpadding="5px">
					<tr>
						<td align="center" colspan="2">
							<h3 style="border-bottom:solid 1px #ccc;padding:0px;padding-bottom:5px;margin:0px;font-family:\'Calibri Regular\'">
								'.ucwords($userRecord->user_type).' RECORD
							</h3>
						</td>
					</tr>
					<tr>
						<td width="50%">
							<div style="text-transform:uppercase;margin-bottom:5px;">Name: <strong>'.$userRecord->name.'</strong></div>
							<div style="text-transform:uppercase;margin-bottom:5px;">'.ucwords($userRecord->user_type).' ID: <strong>'.$userRecord->unique_id.'</strong></div>
							<div style="text-transform:uppercase;margin-bottom:5px;">'.
							(!empty($userRecord->class_name) ? "Class: <strong>{$userRecord->class_name}</strong>" : null).'
							</div>
						</td>
						'.($userRecord->user_type === "student" ? '
						<td width="50%" align="right">
							<h3 style="margin-top:0px;padding:0px;margin-bottom:5px;text-transform:uppercase">Academics</h3>
							<div style="text-transform:uppercase;margin-bottom:5px;">Year: <strong>'.$params->academic_year.'</strong></div>
							<div style="text-transform:uppercase;margin-bottom:5px;">'.$academicSession.': <strong>'.$params->academic_term.'</strong></div>
							<div style="margin-bottom:5px;">'.date("Y-m-d h:ia").'</div>
						</td>' : null).'
					</tr>
					<tr>
					</tr>
				</table>
				<style>table.table tr td {border:solid 1px #dad7d7;padding:5px;}</style>
			</div>';

		// generate the pdf footer
		$html_string .= generate_pdf_footer();

		// return the response
		return [
			'record' => $html_string,
			'filename' => "{$userRecord->firstname}_{$userRecord->lastname}.pdf"
		];
		
	}

	/**
	 * View a users record
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function view(stdClass $params) {
		// global variable
		global $accessObject;
		
		$payload = (object) [
			"clientId" => $params->clientId,
			"unique_or_item_id" => !empty($params->user_id) ? $params->user_id : $params->userData->user_id,
			"limit" => 1,
			"full_details" => true,
			"no_limit" => 1,
			"append_wards" => !empty($params->append_wards),
			// "minified" => !empty($params->minified)
		];

		$record = $this->list($payload);
		
		if(empty($record["data"])) {
			return ["code" => 404, "data" => "Sorry! No record was found."];
		}

		$record = $record["data"][0];
		unset($record->action);
		unset($record->contact_details);
		unset($record->the_status_label);

		return $record;
	}

	/**
	 * View a users record
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function profile(stdClass $params) {
		// global variable
		global $accessObject;
		
		$payload = (object) [
			"clientId" => $params->clientId,
			"user_id" => $params->userId,
			"limit" => 1,
			"full_details" => true,
			"no_limit" => 1,
		];

		$record = $this->list($payload);
		
		if(empty($record["data"])) {
			return ["code" => 404, "data" => "Sorry! No record was found."];
		}

		$record = $record["data"][0];
		unset($record->action);
		unset($record->contact_details);
		unset($record->the_status_label);

		return $record;
	}

	/**
	 * Update a users record
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function update(stdClass $params) {

		// global variable
		global $accessObject, $defaultUser;

		// confirm that the user_id does not already exist
		$i_params = (object) [
			"limit" => 1, 
			"user_id" => $params->user_id, 
			"user_status" => ["Pending", "Active", "Transferred"]
		];
		$the_user = $this->list($i_params)["data"];

		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "data" => "Sorry! Please provide a valid user id."];
		}

		// get the user type
		$_user_type = ($the_user[0]->user_type === "student") ? "student" : ($the_user[0]->user_type === "parent" ? "guardian" : ($params->user_type ?? "admin"));

		// permisssions checker test
		if(($the_user[0]->user_id !== $params->userData->user_id) && !$accessObject->hasAccess("update", $_user_type)) {
			return ["code" => 201, "data" => "Sorry! You are not permitted to modify this user account details."];
		}

		// clean the contact number
		$params->phone = isset($params->phone) ? str_ireplace(["(", ")", "-", "_"], "", $params->phone) : null;
		$params->phone = !empty($params->phone) ? preg_replace("/[\s]/", "", $params->phone) : null;

		// if the email address is not empty
		if(!empty($params->email)) {
			/** Check the email address if not empty */
			if(!isset($params->email) || (isset($params->email) && !filter_var($params->email, FILTER_VALIDATE_EMAIL))) {
				return ["code" => 201, "data" => "Sorry! Provide a valid email address."];
			}
		}

		/** Check the contact number if not empty */
		if((isset($params->phone) && !empty($params->phone) && !preg_match("/^[0-9+]+$/", $params->phone))) {
			return ["code" => 201, "data" => "Sorry! Provide a valid contact number."];
		}
		
		// if the username was parsed and the new one is not the same as the initial one
		if(isset($params->username) && ($params->username !== $the_user[0]->username)) {
			// confirm that the username does not already exist
			$i_params = (object) ["limit" => 1, "username" => $params->username];
			if(!empty($this->quick_list($i_params)["data"])) {
				return ["code" => 201, "response" => "Sorry! The username is already in use."];
			}
		}

		// if the email was parsed and not the same as the initial one
		if(isset($params->email) && ($params->email !== $the_user[0]->email)) {
			// confirm that the email does not already exist
			$i_params = (object) ["limit" => 1, "email" => $params->email];
			// get the user data
			if(!empty($this->quick_list($i_params)["data"])) {
				return ["code" => 201, "response" => "Sorry! The email is already in use."];
			}
			/** Convert the email address to lowercase */
			$params->email = strtolower($params->email);
		}

		$fileName = null;

		// confirm that a logo was parsed
        if(isset($params->image)) {
            // set the upload directory
            $uploadDir = "assets/img/users/";

            // File path config 
            $fileName = basename($params->image["name"]); 

            // check if its a valid image
            if(!empty($fileName) && validate_image($params->image["tmp_name"])){
                // set a new filename
                $fileName = $uploadDir . random_string('alnum', 10)."__{$fileName}";

                // Upload file to the server 
                if(move_uploaded_file($params->image["tmp_name"], $fileName)){}
            } else {
            	$fileName = null;
            }
        }

		// grouping guardian
		$guardian = [];
		if(isset($params->guardian_info) && is_array($params->guardian_info)) {
			foreach($params->guardian_info as $key => $value) {
				foreach($value as $kk => $vv) {
					$guardian[$kk][$key] = $vv;
				}
			}
		}

		// insert the user information
		try {

			// format the date of birth
			$params->date_of_birth = isset($params->date_of_birth) && strtotime($params->date_of_birth) == strtotime(date("Y-m-d")) ? null : ($params->date_of_birth ?? null);

			// begin transaction
			$this->db->beginTransaction();
			$additional = null;

			/** Load the previous record */
            $prevData = $the_user[0];
			
			// usertype and fullname
			$params->client_id = isset($params->clientId) ? strtoupper($params->clientId) : null;
			$params->fullname = $params->firstname. " " . ($params->othername ?? null) . " " .( $params->lastname ?? null);
			
			// convert the user type to lowercase
			$params->user_type = isset($params->user_type) ? $params->user_type : strtolower($the_user[0]->user_type);

			// init course ids
			$course_ids = [];
			// append tutor to Subjects List
			if(isset($params->courses_ids)) {
				// find course ids which were initially attached to the tutor but no longer attached
				$diff = array_diff($prevData->course_ids, $params->courses_ids);

				// append
				$course_ids = $this->append_user_courses($params->courses_ids, $params->user_id, $params->clientId);

				// remove user from courses
				if(!empty($diff)) {
					$this->remove_user_courses($diff, $params->user_id, $params->clientId);
					$course_ids = $params->courses_ids;
				}
			} else {
				$this->remove_all_user_courses($params);
			}

			// log the user access level
			if($params->user_type !== $the_user[0]->user_type) {
				// get the user permissions
				$accessPermissions = $accessObject->getPermissions($params->user_type);

				// if the permission is empty
				if(empty($accessPermissions)) {
					return ["code" => 400, "data" => "Sorry! An invalid user_type was provided for processing."];
				}

				// load the access level permissions
				$permissions = $accessPermissions[0]->user_permissions;
				$access_level = $accessPermissions[0]->id;

				$stmt2 = $this->db->prepare("UPDATE users_roles SET permissions = ? WHERE user_id = ? AND client_id = ? LIMIT 1");
				$stmt2->execute([$permissions, $params->user_id, $params->clientId]);

				// set the value
				$additional = ["href" => "{$this->baseUrl}staff/{$params->user_id}"];
			}

			// get the class id
			$params->class_id = isset($params->class_id) ? $params->class_id : null;

			// convert to arrary if the class id is an array variable
			if(is_array($params->class_id)) {
				$params->class_id = json_encode($params->class_id);
			}

			// if the date of birth is today then set it to null
			if(!empty($params->date_of_birth) && strtotime($params->date_of_birth) == strtotime(date("Y-m-d"))) {
				$params->date_of_birth = null;
			}

			// insert the user information
			$stmt = $this->db->prepare("
				UPDATE users SET last_updated = now(), course_ids = ?
				".(isset($params->firstname) ? ", firstname='{$params->firstname}'" : null)."
				".(isset($params->lastname) ? ", lastname='{$params->lastname}'" : null)."
				".(isset($params->othername) ? ", othername='{$params->othername}'" : null)."
				".(isset($params->fullname) ? ", name='{$params->fullname}'" : null)."
				".(isset($params->email) ? ", email='{$params->email}'" : null)."
				".(isset($params->residence) ? ", residence='{$params->residence}'" : null)."
				".(isset($params->gender) ? ", gender='{$params->gender}'" : null)."
				".(isset($params->user_type) ? ", user_type='{$params->user_type}'" : null)."
				".(!empty($fileName) ? ", image='{$fileName}'" : null)."
				".(isset($access_level) ? ", access_level='{$access_level}'" : null)."
				".(isset($params->is_bus_user) ? ", is_bus_user='{$params->is_bus_user}'" : null)."
				".(isset($params->alergy) ? ", alergy='{$params->alergy}'" : null)."
				".(isset($params->hometown) ? ", hometown='{$params->hometown}'" : null)."

				".(!empty($params->boarding_status) ? ", boarding_status='{$params->boarding_status}'" : null)."
				".(!empty($params->student_type) ? ", student_type='{$params->student_type}'" : null)."
				
				".(isset($params->previous_school) ? ", previous_school='{$params->previous_school}'" : null)."
				".(isset($params->previous_school_remarks) ? ", previous_school_remarks='{$params->previous_school_remarks}'" : null)."
				".(isset($params->previous_school_qualification) ? ", previous_school_qualification='{$params->previous_school_qualification}'" : null)."

				".(isset($params->unique_id) ? ", unique_id='".strtoupper($params->unique_id)."'" : null)."
				".(!empty($params->class_id) ? ", class_id='{$params->class_id}'" : null)."
				".(isset($params->blood_group) ? ", blood_group='{$params->blood_group}'" : null)."
				".(isset($params->religion) ? ", religion='{$params->religion}'" : null)."
				".(isset($params->section) ? ", section='{$params->section}'" : null)."
				".(isset($params->programme) ? ", programme='{$params->programme}'" : null)."
				".(isset($params->department_id) ? ", department='{$params->department_id}'" : null)."
				".(isset($params->enrollment_date) ? ", enrollment_date='{$params->enrollment_date}'" : null)."

				".(isset($params->relationship) ? ", relationship='{$params->relationship}'" : null)."

				".(isset($params->username) ? ", username='{$params->username}'" : null)."
				".(isset($params->created_by) ? ", created_by='{$params->created_by}'" : null)."
				".(isset($params->password) ? ", password='{$params->password}'" : null)."
				".(isset($params->position) ? ", position='{$params->position}'" : null)."
				".(isset($params->phone) ? ", phone_number='{$params->phone}'" : null)."
				".(isset($params->employer) ? ", employer='{$params->employer}'" : null)."
				".(isset($params->phone_2) ? ", phone_number_2='{$params->phone_2}'" : null)."
				".(isset($params->address) ? ", address='{$params->address}'" : null)."
				".(isset($params->description) ? ", description='{$params->description}'" : null)."
				".(isset($params->occupation) ? ", occupation='{$params->occupation}'" : null)."
				".(isset($params->postal_code) ? ", postal_code='{$params->postal_code}'" : null)."
				".(isset($params->nationality) ? ", nationality='{$params->nationality}'" : null)."
				".(isset($params->country) ? ", country='{$params->country}'" : null)."
				".(isset($params->city) ? ", city='{$params->city}'" : null)."
				".(!empty($params->date_of_birth) ? ", date_of_birth='{$params->date_of_birth}'" : null)."
				WHERE item_id = ? AND client_id = ? LIMIT 1
			");

			// execute the insert user data
			$stmt->execute([json_encode($course_ids), $params->user_id, $params->clientId]);

			$guardian_ids = [];
			$theClientData = !empty($this->iclient) ? $this->iclient : $this->client_data($params->clientId);

			// update the user guardian information
			if(!empty($guardian)) {

				// loop through the guardian list
				foreach($guardian as $key => $value) {
					
					// process if the fullname is not empty
					if(!empty($value["guardian_fullname"])) {

						// explode the first and lastnames
						$expl = explode(" ", $value["guardian_fullname"]);
						
						// use the first and second index as the names
						$firstname = $expl[0];
						$lastname = isset($expl[2]) ? $expl[2] : ($expl[1] ?? null);
						$othername = isset($expl[2]) ? $expl[2] : null;

						// generate a new unique user id
						$counter = $this->append_zeros(($this->itemsCount("users", "client_id = '{$params->clientId}' AND user_type='parent'") + 1), $this->append_zeros);
						$unique_id = strtoupper($theClientData->client_preferences->labels->parent_label.$counter.date("Y"));

						// create a new random string
						$guardian_id = random_string("alnum", RANDOM_STRING);

						// join the names as the fullname
						$fullname = "{$firstname} {$othername} {$lastname}";
						$p_username = !empty($value["guardian_email"]) ? $value["guardian_email"] : "";

						// confirm that the guardian information does not already exist
						if(empty($this->pushQuery("id", "users", "item_id='{$value["guardian_id"]}' AND status='1' AND user_type='parent' LIMIT 1"))) {
							// insert the new record
							$stmt = $this->db->prepare("INSERT INTO users SET 
								firstname = ?, lastname = ?, othername = ?, name = ?,
								phone_number = ?, email = ?, `relationship` = ?, 
								`address` = ?, `unique_id` = ?, `client_id` = ?, 
								user_type = ?, item_id = ?, username = ?
							");
							$stmt->execute([
								$firstname, $lastname, $othername, $fullname, 
								$value["guardian_contact"], $value["guardian_email"], 
								$value["guardian_relation"], $value["guardian_address"], 
								$unique_id, $params->clientId, 'parent', $guardian_id, $p_username
							]);
						}
						// update the guardian record if the information already exist
						else {
							$stmt = $this->db->prepare("UPDATE users SET 
								firstname = ?, lastname = ?, othername = ?, name = ?, username = ?,
								phone_number = ?, email = ?, `relationship` = ?, `address` = ? 
								WHERE `item_id` = ? AND `client_id` = ? AND user_type = ? LIMIT 1
							");
							$stmt->execute([
								$firstname, $lastname, $othername, $fullname, $p_username, $value["guardian_contact"], 
								$value["guardian_email"], $value["guardian_relation"], $value["guardian_address"], 
								$value["guardian_id"], $params->clientId, 'parent'
							]);
						}
						$guardian_ids[] = $value["guardian_id"];
					}
				}
				// update the user information if the guardian_ids is not empty
				if(!empty($guardian_ids)) {
					$this->db->query("UPDATE users SET guardian_id='".implode(",", $guardian_ids)."' WHERE item_id='{$params->user_id}' AND user_type='student' LIMIT 1");
				}
			}

			$redirect = ($params->user_type === "student") ? "student" : ($params->user_type === "parent" ? "guardian" : "staff");

			// save the name change
            if(!empty($params->fullname) && ($prevData->name !== $params->fullname)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->name, "Name was changed from {$prevData->name}", $params->userId);

				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }

			// save the email address
            if(!empty($params->email) && ($prevData->email !== $params->email)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->email, "Email Address was changed from {$prevData->email}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }
			
			// save the postal address changes
            if(!empty($params->address) && ($prevData->address !== $params->address)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->address, "Postal Address has been changed.", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }

			// save the date of birth change
            if(!empty($params->date_of_birth) && ($prevData->date_of_birth !== $params->date_of_birth)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->date_of_birth, "Date of Birth has been changed to {$params->date_of_birth}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }

			// save the phone_number change
            if(!empty($params->description) && ($prevData->description !== $params->description)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->description, "User description was altered.", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }

			// save the phone_number_2 change
            if(!empty($params->phone_2) && ($prevData->phone_number !== $params->phone_2)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->phone_number_2, "Primary Contact was been changed from {$prevData->phone_number_2}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }
			
			// save the occupation
            if(!empty($params->occupation) && ($prevData->occupation !== $params->occupation)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->occupation, "Occupation has been altered. {$prevData->occupation} => {$params->occupation}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }
			
			// save the employer
            if(!empty($params->employer) && ($prevData->employer !== $params->employer)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->employer, "Employer details has been altered. {$prevData->employer} => {$params->employer}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }
			
			// save the position
            if(!empty($params->position) && ($prevData->position !== $params->position)) {
                $this->userLogs("{$params->user_type}_account", $params->user_id, $prevData->position, "Position has been altered. {$prevData->position} => {$params->position}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
            }

			// insert the user activity
			if($params->user_id == $params->userId) {
				// Insert the log
				$this->userLogs("{$params->user_type}_account", $params->user_id, null, "You updated your account information", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}{$redirect}/{$params->user_id}"];
			} else {
				// notification object
				global $noticeClass;
				
				// Insert the log
				$this->userLogs("{$params->user_type}_account", $params->user_id, null, "<strong>".($defaultUser->name ?? null)."</strong> updated the account information of <strong>{$the_user[0]->name}</strong>", $params->userId);
				
				// Notify the user that his/her account has been modified
				$param = (object) [
					'_item_id' => random_string("alnum", RANDOM_STRING),
					'user_id' => $params->user_id,
					'subject' => "Account Update",
					'message' => "<strong>".($defaultUser->name ?? null)."</strong> updated your account information",
					'notice_type' => 9,
					'userId' => $params->userId,
					'initiated_by' => 'system'
				];
				
				// add a new notification
				$noticeClass->add($param);
			}

			// commit all opened transactions
			$this->db->commit();

			// set the url
			$url_link = ($params->user_type == "student") ? "{$this->baseUrl}student/{$params->user_id}" : null;

			// append the redirection url
			if($url_link) { $additional = ["href" => $url_link]; }

			#record the password change request
            return ["code" => 200, "data" => ucfirst($redirect). " record successfully updated.", "additional" => $additional ];

		} catch(PDOException $e) {
			$this->db->rollBack();
			return false;
		}

	}

    /**
     * Upload Resource
     * 
     * Upload e-version of the resources attached to this resource
     * 
     * @return Array 
     */
    public function upload_documents(stdClass $params) {

        try {

	        // old record
	        $module = "staff_documents_{$params->employee_id}";

	        // return error message if no attachments has been uploaded
		    if(empty($this->session->{$module})) {
		        return ["code" => 400, "data" => "Sorry! Please upload files to be uploaded."];
		    }

        	// get the previous data
	        $prevData = $this->pushQuery("a.id, a.item_id, a.user_type, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment", 
	            "users a", "a.item_id='{$params->employee_id}' AND a.client_id='{$params->clientId}' LIMIT 1");

	        // if empty then return
	        if(empty($prevData)) {
	            return ["code" => 400, "data" => "Sorry! An invalid staff id was supplied."];
	        }

	        // initialize
	        $initial_attachment = [];

	        /** Confirm that there is an attached document */
	        if(!empty($prevData[0]->attachment)) {
	            // decode the json string
	            $db_attachments = !empty($prevData[0]->attachment) ? json_decode($prevData[0]->attachment) : [];
	            // get the files
	            if(isset($db_attachments->files)) {
	                $initial_attachment = $db_attachments->files;
	            }
	        }

	        // prepare the attachments
	        $filesObj = load_class("files", "controllers");
	        $attachments = $filesObj->prep_attachments($module, $params->userId, $params->employee_id, $initial_attachment);
	        
	        // update attachment if already existing
	        if(isset($db_attachments)) {
	            $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? AND resource = ? LIMIT 1");
	            $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $params->employee_id, "employee_document"]);
	        } else {
	            // insert the record if not already existing
	            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
	            $files->execute(["employee_document", $params->employee_id, json_encode($attachments), $params->employee_id, $params->userId, $attachments["raw_size_mb"], $params->clientId]);
	        }

	        return [
	            "code" => 200,
	            "additional" => [
	            	"url_link" => $prevData[0]->user_type == "student" ? "student" : "staff",
	            ],
	            "data" => "Files was successfully uploaded"
	        ];

	    } catch(PDOException $e) {
	    	return $this->unexpected_error;
	    }

    }

	/**
	 * Resend verification token to the user email
	 * Generate a new verify token and set the expiry to 6 hours from the time of creation
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function resend_token(stdClass $params) {
		
		/** Check the email address if not empty */
		if(!isset($params->email) || (isset($params->email) && !filter_var($params->email, FILTER_VALIDATE_EMAIL))) {
			return ["code" => 201, "response" => "Sorry! Provide a valid email address."];
		}

		/** Check the username if not empty */
		if(!isset($params->user_id) || (isset($params->user_id) && strlen($params->user_id) < 8)) {
			return ["code" => 201, "response" => "Sorry! Please provide a valid user id."];
		}

		try {

			// confirm that the user_id does not already exist
			$i_params = (object) ["limit" => 1, "user_id" => $params->user_id];
			$user = $this->list($i_params)["data"];
			// get the user data
			if(empty($user)) {
				return ["code" => 201, "response" => "Sorry! Please provide a valid user id."];
			}
			
			#set the token expiry time to 6 hours from the moment of request
			$token = random_string("alnum", mt_rand(15, 30));
            $token_expiry = time()+(60*60*6);

			// update the last login for this user
			$stmt = $this->db->prepare("UPDATE users SET verify_token = ?, token_expiry = ? WHERE item_id=? LIMIT 1");
			$stmt->execute([$token, $token_expiry, $params->user_id]);

			// email comfirmation link
			$message = 'Hi '.$user[0]->name ?? null;
			$message .= '<br><br>We click to';
			$message .= '<a class="alert alert-success" href="'.$this->baseUrl.'verify?account&token='.$token.'">Verify your account</a>';
			$message .= '<br><br>If it does not work please copy this link and place it in your browser url.<br><br>';
			$message .= $this->baseUrl.'verify?account&token='.$token;

			// recipient list
			$reciepient = ["recipients_list" => [["fullname" => $user[0]->name ?? null, "email" => $params->email,"customer_id" => $params->user_id]]];

			// insert the email content to be processed by the cron job
			$stmt = $this->db->prepare("
				INSERT INTO users_messaging_list SET template_type = ?, item_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?, users_id = ?
			");
			$stmt->execute([
				'account-verify', random_string("alnum", RANDOM_STRING), json_encode($reciepient),
				$params->user_id, "[{$this->appName}] Account Verification", $message, $params->user_id
			]);

			// insert the user activity
			$this->userLogs("Account Verification", $params->user_id, null, "{$user[0]->name}' has requested that an new verification token be sent via email.", $params->userId ?? $params->user_id, "System Calculation<br>The user triggered the resending of the Account Activation Token.");

			return ["code" => 200, "response" => "The verification token has been generated and sent via email."];

		} catch(PDOException $e) {
			$this->db->rollBack();
			return ["code" => 201, "response" => "Sorry! There was an error while processing the request."];
		}
	}

	/**
	 * Reset the user password
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function reset_password(stdClass $params) {
		
		/** Global variables */
		global $accessObject;

		if(empty($params->password) || empty($params->confirm_password)) {
			return ["code" => 201, "response" => "Sorry! Please provide the password and confirm the password."];
		}

		if($params->password !== $params->confirm_password) {
			return ["code" => 201, "response" => "Sorry! The password and confirm password does not match."];
		}

		if(!passwordTest($params->password)) {
			return ["code" => 201, "response" => "Sorry! The password provided is not strong enough."];
		}

		// check if the user has the permission to manage the settings
		$isSupport = $accessObject->hasAccess("manage", "settings");

		/** Confirm the user permissions */
		if(!$isSupport && !$accessObject->hasAccess("change_password", "permissions")) {
			return ["code" => 201, "data" => $this->permission_denied];
		}

		// set the where clause
		$whereClause = $isSupport ? [
			"limit" => 1, "unique_or_item_id" => $params->user_id, "minified" => false
		] : [
			"limit" => 1, "unique_or_item_id" => $params->user_id, "clientId" => $params->clientId, "minified" => false
		];

		// get the user record
		$user = $this->list((object) $whereClause)["data"];
		$userData = $user[0] ?? [];

		// if the user record is empty
		if(empty($userData)) {
			return ["code" => 201, "response" => "Sorry! An invalid user id was supplied"];
		}

		#encrypt the password
		$password = password_hash($params->password, PASSWORD_DEFAULT);
                    
		#deactivate all reset tokens
		$stmt = $this->db->prepare("UPDATE users SET password = ?, changed_password = ? WHERE (item_id = ? OR unique_id = ?) LIMIT 10");
		$stmt->execute([$password, 1, $params->user_id, $params->user_id]);

		// admin name
		$adminName = $params->userData->name;

		#record the activity
		$this->userLogs("change-password", $params->user_id, null, "{$adminName} successfully changed {$userData->name}'s password.", $params->userId ?? $params->user_id);

		// return the success response
		return [
			"data" => ucwords($userData->name) . "'s password has been successfully updated.",
			"additional" => [
				"clear" => true,
				"close_modal" => "reset_password_mod"
			]
		];
	}

	/**
	 * Activate the user account by seting the activation status to 1
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function activate_account(stdClass $params) {
		
		/** Check the username if not empty */
		if(!isset($params->verify_token) || (isset($params->verify_token) && strlen($params->verify_token) < 60)) {
			return ["code" => 201, "response" => "Sorry! Please provide the verification token generated."];
		}

		/** Check the username if not empty */
		if(!isset($params->user_id) || (isset($params->user_id) && strlen($params->user_id) < 8)) {
			return ["code" => 201, "response" => "Sorry! Please provide a valid user id."];
		}

		try {

			// confirm that the user_id does not already exist
			$i_params = (object) ["limit" => 1, "user_id" => $params->user_id];
			$user = $this->list($i_params)["data"];
			// get the user data
			if(empty($user)) {
				return ["code" => 201, "response" => "Sorry! Please provide a valid user id."];
			}

			// activate the user account
			$stmt = $this->db->prepare("UPDATE users SET verify_token = ?, token_expiry = ?, status = ?, user_status = ?, verified_email = ?, verified_date = now() WHERE item_id=? LIMIT 1");
			$stmt->execute([NULL, NULL, 1, "Active", "Y", $params->user_id]);

			// insert the user activity
			$this->userLogs("User Account", $params->user_id, null, "{$user[0]->name}'s - account was successfully activated.", $params->userId ?? $params->user_id, "Account was manually activated using the Activation link.");

			// success response
			return ["code" => 200, "response" => "The account has successfully been activated."];

		} catch(PDOException $e) {
			$this->db->rollBack();
			return ["code" => 201, "response" => "Sorry! There was an error while processing the request."];
		}
	}

	/**
	 * Initialize the IndexDB on the Users Account
	 * Afterwards update the user information
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function preference(stdClass $params) {
		
		/** User id */
		$userId = isset($params->the_user_id) ? $params->the_user_id : $params->userData->user_id;

		/** Check the username if not empty */
		if(!isset($userId)) {
			return ["code" => 201, "data" => "Sorry! Please provide a valid user id."];
		}

		// confirm that the user_id does not already exist
		$i_params = (object) ["limit" => 1, "user_id" => $userId];
		$the_user = $this->list($i_params)["data"];

		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "data" => "Sorry! Please provide a valid user id."];
		}

		/** Init the data */
		$data = "Nothing to process";

		// contain the error message if any
		try {
			/** Set the use data */
			$userData = $params->userData;

			/** If the label is index db */
			if(isset($params->label) && ($params->label == "init_idb")) {
				
				// set the counter variables
				$countVars = (object) [
					"remote" => false,
					"param" => [
						"records_count" => [
							"messages" => [
								"record_id" => $userData->user_id,
								"column" => "messages_count"
							]
						]
					]
				];

				// create new object
				$records = load_class("records", "controllers");

				/** Update the user information */
				$userData->preferences->idb_init->init = 0;
				$userData->preferences->idb_init->idb_last_init = date("Y-m-d H:i:s");
				$userData->preferences->idb_init->idb_next_init = date("Y-m-d H:i:s", strtotime("+2 days"));
				$preferences = $userData->preferences;

				/** Load the policy forms information */
				$data = [
					"user_preferences" => $userData->preferences,
					"user_information" => $userData,
					"users_list" => $this->prependData("a.id, a.item_id, a.unique_id, a.email, a.employer, a.date_created, a.last_login, a.user_type, a.description, a.position, a.name, a.phone_number, a.phone_number_2, a.date_of_birth, a.nationality, a.residence, a.occupation, a.image, a.address, (SELECT country_name b FROM country b WHERE b.id = a.country) AS country_name", "users a", "a.status='1' AND a.deleted='0' AND a.client_id='{$params->clientId}'"),
				];

				/** Update the table */
				$stmt = $this->db->prepare("UPDATE users SET preferences = ? WHERE item_id=? LIMIT 1");
				$stmt->execute([json_encode($preferences), $userData->user_id]);
			}

			/** Update the user preferences by updating the sidebar parameter */
			elseif(is_array($params->label)) {
				// accepted list
				$accepted_prefs = [
					"theme_color", "font_size", "sidebar_nav", "list_count", "sidebar_color", "new_policy_notification",
					"quick_links", "auto_close_modal", "text_editor", "messages", "payments"
				];
				// loop through the list and append to the array list
				foreach($params->label as $key => $value) {
					// if the preference parsed is accepted
					if(in_array($key, $accepted_prefs)) {
						// set the key and value for this array item
						$userData->preferences->$key = $value;
					} else {
						// end the query
						return ["code" => 201, "data" => "Sorry! An invalid preference was parsed."];
						break;
					}
				}
				// set the user preferences
				$preferences = $userData->preferences;
				/** Update the table */
				$stmt = $this->db->prepare("UPDATE users SET preferences = ? WHERE item_id=? LIMIT 1");
				$stmt->execute([json_encode($preferences), $userId]);
				/** Set the data */
				$data = "User preferences successfully updated";
				// log user activity
				if($userId == $params->userId) {
					$this->userLogs("user-preferences", $userId, $the_user[0]->preferences, "Your preference was successfully updated.", $params->userId);
				} else {
					$this->userLogs("user-preferences", $userId, $the_user[0]->preferences, "<strong>{$params->userData->name}</strong> updated the preferences of <strong>{$the_user[0]->name}</strong>.", $params->userId);
				}
			}
			/** List the user quick links preferences */
			elseif(isset($params->label) && ($params->label == "list_quick_links")) {
				/** Load the user preferences */
				$data = [
					"text" => quick_links($userData->preferences->quick_links ?? null),
					"scripts" => ["assets/vendors/feather-icons/feather.min.js"]
				];
			}

			// return the results
			return [
				"code" => 200,
				"data" => $data
			];
		} catch(PDOException $e) {
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
		}

	}
	
	/**
	 * Load user permissions
	 * 
	 * @param object $params
	 * @param String $params->user_id
	 * 
	 * @return Array
	 */
	public function load_permissions(stdClass $params) {

		/** Global variables */
		global $accessObject;

		/** Confirm the user permissions */
		if(!$accessObject->hasAccess("permissions", "users")) {
			return ["code" => 201, "data" => $this->permission_denied];
		}

		// confirm that the user_id does not already exist
		$i_params = (object) ["limit" => 1, "user_id" => $params->user_id, "columns" => "id, item_id", "remote" => true];
		$the_user = $this->list($i_params)["data"];
		
		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "response" => "Sorry! Please provide a valid user id."];
		}

		// Load the user permissions
		$permissions = $this->pushQuery("permissions", "users_roles", "user_id='{$params->user_id}' LIMIT 1");

		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "response" => "Sorry! No permissions was found for this user."];
		}

		// get the permissions
		$permissions = !empty($permissions[0]->permissions) ? json_decode($permissions[0]->permissions) : [];

		// return the permissions
		return [
			"code" => 200,
			"data" => $permissions
		];

	}

	/**
	 * Save the user permissions
	 * 
	 * @param object $params
	 * @param String $params->user_id
	 * @param String $params->access_level
	 * @param Array $params->permissions_list
	 * 
	 * @return Array
	 */
	public function save_permission(stdClass $params) {

		/** Global variables */
		global $accessObject;
		
		/** Confirm the user permissions */
		if(!$accessObject->hasAccess("update", "permissions")) {
			return ["code" => 201, "data" => $this->permission_denied];
		}

		/** Confirm that the access_level parameter has an array value */
		if(!is_array($params->access_level)) {
			return ["code" => 201, "response" => "Sorry! Permissions list must be a valid array format."];
		}

		// confirm that the user_id does not already exist
		$the_user = $this->pushQuery(
			"a.id, (SELECT b.permissions FROM users_roles b
				WHERE b.user_id='{$params->user_id}' AND 
					b.client_id='{$params->clientId}' LIMIT 1) AS user_permissions", 
			"users a", "a.item_id='{$params->user_id}' LIMIT 1");
		
		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "response" => "Sorry! Please provide a valid user id."];
		}

		// get the first key
		$accessLevel = [];
		$permissions = [];
		$the_user = $the_user[0];

		// run this section if the access level permissions were parsed
		if(!empty($params->access_level)) {
			// initialiate
			// clean the access permissions well
			foreach($params->access_level as $eachKey => $eachValue) {
				foreach($eachValue as $key => $value) {
					foreach($value as $i => $e) {
						$accessLevel[$eachKey][$key] = ($e == "on") ? 1 : 0;
					}
				}
			}
			$permissions["permissions"] = $accessLevel;
		}

		// append the user permissions to the record set
		if(!empty($the_user->user_permissions) && !empty($params->append_permit)) {
			$permits = json_decode($the_user->user_permissions, true);
			foreach($permits["permissions"] as $key => $value) {
				$permissions["permissions"][$key] = array_merge($permissions["permissions"][$key] ?? [], $value);
			}
		}

		try {
			// confirm if the user permission is not empty
			if(!empty($the_user->user_permissions)) {

				// update the user permissions
				$stmt = $this->db->prepare("UPDATE users_roles SET permissions = ? WHERE user_id=? AND client_id = ? LIMIT 1");
				$stmt->execute([json_encode($permissions), $params->user_id, $params->clientId]);

			} else {
				// insert a new record
				$stmt = $this->db->prepare("INSERT INTO users_roles SET permissions = ?, user_id=?, client_id = ?");
				$stmt->execute([json_encode($permissions), $params->user_id, $params->clientId]);
			}

			// return the success response
			return [
				"code" => 200,
				"data" => "User permissions successfully updated."
			];

		} catch(PDOException $e) {
			return $this->unexpected_error;
		}

	}

	/**
	 * Set the default student id
	 * 
	 * @param object $params
	 * @param String $params->student_id
	 * 
	 * @return Array
	 */
	public function set_default_student(stdClass $params) {

		if(empty($params->student_id)) {
			return [
				"code" => 400,
				"data" => "Sorry! Please provide a valid student id."
			];
		}

		// if the student id is to be removed
		if($params->student_id == "remove") {
			$this->session->set([
				"student_id" => null,
				"student_class_row_id" => null,
				"student_class_id" => null,
				"student_courses_id" => null,
				"last_TimetableId" => null
			]);
			return [
				"code" => 200,
				"data" => "Student Id successfully removed"
			];
		}

		// get the student class id
		$stmt = $this->db->prepare("SELECT 
				c.id AS class_row_id, c.item_id AS class_guid, u.last_timetable_id
			FROM users u
			LEFT JOIN classes c ON u.class_id = c.id
			WHERE u.item_id = ? AND u.client_id = ? LIMIT 1
		");
		$stmt->execute([$params->student_id, $params->clientId]);
		$result = $stmt->fetch(PDO::FETCH_OBJ);

		// if the class guid was set and also not empty
		if(isset($result->class_guid) && !empty($result->class_guid)) {
			$stmt = $this->db->prepare("SELECT 
					GROUP_CONCAT(cl.id) AS courses_ids 
				FROM courses cl WHERE cl.class_id LIKE '%{$result->class_guid}%'
					AND cl.academic_year = '{$params->academic_year}' 
					AND cl.academic_term = '{$params->academic_term}'
					AND cl.status = '1' AND cl.client_id = '{$params->clientId}'
				LIMIT {$this->temporal_maximum}
			");
			$stmt->execute();
			$result->courses_ids = $stmt->fetch(PDO::FETCH_OBJ)->courses_ids ?? null;
		}

		// set the student and class id
		$this->session->set([
			"student_id" => $params->student_id,
			"student_class_row_id" => $result->class_row_id,
			"student_class_id" => $result->class_guid ?? null,
			"student_courses_id" => $result->courses_ids ?? null,
			"last_TimetableId" => $result->last_timetable_id ?? null
		]);

		// return the success message
		return [
			"code" => 200,
			"data" => "Student Id successfully changed"
		];

	}

	/**
	 * Append User Courses
	 * 
	 * Loop through the courses that the user has been attached to 
	 * If not in the courses tutors list then append the user_id to it
	 * 
	 * @return Bool
	 */
	public function append_user_courses($courses_ids, $user_id, $client_id) {

		$courses_ids = $this->stringToArray($courses_ids);
		$valid_ids = [];

		foreach($courses_ids as $course) {
			$query = $this->pushQuery("course_tutor", "courses", "id='{$course}' AND client_id='{$client_id}' LIMIT 1");
			if(!empty($query)) {
				$valid_ids[] = $course;
				if(!empty($query[0]->course_tutor)) {
					$result = !empty($query[0]->course_tutor) ? json_decode($query[0]->course_tutor, true) : [];
					if(!in_array($user_id, $result)) {
						array_push($result, $user_id);
						$this->db->query("UPDATE courses SET course_tutor = '".json_encode($result)."' WHERE id='{$course}' LIMIT 1");
					}
				} else {
					$tutors = [$user_id];
					$this->db->query("UPDATE courses SET course_tutor = '".json_encode($tutors)."' WHERE id='{$course}' LIMIT 1");
				}
			}
		}
		return $valid_ids;

	}

	/**
	 * Remove User From All Courses
	 * 
	 * Loop through the courses that the user has been attached to 
	 * If not in the courses tutors list then append the user_id to it
	 * 
	 * @return Bool
	 */
	public function remove_all_user_courses(stdClass $params) {

		$courses_ids = $this->pushQuery("course_tutor, id", "courses", 
			"client_id='{$params->clientId}' AND academic_year='{$params->academic_year}' 
			AND academic_term='{$params->academic_term}' AND status='1' LIMIT {$this->temporal_maximum}");

		foreach($courses_ids as $course) {
			if(!empty($course->course_tutor)) {
				$result = !empty($course->course_tutor) ? json_decode($course->course_tutor, true) : [];
				if(in_array($params->user_id, $result)) {
					$key = array_search($params->user_id, $result);
					if($key !== FALSE) {
						unset($result[$key]);
						$this->db->query("UPDATE courses SET course_tutor = '".json_encode($result)."' WHERE id='{$course->id}' AND status='1' LIMIT 1");
					}
				}
			}
		}
		return true;

	}

	/**
	 * Unattach a course from a user
	 * 
	 * @return Bool
	 */
	public function remove_user_courses($courses_ids, $user_id, $client_id) {

		$courses_ids = $this->stringToArray($courses_ids);
		
		foreach($courses_ids as $course) {
			$query = $this->pushQuery("course_tutor", "courses", "id='{$course}' AND client_id='{$client_id}' LIMIT 1");
			if(!empty($query)) {
				if(!empty($query[0]->course_tutor)) {
					$result = !empty($query[0]->course_tutor) ? json_decode($query[0]->course_tutor, true) : [];
					if(in_array($user_id, $result)) {
						$key = array_search($user_id, $result);
						unset($result[$key]);
						$this->db->query("UPDATE courses SET course_tutor = '".json_encode($result)."' WHERE id='{$course}' LIMIT 1");
					}
				}
			}
		}
	}

	/**
	 * Bulk Update Students Record
	 * 
	 * @param object $params
	 * @param Array		$params->dob
	 * @param Array		$params->end
	 * @param Array 	$params->img
	 * 
	 * @return Array
	 */
	public function bulk_update(stdClass $params) {

		$students_array_list = [];

		// append the date of birth of student
		if(isset($params->dob) && is_array($params->dob)) {
			// loop through the date of birth array list
			foreach($params->dob as $student_id => $dob) {
				if($dob !== "1970-01-01") {
					$students_array_list[$student_id]["date_of_birth"] = $dob;
				}
			}
		}

		// append the enrollment date
		if(isset($params->end) && is_array($params->end)) {
			// loop through the enrollment date array list
			foreach($params->end as $student_id => $end) {
				$students_array_list[$student_id]["enrollment_date"] = $end;
			}
		}

		// append the enrollment date
		if(isset($params->gender) && is_array($params->gender)) {
			// loop through the enrollment date array list
			foreach($params->gender as $student_id => $gender) {
				$students_array_list[$student_id]["gender"] = $gender;
			}
		}

		// append the primary contact number
		if(isset($params->ph) && is_array($params->ph)) {
			// loop through the enrollment date array list
			foreach($params->ph as $student_id => $phone_number) {
				if(!empty($phone_number)) {
					$students_array_list[$student_id]["phone_number"] = substr($phone_number, 0, 10);
				}
			}
		}

		// append the secondary contact number
		if(isset($params->ph2) && is_array($params->ph2)) {
			// loop through the enrollment date array list
			foreach($params->ph2 as $student_id => $phone_number) {
				if(!empty($phone_number)) {
					$students_array_list[$student_id]["phone_number_2"] = substr($phone_number, 0, 10);
				}
			}
		}

		// set the upload directory
		$uploadDir = "assets/img/users/";
		$allowTypes = array('jpg', 'png', 'jpeg'); 

		// append the student image
		if(isset($params->img) && is_array($params->img)) {
			// loop through the student image array list
			foreach($params->img["name"] as $student_id => $image) {
				if(!empty($image)) {
					// if the file is an image file
					if(in_array($params->img["type"][$student_id], ["image/jpeg", "image/png", "image/jpg"])){
						$students_array_list[$student_id]["image"] = $uploadDir . random_string('alnum', 10)."__{$image}";
						$students_array_list[$student_id]["tmp_name"] = $params->img["tmp_name"][$student_id];
					}
				}
			}
		}

		// return error if no student record was parsed
		if(empty($students_array_list)) {
			return ["code" => 400, "data" => "Sorry! No student record was parsed for processing."];
		}

		// create a new $scheduler_id
		$scheduler_id = random_string("alnum", RANDOM_STRING);

		// insert the client user data into the cron scheduler table
        $stmt = $this->db->prepare("INSERT INTO cron_scheduler SET client_id = '{$params->clientId}', item_id = ?, user_id = ?, cron_type = ?, subject = ?, active_date = '{$this->current_timestamp}', query = ?");
        $stmt->execute([$scheduler_id."_".$params->clientId, $params->userId, "bulk_student_update", "Bulk Student Update", json_encode($students_array_list)]);

		// loop through the entire students list
		// foreach($students_array_list as $student_id => $data) {

			// update the image if parsed
			// if(isset($data["image"])) {
			// 	move_uploaded_file($data["tmp_name"], $data["image"]);
			// }

			// update the student record
			// $this->db->query("UPDATE users SET last_updated = now()
			// 	".(isset($data["phone_number_2"]) ? ", phone_number_2 = '{$data["phone_number_2"]}'" : null)."
			// 	".(isset($data["phone_number"]) ? ", phone_number = '{$data["phone_number"]}'" : null)."
			// 	".(isset($data["gender"]) ? ", gender = '{$data["gender"]}'" : null)."
			// 	".(!empty($data["date_of_birth"]) ? ", date_of_birth = '{$data["date_of_birth"]}'" : null)."
			// 	".(!empty($data["enrollment_date"]) ? ", enrollment_date = '{$data["enrollment_date"]}'" : null)."
			// 	".(!empty($data["image"]) ? ", image = '{$data["image"]}'" : null)."
			// 	WHERE id='{$student_id}' AND client_id='{$params->clientId}' AND user_type = 'student' LIMIT 1
			// ");

		// }

		// return successfull message
		return [
			"code" => 200,
			"data" => "Student data was successfully updated."
		];

	}

	/**
	 * Change the status of a list of students
	 * 
	 * @param object $params
	 * @param Array		$params->student_id
	 * @param String		$params->user_status
	 * @param String 	$params->description
	 * 
	 * @return Array
	 */
	public function change_status(stdClass $params) {

		try {

			// confirm that the student id variable is an array
			if(!is_array($params->user_id)) {
				return ["code" => 400, "data" => "Sorry! The user_id variable must be an array."];
			}

			// prepare the statement
			$update = $this->db->prepare("UPDATE users SET user_status = ? WHERE id = ? AND client_id = ? AND can_change_status = ? LIMIT 1");

			// loop through the students ids list and update their respective record list
			foreach($params->user_id as $user_id) {
				// update the results
				$update->execute([$params->user_status, $user_id, $params->clientId, 1]);
			}

			return [
				"data" => "Status change was successful",
				"additional" => [
					"clear" => true,
					"href" => $this->session->user_current_url
				]
			];

		} catch(PDOException $e) {}

	}

    /**
     * Update the user online status
     * 
     * @param String $userId
     * 
     * @return Bool
     */
    final function update_onlineStatus($userId) {
        
        try {

            /** prepare and execute the statement */
            $stmt = $this->db->prepare("UPDATE users SET online='1', last_seen = '{$this->current_timestamp}' WHERE item_id = ? LIMIT 1");
            return $stmt->execute([$userId]);

        } catch(PDOException $e) {
            print "fuck it";
        }
    }

	/**
	 * Generate User Content for Export
	 * @param object $params
	 * @param String	$params->user_id
	 * @param String	$params->clientId
	 * 
	 * @return Array
	 */
	public function export(stdClass $params) {

		try {

			global $clientPrefs, $academicSession;

			// set the client data
			$client = $params->client_data;

			// generate a new unique user id
			$client = !empty($client) ? $client : $this->client_data($params->clientId);

			// get the client logo content
            if(!empty($client->client_logo)) {
                $type = pathinfo($client->client_logo, PATHINFO_EXTENSION);
                $logo_data = file_get_contents($client->client_logo);
                $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
            }

            // get the user data
            $user_record = $this->quick_list($params)["data"][0] ?? null;

            // confirm that the user record was found
            if(empty($user_record)) {
            	return "<h3>Sorry! An invalid user id was parsed for processing.</h3>";
            }

            // get additional information
            // return $user_record;

			// set the bill form
			$html_content = '
			<div style="margin:auto auto;background: #ffffff none repeat scroll 0 0;border-bottom: 2px solid #f4f4f4;position: relative;box-shadow: 0 1px 2px #acacac;width:100%;font-family: \'Calibri Regular\'; width:100%;margin-bottom:2px">
				<div class="row mb-3">
					<div class="text-dark bg-white col-md-12" style="padding-top:20px;width:90%;margin:auto auto;">
						<div align="center">
							'.(!empty($client->client_logo) ? "<img width=\"70px\" src=\"{$client_logo}\">" : "").'
							<h2 style="color:#6777ef;font-size:25px;font-family:helvetica;padding:0px;margin:0px;"> '.strtoupper($client->client_name).'</h2>
							<div>'.$client->client_address.'</div>
							'.(!empty($client->client_contact) ? "<div><strong>Tel:</strong> {$client->client_contact} / {$client->client_secondary_contact}</div>" : "").'
							'.(!empty($client->client_email) ? "<div><strong>Email:</strong> {$client->client_email}</div>" : "").'
						</div>
						<div style="background-color: #2196F3 !important;margin-top:5px;border-bottom: 1px solid #dee2e6 !important;height:3px;"></div>

							<style>table.table tr td {border:solid 1px #dad7d7;padding:5px;}</style>

							<table border="0" width="100%">
								<tr>
									<td align="center" colspan="2">
										<h3 style="border-bottom:solid 1px #ccc;padding:0px;padding-bottom:5px;margin:0px;">
											EXPORT '.strtoupper($user_record->user_type).' RECORD
										</h3>
									</td>
								</tr>
								<tr>
									<td width="100%">
										<table border="0" class="table" width="100%">
											<tr style="font-size:17px;">
												<td>
													<div style="text-transform:uppercase;margin-bottom:5px;">
														Name: <strong>'.$user_record->name.'</strong>
													</div>
												</td>
												<td>
													<div style="text-transform:uppercase;margin-bottom:5px;">
														'.strtoupper($user_record->user_type).' ID: <strong>'.$user_record->unique_id.'</strong>
													</div>
												</td>
												<td>
													<div style="text-transform:uppercase;margin-bottom:5px;">
														Class: <strong>'.$user_record->class_name.'<strong>
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>';


			return $html_content;

		} catch(PDOException $e) {}

	}
	
}
?>