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

$disabled = JSON.parse($(`div[id="disabledSlots"]`).attr("data-disabled_inputs"));
set_Disabled_Inputs($disabled);

var save_Timetable_Record = () => {
    swal({
        title: "Save Timetable",
        text: "Do you want to proceed to save changes effected to this timetable?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let slots = parseInt($(`input[name="slots"]`).val()),
                days = parseInt($(`input[name="days"]`).val()),
                duration = parseInt($(`input[name="duration"]`).val()),
                start_time = $(`input[name="start_time"]`).val(),
                timetable_id = $(`input[name="timetable_id"]`).val();
            let data = {
                slots,
                days,
                duration,
                start_time,
                disabled_inputs,
                timetable_id
            };
            $.post(`${baseUrl}api/timetable/save`, data).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code === 200) {
                    loadPage(`${baseUrl}timetable/${timetable_id}`)
                }
            });
        }
    });
}

drawGrid();