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

// Focus on first legend input if it exists
setTimeout(() => {
    let firstLegendInput = $(`input[name="legend_key[1]"]`);
    if(firstLegendInput.length) {
        firstLegendInput.focus();
    }
}, 100);

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

// ==================== REPORTING TEMPLATE FUNCTIONS ====================

var save_reporting_content = () => {
    let sections = [];
    let sectionCounter = 1;
    
    $.each($(`div[data-section_id]`), function() {
        let $section = $(this);
        let sectionId = parseInt($section.attr("data-section_id")) || Date.now();
        let sectionTitleInput = $section.find(`input.section-title-input`);
        let sectionTitle = sectionTitleInput.length ? (sectionTitleInput.val() || '').trim() : '';
        
        if(!sectionTitle.length) {
            return; // Skip sections without titles
        }
        
        let questionnaires = [];
        $section.find(`div.questionnaires-list input.questionnaire-input`).each(function() {
            let qText = ($(this).val() || '').trim();
            if(qText.length) {
                let qId = parseInt($(this).attr("data-questionnaire_id")) || (sectionId * 1000 + Date.now());
                questionnaires.push({
                    id: qId,
                    text: qText
                });
            }
        });
        
        sections.push({
            id: sectionId,
            title: sectionTitle,
            questionnaires: questionnaires
        });
        sectionCounter++;
    });
    
    $.post(`${baseUrl}api/settings/savesettings`, { 
        sections, 
        setting_name: "preschool_reporting_content" 
    }).then((response) => {
        if(response.code == 200) {
            // Optional: Show success notification
            // notify("Reporting template saved successfully");
        }
    }).catch((error) => {
        console.error("Error saving reporting content:", error);
    });
}

// Create a debounced version of save_reporting_content
var debouncedSaveReportingContent = debounce(save_reporting_content, 1000);

var add_reporting_section = () => {
    let existingSections = $(`div[data-section_id]`);
    let maxSectionId = 0;
    
    existingSections.each(function() {
        let sectionId = parseInt($(this).attr("data-section_id")) || 0;
        if(sectionId > maxSectionId) {
            maxSectionId = sectionId;
        }
    });
    
    // Generate new ID: use max + 1, or timestamp if no sections exist
    let newSectionId = maxSectionId > 0 ? maxSectionId + 1 : Date.now();
    
    let section_html = `
    <div class="mb-3 border rounded p-3" data-section_id="${newSectionId}">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 font-weight-bold text-primary text-uppercase">New Section</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="return add_questionnaire(${newSectionId});" title="Add Questionnaire">
                    <i class="fas fa-plus"></i> Add Row
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="return delete_reporting_section(${newSectionId});" title="Delete Section">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="mb-2">
            <input type="text" class="form-control text-uppercase section-title-input" data-section_id="${newSectionId}" value="" placeholder="Enter section title (e.g. Communication Skills)" maxlength="100">
        </div>
        <div class="questionnaires-list" data-section_id="${newSectionId}">
        </div>
    </div>`;
    
    // Remove the "no sections" message if it exists
    $(`#preschool_reporting_content .text-muted.text-center`).remove();
    
    $(`#preschool_reporting_content`).append(section_html);
    
    // Focus on the section title input
    $(`input.section-title-input[data-section_id="${newSectionId}"]`).focus();
    
    // Initialize event listeners for this section
    track_reporting_content_changes();
}

var delete_reporting_section = (section_id) => {
    swal({
        title: "Delete Section",
        text: "Are you sure you want to delete this section and all its questionnaires?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[data-section_id="${section_id}"]`).remove();
            
            // Show "no sections" message if no sections remain
            if($(`div[data-section_id]`).length === 0) {
                $(`#preschool_reporting_content`).html('<div class="text-muted text-center py-3">No sections added yet. Click "Add New Section" to get started.</div>');
            }
            
            debouncedSaveReportingContent();
        }
    });
}

var add_questionnaire = (section_id) => {
    let questionnairesList = $(`div.questionnaires-list[data-section_id="${section_id}"]`);
    let existingQuestionnaires = questionnairesList.find(`input.questionnaire-input`);
    let maxQId = 0;
    
    existingQuestionnaires.each(function() {
        let qId = parseInt($(this).attr("data-questionnaire_id")) || 0;
        if(qId > maxQId) {
            maxQId = qId;
        }
    });
    
    // Generate new ID: use max + 1, or timestamp-based if no questionnaires exist
    let newQId = maxQId > 0 ? maxQId + 1 : (parseInt(section_id) * 1000 + Date.now());
    
    let questionnaire_html = `
    <div class="d-flex align-items-center mb-2" data-questionnaire_id="${newQId}">
        <div class="flex-grow-1 mr-2">
            <input type="text" class="form-control questionnaire-input" data-section_id="${section_id}" data-questionnaire_id="${newQId}" value="" placeholder="Enter questionnaire item" maxlength="200">
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="return delete_questionnaire(${section_id}, ${newQId});" title="Delete">
            <i class="fas fa-times"></i>
        </button>
    </div>`;
    
    questionnairesList.append(questionnaire_html);
    
    // Focus on the new questionnaire input
    $(`input.questionnaire-input[data-section_id="${section_id}"][data-questionnaire_id="${newQId}"]`).focus();
    
    // Initialize event listeners
    track_reporting_content_changes();
}

