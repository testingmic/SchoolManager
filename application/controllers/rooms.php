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
}