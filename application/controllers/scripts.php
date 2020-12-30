<?php 

class Scripts extends Myschoolgh {
    
    public function attendance(stdClass $params) {

        return "$(function() {

    // sample calendar events data
    var curYear = moment().format('YYYY');
    var curMonth = moment().format('MM');

    // Calendar Event Source
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

    // initialize the external events
    $('#external-events .fc-event').each(function() {
        // store data so the calendar knows to render an event upon drop
        $(this).data('event', {
            title: $.trim($(this).text()), // use the element's text as the event title
            stick: true // maintain when user navigates (see docs on the renderEvent method)
        });
        // make the event draggable using jQuery UI
        $(this).draggable({
            zIndex: 999,
            revert: true,      // will cause the event to go back to its
            revertDuration: 0  //  original position after the drag
        });

    });

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
        }
    });

});";

    }

}