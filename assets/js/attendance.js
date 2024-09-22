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

var fullname_search = () => {
    $.expr[':'].Contains = function(a,i,m){
        return $(a).text().toUpperCase().indexOf(m[3].toUpperCase())>=0;
    };
    $(`div[id="attendance_search_input"] input[name="attendance_fullname"]`).on("input", function(event) {
        let input = $(this).val();
        $(`tr[data-row_search='name']`).addClass('hidden');
        $(`tr[data-row_search='name'][data-attandance_fullname]:Contains(${input}), tr[data-row_search='name'][data-attendance_unique_id]:Contains(${input})`).removeClass('hidden');
    });
}

var list_userAttendance = (query = "") => {
    let attendance_content = $(`div[id="attendance_log_list"]`),
        attendance_log_summary = $(`div[id="attendance_log_summary"]`),
        date_range = $(`input[name="attendance_date"]`).val();
    $.get(`${baseUrl}api/attendance/display_attendance?${query}date_range=${date_range}`).then((response) => {
        if (response.code == 200) {
            $(`div[class~="refresh_attendance_list"]`).removeClass("hidden");
            attendance_content.html(response.data.result.table_content);
            attendance_log_summary.html(response.data.result.bottom_data);
            checkAllState();
        }
        $(`div[id="attendance"] div[class="form-content-loader"]`).css({ "display": "none" });
        $(`button[class~="refresh"]`).html(`<i class='fa fa-notch'></i> Refresh`).prop("disabled", false);
        fullname_search();
    }).catch(() => {
        $(`button[class~="refresh"]`).html(`<i class='fa fa-notch'></i> Refresh`).prop("disabled", false);
        $(`div[id="attendance"] div[class="form-content-loader"]`).css({ "display": "none" });
    });
}

var save_AttendanceLog = (date, user_type = "", class_id = "") => {
    swal({
        title: "Log Attendance",
        text: "Are you sure you want to log attendance?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let attendance = {};
            $.each($(`table[id="attendance_logger"] input[type='radio']`), function(i, e) {
                let user_id = $(this).attr("data-user_id");
                attendance[user_id] = {
                    status: $(`input[type='radio'][data-user_id='${user_id}']:checked`).val(),
                    comments: $(`input[id='comments'][data-user_id='${user_id}']`).val()
                };
            });
            $.pageoverlay.show();
            $.post(`${baseUrl}api/attendance/log`, { date, attendance, user_type, class_id }).then((response) => {
                let m_icon = "error";
                $.pageoverlay.hide();
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
            }).catch(() => {
                $.pageoverlay.hide();
                swal({ text: swalnotice.ajax_error, icon: "error", });
            });
        }
    });
}

var finalize_AttendanceLog = (date, user_type = "", class_id = "", finalize) => {
    let attendance = {};
    $.each($(`table[id="attendance_logger"] input[type='radio']`), function(i, e) {
        let user_id = $(this).attr("data-user_id");
        attendance[user_id] = {
            status: $(`input[type='radio'][data-user_id='${user_id}']:checked`).val(),
            comments: $(`input[id='comments'][data-user_id='${user_id}']`).val()
        };
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
            }).catch(() => {
                swal({ text: swalnotice.ajax_error, icon: "error", });
            });
        }
    });
}

$(`select[id="attendance_category"]`).on("change", function() {
    let value = $(this).val();
    $(`div[id="attendance_log_summary"]`).html(``);
    $(`div[id="attendance_log_list"]`).html(`<div class="text-center font-italic">Users list is displayed here.</div>`);
    if (value == "null") {
        $(`div[class~="attendance_category_list"], div[class~="refresh_attendance_list"]`).addClass("hidden");
    } else if (value == "student") {
        let category = $(`select[id="attendance_category"]`).val();
        $.get(`${baseUrl}api/classes/list?columns=a.id,a.item_id,a.name,a.payment_module&filter=${category}&class_teacher=${$myPrefs.userId}`).then((response) => {
            if (response.code == 200) {
                $(`div[class~="attendance_category_list"]`).removeClass("hidden");
                $(`select[name="attendance_class"]`).find('option').remove().end();
                $(`select[name="attendance_class"]`).append(`<option value="null" selected="selected">Select Class</option>`);
                $.each(response.data.result, (_, e) => {
                    $(`select[name="attendance_class"]`).append(`<option data-payment_module="${e.payment_module}" data-item_id="${e.item_id}" value='${e.id}'>${e.name.toUpperCase()}</option>'`);
                });
            }
        });
    } else {
        $(`div[id="attendance"] div[class="form-content-loader"]`).css({ "display": "flex" });
        $(`div[class~="refresh_attendance_list"]`).removeClass("hidden");
        $(`div[class~="attendance_category_list"]`).addClass("hidden");
        list_userAttendance(`user_type=${value}&`);
    }
});

