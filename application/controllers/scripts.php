<?php 

class Scripts extends Myschoolgh {
    
    public function attendance(stdClass $params) {

        return "// Calendar Event Source
    var calendarEvents = {
        id: 1,
        backgroundColor: '#136ae3bf',
        borderColor: '#0168fa',
        events: {$params->events_list->calendar_events_list}
    };

    // Birthday Events Source
    var birthdayEvents = {
        id: 2,
        backgroundColor: '#128b10d9',
        borderColor: '#10b759',
        events: {$params->events_list->birthday_list}
    };

    var holidayEvents = {
        id: 3,
        backgroundColor: '#f10075b0',
        borderColor: '#f10075',
        events: {$params->events_list->holidays_list}
    };

    function initiateCalendar() {
    // initialize the calendar
    $('#{$params->container}').fullCalendar({
        header: {
            left: 'prev,today,next',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listMonth'
        },
        editable: false,
        droppable: false, 
        draggable: false,
        dragRevertDuration: 0,
        defaultView: 'month',
        eventLimit: true, // allow  link when too many events
        eventSources: [{$params->event_Sources}],
        eventClick:  function(event, jsEvent, view) {
            $('#modalTitle1').html(event.title);
            $('#modalBody1').html(event.description);
            $('#eventUrl').attr('href', event.url);
            $('#fullCalModal').modal();
        },
        dayClick: function(date, jsEvent, view) {
            $(\"#createEventModal\").modal(\"show\");
            $(`#createEventModal input[name=\"date\"]`).val(`\${date.format()}:\${date.format()}`);
        }
    });
}
initiateCalendar();";

    }

}