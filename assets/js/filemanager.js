var document_lookup = (element) => {

	let _unique_id = element.attr("data-parameter_url");

	$(`div[data-parameter="document"], div[data-parameter="file"]`).removeClass("highlighted");
	$(`div[class="document-manager"] input[name="selected_document_id"]`).val(_unique_id);
	element.addClass("highlighted");

	load_document_details(_unique_id);
}

var document_handler = () => {

	let url_link = $(`section[class~="document-wrapper"] input[name="drive_url_link"]`).val();
	
	$(`div[data-parameter="document"], div[data-parameter="file"]`).on("click", function(evt) {
		if(window.event.ctrlKey) {
			if($(this).hasClass("highlighted")) {
				$(this).removeClass("highlighted");
			} else {
				$(this).addClass("highlighted");
			}
		} else {
			document_lookup($(this));
		}
	});

	$(`div[data-parameter="document"], div[data-parameter="file"]`).contextmenu(function(evt) {
		evt.preventDefault();
		let _element = $(this);

		let top_size = (evt.pageY - 10),
			left_size = (evt.pageX + 10),
			_type = _element.attr("data-parameter_type"),
			highlighted_count = 0;

		$.each($(`div[data-parameter="file"][class~="highlighted"]`), function(ii, ee) {
			highlighted_count++;
		});

		$.each($(`div[data-parameter="document"][class~="highlighted"]`), function(ii, ee) {
			highlighted_count++;
		});

		if(_type === "folder") {
			$(`div[class="context-menu"] li[class~="open"]`).removeClass("hidden");
			$(`div[class="context-menu"] li[class~="download"]`).addClass("hidden");
		} else {
			$(`div[class="context-menu"] li[class~="open"]`).addClass("hidden");
			$(`div[class="context-menu"] li[class~="download"], div[class="context-menu"] li[class~="copy"]`).removeClass("hidden");
		}

		$(`div[class="context-menu"]`).css({
			display: "block",
			top: top_size + "px",
			left: left_size + "px"
		});

		if(highlighted_count < 2) {
			$(`div[class="context-menu"] li[class~="modify"]`).removeClass("hidden");
			document_lookup(_element);
		} else {
			$(`div[class="context-menu"] li[class~="open"]`).addClass("hidden");
			$(`div[class="context-menu"] li[class~="download"], div[class="context-menu"] li[class~="modify"]`).addClass("hidden");
		}

	});

	$(`div[data-parameter="document"], div[data-parameter="file"]`).on("dblclick", function() {
		let unique_id = $(this).attr("data-parameter_url"),
			document_type = $(this).attr("data-parameter_type");
		
		let _document = $.array_stream["documents_array_list"];
		let idocument = _document[unique_id];

		$(`div[class="document-manager"] input[name="selected_document_id"]`).val(unique_id);

		if(url_link === "trash") {
			document_action("restore");
		}
		else {
			if(document_type === "folder") {
				keys_count = Object.keys($.array_stream["document_breadcrumbs"]).length;
				$.array_stream["document_breadcrumbs"][(keys_count+1)] = {
					[unique_id]: idocument.name
				};
				$.form_data = $.array_stream["document_breadcrumbs"];
				load(`documents/${url_link}/${unique_id}`);
			}
		}

	});

}