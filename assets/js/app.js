// Links
$.form_data = {};
$.current_page = "";
$.protocol = window.location.protocol;
$.host = window.location.host;
$.baseurl = $.protocol + "//" + $.host;
$.default = $.protocol + "//" + $.host + "/dashboard";
$.pagecontent = $("#pagecontent");
$.mainprogress = $(".main-progress-bar");
$.pageoverlay = $(".pageoverlay");
$.pageloader = $(".loader");

$.most_recent_page = $(`div[class="last_visited_page"]`).attr("value");
$form_modal = $(`div[id="formsModal"]`);
$replies_modal = $(`div[id="repliesModal"]`);
$form_body = $(`div[id="formsModal"] div[class="modal-body"]`);
$form_header = $(`div[id="formsModal"] h5[class="modal-title"]`);
$form_loaded = $(`div[id="formsModal"] input[class="ajax-form-loaded"]`);
$replies_loaded = $(`div[id="repliesModal"] input[class="ajax-replies-loaded"]`);
$form_loader = `<div class="form-content-loader" style="display: flex; position: absolute;"><div class="offline-content text-center"><p><i class="fa fa-spin fa-spinner fa-2x"></i></p><small style='font-size:12px; padding-top:10px'>Populating Data...</small></div></div>`;
$form_error = `<div class="form-content-loader" style="display: flex; position: absolute;"><div class="offline-content text-center"><p><i class="fa text-warning fa-exclamation-triangle fa-2x"></i></p><small class="text-danger" style='font-size:12px; padding-top:10px'>Error processing request!</small><p><small class="text-danger font-weight-bold cursor" data-dismiss="modal" id="close-div">Close</small></p></div></div>`;
$no_record = `<div class="form-content-loader" style="display: flex; position: absolute;"><div class="offline-content text-center"><p><i class="fa text-warning fa-exclamation-triangle fa-2x"></i></p><small class="text-warning" style='font-size:12px; padding-top:10px'>No content found to display at the moment!</small><p><small class="text-danger font-weight-bold cursor" data-dismiss="modal" id="close-div">Close</small></p></div></div>`;

//Main navigation
$.navigation = $(`aside[id="sidebar-wrapper"] ul[class~="sidebar-menu"] > li`);

$.panelIconOpened = 'icon-arrow-up';
$.panelIconClosed = 'icon-arrow-down';

$.minTimetableTime = '7:00 AM';
$.maxTimetableTime = '6:00 PM';
$.array_stream = {};
$.array_stream["document_breadcrumbs"] = {};
$.today = new Date().toISOString().slice(0, 10);

'use strict';

var form_error = (message) => {
    return `
    <div class="form-content-loader" style="display: flex; position: absolute;">
        <div class="offline-content text-center">
            <p><i class="fa text-warning fa-exclamation-triangle fa-2x"></i></p>
            <small class="text-danger" style='font-size:12px; padding-top:10px'>${message}</small>
            <p><small class="text-danger font-weight-bold cursor" data-dismiss="modal" id="close-div">Close</small></p>
        </div>
    </div>`;
}
$(window).on("beforeunload", (evt) => {
    window.location.href = `${baseUrl}dashboard`;
});

var strings = {
    nextwork_online: "<span class='text-success'>App is Online</span>",
    nextwork_offline: "<span class='text-danger'>App is currently Offline</span>",
}

if (navigator.onLine) {
    $(".network-notifier").html(strings.nextwork_online);
} else {
    $(".network-notifier").html(strings.nextwork_offline);
}

window.addEventListener("online", () => {
    $(".network-notifier").html(strings.nextwork_online);
})

window.addEventListener("offline", () => {
    $(".network-notifier").html(strings.nextwork_offline);
});

var appxhr;

window.onpopstate = (e) => {
    if (e.state === null) return
    var current = e.state.current // || document.location.href
    linkHandler(current, false)
}

