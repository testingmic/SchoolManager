var currentModifiedRowId = 0,
    currentModifiedRowData = {};
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

var defaultCeiling = 60;
var save_raw_score = (row_id, input_name) => {
    let totalScore = 0;
    let index = 0;
    let scoreCeiling = parseInt($(`div[id="viewOnlyModal"] input[name="score_ceiling"]`).val());
    scoreCeiling = isNaN(scoreCeiling) ? defaultCeiling : scoreCeiling;

    currentModifiedRowData[row_id][input_name] = {};
    $(`div[id="viewOnlyModal"] input[type="number"][data-input_row_id]`).each(function() {
        let input = $(this);
        totalScore += parseInt(input.val());
        currentModifiedRowData[row_id][input_name][index] = parseInt(input.val());
        index++;
    });

    currentModifiedRowData[row_id][input_name]['score_ceiling'] = scoreCeiling;

    let sba_percentage = window.sba_percentage_lower[input_name] ?? 0;
    let sba_score = Math.round((totalScore / scoreCeiling) * sba_percentage);

    if(totalScore > scoreCeiling) {
        swal({
            text: `SBA score: '${totalScore}' cannot be greater than score ceiling of: '${scoreCeiling}'.`,
            icon: "error",
        });
        return false;
    }

    $(`input[data-input_row_id="${row_id}"][data-input_method="${input_name}"]`).val(sba_score);
    $(`div[id="viewOnlyModal"]`).modal('hide');

    reset_sba_score(row_id, scoreCeiling);
}

var remove_row = (row_id) => {
    $(`div[id="viewOnlyModal"] div[class~="modal-body"] div[data-input_row_id="${row_id}"]`).remove();
}

var add_new_row = () => {

    let lastRowId = $(`div[id="viewOnlyModal"] div[class~="modal-body"] div[data-input_row_id]`).length - 1;
    let newRowId = lastRowId + 1;
    $(`div[id="viewOnlyModal"] div[class~="modal-body"]`).append(`
        <div class="mb-2">
            <div class="d-flex justify-content-between" data-input_row_id="${newRowId}">
                <div>
                    <input type="number" class="form-control" name="score_${newRowId}" data-input_row_id="${newRowId}" data-input_type="marks">
                </div>
                <div>
                    <button class="btn btn-outline-danger" onclick="return remove_row(${newRowId})">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `);
    $(`input[name="score_${newRowId}"]`).focus();
}

var preload_raw_score = (row_id, input_name) => {
    const dataSet = currentModifiedRowData[row_id][input_name];
    
    // Get score_ceiling or use default
    const scoreCeiling = dataSet.score_ceiling || defaultCeiling;
    
    // Get all score entries (numeric keys only, excluding score_ceiling)
    const scoreEntries = Object.keys(dataSet)
        .filter(key => key !== 'score_ceiling' && !isNaN(key))
        .map(key => ({ index: parseInt(key), value: dataSet[key] }))
        .sort((a, b) => a.index - b.index);
    
    // Build score rows HTML
    let scoreRowsHtml = '';
    
    if (scoreEntries.length === 0) {
        // If no entries exist, create one empty row
        scoreRowsHtml = `
            <div class="mb-2">
                <div class="d-flex justify-content-between" data-input_row_id="0">
                    <div>
                        <input type="number" class="form-control" id="score_0" name="score_0" data-input_row_id="0" data-input_type="marks">
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" onclick="return add_new_row()">Add Row</button>
                    </div>
                </div>
            </div>
        `;
    } else {
        // Build rows from existing data
        scoreEntries.forEach((entry, idx) => {
            const rowId = entry.index;
            const scoreValue = entry.value || '';
            
            if (idx === 0) {
                // First row has "Add Row" button
                scoreRowsHtml += `
                    <div class="mb-2">
                        <div class="d-flex justify-content-between" data-input_row_id="${rowId}">
                            <div>
                                <input type="number" class="form-control" id="score_${rowId}" name="score_${rowId}" data-input_row_id="${rowId}" data-input_type="marks" value="${scoreValue}">
                            </div>
                            <div>
                                <button class="btn btn-outline-primary" onclick="return add_new_row()">Add Row</button>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                // Additional rows have "Remove" button
                scoreRowsHtml += `
                    <div class="mb-2">
                        <div class="d-flex justify-content-between" data-input_row_id="${rowId}">
                            <div>
                                <input type="number" class="form-control" name="score_${rowId}" data-input_row_id="${rowId}" data-input_type="marks" value="${scoreValue}">
                            </div>
                            <div>
                                <button class="btn btn-outline-danger" onclick="return remove_row(${rowId})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
    }
    
    // Show modal with preloaded data
    $(`div[id="viewOnlyModal"]`).modal('show');
    $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(`Raw Score Entry - ${input_name}`);
    $(`div[id="viewOnlyModal"] div[class~="modal-body"]`).html(`
        <div class="mb-2">
            <div class="d-flex justify-content-between">
                <div class="border-bottom pb-3 border-primary mb-2">
                    <label for="score_ceiling">Score Ceiling</label>
                    <input type="number" class="form-control" value="${scoreCeiling}" id="score_ceiling" name="score_ceiling">
                </div>
            </div>
        </div>
        ${scoreRowsHtml}
    `);
    
    $(`div[id="viewOnlyModal"] div[class~="modal-footer"]`).html(`
        <div class="mb-2 d-flex justify-content-between w-100">
            <div>
                <button class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
            <div>
                <button class="btn btn-outline-success" onclick="return save_raw_score(${row_id}, '${input_name}')">Save Score</button>
            </div>
        </div>
    `);
}

var raw_score_entry = (row_id, input_name) => {

    if(typeof currentModifiedRowData[row_id] === 'undefined') {
        currentModifiedRowData[row_id] = {};
    }

    if(typeof currentModifiedRowData[row_id][input_name] !== 'undefined') {
        return preload_raw_score(row_id, input_name);
    }

    if(typeof currentModifiedRowData[row_id][input_name] === 'undefined') {
        currentModifiedRowData[row_id][input_name] = {};
    }

    $(`div[id="viewOnlyModal"]`).modal('show');
    $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(`Raw Score Entry - ${input_name}`);
    $(`div[id="viewOnlyModal"] div[class~="modal-body"]`).html(`
        <div class="mb-2">
            <div class="d-flex justify-content-between">
                <div class="border-bottom pb-3 border-primary mb-2">
                    <label for="score_ceiling">Score Ceiling</label>
                    <input type="number" class="form-control" value="${defaultCeiling}" id="score_ceiling" name="score_ceiling">
                </div>
            </div>
        </div>
        <div class="mb-2">
            <div class="d-flex justify-content-between" data-input_row_id="0">
                <div>
                    <input type="number" class="form-control" id="score_0" name="score_0" data-input_row_id="0" data-input_type="marks">
                </div>
                <div>
                    <button class="btn btn-outline-primary" onclick="return add_new_row()">Add Row</button>
                </div>
            </div>
        </div>
    `);

    $(`div[id="viewOnlyModal"] div[class~="modal-footer"]`).html(`
        <div class="mb-2 d-flex justify-content-between w-100">
            <div>
                <button class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
            <div>
                <button class="btn btn-outline-success" onclick="return save_raw_score(${row_id}, '${input_name}')">Save Score</button>
            </div>
        </div>
    `);
}