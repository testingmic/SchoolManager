$(`div[id='log_assessment'] select[name='assigned_to']`).on("change", function() {
    let value = $(this).val();
    if (value === "all_students") {
        $(`div[id='assign_to_students_list']`).addClass("hidden");
    } else {
        $(`div[id='assign_to_students_list']`).removeClass("hidden");
    }
});

$(`div[id='log_assessment'] select[name='class_id']`).on("change", function() {
    let class_id = $(this).val();
    if (class_id !== "null") {
        $.get(`${baseUrl}api/assignments/load_course_students`, { class_id }).then((response) => {
            if (response.code == 200) {
                $(`div[id='log_assessment'] select[name='course_id']`).find('option').remove().end();
                $(`div[id='log_assessment'] select[name='course_id']`).append(`<option value="null" selected="selected">Select Course</option>`);
                $.each(response.data.result.courses_list, (_, e) => {
                    $(`div[id='log_assessment'] select[name='course_id']`).append(`<option data-item_id="${e.item_id}" value='${e.item_id}'>${e.name}</option>'`);
                });

                $(`div[id='log_assessment'] select[name='assigned_to_list']`).find('option').remove().end();
                $(`div[id='log_assessment'] select[name='assigned_to_list']`).append(`<option value="null">Select Students</option>`);
                $.each(response.data.result.students_list, (_, e) => {
                    $(`div[id='log_assessment'] select[name='assigned_to_list']`).append(`<option data-item_id="${e.item_id}" value='${e.item_id}'>${e.name}</option>'`);
                });
            }
        });
    }
});

var prepare_assessment_log = (assessment_log_id = "") => {
    swal({
        title: "Load Students",
        text: `Do you want to proceed with the current action of loading students to award the marks?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let data = {
                "assessment_title" : $(`input[name="assessment_title"]`).val(),
                "assessment_type" : $(`select[name="assessment_type"]`).val(),
                "class_id" : $(`select[name="class_id"]`).val(),
                "course_id" : $(`select[name="course_id"]`).val(),
                "date_due" : $(`input[name="date_due"]`).val(),
                "time_due" : $(`input[name="time_due"]`).val()
            };
            $.get(`${baseUrl}api/assignments/prepare_assessment`, data).then((response) => {
                if (response.code == 200) {
                    
                } else {
                    swal({
                        text: response.data.result,
                        icon: "error",
                    });
                }
            }).catch(() => {
                swal({
                    text: "Sorry! There was an error while processing the request.",
                    icon: "error",
                });
            });
        }
    });
}
