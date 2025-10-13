var reset_object_selector = () => {
    $(`select[id="record_object"]`).find('option').remove().end();
    $(`select[id="record_object"]`).append(`<option value="">Select Bus</option>`);
    $(`div[id="assign_to_object"]`).html("");
}
if($(`select[id="attach_to_object"]`).length > 0) {
    $(`select[id="attach_to_object"]`).on("change", function() {
        reset_object_selector();
        let value = $(this).val();
        if(value === "bus") {
            let load_students = $(`div[id="assign_to_object"]`).length;
            $.get(`${baseUrl}api/related/list?module=bus&students=${load_students}`).then((response) => {
                if(response.code == 200) {
                    let additional = response.data.additional;
                    $.each(response.data.result, function(i, e) {
                        $(`select[id="record_object"]`).append(`<option value="${e.item_id}">${e.brand} (${e.reg_number})</option>`);
                    });
                    if($(`div[id="assign_to_object"]`).length) {
                        $(`div[id="assign_to_object"]`).html(`
                        <div class="form-group">
                            <label for='assign_to_object'>Select Student To Assign</label>
                            <select data-width="100%" name="assign_to_object" id="assign_to_object" class="form-control selectpicker">
                                <option value="">Select Student To Assign</option>
                            </select>
                        </div>`);
                        if(typeof additional.students !== 'undefined') {
                            $.each(additional.students, function(i, e) {
                                $(`select[id="assign_to_object"]`).append(`<option value="${e.item_id}">${e.name} (${e.unique_id})</option>`);
                            });
                        }
                        $('.selectpicker').select2();
                    }
                }
            });
        }
    });
}