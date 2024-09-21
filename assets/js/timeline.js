var view_activity_log = (activity_id) => {
        if ($.array_stream["activity_list_array"] !== undefined) {
            let activity_log = $.array_stream["activity_list_array"];
            if (activity_log[activity_id] !== undefined) {
                let activity = activity_log[activity_id];
                $(`div[id="activity_log_detail"]`).html(`
                <div>
                    <h4 class="text-center border-bottom pb-2">ACTIVITY LOG DETAIL</h4>
                    <div class="mt-2">
                        ${activity.description}
                    </div>
                    <div class="mt-2 border-top pt-2">
                        ${activity.source}
                    </div>
                    <div class="mt-2 border-top pt-2">
                        <p class="mb-0 pb-0"><i class="fa fa-wrench"></i> ${activity.subject.toUpperCase()}</p>
                        <p class="mb-0 pb-0"><i class="fa fa-calendar-check"></i> ${activity.date_recorded}</p>
                        <p class="mb-0 pb-0"><i class="fa fa-edit"></i> ${activity.item_id}</p>
                    </div>
                    <div class="mt-2 border-top pt-2">
                        <div class="d-flex justify-content-start">
                            <div class="mr-3"><img width="80px" src="${baseUrl}${activity.image}" class="rounded-circle author-box-picture"></div>
                            <div>
                                <p class="mb-0 pb-0"><i class="fa fa-user-injured"></i> ${activity.fullname}</p>
                                <p class="mb-0 pb-0"><i class="fa fa-address-card"></i> ${activity.unique_id}</p>
                                <p class="mb-0 pb-0"><i class="fa fa-envelope"></i> ${activity.email}</p>
                                <p class="mb-0 pb-0"><i class="fa fa-phone"></i> ${activity.phone_number}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 border-top pt-3">
                        ${activity.previous_record !== null ? `<h5>PREVIOUS RECORD</h5>${activity.previous_record}` : ""}
                    </div>
                </div>
            `);
        }
    }
}

var view_login_history_log = (activity_id) => {
    if ($.array_stream["user_login_history"] !== undefined) {
        let activity_log = $.array_stream["user_login_history"];
        if (activity_log[activity_id] !== undefined) {
            let activity = activity_log[activity_id];
            $(`div[id="activity_log_detail"]`).html(`
                <div>
                    <h4 class="text-center border-bottom pb-2">LOGIN HISTORY DETAIL</h4>
                    <div class="mt-2">
                        ${activity.log_platform}
                    </div>
                    <div class="mt-2 border-top pt-2">
                        <p class="mb-0 pb-0"><i class="fa fa fa-globe"></i> ${activity.log_browser}</p>
                        <p class="mb-0 pb-0"><i class="fa fa-broadcast-tower"></i> ${activity.log_ipaddress}</p>
                        <p class="mb-0 pb-0"><i class="fa fa-calendar-check"></i> ${activity.lastlogin}</p>
                    </div>
                    <div class="mt-2 border-top pt-2">
                        <div class="d-flex justify-content-start">
                            <div class="mr-3"><img width="80px" src="${baseUrl}${activity.image}" class="rounded-circle author-box-picture"></div>
                            <div>
                                <p class="mb-0 pb-0"><i class="fa fa-home"></i> ${activity.school_name}</p>
                                <p class="mb-0 pb-0"><i class="fa fa-user-injured"></i> ${activity.fullname}</p>
                                <p class="mb-0 pb-0"><i class="fa fa-address-card"></i> ${activity.unique_id}</p>
                                <p class="mb-0 pb-0"><i class="fa fa-envelope"></i> ${activity.email}</p>
                                <p class="mb-0 pb-0"><i class="fa fa-phone"></i> ${activity.phone_number}</p>
                                <p class="mb-0 pb-0"><i class="fa fa-tag"></i> ${activity.user_type.toUpperCase()}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }
}

$(`button[id="filter_User_Activities"]`).on("click", function() {
    let start_date = $(`input[name="start_date"]`).val(),
        end_date = $(`input[name="end_date"]`).val(),
        activity_type = $(`select[name="activity_type"]`).val();
    $.form_data = { start_date, end_date, activity_type };
    loadPage(`${baseUrl}timeline`);
});

$(`button[id="filter_User_Login"]`).on("click", function() {
    let start_date = $(`input[name="start_date"]`).val(),
        end_date = $(`input[name="end_date"]`).val(),
        user_type = $(`select[name="user_type"]`).val(),
        clientId = $(`select[name="clientId"]`).val(),
        user_id = $(`select[name="user_id"]`).val();
    $.form_data = { start_date, end_date, user_type, user_id, clientId };
    loadPage(`${baseUrl}login-history`);
});