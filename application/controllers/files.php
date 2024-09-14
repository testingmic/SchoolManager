<?php

class Files extends Myschoolgh {
    
    private $string_length;

    public function __construct(){
        parent::__construct();

        $this->string_length = (RANDOM_STRING + 3);
    }

    /**
     * Upload a temporary file
     * And return some basic information about the file
     * 
     * @return Array
     */
    public function preview(stdClass $params) {

        // confirm that a logo was parsed
        if(!empty($params->file_upload)) {

            // set the upload directory
            $uploadDir = "assets/uploads/temp/";

            if(!is_dir("assets/uploads/")) {
                mkdir("assets/uploads/");
            }
            // create directory is non existent
            if(!is_dir($uploadDir)) {
                mkdir($uploadDir);
            }

            // File path config 
            $baseName = basename($params->file_upload["name"]); 
            $targetFilePath = $uploadDir . preg_replace("/[\s]/", "_", $baseName); 
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow certain file formats 
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif', 'webp', 'pjpeg']; 

            // check if its a valid image
            if(!empty($baseName) && validate_image($params->image["tmp_name"])){
                
                // set a new filename
                $fileName = $uploadDir . random_string("alnum", RANDOM_STRING).".{$fileType}";
                // get the temporary profile picture
                $this->session->tempProfilePicture = $fileName;
                // Upload file to the server 
                if(move_uploaded_file($params->file_upload["tmp_name"], $fileName)){ 
                    return [
                        "code" => 200,
                        "data" => [
                            "preview" => true,
                            "size" => $params->file_upload["size"],
                            "filename" => $baseName,
                            "href" => $fileName
                        ]
                    ];
                }

            }

        }

    }

