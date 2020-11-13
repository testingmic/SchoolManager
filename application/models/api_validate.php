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

	public function __construct() {
		global $myschoolgh;

		$this->db = $myschoolgh;
	}

	/**
	 * Validate the api key
	 * 
	 * @param $_SERVER	optional if parsed by the user
	 *  
	 * @return Array
	 */
	public function validateApiKey() {

		/** get the user full request headers **/
		$headers = apache_request_headers();
		$authHeader = null;

		// get teh redirect http authorization headers
		if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] != "") {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

		/** authenticate the headers that have been parsed **/
		if((isset($headers["Authorization"]) && ($headers["Authorization"] == $authHeader)) || isset($_GET['access_token'])) {
			
			/** Set the Authorization Code **/
			$accessToken = isset($headers['Authorization']) ? xss_clean($headers['Authorization']) : xss_clean($_GET['access_token']);

			/** Split the Authorization Code **/
			$authorizationToken = base64_decode(str_ireplace("Bearer", "", $accessToken));

			$splitAuthInfo = explode(":", $authorizationToken);
			$userName = xss_clean(
				substr( $authorizationToken, 0, strpos($authorizationToken, ":") )
			);

			/** check if the authorization token was parsed **/
			if(!isset($splitAuthInfo[1])) {
			 	/** Inform the user that a wrong request was parsed **/
			 	return false;
			}

			// verify credentials 
			$result = $this->verifyToken($splitAuthInfo[0], $splitAuthInfo[1]);

			/** Verify the Authorization Token **/
			if(!$result) {
				/** Inform the user that a wrong request was parsed **/
				return false;
			}

			/** Continue with the processing after verification **/
			return $result;			
			
		} else {
			/** Return error message **/
			return false;
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
						WHERE DATE(request_date) = CURDATE()
					) AS requests_count
				FROM users_api_keys a WHERE a.username = '{$username}' AND a.status = ? AND (TIMESTAMP(a.expiry_timestamp) >= CURRENT_TIMESTAMP()) LIMIT 100
			");
			$stmt->execute([1]);
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				// verify the access token that has been parsed
				if(password_verify($accessToken, $result->access_token)) {
					// convert the description into an array
					$result->permissions = json_decode($result->permissions);
					// unset the access token from the result
					unset($result->access_token);
					// return the result
					return $result;
				}
			}
			
		} catch(PDOException $e) {
			return $e->getMessage();
		}

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
		// initializing
		$params = [];
		
		// loop through each item and append to the params array
		// confirm that the incoming data is not empty
		if( !empty($json) ) {
			// loop through the list if its a valid array
			if( is_array($json) ) {

				// populate the user data using the request method parsed
				// however first of all loop through the data
				foreach( $json as $key => $value ) {

					// if the value is not an array in itself
					if( !is_array($value))  {

						// add to list if the value is not empty
						if(!empty($value)) {
							$params[$key] = xss_clean($value);
						}
					}
					// else if the value is an array then loop through the array
					elseif( is_array($value) ) {
						
						// perform the loop
						foreach( $value as $nkey => $nvalue ) {
							
							//: add the data to the array list
							if(!is_array($nvalue)) {
								
								// only add to list if the value is not empty
								if(!empty($nvalue)) {
									$params[$key][$nkey] = xss_clean($nvalue);
								}
							} else {

								// loop through the array values
								foreach($nvalue as $hhKey => $hhValue) {
								
									// only add to list if the value is not empty
									if(!empty($hhValue)) {
										$params[$key][$nkey][$hhKey] = array_map('xss_clean', $hhValue);
									}
								}
							}
							
						}

					}

				}
				
			}
		}

		// if the request is a get method
		else if( ($method == "GET") ) {
			// empty the parameters list
			$params = [];
			// run this section if the content is not empty
			if(!empty($get)) {
				// loop through the url items
				foreach($get as $key => $value) {
					// only parse if the value is not empty
					if( !empty($value) && ($key != "access_token") ) {
						// append the parameters
						$params[$key] = xss_clean($value);
					}
				}
			}
		}

		// if the request is a post method
		else if( ($method == "POST") ) {
			// empty the parameters list
			$params = [];
			
			// run this section if the content is not empty
			if(!empty($post)) {
				// loop through the url items
				foreach($post as $key => $value) {
					// only parse if the value is not empty
					if( !empty($value) && ($key != "access_token") ) {
						// append the parameters
						$params[$key] = (is_array($value)) ? $value : xss_clean($value);
					}
				}
			}
			
			// if files were parsed
			if(!empty($files)) {
				// append files to the parameters
				foreach($files as $key => $value) {
					// only parse if the value is not empty
					if( !empty($value) && !empty($value["tmp_name"]) ) {
						// append the parameters
						$params[$key] = $value;
					}
				}
			}
		}

		// conver the request parameters into an object
		return (object) $params;
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
			/** The request query */
			$stmt = $this->db->prepare("SELECT 
					resource, method, description, parameter AS params, description 
				FROM users_api_endpoints 
				WHERE method='{$method}' AND endpoint='{$endpoint}'
				LIMIT 1
			");
			$stmt->execute();

			// preset the data
			$data = [];

			/** Loop through the list */
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				$result->params = json_decode($result->params, true);
				$result->method = strtoupper($result->method);
				$data = [
					$result->resource => [
						"{$result->method}" => [
							"{$resource}" => [
								"params" => $result->params
							]
						]
					]
				];
			}

			return $data;
		} catch(PDOException $e) {
			return [];
		}
	}

}
?>