var create_exeat = () => {
    $(`div[id="exeatModal"]`).modal("show");
    $(`div[id="exeatModal"] h5[class="modal-title"]`).html(`Add Exeat`);
    $(`div[id="exeatModal"] form[id="ajax-data-form-content"]`).attr("action", `${baseUrl}api/exeats/create`);
    $(`div[id="exeatModal"] input, div[id="exeatModal"] textarea`).val("");
    $(`div[id="exeatModal"] select[name="student_id"]`).val("").trigger("change");
    $(`div[id="exeatModal"] select[name="status"]`).val("Pending").trigger("change");
    $(`div[id="exeatModal"] select[name="exeat_type"]`).val("").trigger("change");
    $(`div[id="exeatModal"] select[name="pickup_by"]`).val("Self").trigger("change");
}

function filter_exeats() {
    let class_id = $(`div[id="filter_Exeats_List"] select[name="class_id"]`).val(),
        status = $(`div[id="filter_Exeats_List"] select[name="status"]`).val(),
        exeat_type = $(`div[id="filter_Exeats_List"] select[name="exeat_type"]`).val(),
        pickup_by = $(`div[id="filter_Exeats_List"] select[name="pickup_by"]`).val();
    $.form_data = { status, class_id, exeat_type, pickup_by };
    loadPage(`${baseUrl}exeats_log`);
}

var update_exeat = (exeat_id) => {
    if ($.array_stream["exeat_list"] !== undefined) {
        
        let exeats = $.array_stream["exeat_list"];
        if (exeats[exeat_id] !== undefined) {
            let exeat = exeats[exeat_id];
            $(`div[id="exeatModal"] h5[class="modal-title"]`).html(`Update Exeat Record`);
            $(`div[id="exeatModal"]`).modal("show");

            let arrayValues = {
                "status": exeat.status,
                "student_id": exeat.student_id,
                "exeat_type": exeat.exeat_type,
                "departure_date": exeat.departure_date,
                "return_date": exeat.return_date,
                "pickup_by": exeat.pickup_by,
                "guardian_contact": exeat.guardian_contact,
                "reason": exeat.reason
            }

            $(`div[id="exeatModal"] input[name="exeat_id"]`).val(exeat_id);
            $.each(arrayValues, (key, value) => {
                if($.inArray(key, ["status", "pickup_by", "student_id", "exeat_type"]) !== -1) {
                    $(`div[id="exeatModal"] select[name="${key}"]`).val(value).trigger("change");
                } else if(key === "reason") {
                    $(`div[id="exeatModal"] textarea[name="${key}"]`).val(value);
                } else {
                    $(`div[id="exeatModal"] input[name="${key}"]`).val(value);
                }
            });
            $(`div[id="exeatModal"] form[id="ajax-data-form-content"]`).attr("action", `${baseUrl}api/exeats/update`);
        }
    }
}

$(`div[class~="toggle-calculator"]`).addClass("hidden");