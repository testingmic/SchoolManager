var checkAllState = () => {
    $(`select[id='select_for_all']`).on("change", function() {
        let value = $(this).val();
        if (value !== "null") {
            $(`table[id="attendance_logger"] input[type='radio'][value='${value}']`).prop("checked", true);
        } else {
            $(`table[id="attendance_logger"] input[type='radio']`).prop("checked", false);
        }
    });
}

var list_userAttendance = (query = "") => {
    let attendance_content = $(`div[id="attendance_log_list"]`),
        date_range = $(`input[name="attendance_date"]`).val();
    $.get(`${baseUrl}api/attendance/display_attendance?${query}date_range=${date_range}`).then((response) => {
        if (response.code == 200) {
            $(`div[class~="refresh_attendance_list"]`).removeClass("hidden");
            attendance_content.html(response.data.result.table_content);
            checkAllState();
        }
        $(`button[class~="refresh"]`).html(`<i class='fa fa-notch'></i> Refresh`).prop("disabled", false);
    });
}

var save_AttendanceLog = (date, user_type = "", class_id = "") => {
    let attendance = {};
    $.each($(`table[id="attendance_logger"] input[type='radio']`), function(i, e) {
        let user_id = $(this).attr("data-user_id");
        attendance[user_id] = $(`input[type='radio'][data-user_id='${user_id}']:checked`).val();
    });
    swal({
        title: "Log Attendance",
        text: "Are you sure you want to log attendance?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/attendance/log`, { date, attendance, user_type, class_id }).then((response) => {
                let m_icon = "error"
                if (response.code == 200) {
                    m_icon = "success";

                    if ($(`select[id="attendance_class"]`).length) {
                        $(`select[id="attendance_class"]`).trigger("change");
                    }
                }
                swal({
                    position: "top",
                    text: response.data.result,
                    icon: m_icon,
                });
            });
        }
    });
}

var finalize_AttendanceLog = (date, user_type = "", class_id = "", finalize) => {
    let attendance = {};
    $.each($(`table[id="attendance_logger"] input[type='radio']`), function(i, e) {
        let user_id = $(this).attr("data-user_id");
        attendance[user_id] = $(`input[type='radio'][data-user_id='${user_id}']:checked`).val();
    });
    swal({
        title: "Finalize Attendance Log",
        text: "Are you sure you want to finalize attendance log? \n \
        Note that you cannot change the information once it has been finalized",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/attendance/log`, { date, attendance, user_type, class_id, finalize }).then((response) => {
                let m_icon = "error"
                if (response.code == 200) {
                    m_icon = "success";
                    $(`table[id="attendance_logger"] input[type='radio']`).prop("disabled", true);
                    $(`select[id="attendance_class"]`).trigger("change");
                }
                swal({
                    position: "top",
                    text: response.data.result,
                    icon: m_icon,
                });
            });
        }
    });
}

$(`select[id="attendance_category"]`).on("change", function() {
    let value = $(this).val(),
        attendance_content = $(`div[id="attendance_log_list"]`);
    attendance_content.html(`<div class="text-center font-italic">Users list is displayed here.</div>`);
    if (value == "null") {
        $(`div[class~="attendance_category_list"], div[class~="refresh_attendance_list"]`).addClass("hidden");
    } else if (value == "student") {
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

$(`select[id="attendance_class"]`).on("change", function() {
    let value = $(this).val();
    if (value !== "null") {
        $(`button[class~="refresh"]`).html(`Refreshing record <i class='fa fa-spin fa-spinner'></i>`).prop("disabled", true);
        list_userAttendance(`class_id=${value}&user_type=student&`);
    } else {
        $(`div[class~="refresh_attendance_list"]`).addClass("hidden");
    }
});

var refresh_AttendanceLog = () => {
    let class_id = $(`select[id="attendance_class"]`).val(),
        category = $(`select[name="attendance_category"]`).val();
    $(`button[class~="refresh"]`).html(`Refreshing record <i class='fa fa-spin fa-spinner'></i>`).prop("disabled", true);
    list_userAttendance(`class_id=${class_id}&user_type=${category}&`);
}