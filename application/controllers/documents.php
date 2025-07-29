<?php 

class Documents extends Myschoolgh {

	private $string_length;
	private $files_count = 5000;
	private $server_size = 1000;

	public function __construct() {
		parent::__construct();

		$this->string_length = (RANDOM_STRING + 3);
	}

	/**
	 * List Files and Directories
	 *
	 * @return Array
	 **/
	public function media_list(stdClass $params) {

		// make no parent query
		$params->no_parent_query = true;
		$params->type = "file";
		$params->columns = "a.id, a.type, a.name, a.description, a.content, a.created_by, a.upload_id, a.file_ref_id, a.state";

		// get the information
		$media_ref_ids = [];
		$attachments_list = [];
		$media_array_full_list = [];
		$media_list = $this->list($params)["data"]["file_list"] ?? [];

		// loop through the records list
		foreach($media_list as $key => $media) {
			$media_ref_ids[] = $media->upload_id;
		}

		// get the unique array list
		$media_ref_ids = array_unique($media_ref_ids);
		$total_uploads = count($media_ref_ids);

		// get the attachments information for the file_attachments table
		$files_attachments = $this->pushQuery("description, record_id", 
			"files_attachment", 
			"record_id IN {$this->inList($media_ref_ids)} ORDER BY id DESC LIMIT {$total_uploads}"
		);

		// loop through the attachments list
		foreach($files_attachments as $files){
			$file = json_decode($files->description);
			$attachments_list[$files->record_id] = $file->files ?? [];
		}

		return [
			"data" => [
				"files_list" => $media_list,
				"attachments_list" => $attachments_list
			]
		];

	}

