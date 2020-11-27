<?php
// ensure this file is being included by a parent file
if( !defined( 'BASEPATH' ) ) die( 'Restricted access' );

class Users extends Myschoolgh {

	private $password_ErrorMessage;
	
	# start the construct
	public function __construct() {
		parent::__construct();

		$this->permission_denied = "Sorry! You do not have the required permission to perform this action.";
		$this->password_ErrorMessage = "<div style='width:100%'>Sorry! Please use a stronger password. <br><strong>Password Format</strong><br><ul>
			<li style='padding-left:15px;'>Password should be at least 8 characters long</li>
			<li style='padding-left:15px;'>At least 1 Uppercase</li>
			<li style='padding-left:15px;'>At least 1 Lowercase</li>
			<li style='padding-left:15px;'>At least 1 Numeric</li>
			<li style='padding-left:15px;'>At least 1 Special Character</li></ul></div>";
	}

	/**
	 * Confirm that the user is currently logged in
	 * 
	 * @return Bool
	 */
	public function loggedIn() {
		return ($this->session->userLoggedIn && $this->session->userId) ? true : false;
	}
	
	/**
	 * Global function to search for item based on the predefined columns and values parsed
	 * 
	 * @param \stdClass $params
	 * @param String $params->user_id 		The unique user id to load the results
	 * @param String $params->user_type		The type of the user to load the result
	 * @param String $params->gender		The gender of the user
	 * 
	 * @return Object
	 */
	public function list(stdClass $params = null) {

		$params->query = "1 ";

		// boolean value
        $params->remote = (bool) (isset($params->remote) && $params->remote);

		// if the field is null (dont perform all these checks if minified was parsed)
		if(!isset($params->minified)) {
			$params->query .= (isset($params->user_id)) ? (preg_match("/^[0-9]+$/", $params->user_id) ? " AND a.id='{$params->user_id}'" : " AND a.item_id='{$params->user_id}'") : null;
			$params->query .= (isset($params->clientId) && !empty($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
			$params->query .= (isset($params->user_type) && !empty($params->user_type)) ? " AND a.user_type IN {$this->inList($params->user_type)}" : null;
			$params->query .= (isset($params->email)) ? " AND a.email='{$params->email}'" : null;
			$params->query .= (isset($params->or_clause) && !empty($params->or_clause)) ? $params->or_clause : null;
			$params->query .= (isset($params->date_of_birth) && !empty($params->date_of_birth)) ? " AND a.date_of_birth='{$params->date_of_birth}'" : null;
			$params->query .= (isset($params->created_by) && !empty($params->created_by)) ? " AND a.created_by='{$params->created_by}'" : null;
			$params->query .= (isset($params->academic_year) && !empty($params->academic_year)) ? " AND a.academic_year='{$params->academic_year}'" : null;
			$params->query .= (isset($params->academic_term) && !empty($params->academic_term)) ? " AND a.academic_term='{$params->academic_term}'" : null;
			$params->query .= (isset($params->firstname) && !empty($params->firstname)) ? " AND a.firstname LIKE '%{$params->firstname}%'" : null;
			$params->query .= (isset($params->lastname) && !empty($params->lastname)) ? " AND a.lastname LIKE '%{$params->lastname}%'" : null;
			$params->query .= (isset($params->department_id) && !empty($params->department_id)) ? " AND a.department='{$params->department_id}'" : null;
			$params->query .= (isset($params->section_id) && !empty($params->section_id)) ? " AND a.section='{$params->section_id}'" : null;
			$params->query .= (isset($params->class_id) && !empty($params->class_id)) ? " AND a.class_id='{$params->class_id}'" : null;
			$params->query .= (isset($params->username)) ? " AND a.username='{$params->username}'" : null;
			$params->query .= (isset($params->gender)) ? " AND a.gender='{$params->gender}'" : null;
		}
		$params->query .= isset($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;

		// if a search parameter was parsed in the request
		$order_by = "ORDER BY a.id ASC";
		$params->query .= (isset($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
		$params->query .= (isset($params->lookup)) ? " AND a.name LIKE '%{$params->lookup}%'" : null;

		// the number of rows to limit the query
		$params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

		// make the request for the record from the model
		try {

			// if minified list was requested
			if(isset($params->minified)) {
				$params->columns = "a.item_id AS user_id, a.name, a.email, a.image";

				// exempt current user
				if(($params->minified == "chat_list_users")) {
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
			}

			// prepare and execute the statement
			$sql = $this->db->prepare("SELECT 
				".((isset($params->columns) ? $params->columns : "
					a.*, a.item_id AS user_id,
					(SELECT b.description FROM users_types b WHERE b.id = a.access_level) AS user_type_description, c.country_name,
					(SELECT COUNT(*) FROM users b WHERE (b.created_by = a.item_id) AND a.deleted='0') AS clients_count,
					(SELECT name FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_name,
					(SELECT name FROM classes WHERE classes.id = a.class_id LIMIT 1) AS class_name,
					(SELECT name FROM departments WHERE departments.id = a.department LIMIT 1) AS department_name,
					(SELECT name FROM sections WHERE sections.id = a.section LIMIT 1) AS section_name,
					(SELECT guardian_information FROM users_guardian WHERE users_guardian.user_id = a.item_id LIMIT 1) AS guardian_information,
					(SELECT name FROM blood_groups WHERE blood_groups.id = a.blood_group LIMIT 1) AS blood_group_name,
					(SELECT phone_number FROM users WHERE users.item_id = a.created_by LIMIT 1) AS created_by_phone
				")).", (SELECT b.permissions FROM users_roles b WHERE b.user_id = a.item_id AND b.client_id = a.client_id LIMIT 1) AS user_permissions
				FROM users a 
				LEFT JOIN country c ON c.id = a.country
				WHERE {$params->query} AND a.deleted='0' {$order_by} LIMIT {$params->limit}
			");
			$sql->execute();
			
			// init
			$row = 0;
			$data = [];
			
			// loop through the results
			while($result = $sql->fetch(PDO::FETCH_OBJ)) {

				// if not a minified suggestion list
				if(!isset($params->minified)) {

					// unset the id
					unset($result->id);
					$result->action = "";
					$result->guardian_information = json_decode($result->guardian_information);

					// if not a remote 
					if(!$params->remote) {

						// contact details
						$result->contact_details = "<i style=\"font-size:10px\" class=\"fa fa-envelope\"></i> {$result->email}<br><i style=\"font-size:10px\" class=\"fa fa-phone\"></i> {$result->phone_number}";
						
						// set the label for the policy
						$result->the_status_label = $this->the_status_label($result->user_status);

						// action buttons
						$result->action .= " &nbsp; <a class='btn p-1 btn-outline-success m-0 btn-sm' title='Click to view details of this policy' href='{$this->baseUrl}profile/{$result->user_id}'><i class='fa fa-eye'></i></a>";
					}

					// if the preference is set
					if(isset($result->preferences)) {
						# return an empty result
						unset($result->password);
						unset($result->item_id);
						$result->preferences = json_decode($result->preferences);
					}
					
					// append to the list and return
					$row++;
					$result->row_id = $row;
				} else {
					unset($result->user_permissions);
				}
				
				// online algorithm (user is online if last activity is at most 5minutes ago)
				if(isset($result->online)) {
					$result->online = $this->user_is_online($result->last_seen);
					$result->last_seen = time_diff($result->last_seen);
				}
				
				// if the message id was queried but empty then generate a new id
				if(isset($result->msg_id) && empty($result->msg_id)) {
					// set the new message id
					$result->msg_id = strtoupper(random_string("alnum", 32));
				}

				// append to the results set to return
				$data[] = $result;

			}

			// exempt current user
			if(isset($params->minified) && ($params->minified == "chat_list_users")) {
				// recent chats list
				$chatsObj = load_class("chats", "controllers");
				$chats_list = $chatsObj->recent($params->userId);

				// set the data to return
				$data = [
					"users_list" => $data,
					"chats_list" => $chats_list
				];
			}

			// return the data
			return [
				"data" => $data,
				"code" => !empty($data) ? 200 : 201
			];

		} catch(PDOException $e) {
			print_r($e->getMessage());
			return ["code" => 201, "data" => "Sorry! There was an error while processing the request."];
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
		
		global $accessObject;
		
		/** Check the contact number if not empty */
		if(!isset($params->phone)) {
			return ["code" => 203, "data" => "Sorry! Provide a valid contact number."];
		}

		// clean the contact number
		$params->phone = str_ireplace(["(", ")", "-", "_"], "", $params->phone);
		$params->phone = preg_replace("/[\s]/", "", $params->phone);

		// client id
		$params->client_id = isset($params->clientId) ? strtoupper($params->clientId) : null;

		/** Check the email address if not empty */
		if(!isset($params->email) || (isset($params->email) && !filter_var($params->email, FILTER_VALIDATE_EMAIL))) {
			return ["code" => 203, "data" => "Sorry! Provide a valid email address."];
		}

		/** If the user is logged in */
		$loggedInAccount = (bool) isset($params->userData->user_id);

		/** Set the changed password value */
		$params->changed_password = 1;
		
		/** Run this section if the user is logged in */
		if($loggedInAccount || (isset($params->remote) && $params->remote)) {

			/** If not permitted */
			if(!$accessObject->hasAccess("add", $params->user_type)) {
				return ["code" => 201, "data" => $this->permission_denied];
			}

			/** Generate a random password */
			$params->password = random_string("alnum", 12);

			/** Set the changed password value */
			$params->changed_password = 0;

			// this user created the account
			$params->created_by = $params->userData->user_id;

			/** Set username if the username is empty */
			$params->username = empty($params->username) ? explode("@", $params->email)[0] : $params->username;

			/** Check the username if not empty */
			if(!isset($params->lastname)) {
				return ["code" => 203, "data" => "Sorry! The lastname cannot be empty"];
			}
			
		}

		// confirm that a logo was parsed
        if(isset($params->image)) {
            // set the upload directory
            $uploadDir = "assets/img/users/";
            // File path config 
            $fileName = basename($params->image["name"]); 
            $targetFilePath = $uploadDir . $fileName; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg');            
            // check if its a valid image
            if(!empty($fileName) && in_array($fileType, $allowTypes)){
                // set a new filename
                $fileName = $uploadDir . random_string('alnum', 10)."__{$fileName}";
                // Upload file to the server 
                if(move_uploaded_file($params->image["tmp_name"], $fileName)){}
            }
        }

		/** Check the username if not empty */
		if(!isset($params->username) || (isset($params->username) && strlen($params->username) < 3)) {
			return ["code" => 203, "data" => "Sorry! Username must be at least 6 characters long."];
		}

		/** Check the contact number if not empty */
		if(!isset($params->phone) || (isset($params->phone) && !preg_match("/^[0-9+]+$/", $params->phone))) {
			return ["code" => 203, "data" => "Sorry! Provide a valid contact number."];
		}

		/** Check the contact number if not empty */
		if(isset($params->portal_registration) && !passwordTest($params->password)) {
			return ["code" => 203, "data" => $this->password_ErrorMessage];
		}

		// set the user type
		$params->user_type = isset($params->user_type) && !empty($params->user_type) ? $params->user_type : "student";
		
		// confirm that the username does not already exist
		$i_params = (object) ["limit" => 1, "username" => $params->username];

		// get the user data
		if(!empty($this->list($i_params)["data"])) {
			return ["code" => 203, "data" => "Sorry! The username is already in use."];
		}

		// convert the user type to lowercase
		$params->user_type = strtolower($params->user_type);

		// get the user permissions
		$accessPermissions = $accessObject->getPermissions($params->user_type);

		// if the permission is empty
		if(empty($accessPermissions)) {
			return ["code" => 203, "data" => "Sorry! An invalid user_type was provided for processing."];
		}

		// if admin access is false and yet the access id is more than 7 then throw an error
		if($accessPermissions[0]->id > 8 && !isset($params->adminAccess)) {
			return ["code" => 203, "data" => "Sorry! An invalid user_type was provided for processing."];
		}

		// confirm that the email does not already exist
		$i_params = (object) ["limit" => 1, "email" => $params->email];
		
		// get the user data
		if(!empty($this->list($i_params)["data"])) {
			return ["code" => 203, "data" => "Sorry! The email is already in use."];
		}

		// generate a new unique user id
		$counter = $this->append_zeros(($this->itemsCount("users", "client_id = '{$params->clientId}' AND user_type='{$params->user_type}'") + 1), $this->append_zeros);
        $unique_id = $this->client_data($params->clientId)->client_preferences->labels->{"{$params->user_type}_label"}.$counter.date("Y");
		// set the unique id
		$params->unique_id = isset($params->unique_id) && !empty($params->unique_id) ? $params->unique_id : $unique_id;

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

			// begin transaction
			$this->db->beginTransaction();

			// variables
			$params->user_id = random_string("alnum", 32);

			#set the token expiry time to 6 hours from the moment of request
			$token = random_string("alnum", mt_rand(60, 75));
            $token_expiry = time()+(60*60*6);

			// encrypt the password sent
			$encrypt_password = password_hash($params->password, PASSWORD_DEFAULT);
			
			// usertype and fullname
			$params->fullname = $params->firstname . " " . $params->lastname ?? null. " " . $params->othername ?? null;
			$params->created_by = $params->created_by ?? $params->user_id;

			// load the access level permissions
			$permissions = $accessPermissions[0]->user_permissions;
			$access_level = $accessPermissions[0]->id;

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
				".(isset($params->created_by) ? ", created_by='{$params->created_by}'" : null)."
				".(isset($encrypt_password) ? ", password='{$encrypt_password}'" : null)."

				".(isset($fileName) ? ", image='{$fileName}'" : null)."

				".(isset($params->enrollment_date) ? ", enrollment_date='{$params->enrollment_date}'" : null)."
				".(isset($params->class_id) ? ", class_id='{$params->class_id}'" : null)."
				".(isset($params->blood_group) ? ", blood_group='{$params->blood_group}'" : null)."
				".(isset($params->religion) ? ", religion='{$params->religion}'" : null)."
				".(isset($params->section) ? ", section='{$params->section}'" : null)."
				".(isset($params->programme) ? ", programme='{$params->programme}'" : null)."
				".(isset($params->department) ? ", department='{$params->department}'" : null)."

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
				".(isset($params->date_of_birth) ? ", date_of_birth='{$params->date_of_birth}'" : null)."
			");
			// execute the insert user data
			$stmt->execute([$params->user_id, $params->user_type, $access_level, $token, $token_expiry, $params->changed_password]);

			// log the user access level
			$stmt2 = $this->db->prepare("INSERT INTO users_roles SET user_id = ?, client_id = ?, permissions = ?");
			$stmt2->execute([$params->user_id, $params->clientId, $permissions]);

			// insert the user guardian information
			$stmt = $this->db->prepare("INSERT INTO users_guardian SET user_id = ?, guardian_information = ?");
			$stmt->execute([$params->user_id, json_encode($guardian)]);
			
			// if the email address was parsed
			if(isset($params->email) && filter_var($params->email, FILTER_VALIDATE_EMAIL)) {

				// email comfirmation link
				$message = "Hello {$params->firstname},";

				$message .= '<a class="alert alert-success" href="'.config_item('base_url').'verify?account&token='.$token.'">Verify your account</a>';
				$message .= '<br><br>If it does not work please copy this link and place it in your browser url.<br><br>';
				$message .= config_item('base_url').'verify?account&token='.$token;

				// recipient list
				$reciepient = ["recipients_list" => [["fullname" => $params->fullname,"email" => $params->email,"customer_id" => $params->user_id]]];

				// insert the email content to be processed by the cron job
				$stmt = $this->db->prepare("
					INSERT INTO users_messaging_list SET template_type = ?, item_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?, users_id = ?
				");
				$stmt->execute([
					'account-verify', random_string("alnum", 32), json_encode($reciepient),
					$params->created_by, "[".config_item('site_name')."] Account Verification", $message, $params->user_id
				]);

			}
			
			// insert the user activity
			$this->userLogs("account-verify", $params->user_id, null, "{$params->fullname} - verify account by clicking on the link sent to the provided email address.", $params->created_by, null);

			// commit all opened transactions
			$this->db->commit();

			# set the output to return when successful
			$return = ["code" => 200, "data" => "User account successfully created. A verification link has been sent via email.", "refresh" => 2000];
			
			# append to the response
			if($loggedInAccount) {
				$return["additional"] = [
					"clear" => true
				];
			}

			// return the output
            return $return;

		} catch(PDOException $e) {
			$this->db->rollBack();
			return false;
		}

	}

	/**
	 * Update a users record
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function update(stdClass $params) {

		// confirm that the user_id does not already exist
		$i_params = (object) ["limit" => 1, "user_id" => $params->user_id];
		$the_user = $this->list($i_params)["data"];
		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "data" => "Sorry! Please provide a valid user id."];
		}

		// permisssions checker test
		if(!in_array($params->userData->user_type, ["insurance_company", "admin"]) && ($the_user[0]->created_by !== $params->userData->user_id)) {
			return ["code" => 201, "data" => "Sorry! You are not permitted to modify this user account details."];
		}

		// clean the contact number
		$params->phone = str_ireplace(["(", ")", "-", "_"], "", $params->phone);
		$params->phone = preg_replace("/[\s]/", "", $params->phone);

		/** Check the email address if not empty */
		if(!isset($params->email) || (isset($params->email) && !filter_var($params->email, FILTER_VALIDATE_EMAIL))) {
			return ["code" => 201, "data" => "Sorry! Provide a valid email address."];
		}

		/** Check the contact number if not empty */
		if(!isset($params->phone) || (isset($params->phone) && !preg_match("/^[0-9+]+$/", $params->phone))) {
			return ["code" => 201, "data" => "Sorry! Provide a valid contact number."];
		}
		
		// if the username was parsed and the new one is not the same as the initial one
		if(isset($params->username) && ($params->username !== $the_user[0]->username)) {
			// confirm that the username does not already exist
			$i_params = (object) ["limit" => 1, "username" => $params->username];
			if(!empty($this->list($i_params)["data"])) {
				return ["code" => 201, "response" => "Sorry! The username is already in use."];
			}
		}

		// if the email was parsed and not the same as the initial one
		if(isset($params->email) && ($params->email !== $the_user[0]->email)) {
			// confirm that the email does not already exist
			$i_params = (object) ["limit" => 1, "email" => $params->email];
			// get the user data
			if(!empty($this->list($i_params)["data"])) {
				return ["code" => 201, "response" => "Sorry! The email is already in use."];
			}
		}

		// confirm that a logo was parsed
        if(isset($params->image)) {
            // set the upload directory
            $uploadDir = "assets/img/users/";
            // File path config 
            $fileName = basename($params->image["name"]); 
            $targetFilePath = $uploadDir . $fileName; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg');            
            // check if its a valid image
            if(!empty($fileName) && in_array($fileType, $allowTypes)){
                // set a new filename
                $fileName = $uploadDir . random_string('alnum', 10)."__{$fileName}";
                // Upload file to the server 
                if(move_uploaded_file($params->image["tmp_name"], $fileName)){}
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

			// begin transaction
			$this->db->beginTransaction();
			$additional = null;

			/** Load the previous record */
            $prevData = $this->prevData("users", $params->user_id);
			
			// usertype and fullname
			$params->client_id = isset($params->clientId) ? strtoupper($params->clientId) : null;
			$params->fullname = $params->firstname . " " . $params->lastname ?? null . " " . $params->othername ?? null;
			
			// convert the user type to lowercase
			$params->user_type = strtolower($the_user[0]->user_type);

			// insert the user information
			$stmt = $this->db->prepare("
				UPDATE users SET last_updated = now()
				".(!empty($params->clientId) ? ", client_id='{$params->clientId}'" : null)."
				".(isset($params->firstname) ? ", firstname='{$params->firstname}'" : null)."
				".(isset($params->lastname) ? ", lastname='{$params->lastname}'" : null)."
				".(isset($params->othername) ? ", othername='{$params->othername}'" : null)."
				".(isset($params->fullname) ? ", name='{$params->fullname}'" : null)."
				".(isset($params->email) ? ", email='{$params->email}'" : null)."
				".(isset($params->residence) ? ", residence='{$params->residence}'" : null)."
				".(isset($params->gender) ? ", gender='{$params->gender}'" : null)."

				".(isset($fileName) ? ", image='{$fileName}'" : null)."

				".(isset($params->unique_id) ? ", unique_id='{$params->unique_id}'" : null)."
				".(isset($params->class_id) ? ", class_id='{$params->class_id}'" : null)."
				".(isset($params->blood_group) ? ", blood_group='{$params->blood_group}'" : null)."
				".(isset($params->religion) ? ", religion='{$params->religion}'" : null)."
				".(isset($params->section) ? ", section='{$params->section}'" : null)."
				".(isset($params->programme) ? ", programme='{$params->programme}'" : null)."
				".(isset($params->department) ? ", department='{$params->department}'" : null)."
				".(isset($params->enrollment_date) ? ", enrollment_date='{$params->enrollment_date}'" : null)."

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
				".(isset($params->date_of_birth) ? ", date_of_birth='{$params->date_of_birth}'" : null)."
				
				WHERE item_id = ? LIMIT 1
			");

			// execute the insert user data
			$stmt->execute([$params->user_id]);

			// update the user guardian information
			$stmt = $this->db->prepare("UPDATE users_guardian SET guardian_information = ? WHERE user_id = ? LIMIT 1");
			$stmt->execute([json_encode($guardian), $params->user_id]);

			// save the name change
            if(isset($params->fullname) && ($prevData->name !== $params->fullname)) {
                $this->userLogs("user-account", $params->user_id, $prevData->name, "Name was changed from {$prevData->name}", $params->userId);

				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }

			// save the email address
            if(isset($params->email) && ($prevData->email !== $params->email)) {
                $this->userLogs("user-account", $params->user_id, $prevData->email, "Email Address was changed from {$prevData->email}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }
			
			// save the postal address changes
            if(isset($params->address) && ($prevData->address !== $params->address)) {
                $this->userLogs("user-account", $params->user_id, $prevData->address, "Postal Address has been changed.", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }

			// save the date of birth change
            if(isset($params->date_of_birth) && ($prevData->date_of_birth !== $params->date_of_birth)) {
                $this->userLogs("user-account", $params->user_id, $prevData->date_of_birth, "Date of Birth has been changed to {$params->date_of_birth}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }

			// save the phone_number change
            if(isset($params->phone) && ($prevData->phone_number !== $params->phone)) {
                $this->userLogs("user-account", $params->user_id, $prevData->phone_number, "Primary Contact was been changed from {$prevData->phone_number}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }

			// save the phone_number_2 change
            if(isset($params->phone_2) && ($prevData->phone_number !== $params->phone_2)) {
                $this->userLogs("user-account", $params->user_id, $prevData->phone_number_2, "Primary Contact was been changed from {$prevData->phone_number_2}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }
			
			// save the occupation
            if(isset($params->occupation) && ($prevData->occupation !== $params->occupation)) {
                $this->userLogs("user-account", $params->user_id, $prevData->occupation, "Occupation has been altered. {$prevData->occupation} => {$params->occupation}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }
			
			// save the employer
            if(isset($params->employer) && ($prevData->employer !== $params->employer)) {
                $this->userLogs("user-account", $params->user_id, $prevData->employer, "Employer details has been altered. {$prevData->employer} => {$params->employer}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }
			
			// save the position
            if(isset($params->position) && ($prevData->position !== $params->position)) {
                $this->userLogs("user-account", $params->user_id, $prevData->position, "Position has been altered. {$prevData->position} => {$params->position}", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
            }

			// insert the user activity
			if($params->user_id == $params->userId) {
				// Insert the log
				$this->userLogs("user-account", $params->user_id, $prevData, "You updated your account information", $params->userId);
				// set the value
				$additional = ["href" => "{$this->baseUrl}update-student/{$params->user_id}/update"];
			} else {
				// notification object
				global $noticeClass;
				
				// Insert the log
				$this->userLogs("user-account", $params->user_id, $prevData, "<strong>{$params->userData->name}</strong> updated the account information of <strong>{$the_user[0]->name}</strong>", $params->userId);
				
				// Notify the user that his/her account has been modified
				$param = (object) [
					'_item_id' => random_string("alnum", 32),
					'user_id' => $params->user_id,
					'subject' => "Account Update",
					'message' => "<strong>{$params->userData->name}</strong> updated your account information",
					'notice_type' => 9,
					'userId' => $params->userId,
					'initiated_by' => 'system'
				];
				
				// add a new notification
				$noticeClass->add($param);
			}

			// commit all opened transactions
			$this->db->commit();

			#record the password change request
            return ["code" => 200, "data" => "Account successfully updated.", "additional" => $additional ];

		} catch(PDOException $e) {
			$this->db->rollBack();
			return false;
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
			$token = random_string("alnum", mt_rand(60, 75));
            $token_expiry = time()+(60*60*6);

			// update the last login for this user
			$stmt = $this->db->prepare("UPDATE users SET verify_token = ?, token_expiry = ? WHERE item_id=? LIMIT 1");
			$stmt->execute([$token, $token_expiry, $params->user_id]);

			// email comfirmation link
			$message = 'Hi '.$user[0]->name ?? null;
			$message .= '<br><br>We click to';
			$message .= '<a class="alert alert-success" href="'.config_item('base_url').'verify?account&token='.$token.'">Verify your account</a>';
			$message .= '<br><br>If it does not work please copy this link and place it in your browser url.<br><br>';
			$message .= config_item('base_url').'verify?account&token='.$token;

			// recipient list
			$reciepient = ["recipients_list" => [["fullname" => $user[0]->name ?? null, "email" => $params->email,"customer_id" => $params->user_id]]];

			// insert the email content to be processed by the cron job
			$stmt = $this->db->prepare("
				INSERT INTO users_messaging_list SET template_type = ?, item_id = ?, recipients_list = ?, created_by = ?, subject = ?, message = ?, users_id = ?
			");
			$stmt->execute([
				'account-verify', random_string("alnum", 32), json_encode($reciepient),
				$params->user_id, "[".config_item('site_name')."] Account Verification", $message, $params->user_id
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
							],
							"notifications" => [
								"record_id" => $userData->user_id,
								"column" => "notices_count"
							],
							"replies" => [
								"column" => "replies_count"
							],
							"complaints" => [
								"column" => "complaints_count"
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
					"records_count" => $records->threads($countVars)["data"]["records_count"],
					"policy_form" => $this->prependData("id, name, type", "policy_form", "status='1'"),
					"users_list" => $this->prependData("a.id, a.item_id, a.email, a.employer, a.date_created, a.last_login, a.description, a.position, a.name, a.phone_number, a.phone_number_2, a.date_of_birth, a.nationality, a.residence, a.occupation, a.image, a.address, (SELECT country_name b FROM country b WHERE b.id = a.country) AS country_name", "users a", "a.status='1' AND a.deleted='0'"),
					"insurance_policies" => $this->prependData(
							"a.id, a.item_id, a.requirements, a.policy_code, a.year_enrolled, a.category, a.name, a.description, a.payment_plans,
							(SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id) AS attachment", "policy_types a", "a.status='1' AND a.deleted='0'"
						),
					"insurance_companies" => $this->prependData("establishment_date AS date_established, item_id, name, contact, contact_2, email, website, logo, address, description, awards, managers", "companies", "activated='1' AND deleted='0'"),
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
	 * Save the user's profile picture
	 * 
	 * @param \stdClass $params
	 * 
	 * @return Array
	 */
	public function save_image(stdClass $params) {

		/** Get the session value */
		if(empty($this->session->tempProfilePicture)) {
			return ["code" => 201, "response" => "Sorry! No picture have been uploaded yet."];
		}

		// confirm that the user_id does not already exist
		$i_params = (object) ["limit" => 1, "user_id" => $params->userData->user_id];
		$the_user = $this->list($i_params)["data"];

		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "response" => "Sorry! Please provide a valid user id."];
		}

		/** Save the user information */
		try {
			
			// begin transaction
			$this->db->beginTransaction();
			$init_image = $this->session->tempProfilePicture;
			$exp = explode("/", $init_image);

			// set the user image
			$user_image = "assets/images/profiles/".preg_replace("/[\s]/", "_", $exp[count($exp)-1]);
			$thumbnail = "assets/images/profiles/thumbnail/".preg_replace("/[\s]/", "_", $exp[count($exp)-1]);

			// save the previous image as history
			$uimage = explode(".", $params->userData->image);
			$logged_image = "assets/images/profiles/logs/".random_string("alnum", 50).".".$uimage[count($uimage)-1];
			
			// copy the previous image and save it under logs
			copy($params->userData->image, $logged_image);

			// form the content of the message to display
			$prevData = "<div class=\"title d-flex align-items-center justify-content-between\">
					<div><img width=\"60px\" src=\"{{APPURL}}{$logged_image}\" class=\"img-fluid rounded-circle\"></div>
					<div><i class=\"fa btn btn-primary btn-sm fa-arrow-right\"></i></div>
					<div><img width=\"90px\" src=\"{{APPURL}}{$user_image}\" class=\"img-fluid rounded\"></div>
				</div>";

			// copy the file to the new destination
			copy($init_image, $user_image);
			create_thumbnail($user_image, $thumbnail);

			// unlink or delete the actual file in temp and the old user image
			unlink($init_image);

			// if previous is not the avatar
			if($params->userData->image !== "assets/images/profiles/avatar.jpg") {
				// remove the previous image
				// unlink($params->userData->image);
			}

			// execute the statement
			$stmt = $this->db->prepare("UPDATE users SET image = ?, perma_image = ? WHERE item_id = ? LIMIT 1");
			$stmt->execute([$user_image, $user_image, $params->user_id]);
			
			// insert the user activity
			if($params->user_id == $params->userId) {
				// Insert the log
				$this->userLogs("Profile Picture", $params->user_id, $prevData, "You successfully changed your profile picture", $params->userId, "Manual profile picture update by {$params->userData->name}");
			} else {
				// notification object
				global $noticeClass;
				// Insert the log
				$this->userLogs("Profile Picture", $params->user_id, $prevData, "<strong>{$params->userData->name}</strong> changed the profile picture of <strong>{$the_user[0]->name}</strong>", $params->userId, "Logged because {$params->userData->name} made the changes.");
				// Notify the user that his/her account has been modified
				$param = (object) [
					'_item_id' => random_string("alnum", 32),
					'user_id' => $params->user_id,
					'subject' => "Picture Update",
					'message' => "<strong>{$params->userData->name}</strong> changed your profile picture",
					'notice_type' => 9,
					'userId' => $params->userId,
					'initiated_by' => 'system'
				];
				// add a new notification
				$noticeClass->add($param);
			}

			// commit all opened transactions
			$this->db->commit();

			// remove the session
			$this->session->remove("tempProfilePicture");

			#record the password change request
            return ["code" => 200, "data" => $user_image ];

		} catch(PDOException $e) {
			$this->db->rollBack();
			return ["code" => 201, "response" => "Sorry! There was an error while processing the request."];
		}

	}
	
	/**
	 * Load user permissions
	 * 
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
		$permissions = json_decode($permissions[0]->permissions);

		// return the permissions
		return [
			"code" => 200,
			"data" => $permissions
		];

	}

	/**
	 * Save the user permissions
	 * 
	 * @param String $params->user_id
	 * @param String $params->access_level
	 * @param Array $params->permissions_list
	 * 
	 * @return Array
	 */
	public function save_permissions(stdClass $params) {

		/** Global variables */
		global $accessObject;
		
		/** Confirm the user permissions */
		if(!$accessObject->hasAccess("permissions", "users")) {
			return ["code" => 201, "data" => $this->permission_denied];
		}

		/** Confirm that the permissions_list parameter has an array value */
		if(!is_array($params->permissions_list)) {
			return ["code" => 201, "response" => "Sorry! Permissions list must be a valid array format."];
		}

		// confirm that the user_id does not already exist
		$i_params = (object) ["limit" => 1, "user_id" => $params->user_id, "columns" => "user_type", "remote" => true];
		$the_user = $this->list($i_params)["data"];
		
		// get the user data
		if(empty($the_user)) {
			return ["code" => 201, "response" => "Sorry! Please provide a valid user id."];
		}

		// get the first key
		$the_user = $the_user[0];
		$user_permissions = json_decode($the_user->user_permissions)->permissions	;

		// initialiate
		$bugs = [];
		$permissions_list = [];

		// clean the access permissions well
		foreach($params->permissions_list as $eachValue) {
			$explode = explode(",", $eachValue);
			$permissions_list[$explode[0]][$explode[1]] = $explode[2];
		}

		// loop through the user permissions and confirm that all matches
		foreach($permissions_list as $key => $value) {
			if(!isset($user_permissions->$key)) {
				$bugs[] =  1;
			}
			// ensure that the permission exist in the array list as well
			foreach($value as $kkey => $vvalue) {
				if(!isset($user_permissions->$key->$kkey)) {
					$bugs[] = $kkey;
				}
			}
		}

		// get the user data
		if(!empty($bugs)) {
			return ["code" => 201, "response" => "Sorry! An invalid permission was parsed."];
		}

		$permissions["permissions"] = (object) $permissions_list;

		try {	
			// update the user permissions
			$stmt = $this->db->prepare("UPDATE users_roles SET permissions = ? WHERE user_id=? LIMIT 1");
			$stmt->execute([json_encode($permissions), $params->user_id]);

			// log user activity
			$this->userLogs("user-permissions", $params->user_id, $user_permissions, "User permissions was successfully updated.", $params->userId);

			// return the success response
			return [
				"code" => 200,
				"data" => "User permissions successfully updated."
			];

		} catch(PDOException $e) {}

	}
	
}
?>