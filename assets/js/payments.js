var load_Pay_Fees_Form = () => {
    let $dept_id = $(`select[name="department_id"]`).val(),
        $class_id = $(`select[name="class_id"]`).val(),
        $student_id = $(`select[name="student_id"]`).val(),
        $category_id = $(`select[name="category_id"]`).val();
    let data = {
        "department_id": $dept_id,
        "class_id": $class_id,
        "student_id": $student_id,
        "category_id": $category_id,
        "show_history": true
    };
    $(`div[id="make_payment_button"] button`).prop("disabled", true).html(`Loading record <i class="fa fa-spin fa-spinner"></i>`);

    $.get(`${baseUrl}api/fees/payment_form`, data).then((response) => {
        $(`div[id="make_payment_button"] button`).prop("disabled", false).html(`Make Payment`);
        if (response.code === 200) {
            $(`div[id="fees_payment_history"]`).html(response.data.result.form);
            $(`button[id="payment_cancel"]`).removeClass("hidden");
            $(`div[id="fees_payment_form"] input[id="amount"]`).focus();

            if (response.data.result.query.paid_status !== undefined) {
                if (response.data.result.query.paid_status == 1) {
                    $(`div[id="fees_payment_form"] *`).prop("disabled", true);
                } else {
                    $(`div[id="fees_payment_preload"] *`).prop("disabled", true);
                    $(`div[id="fees_payment_form"] *`).prop("disabled", false);
                }
            }

        } else {
            swal({
                text: response.data.result,
                icon: "error",
            });
        }
    }).catch(() => {
        $(`div[id="make_payment_button"] button`).prop("disabled", false).html(`Make Payment`);
    });
}

var save_Receive_Payment = () => {

    let $balance = parseInt($(`span[class="outstanding"]`).attr("data-amount_payable")),
        $amount = parseInt($(`div[id="fees_payment_form"] input[name="amount"]`).val()),
        description = $(`div[id="fees_payment_form"] textarea[name="description"]`).val(),
        payment_method = $(`div[id="fees_payment_form"] select[name="payment_method"]`).val(),
        checkout_url = $(`span[class="outstanding"]`).attr("data-checkout_url"),
        t_message = "";

    if ($amount > $balance) {
        t_message = `Are you sure you want to save this payment. 
            An amount of ${$amount} is been paid which is more than the required of ${$balance}`;
    } else if ($amount < $balance) {
        t_message = `Are you sure you want to save this payment. 
            An amount of ${$amount} is been paid which will leave a balance of ${$balance-$amount}.`;
    }
    swal({
        title: "Make Payment",
        text: `${t_message}\nDo you want to proceed to make the payment?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let data = {
                "amount": $amount,
                "description": description,
                "checkout_url": checkout_url,
                "payment_method": payment_method
            };
            if ($(`select[name="payment_method"]`).val() === "cheque") {
                data["bank_id"] = $(`select[name="bank_id"]`).val();
                data["cheque_number"] = $(`input[name="cheque_number"]`).val();
            }
            $.post(`${baseUrl}api/fees/make_payment`, data).then((response) => {
                let s_icon = "error";
                if (response.code === 200) {
                    s_icon = "success";
                    $(`div[id="fees_payment_form"] input[name="amount"]`).val("");
                    $(`div[id="fees_payment_form"] textarea[name="description"]`).val("");
                    let payment_info = response.data.additional.payment;
                    $(`span[class="amount_paid"][data-checkout_url="${checkout_url}"]`).html(`${payment_info.currency} ${payment_info.amount_paid}`);
                    $(`span[class="outstanding"][data-checkout_url="${checkout_url}"]`).html(`${payment_info.currency} ${payment_info.balance}`);
                    if (payment_info.paid_status === "1") {
                        $(`span[data-payment_label='status']`)
                            .removeClass('badge-danger badge-primary')
                            .addClass('badge-success')
                            .html("Paid");
                    } else if (payment_info.paid_status === "2") {
                        $(`span[data-payment_label='status']`)
                            .removeClass('badge-danger badge-success')
                            .addClass('badge-primary')
                            .html("Partly Paid");
                    }
                    $(`div[class='last_payment_container']`).html(`
                    <table width="100%" class="t_table table-hover table-bordered">
                        <tbody>
                            <tr>
                                <td width="55%">Last Payment Info:</td>
                                <td>
                                    <span class="last_payment_id"><strong>Payment ID:</strong> ${payment_info.last_payment_info.pay_id}</span><br>
                                    <span class="amount_paid"><i class="fa fa-money-bill"></i> ${payment_info.last_payment_info.currency} ${payment_info.last_payment_info.amount}</span><br>
                                    <span class="last_payment_date"><i class="fa fa-calendar-check"></i> ${payment_info.last_payment_info.created_date}</span><br>
                                    <p class="mt-3 mb-0 pb-0" id="print_receipt"><a class="btn btn-sm btn-outline-primary" target="_blank" href="${baseUrl}receipt/${payment_info.last_payment_id}"><i class="fa fa-print"></i> Print Receipt</a></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>`);

                    // reset the form
                    $(`div[id="cheque_payment_filter"]`).addClass("hidden");
                    $(`button[id="payment_cancel"]`).addClass("hidden");
                    $(`div[id="fees_payment_form"] *`).prop("disabled", true);
                    $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
                    $(`div[id="fees_payment_form"] input, div[id="fees_payment_form"] textarea`).val("");
                }
                swal({
                    text: response.data.result,
                    icon: s_icon,
                });
            }).catch(() => {
                swal({
                    text: "Sorry! There was an error while trying to process the request.",
                    icon: "error",
                });
            });
        }
    });
}

var cancel_Payment_Form = () => {
    swal({
        title: "Cancel Payment",
        text: `Are you sure you want to discard the form?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[id="fees_payment_history"]`).html(``);
            $(`button[id="payment_cancel"]`).addClass("hidden");
            $(`div[id="fees_payment_form"] *`).prop("disabled", true);
            $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
            $(`div[id="fees_payment_form"] input, div[id="fees_payment_form"] textarea`).val("");
        }
    });
}

var save_Fees_Allocation = () => {
    swal({
        title: "Allocate Fees",
        text: `Are you sure you want to allocate fees to the selected user group?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let $allot_to = $(`div[id="fees_allocation_form"] select[name="allocate_to"]`).val(),
                $class_id = $(`div[id="fees_allocation_form"] select[name="class_id"]`).val(),
                $student_id = $(`div[id="fees_allocation_form"] select[name="student_id"]`).val(),
                $category_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val(),
                $amount = $(`div[id="fees_allocation_form"] input[name="amount"]`).val();
            let data = {
                "allocate_to": $allot_to,
                "class_id": $class_id,
                "student_id": $student_id,
                "category_id": $category_id,
                "amount": $amount
            };

            $(`div[id="fees_allocation_form"] *`).prop("disabled", true);
            $(`div[id="allocate_fees_button"] button`).html(`Processing Request <i class="fa fa-spin fa-spinner"></i>`);

            $.post(`${baseUrl}api/fees/allocate_fees`, data).then((response) => {
                $(`div[id="fees_allocation_form"] *`).prop("disabled", false);
                $(`div[id="allocate_fees_button"] button`).html(`Allocate Fee`);

                let s_icon = "error";
                if (response.code === 200) {
                    s_icon = "success";
                    $(`div[id="fees_allocation_form"] input`).val("");
                }
                swal({
                    position: "top",
                    text: response.data.result,
                    icon: s_icon,
                });
            }).catch(() => {
                $(`div[id="fees_allocation_form"] *`).prop("disabled", false);
                $(`div[id="allocate_fees_button"] button`).html(`Allocate Fee`);
            });
        }
    });
}

