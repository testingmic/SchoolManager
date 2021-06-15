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
        $(`div[id="make_payment_button"] button`).prop("disabled", false).html(`Load Form`);
        if (response.code === 200) {
            $(`div[id="fees_payment_history"]`).html(response.data.result.form);
            $(`button[id="payment_cancel"]`).removeClass("hidden");
            $(`div[id="fees_payment_form"] input[id="amount"]`).focus();

            if (response.data.result.query !== undefined) {
                if (response.data.result.query.paid_status !== undefined) {
                    if (response.data.result.query.paid_status == 1) {
                        $(`div[id="fees_payment_form"] *`).prop("disabled", true);
                    } else {
                        $(`div[id="fees_payment_preload"] *`).prop("disabled", true);
                        $(`div[id="fees_payment_form"] *`).prop("disabled", false);
                    }
                }
            } else if (response.data.result.uncategorized !== undefined) {
                if (response.data.result.paid_status == 1) {
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
        $(`div[id="make_payment_button"] button`).prop("disabled", false).html(`Load Form`);
    });
}

var finalize_payment = (response, checkout_url) => {
    let payment_method = "";
    $(`div[id="fees_payment_form"] input[name="amount"]`).val("");
    $(`div[id="fees_payment_form"] textarea[name="description"]`).val("");

    let payment_info = response.data.additional.payment,
        payment_id = response.data.additional.uniqueId;

    // reset the form
    $(`button[id="payment_cancel"]`).addClass("hidden");
    $(`div[id="fees_payment_form"] *`).prop("disabled", true);
    $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
    $(`div[id="fees_payment_form"] input, div[id="fees_payment_form"] textarea`).val("");
    $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");

    if (payment_info !== undefined) {
        $(`span[class="amount_paid"][data-checkout_url="${checkout_url}"]`).html(`${payment_info.currency} ${payment_info.amount_paid}`);
        $(`span[class="outstanding"][data-checkout_url="${checkout_url}"]`).html(`${payment_info.currency} ${payment_info.balance}`);
        if (payment_info.paid_status === "1" || payment_info.paid_status === 1) {
            $(`span[data-payment_label='status']`)
                .removeClass('badge-danger badge-primary')
                .addClass('badge-success')
                .html("Paid");
        } else if (payment_info.paid_status === "2" || payment_info.paid_status === 2) {
            $(`span[data-payment_label='status']`)
                .removeClass('badge-danger badge-success')
                .addClass('badge-primary')
                .html("Partly Paid");
        }

        if (payment_info.last_payment_info.payment_method !== undefined) {

            if (payment_info.last_payment_info.payment_method === "Cheque") {
                payment_method += `<span class="last_payment_date"><i class="fa fa-home"></i> ${payment_info.last_payment_info.cheque_bank}</span><br>`;
                payment_method += `<span class="last_payment_date"><i class="fa fa-neuter"></i> ${payment_info.last_payment_info.cheque_number}</span><br>`;
            }

            $(`div[class='last_payment_container']`).html(`
                    <table width="100%" class="t_table table-hover table-bordered">
                        <tbody>
                            <tr>
                                <td width="43%">Last Payment Info:</td>
                                <td>
                                    <span class="last_payment_id"><strong>Payment ID:</strong> ${payment_info.last_payment_info.pay_id}</span><br>
                                    <span class="amount_paid"><i class="fa fa-money-bill"></i> ${payment_info.last_payment_info.currency} ${payment_info.last_payment_info.amount}</span><br>
                                    <span class="last_payment_date"><i class="fa fa-calendar-check"></i> ${payment_info.last_payment_info.created_date}</span><br>
                                    <hr class=\"mt-1 mb-1\">
                                    <span class="last_payment_date"><i class="fa fa-air-freshener"></i> ${payment_info.last_payment_info.payment_method}</span><br>
                                    ${payment_method}
                                    <p class="mt-3 mb-0 pb-0" id="print_receipt"><a class="btn btn-sm btn-outline-primary" target="_blank" href="${baseUrl}receipt/${payment_info.last_payment_id}"><i class="fa fa-print"></i> Print Receipt</a></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>`);
        }
        $(`div[id="cheque_payment_filter"]`).addClass("hidden");
    }

    if(payment_id !== null) {
        if(myPrefs.labels.print_receipt !== undefined) {
            window.open(
                `${baseUrl}receipt/${payment_id}`, `Payment Receipt`,
                `width=850,height=750,left=200,resizable,scrollbars=yes,status=1,left=${($(window).width())*0.25}`
            );
        }
    }

    setTimeout(() => {
        load_Pay_Fees_Form();
    }, 1500);
}

