<?php

class Account extends Myschoolgh {

	public function __construct(stdClass $params = null) {
		parent::__construct();
	}

    /**
     * Update Account Information
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        // return error
        if(!isset($params->general["academics"]) || !isset($params->general["labels"])) {
            return ["code" => 203, "data" => "Sorry! Ensure academics and labels have been parsed."];
        }

        // academics and labels must be an array
        if(!is_array($params->general["academics"]) || !is_array($params->general["labels"])) {
            return ["code" => 203, "data" => "Sorry! Academics and Labels must be an array."];
        }

        // get the client data
        $client_data = $this->client_data($params->clientId);

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
            if(!empty($file_name) && in_array($fileType, $allowTypes)){
                // set a new file_name
                $image = $uploadDir . random_string('alnum', 32).".{$fileType}";
                // Upload file to the server 
                if(move_uploaded_file($params->logo["tmp_name"], $image)){}
            } else {
                return ["code" => 203, "Sorry! The event file must be a valid image."];
            }
        }

        // put the preferences together
        $preference["academics"] = $params->general["academics"];
        $preference["labels"] = $params->general["labels"];
        $preference["opening_days"] = $params->general["opening_days"] ?? [];

        // unset the values
        unset($params->general["opening_days"]);
        unset($params->general["academics"]);
        unset($params->general["labels"]);

        // format
        $query = "";
        foreach($params->general as $key => $value) {
            $value = xss_clean($value);
            $query .= "client_{$key}='{$value}',";
        }

        if(empty($query)) {
            return ["code" => 203, "data" => "Sorry! Academics and Labels must be an array."];
        }

        try {

            // run the update of the account information
            $stmt = $this->db->prepare("UPDATE clients_accounts 
                SET {$query} client_preferences	= ? ".(isset($image) ? ", client_logo='{$image}'" : "")."
            WHERE client_id = ? LIMIT 1");
            $stmt->execute([json_encode($preference), $params->clientId]);

            // log the user activity
            // $this->userLogs("account", $params->clientId, $client_data, "{$params->userData->name} updated the Account Information", $params->userId);

            return [
                "data" => "Account information successfully updated."
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Upload CSV File Data
     * 
     * Save the information in a session to be used later on
     * 
     * @return Array
     */
    public function upload_csv(stdClass $params) {

        // reading tmp_file name
        $csv_file = fopen($params->csv_file['tmp_name'], 'r');

        // get the content of the file
        $column = fgetcsv($csv_file);
        $csv_data = array();
        $csvSessionData = array();
        $i = 0;

        //using while loop to get the information
        while($row = fgetcsv($csv_file)) {
            // session data
            $csvSessionData[] = $row;

            // push the data parsed by the user to the page
            if($i < 10)  {
                $csv_data[] = $row;
            }
            // increment
            $i++;
        }

        // set the content in a session
        $this->session->set("{$params->column}_csv_file", $csvSessionData);

        // set the data to send finally
        return  [
            "data" => [
                'column'	=> $column,
                'csv_data'	=>  $csv_data,
                'data_count' => count($csvSessionData)
            ]
        ];

    }

