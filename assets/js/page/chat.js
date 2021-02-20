"use strict";

$.chatCtrl = function(element, chat) {
    var chat = $.extend({
        position: 'chat-right',
        text: '',
        time: moment(new Date().toISOString()).format('hh:mm'),
        picture: '',
        type: 'text', // or typing
        timeout: 0,
        onShow: function() {}
    }, chat);

    var target = $(element),
        element = '<div class="chat-item ' + chat.position + '" style="display:none">' +
        '<img src="' + chat.picture + '">' +
        '<div class="chat-details">' +
        '<div class="chat-text">' + chat.text + '</div>' +
        '<div class="chat-time">' + chat.time + '</div>' +
        '</div>' +
        '</div>',
        typing_element = '<div class="chat-item chat-left chat-typing" style="display:none">' +
        '<img src="' + chat.picture + '">' +
        '<div class="chat-details">' +
        '<div class="chat-text"></div>' +
        '</div>' +
        '</div>';

    var append_element = element;
    if (chat.type == 'typing') {
        append_element = typing_element;
    }

    if (chat.timeout > 0) {
        setTimeout(function() {
            target.find('.chat-content').append($(append_element).fadeIn());
        }, chat.timeout);
    } else {
        target.find('.chat-content').append($(append_element).fadeIn());
    }

    var target_height = 0;
    target.find('.chat-content .chat-item').each(function() {
        target_height += $(this).outerHeight();
    });
    setTimeout(function() {
        target.find('.chat-content').scrollTop(target_height, -1);
    }, 100);
    chat.onShow.call(this, append_element);
}

if ($("#chat-scroll").length) {
    $("#chat-scroll").css({
        height: 450
    }).niceScroll();
}

if ($(".chat-content").length) {
    $(".chat-content").niceScroll({
        cursoropacitymin: .3,
        cursoropacitymax: .8,
    });
    $('.chat-content').getNiceScroll(0).doScrollTop($('.chat-content').height());
}

$("#chat-form").submit(function(event) {
    var me = $(this);
    event.preventDefault();
    if (me.find('input').val().trim().length > 0) {
        $.chatCtrl('#mychatbox', {
            text: me.find('input').val(),
            picture: $myPrefs.user_image,
        });
        let chat = {
            'receiver_id': current_focused_user_id,
            'message_id': current_focused_msg_id,
            'sender_id': $myPrefs.userId,
            'message': me.find('input').val()
        };
        $.post(`${baseUrl}api/chats/send`, chat).then(async(response) => {
            if (response.code === 200) {
                me.find('input').val('');
            }
        });
    }
    return false;
});

var reset_list = () => {
    $(`ul[class~="chat-list"] li[id="search_list"]`).remove();
    $(`div[class~="chat-search"] input[id="search_user"]`).val("");
    $(`ul[class~="chat-list"] li[id="default_list"], li[id="temp_user_list"]`).removeClass("hidden");
}

var display_messages = async(message_id, user_id, name, image, last_seen = "Online") => {
    reset_list();
    current_focused_user_id = user_id,
        current_focused_msg_id = message_id;
    $(`div[class="chat"] div[class~="chat-header"]`).html(`
      <img src="${baseUrl}${image}" alt="avatar">
      <div class="chat-about"><div class="chat-with">${name}</div><div class="chat-num-messages">${last_seen}</div></div>
    `);

    $(`form[id="chat-form"] *`).prop("disabled", false);

    let user_chats = await load_idb_record("chats", current_focused_msg_id),
        the_chat_list = {};

    if (user_chats[current_focused_msg_id] !== undefined) {
        the_chat_list = user_chats[current_focused_msg_id];
    } else {
        let messages = await $.post(`${baseUrl}api/chats/list`, { user_id: user_id });
        if (messages.code == 200) {
            let results_list = messages.data.result

            the_chat_list = results_list.messages[results_list.message_id];
            current_focused_msg_id = results_list.message_id;

            if (current_focused_msg_id !== "null") {
                replace_id_record(the_chat_list, "chats", current_focused_msg_id);
            }
        }
    }
    current_focused_msg_id = message_id;
    $(`form[id="chat-form"] input`).focus();

    if (the_chat_list) {
        let chats_list = "",
            prev_time = "",
            prev_date = "";
        $.each(the_chat_list, function(i, e) {
            if (!prev_date || prev_date !== e.raw_date) {
                chats_list += `<div class="message_list_day_divider_label"><button class="message_list_day_divider_label_pill">${e.clean_date}</button></div>`;
            }
            // chats_list += format_UserChats(e);
            prev_date = e.raw_date;
        });

        $(`div[class~="chat-body"] ul[class="messages"]`).html(chats_list);
    }

}

$(`div[class~="chat-search"] input[id="search_user"]`).on("keyup", function(event) {
    if (event.key === 'Enter') {
        $(`form[id="chat-form"] *`).prop("disabled", true);
        let value = $(`div[class~="chat-search"] input[id="search_user"]`).val();
        if (value.length) {
            $(`ul[class~="chat-list"] li[id="search_list"]`).remove();
            $.get(`${baseUrl}api/chats/search_user`, { q: value }).then((response) => {
                if (response.code === 200) {
                    $(`ul[class~="chat-list"] li[id="default_list"], li[id="temp_user_list"]`).addClass("hidden");
                    let users_list = "";
                    $.each(response.data.result, function(i, user) {
                        let online_text = user.online ? "online" : "offline",
                            online_msg = user.online ? "Online" : `Left ${user.offline_ago}`;
                        users_list += `
                        <li id="search_list" data-message_id="${user.message_unique_id}" onclick="return display_messages('${user.message_unique_id}','${user.user_id}','${user.name}','${user.image}','${user.offline_ago}')" class="clearfix">
                            <img src="${baseUrl}${user.image}" alt="avatar">
                            <div class="about">
                                <div class="name">${user.name}</div>
                                <div class="status">
                                    <i class="material-icons ${online_text}">fiber_manual_record</i>
                                    ${online_msg}
                                </div>
                            </div>
                        </li>`;
                    });
                    $(`ul[class~="chat-list"]`).append(users_list);
                }
            });
        } else {
            reset_list();
        }
    }
});