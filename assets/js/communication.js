var paymentWindow;

var reset_communication_form = (form_url, title = "Create Template") => {
    swal({
        title: "Cancel Form",
        text: "Are you sure you want to cancel this form?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`a[id="templates_list-tab2"]`).trigger("click");
            $(`trix-editor[id="ajax-form-content"]`).html("");
            $(`form[class="ajax-data-form"] input, form[class="ajax-data-form"] textarea`).val("");
            $(`div[id="communication_form"] [class="card-header"]`).html(title);
            $(`div[id="communication_form"] select[name="module"]`).val("").change();
            $(`div[id="communication_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}${form_url}`);
        }
    });
}

var view_template = (template_id, form_url) => {
    if ($.array_stream["templates_array_list"] !== undefined) {
        let template = $.array_stream["templates_array_list"];
        if (template[template_id] !== undefined) {
            let data = template[template_id];
            $(`a[id="add_template-tab2"]`).trigger("click");
            $(`div[id="communication_form"] input[name="name"]`).val(data.name);
            $(`div[id="communication_form"] input[name="type"]`).val(data.type);
            $(`div[id="communication_form"] select[name="module"]`).val(data.module).change();
            $(`div[id="communication_form"] input[name="template_id"]`).val(data.item_id);
            $(`div[id="communication_form"] textarea[name="message"]`).val(data.message);
            $(`div[id="communication_form"] trix-editor[input="trix-editor-input"]`).html(data.message);
            $(`div[id="communication_form"] [class="card-header"]`).html("Update Template");
            $(`div[id="communication_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}${form_url}`);
        }
    }
}

var append_dynamic_tag = (tag, route) => {
    if(route === "sms") {
        let msg = $(`div[id="communication_form"] textarea[name="message"]`).val();
        let new_msg = `${msg} ${tag}`;
        $(`div[id="communication_form"] textarea[name="message"]`).val(new_msg);
    } else {
        let msg = $(`div[id="communication_form"] trix-editor[input="trix-editor-input"]`).html();
        let new_msg = `${msg} ${tag}`;
        $(`div[id="communication_form"] trix-editor[input="trix-editor-input"]`).html(new_msg);
    }
}

var view_message = (type, message_id) => {
        if ($.array_stream["messages_array_list"][type] !== undefined) {
            let template = $.array_stream["messages_array_list"][type];
            if (template[message_id] !== undefined) {
                let data = template[message_id],
                    recipient_list = ``;

                $(`div[id="viewOnlyModal"]`).modal("show");
                $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(`Message Details`);

                $.each(data.recipient_list, function(i, e) {
                    i++;
                    let status = e.status == undefined ? "Pending" : e.status;
                    recipient_list += `
                    <tr>
                        <td>${i}</td>
                        <td>${e.name}</td>
                        <td>${e.unique_id}</td>
                        <td>${data.type == "sms" ? e.phone_number : e.email}</td>
                        <td>${status}</td>
                    </tr>`;
                });

                let content = `
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td width="20%" class="font-weight-bold">Campaign Name</td>
                                <td>${data.campaign_name}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Recipient Group</td>
                                <td>${data.recipient_group}</td>
                            </tr>
                            ${data.type === "email" ? `<tr>
                                <td class="font-weight-bold">Subject</td>
                                <td>${data.subject}</td>
                            </tr>`: ""}
                            <tr>
                                <td class="font-weight-bold">Message</td>
                                <td>${data.message}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Date Created</td>
                                <td>${data.date_created}</td>
                            </tr>
                        </table>
                        <table class="table datatable_start table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Unique ID</th>
                                    <th>${data.type === "email" ? "Email" : "Phone Number"}</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>${recipient_list}</tbody>
                        </table>
                    </div>
                </div>`;

            $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html($.parseHTML(content));
            $('.datatable_start').dataTable();
        }
    }
}

