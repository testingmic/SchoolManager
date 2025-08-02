<?php

class Consultation extends Myschoolgh {

    public $consultationModel;

    public function __construct($params) {
        parent::__construct($params);
        $this->consultationModel = load_class("ConsultationModel", "models/hospital");
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