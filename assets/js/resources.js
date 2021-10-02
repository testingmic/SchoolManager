var course_resources_list = (search_term = "") => {
    let content = $(`div[id="courses_resources_list"]`);
    $(`div[id="elearning"] div[class="form-content-loader"]`).css("display", "flex");
    $.get(`${baseUrl}api/resources/e_courses?rq=${search_term}`).then((response) => {
        if (response.code === 200) {
            if (response.data.result.pagination !== undefined) {
                $(`div[id="total_count"]`).html(`<strong>${response.data.result.pagination.total_count} results</strong>`);
            }
            content.html(response.data.result.html);
            init_image_popup();
        }
        $(`div[id="elearning"] div[class="form-content-loader"]`).css("display", "none");
    }).catch(() => {
        $(`div[id="elearning"] div[class="form-content-loader"]`).css("display", "none");
    });
}

if ($(`div[id="courses_resources_list"]`).length) {
    course_resources_list();
}

var search_Resource = () => {
    let term = $(`input[name="search_term"]`).val();
    course_resources_list(term);
}

$(`div[id="course_resource"] input[name="search_term"]`).on("keyup", function(evt) {
    if (evt.keyCode == 13 && !evt.shiftKey) {
        let term = $(`input[name="search_term"]`).val();
        course_resources_list(term);
    }
});

var elearning_resources_list = (search_term = "") => {
    let content = $(`div[id="elearning_resources_list"]`),
        location = `${baseUrl}e-learning?lookup=${search_term.rq}`;
    $(`div[id="elearning"] div[class="form-content-loader"]`).css("display", "flex");
    $.get(`${baseUrl}api/resources/e_resources`, search_term).then((response) => {
        if (response.code === 200) {
            if (response.data.result.pagination !== undefined) {
                $(`div[id="total_count"]`).html(`<strong>${response.data.result.pagination.total_count} results</strong>`);
            }
            content.html(response.data.result.html);
            init_image_popup();
        }
        window.history.pushState({ current: location }, "", location);
        $(`div[id="elearning"] div[class="form-content-loader"]`).css("display", "none");
    }).catch(() => {
        $(`div[id="elearning"] div[class="form-content-loader"]`).css("display", "none");
    });
}
if ($(`div[id="elearning_resources_list"]`).length) {
    let data = {rq: ""};
    elearning_resources_list(data);
}
var search_E_learning_Resource = () => {
    let rq = $(`input[name="search_term"]`).val(),
        class_id = $(`select[name="class_id"]`).val(),
        course_id = $(`select[name="course_id"]`).val(),
        unit_id = $(`select[name="unit_id]`).val();

    let data = {
        class_id,
        rq,
        course_id,
        unit_id
    };
    elearning_resources_list(data);
}

$(`div[id="e_resources"] input[name="search_term"]`).on("keyup", function(evt) {
    if (evt.keyCode == 13 && !evt.shiftKey) {
        search_E_learning_Resource();
    }
});

if ($(`select[name="course_id"]`).length) {
    $(`select[name="class_id"]`).on("change", function() {
        let value = $(this).val();
        $(`select[name='course_id']`).find('option').remove().end();
        $(`select[name='course_id']`).append(`<option value="null">Please Select Subject</option>`);
        if (value !== "null" && value.length) {
            $.get(`${baseUrl}api/courses/list?class_id=${value}&minified=true`).then((response) => {
                if (response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        $(`select[name='course_id']`).append(`<option value='${e.id}'>${e.name}</option>'`);
                    });
                }
            });
        }
    });
}

if ($(`select[name="unit_id"]`).length) {
    $(`select[name="course_id"]`).on("change", function() {
        let value = $(this).val();
        $(`select[name='unit_id']`).find('option').remove().end();
        $(`select[name='unit_id']`).append(`<option value="null">Please Select Unit</option>`);
        if (value !== "null" && value.length) {
            $.get(`${baseUrl}api/courses/course_unit_lessons_list?course_id=${value}&minified=true`).then((response) => {
                if (response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        $(`select[name='unit_id']`).append(`<option value='${e.item_id}'>${e.name}</option>'`);
                    });
                }
            });
        }
    });
}