function htmlEntities(str) {
    return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

var init_image_popup = () => {
    $(`a[class~="image-popup"]`).magnificPopup({
        type: 'image',
        callbacks: {
            beforeOpen: function() {
                this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure animated zoomInDown');
            }
        },
        gallery: {
            enabled: true
        }
    });
}

function format_currency(total) {
    var neg = false;
    if (total < 0) {
        neg = true;
        total = Math.abs(total);
    }
    return (neg ? "-" : '') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
}

var serializeSelect = (select) => {
    var array = [];
    select.each(function() {
        array.push($(this).val())
    });
    return array;
}

var load = (page_url) => {
    loadPage(`${baseUrl}${page_url}`);
}

$("#history-reload").on("click", function() {
    loadPage(document.location.href);
})

$("#history-back").on("click", function() {
    if (window.history.state === null) return false;
    window.history.back();
})

$("#history-forward").on("click", function() {
    window.history.forward();
});


$("#history-refresh").on("click", function() {
    loadPage($.current_page);
});

$.cachedScript = function(url, options) {
    options = $.extend(options || {}, {
        dataType: "script",
        cache: true,
        url: url
    });
    return $.ajax(options);
};

var play_sound = () => {
    let audio = new Audio("assets/js/beep.mp3");
    audio.play();
}

var logout = async() => {
    await $.post(`${baseUrl}api/auth/logout`).then((resp) => {
        if (resp.code == 200) {
            swal({
                text: "You have successfully been logged out.",
                icon: "success",
            });
            setTimeout(() => {
                window.location.href = `${baseUrl}`
            }, 1500)
        } else {
            notify("Sorry! An unexpected error was encountered.");
        }
    });
}

$(() => {
    init();
    linkClickStopper($(document.body));
    if($.pagecontent.length) {
        linkHandler(document.location.href, true);
    }
    $(document).on('click', '.card-actions a', (e) => {
        e.preventDefault();
        if ($(this).hasClass('btn-close')) {
            $(this).parent().parent().parent().fadeOut();
        } else if ($(this).hasClass('btn-minimize')) {
            var $target = $(this).parent().parent().next('.card-block');
            if (!$(this).hasClass('collapsed')) {
                $('i', $(this)).removeClass($.panelIconOpened).addClass($.panelIconClosed);
            } else {
                $('i', $(this)).removeClass($.panelIconClosed).addClass($.panelIconOpened);
            }
        } else if ($(this).hasClass('btn-setting')) {
            $('#myModal').modal('show');
        }
    });
    initMainMenu();
    $('.trialdismiss').on("click", function() {
        $(this).parents(".trialbox").fadeOut("fast");
    });
    $('.upgradedismiss').on("click", function() {
        $(this).parents(".trialbox").removeClass("visible").addClass("invisible");
    });
    $('.sidebar-close').click(() => {
        $('body').toggleClass('sidebar-opened').parent().toggleClass('sidebar-opened');
    });
    $('a[href="#"][data-top!=true]').click((e) => {
        e.preventDefault();
    });
});

var init = () => {
    $.chatinterval = 5000
    appxhr = []
    initPlugins()
}

var linkClickStopper = (element) => {

    $("a", element).on("click", (event) => {

        if ($(event.currentTarget).hasClass('modal-trigger') ||
            $(event.currentTarget).hasClass('btn-export') ||
            $(event.currentTarget).hasClass('export-btn') ||
            $(event.currentTarget).attr('target') === "_blank" ||
            $(event.currentTarget).attr('role') === "option" ||
            $(event.currentTarget).hasClass('anchor')) {
            return
        }
        event.preventDefault();

        let target = event.currentTarget.href

        if (target === "" || target === undefined ||
            target.substr(target.length - 1) === '#' ||
            target.indexOf('#') >= 0) {
        
            return;
        }

        linkHandler(target, true)
    });
}

var formSubmitStopper = (element) => {
    let forms;
    if (element.is("form")) forms = element;
    else forms = element.find("form");

    if (forms.length) {
        forms.each(function() {
            if ($(this).hasClass("ajaxform")) {
                $(this).on("submit", (event) => {
                    event.preventDefault()
                    loadFormAction($(this))
                })
            }
        })
    }
}

var linkHandler = (target, pushstate) => {
    if (target.includes('/main')) {
        target = target.replace('/main', '/dashboard')
    }
    loadPage(target, pushstate);
}

var deleteReply = function() {
    $(`a[class~="delete-reply"]`).on("click", function() {
        let reply_id = $(this).attr("data-reply-id");
        $.post(`${baseUrl}api/replies/delete`, { reply_id: reply_id }).then((response) => {
            if (response.code == 200) {
                $(`div[data-reply-container="${reply_id}"] div[class="card-body"] [class="tx-14"]`).html(`<div class="font-italic text-">This message was deleted</div>`);
                $(`div[data-reply-container="${reply_id}"] [id="reply-option"]`).remove();
            }
        });
    });
}

var apply_comment_click_handlers = () => {
    $(`span[data-function="toggle-comments-files-attachment-list"]`).on("click", function() {
        let reply_id = $(this).attr("data-reply-id");
        $(`div[class~="attachments_list"][data-reply-id="${reply_id}"]`).slideToggle("slow");
    });

    $(".attachment-container .attachment-item").mouseenter(function() {
        let item = $(this).attr("data-attachment_item");
        $(`span[data-attachment_options='${item}']`).css("display", "block");
    }).mouseleave(function() {
        let item = $(this).attr("data-attachment_item");
        $(`span[data-attachment_options='${item}']`).css("display", "none");
    });

    init_image_popup();
    deleteReply();
}

var trigger_form_submit = () => {
    $(`form[class="ajax-data-form"] button[type="button-submit"]`).on("click", async function(evt) {

        evt.preventDefault();
        let theButton = $(this),
            appendForm_Id = "";
            defaultForm_Id = "ajax-data-form-content";
        let theForm_Id = theButton.attr("data-form_id");

        if(theForm_Id !== undefined) {
            defaultForm_Id = theForm_Id;
            appendForm_Id = `[id="${theForm_Id}"]`;
        }
        let draftButton = $(`form[class="ajax-data-form"]${appendForm_Id} button[type="button-submit"][data-function="draft"]`),
            formAction = $(`form[class="ajax-data-form"]${appendForm_Id}`).attr("action"),
            formMethod = $(`form[class="ajax-data-form"]${appendForm_Id}`).attr("method"),
            formButton = $(`form[class="ajax-data-form"]${appendForm_Id} button[type="button-submit"]`);

        let optional_flow = "We recommend that you save the form as a draft, and review it before submitting. Do you wish to proceed with this action?";
        let myForm = document.getElementById(defaultForm_Id);
        let theFormData = new FormData(myForm);

        let button = theButton.attr("data-function");
        theFormData.append("the_button", button);

        if ($(`form[class="ajax-data-form"]${appendForm_Id} select[name='assigned_to_list']`).length) {
            theFormData.delete("assigned_to_list");
            theFormData.append("assigned_to_list", serializeSelect($(`form[class="ajax-data-form"]${appendForm_Id} select[name="assigned_to_list"]`)));
        }
        
        if ($(`form[class="ajax-data-form"]${appendForm_Id} trix-editor[name="faketext"][id="ajax-form-content"]`).length) {
            theFormData.delete("faketext");
            let content = $(`form[class="ajax-data-form"]${appendForm_Id} trix-editor[id="ajax-form-content"]`).html(),
                form_variable = $(`form[class="ajax-data-form"]${appendForm_Id} trix-editor[id="ajax-form-content"]`).attr("data-predefined_name");
            theFormData.append(form_variable, htmlEntities(content));
        }

        if ($(`form[class="ajax-data-form"]${appendForm_Id} trix-editor[name="faketext_2"][id="ajax-form-content_2"]`).length) {
            theFormData.delete("faketext_2");
            let content = $(`form[class="ajax-data-form"]${appendForm_Id} trix-editor[id="ajax-form-content_2"]`).html();
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
                formButton.prop("disabled", true);
                $.pageoverlay.show();
                $.ajax({
                    url: `${formAction}`,
                    data: theFormData,
                    contentType: false,
                    cache: false,
                    type: formMethod.toUpperCase(),
                    processData: false,
                    success: function(response) {
                        if (response.code == 200) {
                            swal({
                                text: response.data.result,
                                icon: "success",
                            });
                            if (response.data.additional) {
                                if (response.data.additional.clear !== undefined) {
                                    if ($(`form[class="ajax-data-form"]${appendForm_Id} textarea[name="faketext"]`).length) {
                                        CKEDITOR.instances['ajax-form-content'].setData("");
                                    }
                                    if ($(`form[class="ajax-data-form"]${appendForm_Id} trix-editor[name="faketext"][id="ajax-form-content"]`).length) {
                                        $(`form[class="ajax-data-form"]${appendForm_Id} trix-editor[name="faketext"][id="ajax-form-content"]`).html("");
                                    }
                                    $replies_loaded.attr("value", "0");
                                    $replies_loaded.attr("data-form", "none");
                                    $(".modal").modal("hide");
                                    $(`form[class="ajax-data-form"]${appendForm_Id} input, form[class="ajax-data-form"]${appendForm_Id} textarea`).val("");
                                }
                                if (typeof response.data.additional.append !== 'undefined') {
                                    $(`div[id="${response.data.additional.append.div_id}"]`).html(response.data.additional.append.data);
                                }
                                if (typeof response.data.additional.append_data !== 'undefined') {
                                    $(`${response.data.additional.append_data.container}`).after(response.data.additional.append_data.data);
                                    try {
                                        document_handler();
                                        documents_summary();
                                    } catch(err) {}
                                }
                                if (typeof response.data.additional.replace_data !== 'undefined') {
                                    $(`${response.data.additional.replace_data.container}`).html(response.data.additional.replace_data.data);
                                    try {
                                        document_handler();
                                        documents_summary();
                                    } catch(err) {}
                                }
                                if (typeof response.data.additional.delete_divs !== 'undefined') {
                                    $.each(response.data.additional.delete_divs, function(ie, iv) {
                                        $(`${iv}`).remove();
                                    });
                                }
                                if(typeof response.data.additional.array_stream !== 'undefined') {
                                    $.each(response.data.additional.array_stream, function(ie, iv) {
                                        $.each(iv, function(iname, vvalue) {
                                            $.array_stream[ie][iname] = vvalue;
                                        });
                                    }); 
                                }
                                if (typeof response.data.additional.record !== 'undefined') {
                                    $.each(response.data.additional.record, function(ie, iv) {
                                        $(`form[class="ajax-data-form"]${appendForm_Id} input[name="${ie}"]`).val(iv);
                                        $(`[data-record="${ie}"]`).html(iv);
                                    });
                                }
                                if (typeof response.data.additional.href !== 'undefined') {
                                    if (theButton.attr("href") !== undefined) {
                                        setTimeout(() => {
                                            loadPage(theButton.attr("href"));
                                        }, refresh_seconds);
                                    } else {
                                        setTimeout(() => {
                                            loadPage(response.data.additional.href);
                                        }, refresh_seconds);
                                    }
                                }
                                if (typeof response.data.additional.data !== 'undefined') {
                                    preload_AjaxData(response.data.additional.data);
                                }
                            }

                            if (typeof initiateCalendar === "function") {
                                initiateCalendar();
                            }

                            $form_modal.modal("hide");
                            $(`form[class="ajax-data-form"]${appendForm_Id} div[class~="file-preview"]`).html("");
                        } else {
                            if (typeof response.data.result !== 'undefined') {
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
                        $(`form[class="ajax-data-form"]${appendForm_Id} div[id="ajaxFormSubmitModal"]`).modal("hide");
                        $.pageoverlay.hide();
                        formButton.prop("disabled", false);
                    },
                    error: function() {
                        $.pageoverlay.hide();
                        $(`form[class="ajax-data-form"]${appendForm_Id} div[id="ajaxFormSubmitModal"]`).modal("hide");
                        formButton.prop("disabled", false);
                        swal({
                            position: 'top',
                            text: "Sorry! Error processing request.",
                            icon: "error",
                        });
                    }
                });
            }
        });

    });
}

var ajax_trigger_form_submit = () => {

    $(`form[class="_ajax-data-form"] button[type="button-submit"]`).on("click", async function(evt) {

        evt.preventDefault();
        let theButton = $(this),
            draftButton = $(`form[class="_ajax-data-form"] button[type="button-submit"][data-function="draft"]`),
            formAction = $(`form[class="_ajax-data-form"]`).attr("action"),
            formButton = $(`form[class="_ajax-data-form"] button[type="button-submit"]`);

        let optional_flow = "We recommend that you save the form as a draft, and review it before submitting. Do you wish to proceed with this action?";

        let myForm = document.getElementById('_ajax-data-form-content');
        let theFormData = new FormData(myForm);

        let button = theButton.attr("data-function");
        theFormData.append("the_button", button);

        if ($(`select[name='assigned_to_list']`).length) {
            theFormData.delete("assigned_to_list");
            theFormData.append("assigned_to_list", serializeSelect($(`select[name="assigned_to_list"]`)));
        }

        if ($(`trix-editor[name="faketext"][id="_ajax-form-content"]`).length) {
            theFormData.delete("faketext");
            let content = $(`trix-editor[id="_ajax-form-content"]`).html(),
                form_variable = $(`trix-editor[id="ajax-form-content"]`).attr("data-predefined_name");
            form_variable = form_variable === undefined ? "description" : form_variable;
            theFormData.append(form_variable, htmlEntities(content));
        }

        if ($(`trix-editor[name="faketext_2"][id="_ajax-form-content_2"]`).length) {
            theFormData.delete("faketext_2");
            let content = $(`trix-editor[id="_ajax-form-content_2"]`).html();
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
                formButton.prop("disabled", true);
                $.pageoverlay.show();
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
                                    if ($(`textarea[name="faketext"]`).length) {
                                        CKEDITOR.instances['_ajax-form-content'].setData("");
                                    }
                                    if ($(`trix-editor[name="faketext"][id="_ajax-form-content"]`).length) {
                                        $(`trix-editor[name="faketext"][id="_ajax-form-content"]`).html("");
                                    }
                                    $replies_loaded.attr("value", "0");
                                    $replies_loaded.attr("data-form", "none");
                                    $(".modal").modal("hide");
                                    $(`form[class="_ajax-data-form"] select`).val("").change();
                                    $(`form[class="_ajax-data-form"] input, form[class="_ajax-data-form"] textarea`).val("");
                                }
                                if (response.data.additional.append !== undefined) {
                                    $(`div[id="${response.data.additional.append.div_id}"]`).html(response.data.additional.append.data);
                                }
                                if (response.data.additional.record !== undefined) {
                                    $.each(response.data.additional.record, function(ie, iv) {
                                        $(`form[class="_ajax-data-form"] input[name="${ie}"]`).val(iv);
                                        $(`[data-record="${ie}"]`).html(iv);
                                    });
                                }
                                if (response.data.additional.href !== undefined) {
                                    if (theButton.attr("href") !== undefined) {
                                        setTimeout(() => {
                                            loadPage(theButton.attr("href"));
                                        }, refresh_seconds);
                                    } else {
                                        setTimeout(() => {
                                            loadPage(response.data.additional.href);
                                        }, refresh_seconds);
                                    }
                                }
                                if (response.data.additional.data !== undefined) {
                                    preload_AjaxData(response.data.additional.data);
                                }
                            }

                            if (typeof initiateCalendar === "function") {
                                initiateCalendar();
                            }

                            $form_modal.modal("hide");
                            $(`form[class="_ajax-data-form"] div[class~="file-preview"]`).html("");
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
                        $.pageoverlay.hide();
                        formButton.prop("disabled", false);
                        $(`div[id="ajaxFormSubmitModal"]`).modal("hide");
                    },
                    error: function() {
                        $.pageoverlay.hide();
                        $(`div[id="ajaxFormSubmitModal"]`).modal("hide");
                        formButton.prop("disabled", false);
                        swal({
                            position: 'top',
                            text: "Sorry! Error processing request.",
                            icon: "error",
                        });
                    }
                });
            }
        });

    });
}

