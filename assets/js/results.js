$(`input[data-input_type_q="marks"]`).on("input", function() {
    let student_id = $(this).attr("data-input_row_id"),
        score = 0,
        total = 100;
    $.each($(`input[data-input_type_q="marks"][data-input_row_id="${student_id}"]`), function() {
        let mark = parseInt($(this).val());
        score += mark;
    });
    if (score > total) {
        $(`input[data-input_type_q="marks"][data-input_row_id="${student_id}"]`).addClass("border-red");
    } else {
        $(`input[data-input_type_q="marks"][data-input_row_id="${student_id}"]`).removeClass("border-red");
    }
    $(`input[data-input_total_id="${student_id}"]`).val(score);
    $(`span[data-input_save_button="${student_id}"]`).removeClass("hidden");
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

var save_result = (record_id, record_type) => {
    swal({
        title: "Save Results",
        text: "Do you want to save this as the results?",
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
                            ["scores"]: {}
                        };
                    }
                    student_ids.push(record_id);
                    results[record_id]["scores"][i] = {
                        [item]: score
                    }
                });
            } else if (record_type === "results") {
                $.each($(`input[data-input_type_q="marks"]`), function(i, e) {
                    let item_id = $(this).attr(`data-input_row_id`),
                        item = $(this).attr("data-input_name"),
                        score = parseInt($(this).val()),
                        remarks = $(`input[data-input_method="remarks"][data-input_row_id="${item_id}"]`).val();
                    if (results[item_id] === undefined) {
                        results[item_id] = {
                            ["remarks"]: remarks,
                            ["scores"]: {}
                        };
                    }
                    student_ids.push(record_id);
                    results[item_id]["scores"][i] = {
                        [item]: score
                    }
                });
            }
            let label = {
                results: results,
                record: record_type
            };
            $.post(`${baseUrl}api/terminal_reports/update_report`, { label }).then((response) => {
                if (response.code == 200) {

                }
                $.pageoverlay.hide();
            }).catch(() => {
                $.pageoverlay.hide();
            })
        }
    });
}