<?php 

class Attendance extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
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
        $end_date = $explode[1] ?? date("Y-m-d", strtotime("{$this_date} +3 day"));

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
        

        $query = isset($params->user_type) ? " AND user_type='{$params->user_type}'" : null;
        $query .= isset($params->class_id) ? " AND class_id='{$params->class_id}'" : null;
        
        // loop through the days range list
        foreach($list_days as $each_day) {
            // get the attendance log for the day
            $check = $this->pushQuery("users_list", "users_attendance_log", "log_date='{$each_day}' {$query} LIMIT 1");

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
                $present = count(array_intersect($the_list, $users_ids));

                $attendance[] = [
                    "{$each_day}" => [
                        "counter" => [
                            "present" => $present,
                            "absent" => $total_count - $present
                        ],
                        "users_list" => $this->is_present($users_list, $the_list)
                    ]
                ];
            } else {
                $attendance[] = [
                    "{$each_day}" => [
                        "counter" => [
                            "present" => 0,
                            "absent" => $total_count
                        ],
                        "users_list" => $users_list
                    ]
                ];
            }
        }

        return $attendance;
    }

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