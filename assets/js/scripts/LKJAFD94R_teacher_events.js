$(function() {

    // Calendar Event Source
    var calendarEvents = {
        id: 1,
        backgroundColor: '#136ae3bf',
        borderColor: '#0168fa',
        events: [{"title":"Another Test Event","start":"2020-12-01T06:00:00","end":"2020-12-03T18:00:00","description":"\r\n                <div class='row'>\r\n                    <div class='col-md-12'>\r\n                        \r\n                        <div>\r\n                            <div><!--block-->We conducted the audit in accordance with generally accepted auditing standards as well as standards accepted by the Methodist Church Ghana. These standards require that we plan and perform the audit to obtain reasonable assurance about whether the financial statement is free of material misstatement. Our audit includes examining, on a test basis, evidence supporting the amounts and disclosures in the financial statements. Our audit also includes assessing if the accounting principles applied in the financial statements is in conformity to those prescribed by the Methodist Church Ghana.\u00a0<\/div>\r\n                        <\/div>\r\n                        <div class='mt-3'>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar'><\/i> Start Date: 1st December 2020<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar-check'><\/i> End Date: 3rd December 2020<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-users'><\/i>  Audience: STUDENT<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-home'><\/i> Type: Another one<\/p>\r\n                        <\/div>    \r\n                    <\/div>\r\n                <\/div>\r\n                <div class='modal-footer p-0'>\r\n                    <button type='button' class='btn btn-outline-secondary' data-dismiss='modal'>Close<\/button>\r\n                    \r\n                <\/div>","is_editable":true,"item_id":"ht0vuLKSAJpB6i1FzOlIrdPXEqnxw2Ne"},{"title":"Fast Forward Test","start":"2021-01-20T06:00:00","end":"2021-01-22T18:00:00","description":"\r\n                <div class='row'>\r\n                    <div class='col-md-12'>\r\n                        <div><img width='100%' src='http:\/\/localhost\/myschool_gh\/assets\/img\/events\/PtsZ0LmXbB__image.png'><\/div>\r\n                        <div>\r\n                            <div><!--block-->This is an event for the teaching staff for two days.<\/div>\r\n                        <\/div>\r\n                        <div class='mt-3'>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar'><\/i> Start Date: 20th January 2021<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar-check'><\/i> End Date: 22nd January 2021<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-users'><\/i>  Audience: TEACHER<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-home'><\/i> Type: Second Event Test<\/p>\r\n                        <\/div>    \r\n                    <\/div>\r\n                <\/div>\r\n                <div class='modal-footer p-0'>\r\n                    <button type='button' class='btn btn-outline-secondary' data-dismiss='modal'>Close<\/button>\r\n                    \r\n                <\/div>","is_editable":true,"item_id":"cLnFJiO1RyGUEH6I3Ngr8qw54WxVXPjm"},{"title":"Another Test","start":"2021-01-18T06:00:00","end":"2021-01-22T18:00:00","description":"\r\n                <div class='row'>\r\n                    <div class='col-md-12'>\r\n                        <div><img width='100%' src='http:\/\/localhost\/myschool_gh\/assets\/img\/events\/9kqIRylueP__image.png'><\/div>\r\n                        <div>\r\n                            <div><!--block-->This is an event for the teaching staff for two days.<\/div>\r\n                        <\/div>\r\n                        <div class='mt-3'>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar'><\/i> Start Date: 18th January 2021<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar-check'><\/i> End Date: 22nd January 2021<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-users'><\/i>  Audience: TEACHER<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-home'><\/i> Type: Second Event Test<\/p>\r\n                        <\/div>    \r\n                    <\/div>\r\n                <\/div>\r\n                <div class='modal-footer p-0'>\r\n                    <button type='button' class='btn btn-outline-secondary' data-dismiss='modal'>Close<\/button>\r\n                    \r\n                <\/div>","is_editable":true,"item_id":"YWgvhIO4ojzGlM7mR9J1eKXFyAp3NBtn"},{"title":"Test","start":"2021-01-25T06:00:00","end":"2021-01-25T18:00:00","description":"\r\n                <div class='row'>\r\n                    <div class='col-md-12'>\r\n                        <div><img width='100%' src='http:\/\/localhost\/myschool_gh\/assets\/img\/events\/QxDM7P5cjF__image (2).png'><\/div>\r\n                        <div>\r\n                            <div><!--block-->This is here<\/div>\r\n                        <\/div>\r\n                        <div class='mt-3'>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar'><\/i> Start Date: 25th January 2021<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar-check'><\/i> End Date: 25th January 2021<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-users'><\/i>  Audience: STUDENT<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-home'><\/i> Type: New Append to Dropdown<\/p>\r\n                        <\/div>    \r\n                    <\/div>\r\n                <\/div>\r\n                <div class='modal-footer p-0'>\r\n                    <button type='button' class='btn btn-outline-secondary' data-dismiss='modal'>Close<\/button>\r\n                    \r\n                <\/div>","is_editable":true,"item_id":"A3XpN8dZ1T0xLFCqvItM5eSURQ4u6azG"}]
    };

    // Birthday Events Source
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
        events: [{"title":"Vacation Starts","start":"2020-12-31T06:00:00","end":"2021-01-05T18:00:00","description":"\r\n                <div class='row'>\r\n                    <div class='col-md-12'>\r\n                        \r\n                        <div>\r\n                            <div><!--block-->This is the vacation starting point<\/div>\r\n                        <\/div>\r\n                        <div class='mt-3'>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar'><\/i> Start Date: 31st December 2020<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-calendar-check'><\/i> End Date: 5th January 2021<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-users'><\/i>  Audience: ALL<\/p>\r\n                            <p class='p-0 m-0'><i class='fa fa-home'><\/i> Type: Append to Dropdown<\/p>\r\n                        <\/div>    \r\n                    <\/div>\r\n                <\/div>\r\n                <div class='modal-footer p-0'>\r\n                    <button type='button' class='btn btn-outline-secondary' data-dismiss='modal'>Close<\/button>\r\n                    \r\n                <\/div>","is_editable":true,"item_id":"z2JfSOdGTI4Pbxh8yV3UKY1LcMN7rgl6"}]
    };

    // initialize the calendar
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
        eventLimit: true, // allow  link when too many events
        eventSources: [birthdayEvents,holidayEvents,calendarEvents],
        eventClick:  function(event, jsEvent, view) {
            $('#modalTitle1').html(event.title);
            $('#modalBody1').html(event.description);
            $('#eventUrl').attr('href', event.url);
            $('#fullCalModal').modal();
        },
        dayClick: function(date, jsEvent, view) {
            $("#createEventModal").modal("show");
            $(`#createEventModal input[name="date"]`).val(`${date.format()}:${date.format()}`);
        }
    });

});