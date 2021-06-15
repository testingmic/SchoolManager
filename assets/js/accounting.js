var reset_account_form = (form_url, title = "Add Account Type Head") => {
    swal({
        title: "Cancel Form",
        text: "Are you sure you want to cancel this form?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`form[class="ajax-data-form"] input, form[class="ajax-data-form"] textarea`).val("");
            $(`div[id="accounts_form"] select[name="account_type"]`).val("").change();
            $(`div[id="accounts_form"] [class="card-header"]`).html(title);
            $(`div[id="accounts_form"] input[name="opening_balance"]`).attr("disabled", false);
            $(`div[id="accounts_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}${form_url}`);
        }
    });

}

var view_transaction = (transaction_id) => {
    if ($.array_stream["transactions_array_list"] !== undefined) {
        let transaction = $.array_stream["transactions_array_list"];
        if (transaction[transaction_id] !== undefined) {
            let data = transaction[transaction_id];

            $(`div[id="viewOnlyModal"]`).modal("show");
            $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(`Transaction Details`);

            let content = `
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td class="font-weight-bold" width="35%">Account Name</td>
                                <td>${data.account_name}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Account Type</td>
                                <td>${data.account_type_name}</td>
                            </tr>
                            ${
                                data.reference !== null ? 
                                `<tr>
                                    <td class="font-weight-bold">Reference</td>
                                    <td>${data.reference}</td>
                                </tr>` : ""
                            }
                            ${
                                data.description !== null ? 
                                `<tr>
                                    <td class="font-weight-bold">Description</td>
                                    <td>${data.description}</td>
                                </tr>` : ""
                            }
                            <tr>
                                <td class="font-weight-bold">Amount</td>
                                <td>${data.amount}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Payment Medium</td>
                                <td>${data.payment_medium.toUpperCase()}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Transaction Date</td>
                                <td>${data.record_date}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Status</td>
                                <td>${data.state_label}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Recorded By</td>
                                <td>
                                    <div><i class="fa fa-user"></i> ${data.createdby_info.name}</div>
                                    <div><i class="fa fa-phone"></i> ${data.createdby_info.phone_number}</div>
                                    <div><i class="fa fa-envelope"></i> ${data.createdby_info.email}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Record Date</td>
                                <td>${data.date_created}</td>
                            </tr>
                        </table>
                    </div>
                </div>`;

            $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html($.parseHTML(content));
        }
    }
}

var update_account_type = (type_id) => {
    if ($.array_stream["account_headtype_array"] !== undefined) {
        let account_head = $.array_stream["account_headtype_array"];
        if (account_head[type_id] !== undefined) {
            let type = account_head[type_id];
            $(`div[id="accounts_form"] [class="card-header"]`).html("Update Account Type Head");
            $(`div[id="accounts_form"] input[name="name"]`).val(type.name);
            $(`div[id="accounts_form"] input[name="type_id"]`).val(type_id);
            $(`div[id="accounts_form"] select[name="account_type"]`).val(type.type).change();
            $(`div[id="accounts_form"] textarea[name="description"]`).val(type.description);
            $(`div[id="accounts_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}api/accounting/update_accounttype`);
        }
    }
}

var update_bank_account = (account_id) => {
    if ($.array_stream["bank_accounts_array"] !== undefined) {
        let account = $.array_stream["bank_accounts_array"];
        if (account[account_id] !== undefined) {
            let type = account[account_id];
            $(`div[id="accounts_form"] [class="card-header"]`).html("Update Account");
            $(`div[id="accounts_form"] input[name="opening_balance"]`).val(type.opening_balance).attr("disabled", true);
            $(`div[id="accounts_form"] input[name="account_number"]`).val(type.account_number);
            $(`div[id="accounts_form"] textarea[name="description"]`).val(type.description);
            $(`div[id="accounts_form"] input[name="account_name"]`).val(type.account_name);
            $(`div[id="accounts_form"] input[name="account_id"]`).val(account_id);
            $(`div[id="accounts_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}api/accounting/update_account`);
        }
    }
}

$(`input[id="all_delete"]`).on("click", function() {
    if ($(this).is(':checked')) {
        $(`input[class~="cb_delete"]`).prop("checked", true);
    } else {
        $(`input[class~="cb_delete"]`).prop("checked", false);
    }
});