var delete_questionnaire = (section_id, questionnaire_id) => {
    $(`div[data-section_id="${section_id}"] div[data-questionnaire_id="${questionnaire_id}"]`).remove();
    debouncedSaveReportingContent();
}

var track_reporting_content_changes = () => {
    // Use event delegation to handle dynamically added inputs
    $(`#preschool_reporting_content`).off("input", `input.section-title-input`).on("input", `input.section-title-input`, function() {
        let sectionId = $(this).attr("data-section_id");
        let sectionTitle = $(this).val().trim();
        // Update the displayed section title
        $(this).closest(`div[data-section_id="${sectionId}"]`).find(`h6`).text(sectionTitle || "New Section");
        debouncedSaveReportingContent();
    });
    
    $(`#preschool_reporting_content`).off("input", `input.questionnaire-input`).on("input", `input.questionnaire-input`, function() {
        debouncedSaveReportingContent();
    });
}

var save_reporting_classes = () => {
    let classes = $(`select[name="reporting_classes[]"]`).val();
    $.post(`${baseUrl}api/settings/savesettings`, { classes, setting_name: "preschool_reporting_classes" });
}

// ==================== PRESCHOOL RESULTS UPLOAD FUNCTIONS ====================

var preschoolStudentsList = [];
var currentStudentIndex = -1;
var currentClassId = null;
var reportingTemplate = null;
var reportingLegend = null;

var load_preschool_students = (class_id) => {
    if(!class_id || class_id === '') {
        $(`#preschool_student_id`).html('<option value="">Select Class First</option>');
        return;
    }
    
    currentClassId = class_id;
    
    $.get(`${baseUrl}api/users/minimal?class_id=${class_id}&user_type=student`).then((response) => {
        if (response.code == 200) {
            preschoolStudentsList = response.data.result.users || [];
            let options = '<option value="">Select Student</option>';
            $.each(preschoolStudentsList, (index, student) => {
                options += `<option value="${student.user_id}">${student.name.toUpperCase()} (${student.unique_id})</option>`;
            });
            $(`#preschool_student_id`).html(options);
            // $(`#preschool_student_id`).selectpicker('refresh');
        }
    });
}

var load_student_reporting = () => {
    let classId = $(`#preschool_class_id`).val();
    let studentId = $(`#preschool_student_id`).val();
    
    if(!classId) {
        notify("Please select a class first.");
        return;
    }
    
    if(!studentId) {
        notify("Please select a student.");
        return;
    }
    
    // Find student index
    currentStudentIndex = preschoolStudentsList.findIndex(s => s.user_id == studentId);
    if(currentStudentIndex === -1) {
        notify("Student not found in list.");
        return;
    }
    
    // Load reporting template and legend
    load_reporting_template_and_legend().then(() => {
        // Load student's existing results
        load_student_results(studentId, classId);
    });
}

var load_reporting_template_and_legend = () => {
    return new Promise((resolve) => {
        // Load template and legend from the setup page's stored data or via API
        // Try to get from a global variable first (if available), otherwise use API
        if(typeof window.preschoolReportingTemplate !== 'undefined' && typeof window.preschoolReportingLegend !== 'undefined') {
            reportingTemplate = window.preschoolReportingTemplate;
            reportingLegend = window.preschoolReportingLegend;
            validate_template_and_legend(resolve);
            return;
        }
        
        // Load via API - using POST with proper format
        $.post(`${baseUrl}api/settings/getsettings`, {
            clientId: typeof clientId !== 'undefined' ? clientId : '',
            setting_name: ["preschool_reporting_content", "preschool_reporting_legend"]
        }).then((response) => {
            if(response.code == 200) {
                // If multiple settings, data is an object with setting names as keys
                reportingTemplate = response.data.result.preschool_reporting_content || null;
                reportingLegend = response.data.result.preschool_reporting_legend || null;
                validate_template_and_legend(resolve);
            } else {
                notify("Failed to load reporting template.");
                resolve();
            }
        }).catch(() => {
            // If API fails, try loading from localStorage as fallback
            try {
                let storedTemplate = localStorage.getItem('preschool_reporting_template');
                let storedLegend = localStorage.getItem('preschool_reporting_legend');
                if(storedTemplate) reportingTemplate = JSON.parse(storedTemplate);
                if(storedLegend) reportingLegend = JSON.parse(storedLegend);
                validate_template_and_legend(resolve);
            } catch(e) {
                notify("Failed to load reporting template.");
                resolve();
            }
        });
    });
}

var validate_template_and_legend = (resolve) => {
    if(!reportingTemplate || !reportingTemplate.sections || reportingTemplate.sections.length === 0) {
        notify("Reporting template has not been configured. Please set it up first.");
        resolve();
        return;
    }
    
    if(!reportingLegend || !reportingLegend.legend || Object.keys(reportingLegend.legend).length === 0) {
        notify("Reporting legend has not been configured. Please set it up first.");
        resolve();
        return;
    }
    
    resolve();
}

