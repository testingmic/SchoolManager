<?php

class Inventory extends Myschoolgh {

    public $inventoryModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->inventoryModel = load_class("InventoryModel", "models/hospital");
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