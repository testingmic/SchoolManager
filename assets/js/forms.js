var idbDefault, formItem = "application";

var save_application_form = async () => {

    swal({
        title: "Save Application Form",
        text: "Do you want to proceed to save this application form?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {

            let myForm = document.getElementById('jsform-wrapper'),
                theFormData = new FormData(myForm),
                formAction = $(`form[id="jsform-wrapper"]`).attr('action'),
                formLoader = $(`form[id="jsform-wrapper"] div[class="form-content-loader"]`),
                theButton = $(`form[id="jsform-wrapper"] button[type="submit"]`);

            formLoader.css("display", "flex");
            theButton.prop("disabled", true);

            if ($(`trix-editor[name="description"][id="description"]`).length) {
                let content = $(`trix-editor[id="description"]`).html();
                theFormData.append("description", htmlEntities(content));
            }

            if ($(`trix-editor[name="requirements"][id="requirements"]`).length) {
                let content = $(`trix-editor[id="requirements"]`).html();
                theFormData.append("requirements", htmlEntities(content));
            }
            
            theFormData.delete("form");
            theFormData.append("form", JSON.stringify(fieldDefault));

            $.ajax({
                url: `${formAction}`,
                type: `POST`,
                data: theFormData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code)
                    });
                    if (response.code == 200) {
                        if (response.data.additional) {
                            if (response.data.additional.clear) {
                                clearFields();
                            }
                            if (response.data.additional.reload) {
                                setTimeout(() => {
                                    loadPage(response.data.additional.reload);
                                }, refresh_seconds);
                            }
                        }
                    }
                },
                complete: function(data) {
                    formLoader.css("display", "none");
                    theButton.prop('disabled', false);
                },
                error: function(err) {
                    theButton.prop('disabled', false);
                    formLoader.css("display", "none");
                    swal({
                        type: 'error',
                        text: 'Sorry! There was an error while processing the request.'
                    });
                }
            });

        }
    });
}

