var filter = $(`[id="reports_insight"] button[id="filter_Fees_Report"]`),
    bg_colors = [
        "l-bg-green", "l-bg-orange", "l-bg-purple", "l-bg-red", "l-bg-cyan",
        "l-bg-yellow", "l-bg-purple-dark", "bg-deep-orange", "bg-brown", "bg-pink",
        "bg-indigo", "bg-teal", "bg-light-blue", "bg-info", "bg-warning", "bg-dark"
    ],
    to_stream = "";

$(`[id="reports_insight"] select[name="department_id"]`).on("change", function() {
    let value = $(this).val();
    $(`[id="reports_insight"] select[name='class_id']`).find('option').remove().end();
    $(`[id="reports_insight"] select[name='class_id']`).append(`<option value="">Please Select Class</option>`);

    $.get(`${baseUrl}api/classes/list?columns=id,name`, { department_id: value }).then((response) => {
        if (response.code == 200) {
            $.each(response.data.result, function(i, e) {
                $(`[id="reports_insight"] select[name='class_id']`).append(`<option value='${e.id}'>${e.name}</option>'`);
            });
        }
    });
});

function format_currency(total) {
    var neg = false;
    if (total < 0) {
        neg = true;
        total = Math.abs(total);
    }
    return (neg ? "-" : '') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
}

