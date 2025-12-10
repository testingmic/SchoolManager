<?php

class Beds extends Myschoolgh {

    public $bedsModel;

    public function __construct($params = null) {
        parent::__construct();
        $this->bedsModel = load_class("BedsModel", "models/housing");
        
        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;
    }

    /**
     * List Beds
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        global $accessObject, $defaultUser;

        // check permission
        if(!$accessObject->hasAccess("view", "housing")) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        $params->query = "1";
        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // set the user data
        if(!empty($defaultUser)) {
            $params->userData = $defaultUser;
        }

        $params->query .= !empty($params->q) ? " AND (a.name LIKE '%{$params->q}%' OR a.code LIKE '%{$params->q}%')" : null;
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->bed_id) ? " AND a.item_id='{$params->bed_id}'" : null;
        $params->query .= !empty($params->room_id) ? " AND a.room_id='{$params->room_id}'" : null;
        $params->query .= !empty($params->block_id) ? " AND b.block_id='{$params->block_id}'" : null;
        $params->query .= !empty($params->building_id) ? " AND c.building_id='{$params->building_id}'" : null;
        $params->query .= !empty($params->student_id) ? " AND a.student_id='{$params->student_id}'" : null;
        $params->query .= !empty($params->occupied) ? " AND a.occupied='{$params->occupied}'" : null;
        $params->query .= !empty($params->status) ? " AND a.status='{$params->status}'" : null;

        try {

            $loadOccupants = (bool) isset($params->load_occupants);
            $loadRoom = (bool) isset($params->load_room);
            $loadBlock = (bool) isset($params->load_block);
            $loadBuilding = (bool) isset($params->load_building);

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) 
                     FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                    ".(!empty($params->student_id) && $loadOccupants ? ", 
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.unique_id,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type,'|',b.gender) 
                     FROM users b WHERE b.item_id = a.student_id LIMIT 1) AS student_info" : null)."
                    ".(!empty($params->room_id) && $loadRoom ? ", 
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.code,'|',b.room_type,'|',b.room_condition) 
                     FROM housing_rooms b WHERE b.item_id = a.room_id LIMIT 1) AS room_info" : null)."
                    ".(!empty($params->block_id) && $loadBlock ? ", 
                    (SELECT CONCAT(c.item_id,'|',c.name,'|',c.code) 
                     FROM housing_blocks c 
                     LEFT JOIN housing_rooms b ON b.block_id = c.item_id
                     WHERE b.item_id = a.room_id LIMIT 1) AS block_info" : null)."
                    ".(!empty($params->building_id) && $loadBuilding ? ", 
                    (SELECT CONCAT(d.item_id,'|',d.name,'|',d.code) 
                     FROM housing_buildings d 
                     LEFT JOIN housing_blocks c ON c.building_id = d.item_id
                     LEFT JOIN housing_rooms b ON b.block_id = c.item_id
                     WHERE b.item_id = a.room_id LIMIT 1) AS building_info" : null)."
                FROM housing_beds a
                ".(!empty($params->block_id) ? " LEFT JOIN housing_rooms b ON b.item_id = a.room_id" : null)."
                ".(!empty($params->building_id) ? " LEFT JOIN housing_rooms b ON b.item_id = a.room_id LEFT JOIN housing_blocks c ON c.item_id = b.block_id" : null)."
                WHERE {$params->query} AND a.deleted = ? ORDER BY a.name LIMIT {$params->limit}
            ");
            $stmt->execute([0]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                $result->id = (int) $result->id;
                
                // convert created_by_info string into an object
                if(!empty($result->created_by_info)) {
                    $result->created_by_info = (object) $this->stringToArray($result->created_by_info, "|", ["user_id", "name", "phone_number", "email", "image", "user_type"]);
                }

                // convert student_info if loaded
                if(!empty($result->student_info)) {
                    $result->student_info = (object) $this->stringToArray($result->student_info, "|", ["student_id", "name", "unique_id", "phone_number", "email", "image", "user_type", "gender"]);
                }

                // convert room_info if loaded
                if(!empty($result->room_info)) {
                    $result->room_info = (object) $this->stringToArray($result->room_info, "|", ["room_id", "name", "code", "room_type", "room_condition"]);
                }

                // convert block_info if loaded
                if(!empty($result->block_info)) {
                    $result->block_info = (object) $this->stringToArray($result->block_info, "|", ["block_id", "name", "code"]);
                }

                // convert building_info if loaded
                if(!empty($result->building_info)) {
                    $result->building_info = (object) $this->stringToArray($result->building_info, "|", ["building_id", "name", "code"]);
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
     * View a single bed
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function view(stdClass $params) {

        try {

            if(empty($params->bed_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $bedRecord = $this->list($params);
            if(empty($bedRecord["data"])) {
                return ["code" => 404, "data" => "Sorry! No record was found."];
            }

            return $bedRecord["data"][0];

        } catch(PDOException $e) {
            return [
                "code" => 400,
                "data" => $e->getMessage()
            ];
        }

    }

    /**
     * Create a new bed
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function create(stdClass $params) {

        try {

            global $accessObject, $defaultUser;

            // check permission
            if(!$accessObject->hasAccess("create", "housing")) {
                return ["code" => 400, "data" => $this->permission_denied];
            }

            // validate required fields
            if(empty($params->room_id)) {
                return ["code" => 400, "data" => "Sorry! The room is required."];
            }

            // verify room exists
            $roomCheck = $this->pushQuery("item_id, capacity", "housing_rooms", "item_id='{$params->room_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
            if(empty($roomCheck)) {
                return ["code" => 400, "data" => "Sorry! An invalid room was selected."];
            }

            $roomData = $roomCheck[0];

            // check room capacity
            $existingBeds = $this->pushQuery("COUNT(*) as count", "housing_beds", "room_id='{$params->room_id}' AND client_id='{$params->clientId}' AND deleted='0'");
            $existingCount = !empty($existingBeds) ? (int)$existingBeds[0]->count : 0;
            $roomCapacity = (int)($roomData->capacity ?? 1);

            if($existingCount >= $roomCapacity) {
                return ["code" => 400, "data" => "Sorry! This room has reached its maximum capacity of {$roomCapacity} beds."];
            }

            // generate bed code if not provided
            if(empty($params->code)) {
                $counter = $this->append_zeros(($this->itemsCount("housing_beds", "client_id = '{$params->clientId}' AND room_id='{$params->room_id}'") + 1), $this->append_zeros);
                $params->code = "BD".$counter;
            } else {
                // check if code already exists
                $codeCheck = $this->pushQuery("item_id", "housing_beds", "code='{$params->code}' AND client_id='{$params->clientId}' AND room_id='{$params->room_id}' AND deleted='0' LIMIT 1");
                if(!empty($codeCheck)) {
                    return ["code" => 400, "data" => "Sorry! A bed with this code already exists in this room."];
                }
            }

            $item_id = random_string("alnum", RANDOM_STRING);

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO housing_beds SET 
                    item_id = ?, 
                    client_id = ?, 
                    room_id = ?,
                    created_by = ?,
                    name = ?,
                    code = ?,
                    bed_number = ?,
                    description = ?,
                    occupied = ?,
                    student_id = ?,
                    status = ?
            ");
            $stmt->execute([
                $item_id,
                $params->clientId,
                $params->room_id,
                $params->userId ?? $defaultUser->user_id,
                $params->name ?? $params->code,
                $params->code,
                $params->bed_number ?? null,
                $params->description ?? null,
                $params->occupied ?? '0',
                $params->student_id ?? null,
                $params->status ?? '1'
            ]);
            
            // log the user activity
            $this->userLogs("housing_bed", $item_id, null, "{$defaultUser->name} created a new Bed: {$params->code}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Bed successfully created.", 
                "refresh" => 2000,
                "additional" => ["clear" => true, "href" => "{$this->baseUrl}housing/bed/{$item_id}"]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update existing bed
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        try {

            global $accessObject, $defaultUser;

            // check permission
            if(!$accessObject->hasAccess("update", "housing")) {
                return ["code" => 400, "data" => $this->permission_denied];
            }

            if(empty($params->bed_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // get old record
            $prevData = $this->pushQuery("*", "housing_beds", "item_id='{$params->bed_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");

            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $prevData = $prevData[0];

            // check code uniqueness if changed
            if(!empty($params->code) && $params->code !== $prevData->code) {
                $codeCheck = $this->pushQuery("item_id", "housing_beds", "code='{$params->code}' AND client_id='{$params->clientId}' AND room_id='{$prevData->room_id}' AND item_id != '{$params->bed_id}' AND deleted='0' LIMIT 1");
                if(!empty($codeCheck)) {
                    return ["code" => 400, "data" => "Sorry! A bed with this code already exists in this room."];
                }
            }

            // verify room if changed
            if(!empty($params->room_id) && $params->room_id !== $prevData->room_id) {
                $roomCheck = $this->pushQuery("item_id, capacity", "housing_rooms", "item_id='{$params->room_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
                if(empty($roomCheck)) {
                    return ["code" => 400, "data" => "Sorry! An invalid room was selected."];
                }

                // check room capacity
                $existingBeds = $this->pushQuery("COUNT(*) as count", "housing_beds", "room_id='{$params->room_id}' AND client_id='{$params->clientId}' AND deleted='0'");
                $existingCount = !empty($existingBeds) ? (int)$existingBeds[0]->count : 0;
                $roomCapacity = (int)($roomCheck[0]->capacity ?? 1);

                if($existingCount >= $roomCapacity) {
                    return ["code" => 400, "data" => "Sorry! The selected room has reached its maximum capacity."];
                }
            }

            // verify student if assigned
            if(!empty($params->student_id)) {
                $studentCheck = $this->pushQuery("item_id", "users", "item_id='{$params->student_id}' AND client_id='{$params->clientId}' AND user_type='student' AND deleted='0' LIMIT 1");
                if(empty($studentCheck)) {
                    return ["code" => 400, "data" => "Sorry! An invalid student was selected."];
                }

                // check if student already has a bed
                $studentBedCheck = $this->pushQuery("item_id", "housing_beds", "student_id='{$params->student_id}' AND client_id='{$params->clientId}' AND item_id != '{$params->bed_id}' AND deleted='0' LIMIT 1");
                if(!empty($studentBedCheck)) {
                    return ["code" => 400, "data" => "Sorry! This student already has a bed assigned."];
                }
            }

            // build update query
            $updateFields = ["date_updated = now()"];
            $updateValues = [];

            if(isset($params->name)) {
                $updateFields[] = "name = ?";
                $updateValues[] = $params->name;
            }
            if(isset($params->code)) {
                $updateFields[] = "code = ?";
                $updateValues[] = $params->code;
            }
            if(isset($params->room_id)) {
                $updateFields[] = "room_id = ?";
                $updateValues[] = $params->room_id;
            }
            if(isset($params->bed_number)) {
                $updateFields[] = "bed_number = ?";
                $updateValues[] = $params->bed_number;
            }
            if(isset($params->description)) {
                $updateFields[] = "description = ?";
                $updateValues[] = $params->description;
            }
            if(isset($params->occupied)) {
                $updateFields[] = "occupied = ?";
                $updateValues[] = $params->occupied;
            }
            if(isset($params->student_id)) {
                $updateFields[] = "student_id = ?";
                $updateValues[] = $params->student_id;
                // auto-set occupied if student is assigned
                if(empty($params->occupied)) {
                    $updateFields[] = "occupied = '1'";
                }
            } elseif(isset($params->student_id) && empty($params->student_id)) {
                // clear student assignment
                $updateFields[] = "student_id = NULL";
                $updateFields[] = "occupied = '0'";
            }
            if(isset($params->status)) {
                $updateFields[] = "status = ?";
                $updateValues[] = $params->status;
            }

            $updateValues[] = $params->bed_id;
            $updateValues[] = $params->clientId;

            $stmt = $this->db->prepare("
                UPDATE housing_beds SET " . implode(", ", $updateFields) . "
                WHERE item_id = ? AND client_id = ? AND deleted = ? LIMIT 1
            ");
            $stmt->execute(array_merge($updateValues, [0]));
            
            // log the user activity
            $this->userLogs("housing_bed", $params->bed_id, $prevData, "{$defaultUser->name} updated the Bed: {$prevData->code}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Bed successfully updated.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Delete bed (soft delete)
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function delete(stdClass $params) {

        try {

            global $accessObject, $defaultUser;

            // check permission
            if(!$accessObject->hasAccess("delete", "housing")) {
                return ["code" => 400, "data" => $this->permission_denied];
            }

            if(empty($params->bed_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // get the record
            $prevData = $this->pushQuery("*", "housing_beds", "item_id='{$params->bed_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");

            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $prevData = $prevData[0];

            // check if bed is occupied
            if($prevData->occupied == '1' && !empty($prevData->student_id)) {
                return ["code" => 400, "data" => "Sorry! This bed is currently occupied. Please unassign the student before deleting."];
            }

            // soft delete
            $stmt = $this->db->prepare("
                UPDATE housing_beds SET deleted = ?, date_deleted = now() 
                WHERE item_id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([1, $params->bed_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("housing_bed", $params->bed_id, $prevData, "{$defaultUser->name} deleted the Bed: {$prevData->code}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Bed successfully deleted.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Assign student to bed
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function assign_student(stdClass $params) {

        try {

            global $accessObject, $defaultUser;

            // check permission
            if(!$accessObject->hasAccess("update", "housing")) {
                return ["code" => 400, "data" => $this->permission_denied];
            }

            if(empty($params->bed_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid bed id was supplied."];
            }

            if(empty($params->student_id)) {
                return ["code" => 400, "data" => "Sorry! A student is required."];
            }

            // get bed record
            $bedData = $this->pushQuery("*", "housing_beds", "item_id='{$params->bed_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
            if(empty($bedData)) {
                return ["code" => 400, "data" => "Sorry! An invalid bed was selected."];
            }

            $bedData = $bedData[0];

            // verify student exists
            $studentCheck = $this->pushQuery("item_id, name", "users", "item_id='{$params->student_id}' AND client_id='{$params->clientId}' AND user_type='student' AND deleted='0' LIMIT 1");
            if(empty($studentCheck)) {
                return ["code" => 400, "data" => "Sorry! An invalid student was selected."];
            }

            // check if bed is already occupied
            if($bedData->occupied == '1' && !empty($bedData->student_id)) {
                return ["code" => 400, "data" => "Sorry! This bed is already occupied."];
            }

            // check if student already has a bed
            $studentBedCheck = $this->pushQuery("item_id, code", "housing_beds", "student_id='{$params->student_id}' AND client_id='{$params->clientId}' AND item_id != '{$params->bed_id}' AND deleted='0' LIMIT 1");
            if(!empty($studentBedCheck)) {
                return ["code" => 400, "data" => "Sorry! This student already has a bed assigned (Bed: {$studentBedCheck[0]->code})."];
            }

            // assign student
            $stmt = $this->db->prepare("
                UPDATE housing_beds SET 
                    student_id = ?, 
                    occupied = '1',
                    date_updated = now()
                WHERE item_id = ? AND client_id = ? AND deleted = ? LIMIT 1
            ");
            $stmt->execute([$params->student_id, $params->bed_id, $params->clientId, 0]);
            
            // log the user activity
            $this->userLogs("housing_bed", $params->bed_id, $bedData, "{$defaultUser->name} assigned student to Bed: {$bedData->code}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Student successfully assigned to bed.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Unassign student from bed
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function unassign_student(stdClass $params) {

        try {

            global $accessObject, $defaultUser;

            // check permission
            if(!$accessObject->hasAccess("update", "housing")) {
                return ["code" => 400, "data" => $this->permission_denied];
            }

            if(empty($params->bed_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid bed id was supplied."];
            }

            // get bed record
            $bedData = $this->pushQuery("*", "housing_beds", "item_id='{$params->bed_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
            if(empty($bedData)) {
                return ["code" => 400, "data" => "Sorry! An invalid bed was selected."];
            }

            $bedData = $bedData[0];

            // check if bed is occupied
            if($bedData->occupied != '1' || empty($bedData->student_id)) {
                return ["code" => 400, "data" => "Sorry! This bed is not currently occupied."];
            }

            // unassign student
            $stmt = $this->db->prepare("
                UPDATE housing_beds SET 
                    student_id = NULL, 
                    occupied = '0',
                    date_updated = now()
                WHERE item_id = ? AND client_id = ? AND deleted = ? LIMIT 1
            ");
            $stmt->execute([$params->bed_id, $params->clientId, 0]);
            
            // log the user activity
            $this->userLogs("housing_bed", $params->bed_id, $bedData, "{$defaultUser->name} unassigned student from Bed: {$bedData->code}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Student successfully unassigned from bed.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }
    
}
?>