var generate_list = (route, student_id = "") => {
    let recipient_type = $(`select[id="recipient_type"]`).val(),
        class_id = $(`select[name="class_id"]`).val(),
        users_receipients_list = ``;

    $(`input[id="select_all"]`).attr("disabled", true);
    if(!recipient_type) {
        notify("Sorry! Please select recipient group type.");
        return false;
    }

    $(`tbody[class="receipients_list"]`).html(`<tr><td align="center" colspan="4">Processing request <i class="fa fa-spin fa-spinner"></i>.</td></tr>`);

    // the list of members
    let count = 0;
    let members_list = $.array_stream["users_array_list"];

    // set the list of members
    $.each(members_list, function(i, e) {
        if((e.class_id == class_id) && (e.student_debt !== null) && (e.student_debt > 1)) {
            count++;
            let the_value = (e.phone_number !== null) ? e.phone_number : e.email;
            users_receipients_list += `
            <tr row_id="${e.item_id}">
                <td width="10%">${count}</td>
                <td width="75%" class="">
                    <label for="recipients_${e.item_id}" class="cursor text-uppercase text-info">${e.name}</label>
                    <div>
                        <span class="mr-4"><strong>BAL:</strong> ${myPrefs.labels.currency}${format_currency(e.student_debt)}</span>
                        ${((e.arrears !== null) && (e.arrears.length)) ?     
                            `<span><strong class="text-danger">ARREARS:</strong> ${myPrefs.labels.currency}${format_currency(e.arrears)}</span>`
                        : ""}
                    </div>
                </td>
                <td align="center"><input ${student_id == e.item_id ? "checked" : ""} ${((the_value !== null) && (the_value.length)) ? `class="user_contact" name="recipients[]" data-recipient_name="${e.name}" value="${e.item_id}" id="recipients_${e.item_id}"` : "disabled"} style="width:20px;cursor:pointer;height:20px;" type="checkbox"></td>
            </tr>`;
        }
    });

    if(!users_receipients_list) {
        users_receipients_list = `<tr><td align="center" colspan="4">No member was found under the selected category.</td></tr>`;
    } else {
        $(`div[id="message_form"] input, div[id="message_form"] textarea`).attr("disabled", false).val(``);
        $(`div[id="message_form"] select, input[id="select_all"]`).attr("disabled", false);
        $(`div[id="message_form"] input[id="type"]`).val(route);
        $(`input[name="schedule_time"], input[name="schedule_date"]`).attr("disabled", true);
    }

    $(`tbody[class="receipients_list"]`).html(users_receipients_list);
}

var sms_characters_counter = () => {
    var $remaining = $(`span[class~="remaining_count"]`),
        $messages = $remaining.next();
    $(`textarea[name="message"]`).on("input", function() {
        var chars = this.value.length,
            messages = Math.ceil(chars / sms_text_count),
            remaining = messages * sms_text_count - (chars % (messages * sms_text_count) || messages * sms_text_count);
        $remaining.text(`${remaining} characters remaining`);
        $messages.text(`${messages} message`);
    });
}
sms_characters_counter();

$(`form[class="form_send_reminder"]`).on("submit", function(evt) {
    evt.preventDefault();
    let form = $(this);
    let route = form.attr("data-route");

    let myForm = document.getElementById(`send_form_${route}`);
    let theFormData = new FormData(myForm);

    swal({
        title: `Send Reminder`,
        text: `Are you sure you want to send this reminder to the students?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[id="message_form"] div[class="form-content-loader"]`).css("display", "flex");
            $.ajax({
                url: `${baseUrl}api/communication/send_reminder`,
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
                        $(`form[class="form_send_reminder"] select`).val("").change();
                        $(`form[class="form_send_reminder"] input, form[class="form_send_reminder"] textarea`).val("");
                        setTimeout(() => {
                            loadPage(`${baseUrl}reminders`);
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

$(`div[class~="send_smsemail"] div[id^="class_select_"] select`).on("change", function() {
    $(`input[id="select_all"]`).attr("disabled", true).prop('checked', false);
    $(`div[id="message_form"] input, div[id="message_form"] textarea`).attr("disabled", true).val(``);
    $(`tbody[class="receipients_list"]`).html(`<tr><td align="center" colspan="4">No receipient selected at the moment.</td></tr>`);

    let $remaining = $(`span[class~="remaining_count"]`),
        $messages = $remaining.next();
    $remaining.text(`${sms_text_count} characters remaining`);
    $messages.text(`0 message`);
});

$(`div[id="message_form"] input, div[id="message_form"] textarea`).attr("disabled", true);
$(`div[class~="send_smsemail"] input[id="select_all"]`).on("click", function () {
    $(`table[class~="table_list"]`).find(`input[class="user_contact"]:checkbox`).prop('checked', this.checked);
});

if($(`input[id='preload_students_list']`).length) {
    generate_list("reminder", $(`input[id='preload_students_list']`).val());
}
