function assignRoom(room) {
    var slotId = $(".selected")[0].id;
    var slot = $("input[name=" + slotId + "]")[0];
    slot.value = slot.value.split(":")[0] + ":" + room;
}

function resetInfo() {
    $(".showInfo").html('');
    $("tr.data").remove();
    $("#conflict_help").html('&#9679; Drop a course into a conflicting slot to show conflict details');
    $("#conflict_help").show();
    $(".showInfo").removeClass('showInfo conflicting');
}

$(`div[id="courseScroll"] div[class~="course"]`).draggable({
    helper: "clone",
    opacity: 0.7,
    appendTo: "#rightpane",
    tolerance: "fit",
    start: function(e, ui) {
        var blocked = $("." + this.id, ".blocked");
        resetInfo();
        $("input", blocked).each(function() {
            var cell = $("#" + this.name);
            cell.addClass('conflicting');
            $.data(cell[0], "content", cell.html());
            cell.html(this.value);
        })
    },
    stop: function() {
        $(".conflicting").each(function() {
            if ($(this).hasClass('showInfo'))
                return;
            if (this.innerHTML)
                $(this).html($.data(this, "content"));
            $(this).removeClass('conflicting');
        });
    }
});

$(".cell", "#dynamic_timetable").click(function() {
    if (!this.innerHTML || $(this).hasClass('conflicting'))
        return false;
    $(".selected").removeClass('selected');
    $(this).addClass('selected');
    resetInfo();
    $("#roomSelect").html('<div class="center button"></div>');
    let data = {
        query: "rooms",
        slot: this.id,
        timetable_id: $(`input[name="timetable_id"]`).val(),
        course_id: $("input[name=" + this.id + "]", "#courseAlloc").val().split(':')[0],
        class_id: $(`input[name="t_class_id"]`).val()
    };
    $.ajax({
        type: "POST",
        url: `${baseUrl}api/timetable/allocate`,
        data: { data },
        dataType: "json",
        success: function(result) {
            if (result.code === 200) {
                $(`div[id="default_room_label"]`).addClass("hidden");
                $(`div[id="default_room_select"]`).removeClass("hidden");
                var current = $(`div[class~="selected"]`).attr('id'),
                    roomSelect = $(`select[name='t_room_id']`);
                $(`select[name='t_room_id'] > option`).prop("selected", false);
                if (current) {
                    var current_room = $("input[name=" + current + "]", "#courseAlloc").val().split(':')[1];
                    if (current_room && current_room !== "undefined") {
                        $("option[value='" + current_room + "']", roomSelect).prop("selected", true);
                    } else {
                        roomSelect.prop("selectedIndex", 0)
                    }
                    roomSelect.change();
                } else {
                    roomSelect.remove();
                }
            }
        }
    });
});

var active = $(".cell", "#dynamic_timetable").not(".disabled,.blank,.day,.time");
active.droppable({
    drop: function(e, ui) {
        if ($(this).hasClass('conflicting')) {
            $(this).removeAttr('style');
            $(this).addClass('showInfo');
            changes = true;
            $("input[name=" + this.id + "]", "#courseAlloc").remove();
            $("#conflict_help").html('<div class="center button"></div>');

            let data = {
                query: "conflict",
                slot: this.id,
                course_id: ui.draggable[0].id,
                timetable_id: $(`input[name="timetable_id"]`).val(),
                class_id: $(`input[name="t_class_id"]`).val()
            };

            $.ajax({
                type: "POST",
                url: `${baseUrl}api/timetable/allocate?q=conflict`,
                data: { data },
                dataType: "json",
                success: function(response) {
                    // if (response.data.code === 200) {
                    $("#conflict_help").hide();
                    $("#conflict_info").append(data);
                    // }
                }
            })
            return;
        }
        var i = ui.draggable.index() % colors.length;
        var inner = $('<div class="course_holder"></div>');
        $(this).html(inner);
        $.data(this, "content", inner);
        inner.html(ui.draggable.html());
        $(this).css('background-color', colors[i][0]);
        $(this).css('box-shadow', '0 0 25px ' + colors[i][1] + ' inset');
        $("input[name=" + this.id + "]", "#courseAlloc").remove();
        changes = true;
        $("#courseAlloc").append('<input type="hidden" name="' + this.id + '" value="' + ui.draggable[0].id + ":" + $(`select[name='t_room_id']`).val() + '">')
        $(this).click();
    },
    over: function(e, ui) {
        var i = ui.draggable.index() % colors.length;
        if (!this.innerHTML) {
            $(this).css('background-color', colors[i][0]);
            $(this).css('box-shadow', '0 0 25px ' + colors[i][1] + ' inset');
        }
    },
    out: function() {
        if (!this.innerHTML)
            $(this).removeAttr('style');
    }
});

active.dblclick(function() {
    $(this).removeClass('selected');
    $(this).html('');
    $(this).removeAttr('style');
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
            save_button.prop({ "disabled": true });
            save_button.html(`Saving.. <i class="fa fa-spin fa-spinner"></i>`);
            $.post(`${baseUrl}api/timetable/allocate`, { data }).then((response) => {
                save_button.prop({ "disabled": false });
                save_button.html(`<i class="fa fa-save"></i> Save Timetable`);
                if (response.code === 200) {
                    $(`div[class="notices_div"]`).html(`<div class="text-left text-success">${response.data.result}</div>`);
                } else {
                    $(`div[class="notices_div"]`).html(`<div class="text-left text-danger">${response.data.result}</div>`);
                }
            }).catch(() => {
                save_button.prop({ "disabled": false });
                save_button.html(`<i class="fa fa-save"></i> Save Timetable`);
            });
        }
    });
}