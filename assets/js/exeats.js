var create_exeat = () => {
    $(`div[id="exeatModal"]`).modal("show");
    $(`div[id="exeatModal"] h5[class="modal-title"]`).html(`Add Exeat`);
    $(`div[id="exeatModal"] form[id="ajax-data-form-content"]`).attr("action", `${baseUrl}api/exeats/create`);
    $(`div[id="exeatModal"] input, div[id="exeatModal"] textarea`).val("");
    $(`div[id="exeatModal"] select[name="status"]`).val("Pending").trigger("change");
    $(`div[id="exeatModal"] select[name="exeat_type"]`).val("").trigger("change");
    $(`div[id="exeatModal"] select[name="pickup_by"]`).val("Self").trigger("change");
}

function filter_exeats() {
    let class_id = $(`div[id="filter_Exeats_List"] select[name="class_id"]`).val(),
        status = $(`div[id="filter_Exeats_List"] select[name="status"]`).val(),
        exeat_type = $(`div[id="filter_Exeats_List"] select[name="exeat_type"]`).val(),
        pickup_by = $(`div[id="filter_Exeats_List"] select[name="pickup_by"]`).val();
    $.form_data = { status, class_id, exeat_type, pickup_by };
    loadPage(`${baseUrl}exeats_log`);
}

var update_exeat = (exeat_id) => {
    if ($.array_stream["exeat_list"] !== undefined) {
        
        let exeats = $.array_stream["exeat_list"];
        if (exeats[exeat_id] !== undefined) {
            let exeat = exeats[exeat_id];
            $(`div[id="exeatModal"] h5[class="modal-title"]`).html(`Update Exeat Record`);
            $(`div[id="exeatModal"]`).modal("show");

            let arrayValues = {
                "status": exeat.status,
                "student_id": exeat.student_id,
                "exeat_type": exeat.exeat_type,
                "departure_date": exeat.departure_date,
                "return_date": exeat.return_date,
                "pickup_by": exeat.pickup_by,
                "guardian_contact": exeat.guardian_contact,
                "reason": exeat.reason
            }

            $(`div[id="exeatModal"] input[name="exeat_id"]`).val(exeat_id);
            $.each(arrayValues, (key, value) => {
                if($.inArray(key, ["status", "pickup_by", "student_id", "exeat_type"]) !== -1) {
                    $(`div[id="exeatModal"] select[name="${key}"]`).val(value).trigger("change");
                } else if(key === "reason") {
                    $(`div[id="exeatModal"] textarea[name="${key}"]`).val(value);
                } else {
                    $(`div[id="exeatModal"] input[name="${key}"]`).val(value);
                }
            });
            $(`div[id="exeatModal"] form[id="ajax-data-form-content"]`).attr("action", `${baseUrl}api/exeats/update`);
        }
    }
}

if($(`div[id="exeats_summary_cards"]`).length > 0) {
    $.get(`${baseUrl}api/exeats/statistics`, (response) => {
        if(response.code == 200) {
            let result = response.data.result;
            $.each(result.summary.status, (key, value) => {
                $(`div[id="exeats_summary_cards"] h3[data-count="${key}"]`).text(value);
            });
            if(result.summary.overdue.total > 0) {
                $(`tbody[id="exeat_list_table"]`).html("");
                $.each(result.summary.overdue.list, (key, value) => {
                    $(`tbody[id="exeat_list_table"]`).append(`
                        <tr>
                            <td>${value.student_name}</td>
                            <td>${value.class_name}</td>
                            <td>${value.departure_date}</td>
                            <td>${value.return_date}</td>
                            <td>${value.exeat_type}</td>
                            <td>${value.pickup_by}</td>
                            <td>${value.gender}</td>
                        </tr>
                    `);
                });
            }
            $.each(result.summary.exeat_types, (key, value) => {
                $(`div[id="exeat_types"] h3[data-count="${key}"]`).text(value);
            });
            $.each(result.summary.gender, (key, value) => {
                $(`div[id="exeat_gender"] h3[data-count="${key}"]`).text(value);
            });


            if ($(`div[id="exeats_chart"]`).length) {
                var chart_label = result.chart_grouping.legend;
                console.log(chart_label);
    
                $(`div[data-chart_container="exeats_chart"]`).html(`<div id="exeats_chart" style="min-height:450px;"></div>`);
    
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
                    series: result.chart_grouping.data,
                    xaxis: {
                        categories: chart_label,
                    },
                    fill: {
                        opacity: 1
                    }
                }
                var chart = new ApexCharts(
                    document.querySelector("#exeats_chart"),
                    options
                );
                chart.render();
            }


        }
    });
}

$(`div[class~="toggle-calculator"]`).addClass("hidden");