$(`select[id="attendance_class"]`).on("change", function() {
    let value = $(this).val(),
        category = $(`select[name="attendance_category"]`).val();
    $(`a[id="download_link"]`).addClass("hidden");
    if (value !== "null") {
        value = category === "staff" ? "" : value;
        $(`button[class~="refresh"]`).html(`Refreshing record <i class='fa fa-spin fa-spinner'></i>`).prop("disabled", true);
        $(`div[id="attendance"] div[class="form-content-loader"]`).css({ "display": "flex" });
        list_userAttendance(`class_id=${value}&user_type=${category}&`);
    } else {
        $(`div[class~="refresh_attendance_list"]`).addClass("hidden");
    }
});

var refresh_AttendanceLog = () => {
    let class_id = $(`select[id="attendance_class"]`).val(),
        category = $(`select[name="attendance_category"]`).val();
    $(`div[id="attendance"] div[class="form-content-loader"]`).css({ "display": "flex" });
    $(`button[class~="refresh"]`).html(`Refreshing record <i class='fa fa-spin fa-spinner'></i>`).prop("disabled", true);
    class_id = category === "staff" ? "" : class_id;
    list_userAttendance(`class_id=${class_id}&user_type=${category}&`);
}

$(`div[id="attendance_report"] select[id="user_type"]`).on("change", function() {
    let value = $(this).val();
    $(`a[id="download_link"]`).addClass("hidden");
    $(`div[id="attendance_log_summary"]`).html(``);
    $(`div[class="attendance_log_record"]`).html(`<div class="text-center font-italic">Attendance record will be displayed here</div>`);
    if (value == "null") {
        $(`div[id="attendance_report"] select[name="class_id"]`).val("").change();
        $(`div[id="classes_list"]`).addClass("hidden");
    } else if (value == "student") {
        $(`div[id="classes_list"]`).removeClass("hidden");
    } else {
        $(`div[id="attendance_report"] select[name="class_id"]`).val("").change();
        $(`div[id="classes_list"]`).addClass("hidden");
    }
});

var load_attendance_log = () => {
    let class_id = $(`select[name="class_id"]`).val(),
        month_year = $(`input[name="month_year"]`).val(),
        user_type = $(`select[name="user_type"]`).val();

    if (user_type === "") {
        swal({
            text: "Sorry! Please select the user category to continue.",
            icon: "error",
        });
    } else if ((user_type == "student") && (class_id === "")) {
        swal({
            text: "Sorry! Please select the class id to continue.",
            icon: "error",
        });
    } else if (month_year === "") {
        swal({
            text: "Sorry! Please select the month and year to continue.",
            icon: "error",
        });
    } else {
        $(`a[id="download_link"]`).addClass("hidden");
        $.pageoverlay.show();
        $.get(`${baseUrl}api/attendance/attendance_report`, { class_id, month_year, user_type }).then((response) => {
            if (response.code == 200) {
                let attendance_list = response.data.result;
                $(`div[class="attendance_log_record"]`).html(attendance_list.table_content);
                $(`a[id="download_link"]`).removeClass("hidden").attr("href", `${baseUrl}download/attendance?class_id=${class_id}&month_year=${month_year}&user_type=${user_type}&att_d=true`);
                if ($('.datatable').length > 0) {
                    $('.datatable').dataTable({
                        search: null,
                        lengthMenu: [
                            [20, 50, 75, 100, 200, -1],
                            [20, 50, 75, 100, 200, "All"]
                        ],
                        language: {
                            sEmptyTable: "Nothing Found",
                            lengthMenu: "Display _MENU_ rows"
                        }
                    });
                }
            } else {
                swal({ text: response.data.result, icon: "error", });
            }
            $.pageoverlay.hide();
        }).catch(() => {
            $.pageoverlay.hide();
            swal({ text: swalnotice.ajax_error, icon: "error", });
        });
    }
}

function checkForEmptyTable(table){
    let tableCols = table.find("thead tr th").length;
    let tableRows = table.find("tbody tr:visible, div[class~='product-title-cell']:visible").length;
    if(!tableRows) {
      table.find("tbody").append(`<tr class='temp-row'><td colspan='4' class='text-center'>No Item Found</td></tr>`);
    } else if(tableRows == 1) {
      let searchItem = $(`input[id="products-search-input"]`).val();
      $(`td[data-product-code="${searchItem}"] div[class~="checkbox-single"] input[type="checkbox"], div[data-product-code="${searchItem}"] div[class~="checkbox-single"] input[type="checkbox"]`).prop('checked', true).trigger('change');
    }
}