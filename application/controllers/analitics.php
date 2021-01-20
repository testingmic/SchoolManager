<?php 

class Analitics extends Myschoolgh {

    /** This variable will be used for the loading of the information */
    public $stream = [];
    public $current_title = "Today";
    public $previous_title = "Yesterday";
    public $final_report = [];

    public function __construct() {

        parent::__construct();

        $this->default_stream = [
            "summary_report", "students_report"
        ];

        $this->accepted_period = [
            "this_week", "last_week", "last_14days", "last_30days", "today",
            "this_month", "last_month", "last_3months", "last_6months", "this_year", "last_year", 
        ];

        $this->error_codes = [
            "invalid-date" => "Sorry! An invalid date was parsed for the start date.",
            "invalid-range" => "Sorry! An invalid date was parsed for the end date.",
            "exceeds-today" => "Sorry! The date date must not exceed today's date",
            "exceeds-count" => "Sorry! The days between the two ranges must not exceed 366 days",
            "invalid-prevdate" => "Sorry! The start date must not exceed the end date.",
        ];
    }

    /**
     * This will be used for the generation of reports
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function generate(stdClass $params) {

        /** Global variables */
        global $usersClass;

        /** set the date period */
        $params->period = $params->period ?? "this_month"; //"this_week";

        /** Convert the stream into an array list */
        $params->stream = isset($params->label["stream"]) && !empty($params->label["stream"]) ? $this->stringToArray($params->label["stream"]) : $this->default_stream;

        /** Preformat date */
        if(in_array($params->period, $this->accepted_period)) {

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
            $usersClass->preference($user_pref);
            
        } else {
            $the_date = $this->preformat_date($params->period);
        }

        /** If invalid date then end the query */
        if(in_array($the_date, array_keys($this->error_codes))) {
            return ["code" => 203, "data" => $this->error_codes[$the_date]];
        }

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
        $this->query_date_range = isset($params->label["stream_period"]) ? $this->stringToArray($params->label["stream_period"]) : ["current", "previous"];
        $this->user_status = isset($params->label["user_status"]) ? $params->label["user_status"] : "Active";

        // append the arange information
        $this->final_report["date_range"] = $this->date_range;

