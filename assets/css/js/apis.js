var extend_api_expiry = (api_id) => {
	let expiry_date = $(`input[data-api_key_date="${api_id}"]`).val();
	let data = {api_id, expiry_date, action: "extend_date"};
	$.post(`${baseUrl}api/endpoints/api`, {data}).then((response) => {
		if(response.code == 200) {
			notify(response.data.result, "success");
			setTimeout(() => {
				load("application_api_keys");
			}, refresh_seconds);
		} else {
			notify(response.data.result);
		}
	});
}

var create_api_key = () => {
	swal({
		title: "Generate API Keys",
		text: "Are you sure you want to create a new API Keys with a duration of 6 months?",
		icon: 'warning',
		buttons: true,
		dangerMode: true,
	}).then((proceed) => {
		if (proceed) {
			let data = {action: "create"};
			$.post(`${baseUrl}api/endpoints/api`, {data}).then((response) => {
				if(response.code == 200) {
					notify(response.data.result, "success");
					setTimeout(() => {
						load("application_api_keys");
					}, refresh_seconds);
				} else {
					notify(response.data.result);
				}
			});
		}
	});
}

var delete_api_key = (api_id) => {
	swal({
		title: "Delete API Key",
		text: "You have opted to delete this API Key. You will not be able to reverse this action once confirmed. Are you sure you want to proceed?",
		icon: 'warning',
		buttons: true,
		dangerMode: true,
	}).then((proceed) => {
		if (proceed) {
			let data = {action: "delete", api_id};
			$.post(`${baseUrl}api/endpoints/api`, {data}).then((response) => {
				if(response.code == 200) {
					notify(response.data.result, "success");
					setTimeout(() => {
						load("application_api_keys");
					}, refresh_seconds);
				} else {
					notify(response.data.result);
				}
			});
		}
	});
}