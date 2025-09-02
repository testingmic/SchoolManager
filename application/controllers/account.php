<?php

class Account extends Myschoolgh {

    public $accepted_column;
    public $readonly_mode;

	public function __construct($params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;

        // set the columns for the import of csv files
        $this->accepted_column["student"] = [
            "unique_id" => "Student ID", 
            "firstname" => "Firstname", 
            "othername" => "Othernames", 
            "lastname" => "Lastname", 
            "email" => "Email", 
            "phone_number" => "Contact Number",
            "residence" => "Residence", 
            "date_of_birth" => "Date of Birth", 
            "enrollment_date" => "Admission Date", 
            "gender" => "Gender", 
            "department" => "Department", 
            "class_id" => "Class ID"
        ];

        $this->accepted_column["staff"] = [
            "unique_id" => "Employee ID", 
            "firstname" => "Firstname", 
            "othername" => "Othernames",
            "lastname" => "Lastname", 
            "email" => "Email",
            "phone_number" => "Contact Number",
            "city" => "City", 
            "residence" => "Residence", 
            "date_of_birth" => "Date of Birth", 
            "enrollment_date" => "Date of Employment", 
            "gender" => "Gender",
            "occupation" => "Occupation", 
            "department" => "Department", 
            "position" => "Position"
        ];
    
        $this->accepted_column["parent"] = [
            "unique_id" => "Guardian ID", 
            "firstname" => "Firstname", 
            "othername" => "Othernames", 
            "lastname" => "Lastname", 
            "email" => "Email", 
            "phone_number" => "Primary Contact",
            "phone_number_2" => "Secondary Contact", 
            "city" => "City",
            "residence" => "Residence",
            "date_of_birth" => "Date of Birth",
            "employer" => "Employer",
            "occupation" => "Occupation",
            "position" => "Position"
        ];

	}

