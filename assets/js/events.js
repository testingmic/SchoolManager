var add_Event_Type = () => {
    $(`div[id="createEventTypeModal"]`).modal("show");
    $(`div[id="createEventTypeModal"] [class="modal-title"]`).html("Add Event Type");
}

var saveEventType = () => {
    let url = "",
        type_id = $(`div[id="createEventTypeModal"] input[name="type_id"]`).val(),
        name = $(`div[id="createEventTypeModal"] input[name="name"]`).val(),
        description = $(`div[id="createEventTypeModal"] textarea[name="description"]`).val();
    if (type_id > 30) {
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
                let s_icon = "error";
                if (response.code == 200) {
                    s_icon = "success";
                    $(`div[id="createEventTypeModal"] input, div[id="createEventTypeModal"] textarea`).val("")
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