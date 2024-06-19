var save_Receive_Payment = () => {

    let balance = parseFloat($(`div[id="arrears_payment_form"] input[name="outstanding"]`).val()),
        amount = parseFloat($(`div[id="arrears_payment_form"] input[name="amount"]`).val()),
        payment_method = $(`div[id="arrears_payment_form"] select[name="payment_method"]`).val(),
        checkout_url = $(`span[class~="outstanding"]`).attr("data-checkout_url"),
        student_id = $(`div[id="arrears_payment_form"] input[name='arrears_student_id']`).val(),
        email_address = $(`input[name="email_address"]`).val(),
        t_message = "Make full payment for the outstanding fees arrears.";
    
    if (!$(`div[id="arrears_payment_form"] input[name="amount"]`).val().length) {
        notify("Sorry! The amount cannot be empty.");
        $(`div[id="arrears_payment_form"] input[name="amount"]`).focus();
        return false;
    }

    if (amount > balance) {
        notify(`Sorry! You cannot pay more than the outstanding balance of ${balance}`);
        $(`div[id="arrears_payment_form"] input[name="amount"]`).focus();
        return false;
    }

    if (amount < balance) {
        t_message = `Are you sure you want to save this payment. 
            An amount of ${amount} is been paid which will leave a balance of ${balance-amount}.`;
    }

    var data = {
        "amount": amount,
        "student_id": student_id,
        "checkout_url": checkout_url,
        "payment_method": payment_method,
        "email_address": email_address,
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
            $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "flex");

            $.post(`${baseUrl}api/arrears/make_payment`, data).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code === 200) {
                    if(response.data.additional.payment_id !== undefined) {
                        window.open(
                            `${baseUrl}receipt/${response.data.additional.payment_id}`, `Payment Receipt`,
                            `width=850,height=750,left=200,resizable,scrollbars=yes,status=1,left=${($(window).width())*0.25}`
                        );
                    }
                    setTimeout(() => {
                        loadPage(`${baseUrl}arrears/${student_id}`);
                    }, refresh_seconds);
                }
                $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
            }).catch(() => {
                $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
                swal({
                    text: "Sorry! There was an error while trying to process the request.",
                    icon: "error",
                });
            });
        }
    });
}

var log_Momo_Card_Payment = (reference_id, transaction_id) => {

    let amount = parseFloat($(`div[id="arrears_payment_form"] input[name="amount"]`).val()),
        student_id = $(`input[name='arrears_student_id']`).val(),
        email_address = $(`input[name="email_address"]`).val();

    let data = {
        "amount": amount,
        "student_id": student_id,
        "email_address": email_address,
        "reference_id": reference_id,
        "transaction_id": transaction_id,
        "contact_number": $(`input[name="contact_number"]`).val()
    };

    $.post(`${baseUrl}api/arrears/momocard_payment`, data).then((response) => {
        swal({ text: response.data.result, icon: responseCode(response.code) });
        if (response.code == 200) {
            $(`button[id="payment_cancel"]`).addClass("hidden");
            $(`div[id="arrears_payment_form"] *`).prop("disabled", true);
            $(`button[id="momocard_payment_button"]`).addClass("hidden");
            $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
            $(`div[id="arrears_payment_form"] input, div[id="arrears_payment_form"] textarea`).val("");
            setTimeout(() => {
                load_debtor_details();
            }, refresh_seconds);
        }
        $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
    }).catch(() => {
        $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
    });

}

