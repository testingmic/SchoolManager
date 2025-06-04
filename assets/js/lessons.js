var show_Attendance_Grading_Log_Form = (studentId, student_name, type = "", section = "attendance") => {
    $(`div[id="log_grading_attendance"] span[data="student_name"]`).html(`: ${student_name.toUpperCase()}`);
    $(`div[id="log_grading_attendance"] div[class="jsCalendar"] table td`)
        .removeClass("jsCalendar-selected jsCalendar-current")
        .attr("title", "");
    $(`div[id="log_grading_attendance"] input[name="student_name"]`).val(student_name);
    $(`div[id="log_grading_attendance"] table td`).removeClass("grading_selected grading_selected_item").attr("title", "");
    $(`div[id="log_grading_attendance"] input[name="student_id"]`).val(studentId);
    $(`div[id="log_grading_attendance"] input[name="grading_type"]`).val(type);
    $(`button[class~="submit_button"]`).removeClass("hidden");
    $(`input[name="allow_selection"]`).val(1);
    $(`div[id="log_grading_attendance"]`).modal("show");
    if(section == "attendance") {
        $(`span[data="title"]`).html("Attendance Log");
    } else {
        $(`div[data-container="review"]`).addClass("hidden");
        $(`div[id="log_grading_attendance"] div[data-container="new"]`).removeClass("hidden");
        $(`span[data="title"]`).html(`<span class="text-primary">${type.toUpperCase()}-</span> Grade Student`).addClass();
    }
}

var clear_form = () => {
    $(`div[id="attedance_selector"] span`).removeClass("bg-selected");
    $(`div[id="log_grading_attendance"] div[class="jsCalendar"] table td`).removeClass("jsCalendar-selected jsCalendar-current");
    $(`div[id="log_grading_attendance"] span[data="student_name"]`).html(``);
    $(`div[id="log_grading_attendance"] input[name="student_id"], textarea[name="comments"]`).val(``);
    $(`div[id="log_grading_attendance"] textarea[name="comments"]`).attr("readonly", false);
    $(`input[name="allow_selection"]`).val(1);
    $(`div[id="log_grading_attendance"]`).modal("hide");
}

