$(`div[id="bulk_assign_class"] input[id="select_all"], div[id="bulk_assign_department"] input[id="select_all"]`).on("click", function () {
    $(this).parents(`table[id="simple_load_student"]`).find(`input[class="student_ids"]:checkbox`).prop('checked', this.checked);
});

$(`section[class~="list_Students_By_Class"] select[name="department_id"]`).on("change", function() {
    let department_id = $(this).val(),
        class_id = $(`section[class~="list_Students_By_Class"] select[name="class_id"]`).val();
    if(department_id.length && class_id.length) {
        $(`button[type="submit"]`).attr("disabled", false);
    } else {
        $(`button[type="submit"]`).attr("disabled", true);
    }
});

$(`section[class~="list_Students_By_Class"] select[name="class_id"]`).on("change", function() {
    let value = $(this).val(),
        department_id = $(`select[name="class_id"] > option:selected`).attr("data-department_id");
    $(`div[id="bulk_assign_department"] select[name="department_id"]`).val(department_id).change();
    $(`div[id="student_information"], div[id="fees_payment_history"]`).html(``);
    if (value.length && value !== "null") {
        $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">Loading Students Data <i class="fa fa-spin fa-spinner"></i></td></tr>`);
        $.get(`${baseUrl}api/users/quick_list?class_id=${value}&minified=simplified&user_type=student`).then((response) => {
            if (response.code == 200) {
                if($(`table[id="simple_load_student"]`).length) {
                    let students_list = ``,
                        count = 0;
                    $.array_stream["students_filtered_list"] = response.data.result;
                    $.each(response.data.result, function(i, e) {
                        count++;
                        students_list += `
                            <tr>
                                <td style="height:40px">${count}</td>
                                <td>
                                    <label for="student_id_${e.user_id}" class="text-uppercase cursor">${e.name}</label>
                                </td>
                                <td>${e.unique_id}</td>
                                <td>${e.department_name !== null ? e.department_name : ""}</td>
                                <td align="center">
                                    <input class="student_ids" data-student_name="${e.name}" name="student_ids[]" value="${e.id}" id="student_id_${e.user_id}" style="width:20px;cursor:pointer;height:20px;" type="checkbox">
                                </td>
                            </tr>`;
                    });
                    $(`table[id="simple_load_student"] input[id="select_all"]`).attr({"disabled":false, "checked": false});
                    $(`table[id="simple_load_student"] tbody`).html(students_list);
                }
            } else {
                $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">No student record found.</td></tr>`);
            }
        });
    } else {
        $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">Students data appears here.</td></tr>`);
    }
});

$(`div[id="bulk_assign_class"] select[name="class_id"]`).on("change", function() {
    let value = $(this).val();
    if(value.length) {
        $(`div[id="bulk_assign_class"] input[id="select_all"], div[id="bulk_assign_class"] input[class="student_ids"],
            div[id="bulk_assign_class"] button[type="submit"]`).prop("disabled", false);
    } else {
        $(`div[id="bulk_assign_class"] input[id="select_all"], div[id="bulk_assign_class"] input[class="student_ids"],
            div[id="bulk_assign_class"] button[type="submit"]`).prop({"disabled": true, "checked": false});
    }
});

var save_Department_Allocation = () => {
    let class_id = $(`div[id="bulk_assign_department"] select[name="class_id"]`).val(),
        department_id = $(`div[id="bulk_assign_department"] select[name="department_id"]`).val();

    let data = {"class_id": class_id, "department_id": department_id },
        class_name = $(`div[id="bulk_assign_department"] select[name="class_id"] > option:selected`).attr("data-class_name"),
        department_name = $(`div[id="bulk_assign_department"] select[name="department_id"] > option:selected`).attr("data-department_name"),
        student_ids = {};

    $.each($(`div[id="bulk_assign_department"] input[class="student_ids"]:checked`), function(i, e) {
        let item = $(this);
            student_ids[i] = item.val();
    });
    data["student_id"] = student_ids;
    
    swal({
        title: "Assign Students ",
        text: `You are about to assign these ${Object.keys(student_ids).length} students of ${class_name} to ${department_name}`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            $.post(`${baseUrl}api/departments/assign`, {data}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
                if(response.code == 200) {
                    department_id = $(`select[name="department_id"]`).val();
                    $(`select[name="class_id"] > option:selected`).attr("data-department_id", department_id);
                    $(`section[class~="list_Students_By_Class"] select[name="class_id"]`).trigger("change");
                }
                $.pageoverlay.hide();
            }).catch(() => {
                $.pageoverlay.hide();
                swal({
                    text: "Sorry! An error was encountered while processing the request.",
                    icon: "error"
                });
            });
        }
    });
}

