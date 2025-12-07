var removeRow = (type, rowId) => {
    $(`div[class~="${type}-div"] [data-row="${rowId}"]`).remove();
    triggerCalculator(type);
}

var allowanceKeyChangeHandler = (element) => {
    let value = $(element).val();
    let data = $(element).data();

    let rowId = data.row_id || 0;
    let rowType = data.type || '';

    if(rowId == 0) {
        return false;
    }

    let rowData = null;
    $.array_stream['dataset'][rowType]?.map(each => {
        if(each.id == parseInt(value)) {
            rowData = each;
        }
    });

    $(`input[id="${rowType}_amount_${rowId}"]`).val(rowData?.default_amount);
    if(["percentage_on_gross_total", "percentage_on_basic_salary"].includes(rowData?.calculation_method)) {
        $(`input[id="${rowType}_amount_${rowId}"]`).attr({'readonly': true});
        $(`input[id="${rowType}_amount_${rowId}"]`).val(rowData?.calculation_value);
    }

    console.log({rowData, rowId, rowType, value, 'input': `input[name="${rowType}_amount_${rowId}"]`});
}

var addDeductions = () => {
    $(`button[class~="add-deductions"]`).on('click', function(e) {
        let htmlData = $('div[class~="deductions-div"] [data-row]:last select').html();
        var dlastRow = $(`div[class~="deductions-div"] [data-row]`).length;

        dlastRow++;

        let selectOptions = $('div[class~="deductions-div"] [data-row]:last select > option').length;

        if (selectOptions == dlastRow) {
            return false;
        }

        $(`div[class~="deductions-div"] [data-row]:last`).after(`
            <div class="initial mb-2" data-row="${dlastRow}">
                <div class="row">
                    <div class="col-lg-6 mb-2 col-md-6">
                        <select name="deductions[]" data-width="100%" onchange="return allowanceKeyChangeHandler(this)" data-type="deductions" data-row_id="${dlastRow}" id="deductions_${dlastRow}" class="form-control selectpicker">
                            ${htmlData}
                        </select>
                    </div>
                    <div class="col-lg-5 mb-2 col-md-5">
                        <input placeholder="Amount" min="0" max="20000" class="form-control" type="text" name="deductions_amount[]" id="deductions_amount_${dlastRow}">
                    </div>
                    <div class="text-center">
                        <span class="remove-row cursor btn btn-outline-danger" onclick="return removeRow('deductions','${dlastRow}');"><i class="fa fa-trash"></i></span>
                    </div>
                </div>
            </div>
        `);
        $(`.selectpicker`).select2();
        if ($(`div[class~="deductions-list"]`).length) {
            deductionsKeyControl();
        }
    });
}

var addAllowance = () => {
    $(`button[class~="add-allowance"]`).on('click', function(e) {
        let htmlData = $('div[class~="allowance-div"] [data-row]:last select').html();
        var lastRowId = $(`div[class~="allowance-div"] [data-row]`).length;

        lastRowId++;

        let selectOptions = $('div[class~="allowance-div"] [data-row]:last select > option').length;

        if (selectOptions == lastRowId) {
            return false;
        }

        $(`div[class~="allowance-div"] [data-row]:last`).after(`
            <div class="initial mb-2" data-row="${lastRowId}">
                <div class="row">
                    <div class="col-lg-6 mb-2 col-md-6">
                        <select name="allowance[]" data-width="100%" onchange="return allowanceKeyChangeHandler(this)" id="allowance_${lastRowId}" data-type="allowance" data-row_id="${lastRowId}" class="form-control selectpicker">
                            ${htmlData}
                        </select>
                    </div>
                    <div class="col-lg-5 mb-2 col-md-5">
                        <input placeholder="Amount" min="0" max="20000" class="form-control" type="text" name="allowance_amount[]" id="allowance_amount_${lastRowId}">
                    </div>
                    <div class="text-center">
                        <span class="remove-row cursor btn btn-outline-danger" onclick="return removeRow('allowance','${lastRowId}');"><i class="fa fa-trash"></i></span>
                    </div>
                </div>
            </div>
        `);
        $(`.selectpicker`).select2();
        if ($(`div[class~="allowances-list"]`).length) {
            allowanceKeyControl();
        }
    });
}

