<?php 

class Resources extends Myschoolgh {

    private $iclient = [];

    public function __construct(stdClass $params = null) {
		parent::__construct();

        // global variable
        global $defaultClientData;

        // get the client data
        $client_data = $params->client_data ?? $defaultClientData;
        $this->iclient = $client_data;

        // run this query
        $this->academic_term = $client_data->client_preferences->academics->academic_term ?? null;
        $this->academic_year = $client_data->client_preferences->academics->academic_year ?? null;
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

                // upload the file
                if(isset($upload->resource_id) && !empty($upload->resource_id)) {
                    // insert the link record
                    $stmt = $this->db->prepare("
                        UPDATE courses_resource_links 
                        SET course_id = ?, lesson_id = ?, description = ?, link_url = ?, 
                            link_name = ?, created_by = ?, resource_type = ?
                        WHERE item_id = ? AND client_id = ?
                    ");
                    $stmt->execute([$upload->course_id, $lesson_ids, $upload->description, $upload->link_url, 
                        $upload->link_name, $params->userId, "link", $upload->resource_id, $params->clientId
                    ]);    
                    # set the output to return when successful
                    $return = ["code" => 200, "data" => "Resource Link successfully updated.", "refresh" => 2000];
                } else {
                    // insert the link record
                    $stmt = $this->db->prepare("
                        INSERT INTO courses_resource_links 
                        SET item_id = ?, client_id = ?, course_id = ?, lesson_id = ?, description = ?, link_url = ?, 
                        link_name = ?, created_by = ?, academic_year = ?, academic_term = ?
                    ");
                    $stmt->execute([
                        $item_id, $params->clientId, $upload->course_id, $lesson_ids, 
                        $upload->description, $upload->link_url, $upload->link_name, 
                        $params->userId, $params->academic_year, $params->academic_term 
                    ]);    
                    # set the output to return when successful
                    $return = ["code" => 200, "data" => "Resource Link successfully uploaded.", "refresh" => 2000];
                }
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
                if(isset($upload->resource_id) && !empty($upload->resource_id)) {
                    // insert the link record
                    $stmt = $this->db->prepare("
                        UPDATE courses_resource_links 
                        SET course_id = ?, lesson_id = ?, description = ?, link_url = ?, 
                            link_name = ?, created_by = ?, resource_type = ?
                        WHERE item_id = ? AND client_id = ?
                    ");
                    $stmt->execute([$upload->course_id, $lesson_ids, $upload->description, $fileName, 
                        $upload->file_name, $params->userId, "file", $item_id, $params->clientId
                    ]);    
                    # set the output to return when successful
                    $return = ["code" => 200, "data" => "Resource File successfully updated.", "refresh" => 2000];
                } else {
                    // insert the link record
                    $stmt = $this->db->prepare("
                        INSERT INTO courses_resource_links 
                        SET item_id = ?, client_id = ?, course_id = ?, lesson_id = ?,
                        description = ?, link_url = ?, link_name = ?, created_by = ?, resource_type = ?
                    ");
                    $stmt->execute([$item_id, $params->clientId, $upload->course_id, $lesson_ids, 
                        $upload->description, $fileName, $upload->file_name, $params->userId, "file"
                    ]);    
                    # set the output to return when successful
                    $return = ["code" => 200, "data" => "Resource File successfully uploaded.", "refresh" => 2000];
                }
            }

			# append to the response
			$return["additional"] = [
                "clear" => true, 
                "data" => load_class("courses", "controllers")->resources_list($params->clientId, $upload->course_id)
            ];
            
            return $return;

        } catch(PDOException $e) {
            return $this->unexpected_error;
        }

    }

    /**
     * List all Course Resources
     * 
     * @return Array
     */
    public function e_courses(stdClass $params) {

        try {

            $params->minified = true;
            $params->attachments_only = true;
            
            $list_courses = load_class("courses", "controllers")->list($params)["data"];
            
            $resources_array = [];
            foreach($list_courses as $each) {
                foreach($each["files_list"] as $file) {
                    $resources_array[] = $file;
                }
            }

            $total_count = count($resources_array);

            $attachment_html = load_class("forms", "controllers")->list_attachments($resources_array, $params->userId, "col-lg-3 col-md-4", false, false);

            return [
                "data" => [
                    "array" => $resources_array,
                    "pagination" => [
                        "first" => 1,
                        "last" => 20,
                        "page" => 1,
                        "page_count" => 1,
                        "per_page" => 20,
                        "total_count" => $total_count
                    ],
                    "html" => $attachment_html
                ]
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Comments List
     * 
     * @return Array
     */
    public function comments_list(stdClass $params) {
        
        // init the filter
        $where_clause = "";
        $params->query = "1";

        // add some filters
        $params->query .= (isset($params->record_id) && !empty($params->record_id)) ? " AND a.record_id='{$params->record_id}'" : null;
        $params->query .= (isset($params->user_id) && !empty($params->user_id)) ? " AND a.user_id='{$params->user_id}'" : null;
        $params->query .= (isset($params->type) && !empty($params->type)) ? " AND a.type='{$params->type}'" : null;
        $params->query .= (isset($params->comment_id) && !empty($params->comment_id)) ? " AND a.id='{$params->comment_id}'" : null;

        // the number of rows to limit the query
		$params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // check the last item to display
        $params->last_comment_id = isset($params->last_comment_id) && !empty($params->last_comment_id) ? $params->last_comment_id : null;
        if($params->last_comment_id) {
            if($params->last_comment_id == "no_more_record") {
                $where_clause = " AND a.id < '{$params->last_comment_id}'";
            } else {
                $where_clause = " AND a.id < {$params->last_comment_id}";
            }
        }

        //
        try {
            // set initial results
            $results = [];

            // load the very first record in the query parsed by the user
            $comments_count = $this->db->prepare("SELECT COUNT(*) AS comments_count FROM e_learning_comments a WHERE {$params->query} {$where_clause} ORDER BY a.id ASC LIMIT 10000");
            $comments_count->execute();
            $comments_count = $comments_count->fetch(PDO::FETCH_OBJ)->comments_count ?? 0;
            
            // load the very first record in the query parsed by the user
            $last_one = $this->db->prepare("SELECT a.id AS first_item FROM e_learning_comments a WHERE {$params->query} {$where_clause} ORDER BY a.id ASC LIMIT 1");
            $last_one->execute();
            $first_item = $last_one->fetch(PDO::FETCH_OBJ);

            // prepare the statement
            $stmt = $this->db->prepare("SELECT a.*,
                    u.name AS fullname, u.email, u.phone_number, u.image, u.user_type
                FROM e_learning_comments a
                LEFT JOIN users u ON u.item_id = a.user_id
                WHERE {$params->query} {$where_clause} ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // init the values
            $data = [];
            $last_comment_id = "no_more_record";
            // loop through the records
            foreach($results as $key => $result) {
                
                $key++;
                $result->row_id = $key;

                $result->comment = htmlspecialchars_decode($result->comment);
               
                $last_comment_id = $result->id;

                $result->time_ago = time_diff($result->date_created);
                $result->date_created = date("l, F jS, Y \a\\t h:i:sA", strtotime($result->date_created));

                $data[] = $result;
            }

            // last reply id
            if(!empty($data)) {
                $last_comment_id = $last_comment_id;
            }
            
            // last row id record
            return [
                "comments_list" => $data,
                "comments_count" => $comments_count,
                "first_reply_id" => isset($first_item->first_item) ? $first_item->first_item : 0,
                "last_comment_id" => $last_comment_id
            ];
        } catch(PDOException $e) {
            return [];
        }
    
    }

    /**
     * E-Resources List
     * 
     * @return Array
     */
    public function e_resources(stdClass $params) {

        $params->attachments_only = true;
        $load_resources = $this->load_resources($params);

        $resources_array = [];

        // if the files parameter isset
        if(isset($load_resources["files"])) {
            // get the data
            $data = $load_resources["data"];
            // loop through the files list
            foreach($load_resources["files"] as $key => $file) {
                // get the related data
                $this_data = $data[$key];
                // loop through each fle
                foreach($file as $each) {
                    // set the file content
                    $file_content = (object) $each;
                    // assign more information
                    $file_content->related_info = (object) [
                        "name" => $this_data->subject,
                        "date" => $this_data->date_created,
                        "description" => $this_data->description,
                    ];
                    // if this user created it
                    if($this_data->created_by === $params->userId) {
                        $file_content->is_editable = "e-learning_update";
                    }
                    // append to the resource array
                    $resources_array[] = $file_content;
                }
            }

            // return if clean_response was parsed
            if(isset($params->clean_response)) {
                return [
                    "files" => $resources_array,
                    "data" => $load_resources["data"]
                ];
            }
        }

        // return $resources_array;
        
        $total_count = count($resources_array);

        if(empty($resources_array)) {
            $attachment_html = "<div class='text-center'>No e-learning materials have been uploaded yet. 
                Please check back later for any.</div>";
        } else {
            $attachment_html = load_class("forms", "controllers")->list_attachments($resources_array, $params->userId, "col-lg-3 col-md-4", false, "e-learning_view", false);
        }

        return [
            "data" => [
                "array" => $resources_array,
                "pagination" => [
                    "first" => 1,
                    "last" => 20,
                    "page" => 1,
                    "page_count" => 1,
                    "per_page" => 20,
                    "total_count" => $total_count
                ],
                "html" => $attachment_html
            ]
        ];

    }

    /**
     * E-Resources List
     * 
     * @return Array
     */
    public function load_resources(stdClass $params) {

        $params->query = "1";

        // set the limit for the records to load
        $params->limit = isset($params->limit) ? $params->limit : $this->global_limit;

        // append the class_id if the user type is student
        if(($params->userData->user_type === "student")) {
            $params->class_id = $params->userData->class_guid;
            $params->state = "Published";
        } elseif(($params->userData->user_type === "teacher")) {
            $params->course_tutor = $params->userData->user_id;
        } else {
            $params->created_by = $params->userData->user_id;
        }

        // set the academic year and terms
        $params->academic_term = isset($params->academic_term) ? $params->academic_term : $this->academic_term;
        $params->academic_year = isset($params->academic_year) ? $params->academic_year : $this->academic_year;

        $params->query .= (isset($params->rq)) ? " AND a.subject LIKE '%{$params->rq}%'" : null;
        $params->query .= isset($params->academic_year) ? " AND a.academic_year='{$params->academic_year}' AND cs.academic_year='{$params->academic_year}'" : "";
        $params->query .= isset($params->academic_term) ? " AND a.academic_term='{$params->academic_term}' AND cs.academic_term='{$params->academic_term}'" : "";
        $params->query .= isset($params->state) ? " AND a.state='{$params->state}'" : "";
        $params->query .= (isset($params->resource_id) && !empty($params->resource_id)) ? " AND a.item_id='{$params->resource_id}'" : null;
        $params->query .= (isset($params->course_tutor) && !empty($params->course_tutor)) ? " AND a.course_tutors LIKE '%{$params->course_tutor}%'" : null;
        $params->query .= (isset($params->unit_id) && !empty($params->unit_id) && $params->unit_id !== "null") ? " AND a.unit_id LIKE '%{$params->unit_id}%'" : null;
        $params->query .= (isset($params->class_id) && !empty($params->class_id) && preg_match("/^[0-9]+$/", $params->class_id)) ? " AND cl.id = '{$params->class_id}'" : ((isset($params->class_id) && !empty($params->class_id) && ($params->class_id !== "null")) ? " AND a.class_id='{$params->class_id}'" : null);
        $params->query .= (isset($params->course_id) && !empty($params->course_id) && $params->course_id !== "null") ? " AND cs.id='{$params->course_id}'" : null;

        try {

            $attachmentsOnly = (bool) isset($params->attachments_only);
            
            $stmt = $this->db->prepare("SELECT a.*,
                    u.name AS fullname, u.phone_number, u.email, u.image,
                    cl.name AS class_name, cl.id AS class_row_id, 
                    cs.id AS course_row_id, cs.name AS course_name, cp.name AS unit_name,
                    (
                        SELECT b.description FROM files_attachment b 
                        WHERE b.resource='e_learning' AND b.record_id = a.item_id 
                        ORDER BY b.id DESC LIMIT 1
                    ) AS attachment
                FROM e_learning a
                    LEFT JOIN users u ON u.item_id = a.created_by
                    LEFT JOIN classes cl ON cl.item_id = a.class_id
                    LEFT JOIN courses cs ON cs.item_id = a.course_id
                    LEFT JOIN courses_plan cp ON cp.item_id = a.unit_id
                WHERE
                    {$params->query} AND a.client_id = ? AND a.status = ? ORDER BY a.id DESC LIMIT {$params->limit}
            ");
            $stmt->execute([$params->clientId, 1]);

            $data = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // clean the description data
                $result->description = custom_clean(htmlspecialchars_decode($result->description));

                // if attachment variable was parsed
                if($attachmentsOnly) {
                    $files_only = json_decode($result->attachment, true)["files"];
                    $data["files"][] = $files_only;
                    $data["data"][] = $result;
                } else {
                    $result->attachment = json_decode($result->attachment, true);
                    $data[] = $result;
                }
            }

            return $data;

        } catch(PDOException $e) {
            return [];
        }

    }

    /**
     * Upload E-Learning Material
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function upload_4elearning(stdClass $params) {

        $class = $this->pushQuery("id, item_id", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        if(empty($class)) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
        }

        $course = $this->pushQuery("id, item_id, course_tutor", "courses", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        if(empty($course)) {
            return ["code" => 203, "data" => "Sorry! An invalid course id was supplied."];
        }

        $item_id = random_string("alnum", 32);

        try {
            
            // create a new object
            $filesObj = load_class("files", "controllers");

            // confirm that a file has been upload
            if(empty($this->session->elearning_resource)) {
                return ["code" => 203, "data" => "Sorry! You must upload a file."];
            }
            
            // attachments
            $attachments = $filesObj->prep_attachments("elearning_resource", $params->userId, $item_id);

            // prepare and upload the file
            $stmt = $this->db->prepare("INSERT INTO e_learning SET 
                item_id = ?, client_id = ?, subject = ?, description = ?, class_id = ?, allow_comments = ?, course_id = ?,
                unit_id = ?, state = ?, created_by = ?, academic_year = ?, academic_term = ?, course_tutors = ?
            ");
            $stmt->execute([
                $item_id, $params->clientId, $params->title, $params->description ?? null, $class[0]->item_id,
                $params->allow_comment ?? "allow", $course[0]->item_id, $params->unit_id, 
                $params->state ?? "Published", $params->userId, $params->academic_year, 
                $params->academic_term, $course[0]->course_tutor
            ]);

            // insert the record if not already existing
            $files = $this->db->prepare("INSERT INTO files_attachment SET resource= ?, resource_id = ?, description = ?, record_id = ?, created_by = ?, attachment_size = ?");
            $files->execute(["e_learning", "{$course[0]->item_id}_{$item_id}", json_encode($attachments), "{$item_id}", $params->userId, $attachments["raw_size_mb"]]);

            // loop through the files list
            foreach($attachments["files"] as $each) {
                $this->db->query("INSERT INTO e_learning_views SET video_id='{$item_id}_{$each["unique_id"]}', comments='0', views='0'");
            }

            // log the user activity
            $this->userLogs("e_learning", $item_id, null, "{$params->userData->name} uploaded a new e-learning resource.", $params->userId);

			// return the output
            return [
                "code" => 200, 
                "data" => "E-Learning Material was successfully uploaded.", 
                "additional" => [
                    "clear" => true, 
                    "href" => "{$this->baseUrl}e-learning"
                ]
            ];

        } catch(PDOException $e) {}

    }

    /**
     * Upload E-Learning Material
     * 
     * @param stdClass $params
     * 
     * @return Array
     */
    public function update_4elearning(stdClass $params) {

        $class = $this->pushQuery("id, item_id", "classes", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        if(empty($class)) {
            return ["code" => 203, "data" => "Sorry! An invalid class id was supplied."];
        }

        $course = $this->pushQuery("id, item_id, course_tutor", "courses", "id='{$params->class_id}' AND client_id='{$params->clientId}' AND status='1' LIMIT 1");
        if(empty($course)) {
            return ["code" => 203, "data" => "Sorry! An invalid course id was supplied."];
        }

        $return = [];
        $item_id = $params->resource_id;

        try {

            // old record
            $prevData = $this->pushQuery(
                "a.id, (SELECT b.description FROM files_attachment b WHERE b.record_id = a.item_id ORDER BY b.id DESC LIMIT 1) AS attachment",
                "e_learning a", 
                "a.item_id='{$params->resource_id}' AND a.client_id='{$params->clientId}' AND a.status='1' LIMIT 1"
            );

            // if empty then return
            if(empty($prevData)) {
                return ["code" => 203, "data" => "Sorry! An invalid id was supplied."];
            }

            // initialize
            $initial_attachment = [];

            /** Confirm that there is an attached document */
            if(!empty($prevData[0]->attachment)) {
                // decode the json string
                $db_attachments = json_decode($prevData[0]->attachment, true);
                // get the files
                if(isset($db_attachments["files"])) {
                    $initial_attachment = $db_attachments["files"];
                }
                $init_attachment = [];
                foreach($initial_attachment as $attachment) {
                    if(!$attachment["is_deleted"]) {
                        $attachment["existing_already"] = true;
                        $init_attachment[] = $attachment;
                    }
                }
                $initial_attachment = $init_attachment;
            }
            
            // confirm that a file has been upload
            if(empty($this->session->elearning_resource) && empty($initial_attachment)) {
                return ["code" => 203, "data" => "Sorry! You must upload a file."];
            }

            // prepare and upload the file
            $stmt = $this->db->prepare("UPDATE e_learning SET 
                updated_date = now(), updated_by = ?,
                subject = ?, description = ?, class_id = ?, allow_comments = ?, course_id = ?,
                unit_id = ?, state = ?, created_by = ?, academic_year = ?, academic_term = ?, course_tutors = ?
                WHERE item_id = ? AND client_id = ?  LIMIT 1
            ");
            $stmt->execute([
                $params->userId, $params->title, $params->description ?? null, $class[0]->item_id,
                $params->allow_comment ?? "allow", $course[0]->item_id, $params->unit_id, 
                $params->state ?? "Published", $params->userId, $params->academic_year, 
                $params->academic_term, $course[0]->course_tutor, $item_id, $params->clientId
            ]);

            // if the user actually uploaded a new file
            if(!empty($this->session->elearning_resource)) {
                
                // append the attachments
                $filesObj = load_class("files", "controllers");
                $module = "elearning_resource";
                
                // prepare the file to upload
                $attachments = $filesObj->prep_attachments($module, $params->userId, $item_id, $initial_attachment);
                
                // reload the page if the user uploaded a file
                $return = ["href" => "{$this->baseUrl}e-learning_update/{$item_id}"];

                // insert the record if not already existing
                $files = $this->db->prepare("UPDATE files_attachment SET description = ?, attachment_size = ? WHERE record_id = ? LIMIT 1");
                $files->execute([json_encode($attachments), $attachments["raw_size_mb"], $item_id]);

                // loop through the files list
                foreach($attachments["files"] as $each) {
                    // if the file does not already exist
                    if(!isset($each["existing_already"])) {
                        // confirm if the record does not already exist
                        if(empty($this->pushQuery("id", "e_learning_views", "video_id='{$item_id}_{$each["unique_id"]}' LIMIT 1"))) {
                            // insert the new record
                            $this->db->query("INSERT INTO e_learning_views SET video_id='{$item_id}_{$each["unique_id"]}', comments='0', views='0'");
                        }
                    }
                }

            }
            
            // log the user activity
            $this->userLogs("e_learning", $item_id, null, "{$params->userData->name} updated a new e-learning resource material.", $params->userId);

			// return the output
            return [
                "code" => 200, 
                "data" => "E-Learning Material was successfully updated.", 
                "additional" => $return
            ];

        } catch(PDOException $e) {
            print $e->getMessage();
        }

    }

    /**
     * Save the video time
     * 
     * @return Bool
     */
    public function save_time(stdClass $params) {
        // current time
        $timer = isset($params->video_time) ? round($params->video_time) : 1;
        $video_id = isset($params->video_id) ? $params->video_id : null;

        // end query if no video id was parsed
        if(empty($video_id)) {
            return;
        }

        // confirm if there is a record
        $user = $this->pushQuery("timer", "e_learning_timer", "user_id='{$params->userId}' AND video_id='{$video_id}' LIMIT 1");
        if(empty($user)) {
            return $this->db->query("INSERT INTO e_learning_timer SET timer='{$timer}', user_id='{$params->userId}', video_id='{$video_id}'");
        } else {
            return $this->db->query("UPDATE e_learning_timer SET timer ='{$timer}' WHERE user_id='{$params->userId}' AND video_id='{$video_id}' LIMIT 1");
        }

    }

    /**
     * Load the Video Last saved Time
     * 
     * @return Int
     */
    public function video_time(stdClass $params) {
        
        // current time
        $video_id = isset($params->video_id) ? $params->video_id : null;

        // end query if no video id was parsed
        if(empty($video_id)) {
            return;
        }
        
        // confirm if there is a record
        $video = $this->pushQuery(
            "timer", 
            "e_learning_timer", 
            "user_id='{$params->userId}' AND video_id='{$video_id}' LIMIT 1"
        );
        
        return !empty($video) ? $video[0]->timer : 1;

    }

}
?>