<?php
//: set the page header type
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// global variables
global $myClass, $SITEURL, $usersClass, $defaultuser, $myschoolgh;

// incoming inputs from the request
// and convert the request into an array using the PHP Standard Input
$incomingData = json_decode( file_get_contents("php://input"), true );

// get the request method that was parsed by the user
$requestMethod = strtoupper( $_SERVER["REQUEST_METHOD"] );

//: initializing
$response = (object) [
    "status" => "success",
    "code" => 200
];

// get the request url for pattern matching and request payload matching
$requestUri = $_SERVER["REQUEST_URI"];

// The api url will go a maximum of 2 variables long
$link_url = $SITEURL[0];
$inner_url = ( isset($SITEURL[1]) ) ? $SITEURL[1] : null;
$outer_url = ( isset($SITEURL[2]) ) ? $SITEURL[2] : null;

//: create a new object
$apisObject = load_class('api_validate', 'models');

// init the params variable
$remote = false;
$skipProcessing = false;
$userId = !empty($session->userId) ? $session->userId : null;
$clientId = !empty($session->clientId) ? $session->clientId : null;

// validate the user api keys parsed
$apiAccessValues = $apisObject->validateApiKey($_GET + $_POST);

// if the api access values are not empty
if(!empty($apiAccessValues)) {
    // set the user id
    $userId = empty($userId) ? $apiAccessValues->user_id : $userId;
    // set the client id
    $clientId = empty($clientId) ? $apiAccessValues->client_id : $clientId;
}

// get the parameters
$params = $apisObject->paramFormat($requestMethod, $incomingData, $_POST, $_GET, $_FILES);

// get the endpoints
$endpoint = "{$inner_url}/{$outer_url}/";
$endpoint = trim($endpoint, "/");

// move code to an initial handler
$handler = load_class('handler', 'models', [$outer_url, $inner_url, $requestMethod, $params, $session, $userId, $clientId]);
$handler->process();

/**
 * Initialize the API
 * 
 * @param $params
 * 
 * @return JSON
 */
if(($inner_url == "init")) {
    /* Usage of the Api Class */
    $handler->init();
}

/**
 * Process the Offline Payment Request
 * 
 * @param $params
 * 
 * @return JSON
 */
if(($inner_url == "payment") && (in_array($outer_url, ["pay", "verify", "epay_validate"]))) {

    // end query if the client id was not parsed
    if(!isset($params->param["clientId"])) {
        // return error message
        $response->result = "The Client ID is required.";
        echo json_encode($response);
        exit;
    }

    /** Set the API Parameter */
    $api_param = ["clientId" => $params->param["clientId"]];

    // append the user id if the user is logged in
    if(!empty($session->userId)) {
        $api_param["userId"] = $session->userId;
    }

    /* Usage of the Api Class */
    $Api = load_class('api', 'models', $api_param);

    /** Load the parameters */
    $Api->endpoints = $apisObject->apiEndpoint($endpoint, $requestMethod, $outer_url);
    $Api->inner_url = $inner_url;
    $Api->outer_url = $outer_url;

    // set the full endpoint url
    $Api->endpoint_url = "{$inner_url}/{$outer_url}";

    $Api->appendClient = true;
    $Api->requestMethod = $requestMethod;
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

    // get the params checker
    $result = $handler->params_checker($response, $params, $defaultuser, $apiAccessValues, $requestUri, $remote, $apisObject, $endpoint);

    // assign the values
    $Api = $result['Api'];
    $paramChecker = $result['paramChecker'];
    $params = $result['params'];
    $remote = $result['remote'];
    
}

// in continuing your script then you can also do the following
// if an error was found
if( !empty($paramChecker) && ($paramChecker['code'] !== 100)) {

    $output = $paramChecker;
    // check the message to parse
    if(is_array($paramChecker)) {
        $output['data'] = [];
        $output['data']['result'] = $paramChecker['data'] ?? 'Request Failed.';
    }

    // print the json output
    echo json_encode($output);
} else {
    // finalize the handler
    $handler->finalize($Api, $params, $remote);
}
?>