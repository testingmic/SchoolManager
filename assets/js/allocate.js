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
    start: function(e, ui) { },
    stop: function() { }
});

function processAllocations(item) {
    $(`div[class="notices_div"]`).html(`<div class="text-warning">The are no nofications to display currently.</div>`);
    let data = calculateAllocations();
    $.post(`${baseUrl}api/timetable/validate_allocation`, { data, item }).then((response) => {
        if(response.code !== 200) {
            let errors = "";
            $.each(response.data.result, function(i, v) {
                errors += `<div class="text-center text-danger mb-1">${i+1}. ${v}<hr></div>`;
            });
            $(`div[class="notices_div"]`).html(errors);
        }
    });
}

var active = $(".celler", "#allocate_dynamic_timetable").not(".disabled,.blank,.day,.time");
active.droppable({
    drop: function(e, ui) {
        var inner = $('<div class="course_holder"></div>');
        $(this).html(inner);
        $.data(this, "content", inner);
        inner.html(ui.draggable.html());
        $(this).removeClass('hover-background');
        $("input[name=" + this.id + "]", "#courseAlloc").remove();
        $("#courseAlloc").append('<input type="hidden" name="' + this.id + '" value="' + ui.draggable[0].id + ":" + $(`select[name='t_room_id']`).val() + '">');
        let data = {
            course_id: ui.draggable[0].id,
            room_id: $(`select[name='t_room_id']`).val()
        };
        // get the course code from $.array_stream['courses_list'] where the course_id is equal to ui.draggable[0].id
        let course = $.array_stream['courses_list'].find(course => course.item_id === data.course_id);
        data.course_code = course?.course_code;
        data.course_name = course?.name;
        console.log(data);
        $.array_stream['timetable_allocations'][this.id] = [data];
        processAllocations(data);
    },
    over: function(e, ui) {
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
    processAllocations({});
});

$("input", "#courseAlloc").each(function() {
    var slot = $("#" + this.name),
        inner = $('<div class="course_holder"></div>'),
        course = $("#" + this.value.split(':')[0].replace('/', '\\/')),
        i = course.index() % colors.length;
    slot.html(inner);
    inner.html(course.html());
});
colorCourses();

if($(`div[class="notices_div"]`).length > 0) {
    processAllocations();
}

function calculateAllocations() {
    let finalAllocations = {};
    let stream_data = $.array_stream['timetable_allocations'];
    $.each($('td[data-slot_key] div.course_holder'), function(i, v) {
        let td = $(this).closest('td');
        let slot_key = td.data('slot_key');
        let room = stream_data?.[slot_key]?.[0]['room_id'] || null;
        let course = stream_data?.[slot_key]?.[0]['course_id'] || null;
        let course_name = stream_data?.[slot_key]?.[0]['course_name'] || null;
        let course_code = stream_data?.[slot_key]?.[0]['course_code'] || null;
        finalAllocations[i] = {
            slot: slot_key,
            course: course_name,
            weekday: td.data('day'),
            course_code: course_code,
            value: `${course}:${room}`
        };
    });

    return {
        query: "allocation",
        allocations: finalAllocations,
        timetable_id: $(`input[name="timetable_id"]`).val(),
        class_id: $(`input[name="t_class_id"]`).val()
    };
}

var save_TimetableAllocation = () => {
    swal({
        title: "Save Timetable",
        text: "Do you want to proceed to save changes made to this timetable?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let data = calculateAllocations();
            let save_button = $(`button[id="save_TimetableAllocation"]`);
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