<?php 

class Timetable extends Myschoolgh {

    private $color_set;

    public function __construct(stdClass $params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data;
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term;
        $this->academic_year = $client_data->client_preferences->academics->academic_year;

        // set the colors to use for the loading of pages
        $this->color_set = [
            "#007bff", "#6610f2", "#6f42c1", "#e83e8c", "#dc3545", "#fd7e14", 
            "#ffc107", "#28a745", "#20c997", "#17a2b8", "#6c757d", "#343a40", 
            "#007bff", "#6c757d", "#28a745", "#17a2b8", "#ffc107", "#dc3545"
        ];
	}
    
    /**
     * List timetable records
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

        $params->query .= (isset($params->timetable_id) && !empty($params->timetable_id)) ? " AND a.item_id='{$params->timetable_id}'" : null;
        $params->query .= (isset($params->published) && !empty($params->published)) ? " AND a.published='{$params->published}'" : null;
        $params->query .= (isset($params->class_id) && !empty($params->class_id)) ? " AND a.class_id='{$params->class_id}'" : null;
        $params->query .= isset($params->clientId) && !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : "";
        $params->query .= isset($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : "";
        $params->query .= isset($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : "";
        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT name FROM classes WHERE classes.item_id = a.class_id LIMIT 1) AS class_name,
                    (SELECT name FROM departments WHERE departments.item_id = a.department_id LIMIT 1) AS department_name                    
                FROM timetables a
                WHERE {$params->query} AND a.status = ? ORDER BY a.name LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $fullDetails = (bool) isset($params->full_detail);
            $todayOnly = isset($params->today_only) ? $params->today_only : null;

            $data = [];
            $timetable_id = "";
            
            $filters = [
                "yesterday" => "AND a.day = '".date("w", strtotime("-1 day"))."'",
                "today" => "AND a.day = '".date("w")."'",
                "tomorrow" => "AND a.day = '".date("w", strtotime("+1 day"))."'"
            ];

            $query = $todayOnly ? ($filters[$todayOnly] ?? null) : null;
            
            // loop through the result set
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                
                // convert the disabled inputs into an array
                $result->disabled_inputs = !empty($result->disabled_inputs) ? json_decode($result->disabled_inputs, true) : [];
                
                // if the full details is parsed
                if(($fullDetails && isset($params->timetable_id)) || ($fullDetails && isset($params->class_id))) {

                    $allocate = $this->pushQuery("
                        a.day, a.slot, a.day_slot, a.room_id, a.class_id, a.course_id, a.date_created,
                        c.name AS class_name, b.name AS course_name, b.course_code AS course_code,
                        (SELECT name FROM classes_rooms WHERE classes_rooms.item_id = a.room_id LIMIT 1) AS room_name
                    ", "timetables_slots_allocation a 
                        LEFT JOIN courses b ON b.item_id = a.course_id
                        LEFT JOIN classes c ON c.item_id = a.class_id", 
                    "a.timetable_id = '{$result->item_id}' AND b.academic_year = '{$params->academic_year}' AND b.academic_term='{$params->academic_term}' AND a.status='1' {$query}");
                    
                    $allocations = [];
                    foreach($allocate as $alot) {
                        $allocations[$alot->day_slot][] = $alot;
                    }
                    $result->allocations = $allocations;

                    $result->client_details = $this->pushQuery("a.*", "clients_accounts a", "a.client_id = '{$result->client_id}' AND a.client_status='1' LIMIT 1")[0];
                }

                // set the last timetable id to a variable
                $timetable_id = $result->item_id;
                $data[$result->item_id] = $result;
            }

            return [
                "code" => 200,
                "last_id" => $timetable_id,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Save timetable record
     * 
     * This method saves a timetable record
     * 
     * @param String        $params->timetable_id
     * @param Int           $params->slots
     * @param Int           $params->days
     * @param Int           $params->duration
     * @param String        $params->start_time
     * @param String        $params->name
     * @param String        $params->class_id
     * 
     * @return Array
     */
    public function save(stdClass $params) {

        try {

            // confirm that the timetable_id is parsed
            if(isset($params->timetable_id)) {
                // assign
                $item_id = $params->timetable_id;
                // check if a record exist
                if(empty($this->pushQuery("item_id", "timetables", "item_id = '{$item_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
                    return ["code" => 203, "data" => "Sorry! An invalid timetable id was parsed"];
                }
                $isFound = true;
            } else {
                // create a new timetable_id
                $item_id = random_string("alnum", 16);
                $isFound = false;

                // if the class isset
                if(isset($params->class_id)) {
                    // confirm if a class already exist with the same id
                    if(!empty($this->pushQuery("item_id", "timetables", "class_id='{$params->class_id}' AND client_id = '{$params->clientId}' AND status='1'"))) {
                        return ["code" => 203, "data" => "Sorry! There is an existing record in the database for the specified Class ID."];
                    }
                }
            }

            // convert to array
            $disabled_inputs =  isset($params->disabled_inputs) ? $this->stringToArray($params->disabled_inputs) : [];

            // clean input
            if( !is_numeric($params->days) || !is_numeric($params->slots) || !is_numeric($params->duration)) {
                return ["code" => 203, "data" => "Sorry! The days, slots and duration must be a valid numeric integers."];
            }

            // schedules
            $schedules = $params->days * $params->slots;
            
            // set a boundary
            if($schedules > 180) {
                return ["code" => 203, "data" => "Sorry! The maximum number of Allocations must be 180  ie 9 rows & 20 columns."];
            }

            // update the record if found
            if($isFound) {
                $stmt = $this->db->prepare("
                    UPDATE timetables SET 
                        days = ?, slots = ?, duration = ?, start_time = ?, disabled_inputs = ?
                        ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                        ".(isset($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                    WHERE item_id = ? AND client_id = ? LIMIT 1
                ");
                $stmt->execute([$params->days, $params->slots, $params->duration, 
                    $params->start_time, json_encode($disabled_inputs), $item_id, $params->clientId
                ]);
            }
            // insert the record
            else {
                $stmt = $this->db->prepare("
                    INSERT INTO timetables SET 
                        days = ?, slots = ?, duration = ?, start_time = ?, 
                        disabled_inputs = ?, item_id = ?, client_id = ?
                        ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                        ".(isset($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                        ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                        ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ");
                $stmt->execute([$params->days, $params->slots, $params->duration, $params->start_time, 
                    json_encode($disabled_inputs), $item_id, $params->clientId]);
            }

            // set the timetable in session
            $this->session->set("last_TimetableId", $item_id);
            
            return [
                "code" => 200, 
                "data" => "Timetable record was successfully saved.",
                "additional" => [
                    "timetable_id" => $item_id,
                    "disabled_inputs" => $disabled_inputs
                ]
            ];
        
        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Save Timetable Id
     * 
     * Save the parsed timetable id in session after checking if it exists
     * 
     * @param String        $params->timetable_id
     * 
     * @return Array
     */
    public function set_timetable_id(stdClass $params) {
        
        // assign
        $item_id = $params->timetable_id;
        
        // check if a record exist
        if(empty($this->pushQuery("item_id", "timetables", "item_id = '{$item_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
            return ["code" => 203, "data" => "Sorry! An invalid timetable id was parsed"];
        }

        // set in session
        $this->session->set("last_TimetableId", $item_id);

        // return success
        return ["code" => 200, "data" => "Timetable ID successfully set as default."];

    }

    /**
     * Process Slots Allocation
     * 
     * First confirm if the slot has been allocated if so then replace
     * 
     * // TODO ::: Will later on fix the conflict issues
     * 
     * @param String        $params->data["timetable_id"]
     * @param Int           $params->data["course_id"]
     * @param Int           $params->data["query"]
     * @param Int           $params->data["slot"]
     * @param String        $params->data["class_id"]
     * 
     * @return Array
     */
    public function allocate(stdClass $params) {
        
        /** End Query if there was no allocation */
        if(!isset($params->data["query"])) {
            return ["code" => 203, "data" => "Sorry! Query parameter is required"];
        }

        // if the user wants to save changes
        if($params->data["query"] === "allocation") {
            
            // end query if the allocations array is not parsed
            if(!isset($params->data["allocations"]) || !isset($params->data["timetable_id"])) {
                return ["code" => 203, "data" => "Sorry! Allocation / Timetable ID parameters are required"];
            }

            // confirm that allocations is an array
            if(!is_array($params->data["allocations"])) {
                return ["code" => 203, "data" => "Sorry! Allocations must be an array"];
            }

            // validate the id
            $item_id = $params->data["timetable_id"];
            $allocations = $params->data["allocations"];

            try {
            
                // check if a record exist
                if(empty($this->pushQuery("item_id", "timetables", "item_id = '{$item_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
                    return ["code" => 203, "data" => "Sorry! An invalid timetable id was parsed"];
                }

                // init values
                $course_ids = [];

                // loop through the allocations and group them
                foreach($allocations as $slots) {
                    // assign some variables
                    $course_room = explode(":", $slots["value"]);
                    $course = $course_room[0];
                    $course_ids[$course] = isset($course_ids[$course]) ? $course_ids[$course]+1 : 1;

                    // confirm that a room has been assigned
                    if(!isset($course_room[1])) {
                        return ["code" => 203, "data" => "Sorry! There was no room assigned to the course."];
                    }
                }
                
                // load the meeting periods for each course
                $courses_list = $this->pushQuery("name, weekly_meeting, item_id", "courses", "item_id IN {$this->inList(array_keys($course_ids))} AND client_id='{$params->clientId}' LIMIT 200");
                
                // set the bugs list
                $bugs_list = null;
                $class_id = isset($params->data["class_id"]) ? $params->data["class_id"] : null;

                // rectify the error
                foreach($courses_list as $key => $single) {
                    // confirm that the course id is found
                    if(isset($course_ids[$single->item_id])) {
                        // if the weekly meeting is less than the allocated days
                        if($single->weekly_meeting < $course_ids[$single->item_id]) {
                            $bugs_list .= "<div>".($key+1).". {$single->name} has only <strong>{$single->weekly_meeting} meetings</strong> per week.</div>";
                        }
                    } else {
                        return ["code" => 203, "data" => "Sorry! An invalid Course ID was parsed."];
                    }
                }

                // if the bug is not empty then return
                if(!empty($bugs_list)) {
                    return ["code" => 203, "data" => $bugs_list];
                }

                // delete any slots inserted into the database for this class record
                $query = $this->db->prepare("DELETE FROM timetables_slots_allocation WHERE 
                    timetable_id = '{$item_id}' AND course_id IN {$this->inList(array_keys($course_ids))} AND client_id = '{$params->clientId}'
                    ".($class_id ? "AND class_id='{$class_id}'" : null)." LIMIT 200
                ");
                $query->execute();

                // prepare the insert query here
                $allot_slot = $this->db->prepare('INSERT INTO timetables_slots_allocation SET 
                    client_id = ?, timetable_id = ?, `day` = ?, slot = ?, day_slot = ?, room_id = ?, class_id = ?, course_id = ?, `status` = ?
                ');

                // continue processing
                // loop through the allocations again and group them
                foreach($allocations as $slots) {
                    // assign some variables
                    $slot = explode("_", $slots["slot"]);
                    $course_room = explode(":", $slots["value"]);
                    $course = $course_room[0];
                    $room = $course_room[1];

                    // insert the value into the database
                    $allot_slot->execute([$params->clientId, $item_id, $slot[0], $slot[1], $slots["slot"], $room, $class_id, $course, 1]);
                }

                // update the timetable information
                $this->db->query("UPDATE timetables SET last_updated=now() WHERE item_id ='{$item_id}' LIMIT 1");

                // return
                return "The timetable was successfully save!";

            } catch(PDOException $e) {
                print $e->getMessage();
                return $this->unexpected_error;
            }

        } else {

            // if slot is not parsed
            if(!isset($params->data["slot"])) {
                return ["code" => 203, "data" => "Sorry! Slot parameter is required"];
            }

            $slot = explode("_", $params->data["slot"]);

        }

    }

    /**
     * Load Class Timetable Record
     * 
     * @param String      $classId
     * @param String      $clientId
     * 
     * @return Array
     */
    public function class_timetable($classId, $clientId, $today_only = false, $height = null) {

        try {

            // parameters to use to load the information
            $param = (object) [
                "class_id" => $classId,
                "clientId" => $clientId,
                "full_detail" => true,
                "today_only" => $today_only,
                "limit" => 1
            ];
            $result = $this->list($param);

            // if the result is not empty
            if(!empty($result["last_id"])) {

                // assign a variable to the timetable_id
                $timetable_id = $result["last_id"];
                
                // get the data
                $data = $result["data"][$timetable_id];

                // set new param
                $param = (object) [
                    "data" => $data,
                    "height" => $height,
                    "no_header" => true,
                    "today_only" => $today_only,
                    "timetable_id" => $timetable_id,
                    "table_class" => "table-bordered table-hover"
                ];
                $result = $this->draw($param);

                // confirm that the table was found
                if(isset($result["table"])) {
                    // init
                    $table = "";
                    
                    // if not only today
                    if(!$today_only) {
                        // append the results set
                        $table = "
                            <div class='text-center mb-3'>
                                <a class='btn btn-outline-success' target='_blank' href='{$this->baseUrl}download/timetable?tb_id={$timetable_id}&dw=true'>
                                    <i class='fa fa-download'></i> Download Timetable
                                </a>
                            </div>";
                    }
                    $table .= $result["table"];

                    // return the results
                    return $table;
                }
            } else {
                $result = "<div class='text-danger text-center'>No timetable has been generated for this class yet. Please check back later to verify.</div>";
            }

            return $result;

        } catch(PDOException $e) {
            return false;
        }

    }

    /**
     * Load The Teachers Timetable Record
     * 
     * @param String    $course_ids
     * @param String    $clientId
     * 
     * @return Array
     */
    public function teacher_timetable($course_ids, $clientId, $filter = "today") {
        
        // append some filters
        $filters = [
            "yesterday" => "AND ts.day = '".date("w", strtotime("-1 day"))."'",
            "today" => "AND ts.day = '".date("w")."'",
            "tomorrow" => "AND ts.day = '".date("w", strtotime("+1 day"))."'"
        ];
        $query = $filter ? ($filters[$filter] ?? null) : null;

        // get the client details
        $client_details = $this->pushQuery("a.*", "clients_accounts a", "a.client_id = '{$clientId}' AND a.client_status='1' LIMIT 1")[0];
        $client_details->client_preferences = json_decode($client_details->client_preferences);
        
        // init
        $data = [];

        if(!empty($course_ids)) {
            // run a query for the teacher courses taught
            $stmt = $this->db->prepare("SELECT ts.*, c.name AS course_name, r.name AS room_name,
                        cl.name AS class_name, t.disabled_inputs, t.name AS timetable_name, ts.course_id,
                        t.slots, t.days, t.duration, t.start_time, t.allow_conflicts, c.course_code
                    FROM timetables_slots_allocation ts 
                        LEFT JOIN courses c ON c.item_id = ts.course_id
                        LEFT JOIN classes cl ON cl.item_id = ts.class_id
                        LEFT JOIN timetables t ON t.item_id = ts.timetable_id
                        LEFT JOIN classes_rooms r ON r.item_id = ts.room_id
                    WHERE (c.id IN {$this->inList($course_ids)}) AND ts.status = ? AND 
                            ts.client_id = ? {$query} AND t.published = ? AND t.status = ?
                    AND t.academic_year = ? AND t.academic_term = ?    
            ");
            $stmt->execute([1, $clientId, 1, 1, $client_details->client_preferences->academics->academic_year, $client_details->client_preferences->academics->academic_term]);

            // loop through the result set
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                
                // convert the disabled inputs into an array
                $result->disabled_inputs = !empty($result->disabled_inputs) ? json_decode($result->disabled_inputs, true) : [];
                
                // main timetable information
                $timetable = (object) [
                    "days" => $result->days,
                    "name" => $result->timetable_name,
                    "slots" => $result->slots,
                    "duration" => $result->duration,
                    "disabled_inputs" => $result->disabled_inputs,
                    "start_time" => $result->start_time,
                    "allow_conflicts" => $result->allow_conflicts,
                ];

                // set the last timetable id to a variable
                $data[$result->day_slot][] = $result;
            }

        }
        
        // courses list
        $courses_list = [];
        $t_course_ids = [];

        // sort the array
        function sort_lesson_start_time($a, $b) {
            return strtotime($a["lesson_start_time"]) - strtotime($b["lesson_start_time"]);
        }

        // group all the items
        foreach($data as $each) {

            // loop through the array
            foreach($each as $key => $value) {
                // convert to array
                $value = (array) $value;
                $t_course_ids[] = $value["course_id"]; 

                // get the lesson time
                $start = $value["duration"] * $value["slot"];
                $end = ($value["duration"] * $value["slot"]) + $value["duration"];
                // configure the lesson start and end time
                $value["lesson_start_time"] = date("h:i A", strtotime("{$value["start_time"]} +{$start} minutes"));
                $value["lesson_end_time"] = date("h:i A", strtotime("{$value["start_time"]} +{$end} minutes"));
                // append to the list
                $courses_list[] = $value;
            }
        }

        $course_ids = array_unique($t_course_ids);

        // set
        $color_set = [];

        // color coding
        foreach($course_ids as $key => $each) {
            $color_set[$each] = $this->color_set[$key] ?? null;
        }
        
        // order the array set using the date of the event
        usort($courses_list, "sort_lesson_start_time");

        // init
        $lessons_list = "<div class='row'>";

        // loop through the lessons and generate a clean sheet for the teacher
        foreach($courses_list as $course) {
            $lessons_list .= "
            <div class='col-lg-3 col-md-6'>
                <div class='card' style='border-top: solid 4px {$color_set[$course["course_id"]]}'>
                    <div class='card-header pt-1 pb-1'>
                        <strong>{$course["class_name"]}</strong>
                    </div>
                    <div style='min-height:140px' class='card-body pb-0 pt-2 mb-0'>
                        <p>{$course["course_name"]} ({$course["course_code"]})</p>
                        <p class='pb-0 mb-0'><i class='fa fa-clock'></i> {$course["lesson_start_time"]}</p>
                        <p class='pb-0 mb-0'><i class='fa fa-clock'></i> {$course["lesson_end_time"]}</p>
                    </div>
                    <div class='card-footer p-2 border-top mt-0 text-right'>
                        <a href='{$this->baseUrl}course/{$course["course_id"]}' class='btn btn-outline-primary btn-sm'><i class='fa fa-eye'></i> View Course</a>
                    </div>
                </div>
            </div>";
        }
        if(empty($courses_list)) {
            $lessons_list .= "<div class='col-lg-12 text-center'>You do not have any lessons to teach today.</div>";
        }
        
        $lessons_list .= "</div>";

        return $lessons_list;
        
    }

    /**
     * Draw Clendar
     *
     * Use the provided information to draw the calendar
     * 
     * @return Array 
     */
    public function draw(stdClass $params) {

        // initial parameters
        $data = $params->data ?? [];
        $table_class = $params->table_class ?? "";
        $todayOnly = isset($params->today_only) && $params->today_only ? $params->today_only : null;
        $toDownload = isset($params->download) && $params->download ? $params->download : null;
        $codeOnly = (bool) (isset($params->code_only) && $params->code_only);
                
        // if load has been parsed
        if(isset($params->load) && (in_array($params->load, ["yesterday", "today", "tomorrow"]))) {
            $todayOnly = xss_clean($params->load);
        }

        // if the data is empty and the timetable_id isset
        if(empty($data) && isset($params->timetable_id)) {
            
            // load the timetable information
            $param = (object) [
                "limit" => 1,
                "full_detail" => true,
                "timetable_id" => $params->timetable_id,
            ];

            // confirm if the user wants only today_only
            if($todayOnly) {
                $param->today_only = $todayOnly;
            }

            $data = $this->list($param)["data"];

            // if no record was found
            if(empty($data)) {
                return $this->permission_denied;
            }

            // if no record was found
            if(!isset($data[$params->timetable_id])) {
                return $this->permission_denied;
            }

            // get the record and start processing
            $data = $data[$params->timetable_id];
        } elseif(!empty($data) && !isset($data->slots)) {
            return $this->permission_denied;
        }

        // if the user is currently logged in and the timetable id parsed is not the same as the current session value
        if(!empty($this->session->userId) && (isset($params->timetable_id) && $params->timetable_id !== $this->session->last_TimetableId)) {
            // update the user information
            $this->db->query("UPDATE users SET last_timetable_id = '{$params->timetable_id}' WHERE item_id = '{$this->session->userId}' LIMIT 10");
            // save the item in session
            $this->session->set("last_TimetableId", $params->timetable_id);
        }
        
        // column with calculation
        $summary = null;
        $slots = $data->slots;
        $width = round((100/($slots+1)), 2);

        // get the client logo content
        if(!empty($this->iclient->client_logo)) {
            $type = pathinfo($this->iclient->client_logo, PATHINFO_EXTENSION);
            $logo_data = file_get_contents($this->iclient->client_logo);
            $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
        }
        
        // preferences
        if(isset($data->client_details)) {

            // set the preferences
            $prefs = !is_object($data->client_details->client_preferences) ? json_decode($data->client_details->client_preferences) : $data->client_details->client_preferences;
            
            // if the header parameter is not set
            if(!isset($params->no_header)) {
                
                // set the header content
                $summary = '<table width="100%" class="'.$table_class.'" cellpadding="0px" style="margin: auto auto;" cellspacing="0px">'."\n";
                $summary .= "<tr>\n
                        <td width=\"27%\" style=\"padding:10px;\">
                            <strong style=\"font-size:15px;\">Class Name:</strong> {$data->class_name}<br>
                            <strong style=\"font-size:15px;\">Department:</strong> {$data->department_name}
                        </td>
                        <td width=\"46%\" align=\"center\">
                            ".(!empty($this->iclient->client_logo) ? "<img width=\"70px\" src=\"{$client_logo}\"><br>" : "")."
                            <h2 style=\"color:#6777ef;font-family:helvetica;padding:0px;margin:0px;\">".strtoupper($this->iclient->client_name)."</h2>
                            <div style=\"padding:0px;margin:0px;\">{$this->iclient->client_address}</div>
                            <div style=\"padding:0px;margin:0px;\">
                                {$this->iclient->client_contact} ".(!$this->iclient->client_secondary_contact ? " / {$this->iclient->client_secondary_contact}" : null)."
                                ".(!empty($this->iclient->client_email) ? " | {$this->iclient->client_email}" : "")."
                            </div>
                        </td>
                        <td width=\"27%\" style=\"padding:10px;\">
                            <strong style=\"font-size:15px;\">Academic Year:</strong> {$prefs->academics->academic_year}<br>
                            <strong style=\"font-size:15px;\">Academic Term:</strong> {$prefs->academics->academic_term}<br>
                            ".(isset($data->last_updated) ? "
                                <strong style=\"font-size:15px;\">Generated On:</strong> {$data->last_updated}<br>" : ""
                            )."
                        </td>\n
                    </tr>\n
                    </table>\n";
            }
        }

        // start drawing the table
        if($toDownload) {
            $html_table = "<style>table tr td, table tr td {border:1px solid #ccc;}</style>\n";
        } else {
            $html_table = "<style>#t_table tr td, #t_table tr td {border:1px dashed #ccc;}</style>\n";
        }
        $html_table .= $summary.'<table class="'.$table_class.'" id="t_table" width="100%" cellpadding="0px" style="margin: auto auto;" cellspacing="0px">'."\n";
        $html_table .= "<tr ".(isset($params->height) && $params->height ? "style='height:{$params->height}px'" : "").">\n\t<td width=\"{$width}%\"></td>\n";
        $start_time = $data->start_time;
        
        // generate the header
        for($i = 0; $i < $slots; $i++) {
            // set the start time
            $start_time = date("h:i A", strtotime($start_time));
            $end_time = $this->add_time($start_time, $data->duration);

            // show the time
            $html_table .= "\t<td width=\"{$width}%\" style=\"background-color:#607d8b;font-size:12px;color:#fff\">
                <div align=\"center\"><strong>{$start_time}</strong><br>-<br><strong>{$end_time}</strong></div>
            </td>\n";
            $start_time = $end_time;
        }
        $html_table .= "</tr>\n";

        // days of the week
        $d_style = "style=\"background-color:#795548;font-weight:bold;font-size:12px;text-align:center;color:#fff;".(isset($params->height) && $params->height ? "height:{$params->height}px;" : "")."\"";

        // set the array of days
        $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

        // some more features
        $filters = [
            "yesterday" => date("l", strtotime("-1 day")),
            "today" => date("l"),
            "tomorrow" => date("l", strtotime("+1 day"))
        ];

        // init
        $t_course_ids = [];        
        // loop through all the allocations
        if(isset($data->allocations)) {
            foreach($data->allocations as $allot) {
                foreach($allot as $all) {
                    $t_course_ids[] = $all->course_id;
                }
            }
        }
        $course_ids = array_unique($t_course_ids);

        // set
        $color_set = [];

        // color coding
        foreach($course_ids as $key => $each) {
            $color_set[$each] = $this->color_set[$key] ?? null;
        }

        // if the allocations is not empty
        if(!empty($data->allocations)) {
            
            // loop through each day
            for ($d = 0; $d < $data->days; $d++) {

                // if not today only 
                if(!$todayOnly || ($todayOnly && $days[$d] == $filters[$todayOnly])) {

                    // set the begining of the table
                    $row = "<tr>\n";

                    // set the day name of the week
                    $row .= "\t<td {$d_style}>".($days[$d] ?? null)."</td>\n";

                    // loop through the slots
                    for ($i = 0; $i < $slots; $i++) {
                        
                        // set the key
                        $course = "";
                        $bg_color = "style=\"padding:10px\"";
                        $key = ($d + 1)."_".($i + 1);

                        // get the data
                        $allocation = isset($data->allocations[$key]) ? $data->allocations[$key] : [];
                        
                        // loop through each allocation for the day
                        foreach($allocation as $akey => $cleaned) {
                            
                            // set the information to display
                            if(!empty($cleaned)) {
                                $bg_color = "style=\"padding:5px;font-size:13px;background-color:".($color_set[$cleaned->course_id] ?? "#000").";color:#fff\"";
                                $info = !$codeOnly ? $cleaned->course_name. " (" : null; 
                                $info .= "<strong>{$cleaned->course_code}</strong>";
                                $info .= !$codeOnly ? " )" : null; 
                                // $info .= ($akey !== (count($allocation) - 1)) ? "<hr>" : null;
                                $course .=  "<div {$bg_color}>{$info}</div>";
                            }
                            if(in_array($key, $data->disabled_inputs)) {
                                $bg_color = "style=\"padding:5px;background-color:#cccccc;color:#888888;\"";
                                $course .=  "<div {$bg_color}>DISABLED</div>";
                            }
                            
                        }

                        // append the information
                        $row .= "\t<td {$bg_color} align=\"center\">{$course}</td>\n";

                    }

                    $row .= "</tr>\n";
                    $html_table .= $row;
                
                }
            }
        } else {
            $slots = $slots+1;
            $html_table .= "<tr><td align=\"center\" colspan=\"{$slots}\">No timetable record for today was found in the database.</td></tr>";
        }

        $html_table .= "</table>";
        
        // print $html_table;
        // exit;
        
        return [
            "table" => $html_table,
            "result" => $data
        ];

    }

    /**
     * A function to add to the time
     * 
     * @return String
     * 
     */
    private function add_time($start_time, $interval) {
        $time = date("h:i A", strtotime("{$start_time} + {$interval} minutes"));
        return $time;
    }

}
?>