var save_Receive_Payment = () => {

    let $balance = parseInt($(`span[class="outstanding"]`).attr("data-amount_payable")),
        $amount = parseFloat($(`div[id="fees_payment_form"] input[name="amount"]`).val()),
        description = $(`div[id="fees_payment_form"] textarea[name="description"]`).val(),
        payment_method = $(`div[id="fees_payment_form"] select[name="payment_method"]`).val(),
        checkout_url = $(`span[class="outstanding"]`).attr("data-checkout_url"),
        student_id = $(`input[name='fees_payment_student_id']`).val(),
        email_address = $(`input[name="email_address"]`).val(),
        category_id = $(`input[name="fees_payment_category_id"]`).val(),
        t_message = "";

    if (!$(`div[id="fees_payment_form"] input[name="amount"]`).val().length) {
        swal({
            text: "Sorry! The amount cannot be empty.",
            icon: "error",
        });
        return false;
    }

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
            $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "flex");
            let data = {
                "amount": $amount,
                "category_id": category_id,
                "student_id": student_id,
                "description": description,
                "checkout_url": checkout_url,
                "payment_method": payment_method,
                "email_address": email_address,
                "contact_number": $(`input[name="contact_number"]`).val()
            };
            if ($(`select[name="payment_method"]`).val() === "cheque") {
                data["bank_id"] = $(`select[name="bank_id"]`).val();
                data["cheque_number"] = $(`input[name="cheque_number"]`).val();
            }

            $.post(`${baseUrl}api/fees/make_payment`, data).then((response) => {
                let s_icon = "error";
                if (response.code === 200) {
                    finalize_payment(response, checkout_url);
                    s_icon = "success";
                }
                swal({
                    text: response.data.result,
                    icon: s_icon,
                });
            }).catch(() => {
                $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");
                swal({
                    text: "Sorry! There was an error while trying to process the request.",
                    icon: "error",
                });
            });
        }
    });
}

var log_fees_payment = (reference_id, transaction_id) => {

    let amount = parseFloat($(`div[id="fees_payment_form"] input[name="amount"]`).val()),
        description = $(`div[id="fees_payment_form"] textarea[name="description"]`).val(),
        checkout_url = $(`span[class="outstanding"]`).attr("data-checkout_url"),
        student_id = $(`input[name='fees_payment_student_id']`).val(),
        email_address = $(`input[name="email_address"]`).val(),
        category_id = $(`input[name="fees_payment_category_id"]`).val();

    let data = {
        "amount": amount,
        "category_id": category_id,
        "student_id": student_id,
        "description": description,
        "checkout_url": checkout_url,
        "email_address": email_address,
        "reference_id": reference_id,
        "transaction_id": transaction_id,
        "contact_number": $(`input[name="contact_number"]`).val()
    };

    $.post(`${baseUrl}api/fees/momocard_payment`, data).then((response) => {
        if (response.code == 200) {
            $(`button[id="payment_cancel"]`).addClass("hidden");
            $(`div[id="fees_payment_form"] *`).prop("disabled", true);
            $(`button[id="momocard_payment_button"]`).addClass("hidden");
            $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
            $(`div[id="fees_payment_form"] input, div[id="fees_payment_form"] textarea`).val("");
            load_Pay_Fees_Form();
        }
    });

}

var receive_Momo_Card_Payment = () => {

    try {

        let amount = parseFloat($(`div[id="fees_payment_form"] input[name="amount"]`).val()),
            email_address = $(`input[name="email_address"]`).val();

        if (!$(`div[id="fees_payment_form"] input[name="amount"]`).val().length) {
            swal({
                text: "Sorry! The amount cannot be empty.",
                icon: "error",
            });
            return false;
        }
        if (!email_address.length) {
            swal({
                text: "Sorry! The email address section is required.",
                icon: "error",
            });
            return false;
        }
        amount = amount * 100;

        $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "flex");

        var popup = PaystackPop.setup({
            key: pk_payment_key,
            email: email_address,
            amount: amount,
            currency: myPrefs.labels.currency,
            onClose: function() {
                swal({
                    text: "Payment Process Cancelled",
                    icon: "error",
                });
            },
            callback: function(response) {
                let message = `Payment ${response.message}`,
                    code = "error";
                if (response.message == "Approved") {
                    code = "success";
                    log_fees_payment(response.reference, response.transaction);
                } else {
                    swal({ text: message, icon: code });
                    $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");
                }
            }
        });
        popup.openIframe();
    } catch (e) {
        $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");
        swal({
            text: "Connection Failed! Please check your internet connection to proceed.",
            icon: "error",
        });
    }

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

