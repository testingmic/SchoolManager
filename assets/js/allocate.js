function assignRoom(room) {
    var slotId = $(".selected")[0].id;
    var slot = $("input[name=" + slotId + "]")[0];
    slot.value = slot.value.split(":")[0] + ":" + room;
}

$(`div[id="courseScroll"] div[class~="course"]`).draggable({
    helper: "clone",
    opacity: 0.7,
    appendTo: "#rightpane",
    tolerance: "fit",
    start: function(e, ui) {
        var blocked = $("." + this.id, ".blocked");
    },
    stop: function() { }
});

var active = $(".celler", "#allocate_dynamic_timetable").not(".disabled,.blank,.day,.time");
console.log(active);
active.droppable({
    drop: function(e, ui) {
        var inner = $('<div class="course_holder"></div>');
        $(this).html(inner);
        $.data(this, "content", inner);
        inner.html(ui.draggable.html());
        $(this).addClass('hover-background');
        $("input[name=" + this.id + "]", "#courseAlloc").remove();
        $("#courseAlloc").append('<input type="hidden" name="' + this.id + '" value="' + ui.draggable[0].id + ":" + $(`select[name='t_room_id']`).val() + '">')
        $(this).click();
    },
    over: function(e, ui) {
        console.log(this);
        $(this).addClass('hover-background').addClass('border-3px');
    },
    out: function() {
        $(this).removeClass('hover-background').removeClass('border-3px');
    }
});

active.dblclick(function() {
    $(this).removeClass('selected');
    $(this).html('');
    $(this).removeClass('hover-background').removeClass('border-3px');
    $("input[name=" + this.id + "]", "#courseAlloc").remove();
});

$("input", "#courseAlloc").each(function() {
    var slot = $("#" + this.name),
        inner = $('<div class="course_holder"></div>'),
        course = $("#" + this.value.split(':')[0].replace('/', '\\/')),
        i = course.index() % colors.length;
    slot.html(inner);
    inner.html(course.html());
    slot.css('background-color', colors[i][0]);
    slot.css('box-shadow', '0 0 25px ' + colors[i][1] + ' inset');
});
colorCourses();

var save_TimetableAllocation = () => {
    let allocations = {},
        save_button = $(`button[id="save_TimetableAllocation"]`);
    $.each($(`form[id="courseAlloc"] input`), function(i, e) {
        let value = $(this).attr("value"),
            slot = $(this).attr("name");
        allocations[i] = {
            slot,
            value
        }
    });

    let data = {
        allocations,
        query: "allocation",
        timetable_id: $(`input[name="timetable_id"]`).val(),
        class_id: $(`input[name="t_class_id"]`).val()
    };
    swal({
        title: "Save Timetable",
        text: "Do you want to proceed to save changes made to this timetable?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`div[class="notices_div"]`).html(`Processing request... <i class="fa fa-spin fa-spinner"></i>`);
            save_button.prop({ "disabled": true });
            save_button.html(`Saving.. <i class="fa fa-spin fa-spinner"></i>`);
            $.post(`${baseUrl}api/timetable/allocate`, { data }).then((response) => {
                save_button.prop({ "disabled": false });
                save_button.html(`<i class="fa fa-save"></i> Save Timetable`);
                if (response.code === 200) {
                    swal({
                        text: response.data.result,
                        icon: "success",
                    });
                    $(`div[class="notices_div"]`).html(`<div class="text-center text-success">${response.data.result}</div>`);
                } else {
                    $(`div[class="notices_div"]`).html(`<div class="text-center text-danger">${response.data.result}</div>`);
                }
            }).catch(() => {
                save_button.prop({ "disabled": false });
                save_button.html(`<i class="fa fa-save"></i> Save Timetable`);
                $(`div[class="notices_div"]`).html(`<div class="text-center text-danger">Sorry! There was an error while processing the request.</div>`);
            });
        }
    });
}