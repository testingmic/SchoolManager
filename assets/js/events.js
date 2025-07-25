var add_Event_Type = () => {
    $(`div[id="createEventTypeModal"]`).modal("show");
    $(`div[id="modalBody2"] form[id="eventTypeForm"]`)[0].reset();
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
            $(`div[id="createEventTypeModal"] select[name="color_code"]`).val(type.color_code).trigger("change");
            $(`div[id="createEventTypeModal"] textarea[name="description"]`).val(type.description);
        }
    }
}

var save_Event_Type = () => {
    let url = "",
        name = $(`div[id="createEventTypeModal"] input[name="name"]`).val(),
        type_id = $(`div[id="createEventTypeModal"] input[name="type_id"]`).val(),
        color_code = $(`div[id="createEventTypeModal"] select[name="color_code"]`).val(),
        description = $(`div[id="createEventTypeModal"] textarea[name="description"]`).val();
    if (type_id.length > 10) {
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
            $.post(`${url}`, { type_id, name, description, color_code }).then((response) => {
                let s_icon = "error",
                    event_type_list = "";
                if (response.code == 200) {
                    s_icon = "success";
                    $(`div[id="createEventTypeModal"]`).modal("hide");
                    $(`div[id="createEventTypeModal"] input, div[id="createEventTypeModal"] textarea`).val("")

                    $(`div[id='createEventModal'] select[name='type']`).find('option').remove().end();
                    $(`div[id='createEventModal'] select[name='type']`).append(`<option value="null">Select</option>`);

                    let type_buttons = "";

                    $.each(response.data.additional.event_types, function(i, type) {

                        $(`div[id='createEventModal'] select[name='type']`).append(`<option data-row_id='${type.item_id}' value='${type.item_id}'>${type.name}</option>'`);

                        type_buttons = (type.slug == "public-holiday") ? "" : `
                            <div class="d-flex justify-content-between">
                                <div><button onclick="return update_Event_Type('${type.item_id}')" class="btn btn-sm btn-outline-success"><i class="fa fa-edit"></i> Edit</button></div>
                                <div><button href="" onclick="return delete_record('${type.item_id}', 'event_type');" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button></div>
                            </div>`;

                        event_type_list += `
                        <div data-row_id='${type.item_id}' class='col-lg-4 col-md-6'>
                            <div class="card mb-2">
                                <div class="card-header p-2 text-uppercase">${type.name}</div>
                                <div class="card-body p-2">${type.description}</div>
                                <div class="card-footer p-2">
                                    ${type_buttons}
                                </div>
                            </div>
                        </div>`;
                    });
                    $(`div[id="events_types_list"]`).html(`<div class="row p-0">${event_type_list}</div>`);
                    $.array_stream["event_types_array"] = response.data.additional.event_types;
                    setTimeout(() => {
                        initiateCalendar();
                    }, refresh_seconds);
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

var load_Event = (url) => {
    $(`div[id="fullCalModal"]`).modal("hide");
    setTimeout(() => {
        loadPage(url);
    }, 500);
}

var remove_Event_Cover_Image = (event_id) => {
    swal({
        title: "Delete Cover Image",
        text: "Are you sure you want to remove the event cover image?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/events/remove_cimage`, { event_id }).then((response) => {
                if (response.code == 200) {
                    $(`div[class="event_cover_image_${item_id}"]`).html("");
                }
            });
        }
    });
}