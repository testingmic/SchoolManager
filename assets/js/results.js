var recalculate_score = (student_id, total) => {
    let score = 0,
        total_percentage = 0;
    $.each($(`input[data-input_type_q="marks"][data-input_row_id="${student_id}"]`), function() {
        let input = $(this);
        if(input.val().length && !isNaN(input.val())) {
            let mark = parseInt(input.val()),
                raw = parseInt(input.attr("data-max_value")),
                percentage = parseInt(input.attr("data-raw_percentage"));
            score += mark;

            let percent = ((mark * percentage) / raw);
            total_percentage += percent;
        }
    });
    if (score > total) {
        $(`input[data-input_type_q="marks"][data-input_row_id="${student_id}"]`).addClass("border-red");
    } else {
        $(`input[data-input_type_q="marks"][data-input_row_id="${student_id}"]`).removeClass("border-red");
    }
    $(`input[data-input_total_id="${student_id}"]`).val(score);
    $(`span[data-input_save_button="${student_id}"]`).removeClass("hidden");
    $(`span[data-student_percentage="${student_id}"]`).html(`${total_percentage}%`);
}

$(`input[data-input_method="remarks"]`).on("input", function() {
    let input = $(this);
    let student_id = input.attr("data-input_row_id");

    $(`span[data-input_save_button="${student_id}"]`).removeClass("hidden");
});

$(`input[data-input_type_q="marks"]`).on("input", function() {
    let input = $(this);
    let student_id = input.attr("data-input_row_id"),
        total = parseInt(input.attr("data-overall_total")),
        max = parseInt(input.attr("data-max_value"));

    if(input.val() > max) {
        input.val(max);
        recalculate_score(student_id, total);
        return false;
    }
    else if(input.val() < 0) {
        input.val(0);
        recalculate_score(student_id, total);
        return false;
    }
    recalculate_score(student_id, total);
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
            let results = {},
                student_ids = new Array();
            if (record_type === "student") {
                $.each($(`input[data-input_type_q="marks"][data-input_row_id="${record_id}"]`), function(i, e) {
                    let item = $(this).attr("data-input_name"),
                        score = parseInt($(this).val()),
                        remarks = $(`input[data-input_method="remarks"][data-input_row_id="${record_id}"]`).val();
                    if (results[record_id] === undefined) {
                        results[record_id] = {
                            ["remarks"]: remarks,
                            ["marks"]: {}
                        };
                    }
                    results[record_id]["marks"][item] = score;
                });
            } else if (record_type === "results" || record_type === "approve_results") {
                $.each($(`input[data-input_type_q="marks"]`), function(i, e) {
                    let item_id = $(this).attr(`data-input_row_id`),
                        item = $(this).attr("data-input_name"),
                        score = parseInt($(this).val()),
                        remarks = $(`input[data-input_method="remarks"][data-input_row_id="${item_id}"]`).val();
                    if (results[item_id] === undefined) {
                        results[item_id] = {
                            ["remarks"]: remarks,
                            ["marks"]: {}
                        };
                    }
                    results[item_id]["marks"][item] = score;
                });
            }
            let label = {
                results,
                record_type,
                record_id
            };
            if(record_type === "student") {
                label["record_id"] = `${additional_id}_${record_id}`;
            }
            $.post(`${baseUrl}api/terminal_reports/update_report`, { label }).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code == 200) {
                    if (record_type === "student") {
                        $(`span[data-input_save_button="${record_id}"]`).addClass("hidden");
                    } else {
                        if (response.data.additional.href !== undefined) {
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
