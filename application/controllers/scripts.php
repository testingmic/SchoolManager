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
        backgroundColor: 'rgba(1,104,250, .15)',
        borderColor: '#0168fa',
        events: [
        {
            id: '1',
            start: curYear+'-'+curMonth+'-08T08:30:00',
            end: curYear+'-'+curMonth+'-08T13:00:00',
            title: 'Google Developers Meetup',
            description: 'In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis az pede mollis...'
        },{
            id: '2',
            start: curYear+'-'+curMonth+'-10T09:00:00',
            end: curYear+'-'+curMonth+'-10T17:00:00',
            title: 'Design/Code Review',
            description: 'In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis az pede mollis...'
        },{
            id: '3',
            start: curYear+'-'+curMonth+'-13T12:00:00',
            end: curYear+'-'+curMonth+'-13T18:00:00',
            title: 'Lifestyle Conference',
            description: 'Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi...'
        },{
            id: '4',
            start: curYear+'-'+curMonth+'-15T07:30:00',
            end: curYear+'-'+curMonth+'-15T15:30:00',
            title: 'Team Weekly Trip',
            description: 'In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis az pede mollis...'
        },{
            id: '5',
            start: curYear+'-'+curMonth+'-17T10:00:00',
            end: curYear+'-'+curMonth+'-19T15:00:00',
            title: 'DJ Festival',
            description: 'In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis az pede mollis...'
        },{
            id: '6',
            start: curYear+'-'+curMonth+'-08T13:00:00',
            end: curYear+'-'+curMonth+'-08T18:30:00',
            title: 'Carl Henson\'s Wedding',
            description: 'In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis az pede mollis...'
        }
        ]
    };

    // Birthday Events Source
    var birthdayEvents = {
        id: 2,
        backgroundColor: 'rgba(16,183,89, .25)',
        borderColor: '#10b759',
        events: {$params->birthday_list}
    };

    var holidayEvents = {
        id: 3,
        backgroundColor: 'rgba(241,0,117,.25)',
        borderColor: '#f10075',
        events: [
        {
            id: '10',
            start: curYear+'-'+curMonth+'-04',
            end: curYear+'-'+curMonth+'-06',
            title: 'Feast Day'
        },
        {
            id: '11',
            start: curYear+'-'+curMonth+'-26',
            end: curYear+'-'+curMonth+'-27',
            title: 'Memorial Day'
        },
        {
            id: '12',
            start: curYear+'-'+curMonth+'-28',
            end: curYear+'-'+curMonth+'-29',
            title: 'Veteran\'s Day'
        }
        ]
    };

    var discoveredEvents = {
        id: 4,
        backgroundColor: 'rgba(0,204,204,.25)',
        borderColor: '#00cccc',
        events: [
            {
                id: '13',
                start: curYear+'-'+curMonth+'-17T08:00:00',
                end: curYear+'-'+curMonth+'-18T11:00:00',
                title: 'Web Design Workshop Seminar'
            }
        ]
    };

    var meetupEvents = {
        id: 5,
        backgroundColor: 'rgba(91,71,251,.2)',
        borderColor: '#5b47fb',
        events: [
            {
                id: '14',
                start: curYear+'-'+curMonth+'-03',
                end: curYear+'-'+curMonth+'-05',
                title: 'UI/UX Meetup Conference'
            },
            {
                id: '15',
                start: curYear+'-'+curMonth+'-18',
                end: curYear+'-'+curMonth+'-20',
                title: 'Angular Conference Meetup'
            }
        ]
    };

    var otherEvents = {
        id: 6,
        backgroundColor: 'rgba(253,126,20,.25)',
        borderColor: '#fd7e14',
        events: [
            {
                id: '16',
                start: curYear+'-'+curMonth+'-06',
                end: curYear+'-'+curMonth+'-08',
                title: 'My Rest Day'
            },
            {
                id: '17',
                start: curYear+'-'+curMonth+'-29',
                end: curYear+'-'+curMonth+'-31',
                title: 'My Rest Day'
            }
        ]
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