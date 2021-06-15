$(`input[id="student_csv_file"]`).change(function() {
    var fd = new FormData();
    var files = $('input[id="student_csv_file"]')[0].files[0];
    fd.append('csv_file', files);
    fd.append('column', "student");
    load_csv_file_data(fd, "student");
});
$(`input[id="staff_csv_file"]`).change(function() {
    var fd = new FormData();
    var files = $('input[id="staff_csv_file"]')[0].files[0];
    fd.append('csv_file', files);
    fd.append('column', "staff");
    load_csv_file_data(fd, "staff");
});
$(`input[id="course_csv_file"]`).change(function() {
    var fd = new FormData();
    var files = $('input[id="course_csv_file"]')[0].files[0];
    fd.append('csv_file', files);
    fd.append('column', "course");
    load_csv_file_data(fd, "course");
});
$(`input[id="parent_csv_file"]`).change(function() {
    var fd = new FormData();
    var files = $('input[id="parent_csv_file"]')[0].files[0];
    fd.append('csv_file', files);
    fd.append('column', "parent");
    load_csv_file_data(fd, "parent");
});

var select_change_handler = (column) => {
    $(`div[data-csv_import_column="${column}"] div[class~="csv-rows-content"] select`).on('change', function(i, e) {

        var thisKey = $(this);
        thisValue = thisKey.val(),
            thisId = thisKey.data('id');

        if (($.inArray(thisValue, foundArrayList) === -1) && ($.inArray(thisValue, acceptedArray[column]) !== -1)) {

            $(`div[data-row="${thisId}"] div[class="text-center"]`)
                .html(`<h3 class="text-success"><i class="fa fa-check-circle"></i> Valid Column</h3>`);
            $(`div[data-row="${thisId}"] select`).attr('data-name', thisValue);

            foundArrayList.push(thisValue);

        } else if (($.inArray(thisValue, foundArrayList) !== -1)) {
            var otherKey = $(`select[data-name="${thisValue}"]`),
                otherId = otherKey.data('id');

            $(`div[data-row="${thisId}"] select`).attr('data-name', thisValue);

            $(`div[data-row="${thisId}"] div[class="text-center"]`)
                .html(`<h3 class="text-success"><i class="fa fa-check-circle"></i> Valid Column</h3>`);
            $(`div[data-row="${otherId}"] div[class="text-center"]`)
                .html(`<h3 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Unmatched Column</h3>`);

            otherKey.val('null').change();

        } else if (($.inArray(thisValue, foundArrayList) === -1) && ($.inArray(thisValue, acceptedArray[column]) === -1)) {
            $(`div[data-row="${thisId}"] select`).attr('data-name', 'null');
            $(`div[data-row="${thisId}"] div[class="text-center"]`)
                .html(`<h3 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Unmatched Column</h3>`);
        }

        file_checker_text_handler(column);
    });
}

var change_select_input_field = (id, value, column) => {

    if ($.inArray(value, acceptedArray[column]) !== -1) {
        $(`div[data-row="${id}"] div[class="text-center"]`)
            .html(`<h3 class="text-success"><i class="fa fa-check-circle"></i> Valid Column</h3>`);
        $(`select[name="first_col_${id}"]`).val(value).change();

        foundArrayList.push(value);
    } else {
        $(`div[data-row="${id}"] div[class="text-center"]`)
            .html(`<h3 class="text-danger"><i class="fa fa-exclamation-triangle"></i> Unmatched Column</h3>`);
    }

}

