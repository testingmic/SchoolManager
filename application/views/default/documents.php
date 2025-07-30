<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// get the global variables for the page
global $myClass, $defaultUser, $clientFeatures, $isPayableStaff, $accessObject;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$post_data = $_POST;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Documents Manager";
$response->title = $pageTitle;

// If the user is not a teacher, employee, accountant or admin then end the request
if(!$isPayableStaff) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

// include the documents script
$response->scripts = ["assets/js/documents.js"];

// format the documents files list
$breadcrumb_items = null;
$documents_list = null;
$documents_array_list = [];
$last_key = "not_initialized";

// set the item unique id
$unique_id = (confirm_url_id(2) && in_array($SITEURL[1], ["trash", "folders"])) ? $SITEURL[2] : null;
$isTrash = (bool) confirm_url_id(1, "trash");
$urlLink = $isTrash ? "trash" : "folders";
	
// confirm that the school has the documents manager feature enabled
if(!in_array("documents_manager", $clientFeatures)) {

	// permission denied
	$response->html = page_not_found("feature_disabled");

} else {
	// permission check
	$canUpdate = $accessObject->hasAccess("update", "documents");

	// set the query object parameter
	$param = (object)[
		"unique_id" => $unique_id ?? null,
		"parent_id" => $parent_id ?? null,
	    "list_tree" => (bool) $unique_id,
	    "deep_scan" => true,
	    "clientId" => $clientId,
	    "state" => $isTrash ? "Trash" : "Active"
	];

	if(!$canUpdate) {
		$param->user_id = $defaultUser->user_id;
	}

	if(empty($unique_id)) {
		$param->append_summary = true;
	}

	// load the files list
	$documents_array = load_class("documents", "controllers")->list($param)["data"];

	// set the session data to null if the $unique_id is empty
	if(empty($unique_id)) {
		$session->breadcrumb_items = $post_data = [];
	}

	// get the breadcrums post data
	$break = false;
	$document_details = '<div class="text-center"><em>Summary of document details appears here.</em></div>';
	$breadcrumb_array_new = (object) [];
	$breadcrumb_array = !empty($post_data) ? $post_data : $session->breadcrumb_items;

	// post data query
	if(!empty($breadcrumb_array)) {
		// reset the item
		$breadcrumb_array_new = [];

		// set the url in a session
		$session->breadcrumb_items = $breadcrumb_array;

		// count array data
		$breadcrumbs_count = count($breadcrumb_array);

		// loop through the post data
		foreach($breadcrumb_array as $kkey => $data) {

			// if the post data is an array
			if(is_array($data)) {

				// loop through the breadcrumb list
				foreach ($data as $key => $value) {
					// set the last breadcrumb key
					$last_key = $key;

					// set the breadcrumbs
					$breadcrumb_items .= "<span title=\"{$value}\" onclick=\"return jump_to_folder('{$key}');\" class=\"breadcrumb-item\">{$value}</span>";

					// append the array keys
					$breadcrumb_array_new[$kkey][$key] = $value;

					// if the current key is equal to the one parsed
					// then break the loop
					if($key == $unique_id) {
						$break = true;
						break;
					}
				}
			}
			if($break) {
				break;
			}
		}

		// set the url in a session
		$session->breadcrumb_items = $breadcrumb_array_new;

	}

	// init
	$document_summary = [];

	// if an array was parsed
	if(is_array($documents_array)) {

		// init set variables
		$folders_list = "<div class='folders_list_container'><div class='row'>";
		$files_list = "<div class='files_list_container'><div class='row'>";

		// load this section if the unique id has not been set
		if(empty($unique_id)) {

			// init header
			$folders_list .= "<div class='col-lg-12 pl-2 pb-1 font-bold mb-3 mt-3 border-bottom folders_list_header'>Folders</div>";

			// if there are folders
			if(isset($documents_array["directory_list"])) {
				
				// loop through the array list
				foreach ($documents_array["directory_list"] as $key => $value) {
					$documents_array_list[$value->unique_id] = $value;
					$folders_list .= format_directory_item($value, !$isTrash);
				}

			} else {
				$files_list .= "
				<div data-element_type='folder' class='text-center empty_div_container col-lg-12 p-2 text-danger'>
					This folder is empty.
				</div><div data-element_type='folder'></div>";
			}

			// init header
			$files_list .= "<div class='col-lg-12 pl-2 pb-1 font-bold mb-3 mt-3 border-bottom files_list_header'>Files</div>";

			// if there are files
			if(isset($documents_array["file_list"])) {
				
				// loop through the array list
				foreach ($documents_array["file_list"] as $key => $value) {
					// set the favicon
				    $value->favicon = $myClass->favicon_array[$value->file_type] ?? "fa fa-file-alt";
				    // set the color
				    $value->color = empty($value->file_type) ? "text-default" : item_color($value->file_type);

				    // append the data to the array list
					$documents_array_list[$value->unique_id] = $value;
					$files_list .= format_file_item($value, false, $isTrash);
				}
			} else {
				$files_list .= "
				<div data-element_type='file' class='text-center empty_div_container col-lg-12 p-2 text-danger'>
					This folder is empty.
				</div><div data-element_type='file'></div>";
			}

		} else {

			// get the document summary
			if(isset($documents_array["directory_list"]) && !empty($documents_array["directory_list"])) {

				// get the document details
				$document = $documents_array["directory_list"][0];

				// if the document list was parsed
				if(isset($document->directory_tree)) {

					// init header
					$folders_list .= "<div class='col-lg-12 pl-2 pb-1 font-bold mb-3 mt-3 border-bottom folders_list_header'>Folders</div>";

					// if folders are in the
					if(isset($document->directory_tree["directory_list"])) {

						// loop through the directory list
						foreach ($document->directory_tree["directory_list"] as $key => $value) {
							$documents_array_list[$value->unique_id] = $value;
							$folders_list .= format_directory_item($value, !$isTrash);
						}
					} else {
						$files_list .= "
						<div data-element_type='folder' class='text-center empty_div_container col-lg-12 p-2 text-danger'>
							This folder is empty.
						</div><div data-element_type='folder'></div>";
					}

					// init header
					$files_list .= "<div class='col-lg-12 pl-2 pb-1 font-bold mb-3 mt-3 border-bottom files_list_header'>Files</div>";

					// if there are files
					if(isset($document->directory_tree["file_list"])) {

						// loop through the array list
						foreach ($document->directory_tree["file_list"] as $key => $value) {
							// set the favicon
						    $value->favicon = $myClass->favicon_array[$value->file_type] ?? "fa fa-file-alt";
						    // set the color
						    $value->color = empty($value->file_type) ? "text-default" : item_color($value->file_type);

						    // append the data to the array list
							$documents_array_list[$value->unique_id] = $value;
							$files_list .= format_file_item($value, false, $isTrash);
						}

					} else {
						$files_list .= "
						<div data-element_type='file' class='text-center empty_div_container col-lg-12 p-2 text-danger'>
							This folder is empty.
						</div><div data-element_type='file'></div>";
					}

				}

				// get the document summary
				$document_summary = document_summary($documents_array);

				// set the document details
				$document_details = "
					<div class=\"mb-3\">
						<label class=\"pb-0 text-primary mb-0\">Folder Name:</label>
						<div class=\"font-20\">{$document->name}</div>
					</div>
					<div class=\"mb-3\">
						<label class=\"pb-0 text-primary mb-0\">Description:</label>
						<div class=\"font-15\">{$document->description}</div>
					</div>
					<div class=\"mb-3\">
						<label class=\"pb-0 text-primary mb-0\">Created By:</label>
						<div class=\"font-15\">{$document->fullname}</div>
					</div>
					<div class=\"mb-3\">
						<label class=\"pb-0 text-primary mb-0\">Date Created:</label>
						<div class=\"font-15\">{$document->date_created}</div>
					</div>
					<div class=\"mb-3\">
						<label class=\"pb-0 text-primary mb-0\">Last Updated:</label>
						<div class=\"font-15\">{$document->last_updated}</div>
					</div>
					<div class='text-success font-15 font-bold mb-2 border-bottom'>Document Summary</div>
					<div>
						<div class='mb-1'><span class='font-bold'>Files Count: </span><span data-summary='files_count' class='float-right'>{$document_summary["summary"]["files_count"]}</span></div>
						<div class='mb-1'><span class='font-bold'>Folders Count: </span><span data-summary='folders_count' class='float-right'>{$document_summary["summary"]["folder_count"]}</span></div>
						<div class='mb-1'><span class='font-bold'>Last Updated: </span><span data-summary='last_updated' class='float-right'>{$document_summary["summary"]["last_updated"]}</span></div>
					</div>
				";

			}

		}

		$folders_list .= '</div></div>';
		$files_list .= '</div></div>';

	}

	// root document summary information
	if(isset($documents_array["summary"])) {
		// get the summary
		$summary = $documents_array["summary"];

		// set the document details
		$document_details = "
			<div class='text-success font-15 font-bold mb-2 border-bottom'>".($isTrash ? "Trash" : "Server")." Summary</div>
			<div>
				".(!$isTrash ?
					"<div class='mb-2'><span class='font-bold'>Folders Count: </span><span data-summary='folders_count' class='float-right'>{$summary->folders_count}</span></div>
					<div class='mb-2'><span class='font-bold'>Files Count: </span><span data-summary='files_count' class='float-right'>{$summary->files_count}</span></div>
					<div class='mb-2'>
						<span class='font-bold'>Overall Server Size: </span>
						<span class='float-right' data-summary='server_size'>".file_size_convert(round($summary->server_size * 1024))."</span>
					</div>
					<div class='mb-3 border-bottom pb-3'>
						<span class='font-bold'>Files Size: </span>
						<span class='float-right' data-summary='files_size'>".file_size_convert(round($summary->files_size * 1024))."</span>
					</div>" : null
				)."
				<div class='mb-2'>
					<span class='font-bold'>Trash Folders Count: </span>
					<span data-summary='".(!$isTrash ? "trash_folders_count" : "folders_count")."' class='float-right'>
						{$summary->trash_folders_count}
					</span>
					</div>
				<div class='mb-2'>
					<span class='font-bold'>Trash Files Count: </span>
					<span data-summary='".(!$isTrash ? "trash_files_count" : "files_count")."' class='float-right'>
						{$summary->trash_files_count}
					</span>
				</div>
				<div class='mb-2'>
					<span class='font-bold'>Trash Size: </span>
					<span class='float-right' data-summary='".(!$isTrash ? "trash_size" : "files_size")."'>".file_size_convert(round($summary->trash_size * 1024))."</span>
				</div>
			</div>
		";
	}

	// parse the data via javascript
	$response->array_stream["documents_array"] = $documents_array["directory_list"] ?? [];
	$response->array_stream["documents_array_list"] = $documents_array_list;
	$response->array_stream["replace_array_value"]["document_breadcrumbs"] = $breadcrumb_array_new;

	$response->html = '
	    <section class="section document-wrapper">
	        <div class="section-header">
	            <h1><i class="fa fa-folder"></i> '.$pageTitle.'</h1>
	            <div class="section-header-breadcrumb">
	                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
	                <div class="breadcrumb-item">'.$pageTitle.'</div>
	            </div>
	        </div>
	        <div class="row">
	            <div class="col-12 col-sm-12 col-md-3">
	            	<div class="mb-2 d-flex justify-content-between">
	            		'.(!$isTrash && $accessObject->hasAccess("add", "documents") ? 
		            		'<div>
		            			<input type="hidden" hidden name="current_directory_id" value="'.$unique_id.'" disabled>
		            			<input type="hidden" hidden name="drive_url_link" value="'.$urlLink.'" disabled>
		            			<div class="btn-group dropdown d-inline">
									<button type="button" class="btn btn-sm btn-outline-info btn-icon-text dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Create New
									</button>
									<div class="dropdown-menu" style="width:160px" data-function="add-item-module">
										<a href="#" onclick="return load_quick_form(\'document_create_folder\',\''.$unique_id.'\');" class="dropdown-item btn-sm" type="button"><i class="fa fa-folder"></i> New Folder</a>
							            <a href="#" onclick="return load_quick_form(\'document_create_file\',\''.$unique_id.'\');" class="dropdown-item btn-sm" type="button"><i class="fa text-danger fa-file-pdf"></i> PDF Document</a>
							            <a href="#" onclick="return load_quick_form(\'document_upload_files\',\''.$unique_id.'\');" class="dropdown-item btn-sm" type="button"><i class="fa text-primary fa-upload"></i> Upload Files</a>
								    </div>
							    </div>
		            		</div>': null
	            		).'
	            		<div><button onclick="return load(\'documents/'.($isTrash ? "folders" : "trash").'\');" title="Go to '.($isTrash ? "Drive" : "Trash").'" class="btn btn-sm btn-outline-'.($isTrash ? "success" : "danger").'"><i class="fa fa-folder" aria-hidden="true"></i> Go to '.($isTrash ? "Drive" : "Trash").'</button></div>
	            	</div>
	                <div class="card">
	                	<div class="card-header pl-2 mb-0 pb-0">
	                		<span title="Directory Details" class="breadcrumb-item pb-0">Directory Details</span>
	                	</div>
	                    <div class="card-body p-2">
	                    	<div class="directory_summary">'.$document_details.'</div>
	                    	<div class="selected_object_details">
	                    		
	                    	</div>
	                    </div>
	                </div>
	            </div>
	            <div class="col-12 col-sm-12 col-md-9">
	                <div class="card documents">
	                	<div class="card-header pl-2 mb-0 pb-2">
	                		<div class="d-flex justify-content-between width-per-100">
		                		<div>
		                			<span title="'.(!$isTrash ? 'My Drive' : 'Trash Bin').'" '.(!empty($unique_id) ? 'onclick="return load_home(\'documents\');"' : null).' class="breadcrumb-item">'.(!$isTrash ? 'My Drive' : 'Trash Bin').'</span>
		                			'.$breadcrumb_items.'
		                		</div>
		                		'.(!empty($unique_id) ? '
		                			<div align="right">
			                			<span title="Go Back" onclick="return go_back(\''.$last_key.'\');" class="back-arrow"><i class="fa fa-arrow-left"></i></span>
			                		</div>' : null
			                	).'
			                	'.($isTrash && !empty($documents_array_list) ? 
			            			'<div><button onclick="return restore_all();" class="btn btn-outline-success"><i class="fa fa-reply-all"></i> Restore All</button></div>' : null
			            		).'
		                	</div>
	                	</div>
	                    <div class="card-body pt-2 pb-4">
	                		'.$folders_list.'
	                		'.$files_list.'
	                    </div>
	                </div>
	            </div>
	        </div>
	    </section>
		<div class="document-manager">
			<div id="contextMenu" class="context-menu" style="display:none">
				<input type="hidden" value="'.$unique_id.'" name="selected_document_id" disabled="disabled">
				<input type="hidden" value="folder" name="selected_document_type" disabled="disabled">
		    	<ul class="menu list-unstyled">
		    		'.(
		    			$isTrash ? 
		    				'<li class="restore"><a onclick="return document_action(\'restore\');" href="#"><i class="fa fa-reply-all" aria-hidden="true"></i> <strong>Restore</strong></a></li>' : 
		    				'<li class="open"><a onclick="return document_action(\'open\');" href="#"><i class="fa fa-bezier-curve" aria-hidden="true"></i> <strong>Open</strong></a></li>'
		    		).'
		    		'.(!$isTrash ?
		    			'<li class="view"><a onclick="return document_action(\'view\');" href="#"><i class="fa fa-eye" aria-hidden="true"></i> View Document</a></li>
		    			<li hidden class="modify"><a onclick="return document_action(\'modify\');" href="#"><i class="fa fa-edit" aria-hidden="true"></i> Modify</a></li>
		    			<li hidden class="copy"><a onclick="return document_action(\'copy\');" href="#"><i class="fa fa-copy" aria-hidden="true"></i> Copy To</a></li>
		    			<li class="move"><a onclick="return document_action(\'move\');" href="#"><i class="fa fa-paste" aria-hidden="true"></i> Move To</a></li>' : null
		    		).'
		    		<li class="download"><a onclick="return document_action(\'download\');" href="#"><i class="fa fa-download" aria-hidden="true"></i> Download</a></li>
		    		'.($accessObject->hasAccess("delete", "documents") ?
		    			'<li class="'.($isTrash ? "delete" : "trash").'"><a onclick="return document_action(\''.($isTrash ? "delete" : "trash").'\');" href="#" class="text-danger"><i class="fa fa-trash" aria-hidden="true"></i> '.($isTrash ? "Delete" : "Move to Trash").'</a></li>' : null
		    		).'
		    	</ul>
		    </div>
		</div>';
}
// print out the response
echo json_encode($response);
?>