var page_programming = (array_content) => {
    $(`div[id="content_menu_display"]`).html(``);
    if(Object.keys(array_content).length) {
        let menu_content = `
        <div class="content-menu">
            <div class="d-flex justify-content-between">`,
            left_count = 0;
            if(array_content.left !== undefined) {
                left_count = Object.keys(array_content.left).length;
            }
            if(left_count == 0) {
                menu_content += `<div></div>`;
            } else {
                menu_content += `<div>`;
                $.each(array_content.left, function(key, value) {
                    menu_content += `
                    <button class="btn btn-plain" type="button">
                        <strong>${key}</strong> 
                        <span class="text-muted-dark">${myPrefs.labels.currency}</span> ${value}
                    </button>`;
                });
                menu_content += `</div>`;
            }
        if(array_content.right !== undefined) {
            menu_content += "<div>";
                right_list = array_content.right;
            $.each(right_list, function(key, value) {
                menu_content += `<button onclick="load('${key}')" class="btn ${value[2]}"><i class="fa ${value[1]}"></i> ${value[0]}</button>`;
            });
            menu_content += `</div>`;
            $(`div[id="content_menu_display"]`).html(menu_content);
        }
        menu_content += `
            </div>
        </div>`;
    }
}

var loadPage = (loc, pushstate) => {
    
    if (loc == `${$.baseurl}` || loc == `${$.baseurl}/dashboard`) {
        $(`[id="history-refresh"]`).addClass("hidden");
    } else {
        $(`[id="history-refresh"]`).removeClass("hidden");
    }

    if((loc == $.default) || $(`div[id="load_dashboard_content"]`).length) {
        let data_url = $(`div[id="load_dashboard_content"]`).data('loadUrl');
        if(typeof data_url !== 'undefined') {
            loc = data_url;
        }
    }

    $.pageoverlay.show();
    $.ajax({
        url: loc,
        data: $.form_data,
        method: "POST",
        dataType: "JSON",
        beforeSend: () => {
            $.mainprogress.show();
        },
        success: (result) => {
            $(`div[id="viewOnlyModal"]`).modal("hide");
            $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html("");
            $(`div[class~="toggle-calculator"]`).addClass("hidden");
            $(`div[class~="calculator"] div[class~="display"], div[class~="calculator"] div[class~="all-buttons"]`).addClass("hidden");
            if (typeof result.redirect !== "undefined") {
                let redirectUrl = $.baseurl + "/" + result.redirect;
                window.location.href = redirectUrl
                return false;
            }

            $.current_page = loc;

            if (result.scripts !== undefined) {
                let timer = 200,
                    increment = result.timer !== undefined ? result.timer : 1000;
                $.each(result.scripts, function(ii, ie) {
                    try {
                        setTimeout(() => {
                            $.cachedScript(`${baseUrl}${ie}?v=${appVersion}`);
                        }, timer);
                        timer += increment;
                    } catch (err) {}
                });
            }

            if(result.page_programming !== undefined) {
                page_programming(result.page_programming)
            }

            $.pagecontent.html($.parseHTML(result.html));

            if (result.client_auto_save !== undefined) {
                client_auto_save = result.client_auto_save;
            }

            if (result.array_stream !== undefined) {
                $.each(result.array_stream, function(i, e) {
                    if(i !== "replace_array_value") {
                        $.array_stream[i] = e;
                    }
                });
                if(result.array_stream["replace_array_value"] !== undefined) {
                    $.each(result.array_stream["replace_array_value"], function(i, e) {
                        $.array_stream[i] = e;
                    });
                }
            }

            if(result.current_user_url !== undefined) {
                current_url = result.current_user_url;
            }

            if(typeof result.notification_engine !== "undefined") {
                $(`div[class="section-header"]`).append($.parseHTML(result.notification_engine));
            }

            $(`div[id="load_dashboard_content"]`).remove();
            $(`div[class~="settingSidebar"] input`).val("");
            $(`div[class~="settingSidebar"]`).removeClass("showSettingPanel");
            $(`div[id="dictionary_query_results"], div[id="system_query_results"]`).html("");

            document.title = `${result.title} :: ${clientName}`;

            init();
            initDataTables();
            init_image_popup();
            linkClickStopper($.pagecontent);
            formSubmitStopper($.pagecontent);
            trigger_form_submit();
            ajax_trigger_form_submit();
        },
        complete: () => {
            var prev = window.history.state === null ? null : window.history.state.current
            if (pushstate !== false) window.history.pushState({ previous: prev, current: loc }, "", loc)

            if (window.history.state === null) {
                $("#history-back, #history-forward").addClass("disabled");
            } else if (window.history.state.previous === null) {
                $("#history-back").addClass("disabled");
            } else {
                $("#history-back").removeClass("disabled");
            }
            $.pageoverlay.hide();
            setActiveNavLink();
            $('body, html').scrollTop(0);
        },
        error: (err) => {
            $.pageoverlay.hide();
            if ([404, 500].includes(err.status)) {
                swal({
                    title: err.status === 404 ? "404" : "OOPS!",
                    text: err.status === 404 ? "Page Not Found\nThe page you are requesting cannot be found" : "Something went wrong. Please try again",
                    icon: "error",
                });
            }
        }
    })
}