	/**
	 * List Files and Directories
	 *
	 * @return Array
	 **/
	public function list(stdClass $params, &$current_level = 0) {

		try {

			global $defaultUser, $accessObject;

			// if the default user is not empty
			if(!empty($defaultUser)) {
				$params->userData = $defaultUser;
			}

			// limit to query
			$limit = $params->limit ?? $this->global_limit;

			// check if deep scan is parsed
			$userId = null;
			$levels = $params->level ?? 20;
			$isDeepScan = (bool) isset($params->deep_scan) && $params->deep_scan;
			$listTree = (bool) isset($params->list_tree) && $params->list_tree;

			// set the user name
			if(!$accessObject->hasAccess("update", "documents")) {
				$userId = !empty($defaultUser) ? $defaultUser->user_id : $params->userData->user_id;
			}

			// set the status
			$groupByQuery = null;
			$state = isset($params->state) ? $params->state : "Active";
			$noParentQuery = (bool) isset($params->no_parent_query);

			// parameters to use in filtering
			$filters = 1;
			$addQuery = ",a.item_id AS unique_id, u.name AS fullname, u.email, u.username,
				(
					SELECT b.name FROM documents b WHERE b.item_id = a.parent_id LIMIT 1
				) AS parent_name";

			// jump this query if the state is trash
			if(!in_array($state, ["Trash"]) && !$noParentQuery) {
				$filters .= !empty($params->parent_id) ? " AND a.parent_id = '{$params->parent_id}'" : (empty($params->unique_id) ? " AND a.parent_id IS NULL" : null);
			}

			// add more filters
			$filters .= !empty($params->unique_id) ? " AND a.item_id IN {$this->inList($params->unique_id)}" : null;
			$filters .= !empty($params->q) ? (" AND ((a.name LIKE '%{$params->q}%') ".($isDeepScan ? "OR (a.description LIKE '%{$params->q}%')" : null).")") : null;
			$filters .= !empty($params->clientId) ? " AND a.client_id = '{$params->clientId}'" : null;
			$filters .= !empty($params->type) ? " AND a.type = '{$params->type}'" : null;
			$filters .= !empty($userId) ? " AND a.created_by = '{$userId}'" : null;
			$filters .= " AND a.state IN {$this->inList($state)}";

			// if the unique_id is not empty then load the file information
			if(!empty($params->unique_id)) {
				$addQuery .= ", (SELECT b.description FROM files_attachment b WHERE b.resource='documents' AND b.record_id = a.upload_id ORDER BY b.id DESC LIMIT 1) AS attachment";
			}

			// set the columns to load
			$columns = isset($params->columns) ? $params->columns : "a.*";

			// load the information
			if($noParentQuery) {
				$groupByQuery = "ORDER BY a.id DESC";
				$addQuery =  "";
			}

			// perform the query
			$stmt = $this->db->prepare("SELECT {$columns} {$addQuery}
				FROM documents a LEFT JOIN users u ON u.item_id = a.created_by
				WHERE {$filters} {$groupByQuery} LIMIT {$limit}
			");
			$stmt->execute();

			// set the subquery object
			$subquery = (object) [];

			// if the client id has been parsed
			$subquery->clientId = $params->clientId;

			// set the files and folders list
			$group_data = [];

			$data = [];
			$count = 0;
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				// if isset
				if(isset($result->description)) {
					// clean the content
					$result->content = !empty($result->content) ? htmlspecialchars_decode($result->content) : null;
					$result->description = !empty($result->description) ? htmlspecialchars_decode($result->description) : null;
				}

				// if the attachment variable was parsed
				if(!empty($result->attachment)) {
					$result->attachment = json_decode($result->attachment, true)["files"] ?? [];
				}

				// isOwner
				$result->isOwner = (bool) $result->created_by == $params->userData->user_id;

				// if the item type is a directory
				if(($result->type == "directory") && $listTree) {

					// only query if the current level is lower than the level set
					if($levels > $current_level) {
						// set the parent id
						$subquery->list_tree = $listTree;
						$subquery->deep_scan = $isDeepScan;
						$subquery->level = $levels;
						$function = "list_sub";
						$subquery->userId = $userId;
						$subquery->columns = $columns;

						// if deep scan was parsed
						if($isDeepScan) {
							// using a deep scan
							$function = "list";

							// apply the the file type
							$subquery->type = $params->type ?? null;

							// apply the search term
							$subquery->q = $params->q ?? null;
						}
						$subquery->state = $state;
						$subquery->parent_id = $result->unique_id;

						// append the list of items
						$result->directory_tree = $this->{$function}($subquery, $current_level)["data"];
					}

					// increment the level for the loop
					$current_level++;
				}

				// format the name
				$result->name = str_ireplace("-", " ", $result->name);

				// append to the records list
				$data["{$result->type}_list"][$noParentQuery ? $result->file_ref_id : $count] = $result;
				$count++;
			}

			// if append summary was parsed
			if(!empty($params->append_summary)) {
				// get the system summary
				$summary = $this->pushQuery(
					"a.id,
						(
							SELECT COUNT(*) FROM documents b WHERE b.type='directory' AND b.client_id=a.client_id 
							AND b.state = 'Active' ".(!empty($userId) ? " AND b.created_by = '{$userId}'" : null)." 
							LIMIT {$this->files_count}
						) AS folders_count,
						(
							SELECT COUNT(*) FROM documents b WHERE b.type='file' AND b.client_id=a.client_id 
							AND b.state = 'Active' ".(!empty($userId) ? " AND b.created_by = '{$userId}'" : null)." 
							LIMIT {$this->files_count}
						) AS files_count,
						(
							SELECT COUNT(*) FROM documents b WHERE b.type='directory' AND b.client_id=a.client_id 
							AND b.state='Trash' ".(!empty($userId) ? " AND b.created_by = '{$userId}'" : null)." 
							LIMIT {$this->files_count}
						) AS trash_folders_count,
						(
							SELECT COUNT(*) FROM documents b WHERE b.type='file' AND b.client_id=a.client_id 
							AND b.state='Trash' ".(!empty($userId) ? " AND b.created_by = '{$userId}'" : null)." 
							LIMIT {$this->files_count}
						) AS trash_files_count,
						(
							SELECT ROUND(SUM(b.file_size), 2) FROM documents b WHERE b.type='file' AND b.client_id=a.client_id 
							AND b.state != 'Deleted' ".(!empty($userId) ? " AND b.created_by = '{$userId}'" : null)." 
							LIMIT {$this->files_count}
						) AS server_size,
						(
							SELECT ROUND(SUM(b.file_size), 2) FROM documents b WHERE b.type='file' AND b.client_id=a.client_id 
							AND b.state = 'Active' ".(!empty($userId) ? " AND b.created_by = '{$userId}'" : null)." 
							LIMIT {$this->files_count}
						) AS files_size,
						(
							SELECT ROUND(SUM(b.file_size), 2) FROM documents b WHERE b.type='file' AND b.client_id=a.client_id 
							AND b.state='Trash' ".(!empty($userId) ? " AND b.created_by = '{$userId}'" : null)." 
							LIMIT {$this->files_count}
						) AS trash_size
					",
					"clients_accounts a",
					"client_id='{$params->clientId}' LIMIT 1"
				);
				$data["summary"] = $summary[0];
			}

			return [
				"data" => $data,
				"code" => 200
			];

		} catch(PDOException $e) {
			return [];
		}

	}