    public function media_uploads(stdClass $params) {

        //: create a new session
        $root_dir = "assets/uploads";
        $sessionClass = load_class('sessions', 'controllers');

        // set the current user id
        $currentUser_Id = !empty($this->session->student_id) ? $this->session->student_id : $params->userData->user_id;

        // assign a variable to the user information
        $module = "documents_root";
        $userData = $params->userData;

        // if no directory has been created for the user then create one
        if(!is_dir("{$root_dir}/{$currentUser_Id}")) {
            // create additional directories for user
            mkdir("{$root_dir}/{$currentUser_Id}/docs/{$module}", 0777, true);
            mkdir("{$root_dir}/{$currentUser_Id}/tmp/download", 0777, true);
            mkdir("{$root_dir}/{$currentUser_Id}/tmp/thumbnail");
        }

        // set the user's directory
        $tmp_dir = "{$root_dir}/{$currentUser_Id}/tmp/{$module}/";
        $dwn_dir = "{$root_dir}/{$currentUser_Id}/tmp/download/";

        // create the temporary file directory
        if(!is_dir($tmp_dir)) {
            mkdir("{$tmp_dir}", 0777, true);
        }

        // create the download file directory
        if(!is_dir($dwn_dir)) {
            mkdir("{$dwn_dir}", 0777, true);
        }

        // if the attachment file upload is not parsed
        if(!isset($params->files_list)) {
            return ["code" => 203, "data" => "No file attached."];
        }

        // verify if the files_list parameter is an array
        if(!is_array($params->files_list)) {
            return ["code" => 203, "data" => "No file attached."];
        }

        // attachment list
        $attachments_list = $this->session->$module;

        // calculate the file size
        $totalFileSize = 0;
        $files_array_keys = ["name", "type", "tmp_name", "error", "size"];
        
        // set the files list
        $files_list = [];

        // confirm the array keys
        $parsed_keys = array_keys($params->files_list);

        // confirm the keys parsed
        if(count($parsed_keys) !== count($files_array_keys)) {
            return ["code" => 203, "data" => "Please ensure only valid files were attached."];
        }

        // loop through the keys parsed to ensure all are existent in the main
        foreach($parsed_keys as $key) {
            if(!in_array($key, $files_array_keys)) {
                return ["code" => 203, "data" => "Please ensure only valid files were attached."];
            }
        }

        // set a variable for the file
        foreach($params->files_list["name"] as $file_key => $file) {
            $files_list[$file_key] = [
                "name" => $params->files_list["name"][$file_key],
                "type" => $params->files_list["type"][$file_key],
                "tmp_name" => $params->files_list["tmp_name"][$file_key],
                "error" => $params->files_list["error"][$file_key],
                "size" => $params->files_list["size"][$file_key]
            ];
        }

        // clear the files
        $files_uploaded = "";

        // loop through the files list and upload
        foreach($files_list as $file_to_upload) {

            // set the file details to upload
            $fileName = basename($file_to_upload["name"]); 
            $newFileName = random_string('alnum', 32);
            $targetFilePath = $tmp_dir . $fileName;
            $n_FileTitle_Real = str_ireplace(' ', '-', $fileName);
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $obj_key = $fileType;

            // set the accepted files to this list
            $accepted_files = $this->accepted_attachment_file_types;

            // if the accept variable was also parsed
            if(isset($params->accept) && !empty($params->accept)) {
                // set the object key
                $obj_key = ".{$fileType}";

                // convert the accepted files to an array
                $accepted_files = $this->stringToArray($params->accept);

                // confirm that each item is indeed accepted on this server
                foreach($accepted_files as $check) {
                    // remove the fullstop
                    $check = str_replace(".", "", $check);

                    // if the file type is not accepted at all on this server.
                    if(!in_array($check, $this->accepted_attachment_file_types)) {
                        return ["code" => 203, "data" => "Uploaded file type not accepted."];
                    }
                }
            }

            // if the file type is in the list of accepted types
            if(!in_array($obj_key, $accepted_files)){
                return ["code" => 203, "data" => "Uploaded file type not accepted."];
            }

            // validate the file uploaded
            if(!validate_document($fileName, $file_to_upload["tmp_name"])) {
                return ["code" => 203, "data" => "Sorry! The uploaded file type not accepted."];   
            }

            // file size
            $n_FileSize = 0;

            // meaning load the attachments
            if(!empty($attachments_list)) {
                
                // loop through the list of files
                foreach($attachments_list as $each_file) {
                    //: get the file size
                    $n_FileSize = file_size_convert("{$tmp_dir}{$each_file['first']}");
                    $n_FileSize_KB = file_size("{$tmp_dir}{$each_file['first']}");
                    $totalFileSize += $n_FileSize_KB;
                }
                $n_FileSize = round(($totalFileSize / 1024));
            }

            // maximum files fize check
            if($n_FileSize > $this->max_attachment_size) {
                return ["code" => 203, "data" => "Maximum attachment size is {$this->max_attachment_size}MB"];
            }

            // set a new filename
            $uploadPath = $tmp_dir . $newFileName;
            $item_id = "temp_attachment";


            // Upload file to the server 
            if(move_uploaded_file($file_to_upload["tmp_name"], $uploadPath)){ 
                //: set this session data
                $sessionClass->add($module, $newFileName, $n_FileTitle_Real, $item_id, file_size_convert("{$tmp_dir}{$newFileName}", true), $fileType);

                // set the file uploaded
                $files_uploaded .= "{$n_FileTitle_Real} successfully uploaded.\n";
            }

        }

        try {
            // get the attachments list
            $attachments_list = $this->session->documents_root;

            // if the $attachments_list is empty
            if(empty($attachments_list)) {
                return ["code" => 203, "data" => "Please attach a file(s) to proceed."];
            }

            // create a new unique_id
            $upload_id = random_string("alnum", $this->string_length);

            // loop through the files to upload list and insert a record in the documents table
            foreach($attachments_list as $file) {

                // create a new unique id for this file
                $file_size = str_ireplace(["KB"], [""], $file["forth"]);
                $document_id = random_string("alnum", RANDOM_STRING);
                $document_ids[] = $document_id;

                // save the document into the database
                $this->_save("documents", [
                    "upload_id" => $upload_id, "client_id" => $params->clientId, "item_id" => $document_id, "type" => "file", 
                    "name" => $file["second"], "file_type" => $file["fifth"],
                    "created_by" => $params->userId, "file_size" => $file_size, "file_ref_id" => $file["first"]
                ]);
            }
            
            // prepare the attachment files to upload
            $attachments = $this->prep_attachments("documents_root", $params->userId, "root");

            // insert the files attachment
            $this->_save("files_attachment", [
                "resource" => "documents", "resource_id" => "documents_root", "description" => json_encode($attachments), 
                "record_id" => $upload_id, "created_by" => $params->userId, "attachment_size" => $attachments["raw_size_mb"],
                "client_id" => $params->clientId
            ]);

            return [
                "code" => 200,
                "data" => $files_uploaded
            ];

        } catch(PDOException $e) {}

    }

