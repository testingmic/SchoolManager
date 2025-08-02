<?php

class Buildings extends Myschoolgh {

    public $buildingsModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->buildingsModel = load_class("BuildingsModel", "models/housing");
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