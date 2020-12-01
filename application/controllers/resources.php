<?php 

class Resources extends Myschoolgh {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Upload a new resource
     * 
     * This can either be a link or a file
     * 
     * @param StdClass $params
     * 
     * @return Array
     */
    public function upload_4courses(stdClass $params) {

        // convert the parameter into an object
        $lesson_ids = "[\"NULL\"]";
        $upload = (object) $params->upload;

        // end query if no course_id was parsed
        if(!isset($upload->course_id)) {
            return ["code" => 203, "data" => "Sorry! Course ID is required."];
        }

        // confirm if a valid course id was submitted
        $prevData = $this->pushQuery("*", "courses", "item_id='{$upload->course_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");

        // if empty then return
        if(empty($prevData)) {
            return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
        }

        // create an item id
        $item_id = random_string("alnum", 32);

        // clean the lesson id
        if(isset($upload->lesson_id) && is_array($upload->lesson_id)) {
            $lesson_ids = json_encode($upload->lesson_id);
        }

        try {

            // if the request is to upload a link
            if(isset($upload->upload_type) && $upload->upload_type == "is_link") {
                // insert the link record
                $stmt = $this->db->prepare("
                    INSERT INTO courses_resource_links 
                    SET item_id = ?, client_id = ?, course_id = ?, lesson_id = ?,
                    description = ?, link_url = ?, link_name = ?, created_by = ?
                ");
                $stmt->execute([
                    $item_id, $params->clientId, $upload->course_id, $lesson_ids, 
                    $upload->description, $upload->link_url, $upload->link_name, $params->userId
                ]);    
                # set the output to return when successful
                $return = ["code" => 200, "data" => "Resource Link successfully uploaded.", "refresh" => 2000];
            }
            // if a file was uploaded
            elseif(isset($upload->upload_type) && $upload->upload_type == "is_file") {
                // upload the file
                $root = "assets/uploads/{$params->userId}";
                $dir = "{$root}/docs";
                $uploadDir = "{$dir}/resources/";
                
                // loop through the directory list
                foreach([$root, $dir, $uploadDir] as $each) {
                    // create the directory if not existent
                    if(!is_dir($each)) {
                        mkdir($each);
                    }
                }

                // confirm that the file is not empty
                if(isset($params->the_file)) {
                    // File path config 
                    $fileName = basename($params->the_file["name"]); 
                    $targetFilePath = $uploadDir . $fileName; 
                    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                    
                    // Allow certain file formats 
                    $allowTypes = $this->accepted_attachment_file_types;

                    // check if its a valid image
                    if(!empty($fileName) && in_array($fileType, $allowTypes)){
                        // set a new filename
                        $fileName = $uploadDir . random_string('alnum', 10)."__{$fileName}";
                        // Upload file to the server 
                        if(move_uploaded_file($params->the_file["tmp_name"], $fileName)){}
                    } else {
                        return ["code" => 203, "data" => "Sorry! An invalid file type was uploaded"];      
                    }
                } else {
                   return ["code" => 203, "data" => "Sorry! The file cannot be empty"]; 
                }

                // upload the file
                // insert the link record
                $stmt = $this->db->prepare("
                    INSERT INTO courses_resource_links 
                    SET item_id = ?, client_id = ?, course_id = ?, lesson_id = ?,
                    description = ?, link_url = ?, link_name = ?, created_by = ?, resource_type = ?
                ");
                $stmt->execute([
                    $item_id, $params->clientId, $upload->course_id, $lesson_ids, 
                    $upload->description, $fileName, $upload->file_name, $params->userId, "file"
                ]);    
                # set the output to return when successful
                $return = ["code" => 200, "data" => "Resource File successfully uploaded.", "refresh" => 2000];
            }

			# append to the response
			$return["additional"] = ["clear" => true];
            
            return $return;

        } catch(PDOException $e) {}

    }
}
?>