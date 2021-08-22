$(`div[id='create_assignment'] select[name='questions_type']`).on("change", function() {
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
                    $(`div[id='create_assignment'] select[name='course_id']`).append(`<option data-item_id="${e.item_id}" value='${e.item_id}'>${e.name}</option>'`);
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

var save_AssignmentMarks = () => {
    var student_list = [],
        assignment_id = $(`input[name='data-student-id']`).attr("data-assignment_id");
    swal({
        title: "Save Marks",
        text: "Do you want to proceed to award the marks?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            $("#assignment-content :input[name='test_grading']").each(function() {
                let student_id = $(this).attr("data-value"),
                    student_mark = $(this).val();
                student_list.push(student_id + "|" + student_mark);
            });
            $.post(`${baseUrl}api/assignments/award_marks`, { student_list, assignment_id }).then((response) => {
                $.pageoverlay.hide();
                if (response.code == 200) {
                    $(`span[class="graded_count"]`).html(`${response.data.additional.graded_count}`);
                    if (response.data.additional.marks !== undefined) {
                        $(`span[data-item="class_avarage"]`).html(response.data.additional.class_average);
                        $.each(response.data.additional.marks, function(i, e) {
                            $(`a[data-function="single-view"][data-student_id="${i}"]`).attr("data-score", e);
                            $(`input[name="test_grading"][data-value="${i}"]`).attr("value", e);
                        });
                    }
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
            }).catch(() => {
                $.pageoverlay.hide();
                swal({
                    text: "Sorry! There was an error while processing the request.",
                    icon: "error",
                });
            });
        }
    });
}

var view_AssignmentQuestion = (question_id) => {
        if ($.array_stream["questions_array"] !== undefined) {
            let questions = $.array_stream["questions_array"];
            if (questions[question_id] !== undefined) {
                let question = questions[question_id];
                $(`div[id="viewOnlyModal"]`).modal("show");
                $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html(`
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group mb-2">
                            <label><strong>Question</strong></label>
                            <div>${question.question}</div>
                        </div>    
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-2">
                            <label><strong>Answer Type</strong></label>
                            <div>${question.answer_type}</div>
                        </div>    
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-2">
                            <label><strong>Difficulty</strong></label>
                            <div>${question.difficulty}</div>
                        </div>    
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-2">
                            <label><strong>Correct Answer(s)</strong></label>
                            <div>${question.correct_answer}</div>
                        </div>    
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-2">
                            <label class="pb-0 mb-0"><strong>Option A</strong></label>
                            <div>${question.option_a}</div>
                        </div>    
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-2">
                            <label class="pb-0 mb-0"><strong>Option B</strong></label>
                            <div>${question.option_b}</div>
                        </div>    
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-2">
                            <label class="pb-0 mb-0"><strong>Option C</strong></label>
                            <div>${question.option_c}</div>
                        </div>    
                    </div>
                    ${question.option_d ? `
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group mb-2">
                                <label class="pb-0 mb-0"><strong>Option D</strong></label>
                                <div>${question.option_d}</div>
                            </div>    
                        </div>` : ""}
                    ${question.option_e ? `
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group mb-2">
                                <label class="pb-0 mb-0"><strong>Option E</strong></label>
                                <div>${question.option_e}</div>
                            </div>    
                        </div>` : ""}
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group mb-2">
                            <label class="pb-0 mb-0"><strong>Marks</strong></label>
                            <div>${question.marks}</div>
                        </div>    
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group mb-2">
                            <label class="pb-0 mb-0"><strong>Date Created</strong></label>
                            <div>${question.date_created}</div>
                        </div>    
                    </div>
                </div>
            `);
        }
    }
}

