<?php 

class Communication extends Myschoolgh {

    private $iclient;

	public function __construct(stdClass $params = null) {
		parent::__construct();
	}

    /**
     * List Templates
     * 
     * @return Array
     */
    public function list_templates(stdClass $params) {

        $params->query = "1";

        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        $params->query .= (isset($params->q) && !empty($params->q)) ? " AND a.name LIKE '%{$params->q}%'" : null;
        $params->query .= (isset($params->type) && !empty($params->type)) ? " AND a.type='{$params->type}'" : null;
        $params->query .= (isset($params->clientId)) ? " AND a.client_id='{$params->clientId}'" : null;
        $params->query .= (isset($params->template_id) && !empty($params->template_id)) ? " AND a.item_id='{$params->template_id}'" : null;

        try {

            $stmt = $this->db->prepare("
                SELECT a.*,
                    (SELECT CONCAT(b.name,'|',b.phone_number,'|',b.email,'|',b.image,'|',b.user_type) FROM users b WHERE b.item_id = a.created_by LIMIT 1) AS createdby_info
                FROM smsemail_templates a
                WHERE {$params->query} AND a.status = ? ORDER BY a.id LIMIT {$params->limit}
            ");
            $stmt->execute([1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // clean the text
                $result->message = htmlspecialchars_decode($result->message);
                $result->raw_message = htmlspecialchars($result->message);

                // loop through the information
                foreach(["createdby_info"] as $each) {
                    // convert the created by string into an object
                    $result->{$each} = (object) $this->stringToArray($result->{$each}, "|", ["name", "phone_number", "email", "image","user_type"]);
                }

                $data[] = $result;
            }

            return [
                "code" => 200,
                "data" => $data
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

    /**
     * Add a template
     * 
     * @param String $params->name
     * @param String $params->type
     * @param String $params->message
     * 
     * @return Array
     */
    public function add_template(stdClass $params) {

        try {
            
            // create a new item id
            $item_id = random_string("alnum", 15);

            // clean the template
            $params->message = custom_clean(htmlspecialchars_decode($params->message));
            $params->message = htmlspecialchars($params->message);

            // prepare and execute the statement
            $stmt = $this->db->prepare("INSERT INTO smsemail_templates SET 
                item_id = ?, name = ?, message = ?, type = ?, client_id = ?, created_by = ?,
                academic_year = ?, academic_term = ?    
            ");
            $stmt->execute([$item_id, $params->name, $params->message, $params->type, 
                $params->clientId, $params->userId, $params->academic_year, $params->academic_term
            ]);

            // log the user activity
            $this->userLogs("smsemail_template", $item_id, null, "{$params->userData->name} added a {$params->type} template", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Template was successfully added.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}{$params->type}_template"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 
    }

    /**
     * Update a template
     * 
     * @param String $params->name
     * @param String $params->message
     * @param String $params->template_id
     * 
     * @return Array
     */
    public function update_template(stdClass $params) {

        try {

            // old record
            $prevData = $this->pushQuery("*", "smsemail_templates", "item_id='{$params->template_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
            
            // if empty then return
            if(empty($prevData)) { return ["code" => 203, "data" => "Sorry! An invalid id was supplied."]; }

            // clean the template
            $params->message = custom_clean(htmlspecialchars_decode($params->message));
            $params->message = htmlspecialchars($params->message);
            
            // prepare and execute the statement
            $stmt = $this->db->prepare("UPDATE smsemail_templates SET name = ?, message = ? WHERE item_id = ? AND client_id = ? LIMIT 1");
            $stmt->execute([$params->name, $params->message, $params->template_id, $params->clientId]);

            // log the user activity
            $this->userLogs("smsemail_template", $params->template_id, $prevData[0], "{$params->userData->name} updated the template details.", $params->userId);

            // return the success response
            return [
                "code" => 200, 
                "data" => "Template was successfully updated.", 
                "additional" => [
                    "href" => "{$this->baseUrl}{$params->type}_template"
                ]
            ];

        } catch(PDOException $e) {
            return $this->unexpected_error;
        } 

    }

}