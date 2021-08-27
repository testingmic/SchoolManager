$.form_data = {};
if ($(`div[id="filter_Department_Class"]`).length) {

    $(`select[name="department_id"]`).on("change", function() {
        let value = $(this).val();

        $(`select[name='student_id']`).find('option').remove().end();
        $(`select[name='student_id']`).append(`<option value="">Please Select Student</option>`);

        $(`div[id="fees_payment_form"] *`).prop("disabled", true);
        $(`div[id="fees_payment_preload"] select[name='category_id']`).prop("disabled", true);
        $(`div[id="promote_Student_Display"]`).addClass(`hidden`);
        if ((value.length && value !== "null") || $(`div[class~="byPass_Null_Value"]`).length) {
            $(`select[name='class_id'], select[name='course_id']`).find('option').remove().end();
            $(`select[name='class_id'], select[name='course_id']`).append(`<option value="">Please Select</option>`);
            $.get(`${baseUrl}api/classes/list?columns=id,name,slug,item_id`, { department_id: value }).then((response) => {
                if (response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        let value = $(`div[id="promote_Student_Display"]`).length ? e.item_id : e.id;
                        $(`select[name='class_id']`).append(`<option value='${value}'>${e.name}</option>`);
                    });
                }
            });
        }
    });

    $(`button[id="filter_Students_List"]`).on("click", function() {
        department_id = $(`select[name="department_id"]`).val(),
            class_id = $(`select[name="class_id"]`).val(),
            gender = $(`select[name="gender"]`).val();
        $.form_data = { department_id, class_id, gender };
        loadPage(`${baseUrl}list-student`);
    });

    $(`button[id="filter_Courses_List"]`).on("click", function() {
        department_id = $(`select[name="department_id"]`).val(),
            class_id = $(`select[name="class_id"]`).val(),
            course_tutor = $(`select[name="course_tutor"]`).val();
        $.form_data = { department_id, class_id, course_tutor };
        loadPage(`${baseUrl}list-courses`);
    });

    if ($(`select[name="student_id"]`).length) {

        $(`select[name="class_id"]`).on("change", function() {
            let value = $(this).val();
            $(`div[id="make_payment_button"]`).addClass("hidden");
            $(`select[name='student_id']`).find('option').remove().end();

            $(`div[id="fees_payment_history"]`).html(``);
            $(`div[id="fees_allocation_wrapper"] input[id="select_all"], div[id="fees_payment_form"] *`).prop("disabled", true);
            $(`select[name='student_id']`).append(`<option value="">Please Select Student</option>`);
            $(`div[id="fees_allocation_form"] select[name="category_id"],div[id="fees_allocation_form"] input[name="amount"]`).attr("disabled", true).val(``).change();
            
            if (value.length && value !== "null") {
                $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">Loading Students Data <i class="fa fa-spin fa-spinner"></i></td></tr>`);
                $(`div[id="fees_allocation_form"] select[name="category_id"],div[id="fees_allocation_form"] input[name="amount"]`).attr("disabled", false);
                $.get(`${baseUrl}api/users/quick_list?class_id=${value}&minified=simplified&user_type=student`).then((response) => {
                    if (response.code == 200) {
                        $.each(response.data.result, function(i, e) {
                            $(`select[name='student_id']`).append(`<option data-phone_number="${e.phone_number}" data-email="${e.email}" value='${e.user_id}'>${e.name}</option>`);
                        });
                        if($(`table[id="simple_load_student"]`).length) {
                            if(response.data.result.length) {
                                let students_list = ``,
                                    count = 0;
                                $.array_stream["students_filtered_list"] = response.data.result;
                                $.each(response.data.result, function(i, e) {
                                    count++;
                                    students_list += `
                                        <tr>
                                            <td style="height:40px">${count}</td>
                                            <td style="height:40px">
                                                <label for="student_id_${e.user_id}" class="text-uppercase cursor">${e.name}</label>
                                                <span data-column="status" data-item="${e.user_id}"></span>
                                                <div class="hidden"><strong>${e.unique_id}</strong></div>
                                            </td>
                                            <td style="height:40px"><span data-column="due" data-item="${e.user_id}"></span></td>
                                            <td style="height:40px"><span data-column="paid" data-item="${e.user_id}"></span></td>
                                            <td style="height:40px"><span data-column="balance" data-item="${e.user_id}"></span></td>
                                            <td style="height:40px" align="center"><span data-column="select" data-item="${e.user_id}"></span></td>
                                        </tr>`;
                                });
                                $(`table[id="simple_load_student"] tbody`).html(students_list);
                            } else {
                                $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">No student record found.</td></tr>`);        
                            }
                        }
                    } else {
                        $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">No student record found.</td></tr>`);
                    }
                });
            } else {
                $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">Students data appears here.</td></tr>`);
                $(`div[id="fees_allocation_wrapper"] input[id="select_all"]`).prop("disabled", true);
                $(`input[id="contact_number"], input[id="email_address"]`).val("");
            }
        });

        $(`div[id="fees_payment_preload"] select[name="student_id"]`).on("change", function() {
            let value = $(this).val();
            $(`div[id="fees_payment_preload"] select[name='category_id']`).prop("disabled", true);
            $(`div[id="make_payment_button"]`).addClass("hidden");

            $(`div[id="fees_payment_history"]`).html(``);
            $(`div[id="fees_payment_form"] *`).prop("disabled", true);

            if (value.length && value !== "null") {
                $(`div[id="make_payment_button"]`).removeClass("hidden");
                let data = $(`div[id="fees_payment_preload"] select[name="student_id"] option:selected`).data();
                $(`div[id="fees_payment_form"] input[id="email_address"]`).val(data.email);
                $(`div[id="fees_payment_form"] input[id="contact_number"]`).val(data.phone_number);
                $(`div[id="fees_payment_preload"] select[name='category_id']`).prop("disabled", false);
            } else {
                $(`input[id="contact_number"], input[id="email_address"]`).val("");
            }
        });

    }

    if ($(`select[name="course_id"]`).length) {
        $(`select[name="class_id"]`).on("change", function() {
            let value = $(this).val();
            $(`select[name='course_id']`).find('option').remove().end();
            $(`select[name='course_id']`).append(`<option value="">Please Select Course</option>`);
            if (value.length && value !== "null") {
                $.get(`${baseUrl}api/courses/list?class_id=${value}&minified=true`).then((response) => {
                    if (response.code == 200) {
                        $.each(response.data.result, function(i, e) {
                            $(`select[name='course_id']`).append(`<option value='${e.item_id}'>${e.name}</option>`);
                        });
                    }
                });
            }
        });
    }

    $(`button[id="filter_Assignments_List"]`).on("click", function() {
        department_id = $(`select[name="department_id"]`).val(),
            class_id = $(`select[name="class_id"]`).val(),
            course_id = $(`select[name="course_id"]`).val();
        $.form_data = { department_id, class_id, course_id };
        loadPage(`${baseUrl}list-assessment`);
    });

}

