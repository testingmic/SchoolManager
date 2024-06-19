$.form_data = {};

$(`select[name="class_id"]`).on("change", function() {
    let value = $(this).val(),
        class_payment_module = "Termly";

    $(`div[id="make_payment_button"], div[id="payment_month"]`).addClass("hidden");
    $(`div[id="student_information"], div[id="fees_payment_history"]`).html(``);
    $(`div[id="fees_allocation_wrapper"] input[id="select_all"], div[id="fees_payment_form"] *`).prop("disabled", true);
    $(`div[id="fees_allocation_form"] select[name="category_id"],div[id="fees_allocation_form"] input[name="amount"]`).attr("disabled", true).val(``).change();
    
    class_payment_module = $(`select[name="class_id"] > option[value="${value}"]`).attr("data-payment_module");

    if($(`div[id="fees_allocation_form"] select[name="payment_module"]`).length) {
        $(`div[id="fees_allocation_form"] select[name="category_id"],div[id="fees_allocation_form"] input[name="amount"]`).attr("disabled", true).val(``).change();
        $(`div[id="fees_allocation_form"] select[name="payment_module"]`).val(class_payment_module).change();
        $(`div[id="fees_allocation_form"] select[name="payment_module"]`).prop("disabled", true);
    }
    
    if (value.length && value !== "null") {
        $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">Loading Students Data <i class="fa fa-spin fa-spinner"></i></td></tr>`);
        $.get(`${baseUrl}api/users/quick_list?class_id=${value}&user_type=student`).then((response) => {
            $(`div[id="fees_allocation_form"] select[name="category_id"],div[id="fees_allocation_form"] input[name="amount"]`).attr("disabled", false);
            if (response.code == 200) {
                if(response.data.result) {
                    let students_list = ``,
                        count = 0;
                    $.array_stream["students_filtered_list"] = response.data.result;
                    $.each(response.data.result, function(i, e) {
                        count++;
                        students_list += `
                            <tr data-row_search='name' data-student_fullname='${e.name}' data-student_unique_id='${e.unique_id}'>
                                <td style="height:40px">${count}</td>
                                <td style="height:40px">
                                    <label for="student_id_${e.user_id}" class="text-uppercase cursor">${e.name}</label>
                                    <span data-column="status" data-item="${e.user_id}"></span>
                                    <div class="hidden"><strong>${e.unique_id}</strong></div>
                                </td>
                                <td style="height:40px"><span data-column="due" data-item="${e.user_id}"></span></td>
                                <td style="height:40px"><span data-column="paid" data-item="${e.user_id}"></span></td>
                                <td style="height:40px"><span data-column="balance" data-item="${e.user_id}"></span></td>
                                <td style="height:40px" align="center"><span data-column="select" data-item="${e.user_id}"></span></td>
                            </tr>`;
                    });
                    $(`table[id="simple_load_student"] tbody`).html(students_list);
                    student_fullname_search();
                } else {
                    $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">No student record found.</td></tr>`);        
                }
            } else {
                $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">No student record found.</td></tr>`);
            }
        });
    } else {
        $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">Students data appears here.</td></tr>`);
        $(`div[id="fees_allocation_wrapper"] input[id="select_all"]`).prop("disabled", true);
    }
});

$(`div[id="fees_allocation_wrapper"] input[id="select_all"]`).on("click", function () {
    $(this).parents(`table[id="simple_load_student"]`).find(`input[class="student_ids"]:checkbox`).prop('checked', this.checked);
});

