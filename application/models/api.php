<?php
/**
 * Use a class check if any of the keys parsed is not valid
 * This will help prevent the case where users parse an invalid data as part of their
 * payload to the server.
 */
class Api {

    /* Leave it open to allow the user to preset it */
    public $inner_url;
    public $outer_url;

    /* Allow the user to preset the the userId after instantiating the class */
    public $userId;
    

    /* method will contain the request method parsed by the user */
    public $method;
    public $uri;
    public $default_params;

    /* the endpoint variable is only accessible in the class */
    public $endpoints = [];
    private $userData;

    private $requestPayload;

    const PERMISSION_DENIED = "Sorry! You do not have the required permissions to perform this action.";

    /**
     * Preset these variables when the class is initiated
     * 
     * @param {array} $param   This will hold an array of the user brand and client ids
     */
    public function __construct(array $param) {
        /**
         * global variables
         **/
        global $session, $accessObject, $config, $usersClass, $myClass, $myschoolgh;

        $this->session = $session;
        $this->config = $config;
        $this->myschoolgh = $myschoolgh;
        
        $this->userId = $param["userId"];
        $this->clientId = $param["clientId"];

        // the query parameter to load the user information
        $i_params = (object) [
            "limit" => 1,
            "user_id" => $this->userId,
            "clientId" => $this->clientId
        ];

        // load the user data
        $the_data = $usersClass->list($i_params);
        $this->userData = isset($the_data["data"]) ? $the_data["data"] : null;
        
        // if the user data is not empty
        if(!empty($this->userData)) {
            // get the first key
            $this->userData =  $this->userData[0];
            // set the access object parameters
            $this->accessCheck = $accessObject;
            $this->accessCheck->userId = $param["userId"];
            $this->accessCheck->clientId = $param["clientId"];
            $this->accessCheck->userPermits = $this->userData->user_permissions;
        }
        // myschoolgh class initializing
        $this->myClass = $myClass;

        // set the global variables
        $this->myClass->userId = $this->userId;
    }

    /**
     * This method checks the params parsed by the user
     *  @param {array} $params  This is the array of parameters sent by the user
    */
    public function keysChecker(array $params) {
        
        /**
         * check if there is a valid request method in the endpoints
         * 
         * Return an error / success message with a specific code
         */
        if( !isset($this->endpoints[$this->inner_url]) ) {
            // remove the key from the list
            unset($this->endpoints["devlog"]);
            // return the error response
            return $this->output(404, ['accepted' => ["endpoints" => $this->endpoints ] ]);
        }
        elseif( !isset( $this->endpoints[$this->inner_url][$this->method] ) ) {
            return $this->output(400, ['accepted' => ["method" => $this->endpoints[$this->inner_url] ] ]);
        }
        // continue process
        elseif(!isset($this->endpoints[$this->inner_url][$this->method][$this->outer_url])) {
            return $this->output(404, ['accepted' => ["endpoints" => $this->endpoints[$this->inner_url][$this->method]] ]);
        } else {
            // set the acceptable parameters
            $accepted =  $this->endpoints[$this->inner_url][$this->method][$this->outer_url];

            // confirm that the parameters parsed is not more than the accpetable ones
            if( !isset($accepted['params']) ) {
                // return all tests parsed
                return $this->output(100);
            } 
            else {
                
                // get the keys of all the acceptable parameters
                $endpointKeys = array_keys($accepted['params']);
                $errorFound = false;
                
                // confirm that the supplied parameters are within the list of expected parameters
                foreach($params as $key => $value) {
                    if(!in_array($key,  ["the_button", "faketext", "faketext_2", "remote", "message"]) && !in_array($key, $endpointKeys)) {
                        // set the error variable to true
                        $errorFound = true;                        
                        // break the loop
                        break;
                    }
                }
                // if an invalid parameter was parsed
                if($errorFound) {
                    return $this->output(405, ['accepted' => ["parameters" => $accepted['params'] ]]);
                } else {

                    /**
                     * Check if all the required parameters was parsed
                     * This section is necessary considering the following example
                     * 
                     * user parsed - 
                     * $params["firstname"] = Emmanuel Obeng,
                     * $params["age"] = 28
                     * 
                     * This will pass the first test because the count is 2 as compared to the acceptable of 3
                     * Likewise all the keys are within the the set of {firstname, lastname and age}
                     * 
                     * However the lastname is required but was not parsed by the user. So we need to verify it.
                     *
                    */ 
                    /* Set the required into an empty array list */
                    $required = [];
                    $required_text = [];

                    // loop through the accepted parameters and check which one has the description 
                    // required and append to the list
                    foreach($accepted['params'] as $key => $value) {
                        
                        // evaluates to true
                        if( strpos($value, "required") !== false) {
                            $required[] = $key;
                            $required_text[] = $key . ": " . str_replace(["required", "-"], "", $value);
                        }
                    }

                    /**
                     * Confirm the count using an array_intersect
                     * What is happening
                     * 
                     * Get the keys of the parsed parameters
                     * count the number of times the required keys appeared in it
                     * 
                     * compare to the count of the required keys if it matches.
                     * 
                     */
                    $confirm = (count(array_intersect($required, array_keys($params))) == count($required));

                    // If it does not evaluate to true
                    if(!$confirm) {
                        return $this->output(401, ['required' => $required_text]);
                    } else {
                        // return all tests parsed
                        return $this->output(100);
                    }

                }
            }
        }

    }

