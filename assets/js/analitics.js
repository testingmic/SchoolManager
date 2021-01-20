var filter = $(`select[id="filter-dashboard"]`),
    bg_colors = [
        "l-bg-green", "l-bg-orange", "l-bg-purple", "l-bg-red", "l-bg-cyan",
        "l-bg-yellow", "l-bg-purple-dark", "bg-deep-orange", "bg-brown", "bg-pink",
        "bg-indigo", "bg-teal", "bg-light-blue", "bg-info", "bg-warning", "bg-dark"
    ];

function format_currency(total) {
    var neg = false;
    if (total < 0) {
        neg = true;
        total = Math.abs(total);
    }
    return (neg ? "-" : '') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
}

var summary_Reporting = (summary, date_range) => {
    if (summary.users_record_count !== undefined) {
        let user = summary.users_record_count;
        let employees = parseInt(user.count.total_employees_count) + parseInt(user.count.total_accountants_count) + parseInt(user.count.total_admins_count);
        $(`span[data-count="total_employees_count"]`).html(employees);
        $(`span[data-count="total_students_count"]`).html(user.count.total_students_count);
        $(`span[data-count="total_teachers_count"]`).html(user.count.total_teachers_count);
        $(`span[data-count="total_parents_count"]`).html(user.count.total_parents_count);
        // $(`span[data-count="total_employees_count"]`).html(user.comparison.current.total_employees_count);
        // $(`span[data-count="total_students_count"]`).html(user.comparison.current.total_students_count);
        // $(`span[data-count="total_teachers_count"]`).html(user.comparison.current.total_teachers_count);
        // $(`span[data-count="total_parents_count"]`).html(user.comparison.current.total_parents_count);
    }

    if (summary.students_class_record_count !== undefined) {
        let classes = summary.students_class_record_count,
            class_count_list = "",
            key = 0;
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
        console.log(chartKeys);
        console.log(currentValues);
        console.log(previousValues);
        $(`span[data-count="total_revenue_received"]`).html(format_currency(total_revenue));
        $(`span[data-count="previous_amount_received"]`).html(format_currency(previous_amount));
        $(`span[data-count="total_fees_received"]`).html(format_currency(fees.amount.tuition_fees));
        $(`div[class~="quick_loader"] div[class~="form-content-loader"]`).css({ "display": "none" });

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
}

var load_Dashboard_Analitics = () => {
    let period = filter.val();
    $.get(`${baseUrl}api/analitics/generate`).then((response) => {
        if (response.code === 200) {
            if (response.data.result.summary_report !== undefined) {
                summary_Reporting(response.data.result.summary_report, response.data.result.date_range);
            }
        }
    });
}

load_Dashboard_Analitics();

filter.on("change", function() {
    load_Dashboard_Analitics();
});