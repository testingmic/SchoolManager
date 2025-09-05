var filter = $(`[id="reports_insight"] button[id="filter_Fees_Report"]`),
    bg_colors = [
        "l-bg-green", "l-bg-orange", "l-bg-purple", "l-bg-red", "l-bg-cyan",
        "l-bg-yellow", "l-bg-purple-dark", "bg-deep-orange", "bg-brown", "bg-pink",
        "bg-indigo", "bg-teal", "bg-light-blue", "bg-info", "bg-warning", "bg-dark"
    ],
    colors_bank = [
        '#5b71efff', '#ffa601', '#fc544b', '#63ed7a', 
        '#191d21', '#e83e8c', '#6777ef', '#fac576ff', 
        '#ef4fb5ff', '#3f51b5', '#f44336', '#024f05',
        '#b5f740', '#a84cb8ff', '#82d3f8'
    ],
    to_stream = "";

var revenueReporting = (revenue) => {
    
    if ($(`div[id="revenue_flow_chart"]`).length) {

        let the_value = new Array(),
            the_label = new Array();
        $.each(revenue.grouped_list.current.revenue_received_amount.data.data, function(i, e) {
            the_value.push(parseInt(e));
        });
        $.each(revenue.grouped_list.current.revenue_received_amount.data.labels, function(i, e) {
            the_label.push(e);
        });

        $(`div[data-chart="revenue_flow_chart"]`).html(``);
        $(`div[data-chart="revenue_flow_chart"]`).html(`<div id="revenue_flow_chart" style="width:100%;max-height:405px;height:405px;"></div>`);

        var revenue_flow_chart_options = {
            chart: {
                height: 400,
                type: 'area',
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            series: [{
                name: "Fees Payments",
                data: the_value
            }],
            xaxis: {
                type: 'datetime',
                categories: the_label,
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
            document.querySelector("#revenue_flow_chart"),
            revenue_flow_chart_options
        );

        revenue_flow_chart.render();

    }

    if ($(`canvas[id="revenue_payment_category"]`).length) {
        if (revenue.revenue_received_payment_method_count !== undefined) {
            let the_value = new Array(),
                the_label = new Array();
            $.each(revenue.revenue_received_payment_method_count.current.data, function(i, e) {
                the_value.push(parseInt(e.amount_value));
                the_label.push(e.payment_method);
            });
            var ctx = document.getElementById("revenue_payment_category");
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: the_label,
                    datasets: [{
                        label: 'Amount Paid',
                        data: the_value,
                        backgroundColor: ['#304ffe', '#ffa601'],
                        borderColor: ['#fff', '#fff', '#fff']
                    }]
                },
                options: {
                    responsive: true,
                    cutoutPercentage: 70,
                    maintainAspectRatio: false,
                    legend: {
                        display: true
                    }
                }
            });
        }
    }
}

var transactionReport = (transaction, date_range) => {

    if(transaction.category_total !== undefined) {
        let transaction_data = transaction.category_total.current,
            transaction_list = "",
            summation = {};

        summation["Deposit"] = 0;
        summation["Expense"] = 0;
        
        $.each(transaction_data, function(i, e) {
            transaction_list += `<thead>`;
            transaction_list += `<tr class="bg-info">`;
            transaction_list += `<th align="center" class="text-white font-18">${i == "Deposit" ? "Income" : i}</th>`;
            transaction_list += `<th align="center" class="text-white">Description</th>`;
            transaction_list += `<th align="center" class="text-white">Amount</th>`;
            transaction_list += `</tr>`;
            transaction_list += `</thead>`;
            transaction_list += `<tbody>`;

            if(!e.length) {
                transaction_list += `<tr>`;
                transaction_list += `<td align="center" colspan="3">No record found for the selected period.</td>`;
                transaction_list += `</tr>`;
            } else {
                let total = 0;
                $.each(e, function(ii, ee) {
                    total += parseFloat(ee.total_amount);
                    transaction_list += `<tr>`;
                    transaction_list += `<td>${ii+1}</td>`;
                    transaction_list += `<td>${ee.name !== null ? ee.name.toUpperCase() : ee.account_type.toUpperCase()}</td>`;
                    transaction_list += `<td class="font-15">${myPrefs.labels.currency} ${format_currency(ee.total_amount)}</td>`;
                    transaction_list += `</tr>`;
                    summation[i] += parseFloat(ee.total_amount);
                });
                transaction_list += `
                <tr class="bg-secondary">
                    <td></td>
                    <td class="font-bold">TOTAL</td>
                    <td class="font-18 font-bold">${myPrefs.labels.currency} ${format_currency(total)}</td>
                </tr>`;
            }
            transaction_list += `</tbody>`;
        });
        let outstanding = (summation["Deposit"]-summation["Expense"]);
        transaction_list += `
        <tr class="${outstanding < 0 ? "bg-red" : "bg-green"} text-white">
            <td colspan="2" class="font-bold">SURPLUS / (DEFICIT)</td>
            <td class="font-20 font-bold">${myPrefs.labels.currency} ${format_currency(outstanding)}</td>
        </tr>`;
        $(`table[id="transaction_summary"]`).html(transaction_list);
    }

}

