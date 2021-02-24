<?php 

class Classes extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Update existing incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {


        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        if(isset($params->filter, $params->userId)) {
            if(in_array($params->filter, ["teacher"])) {
                $params->class_teacher = $params->userId;
            }
        }

        $params->query .= (isset($params->q)) ? " AND a.name='{$params->q}'" : null;
        $params->query .= (isset($params->class_teacher)) ? " AND a.class_teacher LIKE '%{$params->class_teacher}%'" : null;
        $params->query .= (isset($params->class_assistant)) ? " AND a.class_assistant='{$params->class_assistant}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->department_id)) ? " AND a.department_id='{$params->department_id}'" : null;
        $params->query .= (isset($params->class_id)) ? " AND a.id='{$params->class_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT ".(isset($params->columns) ? $params->columns : " a.*,
                    (SELECT name FROM departments WHERE departments.id = a.department_id LIMIT 1) AS department_name,
                    (SELECT COUNT(*) FROM users b WHERE b.user_status = 'Active' AND b.deleted='0' AND b.user_type='student' AND b.class_id = a.id AND b.client_id = a.client_id) AS students_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.class_assistant LIMIT 1) AS class_assistant_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.last_seen,'|',b.online,'|',b.user_type) FROM users b WHERE b.item_id = a.class_teacher LIMIT 1) AS class_teacher_info
                    ")."
                FROM classes a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $loadCourses = (bool) isset($params->load_courses);
            $loadRooms = (bool) isset($params->load_rooms);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // loop through the information
                foreach(["class_teacher_info", "class_assistant_info", "created_by_info"] as $each) {
                    // confirm that it is set
                    if(isset($result->{$each})) {
                        // convert the created by string into an object
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","last_seen","online","user_type"]);
                    }
                }
                // init
                $result->class_rooms_list = [];
                $result->class_courses_list = [];
                $result->rooms_list = !empty($result->rooms_list) ? json_decode($result->rooms_list, true) : [];

                // if the user also requested to load the courses
                if($loadCourses) {
                    // convert to array
                    $class_courses_list = !empty($result->courses_list) ? json_decode($result->courses_list, true) : [];
                    
                    // loop through the array list
                    foreach($class_courses_list as $course) {
                        // get the course tutor information
                        $course_info = $this->pushQuery("id, item_id, name, course_code, credit_hours, description", "courses", "item_id='{$course}' AND status='1' LIMIT 1");
                        if(!empty($course_info)) {
                            $result->class_courses_list[] = $course_info[0];
                        }
                    }
                }

                // if the user also requested to load the courses
                if($loadRooms) {
                    // loop through the array list
                    foreach($result->rooms_list as $room) {
                        // get the class room information
                        $room_info = $this->pushQuery("item_id, name, code, capacity", "classes_rooms", "item_id='{$room}' AND status='1' LIMIT 1");
                        if(!empty($room_info)) {
                            $result->class_rooms_list[] = $room_info[0];
                        }
                    }
                }

                $data[] = $result;
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
     * Add new class
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function add(stdClass $params) {

        try {

            // create a new class code
            if(isset($params->class_code) && !empty($params->class_code)) {
                // replace any empty space with 
                $params->class_code = str_replace("/^[\s]+$/", "", $params->class_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$params->clientId}' AND class_code='{$params->class_code}'"))) {
                    return ["code" => 203, "data" => "Sorry! There is an existing Class with the same code."];
                }
            } else {
                // generate a new class code
                $counter = $this->append_zeros(($this->itemsCount("classes", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $params->class_code = $this->client_data($params->clientId)->client_preferences->labels->{"class_label"}.$counter;
            }

            // init
			$room_ids = [];
            $item_id = random_string("alnum", 32);

            // append
			if(isset($params->room_id)) {
			    $room_ids = $this->append_class_rooms($params->room_id, $item_id, $params->clientId);
            }

            // convert the code to uppercase
            $params->class_code = strtoupper($params->class_code);

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO classes SET client_id = ?, created_by = ?, rooms_list = ?, item_id = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->class_code) ? ", class_code = '{$params->class_code}'" : null)."
                ".(isset($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(isset($params->class_teacher) ? ", class_teacher = '{$params->class_teacher}'" : null)."
                ".(isset($params->class_size) ? ", class_size = '{$params->class_size}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->class_assistant) ? ", class_assistant = '{$params->class_assistant}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId, json_encode($room_ids), $item_id]);
            
            // log the user activity
            $this->userLogs("classes", $item_id, null, "{$params->userData->name} created a new Class: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Class successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Update existing class record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // create a new class code
            if(isset($params->class_code) && !empty($params->class_code) && ($prevData[0]->class_code !== $params->class_code)) {
                // replace any empty space with 
                $params->class_code = str_replace("/^[\s]+$/", "", $params->class_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$params->clientId}' AND class_code='{$params->class_code}'"))) {
                    return ["code" => 203, "data" => "Sorry! There is an existing Class with the same code."];
                }
            } elseif(empty($prevData[0]->class_code) || !isset($params->class_code)) {
                // generate a new class code
                $counter = $this->append_zeros(($prevData[0]->id), $this->append_zeros);
                $params->class_code = $this->client_data($params->clientId)->client_preferences->labels->{"class_label"}.$counter;
            }

            // convert the code to uppercase
            $params->class_code = strtoupper($params->class_code);

            // init
			$room_ids = [];

            // append tutor to courses list
			if(isset($params->room_id)) {

                // convert the course tutor into an array
                $room_id = !empty($prevData[0]->rooms_list) ? json_decode($prevData[0]->rooms_list, true) : [];

				// find tutor ids which were initially attached to the course but no longer attached
				$diff = array_diff($room_id, $params->room_id);

				// append
				$room_ids = $this->append_class_rooms($params->room_id, $prevData[0]->item_id, $params->clientId);

				// remove user from courses
				if(!empty($diff)) {
					$this->remove_class_room($diff, $params->room_id, $params->clientId);
					$room_ids = $params->room_id;
				}
			} else {
				$this->remove_all_class_rooms($params, $prevData[0]->item_id);
			}

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE classes SET date_updated = now(), rooms_list = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->class_code) ? ", class_code = '{$params->class_code}'" : null)."
                ".(isset($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(isset($params->class_teacher) ? ", class_teacher = '{$params->class_teacher}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->class_size) ? ", class_size = '{$params->class_size}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->class_assistant) ? ", class_assistant = '{$params->class_assistant}'" : null)."
                ".(isset($params->description) ? ", description = '{$params->description}'" : null)."
                WHERE id = ? AND client_id = ?
            ");
            $stmt->execute([json_encode($room_ids), $params->class_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("classes", $params->class_id, $prevData[0], "{$params->userData->name} updated the Class: {$prevData[0]->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Class successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}update-class/{$params->class_id}/update"];

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
	public function append_class_rooms($class_rooms, $class_id, $client_id) {

		$class_rooms = $this->stringToArray($class_rooms);
		$valid_ids = [];

		foreach($class_rooms as $room) {
			$query = $this->pushQuery("classes_list", "classes_rooms", "item_id='{$room}' AND client_id='{$client_id}' AND status='1' LIMIT 1");
			if(!empty($query)) {
				$valid_ids[] = $room;
				if(!empty($query[0]->classes_list)) {
					$result = json_decode($query[0]->classes_list, true);
					if(!in_array($class_id, $result)) {
						array_push($result, $class_id);
						$this->db->query("UPDATE classes_rooms SET classes_list = '".json_encode($result)."' WHERE item_id='{$room}' AND status='1' LIMIT 1");
					}
				} else {
					$classes = [$class_id];
					$this->db->query("UPDATE classes_rooms SET classes_list = '".json_encode($classes)."' WHERE item_id='{$room}' AND status='1' LIMIT 1");
				}
			}
		}
		return $valid_ids;

	}

	/**
	 * Unattach a room from a class
	 * 
	 * @return Bool
	 */
	public function remove_class_room($room_ids, $class_id, $client_id) {

		$room_ids = $this->stringToArray($room_ids);
		
		foreach($room_ids as $class) {
			$query = $this->pushQuery("classes_list", "classes_rooms", "item_id='{$class}' AND client_id='{$client_id}' AND status='1' LIMIT 1");
			if(!empty($query)) {
				if(!empty($query[0]->classes_list)) {
					$result = json_decode($query[0]->classes_list, true);
					if(in_array($class_id, $result)) {
						$key = array_search($class_id, $result);
						unset($result[$key]);
						$this->db->query("UPDATE classes_rooms SET classes_list = '".json_encode($result)."' WHERE item_id='{$class}' LIMIT 1");
					}
				}
			}
		}
	}

	/**
	 * Remove All Course Tutors
	 * 
	 * Loop through the courses that the user has been attached to 
	 * If not in the courses tutors list then append the user_id to it
	 * 
	 * @return Bool
	 */
	public function remove_all_class_rooms(stdClass $params, $item_id) {

		$room_ids = $this->pushQuery("classes_list, item_id", "classes_rooms", "client_id='{$params->clientId}' AND status='1' LIMIT 100");

		foreach($room_ids as $class) {
			if(!empty($class->classes_list)) {
				$result = json_decode($class->classes_list, true);
				if(in_array($item_id, $result)) {
					$key = array_search($item_id, $result);
					if($key !== FALSE) {
						unset($result[$key]);
						$this->db->query("UPDATE classes_rooms SET classes_list = '".json_encode($result)."' WHERE item_id='{$class->item_id}' LIMIT 1");
					}
				}
			}
		}
		return true;

	}
    
}
?>