var email_Receipt = (receipt_id) => {

}

$(`div[id="fees_allocation_form"] select[name="allocate_to"]`).on("change", function() {
    let value = $(this).val();
    if (value === "class") {
        $(`div[id="students_list"]`).addClass("hidden");
    } else {
        $(`div[id="students_list"]`).removeClass("hidden");
    }
});

var load_Fees_Allocation_Amount = () => {
    let $allot_to = $(`div[id="fees_allocation_form"] select[name="allocate_to"]`).val(),
        $class_id = $(`div[id="fees_allocation_form"] select[name="class_id"]`).val(),
        $student_id = $(`div[id="fees_allocation_form"] select[name="student_id"]`).val(),
        $category_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val();

    let data = {
        "allocate_to": $allot_to,
        "class_id": $class_id,
        "student_id": $student_id,
        "category_id": $category_id
    };
    if ($category_id.length && $category_id !== "null") {
        $(`div[id="fees_allocation_form"] input[name="amount"]`).prop("disabled", true);
        $.get(`${baseUrl}api/fees/allocate_fees_amount`, data).then((response) => {
            $(`div[id="fees_allocation_form"] input[name="amount"]`).prop("disabled", false);
            if (response.code == 200) {
                $(`div[id="fees_allocation_form"] input[name="amount"]`).val(response.data.result);
            }
        }).catch(() => {
            $(`div[id="fees_allocation_form"] input[name="amount"]`).prop("disabled", false);
        });
    }
}

$(`div[id="fees_allocation_form"] select[name="category_id"]`).on("change", function() {
    load_Fees_Allocation_Amount();
});

$(`div[id="fees_allocation_form"] select[name="student_id"]`).on("change", function() {
    let value = $(this).val(),
        cat_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val();

    if (value.length && value !== "null" && cat_id.length) {
        load_Fees_Allocation_Amount();
    }
});
$(`div[class~="toggle-calculator"]`).removeClass("hidden");

$(`select[name="payment_method"]`).on("change", function() {
    let mode = $(this).val();
    if (mode === "cash") {
        $(`div[id="cheque_payment_filter"]`).addClass("hidden");
    } else {
        $(`div[id="cheque_payment_filter"]`).removeClass("hidden");
    }
});