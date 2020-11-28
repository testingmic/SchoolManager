/*****
 * CONFIGURATION
 */

// Links
$.protocol = window.location.protocol;
$.host = window.location.host;
$.baseurl = $.protocol + "//" + $.host + "/myschool_gh";
$.default = $.protocol + "//" + $.host + "/myschool_gh/main";
$.pagecontent = $("#pagecontent");
$.mainprogress = $(".main-progress-bar");
$.pageoverlay = $(".pageoverlay");
$.pageloader = $(".loader");
$.env = "development";
$.chatinterval = 2000;

$form_modal = $(`div[id="formsModal"]`);
$replies_modal = $(`div[id="repliesModal"]`);
$form_body = $(`div[id="formsModal"] div[class="modal-body"]`);
$form_header = $(`div[id="formsModal"] h5[class="modal-title"]`);
$form_loaded = $(`div[id="formsModal"] input[class="ajax-form-loaded"]`);
$form_loader = `<div class="form-content-loader" style="display: flex; position: absolute;"><div class="offline-content text-center"><p><i class="fa fa-spin fa-spinner fa-2x"></i></p><small style='font-size:12px; padding-top:10px'>Populating Data...</small></div></div>`;
$form_error = `<div class="form-content-loader" style="display: flex; position: absolute;"><div class="offline-content text-center"><p><i class="fa text-warning fa-exclamation-triangle fa-2x"></i></p><small class="text-danger" style='font-size:12px; padding-top:10px'>Error processing request!</small><p><small class="text-danger font-weight-bold cursor" data-dismiss="modal" id="close-div">Close</small></p></div></div>`;
$no_record = `<div class="form-content-loader" style="display: flex; position: absolute;"><div class="offline-content text-center"><p><i class="fa text-warning fa-exclamation-triangle fa-2x"></i></p><small class="text-warning" style='font-size:12px; padding-top:10px'>No content found to display at the moment!</small><p><small class="text-danger font-weight-bold cursor" data-dismiss="modal" id="close-div">Close</small></p></div></div>`;

//Main navigation
$.navigation = $(`aside[id="sidebar-wrapper"] ul[class~="sidebar-menu"] > li`);

$.panelIconOpened = 'icon-arrow-up';
$.panelIconClosed = 'icon-arrow-down';

$.minTimetableTime = '7:00 AM';
$.maxTimetableTime = '6:00 PM';

'use strict';
var devlog = $.env == "development" ? console.log : () => {}

$(window).on("beforeunload", (evt) => {
    window.location.href = `${baseUrl}main`;
});

var strings = {
    nextwork_online: "<span class='text-success'>Online</span>",
    nextwork_offline: "<span class='text-danger'>Offline</span>",
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
})

var appxhr;

window.onpopstate = (e) => {
    if (e.state === null) return
    var current = e.state.current // || document.location.href
    linkHandler(current, false)
}