var receive_Momo_Card_Payment = async () => {

    let orig_amount = parseFloat($(`div[id="arrears_payment_form"] input[name="amount"]`).val()),
        email_address = $(`div[id="arrears_payment_form"] input[name="email_address"]`).val(),
        subaccount = $(`div[id="arrears_payment_form"] input[name="client_subaccount"]`).val(),
        balance = parseFloat($(`div[id="arrears_payment_form"] input[name="outstanding"]`).val()),
        student_id = $(`input[name='arrears_student_id']`).val();

    if (orig_amount > balance) {
        notify(`Sorry! You cannot pay more than the outstanding balance of ${balance}`);
        return false;
    }

    if (!$(`div[id="arrears_payment_form"] input[name="amount"]`).val().length) {
        notify("Sorry! The amount cannot be empty.");
        return false;
    }
    if (!email_address.length) {
        notify("Sorry! The email address section is required.");
        return false;
    }
    amount = orig_amount * 100;

    var data = {
        subaccount,
        "type": "arrears",
        "amount": amount,
        "student_id": student_id,
        "email_address": email_address,
        "contact_number": $(`input[name="contact_number"]`).val()
    };

    swal({
        title: `PAY WITH MOMO / CARD`,
        text: `Do you want to proceed to make the payment of ${orig_amount} using Mobile Money or Debit Card?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/fees/log`, {data}).then((response) => {
                if(response.code == 200) {

                    let _t_result = response.data.result;
                    var popup = PaystackPop.setup({
                        amount: _t_result.amount,
                        key: _t_result.pk_public_key,
                        email: _t_result.email_address,
                        subaccount: _t_result.subaccount,
                        currency: myPrefs.labels.currency,
                        ref: _t_result.reference_id,
                        onClose: function() {
                            $.post(`${baseUrl}api/arrears/epay_validate`).then((response) => {
                                swal({
                                    text: response.data.result,
                                    icon: responseCode(response.code),
                                });
                                $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
                                if(response.code == 200) {
                                    $(`div[id="arrears_payment_form"] input[name="amount"]`).val("");
                                    setTimeout(() => {
                                        load_debtor_details();
                                    }, refresh_seconds)
                                }
                            });
                        },
                        callback: function(response) {
                            let message = `Payment ${response.message}`,
                                code = "error";
                            if (response.message == "Approved") {
                                code = "success";
                                $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "flex");
                                log_Momo_Card_Payment(response.reference, response.transaction);
                            } else {
                                swal({ text: message, icon: code });
                                $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
                            }
                        }
                    });

                    popup.openIframe();
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

var generate_payment_report = (student_id) => {
    let category_id = $(`select[id="category_id"]`).val(),
        start_date = $(`input[name="group_start_date"]`).val(),
        end_date = $(`input[name="group_end_date"]`).val();
    window.open(`${baseUrl}receipt?category_id=${category_id}&start_date=${start_date}&end_date=${end_date}`);
}

var delete_arrears = (student_id, academic_key) => {
    swal({
        title: "Delete Arrears Record",
        text: "Are you sure you want to delete this arrears record?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $(`div[id="filter_Department_Class"] div[class="form-content-loader"]`).css("display", "flex");
            $.post(`${baseUrl}api/arrears/delete`, {student_id, academic_key}).then((response) => {
                $(`div[id="filter_Department_Class"] div[class="form-content-loader"]`).css("display", "none");
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(response.code == 200) {
                    load_Student_Arrears();
                }
            }).catch(() => {
                $(`div[id="filter_Department_Class"] div[class="form-content-loader"]`).css("display", "none");
            });
        }
    });
}

var load_Student_Arrears = () => {
    let data = {
        "student_id": $(`div[id="arrearsForm"] select[name="student_id"]`).val(),
        "class_id": $(`div[id="arrearsForm"] select[name="class_id"]`).val(),
        "populate": true
    };
    if(data.class_id.length < 1) {
        notify("Sorry! Please select the class to continue");
        return false;
    }
    if(data.student_id.length < 1) {
        notify("Sorry! Please select the student to continue");
        return false;
    }
    $(`table[id="student_Fees_Arrears"] tbody`).html(`<tr><td colspan="3" align="center">Student Record Appears Here.</td></tr>`);
    $(`div[class="change_history"]`).html(``);
    $(`button[data-target="addStudentArrears"]`).addClass("hidden");
    $(`div[id="filter_Department_Class"] div[class="form-content-loader"]`).css("display", "flex");
    $.get(`${baseUrl}api/arrears/list`, {data}).then((response) => {
        $(`div[id="filter_Department_Class"] div[class="form-content-loader"]`).css("display", "none");
        if(response.code == 200) {
            $(`button[data-target="addStudentArrears"]`).removeClass("hidden");
            if(response.data.result.arrears_table.length) {
                $(`table[id="student_Fees_Arrears"] tbody`).html(response.data.result.arrears_table);
                let change_history = "<table class='table'><thead><tr><th>#</th><th>Description</th><th>Date</th></tr></thead><tbody>";
                    $.each(response.data.result.change_history, function(i, e) {
                        change_history += `<tr><td>${i+1}</td><td>${e.description}</td><td>${e.date_created}</td></tr>`;
                    });
                    change_history += "</tbody></table>";
                $(`div[class="change_history"]`).html(change_history);
            } else {
                $(`table[id="student_Fees_Arrears"] tbody`).html(`
                    <tr><td colspan="3" align="center">No record was found for this student.</td></tr>
                `);
            }
        } else {
            notify(response.data.result);
        }
    }).catch(() => {
        $(`div[id="filter_Department_Class"] div[class="form-content-loader"]`).css("display", "none");
    });
}

var quick_Add_Arrears = () => {
    let data = {
        "student_id": $(`div[id="arrearsForm"] select[name="student_id"]`).val(),
        "class_id": $(`div[id="arrearsForm"] select[name="class_id"]`).val(),
        "academic_year": $(`div[id="addStudentArrears"] select[name="academic_year"]`).val(),
        "academic_term": $(`div[id="addStudentArrears"] select[name="academic_term"]`).val(),
        "category_id": $(`div[id="addStudentArrears"] select[name="category_id"]`).val(),
        "amount": $(`div[id="addStudentArrears"] input[name="amount"]`).val(),
    };
    if(data.class_id.length < 1) {
        notify("Sorry! Please select the class to continue");
        return false;
    }
    if(data.student_id.length < 1) {
        notify("Sorry! Please select the student to continue");
        return false;
    }
    if(data.academic_year.length < 1) {
        notify("Sorry! Please select the academic year to continue");
        return false;
    }
    if(data.academic_term.length < 1) {
        notify("Sorry! Please select the academic term to continue");
        return false;
    }
    if(data.category_id.length < 1) {
        notify("Sorry! Please select the fees category to continue");
        return false;
    }
    if(data.amount.length < 1) {
        notify("Sorry! Please enter the amount to continue");
        return false;
    }
    swal({
        title: "Log Fees Arrears",
        text: "Are you sure you save this student's fees arrears record?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $(`div[id="addStudentArrears"] div[class="form-content-loader"]`).css("display", "flex");
            $.post(`${baseUrl}api/arrears/add`, {data}).then((response) => {
                $(`div[id="addStudentArrears"] div[class="form-content-loader"]`).css("display", "none");
                if(response.code == 200) {
                    $(`div[id="addStudentArrears"]`).modal("hide");
                    $(`button[data-target="addStudentArrears"]`).addClass("hidden");
                    load_Student_Arrears();
                } else {
                    notify(response.data.result);
                }
            }).catch(() => {
                $(`div[id="addStudentArrears"] div[class="form-content-loader"]`).css("display", "none");
            });
        }
    });
}

var load_debtor_details = () => {
    let amount = $(`div[id="arrears_payment_form"] input[name="amount"]`).val(),
        student_id = $(`select[name="debtor_id"]`).val(),
        msg = "";
    
    if(!student_id.length) {
        notify("Sorry! Please select student to proceed.");
        return false;
    }
    if (amount.length) {
        msg = "You have not finished processing the current request. Are you sure you want to proceed to refresh the page?";
        swal({
            title: "Reload Page",
            text: `${msg}`,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((proceed) => {
            if (proceed) {
                loadPage(`${baseUrl}arrears/${student_id}`);
            }
        });
    } else {
        loadPage(`${baseUrl}arrears/${student_id}`);
    }
}

$(`div[id="fees_arrears_list"] select[id="debtor_id"]`).on("change", function() {
    $(`div[id="fees_arrears_payment"]`).addClass("hidden");
    $(`div[id="change_notification"]`).removeClass("hidden");
    $(`div[id="arrears_payment_form"] input[name="amount"]`).val(``);
});

$(`div[id="arrearsForm"] select[name="class_id"]`).on("change", function() {
    $(`table[id="student_Fees_Arrears"] tbody`).html(`<tr><td colspan="3" align="center">Student Record Appears Here.</td></tr>`);
    $(`div[class="change_history"]`).html(``);
});

$(`div[id="arrearsForm"] select[name="student_id"]`).on("change", function() {
    $(`table[id="student_Fees_Arrears"] tbody`).html(`<tr><td colspan="3" align="center">Student Record Appears Here.</td></tr>`);
    $(`div[class="change_history"]`).html(``);
    load_Student_Arrears();
});

$(`button[data-target="addStudentArrears"]`).on("click", function() { 
    $(`div[id="addStudentArrears"]`).modal("show");
    $(`div[id="addStudentArrears"] input`).val(``);
});

$(`button[data-target="quickStudentAdd"]`).on("click", function() { 
    $(`div[id="quickStudentAdd"]`).modal("show");
    $(`div[id="quickStudentAdd"] input`).val(``);
});

$(`div[id="arrears_payment_form"] select[name="payment_method"]`).on("change", function() {
    let mode = $(this).val();
    $(`div[id="arrears_payment_form"] label[class="email_label"]`).html(`Email Address`);
    $(`div[id="arrears_payment_form"] button[id="momocard_payment_button"]`).addClass("hidden");
    $(`div[id="arrears_payment_form"] button[id="default_payment_button"]`).removeClass("hidden");
    if (mode === "cash") {
        $(`div[id="cheque_payment_filter"]`).addClass("hidden");
        $(`div[id="arrears_payment_form"] input[name="amount"]`).focus();
    } else if (mode === "cheque") {
        $(`div[id="arrears_payment_form"] input[name="cheque_number"]`).focus();
        $(`div[id="cheque_payment_filter"]`).removeClass("hidden");
    } else if (mode === "momo_card") {
        $(`div[id="arrears_payment_form"] input[name="amount"]`).focus();
        $(`div[id="arrears_payment_form"] button[id="momocard_payment_button"]`).removeClass("hidden");
        $(`div[id="arrears_payment_form"] label[class="email_label"]`).html(`Email Address <span class="required">*</span>`);
        $(`div[id="arrears_payment_form"] button[id="default_payment_button"], div[id="cheque_payment_filter"]`).addClass("hidden");
    }
});
$(`div[id="arrears_payment_form"] input[name="amount"]`).focus();

$(`button[id="filter_Fees_Arrears"]`).on("click", function() {
    let percentage_from = $(`select[name="percentage_from"]`).val(),
        percentage_to = $(`select[name="percentage_to"]`).val(),
        class_id = $(`select[name="class_id"]`).val();
    $.form_data = { percentage_from, percentage_to, class_id };
    if((class_id == undefined) || (!class_id.length)) {
        notify("Sorry! Please select the class to continue");
    } else {
        loadPage(`${baseUrl}debtors`);
    }
});