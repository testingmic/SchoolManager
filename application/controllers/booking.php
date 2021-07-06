<?php 

class Booking extends Myschoolgh {

    public function __construct() {
        parent::__construct();
    }

    /**
     * List The Booking Log
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        try {

            $query = "1";

            $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

            // append to the list
            $query .= isset($params->booking_id) && !empty($params->booking_id) ? " AND a.item_id='{$params->booking_id}'" : null;
            $query .= isset($params->log_date) && !empty($params->log_date) ? " AND a.log_date='{$params->log_date}'" : null;
            $query .= isset($params->member_id) && !empty($params->member_id) ? " AND a.members_ids LIKE '%{$params->member_id}%'" : null;
            $query .= isset($params->created_by) && !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
            
            // summary count
            $summary = [
                "Count" => [
                    "Logs" => 0,
                    "Members" => 0
                ],
                "Gender" => [
                    "Male" => 0,
                    "Female" => 0,
                    "Unspecified" => 0
                ]
            ];

            // get the list of all booking logs
            $stmt = $this->db->prepare("SELECT 
                    a.client_id, a.item_id, a.log_date, a.members_list, a.members_ids, a.state, a.date_created, a.created_by,
                    (SELECT CONCAT(b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM 
                    church_booking_log a
                WHERE {$query} AND a.client_id = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([$params->clientId]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                
                // convert the created by string into an object
                $result->created_by_info = (object) $this->stringToArray($result->created_by_info, "|", ["name", "phone_number", "email", "image","user_type"]);
                
                $result->members_list = json_decode($result->members_list, true);
                $result->members_ids = json_decode($result->members_ids, true);

                // summary analitics
                $summary["Count"]["Logs"]++;

                // add to the counter
                foreach($result->members_list as $key => $member) {
                    $summary["Count"]["Members"]++;
                    !empty($member["gender"]) ? $summary["Gender"][$member["gender"]]++ : $summary["Gender"]["Unspecified"]++;
                }

                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => [
                    "list" => $data,
                    "summary" => $summary
                ]
            ];
            
        } catch(PDOEXception $e) {
            return $this->unexpected_error;
        }
            
    }

    /**
     * Log the Booking Record
     * 
     * @return Array
     */
    public function log(stdClass $params) {

        try {
            
            // initial values
            $errors = null;
            $members_list = [];

            // if the record doesn't exist
            $newRecord = (bool) empty($params->booking_id) || (strlen($params->booking_id) < 10);

            // validate the dates
            if(empty($params->log_date)) {
                return ["code" => 203, "data" => "Sorry! Log date cannot be empty."];
            }

            // validate dates
            if(!$this->validDate($params->log_date)) {
                return ["code" => 203, "data" => "Sorry! Log date must be a valid date."];
            }
            if(strtotime($params->log_date) > strtotime(date("Y-m-d"))) {
                return ["code" => 203, "data" => "Sorry! Log date must not exceed current date."];
            }

            // loop through the fields
            foreach(["fullname", "contact", "residence", "gender", "temperature", "item_id"] as $item) {
                // generate new group id
                $group_id = random_string("alnum", 15);

                // if the field was parsed in the request
                if(isset($params->{$item})) {

                    // loop through the fields for grouping
                    foreach($params->{$item} as $key => $value) {

                        // append to the list
                        $members_list[$key][$item] = $value;

                        // append the member id
                        if(in_array($item, ["item_id"]) && empty($value)) {
                            $members_list[$key][$item] = random_string("alnum", 18);
                            $members_list[$key]["group_id"] = $group_id;
                        }

                        // log the errors
                        if(in_array($item, ["fullname", "temperature"]) && empty($value)) {
                            $errors .= ucfirst($item)." for ".(!empty($members_list[$key]["fullname"]) ? $members_list[$key]["fullname"] : "Member {$key}")." cannot be empty.\n";
                        }
                        if((in_array($item, ["temperature"]) && !empty($value) && !preg_match("/^[0-9.]+$/", $value)) || (in_array($item, ["temperature"]) && (strlen($value) > 4))) {
                            $errors .= ucfirst($item)." for ".(!empty($members_list[$key]["fullname"]) ? $members_list[$key]["fullname"] : "Member {$key}")." must be an integer and less than 5 characters long.\n";
                        }
                    }
                }
            }

            // if there was any error found
            if(!empty($errors)) {
                return ["code" => 203, "data" => $errors];
            }

            // set a new members param
            $members_param = (object) [
                "clientId" => $params->clientId,
                "data" => [
                    "clientId" => $params->clientId,
                    "userId" => $params->userId,
                    "request" => "add_update",
                    "members_list" => $members_list
                ]
            ];

            // push request to the members method
            $this->members($members_param);

            // get the unique id of the members list
            $members_ids = array_column($members_list, "item_id");

            // confirm if the record already exists
            if($newRecord) {

                // attendance log record
                $item_id = random_string("alnum", 15);

                // insert the record
                $stmt = $this->db->prepare("INSERT INTO church_booking_log SET client_id = ?, log_date = ?, members_list = ?, members_ids = ?, item_id = ?, date_created = now(), created_by = ?");
                $stmt->execute([$params->clientId, $params->log_date, json_encode($members_list), json_encode($members_ids), $item_id, $params->userId]);

                // log the user activity
                $this->userLogs("booking_log", $item_id, null, "{$params->userData->name} logged an attendance for <strong>{$params->log_date}</strong>.", $params->userId);

                // return success message
                return [
                    "code" => 200, 
                    "data" => "Attendance was successfully logged.", 
                    "additional" => [
                        "clear" => true
                    ]
                ];

            } else {

                // update the record
                $stmt = $this->db->prepare("UPDATE church_booking_log SET log_date = ?, members_list = ?, members_ids = ? WHERE client_id = ? AND item_id = ? LIMIT 1");
                $stmt->execute([$params->log_date, json_encode($members_list), json_encode($members_ids), $params->clientId, $params->booking_id]);

                // log the user activity
                $this->userLogs("booking_log", $params->booking_id, null, "{$params->userData->name} updated the attendance log for <strong>{$params->log_date}</strong>.", $params->userId);

                // return success message
                return [
                    "code" => 200, 
                    "data" => "Attendance was successfully updated.",
                ];
            }

        } catch(PDOEXception $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Processing members endpoint
     * 
     * @param Array $params->data
     * 
     * @return Array
     */
    public function members(stdClass $params) {

        try {

            if(!is_array($params->data)) {
                return ["code" => 203, "result" => "Sorry! The data parameter must be an array."];
            }

            $params->data["clientId"] = $params->clientId;

            if(!isset($params->data["request"])) {
                return ["code" => 203, "result" => "Sorry! The request variable is required."];
            }

            if(!in_array($params->data["request"], ["list", "add_update"])) {
                return ["code" => 203, "result" => "Sorry! An invalid request was parsed"];
            }
            
            // if the request is either add or update member
            if($params->data["request"] == "add_update") {
                $this->add_update_member($params->data);
            }

            // if the request is to search
            if($params->data["request"] == "list") {
                return $this->list_members($params->data)["data"]["list"];
            }
        
        } catch(PDOException $e) {}

    }

    /**
     * Add or Update Member
     * 
     * @return Bool
     */
    public function add_update_member($params) {

        // get the members list
        $params = is_object($params) ? $params : (object) $params;
        $members_list = $params->members_list;

        // set the insert and update query
        $insert = $this->db->prepare("INSERT INTO church_members SET client_id = ?, item_id = ?, 
            fullname = ?, contact = ?, email = ?, residence = ?, gender = ?, group_id = ?");
        $update = $this->db->prepare("UPDATE church_members SET fullname = ?, contact = ?, email = ?, 
            residence = ?, gender = ?, group_id = ? WHERE client_id = ? AND item_id = ? LIMIT 1");

        // save the user information
        foreach($members_list as $member) {
            if(empty($this->pushQuery("item_id", "church_members", "item_id='{$member["item_id"]}' LIMIT 1"))) {
                $insert->execute([$params->clientId, $member["item_id"], $member["fullname"], $member["contact"] ?? null, 
                    $member["email"] ?? null, $member["residence"] ?? null, $member["gender"] ?? null, $member["group_id"] ?? null]);
            } else {
                $update->execute([$member["fullname"], $member["contact"] ?? null, $member["email"] ?? null, $member["residence"] ?? null, 
                    $member["gender"] ?? null, $member["group_id"] ?? null, $params->clientId, $member["item_id"]]);
            }
        }

        return true;
    }

    /**
     * List Members
     * 
     * @return Array
     */
    public function list_members($params) {

        $params = is_object($params) ? $params : (object) $params;

        $where_clause = 1;
        $where_clause .= isset($params->name) && !empty($params->name) ? " AND a.fullname LIKE '%{$params->name}%'" : null;
        $where_clause .= isset($params->member_id) && !empty($params->member_id) ? " AND a.item_id = '{$params->member_id}'" : null;

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;
        $columns = isset($params->load) ? "a.*" : "a.id, a.fullname, a.item_id, a.contact, a.residence, a.gender, a.residence,
        a.date_of_birth, a.profession, a.email, a.organization";

        $stmt = $this->db->prepare("SELECT {$columns}, a.bible_class, b.name AS bible_class_name
        FROM church_members a 
        LEFT JOIN church_bible_classes b ON b.item_id = a.bible_class
        WHERE 
            {$where_clause} AND a.client_id = ? 
        LIMIT {$params->limit}");
        $stmt->execute([$params->clientId]);

        $data = [];
        $summary = [
            "Count" => [
                "Members" => 0
            ],
            "Gender" => [
                "Male" => 0,
                "Female" => 0,
                "Unspecified" => 0
            ]
        ];
        while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
            // summary analitics
            $summary["Count"]["Members"]++;
            !empty($result->gender) ? $summary["Gender"][$result->gender]++ : $summary["Gender"]["Unspecified"]++;
            
            // additional data
            $result->organizations_list = null;

            // append to the list
            $data[$result->item_id] = $result;
        }

        return [
            "code" => 200,
            "data" => [
                "list" => $data,
                "summary" => $summary
            ]
        ];

    }

    /**
     * Analytics
     * 
     * @return Array
     */
    public function analitics(stdClass $params) {
        
        // get the summary of the data in the logs
        $data["logs_summary"] = $this->list($params)["data"]["summary"];

        // get the summary of all members list
        $data["members_summary"] = $this->list_members(["clientId" => $params->clientId])["data"];

        return [
            "data" => $data
        ];

    }

}
?>