var search_Payment_Log = () => {
    let term = $(`div[id="finance_search_field"] input[id="log_search_term"]`).val();

    if (!term.length) {
        $(`div[id="finance_search_field"] div[id="log_search_term_list"]`).html(``);
        swal({
            text: "Sorry! The search term cannot be empty.",
            icon: "error",
        });
    } else {
        $(`div[id="finance_search_field"] div[id="log_search_term_list"]`).html(`<div align="center">Processing request <i class="fa fa-spin fa-spinner"></i></div>`);
        $.get(`${baseUrl}api/fees/search`, { term }).then((response) => {
            if (response.code === 200) {
                let results_list = ``,
                    location = `${baseUrl}fees-search?term=${term}`;
                $.each(response.data.result, function(i, data) {
                    results_list += `
                    <div class="row mb-2 border-bottom pb-2">
                        <div class="col-md-12">
                            <table width="90%" border="0" cellpadding="3px">
                                <tr>
                                    <td><strong>Student Name:</strong></td>
                                    <td>
                                        <span class="underline" title="Click to view payment history">
                                            <a href="${baseUrl}receipt?student_id=${data.student_id}" target="_blank">${data.student_info.name}</a>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Class: </strong></td>
                                    <td>${data.class_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fee Payment Category: </strong></td>
                                    <td>${data.category_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount Paid: </strong></td>
                                    <td>${data.currency} ${data.amount}</td>
                                </tr>
                                <tr>
                                    <td><strong>Receipt ID: </strong></td>
                                    <td>${data.receipt_id}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date Paid: </strong></td>
                                    <td>${data.recorded_date}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <a href="${baseUrl}fees-view/${data.item_id}?redir=${term}" class="btn btn-sm btn-outline-success" title="Click to view full details"><i class="fa fa-eye"></i> View</a>
                                        <a href="${baseUrl}receipt/${data.item_id}" target="_blank" class="btn btn-sm btn-outline-warning" title="Click to print receipt"><i class="fa fa-print"></i> Print</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>`;
                });
                $(`div[id="finance_search_field"] div[id="log_search_term_list"]`).html(results_list);
                window.history.pushState({ current: location }, "", location);
                linkClickStopper($(`div[id="finance_search_field"] div[id="log_search_term_list"]`));
            } else {
                $(`div[id="finance_search_field"] div[id="log_search_term_list"]`).html(`<div align="center" class="text-danger">${response.result.data}</div>`);
            }
        }).catch(() => {
            $(`div[id="finance_search_field"] div[id="log_search_term_list"]`).html(``);
            swal({
                text: "Sorry! There was an error while processing the request.",
                icon: "error",
            });
        });
    }
}

var generate_payment_report = (student_id) => {
    let category_id = $(`select[id="category_id"]`).val(),
        start_date = $(`input[name="group_start_date"]`).val(),
        end_date = $(`input[name="group_end_date"]`).val();
    window.open(`${baseUrl}receipt?category_id=${category_id}&start_date=${start_date}&end_date=${end_date}`);
}

$(`div[id="finance_search_field"] input[id="log_search_term"]`).on("keyup", function(evt) {
    let search_term = $(this).val();
    if (evt.keyCode == 13 && !evt.shiftKey) {
        search_Payment_Log();
    }
});

$(`div[id="fees_allocation_form"] select[name="allocate_to"]`).on("change", function() {
    let value = $(this).val();
    if (value === "class") {
        $(`div[id="students_list"]`).addClass("hidden");
    } else {
        $(`div[id="students_list"]`).removeClass("hidden");
    }
});

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

$(`select[name="payment_method"]`).on("change", function() {
    let mode = $(this).val();
    $(`label[class="email_label"]`).html(`Email Address`);
    $(`button[id="momocard_payment_button"]`).addClass("hidden");
    $(`button[id="default_payment_button"]`).removeClass("hidden");
    if (mode === "cash") {
        $(`div[id="cheque_payment_filter"]`).addClass("hidden");
    } else if (mode === "cheque") {
        $(`div[id="cheque_payment_filter"]`).removeClass("hidden");
    } else if (mode === "momo_card") {
        $(`button[id="momocard_payment_button"]`).removeClass("hidden");
        $(`label[class="email_label"]`).html(`Email Address <span class="required">*</span>`);
        $(`button[id="default_payment_button"], div[id="cheque_payment_filter"]`).addClass("hidden");
    }
});
