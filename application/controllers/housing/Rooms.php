<?php

class Rooms extends Myschoolgh {

    public $roomsModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->roomsModel = load_class("RoomsModel", "models/housing");
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