// Timetable
var changes_effected = false;

function remove(array) {
    var what, a = arguments,
        L = a.length,
        ax;
    while (L > 1 && array.length) {
        what = a[--L];
        while ((ax = array.indexOf(what)) !== -1) {
            array.splice(ax, 1);
        }
    }
    return array;
}

$("#dynamic_timetable").on("click", ".cell.disabled", function() {
    changes_effected = true;
    $(this).removeClass('disabled').addClass('blue');
    $("input[name=" + this.id + "]").val('active');
    remove(disabled_inputs, this.id);
});
$("#dynamic_timetable").on("click", ".cell.blue", function() {
    changes_effected = true;
    $(this).removeClass('blue').addClass('disabled');
    if (!$("input[name=" + this.id + "]")[0]) {
        disabled_inputs.push(this.id);
    }
    $("input[name=" + this.id + "]").val('disabled');
});

var set_Disabled_Inputs = (disabled_array) => {
    let the_list = "",
        new_array = new Array();
    $.each(disabled_array, function(_, value) {
        new_array.push(value);
        the_list += `<input name="${value}" type="hidden" value="disabled">`;
    });
    $(`div[id="disabledSlots"]`).html(the_list);
    disabled_inputs = new_array;
}

var save_Timetable_Record = () => {
    swal({
        title: "Save Timetable",
        text: "Do you want to proceed to save changes effected to this timetable?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {

            let name = $(`div[id="timetable_form"] input[name="name"]`).val(),
                class_id = $(`div[id="timetable_form"] select[name="class_id"]`).val(),
                slots = parseInt($(`div[id="timetable_form"] input[name="slots"]`).val()),
                days = parseInt($(`div[id="timetable_form"] input[name="days"]`).val()),
                duration = parseInt($(`div[id="timetable_form"] input[name="duration"]`).val()),
                start_time = $(`div[id="timetable_form"] input[name="start_time"]`).val(),
                timetable_id = $(`div[id="timetable_form"] input[name="timetable_id"]`).val();

            let data = { slots, name, class_id, days, duration, start_time, disabled_inputs, timetable_id };

            $.post(`${baseUrl}api/timetable/save`, data).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code === 200) {
                    loadPage(`${baseUrl}timetable-manage/${response.data.additional.timetable_id}`)
                }
            });
        }
    });
}

if ($(`div[id="disabledSlots"]`).length && !$(`div[id="allocate_dynamic_timetable"]`).length) {
    $disabled = JSON.parse($(`div[id="disabledSlots"]`).attr("data-disabled_inputs"));
    set_Disabled_Inputs($disabled);
    drawGrid();
}

$(`select[id="current_TimetableId"]`).on("change", function() {
    let timetable_id = $(this).val();
    swal({
        title: "Select Timetable",
        text: "Do you want to change the current timetable? Please ensure to save all changes before you proceed to do that.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let url = $(`select[id="current_TimetableId"]`).attr("data-url");
            $.post(`${baseUrl}api/timetable/set_timetable_id`, { timetable_id }).then((response) => {
                if (response.code === 200) {
                    $.current_page = `${baseUrl}${url}/${timetable_id}`;
                    loadPage(`${baseUrl}${url}/${timetable_id}`);
                }
            });
        }
    });
});

$(`select[id="change_TimetableViewId"]`).on("change", function() {
    let timetable_id = $(this).val();
    $(`div[id="timetable_content_loader"] div[class="form-content-loader"]`).css("display", "flex");
    $.get(`${baseUrl}api/timetable/draw`, { timetable_id }).then((response) => {
        if (response.code === 200) {
            $(`div[id="timetable_content"]`).html(response.data.result.table);
            $.current_page = `${baseUrl}timetable-view/${timetable_id}`;
            window.history.pushState({ current: $.current_page }, "", $.current_page);
        }
        $(`div[id="timetable_content_loader"] div[class="form-content-loader"]`).css("display", "none");
    }).catch(() => {
        $(`div[id="timetable_content_loader"] div[class="form-content-loader"]`).css("display", "none");
    });
});

if ($(`div[id="allocate_dynamic_timetable"]`).length) {
    let _name = parseInt($(`input[name="t_name"]`).val()),
        _slots = parseInt($(`input[name="t_slots"]`).val()),
        _days = parseInt($(`input[name="t_days"]`).val()),
        _duration = parseInt($(`input[name="t_duration"]`).val()),
        _start_time = $(`input[name="t_start_time"]`).val();
    drawGrid(_name, _slots, _days, _duration, _start_time);
}