var term_fees_allocation_amount = (request = "change") => {
    let $allot_to = $(`div[id="fees_allocation_form"] select[name="allocate_to"]`).val(),
        $class_id = $(`div[id="fees_allocation_form"] select[name="class_id"]`).val(),
        $student_id = $(`div[id="fees_allocation_form"] select[name="student_id"]`).val(),
        $academic_year = $(`div[id="fees_allocation_form"] input[name="academic_year"]`).val(),
        $academic_term = $(`div[id="fees_allocation_form"] input[name="academic_term"]`).val(),
        $category_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val(),
        $payment_month = $(`div[id="fees_allocation_form"] select[name="payment_month"]`).val(),
        $payment_module = $(`div[id="fees_allocation_form"] select[name="payment_module"]`).val();

    let data = {
        "allocate_to": $allot_to,
        "class_id": $class_id,
        "student_id": $student_id,
        "category_id": $category_id,
        "academic_year": $academic_year, 
        "academic_term": $academic_term,
        "payment_month": $payment_month,
        "payment_module": $payment_module
    };
    $(`div[id="fees_allocation_wrapper"] input[id="select_all"]`).prop({"disabled": true, "checked": false});
    if ($category_id.length && $category_id !== "null") {
        $(`div[class="form-content-loader"]`).css("display", "flex");
        $(`div[id="fees_allocation_form"] input[name="amount"]`).prop("disabled", true);
        $.get(`${baseUrl}api/fees/allocated_fees_amount`, data).then((response) => {
            $(`div[id="fees_allocation_form"] input[name="amount"]`).prop("disabled", false);
            if (response.code == 200) {
                $(`div[id="fees_allocation_form"] input[name="amount"]`).val(response.data.result.amount);
                if(response.data.result.students_allocation !== undefined) {
                    if(response.data.result.students_allocation.length) {
                        let payment_module = response.data.result.payment_module,
                            payment_month = response.data.result.payment_month;

                        if(request == "change") {
                            $(`div[id="fees_allocation_form"] select[name="payment_module"]`).val(payment_module).change();
                        }

                        if((payment_module == "Monthly") || ($payment_module == "Monthly")) {
                            $(`div[id="payment_month"]`).removeClass("hidden");
                        }
                        $.each(response.data.result.students_allocation, function(i, e) {
                            let status = `<span class="badge p-1 badge-danger">Unpaid</span>`;
                            if(e.exempted == 1) {
                                status = `<span class="badge p-1 badge-dark">Exempted</span>`;
                            } else if(e.paid_status == 1) {
                                status = `<span class="badge p-1 badge-success">Paid</span>`;
                            } else if(e.paid_status == 2) {
                                status = `<span class="badge p-1 badge-warning">Partly Paid</span>`;
                            }
                            if(!e.is_found) {
                                status = `<span class="badge p-1 badge-primary">Not Set</span>`;
                            }
                            $(`span[data-column="status"][data-item="${e.item_id}"]`).html(status);
                            $(`span[data-column="due"][data-item="${e.item_id}"]`).html(`<strong>${e.amount_due}</strong>`);
                            $(`span[data-column="paid"][data-item="${e.item_id}"]`).html(`${e.amount_paid}`);
                            $(`span[data-column="balance"][data-item="${e.item_id}"]`).html(`<strong>${e.balance}</strong>`);
                            $(`span[data-column="select"][data-item="${e.item_id}"]`).html(`
                                <input ${(e.exempted == 1 || e.paid_status == 1) ? "disabled" : `class="student_ids" name="student_ids[]" value="${e.item_id}" id="student_id_${e.item_id}"`} style="width:20px;cursor:pointer;height:20px;" type="checkbox">
                            `);
                        });
                    }
                    $(`div[id="fees_allocation_wrapper"] input[id="select_all"]`).prop("disabled", false);
                }
            }
            $(`div[class="form-content-loader"]`).css("display", "none");
        }).catch(() => {
            $(`div[class="form-content-loader"]`).css("display", "none");
            $(`div[id="fees_allocation_wrapper"] input[id="select_all"]`).prop({"disabled": true, "checked": false});
            $(`div[id="fees_allocation_form"] input[name="amount"]`).prop("disabled", true);
        });
    }
}

var save_Fees_Allocation = () => {

    let $allot_to = $(`div[id="fees_allocation_form"] select[name="allocate_to"]`).val(),
        $class_id = $(`div[id="fees_allocation_form"] select[name="class_id"]`).val(),
        $category_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val(),
        $academic_year = $(`div[id="fees_allocation_form"] input[name="academic_year"]`).val(),
        $academic_term = $(`div[id="fees_allocation_form"] input[name="academic_term"]`).val(),
        $amount = $(`div[id="fees_allocation_form"] input[name="amount"]`).val(),
        $payment_module = $(`div[id="fees_allocation_form"] select[name="payment_module"]`).val();

    swal({
        title: "Allocate Fees",
        text: `Are you sure you want to allocate fees to the selected user group for ${$academic_term} Term in ${$academic_year} Academic Year?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let data = {
                "allocate_to": $allot_to,
                "class_id": $class_id,
                "category_id": $category_id,
                "amount": $amount,
                "academic_year": $academic_year, 
                "academic_term": $academic_term,
                "payment_module": $payment_module
            };

            let student_ids = {};
            $.each($(`div[id="fees_allocation_wrapper"] input[class="student_ids"]:checked`), function(i, e) {
                let item = $(this);
                    student_ids[i] = item.val();
            });

            data["student_id"] = student_ids;

            $(`div[id="fees_allocation_form"] *`).prop("disabled", true);
            $(`div[id="allocate_fees_button"] button`).html(`Processing Request <i class="fa fa-spin fa-spinner"></i>`);
            $.pageoverlay.show();
            $.post(`${baseUrl}api/fees/allocate_fees`, data).then((response) => {
                $(`div[id="fees_allocation_form"] *`).prop("disabled", false);
                $(`div[id="allocate_fees_button"] button`).html(`Allocate Fee`);
                $.pageoverlay.hide();
                if (response.code === 200) {
                    term_fees_allocation_amount();
                    $(`table[id="simple_load_student"] input[type="checkbox"]`).prop('checked', false);
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
            }).catch(() => {
                $.pageoverlay.hide();
                $(`div[id="fees_allocation_form"] *`).prop("disabled", false);
                $(`div[id="allocate_fees_button"] button`).html(`Allocate Fee`);
            });
        }
    });
}

$(`div[id="fees_allocation_form"] select[name="category_id"]`).on("change", function() {
    term_fees_allocation_amount();
});

$(`div[id="fees_allocation_form"] select[name="payment_month"]`).on("change", function () {
    term_fees_allocation_amount("do_not_change");
});

$(`div[id="fees_allocation_form"] select[name="payment_module"]`).on("change", function() {
    if($(this).val() === "Monthly") {
        $(`div[id="payment_month"]`).removeClass("hidden");
        $(`select[name="payment_month"]`).prop("disabled", false);
    } else {
        $(`div[id="payment_month"]`).addClass("hidden");
        $(`select[name="payment_month"]`).prop("disabled", true);
    }
});