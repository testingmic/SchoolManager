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
                <button type='button' onclick='return remove_grading_mark(${rows_count})' data-grading_id='${rows_count}' class='btn btn-outline-danger'><i class='fa fa-trash'></i></button>
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
            <div class='col-lg-6'>
                <label>Name</label>
                <input type='text' maxlength='20' name='column_name_${rows_count}' data-column_id='${rows_count}' class='form-control'>
            </div>
            <div class='col-lg-2'>
                <label>Marks Cap</label>
                <input type='number' min='0' max='100' name='column_markscap_${rows_count}' data-column_id='${rows_count}' class='form-control'>
            </div>
            <div class='col-lg-2'>
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
                percentage = parseInt($(`input[name="column_percentage_${this_id}"]`).val()),
                markscap = parseInt($(`input[name="column_markscap_${this_id}"]`).val());
            count++;
            other_columns[name] = {percentage, markscap};
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
        if (response.code === 200) {
            $(`div[id='upload_file']`).removeClass("hidden");
            setTimeout(() => {
                window.location.href = `${baseUrl}${response.data.result}`;
            }, 500);
        }
    });
}

var save_terminal_report = () => {
    swal({
        title: "Save Student Marks",
        text: "Do you want to save this as the terminal report grades?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            let student_scores = {},
                class_id = $(`div[id="terminal_reports"] select[name="class_id"]`).val(),
                course_id = $(`div[id="terminal_reports"] select[name="course_id"]`).val();
            $.each($(`div[id="summary_report_sheet_content"] input[data-input_type="score"]`), function(i, e) {
                let item = $(this),
                    row_id = item.attr("data-input_row_id"),
                    student_id = $(`span[data-student_row_id="${row_id}"]`).attr("data-student_id"),
                    remarks = $(`input[data-input_row_id="${row_id}"][data-input_method='remarks']`).val();

                student_scores[i] = {
                    "item": item.attr("name"),
                    "score": item.val(),
                    "student_id": student_id,
                    "remarks": remarks
                };
            });
            let report_sheet = {
                class_id,
                course_id,
                student_scores
            };
            $.post(`${baseUrl}api/terminal_reports/save_report`, { report_sheet }).then((response) => {
                let s_code = "error";
                if (response.code === 200) {
                    s_code = "success";
                    $(`div[id='summary_report_sheet_content']`).html(``);
                    loadPage(`${baseUrl}results-upload/list`);
                }
                swal({
                    text: response.data.result,
                    icon: s_code,
                });
                $.pageoverlay.hide();
            }).catch(() => {
                $.pageoverlay.hide();
            });
        }
    });
}

var total_score_checker = () => {
    $(`div[id="summary_report_sheet_content"] input[data-input_type="score"][data-input_type_q='marks']`).on("input", function(event) {
        let input = $(this),
            unq_id = input.attr("data-input_row_id"),
            total_score = 0;
        $.each($(`input[data-input_row_id="${unq_id}"][data-input_type_q='marks']`), function(i, e) {
            let value = parseInt($(this).val());
            total_score += value;
        });
        if (total_score > 100) {
            $(`div[id="summary_report_sheet_content"] input[data-input_total_id="${unq_id}"]`).addClass("bg-danger text-white");
        } else {
            $(`div[id="summary_report_sheet_content"] input[data-input_total_id="${unq_id}"]`).removeClass("bg-danger text-white").addClass("text-black");
        }
        $(`div[id="summary_report_sheet_content"] input[data-input_total_id="${unq_id}"]`).val(total_score);
    });
}

var load_report_csv_file_data = (formdata) => {
    $(`div[id='summary_report_sheet_content']`).html(``);
    $.pageoverlay.show();
    $.ajax({
        type: 'POST',
        url: `${baseUrl}api/terminal_reports/upload_csv`,
        data: formdata,
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        success: function(response) {
            if (response.code === 200) {
                $(`div[id="terminal_reports"] input[name="upload_report_file"]`).val("");
                $(`div[id='summary_report_sheet_content']`).html(response.data.result);
                total_score_checker();
            } else {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
            }
        },
        complete: function() {
            $.pageoverlay.hide();
        },
        error: function(err) {
            swal({
                text: "Sorry! An unknown file type was uploaded.",
                icon: "error",
            });
            $.pageoverlay.hide();
        }
    });
}