$(`button[id="filter_Incidents_List"]`).on("click", function() {
    user_role = $(`select[name="user_role"]`).val(),
        subject = $(`input[name="subject"]`).val();
    $.form_data = { user_role, subject };
    loadPage(`${baseUrl}incidents_list`);
});

$(`button[id="filter_Fees_Collection"]`).on("click", function() {
    department_id = $(`select[name="department_id"]`).val(),
        class_id = $(`select[name="class_id"]`).val(),
        category_id = $(`select[name="category_id"]`).val();
    $.form_data = { department_id, class_id, category_id };
    loadPage(`${baseUrl}fees-history`);
});

$(`button[id="generate_Fees_Report"]`).on("click", function() {
    let category_id = $(`div[class~="generate_report"] select[name="category_id"]`).val(),
        class_id = $(`div[class~="generate_report"] select[name="class_id"]`).val(),
        student_id = $(`div[class~="generate_report"] select[name="student_id"]`).val(),
        start_date = $(`div[class~="generate_report"] input[name="start_date"]`).val(),
        end_date = $(`div[class~="generate_report"] input[name="end_date"]`).val();
    window.open(`${baseUrl}download/fees?category_id=${category_id}&class_id=${class_id}&student_id=${student_id}&start_date=${start_date}&end_date=${end_date}`);
});

$(`button[id="generate_Account_Statement"]`).on("click", function() {
    let account_id = $(`div[class~="generate_report"] select[name="account_id"]`).val(),
        start_date = $(`div[class~="generate_report"] input[name="start_date"]`).val(),
        end_date = $(`div[class~="generate_report"] input[name="end_date"]`).val();
    window.open(`${baseUrl}download/accounting?account_id=${account_id}&start_date=${start_date}&end_date=${end_date}`);
});

$(`button[id="generate_Account_Notes_Report"]`).on("click", function() {
    let account_id = $(`div[class~="account_note_report"] select[name="account_id"]`).val(),
        start_date = $(`div[class~="account_note_report"] input[name="start_date"]`).val(),
        end_date = $(`div[class~="account_note_report"] input[name="end_date"]`).val();
    window.open(`${baseUrl}download/accounting?account_id=${account_id}&start_date=${start_date}&end_date=${end_date}&display=notes`);
});


$(`button[id="generate_Transaction_Report"]`).on("click", function() {
    let account_id = $(`div[class~="transaction_report"] select[name="account_id"]`).val(),
        start_date = $(`div[class~="transaction_report"] input[name="start_date"]`).val(),
        item_type = $(`div[class~="transaction_report"] select[name="item_type"]`).val(),
        end_date = $(`div[class~="transaction_report"] input[name="end_date"]`).val();
    window.open(`${baseUrl}download/accounting?account_id=${account_id}&start_date=${start_date}&end_date=${end_date}&item_type=${item_type}`);
});

$(`button[id="filter_Staff_List"]`).on("click", function() {
    department_id = $(`select[name="department_id"]`).val(),
        user_type = $(`select[name="user_type"]`).val(),
        gender = $(`select[name="gender"]`).val();
    $.form_data = { department_id, user_type, gender };
    loadPage(`${baseUrl}list-staff`);
});

$(`button[id="filter_Staff_Payroll_List"]`).on("click", function() {
    department_id = $(`select[name="department_id"]`).val(),
        user_type = $(`select[name="user_type"]`).val(),
        gender = $(`select[name="gender"]`).val();
    $.form_data = { department_id, user_type, gender };
    loadPage(`${baseUrl}hr-payroll`);
});