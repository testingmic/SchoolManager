<?php

class Buildings extends Myschoolgh {

    public $buildingsModel;

    public function __construct($params = null) {
        parent::__construct();
        $this->buildingsModel = load_class("BuildingsModel", "models/housing");
        
        // get the client data
        $client_data = $params->client_data ?? [];
        $this->iclient = $client_data;
    }

    /**
     * List Buildings
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
        $params->query .= !empty($params->building_id) ? " AND a.item_id='{$params->building_id}'" : null;
        $params->query .= !empty($params->building_type) ? " AND a.building_type='{$params->building_type}'" : null;
        $params->query .= !empty($params->status) ? " AND a.status='{$params->status}'" : null;

        try {

            $loadBlocks = (bool) isset($params->load_blocks);
            $loadStats = (bool) isset($params->load_stats);

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT COUNT(*) FROM housing_blocks b 
                     WHERE b.building_id = a.item_id AND b.status='1' AND b.client_id='{$params->clientId}') AS blocks_count,
                    (SELECT COUNT(*) FROM housing_rooms c 
                     LEFT JOIN housing_blocks d ON d.item_id = c.block_id
                     WHERE d.building_id = a.item_id AND c.status='1' AND c.client_id='{$params->clientId}') AS rooms_count,
                    (SELECT COUNT(*) FROM housing_beds e 
                     LEFT JOIN housing_rooms f ON f.item_id = e.room_id
                     LEFT JOIN housing_blocks g ON g.item_id = f.block_id
                     WHERE g.building_id = a.item_id AND e.status='1' AND e.client_id='{$params->clientId}') AS beds_count,
                    (SELECT CONCAT(b.item_id,'|',b.name,'|',COALESCE(b.phone_number,'NULL'),'|',COALESCE(b.email,'NULL'),'|',b.image,'|',b.user_type) 
                     FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS created_by_info
                FROM housing_buildings a
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

                // decode JSON fields
                if(!empty($result->facilities)) {
                    $result->facilities = json_decode($result->facilities, true) ?? [];
                } else {
                    $result->facilities = [];
                }

                // load blocks if requested
                if($loadBlocks) {
                    $blocksParams = (object) [
                        "building_id" => $result->item_id,
                        "clientId" => $params->clientId,
                        "limit" => 100
                    ];
                    $blocksController = load_class("Blocks", "controllers/housing", $params);
                    $blocksResult = $blocksController->list($blocksParams);
                    $result->blocks_list = $blocksResult["data"] ?? [];
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
     * View a single building
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function view(stdClass $params) {

        try {

            if(empty($params->building_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $buildingRecord = $this->list($params);
            if(empty($buildingRecord["data"])) {
                return ["code" => 404, "data" => "Sorry! No record was found."];
            }

            $buildingRecord = $buildingRecord["data"][0];
            
            // load blocks with rooms and beds
            $blocksParams = (object) [
                "building_id" => $buildingRecord->item_id,
                "clientId" => $params->clientId,
                "load_rooms" => true,
                "load_beds" => true,
                "limit" => 100
            ];
            $blocksController = load_class("Blocks", "controllers/housing", $params);
            $blocksResult = $blocksController->list($blocksParams);
            $buildingRecord->blocks_list = $blocksResult["data"] ?? [];

            return $buildingRecord;

        } catch(PDOException $e) {
            return [
                "code" => 400,
                "data" => $e->getMessage()
            ];
        }

    }

    /**
     * Create a new building
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
                return ["code" => 400, "data" => "Sorry! The building name is required."];
            }

            // generate building code if not provided
            if(empty($params->code)) {
                $counter = $this->append_zeros(($this->itemsCount("housing_buildings", "client_id = '{$params->clientId}'") + 1), $this->append_zeros);
                $params->code = "BLD".$counter;
            } else {
                // check if code already exists
                $codeCheck = $this->pushQuery("item_id", "housing_buildings", "code='{$params->code}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");
                if(!empty($codeCheck)) {
                    return ["code" => 400, "data" => "Sorry! A building with this code already exists."];
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
                INSERT INTO housing_buildings SET 
                    item_id = ?, 
                    client_id = ?, 
                    created_by = ?,
                    name = ?,
                    code = ?,
                    building_type = ?,
                    address = ?,
                    description = ?,
                    facilities = ?,
                    gender_restriction = ?,
                    capacity = ?,
                    status = ?
            ");
            $stmt->execute([
                $item_id,
                $params->clientId,
                $params->userId ?? $defaultUser->user_id,
                $params->name,
                $params->code,
                $params->building_type ?? 'dormitory',
                $params->address ?? null,
                $params->description ?? null,
                json_encode($facilities),
                $params->gender_restriction ?? 'mixed',
                $params->capacity ?? null,
                $params->status ?? '1'
            ]);
            
            // log the user activity
            $this->userLogs("housing_building", $item_id, null, "{$defaultUser->name} created a new Building: {$params->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Building successfully created.", 
                "refresh" => 2000,
                "additional" => ["clear" => true, "href" => "{$this->baseUrl}housing/building/{$item_id}"]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Update existing building
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

            if(empty($params->building_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // get old record
            $prevData = $this->pushQuery("*", "housing_buildings", "item_id='{$params->building_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");

            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $prevData = $prevData[0];

            // check code uniqueness if changed
            if(!empty($params->code) && $params->code !== $prevData->code) {
                $codeCheck = $this->pushQuery("item_id", "housing_buildings", "code='{$params->code}' AND client_id='{$params->clientId}' AND item_id != '{$params->building_id}' AND deleted='0' LIMIT 1");
                if(!empty($codeCheck)) {
                    return ["code" => 400, "data" => "Sorry! A building with this code already exists."];
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
            if(isset($params->building_type)) {
                $updateFields[] = "building_type = ?";
                $updateValues[] = $params->building_type;
            }
            if(isset($params->address)) {
                $updateFields[] = "address = ?";
                $updateValues[] = $params->address;
            }
            if(isset($params->description)) {
                $updateFields[] = "description = ?";
                $updateValues[] = $params->description;
            }
            if(isset($params->facilities)) {
                $updateFields[] = "facilities = ?";
                $updateValues[] = json_encode($facilities);
            }
            if(isset($params->gender_restriction)) {
                $updateFields[] = "gender_restriction = ?";
                $updateValues[] = $params->gender_restriction;
            }
            if(isset($params->capacity)) {
                $updateFields[] = "capacity = ?";
                $updateValues[] = $params->capacity;
            }
            if(isset($params->status)) {
                $updateFields[] = "status = ?";
                $updateValues[] = $params->status;
            }

            $updateValues[] = $params->building_id;
            $updateValues[] = $params->clientId;

            $stmt = $this->db->prepare("
                UPDATE housing_buildings SET " . implode(", ", $updateFields) . "
                WHERE item_id = ? AND client_id = ? AND deleted = ? LIMIT 1
            ");
            $stmt->execute(array_merge($updateValues, [0]));
            
            // log the user activity
            $this->userLogs("housing_building", $params->building_id, $prevData, "{$defaultUser->name} updated the Building: {$prevData->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Building successfully updated.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * Delete building (soft delete)
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

            if(empty($params->building_id)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            // get the record
            $prevData = $this->pushQuery("*", "housing_buildings", "item_id='{$params->building_id}' AND client_id='{$params->clientId}' AND deleted='0' LIMIT 1");

            if(empty($prevData)) {
                return ["code" => 400, "data" => "Sorry! An invalid id was supplied."];
            }

            $prevData = $prevData[0];

            // check if building has blocks
            $blocksCount = $this->pushQuery("COUNT(*) as count", "housing_blocks", "building_id='{$params->building_id}' AND status='1' AND deleted='0'");
            if(!empty($blocksCount) && $blocksCount[0]->count > 0) {
                return ["code" => 400, "data" => "Sorry! This building has blocks assigned. Please remove all blocks before deleting."];
            }

            // soft delete
            $stmt = $this->db->prepare("
                UPDATE housing_buildings SET deleted = ?, date_deleted = now() 
                WHERE item_id = ? AND client_id = ? LIMIT 1
            ");
            $stmt->execute([1, $params->building_id, $params->clientId]);
            
            // log the user activity
            $this->userLogs("housing_building", $params->building_id, $prevData, "{$defaultUser->name} deleted the Building: {$prevData->name}", $params->userId ?? $defaultUser->user_id);

            return [
                "code" => 200, 
                "data" => "Building successfully deleted.", 
                "refresh" => 2000
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }
    
}
?>