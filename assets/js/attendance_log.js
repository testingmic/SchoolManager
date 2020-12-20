var attendance_content = $(`div[id="attendance_log_list"]`);

var list_userAttendance = (query = "") => {
    let date_range = $(`input[name="attendance_date"]`).val();
    $.get(`${baseUrl}api/attendance/display_attendance?${query}date_range=${date_range}`).then((response) => {
        if (response.code == 200) {
            attendance_content.html(response.data.result.table_content);
        }
    });
}
$(`select[id="attendance_category"]`).on("change", () => {
    let value = $(this).val();
    if (value == "student") {
        $.get(`${baseUrl}api/classes/list?columns=id,item_id,name`).then((response) => {
            if (response.code == 200) {
                $(`div[class~="attendance_category_list"]`).removeClass("hidden");
                $(`select[name="attendance_class"]`).find('option').remove().end();
                $(`select[name="attendance_class"]`).append(`<option value="null" selected="selected">Select Class</option>`);
                $.each(response.data.result, (_, e) => {
                    $(`select[name="attendance_class"]`).append(`<option data-item_id="${e.item_id}" value='${e.id}'>${e.name}</option>'`);
                });
            }
        });
    } else {
        $(`div[class~="attendance_category_list"]`).addClass("hidden");
    }
});

$(`select[id="attendance_class"]`).on("change", () => {
    let value = $(this).val();
    if (value !== "null") {
        list_userAttendance(`class_id=${value}&user_type=student&`);
    }
});