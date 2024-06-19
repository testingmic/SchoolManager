$(`select[id="enquiry_status"]`).on("change", function() {
	let status = $(this).val(),
		request_id = $(this).attr("data-request_id"),
		request_url = $(this).attr("data-request_url");

	$.post(`${baseUrl}api/frontoffice/status`, {status, request_id}).then((response) => {
		notify(response.data.result, responseCode(response.code));
		if(response.code == 200) {
			setTimeout(() => {
				load(`${request_url}/${request_id}`);
			}, reference_id);
		}
	});
});