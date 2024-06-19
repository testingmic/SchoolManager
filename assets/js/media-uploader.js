// $(`div[id="addMediaModal"]`).modal("show");
$(`div[class="upload-content"] button[id="fileupload"]`).on("click", function() {
    $(`div[class="upload-content"] input[id="media_file_upload"]`).trigger("click");
});

var media_name_search = () => {
    $.expr[':'].Contains = function(a,i,m){
        return $(a).text().toUpperCase().indexOf(m[3].toUpperCase())>=0;
    };
    $(`div[id="media_search_input"] input[name="media_name"]`).on("input", function(event) {
        let input = $(this).val();
        $(`div[data-media_item='filter']`).addClass('hidden');
        $(`div[data-media_item='filter'][data-media_name]:Contains(${input})`).removeClass('hidden');
    });
}

var refresh_media_file_uploads = () => {
    $.pageoverlay.show();
    $.get(`${baseUrl}api/documents/media_list`).then((response) => {
        if(response.code == 200) {
            let result = response.data.result;
            if(result.files_list !== undefined) {
                $.each(result.attachments_list, function(ii, files) {
                    $.each(files, function(key, value) {
                        let isImage = ($.inArray(value.type, ["jpg", "jpeg", "png", "gif", ".webp"]) !== -1);
                        // let isImage = ($.inArray(value.type, ["jpg", "jpeg", "png", "gif", ".webp"]) !== -1);
                        $(`div[class="media-modal-content"] div[class~="media-content"] div[class="row"]`)
                            .append(`
                            <div data-media_item="filter" data-media_name="${value.name}" class="col-lg-2 cursor col-md-3 col-sm-6 mb-3 mt-1">
                                <div class="shadow-dark text-center" onclick="return preview_media('${ii}','${value.unique_id}')">
                                    ${isImage ? 
                                        `<img height="130px" src="${value.path}" width="100%">` : 
                                        `<div style="height:130px">
                                            <div class="p-1"><i class="text-${value.color} ${value.favicon} fa-7x"></i></div>
                                            <div class="bg-light font-12">${value.name}</div>
                                        </div>`
                                    }
                                </div>
                            </div>`);
                    });
                });
                setTimeout(() => {
                    $.pageoverlay.hide();
                    media_name_search();
                }, refresh_seconds);
            }
        }
    }).catch(() => {
        $.pageoverlay.hide();
    });
}

var media_file_uploader = (formdata) => {
    $.pageoverlay.show();
    $(`div[class~="upload-document-loader"]`).removeClass("hidden");
    $(`div[class="upload-content"] input[id="media_file_upload"]`).prop("disabled", true);
    $.ajax({
        url: `${baseUrl}api/files/media_uploads`,
        type: 'post',
        data: formdata,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            $.pageoverlay.hide();
            $(`div[class="upload-content"] input[id="media_file_upload"]`).val("").prop("disabled", false);
            if (response.code == 200) {
                notify(`${response.data.result}`, 'success');
                $(`div[class="media-modal-content"] div[class~="media-content"] div[class="row"]`).html(``);
                $(`li[class="nav-item"] a[id="media_library-tab2"]`).trigger("click");
            } else {
                notify(response.data.result);
            }
        }, error: function() {
            $.pageoverlay.hide();
        }
    });
}

$(`div[class="upload-content"] input[id="media_file_upload"]`).change(async function() {
    let files_input = $(this).get(0).files,
        files_data = new FormData(),
        files_uploaded = {};
    for (var i = 0; i < files_input.length; i++) {
        files_data.append(`files_list[${i}]`, files_input[i]);
        files_uploaded[i] = files_input[i].name;
    }
    media_file_uploader(files_data);
});

$(`li[class="nav-item"] a[id="media_library-tab2"]`).on("click", function() {
    refresh_media_file_uploads();
});