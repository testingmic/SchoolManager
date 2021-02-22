<?php

class Account extends Myschoolgh {

    public $accepted_column;

	public function __construct(stdClass $params = null) {
		parent::__construct();

        $this->accepted_column["student"] = [
            "unique_id" => "Student ID", "firstname" => "Firstname", "lastname" => "Lastname", 
            "othername" => "Othernames", "email" => "Email", "phone_number" => "Contact Number",
            "blood_group" => "Blood Group", "city" => "City", "residence" => "Residence", 
            "country" => "Country Code", "date_of_birth" => "Date of Birth", 
            "enrollment_date" => "Admission Date", "gender" => "Gender", "section" => "Section", 
            "department" => "Department", "class_id" => "Class", "description" => "Description",
            "religion" => "Religion",  "previous_school" => "Previous School", 
            "previous_school_qualification" => "Previous School Qualification",
            "previous_school_remarks" => "Previous School Remarks"
        ];

        $this->accepted_column["staff"] = [
            "unique_id" => "Employee ID", "firstname" => "Firstname", "lastname" => "Lastname", 
            "othername" => "Othernames", "email" => "Email", "phone_number" => "Contact Number",
            "blood_group" => "Blood Group", "city" => "City", "residence" => "Residence", 
            "country" => "Country Code", "date_of_birth" => "Date of Birth", 
            "enrollment_date" => "Date of Employment", "gender" => "Gender", "section" => "Section", 
            "department" => "Department", "description" => "Description",
            "religion" => "Religion", "course_ids" => "Courses Taught", "user_type" => "User Type", 
            "employer" => "Employer", "occupation" => "Occupation", "position" => "Position"
        ];
    
        $this->accepted_column["parent"] = [
            "unique_id" => "Employee ID", "firstname" => "Firstname", "lastname" => "Lastname", 
            "othername" => "Othernames", "email" => "Email", "phone_number" => "Primary Contact",
            "phone_number_2" => "Secondary Contact", "blood_group" => "Blood Group", 
            "city" => "City", "residence" => "Residence", "country" => "Country Code", 
            "date_of_birth" => "Date of Birth", 
            "gender" => "Gender", "description" => "Description",
            "religion" => "Religion", "employer" => "Employer",
            "occupation" => "Occupation", "position" => "Position"
        ];

        $this->accepted_column["course"] = [
            "course_code" => "Course Code", "name" => "Title", "credit_hours" => "Credit Hours", 
            "weekly_meeting" => "Weekly Meetings",  "description" => "Description", "course_tutor" => "Course Tutor IDs"
        ];

	}

    /**
     * Complete the Account setup process
     * 
     * This will set the account state to Active
     * 
     * @return Array
     */
    public function complete_setup(stdClass $params) {
        // get the client data
        $stmt = $this->db->prepare("UPDATE clients_accounts SET client_state = ? WHERE client_id = ? LIMIT 1");
        $stmt->execute(['Active', $params->clientId]);

        // generate a new script for this client
        $filename = "assets/js/scripts/{$params->clientId}_{$params->userData->user_type}_events.js";
        $data = $this->init_calender();
        $file = fopen($filename, "w");
        fwrite($file, $data);
        fclose($file);

        return [
            "data" => "Account setup is successfully completed."
        ];
    }

