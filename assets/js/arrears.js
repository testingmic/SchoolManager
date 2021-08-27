var save_Receive_Payment = () => {

    let balance = parseFloat($(`div[id="arrears_payment_form"] input[name="outstanding"]`).val()),
        amount = parseFloat($(`div[id="arrears_payment_form"] input[name="amount"]`).val()),
        payment_method = $(`div[id="arrears_payment_form"] select[name="payment_method"]`).val(),
        checkout_url = $(`span[class~="outstanding"]`).attr("data-checkout_url"),
        student_id = $(`div[id="arrears_payment_form"] input[name='arrears_student_id']`).val(),
        email_address = $(`input[name="email_address"]`).val(),
        t_message = "";
    
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

    swal({
        title: "Make Payment",
        text: `${t_message}\nDo you want to proceed to make the payment?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "flex");
            let data = {
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
            }

            $.post(`${baseUrl}api/arrears/make_payment`, data).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code === 200) {
                    setTimeout(() => {
                        loadPage(`${baseUrl}arrears/${student_id}`);
                    }, 1000);
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
        student_id = $(`input[name='fees_payment_student_id']`).val(),
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
        if (response.code == 200) {
            $(`button[id="payment_cancel"]`).addClass("hidden");
            $(`div[id="arrears_payment_form"] *`).prop("disabled", true);
            $(`button[id="momocard_payment_button"]`).addClass("hidden");
            $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
            $(`div[id="arrears_payment_form"] input, div[id="arrears_payment_form"] textarea`).val("");
        }
        $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
    }).catch(() => {
        $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
    });

}

var receive_Momo_Card_Payment = () => {

    try {

        let amount = parseFloat($(`div[id="arrears_payment_form"] input[name="amount"]`).val()),
            email_address = $(`div[id="arrears_payment_form"] input[name="email_address"]`).val(),
            subaccount = $(`div[id="arrears_payment_form"] input[name="client_subaccount"]`).val(),
            balance = parseFloat($(`div[id="arrears_payment_form"] input[name="outstanding"]`).val());

        if (amount > balance) {
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
        amount = amount * 100;

        var popup = PaystackPop.setup({
            key: pk_payment_key,
            email: email_address,
            amount: amount,
            subaccount: subaccount,
            currency: myPrefs.labels.currency,
            onClose: function() {
                $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
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
                    $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "flex");
                    log_Momo_Card_Payment(response.reference, response.transaction);
                } else {
                    swal({ text: message, icon: code });
                    $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
                }
            }
        });

        popup.openIframe();
    } catch (e) {
        $(`div[id="arrears_payment_form"] div[class="form-content-loader"]`).css("display", "none");
        swal({
            text: "Connection Failed! Please check your internet connection to proceed.",
            icon: "error",
        });
    }

}

var generate_payment_report = (student_id) => {
    let category_id = $(`select[id="category_id"]`).val(),
        start_date = $(`input[name="group_start_date"]`).val(),
        end_date = $(`input[name="group_end_date"]`).val();
    window.open(`${baseUrl}receipt?category_id=${category_id}&start_date=${start_date}&end_date=${end_date}`);
}

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