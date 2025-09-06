<?php
// ensure this file is being included by a parent file
if( !defined( 'BASEPATH' ) ) die( 'Restricted access' );

/**
 * This class validates the Api user and key parsed
 * either in the query parameter as access_token or within the authorization headers
 * 
 * When validated, it will return the results to the end user for verification and subsequent usage.
 */
class Api_validate {

	private $maximum;
	private $db;
	
	public function __construct() {
		global $myschoolgh;
		// this is the maximum number of rows to query
		$this->maximum = 200;
		$this->db = $myschoolgh;
	}

	/**
	 * Validate the api key
	 * 
	 * @param $_SERVER	optional if parsed by the user
	 *  
	 * @return Array
	 */
	public function validateApiKey($payload = []) {

		/** get the user full request headers **/
		$headers = apache_request_headers();
		$authHeader = null;

		// set the proceed to verify
		$proceedToVerify = false;

		// get teh redirect http authorization headers
		if(isset($_SERVER['HTTP_AUTHORIZATION']) && $_SERVER['HTTP_AUTHORIZATION'] !== "") {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

		/** authenticate the headers that have been parsed **/
		if((isset($headers["Authorization"]) && ($headers["Authorization"] == $authHeader)) || isset($payload['access_token'])) {
			
			/** Set the Authorization Code **/
			$accessToken = $payload['access_token'] ?? ($payload['token'] ?? ($headers['Authorization'] ?? null));
			$accessToken = xss_clean($accessToken);

			/** Split the Authorization Code **/
			$authorizationToken = base64_decode(str_ireplace("Bearer", "", $accessToken));
			$authorizationToken = trim($authorizationToken);

			$splitAuthInfo = explode(":", $authorizationToken);

			/** check if the authorization token was parsed **/
			if(!isset($splitAuthInfo[1])) {
			 	/** Inform the user that a wrong request was parsed **/
			 	return false;
			}
			
			/** Proceed to verify the token **/
			$proceedToVerify = true;
	
		}

		// if the proceed to verify is true
		if($proceedToVerify) {
			// verify credentials 
			$result = $this->verifyToken($splitAuthInfo[0], $splitAuthInfo[1]);
			/** Verify the Authorization Token **/
			if(!$result) {
				/** Inform the user that a wrong request was parsed **/
				return false;
			}
			/** Continue with the processing after verification **/
			return $result;		
		}
	}

	/**
	 * Validate the user access tokens
	 * 
	 * @param String $userName
	 * @param String $accessToken
	 * 
	 * @return Array
	 */
	private function verifyToken($username, $accessToken) {

		/** Return error if database connection fails **/
		try {
			$stmt = $this->db->prepare("
				SELECT 
					a.username, a.user_id, a.client_id,
					a.expiry_timestamp, a.access_token,
					a.requests_limit, a.permissions,
					(
						SELECT c.requests_count 
						FROM users_api_queries c
						WHERE DATE(request_date) = CURDATE() LIMIT 1
					) AS requests_count
				FROM users_api_keys a WHERE a.username = '{$username}' AND a.status = ? AND (TIMESTAMP(a.expiry_timestamp) >= CURRENT_TIMESTAMP()) LIMIT {$this->maximum}
			");
			$stmt->execute([1]);
			
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				// verify the access token that has been parsed
				if(password_verify($accessToken, $result->access_token)) {
					// convert the description into an array
					$result->permissions = !empty($result->permissions) ? json_decode($result->permissions) : (object)[];
					// unset the access token from the result
					unset($result->access_token);
					// return the result
					return $result;
				}
			}
			
		} catch(PDOException $e) {}

		return false;
	}

	/**
	 * Format the Parameters that have been submitted by the user
	 * 
	 * @param String $method 	This is the request method parsed in the header of the request
	 * @param Mixed	$json		The json variables parsed by the user
	 * @param Array $_POST		This can be any variables parsed in the request body
	 * @param Array $_GET		This can be any variables parsed in the query parameters
	 * 
	 * @return Object
	 */
	public function paramFormat($method, $json = [], $post = [], $get = [], $files = []) {
		$params = [];

		// Process JSON data
		$params = array_merge($params, $this->processJsonParams($json));

		// Process GET parameters
		$params = array_merge($params, $this->processGetParams($get));

		// Process POST parameters and files
		$postParams = $this->processPostParams($post, $files);

		// Merge the post parameters with the existing parameters
		$params = array_merge($params, $postParams);

		return (object) $params;
	}

	/**
	 * Process the json parameters
	 * 
	 * @param Mixed $json
	 * 
	 * @return Array
	 */
	private function processJsonParams($json) {
		if (empty($json) || !is_array($json)) {
			return [];
		}

		return $this->recursiveClean($json);
	}

	/**
	 * Process the get parameters
	 * 
	 * @param Array $get
	 * 
	 * @return Array
	 */
	private function processGetParams($get) {
		$params = [];
		foreach ($get as $key => $value) {
			if ($key !== "access_token") {
				$params[$key] = is_array($value) ? array_map("xss_clean", $value) : xss_clean($value);
			}
		}
		return $params;
	}

	/**
	 * Process the post parameters
	 * 
	 * @param Array $post
	 * @param Array $files
	 * 
	 * @return Array
	 */	
	private function processPostParams($post, $files) {
		$params = [];
		foreach ($post as $key => $value) {
			if ((!empty($value) && $key != "access_token") || $value === 0) {
				$params[$key] = $this->recursiveClean($value);
			}
		}

		foreach ($files as $key => $value) {
			if (!empty($value) && !empty($value["tmp_name"])) {
				$params[$key] = $value;
			}
		}
		return $params;
	}

	/**
	 * Recursively clean the data
	 * 
	 * @param Mixed $data
	 * 
	 * @return Mixed
	 */
	private function recursiveClean($data) {
		if (is_array($data)) {
			return array_map([$this, 'recursiveClean'], $data);
		}
		return xss_clean($data);
	}

	/**
	 * Load the endpoint information for processing
	 * 
	 * @param String $endpoint		The endpoint to load the information
	 * @param String $method		The request method
	 * 
	 * @return Array
	 */
	public function apiEndpoint($endpoint, $method, $resource = null) {
		
		try {

			/**
			 * The endpoint will be an associative array which where the switch will be done 
			 * based on the request type that the user has sent.
			 * This can be saved in a json file and requested from the server
			 * 
			 * Usage Example
			 * 
			 * Load the file and convert the content into an array 
			 *  
			 * $this->endpoints = json_decode(file_get_contents('assets/endpoints.json'), true)
			 */
			/** Split the endpoint */
			$expl = explode("/", $endpoint);
			$endpoint = isset($expl[1]) ? strtolower($endpoint) : null;

			// set the resource as a lower case
			$expl[0] = strtolower($expl[0]);

			// set the request_method
			$request_method = !empty($_GET["request_method"]) ? "AND method='{$_GET["request_method"]}'" : null;

			$method = $method == 'POST' ? ["POST", "PUT"] : $method;
			$method = stringToArray($method);

			// bypass all the errors for now and work on it later.
			return [];

			/** The request query */
			$stmt = $this->db->prepare("SELECT 
					endpoint, resource, method, description, parameter AS params, description 
				FROM users_api_endpoints 
				WHERE 1 ".(!empty($endpoint) ? " AND method IN ('".implode("', '", $method)."') AND endpoint='{$endpoint}' ORDER BY endpoint LIMIT 1 " : (
					!empty($expl[0]) ? " AND resource='{$expl[0]}' {$request_method} ORDER BY endpoint" : "ORDER BY endpoint")
				)."
			");
			$stmt->execute();
			$data = [];
			$count = $stmt->rowCount();
			$results = $stmt->fetchAll(PDO::FETCH_OBJ);
			
			/** Loop through the list */
			foreach($results as $result) {
				$result->params = json_decode($result->params, true);
				$result->method = strtoupper($result->method);

				if($count == 1) {
					$data = [
						$result->resource => [
							"{$result->method}" => [
								$resource => [
									"params" => $result->params
								]
							]
						]
					];
				} else {
					$data[$result->resource][] = [
						$result->endpoint => [
							"{$result->method}" => [
								"{$result->resource}" => [
									"params" => $result->params
								]
							]
						]
					];
				}
			}

			return $data;
		} catch(PDOException $e) {
			return [];
		}

	}

}
?>