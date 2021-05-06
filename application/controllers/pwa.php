<?php

class Pwa extends Myschoolgh {

    public function __construct(stdClass $params) 
    {
        parent::__construct();
    }

    /**
     * Load the basic information to input into the database
     * 
     * @return Array
     */
    public function idb(stdClass $params) {
        global $usersClass;

        $u_param = (object) [ 
            "minified" => "simplified", "no_permissions" => true,
            "academic_year" => $params->academic_year, "academic_term" => $params->academic_term
        ];
        $result["users_list"] = $usersClass->list($u_param);

        return [
            "data" => $result
        ];

    }

}