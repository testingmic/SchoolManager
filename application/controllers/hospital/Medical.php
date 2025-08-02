<?php

class Medical extends Myschoolgh {

    public $medicalModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->medicalModel = load_class("MedicalModel", "models/hospital");
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