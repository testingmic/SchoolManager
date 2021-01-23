<?php 

class Timetable extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * List timetable records
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {


        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->timetable_id) && !empty($params->timetable_id)) ? " AND a.item_id='{$params->timetable_id}'" : null;
        $params->query .= isset($params->academic_year) ? " AND a.academic_year='{$params->academic_year}'" : "";
        $params->query .= isset($params->academic_term) ? " AND a.academic_term='{$params->academic_term}'" : "";

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT name FROM classes WHERE classes.item_id = a.class_id LIMIT 1) AS class_name
                FROM timetables a
                WHERE {$params->query} AND a.status = ? ORDER BY a.name LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                $result->disabled_inputs = !empty($result->disabled_inputs) ? json_decode($result->disabled_inputs, true) : [];
                $data[$result->item_id] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Save timetable record
     * 
     * This method saves a timetable record
     * 
     * @param String        $params->timetable_id
     * @param Int           $params->slots
     * @param Int           $params->days
     * @param Int           $params->duration
     * @param String        $params->start_time
     * @param String        $params->name
     * @param String        $params->class_id
     * 
     * @return Array
     */
    public function save(stdClass $params) {

        try {

            // confirm that the timetable_id is parsed
            if(isset($params->timetable_id)) {
                // assign
                $item_id = $params->timetable_id;
                // check if a record exist
                if(empty($this->pushQuery("item_id", "timetables", "item_id = '{$item_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
                    return ["code" => 203, "data" => "Sorry! An invalid timetable id was parsed"];
                }
                $isFound = true;
            } else {
                // create a new timetable_id
                $item_id = random_string("alnum", 32);
                $isFound = false;

                // if the class isset
                if(isset($params->class_id)) {
                    // confirm if a class already exist with the same id
                    if(!empty($this->pushQuery("item_id", "timetables", "class_id='{$params->class_id}' AND client_id = '{$params->clientId}' AND status='1'"))) {
                        return ["code" => 203, "data" => "Sorry! There is an existing record in the database for the specified Class ID."];
                    }
                }
            }

            // convert to array
            $disabled_inputs =  isset($params->disabled_inputs) ? $this->stringToArray($params->disabled_inputs) : [];

            // update the record if found
            if($isFound) {
                $stmt = $this->db->prepare("
                    UPDATE timetables SET 
                        days = ?, slots = ?, duration = ?, start_time = ?, disabled_inputs = ?
                        ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                        ".(isset($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                    WHERE item_id = ? AND client_id = ? LIMIT 1
                ");
                $stmt->execute([$params->days, $params->slots, $params->duration, 
                    $params->start_time, json_encode($disabled_inputs), $item_id, $params->clientId
                ]);
            }
            // insert the record
            else {
                $stmt = $this->db->prepare("
                    INSERT INTO timetables SET 
                        days = ?, slots = ?, duration = ?, start_time = ?, 
                        disabled_inputs = ?, item_id = ?, client_id = ?
                        ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                        ".(isset($params->class_id) ? ", class_id = '{$params->class_id}'" : null)."
                        ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                        ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ");
                $stmt->execute([$params->days, $params->slots, $params->duration, $params->start_time, 
                    json_encode($disabled_inputs), $item_id, $params->clientId]);
            }

            // set the timetable in session
            $this->session->set("last_TimetableId", $item_id);
            
            return [
                "code" => 200, 
                "data" => "Timetable record was successfully saved.",
                "additional" => [
                    "timetable_id" => $item_id,
                    "disabled_inputs" => $disabled_inputs
                ]
            ];
        
        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Save Timetable Id
     * 
     * Save the parsed timetable id in session after checking if it exists
     * 
     * @param String        $params->timetable_id
     * 
     * @return Array
     */
    public function set_timetable_id(stdClass $params) {
        
        // assign
        $item_id = $params->timetable_id;
        
        // check if a record exist
        if(empty($this->pushQuery("item_id", "timetables", "item_id = '{$item_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
            return ["code" => 203, "data" => "Sorry! An invalid timetable id was parsed"];
        }

        // set in session
        $this->session->set("last_TimetableId", $item_id);

        // return success
        return ["code" => 200, "data" => "Timetable ID successfully set as default."];

    }
}
?>