    /**
     * File attachments uploads
     * Create a directory for each user and another directory for each module.
     * There must be two (2) directories each, one is temp and the other is docs (permanent)
     * 
     * @param \stdClass $params 
     * 
     * @return Array
     */
    public function attachments(stdClass $params) {

        // global variable
        global $session;

        /** Initialize for processing */
        $root_dir = "assets/uploads";

        // if the client preferences are empty
        if(empty($params->userData->client_preferences)) {
            // return if the user is not logged in
            if((!isset($params->userData->user_id) || !isset($params->module) || (!isset($params->label)) && !isset($params->attachments_list))) {
                return "error";
            }
        }

        // set the current user id
        $currentUser_Id = !empty($session->student_id) ? $session->student_id : (
            $params->userData->user_id ?? $params->userData->client_id
        );
        
        // perform all this checks if the attachmments list was not parsed
        if(!isset($params->attachments_list)) {

            //: create a new session
            $sessionClass = load_class('sessions', 'controllers');

            // assign a variable to the user information
            $module = $params->module;

            // if the module is remove existing then run this query
            if($module == "remove_existing") {                
                // process the user request and remove the record 
                return $this->remove_existing_file($params->label, "yes");
            }

            // if no directory has been created for the user then create one
            if(!is_dir("{$root_dir}/{$currentUser_Id}")) {
                // create additional directories for user
                mkdir("{$root_dir}/{$currentUser_Id}/docs/{$module}", 0777, true);
                mkdir("{$root_dir}/{$currentUser_Id}/tmp/download", 0777, true);
                mkdir("{$root_dir}/{$currentUser_Id}/tmp/thumbnail");
            }

            // set the user's directory
            $tmp_dir = "{$root_dir}/{$currentUser_Id}/tmp/{$module}/";
            $dwn_dir = "{$root_dir}/{$currentUser_Id}/tmp/download/";

            // create the temporary file directory
            if(!is_dir($tmp_dir)) {
                mkdir("{$tmp_dir}", 0777, true);
            }

            // create the download file directory
            if(!is_dir($dwn_dir)) {
                mkdir("{$dwn_dir}", 0777, true);
            }
        
            /** Get the data for processing */
            if($params->label == "upload") {

                // if the attachment file upload is not parsed
                if((!isset($params->attachment_file_upload) || (isset($params->attachment_file_upload) && !isset($params->attachment_file_upload["name"]))) && !isset($params->comment_attachment_file_upload) && !isset($params->_attachment_file_upload)) {
                    return ["code" => 203, "data" => "No file attached."];
                }

                // attachment list
                $attachments_list = $this->session->$module;

                // calculate the file size
                $totalFileSize = 0;

                // set a variable for the file
                $file_to_upload = isset($params->attachment_file_upload) ? $params->attachment_file_upload : ($params->comment_attachment_file_upload ?? ($params->_attachment_file_upload ?? null));

                // if the file attachment is empty
                if(empty($file_to_upload)) {
                    return ["code" => 203, "data" => "No file attached."];
                }
                
                // set the file details to upload
                $fileName = basename($file_to_upload["name"]); 
                $newFileName = random_string('alnum', 32);
                $targetFilePath = $tmp_dir . $fileName;
                $n_FileTitle_Real = str_ireplace(' ', '-', $fileName);
                $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
                $obj_key = $fileType;

                // set the accepted files to this list
                $accepted_files = $this->accepted_attachment_file_types;

                // if the accept variable was also parsed
                if(isset($params->accept) && !empty($params->accept)) {
                    // set the object key
                    $obj_key = ".{$fileType}";

                    // convert the accepted files to an array
                    $accepted_files = $this->stringToArray($params->accept);

                    // confirm that each item is indeed accepted on this server
                    foreach($accepted_files as $check) {
                        // remove the fullstop
                        $check = str_replace(".", "", $check);

                        // if the file type is not accepted at all on this server.
                        if(!in_array($check, $this->accepted_attachment_file_types)) {
                            return ["code" => 203, "data" => "Uploaded file type not accepted."];
                        }
                    }
                }

                // if the file type is in the list of accepted types
                if(!in_array($obj_key, $accepted_files)){
                    return ["code" => 203, "data" => "Uploaded file type not accepted."];
                }

                // validate the file uploaded
                if(!validate_document($fileName, $file_to_upload["tmp_name"])) {
                    return ["code" => 203, "data" => "Sorry! The uploaded file type not accepted."];   
                }

                // file size
                $n_FileSize = 0;

                // meaning load the attachments
                if(!empty($attachments_list)) {
                    
                    // loop through the list of files
                    foreach($attachments_list as $each_file) {

                        //: get the file size
                        $n_FileSize = file_size_convert("{$tmp_dir}{$each_file['first']}");
                        $n_FileSize_KB = file_size("{$tmp_dir}{$each_file['first']}");
                        $totalFileSize += $n_FileSize_KB;
                    }
                    $n_FileSize = round(($totalFileSize / 1024));

                }

                // maximum files fize check
                if($n_FileSize > $this->max_attachment_size) {
                    return ["code" => 203, "data" => "Maximum attachment size is {$this->max_attachment_size}MB"];
                }

                // set a new filename
                $uploadPath = $tmp_dir . $newFileName;
                $params->item_id = isset($params->item_id) ? $params->item_id : "temp_attachment";

                // Upload file to the server 
                if(move_uploaded_file($file_to_upload["tmp_name"], $uploadPath)){ 
                    //: set this session data
                    $sessionClass->add($params->module, $newFileName, $n_FileTitle_Real, $params->item_id, file_size_convert("{$tmp_dir}{$newFileName}", true), $fileType);
                }

                // return the temporary files list after upload
                return [
                    "code" => 200,
                    "data" => $this->list_temp_attachments($module, $tmp_dir)["data"],
                    "additional" => [
                        "filename" => $file_to_upload["name"]
                    ]
                ];
            }

            /** Load the files */
            elseif($params->label == "list") {
                // set module
                $attachments_list = $this->session->$module;
                
                // meaning load the attachments
                if(!empty($attachments_list)) {
                    // list the temporary attachment list
                    return [
                        "code" => 200,
                        "data" => $this->list_temp_attachments($module, $tmp_dir)["data"]
                    ];
                }

            }

            /** Remove an item */
            elseif($params->label == "remove") {
                // if the item id is not parsed
                if(!isset($params->item_id) || empty($this->session->$module)) {
                    return;
                }
                
                // remove the item
                if($sessionClass->remove($params->module, $params->item_id, $tmp_dir)) {
                    return ["code" => 200, "data" => "Attachment successfully removed."];
                }
            }

            /** Download Temporary File */
            elseif($params->label == "download") {

                // if the item id is not parsed
                if(!isset($params->item_id) || empty($this->session->$module)) {
                    return;
                }

                // create the download directory if non existent
                if(!is_dir($dwn_dir)) {
                    mkdir($dwn_dir);
                }

                // set attachment list
                $attachments_list = $this->session->$module;
                
                // loop through list
                foreach($attachments_list as $each) {
                    
                    // if the value for first key matches the item id
                    if($each["first"] == $params->item_id) {
                        
                        // format the download string
                        $file_to_download = "{$dwn_dir}.{$each["fifth"]}";

                        // replace empty fields with underscore
                        $file_to_download = preg_replace("/[\s]/", "_", $file_to_download);
                        
                        // create the document for download
                        copy("{$tmp_dir}{$params->item_id}", $file_to_download);
                        
                        // return the file link
                        return [
                            "code" => 200,
                            "data" => $file_to_download
                        ];

                        // break the loop
                        break;
                    }

                }
            }

            /** Discard attached */
            elseif($params->label == "discard") {
                // discard all items
                return $sessionClass->clear($params->module, $tmp_dir);
            }

        } elseif(isset($params->attachments_list)) {

            // list all the attachments
            $module = $params->module;
            $attachments_list = $params->attachments_list;
            
            // calculate the file size
            $totalFileSize = 0;
            $tmp_dir = "{$root_dir}/{$currentUser_Id}/docs/{$module}/";

            // html string
            $attachments = "<div class='row'>";
            $showView = (bool) isset($params->show_view);

            // if the files parameter is set
            if(!empty($attachments_list->files)) {
                
                // loop through the list of files
                foreach($attachments_list->files as $each_file) {

                    // append the file if not deleted
                    if(!$each_file->is_deleted) {

                        //: get the file information
                        $fileInfo = get_file_info($each_file->path);

                        // if the file was found
                        if(!empty($fileInfo)) {
                            
                            $n_FileSize = file_size_convert($fileInfo["server_path"]);
                            $n_FileSize_KB = file_size($fileInfo["server_path"]);
                            $totalFileSize += $n_FileSize_KB;
                            
                            // // default
                            $color = 'danger';
                            // //: Background color of the icon
                            if(in_array($fileInfo["extension"], ['doc', 'docx'])) {
                                $color = 'primary';
                            } elseif(in_array($fileInfo["extension"], ['xls', 'xlsx', 'csv'])) {
                                $color = 'success';
                            } elseif(in_array($fileInfo["extension"], ['txt', 'json', 'rtf', 'sql', 'css', 'php'])) {
                                $color = 'default';
                            }

                            // get the download link
                            $file_to_download = base64_encode($each_file->path."{$this->underscores}{$each_file->record_id}");

                            // list the files
                            $attachments .= "<div data-file_container='{$each_file->record_id}_{$each_file->unique_id}' title=\"Click to view: {$fileInfo["name"]}\" class=\"col-md-12 pb-1 text-left\" data-document-link=\"{$fileInfo["server_path"]}\">";
                            $attachments .= "<div class=\"bg-inverse-primary mb-1\"><strong class=\"download-temp-file\">
                                <span class=\"text-{$color}\">
                                    <i class=\"{$this->favicon_array[$fileInfo["extension"]]} fa-1x font-20\"></i>
                                </span>
                                <a title=\"Click to Download\" target=\"_blank\" style=\"padding:5px;font-18\" href=\"{$this->baseUrl}download?file={$file_to_download}\">
                                    {$fileInfo["name"]}
                                </a>
                            </strong> ({$n_FileSize})";

                            // if the params has the is_deletable item set to true
                            if($params->is_deletable) {
                                
                                // display the delete button
                                $attachments .= "
                                <span class=\"float-right\">
                                    ".($showView ? "<a title='Click to view material' href='{$this->baseUrl}{$params->show_view}/{$each_file->record_id}_{$each_file->unique_id}' class='btn btn-sm btn-outline-primary'><i class=\"fa fa-eye\"></i></a>" : null)."
                                    <button type=\"button\" onclick=\"return delete_existing_file_attachment('{$each_file->record_id}_{$each_file->unique_id}');\" class=\"btn btn-outline-danger btn-sm delete-attachment-file\">
                                        <i class=\"fa fa-trash\"></i>
                                    </button>
                                </span>";
                            }

                            $attachments .= "</div>";
                            $attachments .= "</div>";

                        }

                    }

                }
            }
            $attachments .= "</div>";
            $n_FileSize = round(($totalFileSize / 1024), 2);

            return [
                "code" => 200,
                "data" => [
                    "files" => $attachments,
                    "module" => $module,
                    "details" => "<strong>Files Size:</strong> {$n_FileSize}MB"
                ]
            ];

        }
        
    }

    /**
     * Delete an existing attached file record from the databas
     * 
     * @param String $record_set_id
     * 
     * @return Array
     */
    final function remove_existing_file($record_set_id, $delete_file = "no") {

        // explode the text
        $name = explode("_", $record_set_id);

        // if no record id is parsed
        if(!isset($name[1]) || empty(($name[1]))) {
            return false;
        }
        // continue processing
        $record_id = $name[0];

        // get the record information
        $attachment_record =  $this->columnValue("resource, resource_id, description", "files_attachment", "record_id='{$record_id}'");
        
        // if no record found
        if(empty($attachment_record)) {
            return "Fuck it";
        }

        // convert the string into an object
        $file_list = json_decode($attachment_record->description);

        // found
        $file_key = null;
        $found = false;
        $file_path = '';

        // loop through each file
        foreach($file_list->files as $key => $eachFile) {
            
            // check if the id matches what has been parsed in the url
            if($eachFile->unique_id == $name[1]) {
                $file_key = $key;
                $file_path = $eachFile->path;
                $name = $eachFile->name;
                $found = true;
                break;
            }

        }

        // end query if not found
        if(!$found) {
            return false;
        }

        // set the is_deleted value to 1
        $file_list->files[$key]->is_deleted = 1;

        // if the user has requested for the deletion of the file
        if($delete_file === "yes") {
            // confirm that the file actually exists
            if(is_file($file_path) && file_exists($file_path)) {
                // delete the file from the system
                unlink($file_path);
            }
        }
        
        // convert the object into string
        $description = json_encode($file_list);

        // save the new information
        $this->db->query("UPDATE files_attachment SET description='{$description}' WHERE record_id='{$record_id}' LIMIT 1");

        return "{$name} deleted!";
    }

    /**
     * List the temporary attached files list
     * 
     * @param String $module
     * @param String $tmp_dir
     * 
     * @return Array
     */
    public function list_temp_attachments($module, $tmp_dir) {

        // attachments list
        $attachments_list = $this->session->$module;

        // calculate the file size
        $totalFileSize = 0;		

        // html string
        $attachments = "<div class='row'>";

        // loop through the list of files
        foreach($attachments_list as $each_file) {

            //: get the file size
            $n_FileSize = file_size_convert("{$tmp_dir}{$each_file['first']}");
            $n_FileSize_KB = file_size("{$tmp_dir}{$each_file['first']}");
            $totalFileSize += $n_FileSize_KB;
            
            // default
            $color = 'danger';

            //: Background color of the icon
            if(in_array($each_file["fifth"], ['doc', 'docx'])) {
                $color = 'primary';
            } elseif(in_array($each_file["fifth"], ['xls', 'xlsx', 'csv'])) {
                $color = 'success';
            } elseif(in_array($each_file["fifth"], ['txt', 'json', 'rtf', 'sql', 'css', 'php'])) {
                $color = 'default';
            }
            $attachments .= "<div title=\"Click to download the file: {$each_file["second"]}.{$each_file["fifth"]}\" class=\"col-md-12 pb-1 text-left\" data-document-link=\"{$each_file["first"]}\">";
            $attachments .= "<div class=\"bg-inverse-primary p-2\"><strong onclick=\"return download_ajax_temp_file('{$module}','{$each_file["first"]}');\" class=\"cursor download-temp-file\"><span class=\"text-{$color}\"><i class=\"{$this->favicon_array[$each_file["fifth"]]} fa-1x\"></i></span> ".substr($each_file["second"], 0, 40)."</strong> ({$each_file["forth"]})";
            $attachments .= "<span class=\"float-right\"><button href=\"#\" onclick=\"return delete_ajax_file_uploaded('{$module}','{$each_file["first"]}')\" data-document-module=\"{$module}\" data-document-link=\"{$each_file["first"]}\" style=\"padding: 0.1rem 0.4rem;\" class=\"btn btn-outline-danger btn-sm delete-attachment-file\"><i class=\"fas fa-trash ml-1\"></i></button></span>";
            $attachments .= "</div>";
            $attachments .= "</div>";
        }
        $attachments .= "</div>";
        $n_FileSize = round(($totalFileSize / 1024), 2);

        return [
            "code" => 200,
            "data" => [
                "files" => $attachments,
                "module" => $module,
                "details" => "<strong>Files Size:</strong> {$n_FileSize}MB"
            ]
        ];

    }

    /**
     * Prepare attachments list and return to to be inserted into the database
     * 
     * @param String $module
     * @param String $user_id
     * @param String $record_id     - This is the unique id of the record
     * @param Array $existing_data  - An existing data 
     * 
     * @return Array
     */
    public function prep_attachments($module, $user_id, $record_id = null, $existing_data = []) {

        // initial variables
        global $defaultUser;
        $n_FileSize = 0;
        $attachments_list = $this->session->$module;

        $attachments = [
            "files" => [],
            "files_count" => 0,
            "files_size" => 0,
            "raw_size_mb" => 0
        ];

        // loop through the list of files
        if(!empty($attachments_list)) {

            //set some variables
            $totalFileSize = 0;

            // set the user's directory
            $tmp_dir = "assets/uploads/{$user_id}/tmp/{$module}/";
            
            // set a new directory
            // $resource = explode("_", $module)[0]; - do not know the reason why i did this
            $resource = $module;
            
            // the document resource directory
            $docs_dir = "assets/uploads/{$user_id}/docs/{$resource}/";

            // create the directory
            if(!is_dir($docs_dir)) {
                mkdir($docs_dir, 0777, true);
            }

            // set the list to the existing record... 
            $files_list = $existing_data;

            try {
                
                // loop through the list of attached files
                foreach($attachments_list as $each_file) {

                    //: get the file size
                    $n_FileSize = file_size_convert("{$tmp_dir}{$each_file['first']}");
                    $n_FileSize_KB = file_size("{$tmp_dir}{$each_file['first']}");
                    $totalFileSize += $n_FileSize_KB;
                    
                    // default
                    $color = 'danger';
                    //: Background color of the icon
                    if(in_array($each_file["fifth"], ['doc', 'docx'])) {
                        $color = 'primary';
                    } elseif(in_array($each_file["fifth"], ['xls', 'xlsx', 'csv'])) {
                        $color = 'success';
                    } elseif(in_array($each_file["fifth"], ['txt', 'json', 'rtf', 'sql', 'css', 'php'])) {
                        $color = 'default';
                    }

                    // set the filename
                    $file_name = $each_file["second"];
                    $file_to_download = "{$docs_dir}{$file_name}";

                    // confirm that there is no existing file with the name.
                    if(is_file($file_to_download) && file_exists($file_to_download)) {
                        $file_to_download = "{$docs_dir}{$file_name}";
                    }

                    // create the document for download
                    copy("{$tmp_dir}{$each_file["first"]}", $file_to_download);
                    
                    // append to the list
                    $files_list[] = [
                        "unique_id" => $each_file["first"],
                        "name" => $each_file["second"],
                        "path" => "{$docs_dir}{$file_name}",
                        "type" => $each_file["fifth"],
                        "size" => $each_file["forth"],
                        "size_raw" => $n_FileSize_KB,
                        "is_deleted" => 0,
                        "record_id" => $record_id,
                        "datetime" => date("l, jS F Y h:i:sA"),
                        "favicon" => "{$this->favicon_array[$each_file["fifth"]]} fa-1x",
                        "color" => $color,
                        "uploaded_by" => "{$defaultUser->name} &bull; ".date("jS M Y"),
                        "uploaded_by_id" => $defaultUser->user_id
                    ];

                    // remove the file
                    unlink("{$tmp_dir}{$each_file["first"]}");
                }

                // unset the session
                $this->session->remove($module);
                
                // set the file size
                $n_FileSize = round(($totalFileSize / 1024), 2);

            } catch(\Exception $e) {}

        } else {
            // set the files list as the existing record which by default is an empty list
            $files_list = $existing_data;
        }

        // format the list
        $attachments = [
            "files" => $files_list,
            "files_count" => count($files_list),
            "raw_size_mb" => $n_FileSize,
            "files_size" => "{$n_FileSize}MB"
        ];

        return $attachments;

    }

    /**
     * List photos of a particular record
     * 
     * Loop through the recent 20 attachments uploaded, get the image files from the list and then return them.
     * 
     * @return Array
     */
    public function list_attachments(stdClass $params) {

        try {

            // filters to append
            $query = "1";
            $query .= isset($params->record_id) ? " AND a.record_id = '{$params->record_id}'" : "";
            $query .= isset($params->created_by) ? " AND a.created_by = '{$params->created_by}'" : "";
            $query .= isset($params->resource) ? " AND a.resource = '{$params->resource}'" : "";

            // specific file type
            $specific_type = isset($params->attachment_type) ? $this->stringToArray($params->attachment_type) : false;

            // query the database and return the results
            $stmt = $this->db->prepare("SELECT description FROM files_attachment a WHERE {$query} ORDER BY a.id DESC LIMIT {$params->limit}");
            $stmt->execute([]);

            // init
            $data = [];

            // append to the files list
            $count = 1;
            $files_list = [];
            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {

                // convert to json object
                $result->description = json_decode($result->description);

                // loop through the files list for this record
                foreach($result->description->files as $eachFile) {

                    // if the specific file types were parsed
                    if($specific_type) {
                        
                        // if the current file type is in the array list
                        if(in_array($eachFile->type, $specific_type)) {
                            $files_list[] = $eachFile;
                        }
                        
                    } else {
                        // append to the array list
                        $files_list[] = $eachFile;
                    }

                    if($count == $params->internal_limit) {
                        break;
                    }

                    $count++;
                }
                $data[] = $result;
            }

            // return the results
            return $files_list;

        } catch(PDOException $e) {
            return [];
        } 
    }

    /**
     * Load all files for a particular resource
     * 
     * After getting all the list, append the files into a single array list
     * 
     * @param String $resource
     * @param String $resource_id
     * @param String $search_term
     * 
     * @return Array
     */
    public function resource_attachments_list($resource, $resource_id, $search_term = null) {

        try {

            $search = !empty($search_term) ? " AND description LIKE '%{$search_term}%'" : "";
            
            $stmt = $this->db->prepare("SELECT description AS attachments, attachment_size FROM files_attachment WHERE resource = ? AND resource_id = ? {$search} ORDER BY id DESC");
            $stmt->execute([$resource, $resource_id]);

            $data = [];
            $size = 0;
            $count = 0;

            while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
                // convert to object
                $result->attachments = json_decode($result->attachments);

                // append only the rows where the description is not empty
                if(!empty($result->attachments->files)) {
                    $data[] = $result->attachments->files;
                    $size += $result->attachments->raw_size_mb;
                    $count += $result->attachments->files_count;
                }
            }

            $new_list = [];
            foreach($data as $value) {
                foreach($value as $k => $v) {
                    $new_list[] = $v;
                }
            }

            return [
                "files_list" => $new_list,
                "files_size" => round($size, 3),
                "files_count" => $count,
            ];

        } catch(PDOException $e) {}
    }
}