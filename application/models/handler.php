<?php 

class Handler {

    private $outer_url;
    private $inner_url;
    private $requestMethod;
    private $params;
    private $session;
    private $userId;
    private $clientId;
    private $response;
    private $db;

    public $messages = array(
        200 => 'Ok',
        201 => 'Created',
        204 => 'No Response',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        419 => 'Page Expired',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    );
    
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
        $this->clientId = $this->session->clientId;

        $this->db = $myschoolgh;

        // get the list access token
        $this->response = (object) ["result" => "Sorry! Please ensure that all the required variables are not empty."];

        if(!empty($params[5])) {
            $this->userId = $params[5];
        }

        if(!empty($params[6])) {
            $this->clientId = $params[6];
        }
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
        
        global $defaultUser, $defaultAcademics, $baseUrl, $session;

        // set the default parameters
        $Api->default_params = $params;

        // revert the params back into an array
        $param = (object) $params;
        $param->remote = $remote;

        if(!empty($session->userdata)) {
            $Api->userId = !empty($Api->userId) ? $Api->userId : ($session->userdata['userId'] ?? $this->userId);
            $Api->clientId = !empty($Api->clientId) ? $Api->clientId : ($session->userdata['clientId'] ?? $this->clientId);
            $Api->defaultUser = !empty($Api->defaultUser) ? $Api->defaultUser : $session->userdata;
            $Api->appendClient = $session->userdata['defaultClientData'] ?? false;
        }

        // run the request
        $ApiRequest = $Api->requestHandler($param, $this->requestMethod);
        // remove access token if in
        if(isset($params->access_token)) {
            unset($params->access_token);
        }

        // set the request payload parsed
        $ApiRequest["data"]["remote_request"]["params"] = $Api->requestParams;

        // set the data to return
        $data = $ApiRequest;

        if(isset($params->raw_loading)) {
            $data = $ApiRequest["data"]["result"];
        }

