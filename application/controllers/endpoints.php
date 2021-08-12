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
        $params->query .= (isset($params->resource) && !empty($params->resource)) ? " AND `resource`='".strtolower($params->resource)."'" : null;
        $params->query .= (isset($params->status) && !empty($params->status)) ? " AND `status`='".strtolower($params->status)."'" : null;
        $params->query .= (isset($params->version) && !empty($params->version)) ? " AND `version`='".strtolower($params->version)."'" : null;
        $params->query .= (isset($params->method) && !empty($params->method)) ? " AND `method`='".strtoupper($params->method)."'" : null;
        $params->query .= (isset($params->endpoint_id) && !empty($params->endpoint_id)) ? " AND `item_id`='{$params->endpoint_id}'" : null;
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

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                $result->object_params = json_decode($result->parameter);
                $data[$result->item_id] = $result;
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
            $params->_item_id  = strtolower(random_string("alnum", 32));

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
                $this->userLogs("endpoints", $params->endpoint_id, null, "<strong>{$params->userData->name}</strong> added a new endpoint: <strong>{$params->endpoint}</strong> to the resource: <strong>{$params->resource}</strong>.", $params->userId);
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
                $this->userLogs("endpoints", $params->endpoint_id, $params->prevData, "<strong>{$params->userData->name}</strong> updated the endpoint.", $params->userId);
            }

            return [
                "code" => $code,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $e->getMessage();
        }

    }

}
?>