<?php 

class Events extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return the list of event types
     * 
     * @param String    $params->type_id
     * @param String    $params->clientId
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        // columns to load
        $query = isset($params->type_id) && !empty($params->type_id) ? "AND a.item_id='{$params->type_id}'" : "";

        // make the request
        $events_types = $this->pushQuery("a.*", "events_types a", "a.client_id = '{$params->clientId}' AND a.status='0' {$query}");

        return $events_types;

    }

    /**
     * Save event type
     * 
     * Return the list of all event types after the query
     * 
     * @return Array
     */
    public function add_type(stdClass $params) {

        /** Push the record into the database */
        $item_id = random_string("alnum", 32);

        /** Insert */
        $stmt = $this->db->prepare("INSERT INTO events_types SET client_id = ?, item_id = ?, name = ?, description = ?, icon = ?");
        $stmt->execute([$params->clientId, $item_id, $params->name, $params->description ?? null, $params->icon ?? null]);

        /** Log the user activity */
        $this->userLogs("events_type", $item_id, null, "{$params->userData->name} created a new Event Type: {$params->name}", $params->userId);

        // return the response together with the types list
        return [
            "data" => "Event type was successfully created",
            "additional" => [
                "event_types" => $this->list($params)
            ]
        ];
    }

    /**
     * Update event type
     * 
     * Return the list of all event types after the query
     * 
     * @return Array
     */
    public function update_type(stdClass $params) {

        // old record
        $prevData = $this->pushQuery("*", "events_types", "item_id='{$params->type_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

        // if empty then return
        if(empty($prevData)) {
            return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
        }
        
        /** Push the record into the database */
        $item_id = $params->type_id;

        /** Insert */
        $stmt = $this->db->prepare("UPDATE events_types SET name = ?
            ".(isset($params->description) ? ",description = '{$params->description}'" : "")."
            ".(isset($params->icon) ? ",icon = '{$params->icon}'" : "")."
            WHERE client_id = ? AND item_id = ?
        ");
        $stmt->execute([$params->name, $params->clientId, $item_id]);

        /** Log the user activity */
        $this->userLogs("events_type", $item_id, $prevData[0], "{$params->userData->name} successfully updated the event type: {$params->name}", $params->userId);

        // unset the type id
        $params->type_id = null;

        // return the response together with the types list
        return [
            "data" => "Event type was successfully updated",
            "additional" => [
                "event_types" => $this->list($params)
            ]
        ];

    }
    
}