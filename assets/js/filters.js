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
            $.get(`${baseUrl}api/classes/list?columns=a.id,a.name,a.slug,a.item_id,a.payment_module`, { department_id: value }).then((response) => {
                if (response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        let value = $(`div[id="promote_Student_Display"]`).length ? e.item_id : e.id;
                        $(`select[name='class_id']`).append(`<option data-payment_module="${e.payment_module}" value='${value}'>${e.name.toUpperCase()}</option>`);
                    });
                }
            });
        }
    });

    $(`button[id="filter_Fees_Allocation_List"]`).on("click", function() {
        let location = $(this).data("location");
        department_id = $(`select[name="department_id"]`).val(),
        class_id = $(`select[name="list_class_id"]`).val();
        $.form_data = { department_id, class_id, filter: 'student' };
        loadPage(`${baseUrl}${location}`);
    });

    $(`button[id="filter_Students_List"]`).on("click", function() {
        department_id = $(`select[name="department_id"]`).val(),
            class_id = $(`select[name="class_id"]`).val(),
            gender = $(`select[name="gender"]`).val(),
            user_status = $(`select[name="user_status"]`).val();
        $.form_data = { department_id, class_id, gender, user_status };
        loadPage(`${baseUrl}students`);
    });

    $(`button[id="filter_Courses_List"]`).on("click", function() {
        department_id = $(`select[name="department_id"]`).val(),
            class_id = $(`select[name="class_id"]`).val(),
            course_tutor = $(`select[name="course_tutor"]`).val();
        $.form_data = { department_id, class_id, course_tutor };
        loadPage(`${baseUrl}courses`);
    });

    if ($(`select[name="student_id"]`).length) {

        $(`select[name="class_id"]`).on("change", function() {
            let value = $(this).val(),
                class_payment_module = "Termly",
                append_filter = $(`input[name="no_status_filters"]`).length ? "&minified=no_status_filters" : "";

            $(`div[id="make_payment_button"], div[id="payment_month"]`).addClass("hidden");
            $(`select[name='student_id']`).find('option').remove().end();
            $(`div[id="student_information"], div[id="fees_payment_history"]`).html(``);
            $(`select[name='student_id']`).append(`<option value="">Please Select Student</option>`);
            $(`a[data-link_item="student_go_back"]`).attr("onclick", `return load('fees-history')`);
            $(`div[id="fees_payment_preload"] select[name="category_id"]`).val("").change();
            $(`div[id="fees_allocation_wrapper"] input[id="select_all"], div[id="fees_payment_form"] *`).prop("disabled", true);
            class_payment_module = $(`select[name="class_id"] > option[value="${value}"]`).attr("data-payment_module");

            if($(`div[id="fees_allocation_form"] select[name="payment_module"]`).length) {
                $(`div[id="fees_allocation_form"] select[name="category_id"],div[id="fees_allocation_form"] input[name="amount"]`).attr("disabled", true).val(``).change();
                $(`div[id="fees_allocation_form"] select[name="payment_module"]`).val(class_payment_module).change();
                $(`div[id="fees_allocation_form"] select[name="payment_module"]`).prop("disabled", true);
            }

            if (value.length && value !== "null") {
                $(`table[id="simple_load_student"] tbody`).html(`<tr><td colspan="6" align="center">Loading Students Data <i class="fa fa-spin fa-spinner"></i></td></tr>`);
                $(`div[id="fees_allocation_form"] select[name="category_id"],div[id="fees_allocation_form"] input[name="amount"]`).attr("disabled", false);
                $.get(`${baseUrl}api/users/quick_list?class_id=${value}&user_type=student${append_filter}`).then((response) => {
                    if (response.code == 200) {

                        $.each(response.data.result, function(i, e) {
                            $(`div[id="filter_Department_Class"] select[name='student_id']`).append(`<option 
                                data-image="${e.image}" data-arrears_formated="${e.arrears_formated}" 
                                data-total_debt_formated="${e.total_debt_formated}" 
                                data-debt_formated="${e.debt_formated}" 
                                data-unique_id="${e.unique_id}" 
                                data-name="${e.name}" 
                                data-phone_number="${e.phone_number}" 
                                data-email="${e.email}" 
                                data-scholarship_status="${e.scholarship_status}"
                                value='${e.user_id}'>${e.name.toUpperCase()}</option>`);
                        });

                        if($(`div[id="fees_payment_preload"]`).length) {
                            $.array_stream["class_students_list"] = response.data.result;
                            if(class_payment_module == "Monthly") {
                                $(`div[id="fees_payment_preload"] div[id="payment_month"]`).removeClass("hidden");
                                $(`div[id="fees_payment_preload"] select[name="payment_month"]`).prop("disabled", true);
                            }
                        }

                        if($(`table[id="simple_load_student"]`).length) {
                            if(response.data.result) {
                                let students_list = ``,
                                    count = 0;
                                $.array_stream["students_filtered_list"] = response.data.result;
                                $.each(response.data.result, function(i, e) {
                                    count++;
                                    students_list += `
                                        <tr data-row_search='name' data-student_fullname='${e.name}' data-student_unique_id='${e.unique_id}'>
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
                                student_fullname_search();
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
            $(`a[data-link_item="student_go_back"]`).attr("onclick", `return load('${value.length ? `student/${value}` : "fees-history"}')`);
            $(`div[id="fees_payment_preload"] select[name='category_id']`).prop("disabled", true);
            $(`div[id="make_payment_button"]`).addClass("hidden");

            $(`div[id="fees_payment_history"]`).html(``);
            $(`div[id="fees_payment_form"] *`).prop("disabled", true);

            if (value.length && value !== "null") {
                let option = $(`select[name="student_id"] > option:selected`).data();
                $(`div[id="student_information"]`).html(`
                <div class="card">
                    <div class="card-body p-3 pb-3 shadow-style">
                        <div class="d-flex justify content-start">
                            <div class="mr-2">
                                <img title="Click to view record of ${option.name}" onclick="return load('student/${value}')" width="60px" class="img-shadow" src="${baseUrl}${option.image}">
                            </div>
                            <div>
                                <div class="font-18 user_name" title="Click to view record of ${option.name}" onclick="return load('student/${value}')"><strong>${option.name}</strong></div>
                                <div><strong>STUDENT ID:</strong> ${option.unique_id}</div>
                                <div><strong>FEES:</strong> <span class="fees_arrears">${myPrefs.labels.currency}${option.debt_formated}</span></div>
                                <div><strong>ARREARS:</strong> ${myPrefs.labels.currency}${option.arrears_formated}</div>
                                <div><strong>OUTSTANDING:</strong> <span class="balance_outstanding">${myPrefs.labels.currency}${option.total_debt_formated}</span></div>
                                ${option.scholarship_status ? `<span class="badge badge-success"><i class="fa fa-ankh"></i> On Scholarship</span>` : ''}
                            </div>                            
                        </div>
                    </div>
                </div>`);
                $(`div[id="make_payment_button"]`).removeClass("hidden");
                $(`div[id="make_payment_button"] button`).attr("disabled", option.scholarship_status ? true : false);
                let data = $(`div[id="fees_payment_preload"] select[name="student_id"] option:selected`).data();
                $(`div[id="fees_payment_form"] input[id="email_address"]`).val(data.email);
                $(`div[id="fees_payment_form"] input[id="contact_number"]`).val(data.phone_number);
                $(`div[id="fees_payment_preload"] select[name='category_id']`).prop("disabled", false);
            } else {
                $(`div[id="student_information"]`).html(``);
                $(`input[id="contact_number"], input[id="email_address"]`).val("");
            }
        });

    }

    if ($(`select[name="course_id"]`).length) {
        $(`select[name="class_id"]`).on("change", function() {
            let value = $(this).val();
            $(`select[name='course_id']`).find('option').remove().end();
            $(`select[name='course_id']`).append(`<option value="">Please Select Subject</option>`);
            if (value.length && value !== "null") {
                $.get(`${baseUrl}api/courses/list?class_id=${value}&minified=true`).then((response) => {
                    if (response.code == 200) {
                        $.each(response.data.result, function(i, e) {
                            $(`select[name='course_id']`).append(`<option value='${e.item_id}'>${e.name.toUpperCase()}</option>`);
                        });
                    }
                });
            }
        });
    }

    $(`button[id="filter_Assignments_List"]`).on("click", function() {
        let department_id = $(`select[name="department_id"]`).val(),
            class_id = $(`select[name="class_id"]`).val(),
            course_id = $(`select[name="course_id"]`).val(),
            assessment_group = $(`select[name="assessment_group"]`).val();
        $.form_data = { department_id, class_id, course_id, assessment_group };
        loadPage(`${baseUrl}assessments`);
    });

}

$(`button[id="filter_Incidents_List"]`).on("click", function() {
    let user_role = $(`select[name="user_role"]`).val(),
        subject = $(`input[name="subject"]`).val();
    $.form_data = { user_role, subject };
    loadPage(`${baseUrl}incidents_list`);
});

$(`button[id="filter_Fees_Collection"]`).on("click", function() {
    let date_range = $(`input[name="date_range"]`).val(),
        class_id = $(`select[name="class_id"]`).val(),
        category_id = $(`select[name="category_id"]`).val();
    $.form_data = { date_range, class_id, category_id };
    loadPage(`${baseUrl}fees-history`);
});

$(`button[id="print_Fees_Collection"]`).on("click", function() {
    let category_id = $(`div[class~="print_Fees_Collection"] select[name="category_id"]`).val(),
        class_id = $(`div[class~="print_Fees_Collection"] select[name="class_id"]`).val(),
        date_range = $(`div[class~="print_Fees_Collection"] input[name="date_range"]`).val();
    window.open(`${baseUrl}download/fees?category_id=${category_id}&class_id=${class_id}&date_range=${date_range}`);
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

$(`button[id="filter_Daily_Reports_List"]`).on("click", function() {
    let class_id = $(`div[id="report_container"] select[name="class_id"]`).val(),
        student_id = $(`div[id="report_container"] select[name="student_id"]`).val();
    $.form_data = { class_id, student_id };
    loadPage(`${baseUrl}staff_weekly_reports`);
});

$(`button[id="filter_Bus_Attendance"]`).on("click", function() {
    let bus_id = $(`div[id="filter_Bus_Driver"] select[name="bus_id"]`).val(),
        user_id = $(`div[id="filter_Bus_Driver"] select[name="user_id"]`).val(),
        action = $(`div[id="filter_Bus_Driver"] select[name="action"]`).val(),
        date_logged = $(`div[id="filter_Bus_Driver"] input[name="date_logged"]`).val();
    $.form_data = { bus_id, user_id, action, date_logged };
    loadPage(`${baseUrl}buses_attendance`);
});

$(`button[id="generate_Transaction_Report"]`).on("click", function() {
    let account_id = $(`div[class~="transaction_report"] select[name="account_id"]`).val(),
        start_date = $(`div[class~="transaction_report"] input[name="start_date"]`).val(),
        item_type = $(`div[class~="transaction_report"] select[name="item_type"]`).val(),
        end_date = $(`div[class~="transaction_report"] input[name="end_date"]`).val();
    window.open(`${baseUrl}download/accounting?account_id=${account_id}&start_date=${start_date}&end_date=${end_date}&item_type=${item_type}`);
});

$(`button[id="filter_Staff_List"]`).on("click", function() {
    let department_id = $(`select[name="department_id"]`).val(),
        user_type = $(`select[name="user_type"]`).val(),
        gender = $(`select[name="gender"]`).val();
    $.form_data = { department_id, user_type, gender };
    loadPage(`${baseUrl}staffs`);
});

$(`button[id="filter_Staff_Payroll_List"]`).on("click", function() {
    let department_id = $(`select[name="department_id"]`).val(),
        user_type = $(`select[name="user_type"]`).val(),
        gender = $(`select[name="gender"]`).val();
    $.form_data = { department_id, user_type, gender };
    loadPage(`${baseUrl}hr-payroll`);
});