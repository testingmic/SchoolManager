var show_EResource_Modal = () => {
    $(`div[id="ebook_Resource_Modal_Content"]`).modal("show");
}

var upload_EBook_Resource = (book_id) => {
    swal({
        title: "Upload Resource",
        text: "Are you sure you want to upload these files for this resource?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/library/upload_resource`, { book_id }).then((response) => {
                if (response.code == 200) {
                    $(`div[id="ebook_Resource_Modal_Content"]`).modal("hide");
                    $(`div[preview-id="ebook_${book_id}"]`).html("");
                    $(`div[data-ebook_resource_list="${book_id}"]`).html(response.data.additional.files_list);
                    swal({
                        position: "top",
                        text: response.data.result,
                        icon: "success",
                    });
                    setTimeout(() => {
                        loadPage(`${baseUrl}update-book/${book_id}/view`);
                    }, 500);
                } else {
                    swal({
                        position: "top",
                        text: response.data.result,
                        icon: "error",
                    });
                }
            });
        }
    });
}

$(`div[id="library_form"] select[name="category_id"]`).on("change", function() {
    let category_id = $(this).val();

    if (category_id.length) {
        $.get(`${baseUrl}api/library/list`, { category_id }).then((response) => {
            if (response.code == 200) {
                $(`div[id='library_form'] select[name='book_id']`).find('option').remove().end();
                $(`div[id='library_form'] select[name='book_id']`).append(`<option value="null">Please Select</option>`);
                $.each(response.data.result, function(i, book) {
                    $(`div[id='library_form'] select[name='book_id']`).append(`<option value='${book.id}'>${book.title}</option>'`);
                });
            }
        });
    }
});