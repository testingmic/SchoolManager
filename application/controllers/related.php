<?php

class Related extends Myschoolgh {

	public function __construct($params = null) {
		parent::__construct();

		$this->iclient = $params->client_data ?? [];
	}

    /**
     * List the related items
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function list(stdClass $params) {

        /** Check if the module is required */
        if(empty($params->module)) {
            return ["code" => 400, "message" => "Module is required"];
        }

        // convert the module to lowercase
        $params->module = strtolower($params->module);
        
        /** Get the buses */
        if(in_array($params->module, ["bus"])) {
            $buses = $this->bus_list($params->clientId, "id, item_id, brand, reg_number, driver_id, description");
            return ["code" => 200, "data" => $buses];
        }

        /** Get the students, parents, and staff */
        if(in_array($params->module, ["student", "parent", "staff"])) {
            $columns = $params->module == "staff" ? "admin,employee,accountant" : $params->module;
            $columns = stringToArray($columns);
            $students = $this->pushQuery("id, item_id, unique_id, name", "users", "client_id = '{$params->clientId}' AND user_type IN {$this->inList($columns)} AND status = '1'", false, "ASSOC");
            return ["code" => 200, "data" => $students, "code" => 200];
        }

    }

}