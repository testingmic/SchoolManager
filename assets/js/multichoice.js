var quiz_answerHandler = () => {
    $(`table[id="multichoice_question"] input[name="answer_option"]:checkbox`).on('change', function() {
        let selected_option = $(this).val();
        let question_type = $(`table[id="multichoice_question"]`).attr("data-answer_type"),
            all_selected = $(`table[id="multichoice_question"] input[name='answer_option']:checkbox:checked`).length;
        if (question_type == "option") {
            if (all_selected > 1) {
                $(`table[id="multichoice_question"] input[name="answer_option"]:checkbox`).prop('checked', false);
                $(`table[id="multichoice_question"] input[name="answer_option"]:checkbox[value='${selected_option}']`).prop('checked', 'checked');
            }
        }
    });
}
quiz_answerHandler();

var loadQuestionInfo = (previous_id = "") => {
    let answers = {},
        question_id = $(`table[id="multichoice_question"]`).attr("data-question_id");
    if ($(`input[name='answer_option'][type='checkbox']`).length) {
        $.each($(`table[id="multichoice_question"] input[name='answer_option']:checked`), function(i, e) {
            answers[i] = $(this).val();
        });
    } else if ($(`input[name='answer_option'][type='number']`).length) {
        answers[0] = $(`input[name='answer_option'][type='number']`).val();
    } else if ($(`textarea[name='answer_option']`).length) {
        answers[0] = $(`input[name='answer_option'][type='number']`).val();
    }

    $.post(`${baseUrl}api/assignments/save_answer`, { question_id, answers, previous_id }).then((response) => {
        if (response.code == 200) {
            $(`div[id='assignment_question_detail']`).html(response.data.result);
            quiz_answerHandler();
        }
    });
}