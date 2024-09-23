<?php 

class Handler {

    private $outer_url;
    private $inner_url;
    private $requestMethod;
    private $params;
    private $session;
    private $userId;
    private $response;
    private $db;
    
    /**
     * Initialize the request
     * 
     * @param {array} $params
     * 
     * @return void
     */
    public function __construct($params = []) {

        global $myschoolgh;

        $this->outer_url = $params[0];
        $this->inner_url = $params[1];
        $this->requestMethod = $params[2];
        $this->params = $params[3];
        $this->session = $params[4];
        $this->userId = $this->session->userId;

        $this->db = $myschoolgh;

        // get the list access token
        $this->response = (object) ["result" => "Sorry! Please ensure that all the required variables are not empty."];
    }

    /**
     * Initialize the request
     * 
     * @return void
     */
    public function init() {
        
        // load the auth object
        $authObject = load_class("auth", "controllers");

        // execute the cron job
        $this->response->result = $authObject->execute_support_cron();

        // print the result
        die(json_encode($this->response));
    }

    /**
     * Finalize the request
     * 
     * @param {object} $Api
     * @param {object} $params
     * @param {bool} $remote
     * 
     * @return void
     */
    public function finalize($Api, $params, $remote) {
        
        // set the default parameters
        $Api->default_params = $params;

        // revert the params back into an array
        $param = (object) $params;
        $param->remote = $remote;

        // run the request
        $ApiRequest = $Api->requestHandler($param, $this->requestMethod);

        // remove access token if in
        if(isset($params->access_token)) {
            unset($params->access_token);
        }

        // set the request payload parsed
        $ApiRequest["data"]["remote_request"]["payload"] = $params;
        $ApiRequest["data"]["remote_request"]["params"] = $Api->requestParams;

        // set the data to return
        $data = $ApiRequest;

        if(isset($params->raw_loading)) {
            $data = $ApiRequest["data"]["result"];
        }
        
        // print out the response
        echo json_encode($data);
    }
    
    /**
     * Process the request
     * 
     * @return void
     */
    public function process() {

        // control
        if((($this->inner_url == "devlog") && ($this->outer_url == "auth")) || ($this->inner_url == "auth" && !$this->outer_url) || ($this->inner_url == "auth" && $this->outer_url == "logout")) {

            // Auth object
            $logObj = load_class("auth", "controllers");
            
            // if the parameters were parsed
            if($this->requestMethod !== "POST") {
                $this->response->result = "Sorry! The method must be POST.";
            }
            // if the user is logging out
            elseif($this->outer_url == "logout") { 
                // append the user id and 
                $this->params->userId = $this->userId;
                // logout the user
                $this->response->result = $logObj->logout($this->params);
            }

            // if the user is logging in
            elseif(isset($this->params->username, $this->params->password) && !isset($this->params->firstname)) {
                // remote login
                $remote_login = (bool) isset($this->params->verify);

                // set the parameters as an object
                $parameters = (object)[
                    "username" => $this->params->username,
                    "password" => $this->params->password,
                    "remote" => $remote_login,
                    "rememberme" => $this->params->rememberme ?? null
                ];
                // Auth the user credentials
                $this->response->result = $logObj->login($parameters);
            }
            // if the user is requesting a password reset
            elseif(isset($this->params->recover, $this->params->email)) {
                // request password reset
                $this->response->result = $logObj->send_password_reset_token($this->params);
            }
            // if the user is resetting their password
            elseif(isset($this->params->reset_token, $this->params->password, $this->params->password_2)) {
                // request password reset
                $this->response->result = $logObj->reset_user_password($this->params);
            }
            // if the user is creating a new portal account
            elseif(isset($this->params->portal_registration, $this->params->school_name, $this->params->school_address, $this->params->school_contact, $this->params->email)) {
                // request password reset
                $this->response->result = $logObj->create($this->params);
            }
            // if the user is still logged in
            elseif(!empty($this->params->onlineCheck)) {
                $this->response->result = "You are still successfully logged in.";
            }

            // print the error description
            die(json_encode($this->response));

        }

    }

}
?>