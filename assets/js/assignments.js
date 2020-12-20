$(`div[id='create_assignment'] select[name='question_set_type']`).on("change", () => {
    let value = $(this).val();
    if (value === "multiple_choice") {
        $(`div[id='upload_question_set_template']`).addClass("hidden");
    } else {
        $(`div[id='upload_question_set_template']`).removeClass("hidden");
    }
});

$(`div[id='create_assignment'] select[name='assign_to']`).on("change", () => {
    let value = $(this).val();
    if (value === "all_students") {
        $(`div[id='assign_to_students_list']`).addClass("hidden");
    } else {
        $(`div[id='assign_to_students_list']`).removeClass("hidden");
    }
});

$(`div[id='create_assignment'] select[name='class_id']`).on("change", () => {
    let value = $(this).val();
    if (value !== "null") {
        $.get(`${baseUrl}api/assignments/load_course_students`, { class_id }).then((response) => {
            if (response.code == 200) {

            }
        });
    }
});