<?php 
class Terminal_reports extends Myschoolgh {

    private $iclient;

    public function __construct(stdClass $params = null) {
        
        // call the parent function
		parent::__construct();

        // get the client data
        $client_data = $params->client_data;
        
        // end the query if no information was found
        if(empty($client_data->client_preferences)) {
            return;
        }

        // run this query
        $academics = $client_data->client_preferences->academics;
        $this->iclient = $client_data;
        
        $this->academic_term = $academics->academic_term;
        $this->academic_year = $academics->academic_year;

        $this->this_term_starts = $academics->term_starts;
        $this->this_term_ends = $academics->term_ends;

        $this->last_term_starts = $academics->last_term_starts ?? $academics->term_starts;
        $this->last_term_ends = $academics->last_term_ends ?? $academics->term_ends;
        
        // prepare the statement
        $this->insert_stmt = $this->db->prepare("INSERT INTO grading_terminal_scores SET 
            class_id = ?, class_name = ?, date_modified = now(),
            course_id = ?, course_name = ?, course_code = ?, scores = ?, total_score = ?, 
            average_score = ?, teacher_ids = ?, class_teacher_remarks = ?, created_by = ?,
            academic_year = ?, academic_term = ?, student_unique_id = ?, client_id = ?, report_id = ?
        ");

        // prepare the statement
        $this->update_stmt = $this->db->prepare("UPDATE grading_terminal_scores SET 
                class_id = ?, class_name = ?, report_id = ?, course_name = ?, 
                course_code = ?, scores = ?, total_score = ?, date_modified = now(),
                average_score = ?, teacher_ids = ?, class_teacher_remarks = ?
            WHERE academic_year = ? AND academic_term = ? AND student_unique_id = ? AND course_id = ? AND client_id = ?
        ");

    }

    /**
     * Uploads List
     * 
     * List all uploaded terminal reports by a user
     * 
     * @return Array
     */
    public function reports_list(stdClass $params) {
        
        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        $params->query = 1;

        // append some additional queries
        $params->query .= isset($params->created_by) && !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= isset($params->class_id) && !empty($params->class_id) ? " AND a.class_id='{$params->class_id}'" : null;
        $params->query .= isset($params->course_id) && !empty($params->course_id) ? " AND a.course_id='{$params->course_id}'" : null;
        $params->query .= isset($params->course_code) && !empty($params->course_code) ? " AND a.course_code='{$params->course_code}'" : null;
        $params->query .= isset($params->status) && !empty($params->status) ? " AND a.status='{$params->status}'" : null;
        $params->query .= isset($params->report_id) && !empty($params->report_id) ? " AND a.report_id='{$params->report_id}'" : null;

        // query the academic year and term
        $params->query .= isset($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : " AND a.academic_year='{$this->academic_year}'";
        $params->query .= isset($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : " AND a.academic_term='{$this->academic_term}'";

        try {

            $stmt = $this->db->prepare("SELECT a.*, u.name AS fullname, u.unique_id AS user_unique_id,
                    (SELECT COUNT(*) FROM grading_terminal_scores b WHERE b.report_id = a.report_id) AS students_count,
                    (SELECT b.average_score FROM grading_terminal_scores b WHERE b.report_id = a.report_id LIMIT 1) AS overall_score
                FROM grading_terminal_logs a
                LEFT JOIN users u ON u.item_id = a.created_by
                WHERE {$params->query} ORDER by a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
            $showScores = (bool) isset($params->show_scores);

            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                if($showScores) {
                    $result->scores_list = $this->result_score_list($result->report_id);
                }
                $data[] = $result;
            }

            return [
                "data" => $data
            ];
    
        } catch(PDOException $e) {}

    }

    /**
     * Get the results
     * 
     * @return Array
     */
    public function result_score_list($report_id = null, $where = null) {

        try {

            global $usersClass;
            
            $groupStudent = false;
            $where_clause = !empty($report_id) ? "a.report_id = '{$report_id}'" : $where;

            if(!empty($where) && is_object($where)) {
                $where_clause = "1";
                $groupStudent = $where->group_by_student;
                foreach($where as $key => $value) {
                    $where_clause .= !empty($value) && $key !== "group_by_student" ? " AND a.{$key}='{$value}'" : null;
                }
            }

            $stmt = $this->db->prepare("SELECT 
                a.*, u.date_of_birth, u.unique_id, u.guardian_id
                FROM grading_terminal_scores a
                LEFT JOIN users u ON u.item_id = a.student_item_id
                WHERE {$where_clause} LIMIT 150
            ");
            $stmt->execute();

            $data = [];

            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                $scores = json_decode($result->scores, true);
                $scores_array = [];
                foreach($scores as $key => $score) {
                    $scores_array[$scores[$key]["item"]] = $scores[$key]["score"];
                }
                $result->scores = $scores_array;

                // if the request is to group by each student
                if($groupStudent) {
                    // if the student array has not been set already
                    if(!isset($data[$result->student_item_id]["data"])) {
                        // set the data
                        $data[$result->student_item_id]["data"] = [
                            "student_name" => $result->student_name,
                            "unique_id" => $result->unique_id,
                            "average_score" => $result->average_score,
                            "class_name" => $result->class_name,
                            "guardian_list" => $usersClass->guardian_list($result->guardian_id, $result->client_id, true),
                            "date_of_birth" => $result->date_of_birth,
                            "student_age" => convert_to_years($result->date_of_birth, date("Y-m-d")),
                            "academic_year" => $result->academic_year,
                            "academic_term" => $result->academic_term,
                        ];
                    }
                    $data[$result->student_item_id]["sheet"][] = $result;
                } else {
                    $data[] = $result;
                }
            }

            return $data;

        } catch(PDOException $e) {}

    }

    /**
     * Check Existence
     * 
     * Confirm that there is an existing record
    */
    public function check_existence(stdClass $params) {

        // get the terminal logs
        $check = $this->pushQuery(
            "status", 
            "grading_terminal_logs", 
            "course_id = '{$params->course_id}' AND 
            academic_year='{$params->academic_year}' AND academic_term='{$params->academic_term}' AND
            class_id='{$params->class_id}' AND client_id='{$params->clientId}'"
        );
            
        if(!empty($check)) {
            if($check[0]->status === "Pending") {
                return ["code" => 200, "data" => "There is an existing record, trying to upload a new set of data will replace the existing one."];
            } elseif($check[0]->status === "Approved") {
                return ["code" => 203, "data" => "Sorry! This result has already been approved hence cannot be updated."];
            } elseif($check[0]->status === "Submitted") {
                return ["code" => 203, "data" => "Sorry! This result has been submitted pending approval hence cannot be updated."];
            }
        } else {
            return ["code" => 200, "data" => "You can proceed to upload the results of the class"];
        }
    }

    /**
     * Download the CSV File
     * 
     * @return Array
     */
    public function download_csv(stdClass $params) {

        // get the client data
        $client_data = $this->client_data($params->clientId);

        // old record
        $class_name = $this->pushQuery("id, item_id, name", "classes", "item_id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        // if empty then return
        if(empty($class_name)) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
        }

        // old record
        $course_item = $this->pushQuery("id, item_id, name, course_code", "courses", "item_id='{$params->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        // if empty then return
        if(empty($course_item)) {
            return ["code" => 203, "data" => "Sorry! An invalid course id was supplied."];
        }

        // get the students for the classes
        $students_list = $this->pushQuery("name, unique_id", "users", 
            "academic_year = '{$params->academic_year}' AND academic_term = '{$params->academic_term}' 
            AND class_id='{$class_name[0]->id}' AND client_id='{$params->clientId}' 
            AND user_status='Active' AND user_type='student'"
        );

        // if empty then return
        if(empty($students_list)) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
        }

        $course_item = $course_item[0];
        $course_name = ucwords($course_item->name);
        $course_code = strtoupper($course_item->course_code);

        $csv_file = "Student Name,Student ID,";
        $csv_file .= "Subject Name,Subject Code,";

        // get the grading structure
        if(!empty($client_data->grading_structure)) {
            $columns = json_decode($client_data->grading_structure);
            if(isset($columns->columns)) {
                $count = 0;
                foreach($columns->columns as $key => $column) {
                    $count++;
                    $csv_file .= "{$key},";
                }
            }
        }

        $csv_file .= "Teacher ID,";
        $csv_file .= "Teacher Remarks\n";

        foreach($students_list as $student) {
            $csv_file .= "{$student->name},{$student->unique_id},{$course_name},{$course_code}\n";
        }

        // upload file
        $temp_dir = "assets/uploads/{$params->clientId}/temp";
        
        // if not a directory then create it
        if(!is_dir($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        // set the name
        $filename = "{$temp_dir}/terminal_report_".create_slug($class_name[0]->name, "_").".csv";

        try {

            // write the content to the sample file    
            $op = fopen($filename, 'w');
            fwrite($op, $csv_file);
            fclose($op);

            return [
                "code" => 200,
                "data" => $filename
            ];
        } catch(\ErrorException $e) {}
    }

    /**
     * Upload CSV File
     * 
     * Upload the terminal report csv file and generate the table to display the data set
     * 
     * @return Array
     */
    public function upload_csv(stdClass $params) {
        
        // reading tmp_file name
        $report_file = fopen($params->report_file['tmp_name'], 'r');

        // get the content of the file
        $file_headers = fgetcsv($report_file);
        $complete_csv_data = [];

        //using while loop to get the information
        while($row = fgetcsv($report_file)) {
            // session data
            $complete_csv_data[] = $row;
        }

        // check the file that have been uploaded
        $headers = ["Student Name","Student ID","Subject Name","Subject Code"];

        // get the client data
        $client_data = $this->client_data($params->clientId);
        $columns = json_decode($client_data->grading_structure, true);

        // get the grading structure
        if(!empty($client_data->grading_structure)) {
            if(isset($columns["columns"])) {
                $count = 0;
                foreach($columns["columns"] as $key => $column) {
                    $count++;
                    $headers[] = $key;
                }
            }
        }
        $headers[] = "Teacher ID";
        $headers[] = "Teacher Remarks";

        // loop through the headers and ensure that they all match
        foreach($file_headers as $item) {
            if(!in_array($item, $headers)) {
                return ["code" => 203, "data" => "Ensure you have uploaded the sample CSV File with the predefined headers."];
            }
        }
        
        // draw a table with the headers
        $report_table = "<table class='table table-bordered'>";
        $report_table .= "<thead>";
        $report_table .= "<th>#</th>";
        foreach($file_headers as $item) {
            $report_table .= "<th>{$item}</th>";
        }
        $report_table .= "</thead>";
        $report_table .= "<tbody>";

        // set the files
        foreach($complete_csv_data as $key => $result) {
            $report_table .= "<tr>";
            $report_table .= "<td>".($key+1)."</td>";
            foreach($result as $kkey => $kvalue) {
                // if the key is in the array list
                if(in_array($file_headers[$kkey], array_keys($columns["columns"]))) {
                    $column = create_slug($file_headers[$kkey], "_");
                    $report_table .= "<td><input ".($columns["columns"][$file_headers[$kkey]] == "100" ? "disabled='disabled' data-input_total_id='{$key}'" : "data-input_type_q='marks' data-input_type='score' data-input_row_id='{$key}'" )." class='form-control font-18 text-center' name='{$column}' min='0' max='1000' type='number' value='{$kvalue}'></td>";
                } elseif($file_headers[$kkey] == "Teacher Remarks") {
                    $report_table .= "<td><input type='text' data-input_method='remarks' data-input_type='score' data-input_row_id='{$key}' class='form-control' value='{$kvalue}'></td>";
                } else {
                    $report_table .= "<td><span ".($file_headers[$kkey] == "Student ID" ? "data-student_row_id='{$key}' data-student_id='{$kvalue}'" : "").">{$kvalue}</span></td>";
                }
            }
            $report_table .= "</tr>";
        }
        $report_table .= "</tbody>";
        $report_table .= "</table>";

        $report_table .= "<div class='text-right'>";
        $report_table .= "<button onclick='return save_terminal_report()' class='btn btn-outline-success'>Save Report</button>";
        $report_table .= "</div>";

        // save the information in a session
        $this->session->set("terminal_report_{$params->class_id}_{$params->course_id}", ["headers" => $headers, "students" => $complete_csv_data]);

        return [
            "data" => $report_table
        ];

    }

    /**
     * Save the Report
     * 
     * Loop through the array of results set parsed and save it in the database
     * 
     * @return Array
     */
    public function save_report(stdClass $params) {

        // confirm that the record set is an array
        if(!is_array($params->report_sheet)) {
            return ["code" => 203, "data" => "Sorry! The report_sheet parameter must be an array."];
        }

        // set the report
        $report = (object) $params->report_sheet;
        $report->clientId = $params->clientId;

        // set the academic year and term
        $report->academic_year = $params->academic_year;
        $report->academic_term = $params->academic_term;

        // check if a file has been uploaded
        if(empty($this->session->get("terminal_report_{$report->class_id}_{$report->course_id}"))) {
            return ["code" => 203, "data" => "Sorry! Upload a valid CSV file to proceed."];
        }

        // confirm that the valid information was parsed
        $record_check = $this->check_existence($report);
        
        // return the response if not a code 200 was returned
        if($record_check["code"] !== 200) {
            return $record_check["data"];
        }

        try {

            // ensure that all the required parameters have been parsed
            if(!isset($report->class_id) || !isset($report->course_id)) {
                return ["code" => 203, "data" => "Sorry! The course_id and class_id are required."];
            }

            // old record
            $class_item = $this->pushQuery("id, item_id, name", "classes", "item_id='{$report->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($class_item)) {
                return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
            }

            // old record
            $course_item = $this->pushQuery("id, item_id, name, course_code", "courses", "item_id='{$report->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($course_item)) {
                return ["code" => 203, "data" => "Sorry! An invalid course id was supplied."];
            }

            // student scores checkeer
            if(!isset($report->student_scores)) {
                return ["code" => 203, "data" => "Sorry! The students scores is required and must be submitted."];
            }

            $overall_score = 0;
            $scores_array = [];
            // group the information in an array
            foreach($report->student_scores as $score) {
                // append to the array if its a numeric figure
                if(is_numeric($score["score"])) {
                    $scores_array[$score["student_id"]]["marks"][] = [
                        "item" => $score["item"],
                        "score" => $score["score"]
                    ];
                    $overall_score += $score["score"];
                }
                // append the teachers remarks to the array
                $scores_array[$score["student_id"]]["remarks"] = $score["remarks"];
            }

            // get the sum for all scores
            foreach($scores_array as $key => $score) {
                $total = array_column($score["marks"], "score");
                $total = !empty($total) ? array_sum($total) : 0;
                $scores_array[$key]["total_score"] = $total;
            }

            $course_item = $course_item[0];
            $class_item = $class_item[0];

            //set more values
            $average_score = $overall_score / count($scores_array);
            $course_name = ucwords($course_item->name);
            $course_code = strtoupper($course_item->course_code);
            
            // session
            $session_array = $this->session->get("terminal_report_{$report->class_id}_{$report->course_id}");

            // get the class teacher id
            $teacher_key = array_search("Teacher ID", $session_array["headers"]);
            $teacher_ids = $session_array["students"][0][$teacher_key];

            // set a new log id
            $report_id = strtoupper(random_string("alnum", 16));
            $isFound = false;

            // loop through the list and insert the record
            foreach($scores_array as $key => $student) {
                
                // confirm if there is an existing record that has already been approved
                $check = $this->pushQuery("a.scores, a.total_score, a.average_score, 
                    a.report_id, a.class_position, u.name AS student_name, a.status", 
                    "grading_terminal_scores a LEFT JOIN users u ON u.unique_id = a.student_unique_id", 
                    "a.student_unique_id='{$key}' AND a.course_id='{$report->course_id}'
                    AND a.academic_year = '{$params->academic_year}' AND a.academic_term='{$params->academic_term}' AND a.client_id = '{$params->clientId}'");
            
                // if there is no existing record
                if(empty($check)) {
                    // execute the statement
                    $this->insert_stmt->execute([
                        $report->class_id, $class_item->name,
                        $report->course_id, $course_name, $course_code, json_encode($student["marks"]),
                        $student["total_score"], $average_score, $teacher_ids, $student["remarks"], $params->userId,
                        $params->academic_year, $params->academic_term, $key, $params->clientId, $report_id
                    ]);
                    $data = "The Terminal Report was successfully inserted.";
                    // log the user activity
                    $this->userLogs("terminal_report", $key, json_encode($student), "{$params->userData->name} uploaded the terminal report for {$params->academic_term} {$params->academic_year} Academic Year of Student With ID: {$key}", $params->userId);
                } else {
                    // is found 
                    $isFound = true;

                    // get the upload id
                    $report_id = $check[0]->report_id;

                    // ensure it hasnt been approved
                    if($check[0]->status === "Saved") {
                        // execute the statement
                        $this->update_stmt->execute([
                            $report->class_id, $class_item->name, $report_id, $course_name, 
                            $course_code, json_encode($student["marks"]),
                            $student["total_score"], $average_score, $teacher_ids, $student["remarks"],
                            $params->academic_year, $params->academic_term, $key, $report->course_id, $params->clientId
                        ]);
                        // log the user activity
                        $this->userLogs("terminal_report", $key, $check[0], "{$params->userData->name} updated the terminal report for {$params->academic_term} {$params->academic_year} Academic Year of <strong>{$check[0]->student_name}</strong> With ID: {$key}", $params->userId);
                    }
                    $data = "The Terminal Report was successfully updated.";
                }
            }

            // if there is an existing record
            if($isFound) {

                // insert the activity into the cron_scheduler
                $query = $this->db->prepare("UPDATE cron_scheduler SET status = ?, active_date = now() WHERE item_id = ? AND cron_type = ?");
                $query->execute([0, $report_id, "terminal_report"]);

                // log the information
                $log_stmt = $this->db->prepare("UPDATE grading_terminal_logs 
                        SET class_name = ?, course_name = ?, date_modified = now(), course_code = ?
                    WHERE report_id = ? AND client_id = ? AND course_id = ? AND class_id = ? AND 
                        academic_year = ? AND academic_term = ?
                ");
                $log_stmt->execute([
                    $class_item->name, $course_name, strtoupper($course_item->course_code), $report_id, 
                    $params->clientId, $report->course_id, 
                    $report->class_id, $params->academic_year, $params->academic_term
                ]);
                // insert the activity into the cron_scheduler
                $query = $this->db->prepare("INSERT INTO cron_scheduler SET item_id = ?, user_id = ?, cron_type = ?, active_date = now()");
                $query->execute([$report_id, $params->userId, "terminal_report"]);
            } else {

                // log the information
                $log_stmt = $this->db->prepare("INSERT INTO grading_terminal_logs SET report_id = ?, client_id = ?, 
                    class_id = ?, class_name = ?, course_id = ?, course_name = ?, academic_year = ?, academic_term = ?,
                    created_by = ?, course_code = ?");
                $log_stmt->execute([
                    $report_id, $params->clientId, $report->class_id, $class_item->name, 
                    $report->course_id, $course_name, $params->academic_year, 
                    $params->academic_term, $params->userId, strtoupper($course_item->course_code)
                ]);

                // insert the activity into the cron_scheduler
                $query = $this->db->prepare("INSERT INTO cron_scheduler SET item_id = ?, user_id = ?, cron_type = ?, active_date = now()");
                $query->execute([$report_id, $params->userId, "terminal_report"]);
            }

            // delete the session
            $this->session->remove("terminal_report_{$report->class_id}_{$report->course_id}");

            return $data;

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Modify the status of the Terminal Report
     * 
     * @return Array
     */
    public function modify(stdClass $params) {

        try {
            
            // confirm that the label is an array variable
            if(!is_array($params->label)) {
                return ["code" => 203, "data" => "Sorry! The label variable must be a valid array."];
            }

            // ensure that the action and report_id were parsed
            if(!isset($params->label["action"]) || !isset($params->label["report_id"])) {
                return ["code" => 203, "data" => "Sorry! Action and Report ID are required."];
            }

            // set the action to capitalize
            $action = ucfirst($params->label["action"]);
            $report_id = $params->label["report_id"];

            // check the action to perform
            if(!in_array($action, ["Submit", "Cancel", "Approve"])) {
                return ["code" => 203, "data" => "Sorry! An invalid action was requested."];
            }

            // confirm if the report contains the user id as well
            $split = explode("_", $report_id);

            if(isset($split[1])) {
                $report_id = xss_clean($split[0]);
                $student_id = xss_clean($split[1]);
            }

            // get the details of the terminal report
            $check = $this->pushQuery("a.status, a.course_name, a.class_name, a.course_code, a.course_id,
                (SELECT COUNT(*) FROM grading_terminal_scores b WHERE b.report_id = a.report_id) AS students_count", 
                "grading_terminal_logs a", "a.report_id='{$report_id}' LIMIT 1");

            if(empty($check)) {
                return ["code" => 203, "data" => "Sorry! An invalid report id was parsed."];
            }

            // set the report
            $where_clause = "";
            $report = $check[0];
            $status = $report->status;
            $students_count = $report->students_count;

            // if the student id was parsed
            if(isset($student_id)) {
                // confirm that the student id is valid
                $student_check = $this->pushQuery("a.status", 
                    "grading_terminal_scores a", "a.report_id='{$report_id}' AND a.student_item_id='{$student_id}' LIMIT 1");
                
                // if empty then return false
                if(empty($student_check)) {
                    return ["code" => 203, "data" => "Sorry! An invalid result id was parsed."];
                }
                $student_info = $student_check[0];
                $status = $student_info->status;
                $students_count = 1;
                $where_clause = " AND student_item_id='{$student_id}'";
            }

            // continue to process the user request
            if(in_array($status, ["Approved", "Cancelled"])) {
                return ["code" => 203, "data" => "Sorry! The result has been {$status} and cannot be modified."];
            }

            // run more checks
            if(($action === "Approve")) {

                // confirm that the report has already been submitted
                if(($status !== "Submitted")) {
                    return ["code" => 203, "data" => "Sorry! The result has not yet been submitted by the Teacher hence cannot be approved."];
                }

                // run this section if the student id was not parsed
                if(!isset($student_id)) {
                    // approve the report
                    $stmt = $this->db->prepare("UPDATE grading_terminal_logs SET status = ? WHERE report_id = ? LIMIT 1");
                    $stmt->execute(["Approved", $report_id]);
                }

                // update the student marks as well
                $stmt = $this->db->prepare("UPDATE grading_terminal_scores SET date_approved = now(), status = ? WHERE report_id = ? AND status = ? AND course_id = ? {$where_clause} LIMIT {$students_count}");
                $stmt->execute(["Approved", $report_id, "Submitted", $report->course_id]);

                // log the user activity
                $this->userLogs("report_result", $report_id, null, "{$params->userData->name} approved the results of <strong>{$report->course_name} ({$report->course_code})</strong> for <strong>{$report->class_name}</strong>", $params->userId);
            }

            // submit a report
            elseif(($action === "Submit")) {

                // confirm that the report has already been submitted
                if(($status === "Submitted")) {
                    return ["code" => 203, "data" => "Sorry! The result has already been submitted by the Teacher hence cannot repeat same action."];
                }
                
                // run this section if the student id was not parsed
                if(!isset($student_id)) {
                    // approve the report
                    $stmt = $this->db->prepare("UPDATE grading_terminal_logs SET status = ? WHERE report_id = ? LIMIT 1");
                    $stmt->execute(["Submitted", $report_id]);
                }
                // update the student marks as well
                $stmt = $this->db->prepare("UPDATE grading_terminal_scores SET date_submitted = now(), status = ? WHERE report_id = ? AND course_id = ? {$where_clause} LIMIT {$students_count}");
                $stmt->execute(["Submitted", $report_id, $report->course_id]);

                // log the user activity
                $this->userLogs("report_result", $report_id, null, "{$params->userData->name} submitted the results of <strong>{$report->course_name} ({$report->course_code})</strong> for <strong>{$report->class_name}</strong>", $params->userId);
            }

            // submit a report
            elseif(($action === "Cancel")) {
                
                // confirm that the report has already been submitted
                if(($status !== "Approved")) {
                    return ["code" => 203, "data" => "Sorry! The result has not yet been Approved hence action cannot be reversed."];
                }

                // run this section if the student id was not parsed
                if(!isset($student_id)) {
                    // approve the report
                    $stmt = $this->db->prepare("UPDATE grading_terminal_logs SET status = ? WHERE report_id = ? LIMIT 1");
                    $stmt->execute(["Cancelled", $report_id]);
                }

                // update the student marks as well
                $stmt = $this->db->prepare("UPDATE grading_terminal_scores SET status = ? WHERE report_id = ? AND course_id = ? {$where_clause} LIMIT {$students_count}");
                $stmt->execute(["Cancelled", $report_id, $report->course_id]);

                // log the user activity
                $this->userLogs("report_result", $report_id, null, "{$params->userData->name} cancelled the results of <strong>{$report->course_name} ({$report->course_code})</strong> for <strong>{$report->class_name}</strong>", $params->userId);
            }

            // return the success message
            return [
                "code" => 200,
                "data" => "Congrats! The request was successfully processed.",
                "additional" => [
                    "disable" => !isset($student_id) ? "record" : "student",
                    "href" => !isset($student_id) ? "{$this->baseUrl}results-upload/view" : "{$this->baseUrl}results-review/{$report_id}"
                ]
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Save the Class Results Set
     * 
     * Populate the Data and Save the new Data
     * 
     * @param       $params->label["record_type"]
     * @param       $params->label["record_id"]
     * @param       $params->label["results"]
     * 
     * @return Array
     */
    public function update_report(stdClass $params) {

        // confirm that the label is an array variable
        if(!is_array($params->label)) {
            return ["code" => 203, "data" => "Sorry! The label variable must be a valid array."];
        }

        // confirm that the results and record_id has been parsed
        if(!isset($params->label["results"], $params->label["record_id"])) {
            return ["code" => 203, "data" => "Sorry! No result set has been parsed."];
        }

        try {

            // report id
            $record_id = $params->label["record_id"];
            $record_type = $params->label["record_type"];

            // if the type is results
            if(in_array($record_type, ["results", "approve_results"])) {
                // get the details of the terminal report
                $check = $this->pushQuery("a.status, a.course_name, a.class_name, a.course_code, a.course_id, a.report_id,
                    (SELECT COUNT(*) FROM grading_terminal_scores b WHERE b.report_id = a.report_id AND a.status='Submitted') AS students_count", 
                    "grading_terminal_logs a", "a.report_id='{$record_id}' LIMIT 1");
            }
            // if the student is empty
            elseif($record_type === "student") {
                // get the details of the terminal report
                $check = $this->pushQuery("a.status, a.student_name, a.report_id, a.course_name, a.class_name, a.course_code, a.course_id", 
                    "grading_terminal_scores a", "a.student_item_id='{$record_id}' LIMIT 1");
            }

            // confirm if the result is not empty
            if(empty($check)) {
                return ["code" => 203, "data" => "Sorry! An invalid report/student id was parsed."];
            }

            // set the report
            $report = $check[0];
            $report_id = $report->report_id;
            $students_count = $report->students_count ?? 1;
            $student_name = $report->student_name ?? ("{$report->course_name} {$report->class_name}");

            // initial values
            $additional = [];
            $overall_score = 0;
            $scores_array = [];
            $students_list = $params->label["results"];

            // group the information in an array
            foreach($students_list as $stu_id => $score) {
                if(isset($score["marks"]) && is_array($score["marks"])) {
                    $count = 0;
                    foreach($score["marks"] as $item => $mark) {
                        if(is_numeric($mark)) {
                            $scores_array[$stu_id]["marks"][$count]["item"] = $item;
                            $scores_array[$stu_id]["marks"][$count]["score"] = $mark;
                            $overall_score += $mark;
                            $count++;
                        }
                    }
                }
                // // append the teachers remarks to the array
                $scores_array[$stu_id]["remarks"] = $score["remarks"];
            }

            // get the sum for all scores
            foreach($scores_array as $key => $score) {
                $total = array_column($score["marks"], "score");
                $total = !empty($total) ? array_sum($total) : 0;
                $scores_array[$key]["total_score"] = $total;
            }

            //set more values
            $average_score = $overall_score / count($scores_array);

            // update query
            $update_stmt = $this->db->prepare("UPDATE grading_terminal_scores SET 
                scores = ?, total_score = ?, date_modified = now(), average_score = ?, class_teacher_remarks = ?
                WHERE academic_year = ? AND academic_term = ? AND student_item_id = ? AND report_id = ? AND client_id = ?
            ");
            
            // loop through the list and insert the record
            foreach($scores_array as $key => $student) {
                
                // execute the statement
                $update_stmt->execute([
                    json_encode($student["marks"]), $student["total_score"], $average_score, $student["remarks"],
                    $params->academic_year, $params->academic_term, $key, $report_id, $params->clientId
                ]);
                // log the user activity
                $this->userLogs("terminal_report", $key, null, "{$params->userData->name} updated the terminal report for {$params->academic_term} {$params->academic_year} Academic Year of <strong>{$student_name}</strong> With ID: {$key}", $params->userId);
                
            }

            // approve the results
            if($record_type === "approve_results") {
                // update the student marks as well
                $stmt = $this->db->prepare("UPDATE grading_terminal_scores SET date_approved = now(), status = ? WHERE report_id = ? AND status = ? AND course_id = ? LIMIT {$students_count}");
                $stmt->execute(["Approved", $report_id, "Submitted", $report->course_id]);

                // update the student marks as well
                $stmt = $this->db->prepare("UPDATE grading_terminal_logs SET status = ? WHERE report_id = ? AND status = ? AND course_id = ? LIMIT {$students_count}");
                $stmt->execute(["Approved", $report_id, "Submitted", $report->course_id]);

                // set the redirect url
                $additional["href"] = "{$this->baseUrl}results-review/{$report_id}";
            }

            return [
                "data" => "The result marks was successfully updated.",
                "additional" => $additional
            ];


        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Generate Report
     * 
     * @param       $params->academic_term
     * @param       $params->academic_year
     * @param       $params->class_id
     * @param       $params->student_id
     * 
     * @return Array
     */
    public function generate(stdClass $params) {

        try {

            // global variable
            global $accessObject;

            // set the student id if the user do not have the permission to view the report for the entire class
            if(isset($params->student_id)) {
                $params->student_id = $params->student_id;
            } elseif($accessObject->hasAccess("generate", "results") && !isset($params->student_id)) {
                $params->student_id = null;
            } else {
                $params->student_id = $accessObject->userId;
            }

            // if the class id was not found
            if(empty($params->class_id) || empty($this->iclient->client_preferences)) {
                return [
                    "data" => [
                        "sheets" => [
                            $this->permission_denied
                        ]
                    ]
                ];
            }

            // set the parameters
            $param = (object) [      
                "group_by_student" => true,
                "class_id" => $params->class_id,
                "academic_year" => $params->academic_year ?? $this->academic_year,
                "academic_term" => $params->academic_term ?? $this->academic_term,
                "student_item_id" => isset($params->student_id) && !empty($params->student_id) && ($params->student_id !== "null") ? $params->student_id : null,
            ];
            $report_data = $this->result_score_list(null, $param);

            // get the user attendance results
            $attendance_param = (object) [
                "clientId" => $this->clientId, 
                "user_types_list" => ["student"], "the_user_type" => "student",
                "period" => "this_term", "is_finalized" => 1, "is_present_check" => 1,
                "start_date" => $this->this_term_starts, "end_date" => $this->this_term_ends,
            ];
            // create a new object of the attendance class
            $attendanceObj = load_class("attendance", "controllers", $params);

            // init loop
            $students = [];
            $bg_color = "#777882";//"#03a9f4";
            $academics = $this->iclient->client_preferences->academics;
            $grading = $this->iclient->grading_structure->columns;
            $interpretation = $this->iclient->grading_system;

            // set the grading column
            $column_count = 0;
            $grading_column = "";

            // loop through the grading columns
            foreach($grading as $key => $value) {
                // increment the count
                $column_count++;
                // grading column
                $grading_column .= "<td align=\"center\" width=\"11%\">".strtoupper($key)."</td>";
            }

            // loop through the report set
            foreach($report_data as $key => $student) {
                
                // get the student attendance logs
                $attendance_param->the_current_user_id = $key;
                $attendance_log = $attendanceObj->range_summary($attendance_param)->summary;

                // set the information
                $table = "<table width=\"100%\" cellspacing=\"5px\" cellpadding=\"5px\" style=\"background-color:{$bg_color}; color:#fff\">";
                // get the student information
                $table .= "<tr>";
                $table .= "<td><strong>".strtoupper($student["data"]["student_name"])."</strong></td>";
                $table .= "<td><strong style=\"text-transform:uppercase\">YEAR: {$student["data"]["academic_year"]} / {$student["data"]["academic_term"]}</strong></td>";
                $table .= "<td><strong>GRADE: {$student["data"]["class_name"]}</strong></td>";
                $table .= "<td><strong>AGE: {$student["data"]["student_age"]}</strong></td>";
                $table .= "</tr>";
                $table .= "</table>";

                // set the address and the other information
                $table .= "<br><br><table cellpadding=\"5\" width=\"100%\">";
                $table .= "<tr>
                    <td width=\"25%\" valign=\"top\">
                        <div style=\"background-color:{$bg_color}; padding:5px; color:#fff; height:80px\">Hello</div>
                        <div style=\"padding:5px;\"><strong style=\"color:#6777ef\">CLASS AVERAGE: {$student["data"]["average_score"]}</strong></div>
                        <div style=\"padding:5px; text-transform:uppercase;\"><strong>SCHOOL RESUMES ON:<br><span style=\"color:#6777ef\">".date("jS M Y", strtotime($academics->next_term_starts))."</span></strong></div>
                    </td>";
                $table .= "
                    <td align=\"center\" width=\"45%\">
                        <img src=\"{$this->baseUrl}{$this->iclient->client_logo}\" width=\"100px\"><br>
                        <span style=\"padding:0px; font-weight:bold; font-size:20px; margin:0px;\">".strtoupper($this->iclient->client_name)."</span><br>
                        <span style=\"padding:0px; font-weight:bold; margin:0px;\">{$this->iclient->client_address}</span><br>
                        <span style=\"padding:0px; font-weight:bold; margin:0px;\">{$this->iclient->client_contact} ".(!$this->iclient->client_secondary_contact ? " / {$this->iclient->client_secondary_contact}" : null)."</span>
                    </td>";
                $table .= "<td style=\"background-color:{$bg_color}; padding:5px; color:#fff;\" align=\"center\" valign=\"top\" width=\"30%\">
                    <div style=\"50px\">
                        <strong>MESSAGE</strong><br>
                        Please visit www.myschoolgh.com/report/{$student["data"]["unique_id"]} for a
                        graphical analysis of this report.
                    </div></td>
                    </tr>";
                $table .= "</table>";
                $table .= "<br><br><table style=\"font-size:11px\" cellpadding=\"5\" width=\"100%\" border=\"1\">";
                $table .= "<tr style=\"font-weight:bold;font-size:15px;background-color:{$bg_color};color:#fff;\">";
                $table .= "<td align=\"center\" colspan=\"".($column_count + 2)."\">END OF TERM REPORT CARD</td>";
                $table .= "</tr>";
                $table .= "<tr style=\"font-weight:bold\">";
                $table .= "<td width=\"25%\">SUBJECT</td>";
                $table .= $grading_column;
                $table .= "<td width=\"18%\">TEACHER</td>";
                $table .= "<td>TEACHER'S COMMENT</td>";
                $table .= "</tr>";

                // // get the results submitted by the teachers for each subject
                foreach($student["sheet"] as $score) {
                    $table .= "<tr>";
                    $table .= "<td>{$score->course_name}</td>";
                    // get the scores
                    foreach($score->scores as $s_score) {
                        $table .= "<td align=\"center\">{$s_score}</td>";
                    }
                    $table .= "<td align=\"center\">{$score->total_score}</td>";
                    $table .= "<td>".strtoupper($score->teachers_name)."</td>";
                    $table .= "<td>{$score->class_teacher_remarks}</td>";
                    $table .= "</tr>";
                }
                $table .= "</table>";

                // set the grading system
                $table .= "<br><br><table cellpadding=\"5px\" border=\"0\" width=\"100%\">";
                $table .= "<tr>";
                $table .= "<td align=\"center\" width=\"35%\">";
                $table .= "<span style=\"font-weight:bold; font-size:20px\">GRADING SYSTEM</span><br>";
                $table .= "<table style=\"font-size:11px\" align=\"left\" cellpadding=\"5px\" border=\"0\" width=\"100%\">\n";
                $table .= "<tr style=\"font-weight:bold\">";
                $table .= "<td>Marks in Percentage (%)</td>";
                $table .= "<td>Interpretation</td>";
                $table .= "</tr>";
                // loop through the grading system
                foreach($interpretation as $ikey => $ivalue) {
                    $table .= "<tr>";
                    $table .= "<td>{$ivalue->start} - {$ivalue->end}</td>";
                    $table .= "<td>{$ivalue->interpretation}</td>";
                    $table .= "</tr>";
                }
                $table .= "</table>";
                $table .= "</td>";
                $table .= "<td width=\"33%\">";
                $table .= "<table width=\"100%\" cellpadding=\"5px\" border=\"1\">";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\" align=\"center\" colspan=\"2\">ATTENDANCE</td>";
                $table .= "</tr>";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\">PRESENT</td>";
                $table .= "<td style=\"font-weight:bold\">".($attendance_log["Present"] ?? 0)."</td>";
                $table .= "</tr>";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\">ABSENT</td>";
                $table .= "<td style=\"font-weight:bold\">".($attendance_log["Absent"] ?? 0)."</td>";
                $table .= "</tr>";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\">TERM DAYS</td>";
                $table .= "<td style=\"font-weight:bold\">".($attendance_log["Term"] ?? 0)."</td>";
                $table .= "</tr>";
                $table .= "</table>";
                $table .= "</td>";
                $table .= "<td width=\"32%\">";
                 $table .= "<table width=\"100%\" cellpadding=\"5px\" border=\"1\">";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\" align=\"center\" colspan=\"2\">NEXT TERM FEES</td>";
                $table .= "</tr>";
                $table .= "</table>";
                $table .= "</td>";
                $table .= "</tr>";
                $table .= "</table>";

                // append to the students list
                $students[$key] = [
                    "report" => $table,
                    "attendance" => $attendance_log
                ];
            }

            return [
                "data" => [
                    "sheets" => $students
                ]
            ];

        } catch(PDOException $e) {}

    }
}