var refreshChatList = (data) => {
    $.each(data, function(i, record) {
        if(record.user_id == current_focused_user_id) {
            $(`div[class="chat-num-messages"]`).html(`${record.offline_ago}`);
        }
        $(`li[data-user_id="${record.user_id}"] div[class="status"]`).html(`
            <i class="material-icons ${record.online ? 'online' : 'offline'}">fiber_manual_record</i>
            ${record.online ? 'Online' : `Left ${record.offline_ago}`}
            <span data-user_id="${record.user_id}" class="float-right">
        `);
    });
}

var new_message_alert = () => {
    let usersList = [];
    $.each($(`ul[class~="chat-list"] li`), function(evt) {
        usersList.push($(this).attr('data-user_id'));
    });
    $.post(`${baseUrl}api/chats/alerts`, { users: usersList }).then((response) => {
        if (response.code === 200) {
            let counter = 0;
            $.each(response.data.result['alerts'], function(i, e) {
                counter += e.count.chats_count
                if($(`span[data-user_id="${e.count.sender_id}"]`).length) {
                    $(`span[data-user_id="${e.count.sender_id}"]`).html(`<i class="fa text-warning fa-comments"></i> ${e.count.chats_count}`);
                } else {
                    let online_text = e.count.online ? "online" : "offline",
                    online_msg = e.count.online ? "Online" : `Left ${e.offline_ago}`;
                    $(`div[id="chat-scroll"] ul[class~="chat-list"]`).append(`
                        <li id="default_list" style="width:100%" data-message_id="${e.count.message_unique_id}" onclick="return display_messages('${e.count.message_unique_id}','${e.count.sender_id}','${e.count.name}','${baseUrl}${e.count.image}','${e.count.offline_ago}')" class="clearfix d-flex">
                            <img src="${baseUrl}${e.count.image}" alt="avatar">
                            <div class="about" style="width:100%">
                                <div class="name">${e.count.name}</div>
                                <div class="status">
                                    <i class="material-icons ${online_text}">fiber_manual_record</i>
                                    ${online_msg}
                                    <span data-user_id="${e.count.sender_id}" class="float-right"></span>
                                </div>
                            </div>
                        </li>`);
                    play_sound();
                }
                if(e.count.sender_id === current_focused_user_id) {
                    $(`div[class="chat-num-messages"]`).html(`${e.count.offline_ago}`);
                    $(`li[data-user_id="${current_focused_user_id}"] div[class="status"]`).html(`
                        <i class="material-icons online">fiber_manual_record</i>Online
                        <span data-user_id="${current_focused_user_id}" class="float-right">
                    `);
                    $.each(e.messages_list, function(ii, chat) {
                        $(`div[class~="chat-box"] div[class~="chat-content"]`).append(
                            `<div class="chat-item chat-left" style="display:block">
                                <img src="${baseUrl}${chat.image}">
                                <div class="chat-details">
                                <div class="chat-text">${chat.message}</div>
                                <div class="chat-time">${chat.sent_time}</div>
                                </div>
                            </div>`
                        );
                    });
                    $(`div[class~="chat-content"]`).animate({ scrollTop: $(`div[class~="chat-content"]`).prop("scrollHeight") }, 0);
                    $(`span[data-user_id="${e.count.sender_id}"]`).html(``);
                    $.post(`${baseUrl}api/chats/read`, {message_id: e.message_id});
                }
            });
            refreshChatList(response.data.result['online']);
            if (counter !== 0) {
                $(`a[data-notification="message"]`).addClass("beep");
            } else {
                $(`a[data-notification="message"]`).removeClass("beep");
            }
        }
    });
}