var file_checker_text_handler = (column) => {

    var selectValues = new Array(),
        dataCount = 0;
    $.each($(`div[data-csv_import_column="${column}"] div[class~="csv-rows-content"] select`), function(i, e) {
        selectValues.push($(this).val());
    });

    $.each(selectValues, function(i, e) {
        if ($.inArray(e, acceptedArray[column]) === -1) {
            dataCount++;
        }
    });

    if (dataCount != 0) {
        $(`div[data-csv_import_column="${column}"] div[class~="file-checker"]`).css('display', 'block');
        if (dataCount > 1) {
            $(`div[data-csv_import_column="${column}"] div[class~="file-checker"]`).html(`<h2>There are <span class="text-danger">${dataCount} columns</span> that are not matched in the uploaded file.</h2>`);
        } else {
            $(`div[data-csv_import_column="${column}"] div[class~="file-checker"]`).html(`<h2>There is <span class="text-danger">${dataCount} column</span> not matched in the uploaded file.</h2>`);
        }
        $(`div[data-csv_import_column="${column}"] button[class~="upload-button"]`).css("display", "none").addClass("hidden");
        $(`div[data-csv_import_column="${column}"] button[class~="cancel-button"]`).css("display", "none").addClass("hidden");
    } else {
        $(`div[data-csv_import_column="${column}"] button[class~="upload-button"]`).css("display", "inline-block").removeClass("hidden");
        $(`div[data-csv_import_column="${column}"] button[class~="cancel-button"]`).css("display", "inline-block").removeClass("hidden");
        $(`div[data-csv_import_column="${column}"] div[class~="file-checker"]`).html(`<h2>All matched! Ready for us to upload the ${column}s information.</h2>`);
    }

    $(`div[data-csv_import_column="${column}"] div[class~="upload-text"]`).removeClass('hidden');
    $(`div[data-csv_import_column="${column}"] button[class~="cancel-button"]`).css("display", "inline-block");
    $(`div[data-csv_import_column="${column}"] div[class="form-content-loader"]`).css("display", "none");
    $(`div[data-csv_import_column="${column}"] form[class="csvDataImportForm"]`).css("display", "none");

}

var populate_select_fields = (headerData, mainContent, column) => {

    var htmlData = '',
        selectData = $(`div[data-csv_import_column="${column}"] div[class~="csv-rows-content"] div[class="form-row"] select`).html(),
        iv = 0;

    $(`div[data-csv_import_column="${column}"] div[class~="csv-rows-content"]`).html(``);

    let h_count = 0;
    $.each(headerData, async function(i, e) {
        htmlData = `<div class="col-md-6 col-sm-12 col-lg-3" style="min-width:250px" data-row="${iv}">
            <div class="form-row">
            <div class="text-center" style="width:100%"></div>
            <select data-name="${e}" data-id="${i}" name="first_col_${iv}" id="first_col_${iv}" class="form-control selectpicker">
            ${selectData}
            </select>
            <div style="width:100%; background:#fff; border-radius:5px; padding-top:10px; margin-top: 5px" class="csv-row-data-${iv} mb-3"></div>
            </div>
            </div>`;
        $(`div[data-csv_import_column="${column}"] div[class~="csv-rows-content"]`).append(htmlData);
        iv++;
        await change_select_input_field(i, e, column);
        h_count++;
    });

    var ii = 0;
    $.each(mainContent, function(i, e) {
        $.each(e, function(iv, v) {
            $(`div[data-csv_import_column="${column}"] div[class~="csv-row-data-${iv}"]`).append(`<p style="padding-left: 5px" data-row-id="${ii}" data-column-id="${iv}" class="border-bottom pb-2">${e[iv]}</p>`);
        });
        ii++;
    });

    select_change_handler(column);
    file_checker_text_handler(column);
    $(`div[data-csv_import_column="${column}"] input[name="csv_file"]`).val(``);
    $(`div[data-csv_import_column="${column}"] select[class~="selectpicker"]`).select2({ width: "100%" });
}

var load_csv_file_data = (formdata, column) => {
    $(`div[data-csv_import_column="${column}"] div[class="form-content-loader"]`).css("display", "flex");
    $.ajax({
        type: 'POST',
        url: `${baseUrl}api/account/upload_csv`,
        data: formdata,
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        success: function(response) {
            if (response.code === 200) {
                current_column = column;
                populate_select_fields(response.data.result.column, response.data.result.csv_data, column);
                $(`div[data-csv_import_column="${column}"] div[class~="csv-rows-counter"]`).html(`A total of <strong>${response.data.result.data_count} items</strong> will be imported.`);
            }
        },
        error: function(err) {
            swal({
                text: "Sorry! An unknown file type was uploaded.",
                icon: "error",
            });
            $(`div[data-csv_import_column="${column}"] input[name="csv_file"]`).val(``);
            $(`div[data-csv_import_column="${column}"] div[class="form-content-loader"]`).css("display", "none");
        }
    });
}

