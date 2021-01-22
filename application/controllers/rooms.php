<?php 

class Rooms extends Myschoolgh {

    public function __construct() {
		parent::__construct();
	}

	/**
     * List class rooms
     * 
	 * @param stdClass $params
	 *  
     * @return Array
     */
	public function list(stdClass $params) {

		$params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->code)) ? " AND a.item_id='{$params->code}'" : null;

        try {

            $loadClasses = (bool) isset($params->load_classes);

            $stmt = $this->db->prepare("
                SELECT a.*
                FROM classes_rooms a
                WHERE {$params->query} AND a.status = ? ORDER BY a.name LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // conver the class ids into an array string
                $result->class_ids = !empty($result->classes_list) ? json_decode($result->classes_list, true) : [];
                $result->room_classes_list = [];

                // if the user also requested to load the courses
                if($loadClasses) {
                    // loop through the array list
                    foreach($result->class_ids as $class) {
                        // get the class room information
                        $room_info = $this->pushQuery("item_id, name, class_code, class_size, weekly_meeting", "classes", "item_id='{$class}' AND status='1' LIMIT 1");
                        if(!empty($room_info)) {
                            $result->room_classes_list[] = $room_info[0];
                        }
                    }
                }

				$data[] = $result;
                
            }

			return [ "code" => 200, "data" => $data ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

	}

    /**
     * Add New Classroom
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add_classroom(stdClass $params) {

        try {

            // create a new room code
            if(isset($params->code) && !empty($params->code)) {
                // replace any empty space with 
                $params->code = str_replace("/^[\s]+$/", "", $params->code);
                // confirm if the room code already exist
                if(!empty($this->pushQuery("item_id, name", "classes_rooms", "status='1' AND client_id='{$params->clientId}' AND code='{$params->code}'"))) {
                    return ["code" => 203, "data" => "Sorry! There is an existing Room with the same code."];
                }
            } else {
                // generate a new room code
                $counter = $this->append_zeros(($this->itemsCount("classes_rooms", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $params->code = $counter;
            }

            // init
			$class_ids = [];
            $item_id = random_string("alnum", 32);

            // append
            if(isset($params->class_id)) {
			    $class_ids = $this->append_class_rooms($params->class_id, $item_id, $params->clientId);
            }

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO classes_rooms SET client_id = ?, classes_list = ?, item_id = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->code) ? ", code = '{$params->code}'" : null)."
                ".(isset($params->capacity) ? ", capacity = '{$params->capacity}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
            ");
            $stmt->execute([$params->clientId, json_encode($class_ids), $item_id]);
            
            // log the user activity
            $this->userLogs("class_room", $item_id, null, "{$params->userData->name} created a new Classroom: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Classroom successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
	 * Append Class Rooms
	 * 
	 * Loop through the courses that the user has been attached to 
	 * If not in the courses tutors list then append the user_id to it
	 * 
	 * @return Bool
	 */
	public function append_class_rooms($classes_list, $room_id, $client_id) {

		$classes_list = $this->stringToArray($classes_list);
		$valid_ids = [];

		foreach($classes_list as $class) {
			$query = $this->pushQuery("rooms_list", "classes", "item_id='{$class}' AND client_id='{$client_id}' AND status='1' LIMIT 1");
			if(!empty($query)) {
				$valid_ids[] = $class;
				if(!empty($query[0]->rooms_list)) {
					$result = json_decode($query[0]->rooms_list, true);
					if(!in_array($room_id, $result)) {
						array_push($result, $room_id);
						$this->db->query("UPDATE classes SET rooms_list = '".json_encode($result)."' WHERE item_id='{$class}' AND status='1' LIMIT 1");
					}
				} else {
					$classes = [$room_id];
					$this->db->query("UPDATE classes SET rooms_list = '".json_encode($classes)."' WHERE item_id='{$class}' AND status='1' LIMIT 1");
				}
			}
		}
		return $valid_ids;
	}


}