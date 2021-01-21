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
        if(!isset($params->finalize) && !is_array($params->attendance)) {
            return ["code" => 203, "data" => "Sorry! The attendance parameter must be an array with the user id as the key."];
        }

        // confirm valid date
        if(!$this->validDate($params->date)) {
            return ["code" => 203, "data" => "Sorry! A valid date is required."];
        }

        // confirm if the user_type was parsed if the finalize parameter was not set
        if(!isset($params->finalize) && !isset($params->user_type)) {
            return ["code" => 203, "data" => "Sorry! Please the user_type is required."];
        }

        // unset the user id if the user type is not teacher
        if(isset($params->user_type) && ($params->user_type !== "student")) {
            // set the class id to null
            $params->class_id = null;
        }

        // validate the class id if parsed
        if(isset($params->class_id) && !empty($params->class_id)) {

            // run the query for the class details
            $classData = $this->pushQuery("id, name", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($classData)) {
                return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
            }
        }

        // init
        $the_query = "";

        // append additional query
        if(isset($params->user_type)) {
            $the_query .= " AND user_type='{$params->user_type}'";
        }

        // append to the query string
        if(isset($params->finalize)) {
            $the_query .= " AND id='{$params->finalize}'";
        }

        // init
        $user_data = [];
        $present_list = [];

        // if the attendance parameter was parsed
        if(isset($params->attendance)) {

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

        }

        // confirm existing record
        $check = $this->pushQuery("users_list, users_data, finalize", 
            "users_attendance_log", 
            "log_date='{$params->date}' AND client_id = '{$params->clientId}' {$the_query} ".(isset($params->class_id) ? " AND class_id='{$params->class_id}'" : "")." LIMIT 1"
        );

        // Return error message if finalize was parsed and yet no results was found
        if(isset($params->finalize) && empty($check)) {
            return ["code" => 203, "data" => "Sorry! An invalid record id was supplied."];
        }

        // insert the record into the database
        if(empty($check)) {

            // prepare and execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO users_attendance_log SET user_type = ?, users_list = ?, users_data = ?, log_date = ?,
                created_by = ?, academic_year = ?, academic_term = ?, client_id = ?
                ".(isset($params->class_id) ? ", class_id='{$params->class_id}'" : "")."
            ");
            $stmt->execute([
                $params->user_type, json_encode($present_list), json_encode($user_data), $params->date, 
                $params->userId, $params->academic_year, $params->academic_term, $params->clientId
            ]);

            // set the success message
            $data = "Attendance was sucessfully logged for {$params->date}.";

            //log the user activity
            if(isset($classData)) {
                // update the for the class
                $this->userLogs("attendance_log", $this->lastRowId("users_attendance_log"), null, "{$params->userData->name} logged attendance for <strong>{$classData[0]->name}</strong> on {$params->date}.", $params->userId);
            } else {
                // update the for user_type
                $this->userLogs("attendance_log", $this->lastRowId("users_attendance_log"), null, "{$params->userData->name} logged attendance for <strong>{$params->user_type}</strong> on {$params->date}.", $params->userId);
            }
        } else {

            // confirm that the user has not finalize the attendance log
            if($check[0]->finalize === 1) {
                return ["code" => 203, "data" => "Sorry! The attendance log for the specified date has already been finalized and cannot be updated."];
            }

            // prepare and execute the statement
            $stmt = $this->db->prepare("
                UPDATE users_attendance_log SET users_list = ?, users_data = ?
                WHERE user_type = ? AND log_date = ? AND client_id = ? ".(isset($params->class_id) ? " AND class_id='{$params->class_id}'" : "")."
            ");
            $stmt->execute([
                json_encode($present_list), json_encode($user_data), $params->user_type, $params->date, $params->clientId
            ]);

            // set the success message
            $data = "Attendance log for {$params->date} was successfully updated.";

            // if the query was parsed
            if(isset($params->finalize)) {

                // execute the statement
                $this->db->query("UPDATE users_attendance_log SET date_finalized = now(), finalize = '1' WHERE id='{$params->finalize}' LIMIT 1");

                // set a new message
                $data = "Attendance log for {$params->date} was successfully finalized.";
            }

            //log the user activity
            if(isset($classData)) {
                // if the class data was parsed and set
                $this->userLogs("attendance_log", $params->finalize, $check[0], "{$params->userData->name} updated logged attendance for <strong>{$classData[0]->name}</strong> on {$params->date}.", $params->userId);
            } else {
                // update the for user type
                $this->userLogs("attendance_log", $params->finalize, $check[0], "{$params->userData->name} updated logged attendance for <strong>{$params->user_type}</strong> on {$params->date}.", $params->userId);
            }
        }

        return ["data" => $data];

    }

    /**
     * Attendance Radio Buttons
     * 
     * Check if the state parsed matches the key, if so then auto check that radio button
     * 
     * @param String    $userId
     * @param String    $user_state
     * 
     * @return String
     */
    public function attendance_radios($userId = null, $user_state = null, $final = false) {
        
        $html = "";
        $labels = ["success" => "Present", "danger" => "Absent", "primary" => "Holiday", "warning" => "Late"];
        $disabled = $final ? "disabled" : "data-user_id='{$userId}' name='attendance_status[{$userId}][]'";

        foreach($labels as $color => $label) {
            $the_key = strtolower($label);
            $html .= "
            <span class='mr-2'>
                <input {$disabled} type='radio' ".($user_state == $the_key ? "checked" : "")." class='cursor' value='{$the_key}' id='{$userId}_{$the_key}'>
                <label class='cursor' for='{$userId}_{$the_key}'>".($user_state == $the_key ? "<strong class='text-{$color}'>{$label}</strong>" : "{$label}")."</label>
            </span>
            ";
        }

        return $html;
    }

    /**
     * Get the User State
     * 
     * Loop through the attendance log and get the value for the user
     * 
     * @param String    $userId
     * @param Array     $attendance_log
     */
    public function the_user_state($userId, $attendance_log) {
        
        // init the state
        $state = "";

        // loop through the list
        foreach($attendance_log as $key => $student) {
            if($student->item_id == $userId) {
                $state = $attendance_log[$key]->state;
                break;
            }
        }
        // return the state
        return $state;
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

        // if no date was parsed
        if(!isset($list_days[0])) {
            return [
                "data" => [
                    "table_content" => "<div class='mt-3 text-danger text-center font-italic'>Sorry! The date must not be a Sunday.</div>"
                ]
            ];
        }

        // confirm that the days range is a maximum of 14 days
        if(count($list_days) > 22) {
            return [
                "data" => "Sorry! The maximum days range but be at most 22."
            ];
        }

        // return error message if the start date is greater than today
        if(strtotime($start_date) > strtotime(date("Y-m-d"))) {
            return [
                "data" => [
                    "table_content" => "<div class='mt-3 text-danger text-center font-italic'>Sorry! The date must not be greater than current date.</div>"
                ]
            ];
        }  

        // append some few query
        $attendance = [];
        
        
        $user_type = isset($params->user_type) ? $params->user_type : null;
        
        // unset the user id if the user type is not teacher
        if($user_type !== "student") {
            // set the class id to null
            $params->class_id = null;
        }
        
        $class_id = isset($params->class_id) && ($params->class_id !== "null") ? $params->class_id : null;

        $query = !empty($params->user_type) ? " AND user_type='{$params->user_type}'" : null;
        $query .= !empty($class_id) ? " AND class_id='{$params->class_id}'" : null;
        
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

        // init
        $summary = [];

        // confirm existing record
        $check = $this->pushQuery("id, users_list, users_data, finalize, date_finalized", "users_attendance_log", "log_date='{$list_days[0]}' {$query} LIMIT 1");
        $attendance_log = !empty($check) ? json_decode($check[0]->users_data) : [];
        $final = !empty($check) ? $check[0]->finalize : null;

        // set the table content
        $table_content = (!$final && !empty($attendance["attendance"][0]["record"]["users_list"]) ? "
        <div class='row'>
            <div class='col-lg-12 text-right mb-2 attendance_control_buttons'>
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
        </div>" : "")."
        <table class='table table-bordered mt-2' id='attendance_logger'>
        <thead>
            <th width='5%'>&#8470;</th>
            <th width='35%'>Name</th>
            <th width='15%'>Unique ID</th>
            <th><span class='float-left'>Status</span></th>
        </thead>
        <tbody>";

        // if attendance was parsed and an array
        if(isset($attendance["attendance"]) && is_array($attendance["attendance"])) {
            
            // summation of the summary
            foreach($attendance["attendance"] as $ikey => $each) {
                foreach($each["record"]["users_data"] as $key => $value) {
                    $summary[$value["state"]] = isset($summary[$value["state"]]) ? $summary[$value["state"]] + 1 : 1;
                }
            }
        
            // loop through the attendance value
            foreach ($attendance["attendance"] as $key => $item) {
                $numb = 0;
                // loop through the users list
                foreach ($item["record"]["users_list"] as $user){
                    $numb++;
                    // get the user state
                    $user_state = $this->the_user_state($user->user_id, $attendance_log);

                    // append to the list
                    $table_content .= "
                    <tr>
                        <td>
                            {$numb}
                        </td>
                        <td>
                            <img src=\"{$user->image}\" width=\"28\" class=\"rounded-circle author-box-picture\" alt=\"User Image\"> {$user->name}
                        </td>
                        <td>{$user->unique_id}</td>
                        <td>".$this->attendance_radios($user->user_id, $user_state, $final, "")."</td>
                    </tr>";
                }
            }
        }

        // append to this list if students were found for this class
        if(!empty($attendance["attendance"][0]["record"]["users_list"])) {

            // show this section if the finalize is empty
            if(!$final) {
                // append the buttons to the table
                $table_content .= "
                <tr class='attendance_control_buttons'>
                    <td align='right' colspan='4'>
                        <button onclick='return save_AttendanceLog(\"{$list_days[0]}\",\"{$user_type}\",\"{$class_id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-save'></i> Save Attendance</button>
                        ".(!empty($attendance_log) && !$check[0]->finalize ? 
                            "<button onclick='return finalize_AttendanceLog(\"{$list_days[0]}\",\"{$user_type}\",\"{$class_id}\", \"{$check[0]->id}\")' class='btn btn-sm btn-outline-primary'><i class='fa'></i> Finalize</button>" : 
                            ""
                        )."
                    </td>
                </tr>";
            } else {
                $table_content .= "
                <tr>
                    <td align='center' colspan='2'>
                        <div class='text-left'>
                            <p class='p-0 pt-2 m-0'><label class='p-0 m-0 font-weight-bold'><i class='fa fa-chart-bar'></i> Summary:</label></p>";
                            foreach($summary as $key => $value) {
                                $table_content .= "<div class='p-0 m-0'><strong class='mr-3'>".ucwords($key).":</strong> {$value}</div>";
                            }
                $table_content .= "</div>
                    </td>
                    <td colspan='2' valign='top'>
                        <div class='text-right'>
                            <span class='p-0 m-0'><label class='p-0 m-0 font-weight-bold'>Date Finalized:</label></span>
                            <p class='p-0 m-0'><i class='fa fa-calendar-check'></i> {$check[0]->date_finalized}</p>
                        </div>
                    </td>
                </tr>";    
            }

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
    
    /**
     * Summary Insight
     * 
     * This gives a brief insight into attendance log for current and previous day
     * 
     * @return Array
     */
    public function summary(stdClass $params) {

        // get the attendance log for the day
        $days = [
            "today" => $this->get_date("today"),
            "yesterday" => $this->get_date("yesterday")
        ];
        $users = ["student", "teacher", "admin", "employee", "accountant"];
        
        // users counter
        $users_count = [];

        // attendance log algo
        // loop through the days for the record
        foreach($days as $key => $day) {

            // loop through the users for each day
            foreach($users as $user) {

                // set a parameter for the user_type
                $user_type = ($user == "admin") ? "('admin','accountant','employee')" : "('{$user}')";
                
                // run a query for the information
                $theQuery = $this->pushQuery("users_list", "users_attendance_log", "log_date='{$day}' AND user_type IN {$user_type} AND client_id='{$params->clientId}'");
                
                // if the query is not empty
                if(!empty($theQuery)) {
                    // convert the users list into an array
                    $present = json_decode($theQuery[0]->users_list, true);
                    $users_count["summary"][$user] = isset($users_count["summary"][$user]) ? ($users_count["summary"][$user] + count($present)) : count($present);
                    $users_count[$key][$user] = isset($users_count[$key][$user]) ? ($users_count[$key][$user] + count($present)) : count($present);
                }
            }
        }
        $users_count = (object) $users_count;
        $today_summary = isset($users_count->today) ? array_sum($users_count->today) : 0;
        
        $result = [
            "users_count" => $users_count,
            "today_summary" => $today_summary
        ];

        return $result;

    }
    
    /**
     * Summary Insight
     * 
     * This gives a brief insight into attendance log for range of dates
     * 
     * @return Array
     */
    public function range_summary(stdClass $params) {

        // get the attendance log for the day
        $days = $this->listDays($params->start_date, $params->end_date, 'Y-m-d', );

        // group the user types
        $users = $params->user_types_list;

        // users counter
        $users_count = [];
        $query = isset($params->is_finalized) ? " AND finalize='1'" : null;
        $checkPresent = (bool) isset($params->is_present_check);

        // attendance log algo
        $logged_count = 0;

        // loop through the days for the record
        foreach($days as $day) {
            
            // loop through the users for each day
            foreach($users as $user) {

                // run a query for the information
                $theQuery = $this->pushQuery("user_type, users_list", "users_attendance_log", "log_date='{$day}' AND user_type IN ('{$user}') AND client_id='{$params->clientId}' {$query}");

                // if the query is not empty
                if(!empty($theQuery)) {

                    // increment the logged count
                    $logged_count++;
                    
                    // loop through the results set
                    foreach($theQuery as $today) {
                        
                        // convert the users list into an array
                        $present = json_decode($today->users_list, true);
                        
                        // set a new variable for the day
                        $the_day = date("D, jS M", strtotime($day));

                        // if the user is not an admin/accountant then verify if the user was present or absent
                        if($checkPresent) {

                            // confirm if present
                            $is_present = (bool) in_array($params->the_current_user_id, $present);

                            // set the label for the day
                            $the_state = $is_present ? "present" : "absent";
                            $users_count["days_list"][$the_day] = $the_state;

                        } else {
                           
                            // set a new label to be used
                            if(in_array($params->the_user_type, ["admin", "accountant"]) && ($today->user_type !== "student")) { 
                                $n_label = "all_employees";
                                $users_count["summary"][$n_label] = isset($users_count["summary"][$n_label]) ? ($users_count["summary"][$n_label] + count($present)) : count($present);
                                $users_count["days_list"][$the_day][$n_label] = isset($users_count["days_list"][$the_day][$n_label]) ? ($users_count["days_list"][$the_day][$n_label] + count($present)) : count($present);
                            }

                            // label to use
                            $the_label = $user.'_count';
                            // append to the summary
                            $users_count["summary"][$the_label] = isset($users_count["summary"][$the_label]) ? ($users_count["summary"][$the_label] + count($present)) : count($present);
                            $users_count["days_list"][$the_day][$the_label] = isset($users_count["days_list"][$the_day][$the_label]) ? ($users_count["days_list"][$the_day][$the_label] + count($present)) : count($present);
                        }

                    }
                    
                }

            }

        }

        // count the number of present and absent
        if($checkPresent) {
            // if the array is set
            if(isset($users_count["days_list"])) {
                // count the values
                $summary_set = [];
                // loop through the records list
                foreach($users_count["days_list"] as $value) {
                    // ucfirst
                    $key = ucfirst($value);
                    // append to the array
                    $summary_set[$key] = isset($summary_set[$key]) ? ($summary_set[$key]+1) : 1;
                }
                $users_count["summary"] = $summary_set;
            } else {
                $users_count["summary"] = ["present" => 0, "absent" => 0];
            }
            $users_count["summary"]["logs_count"] = $logged_count;
            $users_count["chart_summary"] = [
                "Start Date" => $params->start_date,
                "End Date" => $params->end_date,
                "Days Interval" => count($days) . " days interval"
            ];
        } else {
            // using the grouping format
            $new_group = [];
            foreach($users_count["days_list"] as $day) {
                foreach($day as $role => $count) {
                    $new_group[$role][] = $count;
                }
            }
            $fresh_group = [];
            foreach($new_group as $name => $data) {
                $fresh_group[] = [
                    "name" => $name,
                    "data" => array_values($data)
                ];
            }
            $users_count["chart_grouping"] = $fresh_group;
            $users_count["chart_summary"] = [
                "Start Date" => $params->start_date,
                "End Date" => $params->end_date,
                "Days Interval" => count($days) . " days interval",
                "Logs Count" => $logged_count,
            ];
        }

        $users_count = (object) $users_count;
        
        return $users_count;

    }

    /**
     * Get the Current and Previous Dates
     * 
     * This calculates the current and previous date
     * This will ensure it doesnt fall on a Saturday or a Sunday
     * 
     * @param   String  $request
     * 
     * @return String
     */
    public function get_date($request) {

		$fix = date('D');
		if ($fix === 'Sat'){
			$today = date('Y-m-d', strtotime("-1 days"));
		} elseif ($fix === 'Sun'){
			$today = date('Y-m-d', strtotime("-2 days"));
		} elseif (($fix !== 'Sat') && ($fix !== 'Sun')){
			$today = date('Y-m-d');
		}

		$fix = date('D', strtotime("-1 days"));
		if ($fix === 'Sat'){
			$yesterday = date('Y-m-d',strtotime("-2 days"));
		}elseif ($fix === 'Sun'){
			$yesterday = date('Y-m-d',strtotime("-3 days"));
		}elseif(($fix !== 'Sat') && ($fix !== 'Sun')){
			$yesterday = date('Y-m-d',strtotime("-1 days"));
		}

		if($request == "today")
			return $today;
		elseif($request == "yesterday")
			return $yesterday;
	}

    /**
     * Start and End Date for the Week
     * 
     * @return String
     */
	public function Start_End_Date_of_a_week($week, $year){
		$time = strtotime("1 January $year");
		$day = date('w', $time);
		$time += ((7*($week-1))+1-$day)*24*3600;
		$dates[0] = date('Y-m-d', $time);
		return $dates;
	}
    
    /**
     * Get the Date for the start of this week 
     * 
     * @return String
     */
	public function get_week($initial_info, $week_start) {
		
		$fix = date('D');
		if ($fix === 'Sat'){
			$thswk = date('Y-m-d', strtotime("$week_start "." -1 days"));
		} elseif ($fix === 'Sun'){
			$thswk = date('Y-m-d', strtotime("$week_start "." -2 days"));
		} elseif (($fix !== 'Sat') && ($fix !== 'Sun')){
			$thswk = date('Y-m-d', strtotime("$week_start "." +0 day"));
		}
		
		$wk = date("W", strtotime($thswk));
		$yr = date("Y", strtotime($thswk));
		$draw = $this->Start_End_Date_of_a_week($wk,$yr);
		$wstrt = $draw[0];
		$wend = date('Y-m-d', strtotime("+5 days", strtotime($draw[0])));

		$laswk = date("Y-m-d", strtotime("-5 days", strtotime($thswk)));
		$wk = date("W", strtotime($laswk));
		$yr = date("Y", strtotime($laswk));
		$draw = $this->Start_End_Date_of_a_week($wk,$yr);
		$laswstrt = $draw[0];
		$laswend = date('Y-m-d', strtotime("+5 days", strtotime($draw[0])));

		if($initial_info == "this_wkstart")
			return $wstrt;
		elseif($initial_info == "this_wkend")
			return $wend;
		elseif($initial_info == "last_wkstart")
			return $laswstrt;
		elseif($initial_info == "last_wkend")
			return $laswend;

	}

}
?>