var removeRow = () => {
    $(`button[class~="remove_guardian_row"]`).on('click', function() {
        let rowId = $(this).attr('data-row');
        $(`div[id="student_guardian_list"] [data-row="${rowId}"]`).remove();
    });
}

async function randomInt(length = 12) {
    var result = '',
        characters = '123456789',
        charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

var full_scholarship = (student_id, status) => {
    swal({
        title: "Scholarship Status",
        text: "Are you sure you want to mark this student as a full scholarship?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.ajax({
                url: `${baseUrl}api/users/full_scholarship`,
                type: "POST",
                data: {
                    "student_id": student_id,
                    "status": status
                },
                dataType: 'JSON',
                success: (result) => {
                    if(result.code === 200) {
                        notify(result.data.result, "success");
                        $(`#scholarship_status`).html(result.data.additional.html);
                        if(result.data.additional.status == 0) {
                            $(`button[id="student_on_scholarship"]`).removeClass("hidden");
                            $(`span[id="make_payment_button"]`).addClass("hidden");
                            $(`button[id="modify_bill_button"]`).addClass("hidden");
                            $(`button[data-item='pay_fees-button']`)
                                .attr("disabled", true)
                                .html(`<i class="fa fa-ankh"></i> On Scholarship`);
                        } else {
                            $(`button[id="student_on_scholarship"]`).addClass("hidden");
                            $(`span[id="make_payment_button"]`).removeClass("hidden");
                            $(`button[id="modify_bill_button"]`).removeClass("hidden");
                            $(`button[data-item='pay_fees-button']`)
                                .removeAttr("disabled")
                                .html(`<i class="fa fa-adjust"></i> PAY FEES`);
                        }
                    } else {
                        notify(result.data.result);
                    }
                }
            });
        }
    });
}

var generate_payment_report = (student_id) => {
    let category_id = $(`select[id="category_id"]`).val(),
        start_date = $(`input[name="group_start_date"]`).val(),
        end_date = $(`input[name="group_end_date"]`).val();
    window.open(`${baseUrl}receipt?category_id=${category_id}&start_date=${start_date}&end_date=${end_date}&student_id=${student_id}`);
}

$(`select[id="switch_select"]`).on("change", function() {
    let value = $(`select[id="switch_select"]`).val();
    if(value === "add_new") {
        $(`div[id="student_guardian_list"]`).slideDown("slow");
        $(`div[id="student_guardian_list_existing"]`).slideUp("slow");
        $(`div[id="student_guardian_list_existing"] select`).val("").change();
    } else {
        $(`div[id="student_guardian_list"]`).slideUp("slow");
        $(`div[id="student_guardian_list_existing"]`).slideDown("slow");
    }
});

async function append_student_guardian_row() {
    let htmlData = $('div[id="student_guardian_list"] div[data-row]:last select').html(),
        lastRowId = $(`div[id="student_guardian_list"] div[data-row]`).length;
    let random_int = await randomInt(8);

    lastRowId++;

    let selectOptions = $('div[id="student_guardian_list"] div[data-row]:last select > option').length;

    if ((selectOptions + 3) == lastRowId) {
        return false;
    }

    $(`div[id="student_guardian_list"] div[data-row]:last`).after(`
        <div class="row mb-3 mt-4 border-primary border-top pt-4 pb-3" data-row="${lastRowId}">
            <div class="col-lg-4 col-md-4 mb-3">
                <label for="guardian_info[guardian_fullname][${lastRowId}]">Fullname</label>
                <input type="hidden" name="guardian_info[guardian_id][${lastRowId}]" id="guardian_info[guardian_id][${lastRowId}]" value="${random_int}">
                <input type="text" name="guardian_info[guardian_fullname][${lastRowId}]" id="guardian_info[guardian_fullname][${lastRowId}]" class="form-control">
            </div>            
            <div class="col-lg-4 col-md-4 mb-3">
                <label for="guardian_info[guardian_contact][${lastRowId}]">Primary Contact</label>
                <input type="text" name="guardian_info[guardian_contact][${lastRowId}]" id="guardian_info[guardian_contact][${lastRowId}]" class="form-control">
            </div>
            <div class="col-lg-3 col-md-3 mb-3">
                <label for="guardian_info[guardian_contact_2][${lastRowId}]">Secondary Contact</label>
                <input type="text" name="guardian_info[guardian_contact_2][${lastRowId}]" id="guardian_info[guardian_contact_2][${lastRowId}]" class="form-control">
            </div>
            <div class="col-lg-1 col-md-1 text-right">
                <div class="d-flex justify-content-end">
                    <div>
                        <label class="text-white">Button</label>
                        <button data-row="${lastRowId}" class="btn remove_guardian_row btn-danger" type="button"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 mt-2">
                <label for="guardian_info[guardian_email][${lastRowId}]">Email Address</label>
                <input type="text" name="guardian_info[guardian_email][${lastRowId}]" id="guardian_info[guardian_email][${lastRowId}]" class="form-control">
            </div>
            <div class="col-lg-3 col-md-3 mt-2">
                <label for="guardian_info[guardian_relation][${lastRowId}]">Relationship</label>
                <select data-width="100%" name="guardian_info[guardian_relation][${lastRowId}]" id="guardian_info[guardian_relation][${lastRowId}]" class="form-control selectpicker">
                    ${htmlData}
                </select>
            </div>
            <div class="col-lg-6 col-md-6 mt-2">
                <label for="guardian_info[guardian_address][${lastRowId}]">Address</label>
                <input type="text" name="guardian_info[guardian_address][${lastRowId}]" id="guardian_info[guardian_address][${lastRowId}]" class="form-control">
            </div>
        </div>
    `);

    $(`select[class~="selectpicker"]`).select2();
    removeRow();
}
removeRow();

$(`input[data-auto_config="email"]`).on("input", function() {
    let value = $(this).val();
    $(`input[data-auto_config="username"]`).val(value.split("@")[0]);
});

$(`select[name="user_type"]`).on("change", function() {
    let value = $(this).val();
    $(`div[data-value="course_ids_container"]`).fadeOut("slow");
    if(value.length) {
        if(value === "teacher") {
            $(`div[data-value="course_ids_container"]`).fadeIn("slow");
        }
    }
});

sms_characters_counter("student_fees_reminder");

function closeAllDetails() {
    const allDetails = document.querySelectorAll("details");
    allDetails.forEach((detail) => {
        detail.addEventListener("toggle", () => {
            if (detail.open) {
            // Close all other <details>
            allDetails.forEach((otherDetail) => {
                if (otherDetail !== detail) {
                otherDetail.removeAttribute("open");
                }
            });
            }
        });
    });
}
closeAllDetails();