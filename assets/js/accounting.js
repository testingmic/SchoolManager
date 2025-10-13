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
            $(`div[id="accounts_form"] select[name="account_bank"]`).val("").change();
            $(`div[id="accounts_form"] input[name="opening_balance"]`).attr("disabled", false);
            $(`div[id="accounts_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}${form_url}`);
        }
    });
}

var view_bTranc = (transaction_id) => {
    if($.array_stream["bank_transactions_array"] !== undefined) {
        let transaction = $.array_stream["bank_transactions_array"];
        if (transaction[transaction_id] !== undefined) {
            let data = transaction[transaction_id];

            $(`div[id="viewOnlyModal"]`).modal("show");
            $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(`Bank Transaction Details`);

            let content = `
                <div class="row">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-bordered table-md table-striped">
                            <tr>
                                <td class="font-weight-bold" width="35%">Bank Name</td>
                                <td>${data.bank_name}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold" width="35%">Account Name</td>
                                <td>${data.account_name}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Account Number</td>
                                <td>${data.account_number}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Amount</td>
                                <td>${data.amount}</td>
                            </tr>
                            ${
                                data.reference_id !== null ? 
                                `<tr>
                                    <td class="font-weight-bold">Reference</td>
                                    <td>${data.reference_id}</td>
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
                                <td class="font-weight-bold">Academic Term</td>
                                <td>${data.academic_term}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Academic Year</td>
                                <td>${data.academic_year}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Recorded By</td>
                                <td>
                                    <div><i class="fa fa-user"></i> ${data?.createdby_info?.name}</div>
                                    <div><i class="fa fa-phone"></i> ${data?.createdby_info?.phone_number}</div>
                                    <div><i class="fa fa-envelope"></i> ${data?.createdby_info?.email}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Record Date</td>
                                <td>${data.date_created}</td>
                            </tr>
                            ${
                                data.attachment_html !== null ? 
                                `<tr>
                                    <td class="font-weight-bold">Attachments</td>
                                    <td>${data.attachment_html}</td>
                                </tr>` : ""
                            }
                        </table>
                    </div>
                </div>`;

            $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html($.parseHTML(content));

        }
    }
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
                        <table class="table table-bordered table-md table-striped">
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
                            ${
                                data.attach_to_object !== null ? 
                                `<tr>
                                    <td class="font-weight-bold">Received From</td>
                                    <td>${data.assign_to_object_name} - ${data.assign_to_object_unique_id}</td>
                                </tr>` : ""
                            }
                            <tr>
                                <td class="font-weight-bold">Payment Medium</td>
                                <td>${data.payment_medium.toUpperCase()}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Transaction Date</td>
                                <td>${data.record_date}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Academic Term</td>
                                <td>${data.academic_term}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Academic Year</td>
                                <td>${data.academic_year}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Status</td>
                                <td>${data.state_label}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Recorded By</td>
                                <td>
                                    <div><i class="fa fa-user"></i> ${data?.createdby_info?.name}</div>
                                    <div><i class="fa fa-phone"></i> ${data?.createdby_info?.phone_number}</div>
                                    <div><i class="fa fa-envelope"></i> ${data?.createdby_info?.email}</div>
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
            $(`div[id="accounts_form"] select[name="account_bank"]`).val(type.account_bank).change();
            $(`div[id="accounts_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}api/accounting/update_account`);
        }
    }
}

var close_account = (account_id) => {
    swal({
        title: `Close Account`,
        text: `You have opted to set this Account as the Default Primary Account. By doing so, all fees receivables and salary payment (under the payroll section) will be logged in this account.
            Do you wish to proceed?`,
        icon: `warning`,
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/accounting/set_primary_account`, {account_id}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(response.code == 200) {
                    $(`span[class='default_account']`).html(``);
                    $(`span[class='default_account_button'][data-account_id='${account_id}']`).html(``);
                    $(`span[class='default_account'][data-account_id='${account_id}']`).html(`<span title='Default Primary Account' class='text-success'><i class='fa fa-check-circle'></i></span>`);

                    $.each($(`span[class='default_account_button']`), function() {
                        let _account_id = $(this).attr("data-account_id");
                        if(_account_id !== account_id) {
                            $(`span[class='default_account_button'][data-account_id='${_account_id}']`).html(`
                                <button onclick='return mark_as_default("${_account_id}")' data-account_id='${_account_id}' class='btn mb-1 btn-primary btn-sm'>Set As Default</button>
                            `);
                        }
                    });
                }
            }).catch(() => {
                swal({text: swalnotice["ajax_error"], icon: "error"});
            });
        }
    });
}

var mark_as_default = (account_id) => {
    swal({
        title: `Set as Primary Account`,
        text: `
            You have opted to set this Account as the Default Primary Account. By doing so, all fees receivables and salary payment (under the payroll section) will be logged in this account.
            Do you wish to proceed?`,
        icon: `warning`,
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/accounting/set_primary_account`, {account_id}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(response.code == 200) {
                    $(`span[class='default_account']`).html(``);
                    $(`span[class='default_account_button'][data-account_id='${account_id}']`).html(``);
                    $(`span[class='default_account'][data-account_id='${account_id}']`).html(`<span title='Default Primary Account' class='text-success'><i class='fa fa-check-circle'></i></span>`);

                    $.each($(`span[class='default_account_button']`), function() {
                        let _account_id = $(this).attr("data-account_id");
                        if(_account_id !== account_id) {
                            $(`span[class='default_account_button'][data-account_id='${_account_id}']`).html(`
                                <button onclick='return mark_as_default("${_account_id}")' data-account_id='${_account_id}' class='btn mb-1 btn-primary btn-sm'>Set As Default</button>
                            `);
                        }
                    });
                }
            }).catch(() => {
                swal({text: swalnotice["ajax_error"], icon: "error"});
            });
        }
    });
}

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

