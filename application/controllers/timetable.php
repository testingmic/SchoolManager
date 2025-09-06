<?php 

class Timetable extends Myschoolgh {

    /**
     * Constructor
     * 
     * @param stdClass $params
     */
    public function __construct($params = null) {
		parent::__construct();

        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;

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

        $query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

        $query .= !empty($params->timetable_id) ? " AND a.item_id='{$params->timetable_id}'" : null;
        $query .= !empty($params->published) ? " AND a.published='{$params->published}'" : null;
        $query .= !empty($params->class_id) && is_array($params->class_id) ? " AND (b.id IN {$this->inList($params->class_id)})" : (!empty($params->class_id) ? " AND (a.class_id ='{$params->class_id}')" : null);
        $query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : "";
        $query .= !empty($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : null;
        $query .= !empty($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : null;
        $query .= !empty($params->q) ? " AND a.name='{$params->q}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*, b.name AS class_name,
                    (SELECT name FROM departments WHERE departments.item_id = a.department_id LIMIT 1) AS department_name                    
                FROM timetables a
                LEFT JOIN classes b ON b.item_id = a.class_id
                WHERE {$query} AND a.status = ? ORDER BY a.name LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $fullDetails = (bool) isset($params->full_detail);
            $noClientData = (bool) isset($params->no_client_data) && !empty($params->no_client_data);
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
                            ".(!empty($params->academic_year) ? " AND b.academic_year='{$params->academic_year}'" : null)."
                            ".(!empty($params->academic_term) ? " AND b.academic_term='{$params->academic_term}'" : null)."
                            AND b.class_id = a.class_id
                        LEFT JOIN classes c ON c.item_id = a.class_id", 
                    "a.timetable_id = '{$result->item_id}' AND b.academic_year = '{$params->academic_year}' AND b.academic_term='{$params->academic_term}' AND a.status='1' {$query}");
                    
                    $allocations = [];
                    foreach($allocate as $alot) {
                        $allocations[$alot->day_slot][] = $alot;
                    }
                    $result->allocations = $allocations;

                    $result->client_details = !$noClientData ? $this->pushQuery("a.*", "clients_accounts a", "a.client_id = '{$result->client_id}' AND a.client_status='1' LIMIT 1")[0] : [];
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

            if(empty($params->name)) {
                return ["code" => 400, "data" => "Sorry! The name is required"];
            }

            // confirm that the timetable_id is parsed
            if(!empty($params->timetable_id)) {
                // assign
                $item_id = $params->timetable_id;
                // check if a record exist
                if(empty($this->pushQuery("item_id", "timetables", "item_id = '{$item_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
                    return ["code" => 400, "data" => "Sorry! An invalid timetable id was parsed"];
                }
                $isFound = true;
            } else {
                // create a new timetable_id
                $item_id = random_string("alnum", RANDOM_STRING);
                $isFound = false;

                // if the class isset
                if(isset($params->class_id)) {
                    // confirm if a class already exist with the same id
                    if(!empty($this->pushQuery("item_id", "timetables", "class_id='{$params->class_id}' AND client_id = '{$params->clientId}' AND status='1' AND academic_term='{$params->academic_term}' AND academic_year='{$params->academic_year}'"))) {
                        return ["code" => 400, "data" => "Sorry! There is an existing record in the database for the specified Class ID."];
                    }
                }
            }

            // convert to array
            $disabled_inputs =  isset($params->disabled_inputs) ? $this->stringToArray($params->disabled_inputs) : [];

            // expected days
            $expected_days = !empty($params->expected_days) ? $params->expected_days : $this->default_opening_days;

            // set the days
            $params->days = count($expected_days);

            // clean input
            if( !is_numeric($params->days) || !is_numeric($params->slots) || !is_numeric($params->duration)) {
                return ["code" => 400, "data" => "Sorry! The days, slots and duration must be a valid numeric integers."];
            }

            // schedules
            $schedules = $params->days * $params->slots;
            
            // set a boundary
            if($schedules > 180) {
                return ["code" => 400, "data" => "Sorry! The maximum number of Allocations must be 180  ie 9 rows & 20 columns."];
            }

            $expected_days = !empty($params->expected_days) ? json_encode($params->expected_days) : 0;

            // update the record if found
            if($isFound) {
                $stmt = $this->db->prepare("
                    UPDATE timetables SET 
                        days = ?, slots = ?, duration = ?, start_time = ?, disabled_inputs = ?
                        ".(!empty($params->name) ? ", name = '{$params->name}'" : null)."
                        ".(!empty($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                        ".(!empty($expected_days) ? ", expected_days = '{$expected_days}'" : null)."
                        ".(!empty($params->first_break_starts) ? ", first_break_starts = '{$params->first_break_starts}'" : null)."
                        ".(!empty($params->first_break_ends) ? ", first_break_ends = '{$params->first_break_ends}'" : null)."
                        ".(!empty($params->second_break_starts) ? ", second_break_starts = '{$params->second_break_starts}'" : null)."
                        ".(!empty($params->second_break_ends) ? ", second_break_ends = '{$params->second_break_ends}'" : null)."
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
                        ".(!empty($params->name) ? ", name = '{$params->name}'" : null)."
                        ".(!empty($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                        ".(!empty($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                        ".(!empty($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                        ".(!empty($expected_days) ? ", expected_days = '{$expected_days}'" : null)."
                        ".(!empty($params->first_break_starts) ? ", first_break_starts = '{$params->first_break_starts}'" : null)."
                        ".(!empty($params->first_break_ends) ? ", first_break_ends = '{$params->first_break_ends}'" : null)."
                        ".(!empty($params->second_break_starts) ? ", second_break_starts = '{$params->second_break_starts}'" : null)."
                        ".(!empty($params->second_break_ends) ? ", second_break_ends = '{$params->second_break_ends}'" : null)."
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
            return ["code" => 400, "data" => "Sorry! An invalid timetable id was parsed"];
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
            return ["code" => 400, "data" => "Sorry! Query parameter is required"];
        }

        // if the user wants to save changes
        if($params->data["query"] === "allocation") {
            
            // end query if the allocations array is not parsed
            if(!isset($params->data["allocations"]) || !isset($params->data["timetable_id"])) {
                return ["code" => 400, "data" => "Sorry! Allocation / Timetable ID parameters are required"];
            }

            // confirm that allocations is an array
            if(!is_array($params->data["allocations"])) {
                return ["code" => 400, "data" => "Sorry! Allocations must be an array"];
            }

            // validate the id
            $item_id = $params->data["timetable_id"];
            $allocations = $params->data["allocations"];

            try {
            
                // check if a record exist
                if(empty($this->pushQuery("item_id", "timetables", "item_id = '{$item_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
                    return ["code" => 400, "data" => "Sorry! An invalid timetable id was parsed"];
                }

                // init values
                $course_ids = [];

                // loop through the allocations and group them
                foreach($allocations as $slots) {
                    // assign some variables
                    $course_room = !empty($slots["value"]) ? explode(":", $slots["value"]) : [];
                    $course = $course_room[0] ?? null;
                    $course_ids[$course] = isset($course_ids[$course]) ? $course_ids[$course]+1 : 1;

                    // confirm that a room has been assigned
                    if(!isset($course_room[1])) {
                        return ["code" => 400, "data" => "Sorry! There was no room assigned to the course."];
                    }
                }
                
                // load the meeting periods for each course
                $courses_list = $this->pushQuery("name, weekly_meeting, item_id", "courses", 
                    "item_id IN {$this->inList(array_keys($course_ids))} AND 
                    client_id='{$params->clientId}' LIMIT {$this->temporal_maximum}");
                
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
                        return ["code" => 400, "data" => "Sorry! An invalid Course ID was parsed."];
                    }
                }

                // if the bug is not empty then return
                if(!empty($bugs_list)) {
                    // allow subjects to be allocated even if the weekly meeting is less than the allocated days
                    // return ["code" => 400, "data" => $bugs_list];
                }

                // delete any slots inserted into the database for this class record
                $query = $this->db->prepare("DELETE FROM timetables_slots_allocation WHERE 
                    timetable_id = '{$item_id}' AND course_id IN {$this->inList(array_keys($course_ids))} AND client_id = '{$params->clientId}'
                    ".($class_id ? "AND class_id='{$class_id}'" : null)." LIMIT 200
                ");
                $query->execute();

                // prepare the insert query here
                $allot_slot = $this->db->prepare('INSERT INTO timetables_slots_allocation SET 
                    client_id = ?, timetable_id = ?, day = ?, slot = ?, weekday = ?, day_slot = ?, room_id = ?, class_id = ?, course_id = ?, status = ?
                ');

                // continue processing
                // loop through the allocations again and group them
                foreach($allocations as $slots) {
                    // assign some variables
                    $slot = !empty($slots["slot"]) ? explode("_", $slots["slot"]) : [];
                    $course_room = !empty($slots["value"]) ? explode(":", $slots["value"]) : [];
                    $course = $course_room[0] ?? null;
                    $room = $course_room[1] ?? null;

                    // insert the value into the database
                    $allot_slot->execute([$params->clientId, $item_id, $slot[0], $slot[1], $slots["weekday"], $slots["slot"], $room, $class_id, $course, 1]);
                }

                // update the timetable information
                $this->db->query("UPDATE timetables SET last_updated='{$this->current_timestamp}' WHERE item_id ='{$item_id}' LIMIT 1");

                // return
                return "The timetable was successfully save!";

            } catch(PDOException $e) {
                return $this->unexpected_error;
            }

        } else {

            // if slot is not parsed
            if(!isset($params->data["slot"])) {
                return ["code" => 400, "data" => "Sorry! Slot parameter is required"];
            }

            $slot = !empty($params->data["slot"]) ? explode("_", $params->data["slot"]) : [];

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
    public function class_timetable($classId, $clientId, $today_only = false, $height = null, $no_client_data = "no") {

        try {

            // parameters to use to load the information
            $param = (object) [
                "class_id" => $classId,
                "clientId" => $clientId,
                "full_detail" => true,
                "today_only" => $today_only,
                "limit" => 1
            ];

            if($no_client_data === "yes") {
                $param->no_client_data = true;
            }

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

                if($no_client_data === "yes") {
                    // init
                    $lessons_list = "<div class='row'>";
                    if(isset($data->allocations) && is_array($data->allocations)) {

                        // loop through the allocations list
                        foreach($data->allocations as $item => $value) {

                            foreach($value as $ii => $course) {

                                // get the lesson time
                                $start = $data->duration * $course->slot;
                                $end = ($data->duration * $course->slot) + $data->duration;

                                // configure the lesson start and end time
                                $course->lesson_start_time = date("h:i A", strtotime("{$data->start_time} +{$start} minutes"));
                                $course->lesson_end_time = date("h:i A", strtotime("{$data->start_time} +{$end} minutes"));

                                // append the subjects list
                                $lessons_list .= "
                                    <div class='col-lg-3 col-md-6'>
                                        <div class='card' style='border-top: solid 4px'>
                                            <div class='card-header pt-1 pb-1'>
                                                <strong>{$course->class_name}</strong>
                                            </div>
                                            <div style='min-height:140px' class='card-body pb-0 pt-2 mb-0'>
                                                <p>{$course->course_name} ({$course->course_code})</p>
                                                <p class='pb-0 mb-0'><i class='fa fa-clock'></i> {$course->lesson_start_time}</p>
                                                <p class='pb-0 mb-0'><i class='fa fa-clock'></i> {$course->lesson_end_time}</p>
                                            </div>
                                            <div class='card-footer p-2 border-top mt-0 text-center'>
                                                <a href='{$this->baseUrl}gradebook/{$course->course_id}?class_id={$course->class_id}&timetable_id={$data->item_id}' class='btn btn-outline-success btn-sm'><i class='fa fa-book-open'></i> Lesson</a>
                                                <a href='{$this->baseUrl}course/{$course->course_id}' class='btn btn-outline-primary btn-sm'><i class='fa fa-eye'></i> View Subject</a>
                                            </div>
                                        </div>
                                    </div>";
                            }
                        }
                    }
                    $lessons_list .= "</div>";

                    return $lessons_list;
                }
                
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
                $result = no_record_found("No Timetable Record Found", "No timetable has been generated for this class yet. Please check back tomorrow.", null, "Timetable", false, "fas fa-calendar-check", false);
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
     * @param String    $filter
     * @param String    $format
     * 
     * @return Array
     */
    public function teacher_timetable($course_ids, $clientId, $filter = "today", $format = true, $classId = null) {

        // global variable
        global $defaultAcademics, $defaultUser;
        
        // append some filters
        $filters = [
            "yesterday" => "AND ts.day = '".date("w", strtotime("-1 day"))."'",
            "today" => "AND ts.day = '".date("w")."'",
            "tomorrow" => "AND ts.day = '".date("w", strtotime("+1 day"))."'",
            "week" => "AND ts.day BETWEEN '".date("w", strtotime("monday this week"))."' AND '".date("w", strtotime("friday this week"))."'"
        ];
        $query = $filter ? ($filters[$filter] ?? null) : null;

        // init
        $data = [];

        try {

            // run a query for the teacher courses taught
            $stmt = $this->db->prepare("SELECT ts.*, c.name AS course_name, r.name AS room_name,
                    cl.name AS class_name, t.disabled_inputs, t.name AS timetable_name, ts.course_id,
                    t.slots, t.days, t.duration, t.start_time, t.allow_conflicts, c.course_code
                FROM timetables_slots_allocation ts 
                    LEFT JOIN courses c ON c.item_id = ts.course_id
                        AND c.academic_year ='{$defaultAcademics->academic_year}'
                        AND c.academic_term ='{$defaultAcademics->academic_term}'
                    LEFT JOIN classes cl ON cl.item_id = ts.class_id
                    LEFT JOIN timetables t ON t.item_id = ts.timetable_id
                    LEFT JOIN classes_rooms r ON r.item_id = ts.room_id
                WHERE ".(!empty($course_ids) ? "(c.course_tutor LIKE '%{$course_ids}%') AND" : null)."
                    ".(!empty($classId) ? "ts.class_id = '{$classId}' AND " : null)."
                    ts.status = '1' AND ts.client_id = '{$clientId}' AND t.published = '1' AND t.status = '1' {$query}
                AND t.academic_year = ? AND t.academic_term = ? ORDER BY ts.day DESC LIMIT 200 
            ");
            $stmt->execute([$defaultAcademics->academic_year, $defaultAcademics->academic_term]);

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
            // Subjects List
            $courses_list = [];
            $t_course_ids = [];

            // if the record is not empty
            if(!empty($data)) {
            
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

            }

            // init
            $lessons_list = "<div class='row'>";

            // end the query if no course was found
            if(empty($courses_list) && $format) {
                // return error message
                $lessons_list .= "<div class='col-lg-12 text-center'>";
                $lessons_list .= no_record_found("No Lessons Found", "You do not have any lessons to teach today.", null, "Lesson", false, "fas fa-book-reader");
                $lessons_list .= "</div>";
            } else {
                // get only the unique course ids
                $course_ids = array_unique($t_course_ids);

                // set
                $color_set = [];

                // color coding
                foreach($course_ids as $key => $each) {
                    $color_set[$each] = $this->color_set[$key] ?? null;
                }
                
                // order the array set using the date of the event
                usort($courses_list, "sort_lesson_start_time");

                // return the array list
                if(!$format) {
                    return $courses_list;
                }

                // confirm if term has ended
                $termEnded = (bool) $defaultUser->appPrefs->termEnded;

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
                            <div class='card-footer p-2 border-top mt-0 text-center'>
                                <a href='{$this->baseUrl}gradebook/{$course["course_id"]}?class_id={$course["class_id"]}&timetable_id={$course["timetable_id"]}' class='btn btn-outline-success btn-sm'><i class='fa fa-book-open'></i> Lesson</a>
                                <a href='{$this->baseUrl}course/{$course["course_id"]}' class='btn btn-outline-primary btn-sm'><i class='fa fa-eye'></i> View Course</a>
                            </div>
                        </div>
                    </div>";
                }
            }

            $lessons_list .= "</div>";

            return $lessons_list;
        
        } catch(PDOException $e) {
            return false;
        }

    }

    /**
     * Get The List Of Timetables
     * 
     * @param String    $params->clientId
     * @param String    $params->academic_year
     * @param String    $params->academic_term
     * 
     * @return Array
     */
    public function getlist($params = null) {

        global $isAdminAccountant, $isTutor,  $defaultUser;

        $classId = $isTutor || $isAdminAccountant ? null : $defaultUser->class_guid;

        // loop through the days for yesterday, today and tomorrow
        foreach(['yesterday', 'today', 'tomorrow', 'week'] as $day) {

            if(!empty($params->period) && !in_array($day, stringToArray($params->period))) continue;

            if($day == "week" && empty($params->period)) continue;

            // get the timetable list
            $timetableList = $this->teacher_timetable($isTutor ? $defaultUser->user_id : null, $defaultUser->clientId, $day, false, $classId);

            // loop through the timetable list
            foreach($timetableList as $i => $value) {

                $data = [
                    "id" => $value["id"],
                    "day" => dayToWord($value["day"]),
                    "course_id" => $value["course_id"],
                    "class_id" => $value["class_id"],
                    "timetable_id" => $value["timetable_id"],
                    "lesson_start_time" => $value["lesson_start_time"],
                    "lesson_end_time" => $value["lesson_end_time"],
                    "course_name" => $value["course_name"],
                    "course_code" => $value["course_code"],
                    "class_name" => $value["class_name"],
                    "room_name" => $value["room_name"],
                    "timetable_name" => $value["timetable_name"],
                    "duration" => $value["duration"],
                    
                ];

                $todayLessons[$data['day']][] = $data;
            }
        }

        return $todayLessons ?? [];
        
    }

    /**
     * Students Attendance
     * 
     * @param String    $params->course_id
     * @param String    $params->class_id
     * @param String    $params->timetable_id
     * @param String    $params->limit
     * 
     * @return Array
     */
    public function lesson_record_data(stdClass $params) {

        try { 

            $where_clause = "a.client_id = '{$params->clientId}'";

            $limit = isset($params->limit) ? $params->limit : $this->global_limit;

            $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
            $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

            $where_clause .= !empty($params->timetable_id) ? " AND a.timetable_id='{$params->timetable_id}'" : null;
            $where_clause .= !empty($params->course_id) ? " AND a.course_id ='{$params->course_id}'" : null;
            $where_clause .= !empty($params->class_id) ? " AND a.class_id ='{$params->class_id}'" : null;
            $where_clause .= isset($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : "";
            $where_clause .= isset($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : "";

            $stmt = $this->db->prepare("SELECT a.* FROM courses_assessment a WHERE {$where_clause} LIMIT {$limit}");
            $stmt->execute();

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) { 
                foreach(["students_attendance_data", "students_grading_data"] as $item) {
                    $result->{$item} = !empty($item) && !empty($result->{$item}) ? json_decode($result->{$item}, true) : [];
                }
                $data[] = $result;
            }

            return [
                "data" => $data
            ];

        } catch(PDOException $e) {

        }

    }

    /**
     * Log Student Attendance
     * 
     * @param   Int         $params->student_id
     * @param   String      $params->course_id
     * @param   String      $params->timetable_id
     * @param   String      $params->attendance
     * @param   String      $params->comments
     * @param   String      $params->class_id
     * 
     * @return Array
     */
    public function log_attendance(stdClass $params) {
        
        try {

            // variables
            $data = [];
            $today = date("d-m-Y");
            $category = $params->attendance ?? null;
            $student_name = $params->student_name ?? null;
            $comments = isset($params->comments) ? substr($params->comments, 0, 255) : null;

            // get the course information
            $timetable_data = $this->pushQuery("a.id, 
                (
                    SELECT b.students_attendance_data FROM courses_assessment b WHERE 
                    b.course_id='{$params->course_id}' AND b.timetable_id='{$params->timetable_id}'
                    AND b.class_id='{$params->class_id}' LIMIT 1
                ) AS attendance_record,
                (
                    SELECT b.students_grading_data FROM courses_assessment b WHERE 
                    b.course_id='{$params->course_id}' AND b.timetable_id='{$params->timetable_id}'
                    AND b.class_id='{$params->class_id}' LIMIT 1
                ) AS students_grading_data", 
                "timetables_slots_allocation a", 
                "a.course_id='{$params->course_id}' AND a.timetable_id='{$params->timetable_id}'
                AND a.class_id='{$params->class_id}' AND a.client_id='{$params->clientId}' LIMIT 1");
            
            // confirm if the data set is not empty
            if(empty($timetable_data)) {
                return ["code" => 400, "data" => "Sorry! An invalid record data was parsed."];
            }

            // convert the attendance record to json
            $log = !empty($timetable_data[0]->attendance_record) ? json_decode($timetable_data[0]->attendance_record, true) : [];
            $log2 = !empty($timetable_data[0]->students_grading_data) ? json_decode($timetable_data[0]->students_grading_data, true) : [];
            
            // check if the record is empty
            if(empty($log) && empty($log2)) {

                // set the summary data
                $summary_data = [
                    "present" => $category == "present" ? 1 : null,
                    "absent" => $category == "absent" ? 1 : null,
                    "late" => $category == "late" ? 1 : null,
                    "late_excused" => $category == "late_excused" ? 1 : null,
                    "absent_excused" => $category == "absent_excused" ? 1 : null
                ];
                
                // set the student record
                $record = [
                    $params->student_id => [
                        "name" => $student_name,
                        "dates" => [
                            $today => [
                                "status" => $category,
                                "comments" => $comments
                            ]
                        ],
                        "summary" => $summary_data
                    ]
                ];

                // insert the attendance record
                $stmt = $this->db->prepare("INSERT INTO courses_assessment SET client_id = ?, course_id = ?, class_id = ?,
                    timetable_id = ?, academic_year = ?, academic_term = ?, students_attendance_data = ?");

                // execute the prepared statement above
                $stmt->execute([$params->clientId, $params->course_id, $params->class_id, $params->timetable_id, 
                    $params->academic_year, $params->academic_term, json_encode($record)
                ]);
            }
            // append to the record list
            else {
                // if the student record exists in the log
                if(isset($log[$params->student_id])) {
                    // get student data
                    $student_data = $log[$params->student_id];

                    // replace the record set
                    $log[$params->student_id]["dates"][$today] = [
                        "status" => $category,
                        "comments" => $comments
                    ];

                    // init
                    $counted = [];
                    // loop through the items list
                    foreach($log[$params->student_id]["dates"] as $date) {
                        $counted[$date["status"]] = isset($counted[$date["status"]]) ? ($counted[$date["status"]] + 1) : 1;
                    }

                    // set the summary data
                    $summary_data = [
                        "present" => $counted["present"] ?? null,
                        "absent" => $counted["absent"] ?? null,
                        "late" => $counted["late"] ?? null,
                        "late_excused" => $counted["late_excused"] ?? null,
                        "absent_excused" => $counted["absent_excused"] ?? null
                    ];

                    $log[$params->student_id]["summary"] = $summary_data;
                }
                // student record does not already exist in the log
                else {
                    // set the summary data
                    $summary_data = [
                        "present" => $category == "present" ? 1 : null,
                        "absent" => $category == "absent" ? 1 : null,
                        "late" => $category == "late" ? 1 : null,
                        "late_excused" => $category == "late_excused" ? 1 : null,
                        "absent_excused" => $category == "absent_excused" ? 1 : null
                    ];

                    // set the student data
                    $log[$params->student_id] = [
                        "name" => $student_name,
                        "dates" => [
                            $today => [
                                "status" => $category,
                                "comments" => $comments
                            ]
                        ],
                        "summary" => $summary_data
                    ];
                }

                // update the existing record
                $stmt = $this->db->prepare("UPDATE courses_assessment SET students_attendance_data = ? WHERE 
                    client_id = ? AND course_id = ? AND class_id = ? AND timetable_id = ? AND 
                    academic_year = ? AND academic_term = ? LIMIT 1");

                // execute the prepared statement above
                $stmt->execute([json_encode($log),
                    $params->clientId, $params->course_id, $params->class_id, $params->timetable_id, 
                    $params->academic_year, $params->academic_term
                ]);

                // set new variable
                $record = $log;
            }
            
            return [
                "data" => "Attendance Log was successful",
                "additional" => [
                    "date" => $today,
                    "state" => str_ireplace("_", " ", $category),
                    "summary" => $summary_data,
                    "record" => $record[$params->student_id]["dates"]
                ]
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Log Student Attendance
     * 
     * @param   Int         $params->student_id
     * @param   String      $params->course_id
     * @param   String      $params->timetable_id
     * @param   String      $params->attendance
     * @param   String      $params->comments
     * @param   String      $params->class_id
     * 
     * @return Array
     */
    public function bulk_attendance(stdClass $params) {

        try {

            // variables
            $data = [];
            $today = date("d-m-Y");

            // get the course information
            $timetable_data = $this->pushQuery("a.id, 
                (
                    SELECT b.students_attendance_data FROM courses_assessment b WHERE 
                    b.course_id='{$params->course_id}' AND b.timetable_id='{$params->timetable_id}'
                    AND b.class_id='{$params->class_id}' LIMIT 1
                ) AS attendance_record,
                (
                    SELECT b.students_grading_data FROM courses_assessment b WHERE 
                    b.course_id='{$params->course_id}' AND b.timetable_id='{$params->timetable_id}'
                    AND b.class_id='{$params->class_id}' LIMIT 1
                ) AS students_grading_data", 
                "timetables_slots_allocation a", 
                "a.course_id='{$params->course_id}' AND a.timetable_id='{$params->timetable_id}'
                AND a.class_id='{$params->class_id}' AND a.client_id='{$params->clientId}' LIMIT 1");
            
            // confirm if the data set is not empty
            if(empty($timetable_data)) {
                return ["code" => 400, "data" => "Sorry! An invalid record data was parsed."];
            }

            // convert the attendance record to json
            $log = !empty($timetable_data[0]->attendance_record) ? json_decode($timetable_data[0]->attendance_record, true) : [];
            $log2 = !empty($timetable_data[0]->students_grading_data) ? json_decode($timetable_data[0]->students_grading_data, true) : [];

            // group the students record
            // check if the record is empty
            if(empty($log) && empty($log2)) {

                // loop through the students list
                foreach($params->li as $key => $student) {
                    // set the category
                    $category = $student["o"] ?? "absent";

                    // set the summary data
                    $summary_data = [
                        "present" => $category == "present" ? 1 : null,
                        "absent" => $category == "absent" ? 1 : null,
                        "late" => $category == "late" ? 1 : null,
                        "late_excused" => $category == "late_excused" ? 1 : null,
                        "absent_excused" => $category == "absent_excused" ? 1 : null
                    ];
                    
                    // set the student record
                    $record[$key] = [
                        "name" => $student["n"] ?? null,
                        "dates" => [
                            $today => [
                                "status" => $category,
                                "comments" => $student["c"] ?? null
                            ]
                        ],
                        "summary" => $summary_data
                    ];
                }

                // insert the attendance record
                $stmt = $this->db->prepare("INSERT INTO courses_assessment SET client_id = ?, course_id = ?, class_id = ?,
                    timetable_id = ?, academic_year = ?, academic_term = ?, students_attendance_data = ?");

                // execute the prepared statement above
                $stmt->execute([$params->clientId, $params->course_id, $params->class_id, $params->timetable_id, 
                    $params->academic_year, $params->academic_term, json_encode($record)
                ]);
            }
            // append to the record list
            else {

                // loop through the students list
                foreach($params->li as $sid => $student) {
                    // set the category
                    $category = $student["o"];

                    // if the student record exists in the log
                    if(isset($log[$sid])) {

                        // get student data
                        $student_data = $log[$sid];

                        // replace the record set
                        $log[$sid]["dates"][$today] = [
                            "status" => $category,
                            "comments" => $student["c"] ?? null
                        ];

                        // init
                        $counted = [];

                        // loop through the items list
                        foreach($log[$sid]["dates"] as $date) {
                            $counted[$date["status"]] = isset($counted[$date["status"]]) ? ($counted[$date["status"]] + 1) : 1;
                        }

                        // set the summary data
                        $summary_data = [
                            "present" => $counted["present"] ?? null,
                            "absent" => $counted["absent"] ?? null,
                            "late" => $counted["late"] ?? null,
                            "late_excused" => $counted["late_excused"] ?? null,
                            "absent_excused" => $counted["absent_excused"] ?? null
                        ];

                        $log[$sid]["summary"] = $summary_data;
                    }
                    // student record does not already exist in the log
                    else {
                        // set the summary data
                        $summary_data = [
                            "present" => $category == "present" ? 1 : null,
                            "absent" => $category == "absent" ? 1 : null,
                            "late" => $category == "late" ? 1 : null,
                            "late_excused" => $category == "late_excused" ? 1 : null,
                            "absent_excused" => $category == "absent_excused" ? 1 : null
                        ];

                        // set the student data
                        $log[$sid] = [
                            "name" => $student["n"] ?? null,
                            "dates" => [
                                $today => [
                                    "status" => $category,
                                    "comments" => $student["c"] ?? null
                                ]
                            ],
                            "summary" => $summary_data
                        ];
                    }
                }

                // update the existing record
                $stmt = $this->db->prepare("UPDATE courses_assessment SET students_attendance_data = ? WHERE 
                    client_id = ? AND course_id = ? AND class_id = ? AND timetable_id = ? AND 
                    academic_year = ? AND academic_term = ? LIMIT 1");

                // execute the prepared statement above
                $stmt->execute([json_encode($log),
                    $params->clientId, $params->course_id, $params->class_id, $params->timetable_id, 
                    $params->academic_year, $params->academic_term
                ]);

                // set new variable
                $record = $log;
            }

            return [
                "data" => "Student attendance successfully logged.",
            ];


        } catch(PDOException $e) {}

    }

    /**
     * Save Student Grade
     * 
     * @param   Int         $params->student_id
     * @param   String      $params->course_id
     * @param   String      $params->timetable_id
     * @param   String      $params->attendance
     * @param   String      $params->comments
     * @param   String      $params->class_id
     * @param   Int         $params->grade
     * @param   String      $params->grade_type
     *  
     * @return Array
     */
    public function save_grade(stdClass $params) {
        
        try {

            // variables
            $data = [];
            $today = $params->date ?? date("Y-m-d");
            $grade = $params->grade ?? null;
            $grade_type = $params->grade_type ?? null;
            $student_name = $params->student_name ?? null;
            $assessment_id = $params->assessment_id ?? null;
            $comments = isset($params->comments) ? substr($params->comments, 0, 255) : null;

            // ensure the date does not exceed current date
            if(strtotime($today) > strtotime(date("Y-m-d"))) {
                return ["code" => 400, "data" => "Sorry! The date must not exceed the current date."];
            }

            // convert the grade to an int
            $grade = (int) $grade;
            
            // get the course information
            $timetable_data = $this->pushQuery("a.id, 
                (
                    SELECT b.students_grading_data FROM courses_assessment b WHERE 
                    b.course_id='{$params->course_id}' AND b.timetable_id='{$params->timetable_id}'
                    AND b.class_id='{$params->class_id}' LIMIT 1
                ) AS students_grading_data,
                (
                    SELECT b.students_attendance_data FROM courses_assessment b WHERE 
                    b.course_id='{$params->course_id}' AND b.timetable_id='{$params->timetable_id}'
                    AND b.class_id='{$params->class_id}' LIMIT 1
                ) AS students_attendance_data", 
                "timetables_slots_allocation a", 
                "a.course_id='{$params->course_id}' AND a.timetable_id='{$params->timetable_id}'
                AND a.class_id='{$params->class_id}' AND a.client_id='{$params->clientId}' LIMIT 1");
            
            // confirm if the data set is not empty
            if(empty($timetable_data)) {
                return ["code" => 400, "data" => "Sorry! An invalid record data was parsed."];
            }

            // convert the attendance record to json
            $log = !empty($timetable_data[0]->students_grading_data) ? json_decode($timetable_data[0]->students_grading_data, true) : [];
            $log2 = !empty($timetable_data[0]->students_attendance_data) ? json_decode($timetable_data[0]->students_attendance_data, true) : [];

            // check if the record is empty
            if(empty($log) && empty($log2)) {

                // set the summary data
                $summary_data = [
                    "classwork" => $grade_type == "classwork" ? $grade : null,
                    "homework" => $grade_type == "homework" ? $grade : null,
                    "midterm" => $grade_type == "midterm" ? $grade : null,
                    "quiz" => $grade_type == "quiz" ? $grade : null,
                    "groupwork" => $grade_type == "groupwork" ? $grade : null
                ];
                
                // set the student record
                $record[$grade_type]["students"] = [
                    $params->student_id => [
                        "name" => $student_name,
                        "dates" => [
                            $today => [
                                "grade" => $grade,
                                "comments" => $comments,
                                "grade_type" => $grade_type,
                                "assessment_id" => $assessment_id
                            ]
                        ]
                    ]
                ];
                $record[$grade_type]["summary"] = $summary_data;

                // insert the attendance record
                $stmt = $this->db->prepare("INSERT INTO courses_assessment SET client_id = ?, course_id = ?, class_id = ?,
                    timetable_id = ?, academic_year = ?, academic_term = ?, students_grading_data = ?");

                // execute the prepared statement above
                $stmt->execute([$params->clientId, $params->course_id, $params->class_id, $params->timetable_id, 
                    $params->academic_year, $params->academic_term, json_encode($record)
                ]);
            }
            // append to the record list
            else {
                // if the student record exists in the log
                if(isset($log[$grade_type]["students"][$params->student_id])) {
                    // get student data
                    $student_data = $log[$grade_type]["students"][$params->student_id];

                    // replace the record set
                    $log[$grade_type]["students"][$params->student_id]["dates"][$today] = [
                        "grade" => $grade,
                        "comments" => $comments,
                        "grade_type" => $grade_type,
                        "assessment_id" => $assessment_id
                    ];

                    // init
                    $counted = [];
                    // loop through the items list
                    foreach($log[$grade_type]["students"][$params->student_id]["dates"] as $date) {
                        $counted[$date["grade"]] = isset($counted[$date["grade"]]) ? ($counted[$date["grade"]] + $grade) : $grade;
                    }

                    // set the summary data
                    $summary_data = [
                        "classwork" => $counted["classwork"] ?? null,
                        "homework" => $counted["homework"] ?? null,
                        "midterm" => $counted["midterm"] ?? null,
                        "quiz" => $counted["quiz"] ?? null,
                        "groupwork" => $counted["groupwork"] ?? null
                    ];

                    $log[$grade_type]["summary"] = $summary_data;
                }
                // student record does not already exist in the log
                else {
                    // set the summary data
                    $summary_data = [
                        "classwork" => $grade_type == "classwork" ? $grade : null,
                        "homework" => $grade_type == "homework" ? $grade : null,
                        "midterm" => $grade_type == "midterm" ? $grade : null,
                        "quiz" => $grade_type == "quiz" ? $grade : null,
                        "groupwork" => $grade_type == "groupwork" ? $grade : null
                    ];

                    // set the student data
                    $log[$grade_type]["students"][$params->student_id] = [
                        "name" => $student_name,
                        "dates" => [
                            $today => [
                                "grade" => $grade,
                                "comments" => $comments,
                                "grade_type" => $grade_type,
                                "assessment_id" => $assessment_id
                            ]
                        ]
                    ];

                    $log[$grade_type]["summary"] = $summary_data;
                }

                // update the existing record
                $stmt = $this->db->prepare("UPDATE courses_assessment SET students_grading_data = ? WHERE 
                    client_id = ? AND course_id = ? AND class_id = ? AND timetable_id = ? AND 
                    academic_year = ? AND academic_term = ? LIMIT 1");

                // execute the prepared statement above
                $stmt->execute([json_encode($log), $params->clientId, $params->course_id, 
                    $params->class_id, $params->timetable_id, $params->academic_year, $params->academic_term
                ]);

                // set new variable
                $record = $log;
            }

            return [
                "data" => "Grade was successfully alloted to student",
                "additional" => [
                    "date" => date("jS M", strtotime($today)),
                    "raw_date" => $today,
                    "grade" => $grade,
                    "record" => $record[$grade_type]["students"][$params->student_id]
                ]
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Draw Clendar
     *
     * Use the provided information to draw the calendar
     * 
     * @return Array 
     */
    public function draw(stdClass $params) {

        global $academicSession;

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

        // init
        $html_table = "<div class='text-center alert alert-warning'>No timetable record to show at the moment.</div>";
        
        // if the data is not empty
        if(!empty($data)) {
            // column with calculation
            $summary = null;
            $slots = $data->slots;
            $width = round((100/($slots+1)), 2);

            // get the client logo content
            if(!empty($this->iclient->client_logo)) {
                $type = pathinfo($this->iclient->client_logo, PATHINFO_EXTENSION);
                $logo_data = @file_get_contents($this->iclient->client_logo);
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
                                <strong style=\"font-size:15px;\">Academic {$academicSession}:</strong> {$prefs->academics->academic_term}<br>
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

            // Prepare data array for the function
            $timetable_array = [
                'id' => $data->item_id,
                'name' => $data->name,
                'days' => $data->days,
                'slots' => $data->slots,
                'duration' => $data->duration,
                'class_id' => $data->class_id,
                'expected_days' => $data->expected_days,
                'timetable_allocations' => $data->allocations,
                'first_break_starts' => $data->first_break_starts ?? '10:00',
                'first_break_ends' => $data->first_break_ends ?? '10:30',
                'second_break_starts' => $data->second_break_starts ?? '12:30',
                'second_break_ends' => $data->second_break_ends ?? '13:30'
            ];
            
            // Get start time from data or default to 08:00
            $start_time = isset($data->start_time) ? date("H:i", strtotime($data->start_time)) : '08:00';

            $html_table .= $summary.'<table class="'.$table_class.'" id="t_table" width="100%" cellpadding="0px" style="margin: auto auto;" cellspacing="0px">'."\n";
            $html_table .= "<tr ".(isset($params->height) && $params->height ? "style='height:{$params->height}px'" : "").">\n";
            // $start_time = $data->start_time;
            $html_table .= "<td>";

            // Call the timetable drawing function
            $html_table .= draw_timetable_table($timetable_array, $start_time, true);

            $html_table .= "</td>\n";
            $html_table .= "</tr>\n";
            $html_table .= "</table>";

            $html_table .= "</table>";
        }
        
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