var remove_AssignmentQuestion = (assignment_id, question_id) => {
    swal({
        title: "Delete Question",
        text: "Are you sure you want to delete this question? You cannot reverse this action once confirmed.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/assignments/delete_question`, { assignment_id, question_id }).then((response) => {
                if (response.code == 200) {
                    $(`tr[data-row_id="${question_id}"]`).remove();
                    swal({
                        text: response.data.result,
                        icon: "success",
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

var publish_AssignmentQuestion = (assignment_id, questions_count) => {
    if (questions_count < 1) {
        swal({
            position: "top",
            text: "Sorry! You can only publish an assignment once at least one question has been added.",
            icon: "error",
        });
        return false;
    }
    swal({
        title: "Publish Assignment",
        text: "Are you sure you want to publish this Assignment? You cannot update the question set once it has been published.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/assignments/publish`, { assignment_id }).then((response) => {
                if (response.code == 200) {
                    swal({
                        text: "Congrats! The assignment was successfully published.",
                        icon: "success",
                    });
                    loadPage(`${baseUrl}update-assessment/${assignment_id}/view`);
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

var close_Assignment = () => {
    var assignment_id = $(`input[name='data-student-id']`).attr("data-assignment_id");
    swal({
        title: "Close Assignment",
        text: "Are you sure you want to close this Assignment? You cannot award marks once it has been closed.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/assignments/close`, { assignment_id }).then((response) => {
                if (response.code == 200) {
                    $(`div[class="initial_assignment_buttons"]`).remove();
                    $(`input[name="test_grading"]`).attr("disabled", "disabled");
                    $(`span[id="assignment_state"]`).html(`<span class="badge badge-danger">Closed</span>`);
                    swal({
                        text: response.data.result,
                        icon: "success",
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

var reopen_Assignment = () => {
    var assignment_id = $(`input[name='data-student-id']`).attr("data-assignment_id");
    swal({
        title: "Reopen Assignment",
        text: "Are you sure you want to reopen this Assignment? Once opened, marks awarded can be altered to reflect the changes.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/assignments/reopen`, { assignment_id }).then((response) => {
                if (response.code == 200) {
                    $(`input[name="test_grading"]`).prop("disabled", false);
                    $(`span[id="assignment_state"]`).html(`<span class="badge badge-success">Graded</span>`);
                    swal({
                        text: response.data.result,
                        icon: "success",
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

var load_studentInfo = async(student_id, assignment_id) => {
    let returnVal = await $.ajax({
        method: "GET",
        url: `${baseUrl}api/assignments/student_info`,
        data: { student_id, assignment_id, preview: 1 }
    }).then(resp => resp);
    return returnVal;
}

var review_QuizAssignment = (student_id, grading, assignment_id) => {
    $(`div[id="viewOnlyModal"]`).modal("show");
    $(`div[id="viewOnlyModal"] [class="modal-title pt-2"]`).html(`Review Answers`);
    $.post(`${baseUrl}api/assignments/review_answers`, { assignment_id, student_id, show_answer:true }).then((response) => {
        if (response.code = 200) {
            $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html(response.data.result);
        }
    }).catch(() => {});
}

var load_singleStudentData = async(student_id, grading) => {
    let the_data = $(`a[data-function="single-view"][data-student_id="${student_id}"]`).data(),
        results_page = $(".student-assignment-details"),
        htmlData = "";
    $(`input[name="data-student-id"]`).val(the_data.student_id);
    htmlData += "<table class='table'>";
    htmlData += "<thead><tr>";
    htmlData += "<th width='100%' style='font-weight:bolder; font-size:16px'>";
    htmlData += the_data.name;
    htmlData += "</th>";
    htmlData += `<th align='right'>${the_data.score}/${grading}</th>`;
    htmlData += "</tr></thead>";
    htmlData += "<tbody>";
    htmlData += "<tr><td colspan='2'>";
    let the_n_data = await load_studentInfo(the_data.student_id, the_data.assignment_id);
    htmlData += the_n_data.data.result;
    results_page.html(htmlData).css('display', 'block');
    $(".grading-history-div").css('display', 'block');
}

var submit_Answers = (assignment_id) => {
    swal({
        title: "Handin Assignment",
        text: "Are you sure you want to handin your assignment? Please note, you cannot revert the action once submitted.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/assignments/handin`, { assignment_id }).then((response) => {
                if (response.code == 200) {
                    $(`div[id="handin_upload"]`).remove();
                    $(`div[id="handin_documents"]`).removeClass("col-lg-4").addClass("col-lg-12");
                    $(`div[id="handin_documents"]`).html(response.data.additional);

                    swal({
                        text: response.data.result,
                        icon: "success",
                    });
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

trigger_form_submit();