var recalculateTotal = () => {
    let allowance = parseInt($(`div[class~="summary-list"] input[name="total_allowances"]`).val());
    let deductions = parseInt($(`div[class~="summary-list"] input[name="total_deductions"]`).val());
    let basic_salary = parseInt($(`div[class~="summary-list"] input[name="basic_salary"]`).val());

    basic_salary = isNaN(basic_salary) ? 0 : basic_salary;
    allowance = isNaN(allowance) ? 0 : allowance;
    deductions = isNaN(deductions) ? 0 : deductions;

    let grandTotal = (basic_salary + allowance - deductions);

    $(`div[class~="summary-list"] input[name="net_salary"]`).val(grandTotal);
}

var recalculateAllowance = () => {
    var allowance = 0;
    $.each($(`div[class~="allowances-list"] div[class~="initial"]`), function(i, e) {
        allowance += parseInt($(`input[id^="allowance_amount_${$(this).attr('data-row')}"]`).val());
    });
    allowance = isNaN(allowance) ? 0 : allowance;
    $(`div[class~="summary-list"] input[name="total_allowances"]`).val(allowance);

    recalculateTotal();
}

var reset_allowance_type_fields = () => {
    ['subject_to_paye', 'subject_to_ssnit', 'pre_tax_deduction'].forEach(function(item) {
        $(`div[id="allowanceTypesModal"] input[name="${item}"]`).prop('checked', false);
    });
    $(`input[name="calculation_value"]`).val('');
}

var save_payroll_settings = () => {
    let payload = {
        auto_calculate_paye: $(`input[name="auto_calculate_paye"]`).prop('checked'),
        auto_calculate_ssnit: $(`input[name="auto_calculate_ssnit"]`).prop('checked'),
        auto_calculate_tier_2: $(`input[name="auto_calculate_tier_2"]`).prop('checked'),
        auto_validate_payslips: $(`input[name="auto_validate_payslips"]`).prop('checked'),
        payroll_frequency: $(`select[name="payroll_frequency"]`).val(),
        auto_generate_payslip: $(`input[name="auto_generate_payslip"]`).prop('checked'),
        payment_day: $(`input[name="payment_day"]`).val(),
        setting_name: "payroll_settings",
    };
    $.post(`${baseUrl}api/settings/savesettings`, payload).then((response) => {
        notify(response?.data?.result || 'Error processing request.', responseCode(response.code));
    });
}

function allowanceTypeChange() {
    $(`select[name="allowance_type"]`).on('change', function() {
        let allowance_type = $(this).val();
        if(allowance_type == "Deduction") {
            $(`div[data-item_type="deduction"]`).removeClass('hidden');
            $(`div[data-item_type="allowance"]`).addClass('hidden');
        } else {
            $(`div[data-item_type="deduction"]`).addClass('hidden');
            $(`div[data-item_type="allowance"]`).removeClass('hidden');
        }
        reset_allowance_type_fields();
    });
}

$(`select[name="calculation_method"]`).on('change', function() {
    let calculation_method = $(this).val();
    if(calculation_method == "fixed_amount") {
        $(`input[name="calculation_value"]`).prop('disabled', false);
    } else {
        $(`input[name="calculation_value"]`).prop('disabled', true);
    }
});

$(`div[class="summary-list"] input[name="basic_salary"]`).on('input', function() {
    recalculateTotal();
});

