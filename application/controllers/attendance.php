<?php 

class Attendance extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Log Attendance
     * 
     * Loop through the attendance array list and log the attendance for the specified date
     * 
     * @return Array
     */
    public function log(stdClass $params) {

        // confirm a valid list of array for the attendance parameter
        if(!is_array($params->attendance)) {
            return ["code" => 203, "data" => "Sorry! The attendance parameter must be an array with the user id as the key."];
        }

        // confirm valid date
        if(!$this->validDate($params->date)) {
            return ["code" => 203, "data" => "Sorry! A valid date is required."];
        }

        // validate the class id
        $prevData = $this->pushQuery("*", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

        // if empty then return
        if(empty($prevData)) {
            return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
        }

        // init
        $user_data = [];
        $present_list = [];

        // loop through the array list 
        foreach($params->attendance as $key => $value) {
            // append to the list of present users array
            if($value == "present") {
                $present_list[] = $key;
            }
            // load the user data using the key
            $data = $this->pushQuery("item_id, unique_id, name, email, phone_number", "users", "item_id = '{$key}' AND user_type ='{$params->user_type}' LIMIT 1");
            
            // end the query if the result is empty
            if(empty($data)) {
                return ["code" => 203, "data" => "Sorry! The user with GUID {$key} does not fall within the specified user_type."];
            }

            // append the attendance status to the query
            $data[0]->state = $value;

            // append to the array list
            $user_data[] = $data[0];
        }

        // confirm existing record
        $check = $this->pushQuery("users_list, users_data", "users_attendance_log", "log_date='{$params->date}' AND user_type='{$params->user_type}' LIMIT 1");

        // insert the record into the database
        if(empty($check)) {
            // prepare and execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO users_attendance_log SET user_type = ?, users_list = ?, users_data = ?, log_date = ?,
                created_by = ?, academic_year = ?, academic_term = ?
                ".(isset($params->class_id) ? ", class_id='{$params->class_id}'" : "")."
            ");
            $stmt->execute([
                $params->user_type, json_encode($present_list), json_encode($user_data),
                $params->date, $params->userId, $params->academic_year, $params->academic_term
            ]);

            // set the success message
            $data = "Attendance was sucessfully logged for {$params->date}.";

            //log the user activity
        } else {
            // prepare and execute the statement
            $stmt = $this->db->prepare("
                UPDATE users_attendance_log SET users_list = ?, users_data = ?
                WHERE user_type = ? AND log_date = ? ".(isset($params->class_id) ? " AND class_id='{$params->class_id}'" : "")."
            ");
            $stmt->execute([
                json_encode($present_list), json_encode($user_data), $params->user_type, $params->date
            ]);

            // set the success message
            $data = "Attendance log for {$params->date} was successfully updated.";

            //log the user activity
        }

    }

    /**
     * Attendance Radio Buttons
     * 
     * @param String    $userId
     * 
     * @return String
     */
    public function attendance_radios($userId = null) {
        
        $html = "";
        $statuses = ["Present", "Absent", "Holiday", "Late"];

        foreach($statuses as $key => $status) {
            $the_key = strtolower($status);
            $html .= "
            <span class='mr-2'>
                <input type='radio' data-user_id='{$userId}' value='{$the_key}' name='attendance_status[{$userId}][]' id='{$userId}_{$the_key}'>
                <label class='cursor' for='{$userId}_{$the_key}'>{$status}</label>
            </span>
            ";
        }

        return $html;
    }

    /**
     * Display Attendance
     * 
     * List all the users in the category set by the user type.
     * Get the attendance log for the requested date range and confirm if the student was present for the class.
     * 
     * @param String $params->class_id
     * @param String $params->date_range
     * 
     * @return Array
     */
    public function display_attendance(stdClass $params) {
        
        // get the information
        $params->minified = "load_minimal_info";
        $params->append_waspresent = true;
        $params->no_permissions = true;

        // date range mechanism
        $this_date = isset($params->date_range) ? $params->date_range : date("Y-m-d");
        $explode = explode(":", $this_date);
        $start_date = $explode[0];
        $end_date = $explode[1] ?? date("Y-m-d", strtotime("{$this_date} 0 day"));

        // get the list of days 
        $list_days = $this->listDays($start_date, $end_date, "Y-m-d", true);

        // confirm that the days range is a maximum of 14 days
        if(count($list_days) > 22) {
            return [
                "data" => "Sorry! The maximum days range but be at most 22."
            ];
        }       

        // append some few query
        $attendance = [];
        
        $user_type = isset($params->user_type) ? $params->user_type : null;
        $class_id = isset($params->class_id) ? $params->class_id : null;

        $query = isset($params->user_type) ? " AND user_type='{$params->user_type}'" : null;
        $query .= isset($params->class_id) ? " AND class_id='{$params->class_id}'" : null;
        
        // loop through the days range list
        foreach($list_days as $each_day) {

            // get the attendance log for the day
            $check = $this->pushQuery("users_list, users_data, user_type, class_id", "users_attendance_log", "log_date='{$each_day}' {$query} LIMIT 1");
            
            // append the user type is there is a record but the user_type was not initially appended
            if(!empty($check)) {
                // append the parameters
                $params->class_id = $check[0]->class_id;
                $params->user_type = $check[0]->user_type;
            }
            
            // load the students list
            $users_list = load_class("users", "controllers")->list($params)["data"];
            
            // get the total count
            $total_count = count($users_list);

            // get the array of of user ids from the list
            $users_ids = array_column($users_list, "user_id");

            // if the record is not empty
            if(!empty($check)) {

                // convert into an array
                $the_list = json_decode($check[0]->users_list, true);
                $full_user_data = json_decode($check[0]->users_data, true);
                $present = count(array_intersect($the_list, $users_ids));

                $attendance["attendance"][] = [
                    "record" => [
                        "date" => [
                            "raw" => "{$each_day}",
                            "clean" => date("jS M", strtotime($each_day))
                        ],
                        "counter" => [
                            "present" => $present,
                            "absent" => $total_count - $present
                        ],
                        "users_data" => $full_user_data,
                        "users_list" => $this->is_present($users_list, $the_list)
                    ]
                ];

            } else {

                $attendance["attendance"][] = [
                    "record" => [
                        "date" => [
                            "raw" => "{$each_day}",
                            "clean" => date("jS M", strtotime($each_day))
                        ],
                        "counter" => [
                            "present" => 0,
                            "absent" => $total_count
                        ],
                        "users_data" => [],
                        "users_list" => $users_list
                    ]
                ];

            }
        }

        return $attendance;

        // set the table content
        $table_content = "
        <div class='row'>
            <div class='col-lg-12 text-right mb-2'>
                <span class='float-right'>
                    <label>Select for Everyone</label>
                    <select class='form-control selectpicker' id='select_for_all'>
                        <option value='null'>Not Selected</option>
                        <option value='present'>Present</option>
                        <option value='absent'>Absent</option>
                        <option value='holiday'>Holiday</option>
                        <option value='late'>Late</option>
                    </select>
                </span>
            </div>
        </div>
        <table class='table table-bordered mt-2' id='attendance_logger'>
        <thead>
            <th width='5%'>&#8470;</th>
            <th width='30%'>Name</th>
            <th width='12%'>Unique ID</th>
            <th><span class='float-left'>Status</span></th>
        </thead>
        <tbody>";
        foreach ($attendance["attendance"] as $key => $item) {
            $numb = 0;
            foreach ($item["record"]["users_list"] as $user){
                $numb++;
                $table_content .= "<tr>
                    <td>
                        {$numb}
                    </td>
                    <td>
                        <img src=\"{$user->image}\" width=\"28\" class=\"rounded-circle author-box-picture\" alt=\"User Image\"> {$user->name}
                    </td>
                    <td>{$user->unique_id}</td>
                    <td>".$this->attendance_radios($user->user_id, $item["record"]["users_data"])."</td>
                </tr>";
            }
        }
        // append to this list if students were found for this class
        if(!empty($attendance["attendance"][0]["record"]["users_list"])) {
            $table_content .= "
            <tr>
                <td align='right' colspan='4'>
                    <button onclick='return save_AttendanceLog(\"{$list_days[0]}\",\"{$user_type}\",\"{$class_id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-save'></i> Save Attendance</button>
                </td>        
            </tr>";
        } else {
            $table_content .= "
            <tr>
                <td align='center' colspan='4'>
                    <div class='font-italic'>Sorry! No students found under the selected class.</div>
                </td>        
            </tr>";
        }

        $table_content .= "<tbody>";
        $table_content .= "</table>";
        
        // append the users list to the results to display
        $attendance["table_content"] = $table_content;

        return $attendance;
    }
    
    /**
     * Confirm that the student is either present or absent
     * 
     * @param Object $all_users_list
     * @param Array $attendees
     * 
     * @return Array
     */
    public function is_present($all_users_list, $attendees) {
        // new list init
        $new_list = [];

        // loop through the users list
        foreach($all_users_list as $key => $each_user) {
            // confirm if the user_id is in the list of attendees
            if(in_array($each_user->user_id, $attendees)) {
                $each_user->is_present = true;
            }
            $new_list[] = $each_user;
        }

        return $new_list;
    }
    
}
?>