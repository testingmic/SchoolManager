<?php

class Health extends Myschoolgh {

    public $healthModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->healthModel = load_class("HealthModel", "models/hospital");
    }

    public function list() {

    }

    public function create() {
        
    }

    public function update() {
        
    }

    public function delete() {

    }
    
}
?>