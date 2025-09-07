if($(`select[id="attach_to_object"]`).length > 0) {
    $(`select[id="attach_to_object"]`).on("change", function() {
        let value = $(this).val();
        if(value === "bus") {
            $(`select[id="record_object"]`).find('option').remove().end();
            $(`select[id="record_object"]`).append(`<option value="null">Select Bus</option>`);
            $.get(`${baseUrl}api/related/list?module=bus`).then((response) => {
                if(response.code == 200) {
                    $.each(response.data.result, function(i, e) {
                        $(`select[id="record_object"]`).append(`<option value="${e.item_id}">${e.brand} (${e.reg_number})</option>`);
                    });
                }
            });
        }
    });
}