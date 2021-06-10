var sms_characters_counter = () => {
    var $remaining = $(`span[class~="remaining_count"]`),
        $messages = $remaining.next();

    $(`textarea[name="message"]`).on("input", function() {
        var chars = this.value.length,
            messages = Math.ceil(chars / 160),
            remaining = messages * 160 - (chars % (messages * 160) || messages * 160);
        $remaining.text(`${remaining} characters remaining`);
        $messages.text(`${messages} message`);
    });
}
sms_characters_counter();

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
            $(`form[class="ajax-data-form"] input, form[class="ajax-data-form"] textarea`).val("");
            $(`div[id="communication_form"] [class="card-header"]`).html(title);
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
            $(`div[id="communication_form"] input[name="template_id"]`).val(data.item_id);
            $(`div[id="communication_form"] textarea[name="message"]`).val(data.message);
            $(`div[id="communication_form"] trix-editor[input="trix-editor-input"]`).html(data.message);
            $(`div[id="communication_form"] [class="card-header"]`).html("Update Template");
            $(`div[id="communication_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}${form_url}`);
        }
    }
}

$(`div[class="send_smsemail"] input[name="send_later"]`).on("click", function() {
    let route = $(this).attr("data-route");
    if ($(`input[name="send_later"][data-route="${route}"]`).is(':checked')) {
        $(`input[name="schedule_date"][data-route="${route}"]`).prop("disabled", false);
        $(`input[name="schedule_time"][data-route="${route}"]`).prop("disabled", false);
    } else {
        $(`input[name="schedule_date"][data-route="${route}"]`).prop("disabled", true);
        $(`input[name="schedule_time"][data-route="${route}"]`).prop("disabled", true);
    }
});

$(`div[class="send_smsemail"] select[name="template_id"]`).on("change", function() {
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
                $(`div[class="send_smsemail"] textarea[name="message"]`).html(template.message);
                sms_characters_counter();
            } else {
                $(`div[class="send_smsemail"] trix-editor[input="trix-editor-input"]`).html(template.message);
            }
        } else {
            if (route === "sms") {
                $(`textarea[name="message"]`).html("");
            } else {
                $(`div[class="send_smsemail"] trix-editor[input="trix-editor-input"]`).html("");
            }
        }

    }
});

$(`div[class="send_smsemail"] select[name="role_id"]`).on("change", function() {
    let route = $(this).attr("data-route"),
        value = $(this).val();

    $(`div[class="send_smsemail"] div[id='individual_select_list_${route}'] select`).find('option').remove().end();
    $(`div[id='individual_select_list_${route}'] select`).append(`<option value=''>Select</option>`);

    $.each($.array_stream["users_array_list"], function(i, e) {
        if (e.user_type == value) {
            $(`div[id='individual_select_list_${route}'] select`).append(`<option value='${e.item_id}'>${e.name}</option>`);
        }
    });
});

$(`div[class="send_smsemail"] select[name="recipient_type"]`).on("change", function() {

    let route = $(this).attr("data-route"),
        value = $(this).val(),
        class_select = $(`div[id="class_select_${route}"]`),
        individual_select = $(`div[id="individual_select_${route}"]`),
        role_group_select = $(`div[id="role_group_select_${route}"]`),
        individual_select_list = $(`div[id="individual_select_list_${route}"]`);

    if (value == "group") {
        role_group_select.removeClass("hidden");
        class_select.addClass("hidden");
        individual_select.addClass("hidden");
        individual_select_list.addClass("hidden");
    } else if (value == "individual") {
        class_select.addClass("hidden");
        role_group_select.addClass("hidden");
        individual_select.removeClass("hidden");
        individual_select_list.removeClass("hidden");
    } else if (value == "class") {
        class_select.removeClass("hidden");
        role_group_select.addClass("hidden");
        individual_select.addClass("hidden");
        individual_select_list.addClass("hidden");
    } else {
        class_select.addClass("hidden");
        role_group_select.addClass("hidden");
        individual_select.addClass("hidden");
        individual_select_list.addClass("hidden");
    }

});

$(`form[class="form_send_message"]`).on("submit", function(evt) {
    evt.preventDefault();
    let form = $(this);
    let route = form.attr("data-route"),
        data = form.serialize();

    swal({
        title: `Send ${route.toUpperCase()}`,
        text: `Are you sure you want to send this ${route} message?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/communication/send_smsemail`, data).then((response) => {
                if (response.code == 200) {

                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
            }).catch(() => {
                swal({
                    text: swalnotice["ajax_error"],
                    icon: "error",
                });
            });
        }
    });

});