var generate_terminal_report = () => {
    let academic_term = $(`select[name="academic_term"]`).val(),
        academic_year = $(`select[name="academic_year"]`).val(),
        student_id = $(`select[name="student_id"]`).val(),
        class_id = $(`select[name="class_id"]`).val();
    if ((academic_term === "null") || (academic_year === "null")) {
        swal({
            text: "Sorry! The Academic Year & Term are required.",
            icon: "error",
        });
        return false;
    }
    if ((class_id === "null") || !class_id) {
        swal({
            text: "Sorry! Please select the class to generate the terminal report.",
            icon: "error",
        });
        return false;
    }

    window.open(`${baseUrl}download/terminal?academic_term=${academic_term}&academic_year=${academic_year}&class_id=${class_id}&student_id=${student_id}`)
}

$(`div[id="terminal_reports"] select[name="class_id"]`).on("change", function() {
    let class_id = $(this).val();
    $(`div[id="notification"]`).html(``);
    $(`div[id='upload_file']`).addClass("hidden");
    let option_link = $(`select[name="course_id"]`).length !== 0 ? "course_id" : "student_id";
    let option_name = option_link === "course_id" ? "Select the Course" : "Select the Student";
    $(`div[id="terminal_reports"] select[name='${option_link}']`).find('option').remove().end();
    $(`div[id="terminal_reports"] select[name='${option_link}']`).append(`<option value="">${option_name}</option>`);
    if (class_id !== "null") {
        let link = $(`select[name="course_id"]`).length !== 0 ? "courses" : "users";
        $(`div[id="terminal_reports"] button[type='download_csv'], div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", false);
        if (class_id.length) {
            $.get(`${baseUrl}api/${link}/list?class_id=${class_id}&minified=true`).then((response) => {
                if (response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        $(`select[name='${option_link}']`).append(`<option value='${e.item_id !== undefined ? e.item_id : e.user_id}'>${e.name}</option>'`);
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
    $(`div[id="notification"]`).html(``);
    if (course_id !== "null") {
        $(`div[id="upload_file"]`).addClass("hidden");
        let class_id = $(`div[id="terminal_reports"] select[name="class_id"]`).val();
        $(`div[id="terminal_reports"] button[type='download_csv'], div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", false);
        $.get(`${baseUrl}api/terminal_reports/check_existence`, { course_id, class_id }).then((response) => {
            $(`div[id="notification"]`).html(`<span class="text-${response.code == 200 ? "success" : "danger"}">${response.data.result}</span>`);
            if(response.code !== 200) {
                $(`button[type="download_csv"]`).prop("disabled", true);
            } else {
                $(`button[type="download_csv"]`).prop("disabled", false);
            }
        });
    } else {
        $(`div[id="terminal_reports"] button[type='download_csv'], div[id="terminal_reports"] button[type='upload_button']`).prop("disabled", true);
    }
});

$(`div[id="terminal_reports"] select[name='upload_type']`).on("change", function() {
    let value = $(this).val();
    $(`div[id='upload_file']`).addClass("hidden");
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

$(`div[id="terminal_reports"] input[name="upload_report_file"]`).change(function() {
    var fd = new FormData();
    var files = $('div[id="terminal_reports"] input[name="upload_report_file"]')[0].files[0],
        class_id = $(`div[id="terminal_reports"] select[name="class_id"]`).val(),
        course_id = $(`div[id="terminal_reports"] select[name="course_id"]`).val();
    fd.append('report_file', files);
    fd.append('class_id', class_id);
    fd.append('course_id', course_id);
    load_report_csv_file_data(fd, "course");
});