var summaryReporting = (t_summary, date_range) => {

    var summary = t_summary.summary_report;
    $(`[data-filter="current_period"]`).html(date_range.current.title);
    $(`span[data-filter="period"]`).html(date_range.previous.title);

    let paymentsFound = typeof summary.students_class_fees_payment !== 'undefined';

    if($(`tbody[class="class_fees_payment_chart_table"]`).length && !paymentsFound) {
        $(`div[data-chart="class_fees_payment_chart_table"]`).html(
            no_content_wrapper("No Class Found", "No class found has been created yet; hence the fees payment chart cannot be displayed.", "fa-graduation-cap")
        ).addClass('pt-2');
    }
    if(paymentsFound) {

        let class_payments = summary.students_class_fees_payment,
            _class_keys = new Array(),
            _class_values = new Array(),
            _html_content = "";

        $('table[class~="reformat_table"]').DataTable().destroy();
        $.each(class_payments, function(i, e) {
            _class_keys.push(i);
            let t_val = e.actual_total_paid == null ? 0 : e.actual_total_paid;
            _class_values.push(parseInt(t_val));
            
            try {
                let percent = e.balance > 0 ? Math.round((e.balance / e.amount_due) * 100).toFixed(2) : 0;
                _html_content += `
                <tr>
                    <td>${i}</td>
                    <td class='text-center'>${formatMoney(e.amount_due)}</td>
                    <td class='text-center'>${formatMoney(e.actual_total_paid)}</td>
                    <td class='text-center'>${formatMoney(e.balance)}</td>
                    <td class='text-center'>${percent}%</td>
                </tr>`;
            } catch(error) { }
        });
        $(`tbody[class="class_fees_payment_chart_table"]`).html(_html_content);
        
        formatSimpleTable();

        $(`div[data-chart="class_fees_payment_chart_table"] div[class="dataTables_length"]`).remove();
        $(`div[data-chart="class_fees_payment_chart_table"] div[class="dataTables_filter"]`).remove();

        if ($(`canvas[id="class_revenue_donought_chart"]`).length) {

            let color_list = [];
            for(let i = 0; i < _class_values.length; i++) {
                color_list.push(colors_bank[i]);
            }

            var ctx = document.getElementById("class_revenue_donought_chart");
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: _class_keys,
                    datasets: [{
                        label: 'Revenue per Class',
                        data: _class_values,
                        backgroundColor: color_list
                    }]
                },
                options: {
                    responsive: true,
                    cutoutPercentage: 70,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    }
                }
            });
        }

        if($(`div[id="class_fees_payment_chart"]`).length) {

            $(`div[data-chart="class_fees_payment_chart"]`).html(``);
            $(`div[data-chart="class_fees_payment_chart"]`).html(`<div id="class_fees_payment_chart" style="width:100%;max-height:420px;height:420px;"></div>`);

            var class_fees_payment_chart_options = {
                chart: {
                    height: 420,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        endingShape: 'rounded',
                        columnWidth: '25%',
                    },
                },
                dataLabels: {
                    enabled: true
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Amount',
                    data: _class_values
                }],
                xaxis: {
                    categories: _class_keys,
                },
                yaxis: {
                    title: {
                        text: `${myPrefs.labels.currency} (thousands)`
                    }
                },
                fill: {
                    opacity: 1

                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return myPrefs.labels.currency + format_currency(val)
                        }
                    }
                }
            }

            var chart = new ApexCharts(
                document.querySelector("#class_fees_payment_chart"),
                class_fees_payment_chart_options
            );

            chart.render();
        }
        
        setTimeout(() => {
            $(`div[data-chart="class_fees_payment_chart_table"] div[class="dataTables_length"]`).remove();
            $(`div[data-chart="class_fees_payment_chart_table"] div[class="dataTables_filter"]`).remove();
        }, 1000);
        
    }

    if (typeof summary.users_record_count !== 'undefined') {
        let user = summary.users_record_count;
        let employees = parseInt(user.count.total_employees_count) + parseInt(user.count.total_accountants_count) + parseInt(user.count.total_admins_count);
        $(`[data-count="total_employees_count"]`).html(employees);
        $(`[data-count="total_students_count"]`).html(user.count.total_students_count);
        $(`[data-count="total_teachers_count"]`).html(user.count.total_teachers_count);
        $(`[data-count="total_parents_count"]`).html(user.count.total_parents_count);

        let male_student = parseInt(user.gender_count.Male.total_students_count),
            female_student = parseInt(user.gender_count.Female.total_students_count);

        $(`div[data-sex_count="Female"]`).html(female_student);
        $(`div[data-sex_count="Male"]`).html(male_student);

        if ($(`canvas[id="male_female_comparison"]`).length) {
            var ctx = document.getElementById("male_female_comparison");
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Male Students', 'Female Students'],
                    datasets: [{
                        label: '# of Students',
                        data: [male_student, female_student],
                        backgroundColor: ['#ffa601', '#304ffe'],
                        borderColor: ['#fff', '#fff', '#fff']
                    }]
                },
                options: {
                    responsive: true,
                    cutoutPercentage: 70,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    }
                }
            });
        }

    }

    if (typeof t_summary.departments_report !== 'undefined') {
        let department = t_summary.departments_report;
        $(`span[data-count="departments_count"]`).html(department.departments_count);
    }

    if (typeof t_summary.library_report !== 'undefined') {
        let library = t_summary.library_report;
        $(`span[data-count="library_category_count"]`).html(library.library_category_count);
        $(`span[data-count="library_books_count"]`).html(library.library_books_count);
    }

    if(typeof t_summary.students_class_record_count == 'undefined') {
        let no_class_found = no_content_wrapper("No Students Record Found", "No student has been created yet.", "fa-graduation-cap");
        $(`div[id="class_count_list"]`).html(no_class_found);
    }


    if (typeof summary.students_class_record_count !== 'undefined') {
        let classes = summary.students_class_record_count,
            class_count_list = "",
            key = 0;

        $(`span[data-count="total_classes_count"]`).html(classes.total_classes_count);

        $.each(classes.count, function(i, e) {
            key++;
            if(e.value > 0) {
                class_count_list += `
                    <div class="m-b-20">
                        <div class="text-small float-right font-17">${e.value}</div>
                        <div><span onclick="return load('class/${e.class_id}');" class="user_name">${e.name}</span></div>
                        <div class="progress" data-height="5" style="height: 5px;">
                        <div class="progress-bar ${bg_colors[key]}" role="progressbar" data-width="${e.percentage}%" aria-valuenow="${e.percentage}" aria-valuemin="0" aria-valuemax="100" style="width: ${e.percentage}%;"></div>
                        </div>
                    </div>
                `;
            }
        });
        class_count_list = classes.total_classes_count == 0 ? no_content_wrapper("No Class Found", "No class has been created yet.", "fa-graduation-cap") : class_count_list;
        $(`div[id="class_count_list"]`).html(class_count_list);
    }

    if (typeof summary.fees_record_count !== 'undefined') {
        let fees = summary.fees_record_count,
            total_revenue = 0,
            previous_amount = 0,
            chartKeys = new Array(),
            categoryKeys = new Array(),
            categoryValues = new Array(),
            currentValues = new Array(),
            previousValues = new Array();

        $.each(fees.amount, function(i, e) {
            categoryKeys.push(i);
            categoryValues.push(parseFloat(e));
            total_revenue += parseFloat(e);
        });

        let revenue_category_counts = "<div class='row'>";
        $.each(fees.count, function(i, e) {
            let amount = fees.amount[i];
            let percentage = (amount > 0) ? ((amount / total_revenue) * 100).toFixed(2) : 0;
            revenue_category_counts += `
            <div class="col-md-4 col-lg-2 col-sm-6">
                <div class="card text-center">
                    <div class="pt-1 pb-1 bg-primary text-center text-white font-15 pl-2 pr-2 pt-1 text-uppercase pb-0"><strong>${i}</strong></div>
                    <div class="card-body pt-2 pl-2 font-14 text-uppercase pr-2 pb-1">
                        <p class="mb-0 pb-0 pb-1">
                            <strong class="text-success font-20">${myPrefs.labels.currency} ${format_currency(amount)}</strong>
                        </p>
                    </div>
                </div>
            </div>`;
        });
        revenue_category_counts += "</div>";
        $(`div[id="revenue_category_counts"]`).html(revenue_category_counts);

        if (typeof summary.fees_record_count.comparison !== 'undefined') {
            if($(`div[data-chart="revenue_category_chart"]`).length) {
                $(`div[data-chart="revenue_category_chart"]`).html(`<div id="revenue_category_chart"></div>`);
                $.each(summary.fees_record_count.comparison.amount.previous, function(i, e) {
                    chartKeys.push(e.name);
                    previousValues.push(parseFloat(e.value));
                    previous_amount += parseFloat(e.value);
                    currentValues.push(parseFloat(summary.fees_record_count.comparison.amount.current[i].value));
                });

                $(`[data-count="total_revenue_received"]`).html(format_currency(total_revenue));
                $(`[data-count="previous_amount_received"]`).html(format_currency(previous_amount));

                var options = {
                    chart: {
                        height: 400,
                        type: 'bar',
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            endingShape: 'rounded',
                            columnWidth: '25%',
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    series: [{
                        name: 'Amount',
                        data: currentValues
                    }],
                    xaxis: {
                        categories: chartKeys,
                    },
                    yaxis: {
                        title: {
                            text: `${myPrefs.labels.currency} (thousands)`
                        }
                    },
                    fill: {
                        opacity: 1

                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return myPrefs.labels.currency + format_currency(val)
                            }
                        }
                    }
                }

                var chart = new ApexCharts(
                    document.querySelector("#revenue_category_chart"),
                    options
                );

                chart.render();
            }
        }

        if (fees.summation !== undefined) {
            let t_balance = fees.summation.balance !== null ? fees.summation.balance : 0;
            let t_arrears_total = fees.summation.arrears_total !== null ? fees.summation.arrears_total : 0;
            $(`[data-count="total_balance"]`).html(format_currency(t_balance)).addClass('font-22');
            $(`[data-count="arrears_total"]`).html(formatMoney(t_arrears_total)).addClass('font-22');
        }

        $.each(summary.fees_record_count.summation, function(i, e) {
            $(`[data-summary="${i}"]`).html(`${formatMoney(e)}`).addClass('font-20');
        });

        if(summary.transaction_revenue_flow !== undefined) {
            let transaction = summary.transaction_revenue_flow;
            $(`[data-count="total_income_received"]`).html(format_currency(transaction.category_total.current.Deposit)).addClass('font-22');
            $(`[data-count="total_expenditure"]`).html(format_currency(transaction.category_total.current.Expense)).addClass('font-22');
            $(`[data-count="Bank_Deposit"]`).html(format_currency(transaction.category_total.current.Bank_Deposit)).addClass('font-22');
            $(`[data-count="Bank_Withdrawal"]`).html(format_currency(transaction.category_total.current.Bank_Withdrawal)).addClass('font-22');
            $(`[data-count="Bank_Recons"]`).html(format_currency(transaction.category_total.current.Bank_Recons)).addClass('font-22');
            $(`[data-count="account_balance"]`).html(format_currency(transaction.account_balance)).addClass('font-22');
        }

        if ($(`canvas[id="revenue_category_group"]`).length) {
            $(`div[data-chart="revenue_category_group"]`).html(`<canvas style="max-height:420px;height:420px;" id="revenue_category_group"></canvas>`);

            var ctx = document.getElementById("revenue_category_group").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryKeys,
                    datasets: [{
                        label: '# of Reactions',
                        data: categoryValues,
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

    $(`div[class~="quick_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
}

var attendanceReport = (_attendance) => {

    if (typeof _attendance.attendance !== 'undefined') {
        let attendance = _attendance.attendance;

        if(typeof attendance.code !== 'undefined' && attendance.code == 203) {
            notify(attendance.data);
            return false;
        }

        $.each(attendance.summary, function(i, e) {
            $(`h3[data-attendance_count="${i}"]`).html(e);
        });

        let chart_summary = "";
        $.each(attendance.chart_summary, function(i, e) {
            chart_summary += `<div class='mb-1'><strong>${i}:</strong> ${e}</div>`;
        });
        $(`span[data-section="chart_summary"]`).html(chart_summary);

        if ($(`div[id="attendance_chart"]`).length) {
            var chart_label = new Array();
            $.each(attendance.days_list, function(i, day) {
                chart_label.push(i);
            });

            $(`div[data-chart_container="attendance_chart"]`).html(`<div id="attendance_chart" style="min-height:450px;"></div>`);

            var options = {
                chart: {
                    height: 420,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        endingShape: 'rounded',
                        columnWidth: '35%',
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: attendance.chart_grouping ?? [],
                xaxis: {
                    categories: chart_label,
                },
                fill: {
                    opacity: 1
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#attendance_chart"),
                options
            );
            chart.render();
        }

        if ($(`div[id="attendance_log_chart"]`).length) {

            var _log_chart_label = new Array(),
                _array_data = new Array();
            $.each(attendance.days_list, function(i, day) {
                _log_chart_label.push(i);
            });

            $(`div[data-chart_container="attendance_log_chart"]`).html(`<div id="attendance_log_chart" style="min-height:450px;"></div>`);

            $.each(attendance.chart_grouping, function(i, e) {
                if ($.inArray(e.name, ["Student", "Staff"]) > -1) {
                    _array_data.push(e);
                }
            });

            var _attendance_options = {
                chart: {
                    height: 450,
                    type: 'area',
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                series: _array_data,
                xaxis: {
                    type: 'datetime',
                    categories: _log_chart_label,
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

            var _attendance_chart = new ApexCharts(
                document.querySelector("#attendance_log_chart"),
                _attendance_options
            );

            _attendance_chart.render();

        }
    }

    if (typeof _attendance.class_summary !== 'undefined') {
        let _class_summary = _attendance.class_summary,
            _chart_label = new Array(),
            color = 'text-black';
        let rate = _class_summary?.summaries?.attendanceRate ?? 0;
        if(rate < 40) {
            color = 'text-danger';
        } else if(rate > 90) {
            color = 'text-success';
        } else {
            color = 'text-warning';
        }
        $(`h3[data-attendance_count="attendanceRate"]`).html(`<span class='${color}'>${rate}%</span>`);
        if ($(`div[id="class_attendance_chart"]`).length) {
            $.each(_class_summary.summary, function(i, day) {
                _chart_label.push(i);
            });
            options = {
                chart: {
                    height: 350,
                    type: 'bar',
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        endingShape: 'rounded',
                        columnWidth: '35%',
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: _class_summary.chart_grouping,
                xaxis: {
                    categories: _chart_label,
                },
                fill: {
                    opacity: 1
                }
            }
            var chart = new ApexCharts(
                document.querySelector("#class_attendance_chart"),
                options
            );
            chart.render();
        }
    }

    if ($(`div[id="attendance_chart_list"]`).length) {
        let comments = "<em class='font-11'>No comments</em>",
            attendance_chart_list = "<div class='row'>",
            single_data = $(`div[id="single_user_data"]`).length;
        
        if(_attendance.attendance.code !== undefined) {
            if(_attendance.attendance.code == 203) {
                notify(_attendance.attendance.data);
                return false;
            }
        }

        let present = ["Present", "present"];
        let absent = ["Absent", "absent"];
        let holiday = ["Holiday", "holiday"];

        $.each(_attendance.attendance.days_list, function(day, status) {
            comments = _attendance.attendance.days_comments[day].length ? _attendance.attendance.days_comments[day] : "<em class='font-12'>No comments</em>";
            attendance_chart_list += `
            <div class='${single_data ? "col-lg-4 col-md-6" : "col-lg-4 col-md-6"}'>
                <div class='card mb-3'>
                    <div class='card-header pl-2 pr-2 pb-5px'><h5 class='pb-0 mb-0'>${day}</h5></div>
                    <div class='card-body p-2'>
                        <i class="fa ${present.includes(status) ? "text-success fa-check" : (absent.includes(status) ? "text-danger fa-times" : (
                           holiday.includes(status) ? "text-info fa-calendar-times" : "text-warning fa-adjust"
                        ))}"></i> 
                        <strong class="${present.includes(status) ? "text-success" : (absent.includes(status) ? "text-danger" : (
                           holiday.includes(status) ? "text-info" : "text-warning"
                        ))}">${status.toUpperCase()}</strong>
                    </div>
                    <div class='border-top p-2 card-footer'>
                        ${comments}
                    </div>
                </div>
            </div>`;
        });
        attendance_chart_list += "</div>";
        $(`div[id="attendance_chart_list"]`).html(attendance_chart_list);
    }

}

var salaryReport = (_salary) => {
    if (_salary.salary_list !== undefined) {
        let _labels = new Array(),
            net_salary = new Array(),
            gross_salary = new Array();
        $.each(_salary.salary_list, function(i, e) {
            _labels.push(e.month_name);
            gross_salary.push(e.gross_salary);
            net_salary.push(e.net_salary);
        });
        var ctx = document.getElementById("salary_flow_chart");
        if (ctx !== undefined) {
            ctx.height = 355;
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: _labels,
                    datasets: [{
                            label: "Gross Salary",
                            borderColor: "rgba(0,0,0,.09)",
                            borderWidth: "1",
                            backgroundColor: "rgba(0,0,0,.07)",
                            data: gross_salary
                        },
                        {
                            label: "Net Salary",
                            borderColor: "rgba(0, 123, 255, 0.9)",
                            borderWidth: "1",
                            backgroundColor: "rgba(0, 123, 255, 0.5)",
                            pointHighlightStroke: "rgba(26,179,148,1)",
                            data: net_salary
                        }
                    ]
                },
                options: {
                    legend: {
                        position: 'bottom'
                    },
                    responsive: true,
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            }
                        }]
                    }

                }
            });
        }
    }

    $.each(_salary.summary.totals, function(i, e) {
        $(`span[data-count="${i}"]`).html(`${myPrefs.labels.currency}${format_currency(e.total)}`);
    });

    let deductions_list = _salary.category.Deduction,
        allowances_list = _salary.category.Allowance,
        summary_total = _salary.summary_total;
    let break_down = $(`div[data-chart="full_breakdown_chart"]`);
    let table_html = `<table class="table table-bordered">
                <thead>
                    <tr style="background-color:#03a9f4;">
                    <th style="color:#fff;" width="15%">Deductions</th>`;
    $.each(deductions_list, function(i, e) {
        table_html += `<th style="color:#fff;">${i}</th>`;
    });
    table_html += `<td style="color:#fff;" align="center">TOTAL</td>`;
    table_html += `</tr></thead><tbody>`;

    let deductions_totals = new Array(),
        allowances_totals = new Array();
    if (_salary.chart.comparison !== undefined) {
        if (_salary.chart.comparison.deductions !== undefined) {
            deductions_totals = _salary.chart.comparison.deductions;
        }
    }

    if (_salary.chart.comparison !== undefined) {
        if (_salary.chart.comparison.allowances !== undefined) {
            allowances_totals = _salary.chart.comparison.allowances;
        }
    }

    if(_salary.grouping.Deduction !== undefined) {
        $.each(_salary.grouping.Deduction, function(i, e) {
            table_html += `<tr>`;
            table_html += `<td>${e}</td>`;
            $.each(deductions_list, function(ii, ee) {
                let amount = ((deductions_list[ii][e] !== undefined) && (deductions_list[ii][e].amount !== undefined)) ? format_currency(deductions_list[ii][e].amount) : 0;
                table_html += `<td>${myPrefs.labels.currency}${amount}</td>`;
            });
            table_html += `<td align="center"><strong>${myPrefs.labels.currency}${format_currency(summary_total.list.Deduction[e])}</strong></td>`;
            table_html += `</tr>`;
        });
    }

    table_html += `<tr style="background-color:#f4f4f4;">`;
    table_html += `<td><strong>TOTAL</strong></td>`;
    $.each(deductions_totals, function(iv, ev) {
        let _amount = format_currency(ev);
        table_html += `<td><strong>${myPrefs.labels.currency}${_amount}</strong></td>`;
    });
    table_html += `<td align="center"><strong>${myPrefs.labels.currency}${format_currency(summary_total.total.Deduction)}</strong></td>`;
    table_html += `</tr>`;

    table_html += `</tbody></table>`;

    table_html += `<table class="table table-bordered"><thead><tr style="background-color:#03a9f4;">
        <th width="15%" style="color:#fff;">Allowances</th>`;
    $.each(allowances_list, function(i, e) {
        table_html += `<th style="color:#fff;">${i}</th>`;
    });
    table_html += `<td align="center" style="color:#fff;">TOTAL</td>`;
    table_html += `</tr></thead><tbody>`;

    $.each(_salary.grouping.Allowance, function(i, e) {
        table_html += `<tr>`;
        table_html += `<td >${e}</td>`;
        $.each(allowances_list, function(ii, ee) {
            let amount = (allowances_list[ii][e].amount !== undefined) ? format_currency(allowances_list[ii][e].amount) : 0;
            table_html += `<td>${myPrefs.labels.currency}${amount}</td>`;
        });
        table_html += `<td align="center"><strong>${myPrefs.labels.currency}${format_currency(summary_total.list.Allowance[e])}</strong></td>`;
        table_html += `</tr>`;
    });

    table_html += `<tr style="background-color:#f4f4f4;">`;
    table_html += `<td><strong>TOTAL</strong></td>`;
    $.each(allowances_totals, function(iv, ev) {
        let _amount = format_currency(ev);
        table_html += `<td><strong>${myPrefs.labels.currency}${_amount}</strong></td>`;
    });
    table_html += `<td align="center"><strong>${myPrefs.labels.currency}${format_currency(summary_total.total.Allowance)}</strong></td>`;
    table_html += `</tr>`;

    table_html += `</tbody></table>`;

    break_down.html(table_html);
}

var loadDashboardAnalitics = (period) => {
    let to_stream = $(`div[id="data-report_stream"]`).attr(`data-report_stream`);
    $.get(`${baseUrl}api/analitics/generate?period=${period}&label[stream]=${to_stream}`).then((response) => {
        if (response.code === 200) {
            let range = response.data.result.date_range;

            $(`input[id="d_start"]`).val(range.current.start);
            $(`input[id="d_end"]`).val(range.current.end);

            $(`a[data-href="summary_link"]`).attr({"href": `${baseUrl}download/accounting?display=notes&item=summary&start_date=${range.current.start}&end_date=${range.current.end}&group_by=day&breakdown=true`});

            if (response.data.result.summary_report !== undefined) {
                summaryReporting(response.data.result, range);
            }
            if (response.data.result.attendance_report !== undefined) {
                attendanceReport(response.data.result.attendance_report);
            }
            if (response.data.result.salary_report !== undefined) {
                salaryReport(response.data.result.salary_report);
            }
            if (response.data.result.transaction_revenue_flow !== undefined) {
                transactionReport(response.data.result.transaction_revenue_flow, range);
            }
            if (response.data.result.fees_revenue_flow !== undefined) {
                revenueReporting(response.data.result.fees_revenue_flow, range);
            }
        }
        setTimeout(() => {
            $.pageoverlay.hide();
            $(`div[class~="form-content-loader"]`).css({ "display": "none" });
        }, 800);
        $(`div[class~="toggle-calculator"]`).addClass("hidden");
    }).catch((error) => {
        $.pageoverlay.hide();
        console.log({error: error});
        $(`div[class~="form-content-loader"]`).css({ "display": "none" });
    });
}

var filter_Class_Attendance = () => {
    let load_date = $(`input[name="class_date_select"]`).val();
    $(`div[id="class_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "flex" });
    $.get(`${baseUrl}api/analitics/generate?label[stream]=class_attendance_report&label[load_date]=${load_date}`).then((response) => {
        if (response.code === 200) {
            $(`div[data-chart_container="class_attendance_chart"]`).html(`<div style="width:100%;height:345px;" id="class_attendance_chart"></div>`);
            if (response.data.result.attendance_report !== undefined) {
                attendanceReport(response.data.result.attendance_report);
            }
            setTimeout(() => {
                $(`div[id="class_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
            }, refresh_seconds);
        }
    }).catch(() => {
        $(`div[id="class_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
    });
}

var filter_ClassGroup_Attendance = (append_query = "") => {
    let start_date = $(`input[data-item="attendance_performance"][name="group_start_date"]`).val(),
        end_date = $(`input[data-item="attendance_performance"][name="group_end_date"]`).val(),
        class_id = parseInt($(`input[name="filtered_class_id"]`).val());
    
    if(class_id > 0) {
        append_query += `&class_id=${class_id}`;
        append_query += `&is_summary=${class_id}`;
    }

    $(`div[id="class_summary_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "flex" });
    $.get(`${baseUrl}api/analitics/generate?label[stream]=class_attendance_report&label[start_date]=${start_date}&label[end_date]=${end_date}${append_query}`).then((response) => {
        if (response.code === 200) {
            if (typeof response.data.result.attendance_report !== 'undefined') {
                let attendance = response.data.result.attendance_report.class_summary;
                let html = '', students = '', si = 0, i = 0;
                $.each(attendance.attendanceRate, function(id, value) {
                    i++;
                    if(value.totalDays) {
                        html += `<tr>
                            <td class='3%'>${i}</td>
                            <td>${id}</td>
                            <td class='text-center'>${value['Size']}</td>
                            <td class='text-center'>${value['totalDays']}</td>
                            <td class='text-center text-success'>${value['Present']}</td>
                            <td class='text-center text-danger'>${value['Absent']}</td>
                            <td class='text-center text-success'>${value['presentRate']}%</td>
                            <td class='text-center text-warning'>${value['absentRate']}%</td>
                            ${class_id > 0 ? "" : `
                            <td class='text-center'>
                                <button onclick='return loadPage("${baseUrl}attendance/summary/${value['Id']}")' class='btn btn-outline-success'><i class='fas fa-chart-bar'></i> View</button>
                            </td>`}
                        </tr>`;
                    }
                });
                $(`tbody[class="class_summary_attendance_rate"]`).html(html);

                if(class_id && $(`tbody[class="class_students_attendance_rate"]`).length) {
                    let attendance = response.data.result.attendance_report.attendance.students_dataset;
                    $.each(attendance.summary, function(id, value) {
                        i++;
                        if(value.expected) {
                            let presentRate = value.present > 0 ? round(value.present / value.expected * 100, 2) : 0;
                            let absentRate = value.absent > 0 ? round(value.absent / value.expected * 100, 2) : 0;
                            students += `<tr>
                                <td class='3%'>${i}</td>
                                <td>${attendance.breakdown[id]['name']}</td>
                                <td class='text-center'>${value['expected']}</td>
                                <td class='text-center text-success'>${value.summary[id]['present']}</td>
                                <td class='text-center text-danger'>${value['absent']}</td>
                                <td class='text-center text-success'>${presentRate}%</td>
                                <td class='text-center text-warning'>${absentRate}%</td>
                            </tr>`;
                        }
                    });
                    $(`tbody[class="class_students_attendance_rate"]`).html(students);
                }
            }
            setTimeout(() => {
                $(`div[id="class_summary_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
            }, refresh_seconds);
        }
    }).catch(() => {
        $(`div[id="class_summary_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
    });
}

var filter_Single_UserGroup_Attendance = (append_query = "", url_path = "") => {
    let start_date = $(`input[data-item="attendance"][name="group_start_date"]`).val(),
        end_date = $(`input[data-item="attendance"][name="group_end_date"]`).val();
    $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "flex" });
    $.get(`${baseUrl}api/analitics/generate?label[stream]=attendance_report&label[start_date]=${start_date}&label[end_date]=${end_date}${append_query}&class_only=true&is_summary=true`).then((response) => {
        if (response.code === 200) {
            if (response.data.result.attendance_report !== undefined) {
                attendanceReport(response.data.result.attendance_report);
            }
        }
        $(`a[data-href_path="attendance_summary"]`).attr({"href": `${baseUrl}download/attendance?start_date=${start_date}&end_date=${end_date}&${url_path}&att_d=true`});
        setTimeout(() => {
            $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
        }, refresh_seconds);
    }).catch(() => {
        $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
    });
}

var filter_UserGroup_Attendance = (append_query = "") => {
    let start_date = $(`input[data-item="attendance"][name="group_start_date"]`).val(),
        end_date = $(`input[data-item="attendance"][name="group_end_date"]`).val();
    $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "flex" });
    $.get(`${baseUrl}api/analitics/generate?label[stream]=class_attendance_report&label[start_date]=${start_date}&label[end_date]=${end_date}${append_query}&class_only=true&is_summary=true`).then((response) => {
        if (response.code === 200) {
            let summary = response.data.result.attendance_report.class_summary;
            $(`table[id="attendance_logs_by_daychart"]`).html(summary.summaries.table);
            setTimeout(() => {
                $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
            }, refresh_seconds);
        }
    }).catch(() => {
        $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
    });
}

var filter_Transaction_Summary = (stream) => {
    let _start_date = $(`input[id="d_start"]`).val(),
        _end_date = $(`input[id="d_end"]`).val(),
        _t_period = "";
    $(`div[id="data-report_stream"]`).attr("data-report_stream", stream);
    $(`div[id="trasaction_container"] div[class~="form-content-loader"]`).css({ "display": "flex" });
    if(_start_date.length) {
        _t_period += `${_start_date}`;
    }
    if(_end_date.length) {
        _t_period += `:${_end_date}`;
    }
    $(`div[data-filter="quick_summary_filter"] button[type="button"]`).removeClass("active");
    loadDashboardAnalitics(`${_t_period}`);
}

if ($(`div[id="data-report_stream"]`).length) {
    let d_period = $(`div[class~="default_period"]`).attr("data-current_period");

    // get the period for the data-filter="quick_attendance_filter" which has a class of active
    let _attendance_period = $(`div[data-filter="quick_attendance_filter"] button.active`).attr("data-period");
    if(_attendance_period) {
        d_period += `&label[attendance_period]=${_attendance_period}`;
    }

    // get the start and end date for the single user data
    if($(`div[id="single_user_data"]`).length) {
        d_period += `&label[start_date]=${$(`input[data-item="attendance"][name="group_start_date"]`).val()}`;
        d_period += `&label[end_date]=${$(`input[data-item="attendance"][name="group_end_date"]`).val()}`;
    }
    
    loadDashboardAnalitics(d_period);

    filter.on("click", function() {
        let _class_id = $(`[id="reports_insight"] select[name="class_id"]`).val(),
            _academic_year_term = $(`[id="reports_insight"] select[name="academic_year_term"]`).val(),
            _period = $(`[id="reports_insight"] select[id="filter-dashboard"]`).val();
        $.pageoverlay.show();
        if (_class_id !== "null") {
            _period = `${_period}&label[class_id]=${_class_id}&label[academic_year_term]=${_academic_year_term}`;
        }
        loadDashboardAnalitics(_period);
    });

    $(`div[data-filter="quick_revenue_filter"] button`).on("click", function() {
        let item = $(this);
        $(`div[data-filter="quick_revenue_filter"] button`).removeClass("active");
        item.addClass("active");
        let period = item.attr("data-period"),
            stream = item.attr("data-stream");
        $(`select[name="period"]`).val(period).change();
        $(`div[id="data-report_stream"]`).attr("data-report_stream", stream);
        loadDashboardAnalitics(period);
    });

    $(`div[data-filter="quick_summary_filter"] button[type="button"]`).on("click", function() {
        let item = $(this);
        $(`div[data-filter="quick_summary_filter"] button[type="button"]`).removeClass("active");
        item.addClass("active");
        let period = item.attr("data-period"),
            stream = item.attr("data-stream");
        $(`select[name="period"]`).val(period).change();
        $(`div[id="data-report_stream"]`).attr("data-report_stream", stream);
        loadDashboardAnalitics(period);
    });

    $(`div[data-filter="quick_attendance_filter"] button`).on("click", function() {
        let _item = $(this);
        $(`div[data-filter="quick_attendance_filter"] button`).removeClass("active");
        _item.addClass("active");
        let _tperiod = _item.attr("data-period"),
            _tstream = _item.attr("data-stream");
        $(`div[id="data-report_stream"]`).attr("data-report_stream", _tstream);
        loadDashboardAnalitics(_tperiod);
    });

}