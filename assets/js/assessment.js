var assessment_form;

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
    $(`div[id='log_assessment'] select[name='course_id']`).find('option').remove().end();
    $(`div[id='log_assessment'] select[name='course_id']`).append(`<option value="null" selected="selected">Select Course</option>`);
    if (class_id !== "null" && class_id.length) {
        $.get(`${baseUrl}api/assignments/load_course_students`, { class_id }).then((response) => {
            if (response.code == 200) {
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

var track_marks_limit = (max_score) => {
    $(`input[data-item="marks"]`).on("input", function() {
        let item = $(this);
        let student_id = item.attr("data-student_id"),
            score = parseInt(item.val());
        
        if(score > max_score) {
            $(`input[data-item="marks"][data-student_id="${student_id}"]`).val(max_score);
        } else if(score < 0) {
            $(`input[data-item="marks"][data-student_id="${student_id}"]`).val(0);
        }
    });
}

var prepare_assessment_log = (assessment_log_id = "") => {
    let overall_score = $(`input[name="overall_score"]`).val();
    let data = {
        "assessment_title" : $(`textarea[name="assessment_title"]`).val(),
        "assessment_type" : $(`select[name="assessment_type"]`).val(),
        "class_id" : $(`select[name="class_id"]`).val(),
        "course_id" : $(`select[name="course_id"]`).val(),
        "date_due" : $(`input[name="date_due"]`).val(),
        "time_due" : $(`input[name="time_due"]`).val(),
        "overall_score" : overall_score
    };

    if(isNaN(overall_score)) {
        swal({
            text: "Sorry! The overall score must be a numeric integer",
            icon: "error",
        });
        return false;
    }
    $(`div[id="log_assessment"] *`).attr("disabled", true);
    assessment_form = data;
    $(`div[id="award_marks"]`).addClass("hidden");
    $.post(`${baseUrl}api/assignments/log_assessment`, data).then((response) => {
        if (response.code == 200) {
            if(response.data.result.students_list !== undefined) {
                let students_list = "";
                $.each(response.data.result.students_list, function(i, e) {
                    students_list += `
                    <tr>
                        <td>${i+1}</td>
                        <td>
                            <div class="d-flex justify-content-start">
                                <div class="mr-2">
                                    <img alt="image" src="${baseUrl}${e.image}" class="rounded-circle author-box-picture">
                                </div>
                                <div>
                                    ${e.name} <br>
                                    <strong>${e.unique_id}</strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input data-item="marks" data-student_id="${e.item_id}" data-student_name="${e.name}" type="number" min="0" max="100" class="form-control" id="marks[${e.item_id}]" name="marks[${e.item_id}]">
                        </td>
                    </tr>`;
                });
                $(`div[id="award_marks"]`).removeClass("hidden");
                $(`table[id="student_staff_list"] tbody`).html(students_list);
                track_marks_limit(overall_score);
            }
        } else {
            $(`div[id="log_assessment"] *`).attr("disabled", false);
            swal({
                text: response.data.result,
                icon: "error",
            });
        }
    }).catch(() => {
        $(`div[id="log_assessment"] *`).attr("disabled", false);
        swal({
            text: "Sorry! There was an error while processing the request.",
            icon: "error",
        });
    });
}
