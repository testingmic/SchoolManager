var bus_modal = () => {
    $(`div[id="busModal"]`).modal("show");
    $(`div[id="busModal"] h5[class="modal-title"]`).html(`Add Bus`);
    $(`div[id="busModal"] input, div[id="busModal"] textarea`).val("");
}

var delete_bus = (bus_id, bus_name) => {
    swal({
        title: `Delete ${bus_name}`,
        text: `Are you sure you want to delete the bus ${bus_name}? Once confirmed you cannot undo the process. Do you wish to continue?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[data-parameter_url='${bus_id}'] div[class='item_loader']`).html(`
                <div class='form-content-loader' style='display: flex; position: absolute'>
                    <div class='offline-content text-center'>
                        <p><i class='fa fa-spin fa-spinner fa-3x'></i></p>
                    </div>
                </div>`
            );
            $.post(`${baseUrl}api/buses/delete`, {bus_id}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(response.code == 200) {
                    $(`div[data-element_id='${bus_id}']`).remove();
                    $(`div[data-parameter_url='${bus_id}'] div[class='item_loader']`).html(``);
                }
            }).catch(() => {
                $(`div[data-parameter_url='${bus_id}'] div[class='item_loader']`).html(``);
            });            
        }
    });
}

var update_bus = (bus_id) => {
    if ($.array_stream["buses_array_list"] !== undefined) {
        let buses = $.array_stream["buses_array_list"];
        if (buses[bus_id] !== undefined) {
            let bus = buses[bus_id];
            $(`div[id="busModal"] h5[class="modal-title"]`).html(`Update Bus Record`);
            $(`div[id="busModal"]`).modal("show");
            $(`div[id="busModal"] input[name="brand"]`).val(bus.brand);
            $(`div[id="busModal"] textarea[name="description"]`).val(bus.description);
            $(`div[id="busModal"] input[name="registration_number"]`).val(bus.reg_number);
            $(`div[id="busModal"] input[name="bus_id"]`).val(bus_id);
            $(`div[id="busModal"] input[name="amount"]`).val(bus.purchase_price);
            $(`div[id="busModal"] input[name="color"]`).val(bus.color);
            $(`div[id="busModal"] input[name="year_of_purchase"]`).val(bus.year_of_purchase);
            $(`div[id="busModal"] input[name="insurance_company"]`).val(bus.insurance_company);
            $(`div[id="busModal"] input[name="insurance_date"]`).val(bus.insurance_date);
        }
    }
}