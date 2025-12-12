var load_class_list = (class_id = 0, student_id = 0) => {
    let theClassSector = $(`select[name="remarks_class_id"]`);
    let theClassFilter = $(`select[name="filter_remarks_class_id"]`);
    if(!theClassFilter || !theClassSector) {
        return;
    }
    $.get(`${baseUrl}api/classes/list?columns=a.id,a.item_id,a.name`).then((response) => {
        if (response.code == 200) {
            theClassSector.find('option').remove().end();
            theClassFilter.find('option').remove().end();
            if(response.data.result.length > 1) {
                theClassSector.append(`<option value="0" selected="selected">Select Class</option>`);
                theClassFilter.append(`<option value="0" selected="selected">Select Class to Filter</option>`);
            }
            $.each(response.data.result, (_, e) => {
                let isSelected = class_id == parseInt(e.id) ? "selected" : "";
                theClassSector.append(`<option data-item_id="${e.item_id}" value='${e.id}' ${isSelected}>${e.name.toUpperCase()}</option>'`);
                theClassFilter.append(`<option data-item_id="${e.item_id}" value='${e.id}'>${e.name.toUpperCase()}</option>'`);
            });
            if(response.data.result.length == 1) {
                theClassSector.trigger("change");
                theClassFilter.trigger("change");
            }
        }
    });

    if(class_id > 0) {
        theClassSector.val(class_id).trigger("change");
    }

    $(`select[name="remarks_class_id"]`).on("change", function() {
        let value = $(this).val();
        if(value == "0") {
            return;
        }
        $(`select[name="remarks_student_id"]`).find('option').remove().end();
        $(`select[name="remarks_student_id"]`).append(`<option value="0" selected="selected">Select Student</option>`);
        $.get(`${baseUrl}api/users/minimal?class_id=${value}&user_type=student`).then((response) => {
            if (response.code == 200) {
                $.each(response.data.result.users, (_, e) => {
                    let isSelected = student_id == parseInt(e.user_id) ? "selected" : "";
                    $(`select[name="remarks_student_id"]`).append(`<option value='${e.user_id}' ${isSelected}>
                        ${e.name.toUpperCase()} (${e.unique_id})
                    </option>'`);
                });
            }
        });
    });

    if(student_id !== 0) {
        setTimeout(() => {
            $(`select[name="remarks_student_id"]`).val(student_id).trigger("change");
        }, 100);
    }

    $(`select[id='filter_remarks_class_id']`).on("change", function() {
        let class_id = $(this).val();
        if(class_id == "0") {
            loadPage(`${baseUrl}results-remarks`);
            return;
        }
        loadPage(`${baseUrl}results-remarks?class_id=${$(this).val()}`);
    });
}

var add_student_remarks = () => {
    $(`#studentRemarksModal`).modal('show');
}

var delete_student_remarks = (remarks_id) => {
    swal({
        title: "Delete Student Remarks",
        text: "Are you sure you want to delete this student remarks?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/terminal_reports/delete_student_remarks`, { remarks_id }).then((response) => {
                if (response.code == 200) {
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code),
                    });
                    $(`div[data-remarks_id="${remarks_id}"]`).remove();
                }
            });
        }
    });
}

var edit_student_remarks = (remarks_id, class_id, student_id) => {
    add_student_remarks(class_id, student_id);
    setTimeout(() => {
        load_class_list(class_id, student_id);
    }, 100);
}

var search_remarks = () => {
    let search_value = $(`#search_remarks`).val();
    let totalVisible = 0;

    $(`#no_remarks_found`).remove();
    if(search_value == "") {
        $(`div[class~="remarks_item"]`).show();
        totalVisible = $(`div[class~="remarks_item"]`).length;
    }
    else {
        $(`div[class~="remarks_item"]`).each(function() {
            let student_name = $(this).attr("data-student_name");
            if(student_name.toLowerCase().includes(search_value.toLowerCase())) {
                $(this).show();
            }
            else {
                $(this).hide();
            }
        });
        totalVisible = $(`div[class~="remarks_item"]:visible`).length;
    }
    if(totalVisible == 0) {
        $(`div[data-input_item="search"]`)
            .append(`<div id='no_remarks_found' class='alert mt-2 alert-info'>No remarks found matching your search</div>`);
    }
}

