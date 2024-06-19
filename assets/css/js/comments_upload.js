var file_to_upload = $("input.comment_attachment_file_upload");

$('.post-attachment').on('dragenter', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $(`div[class="upload-overlay-cover"]`).css("display", "flex");
    $("div[class~='upload-content']").html("Drop file to upload");
});

$('.post-attachment').on('dragover', function(e) {
    e.stopPropagation();
    e.preventDefault();
    $(`div[class="upload-overlay-cover"]`).css("display", "flex");
    $("div[class~='upload-content']").html("Drop file to upload");
});

$('.post-attachment').on('drop', function(e) {
    e.stopPropagation();
    e.preventDefault();

    $(`div[class="upload-overlay-cover"]`).css("display", "none");

    var file = e.originalEvent.dataTransfer.files;

    $.each(file, function(i, the_file) {
        let fd = new FormData(),
            files = the_file,
            module = file_to_upload.attr("data-form_module"),
            item_id = file_to_upload.attr("data-form_item_id");

        fd.append('comment_attachment_file_upload', files);
        fd.append('module', module);
        fd.append('label', "upload");
        fd.append('item_id', item_id);
        ajax_post_file_upload(fd);
    });

});

$("input.comment_attachment_file_upload").change(function() {
    file_to_upload = $(this);
    $.each(file_to_upload[0].files, function(ii, iv) {
        let fd = new FormData(),
            files = iv,
            module = file_to_upload.attr("data-form_module"),
            item_id = file_to_upload.attr("data-form_item_id");

        fd.append('comment_attachment_file_upload', files);
        fd.append('module', module);
        fd.append('label', "upload");
        fd.append('item_id', item_id);

        ajax_post_file_upload(fd);
    });
});

function delete_ajax_file_uploaded(module, item_id) {
    $(`div[data-document-link="${item_id}"] *`).prop("disabled", true);
    $.post(`${baseUrl}api/files/attachments`, { item_id: item_id, module: module, label: "remove" }).then((response) => {
        if (response.code == 200) {
            $(`div[data-document-link="${item_id}"]`).remove();
        }
    }, 'json').catch(() => {
        $(`div[data-document-link="${item_id}"] *`).prop("disabled", false);
    });
}

function load_ajax_post_file_uploads(response) {
    let preview = $(`div[class~="file-preview"][preview-id="${response.module}"]`),
        preview_details = $(`div[class="file-attachment_logs"]`);
    preview.html(response.files);
    preview_details.html(response.details);
}

function ajax_post_file_upload(formdata) {
    $(`div[class~="upload-document-loader"]`).removeClass("hidden");
    $(`input[class~="comment_attachment_file_upload"]`).prop("disabled", true);
    $.ajax({
        url: $('div[class="file_attachment_url"]').attr("data-url"),
        type: 'post',
        data: formdata,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            $("input.comment_attachment_file_upload").val("");
            if (response.code == 200) {
                load_ajax_post_file_uploads(response.data.result);
            } else {
                notify(response.data.result);
            }
        },
        complete: function() {
            $(`div[class~="upload-document-loader"]`).addClass("hidden");
            $(`input[class~="comment_attachment_file_upload"]`).prop("disabled", false);
        },
        error: function() {
            $(`div[class~="upload-document-loader"]`).addClass("hidden");
            $(`input[class~="comment_attachment_file_upload"]`).prop("disabled", false);
        }
    });
}