var incomeAndExpenseChart = (statistics) => {
    if ($(`div[id="income_and_expense"]`).length) {

        $(`div[data-chart="income_and_expense"]`).html(``);
        $(`div[data-chart="income_and_expense"]`).html(`<div id="income_and_expense" style="width:100%;max-height:405px;height:405px;"></div>`);

        var revenue_flow_chart_options = {
            chart: {
                height: 400,
                type: 'area',
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2,
            },
            series: statistics.data,
            xaxis: {
                type: 'datetime',
                categories: statistics.labels,
                labels: {
                    style: {
                        colors: '#9aa0ac',
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        color: '#9aa0ac',
                    }
                }
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yyyy'
                },
            }
        }

        var revenue_flow_chart = new ApexCharts(
            document.querySelector("#income_and_expense"),
            revenue_flow_chart_options
        );

        revenue_flow_chart.render();

    }
    if($(`canvas[id="bus_financials"]`).length) {
        $(`div[data-chart="bus_financials_chart"]`).html(`<canvas class="height-full" style="max-height:420px;height:420px;" id="bus_financials"></canvas>`);
        var ctx = document.getElementById("bus_financials").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: statistics.buses.labels,
                datasets: [{
                    label: 'Bus Financials',
                    data: statistics.buses.data,
                    backgroundColor: ['#304ffe', '#ffa601', '#fc544b', '#63ed7a', '#191d21', '#e83e8c', '#6777ef'],
                    borderColor: ['#fff', '#fff', '#fff']
                }]
            },
            options: {
                responsive: true,
                cutoutPercentage: 70,
                maintainAspectRatio: false,
                legend: {
                    position: "bottom",
                    display: true
                }
            }
        });
    }
}

if($(`div[data-summary="bus_financials"]`).length) {
    $.get(`${baseUrl}api/buses/financials`).then((response) => {
        if(response.code == 200) {
            let statistics = response.data.result.statistics;
            $.each(statistics.summation_by_type, (key, value) => {
                $(`div[data-summary="bus_financials"] [data-summary="${key}"]`).html(`${formatMoney(value)}`);
            });
            incomeAndExpenseChart(statistics.charts);
        }
    }).catch((error) => {
        console.log(error);
    });
}

$(`div[id="transactions_list"] button[id="filter_Transaction"]`).on("click", function() {
    let item = $(this).attr(`data-type`);
    let account_id = $(`div[id="transactions_list"] select[name="account_id"]`).val(),
        account_type = $(`div[id="transactions_list"] select[name="account_type"]`).val(),
        date_range = $(`div[id="transactions_list"] input[name="date_range"]`).val();
    $.form_data = { account_id, account_type, date_range };
    loadPage(`${baseUrl}${item}`);
});

$(`input[id="all_delete"]`).on("click", function() {
    if ($(this).is(':checked')) {
        $(`input[class~="cb_delete"]`).prop("checked", true);
    } else {
        $(`input[class~="cb_delete"]`).prop("checked", false);
    }
});