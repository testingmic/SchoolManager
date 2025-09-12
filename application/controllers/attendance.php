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
            return ["code" => 400, "data" => "Sorry! The attendance parameter must be an array with the user id as the key."];
        }

        // loop through the parameters and confirm they are set
        foreach(['date', 'user_type'] as $key) {
            if(empty($params->{$key}) && empty($params->finalize)) {
                return ["code" => 400, "data" => "Sorry! The {$key} parameter is required."];
            }
        }

        // confirm valid date
        if(!empty($params->date) && !$this->validDate($params->date)) {
            return ["code" => 400, "data" => "Sorry! A valid date is required."];
        }

        // confirm if the user_type was parsed if the finalize parameter was not set
        if(empty($params->finalize) && empty($params->user_type)) {
            return ["code" => 400, "data" => "Sorry! Please the user_type is required."];
        }

        // unset the user id if the user type is not teacher
        if(empty($params->user_type) && ($params->user_type !== "student")) {
            // set the class id to null
            $params->class_id = null;
        }

        // validate the class id if parsed
        if(!empty($params->class_id)) {

            // run the query for the class details
            $classData = $this->pushQuery("id, name", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($classData)) {
                return ["code" => 400, "data" => "Sorry! An invalid class id was supplied."];
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

        // if the user type was parsed
        if(!empty($params->user_type)) {
            if(!in_array($params->user_type, ["student", "staff"])) {
                return ["code" => 400, "data" => "Sorry! An invalid user type was supplied. Please use the following: student, staff"];
            }

            // confirm if the class id is set for students
            if($params->user_type == "student" && empty($params->class_id)) {
                return ["code" => 400, "data" => "Sorry! The class_id parameter is required for students."];
            }
        }

        // set the class id to 0 if it is empty
        $params->class_id = empty($params->class_id) ? 0 : $params->class_id;

        // confirm existing record
        $check = $this->pushQuery("users_list, users_data, finalize, id, date_created, date_finalized", 
            "users_attendance_log", 
            "log_date='{$params->date}' AND client_id = '{$params->clientId}' {$the_query} ".(!empty($params->class_id) ? " AND class_id='{$params->class_id}'" : "")." LIMIT 1"
        );

        // Return error message if finalize was parsed and yet no results was found
        if(isset($params->finalize) && empty($check)) {
            return ["code" => 400, "data" => "Sorry! An invalid record id was supplied."];
        }

        // if the attendance parameter was parsed
        if(isset($params->attendance)) {
            
            // set a new user type
            $the_user_type = $params->user_type == "staff" ? ["teacher","employee","admin","accountant"] : [$params->user_type];

            if(!empty($check) && !empty($params->appendExisting)) {
                $check[0]->users_data = json_decode($check[0]->users_data, true);

                // loop through the users data
                foreach($check[0]->users_data as $key => $eachInfo) {
                    if(!isset($params->attendance[$key])) {
                        $params->attendance[$key] = $eachInfo;
                    }
                }
            }

            // loop through the array list
            foreach($params->attendance as $key => $eachInfo) {

                // set the value
                $value = $eachInfo["status"] ?? (
                    $eachInfo["state"] ?? ""
                );

                // append to the list of present users array
                if($value == "present") {
                    $present_list[] = $key;
                }
                
                // load the user data using the key
                $data = $this->pushQuery("item_id, unique_id, name, image, phone_number, user_type", "users", "item_id = '{$key}' AND user_type IN {$this->inList($the_user_type)} AND status='1' LIMIT 1");
                
                // end the query if the result is empty
                if(empty($data)) {
                    return ["code" => 400, "data" => "Sorry! The user with GUID {$key} does not fall within the specified user_type."];
                }

                // append the attendance status to the query
                $data[0]->state = $value;
                $data[0]->comments = $eachInfo["comments"] ?? null;

                // append to the array list
                $user_data[$key] = $data[0];
            }

        }

        // insert the record into the database
        if(empty($check)) {

            // prepare and execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO users_attendance_log SET user_type = ?, users_list = ?, users_data = ?, log_date = ?,
                created_by = ?, academic_year = ?, academic_term = ?, client_id = ?
                ".(!empty($params->class_id) ? ", class_id='{$params->class_id}'" : "")."
            ");
            $stmt->execute([
                $params->user_type, json_encode($present_list), json_encode($user_data), $params->date, 
                $params->userId, $params->academic_year, $params->academic_term, $params->clientId
            ]);

            $last_id = $this->lastRowId("users_attendance_log");

            // set the success message
            $data = "Attendance was sucessfully logged for {$params->date}.";

            //log the user activity
            if(!empty($params->userData)) {
                if(isset($classData)) {
                    // update the for the class
                    $this->userLogs("attendance_log", $last_id, null, "{$params->userData->name} logged attendance for <strong>{$classData[0]->name}</strong> on {$params->date}.", $params->userId);
                } else {
                    // update the for user_type
                    $this->userLogs("attendance_log", $last_id, null, "{$params->userData->name} logged attendance for <strong>{$params->user_type}</strong> on {$params->date}.", $params->userId);
                }
            }

            $date_created = date("Y-m-d h:i:s");
        } else {

            // confirm that the user has not finalize the attendance log
            if((int)$check[0]->finalize === 1 && empty($params->appendExisting)) {

                // decode the json data
                $check[0]->users_list = json_decode($check[0]->users_list, true);
                $check[0]->users_data = json_decode($check[0]->users_data, true);

                return [
                    "code" => 200, 
                    "data" => "Sorry! The attendance log for the specified date has already been finalized and cannot be updated.",
                    "record" => $check[0]
                ];
            }

            // set the last id
            $last_id = $check[0]->id;

            $date_created = $check[0]->date_created;

            // prepare and execute the statement
            $stmt = $this->db->prepare("
                UPDATE users_attendance_log SET users_list = ?, users_data = ?
                WHERE user_type = ? AND log_date = ? AND client_id = ? ".(!empty($params->class_id) ? " AND class_id='{$params->class_id}'" : "")."
            ");
            $stmt->execute([
                json_encode($present_list), json_encode($user_data), $params->user_type, $params->date, $params->clientId
            ]);

            // set the success message
            $data = "Attendance log for {$params->date} was successfully updated.";

            // info
            $info = "";

            // if the query was parsed
            if(isset($params->finalize)) {
                
                $info = "The record was finalized and cannot be changed again.";

                // execute the statement
                $this->db->query("UPDATE users_attendance_log SET date_finalized = now(), finalize = '1', finalized_by='{$params->userId}' WHERE id='{$params->finalize}' LIMIT 1");

                // set a new message
                $data = "Attendance log for {$params->date} was successfully finalized.";
            }

            //log the user activity
            if(!empty($params->userData)) {
                if(isset($classData)) {
                    // if the class data was parsed and set
                    $this->userLogs("attendance_log", $params->finalize ?? null, $check[0], "{$params->userData->name} updated logged attendance for <strong>{$classData[0]->name}</strong> on {$params->date}. {$info}", $params->userId);
                } else {
                    // update the for user type
                    $this->userLogs("attendance_log", $params->finalize ?? 1, $check[0], "{$params->userData->name} updated logged attendance for <strong>{$params->user_type}</strong> on {$params->date}. {$info}", $params->userId);
                }
            }
        }

        return [
            "data" => $data, 
            "record" => [
                "attendance_id" => $last_id,
                "users_data" => $user_data,
                "users_list" => $present_list,
                "date_created" => $date_created,
            ]
        ];

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
    public function attendance_radios($userId = null, $user_state = null, $final = false, $mobile = false) {
        
        $html = "";
        if($mobile) {
            $tag = "div";
            $class = "class='mb-1'";
            $labels = ["success" => "Present", "danger" => "Absent", "warning" => "Late"];
        } else {
            $tag = "span";
            $class = "class='mr-2'";
            $labels = ["success" => "Present", "danger" => "Absent", "primary" => "Holiday", "warning" => "Late"];
        }
        $disabled = $final ? "disabled" : "data-user_id='{$userId}' name='attendance_status[{$userId}][]'";

        foreach($labels as $color => $label) {
            $the_key = strtolower($label);
            $html .= "
            <{$tag} {$class}>
                <input {$disabled} type='radio' ".($user_state == $the_key ? "checked" : "")." class='cursor' value='{$the_key}' id='{$userId}_{$the_key}'>
                <label style='".($mobile ? "" : "display: table-cell")."' class='cursor' title='Click to Select {$label}' for='{$userId}_{$the_key}'>
                ".($user_state == $the_key ? "<strong class='text-{$color}'>{$label}</strong>" : "{$label}")."</label>
            </{$tag}>";
        }

        return $html;
    }

    /**
     * Get the User State
     * 
     * Loop through the attendance log and get the value for the user
     * 
     * @param String    $userId
     * @param mixed     $attendance_log
     */
    public function the_user_state($userId, $attendance_log) {
        
        // init the state
        $state = "";
        $comments = "";

        // loop through the list
        foreach($attendance_log as $key => $student) {
            if($student->item_id == $userId) {
                $state = $attendance_log->{$student->item_id}->state ?? "absent";
                $comments = $attendance_log->{$student->item_id}->comments ?? null;
                break;
            }
        }
        // return the state
        return [
            "state" => $state,
            "comments" => $comments
        ];
    }

    /**
     * Generate Attedance Report
     * 
     * @param Int $params->class_id
     * @param String $params->month_year
     * @param String $params->user_type  : This is the user category to load the information
     * 
     * @return String
     */
    public function report(stdClass $params) {

        // set additional parameters
        // $params->weekends = true;
        $params->no_list = true;
        $params->is_finalized = true;

        if(empty($params->user_type)) {
            return ["code" => 400, "data" => "Sorry! The user_type parameter is required."];
        }

        $the_user_type = $params->user_type;
        $isDownloadable = (bool) isset($params->download);

        // set the month and year
        $start_date = !empty($params->start_date) ? $params->start_date : date("Y-m-01");
        $params->end_date = !empty($params->end_date) ? $params->end_date : date("Y-m-t");

        // confirm if its a valid date
        if(!$this->validDate($start_date)) {
            return ["code" => 400, "data" => "Sorry! An invalid date was supplied"];
        }
    
        // set the date range
        $params->date_range = "{$start_date}:{$params->end_date}";

        // get the attendance array data
        $new_array_list = [];
        $array_list = $this->display_attendance($params);

        // begin the table content
        $information = "";
        $table_content = "<div class=\"table-responsive\">\n";

        // confirm if the days range list is an array
        $array_list["days_range_list"] = is_array($array_list["days_range_list"]) ? $array_list["days_range_list"] : [];
        
        // width for each column
        $width = number_format((85 / count($array_list["days_range_list"])), 2);

        // join the names list
        $names_array = [];

        $class_id = isset($params->class_id) && ($params->class_id !== "null") ? $params->class_id : null;
        $user_type = $the_user_type == "staff" ? "('teacher','employee','admin','accountant')" : "('{$the_user_type}')";

        $query = " AND (user_type IN {$user_type})";
        $query .= !empty($class_id) ? " AND class_id='{$params->class_id}'" : null;

        if(!empty($params->user_id)) {
            $query .= " AND item_id='{$params->user_id}'";
        }
        
        // get the list of students
        $names_array = $this->pushQuery("name, item_id, user_type", "users", "1 {$query} AND client_id='{$params->clientId}' AND status='1' AND user_status IN ({$this->default_allowed_status_users_list})");
        
        // if the document is downloadable
        if($isDownloadable) {

            // get the class info
            if(!empty($params->class_id)) {
                // get the class information
                $class_info = $this->pushQuery("name, class_size, class_code", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' LIMIT 1");

                // if the class info is empty
                if(empty($class_info)) {
                    return ["data" => ["table_content" => "Sorry! An invalid class id was parsed."]];
                }
                $class_info = $class_info[0];

                // more information
                $information = "
                    <span><strong>User Type:</strong> <span class='uppercase'>{$the_user_type}</span></span><br>
                    <span><strong>Class Name:</strong> {$class_info->name}</span><br>
                    <span><strong>Class Code:</strong> {$class_info->class_code}</span><br>
                    <span><strong>Class Size:</strong> ".(
                        !empty($class_info->class_size) ? $class_info->class_size : count($names_array)
                    )."</span><br>
                    <span><hr></span>
                    <span><strong>Period:</strong> ".date("jS M Y", strtotime($start_date))." to ".date("jS M Y", strtotime($params->end_date))."</span>
                ";
            } else {
                // more information
                $information = "
                    <span><strong>User Type:</strong> {$the_user_type}</span><br>
                    <span><hr></span>
                    <span><strong>Period:</strong> ".date("jS M Y", strtotime($start_date))." to ".date("jS M Y", strtotime($params->end_date))."</span>
                ";
            }

            // set the client data
            $this->iclient = $params->client_data;

            // get the client logo content
            if(!empty($this->iclient->client_logo)) {
                $type = pathinfo($this->iclient->client_logo, PATHINFO_EXTENSION);
                $logo_data = file_get_contents($this->iclient->client_logo);
                $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
            }

            // set the preferences
            $prefs = !is_object($params->client_data->client_preferences) ? json_decode($params->client_data->client_preferences) : $params->client_data->client_preferences;
            
            // set the header content
            $table_content = '<table width="100%" cellpadding="0px" style="margin: auto auto;" cellspacing="0px">'."\n";
            $table_content .= "<tr>\n
                    <td width=\"27%\">{$information}</td>
                    <td width=\"46%\" align=\"center\">
                        ".(!empty($this->iclient->client_logo) ? "<img width=\"70px\" src=\"{$client_logo}\"><br>" : "")."
                        <h2 style=\"color:#6777ef;font-family:helvetica;padding:0px;margin:0px;\">".strtoupper($this->iclient->client_name)."</h2>
                        <span style=\"padding:0px; font-weight:bold; margin:0px;\">{$this->iclient->client_address}</span><br>
                        <span style=\"padding:0px; font-weight:bold; margin:0px;\">{$this->iclient->client_contact} ".(!$this->iclient->client_secondary_contact ? " / {$this->iclient->client_secondary_contact}" : null)."</span>
                    </td>
                    <td width=\"27%\" valign=\"top\">
                        <strong style=\"\">Academic Year:</strong> {$prefs->academics->academic_year}<br>
                        <strong style=\"\">Academic Term:</strong> {$prefs->academics->academic_term}<br>
                        <strong style=\"\">Generated At:</strong> ".date("Y-m-d h:i:s A")."<br>
                    </td>\n
                </tr>
                ".($isDownloadable ?
                    "<tr>
                        <td colspan='3' align='center'>
                            <div style='color:#0d5e9f;margin-top:10px;font-size:18px;margin-bottom:10px'><strong>ATTENDANCE LOG</strong></div>
                        </td>
                    </tr>"
                : "")."
            </table>\n";
               
        }

        // populate the information
        $table_content .= "<table width=\"100%\" data-rows_count=\"20\" cellpadding=\"2px\" style=\"border: 1px solid #dee2e6;\" class=\"table table-sm table-striped table-bordered datatable\">\n";

        // rearrange the users list
        function rearrange_list($users_list) {
            if(!is_array($users_list)) {
                return [];
            }
            $users_array = [];
            foreach($users_list as $user) {
                $users_array[$user["item_id"]] = $user;
            }
            return $users_array;
        }
            
        // get the table head
        $table_content .= "<thead>\n";
        foreach($array_list as $key => $data) {
            // skip the days list range
            if($key !== "days_range_list") {
                $table_content .= "<tr> \n";
                $table_content .= "<th ".($isDownloadable ? "style='border: 1px solid #dee2e6;'" : "")." width=\"10%\" align=\"left\">".($isDownloadable ? "<strong>Name</strong>" : "Student Name")."</th>\n";
                foreach($data as $key => $value) {
                    $new_array_list[$value["record"]["date"]["raw"]] = rearrange_list($value["record"]["users_data"]);
                    $table_content .= "<th ".($isDownloadable ? "style='font-size:12px;border: 1px solid #dee2e6;'" : "")." width=\"{$width}%\" align=\"center\">
                            <strong>".strtoupper($value["record"]["date"]["day"])."</strong>
                        </th>\n";
                }
                $table_content .= "<th style='border: 1px solid #dee2e6;' width=\"5%\" align=\"center\"><strong>TOT.</strong></th>\n";
                $table_content .= "</tr>\n";
            }
        }
        $table_content .= "</thead>\n";

        // status variables
        $statuses = [
            "nothing" => [
                "icon" => "",
                "title" => "N"
            ],
            "present" => [
                "icon" => "<i class=\"far fa-check-circle text-success\"></i>",
                "title" => "P"
            ],
            "absent" => [
                "icon" => "<i class=\"far fa-times-circle text-danger\"></i>",
                "title" => "A"
            ],
            "holiday" => [
                "icon" => "<i class=\"fa fa-hospital-symbol text-info\"></i>",
                "title" => "H"
            ],
            "late" => [
                "icon" => "<i class=\"fa fa-clock text-warning\"></i>",
                "title" => "L"
            ],
        ];

        // set the table body
        $table_content .= "<tbody>";

        // color demarcation
        $colors = [
            "P" => [
                "color" => "#54ca68",
                "title" => "Present"
            ],
            "A" => [
                "color" => "#fc544b",
                "title" => "Absent"
            ],
            "L" => [
                "color" => "#ffa426",
                "title" => "Late"
            ],
            "H" => [
                "color" => "#3abaf4",
                "title" => "Holiday"
            ],
            "N" => [
                "color" => "#000",
                "title" => "None"
            ]
        ];

        // student items log count
        $student_log_count = [];

        // list the users
        foreach($names_array as $user) {
            $table_content .= "<tr>\n";
            $table_content .= "<td width=\"10%\" ".($isDownloadable ? "style='font-size:12px;border: 1px solid #dee2e6;'" : "").">{$user->name}</td>\n";

            // check the user status
            foreach($new_array_list as $key => $attendance) {
                if(empty($attendance)) {
                    $table_content .= "<td ".($isDownloadable ? "style='border: 1px solid #dee2e6;'" : "")." width=\"{$width}%\" align=\"center\"></td>\n";
                } else {
                    $status = $attendance[$user->item_id]["state"] ?? "nothing";
                    
                    $the_status = $isDownloadable ? "<span style='font-weight:bold;font-size:11px;color:{$colors[$statuses[$status]["title"]]["color"]}'>{$statuses[$status]["title"]}</span>" : $statuses[$status]["icon"];

                    // add the student marks
                    $student_log_count[$user->item_id][$status] = isset($student_log_count[$user->item_id][$status]) ? ($student_log_count[$user->item_id][$status] + 1) : 1;
                    
                    $table_content .= "<td ".($isDownloadable ? "style='border: 1px solid #dee2e6;'" : "")." width=\"{$width}%\" align=\"center\">{$the_status}</td>\n";
                }
            }

            // append the student marks
            $table_content .= "
            <td style='".(!$isDownloadable ? "font-size:14px;" : null)." color:#0d5e9f; border: 1px solid #dee2e6;' align=\"center\">
                <strong>".($student_log_count[$user->item_id]["present"] ?? null)."</strong>
            </td>\n";

            $table_content .= "</tr>\n";
        }

        $table_content .= "</tbody>\n";
        $table_content .= "</table>\n";

        // append the legend
        if($isDownloadable) {
            $table_content .= "<div style='margin-top:20px'><table cellpadding='5px' width='100%'>
            <tr>
                <td style='border: 1px solid #dee2e6;' colspan='".count($colors)."' align='center'><strong>LEGEND</strong></td>
            </tr>
            <tr>";
            foreach($colors as $key => $color) {
                $table_content .= "
                <td class='text-center'>
                    <span style='font-weight:bold;color:{$color["color"]}'>{$key}: {$color["title"]}</span>
                </td>";
            }
            $table_content .= "</tr></table></div>";
        }

        $table_content .= "</div>";

        // exit;
        return [
            "data" => [
                "array_list" => $array_list,
                "table_content" => empty($params->remote) ? $table_content : ""
            ]
        ];
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
        
        // additional parameter
        $appendUsersList = (bool) !isset($params->no_list);

        // global items
        global $accessObject, $usersClass;

        // date range mechanism
        $this_date = isset($params->date_range) ? $params->date_range : date("Y-m-d");
        $explode = explode(":", $this_date);
        $start_date = $explode[0];
        $end_date = $explode[1] ?? date("Y-m-d", strtotime("{$this_date} 0 day"));

        // get the list of days 
        $list_days = $this->listDays($start_date, $end_date, "Y-m-d", !isset($params->weekends));

        // if no date was parsed
        if(!isset($list_days[0])) {
            return [
                "data" => [
                    "table_content" => no_record_found("Weekend Excluded", "Sorry! The selected date is a weekend which has been excluded from recording attendance.", null, "Event", false, "fa-clock")
                ]
            ];
        }

        // confirm that the days range is a maximum of 14 days
        if(count($list_days) > 90) {
            return [
                "data" => "Sorry! The maximum days range must be at most 90."
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
        $bottom_data = "";
        $mobile_bottom = "";
        
        $user_type = isset($params->user_type) ? $params->user_type : null;

        $class_id = !empty($params->class_id) ? $params->class_id : 0;
        $the_user_type = $params->user_type == "staff" ? ["teacher","employee","admin","accountant", "staff"] : [$params->user_type];

        $query = !empty($params->user_type) ? " AND a.user_type = '{$params->user_type}'" : null;
        $query .= !empty($class_id) ? " AND a.class_id='{$params->class_id}'" : null;
        $query .= isset($params->is_finalized) ? " AND a.finalize='1'" : null;
        $params->user_status = ['Active'];
        
        // counter loop
        $counter = 0;

        // loop through the days range list
        foreach($list_days as $each_day) {

            // get the attendance log for the day
            $check = $this->pushQuery(
                "a.users_list, a.users_data, a.user_type, a.class_id", 
                "users_attendance_log a", "a.log_date='{$each_day}' {$query} LIMIT 1"
            );
            
            // append the user type is there is a record but the user_type was not initially appended
            if(!empty($check)) {
                // append the parameters
                $params->class_id = $check[0]->class_id;
                $params->user_type = $check[0]->user_type == "staff" ? ["teacher","employee","admin","accountant"] : $check[0]->user_type;
            } else {
                // set the user type to load
                $params->user_type = $the_user_type;
            }
            
            // load the students list
            if($appendUsersList) {

                // get the users list
                $users_list = $usersClass->quick_list($params)["data"];
            
                // get the total count
                $total_count = count($users_list);

                // get the array of of user ids from the list
                $users_ids = array_column($users_list, "user_id");
            }

            // if the record is not empty
            if(!empty($check)) {

                // convert into an array
                $the_list = !empty($check[0]->users_list) ? json_decode($check[0]->users_list, true) : [];
                $full_user_data = !empty($check[0]->users_data) ? json_decode($check[0]->users_data, true) : [];

                // get the students present count
                if($appendUsersList) {
                    $present = count(array_intersect($the_list, $users_ids));
                }

                $attendance["attendance"][$counter] = [
                    "record" => [
                        "date" => [
                            "raw" => "{$each_day}",
                            "clean" => date("jS M", strtotime($each_day)),
                            "day" => date("D d", strtotime($each_day))
                        ],
                        "users_data" => $full_user_data
                    ]
                ];
                
                // append the list of users
                if($appendUsersList) {
                    $attendance["attendance"][$counter]["record"]["counter"] = [
                        "present" => $present,
                        "absent" => $total_count - $present
                    ];
                    $attendance["attendance"][$counter]["record"]["users_list"] = $this->is_present($users_list, $the_list);
                }

            } else {

                $attendance["attendance"][$counter] = [
                    "record" => [
                        "date" => [
                            "raw" => "{$each_day}",
                            "clean" => date("jS M", strtotime($each_day)),
                            "day" => date("D d", strtotime($each_day))
                        ],
                        "users_data" => [],
                    ]
                ];

                // append the list of users
                if($appendUsersList) {
                    $attendance["attendance"][$counter]["record"]["counter"] = [
                        "present" => 0,
                        "absent" => $total_count
                    ];
                    $attendance["attendance"][$counter]["record"]["users_list"] = $users_list;
                }
            }

            $counter++;
        }

        // init
        $summary = [];

        // users list is set
        $isListed = (bool) isset($attendance["attendance"][0]["record"]["users_list"]);

        // if the users item is parsed
        if($isListed) {

            // confirm existing record
            $check = $this->pushQuery(
                "a.id, a.users_list, a.users_data, a.finalize, a.date_finalized, a.date_created, 
                    c.name AS created_by_name, c.image AS created_by_image, c.email AS created_by_email,
                    f.name AS finalized_by_name, f.image AS finalized_by_image, f.email AS finalized_by_email",
                "users_attendance_log a LEFT JOIN users c ON a.created_by = c.item_id LEFT JOIN users f ON a.finalized_by = f.item_id", 
                "a.log_date='{$list_days[0]}' AND a.status='1' {$query} LIMIT 1");

            $attendance_log = !empty($check) ? json_decode($check[0]->users_data) : [];
            $final = !empty($check) ? $check[0]->finalize : null;

            // set the table content
            $top_section = (!$final && !empty($attendance["attendance"][0]["record"]["users_list"]) ? "
            <div class='row mt-1'>
                <div class='col-lg-9 col-md-8' id='attendance_search_input'>
                    <label>Filter by Name or Registration ID</label>
                    <input type='search' autocomplete='Off' placeholder='Search by fullname' name='attendance_fullname' class='form-control'>
                </div>
                <div class='col-lg-3 col-md-4 attendance_control_buttons'>
                    <div class='form-group'>
                        <label class='font-bold'>Select for Everyone</label>
                        <select data-width='100%' class='form-control cursor selectpicker' id='select_for_all'>
                            <option value='null'>Not Selected</option>
                            <option value='present'>Present</option>
                            <option value='absent'>Absent</option>
                            <option value='holiday'>Holiday</option>
                            <option value='late'>Late</option>
                        </select>
                    </div>
                </div>
            </div>" : "");

            $mobile_version = $top_section;
            $mobile_version .= "<div id='attendance_logger'>";
            $mobile_version .= "<div class='grid gap-2 grid-cols-1 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-1 w-100 flex items-center'>";

            $table_content = $top_section;
            $table_content .= "<div class='table-responsive'>
            <table border='1' class='table table-bordered mt-0' style='width:100%'>
            <thead>
                <th width='5%'>&#8470;</th>
                <th width='25%'>Name</th>
                <th align='left'><span class='float-left'>Status</span></th>
                <th><div>Comments</div></th>
            </thead>
            </table>
            </div>
            <div class='table-responsive'>
            <table border='1' class='table table-bordered mt-2' style='width:100%' id='attendance_logger'>
            <tbody>";
        
        }

        $attendance["days_range_list"] = $list_days;
        
        // can finalize the attendance log
        $canFinalize = $accessObject->hasAccess("finalize", "attendance");

        // if attendance was parsed and an array
        if(isset($attendance["attendance"]) && is_array($attendance["attendance"])) {
            
            // summation of the summary
            foreach($attendance["attendance"] as $ikey => $each) {
                foreach($each["record"]["users_data"] as $key => $value) {
                    $summary[$value["state"]] = isset($summary[$value["state"]]) ? $summary[$value["state"]] + 1 : 1;
                }
            }

            // staff colors
            $color = [
                "admin" => "success",
                "employee" => "primary",
                "accountant" => "danger",
                "teacher" => "warning"
            ];
        
            // loop through the attendance value
            foreach ($attendance["attendance"] as $key => $item) {
                $numb = 0;

                // if the item is set
                if($isListed) {
                    // loop through the users list
                    foreach ($item["record"]["users_list"] as $user){
                        $numb++;
                        // get the user state
                        $_each_data = $this->the_user_state($user->user_id, $attendance_log);
                        $user_state = $_each_data["state"];
                        $user_comments = $_each_data["comments"];

                        $mobile_version .= "
                        <div class='w-100'>
                            <div class='border p-3 rounded-lg overflow-y-auto'>
                                <div ".($final ? "class='user_name' onclick='load(\"".($user->user_type== "student" ? "student" : "staff")."/{$user->user_id}/attendance\")'" : " class='text-primary mb-2 text-uppercase'").">
                                    {$user->name}
                                </div>
                                <div>
                                    ".$this->attendance_radios($user->user_id, $user_state, $final, true)."
                                </div>
                                <div>
                                    <input ".($final ? "readonly title='{$user_comments}'" : "data-user_id='{$user->user_id}' id='comments' autocomplete='Off'")." placeholder='Add remarks (optional)' value='{$user_comments}' class='form-control' type='text'>
                                </div>
                            </div>
                        </div>";

                        // append to the list
                        $table_content .= "
                        <tr data-row_search='name' data-attandance_fullname='{$user->name}' data-attendance_unique_id='{$user->unique_id}'>
                            <td width='5%'>{$numb}</td>
                            <td width='25%'>
                                <div class='d-flex justify-content-start'>
                                    <div class='hidden mr-2'>
                                        <img src=\"{$this->baseUrl}{$user->image}\" width=\"28\" class=\"rounded-circle author-box-picture\" alt=\"User Image\">
                                    </div>
                                    <div style='line-height:30px'>
                                        <span ".($final ? "class='user_name' onclick='load(\"".($user->user_type== "student" ? "student" : "staff")."/{$user->user_id}/attendance\")'" : "class='text-primary'").">{$user->name}</span>
                                    </div>
                                </div>
                                <div>
                                    <strong class='hidden'>{$user->unique_id}</strong>
                                    ".($user->user_type !== "student" ? 
                                        "<span class='text-uppercase font-11 p-1 badge badge-{$color[$user->user_type]}'>
                                            {$user->user_type}
                                        </span>" : null
                                    )."
                                </div>
                            </td>
                            <td width='30%'>".$this->attendance_radios($user->user_id, $user_state, $final, "")."</td>
                            <td><input ".($final ? "readonly title='{$user_comments}'" : "data-user_id='{$user->user_id}' id='comments' autocomplete='Off'")." placeholder='Add remarks (optional)' value='{$user_comments}' class='form-control' type='text'></td>
                        </tr>";
                    }
                }
            }
        }

        //  class='grid grid-cols-1 lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1 w-100 flex items-center'

        // if the users list is parsed
        if($isListed) {

            // append to this list if students were found for this class
            if(empty($attendance["attendance"][0]["record"]["users_list"])) {
                $table_content .= "
                <tr>
                    <td align='center' colspan='4'>
                        <div class='font-italic'>Sorry! No user was found under the selected category.</div>
                    </td>
                </tr>";

                $mobile_version .= "
                <div class='col-lg-12 alert alert-warning text-center'>
                    <div class='font-italic'>Sorry! No user was found under the selected category.</div>
                </div>";
            }

            $table_content .= "</tbody>";
            $table_content .= "</table>";
            $table_content .= "</div>";

            $mobile_version .= "</div>";
            $mobile_version .= "</div>";

            // append to this list if students were found for this class
            if(!empty($attendance["attendance"][0]["record"]["users_list"])) {
                $bottom_data .= "<div class='table-responsive'>";
                $bottom_data .= "<table border='1' width='100%' class='table table-bordered mt-2'>";
                $bottom_data .= "<tbody>";

                $mobile_bottom .= "<div class='table-responsive'>";

                // show this section if the finalize is empty
                if(!$final) {
                    // append the buttons to the table
                    $bottom_data .= "
                    <tr class='attendance_control_buttons'>
                        <td colspan='2'>".(
                            !empty($check) ? 
                                "
                                    <p class='mb-0 pb-0'>Created By: <strong>{$check[0]->created_by_name}</strong></p>
                                    <p class='mb-0 pb-0'>Email Address: <strong>{$check[0]->created_by_email}</strong></p>
                                    <p class='mb-0 pb-0'>Last Updated: <strong>{$check[0]->date_created}</strong></p>
                                " : 
                            "")."
                        </td>
                        <td align='right' colspan='2'>
                            <button onclick='return save_AttendanceLog(\"{$list_days[0]}\",\"{$user_type}\",\"{$class_id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-save'></i> Save Attendance</button>
                            ".(!empty($attendance_log) && $canFinalize && !$check[0]->finalize ? 
                                "<button onclick='return finalize_AttendanceLog(\"{$list_days[0]}\",\"{$user_type}\",\"{$class_id}\", \"{$check[0]->id}\")' class='btn btn-sm btn-outline-primary'><i class='fa'></i> Finalize</button>" : 
                                ""
                            )."
                        </td>
                    </tr>";

                    $mobile_bottom .= "
                    <div class='mt-2 border-top pt-3'>
                        <div class='grid gap-2 grid-cols-2 lg:grid-cols-2 md:grid-cols-2 sm:grid-cols-1 w-100 flex items-center'>
                            <div>
                                ".(!empty($check) ? 
                                    "
                                        <p class='mb-0 pb-0'><strong>{$check[0]->created_by_name}</strong></p>
                                        <p class='mb-0 pb-0'><strong>{$check[0]->created_by_email}</strong></p>
                                        <p class='mb-0 pb-0'><strong>{$check[0]->date_created}</strong></p>
                                    " : 
                                "")."
                            </div>
                            <div class='text-right'>
                                <button onclick='return save_AttendanceLog(\"{$list_days[0]}\",\"{$user_type}\",\"{$class_id}\")' class='btn btn-sm mb-1 btn-outline-success'>
                                    <i class='fa fa-save'></i> Save
                                </button>
                                ".(!empty($attendance_log) && $canFinalize && !$check[0]->finalize ? 
                                    "<button onclick='return finalize_AttendanceLog(\"{$list_days[0]}\",\"{$user_type}\",\"{$class_id}\", \"{$check[0]->id}\")' class='btn mb-1 btn-sm btn-outline-primary'>
                                        <i class='fa fa-check'></i> Finalize
                                    </button>" : 
                                    ""
                                )."
                            </div>
                        </div>
                    </div>";
                } else {
                    $finals = "<div class='text-right'>
                                <p class='mb-0 pb-0'>Finalized By: <strong>{$check[0]->finalized_by_name}</strong></p>
                                <p class='mb-0 pb-0'>Email Address: <strong>{$check[0]->finalized_by_email}</strong></p>
                            </div>
                            <div class='pt-2 text-right border-top mt-2'>
                                <span class='p-0 m-0'><label class='p-0 m-0 font-weight-bold'>Date Finalized:</label></span>
                                <p class='p-0 m-0'><i class='fa fa-calendar-check'></i> {$check[0]->date_finalized}</p>
                            </div>";
                    $new_bottom_data = "
                    <tr>
                        <td colspan='2'>
                            <div class='text-left'>
                                <p class='p-0 pt-2 m-0'><label class='p-0 m-0 font-weight-bold'><i class='fa fa-chart-bar'></i> Summary:</label></p>";
                                foreach($summary as $key => $value) {
                                    $new_bottom_data .= "<div class='p-0 m-0'><strong class='mr-3'>".ucwords($key).":</strong> {$value}</div>";
                                }
                            $new_bottom_data .= "
                            </div>
                            ".(!empty($check) ? "
                                <div class='pt-2 border-top mt-2'>
                                    <p class='mb-0 pb-0'>Created By: <strong>".(!empty($check[0]->created_by_name) ? $check[0]->created_by_name : "System")."</strong></p>
                                    <p class='mb-0 pb-0'>Email Address: <strong>".(!empty($check[0]->created_by_email) ? $check[0]->created_by_email : "System")."</strong></p>
                                    <p class='mb-0 pb-0'>Last Updated: <strong>{$check[0]->date_created}</strong></p>
                                </div>
                                " : 
                            "")."
                        </td>
                        <td colspan='2' valign='top'>
                            {$finals}
                        </td>
                    </tr>";

                    $bottom_data .= $new_bottom_data;
                    $mobile_bottom .= "<div class='mt-2'>";
                    $mobile_bottom .= $finals;
                    $mobile_bottom .= "</div>";
                }
                $bottom_data .= "</tbody>";
                $bottom_data .= "</table>";
                $bottom_data .= "</div>";    
                
                $mobile_bottom .= "</div>";
            }
            
            // append the users list to the results to display
            // $attendance["bottom_data"] = $bottom_data;
            // $attendance["table_content"] = $table_content;
            $attendance["bottom_data"] = $mobile_bottom;
            $attendance["table_content"] = $mobile_version;
            
        }

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
        $users = ["student", "teacher", "admin", "employee", "accountant", "staff"];
        
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
                $theQuery = $this->pushQuery("users_list", "users_attendance_log", "log_date='{$day}' AND status='1' AND user_type IN {$user_type} AND client_id='{$params->clientId}'");
                
                // if the query is not empty
                if(!empty($theQuery)) {
                    // convert the users list into an array
                    $present = !empty($theQuery[0]->users_list) ? json_decode($theQuery[0]->users_list, true) : [];
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

        // global variable
        global $defaultAcademics, $isWardParent, $isStudent, $defaultUser, $defaultClientData;

        // get the attendance log for the day
        $days = $this->listDays($params->start_date, $params->end_date, 'Y-m-d');
        $isThisTerm = (isset($params->period) && ($params->period == "this_term")); 

        // prompt error if the days is more than 90 days
        if(count($days) > 63 && !$isThisTerm) {
            return ["code" => 400, "data" => "Sorry! The period should not exceed 60 days"];
        }

        // group the user types
        $users = $params->user_types_list;

        // users counter
        $users_count = [];
        $query = isset($params->is_finalized) ? " AND finalize='1'" : null;
        $checkPresent = (bool) isset($params->is_present_check);

        // append the class information there
        if($isWardParent) {
            // append the student id to the query information
            $query .= $isStudent ? " AND class_id='{$defaultUser->class_id}'" : " AND class_id='{$this->session->student_class_row_id}'";
        }

        elseif(!empty($params->class_id)) {
            $query .= " AND class_id='{$params->class_id}'";
        }

        // attendance log algo
        $logged_count = 0;
        $activeDaysCount = 0;
        $classSummaryRequest = !empty($params->class_id) && !empty($params->is_summary);

        // set the students dataset
        $students_dataset = [];
        $schoolActiveDays = count(filterWeekendDates($days));

        $acceptedDays = $defaultClientData?->client_preferences?->opening_days ?? [];

        // loop through the days for the record
        foreach($days as $day) {

            if(!in_array(date("l", strtotime($day)), $acceptedDays)) continue;

            $activeDaysCount++;
            
            // loop through the users for each day
            foreach($users as $user) {

                // run a query for the information
                $theQuery = $this->pushQuery("user_type, users_list, users_data, date_created, date_finalized", 
                    "users_attendance_log", "log_date='{$day}' AND user_type IN ('{$user}') AND status='1' AND client_id='{$params->clientId}' {$query} LIMIT ".($isWardParent ? 1 : 20));

                // set a new variable for the day
                $the_day = date("jS M", strtotime($day));

                // label to use
                $the_label = ucfirst($user);

                // if the query is not empty
                if(!empty($theQuery)) {

                    if(!isset($users_count["summary"]["{$user}.Marked_Days"])) {
                        $users_count["summary"]["{$user}.Marked_Days"] = 0;
                    }

                    // append to the list
                    $users_count["summary"]["{$user}.Marked_Days"] += 1;

                    // increment the logged count
                    $logged_count++;
                    
                    // loop through the results set
                    foreach($theQuery as $today) {
                        
                        $getItem = false;

                        // convert the users list into an array
                        $present = !empty($today->users_list) ? json_decode($today->users_list, true) : [];
                        $_user_data = !empty($today->users_data) ? json_decode($today->users_data, true) : [];

                        if($classSummaryRequest) {

                            // loop through the users data
                            foreach($_user_data as $user_id => $user) {

                                // get the state
                                $theState = $user["state"] ?? "absent";

                                if(!isset($students_dataset['breakdown'][$user_id])) {
                                    $students_dataset['breakdown'][$user_id] = [
                                        'days' => [],
                                        'name' => $user["name"],
                                    ];
                                }

                                // append to the breakdown
                                $students_dataset['breakdown'][$user_id]['days'][$the_day] = $theState;

                                // append to the summary
                                if(!isset($students_dataset['summary'][$user_id])) {
                                    $students_dataset['summary'][$user_id] = [
                                        'expected' => $schoolActiveDays,
                                        'holiday' => 0,
                                        'present' => 0,
                                        'absent' => 0,
                                        'late' => 0,
                                    ];
                                }

                                // increment the state count
                                $students_dataset['summary'][$user_id][$theState] += 1;
                            }
                        }

                        // if the user is not an admin/accountant then verify if the user was present or absent
                        if($checkPresent) {
                            // confirm if present
                            $is_present = (bool) in_array($params->the_current_user_id, $present);

                            if(!$is_present) {
                                $getItem = $_user_data[$params->the_current_user_id]["state"] ?? false;
                            }

                            // set the label for the day
                            $the_state = $is_present ? "present" : (!empty($getItem) ? $getItem : "present");
                            
                            $users_count["days_list"][$the_day] = ucwords($the_state);
                            $users_count["days_comments"][$the_day] = $_user_data[$params->the_current_user_id]["comments"] ?? "";
                            $users_count["days_log_time"][$the_day] = empty($today->date_finalized) ? 0 : $today->date_finalized;

                        } else {
                            
                            // append to the summary
                            $users_count["summary"][$the_label] = isset($users_count["summary"][$the_label]) ? ($users_count["summary"][$the_label] + count($present)) : count($present);
                            $users_count["days_list"][$the_day][$the_label] = isset($users_count["days_list"][$the_day][$the_label]) ? ($users_count["days_list"][$the_day][$the_label] + count($present)) : count($present);
                            $users_count["days_comments"][$the_day] = $_user_data[$params->the_current_user_id]["comments"] ?? "";
                            $users_count["days_log_time"][$the_day] = !empty($present) ? count($present) : 0;
                        }

                    }
                    
                } elseif(strtotime($the_day) < time()) {
                    // if the is_present_check is empty
                    if(empty($params->is_present_check)) {
                        // append the absent log to it.
                        $users_count["days_list"][$the_day][$the_label] = 0;
                    } else {
                        $users_count["days_list"][$the_day] = "Not_Logged";
                    }
                    $users_count["days_comments"][$the_day] = "";
                    $users_count["days_log_time"][$the_day] = 0;
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

                // get days in the academic year and term
                $days = $this->listDays($defaultAcademics->term_starts, $defaultAcademics->term_ends);

                // set the summary information
                $summary_set["Term"] = count($days);
                $summary_set["Term_Period"] = $logged_count + ($summary_set["Not Logged"] ?? 0);
                $users_count["summary"] = $summary_set;
            } else {
                $users_count["summary"] = ["present" => 0, "absent" => 0];
            }

            $users_count["summary"]["logs_count"] = $logged_count;
            $users_count["summary"]["ActiveSchoolDays"] = $schoolActiveDays;
            $users_count["chart_summary"] = [
                "Start Date" => $params->start_date,
                "End Date" => $params->end_date,
                "Days Interval" => count($days) . " days interval"
            ];
        } else {
            // using the grouping format
            $new_group = [];
            $users_count["summary"]["ActiveSchoolDays"] = $schoolActiveDays;
            if(isset($users_count["days_list"])) {
                foreach($users_count["days_list"] as $day) {
                    foreach($day as $role => $count) {
                        $new_group[$role][] = $count;
                    }
                }
            }

            $fresh_group = [];
            foreach($new_group as $name => $data) {
                $fresh_group[] = [
                    "name" => $name,
                    "data" => array_values($data)
                ];
            }

            $exclusion = array_map(function($day) {
                if(!in_array(date("l", strtotime($day)), ['Saturday', 'Sunday'])) {
                    return true;
                }
            }, $days);
            
            $users_count["chart_grouping"] = $fresh_group;
            $users_count["chart_summary"] = [
                "Start Date" => $params->start_date,
                "End Date" => $params->end_date,
                "Days Interval" => count(array_filter($exclusion)) . " active days"
            ];
        }

        if(!empty($params->class_id)) {
            $users_count["students_dataset"] = $students_dataset;
        }

        $users_count = (object) $users_count;
        
        return $users_count;

    }

    /**
     * Get the Class Summary for the Current Date
     * 
     * Loop through all classes and get the number of students present 
     * and absent for the specified date
     * 
     * @return Array
     */
    public function class_summary(stdClass $data) {

        global $defaultClientData;

        /** If finalized */
        $query = isset($data->is_finalized) ? " AND finalize='1'" : null;
        $list_days = $this->daysExclusionList($data->start_date, $data->end_date, $defaultClientData);

        $summary = [];
        $summation = [];

        $daysExpected = 0;

        // if the class id is set then filter the query
        $classFilter = !empty($data->class_id) ? " AND a.id = '{$data->class_id}'" : null;

        foreach($list_days as $currentDay) {
            
            if(strtotime($currentDay) > strtotime(date('Y-m-d'))) continue;

            /** Run a query for all classes, append the total logged count as well */
            $classes_list = $this->pushQuery(
                "a.id, a.name, 
                    (
                        SELECT COUNT(*) FROM users b 
                        WHERE 
                            b.user_status = 'Active' AND b.deleted='0' AND 
                            b.user_type='student' AND b.class_id = a.id AND 
                            b.client_id = a.client_id
                    ) AS class_size,
                    (
                        SELECT b.users_list FROM users_attendance_log b
                        WHERE 
                            b.log_date = '{$currentDay}' AND 
                            b.class_id = a.id AND b.user_type = 'student' AND
                            status = '1' {$query}
                        LIMIT 1
                    ) AS users_list, 
                    (
                        SELECT b.users_data FROM users_attendance_log b
                        WHERE 
                            b.log_date = '{$currentDay}' AND 
                            b.class_id = a.id AND b.user_type = 'student' AND
                            status = '1' {$query}
                        LIMIT 1
                    ) AS users_data
                ", 
                "classes a", 
                "a.status='1' AND a.client_id='{$data->clientId}' {$classFilter}"
            );

            if(!empty($classes_list)) {
                $daysExpected++;
            }

            /** Loop through the results list */
            foreach($classes_list as $result) {
                // convert the columns into an array
                $users_list = !empty($result->users_list) ? json_decode($result->users_list, true) : [];
                $users_data = !empty($result->users_data) ? json_decode($result->users_data, true) : [];

                // get the students who were present
                $result->present = !empty($users_list) ? count($users_list) : 0;
                $result->absent = !empty($users_data) ? (count($users_data) - $result->present) : 0;

                // append to the array
                $summary[$result->name][$currentDay] = [
                    'Id' => $result->id,
                    "Present" => $result->present,
                    "Absent" => $result->absent,
                    "Class Size" => (int) $result->class_size,
                ];

                if(!isset($summation[$result->name])) {
                    $summation[$result->name] = ['Size' => (int) $result->class_size, 'Absent' => 0, 'Present' => 0];
                }

                // summation of the dataset
                $summation[$result->name] = [
                    'Id' => $result->id,
                    'Size' => (int) $result->class_size, 
                    'Absent' => $summation[$result->name]['Absent'] + $result->absent, 
                    'Present' => $summation[$result->name]['Present'] + $result->present
                ];
            }

        }

        if($daysExpected) {
            $overallExpected = 0;
            $totalPresent = 0;
            $totalAbsent = 0;
            foreach($summation as $i => $class) {
                if($class['Size'] == 0) continue;
                $presentExpected = $daysExpected * $class['Size'];
                $overallExpected += $presentExpected;
                
                $totalAbsent += $class['Absent'];
                $totalPresent += $class['Present'];

                $summation[$i]['totalDays'] = $daysExpected;
                $summation[$i]['presentExpected'] = $presentExpected;
                $summation[$i]['absentRate'] = $class['Absent'] > 0 ? round(($class['Absent'] / $presentExpected) * 100, 2) : 0;
                $summation[$i]['presentRate'] = $class['Present'] > 0 ? round(($class['Present'] / $presentExpected) * 100, 2) : 0;
            }

            // perform the calculation
            $fresh_group['totalAbsent'] = $totalAbsent;
            $fresh_group['totalPresent'] = $totalPresent;
            $fresh_group['overallExpected'] = $overallExpected;
            $fresh_group['attendanceRate'] = $totalPresent > 0 ? round(($totalPresent / $overallExpected) * 100, 2) : 0;
        }

        if(!empty($data->is_summary) && !empty($data->class_only)) {
            $fresh_group['table'] = render_attendance_table($summary);
        }

        return [
            "summary" => $summary,
            "summaries" => $fresh_group,
            "attendanceRate" => $summation,
        ];
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