$(async function() {

    var formContainer = $(`div[id="jsform-container"]`),
        preloader = $(`div[id="form-pretext"]`),
        selectContainer = $(`div[id="selectFieldModal"] div[class="modal-body"] table tbody`),
        addSelectRow = $(`div[id="selectFieldModal"] button[class~="add-row"]`),
        fieldButton = $(`div[data-function="jsform-module"] a[data-module="jsform"]`);


    $(`div[id="selectFieldModal"] button[class~="btn-outline-success"]`).on("click", function() {
        let row_id = $(`div[id="selectFieldModal"]`).attr("data-row-id"),
            select_items = fieldDefault[row_id]["select"];

        let last_item = $(`div[class='form-group'][data-row="${row_id}"] select[data-role="values"]`);

        last_item.find('option').remove().end();
        last_item.append(`<option value="null">Values Added:</option>`);
        $.each(select_items, function(key, v) {
            last_item.append('<option value=' + v.value + '>' + v.label + '</option>');
        });

        $(`div[id="selectFieldModal"]`).modal("hide");
    });

    var selectController = async() => {
        let label = $(`div[id="selectFieldModal"] tr input[data-row="label"]`),
            del_button = $(`div[id="selectFieldModal"] tr button[type="button"]`);

        await label.on("keyup", function() {
            let row_id = $(this).attr("data-form-row"),
                select_id = $(this).attr("data-select-row"),
                value = $(this).val();
            $(this).attr("value", value);
            fieldDefault[row_id]["select"][select_id]["label"] = value;
            fieldDefault[row_id]["select"][select_id]["value"] = `value_${select_id}`;
        });

        await del_button.on("click", function() {
            let row_id = $(this).attr("data-form-row"),
                select_id = $(this).attr("data-select-row");
            $(`div[id="selectFieldModal"] div[class="modal-body"] table tbody tr[data-select-row="${select_id}"]`).remove();
            delete fieldDefault[row_id]["select"][select_id];
        });
    }

    var selectHandler = () => {

        addSelectRow.on("click", async function() {
            let form_row = $(`div[id="selectFieldModal"]`).attr("data-row-id");
            let row = ``;
            row += `<tr data-select-row="${thisSelectRow}">`;
            row += `<td><input type="text" data-row="label" data-form-row="${form_row}" required name="form[select][${form_row}][label][${thisSelectRow}]" data-select-row="${thisSelectRow}" class="form-control"></td>`;
            row += `<td width="10%" class="text-center"><button type="button" data-form-row="${form_row}" data-select-row="${thisSelectRow}" class="btn remove-row btn-outline-danger btn-sm"><i class="fa fa-times"></i></button></td>`;
            row += `</tr>`;

            fieldDefault[form_row]["select"][thisSelectRow] = {
                "label": "",
                "value": ""
            };
            thisSelectRow++;

            await selectContainer.append(row);
            selectController();
        });
    }
    selectHandler();

    var emptyRows = () => {
        let item_array = Object.entries(fieldDefault);
        if (item_array.length) {
            preloader.addClass("hidden");
            $(`button[class~="preview-form"]`).removeClass("hidden");
        } else {
            $(`button[class~="preview-form"]`).addClass("hidden");
            preloader.removeClass("hidden");
        }
    }

    var formController = async() => {
        let input = $(`div[id="jsform-container"] div[class='form-group'] input[type="text"]`),
            select = $(`div[id="jsform-container"] div[class='form-group'] select[data-role="required"]`),
            _width = $(`div[id="jsform-container"] div[class='form-group'] select[data-role="_width"]`),
            del_button = $(`div[id="jsform-container"] div[class='form-group'] button[type="button"][class~="remove-row"]`),
            update_button = $(`div[id="jsform-container"] div[class='form-group'] button[type="button"][class~="update-row"]`);

        await input.on("keyup", function() {
            let row_id = $(this).attr("data-row"),
                value = $(this).val();
            $(this).attr("value", value);
            fieldDefault[row_id]["label"] = value;
        });

        await select.on("change", function() {
            let row_id = $(this).attr("data-row"),
                value = $(this).val();
            fieldDefault[row_id]["required"] = value;
        });

        await _width.on("change", function() {
            let row_id = $(this).attr("data-row"),
                value = $(this).val();
            fieldDefault[row_id]["_width"] = value;
        });

        await del_button.on("click", function() {
            let row_id = $(this).attr("data-row");
            $(`div[id="jsform-container"] div[class='form-group'][data-row="${row_id}"]`).remove();
            delete fieldDefault[row_id];
            emptyRows();
        });

        await update_button.on("click", function() {
            let row_id = $(this).attr("data-row");
            $(`div[id="selectFieldModal"]`).attr("data-row-id", row_id);

            let selected_options = ``;
            $.each(fieldDefault[row_id]["select"], function(e, t) {
                selected_options += `<tr data-select-row="${e}">`;
                selected_options += `<td><input type="text" value="${t.label}" data-row="label" data-form-row="${row_id}" required name="form[select][label][${e}]" data-select-row="${e}" class="form-control"></td>`;
                selected_options += `<td width="10%" class="text-center"><button type="button" data-form-row="${row_id}" data-select-row="${e}" class="btn remove-row btn-outline-danger btn-sm"><i class="fa fa-times"></i></button></td>`;
                selected_options += `</tr>`;
            });
            selectContainer.html(selected_options);

            $(`div[id="selectFieldModal"]`).modal("show");
            selectController();
        });

        emptyRows();
    }
    formController();

    fieldButton.on("click", async function() {

        let fieldType = $(this).attr("data-field"),
            $inputField;
        if ($.inArray(fieldType, ["input", "date", "email", "textarea"]) !== -1) {
            $inputField = `
            <div class='form-group' data-row="${thisRowId}">
                <div class="input-group">
                    <div class="input-group-prepend">
                    <div class="input-group-text">${fieldType.toUpperCase()} LABEL &nbsp;</div>
                    </div>
                    <input data-name="form[type][${thisRowId}]" hidden type="hidden" value="${fieldType}">
                    <input type="text" input-type="${fieldType}" data-row="${thisRowId}" required data-name="form[label][${thisRowId}]" id="input_label_${thisRowId}" class="form-control">
                    <div class="input-group-prepend">
                        <select class="form-control" data-role="required" data-row="${thisRowId}" style="width:130px" data-name="form[required][${thisRowId}]">
                            <option value="no">Not Required</option>
                            <option value="yes">Required</option>
                        </select>
                        <select class="form-control" data-role="_width" data-row="${thisRowId}" style="width:230px" data-name="form[_width][${thisRowId}]">
                            <option value="6">Field Width: - 50%</option>
                            <option value="3">Half Width (25%):</option>
                            <option value="6">Half Width (50%):</option>
                            <option value="9">Half Width (75%):</option>
                            <option value="12">Full Width (100%):</option>
                        </select>
                        <div class="input-group-text" style="background:none;padding:0px;border:0px; margin-left: 10px;">
                            <button type="button" data-row="${thisRowId}" class="btn remove-row btn-outline-danger btn-sm"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </div>  
            </div>`;
        } else if (fieldType == "select") {
            $inputField = `
            <div class="form-group" data-row="${thisRowId}">
                <div class="input-group">
                    <div class="input-group-prepend">
                    <div class="input-group-text">${fieldType.toUpperCase()} LABEL &nbsp;</div>
                    </div>
                    <input data-name="form[type][${thisRowId}]" hidden type="hidden" value="${fieldType}">
                    <input type="text" input-type="${fieldType}" data-row="${thisRowId}" required data-name="form[label][${thisRowId}]" id="input_label_${thisRowId}" class="form-control">
                    <div class="input-group-prepend">
                        <select class="form-control" data-role="required" data-row="${thisRowId}" style="width:130px" data-name="form[required][${thisRowId}]">
                            <option value="no">Not Required</option>
                            <option value="yes">Required</option>
                        </select>
                        <select class="form-control" data-role="values" data-row="${thisRowId}" style="width:230px" data-name="form[values][${thisRowId}]">
                            <option value="null">No values added:</option>
                        </select>
                        <div class="input-group-text" style="background:none;padding:0px;border:0px; margin-left: 10px;">
                            <button type="button" data-row="${thisRowId}" class="btn update-row btn-outline-success mr-1 btn-sm"><i class="fa fa-edit"></i></button> &nbsp; 
                            <button type="button" data-row="${thisRowId}" class="btn remove-row btn-outline-danger btn-sm"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </div>
            </div>`;
        }

        fieldDefault[thisRowId] = {
            "label": "",
            "_width": "6",
            "required": "no",
            "type": fieldType,
            "select": {}
        };

        idbDefault = {
            "html": formContainer.html(),
            "lastRow": thisRowId,
            "fieldDefault": fieldDefault
        };

        $(`button[class~="preview-form"]`).removeClass("hidden");

        thisRowId++;
        await formContainer.append($inputField);
        await formController();

    });

    $(`form[id="jsform-wrapper"] button[type="submit"]`).on("click", function(evt) {
        $(`div[id="formSubmitModal"]`).modal("show");
        evt.preventDefault();
    });

    var clearFields = () => {
        if ($(`textarea[name="policy_description"]`).length) {
            CKEDITOR.instances['policy_description'].setData("");
        }
        if ($(`trix-editor[name="policy_description"][id="policy_description"]`).length) {
            $(`trix-editor[id="policy_description"]`).html("");
        }
        $replies_loaded.attr("value", "0");
        $replies_loaded.attr("data-form", "none");
        $(`form[class="ajax-data-form"] select`).val("null").change();
        $(`form[class="ajax-data-form"] input, form[class="ajax-data-form"] textarea`).val("");
    }

    $(`button[class~="preview-form"]`).on("click", function() {
        let footnote = "",
            content = "",
            title = $(`input[id="form_title"]`).val(),
            form_preview_content = "<div class='row'>";

        $form_modal.modal("show");
        $form_body.html($form_loader);

        $form_header.html(title.toUpperCase());

        if ($(`trix-editor[name="description"][id="description"]`).length) {
            content = $(`trix-editor[id="description"]`).html();
            form_preview_content += `<div class='col-lg-12 mb-2 mt-2'>${content}</div>`;
        }

        if ($(`trix-editor[name="requirements"][id="requirements"]`).length) {
            content = $(`trix-editor[id="requirements"]`).html();
            form_preview_content += `<div class='col-lg-12 mt-2 border-bottom mb-3 border-primary'>${content}</div>`;
        }

        
        form_preview_content += `<div class='col-lg-12 mt-2'>${footnote}</div>`;

        if ($(`textarea[name="form_footnote"][id="form_footnote"]`).length) {
            let content = $(`textarea[id="form_footnote"]`).html();
            footnote = content;
        }

        $.each(fieldDefault, function(key, iv) {
            let required = (iv.required == "yes") ? "required" : "";
            form_preview_content += `<div class="col-lg-${iv._width} p-0">`;
            form_preview_content += `<div class="form-group">`;
            form_preview_content += `<label for="field[${key}]">${iv.label} ${(required) ? " &nbsp;<span class='required'>*</span>" : ""}</label>`;

            if ($.inArray(iv.type, ["input", "date", "email"]) !== -1) {
                let type = ($.inArray(iv.type, ["input", "date"]) !== -1) ? "text" : iv.type;
                let the_class = (iv.type == "date") ? "datepicker" : "";
                form_preview_content += `<input type="${type}" ${required} class="form-control ${the_class}" name="field[${key}]" id="field[${key}]">`;
            }

            if ($.inArray(iv.type, ["textarea"]) !== -1) {
                form_preview_content += `<textarea rows="5" class="form-control" name="field[${key}]" id="field[${key}]"></textarea>`;
            }

            if ($.inArray(iv.type, ["select"]) !== -1) {
                form_preview_content += `<select class="form-control selectpicker" name="field[${key}]" id="field[${key}]" data-width="100%">`;
                form_preview_content += `<option value="null">Please Select:</option>`;
                $.each(iv.select, function(sk, sv) {
                    form_preview_content += `<option value="${sv.value}">${sv.label}</option>`;
                });
                form_preview_content += "</select>";
            }

            form_preview_content += "</div>";
            form_preview_content += "</div>";
        });

        form_preview_content += `<div class='col-lg-12 mb-2 mt-2'><div class='form-group text-center font-italic mb-1'>${footnote}</div></div>`;
        form_preview_content += `<div class="col-md-12 mt-4 border-top pt-4 mb-3 text-center"><span class="btn btn-outline-secondary" data-dismiss='modal'>Close Preview</span></div>`;
        form_preview_content += `</div>`;

        $form_body.html(form_preview_content);

    });

    if($.array_stream["form_input_fields"] !== undefined) {
        fieldDefault = $.array_stream["form_input_fields"];
    }

    if($.array_stream["thisRowId"] !== undefined) {
        thisRowId = $.array_stream["thisRowId"];
    }

});