var update_smsbalance = (package_id, reference_id, transaction_id) => {
    $.post(`${baseUrl}api/communication/verify_and_update`, {package_id, reference_id, transaction_id}).then((response) => {
        if(response.code == 200) {
            $(`span[id="sms_balance"]`).html(`${response.data.result} SMS Units`);
        }
    });
}

var buy_sms_package = async (amount, package_id) => {

    $(`div[id="buy_sms_package"] div[class="form-content-loader"]`).css("display", "flex");
    let email = $(`input[name="myemail_address"]`).val();
    amount = parseFloat(amount) * 100;

    let data = { amount, package_id, email};
    await $.post(`${baseUrl}api/communication/log`, {data}).then((response) => {

        if(response.code == 200) {

            let _t_result = response.data.result;

            try {

                var popup = PaystackPop.setup({
                    key: _t_result.pk_public_key,
                    email: _t_result.email,
                    amount: _t_result.amount,
                    currency: _t_result.currency,
                    onClose: function() {
                        $(`div[id="buy_sms_package"] div[class="form-content-loader"]`).css("display", "none");
                        swal({
                            text: "Payment Process Cancelled",
                            icon: "error",
                        });
                    },
                    callback: function (response) {
                        let message = `Payment ${response.message}`,
                            code = "error";
                        if(response.message == "Approved") {
                            $(`div[id="buy_sms_package"] div[class="form-content-loader"]`).css("display", "none");
                            $(`div[id="viewOnlyModal"]`).modal("hide");
                            code = "success";
                            update_smsbalance(package_id, response.reference, response.transaction);
                        } else {
                            swal({text: message, icon: code});
                        }
                    }
                });
                popup.openIframe();
                
            } catch(e) {
                swal({
                    text: "Connection Failed! Please check your internet connection to proceed.",
                    icon: "error",
                });
                setTimeout(() => {
                    $(`div[id="buy_sms_package"] div[class="form-content-loader"]`).css("display", "none");
                }, refresh_seconds);
            }
        }
    }).catch(() => {
        $(`div[id="buy_sms_package"] div[class="form-content-loader"]`).css("display", "flex");
        swal({
            text: "Sorry! An error was encountered while processing then request.",
            icon: "error",
        });
    });
    
}

var topup_sms = () => {
    $(`div[id="viewOnlyModal"]`).modal("show");
    $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(`Buy SMS Credit`);
    
    let packages = $.array_stream["sms_packages"],
    packages_list = `
    <div class="row" id="buy_sms_package">
    <div class="form-content-loader" style="display: none; position: absolute">
        <div class="offline-content text-center">
            <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
        </div>
    </div>`;

    $.each(packages, function(i, e) {
        packages_list += `
        <div class="col-lg-4 mb-4 col-md-4" style="overflow:hidden;">
            <div class="buy_container">
                <div class="pricing font-17">GH&cent;${e.amount}</div>
                <div class="buy_text">
                    <span class="font-30 text-success">${e.units} 
                        <font class="font-12 text-dark">Units</font><br>
                    </span>
                </div>
            </div>
            <div class="buy_button" onclick="return buy_sms_package('${e.amount}','${e.item_id}')">
                <i class="fa fa-money-bill"></i> Buy
            </div>
        </div>`;
    });

    packages_list += `</div>`;

    $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html($.parseHTML(packages_list));
}

$(`div[class~="send_smsemail"] input[name="send_later"]`).on("click", function() {
    let route = $(this).attr("data-route");
    if ($(`input[name="send_later"][data-route="${route}"]`).is(':checked')) {
        $(`input[name="schedule_date"][data-route="${route}"]`).prop("disabled", false);
        $(`input[name="schedule_time"][data-route="${route}"]`).prop("disabled", false);
    } else {
        $(`input[name="schedule_date"][data-route="${route}"]`).prop("disabled", true);
        $(`input[name="schedule_time"][data-route="${route}"]`).prop("disabled", true);
    }
});

