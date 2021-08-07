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
    $(`div[id="change_Username_Password"]`).addClass("hidden");
    $(`div[id="change_Username_Password"] *`).val(``);
    $(`input[id="search_user_term"]`).val(``).focus();
    $(`div[id="search_user_term_list"]`).html(``);
}

var show_change_Username_Password_form = (user_id, username, fullname) => {
    $(`div[id="change_Username_Password"] *`).val(``);
    $(`div[id="change_Username_Password"]`).removeClass("hidden");
    $(`div[id="change_Username_Password"] input[name="username"]`).val(username);
    $(`div[id="change_Username_Password"] input[name="user_id"]`).val(user_id);

    if(myUName === username) {
        $(`div[id="change_Username_Password"] *`).attr("disabled", true);
    } else {
        $(`div[id="change_Username_Password"] *`).attr("disabled", false);
    }
}

$(`input[id="search_user_term"]`).on("keyup", function(evt) {
    if (evt.keyCode == 13 && !evt.shiftKey) {
        search_By_Fullname_Unique_ID();
    }
});

var search_By_Fullname_Unique_ID = () => {
    let lookup = $(`input[id="search_user_term"]`).val();
    if (!lookup.length) {
        $(`div[id="search_user_term_list"]`).html(``);
        notify("Sorry! The search term cannot be empty.");
        $(`input[id="search_user_term"]`).val(``).focus();
    } else {
        $(`div[id="search_user_term_list"]`).html(`<div align="center">Processing request <i class="fa fa-spin fa-spinner"></i></div>`);
        $.get(`${baseUrl}api/users/quick_search`, { lookup: lookup }).then((response) => {
            if (response.code === 200) {
                let results_list = ``,
                    location = `${baseUrl}password_manager?lookup=${lookup}`;
                $.each(response.data.result, function(i, data) {
                    results_list += `
                    <div class="row mb-2 border-bottom pb-2">
                        <div class="col-md-12">
                            <table width="95%" border="0">
                                <tr>
                                    <td width="40%"><strong>Student Name:</strong></td>
                                    <td>
                                        <span onclick="return show_change_Username_Password_form('${data.user_id}','${data.username}','${data.name}')" class="underline text-danger" title="Click to view edit user login details">
                                            ${data.name} <i class="fa fa-edit"></i>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Unique ID: </strong></td>
                                    <td><strong>${data.unique_id}</strong></td>
                                </tr>
                                ${data.phone_number !== null ? `
                                <tr>
                                    <td><strong>Contact Number: </strong></td>
                                    <td>${data.phone_number}</td>
                                </tr>` : ""}
                                ${data.email !== null ? `
                                <tr>
                                    <td><strong>Email Address: </strong></td>
                                    <td>${data.email}</td>
                                </tr>` : ""}
                                ${data.class_name !== null ? `
                                <tr>
                                    <td><strong>Class: </strong></td>
                                    <td>${data.class_name}</td>
                                </tr>` : ""}
                                ${data.last_password_change !== null ? `
                                <tr>
                                    <td><strong>Last Change Date: </strong></td>
                                    <td>${data.last_password_change}</td>
                                </tr>` : ""}
                            </table>
                        </div>
                    </div>`;
                });
                if(!results_list) {
                    results_list = `<div class="text-danger text-center">No user found for the search term <strong>${lookup}</strong></div>`;
                }
                $(`div[id="search_user_term_list"]`).html(results_list);
                window.history.pushState({ current: location }, "", location);
                linkClickStopper($(`div[id="search_user_term_list"]`));
            } else {
                $(`div[id="search_user_term_list"]`).html(`<div align="center" class="text-danger">${response.result.data}</div>`);
            }
        })
    }
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

var change_Username_Password = () => {
    swal({
        title: "Change Username And/Or Password",
        text: "Are you sure you want to change the username and/or password of this user?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let data = {
                "request" : "modify",
                "user_id": $(`div[id="change_Username_Password"] input[name="user_id"]`).val(),
                "username": $(`div[id="change_Username_Password"] input[name="username"]`).val(),
                "password": $(`div[id="change_Username_Password"] input[name="passwd"]`).val(),
                "password_2": $(`div[id="change_Username_Password"] input[name="passwd_2"]`).val(),
            };
            $.post(`${baseUrl}api/auth/password_manager`, {data}).then((response) => {
                if(response.code == 200) {
                    
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
            });
        }
    });
}