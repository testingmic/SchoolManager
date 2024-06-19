var promote_display = $(`div[id="promote_Student_Display"]`),
    default_class_guid = null;

var promote_All_Changer = () => {
    $(`input[id="promote_all_student"]`).on("click", function(evt) {
        let value = $(`input[id="promote_all_student"]:checked`).length;
        if (value) {
            $(`input[class~="student_to_promote"]`).prop("checked", true);
        } else {
            $(`input[class~="student_to_promote"]`).prop("checked", false);
        }
    });
}

var promote_Students = () => {
    let promote_to = $(`select[name="promote_to"]`).val(),
        promote_from = $(`select[name="class_id"]`).val(),
        students_array = new Array();

    if (!promote_to.length || promote_to == "null") {
        swal({
            text: `Sorry! You must first select the class to promote the selected students to.`,
            icon: "error",
        });
        return false;
    }
    if (promote_from == promote_to) {
        swal({
            text: `Sorry! The selected students cannot be promoted to the same class.`,
            icon: "error",
        });
        return false;
    }

    $.each($(`input[class~="student_to_promote"]:checked`), function() {
        students_array.push($(this).val());
    });

    let promote_count = students_array.length,
        message = "",
        default_message = "Kindly note that you cannot update this request once submitted. Are you sure you want to proceed with the request?";
    if (!promote_count) {
        message = `You are about to submit the form without promoting any student. ${default_message}`;
    } else {
        message = `You are about to promote ${promote_count} selected students. ${default_message}`;
    }
    swal({
        title: "Promote Students",
        text: message,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let students_list = students_array.join(",");
            $(`input[class~="student_to_promote"], input[id="promote_all_student"]`).prop("disabled", true);
            $(`button[id="promote_students_button"]`).prop({ "disabled": true }).html(`Processing Request <i class="fa fa-spin fa-spinner"></i>`);
            $.post(`${baseUrl}api/promotion/promote`, { promote_from, promote_to, students_list }).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code !== 200) {
                    $(`input[class~="student_to_promote"], input[id="promote_all_student"]`).prop("disabled", false);
                    $(`button[id="promote_students_button"]`).prop({ "disabled": true }).html(`<i class="fa fa-assistive-listening-systems"></i> Promote Students`);
                } else {
                    setTimeout(() => {
                        loadPage(`${baseUrl}promotion/${response.data.additional.href}`);
                    }, refresh_seconds);
                }
            }).catch(() => {
                $(`button[id="promote_students_button"]`).prop({ "disabled": false }).html(`<i class="fa fa-assistive-listening-systems"></i> Promote Students`);
                $(`input[class~="student_to_promote"], input[id="promote_all_student"]`).prop("disabled", false);
                swal({
                    text: swalnotice["ajax_error"],
                    icon: "error",
                });
            });
        }
    });
}

var modify_Student_Promotion = (history_id) => {
    let students_array = new Array(),
        option = $(`div[id="promote_list"] select[name="bulk_modify"]`).val();

    if(!option.length) {
        notify("Sorry! You must first select the option to proceed");
        return false;
    }

    $.each($(`input[class~="student_to_promote"]:checked`), function() {
        students_array.push($(this).val());
    });

    let promote_count = students_array.length,
        message = "";

    if (!promote_count) {
        notify(`Sorry! Kindly select at least one student to proceed.`);
        return false;
    }
    message = `You are about to ${option} ${promote_count} selected students. Do you wish to proceed?`;


    swal({
        title: `${option.toUpperCase()} STUDENTS`,
        text: message,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            let students_list = students_array.join(",");
            $(`div[id="promote_list"] input[class~="student_to_promote"], div[id="promote_list"] button[id="bulk_modify"]`).prop("disabled", true);
            $.post(`${baseUrl}api/promotion/modify`, { history_id, option, students_list }).then((response) => {
                $.pageoverlay.hide();
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code !== 200) {
                    $(`div[id="promote_list"] input[class~="student_to_promote"], div[id="promote_list"] button[id="bulk_modify"]`).prop("disabled", false);
                } else {
                    setTimeout(() => {
                        loadPage($.current_page);
                    }, refresh_seconds);
                }
            }).catch(() => {
                $.pageoverlay.hide();
                $(`div[id="promote_list"] input[class~="student_to_promote"], div[id="promote_list"] button[id="bulk_modify"]`).prop("disabled", false);
                swal({
                    text: swalnotice["ajax_error"],
                    icon: "error",
                });
            });
        }
    });
}

var view_Promotion_Log = (history_log_id) => {
    if ($.array_stream["promotion_list"][history_log_id] !== undefined) {
        let promotion = $.array_stream["promotion_list"][history_log_id],
            log_history = "";
        if (promotion.promotion_log === undefined) {
            log_history = `<div class="text-center text-danger">Sorry no record was found</div>`;
        } else {
            log_history = `
            <table data-empty="" class="table table-striped datatable">
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th>Student Name</th>
                    <th>Promoted From</th>
                    <th>Promoted To</th>
                    <th>Status</th>
                </tr>`;

            $.each(promotion.promotion_log, function(i, student) {
                let status = (student.is_promoted == 1) ? "<span class='badge badge-success'>Promoted</span>" : ((student.is_promoted == 2) ? "<span class='badge badge-primary'>On Hold</span>" : ((student.is_promoted == 3) ? "<span class='badge badge-warning'>Cancelled</span>" : "<span class='badge badge-danger'>Repeated</span>"));
                log_history += `<tr>`;
                log_history += `<td>${(i+1)}</td>`;
                log_history += `<td>${student.name}</td>`;
                log_history += `<td>${student.from_class_name}</td>`;
                log_history += `<td>${student.to_class_name}</td>`;
                log_history += `<td>${status}</td>`;
                log_history += `</tr>`;
            });

            log_history += `</table>`;
        }
        $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html("Promotion Log");
        $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html(log_history);
        $(`div[id="viewOnlyModal"]`).modal("show");
    }
}

