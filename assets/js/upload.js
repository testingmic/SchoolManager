var the_file;
$(function() {
    'use strict';

    $("input.attachment_file_upload").change(function() {
        the_file = $(this);

        let fd = new FormData(),
            files = the_file[0].files[0],
            module = the_file.attr("data-form_module"),
            item_id = the_file.attr("data-form_item_id");

        fd.append('attachment_file_upload', files);
        fd.append('module', module);
        fd.append('label', "upload");
        fd.append('item_id', item_id);

        ajax_file_upload(fd);
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

function download_ajax_temp_file(module, item_id) {
    $.post(`${baseUrl}api/files/attachments`, { item_id: item_id, module: module, label: "download" }).then((response) => {
        if (response.code == 200) {
            window.open(`${baseUrl}${response.data.result}`, "_blank");
        }
    }, 'json');
}

function load_ajax_file_uploads(response) {
    let preview = $(`div[class~="file-preview"][preview-id="${response.module}"]`),
        preview_details = $(`div[class="file-attachment_logs"]`);

    preview.html(response.files);
    preview_details.html(response.details);
}

function ajax_file_upload(formdata) {
    $(`div[class~="upload-document-loader"]`).removeClass("hidden");
    $(`input[class~="attachment_file_upload"]`).prop("disabled", true);
    $.ajax({
        url: $('div[class="file_attachment_url"]').attr("data-url"),
        type: 'post',
        data: formdata,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            the_file.val("");
            if (response.code == 200) {
                load_ajax_file_uploads(response.data.result);
            } else {
                Toast.fire({
                    icon: "error",
                    title: response.data.result
                });
            }
        },
        complete: function() {
            $(`input[class~="attachment_file_upload"]`).prop("disabled", false);
            $(`div[class~="upload-document-loader"]`).addClass("hidden");
        },
        error: function() {
            $(`input[class~="attachment_file_upload"]`).prop("disabled", false);
            $(`div[class~="upload-document-loader"]`).addClass("hidden");
        }
    });
}