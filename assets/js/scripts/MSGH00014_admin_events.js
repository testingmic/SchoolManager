var calendarEvents = {
    id: 1,
    backgroundColor: '#136ae3bf',
    borderColor: '#0168fa',
    events: []
};
var birthdayEvents = {
    id: 2,
    backgroundColor: '#128b10d9',
    borderColor: '#10b759',
    events: []
};
var holidayEvents = {
    id: 3,
    backgroundColor: '#f10075b0',
    borderColor: '#f10075',
    events: []
};

function initiateCalendar() {
    $('#events_management').fullCalendar({
        header: {
            left: 'prev,today,next',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listMonth'
        },
        editable: true,
        droppable: false,
        draggable: false,
        dragRevertDuration: 0,
        defaultView: 'month',
        eventLimit: true,
        eventSources: [birthdayEvents, holidayEvents, calendarEvents],
        eventClick: function(event, jsEvent, view) {
            $('#modalTitle1').html(event.title);
            $('#modalBody1').html(event.description);
            $('#eventUrl').attr('href', event.url);
            $('#fullCalModal').modal();
        },
        dayClick: function(date, jsEvent, view) {
            $(`#createEventModal`).modal("show");
            $(`#createEventModal input[name="date"]`).val(`${date.format()}:${date.format()}`);
        }
    });
}
initiateCalendar();