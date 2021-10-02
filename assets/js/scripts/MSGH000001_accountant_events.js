
    var calendarEvents = {
        id: 1,
        backgroundColor: '#136ae3bf',
        borderColor: '#0168fa',
        events: [{"title":"Financial Accounting for Schools","start":"2021-09-10T06:00:00","end":"2021-09-15T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-12'>\r\n                            <div><img width='100%' src='http:\/\/localhost\/myschool_gh\/assets\/img\/events\/i0wfz7abQ8CuLPBWdFhJV5t6Yp1lrTSq.png'><\/div>\r\n                            <div>\r\n                                <div><!--block-->This is a seminar that is being held for School Administrators and Accountants.<br>This is a must attend seminar as the aim is to help all with the basic principles of accounting in order to effectively manage school finances.<\/div>\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-calendar'><\/i> <strong>Start Date:<\/strong> 10th September 2021<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-calendar-check'><\/i> <strong>End Date:<\/strong> 15th September 2021<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-users'><\/i>  <strong>Audience:<\/strong> ADMIN<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-home'><\/i> <strong>Type:<\/strong> Seminars<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-air-freshener'><\/i> <strong>Status:<\/strong> <span class='badge p-1 badge-primary'>Pending<\/span><\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                    <\/div>\r\n                    <div class='modal-footer p-0'>\r\n                        <button type='button' class='btn btn-sm btn-outline-secondary' data-dismiss='modal'>Close<\/button>\r\n                        \r\n                            <a href='javascript:void(0)' onclick='return load_Event(\"http:\/\/localhost\/myschool_gh\/update-event\/Me1gsHQ7ahUcbyWf\");' class='btn anchor btn-sm btn-outline-success'><i class='fa fa-edit'><\/i> Edit<\/a>\r\n                            <a href='#' onclick='return delete_record(\"Me1gsHQ7ahUcbyWf\", \"event\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'><\/i><\/a>\r\n                        \r\n                    <\/div>","backgroundColor":"#15ae0a","borderColor":"#15ae0a","is_editable":true,"item_id":"Me1gsHQ7ahUcbyWf"}]
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
        editable: false,
        droppable: false, 
        draggable: false,
        dragRevertDuration: 0,
        defaultView: 'month',
        eventLimit: true,
        eventSources: [birthdayEvents,holidayEvents,calendarEvents],
        eventClick:  function(event, jsEvent, view) {
            $('#modalTitle1').html(event.title);
            $('#modalBody1').html(event.description);
            $('#eventUrl').attr('href', event.url);
            $('#fullCalModal').modal();
        },
        dayClick: function(date, jsEvent, view) {
            $(`#createEventModal`).modal(`show`);
            $(`#createEventModal input[name="date"]`).val(`${date.format()}:${date.format()}`);
        }
    });
}
initiateCalendar();