var calculateOverallMonthPayments = () => {
    let allowances = 0;
    let deductions = 0;
    let basic_salary = 0;
    let selectedEmployees = 0;
    $.each($(`div[id="payslip_container"] tr[data-staff_id]`), function(i, e) {
        let row_id = $(this).attr('data-staff_id');
        // only calculate if the checkbox is checked
        if($(`tr[data-staff_id='${row_id}'] input[name="user_ids[]"]`).is(':checked')) {
            selectedEmployees++;
            allowances += parseInt($(`tr[data-staff_id='${row_id}'] span[class~="allowances"]`).text());
            deductions += parseInt($(`tr[data-staff_id='${row_id}'] span[class~="deductions"]`).text());
            basic_salary += parseInt($(`tr[data-staff_id='${row_id}'] input[name="basic_salary"]`).val());
        }
    });
    allowances = isNaN(allowances) ? 0 : allowances;
    deductions = isNaN(deductions) ? 0 : deductions;
    basic_salary = isNaN(basic_salary) ? 0 : basic_salary;
    let net_salary = basic_salary + allowances - deductions;

    $(`div[id="payslip_container"] span[class~="total_basic_salary"]`).text(format_currency(basic_salary));
    $(`div[id="payslip_container"] span[class~="total_allowances"]`).text(format_currency(allowances));
    $(`div[id="payslip_container"] span[class~="total_deductions"]`).text(format_currency(deductions));
    $(`div[id="payslip_container"] span[class~="total_net_salary"]`).text(format_currency(net_salary));

    if(!selectedEmployees) {
        $(`button[id="generate_payslips"]`).prop('disabled', true);
    } else {
        $(`button[id="generate_payslips"]`).prop('disabled', false);
    }
}

if($(`div[id="payslip_container"] input[name='basic_salary']`).length) {
    $(`div[id="payslip_container"] input[name='basic_salary']`).on('input', function() {
        let row_id = $(this).attr('data-staff_id');
        let allowance = parseInt($(`tr[data-staff_id='${row_id}'] span[class~="allowances"]`).text());
        let deductions = parseInt($(`tr[data-staff_id='${row_id}'] span[class~="deductions"]`).text());
        let basic_salary = parseInt($(this).val());

        allowance = isNaN(allowance) ? 0 : allowance;
        deductions = isNaN(deductions) ? 0 : deductions;
        basic_salary = isNaN(basic_salary) ? 0 : basic_salary;

        let net_salary = basic_salary + allowance - deductions;
        if(net_salary > 50000) {
            notify("Sorry! The net salary cannot be greater than " + format_currency(50000, 2));
            $(`input[name="basic_salary"][data-staff_id="${row_id}"]`).val(50000);
            net_salary = 50000;
        }
        net_salary = format_currency(net_salary, 2);
        $(`tr[data-staff_id='${row_id}'] span[class~="net_salary"]`).text(net_salary);
        calculateOverallMonthPayments();
    });
    $(`input[data-item='staff_checkbox']`).on('change', function() {
        calculateOverallMonthPayments();
    });
    calculateOverallMonthPayments();
}

var generate_payslips = () => {
    let year_id = $(`div[id="payslip_container"] select[name="bulk_year_id"]`).val(),
        month_id = $(`div[id="payslip_container"] select[name="bulk_month_id"]`).val();

    swal({
        title: "Generate Payslips",
        text: `Are you sure you want to generate the payslips for the selected employees?\n\nYear: ${year_id}\nMonth: ${month_id}`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let payload = { year_id, month_id, user_ids: [] };
            $(`div[id="payslip_container"] input[name="user_ids[]"]`).map(function() {
                let user_id = $(this).val();
                let user_name = $(this).attr('data-user_name');
                // only append if checked
                if($(this).is(':checked')) {
                    payload.user_ids.push({
                        user_id, 
                        basic_salary: $(`div[id="payslip_container"] input[name="basic_salary"][data-staff_id="${user_id}"]`).val(),
                        user_name
                    });
                }
            }).get();
            $.post(`${baseUrl}api/payroll/generatepayslips`, payload).then((response) => {
                if(response.code === 200) {
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code),
                    });
                    if(typeof response.data.additional !== "undefined") {
                        setTimeout(() => {
                            loadPage(response.data.additional.href);
                        }, refresh_seconds);
                    }
                } else {
                    notify(response.data.result || 'Error processing request.', responseCode(response.code));
                }
            });
        }
    });
}

var reload_employee_payslips = () => {
    let year_id = $(`div[id="payslip_container"] select[name="bulk_year_id"]`).val(),
        month_id = $(`div[id="payslip_container"] select[name="bulk_month_id"]`).val();
    loadPage(`${baseUrl}payslip-bulkgenerate?bulk_year_id=${year_id}&bulk_month_id=${month_id}`);
}

var allowanceKeyControl = () => {
    $(`input[id^="allowance_amount_"]`).on('input', function() {
        recalculateAllowance();
    });
}

