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

                }
            });
        }
    });
}