var loadFormAction = (form) => {

    swal({
        title: "Submit Form",
        text: "Are you sure you want to submit this form for processing?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.ajax({
                url: form[0].action,
                method: form[0].method,
                data: new FormData(form[0]),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: () => {
                    $.pageoverlay.show();
                },
                success: (result) => {
                    var error = result.code === 200 ? null : result.data.result || null;
                    if (error !== null) notify(error)
        
                    if (result.code == 200) {
                        swal({
                            position: 'top',
                            text: result.data.result,
                            icon: "success",
                        });
                        if (result.data.additional !== undefined) {
                            if (result.data.additional.clear !== undefined) {
                                $(`form[class~="ajaxform"] input, form[class~="ajaxform"] textarea`).val("");
                                $(`form[class~="ajaxform"] select`).val("null").change();
                            }
                            if (result.data.additional.href !== undefined) {
                                loadPage(result.data.additional.href);
                            }
                        }
                    }
                },
                complete: () => {
                    $.pageoverlay.hide();
                },
                error: (err) => {
                    $.pageoverlay.hide();
                }
            });
        }
    });
}

var initMainMenu = () => {
    $.navigation.on('click', 'a', (e) => {
        if ($.ajaxLoad) {
            e.preventDefault();
        }
    });
    $(".aside-menu .nav-item").on("click", (e) => {
        let $this = $(e.currentTarget)
        $this.siblings(".nav-item").children('.nav-link').removeClass('active');
        $this.children('.nav-link').addClass('active');
    });
    $('.navbar-toggler').click((e) => {
        let $this = $(e.currentTarget)
        if ($this.hasClass('sidebar-toggler')) {
            if ($('body').hasClass('sidebar-minimized')) {
                $(".nav-link.nav-dropdown-toggle, .nav-link.active").hide();
                $('body').addClass('sidebar-hidden').removeClass('sidebar-minimized');
            } else if ($('body').hasClass('sidebar-hidden')) {
                $(".nav-link.nav-dropdown-toggle, .nav-link.active").show();
                $('body').removeClass('sidebar-hidden');
            } else {
                $(".nav-link.nav-dropdown-toggle, .nav-link.active").show();
                $('body').toggleClass('sidebar-minimized');
            }
        }
    });
}

