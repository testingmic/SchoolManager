var reverse_payment = (payment_id, student_name, amount_paid) => {
    swal({
        title: "Reverse Fees Payment",
        text: `Are you sure you want to reverse the Fees Paid by ${student_name} of an amount ${amount_paid}?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/fees/reverse`, {payment_id}).then((response) => {
                $.pageoverlay.hide();
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(response.code == 200) {
                    $(`span[data-action_id="${payment_id}"]`).html(`REVERSED`).addClass("badge font-bold badge-danger");
                }
            }).catch(() => {
                $.pageoverlay.hide();
            });
        }
    });
}

var reverse_transaction = (transaction_id, item_name, amount_paid) => {
    swal({
        title: "Reverse Transaction",
        text: `Are you sure you want to reverse this Transaction: ${item_name} with an amount of ${amount_paid}?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/accounting/reverse`, {transaction_id}).then((response) => {
                $.pageoverlay.hide();
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(response.code == 200) {
                    $(`span[data-action_id="${transaction_id}"]`).html(`REVERSED`).addClass("badge font-bold badge-danger");
                }
            }).catch(() => {
                $.pageoverlay.hide();
            });
        }
    });
}