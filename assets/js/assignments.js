$(`div[id='create_assignment'] select[name='question_set_type']`).on("change", function() {
    let value = $(this).val();
    if (value === "multiple_choice") {
        $(`button[type="button-submit"]`).html("Add Questions");
        $(`div[id='upload_question_set_template']`).addClass("hidden");
    } else {
        $(`button[type="button-submit"]`).html("Save Assignment");
        $(`div[id='upload_question_set_template']`).removeClass("hidden");
    }
});

$(`div[id='create_assignment'] select[name='assigned_to']`).on("change", function() {
    let value = $(this).val();
    if (value === "all_students") {
        $(`div[id='assign_to_students_list']`).addClass("hidden");
    } else {
        $(`div[id='assign_to_students_list']`).removeClass("hidden");
    }
});

$(`div[id='create_assignment'] select[name='class_id']`).on("change", function() {
    let class_id = $(this).val();
    if (class_id !== "null") {
        $.get(`${baseUrl}api/assignments/load_course_students`, { class_id }).then((response) => {
            if (response.code == 200) {
                $(`div[id='create_assignment'] select[name='course_id']`).find('option').remove().end();
                $(`div[id='create_assignment'] select[name='course_id']`).append(`<option value="null" selected="selected">Select Course</option>`);
                $.each(response.data.result.courses_list, (_, e) => {
                    $(`div[id='create_assignment'] select[name='course_id']`).append(`<option data-item_id="${e.id}" value='${e.id}'>${e.name}</option>'`);
                });

                $(`div[id='create_assignment'] select[name='assigned_to_list']`).find('option').remove().end();
                $(`div[id='create_assignment'] select[name='assigned_to_list']`).append(`<option value="null">Select Students</option>`);
                $.each(response.data.result.students_list, (_, e) => {
                    $(`div[id='create_assignment'] select[name='assigned_to_list']`).append(`<option data-item_id="${e.item_id}" value='${e.item_id}'>${e.name}</option>'`);
                });

            }
        });
    }
});