var save_legend_values = () => {
    let legend = {};
    $.each($(`div[data-legend_item]`), function(key, value) {
        let theRow = $(this).data();
        let ikey = $(`input[name="legend_key[${theRow.legend_item}]"]`).val().trim().toUpperCase();
        let ivalue = $(`input[name="legend_value[${theRow.legend_item}]"]`).val().trim();
        if(ikey.length && ivalue.length) {
            legend[theRow.legend_item] = {
                key: ikey, value: ivalue,
            }
        }
    });
    $.post(`${baseUrl}api/settings/savesettings`, { legend, setting_name: "preschool_reporting_legend" });
}

var delete_legend_item = (legend_item) => {
    $(`div[data-legend_item="${legend_item}"]`).remove();
    save_legend_values();
}

var debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Create a debounced version of save_legend_values
var debouncedGetLegendValues = debounce(save_legend_values, 1000);

var track_legend_value_entry = () => {
    // Use event delegation to handle dynamically added inputs
    $(`div[id="preschool_reporting_legend"]`).off("input", `input[data-item="legend_value"]`).on("input", `input[data-item="legend_value"]`, function() {
        debouncedGetLegendValues();
    });
    $(`div[id="preschool_reporting_legend"]`).off("input", `input[data-item="legend_key"]`).on("input", `input[data-item="legend_key"]`, function() {
        debouncedGetLegendValues();
    });
}

// Initialize the event listener on page load
track_legend_value_entry();

$(`input[name="legend_key[1]"]`).focus();

var add_preschool_reporting = () => {
    let last_item = $(`div[id="preschool_reporting_legend"] div[data-legend_item]:last`).attr("data-legend_item");
    let new_item = last_item == "0" ? 1 : (parseInt(last_item) + 1);
    if(new_item > 10) {
        notify("The report legend should be a maximum of 10 items.");
        return;
    }
    // only proceed to add the next one if the key and interpretation for previous is set
    let prevKey = $(`input[name="legend_key[${last_item}]"]`).val();
    let prevValue = $(`input[name="legend_value[${last_item}]"]`).val();

    save_legend_values();

    if(!prevKey.length) {
        notify(`You have not entered the key item for item ${last_item}. Please ensure that is set`);
        $(`input[name="legend_key[${last_item}]"]`).focus();
        return;
    }

    if(!prevValue.length) {
        notify(`You have not entered the interpretation value for item ${last_item}. Please ensure that is set`);
        $(`input[name="legend_value[${last_item}]"]`).focus();
        return;
    }

    let legend_html = `
    <div class="d-flex gap-2 justify-content-between mb-2" data-legend_item="${new_item}">
        <div class="w-[5%] font-20 text-danger">
            ${new_item}
        </div>
        <div class="w-[30%]">
            <div>
                <input maxlength="5" data-item="legend_key" type="text" name="legend_key[${new_item}]" class="form-control text-uppercase" placeholder="e.g. A">
            </div>
        </div>
        <div class="w-[56%]">
            <div>
                <input maxlength="32" data-item="legend_value" type="text" name="legend_value[${new_item}]" class="form-control" placeholder="e.g. Excellent">
            </div>
        </div>
        <div class="w-[8%]">
            <div>
                <button type="button" class="btn btn-outline-danger" onclick="return delete_legend_item(${new_item});"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    </div>`;
    $(`div[id="preschool_reporting_legend"]`).append(legend_html);

    $(`input[name="legend_key[${new_item}]"]`).focus();
    // No need to call track_legend_value_entry() here anymore since we're using event delegation
}

load_class_list();