var amount_in_words, delayTimer;
var load_Pay_Fees_Form = () => {
    $class_id = $(`select[name="class_id"]`).val(),
    $student_id = $(`select[name="student_id"]`).val(),
    $category_id = $(`select[name="category_id"]`).val(),
    $payment_month = $(`select[name="class_id"] > option[value="${$class_id}"]`).attr("data-payment_module");

    let data = {
        "class_id": $class_id,
        "student_id": $student_id,
        "category_id": $category_id,
        "show_history": true
    };

    if($payment_month == "Monthly") {
        data["payment_month"] = $(`select[name="payment_month"]`).val();
    }

    $(`div[id="make_payment_button"] button`).prop("disabled", true).html(`Loading record <i class="fa fa-spin fa-spinner"></i>`);
    $.get(`${baseUrl}api/fees/payment_form`, data).then((response) => {
        $(`div[id="make_payment_button"] button`).prop("disabled", false).html(`<i class="fa fa-filter"></i> Load Form`);
        if (response.code === 200) {
            let _result = response.data.result; 
            if (_result.query !== undefined) {
                if (_result.query.paid_status !== undefined) {
                    if (_result.query.paid_status == 1) {
                        $(`div[id="fees_payment_form"] *`).prop("disabled", true);
                    } else {
                        $(`div[id="fees_payment_form"] *`).prop("disabled", false);
                    }
                }
            } else if (_result.uncategorized !== undefined) {
                if (_result.paid_status == 1) {
                    $(`div[id="fees_payment_form"] *`).prop("disabled", true);
                } else {
                    $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
                    $(`div[id="fees_payment_form"] *`).prop("disabled", false);
                }
            }
            
            $(`div[id="fees_payment_history"]`).html(_result.form);
            $(`button[id="payment_cancel"]`).removeClass("hidden");
            $(`div[id="fees_payment_form"] select[id="payment_method"]`).val("cash").change();
            $(`div[id="cheque_payment_filter"]`).addClass("hidden");
            $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
            $(`div[id="fees_payment_form"] input[name="email_address"]`)
                .val(_result.student_details.email)
                .attr("value", _result.student_details.email);
            $(`div[id="fees_payment_form"] input[name="contact_number"]`)
                .val(_result.student_details.phone_number)
                .attr("value", _result.student_details.phone_number);
        } else {
            swal({
                text: response.data.result,
                icon: "error",
            });
        }
    }).catch(() => {
        $(`div[id="make_payment_button"] button`).prop("disabled", false).html(`<i class="fa fa-filter"></i> Load Form`);
    });
}