        // set the notification engine
        $data['notification_engine'] = top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl, true);

        // set the response code
        if(isset($data['code']) && $param->remote) {
            header("HTTP/1.1 {$data['code']} {$this->messages[$data['code']]}");
        }

        // remove the method and endpoint
        if(isset($data['method'])) {
            unset($data['method']);
        }

        // remove the endpoint
        if(isset($data['endpoint'])) {
            unset($data['endpoint']);
        }
        
        if(is_array($data) && isset($data['raw_output'])) {
            $data = $data['raw_output'];
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

        global $defaultUser, $defaultAcademics, $baseUrl;
        
        $endpoint = "{$this->inner_url}/{$this->outer_url}";

        // create a sample path
        foreach(['auth', 'auth/logout', 'auth/login', 'devlog/transitioning', 'devlog/auth', 'auth/forgotten'] as $paths) {
            $samplePath[] = "api/{$paths}";
            $samplePath[] = "{$paths}";
        }

        $response = [];

        // remove the trailing slash from the endpoint
        $endpoint = rtrim($endpoint, "/");

        // control
        if(in_array($endpoint, $samplePath)) {

            // load the session class
            $session = load_class('Session', 'libraries/Session');

            // Auth object
            $logObj = load_class("auth", "controllers");

            // if the parameters were parsed
            if($this->requestMethod !== "POST") {
                $response["data"] = "Sorry! The method must be POST.";
            }
            // if the user is logging out
            elseif($this->outer_url == "logout") { 
                // append the user id and 
                $this->params->userId = $this->userId;
                // logout the user
                $response["data"] = $logObj->logout($this->params);
            }

            // if the user is logging in
            elseif(isset($this->params->username, $this->params->password) && !isset($this->params->firstname)) {
                // remote login
                $remote_login = (bool) isset($this->params->verify);

                // set the parameters as an object
                $parameters = (object)[
                    "username" => $this->params->username,
                    "password" => $this->params->password,
                    "remote" => $this->params->remote ?? $remote_login,
                    "rememberme" => $this->params->rememberme ?? false
                ];
                // Auth the user credentials
                $response["data"] = $logObj->login($parameters);
            }
            // if the user is requesting a password reset
            elseif(isset($this->params->recover)) {
                // request password reset
                $response["data"] = $logObj->send_password_reset_token($this->params);
            }
            // if the user is resetting their password
            elseif(isset($this->params->reset_token, $this->params->password, $this->params->password_2)) {
                // request password reset
                $response["data"] = $logObj->reset_user_password($this->params);
            }
            // if the user is creating a new portal account
            elseif(isset($this->params->portal_registration)) {
                // request password reset
                $response["data"] = $logObj->create($this->params);
            }
            // if the user is still logged in
            elseif(!empty($this->params->onlineCheck)) {
                $response["data"] = true;
            }
            elseif(isset($this->params->school_id, $this->params->school_code)) {

                // set the default response
                $response['data']['result'] = "Sorry! The school code is invalid.";
                $response['data']['code'] = 400;
                
                // validate the school code
                $validate = $logObj->validate_school_code($this->params);

                if(empty($validate)) {
                    $response['data']['result'] = "Sorry! The school code is invalid.";
                    $response['data']['code'] = $logObj->reqCode;
                } else {
                    $response['data']['result'] = $validate;
                    $response['data']['code'] = $logObj->reqCode;
                    $response['data']['proceed_signup'] = true;
                    if($logObj->refreshPage) {
                        $response['data']['refresh_page'] = $logObj->refreshPage;
                    }
                }
            }

            if(isset($response["data"]) && is_array($response["data"]) && isset($response["data"]["code"])) {
                $response["code"] = $response["data"]["code"];
                unset($response["data"]["code"]);
            }

            // set the notification engine
            $response["notification_engine"] = top_level_notification_engine($defaultUser, $defaultAcademics, $baseUrl, true);

            if(isset($response["data"]) && isset($response["code"])) {
                header("HTTP/1.1 {$response["code"]} {$this->messages[$response["code"]]}");
            }

            // print the error description
            die(json_encode($response));

        }

        if(($this->inner_url == "devlog") && ($this->outer_url == "transitioning")) {

            // set the default result
            $this->response->result = "Propagation";

            // // loop through the list
            if(!empty($this->session->clientId)) {

                // prepare and execute the statement
                $stmt = $this->db->prepare("
                    SELECT a.*, b.item_id AS default_account_id, b.balance AS default_account_balance
                    FROM clients_accounts a
                    LEFT JOIN accounts b ON b.client_id = a.client_id AND b.default_account = '1' AND b.status = '1'
                    WHERE a.client_id = ? LIMIT 1
                ");
                $stmt->execute([$this->session->clientId]);
                $result = $stmt->fetch(PDO::FETCH_OBJ);

                if(!empty($result)) {
                    if(in_array($result->client_state, ["Active", "Complete"])) {
                        $this->response->result = "Active";
                    }
                }
            }

            die(json_encode($this->response));
        }
        
    }

    /**
     * Check the parameters parsed by the user
     * 
     * @param {object} $response
     * @param {object} $params
     * @param {object} $defaultuser
     * @param {object} $apiAccessValues
     * @param {object} $myClass
     * @param {string} $requestUri
     * @param {bool} $remote
     * @param {object} $apisObject
     * @param {string} $endpoint
     * 
     * @return {array}
     */
    public function params_checker($response, $params, $defaultUser, $apiAccessValues, $requestUri, $remote, $apisObject, $endpoint) {

        global $session, $myClass;

        // initialize the bugs variable
        $bugs = false;
        $invalidToken = false;

        // check if the endpoint is not the auth endpoint
        if(!in_array($endpoint, ["api/auth", "qr/lookup", "qr/logs", "qr/save"])) {

            // confirm that the access token parameter was parsed but did not pass the test
            // confirm if a valid api access key was parsed
            if((!isset($apiAccessValues->user_id) && empty($session->userId)) || (isset($_GET['access_token']) && !isset($apiAccessValues->user_id))) {
                // set the bug good
                $bugs = true;
                // set the description
                $response->reason = "invalid_access_token";
                $response->status = "error";
                $response->data["result"] = "Sorry! An invalid Access Token was supplied or the Access Token has expired.";
                $invalidToken = true;
            } else {
                
                // if the user is making the request from an api endpoint
                if(isset($apiAccessValues->user_id)) {
                    
                    // initiate an empty array of the parameters parsed
                    $userId = $apiAccessValues->user_id;
                    $clientId = $apiAccessValues->client_id;

                    // set the remote access to true
                    $remote = true;
                    $params->remote = true;
                    
                    // convert the item into an integer
                    $dailyRequestLimit = (int) $apiAccessValues->requests_limit;
                    $totalRequests = (int) $apiAccessValues->requests_count;

                    // if the total request is greater or equal to the request limit
                    // then return false
                    if($totalRequests >= $dailyRequestLimit) {
                        $bugs = true;
                        // set the too many requests header
                        http_response_code(200);
                        // set the information to return to the user
                        $response->description = "Sorry! You have reached the maximum of {$dailyRequestLimit} requests that can be made daily.";
                    }

                    // the query parameter to load the user information
                    $i_params = (object) [
                        "limit" => 1, 
                        "user_id" => $userId, 
                        "minified" => "simplified", 
                        "append_wards" => true, 
                        "filter_preferences" => true, 
                        "userId" => $userId, 
                        "append_client" => true, 
                        "user_status" => $myClass->allowed_login_status
                    ];
                    $usersClass = load_class('users', 'controllers');
                    $defaultUser = $usersClass->list($i_params)["data"];

                    // get the first key
                    $defaultUser = $defaultUser[0] ?? [];
                }

                // set the userId
                $myClass->userId = !empty($session->userId) ? $session->userId : $userId;
                $myClass->clientId = !empty($session->clientId) ? $session->clientId : $clientId;

            }
        }

        // confirm that a bug was found
        if($bugs) {

            if(!$invalidToken) {
                // parse the remote request
                !empty($params) ? $response->data["remote_request"]["payload"] = $params : null;
            }

            if($invalidToken) {
                $response->data["must_login"] = true;
            }

            // print the error description
            echo json_encode($response);
            exit;
        }

        // set the user id and client id
        $this->userId = $defaultUser->user_id ?? ($defaultUser->userId ?? ($session->userdata['userId'] ?? null));
        $this->clientId = $defaultUser->client_id ?? ($defaultUser->clientId ?? ($session->userdata['clientId'] ?? $params->client_id));

        /* Usage of the Api Class */
        $Api = load_class('api', 'models', 
            ["userId" => $this->userId, "clientId" => $this->clientId, "defaultUser" => $defaultUser, "apiAccessValues" => $apiAccessValues]
        );

        /**
         * Test examples using the inner url of users
         */
        $Api->inner_url = $this->inner_url;
        $Api->outer_url = $this->outer_url;
        $Api->requestMethod = $this->requestMethod;
        $Api->uri = $requestUri;

        if($remote) {
            $Api->appendClient = true;
        }

        // save user image
        if($this->inner_url === 'save_image' && !empty($params->image) && !empty($params->user_id)) {
            // save the user image
            $upload = load_class('myschoolgh', 'models')->save_user_image($params);
            echo json_encode($upload);
            exit;
        }

        // set the full endpoint url
        $Api->endpoint_url = "{$this->inner_url}/{$this->outer_url}";

        /** Revert the params back into an array */
        $params = (array) $params;

        /** Load the parameters */
        $Api->endpoints = $apisObject->apiEndpoint($endpoint, $this->requestMethod, $this->outer_url);

        // set the default parameters
        $Api->default_params = $params;

        /* Run a check for the parameters and method parsed by the user */
        $paramChecker = $Api->keysChecker($params);

        $remote = isset($params["remote"]) ? (bool) $params["remote"] : $remote;

        return [
            'paramChecker' => $paramChecker,
            'remote' => $remote,
            'params' => $params,
            'Api' => $Api
        ];
    }

}
?>