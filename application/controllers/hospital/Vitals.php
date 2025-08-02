<?php

class Vitals extends Myschoolgh {

    public $vitalsModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->vitalsModel = load_class("VitalsModel", "models/hospital");
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