<?php
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT");
header("Access-Control-Max-Age: 3600");

// global variables
global $myClass, $SITEURL, $usersClass;

// incoming inputs from the request
// and convert the request into an array using the PHP Standard Input
$incomingData = json_decode( file_get_contents("php://input"), true );

// get the request method that was parsed by the user
$method = strtoupper( $_SERVER["REQUEST_METHOD"] );

//: initializing
$response = (object) [
    "code" => 401,
    "description" => "Error Processing The Request",
    "method" => $method,
    "endpoint" => $_SERVER['REQUEST_URI'],
];

// get the request url for pattern matching and request payload matching
$requestUri = $_SERVER["REQUEST_URI"];

// The api url will go a maximum of 2 variables long
$inner_url = ( isset($SITEURL[1]) ) ? $SITEURL[1] : null;
$outer_url = ( isset($SITEURL[2]) ) ? $SITEURL[2] : null;

//: create a new object
$apisObject = load_class('api_validate', 'models');

// init the params variable
$bugs = false;
$remote = false;
$userId = $session->userId;
$clientId = $session->clientId;

// validate the user api keys parsed
$apiAccessValues = $apisObject->validateApiKey();

// get the parameters
$params = $apisObject->paramFormat($method, $incomingData, $_POST, $_GET, $_FILES);

// get the endpoints
$endpoint = "{$inner_url}/{$outer_url}/";
$endpoint = trim($endpoint, "/");

// control
if((($inner_url == "devlog") && ($outer_url == "auth")) || ($inner_url == "auth" && !$outer_url) || ($inner_url == "auth" && $outer_url == "logout")) {
    
    // get the list access token
    $response = (object) ["result" => "Sorry! Please ensure that all the required variables are not empty."];

    // Auth object
    $logObj = load_class("auth", "controllers");
    
    // if the parameters were parsed
    if($method !== "POST") {
        http_response_code(405);
        $response->result = "Sorry! The method must be POST.";
    } elseif($outer_url == "logout") { 
        // append the user id and 
        $params->userId = $userId;
        // logout the user
        $response->result = $logObj->logout($params);
    } elseif(isset($params->username, $params->password) && !isset($params->firstname)) {
        
        // remote login
        $remote_login = (bool) isset($params->verify);

        // set the parameters as an object
        $parameters = (object)[
            "username" => $params->username,
            "password" => $params->password,
            "remote" => $remote_login,
            "rememberme" => $params->rememberme ?? null
        ];
        // Auth the user credentials
        $response->result = $logObj->login($parameters);
    } elseif(isset($params->recover, $params->email)) {
        // request password reset
        $response->result = $logObj->send_password_reset_token($params);
    } elseif(isset($params->reset_token, $params->password, $params->password_2)) {
        // request password reset
        $response->result = $logObj->reset_user_password($params);
    } elseif(isset($params->portal_registration, $params->school_name, $params->school_address, $params->school_contact, $params->email)) {
        // request password reset
        $response->result = $logObj->create($params);
    }

    // print the error description
    echo json_encode($response);

    // Auth the user and regenerate an access token for use
    exit;
}

// default value
$skipProcessing = false;

/**
 * Process the Offline Payment Request
 * 
 * @param $params
 * 
 * @return JSON
 */
if(($inner_url == "payment") && (in_array($outer_url, ["pay", "verify"]))) {

    // end query if the client id was not parsed
    if(!isset($params->param["clientId"])) {
        // return error message
        $response->result = "The Client ID is required.";
        echo json_encode($response);
        exit;
    }

    /** Set the API Parameter */
    $api_param = [
        "clientId" => $params->param["clientId"]
    ];

    // append the user id if the user is logged in
    if(!empty($session->userId)) {
        $api_param["userId"] = $session->userId;
    }

    /* Usage of the Api Class */
    $Api = load_class('api', 'models', $api_param);

    /** Load the parameters */
    $Api->endpoints = $apisObject->apiEndpoint($endpoint, $method, $outer_url);
    $Api->inner_url = $inner_url;
    $Api->outer_url = $outer_url;
    $Api->appendClient = true;
    $Api->method = $method;
    $Api->uri = $requestUri;

    // set the default parameters
    $Api->default_params = $params;
    $params = (array) $params;

    /* Run a check for the parameters and method parsed by the user */
    $paramChecker = $Api->keysChecker($params);
    
    /** Skip processing */
    $skipProcessing = true;
}

/** If the value of $skipProcessing is TRUE */
if(!$skipProcessing) {

    // confirm that the access token parameter was parsed but did not pass the test
    // confirm if a valid api access key was parsed
    if((!isset($apiAccessValues->user_id) && empty($session->userId)) || (isset($_GET['access_token']) && !isset($apiAccessValues->user_id))) {
        // set the bug good
        $bugs = true;
        // set the http header
        http_response_code(401);
        // set the description
        $response->description = "Sorry! An invalid Access Token was supplied or the Access Token has expired.";
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
                http_response_code(429);
                // set the information to return to the user
                $response->description = "Sorry! You have reached the maximum of {$dailyRequestLimit} requests that can be made daily.";
            }
            
        }

        // set the userId
        $myClass->userId = !empty($session->userId) ? $session->userId : $userId;
        $myClass->clientId = !empty($session->clientId) ? $session->clientId : $clientId;
    }

    // confirm that a bug was found
    if($bugs) {

        // parse the remote request
        !empty($params) ? $response->remote_request['payload'] = $params : null;

        // print the error description
        echo json_encode($response);
        exit;
    }

    /* Usage of the Api Class */
    $Api = load_class('api', 'models', ["userId" => $userId, "clientId" => $clientId]);

    /**
     * Test examples using the inner url of users
     */
    $Api->inner_url = $inner_url;
    $Api->outer_url = $outer_url;
    $Api->method = $method;
    $Api->uri = $requestUri;

    /** Revert the params back into an array */
    $params = (array) $params;

    /** Load the parameters */
    $Api->endpoints = $apisObject->apiEndpoint($endpoint, $method, $outer_url);

    // set the default parameters
    $Api->default_params = $params;

    /* Run a check for the parameters and method parsed by the user */
    $paramChecker = $Api->keysChecker($params);

    $remote = isset($params["remote"]) ? (bool) $params["remote"] : $remote;

}

// in continuing your script then you can also do the following
// if an error was found
if( $paramChecker['code'] !== 100) {
    // set it if not existent
    $paramChecker['description'] = $paramChecker['description'] ?? null;

    // check the message to parse
    $paramChecker['data']['result'] = $paramChecker['data']['result'] ?? $paramChecker['description'];

    // unacceptable query made
    if($remote) {
        http_response_code($paramChecker['code']);
    }

    // print the json output
    echo json_encode($paramChecker);
} else {
    /** Set the default parameters */
    $Api->default_params = $params;

    /** Revert the params back into an array */
    $param = (object) $params;
    $param->remote = $remote;

    // run the request
    $ApiRequest = $Api->requestHandler($param, $method);

    /** Set the headers */
    if($remote) {
        http_response_code($ApiRequest["code"]);
    }
    
    // remove access token if in
    if(isset($params["access_token"])) {
        unset($params["access_token"]);
    }

    // set the request payload parsed
    $ApiRequest["data"]["remote_request"]["payload"] = $params;

    // print out the response
    echo json_encode($ApiRequest);
}
?>