var load_student_results = (studentId, classId) => {
    let params = {
        student_id: studentId,
        class_id: classId
    };
    
    $.post(`${baseUrl}api/terminal_reports/get_preschool_results`, params).then((response) => {
        let studentResults = {};
        if(response.code == 200 && response.data && response.data.result) {
            studentResults = response.data.result;
        }
        
        display_student_reporting(studentResults);
        update_student_navigation();
    }).catch(() => {
        // If endpoint doesn't exist yet, just display empty form
        display_student_reporting({});
        update_student_navigation();
    });
}

var display_student_reporting = (studentResults) => {
    if(!reportingTemplate || !reportingLegend) {
        return;
    }
    
    let currentStudent = preschoolStudentsList[currentStudentIndex];
    $(`#student_name_display`).text(`${currentStudent.name.toUpperCase()} (${currentStudent.unique_id})`);
    $(`#student_reporting_container`).show();
    
    let html = '';
    
    $.each(reportingTemplate.sections, (sectionIndex, section) => {
        let sectionId = section.id;
        let sectionTitle = section.title || 'Untitled Section';
        let questionnaires = section.questionnaires || [];
        
        html += `
        <div class="mb-4 border rounded p-3" data-section_id="${sectionId}">
            <h6 class="mb-3 font-weight-bold text-uppercase text-primary">${sectionTitle}</h6>
            <div class="questionnaires-section">`;
        
        if(questionnaires.length === 0) {
            html += '<div class="text-muted">No questionnaires added for this section.</div>';
        } else {
            $.each(questionnaires, (qIndex, questionnaire) => {
                let qId = questionnaire.id;
                let qText = questionnaire.text || '';
                let resultKey = `${sectionId}_${qId}`;
                let selectedValue = studentResults[resultKey] || '';
                
                html += `
                <div class="d-flex align-items-center mb-2 border-bottom" data-questionnaire_id="${qId}">
                    <div class="flex-grow-1 mr-3">
                        <div class="font-weight-medium">${qText}</div>
                    </div>
                    <div class="legend-options">
                        <div class="btn-group gap-1" role="group">`;
                
                // Display legend options
                $.each(reportingLegend.legend, (legendKey, legendItem) => {
                    let legendValue = legendItem.key || '';
                    let isChecked = selectedValue === legendValue ? 'checked' : '';
                    html += `
                            <input type="radio" class="btn-check" name="result_${resultKey}" id="result_${resultKey}_${legendValue}" value="${legendValue}" ${isChecked} onchange="return save_student_result('${resultKey}', '${legendValue}');">
                            <label class="btn btn-outline-primary" for="result_${resultKey}_${legendValue}">${legendValue}</label>`;
                });
                
                html += `
                        </div>
                    </div>
                </div>`;
            });
        }
        
        html += `
            </div>
        </div>`;
    });
    
    $(`#student_reporting_content`).html(html);
}

var save_student_result = (resultKey, value) => {
    let studentId = $(`#preschool_student_id`).val();
    let classId = $(`#preschool_class_id`).val();
    
    if(!studentId || !classId) {
        return;
    }
    
    let data = {
        student_id: studentId,
        class_id: classId,
        result_key: resultKey,
        result_value: value
    };
    
    $.post(`${baseUrl}api/terminal_reports/save_preschool_result`, data).then((response) => {
        if(response.code == 200) {
            // Success - could show a subtle notification
        } else {
            console.error("Error saving result:", response.data);
        }
    }).catch((error) => {
        console.error("Error saving result:", error);
    });
}

var navigate_student = (direction) => {
    if(preschoolStudentsList.length === 0) {
        return;
    }
    
    let newIndex = currentStudentIndex + direction;
    
    if(newIndex < 0) {
        notify("This is the first student.");
        return;
    }
    
    if(newIndex >= preschoolStudentsList.length) {
        notify("This is the last student.");
        return;
    }
    
    currentStudentIndex = newIndex;
    let student = preschoolStudentsList[currentStudentIndex];
    
    // Update dropdown
    $(`#preschool_student_id`).val(student.user_id);
    // $(`#preschool_student_id`).selectpicker('refresh');
    
    // Load student data
    load_student_results(student.user_id, currentClassId);
    update_student_navigation();
}

var update_student_navigation = () => {
    let total = preschoolStudentsList.length;
    let current = currentStudentIndex + 1;
    
    $(`#student_counter`).text(current);
    $(`#total_students`).text(total);
    
    // Disable/enable navigation buttons
    $(`#prev_student_btn`).prop('disabled', currentStudentIndex === 0);
    $(`#next_student_btn`).prop('disabled', currentStudentIndex >= total - 1);
}

// Initialize class change handler
$(document).ready(function() {
    $(`#preschool_class_id`).on('change', function() {
        let classId = $(this).val();
        load_preschool_students(classId);
        $(`#student_reporting_container`).hide();
    });
    
    $(`#preschool_student_id`).on('change', function() {
        $(`#student_reporting_container`).hide();
    });
});

// Initialize the event listener on page load
track_reporting_content_changes();