var load_notifications = (userId = null) => {
    if ($(`div[id="notifications_list"]`).length) {
        let user_id = userId !== null ? userId : $myPrefs.userId;
        $.get(`${baseUrl}api/notification/list`, {user_id}).then((response) => {
            if(response.code == 200) {
                let notifications = "",
                    mark_all = 0;
                $.each(response.data.result, function(i, e) {
                    let state = e.seen_status == "Unseen" ? "unread" : "read";
                    if(mark_all == 0) {
                        if(e.seen_status == "Unseen") {
                            mark_all = 1;
                        }
                    }
                    notifications += `
                    <a href="#" data-notice_id="${e.item_id}" class="${state == "unread" ? "notification" : ""} dropdown-item dropdown-item-${state}">
                        <span class="dropdown-item-icon ${e.favicon_color} text-white">
                            <i class="fas ${e.favicon}"></i>
                        </span>
                        <span class="dropdown-item-desc">
                            ${e.message}
                            <span class="time text-primary">${e.time_to_ago}</span>
                        </span>
                    </a>`;
                });
                $(`div[id="notifications_list"]`).html(notifications);
                if(!mark_all) {
                    $(`div[class~="mark_all_as_read"]`).addClass("hidden");
                } else {
                    $(`div[class~="mark_all_as_read"]`).removeClass("hidden");
                }
                mark_notification_as_read();
            }
        });
    }
}
load_notifications();

var mark_notification_as_read = () => {
    $(`a[class~="notification"]`).on("click", function(evt) {
        evt.preventDefault();
        let notification_id = $(this).data("notice_id");

        $.post(`${baseUrl}api/notification/mark_as_read`, {notification_id}).then((response) => {
            if(response.code == 200) {
                $(`a[class~="notification"][data-notice_id="${notification_id}"]`)
                    .removeClass("dropdown-item-unread")
                    .addClass("dropdown-item-read");
            }
        });
    });
}

var mark_all_notification_as_read = () => {
    $.post(`${baseUrl}api/notification/mark_as_read`, {notification_id: "mark_all_as_read"}).then((response) => {
        if(response.code == 200) {
            $(`div[class~="mark_all_as_read"]`).addClass("hidden");
            $(`a[class~="notification"]`)
                .removeClass("dropdown-item-unread")
                .addClass("dropdown-item-read");
        }
    });
}

var open_dictionary_modal = () => {
    $(".settingSidebar").toggleClass("showSettingPanel");
    $("div[class~='bg-blur'").toggleClass("hidden");
    $(".page-wrapper").on("click", function() {
        $(".settingSidebar").removeClass("showSettingPanel");
    });
    $(".settingSidebar").removeClass("minified-settingSidebar");
    $(`div[class~="attendance_modal"]`).addClass("hidden");
    $(`div[class~="search_dictionary"]`).removeClass("hidden");
    $(`div[id="processing_qr_code"]`).addClass("hidden");
    $(`div[class~="setting-panel-header"] h6`).html("Onboard Dictionary");
}

var open_attendance_modal = () => {
    $(`a[class~="settingPanelToggle"]`).trigger("click");
    $(".settingSidebar").addClass("minified-settingSidebar");
    $(`div[class~="search_dictionary"]`).addClass("hidden");
    $(`div[class~="attendance_modal"]`).removeClass("hidden");
    $(`button[id="qr_code_scanned"]`).removeAttr("disabled");
    $(`div[class="setting-panel-header"] h6`).html("Check In/Out of School");
    let isOpened = $(`div[class~="settingSidebar"]`).hasClass("showSettingPanel");
}

var cancel_qr_code_request = () => {
    open_attendance_modal();
}

var confirm_qr_code_request = () => {
    $(`button[id="qr_code_scanned"]`).attr("disabled", "disabled");
    $(`div[id="processing_qr_code"]`).removeClass("hidden");
}