    /**
     * Import the Data
     * 
     * @return Array
     */
    public function import(stdClass $params) {

        // columns to use for the query
        if($params->column === "student") {
            $accepted_column = [
                "unique_id" => "Student ID", "firstname" => "Firstname", "lastname" => "Lastname", 
                "othername" => "Othernames", "email" => "Email", "phone_number" => "Contact Number",
                "blood_group" => "Blood Group", "residence" => "Residence", "date_of_birth" => "Date of Birth", 
                "enrollment_date" => "Admission Date", "gender" => "Gender", "section" => "Section", 
                "department" => "Department", "class_id" => "Class", "description" => "Description",
                "religion" => "Religion", "city" => "City", 
                "previous_school" => "Previous School", "previous_school_qualification" => "Previous School Qualification",
                "previous_school_remarks" => "Previous School Remarks"
            ];
        } elseif($params->column === "staff") {
            $accepted_column = [
                "unique_id" => "Employee ID", "firstname" => "Firstname", "lastname" => "Lastname", 
                "othername" => "Othernames", "email" => "Email", "phone_number" => "Contact Number",
                "blood_group" => "Blood Group", "residence" => "Residence", "date_of_birth" => "Date of Birth", 
                "enrollment_date" => "Date of Employment", "gender" => "Gender", "section" => "Section", 
                "department" => "Department", "description" => "Description",
                "religion" => "Religion", "city" => "City", 
                "courses_id" => "Courses Taught", "user_type" => "User Type", 
                "employer" => "Employer", "occupation" => "Occupation"
            ];
        } elseif($params->column === "course") {
            $accepted_column = [
                "course_code" => "Course Code", "name" => "Title", "credit_hours" => "Credit Hours", 
                "weekly_meeting" => "Weekly Meetings",  "description" => "Description"
            ];
        }

        // not found
        $notFound = 0;

        // check if the keys are all valid
        foreach($params->csv_keys as $thisKey) {
            if(!in_array($thisKey, array_values($accepted_column))) {
                $notFound++;
            }
        }

        // keys count
        $keys_count = count($params->csv_keys);

        if($keys_count > count(array_keys($accepted_column))) {
            // break the code if an error was found
            return ["code" => 203, "data" => 'Required columns exceeded. Please confirm and try.'];
        } elseif($notFound) {
            // break the code if an error was found
            return ["code" => 203, "data" => 'Invalid column parsed. Please confirm all columns match.'];
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
            return ["code" => 203, "data" => 'Invalid request parsed.'];
        }

        // begin the processing of the array data
        $sqlQuery = "INSERT INTO {$table[$params->column]} (`upload_id`,`created_by`,`item_id`,`client_id`,`academic_year`,`academic_term`,";
        
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
            
            // loop through the values list
            foreach($params->csv_values as $key => $eachCsvValue) {
                // print each csv value
                foreach($eachCsvValue as $eKey => $eValue) {
                    $newCSVArray[$eKey][] = $eachCsvValue[$eKey];
                }
            }

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
            $upload_id = random_string("alnum", 12);

            // loop through each array dataset
            foreach($newCSVArray as $eachData) {

                // set the user type
                $t_user_type = "";

                // append the customer_id column and value
                $unqData = random_string('alnum', 32);

                // initializing
                $sqlQuery .= "('{$upload_id}','{$params->userId}','{$unqData}','{$params->clientId}','{$params->academic_year}','{$params->academic_term}',";
                $ik = 0;

                // loop through each data
                foreach($eachData as $eachKey => $eachValue) {
                    $ik++;

                    // perform these checks for the arrayed list
                    if(in_array($params->column, ["student", "staff", "parent"])) {
                        // if email then validate it
                        if(($params->csv_keys[$eachKey] === "Email") && !filter_var($eachValue, FILTER_VALIDATE_EMAIL)) {
                            $bugs["email"] = "Please ensure the email section contains only valid email addresses.";
                        }
                        if(($params->csv_keys[$eachKey] === "Employee ID") || ($params->csv_keys[$eachKey] === "Student ID")) {
                            $unique_id[$eachValue] = isset($unique_id[$eachValue]) ? ($unique_id[$eachValue]+1) : 1;
                        }
                        if(($params->csv_keys[$eachKey] === "Contact Number") && !preg_match("/^[0-9+]+$/", $eachValue)) {
                            $bugs["phone_number"] = "Please ensure the contact number contains only numeric integers: eg. 0244444444 | +23324444444.";
                        }
                        if(($params->csv_keys[$eachKey] === "Date of Birth") && !isvalid_date($eachValue)) {
                            $bugs["date_of_birth"] = "Please ensure a valid Date of Birth was supplied: eg. YYYY-MM-DD";
                        }
                        if(($params->csv_keys[$eachKey] === "Date of Employment") && !isvalid_date($eachValue)) {
                            $bugs["date_of_employment"] = "Please ensure a valid Date of employment was supplied: eg. YYYY-MM-DD";
                        }
                        if(($params->csv_keys[$eachKey] === "Admission Date") && !isvalid_date($eachValue)) {
                            $bugs["admission_date"] = "Please ensure a valid Admission Date was supplied: eg. YYYY-MM-DD";
                        }
                        if(in_array($params->csv_keys[$eachKey], ["Department", "Section", "Class"])) {
                            $eachValue = $this->get_equivalent($params->csv_keys[$eachKey], $eachValue, $params->clientId);
                        }
                        if(in_array($params->column, ["student", "parent"])) {
                            $t_user_type = $params->column;
                        }
                        if(($params->csv_keys[$eachKey] == "User Type")) {
                            $type = strtolower($eachValue);
                            if(!in_array($type, $user_type)) {
                                $bugs["user_type"] = "Please ensure the user type is one of the following: teacher, employee, accountant, admin";
                            } else {
                                $t_user_type = $user_type;
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
                    $sqlQuery .= "'".xss_clean($eachValue)."'";

                    if($ik < $keys_count) $sqlQuery .= ",";
                }
                $sqlQuery .= "),";

                // if $t_user_type is not empty
                if($t_user_type) {
                    // create a new permission data
                    $userPermission .= "INSERT INTO users_roles SET user_id='{$unqData}', client_id='{$params->clientId}',last_updated=now(), permissions = (SELECT user_permissions FROM users_types WHERE description='{$t_user_type}' LIMIT 1);";
                }
            }

            $sqlQuery = substr($sqlQuery, 0, -1) . ';';

            // confirm that there were no repetitions of the unique ids
            if(!empty($unique_id)) {
                $repeat = 0;
                foreach($unique_id as $key => $value) {
                    if($value > 1) {
                        $repeat += $value;
                    }
                }
                $bugs["unique_id"] = "{$repeat} number of {$params->column} ids were repeated.";
            }

            // return the bugs found
            if(!empty($bugs)) {
                $bugs_list = "";
                $count = 0;
                foreach($bugs as $bug) {
                    $count++;
                    $bugs_list .= "{$count}. {$bug}\n";
                }
                return ["code" => 203, "data" => $bugs_list];
            }
            
            try {
                
                // begin transaction
                $this->db->beginTransaction();

                // execute the sql statement
                $query = $this->db->prepare($sqlQuery);
                $query->execute();

                // if the permission is not empty
                if($userPermission) {
                    // execute the user permissions as well
                    $permit = $this->db->prepare($userPermission);
                    $permit->execute();
                }

                // commit the transactions
                $this->db->commit();

                // capitalize each first word
                $import = ucfirst($params->column);

                // return success
                return ["data" => "{$import}s data was successfully imported."];

            } catch(PDOException $e) {
                $this->db->rollBack();
                return ["code" => 203, "data" => $e->getMessage()];
            }

        }

        // return error
        return ["code" => 203, "data" => "Sorry! No data was submitted to be processed."];

    }

    /**
     * Get Equivalent Unique ID
     * 
     * Get the unique id using the name slug of the name
     * Get just one value from the list
     * 
     * @return String 
     */
    public function get_equivalent($column, $value, $clientId) {
        $tables = [
            "Department" => "departments",
            "Section" => "sections",
            "Class" => "classes",
            "Course" => "courses"
        ];
        if(isset($tables[$column])) {
            $value = create_slug($value);
            $fetch = $this->db->prepare("SELECT item_id FROM {$tables[$column]} WHERE slug='{$value}' AND client_id='{$clientId}' AND status='1' ORDER BY id DESC LIMIT 1");
            $fetch->execute();
            $result = $fetch->fetch(PDO::FETCH_OBJ);

            return $result->item_id ?? null;
        }
    }

}