var show_eDocuments_Modal = () => {
    $(`div[id="ebook_Resource_Modal_Content"]`).modal("show");
}
var upload_Employee_Documents = (employee_id) => {
    swal({
        title: "Upload Documents",
        text: "Are you sure you want to upload these files for this resource. Please note that once confirmed, you cannot delete the files. Do you wish to proceed?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            $.post(`${baseUrl}api/users/upload_documents`, { employee_id }).then((response) => {
                $.pageoverlay.hide();
                if (response.code == 200) {
                    $(`div[id="ebook_Resource_Modal_Content"]`).modal("hide");
                    $(`div[preview-id="ebook_${employee_id}"]`).html("");
                    swal({
                        text: response.data.result,
                        icon: "success",
                    });
                    setTimeout(() => {
                        loadPage(`${baseUrl}${response.data.additional.url_link}/${employee_id}/documents`);
                    }, refresh_seconds);
                } else {
                    swal({
                        text: response.data.result,
                        icon: "error",
                    });
                }
            }).catch(() => {
                $.pageoverlay.hide();
            });
        }
    });
}