var revenueReporting = (revenue) => {
    if ($(`canvas[id="revenue_flow_chart"]`).length) {

        let the_value = new Array(),
            the_label = new Array();
        $.each(revenue.grouped_list.current.revenue_received_amount.data.data, function(i, e) {
            the_value.push(parseInt(e));
        });
        $.each(revenue.grouped_list.current.revenue_received_amount.data.labels, function(i, e) {
            the_label.push(e);
        });

        var draw = Chart.controllers.line.prototype.draw;
        Chart.controllers.lineShadow = Chart.controllers.line.extend({
            draw: function() {
                draw.apply(this, arguments);
                var ctx = this.chart.chart.ctx;
                var _stroke = ctx.stroke;
                ctx.stroke = function() {
                    ctx.save();
                    ctx.shadowColor = '#00000075';
                    ctx.shadowBlur = 6;
                    ctx.shadowOffsetX = 4;
                    ctx.shadowOffsetY = 4;
                    _stroke.apply(this, arguments)
                    ctx.restore();
                }
            }
        });

        $(`div[data-chart="revenue_flow_chart"]`).html(``);
        $(`div[data-chart="revenue_flow_chart"]`).html(`<canvas id="revenue_flow_chart" style="width:100%;max-height:405px;height:405px;"></canvas>`);

        var ctx = document.getElementById('revenue_flow_chart').getContext("2d");

        var gradientStroke = ctx.createLinearGradient(500, 0, 0, 0);
        gradientStroke.addColorStop(0, 'rgba(155, 89, 182, 1)');
        gradientStroke.addColorStop(1, 'rgba(231, 76, 60, 1)');


        var myChart = new Chart(ctx, {
            type: 'lineShadow',
            data: {
                labels: the_label,
                type: 'line',
                defaultFontFamily: 'Arial',
                datasets: [{
                    label: "Revenue Received",
                    data: the_value,
                    borderColor: gradientStroke,
                    pointBorderColor: gradientStroke,
                    pointBackgroundColor: gradientStroke,
                    pointHoverBackgroundColor: gradientStroke,
                    pointHoverBorderColor: gradientStroke,
                    pointBorderWidth: 10,
                    pointHoverRadius: 10,
                    pointHoverBorderWidth: 1,
                    pointRadius: 1,
                    fill: false,
                    borderWidth: 4,
                }]
            },
            options: {
                responsive: true,
                legend: {
                    position: "bottom"
                },
                tooltips: {
                    mode: 'index',
                    titleFontSize: 12,
                    titleFontColor: '#fff',
                    bodyFontColor: '#fff',
                    backgroundColor: '#289cf5',
                    titleFontFamily: 'Poppins',
                    bodyFontFamily: 'Poppins',
                    cornerRadius: 3,
                    intersect: false,
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            fontColor: "rgba(0,0,0,0.5)",
                            fontStyle: "bold",
                            beginAtZero: true,
                            maxTicksLimit: 5,
                            padding: 20
                        },
                        gridLines: {
                            drawTicks: false,
                            display: false
                        }

                    }],
                    xAxes: [{
                        gridLines: {
                            zeroLineColor: "transparent"
                        },
                        ticks: {
                            padding: 20,
                            fontColor: "rgba(0,0,0,0.5)",
                            fontStyle: "bold"
                        }
                    }]
                }
            }
        });

    }
    if ($(`canvas[id="revenue_payment_category"]`).length) {
        console.log(revenue);
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

var summaryReporting = (t_summary, date_range) => {

    var summary = t_summary.summary_report;

    if (summary.users_record_count !== undefined) {
        let user = summary.users_record_count;
        let employees = parseInt(user.count.total_employees_count) + parseInt(user.count.total_accountants_count) + parseInt(user.count.total_admins_count);
        $(`span[data-count="total_employees_count"]`).html(employees);
        $(`span[data-count="total_students_count"]`).html(user.count.total_students_count);
        $(`span[data-count="total_teachers_count"]`).html(user.count.total_teachers_count);
        $(`span[data-count="total_parents_count"]`).html(user.count.total_parents_count);

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
                        label: '# of Reactions',
                        data: [male_student, female_student],
                        backgroundColor: ['#304ffe', '#ffa601'],
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

    if (t_summary.departments_report !== undefined) {
        let department = t_summary.departments_report;
        $(`span[data-count="departments_count"]`).html(department.departments_count);
    }

    if (t_summary.library_report !== undefined) {
        let library = t_summary.library_report;
        $(`span[data-count="library_category_count"]`).html(library.library_category_count);
        $(`span[data-count="library_books_count"]`).html(library.library_books_count);
    }

    if (summary.students_class_record_count !== undefined) {
        let classes = summary.students_class_record_count,
            class_count_list = "",
            key = 0;

        $(`span[data-count="total_classes_count"]`).html(classes.total_classes_count);

        $.each(classes.count, function(i, e) {
            key++;
            class_count_list += `
                <div class="m-b-20">
                    <div class="text-small float-right font-weight-bold text-muted">${e.value}</div>
                    <div class="font-weight-bold">${e.name}</div>
                    <div class="progress" data-height="5" style="height: 5px;">
                    <div class="progress-bar ${bg_colors[key]}" role="progressbar" data-width="${e.percentage}%" aria-valuenow="${e.percentage}" aria-valuemin="0" aria-valuemax="100" style="width: ${e.percentage}%;"></div>
                    </div>
                </div>
            `;
        });
        $(`div[id="class_count_list"]`).html(class_count_list);
    }

    if (summary.fees_record_count !== undefined) {
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
            <div class="col-lg-4 col-md-6">
                <div class="card">
                    <div class="card-header pb-0"><strong>${i}</strong></div>
                    <div class="card-body pt-2 pb-1">
                        <p class="mb-0 pb-0">Processed Count: <strong>${e}</strong></p>
                        <p class="mb-0 pb-0">Amount: <strong>${myPrefs.labels.currency}${format_currency(amount)}</strong></p>
                        <p class="mb-0 pb-0">Percentage: <strong>${percentage}%</strong></p>
                    </div>
                </div>
            </div>`;
        });
        revenue_category_counts += "</div>";
        $(`div[id="revenue_category_counts"]`).html(revenue_category_counts);

        if (summary.fees_record_count.comparison !== undefined) {
            $.each(summary.fees_record_count.comparison.amount.previous, function(i, e) {
                chartKeys.push(e.name);
                previousValues.push(parseFloat(e.value));
                previous_amount += parseFloat(e.value);
                currentValues.push(parseFloat(summary.fees_record_count.comparison.amount.current[i].value));
            });

            $(`span[data-count="total_revenue_received"]`).html(format_currency(total_revenue));
            $(`span[data-count="previous_amount_received"]`).html(format_currency(previous_amount));

            var options = {
                chart: {
                    height: 350,
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
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Previous Revenue',
                    data: previousValues
                }, {
                    name: 'Current Revenue',
                    data: currentValues
                }],
                xaxis: {
                    categories: chartKeys,
                },
                yaxis: {
                    title: {
                        text: '$ (thousands)'
                    }
                },
                fill: {
                    opacity: 1

                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return "$" + format_currency(val)
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


        if ($(`canvas[id="revenue_category_group"]`).length) {
            $(`div[data-chart="revenue_category_group"]`).html(``);
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

    $(`span[data-filter="period"]`).html(date_range.previous.title);
    $(`div[class~="quick_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
}

var attendanceReport = (_attendance) => {

    if (_attendance.attendance !== undefined) {
        let attendance = _attendance.attendance;

        $.each(attendance.summary, function(i, e) {
            $(`h3[data-attendance_count="${i}"]`).html(e);
        });

        let chart_summary = "";
        $.each(attendance.chart_summary, function(i, e) {
            chart_summary += `<div><strong>${i}:</strong> ${e}</div>`;
        });
        $(`span[data-section="chart_summary"]`).html(chart_summary);

        if ($(`div[id="attendance_chart"]`).length) {
            var chart_label = new Array();
            $.each(attendance.days_list, function(i, day) {
                chart_label.push(i);
            });

            var options = {
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
                series: attendance.chart_grouping,
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

            $(`div[data-chart_container="attendance_log_chart"]`).html(`<div id="attendance_log_chart" style="min-height:350px;"></div>`);

            $.each(attendance.chart_grouping, function(i, e) {
                if ($.inArray(e.name, ["Students", "Teachers"]) > -1) {
                    _array_data.push(e);
                }
            });

            var _attendance_options = {
                chart: {
                    height: 350,
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
                        format: 'dd/MM/yy'
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

    if (_attendance.class_summary !== undefined) {
        if ($(`div[id="class_attendance_chart"]`).length) {
            let _class_summary = _attendance.class_summary,
                _chart_label = new Array();
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
        let attendance_chart_list = "<div class='row'>";
        $.each(_attendance.attendance.days_list, function(day, status) {
            attendance_chart_list += `
            <div class='col-lg-3 col-md-4'>
                <div class='card mb-3'>
                    <div class='card-header pb-0'><h5>${day}</h5></div>
                    <div class='card-body pt-2 pb-2'>
                        <i class="fa ${status === "present" ? "text-success fa-check" : "text-danger fa-times"}"></i> 
                        <strong class="${status === "present" ? "text-success" : "text-danger"}">${status.toUpperCase()}</strong>
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
        allowances_list = _salary.category.Allowance;
    let break_down = $(`div[data-chart="full_breakdown_chart"]`);
    let table_html = `<table class="table table-bordered"><thead><tr><th width="15%">Deductions</th>`;
    $.each(deductions_list, function(i, e) {
        table_html += `<th>${i}</th>`;
    });
    table_html += `</tr></thead><tbody>`;
    $.each(_salary.grouping.Deduction, function(i, e) {
        table_html += `<tr>`;
        table_html += `<td>${e}</td>`;
        $.each(deductions_list, function(ii, ee) {
            let amount = (deductions_list[ii][e].amount !== undefined) ? format_currency(deductions_list[ii][e].amount) : 0;
            table_html += `<td>${myPrefs.labels.currency}${amount}</td>`;
        });
        table_html += `</tr>`;
    });
    table_html += `</tbody></table>`;

    table_html += `<table class="table table-bordered"><thead><tr><th width="15%">Allowances</th>`;
    $.each(allowances_list, function(i, e) {
        table_html += `<th>${i}</th>`;
    });
    table_html += `</tr></thead><tbody>`;
    $.each(_salary.grouping.Allowance, function(i, e) {
        table_html += `<tr>`;
        table_html += `<td>${e}</td>`;
        $.each(allowances_list, function(ii, ee) {
            let amount = (allowances_list[ii][e].amount !== undefined) ? format_currency(allowances_list[ii][e].amount) : 0;
            table_html += `<td>${myPrefs.labels.currency}${amount}</td>`;
        });
        table_html += `</tr>`;
    });
    table_html += `</tbody></table>`;

    break_down.html(table_html);
}

var loadDashboardAnalitics = (period) => {
    let to_stream = $(`div[id="data-report_stream"]`).attr(`data-report_stream`);
    $.get(`${baseUrl}api/analitics/generate?period=${period}&label[stream]=${to_stream}`).then((response) => {
        if (response.code === 200) {
            if (response.data.result.summary_report !== undefined) {
                summaryReporting(response.data.result, response.data.result.date_range);
            }
            if (response.data.result.revenue_flow !== undefined) {
                revenueReporting(response.data.result.revenue_flow, response.data.result.date_range);
            }
            if (response.data.result.attendance_report !== undefined) {
                attendanceReport(response.data.result.attendance_report);
            }
            if (response.data.result.salary_report !== undefined) {
                salaryReport(response.data.result.salary_report);
            }
            setTimeout(() => {
                $(`div[class~="quick_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
            }, 800);
        }
        $(`div[class~="toggle-calculator"]`).addClass("hidden");
    }).catch(() => {
        $(`div[class~="quick_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
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
            }, 1000);
        }
    }).catch(() => {
        $(`div[id="class_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
    });
}

var filter_UserGroup_Attendance = () => {
    let start_date = $(`input[name="group_start_date"]`).val(),
        end_date = $(`input[name="group_end_date"]`).val();
    $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "flex" });
    $.get(`${baseUrl}api/analitics/generate?label[stream]=attendance_report&label[start_date]=${start_date}&label[end_date]=${end_date}`).then((response) => {
        if (response.code === 200) {
            $(`div[data-chart_container="users_attendance_chart"]`).html(`<div style="width:100%;height:345px;" id="attendance_chart"></div>`);
            if (response.data.result.attendance_report !== undefined) {
                attendanceReport(response.data.result.attendance_report);
            }
            setTimeout(() => {
                $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
            }, 1000);
        }
    }).catch(() => {
        $(`div[id="users_attendance_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
    });
}

if ($(`div[id="data-report_stream"]`).length) {
    let d_period = $(`div[class~="default_period"]`).attr("data-current_period");
    loadDashboardAnalitics(d_period);

    filter.on("click", function() {
        let class_id = $(`[id="reports_insight"] select[name="class_id"]`).val(),
            _period = $(`[id="reports_insight"] select[id="filter-dashboard"]`).val();

        if (class_id !== "null") {
            _period = `${_period}&label[class_id]=${class_id}`;
        }
        loadDashboardAnalitics(_period);
    });
}