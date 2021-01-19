var filter = $(`select[id="filter-dashboard"]`),
    bg_colors = ["l-bg-green", "l-bg-orange", "l-bg-purple", "l-bg-red", "l-bg-cyan", "l-bg-yellow", "l-bg-purple-dark", "bg-deep-orange", "bg-brown", "bg-pink", "bg-indigo", "bg-teal", "bg-light-blue", "bg-info", "bg-warning"];

var summary_Reporting = (summary) => {
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
}

var load_Dashboard_Analitics = () => {
    let period = filter.val();
    $.get(`${baseUrl}api/analitics/generate`).then((response) => {
        if (response.code === 200) {
            if (response.data.result.summary_report !== undefined) {
                summary_Reporting(response.data.result.summary_report);
            }
        }
    });
}

load_Dashboard_Analitics();

filter.on("change", function() {
    load_Dashboard_Analitics();
});