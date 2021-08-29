var topup_sms_balance = () => {
    let client_id = $(`input[id="client_id"]`).val(),
        topup = parseInt($(`input[name="sms_topup"]`).val());
    
    if(isNaN(topup)) {
        notify("The sms topup must be a valid numeric integer.");
        return false;
    }
    let data = {
        "client_id": client_id,
        "topup": topup,
        "action": "sms_topup"
    };

    swal({
        title: "SMS Topup",
        text: `Are you sure you to proceed to add ${topup} SMS Units to this Account?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/account/modify`, {data}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.data.additional.href !== undefined) {
                    setTimeout(() => {
                        loadPage(response.data.additional.href);
                    }, refresh_seconds);
                }
            }).catch(() => {});
        }
    });
}