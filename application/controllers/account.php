<?php

class Account extends Myschoolgh {

	public function __construct(stdClass $params = null) {
		parent::__construct();
	}

    /**
     * Update Account Information
     * 
     * @return Array
     */
    public function update(stdClass $params) {

        // return error
        if(!isset($params->general["academics"]) || !isset($params->general["labels"])) {
            return ["code" => 203, "data" => "Sorry! Ensure academics and labels have been parsed."];
        }

        // academics and labels must be an array
        if(!is_array($params->general["academics"]) || !is_array($params->general["labels"])) {
            return ["code" => 203, "data" => "Sorry! Academics and Labels must be an array."];
        }

        // get the client data
        $client_data = $this->client_data($params->clientId);

        // confirm that a logo was parsed
        if(isset($params->logo)) {
            // set the upload directory
            $uploadDir = "assets/img/accounts/";

            if(!is_dir($uploadDir)) {
                mkdir($uploadDir);
            }

            // File path config 
            $file_name = basename($params->logo["name"]); 
            $targetFilePath = $uploadDir . $file_name; 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow certain file formats 
            $allowTypes = array('jpg', 'png', 'jpeg','gif');

            // check if its a valid image
            if(!empty($file_name) && in_array($fileType, $allowTypes)){
                // set a new file_name
                $image = $uploadDir . random_string('alnum', 32).".{$fileType}";
                // Upload file to the server 
                if(move_uploaded_file($params->logo["tmp_name"], $image)){}
            } else {
                return ["code" => 203, "Sorry! The event file must be a valid image."];
            }
        }

        // put the preferences together
        $preference["academics"] = $params->general["academics"];
        $preference["labels"] = $params->general["labels"];

        // unset the values
        unset($params->general["academics"]);
        unset($params->general["labels"]);

        // format
        $query = "";
        foreach($params->general as $key => $value) {
            $value = xss_clean($value);
            $query .= "client_{$key}='{$value}',";
        }

        if(empty($query)) {
            return ["code" => 203, "data" => "Sorry! Academics and Labels must be an array."];
        }

        try {

            // run the update of the account information
            $stmt = $this->db->prepare("UPDATE clients_accounts 
                SET {$query} client_preferences	= ? ".(isset($image) ? ", client_logo='{$image}'" : "")."
            WHERE client_id = ? LIMIT 1");
            $stmt->execute([json_encode($preference), $params->clientId]);

            // log the user activity
            // $this->userLogs("account", $params->clientId, $client_data, "{$params->userData->name} updated the Account Information", $params->userId);

            return [
                "data" => "Account information successfully updated."
            ];

        } catch(PDOException $e) {}

    }

}