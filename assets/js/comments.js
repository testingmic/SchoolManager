var comments_container = $(`div[id="comments-container"]`);

var commentsLoader = async(data) => {
    await $.get(`${baseUrl}api/replies/list?feedback_type=comment`, data).then((response) => {
        if (response.code == 200) {
            if (response.data.result) {
                let result = response.data.result;
                comments_container.attr("data-last-reply-id", result.last_reply_id);
                if (result.replies_list) {
                    let html = "",
                        prev_date = "";
                    $.each(result.replies_list, function(_, value) {
                        if (!prev_date || prev_date != value.raw_date) {
                            html += `<div class="message_list_day_divider_label"><button class="message_list_day_divider_label_pill">${value.clean_date}</button></div>`;
                        }
                        html += formatThreadComment(value);
                        prev_date = value.raw_date;
                    });
                    comments_container.append(html);
                    $(`button[id="load-more-replies"]`).fadeIn("slow").html("Load more");
                }
                if (!result.replies_list) {
                    $(`button[id="load-more-replies"]`).html("No comments available").attr("disabled", true);
                }
                if ((result.last_reply_id == "no_more_record") || (result.first_reply_id == result.last_reply_id)) {
                    $(`button[id="load-more-replies"]`).html("No comments available").attr("disabled", true);
                }
                apply_comment_click_handlers();
            }
        }
    });
}

if (comments_container.length) {
    $(`button[id="load-more-replies"]`).html("Loading replies...");
    let autoload = comments_container.attr("data-autoload");

    if (autoload === "true") {
        let data = {
            resource_id: comments_container.attr("data-id"),
            last_reply_id: comments_container.attr("data-last-reply-id"),
            limit: 10
        };
        commentsLoader(data);
        comments_container.attr("data-autoload", "false");
    }
}

$(`button[id="load-more-replies"]`).on("click", function() {
    $(`button[id="load-more-replies"]`).html("Loading replies...");
    let last_comment = comments_container.attr("data-last-reply-id");

    if (last_comment == "no_more_record") {
        $(`button[id="load-more-replies"]`).html("No comments available").attr("disabled", true);
        return false;
    }
    let data = {
        resource_id: comments_container.attr("data-id"),
        last_reply_id: last_comment,
        limit: 10
    };
    commentsLoader(data);
});