	/**
	 * List Sub Files and Directories
	 *
	 * @return Array
	 **/
	public function list_sub(stdClass $params, &$current_level = 0) {

		try {

			global $defaultUser, $accessObject;

			// set the columns to load
			$columns = isset($params->columns) ? $params->columns : "a.*";

			// limit to query
			$limit = $this->global_limit;

			// check if deep scan is parsed
			$user_id = null;
			$levels = $params->level ?? 20;
			$isDeepScan = (bool) isset($params->deep_scan) && $params->deep_scan;
			$listTree = (bool) isset($params->list_tree) && $params->list_tree;

			// if the user has no permissions
			if(!$accessObject->hasAccess("update", "documents")) {
				$user_id = $defaultUser->user_id;
			}

			// set the status
			$state = isset($params->state) ? $params->state : "Active";

			// parameters to use in filtering
			$filters = 1;
			$filters .= !empty($params->parent_id) ? " AND a.parent_id = '{$params->parent_id}'" : (empty($params->unique_id) ? " AND a.parent_id IS NULL" : null);
			$filters .= !empty($params->unique_id) ? " AND a.item_id IN {$this->inList($params->unique_id)}" : null;
			$filters .= !empty($params->q) ? (" AND ((a.name LIKE '%{$params->q}%') ".($isDeepScan ? "OR (a.description LIKE '%{$params->q}%')" : null).")") : null;
			$filters .= !empty($params->clientId) ? " AND a.client_id = '{$params->clientId}'" : null;
			$filters .= !empty($params->type) ? " AND a.type = '{$params->type}'" : null;
			$filters .= " AND a.state IN {$this->inList($state)}";
			$filters .= !empty($user_id) ? " AND a.created_by = '{$user_id}'" : null;

			// perform the query
			$stmt = $this->db->prepare("SELECT {$columns}, a.item_id AS unique_id,
					u.name AS fullname, u.email, u.username,
					(
						SELECT b.name FROM documents b WHERE b.item_id = a.parent_id LIMIT 1
					) AS parent_name 
				FROM documents a LEFT JOIN users u ON u.item_id = a.created_by
				WHERE {$filters} LIMIT {$limit}"
			);
			$stmt->execute();

			// set the subquery object
			$subquery = (object) [
				"clientId" => $params->clientId
			];

			// set the files and folders list
			$group_data = [];

			$data = [];
			while($result = $stmt->fetch(PDO::FETCH_OBJ)) {
				// if isset
				if(isset($result->description)) {
					// clean the content
					$result->content = !empty($result->content) ? htmlspecialchars_decode($result->content) : null;
					$result->description = !empty($result->description) ? htmlspecialchars_decode($result->description) : null;
				}

				// isOwner
				$result->isOwner = (bool) $result->created_by == $defaultUser->user_id;

				// format the name
				$result->name = str_ireplace("-", " ", $result->name);
				
				// append to the records list
				$data["{$result->type}_list"][] = $result;
			}

			return [
				"data" => $data,
				"code" => 200
			];
		} catch(PDOException $e) {
			return $e->getMessage();
		}

	}

