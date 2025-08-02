<?php

class Exeats extends Myschoolgh {

    public function list() {

    }

    /**
     * Create a new exeat
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function create($params = null) {
        
        foreach(['status', 'student_id', 'exeat_type', 'departure_date', 'pickup_by', 'guardian_contact', 'reason'] as $key) {
            if(empty($params->{$key})) {
                return ["code" => 400, "data" => "Sorry! Provide a valid {$key}."];
            }
        }

        if(!in_array($params->status, ['Pending', 'Approved', 'Rejected'])) {
            return ["code" => 400, "data" => "Sorry! Provide a valid status."];
        }

        if(!in_array($params->pickup_by, ['Self', 'Guardian', 'Other'])) {
            return ["code" => 400, "data" => "Sorry! Provide a valid pickup by."];
        }

        if(!in_array($params->exeat_type, ['Day', 'Weekend', 'Emergency'])) {
            return ["code" => 400, "data" => "Sorry! Provide a valid exeat type."];
        }

        if(!empty($params->return_date) && strtotime($params->return_date) < strtotime($params->departure_date)) {
            return ["code" => 400, "data" => "Sorry! The return date must be greater than the departure date."];
        }

        if(!empty($params->reason) && strlen($params->reason) > 255) {
            return ["code" => 400, "data" => "Sorry! The reason must be less than 255 characters."];
        }


    }
    
    /**
     * Update the exeat
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function update($params = null) {
        
        foreach(['exeat_id'] as $key) {
            if(empty($params->{$key})) {
                return ["code" => 400, "data" => "Sorry! Provide a valid {$key}."];
            }
        }

        if(!empty($params->status) && !in_array($params->status, ['Pending', 'Approved', 'Rejected'])) {
            return ["code" => 400, "data" => "Sorry! Provide a valid status."];
        }

        if(!empty($params->pickup_by) && !in_array($params->pickup_by, ['Self', 'Guardian', 'Other'])) {
            return ["code" => 400, "data" => "Sorry! Provide a valid pickup by."];
        }

        if(!empty($params->exeat_type) && !in_array($params->exeat_type, ['Day', 'Weekend', 'Emergency'])) {
            return ["code" => 400, "data" => "Sorry! Provide a valid exeat type."];
        }

        if(!empty($params->return_date) && !empty($params->departure_date) && strtotime($params->return_date) < strtotime($params->departure_date)) {
            return ["code" => 400, "data" => "Sorry! The return date must be greater than the departure date."];
        }

        if(!empty($params->reason) && strlen($params->reason) > 255) {
            return ["code" => 400, "data" => "Sorry! The reason must be less than 255 characters."];
        }

    }
    
    public function delete() {
        
    }
}
?>