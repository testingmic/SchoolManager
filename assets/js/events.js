var add_Event_Type = () => {
    $(`div[id="createEventTypeModal"]`).modal("show");
    $(`div[id="createEventTypeModal"] input[name="type_id"]`).val("");
    $(`div[id="createEventTypeModal"] [class="modal-title"]`).html("Add Event Type");
}

var update_Event_Type = (item_id) => {
    if ($.array_stream["event_types_array"] !== undefined) {
        let event_types = $.array_stream["event_types_array"];
        if (event_types[item_id] !== undefined) {
            let type = event_types[item_id];
            $(`div[id="createEventTypeModal"]`).modal("show");
            $(`div[id="createEventTypeModal"] [class="modal-title"]`).html("Update Event Type");
            $(`div[id="createEventTypeModal"] input[name="name"]`).val(type.name);
            $(`div[id="createEventTypeModal"] input[name="type_id"]`).val(item_id);
            $(`div[id="createEventTypeModal"] textarea[name="description"]`).val(type.description);
        }
    }
}

var save_Event_Type = () => {
    let url = "",
        type_id = $(`div[id="createEventTypeModal"] input[name="type_id"]`).val(),
        name = $(`div[id="createEventTypeModal"] input[name="name"]`).val(),
        description = $(`div[id="createEventTypeModal"] textarea[name="description"]`).val();
    if (type_id.length > 30) {
        url = `${baseUrl}api/events/update_type`;
    } else {
        url = `${baseUrl}api/events/add_type`;
    }
    swal({
        title: "Save Event Type",
        text: "Are you sure you want to save this event type?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${url}`, { type_id, name, description }).then((response) => {
                let s_icon = "error",
                    event_type_list = "";
                if (response.code == 200) {
                    s_icon = "success";
                    $(`div[id="createEventTypeModal"]`).modal("hide");
                    $(`div[id="createEventTypeModal"] input, div[id="createEventTypeModal"] textarea`).val("")

                    $.each(response.data.additional.event_types, function(i, type) {
                        event_type_list += `
                            <div class="card mb-2">
                                <div class="card-header p-2 text-uppercase">${type.name}</div>
                                <div class="card-body p-2">${type.description}</div>
                                <div class="card-footer p-2">
                                    <div class="d-flex justify-content-between">
                                        <div><button onclick="return update_Event_Type('${type.item_id}')" class="btn btn-sm btn-outline-success"><i class="fa fa-edit"></i> Edit</button></div>
                                        <div><a href="#" onclick="return delete_record('${type.description}', 'event_type');" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i> Delete</a></div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $(`div[id="events_types_list"]`).html(event_type_list);
                }
                swal({
                    position: "top",
                    text: response.data.result,
                    icon: s_icon,
                });
            });
        }
    });
}