var cancel_ChangePassword = (request_id) => {
    swal({
        title: "Cancel Request",
        text: "Are you sure you want to cancel this password change request?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let data = {
                "request" : "cancel",
                "request_id" : request_id
            };
            $.post(`${baseUrl}api/auth/password_manager`, {data}).then((response) => {
                if(response.code == 200) {
                    if(response.data.additional.request == "cancel") {
                        $(`span[id="change_status_${request_id}"]`).html(`ANNULED`).removeClass(`text-primary`).addClass(`text-danger`);
                        $(`div[class="change_password_${request_id}"]`).remove();
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

var show_ChangePasword_Form = (request_id, request_token) => {
    $(`div[id="change_Password"]`).modal(`show`);
    $(`div[id="change_Password"] *`).val(``).attr("disabled", false);
    $(`div[id="change_Password"] input[name="request_id"]`).val(request_id);
    $(`div[id="change_Password"] input[name="token"]`).val(request_token);
}

var cancel_ChangePasword_Form = () => {
    $(`div[id="change_Password"]`).modal(`hide`);
    $(`div[id="change_Password"] *`).val(``).attr("disabled", true);
}

var change_Password = () => {
    swal({
        title: "Change Password",
        text: "Are you sure you want to change the password of this user?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let data = {
                "request" : "change",
                "request_id": $(`div[id="change_Password"] input[name="request_id"]`).val(),
                "token": $(`div[id="change_Password"] input[name="token"]`).val(),
                "password": $(`div[id="change_Password"] input[name="password"]`).val(),
                "password_2": $(`div[id="change_Password"] input[name="password_2"]`).val(),
            };
            $.post(`${baseUrl}api/auth/password_manager`, {data}).then((response) => {
                if(response.code == 200) {
                    if(response.data.additional.request == "change") {
                        $(`span[id="change_status_${$(`div[id="change_Password"] input[name="request_id"]`).val()}"]`).html(`USED`).removeClass(`text-primary text-danger`).addClass(`text-success`);
                        $(`div[class="change_password_${$(`div[id="change_Password"] input[name="request_id"]`).val()}"]`).remove();
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