var recalculateDeductions = () => {
    var allowance = 0;
    $.each($(`div[class~="deductions-list"] div[class~="initial"]`), function(i, e) {
        allowance += parseInt($(`input[id^="deductions_amount_${$(this).attr('data-row')}"]`).val());
    });
    $(`div[class~="summary-list"] input[name="total_deductions"]`).val(allowance);

    recalculateTotal();
}

var bulk_validate_payslip_finalize = () => {
    let payload = { payslip_ids: [] };
    swal({
        title: "Finalize Payslip Validation",
        text: "Are you sure you want to finalize the validation of the payslip for the selected employees?.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[id="viewOnlyModal"] tbody[id="not_validated_body"] input[name="user_ids[]"]`).map(function() {
                if($(this).is(':checked')) {
                    let payslip_id = $(this).attr('data-payslip_id');
                    payload.payslip_ids.push(payslip_id);
                }
            }).get();
            $.post(`${baseUrl}api/payroll/bulkvalidatepayslip`, {payslip_ids: payload.payslip_ids}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(typeof response.data.additional !== "undefined") {
                    $(`div[id="viewOnlyModal"]`).modal('hide');
                    setTimeout(() => {
                        loadPage(response.data.additional.href);
                    }, refresh_seconds);
                }
            });
        }
    });
}

var bulk_validate_payslip = () => {
    let not_validated = $.array_stream['not_validated'] ?? [];
    if(not_validated.length == 0) {
        swal({
            text: "No payslips to validate.",
            icon: "error",
        });
        return false;
    }
    $(`div[id="viewOnlyModal"]`).modal('show');
    $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(`Validate Payslips`);
    $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html(`
    <table data-empty="" class="table table-bordered table-md table-striped">
        <thead>
            <tr class="font-17">
                <th width="30%">Staff Name</th>
                <th class="text-center">Period</th>
                <th>Salary Summary</th>
            </tr>
        </thead>
        <tbody id="not_validated_body"></tbody>
    </table>`);
    $(`div[id="viewOnlyModal"] div[class="modal-body"] #not_validated_body`).html(not_validated.map(each => `
        <tr data-row_id='${each.item_id}'>
            <td>
                <div style='padding-left: 2.5rem;' class='custom-control cursor col-lg-12 custom-switch switch-primary'>
                    <input data-item='staff_checkbox' data-payslip_id='${each.id}' data-user_name='${each.employee.name}' type='checkbox' value='${each.item_id}' data-item_id='${each.item_id}' name='user_ids[]' class='custom-control-input cursor' id='user_id_${each.item_id}_${each.id}' checked='checked'>
                    <label class='custom-control-label cursor' for='user_id_${each.item_id}_${each.id}'>${each.employee.name} 
                        <br><strong>${each.unique_id}</strong>
                    </label>
                </div>
            </td>
            <td width="25%" class='text-center font-16'>
                <span class='period'>${each.period}</span>
            </td>
            <td class='font-16'>
                <div class='row'>
                    <div class='col-lg-6'><strong>Basic Salary</strong>:</div> 
                    <div class='col-lg-6'>${each.salary.basic}</div>
                    <div class='col-lg-6'><strong>Total Earnings</strong>:</div> 
                    <div class='col-lg-6'>${each.salary.allowances}</div>
                    <div class='col-lg-6'><strong>Less Deductions</strong>:</div> 
                    <div class='col-lg-6'>${each.salary.deductions}</div>
                    <div class='col-lg-12'>
                        <hr class='mb-1 mt-1'>
                    </div>
                    <div class='col-lg-6 text-success'><strong>Net Salary:</strong></div> 
                    <div class='col-lg-6 text-success'><strong>${each.salary.net}</strong></div>
                </div>
            </td>
        </tr>
    `).join(''));
    $(`div[id="viewOnlyModal"] div[class~="modal-footer"]`).html(`
        <button class="btn btn-light" data-dismiss="modal">Close</button>
        <button class="btn btn-success" onclick="return bulk_validate_payslip_finalize()">Finalize Payslip Validation</button>
    `);
}

var deductionsKeyControl = () => {
    $(`input[id^="deductions_amount_"]`).on('input', function() {
        recalculateDeductions();
    });
}

