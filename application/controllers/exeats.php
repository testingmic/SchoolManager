<?php

class Exeats extends Myschoolgh {

    public $exeat_statuses = [
        "Pending" => "warning",
        "Approved" => "success",
        "Rejected" => "danger",
        "Returned" => "success",
        "Cancelled" => "danger",
        "Overdue" => "secondary"
    ];

    public $users_statuses = [
        "Pending" => "warning"
    ];

    /**
     * List the exeats
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function list($params = null) {

        global $accessObject, $defaultUser, $isWardParent;

        $params->query = "1";

        if(!$accessObject->hasAccess("view", "exeats") && !$isWardParent) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // build the query
        $params->query .= !empty($params->clientId) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= !empty($params->exeat_id) ? " AND a.item_id='{$params->exeat_id}'" : null;
        $params->query .= !empty($params->created_by) ? " AND a.created_by='{$params->created_by}'" : null;
        $params->query .= !empty($params->student_id) ? " AND a.student_id='{$params->student_id}'" : null;
        $params->query .= !empty($params->status) ? " AND a.status='{$params->status}'" : null;
        $params->query .= !empty($params->exeat_type) ? " AND a.exeat_type='{$params->exeat_type}'" : null;
        $params->query .= !empty($params->departure_date) ? " AND a.departure_date='{$params->departure_date}'" : null;
        $params->query .= !empty($params->pickup_by) ? " AND a.pickup_by='{$params->pickup_by}'" : null;
        $params->query .= !empty($params->return_date) ? " AND a.return_date='{$params->return_date}'" : null;

        // if the user does not have the permission to add an exeat
        $hasPermission = $accessObject->hasAccess("add", "exeats");
        $managePermission = $accessObject->hasAccess("manage", "exeats");

        // if the user does not have the permission to add an exeat
        if(!$hasPermission || $isWardParent) {
            $studentIds = [$defaultUser->user_id];
            if($isWardParent) {
                $studentIds = array_merge($studentIds, array_column($defaultUser->wards_list, "student_guid"));
            }
            $params->query .= " AND a.student_id IN {$this->inList($studentIds)}";
        }

        // if the user is not a ward parent and not a manager
        if(!$isWardParent && !$managePermission) {
            $params->query .= " AND a.created_by = '{$defaultUser->user_id}'";
        }

        try {

            $stmt = $this->db->prepare("SELECT a.*, b.name as student_name, b.gender, c.name as class_name
                FROM exeats a
                LEFT JOIN users b ON a.student_id = b.item_id
                LEFT JOIN classes c ON c.id = b.class_id
                WHERE {$params->query} AND a.status != 'Deleted' 
                ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute();

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // if the minified is true
                $data[] = $result;
            }

            return [ "code" => 200, "data" => $data ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }


    }

    /**
     * Get the exeat statistics
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function statistics($params = null) {
        
        global $accessObject;

        // check permission
        if(!$accessObject->hasAccess("manage", "exeats")) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        $result = [
            'summary' => [
                'status' => [
                    'Overdue' => 0,
                    'Total' => 0
                ],
                'gender' => [
                    'Male' => 0,
                    'Female' => 0,
                ],
                'pickup_by' => [
                    'Self' => 0,
                    'Guardian' => 0,
                    'Other' => 0
                ],
                'exeat_types' => [
                    'Day' => 0,
                    'Weekend' => 0,
                    'Emergency' => 0
                ],
                'overdue' => [
                    'total' => 0,
                    'list' => []
                ],
                'listing' => []
            ],
            'chart' => [
                'departure' => [],
                'return' => []
            ],
            'class' => [],
        ];

        $thirtyDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $today = date('Y-m-d', strtotime('+7 days'));

        $listDays = $this->listDays($thirtyDaysAgo, $today);

        foreach($this->exeat_statuses as $key => $value) {
            $result['summary']['status'][$key] = 0;
        }

        $stmt = $this->db->prepare("SELECT a.status, a.exeat_type, a.pickup_by, a.departure_date, a.return_date,
                b.name as student_name, b.gender, c.name as class_name
            FROM exeats a
            LEFT JOIN users b ON a.student_id = b.item_id
            LEFT JOIN classes c ON c.id = b.class_id
            WHERE a.client_id='{$params->clientId}' AND a.status != 'Deleted'
        ");
        $stmt->execute();
        $resultSet = $stmt->fetchAll(PDO::FETCH_OBJ);

        $datesGroup = [
            'yesterday' => date('Y-m-d', strtotime('yesterday')),
            'today' => date('Y-m-d'),
            'tomorrow' => date('Y-m-d', strtotime('tomorrow'))
        ];

        foreach($listDays as $each) {
            $result['chart']['departure'][$each] = 0;
            $result['chart']['return'][$each] = 0;
            $result['chart_grouping']['legend'][] = date('jS M', strtotime($each));
        }


        // loop through the result set
        foreach($resultSet as $each) {

            // check if the class name is already in the result
            if(!isset($result['class'][$each->class_name])) {
                $result['class'][$each->class_name] = [
                    'summary' => [
                        'Total' => 0,
                        'Pending' => 0,
                        'Approved' => 0,
                        'Rejected' => 0,
                        'Returned' => 0,
                        'Cancelled' => 0
                    ]
                ];
            }

            if(!isset($result['chart']['departure'][$each->departure_date])) {
                $result['chart']['departure'][$each->departure_date] = 0;
            }

            if(!isset($result['chart']['return'][$each->return_date])) {
                $result['chart']['return'][$each->return_date] = 0;
            }

            $result['summary']['status']['Total']++;

            // get the various dates and their count for the exeats and the returning
            $result['chart']['departure'][$each->departure_date]++;
            $result['chart']['return'][$each->return_date]++;

            // update the summary
            $result['summary']['exeat_types'][$each->exeat_type] = ($result['summary']['exeat_types'][$each->exeat_type] ?? 0) + 1;
            $result['summary']['pickup_by'][$each->pickup_by] = ($result['summary']['pickup_by'][$each->pickup_by] ?? 0) + 1;
            $result['summary']['status'][$each->status]++;

            if(!empty($each->gender)) {
                // set the gender count
                $result['summary']['gender'][$each->gender]++;
            }
            
            $result['class'][$each->class_name]['summary'][$each->status]++;
            $result['class'][$each->class_name]['summary']['Total']++;

            if(strtotime($each->return_date) < strtotime(date("Y-m-d"))&& $each->status == 'Approved') {
                $result['summary']['overdue']['total']++;
                $result['summary']['overdue']['list'][] = $each;
                $result['summary']['status']['Overdue']++;
            }

            foreach($datesGroup as $period => $date) {

                if(!isset($result['summary']['listing'][$period])) {
                    $result['summary']['listing'][$period] = [
                        'total' => 0,
                        'list' => []
                    ];
                }
                
                if((strtotime($each->departure_date) == strtotime($date)) && $each->status == 'Approved') {
                    $result['summary']['listing'][$period]['total']++;
                    $result['summary']['listing'][$period]['list'][] = $each;
                }

            } 

        }

        foreach(['departure', 'return'] as $ii => $key) {
            $result['chart_grouping']['data'][$ii]['name'] = ucwords($key);
            foreach($result['chart'][$key] as $ikey => $value) {
                $result['chart_grouping']['data'][$ii]['data'][] = $value;
            }
        }

        unset($result['chart']);

        // return the result
        return ["code" => 200, "data" => $result];

    }

    /**
     * Create a new exeat
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function create($params = null) {
        
        global $accessObject, $isWardParent, $isAdmin, $defaultUser;

        $byPass = true;
        $hasPermission = $accessObject->hasAccess("add", "exeats");
        if(!$hasPermission || $isWardParent) {
            $byPass = false;

            $studentIds = [$defaultUser->user_id];

            if($isWardParent) {
                $studentIds = array_merge($studentIds, array_column($defaultUser->wards_list, "student_guid"));
            }
            if(!in_array($params->student_id, $studentIds)) {
                return ["code" => 400, "data" => "Sorry! You are not authorized to create an exeat for this student."];
            }

            $byPass = true;
        }

        // check permission
        if(!$byPass) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

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

        // generate a unique item id
        $item_id = random_string("alnum", RANDOM_STRING);

        // insert the user information
		$stmt = $this->db->prepare("
            INSERT INTO exeats SET item_id = ?, client_id = ?, created_by = ?, last_updated = now()
            ".(!empty($params->student_id) ? ", student_id='{$params->student_id}'" : null)."
            ".(!empty($params->status) ? ", status='{$params->status}'" : null)."
            ".(!empty($params->exeat_type) ? ", exeat_type='{$params->exeat_type}'" : null)."
            ".(!empty($params->departure_date) ? ", departure_date='{$params->departure_date}'" : null)."
            ".(!empty($params->pickup_by) ? ", pickup_by='{$params->pickup_by}'" : null)."
            ".(!empty($params->guardian_contact) ? ", guardian_contact='{$params->guardian_contact}'" : null)."
            ".(!empty($params->reason) ? ", reason='{$params->reason}'" : null)."
            ".(!empty($params->return_date) ? ", return_date='{$params->return_date}'" : null)."
        ");

        // execute the insert user data
        $stmt->execute([$item_id, $params->clientId, $params->userId]);

        // log the user activity
        $this->userLogs("exeats", $item_id, null, "{$params->userData->name} created a new Exeat record for the Student with ID::{$params->student_id}", $params->userId);

        return ["code" => 200, "data" => "Exeat record successfully created.", "refresh" => 2000, "additional" => ["href" => "{$this->baseUrl}exeats_log"]];

    }
    
    /**
     * Update the exeat
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function update($params = null) {
        
        global $accessObject, $defaultUser;

        // check permission
        if(!$accessObject->hasAccess("update", "exeats")) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        foreach(['exeat_id'] as $key) {
            if(empty($params->{$key})) {
                return ["code" => 400, "data" => "Sorry! Provide a valid {$key}."];
            }
        }

        // get the permission
        $managePermission = $accessObject->hasAccess("manage", "exeats");
        $queryString = "";

        // if the user is not a manager
        if(!$managePermission) {
            $queryString .= " AND a.created_by = '{$params->userId}'";
        }

        // check if the exeat record exists
        $check = $this->pushQuery("id", "exeats", "item_id='{$params->exeat_id}' AND client_id='{$params->clientId}' AND status != 'Deleted' {$queryString} LIMIT 1");
        if(empty($check)) {
            return ["code" => 400, "data" => "Sorry! The exeat record does not exist."];
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

        // insert the user information
		$stmt = $this->db->prepare("
            UPDATE exeats SET last_updated = now()
                ".(!empty($params->student_id) ? ", student_id='{$params->student_id}'" : null)."
                ".(!empty($params->status) ? ", status='{$params->status}'" : null)."
                ".(!empty($params->exeat_type) ? ", exeat_type='{$params->exeat_type}'" : null)."
                ".(!empty($params->departure_date) ? ", departure_date='{$params->departure_date}'" : null)."
                ".(!empty($params->pickup_by) ? ", pickup_by='{$params->pickup_by}'" : null)."
                ".(!empty($params->guardian_contact) ? ", guardian_contact='{$params->guardian_contact}'" : null)."
                ".(!empty($params->reason) ? ", reason='{$params->reason}'" : null)."
                ".(!empty($params->return_date) ? ", return_date='{$params->return_date}'" : null)."
            WHERE item_id='{$params->exeat_id}' AND client_id='{$params->clientId}' LIMIT 1
        ");

        // execute the update user data
        $stmt->execute();

        // log the user activity
        $this->userLogs("exeats", $params->exeat_id, null, "{$params->userData->name} updated the Exeat record for the Student with ID::{$params->student_id}", $params->userId);

        return ["code" => 200, "data" => "Exeat record successfully updated.", "refresh" => 2000, "additional" => ["href" => "{$this->baseUrl}exeats_log"]];
    }
    
    /**
     * Delete the exeat
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function delete($params = null) {
        
        global $accessObject;

        // check permission
        if(!$accessObject->hasAccess("delete", "exeats")) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        // get the permission
        $managePermission = $accessObject->hasAccess("manage", "exeats");
        $queryString = "";

        // if the user is not a manager
        if(!$managePermission) {
            $queryString .= " AND a.created_by = '{$params->userId}'";
        }

        // check if the exeat record exists
        $check = $this->pushQuery("id", "exeats", "item_id='{$params->exeat_id}' AND client_id='{$params->clientId}' AND status != 'Deleted' {$queryString} LIMIT 1");
        if(empty($check)) {
            return ["code" => 400, "data" => "Sorry! The exeat record does not exist."];
        }

        // update the status to deleted
        $stmt = $this->db->prepare("UPDATE exeats SET status='Deleted', last_updated = now() WHERE item_id='{$params->exeat_id}' AND client_id='{$params->clientId}' LIMIT 1");
        $stmt->execute();

        // log the user activity
        $this->userLogs("exeats", $params->exeat_id, null, "{$params->userData->name} deleted the Exeat record for the Student with ID::{$params->student_id}", $params->userId);

        return [
            "code" => 200, 
            "data" => "Exeat record successfully deleted.", 
            "refresh" => 2000, 
            "additional" => [
                "clear" => true,
                "href" => "{$this->baseUrl}exeats_log"
            ]
        ];

    }

    /**
     * Update the exeat status
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function status($params = null) {
        
        global $accessObject;

        // check permission
        if(!$accessObject->hasAccess("manage", "exeats")) {
            return ["code" => 400, "data" => $this->permission_denied];
        }

        // check if the exeat record exists
        $check = $this->pushQuery("id", "exeats", "item_id='{$params->exeat_id}' AND client_id='{$params->clientId}' AND status != 'Deleted' LIMIT 1");
        if(empty($check)) {
            return ["code" => 400, "data" => "Sorry! The exeat record does not exist."];
        }

        // update the status to deleted
        $stmt = $this->db->prepare("UPDATE exeats SET status='{$params->status}', last_updated = now() WHERE item_id='{$params->exeat_id}' AND client_id='{$params->clientId}' LIMIT 1");
        $stmt->execute();
        
        return [
            "code" => 200, 
            "data" => "Exeat record successfully updated.", 
            "additional" => [
                "clear" => true,
                "refresh" => 2000, 
            ]
        ];

    }

}
?>