var removeRow = () => {
    $(`span[class~="remove-row"]`).on('click', function() {
        let type = $(this).attr('data-type');
        let rowId = $(this).attr('data-value');
        $(`div[class~="${type}-div"] [data-row="${rowId}"]`).remove();
    });
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
                        <span class="remove-row cursor btn btn-outline-danger" data-type="deductions" data-value="${dlastRow}"><i class="fa fa-trash"></i></span>
                    </div>
                </div>
            </div>
        `);
        if ($(`div[class~="deductions-list"]`).length) {
            deductionsKeyControl();
        }
        removeRow();
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
                        <span class="remove-row cursor btn btn-outline-danger" data-type="allowance" data-value="${lastRowId}"><i class="fa fa-trash"></i></span>
                    </div>
                </div>
            </div>
        `);
        if ($(`div[class~="allowances-list"]`).length) {
            allowanceKeyControl();
        }
        removeRow();
    });
}

var recalculateTotal = () => {
    let allowance = parseInt($(`div[class~="summary-list"] input[name="total_allowances"]`).val());
    let deductions = parseInt($(`div[class~="summary-list"] input[name="total_deductions"]`).val());
    let basic_salary = parseInt($(`div[class~="summary-list"] input[name="basic_salary"]`).val());

    let grandTotal = (basic_salary + allowance - deductions);

    $(`div[class~="summary-list"] input[name="net_salary"]`).val(grandTotal);
}

var recalculateAllowance = () => {
    var allowance = 0;
    $.each($(`div[class~="allowances-list"] div[class~="initial"]`), function(i, e) {
        allowance += parseInt($(`input[id^="allowance_amount_${$(this).attr('data-row')}"]`).val());
    });
    $(`div[class~="summary-list"] input[name="total_allowances"]`).val(allowance);

    recalculateTotal();
}

$(`div[class="summary-list"] input[name="basic_salary"]`).on('input', function() {
    recalculateTotal();
});

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

var triggerCalculator = () => {
    $(`span[class~="remove-row"]`).on('click', function() {
        let type = $(this).attr('data-type');
        let rowId = $(this).attr('data-value');
        if (type == "allowance") {
            recalculateAllowance();
        } else {
            recalculateDeductions();
        }
    });
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

            });
        }
    });
}

removeRow();
addAllowance();
addDeductions();