var filter = $(`select[id="filter-dashboard"]`),
    bg_colors = [
        "l-bg-green", "l-bg-orange", "l-bg-purple", "l-bg-red", "l-bg-cyan",
        "l-bg-yellow", "l-bg-purple-dark", "bg-deep-orange", "bg-brown", "bg-pink",
        "bg-indigo", "bg-teal", "bg-light-blue", "bg-info", "bg-warning", "bg-dark"
    ],
    to_stream = "";

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
            currentValues = new Array(),
            previousValues = new Array();
        $.each(fees.amount, function(i, e) {
            total_revenue += parseInt(e);
        });
        $.each(summary.fees_record_count.comparison.amount.previous, function(i, e) {
            chartKeys.push(e.name);
            previousValues.push(parseInt(e.value));
            previous_amount += parseInt(e.value);
            currentValues.push(parseInt(summary.fees_record_count.comparison.amount.current[i].value));
        });

        $(`span[data-count="total_revenue_received"]`).html(format_currency(total_revenue));
        $(`span[data-count="previous_amount_received"]`).html(format_currency(previous_amount));
        $(`span[data-count="total_fees_received"]`).html(format_currency(fees.amount.tuition_fees));

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
                name: 'Current Revenue',
                data: currentValues
            }, {
                name: 'Previous Revenue',
                data: previousValues
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
        console.log(_attendance.attendance.days_list);
        $.each(_attendance.attendance.days_list, function(day, status) {
            attendance_chart_list += `
            <div class='col-lg-3 col-md-6'>
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

var loadDashboardAnalitics = () => {
    let to_stream = $(`div[id="data-report_stream"]`).attr(`data-report_stream`);
    $.get(`${baseUrl}api/analitics/generate?label[stream]=${to_stream}`).then((response) => {
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
            setTimeout(() => {
                $(`div[class~="quick_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });
            }, 1000);
        }
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
    loadDashboardAnalitics();
    filter.on("change", function() {
        loadDashboardAnalitics();
    });
}