var save_Class_Allocation = () => {
    let class_id = $(`div[id="bulk_assign_class"] select[name="class_id"]`),
        assign_fees = $(`div[id="bulk_assign_class"] select[name="assign_fees"]`).val();

    let data = {"class_id": class_id.val(), "assign_fees": assign_fees },
        class_name = $(`div[id="bulk_assign_class"] select[name="class_id"] > option:selected`).attr("data-class_name"),
        student_ids = {},
        label = "";

    $.each($(`div[id="bulk_assign_class"] input[class="student_ids"]:checked`), function(i, e) {
        let item = $(this);
            label += `${i+1}. ${item.attr("data-student_name")}\n`;
            student_ids[i] = item.val();
    });
    data["student_id"] = student_ids;
    
    if(Object.keys(student_ids).length == 0) {
        swal({
            text: "Sorry! You must select at least one student to proceed.",
            icon: "error"
        });
        return false;
    }

    swal({
        title: "Assign Class to Student",
        text: `You are about to assign these ${Object.keys(student_ids).length} students to ${class_name}:\n 
            ${label}\nOnce confirmed you cannot reverse the action on this page. Do you wish to proceed with this action?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            $.post(`${baseUrl}api/classes/assign`, {data}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
                if(response.code == 200) {
                    if(response.data.additional !== undefined) {
                        $.each(response.data.additional, function(i, e) {
                            $(`tr[data-row_id="${e}"]`).remove();
                            $(`div[id="bulk_assign_class"] input[id="select_all"], div[id="bulk_assign_class"] input[class="student_ids"],
                                div[id="bulk_assign_class"] button[type="submit"]`).prop({"checked": false});
                        });
                    }
                } else {

                }
                $.pageoverlay.hide();
            }).catch(() => {
                $.pageoverlay.hide();
                swal({
                    text: "Sorry! An error was encountered while processing the request.",
                    icon: "error"
                });
            });
        }
    });

}

$(`form[class="users_bulk_update"]`).on("submit", function(evt) {
    evt.preventDefault();
    
    let myForm = document.getElementById(`users_bulk_update`);
    let theFormData = new FormData(myForm);

    swal({
        title: `Submit Form`,
        text: `Are you sure you want to submit this form for bulk processing?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {            
            $.pageoverlay.show();
            $.ajax({
                url: `${baseUrl}api/users/bulk_update`,
                data: theFormData,
                contentType: false,
                cache: false,
                type: `POST`,
                processData: false,
                success: function(response) {
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code),
                    });
                    if (response.code == 200) {
                        load(`assign-modify_student`);
                    }
                },
                complete: function() {
                    $.pageoverlay.hide();   
                },
                error: function() {
                    $.pageoverlay.hide();
                    swal({text: swalnotice["ajax_error"], icon: "error"});
                }
            });
        }
    });

});

var fullname_search = () => {
    $.expr[':'].Contains = function(a,i,m){
        return $(a).text().toUpperCase().indexOf(m[3].toUpperCase())>=0;
    };
    $(`div[class~="modify_search_input"] input[id="student_fullname"]`).on("input", function(event) {
        let input = $(this).val();
        $(`tr[data-row_search='name']`).addClass('hidden');
        $(`tr[data-row_search='name'][data-student_fullname]:Contains(${input}), tr[data-row_search='name'][data-unique_id]:Contains(${input})`).removeClass('hidden');
    });
}

var apply_to_all = () => {
    let value = $(`select[id="t_column"]`).val(),
        input = $(`input[id="t_input"]`).val();
    $(`input[name^="${value}"]`).val(input);
}

var generate_list = () => {
    
    $.pageoverlay.show();
    $(`div[class~="modify_search_input"] *`).attr({"disabled": true});

    let class_id = $(`select[name="class_id"]`).val(),
        users_receipients_list = ``;

    $(`tbody[class="list_students_record"]`).html(`<tr><td align="center" colspan="4">Processing request <i class="fa fa-spin fa-spinner"></i>.</td></tr>`);

    // the list of members
    let count = 0;
    let members_list = $.array_stream["users_array_list"];

    // set the list of members
    $.each(members_list, function(i, e) {
        if((e.class_id == class_id)) {
            count++;
            users_receipients_list += `
            <tr data-student_fullname="${e.name}" data-row_search='name' data-unique_id="${e.unique_id}" data-row_id="${e.id}">
                <td width="5%" align="left">${count}</td>
                <td width="35%">
                    <div class="d-flex justify-content-start">
                        <div class="mr-3" data-image_item="${e.item_id}">
                            <img class="img-shadow" align="left" src="${baseUrl}${e.image}" width="40px">
                        </div>
                        <div>
                            <span for="recipients_${e.item_id}" class="font-bold text-uppercase text-info">${e.name}</span>
                            <br><strong>${e.unique_id}</strong>
                        </div>
                    </div>
                </td>
                <td width="20%">
                    <input type="date" class="form-control" value="${e.date_of_birth}" name="dob[${e.id}]">
                </td>
                <td width="20%">
                    <input type="date" class="form-control" value="${e.enrollment_date !== null ? e.enrollment_date : ""}" name="end[${e.id}]">
                </td>
                <td width="20%">
                    <input type="file" accept="image/*" class="form-control" data-image_item="${e.item_id}" name="img[${e.id}]">
                </td>
            </tr>`;
        }
    });

    if(!users_receipients_list) {
        users_receipients_list = `<tr><td align="center" colspan="4">No member was found under the selected category.</td></tr>`;
    }

    $(`tbody[class="list_students_record"]`).html(users_receipients_list);
    fullname_search();

    $.pageoverlay.hide();
    $(`div[class~="modify_search_input"] *`).attr({"disabled": false});
}