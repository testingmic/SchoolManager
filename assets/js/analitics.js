let filter = $(`select[id="filter-dashboard"]`);

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