	/**
	 * Add and Update Folders
	 *
	 * Using the Object $params->request to confirm whether the record already exists or not
	 * 
	 * Load the just inserted/modified directory and return it as part of the response
	 * 
	 * @return Array
	 */
	public function folders(stdClass $params) {

		// if the request is to create a new document
		if($params->request === "save") {

			// create a new unique id for this file
			$unique_id = random_string("alnum", $this->string_length);

			// clean the description 
			$params->description = !empty($params->description) ? custom_clean(htmlspecialchars_decode($params->description)) : null;
			$params->description = !empty($params->description) ? htmlspecialchars($params->description) : null;

			// save the document into the database
			$this->_save("documents", [
				"client_id" => $params->clientId, "item_id" => $unique_id, "type" => "directory", "parent_id" => $params->unique_id ?? null,
				"name" => $params->name, "description" => $params->description, "created_by" => $params->userId
			]);

			// load the record
			$record = $this->pushQuery("*", "documents", "item_id='{$unique_id}' AND client_id='{$params->clientId}' LIMIT 1")[0];
			$record->description = !empty($record->description) ? htmlspecialchars_decode($record->description) : null;

			// return the success message
			return [
				"data" => "Folder successfully created.",
				"additional" => [
					"append_data" => [
						"container" => "div[data-element_type='folder']:last",
						"data" => format_directory_item($record)
					],
					"array_stream" => [
						"documents_array_list" => [
							$unique_id => $record
						]
					],
					"delete_divs" => [
						"div[class~='empty_div_container'][data-element_type='folder']"
					]
				]
			];
		}

		// if the request is to modify the record
		elseif($params->request === "modify") {

			// clean the description 
			if(!empty($params->description)) {
				$params->description = custom_clean(htmlspecialchars_decode($params->description));
				$params->description = htmlspecialchars($params->description);
			}

			// save the document into the database
			$this->_save("documents", 
				["name" => $params->name, "description" => ($params->description ?? null), "last_updated" => "now()"], 
				["client_id" => $params->clientId, "item_id" => $params->unique_id]
			);

			// load the record
			$record = $this->pushQuery("*", "documents", "item_id='{$params->unique_id}' AND client_id='{$params->clientId}' LIMIT 1")[0];
			$record->description = !empty($record->description) ? htmlspecialchars_decode($record->description) : null;

			// return the success message
			return [
				"data" => "Folder successfully modified.",
				"additional" => [
					"replace_data" => [
						"container" => "div[data-element_type='folder'][data-element_id='{$params->unique_id}']",
						"data" => format_directory_item($record, true, true)
					],
					"array_stream" => [
						"documents_array_list" => [
							$params->unique_id => $record
						]
					],
					"delete_divs" => [
						"div[class~='empty_div_container'][data-element_type='folder']"
					]
				]
			];

		}
	}

	/**
	 * Add and Update Files
	 *
	 * Using the Object $params->request to confirm whether the record already exists or not
	 * 
	 * Load the just inserted/modified directory and return it as part of the response
	 * 
	 * @return Array
	 */
	public function files(stdClass $params) {

		// if the request is to create a new document
		if($params->request === "save") {

			// ensure that the description is not empty
			if(empty($params->description)) {
				return ["code" => 400, "data" => "Sorry! The document content is required."];
			}

			// create a new unique id for this file
			$unique_id = random_string("alnum", $this->string_length);

			// clean the description 
			$params->description = !empty($params->description) ? custom_clean(htmlspecialchars_decode($params->description)) : null;
			$params->description = htmlspecialchars($params->description);

			// save the document into the database
			$this->_save("documents", [
				"client_id" => $params->clientId, "item_id" => $unique_id, 
				"type" => "file", "parent_id" => $params->unique_id ?? null,
				"name" => $params->name, "content" => ($params->description ?? null), 
				"description" => "This PDF Document was created online", "file_type" => "pdf",
				"created_by" => $params->userId, "mode" => "manual"
			]);

			// load the record
			$record = $this->pushQuery("*, item_id AS unique_id", "documents", "item_id='{$unique_id}' AND client_id='{$params->clientId}' LIMIT 1")[0];
			$record->description = !empty($record->description) ? htmlspecialchars_decode($record->description) : null;

			// set the favicon
		    $record->favicon = $this->favicon_array["pdf"] ?? "fa fa-file-alt";
		    
		    // set the color
		    $record->color = item_color("pdf");

			// return the success message
			return [
				"data" => "File successfully created.",
				"additional" => [
					"append_data" => [
						"container" => "div[data-element_type='file']:last",
						"data" => format_file_item($record)
					],
					"array_stream" => [
						"documents_array_list" => [
							$unique_id => $record
						]
					],
					"delete_divs" => [
						"div[class~='empty_div_container'][data-element_type='file']"
					]
				]
			];

		}

		// if the request is to modify the record
		elseif($params->request === "modify") {

			// clean the description 
			if(!empty($params->description)) {
				$params->description = !empty($params->description) ? custom_clean(htmlspecialchars_decode($params->description)) : null;
				$params->description = !empty($params->description) ? htmlspecialchars($params->description) : null;
			}

			// load the record
			$record = $this->pushQuery("mode", "documents", "item_id='{$params->unique_id}' AND client_id='{$params->clientId}' LIMIT 1");

			if(empty($record)) {
				return ["code" => 400, "data" => "Sorry! An invalid document id was submitted for processing"];
			}

			// if the document was created manually
			if($record[0]->mode === "manual") {
				// ensure that the description is not empty
				if(empty($params->description)) {
					return ["code" => 400, "data" => "Sorry! The document content is required."];
				}
				
				// save the document into the database
				$this->_save("documents", 
					["name" => $params->name, "content" => $params->description, "last_updated" => "now()"], 
					["client_id" => $params->clientId, "item_id" => $params->unique_id]
				);
			} else {
				// save the document into the database
				$this->_save("documents", 
					["name" => $params->name, "description" => ($params->description ?? null), "last_updated" => "now()"], 
					["client_id" => $params->clientId, "item_id" => $params->unique_id]
				);
			}

			// load the record
			$record = $this->pushQuery("*, item_id AS unique_id", "documents", "item_id='{$params->unique_id}' AND client_id='{$params->clientId}' LIMIT 1")[0];
			$record->description = !empty($record->description) ? htmlspecialchars_decode($record->description) : null;
			
			// set the favicon
		    $record->favicon = $this->favicon_array[$record->file_type] ?? "fa fa-file-alt";
		    
		    // set the color
		    $record->color = empty($record->file_type) ? "text-default" : item_color($record->file_type);

			// return the success message
			return [
				"data" => "File successfully modified.",
				"additional" => [
					"replace_data" => [
						"container" => "div[data-element_type='file'][data-element_id='{$params->unique_id}']",
						"data" => format_file_item($record, true)
					],
					"array_stream" => [
						"documents_array_list" => [
							$params->unique_id => $record
						]
					],
					"delete_divs" => [
						"div[class~='empty_div_container'][data-element_type='file']"
					]
				]
			];

		}
	}

