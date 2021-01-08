$.form_data = {};
if ($(`div[id="filter_Department_Class"]`).length) {

    $(`div[id="fees_payment_form"] *`).prop("disabled", true);

    $(`select[name="department_id"]`).on("change", function() {
        let value = $(this).val();
        $(`select[name='class_id']`).find('option').remove().end();
        $(`select[name='class_id']`).append(`<option value="">Please Select Class</option>`);

        $(`select[name='student_id']`).find('option').remove().end();
        $(`select[name='student_id']`).append(`<option value="">Please Select Student</option>`);

        $(`div[id="fees_payment_form"] *`).prop("disabled", true);
        $(`div[id="fees_payment_preload"] select[name='category_id']`).prop("disabled", true);

        if (value.length && value !== "null") {
            $.get(`${baseUrl}api/classes/list?columns=id,name`, { department_id: value }).then((response) => {
                if (response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        $(`select[name='class_id']`).append(`<option value='${e.id}'>${e.name}</option>'`);
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
            $(`div[id="fees_payment_form"] *`).prop("disabled", true);

            $(`select[name='student_id']`).append(`<option value="">Please Select Student</option>`);
            if (value.length && value !== "null") {
                $.get(`${baseUrl}api/users/list?class_id=${value}&minified=simplified&user_type=student`).then((response) => {
                    if (response.code == 200) {
                        $.each(response.data.result, function(i, e) {
                            $(`select[name='student_id']`).append(`<option value='${e.user_id}'>${e.name}</option>'`);
                        });
                    }
                });
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
                $(`div[id="fees_payment_preload"] select[name='category_id']`).prop("disabled", false);
            }
        });
    }

}

$(`button[id="filter_Fees_Collection"]`).on("click", function() {
    department_id = $(`select[name="department_id"]`).val(),
        class_id = $(`select[name="class_id"]`).val(),
        category_id = $(`select[name="category_id"]`).val();
    $.form_data = { department_id, class_id, category_id };
    loadPage(`${baseUrl}fees-history`);
});

$(`button[id="filter_Staff_List"]`).on("click", function() {
    department_id = $(`select[name="department_id"]`).val(),
        user_type = $(`select[name="user_type"]`).val(),
        gender = $(`select[name="gender"]`).val();
    $.form_data = { department_id, user_type, gender };
    loadPage(`${baseUrl}list-staff`);
});