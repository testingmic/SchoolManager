var load_Pay_Fees_Form = () => {
    let $dept_id = $(`select[name="department_id"]`).val(),
        $class_id = $(`select[name="class_id"]`).val(),
        $student_id = $(`select[name="student_id"]`).val(),
        $category_id = $(`select[name="category_id"]`).val();
    let data = {
        "department_id": $dept_id,
        "class_id": $class_id,
        "student_id": $student_id,
        "category_id": $category_id,
        "show_history": true
    };
    $(`div[id="make_payment_button"] button`).prop("disabled", true).html(`Loading record <i class="fa fa-spin fa-spinner"></i>`);

    $.get(`${baseUrl}api/fees/payment_form`, data).then((response) => {
        $(`div[id="make_payment_button"] button`).prop("disabled", false).html(`Make Payment`);
        if (response.code === 200) {
            $(`div[id="fees_payment_history"]`).html(response.data.result);
            $(`div[id="fees_payment_form"] *`).prop("disabled", false);
            $(`div[id="fees_payment_preload"] *`).prop("disabled", true);
            $(`button[id="payment_cancel"]`).removeClass("hidden");
            $(`div[id="fees_payment_form"] input[id="amount"]`).focus();
        }
    }).catch(() => {
        $(`div[id="make_payment_button"] button`).prop("disabled", false).html(`Make Payment`);
    });
}

var cancel_Payment_Form = () => {
    swal({
        title: "Cancel Payment",
        text: `Are you sure you want to discard the form?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[id="fees_payment_history"]`).html(``);
            $(`button[id="payment_cancel"]`).addClass("hidden");
            $(`div[id="fees_payment_form"] *`).prop("disabled", true);
            $(`div[id="fees_payment_preload"] *`).prop("disabled", false);
        }
    });
}

var save_Fees_Allocation = () => {
    swal({
        title: "Allocate Fees",
        text: `Are you sure you want to allocate fees to the selected user group?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let $allot_to = $(`div[id="fees_allocation_form"] select[name="allocate_to"]`).val(),
                $class_id = $(`div[id="fees_allocation_form"] select[name="class_id"]`).val(),
                $student_id = $(`div[id="fees_allocation_form"] select[name="student_id"]`).val(),
                $category_id = $(`div[id="fees_allocation_form"] select[name="category_id"]`).val(),
                $amount = $(`div[id="fees_allocation_form"] input[name="amount"]`).val();
            let data = {
                "allocate_to": $allot_to,
                "class_id": $class_id,
                "student_id": $student_id,
                "category_id": $category_id,
                "amount": $amount
            };

            $(`div[id="fees_allocation_form"] *`).prop("disabled", true);
            $(`div[id="allocate_fees_button"] button`).html(`Processing Request <i class="fa fa-spin fa-spinner"></i>`);

            $.post(`${baseUrl}api/fees/allocate_fees`, data).then((response) => {
                $(`div[id="fees_allocation_form"] *`).prop("disabled", false);
                $(`div[id="allocate_fees_button"] button`).html(`Allocate Fee`);
                if (response.code === 200) {
                    $(`div[id="fees_allocation_form"] input`).val("");
                }
            }).catch(() => {
                $(`div[id="fees_allocation_form"] *`).prop("disabled", false);
                $(`div[id="allocate_fees_button"] button`).html(`Allocate Fee`);
            });
        }
    });
}

$(`select[name="allocate_to"]`).on("change", function() {
    let value = $(this).val();
    if (value === "class") {
        $(`div[id="students_list"]`).addClass("hidden");
    } else {
        $(`div[id="students_list"]`).removeClass("hidden");
    }
});