var details_container = $(`div[class="selected_object_details"]`),
	url_link = $(`section[class~="document-wrapper"] input[name="drive_url_link"]`).val();

var reset_panel = () => {
	details_container.html(``);
	$(`div[class="context-menu"]`).css({display: "none"});
	$(`div[class~="directory_summary"]`).removeClass("hidden");
	$(`div[class="upload-overlay-cover"]`).css("display", "none");
	$(`div[data-parameter="document"], div[data-parameter="file"]`).removeClass("highlighted");
}

$(document).keyup(function(evt) {
	if(evt.key === "Escape") {
		reset_panel();
	}
});

var documents_summary = () => {
	let files_count = $(`div[class="files_list_container"] div[data-element_type="file"][data-element_id]`).length,
		folders_count = $(`div[class="folders_list_container"] div[data-element_type="folder"][data-element_id]`).length;

	$(`[data-summary='folders_count']`).html(folders_count);
	$(`[data-summary='files_count']`).html(files_count);

	reset_panel();

}

var load_document = (unique_id) => {
	keys_count = Object.keys($.array_stream["document_breadcrumbs"]).length;

	let _document = $.array_stream["documents_array_list"];
	let idocument = _document[unique_id];

	$.array_stream["document_breadcrumbs"][(keys_count+1)] = {
		[unique_id]: idocument.name
	};
	$.form_data = $.array_stream["document_breadcrumbs"];
	load(`documents/${url_link}/${unique_id}`);
}

var document_download = (_document_id) => {
	let _document = $.array_stream["documents_array_list"];
	let idocument = _document[_document_id];
	window.open(`${baseUrl}download?file_id=${idocument.upload_id}&file_uid=${idocument.file_ref_id}&ref=docs`);
}

