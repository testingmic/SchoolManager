<?php 
class Terminal_reports extends Myschoolgh {

    public $iclient;

    public function __construct($params = null) {
        
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

    }

    /**
     * Uploads List
     * 
     * List all uploaded terminal reports by a user
     * 
     * @return Array
     */
    public function results_list(stdClass $params) {
        
        // additional variables
        global $defaultClientData, $clientPrefs;

        // set some values
        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        $params->query = 1;
        $grading = !empty($defaultClientData->grading_structure) ? $defaultClientData->grading_structure->columns : [];
        
        // get the teachers id
        if(in_array($params->userData->user_type, ["teacher", "parent", "student"])) {
            if($params->userData->user_type == "teacher") {
                $params->teacher_id = !isset($params->teacher_id) ? $params->userData->unique_id : $params->teacher_id;
            }
            elseif($params->userData->user_type == "student") {
                $params->class_id = !isset($params->class_id) ? $params->userData->class_id : $params->class_id;
            }
        }
        
        // append some additional queries
        $params->query .= isset($params->created_by) && !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= isset($params->class_id) && !empty($params->class_id) ? " AND a.class_id='{$params->class_id}'" : null;
        $params->query .= isset($params->teacher_id) && !empty($params->teacher_id) ? " AND (a.teacher_ids='{$params->teacher_id}' OR a.created_by='{$params->userData->user_id}')" : null;
        $params->query .= isset($params->course_id) && !empty($params->course_id) ? " AND a.course_id='{$params->course_id}'" : null;
        $params->query .= isset($params->course_code) && !empty($params->course_code) ? " AND a.course_code='{$params->course_code}'" : null;
        $params->query .= isset($params->status) && !empty($params->status) ? " AND a.status='{$params->status}'" : null;
        $params->query .= isset($params->result_id) && !empty($params->result_id) ? " AND a.report_id='{$params->result_id}'" : null;

        // query the academic year and term
        $params->academic_year = isset($params->academic_year) && !empty($params->academic_year) ? $params->academic_year : $this->academic_year;
        $params->academic_term = isset($params->academic_term) && !empty($params->academic_term) ? $params->academic_term : $this->academic_term;

        // set the academic year and term
        $params->query .= " AND a.academic_year='{$params->academic_year}'";
        $params->query .= " AND a.academic_term='{$params->academic_term}'";

        try {

            $stmt = $this->db->prepare("SELECT a.*, u.name AS fullname, u.unique_id AS user_unique_id, u.item_id AS student_item_id,
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

                $result->grading = !empty($result->grading) ? json_decode($result->grading) : $grading;
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
     * @param String $result_id
     * @param String $where
     * 
     * @return Array
     */
    public function result_score_list($result_id = null, $where = null, $get_percentage = false) {

        try {

            global $usersClass, $defaultUser;
            
            $groupStudent = false;
            $where_clause = !empty($result_id) ? "a.report_id = '{$result_id}'" : $where;

            if(!empty($where) && is_object($where)) {
                $where_clause = "1";
                $groupStudent = $where->group_by_student;
                foreach($where as $key => $value) {
                    $where_clause .= !empty($value) && $key !== "group_by_student" ? " AND a.{$key}='{$value}'" : null;
                }
            }

            // if the user is a student
            if($defaultUser->user_type == "student") {
                $where_clause .= " AND a.student_item_id IN {$this->inList($defaultUser->user_id)}";
            }

            // if the user is a parent
            if($defaultUser->user_type == "parent") {

                // if the wards list is empty then return
                if(empty($defaultUser->wards_list_ids)) return [];

                // append the wards list to the where clause
                $where_clause .= " AND a.student_item_id IN {$this->inList($defaultUser->wards_list_ids)}";
            }

            // prepare the query
            $stmt = $this->db->prepare("SELECT 
                    a.*, (
                        SELECT CONCAT(
                            COALESCE(u.name,'NULL'),'|',COALESCE(u.date_of_birth,'NULL'),'|',
                            COALESCE(u.unique_id,'NULL'),'|',COALESCE(u.guardian_id,'NULL')
                        ) 
                        FROM users u WHERE u.item_id = a.student_item_id LIMIT 1
                    ) AS student_info
                FROM grading_terminal_scores a
                WHERE {$where_clause} LIMIT 200
            ");
            $stmt->execute();
            
            $getPercentage = (bool) $get_percentage;
            $getItem = $getPercentage ? "percent_value" : "score";

            $data = [];

            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                $scores = json_decode($result->scores, true);
                $scores_array = [];

                // loop through the user scores
                if(!empty($scores) && is_array($scores)) {
                    foreach($scores as $key => $score) {
                        $scores_array[$scores[$key]["item"]] = $score;
                    }
                }
                
                // set the scores as an array
                $result->scores = $scores_array;

                // breakdown the student info
                $result->student_info = $this->stringToArray($result->student_info, "|", ["student_name", "date_of_birth", "unique_id", "guardian_id"]);
                
                // if the request is to group by each student
                if($groupStudent) {
                    
                    // if the student array has not been set already
                    if(!isset($data[$result->student_item_id]["data"])) {

                        // set the guardian id
                        $guardian = $result->student_info["guardian_id"] ?? null;

                        // set the data
                        $data[$result->student_item_id]["data"] = [
                            "student_name" => $result->student_info["student_name"],
                            "unique_id" => $result->student_info["unique_id"],
                            "average_score" => $result->average_score,
                            "total_score" => $result->total_score,
                            "total_percentage" => $result->total_percentage,
                            "class_name" => $result->class_name,
                            "guardian_list" => $usersClass->guardian_list($guardian, $result->client_id, true),
                            "date_of_birth" => $result->student_info["date_of_birth"],
                            "student_age" => convert_to_years($result->student_info["date_of_birth"], date("Y-m-d")),
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

        } catch(PDOException $e) {
            return [];
        }

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
            class_id='{$params->class_id}' AND client_id='{$params->clientId}' LIMIT 1"
        );
            
        if(!empty($check)) {
            if($check[0]->status === "Pending") {
                return ["code" => 200, "data" => "There is an existing record, trying to upload a new set of data will replace the existing one."];
            } elseif($check[0]->status === "Approved") {
                return ["code" => 400, "data" => "Sorry! This result has already been approved hence cannot be updated."];
            } elseif($check[0]->status === "Submitted") {
                return ["code" => 400, "data" => "Sorry! This result has been submitted pending approval hence cannot be updated."];
            }
        } else {
            return ["code" => 200, "data" => "You can proceed to upload the results of the class."];
        }
    }

    /**
     * Download the CSV File
     * 
     * @return Array
     */
    public function download_csv(stdClass $params) {

        // set the global variable
        global $defaultClientData;

        // get the client data
        $client_data = $defaultClientData;

        // old record
        $class_name = $this->pushQuery("id, item_id, name", "classes", "item_id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        // if empty then return
        if(empty($class_name)) {
            return ["code" => 400, "data" => "Sorry! An invalid class id was supplied."];
        }

        // old record
        $course_item = $this->pushQuery("id, item_id, name, course_code", "courses", "item_id='{$params->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        // if empty then return
        if(empty($course_item)) {
            return ["code" => 400, "data" => "Sorry! An invalid course id was supplied."];
        }

        // get the students for the classes
        $students_list = $this->pushQuery("name, unique_id, item_id", "users", 
            "class_id='{$class_name[0]->id}' AND client_id='{$params->clientId}' 
            AND user_status='Active' AND user_type='student' LIMIT {$this->global_limit}"
        );

        // if empty then return
        if(empty($students_list)) {
            return ["code" => 400, "data" => "Sorry! No student was found under the specified Class."];
        }

        $course_item = $course_item[0];
        $course_name = ucwords($course_item->name);

        $csv_file = "STUDENT,STUDENT ID,";
        $csv_file .= "SUBJECT,";

        $grading_sba = $client_data->grading_sba ?? [];

        // get the grading structure
        if(!empty($grading_sba)) {
            foreach($grading_sba as $keyName => $item) {
                if($item['sba_checkbox'] == 'true') {
                    $csv_file .= strtoupper("{$keyName} - {$item['percentage']}%,");
                }
            }
        }

        $csv_notes = "";

        // get the grading structure
        if(!empty($client_data->grading_structure)) {
            $columns = $client_data->grading_structure;
            if(isset($columns->columns)) {
                $count = 0;
                foreach($columns->columns as $key => $column) {
                    $count++;
                    $percent = $column->percentage;
                    if($key == 'School Based Assessment') {
                        $csv_notes = "{$key} is {$column->percentage}% of the total scores";
                    }
                    if($key == 'Examination') {
                        $percent = 100;
                        $csv_notes = "{$key} will be calculated as {$column->percentage}% of the total raw examination score.";
                    }
                    $csv_file .= strtoupper("{$key} - {$percent}%,");
                }
            }
        }

        // $csv_file .= "TEACHER ID,";
        $csv_file .= "TEACHER REMARKS\n";
        $course_name = str_ireplace(", ", "", $course_name);

        // get the sba results only if the grading structure is not empty
        if(!empty($grading_sba)) {
            $sba_results = $this->perform_raw_query("SELECT a.student_id, b.assignment_type, b.course_id, SUM(b.grading) AS total_score, 
                    SUM(a.score) AS student_score
                FROM `assignments_submitted` a
                INNER JOIN assignments b ON b.item_id = a.assignment_id
                WHERE a.handed_in = 'Graded' AND b.academic_year = '{$params->academic_year}' AND b.academic_term = '{$params->academic_term}' AND b.course_id = '{$params->course_id}'
                GROUP BY a.student_id, b.assignment_type, b.course_id
                ORDER BY a.student_id;'");

            // regroup the results list by student id
            $sba_results_list = [];

            // loop through the sba results
            foreach($sba_results as $result) {

                // if the total percentage is not set then set it to 0
                if(!isset($sba_results_list[$result->student_id]['total_percentage'])) {
                    $sba_results_list[$result->student_id]['total_percentage'] = 0;
                }

                // raw score
                $sba_results_list[$result->student_id]['raw'][$result->assignment_type] = $result->student_score;

                // set the total score
                $sba_results_list[$result->student_id]['total'][$result->assignment_type] = $result->total_score;

                // grading value
                $grading_value = $grading_sba[$result->assignment_type] ?? [];
                $grading_value = $grading_value['percentage'] ?? 0;

                // calculate the percentage
                $percentage = $grading_value !== 0 ? ($result->student_score / $result->total_score) * $grading_value : 0;
                $sba_results_list[$result->student_id]['percentage'][$result->assignment_type] = round($percentage);

                // set the total percentage
                $sba_results_list[$result->student_id]['total_percentage'] += round($percentage);
                $sba_results_list[$result->student_id]['grading_value'][$result->assignment_type] = round($grading_value);

            }
        }

        // get the sba percentage
        $sbaPercentage = $client_data->grading_structure->columns->{"School Based Assessment"}->percentage ?? 0;

        // loop through the students
        foreach($students_list as $student) {
            $csv_file .= strtoupper($student->name).",{$student->unique_id},{$course_name},";
            $totalSba = 0;
            foreach($grading_sba as $keyName => $item) {
                if($item['sba_checkbox'] == 'true') {
                    if (isset($sba_results_list[$student->item_id])) {
                        $getRecord = $sba_results_list[$student->item_id];
                        $score = $getRecord['percentage'][$keyName] ?? 0;
                        $csv_file .= trim($score).",";
                        $totalSba += $score;
                    } else {
                        $csv_file .= "0,";
                    }
                }
            }
            if($totalSba > 0) {
                $csv_file .= round(($totalSba / 100) * $sbaPercentage) . ",";
            }
            $csv_file .= "\n";
        }

        $csv_file .= "\n{$csv_notes}\n";

        // upload file
        $temp_dir = "assets/uploads/results/{$params->clientId}/tmp/";
        
        // if not a directory then create it
        if(!is_dir($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        // set the name
        $filename = $temp_dir . create_slug($class_name[0]->name, "_")."_".create_slug($course_item->name, "_")."_results.csv";

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

        // set the global variable
        global $defaultClientData;

        if(!isset($params->report_file['tmp_name'])) {
            return ["code" => 400, "data" => "Sorry! Please select a file to upload."];
        }
    
        // reading tmp_file name
        $report_file = fopen($params->report_file['tmp_name'], 'r');

        // get the content of the file
        $csv_headers = fgetcsv($report_file);
        $complete_csv_data = [];

        //using while loop to get the information
        while($row = fgetcsv($report_file)) {
            // session data
            $complete_csv_data[] = $row;
        }

        // check the file that have been uploaded
        $headers = ["STUDENT","STUDENT ID","SUBJECT"];

        // get the client data
        $client_data = $defaultClientData;
        $columns = json_encode($client_data->grading_structure);
        $columns = json_decode($columns, true);

        // get the grading structure
        if(!empty($client_data->grading_structure)) {
            if(isset($columns["columns"])) {
                $count = 0;
                foreach($columns["columns"] as $key => $column) {
                    $count++;
                    $headers[] = strtoupper(trim($key));
                }

                // set new variables
                $_columns_new = [];
                foreach($columns["columns"] as $key => $column) {
                    $_columns_new[strtoupper($key)] = $column;
                }

                $columns["columns"] = $_columns_new;

            }
        }
        $grading_sba = $client_data->grading_sba ?? [];
        $sba_percentage = [];

        // get the grading structure
        if(!empty($grading_sba)) {
            foreach($grading_sba as $keyName => $item) {
                if($item['sba_checkbox'] == 'true') {
                    $headers[] = strtoupper($keyName);
                    $sba_percentage[$keyName] = $item['percentage'];
                }
            }
        }

        // $headers[] = "TEACHER ID";
        $headers[] = "TEACHER REMARKS";

        // set a new header
        $file_headers = [];
        foreach($csv_headers as $item) {
            // explode the text
            $_item = explode("-", $item);

            // change the headers to uppercase
            $h_name = trim($_item[0]);

            // if the string length is more than one
            if(strlen($h_name) > 1) {
                // append the array list
                $file_headers[] = $h_name;
            }
        }

        // loop through the headers and ensure that they all match
        foreach($file_headers as $item) {
            // change the header item to uppercase
            $h_item = strtoupper($item);

            // if not in array
            if(!in_array($h_item, $headers)) {
                return ["code" => 400, "data" => "Ensure you have uploaded the sample CSV File with the predefined headers."];
            }
        }
        
        // draw a table with the headers
        $report_table = "<div style='max-width: 100%' class='table-responsive trix-slim-scroll'>";
        $report_table .= "<table width='100%' class='table table-bordered font-13'>";
        $report_table .= "<thead>";
        
        foreach($file_headers as $item) {
            $item = $item == 'SCHOOL BASED ASSESSMENT' ? 'SBA' : $item;
            if(in_array($item, ['SUBJECT'])) continue;
            $report_table .= "<th class='text-center'>{$item}</th>";
        }
        $report_table .= "</thead>";
        $report_table .= "<tbody>";

        // set the files
        foreach($complete_csv_data as $key => $result) {
            if(empty($result[2]) && empty($result[3])) continue;
            $report_table .= "<tr>";
            foreach($result as $kkey => $kvalue) {
                if(in_array($kkey, [2])) continue;
                // if the key is in the array list
                if(in_array($file_headers[$kkey], array_keys($columns["columns"]))) {
                    $column = create_slug($file_headers[$kkey], "_");
                    $readOnly = in_array($column, ['school_based_assessment']) ? "readonly='readonly'" : "";
                    $report_table .= "<td><input style='min-width:100px;' ".($columns["columns"][$file_headers[$kkey]] == "100" ? "disabled='disabled' data-input_total_id='{$key}'" : "data-input_type_q='marks' data-input_type='score' data-input_row_id='{$key}'" )." class='form-control pl-0 pr-0 font-18 text-center' name='{$column}' min='0' max='1000' type='number' value='{$kvalue}' {$readOnly}></td>";
                } elseif($file_headers[$kkey] == "TEACHER REMARKS") {
                    $report_table .= "<td><input style='min-width:300px' type='text' data-input_method='remarks' data-input_type='score' data-input_row_id='{$key}' class='form-control' value='{$kvalue}'></td>";
                } elseif($file_headers[$kkey] == "TEACHER ID") {
                    $report_table .= "<td><span style='font-weight-bold font-17'>".(empty($kvalue) ? $params->userData->unique_id : $kvalue)."</span></td>";                    
                } else {
                    $data_key = "data-".strtolower(str_ireplace(" ", "_", $file_headers[$kkey]))."='{$kvalue}'";
                    $report_table .= "<td class='text-center'><span data-student_row_id='{$key}' {$data_key}>{$kvalue}</span></td>";
                }
            }
            $report_table .= "</tr>";
        }
        $report_table .= "</tbody>";
        $report_table .= "</table>";
        $report_table .= "</div>";

        $report_table .= "<div class='text-right mt-3'>";
        $report_table .= "<button onclick='return save_terminal_report()' class='btn btn-outline-success'>Save Report</button>";
        $report_table .= "</div>";

        // save the information in a session
        $this->session->set("terminal_report_{$params->class_id}_{$params->course_id}", ["headers" => $headers, "students" => $complete_csv_data]);

        return ["data" => $report_table];

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
        if(!is_array($params->rs)) {
            return ["code" => 400, "data" => "Sorry! The rs parameter must be an array."];
        }

        global $defaultClientData;

        // set the report
        $report = (object) $params->rs;
        $report->clientId = $params->clientId;

        // set the academic year and term
        $report->academic_year = $params->academic_year;
        $report->academic_term = $params->academic_term;

        // check if a file has been uploaded
        if(empty($this->session->get("terminal_report_{$report->class_id}_{$report->course_id}"))) {
            return ["code" => 400, "data" => "Sorry! Upload a valid CSV file to proceed."];
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
                return ["code" => 400, "data" => "Sorry! The course_id and class_id are required."];
            }

            // old record
            $class_item = $this->pushQuery("id, item_id, name", "classes", "item_id='{$report->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($class_item)) {
                return ["code" => 400, "data" => "Sorry! An invalid class id was supplied."];
            }

            // old record
            $course_item = $this->pushQuery("id, item_id, name, course_code", "courses", "item_id='{$report->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($course_item)) {
                return ["code" => 400, "data" => "Sorry! An invalid course id was supplied."];
            }

            // student scores checkeer
            if(!isset($report->ss)) {
                return ["code" => 400, "data" => "Sorry! The students scores is required and must be submitted."];
            }

            $overall_score = 0;
            $percentage = 0;
            $scores_array = [];

            // set the percentage
            if(isset($defaultClientData?->grading_structure?->columns)) {
                $percentage = $defaultClientData?->grading_structure?->columns?->Examination?->percentage ?? 0;
            }

            // group the information in an array
            foreach($report->ss as $score) {
                // explode the record
                $split = explode("|", $score);

                foreach($split as $item) {
                    $spl = explode("=", $item);
                    if($spl[0] == "id") {
                        $student_id = $spl[1];
                    }
                    if($spl[0] == "remarks") {
                        $scores_array[$student_id]["remarks"] = $spl[1];
                    }
                    if(!in_array($spl[0], ['name', 'id', 'remarks'])) {
                        if($spl[0] == 'marks' && $percentage) {
                            $spl[1] = $spl[1] > 0 ? round(($spl[1] / 100) * $percentage) : 0;
                        }
                        $scores_array[$student_id]["marks"][] = [
                            "item" => $spl[0],
                            "score" => $spl[1]
                        ];
                        if(!isset($scores_array[$student_id]["total_score"])) {
                            $scores_array[$student_id]["total_score"] = 0;
                            $scores_array[$student_id]["total_percentage"] = 0;
                        }
                        if(in_array($spl[0], ['sba', 'marks'])) {
                            $scores_array[$student_id]["total_percentage"] += $spl[1];
                        }
                        $scores_array[$student_id]["total_score"] += $spl[1];
                    }
                }
            }

            $course_item = $course_item[0];
            $class_item = $class_item[0];

            //set more values
            $average_score = 0;
            $course_name = ucwords($course_item->name);
            $course_code = strtoupper($course_item->course_code);
            
            // session
            $session_array = $this->session->get("terminal_report_{$report->class_id}_{$report->course_id}");

            // get the class teacher id
            $teacher_key = array_search("TEACHER ID", $session_array["headers"]);
            // $teacher_ids = $session_array["students"][0][$teacher_key];

            // set the current user id if the teacher id was not parsed.
            // $teacher_ids = empty($teacher_ids) ? $params->userData->unique_id : $teacher_ids;
              
            // temporary use this as the teacher id
            $teacher_ids = $params->userId;

            // set a new log id
            $report_id = random_string("alnum", RANDOM_STRING);

            // old record
            $isFound = $this->pushQuery("id, report_id", 
                "grading_terminal_logs", 
                "academic_year='{$report->academic_year}' AND academic_term='{$report->academic_term}' AND course_id='{$report->course_id}' AND client_id='{$params->clientId}' LIMIT 1");

            // if there is an existing record
            if(empty($isFound)) {
                // log the information
                $log_stmt = $this->db->prepare("INSERT INTO grading_terminal_logs SET report_id = ?, client_id = ?, 
                    class_id = ?, class_name = ?, course_id = ?, course_name = ?, academic_year = ?, academic_term = ?,
                    created_by = ?, course_code = ?, grading = ?");
                $log_stmt->execute([
                    $report_id, $params->clientId, $report->class_id, $class_item->name, 
                    $report->course_id, $course_name, $params->academic_year, 
                    $params->academic_term, $params->userId, strtoupper($course_item->course_code),
                    json_encode($defaultClientData->grading_structure->columns)
                ]);
            } else {
                $report_id = $isFound[0]->report_id;
            }

            // loop through the list and insert the record
            foreach($scores_array as $key => $student) {
                
                // generate a unique record
                $distinct_record = md5($params->academic_year.$params->academic_term.$key.$report->course_id);
                
                // prepare the statement
                $stmt = $this->db->prepare("INSERT IGNORE INTO grading_terminal_scores SET 
                    distinct_record = ?, class_id = ?, class_name = ?, date_modified = now(),
                    course_id = ?, course_name = ?, course_code = ?, scores = ?, total_score = ?, 
                    average_score = ?, teacher_ids = ?, class_teacher_remarks = ?, created_by = ?,
                    academic_year = ?, academic_term = ?, student_unique_id = ?, client_id = ?, report_id = ?
                    ON DUPLICATE KEY UPDATE scores = ?, total_score = ?, average_score = ?, class_teacher_remarks = ?
                ");
                // execute the statement
                $stmt->execute([
                    $distinct_record, $report->class_id, $class_item->name,
                    $report->course_id, $course_name, $course_code, json_encode($student["marks"]),
                    $student["total_score"], $average_score, $teacher_ids, $student["remarks"], $params->userId,
                    $params->academic_year, $params->academic_term, $key, $params->clientId, $report_id,
                    json_encode($student["marks"]), $student["total_score"], $average_score, $student["remarks"]
                ]);
                // log the user activity
                $this->userLogs("terminal_report", $key, json_encode($student), 
                    "{$params->userData->name} uploaded the terminal report for {$params->academic_term} {$params->academic_year} 
                    Academic Year of Student With ID: {$key}", $params->userId
                );

            }

            // insert the activity into the cron_scheduler
            // $query = $this->db->prepare("INSERT INTO cron_scheduler SET client_id = '{$params->clientId}', item_id = ?, user_id = ?, cron_type = ?, active_date = now()");
            // $query->execute([$report_id, $params->userId, "terminal_report"]);

            // delete the session
            $this->session->remove("terminal_report_{$report->class_id}_{$report->course_id}");

            return [
                "data" => "The report information was successfully saved.",
                "additional" => [
                    "reportId" => $report_id
                ]
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Run the results cron job
     * 
     * @param String    $report_id
     * @param String    $clientId
     * 
     * @return Bool
     */
    public function run_result_cron_job($report_id, $clientId) {

        try {
            // set the fullname of the user
            $u_stmt = $this->db->prepare("
                UPDATE grading_terminal_scores a SET 
                a.student_item_id = (SELECT u.item_id FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1),
                a.student_name = (SELECT u.name FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1),
                a.teachers_name = (SELECT u.name FROM users u WHERE u.unique_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1),
                a.student_row_id = (SELECT u.id FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1)
            WHERE a.report_id='{$report_id}' AND a.client_id = '{$clientId}' LIMIT 500");
            $u_stmt->execute();

            // get the list of all users that was uploaded
            $u_stmt = $this->db->prepare("UPDATE grading_terminal_logs a SET 
                a.teachers_name = (SELECT u.name FROM users u WHERE u.unique_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1)
            WHERE a.report_id='{$report_id}' AND a.client_id='{$clientId}' LIMIT 1");
            $u_stmt->execute();

        } catch(PDOException $e) {}

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
                return ["code" => 400, "data" => "Sorry! The label variable must be a valid array."];
            }

            // ensure that the action and report_id were parsed
            if(!isset($params->label["action"]) || !isset($params->label["report_id"])) {
                return ["code" => 400, "data" => "Sorry! Action and Report ID are required."];
            }

            // set the action to capitalize
            $action = ucfirst($params->label["action"]);
            $report_id = $params->label["report_id"];

            // check the action to perform
            if(!in_array($action, ["Submit", "Cancel", "Approve"])) {
                return ["code" => 400, "data" => "Sorry! An invalid action was requested."];
            }

            // confirm if the report contains the user id as well
            $split = explode("_", $report_id);

            if(isset($split[1])) {
                $report_id = xss_clean($split[0]);
                $student_id = xss_clean($split[1]);
            }

            // get the details of the terminal report
            $check = $this->pushQuery("a.status, a.course_name, a.class_name, a.course_code, a.course_id, a.teachers_name,
                (SELECT COUNT(*) FROM grading_terminal_scores b WHERE b.report_id = a.report_id) AS students_count", 
                "grading_terminal_logs a", "a.report_id='{$report_id}' LIMIT 1");

            if(empty($check)) {
                return ["code" => 400, "data" => "Sorry! An invalid report id was parsed."];
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
                    "grading_terminal_scores a", "a.report_id='{$report_id}' AND a.student_row_id='{$student_id}' LIMIT 1");
                
                // if empty then return false
                if(empty($student_check)) {
                    return ["code" => 400, "data" => "Sorry! An invalid result id was parsed."];
                }
                $student_info = $student_check[0];
                $status = $student_info->status;
                $students_count = 1;
                $where_clause = " AND student_row_id='{$student_id}'";
            }

            // continue to process the user request
            if(in_array($status, ["Approved", "Cancelled"])) {
                return ["code" => 400, "data" => "Sorry! The result has been {$status} and cannot be modified."];
            }

            // run more checks
            if(($action === "Approve")) {

                // confirm that the report has already been submitted
                if(($status !== "Submitted")) {
                    return ["code" => 400, "data" => "Sorry! The result has not yet been submitted by the Teacher hence cannot be approved."];
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
                    return ["code" => 400, "data" => "Sorry! The result has already been submitted by the Teacher hence cannot repeat same action."];
                }
                
                // run this section if the student id was not parsed
                if(!isset($student_id)) {
                    // approve the report
                    $stmt = $this->db->prepare("UPDATE grading_terminal_logs SET status = ? WHERE report_id = ? LIMIT 1");
                    $stmt->execute(["Submitted", $report_id]);
                }

                // run this section if the teachers_name is empty
                if(empty($check->teachers_name)) {
                    // run the cron job
                    $this->run_result_cron_job($report_id, $params->clientId);
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
                    return ["code" => 400, "data" => "Sorry! The result has not yet been Approved hence action cannot be reversed."];
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
                    "href" => "{$this->baseUrl}results-review/{$report_id}"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Sort by percentage with position
     * 
     * @param       $array
     * 
     * @return Array
     */
    public function sortByPercentageWithPosition($array) {
        // Sort by percentage in descending order
        uasort($array, function ($a, $b) {
            return $b['percentage'] <=> $a['percentage'];
        });

        // Assign positions with tie handling
        $position = 1;
        $prevPercentage = null;
        $sameRankCount = 0;

        foreach ($array as &$item) {
            if ($item['percentage'] === $prevPercentage) {
                // Tie: assign the same position as previous
                $item['position'] = $position;
                $sameRankCount++;
            } else {
                // New rank: increment position by the number of tied entries
                $position += $sameRankCount;
                $item['position'] = $position;
                $sameRankCount = 1;
                $prevPercentage = $item['percentage'];
            }
        }

        return $array;
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
            return ["code" => 400, "data" => "Sorry! The label variable must be a valid array."];
        }

        // confirm that the results and record_id has been parsed
        if(!isset($params->label["results"], $params->label["record_id"])) {
            return ["code" => 400, "data" => "Sorry! No result set has been parsed."];
        }

        try {

            // report id
            $record_id = $params->label["record_id"];
            $record_type = $params->label["record_type"];

            // get the details of the terminal report
            $check = $this->pushQuery("a.status, a.course_name, a.class_name, a.course_code, a.course_id, a.report_id,
            (SELECT COUNT(*) FROM grading_terminal_scores b WHERE b.report_id = a.report_id AND a.status='Submitted') AS students_count", 
            "grading_terminal_logs a", "a.report_id='{$record_id}' LIMIT 1");

            // confirm if the result is not empty
            if(empty($check)) {
                return ["code" => 400, "data" => "Sorry! An invalid report/student id was parsed."];
            }

            // set the report
            $report = $check[0];
            $report_id = $report->report_id;
            $students_count = $report->students_count ?? 1;

            // initial values
            $additional = [];
            $scores_array = [];
            $students_list = $params->label["results"];

            // init the scores
            $sba_score = 0;
            $exams_score = 0;

            // group the information in an array
            foreach($students_list as $stu_id => $score) {
                // if the marks is set and an array
                if(isset($score["marks"]) && is_array($score["marks"])) {
                    $sba_score += $score["marks"]["sba"] ?? 0;
                    $exams_score += $score["marks"]["marks"] ?? 0;
                }
                // append the teachers remarks to the array
                $scores_array[$stu_id]["percentage"] = ($score["marks"]["sba"] ?? 0) + ($score["marks"]["marks"] ?? 0);
                $scores_array[$stu_id]["remarks"] = $score["remarks"];

                // raw scores
                $raw_score = 0;
                foreach($score["marks"] as $key => $value) {
                    if(!in_array($key, ["sba", "marks"])) {
                        $raw_score += $value;
                    }
                    $scores_array[$stu_id]['marks'][] = [
                        'item' => $key,
                        'score' => $value
                    ];
                }
                $scores_array[$stu_id]["raw_score"] = $raw_score;
            }

            $scores_array = $this->sortByPercentageWithPosition($scores_array);

            // calculate the average score
            $average_score = ($sba_score + $exams_score) / count($students_list);

            // exceeds the total 100% score
            $exceeds = [];

            // if an error was found
            if(!empty($exceeds)) {
                return ["code" => 400, "data" => "Sorry! Please ensure the total score of any student does not exceed 100%."];
            }

            // update query
            $update_stmt = $this->db->prepare("UPDATE grading_terminal_scores SET class_position = ?,
                scores = ?, total_score = ?, total_percentage = ?, date_modified = now(), average_score = ?, class_teacher_remarks = ?
                WHERE academic_year = ? AND academic_term = ? AND student_item_id = ? AND report_id = ? AND client_id = ? LIMIT 1
            ");
            
            // loop through the list and insert the record
            foreach($scores_array as $key => $student) {
                // execute the statement
                $update_stmt->execute([
                    $student["position"], json_encode($student["marks"]), $student["raw_score"], $student["percentage"], number_format($average_score, 2), $student["remarks"],
                    $params->academic_year, $params->academic_term, $key, $report_id, $params->clientId
                ]);
            }

            // approve the results
            if($record_type === "approve_results") {
                // update the student marks as well
                $stmt = $this->db->prepare("UPDATE grading_terminal_scores SET date_approved = now(), status = ? WHERE report_id = ? AND status = ? AND course_id = ? LIMIT {$students_count}");
                $stmt->execute(["Approved", $report_id, "Submitted", $report->course_id]);

                // update the student marks as well
                $stmt = $this->db->prepare("UPDATE grading_terminal_logs SET status = ? WHERE report_id = ? AND status = ? AND course_id = ? LIMIT {$students_count}");
                $stmt->execute(["Approved", $report_id, "Submitted", $report->course_id]);
            }

            // if the request is not to update a single student record
            if($record_type !== "student") {
                // set the redirect url
                $additional["href"] = "{$this->baseUrl}results-review/{$report_id}";
            }

            return [
                "code" => 200,
                "data" => "The result marks was successfully updated.",
                "additional" => $additional
            ];


        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Save the SBA Score Cap
     * 
     * @param       $params->label["sba_score_cap"]
     * @param       $params->label["result_id"]
     * 
     * @return Array
     */
    public function save_sba_score_cap(stdClass $params) {
        try {

            // confirm that the score cap and result id is parsed
            if(empty($params->sba_score_cap) || empty($params->result_id)) {
                return ["code" => 400, "data" => "Sorry! No SBA Score Cap or Result ID was parsed."];
            }

            // confirm that the score cap is a number
            if(!preg_match("/^[0-9]+$/", $params->sba_score_cap)) {
                return ["code" => 400, "data" => "Sorry! The SBA Score Cap must be a valid number."];
            }

            // confirm that the score cap is between 1 and 200
            if($params->sba_score_cap < 1 || $params->sba_score_cap > 200) {
                return ["code" => 400, "data" => "Sorry! The SBA Score Cap must be between 1 and 200."];
            }

            // get the list of records inserted already in the grading_terminal_scores table
            $existingRecords = $this->pushQuery("scores", "grading_terminal_scores", "report_id = '{$params->result_id}'");

            if(!empty($existingRecords)) {
                $highest = 0;
                foreach($existingRecords as $record) {
                    $scores = json_decode($record->scores, true);
                    $iscore = $scores[0]['score'];
                    if($iscore > $highest) {
                        $highest = $iscore;
                    }
                }
                if($params->sba_score_cap < $highest) {
                    return ["code" => 400, "data" => "Sorry! The SBA Score Cap must be greater than the highest score '{$highest}' in the result."];
                }
            }

            // update the sba score cap
            $stmt = $this->db->prepare("UPDATE grading_terminal_logs SET sba_score_cap = ? WHERE report_id = ? LIMIT 1");
            $stmt->execute([$params->sba_score_cap, $params->result_id]);

            return [
                "code" => 200,
                "data" => "The SBA Score Cap was successfully updated."
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
            global $accessObject, $academicSession;

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
                "class_id" => $params->class_id ?? null,
                "academic_year" => $params->academic_year ?? $this->academic_year,
                "academic_term" => $params->academic_term ?? $this->academic_term,
                "student_item_id" => isset($params->student_id) && !empty($params->student_id) && ($params->student_id !== "null") ? $params->student_id : null,
            ];
            $report_data = $this->result_score_list(null, $param, true);

            // get the user attendance results
            $attendance_param = (object) [
                "clientId" => $this->clientId, 
                "academic_year" => $params->academic_year,
                "academic_term" => $params->academic_term,
                "user_types_list" => ["student"], "the_user_type" => "student",
                "period" => "this_term", 
                "is_finalized" => 1, 
                "is_present_check" => 1,
                "start_date" => $this->this_term_starts, 
                "end_date" => $this->this_term_ends,
            ];
            // create a new object of the attendance class
            $attendanceObj = load_class("attendance", "controllers", $params);

            // init loop
            $students = [];
            $bg_color = "#6877ef";
            $academics = $this->iclient->client_preferences->academics;
            $grading = $this->iclient->grading_structure->columns;
            $interpretation = $this->iclient->grading_system;

            // set the grading column
            $column_count = 0;
            $grading_column = "";
            $next_term_fees = null;

            // loop through the grading columns
            foreach($grading as $key => $value) {
                // increment the count
                $column_count++;
                // grading column
                $grading_column .= "<td align=\"center\" width=\"11%\">".strtoupper($key)."</td>";
            }

            // get the client logo content
            if(!empty($this->iclient->client_logo)) {
                $type = pathinfo($this->iclient->client_logo, PATHINFO_EXTENSION);
                $logo_data = file_get_contents($this->iclient->client_logo);
                $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
            }

            $defaultFontSize = "font-size:12px";
            $increaseFontSize = "font-size:18px";

            // loop through the report set
            foreach($report_data as $key => $student) {

                // get the student attendance logs
                $attendance_param->the_current_user_id = $key;
                $attendance_log = $attendanceObj->range_summary($attendance_param)->summary;

                // set the information
                $table = "<table width=\"100%\" cellspacing=\"5px\" cellpadding=\"5px\" style=\"background-color:#050f58; color:#fff\">\n";
                // get the student information
                $table .= "<tr>";
                $table .= "<td><strong>".strtoupper($student["data"]["student_name"])."</strong></td>\n";
                $table .= "<td><strong style=\"text-transform:uppercase\">YEAR: {$student["data"]["academic_year"]} - {$student["data"]["academic_term"]} {$academicSession}</strong></td>\n";
                $table .= "<td><strong>GRADE: ".strtoupper($student["data"]["class_name"])."</strong></td>\n";
                $table .= "<td><strong>AGE: {$student["data"]["student_age"]}</strong></td>\n";
                $table .= "</tr>";
                $table .= "</table>\n";

                // set the address and the other information
                $table .= "<table cellpadding=\"5\" width=\"100%\">";
                $table .= "<tr>
                    <td valign=\"top\">
                        <table style=\"border: 1px solid #dee2e6;\" width='100%'>
                            <tr>
                                <td style=\"border: 1px solid #dee2e6;\" align='center' width='110px'>
                                    ".(!empty($this->iclient->client_logo) ? "<img width=\"100px\" src=\"{$client_logo}\">" : "")."
                                </td>
                                <td style=\"border: 1px solid #dee2e6;\" align='center' valign='top'>
                                    <h2 style=\"color:#6777ef;font-family:helvetica;padding:0px;margin:0px;\">
                                        ".strtoupper($this->iclient->client_name)."
                                    </h2>
                                    <span style=\"padding:0px; margin:0px;\">
                                        <strong>Address:</strong> {$this->iclient->client_address}
                                    </span><br>
                                    <span style=\"padding:0px; margin:0px;\">
                                        <strong>Contact:</strong> {$this->iclient->client_contact} ".(!$this->iclient->client_secondary_contact ? " / {$this->iclient->client_secondary_contact}" : null)."
                                    </span><br>
                                    <span style=\"padding:0px; margin:0px;\">
                                        <strong>Email:</strong> {$this->iclient->client_email}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>";
                $table .= "<td style=\"padding:5px;\" align=\"center\" valign=\"top\" width=\"30%\">
                    <div style=\"padding:5px;\">
                        <strong style=\"color:#6777ef\">CLASS AVERAGE: ".round($student["data"]["average_score"], 2)."</strong>
                    </div>
                    <div style=\"padding:5px; text-transform:uppercase;\">
                        <strong>SCHOOL RESUMES ON:<br>
                            <span style=\"color:#6777ef\">".date("jS M Y", strtotime($academics->next_term_starts))."</span>
                        </strong>
                    </div>

                    <div style=\"padding:10px; color:#fff; background-color:{$bg_color};\">
                        Please visit app.myschoolgh.com/report/{$student["data"]["unique_id"]} for a graphical analysis of this report.
                    </div>

                    </td>
                    </tr>";
                $table .= "</table>\n";
                $table .= "<table style=\"font-size:10px\" cellpadding=\"5\" width=\"100%\" style=\"border: 1px solid #dee2e6;\">";
                $table .= "<tr style=\"font-weight:bold;font-size:15px;background-color:#050f58;color:#fff;\">";
                $table .= "<td align=\"center\" colspan=\"".($column_count + 4)."\">END OF TERM REPORT CARD</td>";
                $table .= "</tr>";
                $table .= "<tr style=\"font-weight:bold;{$defaultFontSize}\">";
                $table .= "<td style=\"{$defaultFontSize}\" width=\"25%\">SUBJECT</td>";
                $table .= $grading_column;
                $table .= "<td style=\"{$defaultFontSize}\" align=\"center\" width=\"10%\">TOTAL SCORE</td>";
                $table .= "<td style=\"{$defaultFontSize}\" width=\"15%\">TEACHER</td>";
                $table .= "<td style=\"{$defaultFontSize}\">TEACHER'S COMMENT</td>";
                $table .= "</tr>";

                // // get the results submitted by the teachers for each subject
                foreach($student["sheet"] as $score) {
                    // only show the subject if approved
                    if($score->status === "Approved") {
                        // append to the table
                        $table .= "<tr>";
                        $table .= "<td style=\"border: 1px solid #dee2e6;\">{$score->course_name}</td>";
                        // get the scores
                        foreach($score->scores as $s_score) {
                            if(!in_array($s_score['item'], ["sba", "marks"])) continue;
                            $s_score = $s_score['score'] ?? 0;
                            $table .= "<td style=\"border: 1px solid #dee2e6;{$increaseFontSize}\" align=\"center\">".round($s_score, 2)."</td>";
                        }
                        $table .= "<td style=\"border: 1px solid #dee2e6;{$increaseFontSize}\" align=\"center\">".round($score->total_percentage, 2)."</td>";
                        $table .= "<td style=\"border: 1px solid #dee2e6;{$defaultFontSize}\">".strtoupper($score->teachers_name)."</td>";
                        $table .= "<td style=\"border: 1px solid #dee2e6;{$defaultFontSize}\">{$score->class_teacher_remarks}</td>";
                        $table .= "</tr>";
                    }
                }
                $table .= "</table>";

                // set the grading system
                $table .= "<table cellpadding=\"5px\" border=\"0\" width=\"100%\">";
                $table .= "<tr>";
                $table .= "<td align=\"center\" width=\"35%\" valign=\"top\">";
                $table .= "<table style=\"{$defaultFontSize}\" align=\"left\" cellpadding=\"5px\" border=\"0\" width=\"100%\">";
                $table .= "<tr style=\"font-weight:bold\">";
                $table .= "<td colspan=\"2\" align=\"center\">";
                $table .= "<span style=\"font-weight:bold; font-size:15px\">GRADING SYSTEM</span>";
                $table .= "</td>";
                $table .= "</tr>";
                $table .= "<tr style=\"font-weight:bold\">";
                $table .= "<td>Marks in Percentage (%)</td>";
                $table .= "<td>Interpretation</td>";
                $table .= "</tr>\n";
                // loop through the grading system
                foreach($interpretation as $ikey => $ivalue) {
                    $table .= "<tr>";
                    $table .= "<td>{$ivalue->start} - {$ivalue->end}</td>";
                    $table .= "<td>{$ivalue->interpretation}</td>";
                    $table .= "</tr>";
                }
                $table .= "</table>";
                $table .= "</td>";
                $table .= "<td width=\"33%\" valign=\"top\">\n";
                $table .= "<table width=\"100%\" cellpadding=\"5px\" style=\"border: 1px solid #dee2e6;\" border=\"0\">\n";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\" align=\"center\" colspan=\"2\">ATTENDANCE</td>\n";
                $table .= "</tr>\n";
                $table .= "<tr>\n";
                $table .= "<td style=\"font-weight:bold\">PRESENT</td>\n";
                $table .= "<td style=\"font-weight:bold\">".($attendance_log["Present"] ?? 0)."</td>\n";
                $table .= "</tr>\n";
                $table .= "<tr>\n";
                $table .= "<td style=\"font-weight:bold\">ABSENT</td>\n";
                $table .= "<td style=\"font-weight:bold\">".($attendance_log["Absent"] ?? 0)."</td>";
                $table .= "</tr>";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\">TERM DAYS</td>";
                $table .= "<td style=\"font-weight:bold\">".($attendance_log["Term"] ?? 0)."</td>\n";
                $table .= "</tr>";
                $table .= "</table>\n";
                $table .= "</td>\n";
                $table .= "<td width=\"32%\" valign=\"top\">\n";
                 $table .= "<table width=\"100%\" cellpadding=\"5px\" style=\"border: 1px solid #dee2e6;\">\n";
                $table .= "<tr>\n";
                $table .= "<td style=\"font-weight:bold\" align=\"center\" colspan=\"2\">NEXT TERM FEES</td>\n";
                $table .= "</tr>\n";
                $table .= "</table>\n";
                $table .= "</td>\n";
                $table .= "</tr>\n";
                $table .= "</table>\n";

                // append to the students list
                $students[$key] = [
                    "report" => $table,
                    "attendance" => $attendance_log,
                    "term_fees" => $next_term_fees
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
