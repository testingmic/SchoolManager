<?php 

class Analitics extends Myschoolgh {

    /** This variable will be used for the loading of the information */
    public $stream = [];
    public $final_report = [];
    private $iclient;
    public $current_title = "Today";
    public $previous_title = "Yesterday";

    private $class_id_query;
    private $class_idm_query;
    private $employee_id_query;

    public function __construct(stdClass $params) {

        parent::__construct();

        $this->default_stream = [
            "summary_report", "students_report", "revenue_flow", "library_report", 
            "departments_report", "attendance_report", "class_attendance_report",
            "salary_report"
        ];

        $this->error_codes = [
            "invalid-date" => "Sorry! An invalid date was parsed for the start date.",
            "invalid-range" => "Sorry! An invalid date was parsed for the end date.",
            "exceeds-today" => "Sorry! The date date must not exceed today's date",
            "exceeds-count" => "Sorry! The days between the two ranges must not exceed 366 days",
            "invalid-prevdate" => "Sorry! The start date must not exceed the end date.",
        ];

        // get the client data
        $client_data = $params->client_data;

        // run this query
        $academics = $client_data->client_preferences->academics;
        
        $this->iclient = $params;
        $this->academic_term = $academics->academic_term;
        $this->academic_year = $academics->academic_year;

        $this->this_term_starts = $academics->term_starts;
        $this->this_term_ends = $academics->term_ends;

        $this->last_term_starts = $academics->last_term_starts ?? $academics->term_starts;
        $this->last_term_ends = $academics->last_term_ends ?? $academics->term_ends;

    }

    /**
     * This will be used for the generation of reports
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function generate(stdClass $params) {

        /** set the date period */
        $params->period = $params->period ?? "last_14days";

        /** Convert the stream into an array list */
        $params->stream = isset($params->label["stream"]) && !empty($params->label["stream"]) ? $this->stringToArray($params->label["stream"]) : $this->default_stream;
        $this->info_to_stream = $params->stream;

        /** Preformat date */
        if(in_array($params->period, array_keys($this->accepted_period))) {

            /** Get the date formatter */
            $the_date = $this->format_date($params->period);

            /** Save the user period parsed */
            $user_pref = (object) [
                "userData" => $params->userData,
                "reports_auto_push" => true,
                "label" => [
                    "reports" => [
                        "period" => $params->period
                    ]
                ]
            ];
            // $usersClass->preference($user_pref);            
        } else {
            // bypass the query period
            $bypass = (bool) in_array("attendance_report", $params->stream);

            // format the date to use
            $the_date = $this->preformat_date($params->period, $bypass);
        }

        /** If invalid date then end the query */
        if(in_array($the_date, array_keys($this->error_codes))) {
            return ["code" => 203, "data" => $this->error_codes[$the_date]];
        }

        // set the period in session
        $this->session->set("reportPeriod", $params->period);

        // date ranges to use for the query
        $this->date_range = [
            "previous" => [
                "start" => $this->prevstart_date,
                "end" => $this->prevend_date,
                "title" => $this->previous_title
            ],
            "current" => [
                "start" => $this->start_date,
                "end" => $this->end_date,
                "title" => $this->current_title
            ]
        ];

        // confirm if the user pushed a query set
        $this->user_status = isset($params->label["user_status"]) ? $params->label["user_status"] : "Active";
        $this->class_id_query = (isset($params->label["class_id"]) && !empty($params->label["class_id"])) ? $params->label["class_id"] : null;
        $this->class_idm_query = (isset($params->label["class_id"]) && !empty($params->label["class_id"])) ? " AND a.class_id IN {$this->inList($params->label["class_id"])}" : null;
        $this->class_idd_query = (isset($params->label["class_id"]) && !empty($params->label["class_id"])) ? " AND a.id IN {$this->inList($params->label["class_id"])}" : null;
        $this->student_id_query = (isset($params->label["student_id"])&& !empty($params->label["student_id"])) ? " AND a.student_id = '{$params->label["student_id"]}'" : null;
        $this->employee_id_query = (isset($params->label["employee_id"]) && !empty($params->label["employee_id"])) ? " AND a.employee_id IN {$this->inList($params->label["employee_id"])}" : null;
        $this->fees_category_id = isset($params->label["category_id"]) ? " AND a.category_id IN {$this->inList($params->label["category_id"])}" : null;
        $this->query_date_range = isset($params->label["stream_period"]) ? $this->stringToArray($params->label["stream_period"]) : ["current", "previous"];

        // append the arange information
        $this->final_report["date_range"] = $this->date_range;

        // get the clients information if not parsed in the stream
        if(in_array("summary_report", $params->stream)) {
            // query the clients data
            $this->final_report["summary_report"] = $this->summary_report($params);
            // get the percentage difference between the current and previous
            $this->calculate_percentages($this->final_report, "summary_report.students_class_record_count.count");
        }

        // get the revenue information if not parsed in the stream
        if(in_array("revenue_flow", $params->stream)) {
            // query the revenue data
            $this->final_report["revenue_flow"] = $this->revenue_flow($params);
        }

        // get the salary information if not parsed in the stream
        if(in_array("salary_report", $params->stream)) {
            // query the salary data
            $this->final_report["salary_report"] = $this->salary_report($params);
        }

