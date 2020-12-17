var attendance_array;
var calendar = $('#attendance_calendar').fullCalendar({
    height: 'auto',
    defaultView: 'month',
    editable: false,
    weekends: false,
    selectable: true,
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listMonth'
    },
    events: [],
    dayClick: function(date, jsEvent, view) {
        let this_date = date.format();
    }
});