$(`div[class~="send_smsemail"] select[name="template_id"]`).on("change", function() {
    let route = $(this).attr("data-route"),
        template_id = $(this).val();

    if ($.array_stream["templates_array"] !== undefined) {
        let templates = $.array_stream["templates_array"],
            template = "";
        $.each(templates, function(i, e) {
            if (e.item_id == template_id) {
                template = e;
                return;
            }
        });
        if (template !== "") {
            if (route === "sms") {
                $(`div[class~="send_smsemail"] textarea[name="message"]`).html(template.message);
                sms_characters_counter();
            } else {
                $(`div[class~="send_smsemail"] trix-editor[input="trix-editor-input"]`).html(template.message);
            }
        } else {
            if (route === "sms") {
                $(`textarea[name="message"]`).html("");
            } else {
                $(`div[class~="send_smsemail"] trix-editor[input="trix-editor-input"]`).html("");
            }
        }

    }
});

$(`div[class~="send_smsemail"] div[id^="class_select_"] select,
    div[class~="send_smsemail"] div[id^="individual_select_"] select,
    div[class~="send_smsemail"] div[id^="role_group_select_"] select`).on("change", function() {
    $(`input[id="select_all"]`).attr("disabled", true).prop('checked', false);
    $(`div[id="message_form"] *`).attr("disabled", true).val(``);
    $(`tbody[class="receipients_list"]`).html(`<tr><td align="center" colspan="4">No receipient selected at the moment.</td></tr>`);

    let $remaining = $(`span[class~="remaining_count"]`),
        $messages = $remaining.next();
    $remaining.text(`${sms_text_count} characters remaining`);
    $messages.text(`0 message`);
});

