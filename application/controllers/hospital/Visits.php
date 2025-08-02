<?php

class Visits extends Myschoolgh {

    public $visitsModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->visitsModel = load_class("VisitsModel", "models/hospital");
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