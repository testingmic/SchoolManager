<?php 
class Terminal_reports extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Uploads List
     * 
     * List all uploaded terminal reports by a user
     * 
     * @return Array
     */
    public function uploads_list(stdClass $params) {
        
        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        try {

            $stmt = $this->db->prepare("SELECT a.*, u.name AS fullname, u.unique_id AS user_unique_id,
                    (SELECT COUNT(*) FROM grading_terminal_scores b WHERE b.upload_id = a.upload_id) AS students_count,
                    (SELECT b.average_score FROM grading_terminal_scores b WHERE b.upload_id = a.upload_id LIMIT 1) AS overall_score
                FROM grading_terminal_logs a
                LEFT JOIN users u ON u.item_id = a.created_by
                WHERE 1 ORDER by a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
    
        } catch(PDOException $e) {}

    }

    /**
     * Check Existence
     * 
     * Confirm that there is an existing record
    */
    public function check_existence(stdClass $params) {
        $check = $this->pushQuery("status", "grading_terminal_logs", "course_id = '{$params->course_id}' AND class_id='{$params->class_id}' AND client_id='{$params->clientId}'");
        if(!empty($check)) {
            if($check[0]->status === "Pending") {
                return ["code" => 200, "data" => "There is an existing record, trying to upload a new set of data will replace the existing one."];
            } elseif($check[0]->status === "Approved") {
                return ["code" => 203, "data" => "Sorry! This report has already been approved hence cannot be updated."];
            } elseif($check[0]->status === "Submitted") {
                return ["code" => 203, "data" => "Sorry! This report has been submitted pending approval hence cannot be updated."];
            }
        } else {
            return ["code" => 200, "data" => "You can proceed to upload the terminal report of the class"];
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

        $csv_file .= "Class Teacher ID,";
        $csv_file .= "Class Teacher Remarks\n";

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
        $headers[] = "Class Teacher ID";
        $headers[] = "Class Teacher Remarks";

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
                if(in_array($file_headers[$kkey], array_keys($columns["columns"]))) {
                    $column = create_slug($file_headers[$kkey], "_");
                    $report_table .= "<td><input ".($columns["columns"][$file_headers[$kkey]] == "100" ? "disabled='disabled' data-input_total_id='{$key}'" : "data-input_type_q='marks' data-input_type='score' data-input_row_id='{$key}'" )." class='form-control font-18 text-center' name='{$column}' min='0' max='1000' type='number' value='{$kvalue}'></td>";
                } elseif($file_headers[$kkey] == "Class Teacher Remarks") {
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
            $teacher_key = array_search("Class Teacher ID", $session_array["headers"]);
            $teacher_ids = $session_array["students"][0][$teacher_key];

            // prepare the statement
            $insert_stmt = $this->db->prepare("INSERT INTO grading_terminal_scores SET 
                class_id = ?, class_name = ?,
                course_id = ?, course_name = ?, course_code = ?, scores = ?, total_score = ?, 
                average_score = ?, teacher_ids = ?, class_teacher_remarks = ?, created_by = ?,
                academic_year = ?, academic_term = ?, student_unique_id = ?, client_id = ?, upload_id = ?
            ");

            // prepare the statement
            $update_stmt = $this->db->prepare("UPDATE grading_terminal_scores SET 
                    class_id = ?, class_name = ?, upload_id = ?, course_name = ?, 
                    course_code = ?, scores = ?, total_score = ?, 
                    average_score = ?, teacher_ids = ?, class_teacher_remarks = ?
                WHERE academic_year = ? AND academic_term = ? AND student_unique_id = ? AND course_id = ? AND client_id = ?
            ");

            // set a new log id
            $upload_id = random_string("alnum", 16);
            $isFound = false;

            // loop through the list and insert the record
            foreach($scores_array as $key => $student) {
                
                // confirm if there is an existing record that has already been approved
                $check = $this->pushQuery("a.scores, a.total_score, a.average_score, 
                    a.upload_id, a.class_position, u.name AS student_name, a.status", 
                    "grading_terminal_scores a LEFT JOIN users u ON u.unique_id = a.student_unique_id", 
                    "a.student_unique_id='{$key}' AND a.course_id='{$report->course_id}'
                    AND a.academic_year = '{$params->academic_year}' AND a.academic_term='{$params->academic_term}' AND a.client_id = '{$params->clientId}'");
            
                // if there is no existing record
                if(empty($check)) {
                    // execute the statement
                    $insert_stmt->execute([
                        $report->class_id, $class_item->name,
                        $report->course_id, $course_name, $course_code, json_encode($student["marks"]),
                        $student["total_score"], $average_score, $teacher_ids, $student["remarks"], $params->userId,
                        $params->academic_year, $params->academic_term, $key, $params->clientId, $upload_id
                    ]);
                    $data = "The Terminal Report was successfully inserted.";
                    // log the user activity
                    $this->userLogs("terminal_report", $key, json_encode($student), "{$params->userData->name} uploaded the terminal report for {$params->academic_term} {$params->academic_year} Academic Year of Student With ID: {$key}", $params->userId);
                } else {
                    // is found 
                    $isFound = true;
                    $upload_id = $check[0]->upload_id;
                    // ensure it hasnt been approved
                    if($check[0]->status === "Saved") {
                        // execute the statement
                        $update_stmt->execute([
                            $report->class_id, $class_item->name, $upload_id, $course_name, 
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
                $query->execute([0, $upload_id, "terminal_report"]);

                // log the information
                $log_stmt = $this->db->prepare("UPDATE grading_terminal_logs 
                        SET class_name = ?, course_name = ?, course_code = ?
                    WHERE upload_id = ? AND client_id = ? AND course_id = ? AND class_id = ? AND 
                        academic_year = ? AND academic_term = ?
                ");
                $log_stmt->execute([
                    $class_item->name, $course_name, strtoupper($course_item->course_code), $upload_id, 
                    $params->clientId, $report->course_id, 
                    $report->class_id, $params->academic_year, $params->academic_term
                ]);
            } else {

                // log the information
                $log_stmt = $this->db->prepare("INSERT INTO grading_terminal_logs SET upload_id = ?, client_id = ?, 
                    class_id = ?, class_name = ?, course_id = ?, course_name = ?, academic_year = ?, academic_term = ?,
                    created_by = ?, course_code = ?");
                $log_stmt->execute([
                    $upload_id, $params->clientId, $report->class_id, $class_item->name, 
                    $report->course_id, $course_name, $params->academic_year, 
                    $params->academic_term, $params->userId, strtoupper($course_item->course_code)
                ]);

                // insert the activity into the cron_scheduler
                $query = $this->db->prepare("INSERT INTO cron_scheduler SET item_id = ?, user_id = ?, cron_type = ?, active_date = now()");
                $query->execute([$upload_id, $params->userId, "terminal_report"]);
            }

            // delete the session
            $this->session->remove("terminal_report_{$report->class_id}_{$report->course_id}");

            return $data;

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

}
