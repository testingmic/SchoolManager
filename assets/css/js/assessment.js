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
    $(`div[id='log_assessment'] select[name='course_id']`).append(`<option value="null" selected="selected">Select Subject</option>`);
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

var award_marks = (mode) => {
    let title = (mode == "save") ? "Award Marks & Save" : "Award Marks & Close",
        message = (mode == "save") ? `Are you sure you want to submit this form? You will be able update the list later on.`
            : `Are you sure you want to submit this form? You will lose the opportunity to update the marks awarded to students.`;
        
    swal({
        title: title,
        text: message,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            $(`div[id="award_marks"] *`).attr("disabled", true);
            let students_list = {}
                counter = 0;
            $.each($(`input[data-item="marks"]`), function(i, e) {
                let item = $(this);
                counter++;
                let student_id = item.attr("data-student_id"),
                    student_rid = item.attr("data-student_rid"),
                    student_name = item.attr("data-student_name"),
                    awarded_score = parseInt(item.val());
                    students_list[student_id] = {
                        name: student_name,
                        student_rid: student_rid,
                        score: awarded_score
                    }
            });
            
            assessment_form["mode"] = mode;

            let content = {
                "data": assessment_form,
                "students_list": students_list
            }
            $.post(`${baseUrl}api/assignments/save_assessment`, content).then((response) => {
                $.pageoverlay.hide();
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code == 200) {
                    if(response.data.additional !== undefined) {
                        setTimeout(() => {
                            loadPage(response.data.additional.href);
                        }, refresh_seconds);
                    }
                } else {
                    $(`div[id="award_marks"] *`).attr("disabled", false);
                }
            }).catch(() => {
                $.pageoverlay.hide();
                $(`div[id="award_marks"] *`).attr("disabled", false);
                swal({
                    text: "Sorry! There was an error while processing the request.",
                    icon: "error",
                });
            });
        }
    });
}

var cancel_assessment = () => {
    swal({
        title: "Cancel Form",
        text: "Are you sure you want to cancel the awarding of marks to students?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`table[id="student_staff_list"] tbody`).html("");
            $(`div[id="log_assessment"] *`).attr("disabled", false);
            $(`div[id="award_marks"]`).addClass("hidden");
            $(`div[id="init_data"]`).removeClass("hidden");
        }
    });
}

var cancel_form = () => {
    $(`div[id="init_data"]`).removeClass("hidden");
    $(`table[id="student_staff_list"] tbody`).html("");
    $(`div[id="log_assessment"] *`).attr("disabled", false);
    $(`div[id="award_marks"]`).addClass("hidden");
}

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
        "assessment_description" : htmlEntities($(`trix-editor[id="assessment_description"]`).html()),
        "assessment_title" : $(`input[name="assessment_title"]`).val(),
        "assessment_type" : $(`select[name="assessment_type"]`).val(),
        "class_id" : $(`select[name="class_id"]`).val(),
        "course_id" : $(`select[name="course_id"]`).val(),
        "date_due" : $(`input[name="date_due"]`).val(),
        "time_due" : $(`input[name="time_due"]`).val(),
        "overall_score" : overall_score
    };
    if(isNaN(overall_score)) {
        notify("Sorry! The overall score must be a numeric integer");
        return false;
    }
    $(`div[id="log_assessment"] *`).attr("disabled", true);
    assessment_form = data;
    $(`div[id="award_marks"]`).addClass("hidden");
    $.post(`${baseUrl}api/assignments/prepare_assessment`, data).then((response) => {
        if (response.code == 200) {
            if(response.data.result.students_list !== undefined) {
                let students_list = "";
                $.each(response.data.result.students_list, function(i, e) {
                    students_list += `
                    <tr data-row_search='name' data-student_fullname='${e.name}' data-student_unique_id='${e.unique_id}'>
                        <td>${i+1}</td>
                        <td>
                            <div class="d-flex justify-content-start">
                                <div class="mr-2">
                                    <img width="40px" alt="image" src="${baseUrl}${e.image}" class="rounded-circle author-box-picture">
                                </div>
                                <div>
                                    ${e.name.toUpperCase()} <br>
                                    <strong>${e.unique_id}</strong>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input data-item="marks" data-student_id="${e.item_id}" data-student_rid="${e.id}" data-student_name="${e.name}" type="number" min="0" max="100" class="form-control text-center" id="marks[${e.item_id}]" name="marks[${e.item_id}]">
                        </td>
                    </tr>`;
                });
                if(!students_list) {
                    students_list += `
                    <tr>
                        <td align="center" colspan="3">
                        Sorry! No student found for the selected class. <span onclick="return cancel_form()" class="underline text-primary">Click to cancel</span>
                        </td>
                    </tr>`;
                    $(`div[id="award_marks"] div[id="buttons"]`).addClass("hidden");
                } else {
                    $(`div[id="award_marks"] div[id="buttons"]`).removeClass("hidden");
                }
                $(`div[id="init_data"]`).addClass("hidden");
                $(`div[id="award_marks"]`).removeClass("hidden");
                $(`table[id="student_staff_list"] tbody`).html(students_list);
                track_marks_limit(overall_score);
                student_fullname_search();
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