var cancel_Promotion_Log = (history_log_id) => {
    swal({
        title: "Cancel Promotion Log",
        text: swalnotice["cancel_promotion_log"],
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/promotion/cancel`, { history_id: history_log_id }).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code == 200) {
                    setTimeout(() => {
                        loadPage($.current_page);
                    }, refresh_seconds);
                }
            }).catch(() => {
                swal({
                    text: swalnotice["ajax_error"],
                    icon: "error",
                });
            });
        }
    });
}

var validate_Promotion_Log = (history_log_id) => {
    swal({
        title: "Validate Promotion Log",
        text: swalnotice["validate_promotion_log"],
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/promotion/validate`, { history_id: history_log_id }).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code == 200) {
                    setTimeout(() => {
                        loadPage($.current_page);
                    }, refresh_seconds);
                }
            }).catch(() => {
                swal({
                    text: swalnotice["ajax_error"],
                    icon: "error",
                });
            });
        }
    });
}

$(`div[id="promotion"] select[name="class_id"]`).on("change", function(evt) {
    let _class_id = $(this).val();
    $(`div[id="promotion"] select[name="promote_to"]`).val("");
    if (_class_id !== default_class_guid) {
        $(`div[id="promotion"] select[name="promote_to"]`).prop("disabled", false);
        $(`div[id="promote_Student_Display"]`).removeClass(`hidden`);
    } else {
        $(`div[id="promotion"] select[name="promote_to"]`).prop("disabled", true);
        $(`div[id="promote_Student_Display"]`).addClass(`hidden`);
    }
});

$(`button[id="filter_Promotion_Students_List"]`).on("click", function() {
    let department_id = $(`div[id="promotion"] select[name="department_id"]`).val(),
        class_id = $(`div[id="promotion"] select[name="class_id"]`).val(),
        promote_to = $(`div[id="promotion"] select[name="promote_to"]`).val();;

    if (!class_id.length || class_id == "null") {
        swal({
            text: `Please select the class to load the students list.`,
            icon: "error",
        });
        return false;
    }
    promote_display.html(`<div class="text-center col-lg-12">Processing request <i class="fa fa-spin fa-spinner"></i></div>`);
    $.get(`${baseUrl}api/promotion/students`, { department_id, class_id, promote_to }).then((response) => {
        default_class_guid = class_id;
        $(`div[id="promote_Student_Display"]`).removeClass(`hidden`);
        if (response.code === 200) {

            let students_list = `<table class="table table-bordered" width="100%">`,
                the_list = response.data.result.students_list,
                count = 0;

            students_list += `<tr>`;
            students_list += `<th width="10%">#</th>`;
            students_list += `<th width="65%">STUDENT NAME</th>`;
            students_list += `<th class="font-weight-bold" align="center">
                    <div class="row m-0">
                        <div class="col-lg-8">PROMOTE STUDENT</div>
                        <div class="col-lg-4">
                            <input type="checkbox" style="height:25px" title="Promote all students" class="cursor form-control" id="promote_all_student">
                        </div>
                    </div>
                </th>`;
            students_list += `</tr>`;
            $.each(the_list, function(i, value) {
                count++;
                students_list += `
                    <tr>
                        <td>${(count)}</td>
                        <td>
                            <div class="d-flex justify-content-start">
                                <div class="mr-2">
                                    <img title='Click to view student details' class='rounded-circle cursor author-box-picture' width='40px' src="${baseUrl}${value.image}">
                                </div>
                                <div>
                                    <span class="font-bold">${value.name.toUpperCase()}</span><br>
                                    ${value.unique_id}
                                </div>
                            </div>
                        </td>
                        <td align="right">
                            <input ${value.is_promoted == 1 ? "checked='checked'" : ""} style="height:25px" type='checkbox' name='student_to_promote[]' class='student_to_promote form-control cursor' value='${value.item_id}'>
                        </td>
                    </tr>`;
            });
            if (count && response.data.result.promotion_log == false) {
                students_list += `
                <tr>
                    <td colspan="3" align="center">
                        <button onclick="return promote_Students()" id="promote_students_button" class="btn btn-outline-success"><i class="fa fa-assistive-listening-systems"></i> Promote Students</button>
                    </td>
                </tr>`;
                $(`select[name="promote_to"]`).prop("disabled", false);
                $(`input[id="promote_all_student"]`).removeClass("hidden");
            } else if (count && response.data.result.promotion_log == true) {
                students_list = `
                <table class="table table-bordered" width="100%">
                    <tr>
                        <td align="center" colspan="3">
                            <div class="alert alert-warning text-center">Sorry! This class has already been promoted.</div>
                        </td>
                    </tr>`;
            } else {
                $(`select[name="promote_to"]`).prop("disabled", true);
                $(`input[id="promote_all_student"]`).addClass("hidden");
                students_list = `
                <table class="table table-bordered" width="100%">
                <tr>
                    <td align="center" colspan="3">
                        <div class="alert alert-warning text-center">Sorry! No students found under this class.</div>
                    </td>
                </tr>`;
            }
            students_list += `</table>`;
            promote_display.html(students_list);
            promote_All_Changer();
        } else {
            swal({
                text: response.data.result,
                icon: "error",
            });
        }
    }).catch(() => {
        swal({
            text: swalnotice["ajax_error"],
            icon: "error",
        });
    });
    promote_display.html(``);
});

$(`div[id="promote_list"] select[name="bulk_modify"], div[id="promote_list"] button[id="bulk_modify"]`).prop("disabled", false);

promote_All_Changer();
student_fullname_search();