var restore_all = () => {
	swal({
        title: "Restore All Documents",
        text: `Do you wish to restore all trashed files and documents?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
    	if(proceed) {
    		$(`div[class='item_loader']`).html(`
		    	<div class='form-content-loader' style='display: flex; position: absolute'>
	                <div class='offline-content text-center'>
	                    <p><i class='fa fa-spin fa-spinner fa-3x'></i></p>
	                </div>
	            </div>`);
	    	$.post(`${baseUrl}api/documents/restore_all`).then((response) => {
	    		swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                $(`div[class='item_loader']`).html(``);
                if(response.code == 200) {
			    	$(`div[data-element_type='file'], div[data-element_type='folder']`).remove();

					if(!$(`div[class="files_list_container"] div[data-element_type="files"]`).length) {
						if(!$(`div[data-element_type="file"][class~="empty_div_container"]`).length) {
							$(`div[class="files_list_container"] div[class~="files_list_header"]`).after(`<div data-element_type="file" class="text-center empty_div_container col-lg-12 p-2 text-danger">This folder is empty.</div>`);
						}
					}

					if(!$(`div[class="folders_list_container"] div[data-element_type="folder"]`).length) {
						if(!$(`div[data-element_type="folder"][class~="empty_div_container"]`).length) {
							$(`div[class="folders_list_container"] div[class~="folders_list_header"]`).after(`<div data-element_type="folder" class="text-center empty_div_container col-lg-12 p-2 text-danger">This folder is empty.</div>`);
						}
					}
            	}
	    	}).catch(() => {
	    		$(`div[class='item_loader']`).html(``);
	    	});
    	}
    });
}

var document_action = (action) => {

	let _document_id = $(`div[class="document-manager"] input[name="selected_document_id"]`).val();
	$(`div[class="context-menu"]`).css({display: "none"});

	let highlighted_arrays = new Array(),
		highlighted_ids = "",
		highlighted = 0,
		document_type = "",
		unique_id = "",
		file_name = "";

	$.each($(`div[data-parameter="file"][class~="highlighted"]`), function(ii, ee) {
		unique_id = $(this).attr("data-parameter_url");
		highlighted_ids += unique_id + ",";
		highlighted_arrays.push(unique_id);
		highlighted++;
	});

	$.each($(`div[data-parameter="document"][class~="highlighted"]`), function(ii, ee) {
		unique_id = $(this).attr("data-parameter_url");
		highlighted_ids += $(this).attr("data-parameter_url") + ",";
		highlighted_arrays.push(unique_id);
		highlighted++;
	});

	if(highlighted < 2) {

		let _document = $.array_stream["documents_array_list"];
		let idocument = _document[_document_id];

		if(idocument === undefined) {
			notify("Sorry! The file could not be located.");
			return false;
		}

		document_type = idocument.type;
		file_name = idocument.name;
		_module = (idocument.type == "directory") ? "document_update_folder" : "document_update_file";

	} else {
		_document_id = highlighted_ids;
		document_type = "Selected Files";
		file_name = "Multiple Files";
	}

	if($.inArray(action, ["trash", "delete", "restore"]) !== -1) {

		let _title = (action === "trash") ? `Move '${file_name}' to Trash` : ((action === "delete") ? `Permanently Delete '${file_name}'` : `Restore '${file_name}'`),
			_message = (action === "trash") ? `Are you sure you want to move this ${document_type} into Trash? You can move it back to your drive when necessary.` :
				((action === "delete") ? `Are you sure you want to permanently delete ${file_name}? You cannot reverse this action once confirmed.` : 
					`Are you sure you want restore this document?`)

		swal({
	        title: _title,
	        text: `${_message} Do you wish to proceed?`,
	        icon: 'warning',
	        buttons: true,
	        dangerMode: true,
	    }).then((proceed) => {
	    	if(proceed) {
	    		highlighted_arrays.forEach((value, key) => {
		    		$(`div[data-parameter_url='${value}'] div[class='item_loader']`).html(`
				    	<div class='form-content-loader' style='display: flex; position: absolute'>
			                <div class='offline-content text-center'>
			                    <p><i class='fa fa-spin fa-spinner fa-3x'></i></p>
			                </div>
			            </div>`);
	    		});
		    	$.post(`${baseUrl}api/documents/${action}`, {document_id: _document_id}).then((response) => {
		    		swal({
	                    text: response.data.result,
	                    icon: responseCode(response.code),
	                });
	                if(response.code == 200) {
		                highlighted_arrays.forEach((value, key) => {
		                	$(`div[data-parameter_url='${value}'] div[class='item_loader']`).html(``);
				    		$(`div[data-element_id='${value}']`).remove();
						});

						if(!$(`div[class="files_list_container"] div[data-element_type="files"][data-element_id]`).length) {
							if(!$(`div[data-element_type="file"][class~="empty_div_container"]`).length) {
								$(`div[class="files_list_container"] div[class~="files_list_header"]`).after(`<div data-element_type="file" class="text-center empty_div_container col-lg-12 p-2 text-danger">This folder is empty.</div>`);
							}
						}

						if(!$(`div[class="folders_list_container"] div[data-element_type="folder"][data-element_id]`).length) {
							if(!$(`div[data-element_type="folder"][class~="empty_div_container"]`).length) {
								$(`div[class="folders_list_container"] div[class~="folders_list_header"]`).after(`<div data-element_type="folder" class="text-center empty_div_container col-lg-12 p-2 text-danger">This folder is empty.</div>`);
							}
						}
						documents_summary();
						setTimeout(() => {
							// loadPage(r0esponse.data.additional.href);
						}, 1000);
	            	}
		    	}).catch(() => {
		    		highlighted_arrays.forEach((value, key) => {
		    			$(`div[data-parameter_url='${value}'] div[class='item_loader']`).html(``);
		    		});
		    	});
	    	}
	    });
	}
	else if(action === "modify") {
		load_quick_form(`${_module}`, _document_id);
	}
	else if(action === "open") {
		load_document(_document_id, idocument.name);
	}
	else if(action === "view") {
		load(`document/${_document_id}`);
	}
	else if(action === "download") {
		document_download(_document_id);
	}
	else if($.inArray(action, ["copy", "move"]) !== -1) {
		_module = (action == "copy") ? "document_copy_command" : "document_move_command";

		if(highlighted > 1) {
			_module = (action == "copy") ? "multiple_document_copy" : "multiple_document_move";
			_document_id = highlighted_ids;
		}

		load_quick_form(`${_module}`, _document_id);
	}
	
}

var load_document_details = (unique_id) => {
	let _document = $.array_stream["documents_array_list"],
		t_document = ``;

	$(`div[class~="directory_summary"]`).removeClass("hidden");

	if($.array_stream["documents_array_list"] !== undefined) {
		idocument = _document[unique_id] !== undefined ? _document[unique_id] : ``;

		if(idocument) {
			$(`div[class~="directory_summary"]`).addClass("hidden");
			details_container.html(`
				<div class="mb-3">
					<label class="pb-0 mb-0">${idocument.type == "file" ? "Document" : "Folder"} Name:</label>
					<div class="font-20">${idocument.name}</div>
				</div>
				${idocument.description !== null ? 
					`<div class="mb-3">
						<label class="pb-0 text-primary mb-0">Description:</label>
						<div class="font-17">${idocument.description}</div>
					</div>` : ""}
				${idocument.type == "file" ? `
				<div class="mb-3">
					<label class="pb-0 text-primary mb-0">File Size:</label>
					<div class="font-17">${idocument.file_size}KB</div>
				</div>
				<div class="mb-3">
					<label class="pb-0 text-primary mb-0">Document Type:</label>
					<div class="font-17">${idocument.file_type}</div>
				</div>
				` : ""}
				<div class="mb-3">
					<label class="pb-0 text-primary mb-0">Created By:</label>
					<div class="font-17">${idocument.fullname}</div>
				</div>
				<div class="mb-3">
					<label class="pb-0 text-primary mb-0">Date Created:</label>
					<div class="font-17">${idocument.date_created}</div>
				</div>
				<div class="mb-3">
					<label class="pb-0 text-primary mb-0">Last Updated:</label>
					<div class="font-17">${idocument.last_updated}</div>
				</div>
				${idocument.type == "file" && (idocument.file_ref_id !== null) ? `
				<div class="mb-3 border-top pt-3">
					<span class="float-left">
						<button onclick="return load('document/${idocument.item_id}');" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i> View File</button>
					</span>
					<span class="font-17 float-right">
						<a href="${baseUrl}download?file_id=${idocument.upload_id}&file_uid=${idocument.file_ref_id}" target="_blank" class="btn btn-sm btn-outline-success"><i class="fa fa-download"></i> Download File</a>
					</span>
				</div>` : `
				<div class="mb-3 border-top pt-3">
					<span class="float-right">
						<button onclick="return load('document/${idocument.item_id}');" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i> View Directory</button>
					</span>
				</div>`}
			`);
		}
	}

}

$(`section[class~="document-wrapper"]`).on("click", function() {
	$(`div[class="context-menu"]`).css({display: "none"});
});

var load_home = () => {
	$.array_stream["document_breadcrumbs"] = {};
	$.form_data = {};
	load(`documents/${url_link}`);
}

var jump_to_folder = (unique_id) => {
	load(`documents/${url_link}/${unique_id}`);
}

var go_back = (unique_id = "not_initialized") => {
	if(unique_id !== "not_initialized") {
		keys_count = Object.keys($.array_stream["document_breadcrumbs"]).length;
		delete $.array_stream["document_breadcrumbs"][keys_count];

		let n_key = "";
		$.each($.array_stream["document_breadcrumbs"], function(i, e) {
			$.each(e, function(ii, ee) {
				n_key = ii;
			});
		});
		load(`documents/${url_link}/${n_key}`);
	} else {
		load(`documents/${url_link}/${unique_id}`);
	}
}

document_handler(url_link);