function htmlEntities(str) {
    return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

$("#lock-system").on("click", function(e) {
    e.preventDefault();
    if ($(this).hasClass("disabled")) {
        $(".upgradebox").removeClass("invisible").addClass("visible");
        return false;
    }
    setApplicationLock("lock").then(() => {
        $(".lockbox").removeClass("invisible").addClass("visible");
    })
})

$("#history-reload").on("click", function() {
    linkHandler(document.location.href, false)
})

$("#history-back").on("click", function() {
    // console.log("before", window.history.state)
    if (window.history.state === null) return false;
    window.history.back();
})

$("#history-forward").on("click", function() {
    window.history.forward();
});

var require = require || undefined

if (typeof module === "undefined" && require !== undefined) {
    const electron = require("electron");
    const { ipcRenderer } = electron;
    var webContents = electron.remote.getCurrentWebContents();

    ipcRenderer.on("menu:shortcut", (event, shortcut) => {
        switch (shortcut) {
            case "goback":
                window.history.back()
                break;
            case "goforward":
                window.history.forward()
                break;
            case "search":
                $('.navbar-toggler.aside-menu-toggler.search-button').trigger('click');
                break;
            case "dictionary":
                $('.navbar-toggler.aside-menu-toggler.dictionary-button').trigger('click');
                break;
            case "calculator":
                $('.navbar-toggler.aside-menu-toggler.calculator-button').trigger('click');
                break;
            case "lockpage":
                setApplicationLock("lock").then(() => {
                    $(".lockbox").removeClass("invisible").addClass("visible");
                });
                break;
        }
    });
}

$.cachedScript = function(url, options) {
    options = $.extend(options || {}, {
        dataType: "script",
        cache: true,
        url: url
    });
    return $.ajax(options);
};

$(() => {
    init()
    linkClickStopper($(document.body))
    linkHandler(document.location.href, true)

    // CARDS ACTIONS
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

    initMainMenu()
    initQuickSearch()
    initDictionary()
    processPreferences()
    processActivation()
    processAppLock()

    if ($("#chat-page").length === 0 && $.env !== "development") {
        // fetchUserChats();
    }

    $('.trialdismiss').on("click", function() {
        $(this).parents(".trialbox").fadeOut("fast");
    });

    $('.upgradedismiss').on("click", function() {
        $(this).parents(".trialbox").removeClass("visible").addClass("invisible");
    });

    // $('.sidebarcalculator').calculator({ showFormula: true });

    $('.sidebar-close').click(() => {
        $('body').toggleClass('sidebar-opened').parent().toggleClass('sidebar-opened');
    });

    // ---------- Disable moving to top ---------- 
    $('a[href="#"][data-top!=true]').click((e) => {
        e.preventDefault();
    });
});

var init = () => {

    $.chatinterval = 2000
    appxhr = []
    initPlugins()
    initExportButtons()
}

var linkClickStopper = (element) => {

    devlog("linkClickStopper(" + element.selector + "). I'm waiting for click =>")

    $("a", element).on("click", (event) => {

        if ($(event.currentTarget).hasClass('modal-trigger') ||
            $(event.currentTarget).hasClass('btn-export') ||
            $(event.currentTarget).hasClass('export-btn') ||
            $(event.currentTarget).attr('target') === "_blank" ||
            $(event.currentTarget).attr('role') === "option" ||
            $(event.currentTarget).hasClass('anchor')) {
            return
        }
        event.preventDefault()

        let target = event.currentTarget.href

        if (target === "" || target === undefined ||
            target.substr(target.length - 1) === '#' ||
            target.indexOf('#') >= 0) {
            devlog("page anchor", target)
            return;
        }

        devlog("CLICK! I called =>")
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
    devlog("linkHandler(). I called =>")

    let callback = getCallback(target)

    if (target.slice(0, -1) === $.baseurl || target === $.baseurl || target === $.default) {
        target = $.baseurl + "/dashboard"
    }

    loadPage(target, callback, pushstate)
}

let getCallback = (target) => {

    var splitlink = String(target).split("/")
    var mainpage = splitlink[3] || ""
    var subpage = splitlink[4]
    let callback;
    devlog("mainpage: ", mainpage)

    switch (mainpage) {
        case "myschool_gh":
            callback = initDashboard;
            break;
        case "dashboard":
            callback = initDashboard;
            break
        case "academic":
            callback = initAcademic
            break
        case "students":
            callback = initStudents
            break
        case "staff":
            callback = initStaff
            break
        case "finances":
            callback = initFinances
            break
        case "account":
        case "settings":
            callback = initSettings
            break
        case "sms":
            callback = initSms
            break
        case "library":
            callback = initLibrary
            break
        case "messages":
            callback = initMessages
            break
        case "transportation":
            callback = initTransportation
            break
        case "access":
            callback = initAccess
            break
        case "reports":
            callback = initReports
            break
        default:
            if (mainpage.indexOf("search") === 0) {
                callback = initSearch
            } else callback = () => {}
            break
    }
    return callback
}

var abortscripts = () => {
    appxhr.forEach((xhrr) => { xhrr.abort() });
    appxhr = []
        // $.post($.baseurl+"/process/abortscripts", {abort: true})
}

var loadPage = (loc, callback, pushstate) => {
    devlog("loadPage(", loc, ") I called =>")

    if (appxhr.length > 0) abortscripts();
    if (callback === undefined) callback = getCallback(loc)

    let progress = moveProgress();
    $.ajax({
        url: loc,
        method: "GET",
        dataType: "JSON",
        beforeSend: () => {
            $.mainprogress.show()
            progress.move($.mainprogress, 5, true)
            $.pageoverlay.show();
            // $.pageloader.show();
        },
        success: (result) => {
            if (result.redirect !== undefined) {
                let redirectUrl = $.baseurl + "/" + result.redirect;
                window.location = redirectUrl
                return false;
            }

            if (result.scripts !== undefined) {
                $.each(result.scripts, function(ii, ie) {
                    $.cachedScript(`${baseUrl}${ie}`);
                });
            }
            $.pagecontent.html($.parseHTML(result.html))

            document.title = result.title
            progress.complete($.mainprogress, false)
            $.pageoverlay.hide()
            init()
            callback(true)
            initDataTables();
            linkClickStopper($.pagecontent)
            formSubmitStopper($.pagecontent)
            var prev = window.history.state === null ? null : window.history.state.current
            if (pushstate !== false) window.history.pushState({ previous: prev, current: loc }, "", loc)

            if (window.history.state === null) {
                $("#history-back, #history-forward").addClass("disabled");
            } else if (window.history.state.previous === null) {
                $("#history-back").addClass("disabled");
            } else {
                $("#history-back").removeClass("disabled");
            }

            setActiveNavLink()
            $('body, html').scrollTop(0);
        },
        error: (err) => {
            if ([404, 500].includes(err.status)) {
                progress.complete($.mainprogress, false)
                $.pageoverlay.hide()
                swal({
                    title: err.status === 404 ? "404" : "OOPS!",
                    text: err.status === 404 ? "Page Not Found\nThe page you are requesting cannot be found" : "Something went wrong. Please try again",
                    icon: "error",
                });
            }
        }
    })
}

var loadFormAction = (form) => {

    $.ajax({
        url: form[0].action,
        method: form[0].method,
        data: new FormData(form[0]),
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: () => {
            $.mainprogress.show()
            $.pageoverlay.show()
        },
        success: (result) => {
            var urlLink = result.data.additional === undefined ? null : result.data.additional.href || null;
            var error = result.code === 200 ? null : result.data.result || null;
            $.pageoverlay.hide();

            if (error !== null) notify(error)

            if (result.code == 200) {
                swal({
                    position: 'top',
                    text: result.data.result,
                    icon: "success",
                });
                if (result.data.additional !== undefined) {
                    if (result.data.additional.clear !== undefined) {
                        $(`form[class~="ajaxform"] input`).val("");
                        $(`form[class~="ajaxform"] select`).val("null").change();
                    }
                    if (result.data.additional.href !== undefined) {
                        loadPage(result.data.additional.href);
                    }
                }
            }
        },
        error: (err) => {}
    })
}

var loadFormSubmitAction = (form, container) => {
    var container = container || form
    let progress = moveProgress()
    let bar = $("." + form.attr("data-progress"));
    $.ajax({
        url: form[0].action,
        method: form[0].method,
        dataType: "JSON",
        data: form.serialize(),
        beforeSend: () => {
            bar.show()
            progress.move(bar, 5, true)
            container.append("<div class='throbber-light temp-throbber'></div>");
        },
        success: (res) => {
            if (res.status === "success") {
                $(".modal").each(function() {
                    $(this).modal("hideModal")
                })
                if (res.redirectpage !== undefined) {
                    location = res.redirectpage
                    return
                }
                notify(res.message, "success")
                if (res.extra !== undefined) {
                    notify(res.extra.message, res.extra.status)
                }
                loadPage(res.loadpage)
            } else {
                notify(res.message)
                $(".temp-throbber").remove();
            }
            progress.complete(bar, false)
        },
        error: (err) => {
            devlog(err)
        }
    })
}

var processPreferences = () => {
    // ---------- Save preference ---------- 
    $("input[name='menutype']").change((event) => {
        var menutype = $(event.target).val();
        var menuclass;
        switch (menutype) {
            case "compact":
                $('body').removeClass('sidebar-minimized sidebar-hidden').addClass('sidebar-compact');
                saveMenuPreference('sidebar-compact');
                break;
            case "minimized":
                $('body').removeClass('sidebar-compact sidebar-hidden').addClass('sidebar-minimized');
                saveMenuPreference('sidebar-minimized');
                break;
            case "hidden":
                $('body').removeClass('sidebar-compact sidebar-minimized').addClass('sidebar-hidden');
                saveMenuPreference('sidebar-hidden');
                break;
            default:
                $('body').removeClass('sidebar-compact sidebar-minimized sidebar-hidden');
                saveMenuPreference('');
                break;
        }
    });

    $("input[name='theme']").change((event) => {
        if ($(event.target).is(":checked")) {
            $('body').removeClass('light').addClass('dark');
            saveThemePreference('dark');
        } else {
            $('body').removeClass('dark');
            saveThemePreference('light');
        }
    });
}

var processAppLock = () => {
    $('#applock_form').on("submit", (event) => {
        event.preventDefault();
        var password_input = $("#applock_password_input");
        if (password_input.val().trim().length < 1) {
            notify("Please enter password");
            hasError(password_input);
            return false;
        }
        $.ajax({
            url: $.baseurl + "/process/verifyapplicationunlock",
            data: { password: password_input.val() },
            method: "POST",
            dataType: "JSON",
            beforeSend: () => {
                $(".lockboxcontent").append("<div class='throbber unlockthrobber'></div>");
            },
            success: (res) => {
                $(".unlockthrobber").remove();
                if (res.status == "success") {
                    notify(res.message, "success");
                    setApplicationLock("unlock");
                    $(".lockbox").removeClass("visible").addClass("invisible");
                    password_input.val("");
                } else {
                    notify(res.message);
                    hasError(password_input);
                }
            },
            error: (res) => {
                notify("Error Processing Request");
                $(".unlockthrobber").remove();
            }
        })
    });
}

var initMainMenu = () => {
    if ($(".notifications-button").length) {
        if ($(".notifications-button").hasClass("hasnotification")) {
            setTimeout(_swingBell(), 2000);
        }
    }
    // Dropdown Menu
    $.navigation.on('click', 'a', (e) => {
        let $this = $(e.currentTarget)
        if ($.ajaxLoad) {
            e.preventDefault();
        }
        if ($this.hasClass('nav-dropdown-toggle')) {
            $this.parent().toggleClass('open');
            resizeBroadcast();
        } else if ($('body').hasClass('sidebar-minimized')) {
            resizeBroadcast();
        }
    });
    // ---------- Main Menu Open/Close, Min/Full ---------- 
    $(".aside-menu .nav-item").on("click", (e) => {
        let $this = $(e.currentTarget)
        $this.siblings(".nav-item").children('.nav-link').removeClass('active');
        $this.children('.nav-link').addClass('active');
        // $(this).trigger('click');
    });

    $('.navbar-toggler').click((e) => {
        let $this = $(e.currentTarget)

        if ($this.hasClass('sidebar-toggler')) {
            if ($('body').hasClass('sidebar-minimized')) {
                $(".nav-link.nav-dropdown-toggle, .nav-link.active").hide();
                $('body')
                    .addClass('sidebar-hidden')
                    .removeClass('sidebar-minimized');
            } else if ($('body').hasClass('sidebar-hidden')) {
                $(".nav-link.nav-dropdown-toggle, .nav-link.active").show();
                $('body')
                    .removeClass('sidebar-hidden');
                // $('body').toggleClass('sidebar-minimized');
            } else {
                $(".nav-link.nav-dropdown-toggle, .nav-link.active").show();
                $('body')
                    .toggleClass('sidebar-minimized');
            }
            resizeBroadcast();
        }

        if ($this.hasClass('sidebar-minimizer')) {
            $('body').toggleClass('sidebar-minimized');
            resizeBroadcast();
        }

        if ($this.hasClass('aside-menu-toggler')) {

            if ($this.hasClass("search-button")) {
                $("[href='#quicksearch']").trigger('click');
                $("#quicksearchinput").focus();
                if ($('body').hasClass('aside-menu-hidden') == false) {
                    return false
                }
            } else if ($this.hasClass("dictionary-button")) {
                $("[href='#dictionary']").trigger('click');
                $("#dictionaryinput").focus();
                if ($('body').hasClass('aside-menu-hidden') == false) {
                    return false
                }
            } else if ($this.hasClass("calculator-button")) {
                $("[href='#calculator']").trigger('click');
                $(".sidebarcalculator").trigger("click");
                if ($('body').hasClass('aside-menu-hidden') == false) {
                    return false
                }
            } else if ($this.hasClass("notifications-button")) {
                $("[href='#notifications']").trigger('click');
                if ($('body').hasClass('aside-menu-hidden') == false) {
                    return false
                }
            }
            $('body').toggleClass('aside-menu-hidden');
            resizeBroadcast();
            $(document).on('click', (ev) => {
                if ($("body").hasClass('aside-menu-hidden') == false) {
                    var asidemenuclicked = $(ev.target).parents('.aside-menu').length;
                    var iscalculatorbutton = $(ev.target).hasClass('calcbtn');
                    var istoggler = $(ev.target).hasClass('navbar-toggler') || $(ev.target).parents('a').hasClass('navbar-toggler');
                    if (!(asidemenuclicked)) {
                        if (istoggler || iscalculatorbutton) return false;
                        $('body').addClass('aside-menu-hidden');
                    }
                }
            })
        }
        if ($this.hasClass('mobile-sidebar-toggler')) {
            $('body').toggleClass('sidebar-mobile-show');
            resizeBroadcast();
        }
    });
}

var initPlugins = () => {

    if ($('.datepicker').length > 0) {
        $('.datepicker').daterangepicker({
            locale: { format: 'YYYY-MM-DD' },
            singleDatePicker: true,
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
                    // onShow : timerange,
                    // onChangeDateTime: timerange
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
        })
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
}

var setActiveNavLink = () => {
    devlog("setActiveNavLink() I called =>")
    var splitter = String(window.location).split('/');
    var cUrl = splitter[0];

    if (cUrl.substr(cUrl.length - 1) == '#') {
        cUrl = cUrl.slice(0, -1);
    } else {
        if (splitter[6] !== undefined && splitter[5] == 'bulk') splitter[5] = 'bulk/' + splitter[6];

        if (splitter[5] !== undefined && splitter[5] == 'classinfo') splitter[5] = 'classes';
        if (splitter[5] !== undefined && splitter[4] == 'students' && splitter[5] == 'info' || splitter[5] == 'edit') splitter[5] = '';
        if (splitter[5] !== undefined && splitter[4] == 'staff' && splitter[5] == 'info') splitter[5] = '';
        if (splitter[5] !== undefined && splitter[4] == 'transportation' && splitter[5] == 'vehicleinfo') splitter[5] = 'vehicles';
        cUrl = $.protocol + '//' + splitter[2] + '/' + splitter[3] + '/' + splitter[4] + (splitter.length > 5 ? '/' + splitter[5] : "");
    }
    cUrl = cUrl.charAt(cUrl.length - 1) == '/' && cUrl != $.baseurl + '/' ? cUrl.slice("/", -1) : cUrl;

    $.navigation.removeClass('active');

    $.navigation.find('a').each((index, el) => {
        if ($(el)[0].href == cUrl) {
            let parentDropdown = $(el).parent("li").parent("ul");
            parentDropdown.css("display", "block");
            $("ul.dropdown-menu").not(parentDropdown).css("display", "none");
            $(el).parent("li").parent("ul").parent("li").addClass('active');
        } else {}
    });
}

var initQuickSearch = () => {
    let searchForm = $("#quicksearchform");
    searchForm.submit((event) => {
        event.preventDefault();
        if ($("#fullsearch")[0].checked) {
            loadFormAction(searchForm)
            $('body').addClass('aside-menu-hidden');
            return false;
        }

        var keyword = $("#quicksearchinput").val().trim();

        if (keyword.length < 1) {
            notify("Please enter a search keyword", null, null);
            hasError($("#quicksearchinput"));
            return false;
        }
        $.ajax({
            // url: $(this).attr('action')+'/'+encodeURIComponent(btoa(keyword.replace(' ', '_'))),
            url: searchForm.attr('action'),
            data: { suggestions: true, q: keyword },
            method: 'post',
            beforeSend: () => {
                $("#searchresults").html("<div class='text-center text-muted'><i class='fa fa-spin fa-3x icon-support'></i><br>searching...</div>");
            },
            success: (result) => {
                $("#searchresults").html(result);
                linkClickStopper($("#searchresults"))
            }
        })
        event.preventDefault();
    });
}

var initDictionary = () => {
    let dicForm = $("#dictionaryform")
    dicForm.submit((event) => {
        var keyword = $("#dictionaryinput").val().trim();

        if (keyword.length <= 0) {
            notify("Please enter a search keyword", null, null);
            hasError($("#dictionaryinput"));
            return false;
        }
        $.ajax({
            url: dicForm.attr('action'),
            data: { suggestions: true, w: keyword },
            method: 'post',
            beforeSend: () => {
                $("#dictionaryresults").html("<div class='text-center text-muted'><i class='fa fa-spin fa-3x icon-support'></i><br>searching...</div>");
            },
            success: (result) => {
                $("#dictionaryresults").html(result);
                linkClickStopper($("#dictionaryresults"))
            }
        })
        event.preventDefault();
    });
}

var initExportButtons = () => {
    $(".btn-export, .export-btn").on("click", (event) => {
        event.preventDefault();
        $(window).on("beforeunload", () => {
            $('.mainpageloader').css({ "visibility": "hidden" });
        })

        let button = $(event.target),
            link = button.attr("href"),
            exportContent = button.attr("data-controls"),
            data1 = button.attr("data-input1") == undefined ? null : $(button.attr("data-input1")).val(),
            data2 = button.attr("data-input2") == undefined ? null : $(button.attr("data-input2")).val();
        $.ajax({
            url: link,
            dataType: "JSON",
            data: { data1: data1, data2: data2 },
            beforeSend: () => {
                button.addClass("disabled");
                $(exportContent).prepend("<div class='export-loader'></div>");
            },
            success: (res) => {
                button.removeClass("disabled");
                $('.export-loader').remove();
                if (res.status == 'success') {
                    notify(res.message, "success");
                    // linkHandler(res.file)
                    location.href = res.file;
                } else {
                    notify(res.message);
                }
            },
            error: () => {
                button.removeClass("disabled");
                $('.export-loader').remove();
                notify("Error Exporting Data");
            }
        });
    });
}


var initDataTables = () => {
    if ($('.datatable').length > 0) {

        $('.datatable').each(function(index, element) {
            let ifempty = $(this).attr("data-empty");
            $(element).dataTable({
                search: null,
                lengthMenu: [
                    [20, 35, 50, 75, 100, 200, -1],
                    [20, 35, 50, 75, 100, 200, "All"]
                ],
                language: {
                    sEmptyTable: ifempty == undefined ? "Nothing Found" : ifempty,
                    lengthMenu: "Display _MENU_ rows"
                }
            });
        });
    }
}

var processActivation = () => {
    if ($("#activationform").length) {
        let activationForm = $("#activationform")
        activationForm.on("submit", (event) => {
            event.preventDefault();
            let activationkey = $("#activationkey"),
                activationsubmit = $("#activationsubmit"),
                ref = window.location.href,
                action = activationForm.attr("action");

            if (activationkey.val().trim().length < 1) {
                notify("Please Enter Activation Key");
                hasError(activationkey);
                return false;
            }
            $.ajax({
                url: action,
                data: { key: activationkey.val(), ref: ref },
                dataType: "JSON",
                method: "POST",
                beforeSend: () => {
                    activationsubmit.append("<div class='throbber activationthrobber'></div>").attr({ disabled: "on" });
                },
                success: (result) => {
                    $(".activationthrobber").remove();
                    activationsubmit.removeAttr("disabled");
                    if (result.status == "success") {
                        notify(result.message, "success", 10000);
                        activationForm.trigger("reset");
                        $(".trialboxcontent").html("<div class='card card-success'>" +
                            "<div class='card-block text-center'>" +
                            "<div class='h4'><i class='icon-like'></i> Activation Successful</div>" +
                            "<div class='py-4 font-lg'>Thank you for purchasing software</div>" +
                            "<div><button class='btn btn-inverse btn-lg activationdismiss'>OK</button></div>" +
                            "</div>" +
                            "</div>");

                        $('.activationdismiss').on("click", () => {
                            $(".trialbox").fadeOut();
                        });
                    } else {
                        notify(result.message);
                        hasError(activationkey);
                    }
                },
                error: (res) => {
                    $(".activationthrobber").remove();
                    activationsubmit.removeAttr("disabled");
                }
            });
        });
    }
}

var getSmsBalance = () => {
    var smsbalance = $(".smscreditsbalance");
    appxhr.push($.ajax({
        url: $.baseurl + "/sms_process/get_sms_balance",
        method: "POST",
        beforeSend: () => {
            smsbalance.html("<i class='fa fa-gear fa-spin'></i>");
        },
        success: (result) => {
            smsbalance.html(result);
        },
        error: (result) => {
            smsbalance.html("N/A");
        }
    }));
}

var moneyFormat = (value, decimals) => {
    value = value.toFixed(decimals)
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    return value
}

var fetchUserChats = () => {
    appxhr.push(
        $.ajax({
            url: $.baseurl + "/messages_process/fetch_user_chats",
            method: 'POST',
            dataType: "JSON",
            success: (res) => {
                if (res.count > 0) {
                    playAudio("alert");
                    $('.messagescount-badge').text(res.count);
                    if ($('.messagescount-badge').length < 2) {
                        $('.messagescount').append("<span class='badge badge-pill badge-success messagescount-badge'>" + res.count + "</span>");
                    }
                    notify(res.message);
                }
            }
        })
    );
    setTimeout(() => {
        // fetchUserChats()
    }, $.chatinterval);
}

var saveMenuPreference = (menutype) => {
    $.post($.baseurl + "/process/savemenupreference", { menutype: menutype }, () => {});
}

var saveThemePreference = (theme) => {
    $.post($.baseurl + "/process/savethemepreference", { theme: theme }, () => {});
}

var setApplicationLock = (type) => {
    return $.post($.baseurl + "/process/setapplicationlock", { type: type }, () => {});
}

var capitalizeFirstLetter = (string) => {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

var resizeBroadcast = () => {
    var timesRun = 0;
    var interval = setInterval(() => {
        timesRun += 1;
        if (timesRun === 5) {
            clearInterval(interval);
        }
        window.dispatchEvent(new Event('resize'));
    }, 62.5);
}

var _swingBell = () => {
    $(".notifications-button").addClass("animated infinite swing");
    var timeoutID = window.setTimeout(() => {
        _stopSwingBell();
    }, 10000);
}

var _stopSwingBell = () => {
    $(".notifications-button").removeClass("animated infinite swing");
    var timeoutID = window.setTimeout(() => {
        _swingBell();
    }, 10000);
}

let timeout
var moveProgress = () => {
    let move = (bar, startwidth, clear) => {
        let width = startwidth || 5
        if (clear) clearTimeout(timeout) // In case new loader needed 
        timeout = setTimeout(() => {
            bar.find(".progress-bar").css({ width: width + "%" })
            if (width < 90) width += 5;
            else width -= 3;
            move(bar, width, false)
        }, randomInt(400, 800))
    }

    let complete = (bar, removebar) => {
        clearTimeout(timeout)
        bar.find(".progress-bar").css({ width: "100%" })
        setTimeout(() => { hide(bar, removebar) }, 1000)
    }

    let hide = (bar, removeBar) => {
        let removebar = removeBar || false
        if (removebar) {
            bar.remove()
        } else {
            bar.hide()
                .find(".progress-bar").css({ width: "5%" })
        }
    }

    return {
        move: move,
        complete: complete
    }
}

var notify = (text, type = "error") => {
    $.notify(text, type);
}

var hasError = (element) => {
    if (element.is("select") && element.hasClass("selectpicker")) {
        let elementid = element.attr("id");
        let elementselect = $("[data-id='" + elementid + "'");
        element.parents(".form-group").addClass("has-danger");
        elementselect.removeClass("btn-outline-primary").addClass("btn-outline-danger");
        prepareForErrorFix(element);
        element = elementselect;
    } else {
        element.parents(".form-group").addClass("has-danger");
        element.focus();
        prepareForErrorFix(element);
    }

    if (element.hasClass('animated shake')) {
        element.removeClass('animated shake');
        element.addClass('animated headShake');
    } else if (element.hasClass('animated headShake')) {
        element.removeClass('animated headShake');
        element.addClass('animated shake');
    } else {
        element.addClass('animated shake');
    }
}

var prepareForErrorFix = (element) => {
    if (element.is("select") && element.hasClass("selectpicker")) {
        element.on("change", () => {
            let elementid = element.attr("id");
            let elementselect = $("[data-id='" + elementid + "'");
            elementselect.removeClass("btn-outline-danger").addClass("btn-outline-primary");
            element.parents(".form-group").removeClass("has-danger");
            $.notifyClose();
        });
    } else if (element.is("input") && !element.hasClass("selectpicker")) {
        element.on("change", () => {
            element.parents(".form-group").removeClass("has-danger");
            $.notifyClose();
        });
    } else {
        element.on("change", () => {
            element.parents(".form-group").removeClass("has-danger");
            $.notifyClose();
        });
    }

    if (element.is("input")) {
        element.on("input", () => {
            element.parents(".form-group").removeClass("has-danger");
            $.notifyClose();
        });
    }
}

var isValidFloat = (number) => {
    var num = parseFloat(number);
    if (isNaN(num)) return false;
    else if (num < 0) return false;
    else return true;
}

var playAudio = (file) => {
    let alertaudio;
    switch (file) {
        case 'alert':
            alertaudio = document.getElementById('audio-alert');
            alertaudio.volume = 0.3;
            break;
        case 'load':
            alertaudio = document.getElementById('audio-load');
            alertaudio.volume = 0.7;
            break;
        case 'pop':
            alertaudio = document.getElementById('audio-pop');
            alertaudio.volume = 0.7;
            break;
        default:
            alertaudio = document.getElementById('audio-warning');
            alertaudio.volume = 0.1;
            break;
    }
    alertaudio.pause();
    alertaudio.currentTime = 0;
    alertaudio.play();
}

var randomInt = (min, max) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min; //The maximum is exclusive and the minimum is inclusive
}

$(`div[id="ajaxFormSubmitModal"] button[class~="btn-outline-success"]`).on("click", async function(evt) {

    let formLoader = $(`form[class="ajax-data-form"] div[class="form-content-loader"]`),
        submitLoader = $(`div[id="ajaxFormSubmitModal"] div[class="form-content-loader"]`),
        formButton = $(`form[class="app-data-form"] button[type="button-submit"]`);

    formLoader.css({ "display": "flex", "position": "fixed" });
    submitLoader.css({ "display": "flex", "position": "fixed" });

    await $.ajax({
        url: `${ajaxFormAction}`,
        data: ajaxFormData,
        contentType: false,
        cache: false,
        type: `POST`,
        processData: false,
        success: function(response) {

            formButton.prop("disabled", false);

            if (response.code == 200) {

                swal({
                    position: 'top',
                    text: response.data.result,
                    icon: "success",
                });

                if (response.data.additional) {
                    if (response.data.additional.clear) {
                        if ($(`textarea[name="faketext"]`).length) {
                            CKEDITOR.instances['ajax-form-content'].setData("");
                        }
                        if ($(`trix-editor[name="faketext"][id="ajax-form-content"]`).length) {
                            $(`trix-editor[name="faketext"][id="ajax-form-content"]`).html("");
                        }
                        $replies_loaded.attr("value", "0");
                        $replies_loaded.attr("data-form", "none");
                        $(`form[class="ajax-data-form"] select`).val("null").change();
                        $(`form[class="ajax-data-form"] input, form[class="ajax-data-form"] textarea`).val("");
                    }
                    if (response.data.additional.append) {
                        $(`div[id="${response.data.additional.append.div_id}"]`).html(response.data.additional.append.data);
                    }
                    if (response.data.additional.record) {
                        $.each(response.data.additional.record, function(ie, iv) {
                            $(`form[class="ajax-data-form"] input[name="${ie}"]`).val(iv);
                            $(`[data-record="${ie}"]`).html(iv);
                        });
                    }
                    if (response.data.additional.href !== undefined) {
                        loadPage(response.data.additional.href);
                    }
                }
                $(`form[class="ajax-data-form"] div[class~="file-preview"]`).html("");
            } else {
                if (response.data.result !== undefined) {
                    notify(response.data.result);
                } else {
                    notify("Sorry! Error processing request.");
                }
            }
        },
        complete: function() {
            formLoader.css({ "display": "none" });
            submitLoader.css({ "display": "none" });
            formButton.prop("disabled", false);
            $(`div[id="ajaxFormSubmitModal"]`).modal("hide");
        },
        error: function() {
            formLoader.css({ "display": "none" });
            submitLoader.css({ "display": "none" });
            formButton.prop("disabled", false);
            $(`div[id="ajaxFormSubmitModal"]`).modal("hide");
            Toast.fire({
                title: "Error processing request!",
                icon: "error"
            });
        }
    });

});

var trigger_form_submit = () => {
    $(`form[class="ajax-data-form"] button[type="button-submit"]`).on("click", async function(evt) {

        evt.preventDefault();
        let theButton = $(this),
            draftButton = $(`form[class="ajax-data-form"] button[type="button-submit"][data-function="draft"]`),
            formAction = $(`form[class="ajax-data-form"]`).attr("action"),
            formButton = $(`form[class="app-data-form"] button[type="button-submit"]`);

        let optional_flow = "We recommend that you save the form as a draft, and review it before submitting. Do you wish to proceed with this action?";

        $(`div[id="ajaxFormSubmitModal"] div[class="modal-body"]`).html(`Are you sure you want to Submit this form? ${draftButton.length ? optional_flow : ""}`);

        formButton.prop("disabled", true);

        let myForm = document.getElementById('ajax-data-form-content');
        let theFormData = new FormData(myForm);

        let button = theButton.attr("data-function");
        theFormData.append("the_button", button);

        if ($(`textarea[name="faketext"]`).length) {
            theFormData.delete("faketext");
            let content = CKEDITOR.instances['ajax-form-content'].getData();
            theFormData.append("description", htmlEntities(content));
        }

        if ($(`textarea[name="faketext_2"]`).length) {
            theFormData.delete("faketext_2");
            let content = CKEDITOR.instances['ajax-form-content_2'].getData();
            theFormData.append("reason", htmlEntities(content));
        }

        if ($(`trix-editor[name="faketext"][id="ajax-form-content"]`).length) {
            theFormData.delete("faketext");
            let content = $(`trix-editor[id="ajax-form-content"]`).html();
            theFormData.append("description", htmlEntities(content));
        }

        if ($(`trix-editor[name="faketext_2"][id="ajax-form-content_2"]`).length) {
            theFormData.delete("faketext_2");
            let content = $(`trix-editor[id="ajax-form-content_2"]`).html();
            theFormData.append("reason", htmlEntities(content));
        }

        ajaxFormAction = formAction,
            ajaxFormData = theFormData;

        $(`div[id="ajaxFormSubmitModal"]`).modal("show");
    });
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
        if (response.code !== 200) {
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

            initPlugins();

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

var load_quick_form = async(module, module_item_id) => {
    $replies_modal.modal("hide");
    $form_header.html(form_modules[module]);

    if (module == "user_basic_information") {
        let user_info = await load_idb_record("user_list", module_item_id);
        if (user_info) {
            $basic_data_modal.modal("show");
            $basic_data_header.html(form_modules[module]);
            preview_user_info(user_info);
            return false;
        }
    }

    $form_modal.modal("show");
    $form_body.html($form_loader);

    form_loader(module, module_item_id);
}