var clear_csv_upload = (column) => {
    foundArrayList = new Array(),
        csvContent = new Array();
    $(`div[data-csv_import_column="${column}"] div[class~="upload-text"]`).addClass('hidden');
    $(`div[data-csv_import_column="${column}"] form[class="csvDataImportForm"]`).css("display", "block");
    $(`div[data-csv_import_column="${column}"] div[class~="file-checker"]`).html(``);
    $(`div[data-csv_import_column="${column}"] div[class~="csv-rows-content"]`).html(``);
    file_checker_text_handler(column);
    loadPage(`${baseUrl}settings`);
}

var cancel_csv_upload = (column) => {
    swal({
        title: "Cancel Data Upload",
        text: "Do you want to proceed to cancel the upload of the CSV File Data?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            clear_csv_upload(column);
        }
    });
}

var import_csv_data = (this_column) => {
    var btnClick = $(`div[data-csv_import_column="${this_column}"] div[class~="upload-buttons"] button[type="submit"]`);
    btnClick.prop({
        'disabled': true,
        'title': 'Processing content'
    }).html(`<i class="fa fa-spin fa-spinner"></i> Processing Content`);

    $(`div[data-csv_import_column="${this_column}"] div[class="form-content-loader"]`).css("display", "flex");
    $.each($(`div[data-csv_import_column="${this_column}"] div[class~="csv-rows-content"] select`), function(id, key) {
        var selId = $(this).data('id'),
            selName = $(this).attr('data-name');
        csvContent[selName] = new Array();
        $.each($(`div[class~="csv-row-data-${selId}"] p`), function(i, e) {
            var pgText = $(this).text();
            csvContent[selName].push(pgText);
        });
    });
    swal({
        title: "Import Data",
        text: "Do you want to continue data upload?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[data-csv_import_column="${this_column}"] div[class="form-content-loader"]`).css("display", "flex");
            $.ajax({
                type: "POST",
                url: `${baseUrl}api/account/import`,
                data: { csv_keys: Object.keys(csvContent), csv_values: Object.values(csvContent), column: this_column },
                dataType: "json",
                success: function(response) {
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code),
                    });
                    if (response.code === 200) {
                        clear_csv_upload(this_column);
                        $(`div[data-csv_import_column="${this_column}"] div[class~="file-checker"]`).html(`
                            <h2 class="text-success">${response.data.result}.</h2>
                        `);
                        $(`div[data-csv_import_column="${this_column}"] div[class~="upload-buttons"]`).remove();
                    }
                },
                complete: function(data) {
                    btnClick.prop({
                        'disabled': false,
                        'title': ''
                    }).html(`<i class="fa fa-upload"></i> Continue Data Import`);
                    $(`div[data-csv_import_column="${this_column}"] div[class="form-content-loader"]`).css("display", "none");
                },
                error: function(err) {
                    swal({
                        text: "Sorry! An unknown file type was uploaded.",
                        icon: "error",
                    });
                    btnClick.prop({
                        'disabled': false,
                        'title': ''
                    }).html(`<i class="fa fa-upload"></i> Continue Data Import`);
                    $(`div[data-csv_import_column="${this_column}"] div[class="form-content-loader"]`).css("display", "none");
                }
            });
        } else {
            btnClick.prop({
                'disabled': false,
                'title': ''
            }).html(`<i class="fa fa-upload"></i> Continue Data Import`);
            $(`div[data-csv_import_column="${this_column}"] div[class="form-content-loader"]`).css("display", "none");
        }
    });
}

var download_sample_csv = (column) => {
    $(`button[data-download_button="${column}"]`).prop("disabled", true).html(`Processing... <i class="fa fa-spin fa-spinner"></i>`);
    $.get(`${baseUrl}api/account/download_temp`, { file: column }).then((response) => {
        if (response.code === 200) {
            $.each(response.data.result, function(i, e) {
                window.location.href = `${baseUrl}${e}`;
            });
        }
        $(`button[data-download_button="${column}"]`).prop("disabled", false).html(`<i class="fa fa-download"></i> Download Sample CSV File`);
    }).catch(() => {
        $(`button[data-download_button="${column}"]`).prop("disabled", false).html(`<i class="fa fa-download"></i> Download Sample CSV File`);
    });
}