var triggerCalculator = (type = "allowance") => {
    if (type == "allowance") {
        recalculateAllowance();
    } else {
        recalculateDeductions();
    }
}

var save_staff_allowances = () => {
    swal({
        title: "Save Staff Allowances",
        text: "Are you sure you want to save the allowances of this staff?.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let allowances = {},
                basic_salary = $(`input[name="basic_salary"]`).val(),
                employee_id = $(`input[name="employee_id"]`).val();
            $.each($(`div[class~="allowance-div"] div[class~="initial"]`), function(i, e) {
                let rowNumber = $(this).attr('data-row'),
                    allowance_id = $(`select[id^="allowance_${rowNumber}"]`).val(),
                    allowance_amount = $(`input[id^="allowance_amount_${rowNumber}"]`).val();
                allowances[allowance_id] = allowance_amount;
            });

            let deductions = {};
            $.each($(`div[class~="deductions-div"] div[class~="initial"]`), function(i, e) {
                let rowNumber = $(this).attr('data-row'),
                    deductions_id = $(`select[id^="deductions_${rowNumber}"]`).val(),
                    deductions_amount = $(`input[id^="deductions_amount_${rowNumber}"]`).val();
                deductions[deductions_id] = deductions_amount;
            });

            $.post(`${baseUrl}api/payroll/paymentdetails`, { basic_salary, allowances, deductions, employee_id }).then((response) => {
                let s_icon = "error";
                if (response.code === 200) {
                    s_icon = "success";
                    setTimeout(() => {
                        loadPage(`${baseUrl}payroll-view/${employee_id}/salary`);
                    }, refresh_seconds);
                }
                swal({
                    text: response.data.result,
                    icon: s_icon,
                });
            });
        }
    });
}

var cancelPayslip = () => {
    swal({
        title: "Cancel Payslip Generation",
        text: "Are you sure you want to cancel the generation of the payslip for this employee?.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[class~="summary-div"]`).addClass('hidden');
            $(`div[class~="allowance-note"]`).html('');
            $(`div[class~="allowance-div"] div[class~="allowances-list"]`).html(`<div class="text-center">Load Employee Allowance Data</div>`);
            $(`div[class~="deductions-div"] div[class~="deductions-list"]`).html(`<div class="text-center">Load Employee Deductions Data</div>`);
            $(`div[class~="summary-list"] textarea`).val('');
            $(`div[class~="summary-list"] select`).val('null').change();
            $(`div[class~="summary-list"] input`).val('');
        }
    });
}

