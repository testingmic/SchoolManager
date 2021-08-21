var quick_formHandler = () => {
    $(`div[id="add_question_container"] select[id="answer_type"]`).on('change', function(e) {
        question_typeProcessor($(this).val());
    });

    $(`div[id="add_question_container"] input[name="answer_option"]:checkbox`).on('change', function() {
        let selected_option = $(this).val();
        let question_type = $(`div[id="add_question_container"] select[id="answer_type"]`).val();
        let all_selected = $("input[name='answer_option']:checkbox:checked").length;
        if (question_type == "option") {
            if (all_selected > 1) {
                $(`input[name="answer_option"]:checkbox`).prop('checked', false);
                $(`input[name="answer_option"]:checkbox[value='${selected_option}']`).prop('checked', 'checked');
            }
        }
    });
}

var question_typeProcessor = (value, answer_value = null) => {
    let entry_points = ["option", "multiple"];
    if ($.inArray(value, entry_points) !== -1) {
        $(`div[id="add_question_container"] .answers-div`).slideDown();
        $(`div[id="add_question_container"] .numeric-answer`).slideUp();
        $("input:checkbox[value='" + answer_value + "']").prop('checked', true);
        let all_selected = $("input[name='answer_option']:checkbox:checked").length;
        if (all_selected > 1) {
            $(`input[name="answer_option"]:checkbox`).prop('checked', false);
        }
    } else {
        $(`div[id="add_question_container"] .answers-div`).slideUp();
        $(`div[id="add_question_container"] .numeric-answer`).slideUp();
        $(`input[name="answer_option"]:checkbox`).prop('checked', false);
        $(`div[id="add_question_container"] .numeric-answer input[name='numeric_answer']`).val(answer_value);
        if (value == "numeric") {
            $(`div[id="add_question_container"] .numeric-answer`).slideDown();
        }
    }
}

var review_AssignmentQuestion = (assignment_id, question_id) => {
    $.get(`${baseUrl}api/assignments/review_question`, { assignment_id, question_id }).then((response) => {
        if (response.code == 200) {
            $(`div[id="full_question_detail"]`).html(response.data.result);
            quick_formHandler();
            $(`textarea[name="question"]`).focus();
            let loc = `${baseUrl}add-assessment/add_question?qid=${assignment_id}&q_id=${question_id}`;
            window.history.pushState({ current: loc }, "", loc);
        }
    });
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
                    let marks = 0;
                    $.each($(`table[id='questionnaire_table'] tr td[data-column='mark']`), function(i, e) {
                        marks += parseInt($(this).text());
                    });
                    $(`table[id='questionnaire_table'] tr td[data-column='total_marks']`).html(`<strong>${marks}</strong>`);
                }
            });
        }
    });
}

var clear_questionForm = () => {
    $(`input[name="question_id"]`).val("");
    $(`textarea[name="question"]`).focus();
    $(`select[name="difficulty"]`).val("medium").change();
    $(`select[name="answer_type"]`).val("option").change();
    $(`input[name="answer_option"]:checkbox`).prop('checked', false);
    $(`input[class~="objective_question"], [name="question"]`).val("");
    window.history.pushState({ current: `${baseUrl}add-assessment/add_question` }, "", `${baseUrl}add-assessment/add_question`);
}

var cancel_AssignmentQuestion = () => {
    swal({
        title: "Discard",
        text: "Are you sure you want to discard updates to this question.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            clear_questionForm();
        }
    });
}

var save_AssignmentQuestion = (assignment_id) => {
    let answers = {},
        question_data = {},
        options = { option_a, option_b, option_c, option_d, option_e };
    $.each($("input[name='answer_option']:checked"), function(i, e) {
        answers[i] = $(this).val();
    });
    let question = $(`textarea[name="question"]`).val(),
        answer_type = $(`select[name="answer_type"]`).val(),
        question_id = $(`input[name="question_id"]`).val(),
        marks = $(`input[name="marks"]`).val(),
        numeric_answer = $(`input[name="numeric_answer"]`).val(),
        difficulty = $(`select[name="difficulty"]`).val();
    $.each(options, function(i, e) {
        question_data[i] = $(`input[class~="objective_question"][name=${i}]`).val();
    });

    question_data["question"] = question;
    question_data["answer_type"] = answer_type;
    question_data["question_id"] = question_id;
    question_data["marks"] = marks;
    question_data["numeric_answer"] = numeric_answer;
    question_data["assignment_id"] = assignment_id;
    question_data["difficulty"] = difficulty;
    question_data["answers"] = answers;

    swal({
        title: "Save Question",
        text: "Are you sure you want to save this question?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/assignments/add_question`, question_data).then((response) => {
                let the_icon = "error";
                if (response.code == 200) {
                    clear_questionForm();
                    let questions_list = "",
                        count = 0,
                        marks = 0;
                    the_icon = "success";
                    $.each(response.data.additional.questions, function(i, e) {
                        count++;
                        marks += parseInt(e.marks);
                        questions_list += `
                        <tr data-row_id="${e.item_id}">
                            <td>${count}</td>
                            <td>${e.question}</td>
                            <td data-column='mark'>${e.marks}</td>
                            <td>
                                <button class="btn btn-outline-success btn-sm" onclick="return review_AssignmentQuestion('${assignment_id}','${e.item_id}')"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-outline-danger btn-sm" onclick="return remove_AssignmentQuestion('${assignment_id}','${e.item_id}')"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                    questions_list += `<tr><td></td><td align='right' data-column='total_marks'><strong>Total Marks:</strong></td><td><strong>${marks}</strong></td><td></td></tr>`;
                    $(`tbody[id='added_questions']`).html(questions_list);
                }
                swal({
                    text: response.data.result,
                    icon: the_icon,
                });
            });
        }
    });
}

quick_formHandler();