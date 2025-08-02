<?php

class Blocks extends Myschoolgh {

    public $blocksModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->blocksModel = load_class("BlocksModel", "models/housing");
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