$(`div[id='create_assignment'] select[name='assignment_type']`).on("change", function() {
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

$(`button[class~="save-marks"]`).on("click", function() {
    var student_list = [],
        assignment_id = $(`input[name='data-student-id']`).attr("data-assignment_id"),
        grading = $(`input[name='data-student-id']`).attr("data-grading");
    if (confirm("Do you want to proceed to award the marks?")) {
        $("#assignment-content :input[name='test_grading']").each(function() {
            let student_id = $(this).attr("data-value"),
                student_mark = $(this).val();
            student_list.push(student_id + "|" + student_mark);
        });

        $.post(`${baseUrl}api/assignments/award_marks`, { student_list, assignment_id }).then((response) => {
            let the_icon = "error";
            if (response.code == 200) {
                the_icon = "success";
            }
            swal({
                position: 'top',
                text: response.data.result,
                icon: the_icon,
            });
        })
    }
});

var load_studentInfo = async(student_id, assignment_id) => {
    let returnVal = await $.ajax({
        method: "GET",
        url: `${baseUrl}api/assignments/student_info`,
        data: { student_id, assignment_id, preview: 1 }
    }).then(resp => resp);

    return returnVal;
}

var load_singleStudentData = async(assignment_id, grading) => {
    let the_data = $(`a[data-function="single-view"][data-value="${assignment_id}"]`).data(),
        results_page = $(".student-assignment-details"),
        htmlData = "";

    htmlData += "<table class='table'>";
    htmlData += "<thead><tr>";
    htmlData += "<th width='100%' style='font-weight:bolder; font-size:16px'>";
    htmlData += the_data.name;
    htmlData += "</th>";
    htmlData += `<th align='right'>${the_data.score}/${grading}</th>`;
    htmlData += "</tr></thead>";
    htmlData += "<tbody>";
    htmlData += "<tr><td colspan='2'>";

    let the_n_data = await load_studentInfo(the_data.student_id, assignment_id);

    htmlData += the_n_data.data.result;

    results_page.html(htmlData).css('display', 'block');
    $(".grading-history-div").css('display', 'block');
    $(`input[name="data-student-id"]`).val(the_data.student_id);
}