var the_date_picker = () => {

    if ($('._datepicker').length > 0) {
        $('._datepicker').datepicker();
    }
    if ($('.datepicker').length > 0) {
        $('.datepicker').each((index, el) => {
            let input = $(el);
            let date_format = input.attr("date-format") == undefined ? "YYYY-MM-DD" : input.attr("date-format"),
                max_date = input.attr("data-maxdate") == undefined ? $.today : input.attr("data-maxdate"),
                min_date = input.attr("data-mindate") == undefined ? $(`input[name="minimum_date"]`).val() : input.attr("data-mindate");
            input.daterangepicker({
                locale: { format: date_format },
                singleDatePicker: true,
                showDropdowns: true,
                maxDate: max_date,
                minDate: min_date
            });
        });
    }
    if ($('.att_datepicker').length > 0) {
        $('.att_datepicker').daterangepicker({
            locale: { format: 'YYYY-MM-DD' },
            singleDatePicker: true,
            showDropdowns: true,
            maxDate: $.today
        });
    }
}

var initPlugins = () => {
    
    if ($('.monthyear').length > 0) {
        $('.monthyear').daterangepicker({
            locale: { format: 'YYYY-MM' },
            singleDatePicker: true,
            maxDate: $.today,
            drops: 'down'
        });
    }

    the_date_picker();

    if (('.daterange').length > 0) {
        $('.daterange').daterangepicker({
            locale: { format: 'YYYY-MM-DD', separator: ':' },
            drops: 'down'
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
        $('.selectpicker').each((index, el) => {
            let select = $(el),
                title = select.attr("data-select-title"),
                itemText = select.attr("data-itemtext"),
                itemsText = select.attr("data-itemstext"),
                width = select.attr("data-select-width"),
                maxOptions = select.attr("data-select-max");

            select.select2();
        });
    }
    // ---------- Tooltip ---------- 
    $('[rel="tooltip"],[data-rel="tooltip"],[data-toggle="tooltip"]').tooltip();

    // ---------- Popover ---------- 
    $('[rel="popover"],[data-rel="popover"],[data-toggle="popover"]').popover();

    if ($(".icheckbox").length > 0) {
        $(".icheckbox,.iradio").iCheck({ checkboxClass: 'icheckbox_minimal-grey', radioClass: 'iradio_minimal-grey' });
    }

    if ($(".lightbox").length) {
        $(".lightbox").nivoLightbox()
    }

    $(`div[class~="trix-button-row"] span[class~="trix-button-group--file-tools"], div[class~="trix-button-row"] span[class~="trix-button-group-spacer"]`).remove();
}

var setActiveNavLink = () => {

    let location = window.location.href,
        screen_width = $(window).width();

    $.navigation.removeClass('active');
    $("ul.dropdown-menu").css("display", "none");

    if(screen_width < 900) {
        $(`body`).removeClass("sidebar-show").addClass("sidebar-gone");
    } else {
        $(`body`).removeClass("sidebar-gone").addClass("sidebar-show");
        $.navigation.find('a').each((index, el) => {
            if ($(el)[0].href == location) {
                let parentDropdown = $(el).parent("li").parent("ul");
                parentDropdown.css("display", "block");
            } else {}
        });
    }
}

var initDataTables = () => {

    try {
        if ($('.datatable').length > 0) {
            let rows_count = $(`table[class~="datatable"]`).attr("data-rows_count") == undefined ? 20 : $(`table[class~="datatable"]`).attr("data-rows_count");
            $('.datatable').DataTable({
                search: null,
                lengthMenu: [
                    [rows_count, 30, 50, 75, 100, 200, -1],
                    [rows_count, 30, 50, 75, 100, 200, "All"]
                ],
                language: {
                    sEmptyTable: "Nothing Found",
                    lengthMenu: "Display _MENU_ rows"
                },
                dom: '<"row"<"col-lg-12 mb-2">><"row d-flex justify-content-between"<B><"align-left"><f>>rt<"bottom"ip><"clear">',
                buttons: ['excel', 'pdf', 'print'],
                initComplete: function() {
                    $('.buttons-excel').html('<i class="fa fa-file-excel"></i>')
                        .removeClass("btn-success buttons-excel")
                        .addClass("btn font-18 btn-outline-success")
                        .attr("title", "Export Table to Excel");
                    $('.buttons-pdf').html('<i class="fa fa-file-pdf"></i>')
                        .removeClass("buttons-pdf")
                        .addClass("btn font-18 btn-outline-danger")
                        .attr("title", "Export Table to PDF");
                    $('.buttons-print').html('<i class="fa fa-print"></i>')
                        .removeClass("btn-secondary buttons-print")
                        .addClass("btn font-18 btn-outline-secondary print-buttons")
                        .attr("title", "Print Table Content");
                }
            });
        }
    
        if ($(`table[class~="raw_datatable"]`).length > 0) {
            let t_order = $(`table[class~="raw_datatable"]`).attr("data-order_item") == undefined ? "asc" : $(`table[class~="raw_datatable"]`).attr("data-order_item");
            let rows_count = $(`table[class~="raw_datatable"]`).attr("data-rows_count") == undefined ? 15 : $(`table[class~="raw_datatable"]`).attr("data-rows_count");
            $(`table[class~="raw_datatable"]`).DataTable({
                order: [0, t_order],
                search: null,
                lengthMenu: [
                    [rows_count, 30, 50, 75, 100, 200, -1],
                    [rows_count, 30, 50, 75, 100, 200, "All"]
                ],
                language: {
                    sEmptyTable: "Nothing Found",
                    lengthMenu: "Display _MENU_ rows"
                }
            });
        }
    } catch(err) {}
}

var moneyFormat = (value, decimals) => {
    value = value.toFixed(decimals)
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    return value
}

var notify = (text, type = "error") => {
    $.notify(text, type);
}

var randomInt = (min, max) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min; //The maximum is exclusive and the minimum is inclusive
}

var form_processing = () => {

    $(`select[name="related_to"]`).on("change", function() {
        let value = $(this).val(),
            module = $(this).attr("data-module"),
            related_to_id = $(`select[name="related_to_id"]`);
        if (value == "null") {
            related_to_id.find('option').remove().end();
            related_to_id.append(`<option value="">Please select item</option>`);
            return;
        }

        related_to_id.find('option').remove().end();
        related_to_id.append(`<option value="">Select select item</option>`);
        $.get(`${baseUrl}api/related/list?module=${module}&related_item=${value}`).then((response) => {
            if (response.code == 200) {
                select_options("related_to_id", response.data.result, "Please select item");
            }
        });
    });

    trigger_form_submit();
    ajax_trigger_form_submit();
}

var select_option_filter = () => {
    $(`div[id="formsModal"] div[class="modal-body"] select[name="class_id"]`).on("change", function() {
        let value = $(this).val();
        $(`div[id="formsModal"] div[class="modal-body"] select[name='student_id']`).find('option').remove().end();
        if (value.length && value !== "null") {
            $.get(`${baseUrl}api/users/quick_list?class_id=${value}&user_type=student&minified=minidata`).then((response) => {
                if (response.code == 200) {
                    $(`select[name='student_id']`).append(`<option value="">Select Student</option>`);
                    $.each(response.data.result, function(i, e) {
                        $(`div[id="formsModal"] div[class="modal-body"] select[name='student_id']`)
                            .append(`<option value='${e.user_id}'>${e.name.toUpperCase()}</option>`);
                    });
                }
            });
        }
    });
}

var form_loader = async(form_module, module_item_id) => {
    let $module = {
        label: form_module,
        item_id: module_item_id,
        content: "form"
    }
    $form_loaded.attr("value", 0);
    $form_loaded.attr("data-form", "none");

    await $.post(`${baseUrl}api/forms/load`, { module: $module }).then((response) => {
        if (response.code === 401) {
            $form_body.html(form_error(response.description));
        } else if (response.code !== 200) {
            $form_body.html($form_error);
        } else {
            let formRecord = response.data.result;

            if (!formRecord.form) {
                $form_body.html($form_error);
                return false;
            }

            $form_loaded.attr("value", 1);
            $form_loaded.attr("data-form", form_module);
            $form_body.html(formRecord.form);

            select_option_filter();
            initPlugins();
            init_image_popup();

            $(`div[class~="trix-button-row"] span[class~="trix-button-group--file-tools"], div[class~="trix-button-row"] span[class~="trix-button-group-spacer"]`).remove();

            if (formRecord.resources) {
                var time = 100;
                $.each(formRecord.resources, function(ii, ie) {
                    setTimeout(function() {
                        $.cachedScript(`${baseUrl}${ie}`);
                    }, time);
                    time += 500;
                });
            }
            if (formRecord.content) {
                $.each(formRecord.content, function(key, value) {
                    $(`trix-editor[id="${key}"]`).html(`${value}`);
                });
            }
            form_processing();
        }
    }).catch(() => {
        $form_body.html($form_error);
    });
}

var load_quick_form = async(_module, module_item_id) => {
    $replies_modal.modal("hide");
    $form_header.html(form_modules[_module]);

    if (_module == "user_basic_information") {
        let user_info = await load_idb_record("user_list", module_item_id);
        if (user_info) {
            $basic_data_modal.modal("show");
            $basic_data_header.html(form_modules[_module]);
            preview_user_info(user_info);
            return false;
        }
    }

    $form_modal.modal("show");
    $form_body.html($form_loader);

    form_loader(_module, module_item_id);
}

var delete_existing_file_attachment = async(record_id) => {
    swal({
        title: "Delete File",
        text: "Are you sure you want to delete this existing file?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(async (proceed) => {
        if (proceed) {
            let _module = {
                "module": "remove_existing",
                "label": record_id
            };
            $(`div[data-file_container="${record_id}"]`).append(`
            <div class="form-content-loader" style="display: flex; position: absolute">
                <div class="offline-content text-center">
                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                </div>
            </div>`);
            await $.post(`${baseUrl}api/files/attachments`, _module).then((response) => {
                if (response.code == 200) {
                    notify(response.data.result, "success");
                    $(`div[data-file_container="${record_id}"]`).remove();
                }
            });
        }
    });
}

var format_followup_thread = (data) => {
    return `
    <div class="col-md-12 p-0 grid-margin" id="comment-listing" data-reply-container="${data.item_id}">
        <div class="card mb-4 rounded replies-item">
            <div class="card-header pb-0 mb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img width="40px" class="img-xs rounded-circle" src="${baseUrl}${data.created_by_information.image}" alt="">
                        <div class="ml-2">
                            <p class="cursor underline m-0" title="Click to view summary information about ${data.created_by_information.name}" onclick="return user_basic_information('${data.created_by}')" data-id="${data.created_by}">${data.created_by_information.name}</p>
                            <p title="${data.date_created}" class="tx-11 mb-2 replies-timestamp text-muted">${data.time_ago}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body mt-0 pt-2 pb-2">
                <div class="tx-14">${data.description}</div>
            </div>
        </div>
    </div>`;
}

var post_incident_followup = (user_id, incident_id) => {
    $(`button[id="post_incident_followup"][data-resource_id="${incident_id}"]`)
        .attr("disabled", true)
        .html("Processing! Please wait...");
    let content = $(`textarea[name="incident_followup"]`).val();

    let comment = {
        incident_id: incident_id,
        user_id: user_id,
        comment: content
    };

    if (content.length < 5) {
        notify("The comment field cannot be empty");
        $(`button[id="post_incident_followup"][data-resource_id="${incident_id}"]`)
            .attr("disabled", false)
            .html("Share Comment");
        return false;
    }

    $(`[data-threads="cancel_policy_list"]`).attr("data-autoload", "true");

    $.post(`${baseUrl}api/incidents/add_followup`, comment).then((response) => {
        if (response.code == 200) {
            if (response.data.additional.data !== undefined) {
                $(`div[id="no_message_content"]`).remove();
                let the_comment = format_followup_thread(response.data.additional.data);
                if ($(`div[id="formsModal"] div[id="incident_log_followup_list"] div[id="comment-listing"]:first`).length) {
                    $(`div[id="formsModal"] div[id="incident_log_followup_list"] div[id="comment-listing"]:first`).before(the_comment);
                } else {
                    $(`div[id="formsModal"] div[id="incident_log_followup_list"]`).append(the_comment);
                }
                $(`textarea[name="incident_followup"]`).val("");
            }
        } else {
            notify(response.data.result);
        }
        $(`button[id="post_incident_followup"][data-resource_id="${incident_id}"]`)
            .attr("disabled", false)
            .html("Share Comment");
    }).catch(() => {
        $(`button[id="post_incident_followup"][data-resource_id="${incident_id}"]`)
            .attr("disabled", false)
            .html("Share Comment");
        notify("Sorry! Error processing request.");
    });

}

var form_submit_stopper = () => {
    let forms;
    let element = $.pagecontent;
    if (element.is("form")) forms = element;
    else forms = element.find("form");

    if (forms.length) {
        forms.each(function() {
            if ($(this).hasClass("_ajaxform")) {
                $(this).on("submit", (event) => {
                    event.preventDefault()
                    load_form_action($(this))
                })
            }
        })
    }
}

var clear_input = () => {
    $(`div[class~="member_item"] textarea, div[class~="member_item"] input`).val("");
    $(`div[class~="member_item"] input[id='log_date']`).val($(`input[id="todays_date"]`).val());
    $(`div[class~="member_item"] select`).val("").change();
    $(`div[id="log_attendance_container"] div[class~="member_item"]`).not(`:first`).remove();
}

var load_form_action = (form) => {

    swal({
        title: "Log Attendance",
        text: "Are you sure you want to log attendance?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            $.ajax({
                url: form[0].action,
                method: form[0].method,
                data: new FormData(form[0]),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: (result) => {
                    if (result.code == 200) {
                        swal({
                            position: 'top',
                            text: result.data.result,
                            icon: "success",
                        });
                        if (result.data.additional !== undefined) {
                            if (result.data.additional.clear !== undefined) {
                                $(`form[class~="_ajaxform"] input, form[class~="_ajaxform"] textarea`).val("");
                                $(`form[class~="_ajaxform"] select`).val("null").change();
                                clear_input();
                            }
                            if (result.data.additional.href !== undefined) {
                                setTimeout(() => {
                                    loadPage(result.data.additional.href);
                                }, refresh_seconds);
                            }
                        }
                    } else {
                        swal({
                            position: 'top',
                            text: result.data.result,
                            icon: "error",
                        });
                    }
                },
                complete: () => {
                    $.pageoverlay.hide();
                },
                error: (err) => {
                    $.pageoverlay.hide();
                    swal({
                        position: 'top',
                        text: 'Sorry! An unknown error was encountered',
                        icon: "error",
                    });
                    $(`div[class="pageoverlay"]`).css("display", "none");
                }
            });
        }
    });
}

