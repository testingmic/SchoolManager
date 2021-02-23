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
        $students_list = $this->pushQuery("name, unique_id", "users", "class_id='{$class_name[0]->id}' AND client_id='{$params->clientId}' AND user_status='Active' AND user_type='student'");
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

        $csv_file .= "Average Score,";
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

}