var generate_payslip = () => {

    let month_id = $(`div[id="payslip_container"] select[name="month_id"]`).val(),
        year_id = $(`div[id="payslip_container"] select[name="year_id"]`).val(),
        employee_id = $(`div[id="payslip_container"] select[name="employee_id"]`).val(),
        basic_salary = $(`div[class~="summary-div"] input[name="basic_salary"]`).val(),
        payment_mode = $(`div[class~="summary-div"] select[name="payment_mode"]`).val(),
        payment_status = $(`div[class~="summary-div"] select[name="payment_status"]`).val(),
        comments = $(`div[class~="summary-div"] textarea[name="comments"]`).val();

    let allowances = {};
    $.each($(`div[class~="allowance-div"] div[class~="initial"]`), function(i, e) {
        let rowNumber = $(this).attr('data-row'),
            allowance_id = $(`select[id^="allowance_${rowNumber}"]`).val(),
            allowance_amount = $(`input[id^="allowance_amount_${rowNumber}"]`).val();
        allowances[allowance_id] = allowance_amount;
    });

    let deductions = {};
    $.each($(`div[class~="deductions-div"] div[class~="initial"]`), function(i, e) {
        let rowNumber = $(this).attr('data-row'),
            deductions_id = $(`select[id^="deductions_${rowNumber}"]`).val(),
            deductions_amount = $(`input[id^="deductions_amount_${rowNumber}"]`).val();
        deductions[deductions_id] = deductions_amount;
    });

    if (employee_id == 'null') {
        $(`div[class~="generate-result"]`).html('<div class="alert alert-danger">Select an Employee to continue.</div>');
        clearIt();
    } else if (month_id == 'null') {
        $(`div[class~="generate-result"]`).html('<div class="alert alert-danger">Select the Month to continue.</div>');
        clearIt();
    } else if (year_id == 'null') {
        $(`div[class~="generate-result"]`).html('<div class="alert alert-danger">Select the Year to continue.</div>');
        clearIt();
    } else {
        swal({
            title: "Generate Payslip",
            text: "Are you sure you want to generate the payslip for this employee?.",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((proceed) => {
            if (proceed) {
                $(`div[class~="allowance-div"] div[class~="allowances-list"] input, div[class~="deductions-div"] div[class~="deductions-list"] input, div[class~="deductions-div"] div[class~="deductions-list"] select, div[class~="allowance-div"] div[class~="allowances-list"] select`).prop('disabled', true);
                $(`div[class~="summary-list"] select`).prop('disabled', true);
                $(`div[class~="summary-list"] input`).prop('disabled', true);
                $(`div[class~="summary-list"] textarea`).prop('disabled', true);
                $(`div[class~="allowances-div"] button, div[class~="deductions-div"] button`).prop('disabled', true);
                let the_data = {
                    allowances,
                    deductions,
                    basic_salary,
                    comments,
                    payment_mode,
                    payment_status,
                    month_id,
                    year_id,
                    employee_id
                };
                $.post(`${baseUrl}api/payroll/generatepayslip`, the_data).then((response) => {
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code),
                    });
                    if (response.code !== 200) {
                        $(`div[class~="allowance-div"] div[class~="allowances-list"] input, div[class~="deductions-div"] div[class~="deductions-list"] input, div[class~="deductions-div"] div[class~="deductions-list"] select, div[class~="allowance-div"] div[class~="allowances-list"] select`).prop('disabled', false);
                        $(`div[class~="summary-list"] select`).prop('disabled', false);
                        $(`div[class~="summary-list"] input`).prop('disabled', false);
                        $(`div[class~="summary-list"] textarea`).prop('disabled', false);
                        $(`div[class~="summary-list"] button`).prop('disabled', false);
                        $(`div[class~="allowances-div"] button, div[class~="deductions-div"] button`).prop('disabled', false);
                        $(`div[class~="summary-list"] input[name="basic_salary"]`).prop('disabled', true);
                    } else {
                        $(`div[class~="allowance-div"] div[class~="allowances-list"]`).html(``);
                        $(`div[class~="deductions-div"] div[class~="deductions-list"]`).html(``);
                        setTimeout(function() {
                            loadPage(`${baseUrl}payslips`);
                        }, refresh_seconds);
                    }
                }).catch(() => {
                    swal({
                        text: "Sorry! An error was encountered while processing the request.",
                        icon: "error",
                    });
                    $(`div[class~="summary-list"] input[name="basic_salary"]`).prop('disabled', true);
                    $(`div[class~="allowance-div"] div[class~="allowances-list"] input, div[class~="deductions-div"] div[class~="deductions-list"] input, div[class~="deductions-div"] div[class~="deductions-list"] select, div[class~="allowance-div"] div[class~="allowances-list"] select`).prop('disabled', false);
                    $(`div[class~="summary-list"] select`).prop('disabled', false);
                    $(`div[class~="summary-list"] input`).prop('disabled', false);
                    $(`div[class~="summary-list"] textarea`).prop('disabled', false);
                    $(`div[class~="summary-list"] button`).prop('disabled', false);
                    $(`div[class~="allowances-div"] button, div[class~="deductions-div"] button`).prop('disabled', false);
                });
            }
        });
    }
}

