var remove_category = (category_id) => {
    $(`tr[data-row_id="${category_id}"] input[data-item="amount"]`).attr({"disabled": true}).addClass("removed");
    $(`tr[data-row_id="${category_id}"] button[data-save_category="${category_id}"],
        tr[data-row_id="${category_id}"] button[data-remove_category="${category_id}"]`)
        .addClass("hidden");
    $(`tr[data-row_id="${category_id}"] button[data-reverse_action="${category_id}"]`)
        .removeClass("hidden");
}

var reverse_action = (category_id) => {
    $(`tr[data-row_id="${category_id}"] input[data-item="amount"]`).attr({"disabled": false}).removeClass("removed");
    $(`tr[data-row_id="${category_id}"] button[data-save_category="${category_id}"],
        tr[data-row_id="${category_id}"] button[data-remove_category="${category_id}"]`
    ).removeClass("hidden");
    $(`tr[data-row_id="${category_id}"] button[data-reverse_action="${category_id}"]`)
        .addClass("hidden");
}

var save_category = (category, student_id) => {
    swal({
        title: "Save Fee Allocation",
        text: `Do you want to proceed to save the fee category allocation?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $(`div[class~='request_buttons'][data-category_id="${category}"]`).addClass("hidden");
            $(`div[class~='request_loader'][data-category_id="${category}"]`).removeClass("hidden");
            let category_id = {};
            category_id[category] = parseFloat($(`input[data-item='amount'][data-category_id="${category}"]`).val());
            $.post(`${baseUrl}api/fees/quick_allocate`, {category_id, student_id}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
                if(response.code == 200) {
                    setTimeout(() => {
                        loadPage(`${baseUrl}fees-allocate/${student_id}`);
                    }, 2000);
                }
                $(`div[class~='request_buttons'][data-category_id="${category}"]`).removeClass("hidden");
                $(`div[class~='request_loader'][data-category_id="${category}"]`).addClass("hidden");
            }).catch(() => {
                $(`div[class~='request_buttons'][data-category_id="${category}"]`).removeClass("hidden");
                $(`div[class~='request_loader'][data-category_id="${category}"]`).addClass("hidden");
            });
        }
    });
}

var save_student_bill = (student_id, student_name) => {
    swal({
        title: "Save Student Bill",
        text: `Do you want to proceed to save the bill of ${student_name}?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[id="fees_allocation_table"] div[class="form-content-loader"]`).css("display", "flex");
            let category_id = {},
                exemptions = {};
            $.each($(`input[data-item='amount']`), function(i, e) {
                let item = $(this);
                let category = item.attr(`data-category_id`),
                    amount = parseFloat(item.val());
                if(!item.hasClass("removed")) {
                    category_id[category] = amount;
                } else {
                    exemptions[category] = amount;
                }
            });
            $.post(`${baseUrl}api/fees/quick_allocate`, {category_id, student_id, exemptions}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
                if(response.code == 200) {
                    setTimeout(() => {
                        loadPage(`${baseUrl}fees-allocate/${student_id}`);
                    }, 2000);
                }
                $(`div[id="fees_allocation_table"] div[class="form-content-loader"]`).css("display", "none");
            }).catch(() => {
                $(`div[id="fees_allocation_table"] div[class="form-content-loader"]`).css("display", "none");
            });
        }
    });
}