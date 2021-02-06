var comments_container = $(`div[id="comments-container"]`);

window.addEventListener("click", (element) => {
    if ($(`div[id="public_comment_button"]`).length) {
        if (element.toElement.classList.contains("public_comment")) {
            $(`div[id="public_comment_button"]`).removeClass("hidden");
        } else {
            let value = $(`div[id="public_comment"]`).html();
            if (value.length) {
                $(`div[id="public_comment_button"]`).removeClass("hidden");
            } else {
                $(`div[id="public_comment_button"]`).addClass("hidden");
            }
        }
    }
});

var save_video_state = () => {
    let video = document.getElementById("elearning_video"),
        video_id = $(`video[id="elearning_video"]`).data("video_unique_id");
    let video_time = video.currentTime;
    $.form_data = { video_id, video_time };
}

var cancel_comment = () => {
    $(`div[id="public_comment"]`).html("");
}

var share_comment = (comment_id = "") => {
    let _video = document.getElementById("elearning_video");
    let comment = $(`div[id="public_comment"]`).html(),
        record_id = $(`video[id="elearning_video"]`).data("video_unique_id"),
        video_time = _video.currentTime;

    $(`div[id="public_comment_button"]`).removeClass("hidden");
    $(`div[id="public_comment_button"] button`).prop("disabled", true);
    if (comment.length) {
        comment = htmlEntities(comment);
        let _data = { comment, comment_id, record_id, video_time };
        $.post(`${baseUrl}api/replies/share`, _data).then((response) => {
            if (response.code === 200) {
                $(`div[id="public_comment"]`).html("");
                $(`div[id="public_comment_button"]`).addClass("hidden");
                $(`div[id="public_comment_button"] button`).prop("disabled", true);

                let comment = format_comment(response.data.result);
                if ($(`div[id="comments-container"] div[id="comment-listing"]:first`).length) {
                    $(`div[id="comments-container"] div[id="comment-listing"]:first`).before(comment);
                } else {
                    $(`div[id="comments-container"]`).append(comment);
                }

            }
        });
    } else {
        $(`div[id="public_comment_button"] button`).prop("disabled", false);
        $(`div[class="comment_response"]`).html(`<span class="text-danger">The comment cannot be empty.</span>`);
    }
}

$(`div[id='related_video'] video`).on("click", function() {
    let data = $(this).data();
    save_video_state();
    $(`video[id="elearning_video"]`).attr({ "src": data.src, "autoplay": true });
    loadPage(`${data.href}`);
    $.form_data = {};
});

$(`div[id="public_comment"]`).on("click", function() {
    $(`div[id="public_comment_button"]`).removeClass("hidden");
});

$(`div[id="public_comment"]`).on("input", function() {
    let value = $(this).html();
    if (value.length) {
        $(`div[id="public_comment_button"] button`).prop("disabled", false);
    } else {
        $(`div[id="public_comment_button"] button`).prop("disabled", true);
    }
});

var format_comment = (comment) => {
    return `
    <div id="comment-listing" class="mb-2 border-bottom bg-white text-dark p-2" data-comment_id="${comment.id}">
        <div class="d-flex justify-content-start">
            <div class="mr-2">
                <img src="${baseUrl}${comment.image}" class="rounded-circle author-box-picture" width="40px">
            </div>
            <div>
                <div><strong>${comment.fullname}</strong> . ${comment.time_ago}</div>
                <div>${comment.comment}</div>
            </div>
        </div>
    </div>`;
}

var load_comments = async(data) => {
    await $.get(`${baseUrl}api/resources/comments_list`, data).then((response) => {
        if (response.code == 200) {
            if (response.data.result) {
                let result = response.data.result;
                comments_container.attr("data-last-reply-id", result.last_comment_id);
                if (result.comments_list) {
                    let html = "";
                    $.each(result.comments_list, function(_, value) {
                        html += format_comment(value);
                    });
                    comments_container.append(html);
                    $(`button[id="load-more-replies"]`).fadeIn("slow").html("Load more");
                }
                if (!result.comments_list) {
                    $(`button[id="load-more-replies"]`).html("No comments available").attr("disabled", true);
                }
                if ((result.last_comment_id == "no_more_record") || (result.first_reply_id == result.last_comment_id)) {
                    $(`button[id="load-more-replies"]`).html("No comments available").attr("disabled", true);
                }
            }
        }
    });
}

if (comments_container.length) {
    $(`button[id="load-more-replies"]`).html("Loading replies...");
    let autoload = comments_container.attr("data-autoload");

    if (autoload === "true") {
        let data = {
            record_id: comments_container.attr("data-id"),
            last_comment_id: comments_container.attr("data-last-reply-id"),
            limit: 10
        };
        load_comments(data);
        comments_container.attr("data-autoload", "false");
    }
}

if (comments_container.length) {
    $(`button[id="load-more-replies"]`).html("Loading replies...");
    let autoload = comments_container.attr("data-autoload");

    if (autoload === "true") {
        let data = {
            record_id: comments_container.attr("data-id"),
            last_comment_id: comments_container.attr("data-last-reply-id"),
            limit: 10
        };
        load_comments(data);
        comments_container.attr("data-autoload", "false");
    }
}