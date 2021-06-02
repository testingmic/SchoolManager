var reset_account_form = (form_url, title = "Add Account Type Head") => {
    $(`form[class="ajax-data-form"] input`).val("");
    $(`div[id="accounts_form"] select[name="account_type"]`).val("").change();
    $(`div[id="accounts_form"] [class="card-header"]`).html(title);
    $(`div[id="accounts_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}${form_url}`);
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
            $(`div[id="accounts_form"] input[name="opening_balance"]`).val(type.opening_balance);
            $(`div[id="accounts_form"] input[name="account_number"]`).val(type.account_number);
            $(`div[id="accounts_form"] textarea[name="description"]`).val(type.description);
            $(`div[id="accounts_form"] input[name="account_name"]`).val(type.account_name);
            $(`div[id="accounts_form"] input[name="account_id"]`).val(account_id);
            $(`div[id="accounts_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}api/accounting/update_account`);
        }
    }
}