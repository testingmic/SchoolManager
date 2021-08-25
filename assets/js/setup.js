var foundArrayList = new Array(),
    csvContent = new Array();

function htmlEntities(str) {
    return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

var loadPage = (page) => {}

var responseCode = (code) => {
    if (code == 200 || code == 201) {
        return "success";
    } else {
        return "error";
    }
}

var trigger_form_submit = () => {
    $(`form[class="ajax-data-form"] button[type="button-submit"]`).on("click", async function(evt) {

        evt.preventDefault();
        let theButton = $(this);
        let the_form_id = theButton.attr("data-form_id"),
            draftButton = $(`form[id="${the_form_id}"] button[type="button-submit"][data-function="draft"]`),
            formAction = $(`form[id="${the_form_id}"]`).attr("action"),
            formButton = $(`form[id="${the_form_id}"] button[type="button-submit"]`);
        let optional_flow = "We recommend that you save the form as a draft, and review it before submitting. Do you wish to proceed with this action?";

        formButton.prop("disabled", true);

        let myForm = document.getElementById(the_form_id);
        let theFormData = new FormData(myForm);

        let button = theButton.attr("data-function");
        theFormData.append("the_button", button);

        if ($(`form[id="${the_form_id}"] select[name='assigned_to_list']`).length) {
            theFormData.delete("assigned_to_list");
            theFormData.append("assigned_to_list", serializeSelect($(`select[name="assigned_to_list"]`)));
        }

        if ($(`form[id="${the_form_id}"] textarea[name="faketext"]`).length) {
            theFormData.delete("faketext");
            let content = CKEDITOR.instances['ajax-form-content'].getData();
            theFormData.append("description", htmlEntities(content));
        }

        if ($(`form[id="${the_form_id}"] textarea[name="faketext_2"]`).length) {
            theFormData.delete("faketext_2");
            let content = CKEDITOR.instances['ajax-form-content_2'].getData();
            theFormData.append("reason", htmlEntities(content));
        }

        if ($(`form[id="${the_form_id}"] trix-editor[name="faketext"][id="ajax-form-content"]`).length) {
            theFormData.delete("faketext");
            let content = $(`trix-editor[id="ajax-form-content"]`).html();
            theFormData.append("description", htmlEntities(content));
        }

        if ($(`form[id="${the_form_id}"] trix-editor[name="faketext_2"][id="ajax-form-content_2"]`).length) {
            theFormData.delete("faketext_2");
            let content = $(`form[id="${the_form_id}"] trix-editor[id="ajax-form-content_2"]`).html();
            theFormData.append("reason", htmlEntities(content));
        }

        swal({
            title: "Submit Form",
            text: `Are you sure you want to Submit this form? ${draftButton.length ? optional_flow : ""}`,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((proceed) => {
            if (proceed) {
                $.ajax({
                    url: `${formAction}`,
                    data: theFormData,
                    contentType: false,
                    cache: false,
                    type: `POST`,
                    processData: false,
                    success: function(response) {
                        if (response.code == 200) {
                            swal({
                                position: 'top',
                                text: response.data.result,
                                icon: "success",
                            });
                            if (response.data.additional) {
                                if (response.data.additional.clear !== undefined) {

                                }
                                if (response.data.additional.append !== undefined) {
                                    $(`div[id="${response.data.additional.append.div_id}"]`).html(response.data.additional.append.data);
                                }
                                if (response.data.additional.record !== undefined) {
                                    $.each(response.data.additional.record, function(ie, iv) {
                                        $(`form[id="${the_form_id}"] input[name="${ie}"]`).val(iv);
                                        $(`[data-record="${ie}"]`).html(iv);
                                    });
                                }
                                if (response.data.additional.href !== undefined) {
                                    if (theButton.attr("href") !== undefined) {
                                        setTimeout(() => {
                                            window.location.href = `${theButton.attr("href")}`;
                                        }, 1000);
                                    } else {
                                        setTimeout(() => {
                                            window.location.href = `${response.data.additional.href}`;
                                        }, 2000);
                                    }
                                }
                            }
                            $(`form[id="${the_form_id}"] div[class~="file-preview"]`).html("");
                        } else {
                            if (response.data.result !== undefined) {
                                swal({
                                    position: 'top',
                                    text: response.data.result,
                                    icon: "error",
                                });
                            } else {
                                swal({
                                    position: 'top',
                                    text: "Sorry! Error processing request.",
                                    icon: "error",
                                });
                            }
                        }
                    },
                    complete: function() {
                        formButton.prop("disabled", false);
                    },
                    error: function() {
                        swal({
                            position: 'top',
                            text: "Sorry! Error processing request.",
                            icon: "error",
                        });
                    }
                });
            } else {
                formButton.prop("disabled", false);
            }
        });

    });
}

var initPlugins = () => {
    if ($('._datepicker').length > 0) {
        $('._datepicker').datepicker({
            locale: { format: 'YYYY-MM-DD' },
            singleDatePicker: true,
            drops: 'down',
            opens: 'right'
        });
    }
    if ($('.datepicker').length > 0) {
        $('.datepicker').daterangepicker({
            locale: { format: 'YYYY-MM-DD' },
            singleDatePicker: true,
            drops: 'down',
            opens: 'right'
        });
    }

    if ($('.att_datepicker').length > 0) {
        $('.att_datepicker').daterangepicker({
            locale: { format: 'YYYY-MM-DD' },
            singleDatePicker: true,
            drops: 'down',
            opens: 'right'
        });
    }

    if (('.daterange').length > 0) {
        $('.daterange').daterangepicker({
            locale: { format: 'YYYY-MM-DD', separator: ':' },
            drops: 'down',
            opens: 'right'
        });
    }

    if ($('.timepicker').length > 0) {
        $('.timepicker').each((index, el) => {
            let input = $(el);
            input.attr({ readonly: "readonly" });
            input.datetimepicker({
                format: "h:i A",
                formatTime: 'h:i A',
                step: input.hasClass('timetabletime') ? 30 : 10,
                datepicker: false,
                minTime: input.hasClass('timetabletime') ? $.minTimetableTime : false,
                maxTime: input.hasClass('timetabletime') ? $.maxTimetableTime : false
            });
        });
    }
    if ($('.selectpicker').length > 0) {
        $('.selectpicker').select2();
    }
    $('[rel="tooltip"],[data-rel="tooltip"],[data-toggle="tooltip"]').tooltip();
    $('[rel="popover"],[data-rel="popover"],[data-toggle="popover"]').popover();
    $(`div[class~="trix-button-row"] span[class~="trix-button-group--file-tools"], div[class~="trix-button-row"] span[class~="trix-button-group-spacer"]`).remove();
}

var complete_setup_process = () => {
    swal({
        title: "Complete Setup",
        text: `Are you sure you want to complete the setup process?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/account/complete_setup`).then((response) => {
                swal({
                    position: 'top',
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code === 200) {
                    setTimeout(() => {
                        window.location.href = `${baseUrl}`;
                    }, 2000);
                }
            });
        }
    });
}

$(() => {
    initPlugins();
    trigger_form_submit();
    ajax_trigger_form_submit();
});