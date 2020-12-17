<?php 

class Scripts extends Myschoolgh {

    public function attendance($params = null) {

        return "var attendance_array = ".($params->attendance_logged_list ?? "new Array()").";
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
        $(`h5[class=\"modal-title\"]`).html(`Showing Record for: \${this_date}`);
        $(`div[id=\"pickCalendarActionModal\"]`).modal(\"show\");
        
        if(!attendance_array.length) {
            $(`div[id=\"pickCalendarActionModal\"] div[class~=\"modal-body\"]`).html(`
                <div class=\"font-italic text-center\">
                    You have currently not logged attendance for
                    <strong>\${this_date}</strong>.
                    <div class=\"mt-3\">
                        <button data-dismiss=\"modal\" onclick=\"return redirect_btnClicked('{$this->baseUrl}attendance_log?date=\${this_date}', 'pickCalendarActionModal');\" class=\"btn btn-outline-success\">Log Attendance</button>
                    </div>
                </div>
            `);
        }
        // $.post(`\${baseUrl}api/calendar/show_loggedlist`, {date: this_date}).then((response) => {
            
        //     if(response.code == 200) {

        //     }    
        // }).catch(() => {
        // });
    }
});";

    }

}