    /**
     * Init Calendar
     * 
     * This is the initial calendar to be created when a user creates a new account
     * 
     * @return String 
     */
    public function init_calender() {
        return "var calendarEvents = {
    id: 1,
    backgroundColor: '#136ae3bf',
    borderColor: '#0168fa',
    events: []
};
var birthdayEvents = {
    id: 2,
    backgroundColor: '#128b10d9',
    borderColor: '#10b759',
    events: []
};
var holidayEvents = {
    id: 3,
    backgroundColor: '#f10075b0',
    borderColor: '#f10075',
    events: []
};

function initiateCalendar() {
    $('#events_management').fullCalendar({
        header: {
            left: 'prev,today,next',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listMonth'
        },
        editable: false,
        droppable: false,
        draggable: false,
        dragRevertDuration: 0,
        defaultView: 'month',
        eventLimit: true,
        eventSources: [birthdayEvents, holidayEvents, calendarEvents],
        eventClick: function(event, jsEvent, view) {
            $('#modalTitle1').html(event.title);
            $('#modalBody1').html(event.description);
            $('#eventUrl').attr('href', event.url);
            $('#fullCalModal').modal();
        },
        dayClick: function(date, jsEvent, view) {
            $(`#createEventModal`).modal(\"show\");
            $(`#createEventModal input[name=\"date\"]`).val(`\${date.format()}:\${date.format()}`);
        }
    });
}
initiateCalendar();";
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
                SET {$query} client_preferences	= ? ".(isset($image) ? ", client_logo='{$image}'" : "")." WHERE client_id = ? LIMIT 1");
            $stmt->execute([json_encode($preference), $params->clientId]);

            // log the user activity
            $this->userLogs("account", $params->clientId, $client_data, "{$params->userData->name} updated the Account Information", $params->userId);

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

        if(!isset($this->accepted_column[$params->column])) {
            return ["code" => 203, "data" => "Sorry! An invalid column value was parsed"];
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
            $clean_set = array_slice($each, 0, $c_count-1);
            $data[] = $clean_set;
            // push the data parsed by the user to the page
            if($i < 10)  {
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
        $headers = array_slice($headers, 0, $c_count-1);

        // set the content in a session
        $this->session->set("{$params->column}_csv_file", $csv_data);

        // set the data to send finally
        return  [
            "data" => [
                'column'	=> $headers,
                'sample_csv_data' => $sample_csv_data,
                'csv_data'	=>  $csv_data,
                'data_count' => count($csv_data)
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
        $accepted_column = $this->accepted_column[$params->column] ?? [];

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
        
        // if the user type is student
        if(in_array($params->column, ["student", "parent"])) {
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
            $upload_id = random_string("alnum", 12);

            $isUser = (bool) in_array($params->column, ["student", "staff", "parent"]);
            
            // loop through each array dataset
            foreach($newCSVArray as $eachData) {

                // set the user type
                $t_user_type = "";

                // append the customer_id column and value
                $unqData = random_string('alnum', 32);

                // initializing
                $sqlQuery .= "('{$upload_id}','{$params->userId}','{$unqData}','{$params->clientId}','{$params->academic_year}','{$params->academic_term}',";
                $ik = 0;

                if(in_array($params->column, ["student","parent"])) {
                    $sqlQuery .= "'{$params->column}',";
                }

                // loop through each data
                foreach($eachData as $eachKey => $eachValue) {
                    $ik++;

                    // perform these checks for the arrayed list
                    if($isUser) {
                        // if email then validate it
                        if(($params->csv_keys[$eachKey] === "Email") && !filter_var($eachValue, FILTER_VALIDATE_EMAIL)) {
                            $bugs["email"] = "Please ensure the email section contains only valid email addresses.";
                        }
                        if(($params->csv_keys[$eachKey] === "Employee ID") || ($params->csv_keys[$eachKey] === "Student ID")) {
                            $eachValue = strtoupper($eachValue);
                            $unique_id[$eachValue] = isset($unique_id[$eachValue]) ? ($unique_id[$eachValue]+1) : 1;
                        }
                        if(($params->csv_keys[$eachKey] === "Contact Number") && !preg_match("/^[0-9+]+$/", $eachValue)) {
                            $bugs["phone_number"] = "Please ensure the contact number contains only numeric integers: eg. 0244444444 | +23324444444.";
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
                        if(in_array($params->csv_keys[$eachKey], ["Department", "Section", "Class"])) {
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
                return ["code" => 203, "data" => $bugs_list];
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
                    $query = $this->db->prepare("INSERT INTO cron_scheduler SET item_id = ?, user_id = ?, cron_type = ?, active_date = now()");
                    $query->execute([$upload_id, $params->userId, "users_upload"]);
                    // set the sesssion value
                    $this->session->last_recordUpload = $params->column;
                }

                // if the upload was for a course
                if($params->column === "course") {
                    // insert the activity into the cron_scheduler
                    $query = $this->db->prepare("INSERT INTO cron_scheduler SET item_id = ?, user_id = ?, cron_type = ?, active_date = now()");
                    $query->execute([$upload_id, $params->userId, "course_tutor"]);
                    // set the sesssion value
                    $this->session->last_recordUpload = $params->column;
                }

                // return success
                return ["data" => "{$import}s data was successfully imported."];

            } catch(PDOException $e) {
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
            $n_value = create_slug($value);
            
            $t_code = strtoupper($value);
            $item_code = strtolower($column)."_code";

            try {
                $fetch = $this->db->prepare("SELECT item_id FROM {$tables[$column]} WHERE (slug='{$n_value}' OR {$item_code}='{$t_code}') AND client_id='{$clientId}' AND status='1' ORDER BY id DESC LIMIT 1");
                $fetch->execute();
                $result = $fetch->fetch(PDO::FETCH_OBJ);

                return $result->item_id ?? null;

            } catch(PDOException $e) {} 
        }
    }

    /**
     * Get Equivalent Country ID
     * 
     * Get the unique id using the name slug of the name
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
        } catch(PDOException $e) {} 
    }

    /**
     * Download Temporary CSV Files for Uploads
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
                $step = count($columns)+3;

                $table = [
                    "department" => ["column" => "name, department_code"]
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

                    // general queries
                    $dept_stmt = $this->db->prepare("SELECT {$table["department"]["column"]} FROM departments WHERE client_id = '{$params->clientId}' AND status='1'");
                    $dept_stmt->execute();

                    // if the row count is not zero
                    if($dept_stmt->rowCount()) {
                        // append the header
                        $content .= str_repeat(',', $step)."DEPARTMENT NAME,,,,DEPARTMENT CODE\n";
                        // loop through the list of programmes
                        while($result = $dept_stmt->fetch(PDO::FETCH_OBJ)) {
                            // print the course information
                            $content .= str_repeat(',', $step)."{$result->name},,,,{$result->department_code}\n";
                        }
                    }

                    // load this section for only student
                    if($file === "student") {
                        //append some empty fields
                        $content .= str_repeat(',', $step)."\n";
                        $content .= str_repeat(',', $step)."\n";

                        //append some empty fields
                        $content .= str_repeat(',', $step)."\n";
                        $content .= str_repeat(',', $step)."\n";

                    }
                }

                $filename = "{$temp_dir}/{$file}.csv";

                // return $content;

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