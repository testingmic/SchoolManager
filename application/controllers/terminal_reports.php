<?php 
class Terminal_reports extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
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
                    $report_table .= "<td><input ".($columns["columns"][$file_headers[$kkey]] == "100" ? "disabled='disabled' data-input_total_id='{$key}'" : "data-input_type='score' data-input_row_id='{$key}'" )." class='form-control font-18 text-center' name='{$column}' min='0' max='1000' type='number' value='{$kvalue}'></td>";
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

        if(!is_array($params->report_sheet)) {
            return ["code" => 203, "data" => "Sorry! The report_sheet parameter must be an array."];
        }

        $report = (object) $params->report_sheet;

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
            $class_name = $class_item[0];

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
            $stmt = $this->db->prepare("INSERT INTO grading_terminal_scores SET 
                course_id = ?, course_name = ?, course_code = ?, scores = ?, total_score = ?, 
                average_score = ?, teacher_ids = ?, class_teacher_remarks = ?
            ");

            // loop through the list and insert the record
            foreach($scores_array as $student) {

            }
            
            return $scores_array;

        } catch(PDOException $e) {}
    }

}