var log_Student_Grading = () => {

    let grade_div = $(`div[id="log_grading_attendance"] table td[class~='grading_selected']`),
        today = $(`div[id="log_grading_attendance"] input[name="grading_date"]`).val();

    if(!today.length) {
        swal({text: "Sorry! Please select the date to proceed.", icon: "error" });
        return false;
    }

    if(!grade_div.length) {
        swal({text: "Sorry! Please select the grade of this student.", icon: "error" });
        return false;
    }

    let grade = grade_div.attr("data-grade_value"),
        comments = $(`div[id="log_grading_attendance"] textarea[name="comments"]`).val(),
        course_id = $(`div[id="log_grading_attendance"] input[name="course_id"]`).val(),
        student_id = $(`div[id="log_grading_attendance"] input[name="student_id"]`).val(),
        student_name = $(`div[id="log_grading_attendance"] input[name="student_name"]`).val(),
        timetable_id = $(`div[id="log_grading_attendance"] input[name="timetable_id"]`).val(),
        class_id = $(`div[id="log_grading_attendance"] input[name="class_id"]`).val(),
        grade_type = $(`div[id="log_grading_attendance"] input[name="grading_type"]`).val();

    if(!student_id.length) {
        swal({text: "Sorry! Student ID has not yet been specified", icon: "error" });
        return false;
    }

    let data = {course_id, timetable_id, class_id, student_id, student_name, comments, date: today, grade, grade_type};

    swal({
        title: "Save Student Grade",
        text: `Do you want to proceed to save the Student Grade?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.post(`${baseUrl}api/timetable/save_grade`, data).then((response) => {
                if(response.code == 200) {
                    clear_form();
                    let t_data = `<span item_date="${response.data.additional.raw_date}" onclick='return show_Grading_Log("${student_id}","${response.data.additional.raw_date}","${grade_type}","${student_name}");' class="grading_log">${response.data.additional.date}<br>Score: <strong class='font-20'>${response.data.additional.grade}</strong></span>`;
                    if($(`tr[data-row_id="${student_id}"] td[a_state="${grade_type}"] span[class="grading_log"]`).length) {
                        $(`tr[data-row_id="${student_id}"] td[a_state="${grade_type}"] span[class="grading_log"]:last`).after(t_data);
                    } else {
                        let btn = `<button onclick="return show_Attendance_Grading_Log_Form('${student_id}','${student_name}','${grade_type}','grading');" class="btn btn-secondary font-bold font-14 bg-black pt-1 pb-1">New</button>`;
                        $(`tr[data-row_id="${student_id}"] td[a_state="${grade_type}"]`).html(`${t_data}${btn}`);
                    }
                    $.array_stream.students_attendance_grading_list[student_id][grade_type] = response.data.additional.record;
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
            }).catch(() => {
            });
        }
    });
}

var log_Student_Attendance = () => {
    let attendance = "",
        comments = $(`div[id="log_grading_attendance"] textarea[name="comments"]`).val(),
        course_id = $(`div[id="log_grading_attendance"] input[name="course_id"]`).val(),
        student_id = $(`div[id="log_grading_attendance"] input[name="student_id"]`).val(),
        student_name = $(`div[id="log_grading_attendance"] input[name="student_name"]`).val(),
        timetable_id = $(`div[id="log_grading_attendance"] input[name="timetable_id"]`).val(),
        class_id = $(`div[id="log_grading_attendance"] input[name="class_id"]`).val();

    $.each($(`div[id="log_grading_attendance"] span`), function() {
        let item = $(this);
        if(item.hasClass("bg-selected")) {
            attendance = item.attr("data-option");
            return;
        } 
    });

    if(!student_id.length) {
        swal({text: "Sorry! Student ID has not yet been specified", icon: "error" });
        return false;
    }
    if(!attendance.length) {
        swal({text: "Sorry! Please select the attendance state for this student.", icon: "error" });
        return false;
    }
    let data = {course_id, timetable_id, class_id, student_id, student_name, attendance, comments};

    swal({
        title: "Log Attendance",
        text: `Do you want to proceed to log the attendance for ${student_name}?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.post(`${baseUrl}api/timetable/log_attendance`, data).then((response) => {
                if(response.code == 200) {
                    clear_form();
                    let t_data = `<span onclick='return show_Attendance_Log("${student_id}","${response.data.additional.date}","${student_name}");' class='attendance_log'>${response.data.additional.date}<br><strong>${response.data.additional.state}</strong></span>`;
                    if($(`tr[data-row_id="${student_id}"] span[class="attendance_log"]`).length) {
                        $(`tr[data-row_id="${student_id}"] span[class="attendance_log"]:last`).after(t_data);
                    } else {
                        $(`tr[data-row_id="${student_id}"] td[class="attendance_content"]`).html(t_data);
                    }                    
                    $(`tr[data-row_id="${student_id}"] button[class~='btn-secondary']`).remove();
                    $.each(response.data.additional.summary, function(i, e) {
                        $(`tr[data-row_id="${student_id}"] td[class="attendance_count"][a_state='${i}']`).html(e);
                    });
                    $.array_stream.students_attendance_grading_list[student_id] = response.data.additional.record;
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
            }).catch(() => {
            });
        }
    });
}

var show_Grading_Log = (student_id, _date, grade_type, student_name = "") => {
    if($.array_stream.students_attendance_grading_list !== undefined) {
        $(`table td`).removeClass("grading_selected_item");
        if($.array_stream.students_attendance_grading_list[student_id] !== undefined) {
            let grading = $.array_stream.students_attendance_grading_list[student_id];
            if(grading[grade_type]["dates"][_date] !== undefined) {
                let _grading = grading[grade_type]["dates"][_date];
                $(`button[class~="submit_button"], div[data-container="new"]`).addClass("hidden");
                $(`div[id="log_grading_attendance"] textarea[name="comments"]`)
                    .val(_grading.comments)
                    .attr("readonly", true);
                $(`input[name="_date"]`).val(_date);
                $(`input[name="_grade"]`).val(_grading.grade);
                $(`div[data-container="review"]`).removeClass("hidden");
                $(`div[id="log_grading_attendance"] span[data="student_name"]`).html(`: ${student_name.toUpperCase()}`);
                $(`span[data="title"]`).html(`<span class="text-primary">${grade_type.toUpperCase()}-</span> Grade Student`).addClass();
                $(`div[id="log_grading_attendance"]`).modal("show");
            }
        }
    }
}

