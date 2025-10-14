var removeRow = (type, rowId) => {
    $(`div[class~="${type}-div"] [data-row="${rowId}"]`).remove();
    triggerCalculator(type);
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
                        <select name="deductions[]" id="deductions_${dlastRow}" class="form-control selectpicker">${htmlData}</select>
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
                        <select name="allowance[]" id="allowance_${lastRowId}" class="form-control selectpicker">${htmlData}</select>
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
                payload.user_ids.push({
                    user_id, 
                    basic_salary: $(`div[id="payslip_container"] input[name="basic_salary"][data-staff_id="${user_id}"]`).val(),
                    user_name
                })
            }).get();
            $.post(`${baseUrl}api/payroll/generatepayslips`, payload).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(typeof response.data.additional !== "undefined") {
                    setTimeout(() => {
                        loadPage(response.data.additional.href);
                    }, refresh_seconds);
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
        if (activity_log[allowance_id] !== undefined) {
            let allowance = activity_log[allowance_id];
            $(`div[id="allowanceTypesModal"] h5[class="modal-title"]`).html(`Update ${allowance.type} Record`);
            $(`div[id="allowanceTypesModal"]`).modal("show");
            $(`div[id="allowanceTypesModal"] input[name="name"]`).val(allowance.name);
            $(`div[id="allowanceTypesModal"] input[name="allowance_id"]`).val(allowance_id);
            $(`div[id="allowanceTypesModal"] select[name="type"]`).val(allowance.type).change();
            $(`div[id="allowanceTypesModal"] select[name="is_statutory"]`).val(allowance.is_statutory).change();
            $(`div[id="allowanceTypesModal"] textarea[name="description"]`).val(allowance.description);
            $(`div[id="allowanceTypesModal"] input[name="default_amount"]`).val(allowance.default_amount);
            setTimeout(() => {
                $(`div[class~="modal-backdrop"]`).addClass("hidden");
            }, 200);
        }
    }
}

var add_allowance = () => {
    $(`div[id="allowanceTypesModal"]`).modal("show");
    $(`div[class~="modal-backdrop"]`).addClass("hidden");
    $(`div[id="allowanceTypesModal"] h5[class="modal-title"]`).html(`Add Allowance Item`);
    $(`div[id="allowanceTypesModal"] input, div[id="allowanceTypesModal"] textarea`).val("");
    $(`div[id="allowanceTypesModal"] select[name="is_statutory"]`).val("No").change();
}

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