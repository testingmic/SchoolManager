<?php

class Sickbay extends Myschoolgh {

    public $iclient;
    public $visitsObject;
    public $vitalsObject;
    public $healthObject;
    public $medicalObject;
    public $inventoryObject;
    public $consultationObject;

    public function __construct($params = null) {
		parent::__construct();
        $this->iclient = $params->client_data ?? [];
	}

    /**
     * Vitals Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function vitalsHandler($params, $action = "list") {
        $this->vitalsObject = load_class("Vitals", "controllers/medical", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->vitalsObject, $method)) {
                return $this->vitalsObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Visits Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function visitsHandler($params, $action = "list") {
        $this->visitsObject = load_class("Visits", "controllers/hospital", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->visitsObject, $method)) {
                return $this->visitsObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Medical Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function medicalHandler($params, $action = "list") {
        $this->medicalObject = load_class("Medical", "controllers/medical", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->medicalObject, $method)) {
                return $this->medicalObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Consultation Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function consultationHandler($params, $action = "list") {
        $this->consultationObject = load_class("Consultation", "controllers/medical", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->consultationObject, $method)) {
                return $this->consultationObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Inventory Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function inventoryHandler($params, $action = "list") {
        $this->inventoryObject = load_class("Inventory", "controllers/medical", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->inventoryObject, $method)) {
                return $this->inventoryObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Health Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function healthHandler($params, $action = "list") {
        $this->healthObject = load_class("Health", "controllers/medical", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->healthObject, $method)) {
                return $this->healthObject->{$method}($params);
            }
        }
        return [];
    }
    
}
?>