var show_Attendance_Log = (student_id, _date, student_name = "") => {
    if($.array_stream.students_attendance_grading_list !== undefined) {
        if($.array_stream.students_attendance_grading_list[student_id] !== undefined) {
            let attendance = $.array_stream.students_attendance_grading_list[student_id];
            let _attendance = attendance[_date];
            $(`input[name="allow_selection"]`).val(0);
            $(`div[id="log_grading_attendance"]`).modal("show");
            $(`div[id="log_grading_attendance"] textarea[name="comments"]`)
                .val(_attendance.comments)
                .attr("readonly", true);
            $(`button[class~="submit_button"]`).addClass("hidden");
            $(`span[data-option="${_attendance.status}"]`).addClass("bg-selected");
            $(`div[id="log_grading_attendance"] span[data="student_name"]`).html(`: ${student_name.toUpperCase()}`);
        }
    }
}

var export_Assessment_Marks = (assignment_id, assessment_type, class_id, course_id) => {
    swal({
        title: "Export Marks to Gradebook",
        text: `Do you want to export the marks awarded to the Students for this ${assessment_type} to Grading?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.pageoverlay.show();
            let timetable_id = $(`div[id="assessment_container"] input[name="timetable_id"]`).val();

            let data = {course_id, timetable_id, class_id, assignment_id};

            $.post(`${baseUrl}api/assignments/export_grade`, data).then((response) => {
                if(response.code == 200) {
                    setTimeout(() => {
                        loadPage(current_url);                        
                    }, 1000);
                }
                $.pageoverlay.hide();
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
            }).catch(() => {
                $.pageoverlay.hide();
            });
        }
    });
}

var log_Bulk_Attendance = () => {
    let li = {},
        course_id = $(`div[id="bulk_Attendance_Log"] input[name="course_id"]`).val(),
        timetable_id = $(`div[id="bulk_Attendance_Log"] input[name="timetable_id"]`).val(),
        class_id = $(`div[id="bulk_Attendance_Log"] input[name="class_id"]`).val();

    $.each($(`div[id="bulk_Attendance_Log"] span[class~="hover-border"]`), function() {
        let item = $(this);
        if(item.hasClass("bg-selected")) {
            let opt = item.attr("data-option"),
                sid = item.attr("data-student_row_id"),
                name = $(`div[id="student"][data-student_row_id="${sid}"] div[class~="card-header"] strong`).text();
                com = $(`div[id="student"][data-student_row_id="${sid}"] input[name="comments"]`).val();
            li[sid] = {
                o: opt, c: com, n: name
            };
            return;
        } 
    });

    let data = {course_id, timetable_id, class_id, li};

    swal({
        title: "Log Attendance",
        text: `Do you want to proceed to log the attendance for these students? Please note that you cannot change once confirmed.`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.pageoverlay.show();
            $.post(`${baseUrl}api/timetable/bulk_attendance`, data).then((response) => {
                $.pageoverlay.hide();
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
                if(response.code == 200) {
                    $(`div[id="bulk_Attendance_Log"]`).modal("hide");
                    $(`div[id="bulk_Attendance_Log"] div[class~="modal-body"]`).html(``);
                    setTimeout(() => {
                        loadPage(current_url);
                    }, refresh_seconds);
                }
            }).catch(() => {
                $.pageoverlay.hide();
            });
        }
    });

}

var bulk_Attendance_Selection = () => {
    $(`div[class="attedance_selector"] span`).on("click", function() {
        let item = $(this),
            t_color = "success",
            option = item.attr("data-option"),
            student_row_id = item.attr("data-student_row_id");
        let allow_selection = parseInt($(`div[class="attedance_selector"][data-student_row_id="${student_row_id}"]`).attr("data-allow_selection"));
        if(allow_selection == 1) {
            $(`div[class="attedance_selector"][data-student_row_id="${student_row_id}"] span`).removeClass("bg-selected");
            item.addClass("bg-selected");
            t_color = (option == "absent") ? "danger" : ((option == "late" ? "warning" : "success"));
            $(`div[id="student"][data-student_row_id="${student_row_id}"] span[class~="selected"]`).html(`<i class="text-${t_color} fa fa-check-circle"></i>`);
        }
    });
}

var show_Bulk_Attendance = () => {
    $(`div[id="bulk_Attendance_Log"]`).modal("show");
    let content = $(`div[id="bulk_Attendance_Log"] div[class~="modal-body"]`),
        no_available_student = `<tr><td class="text-center">No student was found.</td></tr>`;

    let data =`<div class="row">`;
    if($.array_stream["students_array_list"] !== undefined) {
        let row = 0,
            style = 'style="border:solid 1px #fff"';
        $.each($.array_stream["students_array_list"], function(i, e) {
            row++;
            // set the student name
            let student_name = e.name.toUpperCase();

            // get the first 20 characters of the student name
            student_name = student_name.substring(0, 25);

            data += `
            <div id="student" class="col-lg-4 col-md-6 mb-2" data-student_row_id="${i}">
                <div class="card mb-2 pb-0">
                    <div class="card-header p-0 pl-1" style="display:block">
                        <strong>${student_name}</strong> <span class="selected"></span>
                    </div>
                    <div class="card-body p-1">
                        <div class="attedance_selector" data-allow_selection="1" data-student_row_id="${i}" align="center">
                            <span data-student_row_id="${i}" ${style} data-option="present" class="badge cursor mb-1 hover-border badge-success">Present</span>
                            <span data-student_row_id="${i}" ${style} data-option="late" class="badge cursor mb-1 hover-border bg-warning">Late</span>
                            <span data-student_row_id="${i}" ${style} data-option="absent" class="badge cursor mb-1 hover-border badge-danger">Absent</span>
                        </div>
                        <div class="p-2">
                            <input type="text" name="comments" placeholder="Add comments" class="form-control font-12 font-italic" style="height:30px">
                        </div>
                    </div>
                </div>
            </div>`;
        });
    }
    data += `</div>`;

    content.html(data);
    bulk_Attendance_Selection();

}

if($.array_stream["performance_chart"] !== undefined) {
    let percentages = new Array(),
        labels = new Array(),
        summation = $.array_stream["performance_chart"]["summation"];
    $.each($.array_stream["performance_chart"]["overall"], function(e, i) {
        labels.push(e.toUpperCase());
        let percent = (i / summation) * 100;
        percentages.push(parseFloat(percent.toFixed(2)));
    });
    if ($(`canvas[id="student_performance"]`).length) {
        var ctx = document.getElementById("student_performance");
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: '# of Reactions',
                    data: percentages,
                    backgroundColor: ['#54ca68', '#ffa426', '#cdd3d8', '#6777ef'],
                    borderColor: ['#fff']
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

$(`select[name="lesson_subject_id"]`).on("change", function() {
    let value = $(this).val();
    loadPage(`${baseUrl}gradebook/${value}`);
});

$(`div[id="log_grading_attendance"] button[data-dismiss="modal"]`).on("click", function(evt) {
    evt.preventDefault();
    clear_form();
});

$(`div[id="attedance_selector"] span`).on("click", function() {
    let item = $(this),
        allow_selection = parseInt($(`input[name="allow_selection"]`).val());
    if(allow_selection == 1) {
        $(`div[id="attedance_selector"] span`).removeClass("bg-selected");
        item.addClass("bg-selected");
    }
});

$(`div[id="log_grading_attendance"] table td[class='grade_select']`).on("click", function(){
    let item = $(this),
        allow_selection = parseInt($(`input[name="allow_selection"]`).val());
    if(allow_selection == 1) {
        $(`div[id="log_grading_attendance"] table td`).removeClass("grading_selected");
        item.addClass("grading_selected");
    }
});
student_fullname_search();