var show_defaultPassword = () => {
    let pass = $(`span[id='default_password']`).html();
    if(pass.includes('Password')) {
        $(`span[id='default_password']`).html('**************');
    } else {
        $(`span[id='default_password']`).html(defaultPassword);
    }
}

let timePlugins = () => {
  var tp_clock = function(){
    function tp_clock_time() {
      let now   = new Date(),
        hour    = now.getHours(),
        minutes = now.getMinutes(),
        seconds = now.getSeconds();

      let   ampm  = hour >= 12 ? "PM" : "AM";
            hour = hour > 12 ? hour-12 : hour;
            minutes = minutes < 10 ? `0${minutes}` : minutes;
            // hour = hour < 10 ? `0${hour}` : hour;
      $(".plugin-clock").html(`${hour} <span>:</span> ${minutes} ${ampm}`);
    }
    if($(".plugin-clock").length > 0){
      tp_clock_time();
      window.setInterval(function(){
        tp_clock_time();
      }, 5000);
    }
  } 
  return {
    init: () => {
      tp_clock();
    }
  }
}

var online_check = () => {
    $.post(`${baseUrl}api/devlog/auth`, { onlineCheck: true }, (response) => {
        if(typeof response.result !== "undefined") {
            if($(`div[class="section-header"]`).length) {
                if(typeof response.notification_engine !== "undefined") {
                    if(response.notification_engine.length) {
                        $(`div[class="notification-engine"]`).remove();
                    }
                    $(`div[class="section-header"]`).after($.parseHTML(response.notification_engine));
                }
            }
        }
    });
}

$(window).on("load", function() {
    if($(`div[class="settingSidebar"]`).length) {
        online_check();
        setInterval(() => { 
            online_check();
        }, 10000);
    }
    if($(`div[id="transitioning_data"]`).length) {
        setInterval(() => {
            $.post(`${baseUrl}api/devlog/transitioning`, { activeCheck: true }, (response) => {
                if(typeof response.result !== "undefined") {
                    if(response.result == "Active") {
                        window.location.reload();
                    }
                }
            });
        }, 30000);
    }
});