	/**
	 * Add and Update Folders
	 * 
	 * Load the just uploaded files/documents and return it as part of the response
	 * 
	 * @return Array
	 */
	public function upload(stdClass $params) {

		// call the global variable for the user access information
		global $accessObject;

		// set the endpoint for the documents uploaded
		$unique_id = (isset($params->unique_id) && !empty($params->unique_id)) ? $params->unique_id : "root";
		$endpoint = "documents_{$unique_id}";
		$attachments_list = $this->session->{$endpoint};

		// confirm that the session is not empty
		if(empty($attachments_list)) {
			return ["data" => "Sorry! Please upload at least one file to proceed.", "code" => 400];
		}

		// create an object of the files class
		$filesObj = load_class("files", "controllers");

		// create a new unique_id
		$upload_id = random_string("alnum", $this->string_length);

		// get the ids of the inserted files list
		$document_ids = [];

		// loop through the files to upload list and insert a record in the documents table
		foreach($attachments_list as $file) {

			// create a new unique id for this file
			$file_size = str_ireplace(["KB"], [""], $file["forth"]);
			$document_id = random_string("alnum", RANDOM_STRING);
			$document_ids[] = $document_id;

			// save the document into the database
			$this->_save("documents", [
				"upload_id" => $upload_id, "client_id" => $params->clientId, "item_id" => $document_id, "type" => "file", 
				"parent_id" => $params->unique_id ?? null, "name" => $file["second"], "file_type" => $file["fifth"],
				"created_by" => $params->userId, "file_size" => $file_size, "file_ref_id" => $file["first"]
			]);
		}
		
		// prepare the attachment files to upload
        $attachments = $filesObj->prep_attachments($endpoint, $params->userId, $unique_id);

        // insert the files attachment
        $this->_save("files_attachment", [
			"resource" => "documents", "resource_id" => ($params->unique_id ?? $endpoint), "description" => json_encode($attachments), 
			"record_id" => $upload_id, "created_by" => $params->userId, "attachment_size" => $attachments["raw_size_mb"],
			"client_id" => $params->clientId
		]);

        // load the record
		$records = $this->pushQuery("*, item_id AS unique_id", "documents", "item_id IN {$this->inList($document_ids)} AND client_id='{$params->clientId}' AND created_by='{$params->userId}' LIMIT 50");

		// set the documents array list
		$documents_array = [];
		$documents_list = "";

		// loop through the documents list load
		foreach ($records as $key => $value) {

			// set the favicon
		    $value->favicon = $this->favicon_array[$value->file_type] ?? "fa fa-file-alt";

		    // set the color
		    $value->color = empty($value->file_type) ? "text-default" : item_color($value->file_type);

			// format the file display
			$documents_list .= format_file_item($value);

			// append to the record to return
			$documents_array[$value->unique_id] = $value;
		}

		// return the success message
		return [
			"data" => "Files were successfully uploaded.",
			"additional" => [
				"append_data" => [
					"container" => "div[data-element_type='file']:last",
					"data" => $documents_list
				],
				"array_stream" => [
					"documents_array_list" => $documents_array
				],
				"delete_divs" => [
					"div[class~='empty_div_container'][data-element_type='file']"
				]
			]
		];
	}

