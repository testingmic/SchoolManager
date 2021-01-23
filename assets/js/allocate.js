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
    $.ajax({
        type: "POST",
        url: `${baseUrl}api/timetable/allocate`,
        data: "slot=" + this.id + "&course_id=" + $("input[name=" + this.id + "]", "#courseAlloc").val().split(':')[0],
        dataType: "json",
        success: function(result) {
            if (result.code === 200) {
                $("#roomSelect").html('<select name="room_name" style="width:150px" class="updateSelect"  data-placeholder="Choose Room..." required onchange="assignRoom(this.value)">');
                var roomSelect = $("select[name=room_name]"),
                    rooms = JSON.parse(result);
                for (i = 0; i < rooms.length; i++)
                    roomSelect.append('<option value="' + rooms[i][0] + '">' + rooms[i][0] + ' (' + rooms[i][1] + ')</option>');
                var current = $(".selected").attr('id');
                if (current) {
                    var current_room = $("input[name=" + current + "]", "#courseAlloc").val().split(':')[1];
                    if (current_room && current_room != "undefined")
                        $("option[value='" + current_room + "']", roomSelect).attr("selected", "selected");
                    else {
                        roomSelect.prop("selectedIndex", 0)
                    }
                    roomSelect.change();
                } else
                    roomSelect.remove();
            }
        }
    });
});

var active = $(".cell", "#dynamic_timetable").not(".disabled,.blank,.day,.time");
active.droppable({
    // drop: function(e, ui) {
    //     var i = ui.draggable.index() % colors.length;
    //     var inner = $('<div class="course_holder"></div>');
    //     $(this).html(inner);
    //     $.data(this, "content", inner);
    //     inner.html(ui.draggable.html());
    //     $(this).css('background-color', colors[i][0]);
    //     $(this).css('box-shadow', '0 0 25px ' + colors[i][1] + ' inset');
    //     $("input[name=" + this.id + "]", "#courseAlloc").remove();
    //     changes = true;
    //     $("#courseAlloc").append('<input type="hidden" name="' + this.id + '" value="' + ui.draggable[0].id + ":" + $("select[name=room_name]").val() + '">')
    //     $(this).click();
    // },
    // over: function(e, ui) {
    //     var i = ui.draggable.index() % colors.length;
    //     if (!this.innerHTML) {
    //         $(this).css('background-color', colors[i][0]);
    //         $(this).css('box-shadow', '0 0 25px ' + colors[i][1] + ' inset');
    //     }
    // },
    // out: function() {
    //     if (!this.innerHTML)
    //         $(this).removeAttr('style');
    // }
    drop: function(e, ui) {
        var i = ui.draggable.index() % colors.length;
        var inner = $('<div class="course_holder"></div>');
        $(this).html(inner);
        $.data(this, "content", inner);
        inner.html(ui.draggable.html());
        $(this).css('background-color', colors[i][0]);
        $(this).css('box-shadow', '0 0 25px ' + colors[i][1] + ' inset');
        $("input[name=" + this.id + "]", "#courseAlloc").remove();
        changes = true;
        $("#courseAlloc").append('<input type="hidden" name="' + this.id + '" value="' + ui.draggable[0].id + ":" + $("select[name=room_name]").val() + '">')
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