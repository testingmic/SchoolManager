var access_permission = (access_id) => {
	if ($.array_stream["access_permission_array"] !== undefined) {
		$(`div[id="access_control"] *`).prop("disabled", true);
        let permissions = $.array_stream["access_permission_array"];
        if (permissions[access_id] !== undefined) {
            let data = permissions[access_id];
            $(`input[class="access_id"]`).val(access_id);
            $(`span[id="access_level"]`).html(data.name);
            $(`textarea[id="access_permission"]`).val(data.user_permissions);
            $(`div[id="access_control"] *`).prop("disabled", false);
        }
    }
}

$(`div[id="access_control"] button[class~="btn-secondary"]`).on("click", function() {
	$(`div[id="access_control"] *`).prop("disabled", true).val("");
	$(`span[id="access_level"]`).html(`<em>Access Level Appears Here</em>`);
});

$(`div[id="access_control"] button[class~="btn-success"]`).on("click", function() {
	$(`div[id="access_control"] *`).prop("disabled", true);
	let level = $(`div[id="access_level"]`).text();
	let data = {
		access_id: $(`input[class="access_id"]`).val(), 
		permission: $(`textarea[id="access_permission"]`).val()
	};
	swal({
        title: "Save Access Permissions",
        text: `Are you sure you want to save this access permission for ${level}?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
        	$(`div[id="access_control"] div[class="form-content-loader"]`).css("display", "flex");
        	$.post(`${baseUrl}api/support/access_permission`, {data}).then((response) => {
        		$(`div[id="access_control"] *`).prop("disabled", false);
        		$(`div[id="access_control"] div[class="form-content-loader"]`).css("display", "none");
        		swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
        	}).catch(() =>{
        		swal({
                    text: "Sorry! There was an error while processing the request.",
                    icon: "error"
                });
                $(`div[id="access_control"] *`).prop("disabled", false);
        		$(`div[id="access_control"] div[class="form-content-loader"]`).css("display", "none");
        	});
        }
    });
});