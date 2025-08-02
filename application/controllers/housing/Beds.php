<?php

class Beds extends Myschoolgh {

    public $bedsModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->bedsModel = load_class("BedsModel", "models/housing");
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