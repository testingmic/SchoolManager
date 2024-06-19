var the_file,
    file_input = $(`input[class~="attachment_file_upload"]`),
    dd_container = $(`form[class~='ajax-data-form'] div[class~="post-attachment"]`);

$(`input[class~="attachment_file_upload"]`).change(function() {
    let file_input = $(this);
    let files_list = file_input[0].files;

    let _module = file_input.attr("data-form_module"),
    item_id = file_input.attr("data-form_item_id"),
    accept = file_input.attr("accept");

    let fd = new FormData();

    $.each(files_list, async function(i, the_file) {

        fd.append('attachment_file_upload', the_file);
        fd.append('module', _module);
        fd.append('label', "upload");
        fd.append('item_id', item_id);
        fd.append('accept', accept);

        await ajax_file_upload(fd);

        fd.delete("attachment_file_upload");
    });

});

$(`button[id='ajax-upload-input']`).on("click", function() {
    $(`input[class~="attachment_file_upload"]`).trigger("click");
});

function delete_ajax_file_uploaded(_module, item_id) {
    $(`div[data-document-link="${item_id}"] *`).prop("disabled", true);
    $.post(`${baseUrl}api/files/attachments`, { item_id: item_id, module: _module, label: "remove" }).then((response) => {
        if (response.code == 200) {
            $(`div[data-document-link="${item_id}"]`).remove();
            notify(response.data.result, 'success');
        } else {
            notify(response.data.result);
        }
    }, 'json').catch(() => {
        $(`div[data-document-link="${item_id}"] *`).prop("disabled", false);
    });
}

function download_ajax_temp_file(_module, item_id) {
    $.post(`${baseUrl}api/files/attachments`, { item_id: item_id, module: _module, label: "download" }).then((response) => {
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

async function ajax_file_upload(formdata) {
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
            $(`input[class~="attachment_file_upload"]`).val("");
            if (response.code == 200) {
                notify(`${response.data.additional.filename} successfully uploaded.`, 'success');
                load_ajax_file_uploads(response.data.result);
            } else {
                notify(response.data.result);
            }
        },
        complete: function() {
            $(`input[class~="attachment_file_upload"]`).prop("disabled", false);
            $(`div[class~="upload-document-loader"]`).addClass("hidden");
        },
        error: function() {
            $(`input[class~="attachment_file_upload"]`).val("").prop("disabled", false);
            $(`div[class~="upload-document-loader"]`).addClass("hidden");
        }
    });
}