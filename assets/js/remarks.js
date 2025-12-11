var add_student_remarks = () => {
    $(`#studentRemarksModal`).modal('show');

    $.get(`${baseUrl}api/classes/list?columns=a.id,a.item_id,a.name`).then((response) => {
        if (response.code == 200) {
            $(`select[name="remarks_class_id"]`).find('option').remove().end();
            if(response.data.result.length > 1) {
                $(`select[name="remarks_class_id"]`).append(`<option value="null" selected="selected">Select Class</option>`);
            }
            $.each(response.data.result, (_, e) => {
                $(`select[name="remarks_class_id"]`).append(`<option data-item_id="${e.item_id}" value='${e.id}'>${e.name.toUpperCase()}</option>'`);
            });
            if(response.data.result.length == 1) {
                $(`select[name="remarks_class_id"]`).trigger("change");
            }
        }
    });

    $(`select[name="remarks_class_id"]`).on("change", function() {
        let value = $(this).val();
        if(value == "null") {
            return;
        }
        $(`select[name="remarks_student_id"]`).find('option').remove().end();
        $(`select[name="remarks_student_id"]`).append(`<option value="null" selected="selected">Select Student</option>`);
        $.get(`${baseUrl}api/users/minimal?class_id=${value}&user_type=student`).then((response) => {
            if (response.code == 200) {
                $.each(response.data.result.users, (_, e) => {
                    $(`select[name="remarks_student_id"]`).append(`<option value='${e.user_id}'>
                        ${e.firstname.toUpperCase()} ${e.lastname.toUpperCase()} (${e.unique_id})
                    </option>'`);
                });
            }
        });
    });
}

var delete_student_remarks = (remarks_id) => {
    swal({
        title: "Delete Student Remarks",
        text: "Are you sure you want to delete this student remarks?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/terminal_reports/delete_student_remarks`, { remarks_id }).then((response) => {
                if (response.code == 200) {
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code),
                    });
                    $(`div[data-remarks_id="${remarks_id}"]`).remove();
                    // if(response.code == 200) {
                    //     loadPage(`${baseUrl}results-remarks`);
                    // }
                }
            });
        }
    });
}