<?php 

class Endpoints extends Myschoolgh {

    /** Variables */
    private $model;
    private $item_id;
    private $code = 500;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Since v1.0
     * 
     * This method lists all api endpoints
     * 
     * @param \stdClass $params
     * 
     * @return Object
     */
    public function list(stdClass $params = null) {

        // filters
        $params->query = "1";
        $params->query .= !empty($params->resource) ? " AND `resource`='".strtolower($params->resource)."'" : null;
        $params->query .= !empty($params->status) ? " AND `status`='".strtolower($params->status)."'" : null;
        $params->query .= !empty($params->version) ? " AND `version`='".strtolower($params->version)."'" : null;
        $params->query .= !empty($params->method) ? " AND `method`='".strtoupper($params->method)."'" : null;
        $params->query .= !empty($params->endpoint_id) ? " AND `item_id`='{$params->endpoint_id}'" : null;
        $params->limit = isset($params->limit) ? (int) $params->limit : 1000;

        $params->resource = isset($params->resource_only) ? " GROUP BY resource" : null;

        // make the request for the record from the model
        try {

            $stmt = $this->db->prepare("
                SELECT 
                    resource, endpoint, method, description, parameter, status,  
                    counter, date_created, last_updated, version, item_id, item_id AS endpoint_id,
                    deprecated, deleted
                FROM users_api_endpoints 
                WHERE {$params->query} {$params->resource} ORDER BY resource LIMIT {$params->limit}
            ");
            $stmt->execute();

            // group by method or resource
            $groupBy = isset($params->group) ? (in_array($params->group, ['method', 'resource']) ? 1 : (in_array($params->group, ['resource_method']) ? 2 : 0)) : 3;
            
            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                if($groupBy == 1) {
                    $data[$result->{$params->group}][] = $result;
                } elseif($groupBy == 2) {
                    $data[$result->resource][$result->method][] = $result;
                } else {
                    $data[$result->item_id] = $result;
                }
            }

            // return the data
			return [
				"data" => $data,
				"code" => !empty($data) ? 200 : 201
			];
        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }
    
    /**
     * Since v1.0
     * 
     * Add an endpoint
     * 
     * @param \stdClass $params
     * 
     * @return Object
     */
    public function add(stdClass $params) {

        // try the query
        try {

            $code = 500;

            // endpoint information update
            if(!isset($params->resource) || empty($params->resource)) {
                return ["code" => $code, "data" => "Sorry! The resource name cannot be empty."];
            }
            if(!isset($params->endpoint) || empty($params->endpoint)) {
                return ["code" => $code, "data" => "Sorry! The resource endpoint cannot be empty."];
            }
            if(!isset($params->method) || empty($params->method)) {
                return ["code" => $code, "data" => "Sorry! The request method cannot be empty (GET, POST, PUT or DELETE)."];
            }
            if(!isset($params->status) || empty($params->status)) {
                return ["code" => $code, "data" => "Sorry! The endpoint status cannot be empty (Active, Inactive, Dormant or Overloaded)."];
            }

            // confirm that a valid json content was parsed
            if(isset($params->parameter) && !empty($params->parameter)) {
                // convert to json
                $param = json_decode($params->parameter);
                // if the param is empty
                if(empty($param)) {
                   return ["code" => $code, "data" => "Please ensure a valid json data was parsed as parameter."];
                }
            }

            // convert the method to an uppercase
            $params->method = strtoupper($params->method);
            $params->resource = create_slug($params->resource, "_");

            // confirm the request method parsed
            if(!in_array($params->method, ["GET", "POST", "PUT", "DELETE"])) {
                return ["code" => $code, "data" => "Sorry! The request method must either be GET, POST, PUT or DELETE."];  
            }

            // clean the endpoint
            $params->resource = strtolower($params->resource);
            $params->endpoint = trim(strtolower($params->endpoint), "/");

            // convert the status to a lowercase
            $params->status = strtolower($params->status);

            // confirm that a valid status was parsed
            if(!in_array($params->status, ['overloaded','active','dormant','inactive'])) {
                return ["code" => $code, "data" => "Sorry! The request method must either be Active, Inactive, Dormant or Overloaded."];  
            }
            // check if there is no existing endpoint with same record
            $confirm = $this->pushQuery("id", "users_api_endpoints", "resource='{$params->resource}' AND endpoint='{$params->endpoint}' AND method='{$params->method}' AND deleted='0'");

            // save the endpoint
            if($confirm) {
                return ["code" => $code, "data" => "Sorry! There is an active existing record using the same endpoint."];  
            }

            // create a new item id of 32 alphanumeric characters
            $params->_item_id  = random_string("alnum", RANDOM_STRING);

            // insert the record
            $stmt = $this->db->prepare("
                INSERT INTO users_api_endpoints SET resource = ?, endpoint = ?, method = ?, description = ?,
                parameter = ?, status = ?, added_by = ?, item_id = ?
            ");
            // execute the prepared statement
            if($stmt->execute([$params->resource, $params->endpoint, $params->method, $params->description, $params->parameter, $params->status, $params->userId, $params->_item_id])) {
                $code = 200;
                $data = "The endpoint request was successfully executed.";
                // log the user activity
                $this->userLogs("endpoints", $params->endpoint_id, null, "<strong>".($params->userData->name ?? "System User")."</strong> added a new endpoint: <strong>{$params->endpoint}</strong> to the resource: <strong>{$params->resource}</strong>.", $params->userId);
            } else {
                $data = "Sorry! There was an error while processing the request.";
            }

            return [
                "code" => $code,
                "data" => $data,
                "record_id" => $params->_item_id
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Since v1.0
     * 
     * Update an endpoint
     * 
     * @param \stdClass $params
     * 
     * @return Object
     */
    public function update(stdClass $params = null) {
        
        try {

            /** Load the previous record */
            $params->prevData = $this->prevData("users_api_endpoints", $params->endpoint_id);

            // confirm that a valid json content was parsed
            if(isset($params->parameter) && !empty($params->parameter)) {
                // convert to json
                $param = json_decode($params->parameter);
                // if the param is empty
                if(empty($param)) {
                   return ["code" => 500, "data" => "Please ensure a valid json data was parsed as parameter."];
                }
            }

            // convert the method to an uppercase
            $params->resource = strtolower($params->resource);
            $params->endpoint = strtolower($params->endpoint);
            $params->method = isset($params->method) ? strtoupper($params->method) : null;
            
            $data = "";

            // prepare the the statement
            $stmt = $this->db->prepare("
                UPDATE users_api_endpoints SET last_updated=now(), updated_by = ?
                ".(isset($params->label) && ($params->label == "deprecate") ? ", deprecated='1'" : null)."
                ".(isset($params->label) && ($params->label == "restore") ? ", deprecated='0'" : null)."
                ".(isset($params->label) && ($params->label == "delete") ? ", deleted='1'" : null)."
                ".(isset($params->status) ? ", status='{$params->status}'" : null)."
                ".((isset($params->method) && !empty($params->method)) ? ", method='{$params->method}'" : null)."
                ".(isset($params->parameter) ? ", parameter='{$params->parameter}'" : null)."
                ".(isset($params->endpoint) ? ", endpoint='{$params->endpoint}'" : null)."
                ".(isset($params->resource) ? ", resource='{$params->resource}'" : null)."
                ".(isset($params->description) ? ", description='".addslashes($params->description)."'" : null)."
                WHERE item_id = ?
            ");

            // if the update was successful
            if($stmt->execute([$params->userId, $params->endpoint_id])) {
                $code = 200;
                $data = "Endpoint successfully updated.";
                // log the user activity
                $this->userLogs("endpoints", $params->endpoint_id, $params->prevData, "<strong>".($params->userData->name ?? "System User")."</strong> updated the endpoint.", $params->userId);
            }

            return [
                "code" => $code,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

    /**
     * Since v1.0
     * 
     * Update an Api Key
     * 
     * @param \stdClass $params
     * 
     * @return Object
     */
    public function api(stdClass $params) {

        try {

            global $isSupport, $isAdmin, $defaultUser;

            // if the user is neither an admin or support personnel
            if(!$isAdmin && !$isSupport) {
                return ["code" => 203, "data" => $this->permission_denied];
            }

            // if the action was submitted
            if(!isset($params->data["action"])) {
                return ["code" => 203, "data" => "Ensure all required parameters were parsed."];
            }

            // set the date
            if(in_array($params->data["action"], ["extend_date", "delete"])) {

                // if the request is to extend the date
                if(in_array($params->data["action"], ["extend_date"])) {

                    // if the expiry date or the api id was not parsed
                    if(!isset($params->data["expiry_date"]) || !isset($params->data["api_id"])) {
                        return ["code" => 203, "data" => "Ensure all required parameters were parsed."];                
                    }

                    // set the date
                    $date = $params->data["expiry_date"];
                }

                // set the id
                $api_id = $params->data["api_id"];

                // confirm the api key id
                if(empty($this->pushQuery("*", "users_api_keys", "id='{$api_id}' AND client_id='{$params->clientId}' LIMIT 1"))) {
                    // log the attempt to bypass the system security
                    $this->db->query("INSERT INTO security_logs SET client_id='{$params->clientId}', created_by='{$params->userId}', section='Update API Key', description='The user attempted to update an <strong>API Key Expiry Date</strong> which does not belong to the user.'
                    ");
                    // return a warning to the user.
                    return ["code" => 203, "data" => "Sorry! You attempted to update a non existent api key."];
                }
                
                // if the request is to update the date
                if(in_array($params->data["action"], ["extend_date"])) {
                    // confirm the validity of the date
                    if(!$this->validDate($params->data["expiry_date"])) {
                        return ["code" => 203, "data" => "Sorry! An invalid date was submitted."];   
                    }

                    // update the information
                    $this->db->query("UPDATE users_api_keys SET expiry_date='{$date}', expiry_timestamp='{$date} 11:59:00', access_type = 'permanent' WHERE id='{$api_id}' LIMIT 1");
                } 
                // delete the api key
                elseif(in_array($params->data["action"], ["delete"])) {
                    // update the information
                    $this->db->query("UPDATE users_api_keys SET expiry_date=now(), expiry_timestamp=now(), status='0'
                        WHERE id='{$api_id}' LIMIT 1");
                }

                // return success message
                return ["code" => 200, "data" => "Api Key expiry date successfully updated."];

            } elseif($params->data["action"] == "create") {

                // set the token
                $token = random_string("alnum", 32);

                // create the temporary token
                $this->db->query("INSERT INTO users_api_keys 
                    SET user_id = '{$params->userId}', username = '{$defaultUser->username}', 
                    access_token = '".password_hash($token, PASSWORD_DEFAULT)."', access_type = 'permanent', 
                    expiry_date = '".date("Y-m-d", strtotime("+6 month"))."', 
                    expiry_timestamp = '".date("Y-m-d H:i:s", strtotime("+6 month"))."', 
                    requests_limit = '5000', access_key = '{$token}', client_id = '{$params->clientId}'
                ");

                return ["code" => 200, "data" => "Api Key successfully created."];

            } else {
                return ["code" => 203, "data" => "Sorry! An unknown request was parsed."];
            }


        } catch(PDOException $e) {}

    }

}
?>