    /**
     * Client Analitics
     * This method Loads all The Basic Information of a particular School
     * It returns an array data of the counts
     * 
     * @return Array
     */
    public function client($params = null) {

        $result = [];
        
        try {

            $stmt = $this->db->prepare("SELECT 
                (SELECT COUNT(DISTINCT b.item_id) FROM users b WHERE b.client_id = a.client_id AND b.user_type IN ('admin')) AS admins_count,
                (SELECT COUNT(DISTINCT b.item_id) FROM users b WHERE b.client_id = a.client_id AND b.user_type='student') AS students_count,
                (SELECT COUNT(DISTINCT b.item_id) FROM users b WHERE b.client_id = a.client_id AND b.user_type IN ('teacher','employee','accountant')) AS staff_count,
                (SELECT COUNT(DISTINCT b.item_id) FROM classes b WHERE b.client_id = a.client_id AND b.status='1') AS classes_count,
                (SELECT COUNT(DISTINCT b.id) FROM departments b WHERE b.client_id = a.client_id AND b.status='1') AS departments_count,
                (SELECT COUNT(DISTINCT b.id) FROM sections b WHERE b.client_id = a.client_id AND b.status='1') AS sections_count,
                (SELECT b.sms_balance FROM smsemail_balance b WHERE b.client_id = a.client_id LIMIT 1) AS sms_balance
                FROM clients_accounts a WHERE a.client_id = ? LIMIT 1
            ");
            $stmt->execute([$params->clientId]);
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            return $result;

        } catch(PDOException $e) {
            return [];
        }

    }

    /**
     * Transfer a Client Account
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function transfer($params = null) {

        try {

            if(empty($params->transfer_from) || empty($params->transfer_to)) {
                return [
                    "code" => 400,
                    "data" => "Sorry! The transfer_from and transfer_to parameters are required."
                ];
            }

            if($params->transfer_from == $params->transfer_to) {
                return [
                    "code" => 400,
                    "data" => "Sorry! The transfer_from and transfer_to parameters cannot be the same."
                ];
            }

            $transfer_from = $params->transfer_from;
            $transfer_to = $params->transfer_to;

            // get all users from the transfer_from client
            $users = $this->pushQuery("*", "users", "client_id='{$transfer_from}' AND user_type IN ('teacher', 'student')");

            $totalCount = [
                'success' => [
                    'student' => 0,
                    'teacher' => 0
                ],
                'failed' => [
                    'total' => 0
                ]
            ];

            $password = password_hash('Pa$$word!', PASSWORD_DEFAULT);
            foreach($users as $user) {
                // insert the user into the transfer_to client dynamically
                unset($user->id);
                unset($user->date_of_birth);
                $user->email = "";
                $user->username = random_string("alnum", RANDOM_STRING);
                $user->password = $password;
                $user->class_id = null;
                $user->item_id = random_string("alnum", RANDOM_STRING);
                $user->unique_id = str_ireplace("HISS", "JOE", $user->unique_id);
                $user->unique_id = str_ireplace("STL", "TEACH", $user->unique_id);
                $user->client_id = $transfer_to;
                $user->date_created = date("Y-m-d H:i:s");
                $user->last_updated = date("Y-m-d H:i:s");
                $user->last_login = date("Y-m-d H:i:s");
                $user->last_password_change = date("Y-m-d H:i:s");

                $user = json_decode(json_encode($user), true);
                $columns = implode(", ", array_keys($user));
                $values = implode("', '", array_values($user));

                try {
                    $this->db->query("INSERT INTO users ({$columns}) VALUES ('{$values}')");
                    if(!empty($user->user_type)) {
                        $totalCount['success'][$user->user_type]++;
                    }
                } catch(PDOException $e) {
                    $totalCount['failed']['total']++;
                }
            }

            return [
                "code" => 200,
                "data" => [
                    "message" => "All students have been successfully transferred to the new client.",
                    "totalCount" => $totalCount
                ]
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Save the Grade Remarks List
     * 
     * @param Array     $params->remarks_list
     * 
     * @return Array
     */
    public function remarks(stdClass $params) {

        try {

            // if the remarks_list is empty or not an array
            if(empty($params->remarks_list) || !is_array($params->remarks_list)) {
                return ["code" => 400, "data" => "Sorry! An array data is expected in the request"];
            }

            // set the remarks
            $remarks = $params->remarks_list;

            // confirm if the record already exists
            if(!empty($this->pushQuery("id", "grading_remarks_list", "client_id='{$params->clientId}' LIMIT 1"))) {
                // insert the new record
                $stmt = $this->db->prepare("UPDATE grading_remarks_list SET remarks=? WHERE client_id= ? LIMIT 1");
                $stmt->execute([json_encode($remarks), $params->clientId]);
            } else {
                // update the existing record
                $stmt = $this->db->prepare("INSERT INTO grading_remarks_list SET remarks=?, client_id= ?");
                $stmt->execute([json_encode($remarks), $params->clientId]);
            }

            // return success message
            return [
                "code" => 200,
                "data" => "Grade remarks successfully saved"
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Create a New Package
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function create_package(stdClass $params) {

        try {

            global $accessObject;

            if(!$accessObject->hasAccess("schools", "settings")) {
                return ["code" => 403, "data" => $this->permission_denied];
            }
            
            $setClause = "";
            foreach(['package', 'student', 'staff', 'admin', 'monthly_sms', 'fees', 'pricing'] as $key => $value) {
                if(isset($params->{$value})) {
                    $setClause .= "{$value} = '{$params->{$value}}', ";
                }
            }
    
            $setClause = !empty($setClause) ? substr($setClause, 0, -2) : null;

            if(empty($setClause)) {
                return [
                    "code" => 400,
                    "data" => "Sorry! No data was parsed in the request."
                ];
            }
    
            $stmt = $this->db->prepare("INSERT INTO clients_packages SET {$setClause}");
            $stmt->execute();
    
            return [
                "code" => 200,
                "data" => "Package information was successfully created.",
                "additional" => [
                    "clear" => true,
                    "href" => "{$this->baseUrl}packages"
                ]
            ];
        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Update the Package Information
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update_package(stdClass $params) {

        try {

            global $accessObject;

            if(!$accessObject->hasAccess("schools", "settings")) {
                return ["code" => 403, "data" => $this->permission_denied];
            }

            if(empty($params->package_id)) {
                return [
                    "code" => 400,
                    "data" => "Sorry! A valid package id must be parsed in the request."
                ];
            }
            
            $setClause = "";
            foreach(['package', 'student', 'staff', 'admin', 'monthly_sms', 'fees', 'pricing', 'status'] as $key => $value) {
                if(isset($params->{$value})) {
                    $setClause .= "{$value} = '{$params->{$value}}', ";
                }
            }
    
            $setClause = !empty($setClause) ? substr($setClause, 0, -2) : null;

            if(empty($setClause)) {
                return [
                    "code" => 400,
                    "data" => "Sorry! No data was parsed in the request."
                ];
            }

            $stmt = $this->db->prepare("UPDATE clients_packages SET {$setClause} WHERE id = ? LIMIT 1");
            $stmt->execute([$params->package_id]);
    
            return [
                "code" => 200,
                "data" => "Package information was successfully updated.",
                "additional" => [
                    "href" => "{$this->baseUrl}packages"
                ]
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Modify Client Data
     * 
     * @param Array     $params->data
     * 
     * @return Array
     */
    public function modify(stdClass $params) {

        try {

            global $accessObject;

            if(!$accessObject->hasAccess("schools", "settings")) {
                return ["code" => 403, "data" => $this->permission_denied];
            }

            // set the variable
            $data = $params->data;

            // if not an array data is parsed
            if(!is_array($params->data)) {
                return ["code" => 400, "data" => "Sorry! An array data is expected"];
            }

            // confirm the client id
            if(!isset($data["client_id"])) {
                return ["code" => 400, "data" => "Sorry! A valid client_id must be parsed in the request."];
            }

            // check the client id if existing
            $check = $this->pushQuery(
                "a.client_preferences, a.client_state, a.client_name,
                (SELECT b.sms_balance FROM smsemail_balance b WHERE b.client_id = a.client_id) AS sms_balance", 
                "clients_accounts a", "a.client_id='{$data["client_id"]}' LIMIT 1");
            
            // if no record was found
            if(empty($check)) { return ["code" => 400, "data" => "Sorry! A valid client_id must be parsed in the request."]; }

            // set the redirection link
            $additional["href"] = "{$this->baseUrl}schools/{$data["client_id"]}";

            // if account topup
            if(isset($params->data["action"], $params->data["topup"])) {
                
                // update only if the account has not been suspended or expired
                if(in_array($check[0]->client_state, ["Expired", "Suspended"])) {
                    return ["code" => 400, "data" => "Sorry! You cannot modify a {$check[0]->client_state} account. First change the status to continue."];
                }

                // update the user sms balance
                $this->db->query("UPDATE smsemail_balance SET 
                    sms_balance=(sms_balance + {$data["topup"]}),
                    last_topup_details = '{$data["topup"]} SMS Units was '
                    WHERE client_id='{$data["client_id"]}' LIMIT 1
                ");
                
                // log the user activity
                $this->userLogs("sms_topup", $data["client_id"], null,  "{$params->userData->name} added 
                    <strong>{$data["topup"]} sms units</strong> to the Account of <strong>{$check[0]->client_name}</strong>.
                    New Balance = ".($check[0]->sms_balance + $data["topup"]), $params->userId);

                // return a success message
                return ["data" => "SMS Balance Successfully updated.", "additional" => $additional];

            }

            // required parameters
            $required = ["account_package", "account_expiry", "client_state", "sms_sender", "client_account", "client_id"];

            foreach($data as $key => $value) {
                if(!in_array($key, $required)) {
                    return ["code" => 400, "data" => "Sorry! An unexpected parameter was parsed."];
                }
            }


            // get teh client data
            $additional = [];
            $prefs = json_decode($check[0]->client_preferences);

            // if the package is not the same as the previous
            if($data["account_package"] !== $prefs->account->package){
                // append to the account parameter
                $package = $this->pushQuery("*", "clients_packages", "package='{$data["account_package"]}' LIMIT 1");
                
                // return error if the package was not found
                if(empty($package)) {
                    // log the attempt to bypass the system security
                    $this->db->query("INSERT INTO security_logs SET 
                        client_id='{$params->clientId}', created_by='{$params->userId}', section='Account Package',
                        description='The user attempted to assign a non existent package to {$check[0]->client_name}'
                    ");
                    // return a warning to the user.
                    return ["code" => 400, "data" => "Sorry! You attempted to assign a non existent package to the user."];
                }

                // continue with the processing
                $package = (array) $package[0];
                unset($package["id"]);

                $account = (array) $prefs->account;
                $account = array_merge($account, $package);

                $prefs->account = (object) $account;

            }

            // set the package information
            $prefs->account->package = $data["account_package"];
            $prefs->account->expiry = $data["account_expiry"];

            // if the expiry datetime is greater than the current time the set the client state to active
            if(strtotime($data["account_expiry"]) > time()) {
                $data["client_state"] = $data["client_state"] ?? "Active";
            } else {
                // set the client state to have expired
                $data["client_state"] = $data["client_state"] ?? "Expired"; 
            }

            // if the client id was parsed and not empty
            if(isset($data["client_account"]) && !empty($data["client_account"])) {}

            $stmt = $this->db->prepare("UPDATE clients_accounts SET client_state = ?, sms_sender = ?, client_account = ?, client_preferences = ? WHERE client_id = ? LIMIT 1");
            $stmt->execute([$data["client_state"], $data["sms_sender"], $data["client_account"], json_encode($prefs), $data["client_id"]]);

            // return a success message
            return ["data" => "Client information was successfully updated.", "additional" => $additional];

        } catch(PDOException $e) {}

    }

    /**
     * End the Academic Term
     * 
     * This method will end the academic term. The fist step will be to lock the system to
     * disable the user from performing any actions necessary to begin the next academic term
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function endacademicterm(stdClass $params) {

        // global variables
        global $defaultUser;

        // create a new scheduler id
        $scheduler_id = strtoupper(random_string("alnum", RANDOM_STRING));

        // assign a new variable
		$academics = $this->iclient->client_preferences->academics;
		
		// set variables for the academic year and term
		$academic_year = $academics->academic_year;
		$academic_term = $academics->academic_term;
		$next_academic_year = $academics->next_academic_year;
		$next_academic_term = $academics->next_academic_term;

        // confirm that the academic term and year are not empty
        if(empty($next_academic_year) || empty($next_academic_term)) {
            return [
                "code" => 400,
                "data" => "Sorry! The next academic year and term cannot be empty. Ensure it has been correctly set before proceeding"
            ];
        }

        // verify that the next academic year/term isnt the same as the current one
        if("{$academic_year}_{$academic_term}" == "{$next_academic_year}_{$next_academic_term}") {
            // return an error message
            return [
                "code" => 400,
                "data" => "Fatal Error! Please ensure that the current academic year and term is not the same as the next academic year and term.
                    This can be corrected under the SETTINGS panel.",
            ];
        }

        // get the data to import
        $data_to_import = isset($params->data_to_import) ? $this->stringToArray($params->data_to_import) : [];

        // set the current timestamp
        $current_timestamp = date("Y-m-d H:i:s", strtotime("+2 minutes"));

        // insert a new cron job scheduler for this activity
        $stmt = $this->db->prepare("INSERT INTO cron_scheduler SET client_id = '{$params->clientId}', item_id = ?, user_id = ?, cron_type = ?, subject = ?, active_date = '{$current_timestamp}', query = ?");
        $stmt->execute([$scheduler_id."_".$params->clientId, $params->userId, "end_academic_term", "End Academic Term for {$defaultUser->appPrefs->academics->academic_year}", json_encode($data_to_import)]);

        // update the information in the database table
        $stmt = $this->db->prepare("UPDATE clients_accounts SET client_state = ? WHERE client_id = ? LIMIT 1");
        $stmt->execute(["Propagation", $params->clientId]);

        // log the user activity
        $this->userLogs("end_academic_term", $params->clientId, null, "{$params->userData->name} requested to end this academic term.", $params->userId);

        // reset the client data information
        $this->client_session_data($params->clientId, true);

        // return the success reponse
        return [
            "code" => 200,
            "data" => "You have successfully initiated the propagation process.",
        ];
    }

    /**
     * Complete the Account setup process
     * 
     * This will set the account state to Active
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function complete_setup(stdClass $params) {

        // get the client state
        $client_state = $this->iclient->client_state;

        // get the client data
        $stmt = $this->db->prepare("UPDATE clients_accounts SET client_state = ? WHERE client_id = ? LIMIT 1");
        $stmt->execute(['Active', $params->clientId]);

        // confirm that this files already exists
        if(!in_array($client_state, ["Complete", "Propagation"])) {} else {
            // set the last visited page
            $stmt = $this->db->prepare("UPDATE users SET last_visited_page='{{APPURL}}dashboard' WHERE item_id=? LIMIT 1");
            $stmt->execute([$params->userId]);
        }

        // reset the client data information
        $this->client_session_data($params->clientId, true);

        // return a success message
        return [
            "code" => 200,
            "data" => "Account setup is successfully completed."
        ];
    }

    /**
     * Update Account Information
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        // return error
        if(!isset($params->general["labels"])) {
            return ["code" => 400, "data" => "Sorry! Ensure labels have been parsed."];
        }

        // academics and labels must be an array
        if(!is_array($params->general["labels"])) {
            return ["code" => 400, "data" => "Sorry! Labels must be an array."];
        }

        // get the client data
        $client_data = $this->iclient;

        $return = ["data" => "Account information successfully updated."];

        // confirm that a logo was parsed
        if(isset($params->logo)) {

            // set the upload directory
            $uploadDir = "assets/img/accounts/";

            if(!is_dir($uploadDir)) {
                mkdir($uploadDir);
            }

            // File path config 
            $file_name = basename($params->logo["name"]); 
            $targetFilePath = $uploadDir . $file_name; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg','gif');
            // check if its a valid image
            if(!empty($file_name) && validate_image($params->logo["tmp_name"])){
                // set a new file_name
                $image = $uploadDir . random_string("alnum", RANDOM_STRING).".{$fileType}";
                // Upload file to the server 
                if(move_uploaded_file($params->logo["tmp_name"], $image)){
                    // set the redirection
                    $return["additional"] = ["href" => "{$this->baseUrl}settings/_general"];
                }
            } else {
                return ["code" => 400, "data" => "Sorry! The logo must be a valid image."];
            }
        }

        // put the preferences together
        $preference["labels"] = $params->general["labels"];
        $preference["opening_days"] = $params->general["opening_days"] ?? [];
        $preference["features_list"] = $client_data->client_preferences->features_list ?? [];
        $preference["account"] = $client_data->client_preferences->account;
        $preference["academics"] = $client_data->client_preferences->academics;
        $preference["billing"] = $params->general["billing"] ?? [];

        foreach(["account_info", "additional_info"] as $key) {
            if(!empty($preference["billing"][$key])) {
                $preference["billing"][$key] = nl2br($preference["billing"][$key]);
            }
        }
        $preference["billing"]["registration_code"] = $params->general["registration_code"] ?? null;

        if(!empty($preference["billing"]["registration_code"])) {
            $preference["billing"]["registration_code"] = strtoupper($preference["billing"]["registration_code"]);
        }

        // if the id card is not empty
        if(!empty($client_data->client_preferences->id_card)) {
            $preference["id_card"] = $client_data->client_preferences->id_card;
        }

        // unset the values
        unset($params->general["opening_days"]);
        unset($params->general["academics"]);
        unset($params->general["labels"]);
        unset($params->general["billing"]);
        unset($params->general["registration_code"]);

        // format
        $query = "";
        foreach($params->general as $key => $value) {
            $value = xss_clean($value);
            $query .= "client_{$key}='{$value}',";
        }

        if(empty($query)) {
            return ["code" => 400, "data" => "Sorry! Academics and Labels must be an array."];
        }
        try {

            // run the update of the account information
            $stmt = $this->db->prepare("UPDATE clients_accounts 
                SET {$query} client_preferences	= ? ".(isset($image) ? ", client_logo='{$image}'" : "")." WHERE client_id = ? LIMIT 1");
            $stmt->execute([json_encode($preference), $params->clientId]);

            // log the user activity
            $this->userLogs("account", $params->clientId, $client_data, "{$params->userData->name} updated the Account Information", $params->userId);

            // reset the client data information
            $this->defaultClientData = null;
            $this->client_session_data($params->clientId, true);

            return $return;

        } catch(PDOException $e) {
            return ["code" => 400, "data" => $e->getMessage()];
        }

    }

    /**
     * Update Card Settings
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update_card_settings(stdClass $params) {
        // get the client data
        $client_data = $this->iclient;
        
        $preference = $client_data->client_preferences;
        $preference->id_card = [
            'front_color' => $params->front_color,
            'back_color' => $params->back_color,
            'front_text_color' => $params->front_text_color,
            'back_text_color' => $params->back_text_color,
            'back_found_message' => $params->back_found_message
        ];

        try {

            // run the update of the account information
            $stmt = $this->db->prepare("UPDATE clients_accounts 
                SET client_preferences	= ? ".(!empty($params->contact_numbers) ? ", client_contact='{$params->contact_numbers}'" : "")." WHERE client_id = ? LIMIT 1");
            $stmt->execute([json_encode($preference), $params->clientId]);

            // return success message
            return ["code" => 200, "data" => "Card settings were successfully updated."];

        } catch(PDOException $e) {
            return ["code" => 400, "data" => $e->getMessage()];
        }
        
    }

    /**
     * Update Account Features Information
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function features(stdClass $params) {

        // global parameters
        global $isSupport;
        
        // ensure an array was parsed
        if(!empty($params->features) && !is_array($params->features)) {
            return ["code" => 400, "data" => "Sorry! Features must be an array."];
        }

        // if the user is an admin
        if(!$isSupport && ($params->client_id !== $params->clientId)) {
            return ["code" => 400, "data" => "Sorry! You attempted to update an account that is not yours."];
        }

        // if a user is a support
        if($isSupport) {
            // set the parameters for the data to load
            $t_data = $this->pushQuery("*", "clients_accounts", "client_id='{$params->client_id}' LIMIT 1");
            $client_data = $t_data[0];
            $client_data->client_preferences = json_decode($client_data->client_preferences);
            $preference = $client_data->client_preferences;
        } else {
            // get the client data
            $client_data = $this->iclient;
            $preference = $client_data->client_preferences;
        }

        // append the card_momo_payments
        $this->features_list["e_payments"] = "Card_MoMo_Payments";

        // get the features list
        $accepted_features = array_keys($this->features_list);
        $features_list = !empty($params->features) ? array_values($params->features) : [];

        // loop through the list to ensure a valid item was parsed
        foreach($features_list as $item) {
            if(!in_array($item, $accepted_features)) {
                return ["code" => 400, "data" => "Sorry! An invalid feature was parsed."];
            }
        }

        // set the features list
        $client_data->client_preferences->features_list = $features_list;

        // run the update of the account information
        $stmt = $this->db->prepare("UPDATE clients_accounts SET client_preferences	= ? WHERE client_id = ? LIMIT 1");
        $stmt->execute([json_encode($client_data->client_preferences), $params->client_id ?? $params->clientId]);

        // log the user activity
        $this->userLogs("setup_preference", $params->client_id ?? $params->clientId, $preference, "{$params->userData->name} updated the Account Preferences", $params->userId);

        // reset the client data if not a support personnel
        if($isSupport) {
            // reset the client data information
            $this->client_session_data($params->client_id ?? $params->clientId, true);
        }

        // parse success message
        return ["code" => 200, "data" => "Account preference was successfully updated."];
    }

    /**
     * Update Academic Calendar Information
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function calendar(stdClass $params) {

        // global variable
        global $academicSession;

        // readonly mode session
        if(!empty($this->session->is_only_readable_app)) {
            return $this->readonly_mode;
        }

        // return error
        if(!isset($params->general["academics"])) {
            return ["code" => 400, "data" => "Sorry! Ensure academics have been parsed."];
        }

        // academics and labels must be an array
        if(!is_array($params->general["academics"])) {
            return ["code" => 400, "data" => "Sorry! Academics must be an array."];
        }

        // get the client data
        $client_data = $this->iclient;

        $return = [
            "data" => "Academic Calendar was successfully updated.", 
            "additional" => ["href" => "{$this->baseUrl}settings/_calendar"]
        ];

        // put the preferences together
        $_academics = $params->general["academics"];
        $preference["academics"] = $_academics;
        $n_session = $params->general["sessions"]["session"];
        $preference["sessions"] = $params->general["sessions"];
        $preference["labels"] = $client_data->client_preferences->labels;
        $preference["opening_days"] = $client_data->client_preferences->opening_days ?? [];
        $preference["account"] = $client_data->client_preferences->account;
        $preference["features_list"] = $client_data->client_preferences->features_list ?? [];

        // get the academic year and term
        $_academic_year = $_academics["academic_year"];
        $_academic_term = $_academics["academic_term"];
        $_next_academic_year = $_academics["next_academic_year"];
        $_next_academic_term = $_academics["next_academic_term"];

        // if the id card is not empty
        if(!empty($client_data->client_preferences->id_card)) {
            $preference["id_card"] = $client_data->client_preferences->id_card;
        }

        // confirm if the next academic year and term is equal to the current academic year and term
        if("{$_next_academic_year}_{$_next_academic_term}" == "{$_academic_year}_{$_academic_term}") {
            return ["code" => 400, "data" => "Sorry! The next Academic Year and term cannot be the same as the current details."];
        }

        // check if the next academic year and term is already logged as completed
        $_check = $this->pushQuery(
            "year_starts,year_ends,term_starts,term_ends", 
            "clients_terminal_log", 
            "client_id='{$params->clientId}' AND academic_year='{$_next_academic_year}' AND academic_term='{$_next_academic_term}' LIMIT 1");

        // confirm that the academic year and term is not empty
        if(!empty($_check)) {
            return ["code" => 400, "data" => "Sorry! The selected Next Academic Year & Term began on
                ".date("jS F Y", strtotime($_check[0]->term_starts))." to 
                ".date("jS F Y", strtotime($_check[0]->term_ends))." hence cannot be repeated."];
        }

        // check if the current academic year and term has been logged as completed
        $_check = $this->pushQuery(
            "year_starts,year_ends,term_starts,term_ends", 
            "clients_terminal_log", 
            "client_id='{$params->clientId}' AND academic_year='{$_academic_year}' AND academic_term='{$_academic_term}' LIMIT 1");
        
        // set the session is_only_readable_app if not empty
        if(!empty($_check)) {
            // set it as readonly
            return ["code" => 400, "data" => "Sorry! The selected Academic Year & Term began on
                ".date("jS F Y", strtotime($_check[0]->term_starts))." to 
                ".date("jS F Y", strtotime($_check[0]->term_ends))." hence cannot be repeated."];
        }

        // set a random limit
        // not sure the academic terms will exceed 10 for any school
        $rand_limit = 10;

        // change the academic session names if the current doesn't match what we have in the database
        $list = $this->pushQuery("id, name", "academic_terms", "client_id='{$params->clientId}' LIMIT {$rand_limit}");
        
        // loop through the list
        foreach($list as $item) {
            // update the record
            $this->db->query("UPDATE academic_terms SET description='{$item->name} {$n_session}' WHERE id='{$item->id}' LIMIT 1");
        }

        try {

            // run the update of the account information
            $stmt = $this->db->prepare("UPDATE clients_accounts SET client_preferences	= ? WHERE client_id = ? LIMIT 1");
            $stmt->execute([json_encode($preference), $params->clientId]);

            // old record
            $prevData = $this->pushQuery("a.description", "files_attachment a", "a.resource='settings_calendar' AND a.resource_id='{$params->clientId}' LIMIT 1");

            /** Confirm that there is an attached document */
            if(!empty($prevData) && isset($prevData[0]->description)) {
                // decode the json string
                $db_attachments = json_decode($prevData[0]->description);
                // get the files
                if(isset($db_attachments->files)) {
                    $initial_attachment = $db_attachments->files;
                }
            }

            // files object and upload the file for the academic calendar
            $filesObj = load_class("files", "controllers");
            $attachments = $filesObj->prep_attachments("settings_calendar", $params->userId, $params->clientId, $initial_attachment ?? []);

            // update attachment if already existing
            if(isset($db_attachments)) {
                $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE resource = ? AND client_id = ? LIMIT 1");
                $files->execute([json_encode($attachments), $attachments["raw_size_mb"], 'settings_calendar', $params->clientId]);
            } else {
                // insert the record if not already existing
                $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?, client_id = ?");
                $files->execute(["settings_calendar", $params->clientId, json_encode($attachments), "{$params->clientId}", $params->userId, $attachments["raw_size_mb"], $params->clientId]);
            }

            // log the user activity
            $this->userLogs("account", $params->clientId, $client_data, "{$params->userData->name} updated the Account Information", $params->userId);
            
            // reset the client data information
            $this->client_session_data($params->clientId, true);

            return $return;

        } catch(PDOException $e) {}

    }

    /**
     * Chanage the Current Academic Year/Term
     *
     * This method is used to review previous academic year and term.
     * 
     * @param String    $params->academic_year
     * @param String    $params->academic_term
     *
     * @return Array
     **/
    public function set_default_year(stdClass $params) {

        // check if the current academic year and term has been logged as completed
        $_check = $this->pushQuery(
            "year_starts,year_ends,term_starts,term_ends", 
            "clients_terminal_log", 
            "client_id='{$params->clientId}' AND academic_year='{$params->academic_year}' AND academic_term='{$params->academic_term}' LIMIT 1");
        
        // set the session is_only_readable_app if not empty
        if(!empty($_check)) {
            // set it in an array
            $this->session->set([
                "is_only_readable_app" => true,
                "is_readonly_academic_year" => $params->academic_year,
                "is_readonly_academic_term" => $params->academic_term,
                "is_readonly_term_starts" => $_check[0]->term_starts,
                "is_readonly_term_ends" => $_check[0]->term_ends,
                "is_readonly_year_starts" => $_check[0]->year_starts,
                "is_readonly_year_ends" => $_check[0]->year_ends
            ]);
        } else {
            // remove the sessions that has been set
            $this->session->remove(["is_only_readable_app", "is_readonly_academic_year", "is_readonly_term_starts", 
                "is_readonly_academic_term", "is_readonly_year_starts", "is_readonly_year_ends", "is_readonly_term_ends"
            ]);
            // reset the client data information
            $this->client_session_data($params->clientId, true);
        }

        // set it as readonly
        return ["code" => 200, "data" => "The Academic Session was successfully changed."];
    }

    /**
     * Save the Academic Grading System
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update_grading(stdClass $params) {

        // readonly mode session
        if(!empty($this->session->is_only_readable_app)) {
            return $this->readonly_mode;
        }

        // confirm its an array
        if(!is_array($params->grading_values) || !is_array($params->report_columns)) {
            return ["code" => 400, "data" => "Sorry! An array data is expected"];
        }

        // get the client data
        global $defaultClientData;
        $client_data = $defaultClientData;

        // grading_sba calculation
        if(!empty($params->grading_sba) && is_array($params->grading_sba)) {
            $total_grade = 0;
            foreach($params->grading_sba as $key => $value) {
                if(isset($value['percentage']) && preg_match("/^[0-9]+$/", $value['percentage']) && $value['sba_checkbox'] == 'true') {
                    $total_grade += $value['percentage'];
                }
            }
            if($total_grade !== 100) {
                return ["code" => 400, "data" => "Sorry! The selected SBA Summation must be equal to 100%. The current value is {$total_grade}%."];
            }
        }

        // check the report grading columns and ensure it does not exceed 100%
        if(isset($params->report_columns["columns"]) && !empty($params->report_columns["columns"])) {
            // assign
            $score = 0;
            $grading = (array) $params->report_columns["columns"];

            // loop through the grading
            foreach($grading as $key => $value) {
                $score += !preg_match("/^[0-9]+$/", $value["percentage"]) ? 0 : $value["percentage"];
            }

            // if the score is more than 100 then alert the user
            if($score > 100) {
                return ["code" => 400, "data" => "Sorry! The score must be equal to 100%. The current value is {$score}%."];
            }
        }

        // insert a new record
        if(empty($client_data->grading_system)) {

            // prepare and execute the statement.
            $stmt = $this->db->prepare("INSERT INTO grading_system SET client_id = ?, grading = ?, structure = ?, academic_year = ?, academic_term = ?
                ".(isset($params->report_columns["show_position"]) ? ",show_position='{$params->report_columns["show_position"]}'" : "")."
                ".(isset($params->report_columns["group_sba"]) ? ",group_sba='{$params->report_columns["group_sba"]}'" : "")."
                ".(isset($params->report_columns["show_teacher_name"]) ? ",show_teacher_name='{$params->report_columns["show_teacher_name"]}'" : "")."
                ".(isset($params->report_columns["allow_submission"]) ? ",allow_submission='{$params->report_columns["allow_submission"]}'" : "")."
                ".(isset($params->grading_sba) ? ",sba='".json_encode($params->grading_sba)."'" : "")."
            ");
            $stmt->execute([$params->clientId, json_encode($params->grading_values), json_encode($params->report_columns), $params->academic_year, $params->academic_term]);

            // reset the client data information
            $this->client_session_data($params->clientId, true);
        } else {

            // update the values if not already set
            $stmt = $this->db->prepare("UPDATE grading_system SET grading = ?, structure = ?
                ".(isset($params->report_columns["show_position"]) ? ",show_position='{$params->report_columns["show_position"]}'" : "")."
                ".(isset($params->report_columns["group_sba"]) ? ",group_sba='{$params->report_columns["group_sba"]}'" : "")."
                ".(isset($params->report_columns["show_teacher_name"]) ? ",show_teacher_name='{$params->report_columns["show_teacher_name"]}'" : "")."
                ".(isset($params->report_columns["allow_submission"]) ? ",allow_submission='{$params->report_columns["allow_submission"]}'" : "")."
                ".(isset($params->grading_sba) ? ",sba='".json_encode($params->grading_sba)."'" : "")."
            WHERE client_id = ? AND academic_year = ? AND academic_term = ? LIMIT 1");
            $stmt->execute([json_encode($params->grading_values), json_encode($params->report_columns), $params->clientId, $params->academic_year, $params->academic_term]);

            // reset the client data information
            $this->client_session_data($params->clientId, true);
        }

        // return a success messsage
        return [
            "data" => "The grading system have successfully been updated", 
            "additional" => ["href" => "{$this->baseUrl}settings/_grading"]
        ];
    }

    /**
     * Upload CSV File Data
     * 
     * Save the information in a session to be used later on
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function upload_csv(stdClass $params) {

        if(!isset($this->accepted_column[$params->column])) {
            return ["code" => 400, "data" => "Sorry! An invalid column value was parsed"];
        }

        if(empty($params->csv_file['tmp_name'])) {
            return  [
                "data" => [
                    'column' => [], 'sample_csv_data' => [], 'data_count' => 0
                ]
            ];
        }

        // reading tmp_file name
        $csv_file = fopen($params->csv_file['tmp_name'], 'r');

        // get the content of the file
        $headers = fgetcsv($csv_file);
        $sample_csv_data = [];
        $complete_csv_data = [];

        //using while loop to get the information
        while($row = fgetcsv($csv_file)) {
            // session data
            $complete_csv_data[] = $row;
        }

        $i = 0;
        $data = [];
        $c_count = count($this->accepted_column[$params->column]);

        // loop through the data received from the 
        foreach($complete_csv_data as $each) {
            // clean the array set
            $clean_set = array_slice($each, 0, $c_count);
            $data[] = $clean_set;
            // push the data parsed by the user to the page
            if($i < 20)  {
                if(empty($clean_set[1]) || empty($clean_set[2])) continue;
                $sample_csv_data[] = $clean_set;
            }
            // increment
            $i++;
        }

        $clean = function($v) {
            return array_filter($v) != array();
        };
        $csv_data = array_filter($data, $clean);

        // slice the header
        $headers = array_slice($headers, 0, $c_count);

        // set the content in a session
        $this->session->set("{$params->column}_csv_file", $csv_data);

        // set the data to send finally
        return  [
            "data" => [
                'column'	=> $headers,
                'sample_csv_data' => $sample_csv_data,
                'data_count' => count($csv_data)
            ]
        ];

    }

    /**
     * Import the Data
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function import(stdClass $params) {

        global $clientPrefs;

        if(empty($params->column)) {
            return ["code" => 400, "data" => "Invalid request parsed."];
        }

        // columns to use for the query
        $accepted_column = $this->accepted_column[$params->column] ?? [];

        // not found
        $notFound = 0;

        // check if the keys are all valid
        foreach($params->csv_keys as $thisKey) {
            if(!in_array($thisKey, array_values($accepted_column))) {
                $notFound++;
            }
        }

        if(empty($accepted_column)) {
            return ["code" => 400, "data" => "Invalid request parsed to save the data."];
        }

        // keys count
        $keys_count = count($params->csv_keys);

        if($keys_count > count(array_keys($accepted_column))) {
            // break the code if an error was found
            return ["code" => 400, "data" => 'Required columns exceeded. Please confirm and try.'];
        } elseif($notFound) {
            // break the code if an error was found
            return ["code" => 400, "data" => 'Invalid column parsed. Please confirm all columns match.'];
        }

        // start at zero
        $i = 0;

        // append the user_id column and value
        $table = [
            "student" => "users",
            "staff" => "users",
            "course" => "courses",
            "parent" => "users",
            "class" => "classes"
        ];
        $user_type = ["teacher", "admin", "employee", "accountant"];

        // confirm that the column table exists
        if(!isset($table[$params->column])) {
            return ["code" => 400, "data" => 'Invalid request parsed.'];
        }

        // begin the processing of the array data
        $sqlQuery = "INSERT INTO {$table[$params->column]} (`upload_id`,`created_by`,`item_id`,`client_id`,`academic_year`,`academic_term`,";
        
        // if the user type is student
        if(in_array($params->column, ["student", "parent", "staff"])) {
            $sqlQuery = "INSERT INTO {$table[$params->column]} (`upload_id`,`created_by`,`item_id`,`client_id`,";
            $sqlQuery .= "`user_type`,";
        }

        // continue processing the request
        foreach($params->csv_keys as $thisKey) {
            // increment
            $i++;
            // append to the sql query
            $sqlQuery .= "`".array_search(xss_clean($thisKey), $accepted_column)."`";
            // append a comma if the loop hasn't ended yet
            if($i < $keys_count) $sqlQuery .= ",";
        }

        // append the last bracket
	    $sqlQuery .= ") VALUES";

        $newCSVArray = [];
        $session_key = "{$params->column}_csv_file";

        // set the values
        if(!empty($params->csv_values) and is_array($params->csv_values)) {
            $newCSVArray = [];
            foreach($this->session->{$session_key} as $key => $eachCsvValue) {
                $newCSVArray[$key] = $eachCsvValue;
            }
        }

        // run this section if the new array is not empty
        if(!empty($newCSVArray)) {

            // init bugs checker
            $bugs = [];

            // confirm some uniqueness of the ids supplied
            $unique_id = [];
            $userPermission = null;
            $upload_id = random_string("alnum", RANDOM_STRING);

            $isUser = (bool) in_array($params->column, ["student", "staff", "parent"]);

            // generate  new key
            if($isUser) {
                $items_last_row["student"] = $this->itemsCount("users", "client_id = '{$params->clientId}' AND user_type='student' LIMIT {$this->global_limit}");
                $items_last_row["guardian"] = $this->itemsCount("users", "client_id = '{$params->clientId}' AND user_type='parent' LIMIT {$this->global_limit}");
                $items_last_row["staff"] = $this->itemsCount("users", "client_id = '{$params->clientId}' AND user_type NOT IN ('student', 'parent') LIMIT {$this->global_limit}");
            }

            $not_yet_set = true;

            // set a new variable
            if(in_array($params->column, ["student","parent"]) && $not_yet_set) {
                $fresh_unique_id = $items_last_row[$params->column];
                $label = $params->column.'_label';
            } else {
                $label = "staff_label";
            }
            
            // loop through each array dataset
            foreach($newCSVArray as $eachData) {

                // set the user type
                $t_user_type = "";

                // append the customer_id column and value
                $unqData = random_string("alnum", RANDOM_STRING);

                // initializing
                // $sqlQuery .= '("'.$upload_id.'","'.$params->userId.'","'.$unqData.'","'.$params->clientId.'","'.$params->academic_year.'","'.$params->academic_term.'",';
                $sqlQuery .= '("'.$upload_id.'","'.$params->userId.'","'.$unqData.'","'.$params->clientId.'",';
                $ik = 0;

                if(in_array($params->column, ["student","parent"])) {
                    $sqlQuery .= '"'.$params->column.'",';
                }

                // loop through each data
                foreach($eachData as $eachKey => $eachValue) {
                    $ik++;

                    // perform these checks for the arrayed list
                    if($isUser) {
                        
                        // trim the values
                        $eachValue = trim($eachValue);
                        $params->csv_keys[$eachKey] = trim($params->csv_keys[$eachKey]);

                        // if email then validate it
                        if(($params->csv_keys[$eachKey] === "Email") && !empty($eachValue) && !filter_var($eachValue, FILTER_VALIDATE_EMAIL)) {
                            $bugs["email"] = "Please ensure the email section contains only valid email addresses.";
                        }
                        if(($params->csv_keys[$eachKey] === "Employee ID") || ($params->csv_keys[$eachKey] === "Student ID") || ($params->csv_keys[$eachKey] === "Guardian ID")) {

                            // increment the counter
                            $counter = $this->append_zeros(($fresh_unique_id + 1), $this->append_zeros);
                            $user_unique_id = $clientPrefs->labels->{$label}.$counter.date("Y");

                            // set the new id if empty
                            if(empty($eachValue)) {
                                $not_yet_set = false;
                                $fresh_unique_id += 1;
                                $eachValue = $user_unique_id;
                            }
                            // set the new user id
                            $eachValue = strtoupper($eachValue);
                            $unique_id[$eachValue] = isset($unique_id[$eachValue]) ? ($unique_id[$eachValue]+1) : 1;
                        }
                        if(($params->csv_keys[$eachKey] === "Contact Number") && !empty($eachValue)) {
                            $eachValue = str_ireplace(" ", "", $eachValue);
                            if(!preg_match("/^[0-9 +]+$/", $eachValue)) {
                                $bugs["phone_number"] = "Please ensure the contact number contains only numeric integers: eg. 0244444444 | +23324444444.";
                            }
                        }
                        if(($params->csv_keys[$eachKey] === "Date of Birth")) {
                            $eachValue = date("Y-m-d", strtotime($eachValue));
                            if( !isvalid_date($eachValue) ) {
                                $bugs["date_of_birth"] = "Please ensure a valid Date of Birth was supplied: eg. YYYY-MM-DD";
                            }
                        }
                        if(($params->csv_keys[$eachKey] === "Gender")) {
                            $eachValue = ucfirst(strtolower($eachValue));
                        }
                        if(($params->csv_keys[$eachKey] === "Blood Group")) {
                            $eachValue = strtoupper($eachValue);
                        }
                        if(($params->csv_keys[$eachKey] === "Date of Employment")) {
                            $eachValue = date("Y-m-d", strtotime($eachValue));
                            if( !isvalid_date($eachValue) ) {
                                $bugs["date_of_employment"] = "Please ensure a valid Date of employment was supplied: eg. YYYY-MM-DD";
                            }
                        }
                        if(($params->csv_keys[$eachKey] === "Admission Date")) {
                            $eachValue = date("Y-m-d", strtotime($eachValue));
                            if( !isvalid_date($eachValue) ) {
                                $bugs["admission_date"] = "Please ensure a valid Admission Date was supplied: eg. YYYY-MM-DD";
                            }
                        }
                        if(in_array($params->csv_keys[$eachKey], ["Department", "Section", "Class ID"])) {
                            $eachValue = $this->get_equivalent($params->csv_keys[$eachKey], $eachValue, $params->clientId);
                        }
                        if(in_array($params->csv_keys[$eachKey], ["Country Code"])) {
                            $eachValue = $this->country_equivalent($eachValue);
                        }
                        if(in_array($params->column, ["student", "parent"])) {
                            $t_user_type = $params->column;
                        }
                        if(($params->csv_keys[$eachKey] == "User Type")) {
                            $type = strtolower($eachValue);
                            $fresh_unique_id = $items_last_row[$type] ?? NULL;
                            if(!in_array($type, $user_type)) {
                                $bugs["user_type"] = "Please ensure the user type is one of the following: teacher, employee, accountant, admin";
                            } else {
                                $t_user_type = $type;
                            }
                        }
                    }
                    
                    if(in_array($params->column, ["course", "class"])) {
                        if(($params->csv_keys[$eachKey] === "Course Code") || ($params->csv_keys[$eachKey] === "Class Code")) {
                            $unique_id[$eachValue] = isset($unique_id[$eachValue]) ? ($unique_id[$eachValue]+1) : 1;
                        }
                        if(($params->csv_keys[$eachKey] === "Credit Hours") && !preg_match("/^[0-9]+$/", $eachValue)) {
                            $bugs["credit_hours"] = "The credit hours must be a numeric interger: 0-9.";
                        }
                    }

                    // create sql string for the values
                    $sqlQuery .= '"'.xss_clean($eachValue).'"';

                    if($ik < $keys_count) $sqlQuery .= ",";
                }
                $sqlQuery .= '),';

                // if $t_user_type is not empty
                if($t_user_type) {
                    // create a new permission data
                    $userPermission .= "INSERT INTO users_roles SET user_id='{$unqData}', client_id='{$params->clientId}',last_updated='{$this->current_timestamp}', permissions = (SELECT user_permissions FROM users_types WHERE description='{$t_user_type}' LIMIT 1);";
                }
            }

            $sqlQuery = substr($sqlQuery, 0, -1) . ';';

            // confirm that there were no repetitions of the unique ids
            if(!empty($unique_id)) {
                $repeat = 0;
                foreach($unique_id as $key => $value) {
                    if($value > 1) {
                        $repeat += $value;
                        $bugs["unique_id"] = "{$repeat} number of {$params->column} ids were repeated.";
                    }
                }
            }

            // return the bugs found
            if(!empty($bugs)) {
                $bugs_list = "";
                $count = 0;
                foreach($bugs as $bug) {
                    $count++;
                    $bugs_list .= "{$count}. {$bug}\n";
                }
                return ["code" => 400, "data" => $bugs_list];
            }
            
            try {

                // execute the sql statement
                $query = $this->db->prepare($sqlQuery);
                $query->execute();

                // if the permission is not empty
                if($userPermission) {
                    // execute the user permissions as well
                    $permit = $this->db->prepare($userPermission);
                    $permit->execute();
                }

                // capitalize each first word
                $import = ucfirst($params->column);

                // set a cron job activity for the users_uploaded
                if($isUser) {
                    // insert the activity into the cron_scheduler
                    $query = $this->db->prepare("INSERT INTO cron_scheduler SET client_id = ?, item_id = ?, user_id = ?, cron_type = ?, active_date = '{$this->current_timestamp}'");
                    $query->execute([$params->clientId, $upload_id, $params->userId, "users_upload"]);
                    // set the sesssion value
                    $this->session->last_recordUpload = $params->column;
                }
                
                // if the upload was for a course
                if(in_array($params->column, ["course", "staff"])) {
                    // insert the activity into the cron_scheduler
                    $query = $this->db->prepare("INSERT INTO cron_scheduler SET client_id = ?, item_id = ?, user_id = ?, cron_type = ?, active_date = '{$this->current_timestamp}'");
                    $query->execute([$params->clientId, $upload_id, $params->userId, "course_tutor"]);
                    // set the sesssion value
                    $this->session->last_recordUpload = $params->column;
                }

                // unset all existing sessions
                $this->session->remove([$session_key, "last_recordUpload"]);

                // return success
                return ["data" => "{$import}s data was successfully imported."];

            } catch(PDOException $e) {
                return ["code" => 400, "data" => $e->getMessage()];
            }

        }

        // return error
        return ["code" => 400, "data" => "Sorry! No data was submitted to be processed."];

    }

    /**
     * Get Equivalent Unique ID
     * 
     * Get the unique id using the name slug of the name
     * Get just one value from the list
     * 
     * @param String $column
     * @param String $value
     * @param String $clientId
     * 
     * @return String 
     */
    public function get_equivalent($column, $value, $clientId) {
        $tables = [
            "Department" => ["departments", "department"],
            "Section" => ["sections", "section"],
            "Class" => ["classes", "class"],
            "Class ID" => ["classes", "class"],
            "Course" => ["courses", "course"]
        ];
        $column = trim($column);

        // if the column is set
        if(isset($tables[$column])) {
            $n_value = create_slug($value);
            
            // get the code value
            $t_code = strtoupper($value);
            $item_code = strtolower($tables[$column][1])."_code";

            try {

                $field = in_array($column, ["Class", "Class ID", "Department"]) ? "id" : "item_id";

                $fetch = $this->db->prepare("SELECT {$field} FROM {$tables[$column][0]} WHERE (slug='{$n_value}' OR {$item_code}='{$t_code}') AND client_id='{$clientId}' AND status='1' ORDER BY id DESC LIMIT 1");
                $fetch->execute();
                $result = $fetch->fetch(PDO::FETCH_OBJ);

                return $result->item_id ?? (
                    $result->id ?? null
                );

            } catch(PDOException $e) { } 
        }
    }

    /**
     * Get Equivalent Country ID
     * 
     * Get the unique id using the name slug of the name
     * 
     * @param String $value
     * 
     * @return Int 
     */
    public function country_equivalent($value) {
        $n_value = strtoupper($value);
        try {
            $fetch = $this->db->prepare("SELECT id FROM country WHERE country_code='{$n_value}' LIMIT 1");
            $fetch->execute();
            $result = $fetch->fetch(PDO::FETCH_OBJ);
            return $result->id ?? null;
        } catch(PDOException $e) { } 
    }

    /**
     * Download Temporary CSV Files for Uploads
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function download_temp(stdClass $params) {
        
        // init
        $file_list = [];

        // convert the files to generate in an array
        $columns = $this->stringToArray($params->file);

        // upload file
        $temp_dir = "assets/uploads/{$params->clientId}/temp";
        
        // if not a directory then create it
        if(!is_dir($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        foreach($columns as $file) {

            // input item
            $content = "";
        
            if(isset($this->accepted_column[$file])) {

                // set the content
                $columns = array_values($this->accepted_column[$file]);
                $step = count($columns)+2;

                $table = [
                    "departments" => ["column" => "id, name, department_code AS code"],
                    "classes" => ["column" => "id, name, class_code AS code"]
                ];

                // set the content of the file to download
                $content = implode(",", $columns)."\n";

                // run this section for student and staff
                if(in_array($file, ["student", "staff"])) {
                    
                    //append some empty fields
                    $content .= str_repeat(',', $step)."\n";
                    $content .= str_repeat(',', $step)."\n";

                    // load this section for only student
                    if($file === "staff") {
                        $content .= str_repeat(',', $step)."USER TYPES\n";
                        foreach(["teacher", "employee", "accountant", "admin"] as $user_type) {
                            $content .= str_repeat(',', $step).ucwords($user_type)."\n";
                        }
                        $content .= str_repeat(',', $step)."\n";
                        $content .= str_repeat(',', $step)."\n";
                    }

                    foreach($table as $i => $v) {
                        try {
                            // general queries
                            $data_stmt = $this->db->prepare("SELECT {$v['column']} FROM {$i} WHERE client_id = '{$params->clientId}' AND status='1'");
                            $data_stmt->execute();

                            // data set
                            $content .= str_repeat(',', $step) . "".strtoupper($i)." LIST\n";

                            // if the row count is not zero
                            if($data_stmt->rowCount()) {
                                // append the header
                                $content .= str_repeat(',', $step)."ID,,NAME,,CODE\n";
                                // loop through the list of programmes
                                while($result = $data_stmt->fetch(PDO::FETCH_OBJ)) {
                                    // print the course information
                                    $content .= str_repeat(',', $step)."{$result->id},,{$result->name},,{$result->code}\n";
                                }
                                $content .= "\n\n";
                            }
                        } catch(\Exception $e) {}
                    }
                }

                $filename = "{$temp_dir}/{$file}_bulk_upload.csv";

                // write the content to the sample file
                $op = fopen($filename, 'w');
                fwrite($op, $content);
                fclose($op);

                $file_list[] = $filename;
            }
        }

        return [
            "code" => 200,
            "data" => $file_list
        ];

    }

}