$(function() {

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
        events: [{"title":"Admin Account","start":"2020-11-21T06:00:00","end":"2020-11-21T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Admin Account<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=uIkajsw123456789064hxk1fc3efmnva&name=Admin Account\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-phone'><\/i> +233240889023<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> test_admin@gmail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/user.png'>\r\n                        <\/div>\r\n                    <\/div>"},{"title":"Teacher Account","start":"2020-03-22T06:00:00","end":"2020-03-22T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Teacher Account<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=a6ImKRhGstOi8vMW0zQ2A57nqLJZNkYe&name=Teacher Account\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-phone'><\/i> 0550107770<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> emmallob14@gmail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/users\/WgAzcUqmSK__Methodist.jpg'>\r\n                        <\/div>\r\n                    <\/div>"},{"title":"Solomon Obeng Darko","start":"2020-10-15T06:00:00","end":"2020-10-15T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Solomon Obeng Darko<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=Zwk0Yt1R2zfW6COd3sNVLxivuUjh4TEn&name=Solomon Obeng Darko\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-phone'><\/i> 00930993093<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> themailhereisthere@mail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/users\/jns7h1WK2G__appimg-88bdf4ad97eeb380c2f931b768b0ad14.png'>\r\n                        <\/div>\r\n                    <\/div>"},{"title":"Grace Obeng-Yeboah","start":"2020-12-20T06:00:00","end":"2020-12-20T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Grace Obeng-Yeboah<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=xihKJ9UZ7Xbp5eQHl4zTtgD6rvcE1Ofo&name=Grace Obeng-Yeboah\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-phone'><\/i> 00930993093<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> graciellaob@gmail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/user.png'>\r\n                        <\/div>\r\n                    <\/div>"},{"title":"Emmanuella Darko Sarfowaa","start":"2020-09-04T06:00:00","end":"2020-09-04T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Emmanuella Darko Sarfowaa<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=SZM14dtqDkbfn5cBl0ARgPCj287hym36&name=Emmanuella Darko Sarfowaa\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-phone'><\/i> 0247685521<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> jauntygirl@gmail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/user.png'>\r\n                        <\/div>\r\n                    <\/div>"},{"title":"Frank Amponsah Amoah","start":"2020-12-12T06:00:00","end":"2020-12-12T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Frank Amponsah Amoah<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=ZjwEitrBYeVaFJyb9G6Dgk5uKASRzN7v&name=Frank Amponsah Amoah\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                \r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> frankamoah@gmail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/user.png'>\r\n                        <\/div>\r\n                    <\/div>"},{"title":"Cecilia Boateng","start":"2020-07-10T06:00:00","end":"2020-07-10T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Cecilia Boateng<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=SZM14dtqcccfn5cBl0ARgPCj287hym36&name=Cecilia Boateng\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-phone'><\/i> 0247685521<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> jauntygirl@gmail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/user.png'>\r\n                        <\/div>\r\n                    <\/div>"},{"title":"Maureen Anim","start":"2020-11-14T06:00:00","end":"2020-11-14T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Maureen Anim<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=SZMsssqcccfn5cBl0ARgPCj287hym36&name=Maureen Anim\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-phone'><\/i> 0247685521<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> jauntygirl@gmail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/user.png'>\r\n                        <\/div>\r\n                    <\/div>"},{"title":"Felicia Amponsah","start":"2020-06-14T06:00:00","end":"2020-06-14T18:00:00","description":"\r\n                    <div class='row'>\r\n                        <div class='col-md-10'>\r\n                            <div>\r\n                                This is the birthday of <strong>Felicia Amponsah<\/strong>. \r\n                                <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"http:\/\/localhost\/myschool_gh\/compose?user_id=SZMsssqcccfn5cBl0aaaPCj287hym36&name=Felicia Amponsah\")'>Click Here<\/a> \r\n                                to send a Email or SMS message to the user.\r\n                            <\/div>\r\n                            <div class='mt-3'>\r\n                                <p class='p-0 m-0'><i class='fa fa-phone'><\/i> 0247685521<\/p>\r\n                                <p class='p-0 m-0'><i class='fa fa-envelope'><\/i> jauntygirl@gmail.com<\/p>\r\n                            <\/div>    \r\n                        <\/div>\r\n                        <div class='col-md-2'>\r\n                            <img class='rounded-circle cursor author-box-picture' width='60px' src='http:\/\/localhost\/myschool_gh\/assets\/img\/user.png'>\r\n                        <\/div>\r\n                    <\/div>"}]
    };

    var holidayEvents = {
        id: 3,
        backgroundColor: 'rgba(241,0,117,.25)',
        borderColor: '#f10075',
        events: [{"title":"Vacation Starts","start":"2020-12-30T06:00:00","end":"2021-01-01T18:00:00","description":"\r\n            <div class='row'>\r\n                <div class='col-md-12'>\r\n                    \r\n                    <div>\r\n                        <div><!--block-->This is the vacation starting point<\/div>\r\n                    <\/div>\r\n                    <div class='mt-3'>\r\n                        <p class='p-0 m-0'><i class='fa fa-calendar'><\/i> Start Date: 30th December 2020<\/p>\r\n                        <p class='p-0 m-0'><i class='fa fa-calendar-check'><\/i> End Date: 1st January 2021<\/p>\r\n                        <p class='p-0 m-0'><i class='fa fa-users'><\/i>  Audience: ALL<\/p>\r\n                    <\/div>    \r\n                <\/div>\r\n            <\/div>"}]
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
        eventSources: [birthdayEvents,holidayEvents],
        eventClick:  function(event, jsEvent, view) {
            $('#modalTitle1').html(event.title);
            $('#modalBody1').html(event.description);
            $('#eventUrl').attr('href', event.url);
            $('#fullCalModal').modal();
        },
        dayClick: function(date, jsEvent, view) {
            $("#createEventModal").modal("show");
        }
    });

});