var generate_list = (route) => {
    let recipient_type = $(`select[name="recipient_type"]`).val(),
        role_group = $(`div[id="role_group_select_${route}"] select`).val(),
        individual_select = $(`div[id="individual_select_${route}"] select`).val(),
        class_select = $(`div[id="class_select_${route}"] select`).val(),
        users_receipients_list = ``;

    $(`input[id="select_all"]`).attr("disabled", true);
    if(!recipient_type) {
        notify("Sorry! Please select recipient group type.");
        return false;
    }
    else if((recipient_type === "group") && !role_group.length) {
        notify("Sorry! Please select at least a category of user group.");
        return false;
    }
    else if((recipient_type === "individual") && !individual_select.length) {
        notify("Sorry! Please select the category within which the individual recipient falls.");
        return false;
    }
    else if((recipient_type === "class") && !class_select.length) {
        notify("Sorry! Please select the class to load the students.");
        return false;
    }
    
    $(`tbody[class="receipients_list"]`).html(`<tr><td align="center" colspan="4">Processing request <i class="fa fa-spin fa-spinner"></i>.</td></tr>`);

    // the list of members
    let count = 0;
    let members_list = $.array_stream["users_array_list"];

    // set the list of members
    if((recipient_type === "class") && class_select.length) {
        $.each(members_list, function(i, e) {
            if ((e.user_type == "student") && (e.class_id == class_select)) {
                count++;
                let the_value = (route == "sms") ? e.phone_number : e.email;
                if(the_value) {
                    users_receipients_list += `
                    <tr row_id="${e.item_id}">
                        <td width="10%">${count}</td>
                        <td width="50%">
                            <label for="recipients_${e.item_id}" class="cursor mb-0 pb-0 underline text-uppercase text-info">${e.name}</label>
                            <br><strong>${e.unique_id}</strong>
                        </td>
                        <td width="25%"><label class="cursor" for="recipients_${e.item_id}">${((the_value !== null) && (the_value.length)) ? the_value : ""}</label></td>
                        <td align="center">${((the_value !== null) && (the_value.length)) ? `<input class="user_contact" name="recipients[]" data-recipient_name="${e.name}" value="${e.item_id}" id="recipients_${e.item_id}" style="width:20px;cursor:pointer;height:20px;" type="checkbox">` : ""}</td>
                    </tr>`;
                }
            }
        });
    }
    else if((recipient_type === "individual") && individual_select) {
        $.each(members_list, function(i, e) {
            if ((e.user_type == individual_select)) {
                count++;
                let the_value = (route == "sms") ? e.phone_number : e.email;
                if(the_value) {
                    users_receipients_list += `
                    <tr row_id="${e.item_id}">
                        <td width="10%">${count}</td>
                        <td width="50%">
                            <label for="recipients_${e.item_id}" class="cursor mb-0 pb-0 text-uppercase text-info">${e.name}</label>
                            <br><strong>${e.unique_id}</strong>
                        </td>
                        <td width="25%"><label class="cursor" for="recipients_${e.item_id}">${((the_value !== null) && (the_value.length)) ? the_value : ""}</label></td>
                        <td align="center">${((the_value !== null) && (the_value.length)) ? `<input class="user_contact" name="recipients[]" data-recipient_name="${e.name}" value="${e.item_id}" id="recipients_${e.item_id}" style="width:20px;cursor:pointer;height:20px;" type="checkbox">` : ""}</td>
                    </tr>`;
                }
            }
        });
    } else if((recipient_type === "group") && role_group.length) {
        $.each(members_list, function(i, e) {
            if ($.inArray(e.user_type, role_group) !== -1) {
                count++;
                let the_value = (route == "sms") ? e.phone_number : e.email;
                if(the_value) {
                    users_receipients_list += `
                    <tr row_id="${e.item_id}">
                        <td width="10%">${count}</td>
                        <td width="50%">
                            <label for="recipients_${e.item_id}" class="cursor mb-0 pb-0 text-uppercase text-info">${e.name}</label>
                            <br><strong>${e.unique_id}</strong>
                        </td>
                        <td width="25%"><label class="cursor" for="recipients_${e.item_id}">${((the_value !== null) && (the_value.length)) ? the_value : ""}</label></td>
                        <td align="center">${((the_value !== null) && (the_value.length)) ? `<input class="user_contact" name="recipients[]" data-recipient_name="${e.name}" value="${e.item_id}" id="recipients_${e.item_id}" style="width:20px;cursor:pointer;height:20px;" type="checkbox">` : ""}</td>
                    </tr>`;
                }
            }
        });
    }
    
    if(!users_receipients_list) {
        users_receipients_list = `<tr><td align="center" colspan="4">No member was found under the selected category.</td></tr>`;
    } else {
        $(`div[id="message_form"] *`).attr("disabled", false).val(``);
        $(`input[id="select_all"]`).attr("disabled", false);
        $(`div[id="message_form"] input[name="type"]`).val(route);
        $(`input[name="schedule_time"], input[name="schedule_date"]`).attr("disabled", true);
    }

    $(`tbody[class="receipients_list"]`).html(users_receipients_list);
}

$(`div[class~="send_smsemail"] select[name="recipient_type"]`).on("change", function() {

    let route = $(this).attr("data-route"),
        value = $(this).val(),
        class_select = $(`div[id="class_select_${route}"]`),
        individual_select = $(`div[id="individual_select_${route}"]`),
        role_group_select = $(`div[id="role_group_select_${route}"]`),
        individual_select_list = $(`div[id="individual_select_list_${route}"]`);

    $(`tbody[class="receipients_list"]`).html(`<tr><td align="center" colspan="4">No receipient selected at the moment.</td></tr>`);
    
    $(`div[id="message_form"] *`).attr("disabled", true).val(``);
    if (value == "group") {
        role_group_select.removeClass("hidden");
        class_select.addClass("hidden");
        individual_select.addClass("hidden");
        individual_select_list.addClass("hidden");
        $(`button[id="generate_list_button"]`).removeClass("hidden");
    } else if (value == "individual") {
        class_select.addClass("hidden");
        role_group_select.addClass("hidden");
        individual_select.removeClass("hidden");
        individual_select_list.removeClass("hidden");
        $(`button[id="generate_list_button"]`).removeClass("hidden");
    } else if (value == "class") {
        class_select.removeClass("hidden");
        role_group_select.addClass("hidden");
        individual_select.addClass("hidden");
        individual_select_list.addClass("hidden");
        $(`button[id="generate_list_button"]`).removeClass("hidden");
    } else {
        class_select.addClass("hidden");
        role_group_select.addClass("hidden");
        individual_select.addClass("hidden");
        individual_select_list.addClass("hidden");
        $(`button[id="generate_list_button"]`).addClass("hidden");
    }

});

