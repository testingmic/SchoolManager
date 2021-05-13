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
            $(`button[id="promote_students_button"]`).prop({ "disabled": true }).html(`Processing Request <i class="fa fa-spin fa-spinner"></i>`);
            $.post(`${baseUrl}api/promotion/promote`, { promote_from, promote_to, students_list }).then((response) => {
                if (response.code === 200) {
                    promote_display.html(``);
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                $(`button[id="promote_students_button"]`).prop({ "disabled": false }).html(`<i class="fa fa-assistive-listening-systems"></i> Promote Students`);
            }).catch(() => {
                $(`button[id="promote_students_button"]`).prop({ "disabled": false }).html(`<i class="fa fa-assistive-listening-systems"></i> Promote Students`);
                swal({
                    text: `Sorry! There is an error while processing the request.`,
                    icon: "error",
                });
            });
        }
    });
}

$(`select[name="class_id"]`).on("change", function(evt) {
    if ($(this).val() === default_class_guid) {
        $(`select[name="promote_to"]`).prop("disabled", false);
        $(`div[id="promote_Student_Display"]`).removeClass(`hidden`);
    } else {
        $(`select[name="promote_to"]`).prop("disabled", true);
        $(`div[id="promote_Student_Display"]`).addClass(`hidden`);
    }
});

$(`button[id="filter_Promotion_Students_List"]`).on("click", function() {
    let department_id = $(`select[name="department_id"]`).val(),
        class_id = $(`select[name="class_id"]`).val();

    if (!class_id.length || class_id == "null") {
        swal({
            text: `Please select the class to load the students list.`,
            icon: "error",
        });
        return false;
    }
    promote_display.html(`<div class="text-center col-lg-12">Processing request <i class="fa fa-spin fa-spinner"></i></div>`);
    $.get(`${baseUrl}api/promotion/students`, { department_id, class_id }).then((response) => {
        default_class_guid = class_id;
        $(`div[id="promote_Student_Display"]`).removeClass(`hidden`);
        if (response.code === 200) {
            let students_list = `<table class="table table-bordered" width="100%">`;
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
            let the_list = response.data.result.students_list,
                count = 0;
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
                                    ${value.name}<br>
                                    <strong>${value.unique_id}</strong>
                                </div>
                            </div>
                        </td>
                        <td align="right">
                            <input style="height:25px" type='checkbox' name='student_to_promote[]' class='student_to_promote form-control cursor' value='${value.item_id}'>
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
            } else if (count && response.data.result.promotion_log == true) {
                students_list = `
                <table class="table table-bordered" width="100%">
                    <tr>
                        <td align="center" colspan="3">
                            <div class="alert alert-danger text-center">Sorry! This class has already been promoted.</div>
                        </td>
                    </tr>`;
            } else {
                $(`select[name="promote_to"]`).prop("disabled", true);
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
            text: `Sorry! There is an error while processing the request.`,
            icon: "error",
        });
    });
    promote_display.html(``);
});