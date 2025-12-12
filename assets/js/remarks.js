var load_class_list = () => {
    let theClassSector = $(`select[name="remarks_class_id"]`);
    let theClassFilter = $(`select[name="filter_remarks_class_id"]`);

    $.get(`${baseUrl}api/classes/list?columns=a.id,a.item_id,a.name`).then((response) => {
        if (response.code == 200) {
            theClassSector.find('option').remove().end();
            theClassFilter.find('option').remove().end();
            if(response.data.result.length > 1) {
                theClassSector.append(`<option value="0" selected="selected">Select Class</option>`);
                theClassFilter.append(`<option value="0" selected="selected">Select Class to Filter</option>`);
            }
            $.each(response.data.result, (_, e) => {
                theClassSector.append(`<option data-item_id="${e.item_id}" value='${e.id}'>${e.name.toUpperCase()}</option>'`);
                theClassFilter.append(`<option data-item_id="${e.item_id}" value='${e.id}'>${e.name.toUpperCase()}</option>'`);
            });
            if(response.data.result.length == 1) {
                theClassSector.trigger("change");
                theClassFilter.trigger("change");
            }
        }
    });

    $(`select[name="remarks_class_id"]`).on("change", function() {
        let value = $(this).val();
        if(value == "0") {
            return;
        }
        $(`select[name="remarks_student_id"]`).find('option').remove().end();
        $(`select[name="remarks_student_id"]`).append(`<option value="0" selected="selected">Select Student</option>`);
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

    $(`select[id='filter_remarks_class_id']`).on("change", function() {
        let class_id = $(this).val();
        if(class_id == "0") {
            loadPage(`${baseUrl}results-remarks`);
            return;
        }
        loadPage(`${baseUrl}results-remarks?class_id=${$(this).val()}`);
    });
}

var add_student_remarks = () => {
    $(`#studentRemarksModal`).modal('show');
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
                }
            });
        }
    });
}

var search_remarks = () => {
    let search_value = $(`#search_remarks`).val();
    let totalVisible = 0;

    $(`#no_remarks_found`).remove();
    if(search_value == "") {
        $(`div[class~="remarks_item"]`).show();
        totalVisible = $(`div[class~="remarks_item"]`).length;
    }
    else {
        $(`div[class~="remarks_item"]`).each(function() {
            let student_name = $(this).attr("data-student_name");
            if(student_name.toLowerCase().includes(search_value.toLowerCase())) {
                $(this).show();
            }
            else {
                $(this).hide();
            }
        });
        totalVisible = $(`div[class~="remarks_item"]:visible`).length;
    }
    if(totalVisible == 0) {
        $(`div[data-input_item="search"]`)
            .append(`<div id='no_remarks_found' class='alert mt-2 alert-info'>No remarks found matching your search</div>`);
    }
}

load_class_list();