	/**
	 * Permanently Delete a file or directory recursively
	 * 
	 * @param String 		$params->document_id
	 *
	 * @return Array
	 **/
	public function delete(stdClass $params) {

		// call the global variable for the user access information
		global $accessObject;

		// convert the document ids to an array
		$params->document_id = $this->stringToArray($params->document_id);

		// get the document and its tree
		$param = (object) [
			"clientId" => $params->clientId,
			"unique_id" => $params->document_id,
			"limit" => 500,
			"state" => "Trash",
			"columns" => "a.name, a.item_id, a.created_by, a.type, a.parent_id, a.file_ref_id, a.upload_id",
			"list_tree" => true,
			"deep_scan" => true
		];
		$documents = $this->list($param)["data"];

		// if the record set is empty
		if(empty($documents)) {
			return ["data" => "Sorry! An invalid document id was submitted.", "code" => 400];
		}

		// if both the directory_list and file_list are empty
		if(!isset($documents["directory_list"]) && !isset($documents["file_list"])) {
			return ["data" => "Sorry! An invalid document id was submitted.", "code" => 400];
		}

		// if the item is a file
		if(!empty($documents["file_list"])) {

			// create new object
			$filesObj = load_class("files", "controllers");

			// loop through the files list
			foreach($documents["file_list"] as $document) {

				// only delete if the user is the owner
				if($document->isOwner) {

					// set the record id
					$record_set_id = "{$document->upload_id}_{$document->file_ref_id}";

					// delete the file from the system
					$filesObj->remove_existing_file($record_set_id, "no");

					// change the status of the file
					$this->_save("documents", 
						["last_updated" => "now()", "status" => 0, "state" => "Deleted"], 
						["client_id" => $params->clientId, "item_id" => $document->unique_id, "created_by" => $params->userId]
					);
				}
				
			}
		}

		return ["data" => "File(s) successfully deleted.", "code" => 200, "additional" => ["href" => $this->session->user_current_url]];

	}

	/**
	 * Move a file or directory recursively to Trash
	 * 
	 * @param String 		$params->document_id
	 *
	 * @return Array
	 **/
	public function trash(stdClass $params) {

		// call the global variable for the user access information
		global $accessObject;

		// convert the document ids to an array
		$params->document_id = $this->stringToArray($params->document_id);

		// get the document and its tree
		$param = (object) [
			"columns" => "a.name, a.item_id, a.created_by, a.type",
			"state" => ["Active"],
			"clientId" => $params->clientId,
			"unique_id" => $params->document_id,
			"limit" => count($params->document_id)
		];
		$documents = $this->list($param)["data"];

		// if the record set is empty
		if(empty($documents)) {
			return ["data" => "Sorry! An invalid document id was submitted.", "code" => 400];
		}

		// if both the directory_list and file_list are empty
		if(!isset($documents["directory_list"]) && !isset($documents["file_list"])) {
			return ["data" => "Sorry! An invalid document id was submitted.", "code" => 400];
		}

		// if the item is a file
		if(!empty($documents["file_list"])) {

			// loop through the documents list
			foreach($documents["file_list"] as $document) {

				// change the status of the file
				$this->_save("documents", 
					["last_updated" => "now()", "status" => 2, "state" => "Trash"], 
					["client_id" => $params->clientId, "item_id" => $document->unique_id, "created_by" => $params->userId]
				);
			}
		}

		// if the item is a folder
		if(!empty($documents["directory_list"])) {
			
			// loop through the documents list
			foreach($documents["directory_list"] as $document) {

				// change the status of the file
				$this->_save("documents", 
					["last_updated" => "now()", "status" => 2, "state" => "Trash"], 
					["client_id" => $params->clientId, "item_id" => $document->unique_id, "created_by" => $params->userId]
				);

			}

		}

		return ["data" => "Selected file(s) successfully moved to trash.", "code" => 200, "additional" => ["href" => $this->session->user_current_url]];
	}

