'use strict';
var notify = (text, type = "error") => {
    $.notify(text, type);
}

var validate_payment = (reference_id, transaction_id) => {
    let data = { "reference_id": reference_id,"transaction_id": transaction_id };
    $.post(`${baseUrl}api/fees/momocard_payment`, data).then((response) => {
        if (response.code == 200) {
            notify(`Fees payment was successful.`, `success`);
        }
        $(`div[id="make_fee_payment"] div[id="loader"]`).css("display", "none");
    }).catch(() => {
        $(`div[id="make_fee_payment"] div[id="loader"]`).css("display", "none");
    });
}

var make_fee_payment = () => {
    var email = $(`div[id="make_fee_payment"] input[name="email"]`).val();
    var contact = $(`div[id="make_fee_payment"] input[name="contact"]`).val();
    var amount = parseFloat($(`div[id="make_fee_payment"] input[name="amount"]`).val());
    var param = JSON.parse($(`div[id="make_fee_payment"] input[name="payment_param"]`).val());
    var outstanding = parseFloat($(`div[id="make_fee_payment"] input[name="outstanding"]`).val());
    
    if(!email.length) {
        notify(`Please specify a valid email address.`);
        $(`div[id="make_fee_payment"] input[name="email"]`).focus();
    }
    else if(!contact.length) {
        notify(`Please specify a valid contact number.`);
        $(`div[id="make_fee_payment"] input[name="contact"]`).focus();
    } else if(isNaN(amount)) {
        notify(`Please specify a valid amount.`);
        $(`div[id="make_fee_payment"] input[name="amount"]`).focus();
    } else if(amount > outstanding) { 
        notify(`Sorry! The amount to be paid must not exceed the oustanding balance.`);
        $(`div[id="make_fee_payment"] input[name="amount"]`).focus();
    } else {
        $(`div[id="make_fee_payment"] div[id="loader"]`).css("display", "flex");
        $.post(`${baseUrl}api/payment/pay`, {email, contact, amount, param}).then((response) => {
            if(response.code == 200) {
                var popup = PaystackPop.setup({
                    key: response.payment_key,
                    email: response.email,
                    amount: response.amount,
                    currency: response.currency,
                    subaccount: response.subaccount,
                    ref: response.reference,
                    onClose: function() {
                        $(`div[id="make_fee_payment"] div[id="loader"]`).css("display", "none");
                        notify(`Payment Process Cancelled.`);
                    },
                    callback: function(data) {
                        let message = `Payment ${data.message}`,
                            code = "error";
                        if (data.message == "Approved") {
                            code = "success";
                            validate_payment(data.reference, data.transaction);
                        } else {
                            notify(message, code);
                            $(`div[id="make_fee_payment"] div[id="loader"]`).css("display", "none");
                        }
                    }
                });
                popup.openIframe();
            } else {
                notify(response.data.result);
            }
            $(`div[id="make_fee_payment"] div[id="loader"]`).css("display", "none");
        }).catch(() => {
            $(`div[id="make_fee_payment"] div[id="loader"]`).css("display", "none");
            notify(`Sorry! An Error occured while processing the request.`);
        });
    }
}