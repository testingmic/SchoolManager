<?php 

class Classes extends Myschoolgh {
    

    /**
     * Constructor
     * 
     * @param stdClass $data
     * 
     * @return void
     */
    public function __construct($data = null) {
    
        parent::__construct();

        $this->iclient = $data->client_data ?? [];

		// run this query
        $this->academic_term = $data->client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $data->client_data->client_preferences->academics->academic_year ?? null;
    }
    
    /**
     * Update existing incident record
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        global $isAdmin, $defaultUser;

        $params->query = "1";

        // set the user data
        if(!empty($defaultUser)) {
            $params->userData = $defaultUser;
        }

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // if the class id was not parsed
        if(empty($params->class_id)) {

            // then use this query
            if($params->userData->user_type === "teacher") {
                $params->class_id = $params->userData->class_ids;
            } elseif($params->userData->user_type === "student") {
                $params->class_id = $params->userData->class_id;
            }
        }

        $params->academic_term = !empty($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = !empty($params->academic_year) ? $params->academic_year : $this->academic_year;
        
        if(!empty($params->forceYear) && !empty($params->userData)) {
            $academics = $params->userData->client->client_preferences->academics;
            $params->academic_year = empty($params->academic_year) ? $academics->academic_year : $params->academic_year;
            $params->academic_term = empty($params->academic_term) ? $academics->academic_term : $params->academic_term;
        }

        $params->query .= !empty($params->q) ? " AND a.name='{$params->q}'" : null;
        $params->query .= !$isAdmin && !empty($params->class_teacher) ? " AND a.class_teacher LIKE '%{$params->class_teacher}%'" : null;
        $params->query .= !empty($params->class_assistant) ? " AND a.class_assistant='{$params->class_assistant}'" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->department_id) ? " AND a.department_id='{$params->department_id}'" : null;

        // format the class id query
        if(!empty($params->class_id)) {
            // convert the class id to array
            $class_ids = $this->stringToArray($params->class_id);

            // if the strlen is greater than 6
            if(strlen($class_ids[0]) > 6) {
                $params->query .= !empty($params->class_id) ? " AND a.item_id IN {$this->inList($params->class_id)}" : null;
            } else {
                $params->query .= !empty($params->class_id) ? " AND a.id IN {$this->inList($params->class_id)}" : null;
            }
        }
		
        try {

            $stmt = $this->db->prepare("
                SELECT ".(isset($params->columns) ? $params->columns : " a.*, dp.name AS department_name,
                    (
                        SELECT COUNT(*) FROM users b 
                        WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' 
                        AND b.class_id = a.id AND b.client_id='{$params->clientId}' AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)} LIMIT {$this->maximum_class_count}
                    ) AS students_count,
                    (
                        SELECT COUNT(*) FROM users b 
                        WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' 
                        AND b.gender='Male' AND b.class_id = a.id AND b.client_id='{$params->clientId}' AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)} LIMIT {$this->maximum_class_count}
                    ) AS students_male_count,
                    (
                        SELECT COUNT(*) FROM users b 
                        WHERE b.user_status IN ({$this->default_allowed_status_users_list}) AND b.deleted='0' AND b.user_type='student' 
                        AND b.gender='Female' AND b.class_id = a.id AND b.client_id='{$params->clientId}' AND b.user_status IN {$this->inList($this->default_allowed_status_users_array)} LIMIT {$this->maximum_class_count}
                    ) AS students_female_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.class_assistant LIMIT 1) AS class_assistant_info,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.class_teacher LIMIT 1) AS class_teacher_info
                    ")."
                FROM classes a
                LEFT JOIN departments dp ON dp.id = a.department_id
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
                        $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["user_id", "name", "phone_number", "email", "image","user_type"]);
                    }
                }

                if(!empty($result->description)) {
                    $result->description = clean_html($result->description);
                }

                // init
                $result->class_rooms_list = [];
                $result->class_courses_list = [];
                $result->rooms_list = !empty($result->rooms_list) ? json_decode($result->rooms_list, true) : [];

                // if the user also requested to load the courses
                if($loadCourses) {
                    // convert to array
                    $result->class_courses_list = $this->pushQuery("id, item_id, name, course_code, credit_hours, description, class_id", "courses", "class_id LIKE '%{$result->item_id}%' AND status='1' AND academic_term='{$params->academic_term}' AND academic_year='{$params->academic_year}' LIMIT 15");
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

            // global variable
            global $defaultClientData;

            // create a new class code
            if(isset($params->class_code) && !empty($params->class_code)) {
                // replace any empty space with 
                $params->class_code = str_replace("/^[\s]+$/", "", $params->class_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$params->clientId}' AND class_code='{$params->class_code}'"))) {
                    return ["code" => 400, "data" => "Sorry! There is an existing Class with the same code."];
                }
            } else {
                // generate a new class code
                $counter = $this->append_zeros(($this->itemsCount("classes", "client_id = '{$params->clientId}'") + 1), 2);
                $params->class_code = $defaultClientData->client_preferences->labels->{"class_label"}.$counter;
            }

            // init
			$room_ids = [];
            $item_id = random_string("alnum", RANDOM_STRING);

            // append
			if(isset($params->room_id)) {
			    $room_ids = $this->append_class_rooms($params->room_id, $item_id, $params->clientId);
            }

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO classes SET client_id = ?, created_by = ?, rooms_list = ?, item_id = ?
                ".(!empty($params->name) ? ", name = '{$params->name}'" : null)."
                ".(!empty($params->class_code) ? ", class_code = '{$params->class_code}'" : null)."
                ".(!empty($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(!empty($params->payment_module) ? ", payment_module = '{$params->payment_module}'" : null)."
                ".(!empty($params->class_teacher) ? ", class_teacher = '{$params->class_teacher}'" : null)."
                ".(!empty($params->class_size) ? ", class_size = '{$params->class_size}'" : null)."
                ".(!empty($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(!empty($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(!empty($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(!empty($params->class_assistant) ? ", class_assistant = '{$params->class_assistant}'" : null)."
                ".(!empty($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(!empty($params->graduation_level) ? ", is_graduation_level = '{$params->graduation_level}'" : null)."
            ");
            $stmt->execute([$params->clientId, $params->userId, json_encode($room_ids), $item_id]);
            
            // log the user activity
            $this->userLogs("classes", $item_id, null, "{$params->userData->name} created a new Class: {$params->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Class successfully created.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["clear" => true, "href" => "{$this->baseUrl}class/{$item_id}"];

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

            // get a default client data
            global $defaultClientData;

            // old record
            $prevData = $this->pushQuery("*", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // create a new class code
            if(isset($params->class_code) && !empty($params->class_code) && ($prevData[0]->class_code !== $params->class_code)) {
                // replace any empty space with 
                $params->class_code = str_replace("/^[\s]+$/", "", $params->class_code);
                // confirm if the class code already exist
                if(!empty($this->pushQuery("id, name", "classes", "status='1' AND client_id='{$params->clientId}' AND class_code='{$params->class_code}' LIMIT 1"))) {
                    return ["code" => 400, "data" => "Sorry! There is an existing Class with the same code."];
                }
            } elseif(empty($prevData[0]->class_code) || !isset($params->class_code)) {
                // generate a new class code
                $counter = $this->append_zeros(($prevData[0]->id), 2);
                $params->class_code = $defaultClientData->client_preferences->labels->{"class_label"}.$counter;
            }

            // init
			$room_ids = [];

            // append tutor to Subjects List
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
            
            // convert the class teacher to a string
            if(!empty($params->class_teacher) && is_array($params->class_teacher)) {
                $params->class_teacher = implode(",", $params->class_teacher);
            }

            // execute the statement
            $stmt = $this->db->prepare("
                UPDATE classes SET date_updated = now(), rooms_list = ?
                ".(isset($params->name) ? ", name = '{$params->name}'" : null)."
                ".(isset($params->class_code) ? ", class_code = '{$params->class_code}'" : null)."
                ".(isset($params->department_id) ? ", department_id = '{$params->department_id}'" : null)."
                ".(isset($params->class_teacher) ? ", class_teacher = '{$params->class_teacher}'" : null)."
                ".(isset($params->name) ? ", slug = '".create_slug($params->name)."'" : null)."
                ".(isset($params->payment_module) ? ", payment_module = '{$params->payment_module}'" : null)."
                ".(isset($params->class_size) ? ", class_size = '{$params->class_size}'" : null)."
                ".(isset($params->academic_term) ? ", academic_term = '{$params->academic_term}'" : null)."
                ".(isset($params->academic_year) ? ", academic_year = '{$params->academic_year}'" : null)."
                ".(isset($params->class_assistant) ? ", class_assistant = '{$params->class_assistant}'" : null)."
                ".(isset($params->description) ? ", description = '".addslashes($params->description)."'" : null)."
                ".(!empty($params->graduation_level) ? ", is_graduation_level = '{$params->graduation_level}'" : null)."
                WHERE id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([json_encode($room_ids), $params->class_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("classes", $params->class_id, $prevData[0], "{$params->userData->name} updated the Class: {$prevData[0]->name}", $params->userId);

            # set the output to return when successful
			$return = ["code" => 200, "data" => "Class successfully updated.", "refresh" => 2000];
			
			# append to the response
			$return["additional"] = ["href" => "{$this->baseUrl}class/{$params->class_id}/update"];

			// return the output
            return $return;

        } catch(PDOException $e) {
            return $e->getMessage();
        } 
        
    }

    /**
     * Assign Students to a Class
     * 
     * @param Array     $params->data
     * @param Array     $params->data["assign_fees"]
     * @param Array     $params->data["class_id"]
     * @param Array     $params->data["student_id"]
     * 
     * @return Array
     */
    public function assign(stdClass $params) {

        try {

            // confirm that the variable is an array
            if(empty($params->data) && !is_array($params->data)) {
                return ["code" => 400, "data" => "Sorry! The data array must be a valid array."];
            }

            // confirm that the class id was parsed
            if(!isset($params->data["class_id"])) {
                return ["code" => 400, "data" => "Sorry! Ensure that the class id was parsed."];
            }

            // confirm that the class id was parsed
            if(!isset($params->data["student_id"])) {
                return ["code" => 400, "data" => "Sorry! Ensure that the student id was parsed."];
            }

            // confirm that the student id is an array
            if(!is_array($params->data["student_id"])) {
                return ["code" => 400, "data" => "Sorry! Ensure that the student id parsed is a valid array."];
            }

            // confirm that the class is parsed
            $check = $this->pushQuery("id, name", "classes", "id='{$params->data["class_id"]}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            if(empty($check)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // confirm if the current class fees must be assign to the students
            $assignFees = (bool) isset($params->data["assign_fees"]) && ($params->data["assign_fees"] == "assign");

            // update query
            $update = $this->db->prepare("UPDATE users SET class_id = ? WHERE id = ? AND client_id = ? AND user_type = ? LIMIT 1");

            // loop through the students list
            foreach($params->data["student_id"] as $student) {
                // execute the update statement
                $update->execute([$params->data["class_id"], $student, $params->clientId, "student"]);
            }

            // if the assign fees was parsed
            if($assignFees) {
                // set the unique id
                $item_id = random_string("alnum", RANDOM_STRING);

                // Insert the activity into the cron_scheduler
                $query = $this->db->prepare("INSERT INTO cron_scheduler SET `client_id` = ?, `item_id` = ?, `user_id` = ?, 
                    `cron_type` = ?, `active_date` = now(), `query` = ?, `subject` = ?, academic_year = ?, academic_term = ?");
                $query->execute([$params->clientId, $item_id, $params->userId, "assign_student_fees", 
                    json_encode($params->data["student_id"]), $params->data["class_id"],
                    $params->academic_year, $params->academic_term
                ]);
            }

			// return the output
            return [
                "code" => 200, 
                "data" => count($params->data["student_id"]) ." Students were successfully assigned to {$check[0]->name}.", 
                "additional" => [
                    "students_list" => $params->data["student_id"]
                ]
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
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

		$room_ids = $this->pushQuery("classes_list, item_id", "classes_rooms", "client_id='{$params->clientId}' AND status='1' LIMIT {$this->temporal_maximum}");

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