        // get the library information if not parsed in the stream
        if(in_array("library_report", $params->stream)) {
            // append this paramter
            $params->load_summary_info = true;
            // query the library data
            $this->final_report["library_report"] = $this->library_report($params);
        }

        // get the departments information if not parsed in the stream
        if(in_array("attendance_report", $params->stream)) {
            // append this paramter
            $params->load_summary_info = true;
            // query the departments data
            $this->final_report["attendance_report"] = $this->attendance_report($params);
        }

        // get the department information if not parsed in the stream
        if(in_array("departments_report", $params->stream)) {
            // append this paramter
            $params->load_summary_info = true;
            // query the department data
            $this->final_report["departments_report"] = $this->departments_report($params);
        }

        // get the class attendance information if not parsed in the stream
        if(in_array("class_attendance_report", $this->info_to_stream)) {
            // query the class attendance data
            $this->final_report["attendance_report"] = $this->attendance_report($params);
        }

        return $this->final_report;
        
    }

    /**
     * Generate Summary Counts Report
     * 
     * Loop through the various user types and get the total counts and that of the current and previous values
     * 
     * @return Array
     */
    public function summary_report(stdClass $params) {
        
        global $usersClass;
        $result = [];
        $sex_group = [];

        /**
         * Loop through the user roles and get the count per each user category
         * 
         * Also, get the total number of male and female records
         */
        foreach($this->the_user_roles as $role => $value) {
            
            /** Parameter */
            $client_param = (Object) [
                "reporting" => true,
                "user_type" => $role,
                "remove_user_data" => true,
                "userId" => $params->userId,
                "return_where_clause" => true,
                "user_status" => $this->user_status,
                "academic_term" => $this->academic_term,
                "academic_year" => $this->academic_year,
            ];
            if($role === "parent") {
                $client_param->no_academic_year = true;
            } else {
                $client_param->no_academic_year = false;
            }
            $where_clause = $usersClass->list($client_param);

            // value_name
            $value_count = "total_{$role}s_count";

            $query = $this->db->prepare("SELECT COUNT(*) AS {$value_count} FROM users a WHERE {$where_clause} AND status='1'");
            $query->execute([$this->user_status]);
            $result["users_record_count"]["count"][$value_count] = $query->fetch(PDO::FETCH_OBJ)->{$value_count} ?? 0;

            // set the sex
            foreach(["Male", "Female"] as $gender) {

                // get the user gender
                $client_param->gender = $gender;

                // set a where clause
                $where_clause = $usersClass->list($client_param);

                // run a query for the user count
                $query = $this->db->prepare("SELECT COUNT(*) AS {$value_count} FROM users a WHERE {$where_clause} AND status='1'");
                $query->execute([$this->user_status]);

                // append to the result array
                $result["users_record_count"]["gender_count"][$gender][$value_count] = $query->fetch(PDO::FETCH_OBJ)->{$value_count} ?? 0;
            }

            $client_param->gender = null;

            // loop through the date ranges for the current and previous
            foreach($this->date_range as $range_key => $range_value) {
                
                // set the date range
                $client_param->date_range = "{$range_value["start"]}:{$range_value["end"]}";

                // set a where clause
                $where_clause = $usersClass->list($client_param);

                // run a query for the user count
                $query = $this->db->prepare("SELECT COUNT(*) AS {$value_count} FROM users a WHERE {$where_clause}");
                $query->execute([$this->user_status]);

                // append to the result array
                $result["users_record_count"]["comparison"][$range_key][$value_count] = $query->fetch(PDO::FETCH_OBJ)->{$value_count} ?? 0;

            }

        }

        /**
         * Processing the Class Students Count
         * 
         * Load the fees paid count by category and get the amounts paid per category
         * Run a comparison between the current and previous record set
         */
        // get the fees categories
        $class_list = $this->pushQuery("a.id, a.name", "classes a", "a.status='1' AND a.client_id='{$params->clientId}' {$this->class_idd_query}");
        $result["students_class_record_count"]["total_classes_count"] = count($class_list);

        // load the fees records
        foreach($class_list as $key => $value) {
            
            /** Parameter */
            $client_param = (Object) [
                "userId" => $params->userId,
                "user_type" => "student",
                "class_id" => $value->id,
                "user_status" => $this->user_status,
                "reporting" => true,
                "remove_user_data" => true,
                "return_where_clause" => true,
            ];
            $where_clause = $usersClass->list($client_param);

            // value_name
            $class_name = create_slug($value->name, "_");
            $value_count = "{$class_name}_total_count";

            $query = $this->db->prepare("SELECT COUNT(*) AS {$value_count} FROM users a WHERE {$where_clause}");
            $query->execute([$this->user_status]);
            $_q_result = $query->fetch(PDO::FETCH_OBJ);

            $result["students_class_record_count"]["count"][$value_count] = [
                "value" => $_q_result->{$value_count} ?? 0,
                "name" => $value->name
            ];

            // loop through the date ranges for the current and previous
            foreach($this->date_range as $range_key => $range_value) {
                
                // set the date range
                $client_param->date_range = "{$range_value["start"]}:{$range_value["end"]}";

                // set a where clause
                $where_clause = $usersClass->list($client_param);

                // run a query for the user count
                $query = $this->db->prepare("SELECT COUNT(*) AS {$value_count} FROM users a WHERE {$where_clause}");
                $query->execute([$this->user_status]);
                $_q_result = $query->fetch(PDO::FETCH_OBJ);

                // append to the result array
                $result["students_class_record_count"]["comparison"][$range_key][$value_count] = [
                    "value" => $_q_result->{$value_count} ?? 0,
                    "name" => $value->name
                ];
            }

        }
        
        /**
         * Processing the Request for Fees
         * 
         * Load the fees paid count by category and get the amounts paid per category
         * Run a comparison between the current and previous record set
         */
        $feesClass = load_class("fees", "controllers", $this->iclient);

        // get the fees categories
        $fees_category_list = $this->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$params->clientId}'");
        $result["fees_record_count"]["total_count"] = count($fees_category_list);

        // load the fees records
        foreach($fees_category_list as $key => $value) {
            
            /** Parameter */
            $fees_param = (Object) [
                "limit" => 100000,
                "userId" => $params->userId,
                "userData" => $params->userData,
                "class_id" => $this->class_id_query,
                "category_id" => $value->id,
                "date_range" => "{$this->start_date}:{$this->end_date}",
                "return_where_clause" => true
            ];
            $where_clause = $feesClass->list($fees_param);

            // value_name
            $raw_name = create_slug($value->name, "_");
            $amount_paid = $raw_name;
            $value_count = "{$raw_name}_count";

            $query = $this->db->prepare("SELECT 
                    COUNT(*) AS {$value_count},
                    SUM(amount) AS {$amount_paid}
                FROM fees_collection a
                LEFT JOIN users u ON u.item_id = a.student_id
                WHERE {$where_clause}
            ");
            $query->execute([$this->user_status]);
            $q_result = $query->fetch(PDO::FETCH_OBJ);
            
            // append the result values
            $result["fees_record_count"]["count"][$value->name] = $q_result->{$value_count} ?? 0;
            $result["fees_record_count"]["amount"][$value->name] = $q_result->{$amount_paid} ?? 0;
            
            // loop through the date ranges for the current and previous
            foreach($this->date_range as $range_key => $range_value) {
                  
                // set the date range
                $fees_param->date_range = "{$range_value["start"]}:{$range_value["end"]}";
                
                // set a where clause
                $_where_clause = $feesClass->list($fees_param);

                // run a query for the user count
                $_query = $this->db->prepare("SELECT 
                        COUNT(*) AS {$value_count}, 
                        SUM(amount) AS {$amount_paid}
                    FROM fees_collection a 
                    LEFT JOIN users u ON u.item_id = a.student_id
                    WHERE {$_where_clause}
                ");
                $_query->execute([$this->user_status]);
                $_q_result = $_query->fetch(PDO::FETCH_OBJ);
                
                // append to the result array
                $result["fees_record_count"]["comparison"]["count"][$range_key][$value->name] = [
                    "value" => $_q_result->{$value_count} ?? 0,
                    "name" => $value->name
                ];
                $result["fees_record_count"]["comparison"]["amount"][$range_key][$value->name] = [
                    "value" => $_q_result->{$amount_paid} ?? 0,
                    "name" => $value->name
                ];
            }

        }

        return $result;
    }

    /**
     * Library Manager
     * 
     * Loop through the dates and categories of the categories type and get the records
     * 
     * @return Array
     */
    public function library_report(stdClass $params) {

        try {
            
            /** Set the parameters */
            $result = [];

            // if the summary load was parsed
            if(isset($params->load_summary_info)) {

                // set the quick information
                $params->quick_analitics_load = true;

                /** Load the Books */
                $result["library_category_list"] = load_class("library", "controllers")->category_list($params);

                /** Get the books count */
                $result["library_category_count"] = count($result["library_category_list"]);
                $result["library_books_count"] = array_sum(array_column($result["library_category_list"], "books_count"));

            }

            // return the result
            return $result;

        } catch(PDOException $e) {

        }
    }

    /**
     * Departments Manager
     * 
     * Loop through the dates and departments
     * 
     * @return Array
     */
    public function departments_report(stdClass $params) {

        try {
            
            /** Set the parameters */
            $result = [];

            // if the summary load was parsed
            if(isset($params->load_summary_info)) {

                // set the quick information
                $params->quick_analitics_load = true;

                /** Load the Books */
                $result["departments_list"] = load_class("departments", "controllers")->list($params);

                /** Get the books count */
                $result["departments_count"] = count($result["departments_list"]);
            }

            // return the result
            return $result;

        } catch(PDOException $e) {

        }
    }

    /**
     * Attendance Manager
     * 
     * Loop through the dates and Attendance
     * 
     * @return Array
     */
    public function attendance_report(stdClass $params) {

        try {
            
            /** Set the parameters */
            $result = [];
            
            // create new object
            $attendClass = load_class("attendance", "controllers");

            // if finalized record is requested
            $params->is_finalized = true;

            // if the summary load was parsed
            if(isset($params->load_summary_info)) {
                
                // set additional parameters
                $params->start_date = isset($params->label["start_date"]) ? $params->label["start_date"] : $this->start_date;
                $params->end_date = isset($params->label["end_date"]) ? $params->label["end_date"] : $this->end_date;

                // set the quick information
                $user_type = $params->userData->user_type;
                $params->the_user_type = $user_type;
                
                // set the user types that each person can view
                if(in_array($user_type, ["admin", "accountant"])) {
                    $params->user_types_list = ["staff", "student"];
                } else {
                    $params->is_present_check = true;
                    $params->user_types_list = ($user_type === "parent") ? ["student"] : ($user_type === "student" ? ["student"] : ["staff"]);
                    $params->the_current_user_id = ($user_type === "parent") ? $this->session->student_id : $params->userId;
                }
                
                // load the attendance summary
                $result["attendance"] = $attendClass->range_summary($params);

            }

            // if class summary was also requested
            if(in_array("class_attendance_report", $params->stream)) {

                // load the attendance summary
                $params->load_date = isset($params->label["load_date"]) ? $params->label["load_date"] : date("Y-m-d");
                
                // load the class summary
                $result["class_summary"] = $attendClass->class_summary($params);
            }

            // return the result
            return $result;

        } catch(PDOException $e) {

        }
    }

    /**
     * Fees Record
     * 
     * Loop through the dates and categories of the fees type and get the records
     */

    /**
     * Generate report on the revenue flow
     * 
     * This gets the amount received grouped by hour/day/months as the case may be
     * 
     * @return Array
     */
    public function revenue_flow(stdClass $params) {

        try {

            // init
            $ranger = [];

            // loop through the date ranges for the current and previous
            foreach($this->date_range as $range_key => $range_value) {

                // confirm that the period was in the request
                if(in_array($range_key, $this->query_date_range)) {

                    /** The policies created for the period */
                    $stmt = $this->db->prepare("
                        SELECT 
                            COUNT(*) AS value, {$this->group_by}(a.recorded_date) AS value_date, SUM(a.amount) AS amount_value
                        FROM fees_collection a 
                        WHERE 
                            a.status = '1' AND a.reversed='0' {$this->student_id_query} {$this->fees_category_id}
                            AND (
                                DATE(a.recorded_date) >= '{$range_value["start"]}' AND DATE(a.recorded_date) <= '{$range_value["end"]}'
                            ) {$this->class_idm_query}
                        GROUP BY {$this->group_by}(a.recorded_date)
                    ");
                    $stmt->execute();
                    $counter = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $result["revenue_received_count"][$range_key]["data"] = $counter;

                    // create new array
                    $count_converted = [];
                    // convert to int
                    foreach($counter as $count) {
                        $count->value = (float) $count->value;
                        $count->amount_value = (float) $count->amount_value;
                        $count_converted[] = $count;
                    }
                    // assign the int cast value back to the array
                    $result["revenue_received_count"][$range_key]["data"] = $count_converted;

                    /** claims paid for the period */
                    $stmt = $this->db->prepare("
                        SELECT {$this->group_by}(a.recorded_date) AS value_date, SUM(a.amount) AS value 
                        FROM fees_collection a
                        WHERE a.status = '1' AND a.reversed = '0' {$this->student_id_query} {$this->fees_category_id}
                        AND (
                            DATE(a.recorded_date) >= '{$range_value["start"]}' AND DATE(a.recorded_date) <= '{$range_value["end"]}'
                        ) {$this->class_idm_query}
                        GROUP BY {$this->group_by}(a.recorded_date)
                    ");
                    $stmt->execute();
                    $amount_received = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $result["revenue_received_amount"][$range_key]["data"] = $amount_received;

                    // if the summary report was requested as well
                    if(in_array("summary_report", $params->stream)) {
                        $revenue_total_sum = !empty($amount_received) ? array_sum(array_column($amount_received, "value")) : 0;
                        $this->final_report["summary_report"][$range_key]["data"]["revenue_received_amount"] = $revenue_total_sum;
                    }

                    // loop through the results set
                    foreach($result as $the_key => $value) {
                        
                        // get the data to use
                        $the_data = isset($value[$range_key]) ? $value[$range_key]["data"] : [];

                        // combine the date and sales from the database into one set
                        $combined = array_combine(array_column($the_data, "value_date"), array_column($the_data, "value"));
                        
                        // labels check 
                        $listing = "list-days";
                        if($params->period == "today") {
                            $listing = "hour";
                        }

                        // if the period is a year
                        if(in_array($params->period, ["this_year", "last_6months", "last_year"])) {
                            $listing = "year-to-months";
                        }
                        
                        // replace the empty fields with 0
                        $replaceEmptyField = $this->value_replacer($listing, array_column($the_data, "value_date"), array($range_value["start"], $range_value["end"]));

                        // append the fresh dataset to the old dataset
                        $freshData = array_replace($combined, $replaceEmptyField);
                        ksort($freshData);

                        /** Labels control */
                        $labelArray = array_keys($freshData);
                        $labels = [];
                        // confirm which period we are dealing with
                        if($listing == "list-days") {
                            // change the labels to hours of day
                            foreach($labelArray as $value) {
                                $labels[] = date("jS M", strtotime($value));
                            }
                        } elseif($listing == "hour") {
                            // change the labels to hours of day
                            foreach($labelArray as $value) {
                                $labels[] = $this->convertToPeriod("hour", $value);
                            }
                        } elseif($listing == "year-to-months") {
                            // change the labels to hours of day
                            foreach($labelArray as $value) {
                                $labels[] = $this->convertToPeriod("month", $value, "F");
                            }
                        }

                        // Parse the amount into the chart array data
                        $resultData = [];
                        $resultData["labels"] = $labels;
                        $resultData["data"] = array_values($freshData);

                        $ranger[$range_key][$the_key]["data"] = $resultData;
                        $ranger[$range_key][$the_key]["period"] = $range_value;

                    }

                    /** The payments made for the period grouped by class */
                    $class_stmt = $this->db->prepare("
                        SELECT 
                            COUNT(*) AS value, a.class_id, SUM(a.amount) AS amount_value
                        FROM fees_collection a 
                        WHERE 
                            a.status = '1' AND a.reversed='0' {$this->student_id_query} {$this->fees_category_id}
                            AND (
                                DATE(a.recorded_date) >= '{$range_value["start"]}' AND DATE(a.recorded_date) <= '{$range_value["end"]}'
                            ) {$this->class_idm_query}
                        GROUP BY a.class_id
                    ");
                    $class_stmt->execute();
                    $class_counter = $class_stmt->fetchAll(PDO::FETCH_OBJ);
                    $result["revenue_received_byclass_count"][$range_key]["data"] = $class_counter;

                    // create new array
                    $class_count_converted = [];
                    // convert to int
                    foreach($class_counter as $count) {
                        $count->value = (float) $count->value;
                        $count->class_id = (float) $count->class_id;
                        $count->amount_value = (float) $count->amount_value;
                        $class_count_converted[] = $count;
                    }
                    // assign the int cast value back to the array
                    $result["revenue_received_byclass_count"][$range_key]["data"] = $class_count_converted;


                    /** The payments received for the period grouped by payment_method */
                    $payment_method_stmt = $this->db->prepare("
                        SELECT 
                            COUNT(*) AS value, a.payment_method, SUM(a.amount) AS amount_value
                        FROM fees_collection a 
                        WHERE 
                            a.status = '1' AND a.reversed='0' {$this->student_id_query} {$this->fees_category_id}
                            AND (
                                DATE(a.recorded_date) >= '{$range_value["start"]}' AND DATE(a.recorded_date) <= '{$range_value["end"]}'
                            ) {$this->class_idm_query}
                        GROUP BY a.payment_method
                    ");
                    $payment_method_stmt->execute();
                    $payment_method_counter = $payment_method_stmt->fetchAll(PDO::FETCH_OBJ);
                    $result["revenue_received_payment_method_count"][$range_key]["data"] = $payment_method_counter;

                    // create new array
                    $payment_method_count_converted = [];
                    // convert to int
                    foreach($payment_method_counter as $count) {
                        $count->value = (float) $count->value;
                        $count->payment_method = $count->payment_method;
                        $count->amount_value = (float) $count->amount_value;
                        $payment_method_count_converted[] = $count;
                    }
                    // assign the int cast value back to the array
                    $result["revenue_received_payment_method_count"][$range_key]["data"] = $payment_method_counter;

                    // append the period to to the array values
                    $result["revenue_received_count"][$range_key]["period"] = $range_value;
                    $result["revenue_received_amount"][$range_key]["period"] = $range_value;
                
                }

            }

            $result["grouped_list"] = $ranger;
            
            return $result;

        } catch(PDOException $e) {
            return [];
        }

    }

    /**
     * Generate report on the salary payment flow
     * 
     * This gets the amount received grouped by hour/day/months as the case may be
     * 
     * @return Array
     */
    public function salary_report(stdClass $params) {

        /** Salary Category Logs */
        $result = [];

        // get the salary categories
        $stmt = $this->db->prepare("
            SELECT 
                c.name, c.type,
                SUM(a.amount) AS amount,
                MONTH(b.payslip_month_id) AS month_id, MONTHNAME(b.payslip_month_id) AS month_name
            FROM payslips_details a
            LEFT JOIN payslips b ON b.id = a.payslip_id
            LEFT JOIN payslips_allowance_types c ON c.id = a.allowance_id
            WHERE 
                a.client_id = ? AND b.validated = ? AND b.deleted = ? 
                AND DATE(payslip_month_id) >= '{$this->start_date}' AND DATE(payslip_month_id) <= '{$this->end_date}'
                {$this->employee_id_query}
            GROUP BY MONTH(payslip_month_id), a.allowance_id
        ");
        $stmt->execute([$params->clientId, 1, 0]);
        $category_list = $stmt->fetchAll(PDO::FETCH_OBJ);

        $grouping = [];
        $category_array = [];
        foreach($category_list as $category) {
            $grouping[$category->type][] = $category->name;
            $category_array[$category->type][$category->month_name][$category->name] = $category;
        }

        $result["category"] = $category_array;

        $grouped = [];
        foreach($grouping as $key => $array) {
            $grouped[$key] = array_unique($grouping[$key]);
        }
        $result["grouping"] = $grouped;

        // get the fees categories
        $stmt = $this->db->prepare("
            SELECT 
                MONTH(a.payslip_month_id) AS month_id, MONTHNAME(a.payslip_month_id) AS month_name,
                SUM(a.basic_salary) AS basic_salary, SUM(a.total_allowance) AS total_allowance,
                SUM(a.gross_salary) AS gross_salary, SUM(a.total_deductions) AS total_deductions,
                SUM(a.net_salary) AS net_salary
            FROM payslips a
            WHERE 
                a.client_id = ? AND a.validated = ? AND a.deleted = ? 
                AND DATE(a.payslip_month_id) >= '{$this->start_date}' AND DATE(a.payslip_month_id) <= '{$this->end_date}'
                {$this->employee_id_query}
            GROUP BY MONTH(a.payslip_month_id)
        ");
        $stmt->execute([$params->clientId, 1, 0]);
        $summary = $stmt->fetchAll(PDO::FETCH_OBJ);

        $months_labels = array_column($summary, "month_name");
        $net_salary_array = array_column($summary, "net_salary");
        $allowances_array = array_column($summary, "total_allowance");
        $deductions_array = array_column($summary, "total_deductions");

        $result["salary_list"] = $summary;

        $result["chart"]["labels"] = $months_labels;
        $result["chart"]["data"] = $net_salary_array;
        $result["chart"]["comparison"] = ["allowances" => $allowances_array, "deductions" => $deductions_array];

        $result["summary"]["totals"]["basic_salary"] = [
            "title" => "Basic Salary",
            "total" => array_sum(array_column($summary, "basic_salary"))
        ];
        $result["summary"]["totals"]["total_allowance"] = [
            "title" => "Total Allowances",
            "total" => array_sum($allowances_array)
        ];
        $result["summary"]["totals"]["gross_salary"] = [
            "title" => "Gross Salary",
            "total" => array_sum(array_column($summary, "gross_salary"))
        ];
        $result["summary"]["totals"]["total_deductions"] = [
            "title" => "Total Deductions",
            "total" => array_sum($deductions_array)
        ];
        $result["summary"]["totals"]["net_salary"] = [
            "title" => "Net Salary",
            "total" => array_sum($net_salary_array)
        ];
        
        return $result;


    }

    /**
     * Preformat the date
     * 
     * This algo formats the dates that have been submitted by the user
     * 
     * @param String $period        This is the date to process
     */
    public function preformat_date($period, $bypass = false) {

        /** initial variables */
        $today = date("Y-m-d");
        $explode = explode(":", $period);
        $explode[1] = isset($explode[1]) ? $explode[1] : date("Y-m-d");

        /** Confirm that a valid date was parsed */
        if(!$this->validDate($explode[0])) {
            return "invalid-date";
        }

        /** If the next param was set */
        if(isset($explode[1]) && !$this->validDate($explode[1])) {
            return "invalid-range";
        }

        /** Confirm that the last date is not more than today */
        if(isset($explode[1]) && strtotime($explode[1]) > strtotime($today) && !$bypass) {
            return "exceeds-today";
        }

        /** confirm that the starting date is not greater than the end date */
        if(isset($explode[1]) && strtotime($explode[0]) > strtotime($explode[1])) {
            return "invalid-prevdate";
        }

        /** Confirm valid dates */
        if(!preg_match("/^[0-9-]+$/", $explode[0]) || !preg_match("/^[0-9-]+$/", $explode[1])) {
            return "invalid-range";
        }

        /** Check the days difference */
        $days_list = $this->listDays($explode[0], $explode[1]);
        $count = count($days_list);

        /** ensure that the days count does not exceed 90 days */
        if($count > 366) {
            return "exceeds-count";
        }

        $format = "jS M Y";
        $group = "DATE";
        if($count >= 32 && $count <= 60) {
            $group = "MONTH";
            $format = "F";
        }
        
        $this->start_date = $days_list[0];
        $this->end_date = end($days_list);
        $this->group_by = $group;
        $this->current_title = "Past {$count} days";
        $this->previous_title = "Previous {$count} days";
        $this->date_format = $format;
        $this->prevstart_date = date("Y-m-d", strtotime("today -".($count * 2)." days"));
        $this->prevend_date = date("Y-m-d", strtotime("today -{$count} days"));

        return $this;

    }

	/**
     * This formats the correct date range
     *  
     * @param String    $datePeriod      This is the date period that was parsed
     * 
     * @return This     $this->start_date, $this->end_date;
     */
    public function format_date($datePeriod = "this_week") {

        // Check Sales Period
        switch ($datePeriod) {
            case 'this_term':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "This Term";
                $previousTitle = "Last Term";
                $dateFrom = $this->this_term_starts;
                $dateTo = $this->this_term_ends;
                $prevFrom = $this->last_term_starts;
                $prevTo = $this->last_term_ends;
                break;
            case 'this_week':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "This Week";
                $previousTitle = "Last Week";
                $dateFrom = date("Y-m-d", strtotime("today -1 weeks"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("today -2 weeks"));
                $prevTo = date("Y-m-d", strtotime("today -1 weeks"));
                break;
            case 'last_week':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "Last Weeks";
                $previousTitle = "Last 2 Weeks";
                $dateFrom = date("Y-m-d", strtotime("-2 weeks"));
                $dateTo = date("Y-m-d", strtotime("-1 weeks"));
                $prevFrom = date("Y-m-d", strtotime("today -3 weeks"));
                $prevTo = date("Y-m-d", strtotime("today -2 weeks"));
                break;
            case 'this_month':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "This Month";
                $previousTitle = "Last Month";
                $dateFrom = date("Y-m-01");
                $dateTo = date("Y-m-t");
                $prevFrom = date("Y-m-01", strtotime("last month"));
                $prevTo = date("Y-m-t", strtotime("last month"));
                break;
            case 'last_month':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "Last Months";
                $previousTitle = "Last 2 Months";
                $dateFrom = date("Y-m-01", strtotime("last month"));
                $dateTo = date("Y-m-t", strtotime("last month"));
                $prevFrom = date("Y-m-01", strtotime("last 2 month"));
                $prevTo = date("Y-m-t", strtotime("last 2 month"));
                break;
            case 'last_14days':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "Last 14 Days";
                $previousTitle = "Previous 14 Days";
                $dateFrom = date("Y-m-d", strtotime("-2 weeks"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("-4 weeks"));
                $prevTo = date("Y-m-d", strtotime("-2 weeks"));
                break;
            case 'last_30days':
                $groupBy = "DATE";
                $format = "jS M Y";
                $currentTitle = "Last 30 Days";
                $previousTitle = "Previous 30 Days";
                $dateFrom = date("Y-m-d", strtotime("-30 days"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("-60 days"));
                $prevTo = date("Y-m-d", strtotime("-30 days"));
                break;
            case 'last_3months':
                $groupBy = "MONTH";
                $format = "jS M Y";
                $currentTitle = "Last 3 months";
                $previousTitle = "Previous 3 months";
                $dateFrom = date("Y-m-d", strtotime("today -3 months"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("today -6 months"));
                $prevTo = date("Y-m-d", strtotime("today -3 months"));
                break;
            case 'last_6months':
                $groupBy = "MONTH";
                $format = "jS M Y";
                $currentTitle = "Last 6 Months";
                $previousTitle = "Previous 6 Months";
                $dateFrom = date("Y-m-d", strtotime("today -6 months"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-01", strtotime("today -12 months"));
                $prevTo = date("Y-m-t", strtotime("today -6 months"));
                break;
            case 'this_year':
                $groupBy = "MONTH";
                $format = "F";
                $dateFrom = date('Y-01-01');
                $dateTo = date('Y-12-31');
                $currentTitle = "This Year";
                $previousTitle = "Last Year";
                $prevFrom = date("Y-01-01", strtotime("last year"));
                $prevTo = date("Y-12-31", strtotime("last year"));
                break;
            case 'last_year':
                $groupBy = "MONTH";
                $format = "F";
                $currentTitle = "Last Year";
                $previousTitle = "Last Year";
                $dateFrom = date('Y-01-01', strtotime("last year"));
                $dateTo = date('Y-12-31', strtotime("last year"));
                $prevFrom = date('Y-01-01', strtotime("-2 years"));
                $prevTo = date('Y-12-31', strtotime("-2 years"));
                break;
            default:
				$groupBy = "HOUR";
                $format = "jS M Y";
                $currentTitle = "Today";
                $previousTitle = "Yesterday";
                $dateFrom = date("Y-m-d", strtotime("today -1 days"));
                $dateTo = date("Y-m-d", strtotime("today"));
                $prevFrom = date("Y-m-d", strtotime("today -2 days"));
                $prevTo = date("Y-m-d", strtotime("today -1 days"));
                break;
        }

        $this->start_date = $dateFrom;
        $this->end_date = $dateTo;
        $this->prevstart_date = $prevFrom;
        $this->prevend_date = $prevTo;
        $this->current_title = $currentTitle;
        $this->previous_title = $previousTitle;
        $this->group_by = $groupBy;
        $this->date_format = $format;

        return $this;

    }

    /**
     * Replace empty dates with 0 for that day
     * 
     * @param String $rangeSet
     * @param Array $dataSet
     * @param Array $dateRange
     * 
     * @return Array
     */
    public function value_replacer($rangeSet, $dataSet, $dateRange = [], $weekDays = null) {
		// return data set
		$returnDataSet = [];

		if($rangeSet == "list-days") {
			// set the dates
			$dates = array();
			
			// get the list of days
			$datesList = $this->listDays($dateRange[0], $dateRange[1]);
			
			$notFoundDays = array_diff($datesList, $dataSet);
			$dataSet = [];

			foreach($notFoundDays as $value) {
				$dataSet[$value] = 0;
			}
			// check the rangeset that has been parsed
		} elseif($rangeSet == "hour") {
			// set the first parameter for the hours
			$hourOfDay = array();

			// get the hours for the day
			$hours = range(0, 23, 1);
			
			// loop through the hours
			foreach ($hours as $hr) {
				$hourOfDay[] = $hr;
			}
			
			// find the difference in the two array set
			$notFoundHours = array_diff($hourOfDay, $dataSet);
			$dataSet = [];
			
			foreach($notFoundHours as $value) {
				$dataSet[$value] = 0.00;
			}
		} elseif($rangeSet == "year-to-months") {
			// set the first parameter for the hours
			$monthsOfYear = array();

			// loop through the hours
			for($i=1; $i <= 12; $i++) {
				$monthsOfYear[] = $i;
			}

			// find the difference in the two array set
			$notFoundMonths = array_diff($monthsOfYear, $dataSet);
			
			$dataSet = [];
			foreach($notFoundMonths as $value) {
				$dataSet[$value] = 0.00;
			}
		}

		return $dataSet;
	}

    /**
     * Convert to string to a valid date form
     * 
     * @param String $timeFrame
     * @param String $period
     * @param String $format
     * 
     * @return String
     */
    public function convertToPeriod($timeFrame, $period, $format='Y-m-01') {
		// Check the time frame hourly
		if($timeFrame == "hour") {
			// get the hours for the day
			$hours = range(0, 23, 1);
			// loop through the hours
			foreach ($hours as $hr) {
				if($hr == $period) {
					return date('hA', strtotime("today +$hr hours"));
					break;
				}
			}
		}
		// Check the time frame monthly
		elseif($timeFrame == "month") {
			// get the months for the day
			$months = range(0, 11, 1);
			// loop through the months
			foreach ($months as $hr) {
				if($hr == ($period-1)) {
					return date($format, strtotime("January +$hr month"));
					break;
				}
			}
		} else {
			//return $period;
		}
	}

    /**
     * Calculate Percentages
     * 
     * Loop through the array, get the match and find the percentage difference between 
     * the previous and the current values.
     * 
     * @return Array
     */
    public function calculate_percentages($array_data, $section) {

        // split the section
        $section = explode(".", $section);
        $array_list = $array_data[$section[0]];

        // set the item
        if(isset($section[1])) {
            $array_list = $array_data[$section[0]][$section[1]] ?? [];
        }
        // set the item
        if(isset($section[2])) {
            $array_list = $array_data[$section[0]][$section[1]][$section[2]] ?? [];
        }

        // get the array key
        $total_value = array_sum(array_column($array_list, "value"));

        foreach($array_list as $key => $value) {
            $percentage = $value["value"] > 0 ? (($value["value"] / $total_value) * 100) : 0;
            $array_data[$section[0]][$section[1]][$section[2]][$key]["percentage"] = $percentage;
        }

        $this->final_report = $array_data;

        return $this;

    }

    /**
     * Percentage difference
     * 
     * Loop through the array, get the match and find the percentage difference between 
     * the previous and the current values.
     * 
     * @return Array
     */
    public function array_percentage_diff($array_data) {

        // get the array key
        $difference = [];
        $array_key = array_keys($array_data);
        
        // if its an array
        if(is_array($array_data[$array_key[0]])) {

            // loop through the summary information
            foreach($array_data[$array_key[0]] as $key => $value) {
                
                // confirm that the value is an array
                if(is_array($value) && isset($value["data"])) {
                    
                    // loop through the data array
                    foreach($value["data"] as $kkey => $kvalue) {

                        // get the raw_value
                        $raw_value = !is_array($kvalue) ? (isset($difference[$kkey]["value"]) ? $kvalue - $difference[$kkey]["value"] : $kvalue) : null;
                        
                        // confirm if the value already exists
                        if(isset($difference[$kkey]["value"])) {
                            // then the new value is the current one
                            $percentage = $this->percentage_diff($kvalue, $difference[$kkey]["value"]);
                        } else {
                            $percentage = $this->percentage_diff($kvalue, 0);
                        }

                        // find the difference
                        $difference[$kkey] = [
                            "percentage" => $percentage,
                            "value" => $raw_value,
                        ];

                    }                    

                }

            }
            
        }

        return $this->final_report[$array_key[0]]["difference"] = $difference;

    }
    
	/**
	 * percentage_diff
	 * 
	 * Find the percentage difference between two values
	 * 
	 * @return String
	 */
	public function percentage_diff($current_value, $previous_value, $rate=null) {

		$percentage = 0;

		if(strlen($previous_value) > 0) {

			// confirm that each value is a valid integer number
			if(!preg_match("/^[0-9.]+$/", $current_value) || !preg_match("/^[0-9.]+$/", $previous_value)) {
				return;
			}
			

			$difference = ($current_value - $previous_value);

			if($current_value != 0){
				$percentage = ( ($current_value - $previous_value) / ($current_value + $previous_value) ) * 100;
			}

			$percentage = ($percentage < 0) ? ($percentage * -1) : $percentage;

			if($previous_value > $current_value) {
				$class = "text-danger";
				$prefix = '<i class="fa fa-arrow-down"></i>';
			} else {
				$class = "text-success";
				$prefix = '<i class="fa fa-arrow-up"></i>';
			}

			if($current_value == 0){
				$percentile = 100;
			} else {
				$percentile = number_format(round($percentage), 0);
			}

			$money = ($rate) ? number_format($difference, 2) : number_format($difference, 0);

			return [
				"class" => $class,
				"value" => $percentile,
				"text" => ' <span class="bold">'. $percentile . "%</span> " . $prefix
			];

		} else {
			return '<i class="fa fa-arrow-circle-up fa-2x text-success"></i><span class="bold">0.00 (0%)</span>';
		}
	}

}