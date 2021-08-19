$(`div[id="bulk_assign_class"] input[id="select_all"]`).on("click", function () {
    $(this).parents(`table[id="simple_load_student"]`).find(`input[class="student_ids"]:checkbox`).prop('checked', this.checked);
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