var load_employee_payslip = () => {

    let month_id = $(`div[id="payslip_container"] select[name="month_id"]`).val(),
        year_id = $(`div[id="payslip_container"] select[name="year_id"]`).val(),
        employee_id = $(`div[id="payslip_container"] select[name="employee_id"]`).val();

    $(`div[class~="summary-div"]`).addClass('hidden');
    $(`div[class~="allowance-div"]`).addClass('hidden');
    $(`div[class~="deductions-div"]`).addClass('hidden');

    $.get(`${baseUrl}api/payroll/payslipdetails`, { employee_id, month_id, year_id }).then((response) => {

        if (response.code === 200) {
            let result = response.data.result;
            $(`div[class~="allowance-div"] div[class~="allowances-list"]`).html(result.allowance_data);
            $(`div[class~="deductions-div"] div[class~="deductions-list"]`).html(result.deductions_data);

            $(`div[class~="summary-list"] input[name="basic_salary"]`).val(result.payslip_data.basic_salary);
            $(`div[class~="summary-list"] input[name="total_allowances"]`).val(result.payslip_data.total_allowance);
            $(`div[class~="summary-list"] input[name="total_deductions"]`).val(result.payslip_data.total_deductions);
            $(`div[class~="summary-list"] input[name="net_salary"]`).val(result.payslip_data.total_takehome);
            $(`div[class~="summary-list"] textarea[name="comments"]`).val(result.payslip_data.comments);
            $(`div[class~="summary-list"] select[name="payment_mode"]`).val(result.payslip_data.payment_mode).change();
            $(`div[class~="summary-list"] select[name="payment_status"]`).val(result.payslip_data.status).change();
            $(`div[class~="allowance-note"]`).html(result.note);

            if (result.payslip_data.status == 1) {
                $(`div[class~="deductions-list"] select, div[class~="allowances-list"] select, div[class~="summary-list"] select`).prop('disabled', true);
                $(`div[class~="deductions-list"] input, div[class~="allowances-list"] input, div[class~="summary-list"] input`).prop('disabled', true);
                $(`div[class~="deductions-list"] button, div[class~="allowances-list"] button, div[class~="summary-list"] button`).prop('disabled', true);
                $(`div[class~="deductions-list"] button, div[class~="allowances-list"] button`).remove();
                $(`div[class~="summary-list"] textarea`).prop('disabled', true);
            } else {
                $(`div[class~="summary-list"] textarea`).prop('disabled', false);
                $(`div[class~="summary-list"] select`).prop('disabled', false);
                $(`div[class~="summary-list"] input`).prop('disabled', false);
            }
            $(`div[class~="summary-list"] input[name="basic_salary"]`).prop('disabled', true);

            $(`div[class~="summary-div"]`).removeClass('hidden');
            $(`div[class~="allowance-div"]`).removeClass('hidden');
            $(`div[class~="deductions-div"]`).removeClass('hidden');

            addAllowance();
            addDeductions();
            allowanceKeyControl();
            deductionsKeyControl();
            triggerCalculator();
            recalculateTotal();
            $(`select[class~="selectpicker"]`).select2();
            $(`div[class~="toggle-calculator"]`).removeClass("hidden");
        } else {
            swal({
                text: response.data.result,
                icon: responseCode(response.code),
            });
        }

    });
}

if ($(`input[id^="deductions_amount_"]`).length) {
    addAllowance();
    addDeductions();
}

var update_allowance = (allowance_id) => {
    if ($.array_stream["allowance_array_list"] !== undefined) {
        let activity_log = $.array_stream["allowance_array_list"];
        loadPage(`${baseUrl}payroll-category/preview/${allowance_id}`);
    }
}

var add_allowance = () => {
    $(`div[id="allowanceTypesModal"]`).modal("show");
    $(`div[id="allowanceTypesModal"] h5[class="modal-title"]`).html(`Add Allowance Item`);
    $(`div[id="allowanceTypesModal"] input, div[id="allowanceTypesModal"] textarea`).val("");
    $(`div[id="allowanceTypesModal"] select[name="is_statutory"]`).val("No").change();
    $(`div[data-item_type="deduction"]`).addClass('hidden');
    $(`div[data-item_type="allowance"]`).addClass('hidden');
    reset_allowance_type_fields();
}

allowanceTypeChange();

if(typeof update_expected_days == "undefined") {
    var update_expected_days = (user_id, table) => {
        const expected_days = $(`input[name="expected_days[]"]:checked`).map((index, element) => $(element).val()).get();
        $.post(`${baseUrl}api/${table}/expected_days`, { user_id, table, expected_days }).then((response) => {});
    }
}

if(typeof update_leave_days == "undefined") {
    var update_leave_days = (user_id, table) => {
        const leave_days = $(`select[name="leave_days"]`).val();
        $.post(`${baseUrl}api/${table}/leave_days`, { user_id, table, leave_days }).then((response) => {});
    }
}