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

var remove_AssignmentQuestion = (assignment_id) => {

}

var cancel_AssignmentQuestion = (assignment_id) => {
    swal({
        title: "Discard",
        text: "Are you sure you want to discard updates to this question.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`input[class~="objective_question"]`).val("");
            $(`select[name="difficulty"]`).val("medium").change();
            $(`select[name="answer_type"]`).val("option").change();
            $(`input[name="answer_option"]:checkbox`).prop('checked', false);
        }
    });
}

var save_AssignmentQuestion = (assignment_id) => {

}