        // get the clients information if not parsed in the stream
        if(in_array("summary_report", $params->stream)) {
            // query the clients data
            $this->final_report["summary_report"] = $this->summary_report($params);
            // get the percentage difference between the current and previous
            $this->calculate_percentages($this->final_report, "summary_report.students_class_record_count.count");
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
    public function summary_report($params) {
        
        global $usersClass;
        $result = [];
        $ranger = [];

        foreach($this->the_user_roles as $role => $value) {
            
            /** Parameter */
            $client_param = (Object) [
                "userId" => $params->userId,
                "user_type" => $role,
                "user_status" => $this->user_status,
                "reporting" => true,
                "remove_user_data" => true,
                "return_where_clause" => true,
            ];
            $where_clause = $usersClass->list($client_param);

            // value_name
            $value_count = "total_{$role}s_count";

            $query = $this->db->prepare("SELECT COUNT(*) AS {$value_count} FROM users a WHERE {$where_clause}");
            $query->execute([$this->user_status]);
            $result["users_record_count"]["count"][$value_count] = $query->fetch(PDO::FETCH_OBJ)->{$value_count} ?? 0;

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
        $class_list = $this->pushQuery("id, name", "classes", "status='1' AND client_id='{$params->clientId}'");

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
        // create a new object
        $feesClass = load_class("fees", "controllers");

        // get the fees categories
        $fees_category_list = $this->pushQuery("id, name", "fees_category", "status='1' AND client_id='{$params->clientId}'");

        // load the fees records
        foreach($fees_category_list as $key => $value) {
            
            /** Parameter */
            $fees_param = (Object) [
                "limit" => 100000,
                "userId" => $params->userId,
                "userData" => $params->userData,
                "category_id" => $value->id,
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
                FROM fees_collection a WHERE {$where_clause}
            ");
            $query->execute([$this->user_status]);
            $q_result = $query->fetch(PDO::FETCH_OBJ);
            
            // append the result values
            $result["fees_record_count"]["count"][$value_count] = $q_result->{$value_count} ?? 0;
            $result["fees_record_count"]["amount"][$amount_paid] = $q_result->{$amount_paid} ?? 0;
            
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
                    FROM fees_collection a WHERE {$_where_clause}
                ");
                $_query->execute([$this->user_status]);
                $_q_result = $_query->fetch(PDO::FETCH_OBJ);
                
                // append to the result array
                $result["fees_record_count"]["comparison"]["count"][$range_key][$value_count] = [
                    "value" => $_q_result->{$value_count} ?? 0,
                    "name" => $value->name
                ];
                $result["fees_record_count"]["comparison"]["amount"][$range_key][$amount_paid] = [
                    "value" => $_q_result->{$amount_paid} ?? 0,
                    "name" => $value->name
                ];
            }

        }

        return $result;
    }

    /**
     * Generate Students Report
     */
    public function _students_report($params) {
        
        try {

            // init
            global $usersClass;
            $result = [];
            $ranger = [];

            // loop through the date ranges for the current and previous
            foreach($this->date_range as $range_key => $range_value) {

                // confirm that the period was in the request
                if(in_array($range_key, $this->query_date_range)) {

                    /** Parameter */
                    $client_param = (Object) [
                        "date_range" => "{$range_value["start"]}:{$range_value["end"]}",
                        "userId" => $params->userId,
                        "userData" => $params->userData,
                        "user_type" => "student",
                        "limit" => 200000,
                        "exempt_admin_users" => true,
                        "reporting" => true,
                        "minified" => "reporting_list",
                        "remote" => true
                    ];

                    // requests timelines
                    $clients_query = $usersClass->list($client_param);
                    $result["users_count"][$range_key] = $clients_query;

                    // if the summary report was requested as well
                    if(in_array("summary_report", $params->stream)) {
                        $this->final_report["summary_report"][$range_key]["data"]["clients_count"] = $clients_query["clients_count"];
                        $this->final_report["summary_report"][$range_key]["data"]["all_users_count"] = $clients_query["total_count"];
                    }

                    // get the where clause for generating the report
                    $client_param->addition_query = null;
                    $client_param->return_where_clause = true;
                    $client_param->user_type = "user,business";
                    $where_clause = $usersClass->list($client_param);

                    /** The policies created for the period */
                    $stmt = $this->db->prepare("
                        SELECT 
                            COUNT(*) AS value, {$this->group_by}(a.date_created) AS value_date
                        FROM users a 
                        WHERE 
                           {$where_clause} 
                        GROUP BY {$this->group_by}(a.date_created) LIMIT {$client_param->limit}
                    ");
                    $stmt->execute();
                    $counter = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $result["users_counter"][$range_key]["data"] = $counter;

                    // get the data to use
                    $the_data = !empty($result["users_counter"][$range_key]) ? $result["users_counter"][$range_key]["data"] : [];

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

                    $ranger[$range_key]["users_counter"]["data"] = $resultData;
                    $ranger[$range_key]["users_counter"]["period"] = $range_value;
                    

                    // append the period to to the array values
                    $result["users_counter"][$range_key]["period"] = $range_value;

                }

            }

            $result["grouped_list"] = $ranger;

            return $result;

        } catch(PDOException $e) {
            return [];
        }

    }

    /**
     * Preformat the date
     * 
     * This algo formats the dates that have been submitted by the user
     * 
     * @param String $period        This is the date to process
     */
    public function preformat_date($period) {

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
        if(isset($explode[1]) && strtotime($explode[1]) > strtotime($today)) {
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
				$dataSet[$value] = "0.00";
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
				$dataSet[$value] = "0.00";
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
				$dataSet[$value] = "0.00";
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
        $report = $section[0];
        $array_list = $array_data[$section[0]];

        // set the item
        if(isset($section[1])) {
            $array_list = $array_data[$section[0]][$section[1]];
        }
        // set the item
        if(isset($section[2])) {
            $array_list = $array_data[$section[0]][$section[1]][$section[2]];
        }

        // get the array key
        $total_value = array_sum(array_column($array_list, "value"));

        foreach($array_list as $key => $value) {
            $percentage = (($value["value"] / $total_value) * 100);
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