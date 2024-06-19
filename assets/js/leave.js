$(`select[id="leave_status"]`).on("change", function() {
	let status = $(this).val(),
		leave_id = $(this).attr("data-leave_id");

	$.post(`${baseUrl}api/leave/status`, {status, leave_id}).then((response) => {
		notify(response.data.result, responseCode(response.code));
		if(response.code == 200) {
			if(status === 'Cancelled') {
				setTimeout(() => {
					load(`leave/${leave_id}`);
				}, refresh_seconds);
			}
		}
	});
});