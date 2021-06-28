<?php 

class Booking extends Myschoolgh {

    public function __construct() {
        parent::__construct();
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
            foreach(["fullname", "contact", "residence", "temperature"] as $item) {
                // if the field was parsed in the request
                if(isset($params->{$item})) {
                    // loop through the fields for grouping
                    foreach($params->{$item} as $key => $value) {
                        // append to the list
                        $members_list[$key][$item] = $value;
                        // log the errors
                        if(in_array($item, ["fullname", "temperature"]) && empty($value)) {
                            $errors .= ucfirst($item)." for Member {$key} cannot be empty.\n";
                        }
                        if((in_array($item, ["temperature"]) && !empty($value) && !preg_match("/^[0-9.]+$/", $value)) || (in_array($item, ["temperature"]) && (strlen($value) > 4))) {
                            $errors .= ucfirst($item)." for Member {$key} must be an integer and less than 5 characters long.\n";
                        }
                    }
                }
            }

            // if there was any error found
            if(!empty($errors)) {
                return ["code" => 203, "data" => $errors];
            }

            // confirm if the record already exists
            if(empty($params->booking_id) || strlen($params->booking_id) < 10) {

                // attendance log record
                $item_id = random_string("alnum", 15);

                // insert the record
                $stmt = $this->db->prepare("INSERT INTO booking_log SET client_id = ?, log_date = ?, members_list = ?, item_id = ?, date_created = now(), created_by = ?");
                $stmt->execute([$params->clientId, $params->log_date, json_encode($members_list), $item_id, $params->userId]);

                // log the user activity
                $this->userLogs("booking_log", $item_id, null, "{$params->userData->name} logged an attendance.", $params->userId);

                // return success message
                return [
                    "code" => 200, 
                    "data" => "Attendance was successfully logged.", 
                    "additional" => [
                        "clear" => true
                    ]
                ];

            }

        } catch(PDOEXception $e) {
            return $this->unexpected_error;
        }

    }

}
?>