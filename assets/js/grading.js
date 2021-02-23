var remove_grading_mark = (grading_id) => {
    $(`div[class~="grade_item"][data-grading_id='${grading_id}']`).remove();
    let grades_count = $(`div[id="grading_system_list"] div[class~="grade_item"]`).length;
    if (!grades_count) {
        $(`button[id="save_grading_mark"]`).addClass("hidden");
    } else {
        $(`button[id="save_grading_mark"]`).removeClass("hidden");
    }
}

var add_grading_mark = () => {
    let grades_list = $(`div[id="grading_system_list"] div[class~="grade_item"]:last`).attr("data-grading_id"),
        rows_count = isNaN(grades_list) ? 1 : (parseInt(grades_list) + 1);
    let grade_html = `
        <div class='row mb-2 grade_item' data-grading_id='${rows_count}'>
            <div class='col-lg-3'>
                <label>Mark Begin(%)</label>
                <input type='number' min='0' max='100' name='start_${rows_count}' data-grading_id='${rows_count}' class='form-control'>
            </div>
            <div class='col-lg-3'>
                <label>Marks End Point(%)</label>
                <input type='number' min='0' max='100' name='end_${rows_count}' data-grading_id='${rows_count}' class='form-control'>
            </div>
            <div class='col-lg-5'>
                <label>Interpretation</label>
                <input type='text' min='0' max='100' name='interpretation_${rows_count}' data-grading_id='${rows_count}' class='form-control'>
            </div>
            <div class='col-lg-1'>
                <label>&nbsp;</label>
                <button type='button' onclick='return remove_grading_mark(${rows_count})' data-grading_id='${rows_count}' class='btn btn-block btn-outline-danger'><i class='fa fa-trash'></i></button>
            </div>
        </div>`;
    $(`button[id="save_grading_mark"]`).removeClass("hidden");
    $(`div[id="grading_system_list"]`).append(grade_html);
}

var remove_report_column = (column_id) => {
    $(`div[class~="column_item"][data-column_id='${column_id}']`).remove();
}

var add_report_column = () => {
    let columns_list = $(`div[id="term_report_columns_list"] div[class~="column_item"]:last`).attr("data-column_id"),
        rows_count = isNaN(columns_list) ? 1 : (parseInt(columns_list) + 1);
    let grade_html = `
        <div class='row mb-2 column_item' data-column_id='${rows_count}'>
            <div class='col-lg-7'>
                <label>Name</label>
                <input type='text' maxlength='20' name='column_name_${rows_count}' data-column_id='${rows_count}' class='form-control'>
            </div>
            <div class='col-lg-3'>
                <label>Percentage(%)</label>
                <input type='number' min='0' max='100' name='column_percentage_${rows_count}' data-column_id='${rows_count}' class='form-control'>
            </div>
            <div class='col-lg-2'>
                <label>&nbsp;</label>
                <button type='button' onclick='return remove_report_column(${rows_count})' data-column_id='${rows_count}' class='btn btn-block btn-outline-danger'><i class='fa fa-trash'></i></button>
            </div>
        </div>`;
    $(`div[id="term_report_columns_list"]`).append(grade_html);
}

var save_grading_mark = () => {
    let grades_count = $(`div[id="grading_system_list"] div[class~="grade_item"]`).length;
    if (!grades_count) {
        swal({
            text: "Sorry! Please add a grade to continue.",
            icon: "error",
        });
    } else {
        let grading_values = {},
            report_columns = {},
            other_columns = {},
            count = 0;
        $.each($(`div[id="grading_system_list"] div[class~="grade_item"]`), function(i, e) {
            let this_id = $(this).attr("data-grading_id"),
                start = $(`input[name="start_${this_id}"]`).val(),
                end = $(`input[name="end_${this_id}"]`).val(),
                interpretation = $(`input[name="interpretation_${this_id}"]`).val();
            count++;
            grading_values[count] = {
                start,
                end,
                interpretation
            }
        });

        report_columns["course_title"] = true;

        $.each($(`div[id="term_report_columns_list"] div[class~="column_item"]`), function(i, e) {
            let this_id = $(this).attr("data-column_id"),
                name = $(`input[name="column_name_${this_id}"]`).val(),
                percentage = $(`input[name="column_percentage_${this_id}"]`).val();
            count++;
            other_columns[name] = percentage;
        });

        report_columns["columns"] = other_columns;
        report_columns["average_score"] = true;
        report_columns["show_position"] = $(`select[name="show_position"]`).val();
        report_columns["show_teacher_name"] = $(`select[name="show_teacher_name"]`).val();
        report_columns["allow_submission"] = $(`select[name="allow_submission"]`).val();
        report_columns["teacher_comments"] = true;

        swal({
            title: "Save Grades",
            text: "Do you want to save this as the grading system?",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((proceed) => {
            if (proceed) {
                $.post(`${baseUrl}api/account/update_grading`, { grading_values, report_columns }).then((response) => {
                    if (response.code == 200) {
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
                });
            }
        });
    }
}

var download_report_csv = () => {
    let class_id = $(`div[id="terminal_reports"] select[name="class_id"]`).val(),
        course_id = $(`div[id="terminal_reports"] select[name="course_id"]`).val();
    $.get(`${baseUrl}api/terminal_reports/download_csv`, { class_id, course_id }).then((response) => {
        if (response.code == 200) {
            window.location.href = `${baseUrl}${response.data.result}`;
        }
    });
}

$(`div[id="terminal_reports"] select[name="class_id"]`).on("change", function() {
    let class_id = $(this).val();
    $(`div[id="terminal_reports"] select[name='course_id']`).find('option').remove().end();
    $(`div[id="terminal_reports"] select[name='course_id']`).append(`<option value="null">Please Select Course</option>`);
    if (class_id !== "null") {
        $(`div[id="terminal_reports"] button[type='download_csv'], div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", false);
        if (class_id.length) {
            $.get(`${baseUrl}api/courses/list?class_id=${class_id}&minified=true`).then((response) => {
                if (response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        $(`select[name='course_id']`).append(`<option value='${e.item_id}'>${e.name}</option>'`);
                    });
                }
            });
        }
    } else {
        $(`div[id="terminal_reports"] button[type='download_csv'], div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", true);
    }
});

$(`div[id="terminal_reports"] select[name="course_id"]`).on("change", function() {
    let course_id = $(this).val();
    if (course_id !== "null") {
        $(`div[id="terminal_reports"] button[type='download_csv'], div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", false);
    } else {
        $(`div[id="terminal_reports"] button[type='download_csv'], div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", true);
    }
});

$(`div[id="terminal_reports"] select[name='upload_type']`).on("change", function() {
    let value = $(this).val();
    if (value === "download") {
        $(`div[id="terminal_reports"] div[id='upload_button']`).addClass("hidden");
        $(`div[id="terminal_reports"] div[id='download_button']`).removeClass("hidden");
        $(`div[id="terminal_reports"] button[type='download_csv']`).prop("disabled", false);
        $(`div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", true);
    } else {
        $(`div[id="terminal_reports"] div[id='download_button']`).addClass("hidden");
        $(`div[id="terminal_reports"] div[id='upload_button']`).removeClass("hidden");
        $(`div[id="terminal_reports"] button[type='download_csv']`).prop("disabled", true);
        $(`div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", false);
    }
});