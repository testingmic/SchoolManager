<?php

class Rooms extends Myschoolgh {

    public $roomsModel;

    public function __construct($params = null) {
        parent::__construct();
        $this->roomsModel = load_class("RoomsModel", "models/housing");
        
        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;
    }

    /**
     * List Rooms
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
        $params->query .= !empty($params->room_id) ? " AND a.item_id='{$params->room_id}'" : null;
        $params->query .= !empty($params->block_id) ? " AND a.block_id='{$params->block_id}'" : null;
        $params->query .= !empty($params->building_id) ? " AND b.building_id='{$params->building_id}'" : null;
        $params->query .= !empty($params->room_type) ? " AND a.room_type='{$params->room_type}'" : null;
        $params->query .= !empty($params->room_condition) ? " AND a.room_condition='{$params->room_condition}'" : null;
        $params->query .= !empty($params->status) ? " AND a.status='{$params->status}'" : null;
        $params->query .= !empty($params->occupied) ? " AND a.occupied='{$params->occupied}'" : null;

        try {

            $loadBeds = (bool) isset($params->load_beds);
            $loadBlock = (bool) isset($params->load_block);
            $loadBuilding = (bool) isset($params->load_building);
            $loadOccupants = (bool) isset($params->load_occupants);

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM housing_beds c 
                     WHERE c.room_id = a.item_id AND c.status='1' AND c.client_id='{$params->clientId}') AS beds_count,
                    (SELECT COUNT(*) FROM housing_beds c 
                     WHERE c.room_id = a.item_id AND c.status='1' AND c.occupied='1' AND c.client_id='{$params->clientId}') AS occupied_beds_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) 
                     FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                    ".(!empty($params->block_id) && $loadBlock ? ", 
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.code,'|',b.floor_number) 
                     FROM housing_blocks b WHERE b.item_id = a.block_id LIMIT 1) AS block_info" : null)."
                    ".(!empty($params->building_id) && $loadBuilding ? ", 
                    (SELECT CONCAT(c.item_id,'|',c.name,'|',c.code,'|',c.building_type) 
                     FROM housing_buildings c 
                     LEFT JOIN housing_blocks b ON b.building_id = c.item_id
                     WHERE b.item_id = a.block_id LIMIT 1) AS building_info" : null)."
                FROM housing_rooms a
                ".(!empty($params->building_id) ? " LEFT JOIN housing_blocks b ON b.item_id = a.block_id" : null)."
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

                // convert block_info if loaded
                if(!empty($result->block_info)) {
                    $result->block_info = (object) $this->stringToArray($result->block_info, "|", ["block_id", "name", "code", "floor_number"]);
                }

                // convert building_info if loaded
                if(!empty($result->building_info)) {
                    $result->building_info = (object) $this->stringToArray($result->building_info, "|", ["building_id", "name", "code", "building_type"]);
                }

                // decode JSON fields
                if(!empty($result->facilities)) {
                    $result->facilities = json_decode($result->facilities, true) ?? [];
                } else {
                    $result->facilities = [];
                }

                // calculate availability
                $result->available_beds = ($result->beds_count ?? 0) - ($result->occupied_beds_count ?? 0);
                $result->occupancy_rate = ($result->beds_count > 0) ? round(($result->occupied_beds_count / $result->beds_count) * 100, 2) : 0;

                // load beds if requested
                if($loadBeds) {
                    $bedsParams = (object) [
                        "room_id" => $result->item_id,
                        "clientId" => $params->clientId,
                        "load_occupants" => $loadOccupants,
                        "limit" => 100
                    ];
                    $bedsController = load_class("Beds", "controllers/housing", $params);
                    $bedsResult = $bedsController->list($bedsParams);
                    $result->beds_list = $bedsResult["data"] ?? [];
                }

                // load occupants if requested
                if($loadOccupants && !$loadBeds) {
                    $bedsParams = (object) [
                        "room_id" => $result->item_id,
                        "clientId" => $params->clientId,
                        "occupied" => "1",
                        "load_occupants" => true,
                        "limit" => 100
                    ];
                    $bedsController = load_class("Beds", "controllers/housing", $params);
                    $bedsResult = $bedsController->list($bedsParams);
                    $result->occupants_list = $bedsResult["data"] ?? [];
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
     * View a single room
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function view(stdClass $params) {

        try {

            if(empty($params->room_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $roomRecord = $this->list($params);
            if(empty($roomRecord["data"])) {
                return ["code" => 404, "data" => "Sorry! No record was found."];
            }

            $roomRecord = $roomRecord["data"][0];
            
            // load beds with occupants
            $bedsParams = (object) [
                "room_id" => $roomRecord->item_id,
                "clientId" => $params->clientId,
                "load_occupants" => true,
                "limit" => 100
            ];
            $bedsController = load_class("Beds", "controllers/housing", $params);
            $bedsResult = $bedsController->list($bedsParams);
            $roomRecord->beds_list = $bedsResult["data"] ?? [];

            return $roomRecord;

        } catch(PDOException $e) {
            return [
                "code" => 400,
                "data" => $e->getMessage()
            ];
        }

    }

    /**
     * Create a new room
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
            if(empty($params->name)) {
                return ["code" => 400, "data" => "Sorry! The room name is required."];
            }

            if(empty($params->block_id)) {
                return ["code" => 400, "data" => "Sorry! The block is required."];
            }

            // verify block exists
            $blockCheck = $this->pushQuery("item_id", "housing_blocks", "item_id='{$params->block_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
            if(empty($blockCheck)) {
                return ["code" => 400, "data" => "Sorry! An invalid block was selected."];
            }

            // generate room code if not provided
            if(empty($params->code)) {
                $counter = $this->append_zeros(($this->itemsCount("housing_rooms", "client_id = '{$params->clientId}' AND block_id='{$params->block_id}'") + 1), $this->append_zeros);
                $params->code = "RM".$counter;
            } else {
                // check if code already exists
                $codeCheck = $this->pushQuery("item_id", "housing_rooms", "code='{$params->code}' AND client_id='{$params->clientId}' AND block_id='{$params->block_id}' AND deleted='0' LIMIT 1");
                if(!empty($codeCheck)) {
                    return ["code" => 400, "data" => "Sorry! A room with this code already exists in this block."];
                }
            }

            $item_id = random_string("alnum", RANDOM_STRING);

            // prepare facilities array
            $facilities = [];
            if(!empty($params->facilities) && is_array($params->facilities)) {
                $facilities = $params->facilities;
            }

            // execute the statement
            $stmt = $this->db->prepare("
                INSERT INTO housing_rooms SET 
                    item_id = ?, 
                    client_id = ?, 
                    block_id = ?,
                    created_by = ?,
                    name = ?,
                    code = ?,
                    room_type = ?,
                    room_condition = ?,
                    description = ?,
                    facilities = ?,
                    capacity = ?,
                    occupied = ?,
                    status = ?
            ");
            $stmt->execute([
                $item_id,
                $params->clientId,
                $params->block_id,
                $params->userId ?? $defaultUser->user_id,
                $params->name,
                $params->code,
                $params->room_type ?? 'single',
                $params->room_condition ?? 'good',
                $params->description ?? null,
                json_encode($facilities),
                $params->capacity ?? 1,
                $params->occupied ?? '0',
                $params->status ?? '1'
            ]);
            
            // log the user activity
            $this->userLogs("housing_room", $item_id, null, "{$defaultUser->name} created a new Room: {$params->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Room successfully created.", 
                "refresh" => 2000,
                "additional" => ["clear" => true, "href" => "{$this->baseUrl}housing/room/{$item_id}"]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update existing room
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

            if(empty($params->room_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // get old record
            $prevData = $this->pushQuery("*", "housing_rooms", "item_id='{$params->room_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");

            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $prevData = $prevData[0];

            // check code uniqueness if changed
            if(!empty($params->code) && $params->code !== $prevData->code) {
                $codeCheck = $this->pushQuery("item_id", "housing_rooms", "code='{$params->code}' AND client_id='{$params->clientId}' AND block_id='{$prevData->block_id}' AND item_id != '{$params->room_id}' AND deleted='0' LIMIT 1");
                if(!empty($codeCheck)) {
                    return ["code" => 400, "data" => "Sorry! A room with this code already exists in this block."];
                }
            }

            // verify block if changed
            if(!empty($params->block_id) && $params->block_id !== $prevData->block_id) {
                $blockCheck = $this->pushQuery("item_id", "housing_blocks", "item_id='{$params->block_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
                if(empty($blockCheck)) {
                    return ["code" => 400, "data" => "Sorry! An invalid block was selected."];
                }
            }

            // prepare facilities array
            $facilities = [];
            if(isset($params->facilities) && is_array($params->facilities)) {
                $facilities = $params->facilities;
            } elseif(!empty($prevData->facilities)) {
                $facilities = json_decode($prevData->facilities, true) ?? [];
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
            if(isset($params->block_id)) {
                $updateFields[] = "block_id = ?";
                $updateValues[] = $params->block_id;
            }
            if(isset($params->room_type)) {
                $updateFields[] = "room_type = ?";
                $updateValues[] = $params->room_type;
            }
            if(isset($params->room_condition)) {
                $updateFields[] = "room_condition = ?";
                $updateValues[] = $params->room_condition;
            }
            if(isset($params->description)) {
                $updateFields[] = "description = ?";
                $updateValues[] = $params->description;
            }
            if(isset($params->facilities)) {
                $updateFields[] = "facilities = ?";
                $updateValues[] = json_encode($facilities);
            }
            if(isset($params->capacity)) {
                $updateFields[] = "capacity = ?";
                $updateValues[] = $params->capacity;
            }
            if(isset($params->occupied)) {
                $updateFields[] = "occupied = ?";
                $updateValues[] = $params->occupied;
            }
            if(isset($params->status)) {
                $updateFields[] = "status = ?";
                $updateValues[] = $params->status;
            }

            $updateValues[] = $params->room_id;
            $updateValues[] = $params->clientId;

            $stmt = $this->db->prepare("
                UPDATE housing_rooms SET " . implode(", ", $updateFields) . "
                WHERE item_id = ? AND client_id = ? AND deleted = ? LIMIT 1
            ");
            $stmt->execute(array_merge($updateValues, [0]));
            
            // log the user activity
            $this->userLogs("housing_room", $params->room_id, $prevData, "{$defaultUser->name} updated the Room: {$prevData->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Room successfully updated.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Delete room (soft delete)
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

            if(empty($params->room_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // get the record
            $prevData = $this->pushQuery("*", "housing_rooms", "item_id='{$params->room_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");

            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $prevData = $prevData[0];

            // check if room has beds
            $bedsCount = $this->pushQuery("COUNT(*) as count", "housing_beds", "room_id='{$params->room_id}' AND status='1' AND deleted='0'");
            if(!empty($bedsCount) && $bedsCount[0]->count > 0) {
                return ["code" => 400, "data" => "Sorry! This room has beds assigned. Please remove all beds before deleting."];
            }

            // soft delete
            $stmt = $this->db->prepare("
                UPDATE housing_rooms SET deleted = ?, date_deleted = now() 
                WHERE item_id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([1, $params->room_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("housing_room", $params->room_id, $prevData, "{$defaultUser->name} deleted the Room: {$prevData->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Room successfully deleted.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }
    
}
?>