    /**
     * This handles all requests by redirecting it to the appropriate
     * Controller class for that particular endpoint request
     * 
     * @param stdClass $params         - This the array of parameters that the user parsed in the request
     * 
     * @return  
     */
    final function requestHandler(stdClass $params) {
        // preset the response
        $result = [];
        $code = 203;

        $this->requestPayload = $params;
        
        // get the client data
        $client_data = $this->myClass->client_data($this->clientId);
        
        // reassign the variable data
        $academics = $client_data->client_preferences->academics;
        
        // set the academic year and term
        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $academics->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $academics->academic_year;

        // set additional parameters
        $params->userId = $this->userId;
        $params->clientId = $this->clientId;
        $params->userData = (object) $this->userData;

        // set the default limit to 1000
        $params->limit = isset($params->limit) ? (int) $params->limit : $this->myClass->global_limit;

        // developer access permission check
        $params->devAccess = $this->accessCheck->hasAccess('developer', 'control') ? true : false;
        
        // if the client id is empty and yet the user is not selecting which account to manage
        if(empty($this->userId) && (($this->outer_url !== "select" && $this->inner_url !== "account"))) {
            return $this->output($code, $result);
        }

        // set the default object to parse when instantiating a class
        $default = (object) ["clientId" => $this->clientId, "default_User_Id" => $this->userId, "client_data" => $client_data];
        
        // create a new class for handling the resource
        $classObject = load_class("{$this->inner_url}", "controllers", $default);
        
        // confirm that there is a method to process the resource endpoint
        if(!empty($this->clientId) && method_exists($classObject, $this->outer_url)) {

            // set the method to load
            $method = $this->outer_url;
            
            // convert the response into an arry if not already in there
            $request = $classObject->$method($params);
            
            // set the response code to return
            $code = is_array($request) && isset($request['code']) ? $request['code'] : 200;
            
            // set the result
            $result['result'] =  is_array($request) && isset($request["data"]) ? $request["data"] : $request;
            
            // if additional parameter was parsed
            if(is_array($request) && isset($request['additional'])) {
                // set the additional parameter
                $result['additional'] = $request["additional"];
            }
            
        }

        // parse the code to return
        $code = !empty($code) ? $code : 201;

        // log the user request
        $this->update_onlineStatus($this->userId);
        // $this->logRequest($this->default_params, $code);

        // output the results
        return $this->output($code, $result);
    }

