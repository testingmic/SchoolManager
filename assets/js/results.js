var recalculate_score = (student_id, sba_percentage, examination) => {
    let score = 0;
    $.each($(`input[data-input_type_q="marks"][data-input_row_id="${student_id}"]`), function() {
        let input = $(this);
        if(input.val().length && !isNaN(input.val())) {
            score += parseInt(input.val());
        }
    });
    let sba_score = (score * sba_percentage) / 100;
    let examination_score = examination;
    let total_score = sba_score + examination_score;
    
    $(`span[data-input_row_id="${student_id}"][data-examination]`).text(Math.round(examination_score));
    $(`span[data-input_row_id="${student_id}"][data-school_based_assessment]`).text(Math.round(sba_score));
    $(`span[data-input_row_id="${student_id}"][data-student_percentage]`).text(`${Math.round(total_score)}%`);
}

$(`input[data-input_method="remarks"]`).on("input", function() {
    let input = $(this);
    let student_id = input.attr("data-input_row_id");

    $(`span[data-input_save_button="${student_id}"]`).removeClass("hidden");
});

$(`input[data-input_type_q="marks"]`).on("input", function() {
    let input = $(this);
    let row_id = parseInt(input.attr("data-input_row_id")),
        max_value = parseInt(input.attr("data-max_value")),
        input_value = parseInt(input.val());

    if(input_value > max_value) {
        input.val(max_value);
    } else if(input_value < 0) {
        input.val(0);
    }
    let sba_percentage = parseInt($(`span[data-grade_name="sba"]`).attr("data-grade_percentage")),
        examination = parseInt($(`span[data-input_row_id="${row_id}"][data-examination]`).attr("data-examination"));
    recalculate_score(row_id, sba_percentage, examination);
});

var modify_result = (action, report_id) => {

    let s_title = (action == "submit") ? "Submit Results" : (action == "cancel" ? "Cancel Results" : "Approve Results");
    swal({
        title: s_title,
        text: `You have opted to ${action} this Results. Please note that you will not be able to update the record once it has been submitted. Do you want to proceed?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            let label = {
                "action": action,
                "report_id": report_id
            };
            $.post(`${baseUrl}api/terminal_reports/modify`, { label }).then((response) => {
                let s_code = "error";
                if (response.code == 200) {
                    s_code = "success";
                }
                swal({
                    text: response.data.result,
                    icon: s_code,
                });
                if (response.data.additional.disable !== undefined) {
                    if (response.data.additional.disable == "student") {
                        $(`tr[data-result_row_id="${report_id}"] *`).attr({ "disabled": true, "data-input_method": false, "data-input_type": false });
                        $(`tr[data-result_row_id="${report_id}"] span[data-input_approve_button]`).remove();
                    }
                }
                $.pageoverlay.hide();
            }).catch(() => {
                $.pageoverlay.hide();
            });
        }
    });
}

var save_result = (record_id, record_type, additional_id = "") => {
    let s_title = (record_type == "approve_results") ? "Approve Results" : "Save Results",
        s_message = (record_type == "approve_results") ? `You have opted to Approve this Results. Please note that you will not be able to update the record once it has been submitted. Do you want to proceed?` : "Do you want to save this updated results?";

    swal({
        title: s_title,
        text: s_message,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            let results = {};
            let raw_record_id = record_id;
            let refresh = true;
            if (record_type === "student") {
                $.each($(`input[data-input_type_q="marks"][data-input_row_id="${record_id}"]`), function(i, e) {
                    let item_id = $(this).attr(`data-input_row_id`),
                        item = $(this).attr("data-input_name"),
                        student_id = $(this).attr(`data-result_student_id`),
                        score = parseInt($(this).val()),
                        sba = $(`span[data-result_student_id="${student_id}"][data-school_based_assessment]`).text(),
                        examination = $(`span[data-result_student_id="${student_id}"][data-examination]`).text(),
                        remarks = $(`input[data-input_method="remarks"][data-input_row_id="${item_id}"]`).val();
                    if (typeof results[student_id] === 'undefined') {
                        results[student_id] = {
                            ["remarks"]: remarks,
                            ["marks"]: {}
                        };
                    }
                    results[student_id]["marks"][item] = score;
                    results[student_id]["marks"]["sba"] = sba;
                    results[student_id]["marks"]["marks"] = examination;
                });
                record_id = additional_id;
                record_type = "results";
                refresh = false;
            } else if (record_type === "results" || record_type === "approve_results") {
                $.each($(`input[data-input_type_q="marks"]`), function(i, e) {
                    let item_id = $(this).attr(`data-input_row_id`),
                        item = $(this).attr("data-input_name"),
                        student_id = $(this).attr(`data-result_student_id`),
                        score = parseInt($(this).val()),
                        sba = $(`span[data-result_student_id="${student_id}"][data-school_based_assessment]`).text(),
                        examination = $(`span[data-result_student_id="${student_id}"][data-examination]`).text(),
                        remarks = $(`input[data-input_method="remarks"][data-input_row_id="${item_id}"]`).val();
                    if (typeof results[student_id] === 'undefined') {
                        results[student_id] = {
                            ["remarks"]: remarks,
                            ["marks"]: {}
                        };
                    }
                    results[student_id]["marks"][item] = score;
                    results[student_id]["marks"]["sba"] = sba;
                    results[student_id]["marks"]["marks"] = examination;
                });
            }
            let label = {
                results,
                record_type,
                record_id
            };
            $.post(`${baseUrl}api/terminal_reports/update_report`, { label }).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code == 200) {
                    $(`span[data-input_save_button="${record_id}"]`).addClass("hidden");
                    $(`span[data-input_save_button="${raw_record_id}"]`).addClass("hidden");
                    if(refresh) {
                        if (typeof response.data.additional.href !== 'undefined') {
                            setTimeout(() => {
                                loadPage(response.data.additional.href);
                            }, refresh_seconds);
                        }
                    }
                }
                $.pageoverlay.hide();
            }).catch(() => {
                $.pageoverlay.hide();
            })
        }
    });
}

var save_sba_score_cap = (result_id) => {
    let sba_score_cap = $(`input[name="sba_score_cap"][data-result_id="${result_id}"]`).val();
    let label = {
        sba_score_cap,
        result_id
    };
    $.post(`${baseUrl}api/terminal_reports/save_sba_score_cap`, label).then((response) => {
        if(response.code == 200) {
            setTimeout(() => {
                $(`span[data-input_save_button="${result_id}"]`).trigger("click");
            }, refresh_seconds);
        } else {
            swal({
                text: response.data.result || response.description,
                icon: responseCode(response.code),
            });
        }
    });
}
