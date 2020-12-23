$(`div[id="add_question_container"] select[id="answer_type"]`).on('change', function(e) {
    question_typeProcessor($(this).val());
});

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

var review_AssignmentQuestion = (assignment_id) => {

}

var remove_AssignmentQuestion = (question_id) => {
    swal({
        title: "Delete Question",
        text: "Are you sure you want to delete this question? You cannot reverse this action once confirmed.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/assignments/delete_question`, { question_id }).then((response) => {
                if (response.code == 200) {
                    $(`tr[data-row_id="${question_id}"]`).remove();
                    swal({
                        text: response.data.result,
                        icon: "success",
                    });
                }
            });
        }
    });
}

var clear_questionForm = () => {
    $(`select[name="difficulty"]`).val("medium").change();
    $(`select[name="answer_type"]`).val("option").change();
    $(`input[name="answer_option"]:checkbox`).prop('checked', false);
    $(`input[class~="objective_question"], [name="question"]`).val("");
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
        numeric_answer = $(`input[name="numeric_answer"]`).val(),
        difficulty = $(`select[name="difficulty"]`).val();
    $.each(options, function(i, e) {
        question_data[i] = $(`input[class~="objective_question"][name=${i}]`).val();
    });

    question_data["question"] = question;
    question_data["answer_type"] = answer_type;
    question_data["question_id"] = question_id;
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
                swal({
                    text: response.data.result,
                    icon: "success",
                });
                if (response.code == 200) {
                    clear_questionForm();
                    let questions_list = "",
                        count = 0;
                    $.each(response.data.additional.questions, function(i, e) {
                        count++;
                        questions_list += `
                        <tr data-row_id="${e.item_id}">
                            <td>${count}</td>
                            <td>${e.question}</td>
                            <td>
                                <button class="btn btn-outline-success btn-sm" onclick="return review_AssignmentQuestion('${e.item_id}')"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-outline-danger btn-sm" onclick="return remove_AssignmentQuestion('${e.item_id}')"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>`;
                    });
                    $(`tbody[id='added_questions']`).html(questions_list);
                }
            });
        }
    });
}