    /**
     * Update the user online status
     * 
     * @param String $userId
     * 
     * @return Bool
     */
    private function update_onlineStatus($userId) {
        
        try {

            /** prepare and execute the statement */
            $stmt = $this->myschoolgh->prepare("UPDATE users SET online='1', last_seen = now() WHERE item_id = ? LIMIT 1");
            return $stmt->execute([$userId]);

        } catch(PDOException $e) {}
    }
    
    /**
     * Log the request made by the user
     * 
     * @param Array $default_params     The parameters that was parsed in the request
     * @param String $code                  This is the response code
     * 
     * @return Bool
     */
    private function logRequest(array $default_params, $code = null) {
		
		try {

			// check if a request has been made today and then increment
			if($this->todayRequestCheck()) {
				// update the request count
				$this->myschoolgh->query("
					UPDATE users_api_queries SET requests_count = (requests_count+1) WHERE  DATE(request_date) = CURDATE()
				");
			} else {
				// insert a new query count
				$this->myschoolgh->query("
					INSERT INTO users_api_queries SET requests_count = '1', request_date = now()
				");
			}

			// log the request parsed by the user
			$stmt = $this->myschoolgh->prepare("
				INSERT INTO users_api_requests SET  user_id = ?,
				request_uri = ?, user_ipaddress = ?, user_agent = ?, response_code = ?,
				request_payload = ?, request_method = ?
			");
			$stmt->execute([
				$this->userId, $this->uri, ip_address(), 
				"{$this->myClass->platform} {$this->myClass->browser}",
				$code, json_encode($default_params), $this->method
			]);

			return true;
		} catch(PDOException $e) {
			return $e->getMessage();
		}

	}
    
    /**
     * Confirm that the request has been made to the endpoint
     * 
     * @return Bool
     */
	private function todayRequestCheck() {

		try {

			$stmt = $this->myschoolgh->prepare("
				SELECT COUNT(*) AS row_count FROM users_api_queries
				WHERE DATE(request_date) = CURDATE()
			");
			$stmt->execute();

			return (!empty($stmt->fetch(PDO::FETCH_OBJ)->row_count)) ? true : false; 

		} catch(PDOException $e) {
			return false;
		}
	}

    /**
     * Outputs to the screen
     * 
     * @param {int}             $code   This is the code after processing the user request
     * @param {string/array}    $data   Any addition data to parse to the user
     */
    private function output($code, $message = null) {
        // format the data to return
        $data = [
            'code' => $code,
            'description' => $this->outputMessage($code),
            'method' => $this->method,
            'endpoint' => $_SERVER["REQUEST_URI"]
        ];
        // remove the description endpoint if the response is 200
        if($code == 200) {
            unset($data['description']);
        }
        ( !empty($message) ) ? ($data['data'] = $message) : null;

        return $data;
    }

    /**
     * This is the output message based on the code
     * 
     * @param Int $code
     * 
     * @return String
     */
    private function outputMessage($code) {

        $description = [
            200 => 'The request was successfully executed and returned some results.',
            201 => 'The request was successful however, no results was found.',
            205 => 'The record was successfully updated.',
            202 => 'The data was successfully inserted into the database.',
            203 => 'Sorry! An error was encountered while processing the request.',
            400 => 'Invalid request method parsed.',
            401 => 'Sorry! Please ensure all required fields are not empty.',
            404 => 'Invalid request node parsed.',
            405 => 'Invalid parameters was parsed to the endpoint.',
            100 => 'All tests parsed',
            501 => "Sorry! You do not have the required permissions to perform this action.",
            600 => "Sorry! Your current subscription does not grant you permission to perform this action.",
            700 => "Unknown request parsed",
            999 => "An error occurred please try again later"
        ];
        
        return $description[$code] ?? $description[700];
    }

}
?>