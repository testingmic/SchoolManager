if ($(`div[id="filter_Department_Class"]`).length) {

    $(`select[name="department_id"]`).on("change", function() {
        let value = $(this).val();
        $(`select[name='class_id']`).find('option').remove().end();
        $(`select[name='class_id']`).append(`<option value="">Please Select Class</option>`);
        if (value.length && value !== "null") {
            $.get(`${baseUrl}api/classes/list?columns=id,name`, { department_id: value }).then((response) => {
                if (response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        $(`select[name='class_id']`).append(`<option value='${e.id}'>${e.name}</option>'`);
                    });
                }
            });
        }
    });

    $(`button[id="filter_Students_List"]`).on("click", function() {
        department_id = $(`select[name="department_id"]`).val(),
            class_id = $(`select[name="class_id"]`).val(),
            gender = $(`select[name="gender"]`).val();
        $.form_data = { department_id, class_id, gender };
        loadPage(`${baseUrl}list-student`);
    });
}