<?php

class Blocks extends Myschoolgh {

    public $blocksModel;

    public function __construct($params = null) {
        parent::__construct();
        $this->blocksModel = load_class("BlocksModel", "models/housing");
        
        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;
    }

    /**
     * List Blocks
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
        $params->query .= !empty($params->block_id) ? " AND a.item_id='{$params->block_id}'" : null;
        $params->query .= !empty($params->building_id) ? " AND a.building_id='{$params->building_id}'" : null;
        $params->query .= !empty($params->status) ? " AND a.status='{$params->status}'" : null;

        try {

            $loadRooms = (bool) isset($params->load_rooms);
            $loadBeds = (bool) isset($params->load_beds);
            $loadBuilding = (bool) isset($params->load_building);

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM housing_rooms b 
                     WHERE b.block_id = a.item_id AND b.status='1' AND b.client_id='{$params->clientId}') AS rooms_count,
                    (SELECT COUNT(*) FROM housing_beds c 
                     LEFT JOIN housing_rooms d ON d.item_id = c.room_id
                     WHERE d.block_id = a.item_id AND c.status='1' AND c.client_id='{$params->clientId}') AS beds_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) 
                     FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                    ".(!empty($params->building_id) && $loadBuilding ? ", 
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',b.code,'|',b.building_type) 
                     FROM housing_buildings b WHERE b.item_id = a.building_id LIMIT 1) AS building_info" : null)."
                FROM housing_blocks a
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

                // load rooms if requested
                if($loadRooms) {
                    $roomsParams = (object) [
                        "block_id" => $result->item_id,
                        "clientId" => $params->clientId,
                        "load_beds" => $loadBeds,
                        "limit" => 100
                    ];
                    $roomsController = load_class("Rooms", "controllers/housing", $params);
                    $roomsResult = $roomsController->list($roomsParams);
                    $result->rooms_list = $roomsResult["data"] ?? [];
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
     * View a single block
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function view(stdClass $params) {

        try {

            if(empty($params->block_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $blockRecord = $this->list($params);
            if(empty($blockRecord["data"])) {
                return ["code" => 404, "data" => "Sorry! No record was found."];
            }

            $blockRecord = $blockRecord["data"][0];
            
            // load rooms with beds
            $roomsParams = (object) [
                "block_id" => $blockRecord->item_id,
                "clientId" => $params->clientId,
                "load_beds" => true,
                "limit" => 100
            ];
            $roomsController = load_class("Rooms", "controllers/housing", $params);
            $roomsResult = $roomsController->list($roomsParams);
            $blockRecord->rooms_list = $roomsResult["data"] ?? [];

            return $blockRecord;

        } catch(PDOException $e) {
            return [
                "code" => 400,
                "data" => $e->getMessage()
            ];
        }

    }

    /**
     * Create a new block
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
                return ["code" => 400, "data" => "Sorry! The block name is required."];
            }

            if(empty($params->building_id)) {
                return ["code" => 400, "data" => "Sorry! The building is required."];
            }

            // verify building exists
            $buildingCheck = $this->pushQuery("item_id", "housing_buildings", "item_id='{$params->building_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
            if(empty($buildingCheck)) {
                return ["code" => 400, "data" => "Sorry! An invalid building was selected."];
            }

            // generate block code if not provided
            if(empty($params->code)) {
                $counter = $this->append_zeros(($this->itemsCount("housing_blocks", "client_id = '{$params->clientId}' AND building_id='{$params->building_id}'") + 1), $this->append_zeros);
                $params->code = "BLK".$counter;
            } else {
                // check if code already exists
                $codeCheck = $this->pushQuery("item_id", "housing_blocks", "code='{$params->code}' AND client_id='{$params->clientId}' AND building_id='{$params->building_id}' AND deleted='0' LIMIT 1");
                if(!empty($codeCheck)) {
                    return ["code" => 400, "data" => "Sorry! A block with this code already exists in this building."];
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
                INSERT INTO housing_blocks SET 
                    item_id = ?, 
                    client_id = ?, 
                    building_id = ?,
                    created_by = ?,
                    name = ?,
                    code = ?,
                    floor_number = ?,
                    description = ?,
                    facilities = ?,
                    capacity = ?,
                    status = ?
            ");
            $stmt->execute([
                $item_id,
                $params->clientId,
                $params->building_id,
                $params->userId ?? $defaultUser->user_id,
                $params->name,
                $params->code,
                $params->floor_number ?? null,
                $params->description ?? null,
                json_encode($facilities),
                $params->capacity ?? null,
                $params->status ?? '1'
            ]);
            
            // log the user activity
            $this->userLogs("housing_block", $item_id, null, "{$defaultUser->name} created a new Block: {$params->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Block successfully created.", 
                "refresh" => 2000,
                "additional" => ["clear" => true, "href" => "{$this->baseUrl}housing/block/{$item_id}"]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update existing block
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

            if(empty($params->block_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // get old record
            $prevData = $this->pushQuery("*", "housing_blocks", "item_id='{$params->block_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");

            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $prevData = $prevData[0];

            // check code uniqueness if changed
            if(!empty($params->code) && $params->code !== $prevData->code) {
                $codeCheck = $this->pushQuery("item_id", "housing_blocks", "code='{$params->code}' AND client_id='{$params->clientId}' AND building_id='{$prevData->building_id}' AND item_id != '{$params->block_id}' AND deleted='0' LIMIT 1");
                if(!empty($codeCheck)) {
                    return ["code" => 400, "data" => "Sorry! A block with this code already exists in this building."];
                }
            }

            // verify building if changed
            if(!empty($params->building_id) && $params->building_id !== $prevData->building_id) {
                $buildingCheck = $this->pushQuery("item_id", "housing_buildings", "item_id='{$params->building_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
                if(empty($buildingCheck)) {
                    return ["code" => 400, "data" => "Sorry! An invalid building was selected."];
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
            if(isset($params->building_id)) {
                $updateFields[] = "building_id = ?";
                $updateValues[] = $params->building_id;
            }
            if(isset($params->floor_number)) {
                $updateFields[] = "floor_number = ?";
                $updateValues[] = $params->floor_number;
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
            if(isset($params->status)) {
                $updateFields[] = "status = ?";
                $updateValues[] = $params->status;
            }

            $updateValues[] = $params->block_id;
            $updateValues[] = $params->clientId;

            $stmt = $this->db->prepare("
                UPDATE housing_blocks SET " . implode(", ", $updateFields) . "
                WHERE item_id = ? AND client_id = ? AND deleted = ? LIMIT 1
            ");
            $stmt->execute(array_merge($updateValues, [0]));
            
            // log the user activity
            $this->userLogs("housing_block", $params->block_id, $prevData, "{$defaultUser->name} updated the Block: {$prevData->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Block successfully updated.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Delete block (soft delete)
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

            if(empty($params->block_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // get the record
            $prevData = $this->pushQuery("*", "housing_blocks", "item_id='{$params->block_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");

            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $prevData = $prevData[0];

            // check if block has rooms
            $roomsCount = $this->pushQuery("COUNT(*) as count", "housing_rooms", "block_id='{$params->block_id}' AND status='1' AND deleted='0'");
            if(!empty($roomsCount) && $roomsCount[0]->count > 0) {
                return ["code" => 400, "data" => "Sorry! This block has rooms assigned. Please remove all rooms before deleting."];
            }

            // soft delete
            $stmt = $this->db->prepare("
                UPDATE housing_blocks SET deleted = ?, date_deleted = now() 
                WHERE item_id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([1, $params->block_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("housing_block", $params->block_id, $prevData, "{$defaultUser->name} deleted the Block: {$prevData->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Block successfully deleted.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }
    
}
?>