var finalize_payment = (response, checkout_url) => {
    let payment_method = "";
    $(`div[id="fees_payment_form"] input[name="amount"]`).val("");
    $(`div[id="fees_payment_form"] textarea[name="description"]`).val("");

    let payment = response.data.additional.payment,
        payment_id = response.data.additional.uniqueId;

    // reset the form
    $(`input[id='auto_load_form']`).remove();
    $(`button[id="payment_cancel"]`).addClass("hidden");
    $(`div[id="fees_payment_form"] *`).prop("disabled", true);
    $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
    $(`div[id="fees_payment_form"] input, div[id="fees_payment_form"] textarea`).val("");
    $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");

    if(payment !== undefined) {
        $(`span[class="fees_arrears"]`).html(`${myPrefs.labels.currency}${payment.debt}`);
        $(`span[class="balance_outstanding"]`).html(`${myPrefs.labels.currency}${payment.total_debt_formated}`);
        $(`select[name="student_id"] > option[value="${payment.user_id}"]`).prop("data-debt_formated", payment.debt_formated);
        $(`select[name="student_id"] > option[value="${payment.user_id}"]`).prop("data-total_debt_formated", payment.total_debt_formated)
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

$(`div[id="fees_payment_form"] input[name="amount"]`).on("input", async function() {
    let _amount = parseFloat($(this).val()),
        _balance = parseFloat($(`span[class~="outstanding"]`).attr("data-amount_payable"));
    clearTimeout(delayTimer);
    delayTimer = setTimeout(() => {
        let _arrears = _balance - _amount;
        let _amount_to_words = convert_amount_to_Words(`${_amount},${_balance},${_arrears}`).then(function(amount_words) {
            amount_in_words = amount_words;
        });
    }, 700);
});

var save_Receive_Payment = async () => {

    let $balance = parseInt($(`span[class~="outstanding"]`).attr("data-amount_payable")),
        $amount = parseInt($(`div[id="fees_payment_form"] input[name="amount"]`).val()),
        description = $(`div[id="fees_payment_form"] textarea[name="description"]`).val(),
        payment_method = $(`div[id="fees_payment_form"] select[name="payment_method"]`).val(),
        checkout_url = $(`span[class~="outstanding"]`).attr("data-checkout_url"),
        student_id = $(`input[name='fees_payment_student_id']`).val(),
        email_address = $(`input[name="email_address"]`).val(),
        category_id = $(`input[name="fees_payment_category_id"]`).val(),
        t_message = "Make full payment for the outstanding fees.";

    let _arrears = $balance - $amount;

    if (!$(`div[id="fees_payment_form"] input[name="amount"]`).val().length) {
        notify("Sorry! The amount cannot be empty.");
        $(`div[id="fees_payment_form"] input[name="amount"]`).focus();
        return false;
    }

    if ($amount > $balance) {
        notify(`Sorry! You cannot pay more than the outstanding balance of ${amount_in_words[$balance]} (${myPrefs.labels.currency}${$balance})`);
        $(`div[id="fees_payment_form"] input[name="amount"]`).focus();
        return false;
    }

    if ($amount < $balance) {
        t_message = `Are you sure you want to save this payment. 
            An amount of ${amount_in_words[$amount]} (${myPrefs.labels.currency}${$amount}) is been paid which will leave a balance of ${amount_in_words[_arrears]} (${myPrefs.labels.currency}${_arrears}).`;
    }

    var data = {
        "amount": $amount,
        "category_id": category_id,
        "student_id": student_id,
        "description": description,
        "checkout_url": checkout_url,
        "payment_method": payment_method,
        "email_address": email_address,
        "payment_month": $(`select[name="payment_month"]`).val(),
        "contact_number": $(`input[name="contact_number"]`).val()
    };
    if ($(`select[name="payment_method"]`).val() === "cheque") {
        data["bank_id"] = $(`select[name="bank_id"]`).val();
        data["cheque_number"] = $(`input[name="cheque_number"]`).val();
        data["cheque_security"] = $(`input[name="cheque_security"]`).val();

        if (!data["bank_id"].length) {
            notify(`Sorry! Select the bank to proceed.`);
            return false;
        }

        if (!data["cheque_number"].length) {
            notify(`Sorry! Enter the cheque number to proceed.`);
            $(`div[id="arrears_payment_form"] input[name="cheque_number"]`).focus();
            return false;
        }

        if (!data["cheque_security"].length) {
            notify(`Sorry! Enter the cheque security code to proceed.`);
            $(`div[id="arrears_payment_form"] input[name="cheque_security"]`).focus();
            return false;
        }
    }

    swal({
        title: `PAY WITH ${payment_method.toUpperCase()}`,
        text: `${t_message}\nDo you want to proceed to make the payment?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "flex");
            
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
                $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");
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

var log_Momo_Card_Payment = (reference_id, transaction_id) => {

    let amount = parseFloat($(`div[id="fees_payment_form"] input[name="amount"]`).val()),
        description = $(`div[id="fees_payment_form"] textarea[name="description"]`).val(),
        checkout_url = $(`span[class~="outstanding"]`).attr("data-checkout_url"),
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

            if(response.data.additional !== undefined) {
                if(myPrefs.labels.print_receipt !== undefined) {
                    window.open(
                        `${baseUrl}receipt/${response.data.additional.uniqueId}`, `Payment Receipt`,
                        `width=850,height=750,left=200,resizable,scrollbars=yes,status=1,left=${($(window).width())*0.25}`
                    );
                }
            }
            $(`button[id="payment_cancel"]`).addClass("hidden");
            $(`div[id="fees_payment_form"] *`).prop("disabled", true);
            $(`button[id="momocard_payment_button"]`).addClass("hidden");
            $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
            $(`div[id="fees_payment_form"] input, div[id="fees_payment_form"] textarea`).val("");
            load_Pay_Fees_Form();
        }
        $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");
    }).catch(() => {
        $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");
    });

}

var receive_Momo_Card_Payment = async () => {

    let orig_amount = parseFloat($(`div[id="fees_payment_form"] input[name="amount"]`).val()),
        email_address = $(`input[name="email_address"]`).val(),
        subaccount = $(`input[name="client_subaccount"]`).val(),
        balance = parseInt($(`span[class~="outstanding"]`).attr("data-amount_payable")),
        description = $(`div[id="fees_payment_form"] textarea[name="description"]`).val(),
        checkout_url = $(`span[class~="outstanding"]`).attr("data-checkout_url"),
        student_id = $(`input[name='fees_payment_student_id']`).val(),
        category_id = $(`input[name="fees_payment_category_id"]`).val();

    if (orig_amount > balance) {
        notify(`Sorry! You cannot pay more than the outstanding balance of ${balance}`);
        return false;
    }
        
    if (!$(`div[id="fees_payment_form"] input[name="amount"]`).val().length) {
        notify("Sorry! The amount cannot be empty.");
        return false;
    }

    if(!email_address.length) {
        email_address = $(`input[name="client_email_address"]`).val();
    }

    if (!email_address.length) {
        notify("Sorry! The email address section is required.");
        return false;
    }
    
    amount = orig_amount * 100;

    swal({
        title: `PAY WITH MOMO / CARD`,
        text: `Do you want to proceed to make the payment of ${orig_amount} using Mobile Money or Debit Card?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(async (proceed) => {
        if (proceed) {

            let data = {
                subaccount,
                "amount": amount,
                "category_id": category_id,
                "student_id": student_id,
                "description": description,
                "checkout_url": checkout_url,
                "email_address": email_address,
                "contact_number": $(`input[name="contact_number"]`).val()
            };

            await $.post(`${baseUrl}api/fees/log`, {data}).then((response) => {
                if(response.code == 200) {
                    let _t_result = response.data.result;

                    try {

                        var popup = PaystackPop.setup({
                            amount: _t_result.amount,
                            key: _t_result.pk_public_key,
                            email: _t_result.email_address,
                            subaccount: _t_result.subaccount,
                            currency: myPrefs.labels.currency,
                            ref: _t_result.reference_id,
                            onClose: function() {
                                $.post(`${baseUrl}api/fees/epay_validate`).then((response) => {
                                    swal({
                                        text: response.data.result,
                                        icon: responseCode(response.code),
                                    });
                                    $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "none");
                                    if(response.code == 200) {
                                        load_Pay_Fees_Form();
                                    }
                                });
                            },
                            callback: function(response) {
                                let message = `Payment ${response.message}`,
                                    code = "error";
                                if (response.message == "Approved") {
                                    code = "success";
                                    $(`div[id="fees_payment_form"] div[class="form-content-loader"]`).css("display", "flex");
                                    log_Momo_Card_Payment(response.reference, response.transaction);
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
                } else {
                    swal({
                        text: response.data.result,
                        icon: "error",
                    });
                }
            }).catch(() => {
                swal({
                    text: "Error encountered while processing the request.",
                    icon: "error",
                });
            });
        }
    });
}

var freeze_form = () => {
    $(`div[id="fees_payment_history"]`).html(``);
    $(`div[id="fees_payment_form"] *`).prop("disabled", true);
    $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
    $(`div[id="fees_payment_form"] input, div[id="fees_payment_form"] textarea`).val("");
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
            freeze_form();
            $(`select[name="category_id"]`).val("").change();
            $(`button[id="payment_cancel"]`).addClass("hidden");
            $(`div[id="fees_payment_preload"] select[name="payment_month"]`).prop("disabled", true).val("").change();
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
                $category_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val(),
                $amount = $(`div[id="fees_allocation_form"] input[name="amount"]`).val(),
                $payment_module = $(`div[id="fees_allocation_form"] select[name="payment_module"]`).val(),
                $payment_month = $(`div[id="fees_allocation_form"] select[name="payment_month"]`).val();

            let data = {
                "allocate_to": $allot_to,
                "class_id": $class_id,
                "category_id": $category_id,
                "amount": $amount,
                "payment_module": $payment_module,
                "payment_month": $payment_month
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
                    // $(`div[id="fees_allocation_form"] input`).val("");
                    load_Fees_Allocation_Amount();
                    $(`table[id="simple_load_student"] input[type="checkbox"]`).prop('checked', false);
                }
                swal({
                    position: "top",
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

var email_Receipt = (receipt_id) => {

}

var load_Fees_Allocation_Amount = (request = "change") => {
    let $allot_to = $(`div[id="fees_allocation_form"] select[name="allocate_to"]`).val(),
        $class_id = $(`div[id="fees_allocation_form"] select[name="class_id"]`).val(),
        $student_id = $(`div[id="fees_allocation_form"] select[name="student_id"]`).val(),
        $category_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val(),
        $payment_month = $(`div[id="fees_allocation_form"] select[name="payment_month"]`).val(),
        $payment_module = $(`div[id="fees_allocation_form"] select[name="payment_module"]`).val();

    let data = {
        "class_id": $class_id,
        "allocate_to": $allot_to,
        "student_id": $student_id,
        "category_id": $category_id,
        "payment_month": $payment_month,
        "payment_module": $payment_module
    };
    $(`div[id="fees_allocation_wrapper"] input[id="select_all"]`).prop({"disabled": true, "checked": false});
    if ($category_id.length && $category_id !== "null") {
        $(`div[id="payment_month"]`).addClass("hidden");
        $(`div[class="form-content-loader"]`).css("display", "flex");
        $(`div[id="fees_allocation_form"] input[name="amount"]`).prop("disabled", true);
        $.get(`${baseUrl}api/fees/allocated_fees_amount`, data).then((response) => {
            $(`div[id="fees_allocation_form"] input[name="amount"]`).prop("disabled", false);
            if (response.code == 200) {
                $(`div[id="fees_allocation_form"] input[name="amount"]`).val(response.data.result.amount);
                let payment_module = response.data.result.payment_module,
                    payment_month = response.data.result.payment_month;

                if(request == "change") {
                    $(`div[id="fees_allocation_form"] select[name="payment_module"]`).val(payment_module).change();
                }

                if((payment_module == "Monthly") || ($payment_module == "Monthly")) {
                    $(`div[id="payment_month"]`).removeClass("hidden");
                }

                if(response.data.result.students_allocation !== undefined) {
                    if(response.data.result.students_allocation.length) {
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

$(`div[id="fees_allocation_form"] select[name="payment_month"]`).on("change", function () {
    load_Fees_Allocation_Amount("do_not_change");
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

$(`div[id="fees_allocation_wrapper"] input[id="select_all"]`).on("click", function () {
    $(this).parents(`table[id="simple_load_student"]`).find(`input[class="student_ids"]:checkbox`).prop('checked', this.checked);
});

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
                                    <td><strong>Fees Category: </strong></td>
                                    <td>${data.category_name !== null ? data.category_name : data.category_id}</td>
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
                                        <a href="${baseUrl}fees_view/${data.item_id}?redir=${term}" class="btn btn-sm btn-outline-success" title="Click to view full details"><i class="fa fa-eye"></i> View</a>
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

$(`div[id="fees_payment_preload"] select[name="category_id"]`).on("change", function() {
    let category_id = $(this).val(),
        student_id = $(`div[id="fees_allocation_form"] select[name="student_id"]`).val();
        freeze_form();
    if(category_id.length) {
        $(`div[id="fees_payment_preload"] select[name="payment_month"]`).prop("disabled", false);
    } else {
        $(`div[id="fees_payment_preload"] select[name="payment_month"]`).prop("disabled", true).val("").change();
    }
});

$(`div[id="fees_payment_preload"] select[name="payment_month"]`).on("change", function() {
    let payment_month = $(this).val();
    freeze_form();
});

$(`div[id="fees_allocation_form"] select[name="student_id"]`).on("change", function() {
    let value = $(this).val(),
        cat_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val();
    if (value.length && value !== "null" && cat_id.length) {
        load_Fees_Allocation_Amount();
    }
});

$(`div[id="fees_payment_form"] select[name="payment_method"]`).on("change", function() {

    let mode = $(this).val();
    $(`label[class="email_label"]`).html(`Email Address`);
    $(`button[id="momocard_payment_button"]`).addClass("hidden");
    $(`button[id="default_payment_button"]`).removeClass("hidden");
    $(`div[id="payment_amount_input"]`).removeClass("col-md-4").addClass("col-md-6");
    if (mode === "cash") {
        $(`div[id="cheque_payment_filter"]`).addClass("hidden");
    } else if (mode === "cheque") {
        $(`div[id="payment_amount_input"]`).removeClass("col-md-6").addClass("col-md-4");
        $(`div[id="cheque_payment_filter"]`).removeClass("hidden");
    } else if (mode === "momo_card") {
        $(`button[id="momocard_payment_button"]`).removeClass("hidden");
        $(`label[class="email_label"]`).html(`Email Address <span class="required">*</span>`);
        $(`button[id="default_payment_button"], div[id="cheque_payment_filter"]`).addClass("hidden");
    }

});
$(`div[id="fees_payment_form"] input[name="amount"]`).focus();

if($(`input[id='auto_load_form']`).length) {
    load_Pay_Fees_Form();
}