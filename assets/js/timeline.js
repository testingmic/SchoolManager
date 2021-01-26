var view_activity_log = (activity_id) => {
    if ($.array_stream["activity_list_array"] !== undefined) {
        let activity_log = $.array_stream["activity_list_array"];
        if (activity_log[activity_id] !== undefined) {
            let activity = activity_log[activity_id];

            console.log(activity);
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
                </div>
            `);
        }
    }
}