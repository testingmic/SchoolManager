var cancel_ChangePassword = (request_id) => {
    swal({
        title: "Cancel Request",
        text: "Are you sure you want to cancel this password change request?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/auth/password_manager`, ).then((response) => {
                if(response.code == 200) {
                    if(response.data.result.request == "cancel") {
                        $(`div[change_password_${request_id}]`).remove();
                    }
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
            });
        }
    });
}