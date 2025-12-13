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
     * Get the results remarks list
     * 
     * @param stdClass $params
     * @return Array
     */
    public function results_remarks(stdClass $params) {

        global $defaultUser, $isStudent, $isTeacher;

        $whereClause = "";

        // if the user is a teacher
        if($isTeacher) {
            $whereClause .= " AND a.created_by='{$defaultUser->user_id}'";
        }
        
        if($isStudent) {
            $whereClause .= " AND a.student_id='{$defaultUser->user_id}'";
        }
        
        // class_id='{$params->remarks_class_id}' AND student_id='{$params->remarks_student_id}'
        $whereClause = !empty($params->class_id) ? " AND a.class_id='{$params->class_id}'" : null;
        $whereClause .= !empty($params->remarks_student_id) ? " AND a.student_id='{$params->remarks_student_id}'" : null;
        $whereClause .= !empty($params->remarks_id) ? " AND a.id='{$params->remarks_id}'" : null;
        $whereClause .= !empty($params->class_ids) ? " AND a.class_id IN {$this->inList($params->class_ids)}" : null;

        // get the list of remarks
        $listRemarks = $this->pushQuery(
            "a.*, b.name AS class_name, c.name AS student_name", 
            "grading_terminal_remarks a
            LEFT JOIN classes b ON b.id = a.class_id
            LEFT JOIN users c ON c.item_id = a.student_id", 
            "a.client_id='{$params->clientId}' AND a.academic_year='{$this->academic_year}' AND a.academic_term='{$this->academic_term}' {$whereClause}"
        );

        // return the list of remarks
        return [
            "code" => 200,
            "data" => $listRemarks
        ];

    }

    /**
     * Save the student remarks
     * 
     * @param stdClass $params
     * @return Array
     */
    public function save_student_remarks(stdClass $params) {
        
        global $isAdmin, $isTeacher, $defaultUser;

        if(!$isAdmin && !$isTeacher) {
            return $this->permission_denied_code;
        }

        foreach(['remarks_class_id', 'remarks_student_id', 'remarks'] as $key) {
            if(empty($params->{$key})) {
                return ["code" => 400, "data" => "Sorry! The {$key} field is required."];
            }
        }

        // check if a remark exists for this student in the class for the acadmic year
        $checkExist = $this->pushQuery(
            "id", 
            "grading_terminal_remarks", 
            "class_id='{$params->remarks_class_id}' AND student_id='{$params->remarks_student_id}' AND academic_year='{$this->academic_year}' AND academic_term='{$this->academic_term}' LIMIT 1"
        );

        $remarks_id = null;
        if(!empty($checkExist)) {
            
            $this->db->query("UPDATE grading_terminal_remarks SET 
                remarks='{$params->remarks}', updated_on = now()
                WHERE id='{$checkExist[0]->id}' LIMIT 1"
            );

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Student remarks successfully updated.", "refresh" => 2000];
            $return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}results-remarks/{$checkExist[0]->id}"];

            return $return;

        } else {

            // insert the new remark
            $this->db->query("INSERT INTO grading_terminal_remarks SET 
                client_id = '{$params->clientId}',
                class_id = '{$params->remarks_class_id}', 
                student_id = '{$params->remarks_student_id}', 
                remarks = '{$params->remarks}', 
                academic_year = '{$this->academic_year}', 
                academic_term = '{$this->academic_term}',
                created_by = '{$defaultUser->user_id}'
            ");

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Student remarks successfully created.", "refresh" => 2000];
            $return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}results-remarks"];

            return $return;

        }

    }
    
    /**
     * Delete the student remarks
     * 
     * @param stdClass $params
     * @return Array
     */
    public function delete_student_remarks(stdClass $params) {
        global $isAdmin, $isTeacher;
        if(!$isAdmin && !$isTeacher) {
            return $this->permission_denied_code;
        }

        if(empty($params->remarks_id)) {
            return ["code" => 404, "data" => "Sorry! The remarks record was not found."];
        }

        $record = $this->results_remarks($params);
        if(empty($record['data'])) {
            return ["code" => 404, "data" => "Sorry! The remarks record was not found."];
        }

        $this->db->query("DELETE FROM grading_terminal_remarks WHERE id='{$params->remarks_id}' LIMIT 1");

        return ["code" => 200, "data" => "Student remarks successfully deleted."];
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
                return ["code" => 200, "data" => "There is an existing record, an update will replace it."];
            } elseif($check[0]->status === "Approved") {
                return ["code" => 400, "data" => "Sorry! This result has already been approved hence cannot be updated."];
            } elseif($check[0]->status === "Submitted") {
                return ["code" => 400, "data" => "Sorry! This result has been submitted pending approval hence cannot be updated."];
            }
        } else {
            return ["code" => 200, "data" => "Upload the Results via the <strong>CSV Uploader</strong> or Manual input."];
        }
    }

    /**
     * Download the CSV File
     * 
     * @return array|string|null
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

        $overallSBA = 0;

        // get the grading structure
        if(!empty($grading_sba)) {
            foreach($grading_sba as $keyName => $item) {
                if($keyName == 'total_assessment_score') {
                    $overallSBA = $item;
                    continue;
                }
                if(!isset($item['percentage'])) continue;
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

        // if the manual upload is true then return the csv file
        if(!empty($params->manual_report_upload)) {
            return ['csv_file' => $csv_file, 'students_list' => $students_list, 'course_item' => $course_item];
        }

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

        $overallSBA = 0;

        // get the sba percentage
        $sbaPercentage = $client_data->grading_structure->columns->{"School Based Assessment"}->percentage ?? 0;

        // loop through the students
        foreach($students_list as $student) {
            $csv_file .= strtoupper($student->name).",{$student->unique_id},{$course_name},";
            $totalSba = 0;
            foreach($grading_sba as $keyName => $item) {
                if($keyName == 'total_assessment_score') {
                    $overallSBA = $item;
                    continue;
                }
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
     * Get the remark for the score
     * 
     * @param int $score
     * @return string
     */
    public function get_the_remark($score) {

        $grading_system = $this->client_data?->grading_system ?? [];

        if($score == 0) return '';
        
        foreach($grading_system as $item) {
            if($score >= $item->start && $score <= $item->end) {
                return $item->interpretation;
            }
        }

        return '';

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

        // if the csv headers are not set then upload the file
        if(empty($params->r_csv_headers)) {
            if(!isset($params->report_file['tmp_name'])) {
                return ["code" => 400, "data" => "Sorry! Please select a file to upload."];
            }
        
            // reading tmp_file name
            $report_file = fopen($params->report_file['tmp_name'], 'r');
        }

        // get the content of the file
        $csv_headers = !empty($params->r_csv_headers) ? $params->r_csv_headers : fgetcsv($report_file);
        $complete_csv_data = !empty($params->r_csv_data) ? $params->r_csv_data : [];

        if(empty($params->r_csv_data)) {
            //using while loop to get the information
            while($row = fgetcsv($report_file)) {
                // session data
                $complete_csv_data[] = $row;
            }
        }


        // check the file that have been uploaded
        $headers = ["STUDENT","STUDENT ID","SUBJECT"];

        // get the client data
        $client_data = $defaultClientData;
        $this->client_data = $client_data;
        $columns = json_encode($client_data->grading_structure ?? '[]');
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
        $sba_percentage_lower = [];

        $overallSBA = 0;

        // get the grading structure
        if(!empty($grading_sba)) {
            foreach($grading_sba as $keyName => $item) {
                if($keyName == 'total_assessment_score') {
                    $overallSBA = $item;
                    continue;
                }
                if(!isset($item['percentage'])) continue;
                if($item['sba_checkbox'] == 'true') {
                    $headers[] = strtoupper($keyName);
                    $sba_percentage[$keyName] = $item['percentage'];
                    $sba_percentage_lower[strtolower($keyName)] = $item['percentage'];
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

        $mobile_view = "<div class='row'>";
        
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

        // get the preset dataset
        $preset_dataset = !empty($params->r_csv_data) && !empty($params->preset_dataset) ? $params->preset_dataset : [];
        $remarks_dataset = !empty($params->r_csv_data) && !empty($params->remarks_dataset) ? $params->remarks_dataset : [];

        $mobileViewer = [];
        $studentNamesList = [];

        // set the files
        foreach($complete_csv_data as $key => $result) {
            
            if(empty($result[2]) && empty($result[3])) continue;

            if(!isset($mobileViewer[$key])) {
                $mobileViewer[$key] = "";
            }

            $report_table .= "<tr data-student_row_id='{$key}'>";

            $appendMobileView = "<div class='col-12 col-md-6 col-lg-4 mb-2 pr-2 pl-2' data-student_row_id='{$key}'>";

            // set the name as the header
            $appendMobileView .= "<div class='card'>";
            
            $appendMobileView .= "<div class='card-header pb-0 flex-column'>";
            $appendMobileView .= "<div><h5 class='text-primary mb-0 pb-0'>".strtoupper($result[0])."</h5></div>";
            $appendMobileView .= "<div class='text-muted'>".$result[1]."</div>";
            $appendMobileView .= "</div>";

            $appendMobileView .= "<div class='card-body p-2'>";
            

            foreach($result as $kkey => $kvalue) {
                if(in_array($kkey, [2])) continue;
                
                // if the key is in the array list
                if(in_array($file_headers[$kkey], array_keys($columns["columns"]))) {

                    $column = create_slug($file_headers[$kkey], "_");
                    $readOnly = in_array($column, ['school_based_assessment']) ? "readonly='readonly'" : "";

                    if(isset($preset_dataset[$result[1]])) {
                        if($column == 'school_based_assessment') {
                            $kvalue = $preset_dataset[$result[1]]['sba'] ?? '';
                        }
                        // if the column is examination then convert the value to 100% from the original percentage
                        if($column == 'examination') {
                            $kvalue = $preset_dataset[$result[1]]['marks'] ?? 0;
                            $kvalue = $kvalue > 0 ? round(($kvalue / $columns["columns"][$file_headers[$kkey]]['percentage']) * 100) : 0;
                            $kvalue = empty($kvalue) ? '' : $kvalue;
                        }
                    }

                    $iName = $file_headers[$kkey] == 'SCHOOL BASED ASSESSMENT' ? 'SBA' : $file_headers[$kkey];

                    $report_table .= "<td>
                    <input style='width:80px;' ".($columns["columns"][$file_headers[$kkey]] == "100" ? 
                        "disabled='disabled' data-input_total_id='{$key}'" : 
                        "data-input_type_q='marks' data-input_type='score' data-input_row_id='{$key}'" )." 
                        class='form-control pl-0 pr-0 font-18 text-center' data-max_percentage='{$columns["columns"][$file_headers[$kkey]]['percentage']}' 
                        name='{$column}' min='0' max='1000' type='number' value='{$kvalue}' {$readOnly}>
                    </td>";

                    $appendMobileView .= "<div class='d-flex justify-content-between items-center mb-2'>";
                    $appendMobileView .= "<div>".ucwords($iName)."</div>";
                    $appendMobileView .= "<div>
                    <input style='width:80px;' ".($columns["columns"][$file_headers[$kkey]] == "100" ? 
                        "disabled='disabled' data-input_total_id='{$key}'" : 
                        "data-input_type_q='marks' data-input_type='score' data-input_row_id='{$key}'" )." 
                        class='form-control pl-0 pr-0 font-18 text-center' data-max_percentage='{$columns["columns"][$file_headers[$kkey]]['percentage']}' 
                        name='{$column}' min='0' max='1000' type='number' value='{$kvalue}' {$readOnly}>
                    </div>";
                    $appendMobileView .= "</div>";

                } elseif($file_headers[$kkey] == "TEACHER REMARKS") {
                    
                    $kvalue = $remarks_dataset[$result[1]] ?? '';

                    if(isset($file_headers[8]) && isset($columns["columns"][$file_headers[8]])) {
                        $m_kvalue = $preset_dataset[$result[1]]['marks'] ?? 0;
                        $m_kvalue = $m_kvalue > 0 ? round(($m_kvalue / $columns["columns"][$file_headers[8]]['percentage']) * 100) : 0;
                        $preset = $preset_dataset[$result[1]] ?? [];
                        $kvalue = $this->get_the_remark($m_kvalue + ($preset['sba'] ?? 0));
                    }
                
                    $report_table .= "<td><input placeholder=\"Teacher's remarks\" style='min-width:300px' type='text' data-input_method='remarks' data-input_type='score' data-input_row_id='{$key}' class='form-control font-dark text-dark' value='{$kvalue}'></td>";
                
                    $appendMobileView .= "<input placeholder=\"Teacher's remarks\" style='width:100%' type='text' data-input_method='remarks' data-input_type='score' data-input_row_id='{$key}' class='form-control font-dark text-dark' value='{$kvalue}'>";
                
                } elseif($file_headers[$kkey] == "TEACHER ID") {
                    $report_table .= "<td><span style='font-weight-bold font-17'>".(empty($kvalue) ? $params->userData->unique_id : $kvalue)."</span></td>";                    
                } elseif(in_array($file_headers[$kkey], ['SUBJECT', 'STUDENT', 'STUDENT ID'])) {
                    $data_key = "data-".strtolower(str_ireplace(" ", "_", $file_headers[$kkey]))."='{$kvalue}'";
                    
                    $fullvalue = $kvalue;
                    $ikvalue = $file_headers[$kkey] == "STUDENT ID" ? strtoupper(substr($kvalue, 0, 8) . "...") : strtoupper($kvalue);

                    $report_table .= "<td class='text-left'><span title='{$fullvalue}' data-student_row_id='{$key}' {$data_key}>{$ikvalue}</span></td>";
                    
                    $appendMobileView .= "<div class='text-left'><span data-student_row_id='{$key}' {$data_key}></span></div>";

                    if($file_headers[$kkey] == "STUDENT ID") {
                        $studentNamesList[$kvalue] = $key;
                    }

                } else {
                    $theValue = $sba_percentage_lower[strtolower($file_headers[$kkey])] ?? '';
                    $kvalue = !empty($kvalue) ? $kvalue : '';
                    $iname = create_slug($file_headers[$kkey], "_");

                    if(isset($preset_dataset[$result[1]])) {
                        $kvalue = $preset_dataset[$result[1]][$iname] ?? '';
                        $kvalue = empty($kvalue) ? '' : $kvalue;
                    }

                    $report_table .= "
                        <td class='relative text-center'>
                            <div class='d-flex justify-content-around mt-1 items-center align-items-md-baseline'>
                                <input style='width:70px; font-size:18px;' type='number' data-max_percentage='{$theValue}'
                                data-input_method='{$iname}' data-input_type='marks' data-input_row_id='{$key}' 
                                class='form-control text-center mr-1' value='{$kvalue}'>
                            </div>
                            <span onclick='return raw_score_entry(\"{$key}\", \"{$iname}\")' title='Calculate the {$iname} score based on the percentage' class='edit-icon text-success cursor-pointer'>
                                <i class='fa fa-edit'></i> Edit
                            </span>
                        </td>";

                    $appendMobileView .= "<div class='d-flex justify-content-between items-center mb-2'>";
                    $appendMobileView .= "<div style='width:60%'>".ucwords(str_ireplace("_", " ", $file_headers[$kkey]))."  
                        <span onclick='return raw_score_entry(\"{$key}\", \"{$iname}\")' title='Calculate the {$iname} score based on the percentage' class='edit-icon text-success float-right cursor-pointer'>
                            <i class='fa fa-edit'></i> Edit
                        </span>
                    </div>";
                    $appendMobileView .= "<div><input style='width:70px; font-size:18px;' type='number' data-max_percentage='{$theValue}'
                    data-input_method='{$iname}' data-input_type='marks' data-input_row_id='{$key}' 
                    class='form-control text-center' value='{$kvalue}'></div>";
                    $appendMobileView .= "</div>";

                }

            }

            $appendMobileView .= "</div>";
            $appendMobileView .= "</div>";

            $appendMobileView .= "</div>";

            $report_table .= "</tr>";
            
            $mobile_view .= $appendMobileView;

            $mobileViewer[$key] .= $appendMobileView;

        }

        $report_table .= "</tbody>";
        $report_table .= "</table>";
        $report_table .= "</div>";

        $sba_percentage_lower['Exams'] = 100;

        $footer_table = "<div class='d-flex justify-content-between border-top border-primary mt-3'>";
        $footer_table .= "<div class='text-left mt-3'>";
        $footer_table .= "<button onclick='return cancel_terminal_report()' class='btn p-2 font-16 btn-outline-danger'>
            <i class='fa fa-save'></i> Cancel Upload
        </button>";
        $footer_table .= "</div>";
        $footer_table .= "<div class='text-right mt-3'>";
        $footer_table .= "
        <button onclick='return save_terminal_report()' class='btn p-2 font-16 btn-outline-success'>
            <i class='fa fa-save'></i> Save Class Results
        </button>";
        $footer_table .= "</div>";
        $footer_table .= "</div>";

        $notices = "";

        $footer_table .= "<div class='d-flex justify-content-between border-top border-primary mt-3'>";
        $footer_table .= "<div class='text-left mt-3'>";
        foreach($sba_percentage_lower as $key => $value) {
            $footer_table .= "<span class='badge badge-success mr-2 mb-1 font-16'>".ucwords(str_ireplace("_", " ", $key)).": {$value}%</span>";
            $notices .= "<span class='badge badge-success mr-2 mb-1 font-16'>".ucwords(str_ireplace("_", " ", $key)).": {$value}%</span>";
            $sba_percentage_lower[$key] = (int)$value;
        }
        $footer_table .= "</div>";
        $footer_table .= "</div>";

        $mobile_view .= "</div>";

        // append the footer table to the report table and mobile view
        $report_table .= $footer_table;
        $mobile_view .= $footer_table;

        // save the information in a session
        $this->session->set("terminal_report_{$params->class_id}_{$params->course_id}", ["headers" => $headers, "students" => $complete_csv_data]);

        return ["data" => [
            "table_view" => $report_table, 
            "mobile_view" => $mobile_view, 
            "mobileViewer" => $mobileViewer, 
            "overallSBA" => (int)($overallSBA ?? 0),
            "notices" => $notices,
            "sba_percentage_lower" => $sba_percentage_lower,
            "studentNamesList" => $studentNamesList
        ]];

    }

    /**
     * Manual Report Upload
     * 
     * @param       $params->academic_year
     * @param       $params->academic_term
     * @param       $params->class_id
     * @param       $params->course_id
     * 
     * @return Array
     */
    public function manual_report_upload(stdClass $params) {

        // set the manual report upload to true
        $params->manual_report_upload = true;

        $data = $this->download_csv($params);

        // get the headers
        $headers = explode(",", $data['csv_file']);

        // map the headers
        $params->r_csv_headers = array_map('trim', $headers);
        
        foreach($data['students_list'] as $ii => $student) {
            for($i = 0; $i < count($headers); $i++) {
                $ivalue = $i == 0 ? $student->name : (
                    $i == 1 ? $student->unique_id : (
                        $i == count($headers) - 1 ? null : (
                            $i == 2 ? $data['course_item']->name : 0
                        )
                    )
                );
                $params->r_csv_data[$ii][$i] = $ivalue;
            }
        }

        $params->show_scores = true;
        $params->preset_dataset = [];

        // get the results list
        $item = $this->results_list($params);

        // if the scores list is not empty then set the preset dataset
        if(!empty($item['data'])) {
            $scores = $item['data'][0]->scores_list;
            
            $students_list = [];
            $remarks_list = [];

            foreach($scores as $score) {
                $students_list[$score->student_unique_id] = [];
                $remarks_list[$score->student_unique_id] = $score->class_teacher_remarks;
                foreach($score->scores as $i => $v) {
                    $students_list[$score->student_unique_id][$i] = $v['score'];
                }
            }

            $params->remarks_dataset = $remarks_list;
            $params->preset_dataset = $students_list;
        }

        return $this->upload_csv($params);
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

            $percentage = 0;
            $scores_array = [];

            // set the percentage
            if(isset($defaultClientData?->grading_structure?->columns)) {
                $percentage = $defaultClientData?->grading_structure?->columns?->Examination?->percentage ?? 0;
            }

            $overallSBA = $defaultClientData?->grading_structure?->total_assessment_score ?? 100;
            $allowedColumns = ['sba', 'marks'];
            foreach($defaultClientData->grading_sba as $key => $value) {
                if($key == 'total_assessment_score') {
                    $overallSBA = $value;
                    continue;
                }
                if($value['sba_checkbox'] == 'true') {
                    $allowedColumns[] = strtolower(str_ireplace(" ", "_", $key));
                }
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
                        if(!in_array($spl[0], $allowedColumns)) {
                            continue;
                        }
                        $scores_array[$student_id]["marks"][] = [
                            "item" => $spl[0],
                            "score" => empty($spl[1]) ? 0 : $spl[1]
                        ];
                        if(!isset($scores_array[$student_id]["total_score"])) {
                            $scores_array[$student_id]["total_score"] = 0;
                            $scores_array[$student_id]["total_percentage"] = 0;
                        }
                        if(in_array($spl[0], ['sba', 'marks'])) {
                            $scores_array[$student_id]["total_percentage"] += empty($spl[1]) ? 0 : $spl[1];
                        }
                        $scores_array[$student_id]["total_score"] += empty($spl[1]) ? 0 : $spl[1];
                    }
                }
            }

            $course_item = $course_item[0];
            $class_item = $class_item[0];

            //set more values
            $average_score = 0;
            $course_name = ucwords($course_item->name);
            $course_code = strtoupper($course_item->course_code);
              
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
                a.student_row_id = (SELECT u.id FROM users u WHERE u.unique_id = a.student_unique_id AND u.user_type='student' LIMIT 1)
            WHERE a.client_id = '{$clientId}' LIMIT 500");
            $u_stmt->execute();

            // get the list of all users that was uploaded
            $this->db->query("UPDATE grading_terminal_scores a SET 
                a.teachers_name = (
                    SELECT u.name 
                    FROM users u 
                    WHERE u.item_id = a.teacher_ids AND u.user_type NOT IN ('student','parent') LIMIT 1
                ) WHERE a.report_id = '{$report_id}' AND a.client_id = '{$clientId}' LIMIT 5000");

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
                $scores_array[$stu_id]["remarks"] = $score["remarks"] ?? '';

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
     * @param StdClass $params
     * @param string $params->academic_term
     * @param string $params->academic_year
     * @param string $params->class_id
     * @param string $params->student_id
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
                $key = strtoupper($key) === "SCHOOL BASED ASSESSMENT" ? "SBA" : $key;
                $key = strtoupper($key) === "EXAMINATION" ? "EXAM SCORE" : $key;
                // grading column
                $grading_column .= "<td align=\"center\" width=\"11%\">".strtoupper($key)."</td>";
            }
            $grading_column .= "<td align=\"center\" width=\"11%\">CLASS AVERAGE</td>";

            // get the client logo content
            if(!empty($this->iclient->client_logo)) {
                $type = pathinfo($this->iclient->client_logo, PATHINFO_EXTENSION);
                $logo_data = file_get_contents($this->iclient->client_logo);
                $client_logo = 'data:image/' . $type . ';base64,' . base64_encode($logo_data);
            }

            $defaultFontSize = "font-size:13px";
            $increaseFontSize = "font-size:15px";

            // get the class id using the item_id
            $classInfo = $this->pushQuery("id, item_id, name", "classes", "item_id='{$params->class_id}' AND client_id='{$this->clientId}' LIMIT 1");

            $teacherRemarks = [];
            // load the remarks information
            if(!empty($classInfo)) {
                $remarksInfo = $this->pushQuery("remarks, student_id", "grading_terminal_remarks", "class_id='{$classInfo[0]->id}' AND academic_year='{$params->academic_year}' AND academic_term='{$params->academic_term}' AND client_id='{$this->clientId}'");
                foreach($remarksInfo as $remark) {
                    $teacherRemarks[$remark->student_id] = $remark->remarks;
                }
            }

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
                    <td valign=\"top\" width=\"80%\">
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

                $theTotal = 0;
                $theCount = 0;
                foreach($student["sheet"] as $score) {
                    if($score->status === "Approved") {
                        $theTotal += $score->total_percentage;
                        $theCount++;
                    }
                }

                $studentAverage = $theTotal > 0 ? round($theTotal / $theCount, 2) : 0;

                $table .= "
                    <td style=\"padding:5px;\" align=\"center\" valign=\"top\" width=\"20%\">
                        <div style=\"padding:5px;\">
                            <strong style=\"color:#6777ef\">STUDENT AVERAGE: ".round($studentAverage, 2)."</strong>
                        </div>
                        <div style=\"padding:5px; text-transform:uppercase;\">
                            <strong>SCHOOL RESUMES ON:<br>
                                <span style=\"color:#6777ef\">".date("jS M Y", strtotime($academics->next_term_starts))."</span>
                            </strong>
                        </div>
                    </td>
                    </tr>";
                $table .= "</table>\n";
                $table .= "<table cellpadding=\"5\" width=\"100%\" style=\"font-size:10px;border: 1px solid #dee2e6; min-height: 400px;\">";
                $table .= "<tr style=\"font-weight:bold;font-size:15px;background-color:#050f58;color:#fff;\">";
                $table .= "<td align=\"center\" colspan=\"".($column_count + 5)."\">END OF TERM REPORT CARD</td>";
                $table .= "</tr>";
                $table .= "<tr style=\"font-weight:bold;{$defaultFontSize};background-color:#d9d9d9;\">";
                $table .= "<td style=\"{$defaultFontSize}\" width=\"25%\">SUBJECT</td>";
                $table .= $grading_column;
                $table .= "<td style=\"{$defaultFontSize}\" align=\"center\" width=\"10%\">TOTAL SCORE</td>";
                $table .= "<td style=\"{$defaultFontSize}\" width=\"15%\">TEACHER</td>";
                $table .= "<td style=\"{$defaultFontSize}\">TEACHER'S COMMENT</td>";
                $table .= "</tr>";

                // set the row
                $irow = 0;

                // // get the results submitted by the teachers for each subject
                foreach($student["sheet"] as $score) {
                    // only show the subject if approved
                    if($score->status === "Approved") {

                        $irow++;

                        $bg_color = $irow % 2 === 0 ? "#cccccc" : "#ffffff";

                        $background = "background-color:{$bg_color};";

                        // append to the table
                        $table .= "<tr>";
                        $table .= "<td style=\"border: 1px solid #dee2e6; {$background} font-size:13px;\">{$score->course_name}</td>";
                        // get the scores
                        foreach($score->scores as $s_score) {
                            if(!in_array($s_score['item'], ["sba", "marks"])) continue;
                            $s_score = $s_score['score'] ?? 0;
                            $table .= "<td style=\"border: 1px solid #dee2e6;{$background}{$increaseFontSize}\" align=\"center\">".round($s_score, 2)."</td>";
                        }
                        $table .= "<td style=\"border: 1px solid #dee2e6;{$background}{$increaseFontSize}\" align=\"center\">".round($score->average_score ?? 0, 2)."</td>";
                        $table .= "<td style=\"border: 1px solid #dee2e6;{$background}{$increaseFontSize}\" align=\"center\">".round($score->total_percentage, 2)."</td>";
                        $table .= "<td style=\"border: 1px solid #dee2e6;{$background} font-size:11px;\">".strtoupper($score->teachers_name)."</td>";
                        $table .= "<td style=\"border: 1px solid #dee2e6;{$background}{$defaultFontSize}\">{$score->class_teacher_remarks}</td>";
                        $table .= "</tr>";
                    }
                }
                $table .= "</table>";

                // set the grading system
                $table .= "<table cellpadding=\"5px\" border=\"0\" width=\"100%\">";
                $table .= "<tr>";
                $table .= "<td align=\"center\" width=\"40%\" valign=\"top\">";
                $table .= "<table style=\"{$defaultFontSize}\" align=\"left\" cellpadding=\"5px\" border=\"1\" width=\"100%\">";
                $table .= "<tr style=\"font-weight:bold\">";
                $table .= "<td colspan=\"2\" align=\"center\">";
                $table .= "<span style=\"font-weight:bold; font-size:15px\">GRADING SYSTEM</span>";
                $table .= "</td>";
                $table .= "</tr>";
                $table .= "<tr style=\"font-weight:bold\">";
                $table .= "<td>Marks in Percentage (%)</td>";
                $table .= "<td>Grade</td>";
                $table .= "<td>Interpretation</td>";
                $table .= "</tr>\n";
                // loop through the grading system
                foreach($interpretation as $ikey => $ivalue) {
                    $table .= "<tr>";
                    $table .= "<td>{$ivalue->start} - {$ivalue->end}</td>";
                    $table .= "<td>{$ikey}</td>";
                    $table .= "<td>{$ivalue->interpretation}</td>";
                    $table .= "</tr>";
                }
                $table .= "</table>";
                $table .= "</td>";
                $table .= "<td width=\"25%\" valign=\"top\">\n";
                $table .= "<table width=\"100%\" cellpadding=\"5px\" style=\"border: 1px solid #dee2e6;\" border=\"0\">\n";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\" align=\"center\" colspan=\"2\">ATTENDANCE</td>\n";
                $table .= "</tr>\n";
                $table .= "<tr>\n";
                $table .= "<td style=\"font-weight:bold\">PRESENT</td>\n";
                $table .= "<td style=\"font-weight:bold; text-align:right;\">".($attendance_log["Present"] ?? 0)."</td>\n";
                $table .= "</tr>\n";
                $table .= "<tr>\n";
                $table .= "<td style=\"font-weight:bold\">ABSENT</td>\n";
                $table .= "<td style=\"font-weight:bold; text-align:right;\">".($attendance_log["Absent"] ?? 0)."</td>";
                $table .= "</tr>";
                $table .= "<tr>";
                $table .= "<td style=\"font-weight:bold\">TERM DAYS</td>";
                $table .= "<td style=\"font-weight:bold; text-align:right;\">".($attendance_log["Term"] ?? 0)."</td>\n";
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

                $table .= "<table style=\"margin-top:20px;\" cellpadding=\"5px\" border=\"0\" width=\"100%\">
                <tr style=\"padding:15px;\">
                    <td width=\"50%\">
                        <div style=\"font-size:17px; padding:10px; border: solid 1px #cccccc; text-align:center;\">
                            <div>&nbsp;</div>
                            <span style=\"font-weight:bold; font-size:15px\">TEACHER'S REMARKS</span>
                            <div style=\"font-size:17px; padding:10px;text-align:center;\">
                                ".limit_words($teacherRemarks[$key] ?? "N/A", 45)."
                            </div>
                            <div>&nbsp;</div>
                        </div>
                    </td>
                    <td>
                        <div style=\"font-size:17px; padding:10px; border: solid 1px #cccccc; text-align:center;\">
                           <div>&nbsp;</div>
                           <div>&nbsp;</div>
                           PRINCIPAL: .........................................................
                        </div>
                        <div>&nbsp;</div>
                        <div style=\"font-size:17px; padding:10px; border: solid 1px #cccccc; text-align:center;\">
                           <div>&nbsp;</div>
                           <div>&nbsp;</div>
                           DIRECTOR: .........................................................
                        </div>
                    </td>
                </tr>
                </table>";

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

    /**
     * Get Preschool Results
     * 
     * Retrieves preschool reporting results for a specific student
     * 
     * @param stdClass $params
     * @return Array
     */
    public function get_preschool_results(stdClass $params) {
        
        global $isTeacher, $isAdmin, $isEmployee;
        
        // Check permissions
        if(!$isTeacher && !$isAdmin && !$isEmployee) {
            return ["code" => 403, "data" => "Sorry! You do not have the permissions to view preschool results."];
        }
        
        // Validate required parameters
        if(empty($params->student_id) || empty($params->class_id)) {
            return ["code" => 400, "data" => "Student ID and Class ID are required."];
        }
        
        $student_id = xss_clean($params->student_id);
        $class_id = xss_clean($params->class_id);
        $clientId = $params->clientId ?? $params->clientId;
        
        try {
            // Check if results table exists, if not return empty results
            $stmt = $this->db->prepare("
                SELECT result_key, result_value 
                FROM preschool_results 
                WHERE student_id = ? AND class_id = ? AND client_id = ? AND academic_year = ? AND academic_term = ?
            ");
            $stmt->execute([$student_id, $class_id, $clientId, $this->academic_year, $this->academic_term]);
            
            $results = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[$row['result_key']] = $row['result_value'];
            }
            
            return [
                "code" => 200,
                "data" => $results
            ];
            
        } catch(PDOException $e) {
            // If table doesn't exist, return empty results
            return [
                "code" => 200,
                "data" => [
                    "result" => []
                ]
            ];
        }
    }

    /**
     * Save Preschool Result
     * 
     * Saves a single preschool reporting result for a student
     * 
     * @param stdClass $params
     * @return Array
     */
    public function save_preschool_result(stdClass $params) {
        
        global $isTeacher, $isAdmin, $isEmployee;
        
        // Check permissions
        if(!$isTeacher && !$isAdmin && !$isEmployee) {
            return ["code" => 403, "data" => "Sorry! You do not have the permissions to save preschool results."];
        }
        
        // Validate required parameters
        if(empty($params->student_id) || empty($params->class_id) || empty($params->result_key)) {
            return ["code" => 400, "data" => "Student ID, Class ID, and Result Key are required."];
        }
        
        $student_id = xss_clean($params->student_id);
        $class_id = xss_clean($params->class_id);
        $result_key = xss_clean($params->result_key);
        $result_value = isset($params->result_value) ? xss_clean($params->result_value) : '';
        $clientId = $params->clientId ?? $params->clientId;
        
        try {
            // Insert or update the result
            $stmt = $this->db->prepare("
                INSERT INTO preschool_results (student_id, class_id, client_id, result_key, result_value, academic_year, academic_term)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE result_value = VALUES(result_value), updated_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$student_id, $class_id, $clientId, $result_key, $result_value, $this->academic_year, $this->academic_term]);
            
            return [
                "code" => 200,
                "data" => "Result saved successfully."
            ];
            
        } catch(PDOException $e) {
            return [
                "code" => 500,
                "data" => "An error occurred while saving the result."
            ];
        }
    }

}