	/**
	 * Restore the trashed file / directory
	 * 
	 * @param String 		$params->document_id
	 *
	 * @return Array
	 **/
	public function restore(stdClass $params) {

		// call the global variable for the user access information
		global $accessObject;

		// convert the document ids to an array
		$params->document_id = $this->stringToArray($params->document_id);

		// get the document and its tree
		$param = (object) [
			"clientId" => $params->clientId,
			"unique_id" => $params->document_id,
			"limit" => count($params->document_id),
			"columns" => "a.name, a.item_id, a.created_by, a.type",
			"state" => "Trash",
		];
		$documents = $this->list($param)["data"];

		// if the record set is empty
		if(empty($documents)) {
			return ["data" => "Sorry! An invalid document id was submitted.", "code" => 400];
		}

		// if both the directory_list and file_list are empty
		if(!isset($documents["directory_list"]) && !isset($documents["file_list"])) {
			return ["data" => "Sorry! An invalid document id was submitted.", "code" => 400];
		}

		// if the item is a file
		if(!empty($documents["file_list"])) {

			// loop through the documents list
			foreach($documents["file_list"] as $document) {

				// change the status of the file
				$this->_save("documents", 
					["last_updated" => "now()", "status" => 1, "state" => "Active"], 
					["client_id" => $params->clientId, "item_id" => $document->unique_id, "created_by" => $params->userId]
				);

			}

		}

		// if the item is a folder
		if(!empty($documents["directory_list"])) {

			// loop through the documents list
			foreach($documents["directory_list"] as $document) {

				// change the status of the file
				$this->_save("documents", 
					["last_updated" => "now()", "status" => 1, "state" => "Active"], 
					["client_id" => $params->clientId, "item_id" => $document->unique_id, "created_by" => $params->userId]
				);

			}

		}

		return ["data" => "Selected file(s) successfully moved to trash.", "code" => 200, "additional" => ["href" => $this->session->user_current_url]];

	}

	/**
	 * Restore all the trashed file / directory
	 *
	 * @return Array
	 **/
	public function restore_all(stdClass $params) {

		// call the global variable for the user access information
		global $accessObject;

		// change the status of the files and documents in the trash
		$this->_save("documents", 
			["last_updated" => "now()", "status" => 1, "state" => "Active"], 
			["client_id" => $params->clientId, "state" => "Trash", "created_by" => $params->userId], 100
		);

		return ["data" => "All documents(s) successfully restored.", "code" => 200, "additional" => ["href" => $this->session->user_current_url]];

	}

	/**
	 * Preview a File
	 *
	 * @return Array
	 **/
	public function preview(stdClass $params) {

		try {
			
			// set the site_url
			$file = explode("_", $params->file);

			// if the file was not found
			if(!isset($file[1])) {
				return ["code" => 400, "data" => "File was not found"];
			}

	       	// get the record id
	        $record_id = $file[0];
	        
	        // get the record information
	        $attachment_record =  $this->columnValue("resource, client_id, resource_id, description", "files_attachment", "record_id='{$record_id}'");
	        
	        // if no record found
	        if(empty($attachment_record)) {
	            return ["code" => 400, "data" => "File was not found"];
	        }

	        // set the file to download
	        $file_to_download = $file[1];

            // convert the string into an object
            $file_ref_id = xss_clean($file[1]);
            $file_list = json_decode($attachment_record->description);

            // found
            $found = false;

            // loop through each file
            foreach($file_list->files as $key => $eachFile) {
                
                // check if the id matches what has been parsed in the url
                if($eachFile->unique_id == $file_to_download) {
                    $file_to_download = $eachFile->path;
                    $found = true;
                    break;
                }
            }

            return $file_to_download;


		} catch(PDOException $e) {}

	}

}