<?php

class Housing extends Myschoolgh {

    public $iclient;
    public $buildingsObject;
    public $blocksObject;
    public $roomsObject;
    public $bedsObject;

    public function __construct($params = null) {
		parent::__construct();
        $this->iclient = $params->client_data ?? [];
	}

    /**
     * Buildings Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function buildingsHandler($params, $action = "list") {
        $this->buildingsObject = load_class("Buildings", "controllers/housing", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->buildingsObject, $method)) {
                return $this->buildingsObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Blocks Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function blocksHandler($params, $action = "list") {
        $this->blocksObject = load_class("Blocks", "controllers/housing", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->blocksObject, $method)) {
                return $this->blocksObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Rooms Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function roomsHandler($params, $action = "list") {
        $this->roomsObject = load_class("Rooms", "controllers/housing", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->roomsObject, $method)) {
                return $this->roomsObject->{$method}($params);
            }
        }
        return [];
    }

    /**
     * Beds Handler
     * 
     * @param stdClass $params
     * @param String $action
     * 
     * @return Array
     */
    public function bedsHandler($params, $action = "list") {
        $this->bedsObject = load_class("Beds", "controllers/housing", $params);
        foreach(['list', 'create', 'update', 'delete'] as $method) {
            if($method !== $action) continue;
            if(method_exists($this->bedsObject, $method)) {
                return $this->bedsObject->{$method}($params);
            }
        }
        return [];
    }
    
}
?>