$(`form[class="form_send_message"]`).on("submit", function(evt) {
    evt.preventDefault();
    let form = $(this);
    let route = form.attr("data-route");

    let myForm = document.getElementById(`send_form_${route}`);
    let theFormData = new FormData(myForm);

    swal({
        title: `Send ${route.toUpperCase()}`,
        text: `Are you sure you want to send this ${route} message?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {

            if(route === "sms") {
                theFormData.delete("message");
                let content = $(`textarea[name="message"]`).html();
                theFormData.append("message", htmlEntities(content));
            } else {
                if ($(`trix-editor[name="faketext"][id="ajax-form-content"]`).length) {
                    theFormData.delete("faketext");
                    let content = $(`trix-editor[id="ajax-form-content"]`).html(),
                        form_variable = $(`trix-editor[id="ajax-form-content"]`).attr("data-predefined_name");
                    theFormData.append(form_variable, htmlEntities(content));
                }
            }
            
            $(`div[id="message_form"] div[class="form-content-loader"]`).css("display", "flex");
            $.ajax({
                url: `${baseUrl}api/communication/send_smsemail`,
                data: theFormData,
                contentType: false,
                cache: false,
                type: `POST`,
                processData: false,
                success: function(response) {
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code),
                    });
                    if (response.code == 200) {
                        let $remaining = $(`span[class~="remaining_count"]`),
                        $messages = $remaining.next();
                        $remaining.text(`${sms_text_count} characters remaining`);
                        $messages.text(`0 message`);
                        $(`div[id="message_form"] *`).attr("disabled", true).val(``);
                        $(`form[class="form_send_message"] select`).val("").change();
                        $(`form[class="form_send_message"] input, form[class="form_send_message"] textarea`).val("");
                        setTimeout(() => {
                            loadPage(`${baseUrl}smsemail_report?msg_id=${response.data.additional.item_id}`);
                        }, refresh_seconds);
                    }
                },
                complete: function() {
                    $(`div[id="message_form"] div[class="form-content-loader"]`).css("display", "none");
                },
                error: function() {
                    $(`div[id="message_form"] div[class="form-content-loader"]`).css("display", "none");
                    swal({text: swalnotice["ajax_error"], icon: "error"});
                }
            });
        }
    });

});

var cancel_sending_form = () => {
    swal({
        title: "Cancel Form",
        text: "Are you sure you want to cancel this form?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[id="message_form"] *`).attr("disabled", true).val(``);
            $(`input[id="select_all"]`).attr("disabled", true).prop('checked', false);
            $(`div[id="message_form"] *`).attr("disabled", true).val(``);
            $(`tbody[class="receipients_list"]`).html(`<tr><td align="center" colspan="4">No receipient selected at the moment.</td></tr>`);
            let $remaining = $(`span[class~="remaining_count"]`),
                $messages = $remaining.next();
            $remaining.text(`${sms_text_count} characters remaining`);
            $messages.text(`0 message`);
        }
    });
}

$(`div[id="message_form"] *`).attr("disabled", true);
$(`div[class="trix-dialogs"] input[type="url"][name="href"], button[data-trix-attribute="href"]`).remove();

$(`div[class~="send_smsemail"] input[id="select_all"]`).on("click", function () {
    $(`table[class~="table_list"]`).find(`input[class="user_contact"]:checkbox`).prop('checked', this.checked);
    $(`div[id="message_form"] button`).attr("disabled", false);
});

sms_characters_counter("message_textarea");