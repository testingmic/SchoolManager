var client_auto_save;
var initDashboard = () => {}

var redirect_btnClicked = (href, modal_id = "") => {
    $.pageoverlay.show();
    if ($(`div[class~="${modal_id}"]`).length) {
        $(`div[class~="${modal_id}"]`).modal("hide");
    }
    setTimeout(() => {
        loadPage(href);
    }, 300);
}

var preload_AjaxData = (data) => {
    if (data.link !== undefined) {
        let resource_links = "";
        $.each(data.link, function(i, e) {
            i++;
            resource_links += `
                <div id="accordion" data-row_id="${e.item_id}">
                    <div class="accordion">
                        <div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#panel-body-${i}" aria-expanded="false">
                            <div class="d-flex justify-content-between">
                                <div><h4>${i}. ${e.link_name}</h4></div>
                                <div><i class="fa fa-calendar-check"></i> ${e.date_created}</div>
                            </div>
                        </div>
                        <div class="accordion-body collapse" data-row_id="${e.item_id}" id="panel-body-${i}" data-parent="#accordion" style="">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>${e.link_url}</strong> <br>
                                    <a href="${e.link_url}" class="anchor" target="_blank">Visit Link</a>
                                </div>
                                <div>
                                    <button onclick="return load_quick_form('course_link_upload','${e.course_id}_${e.item_id}');" class="btn btn-outline-success btn-sm" type="button"><i class="fa fa-edit"></i> Edit</button>
                                    <a href="#" onclick="return delete_record('${e.item_id}', 'resource_link');" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></a>
                                </div>
                            </div>
                            <div class="mt-2 mb-3">${e.description}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        $(`div[id="resource_link_list"]`).html(resource_links);
    }
}

var modifyGuardianWard = (user_id, todo) => {
    let the_text = (todo == "remove") ? "Are you sure you to remove ward from Guardian? You cannot reverse this action once confirmed." : "Add this student to the list of guardian wards.";
    swal({
        title: "Modify Ward",
        text: the_text,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/users/modify_guardianward`, { user_id, todo }).then((response) => {
                if (response.code == 200) {
                    if (todo == "remove") {
                        $.each(response.data.result.removed_list, function(i, e) {
                            $(`div[class~="load_ward_information"][data-id="${e}"]`).remove();
                        });
                    } else {
                        $(`div[id='guardian_ward_listing']`).html(response.data.result.wards_list);
                    }
                    search_usersList("student");
                }
            });
        }
    });
}

var modifyWardGuardian = (user_id, todo) => {
    let the_text = (todo == "remove") ? "Are you sure you to remove guardian attached to this Ward? You cannot reverse this action once confirmed." : "Add this guardian to the list of student guardians.";
    swal({
        title: "Modify Guardian",
        text: the_text,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/users/modify_wardguardian`, { user_id, todo }).then((response) => {
                if (response.code == 200) {
                    $(`div[id='user_search_list']`).html(``);
                    if (todo == "remove") {
                        $.each(response.data.result.removed_list, function(i, e) {
                            $(`div[id="ward_guardian_information"] div[data-ward_guardian_id="${e}"]`).remove();
                        });
                    } else {
                        loadPage(`${baseUrl}update-student/${response.data.result.user_id}/view`);
                    }
                }
            });
        }
    });
}

var search_usersList = (user_type = "") => {
    let user_name = $(`input[name='user_name_search']`).val();
    $(`div[id='user_search_list']`).html(`<div class='text-center'>Processing request <i class='fa fa-spinner fa-spin'></i></div>`);
    if (user_name.length > 2) {
        $.get(`${baseUrl}api/users/list?q=${user_name}&user_type=${user_type}&minified=simplied_load_withclass`).then((response) => {
            if (response.code !== 200) {
                $(`div[id='user_search_list']`).html(`<div class='text-center text-danger font-italic'>No ${user_type} found for the specified search term</div>`);
            } else {
                let users_list = "<div class='row'>";

                if (user_type == "student") {
                    let guardian_id = $(`div[id='user_search_list']`).attr("data-guardian_id");
                    $.each(response.data.result, function(i, e) {
                        let is_found = e.guardian_id.length && ($.inArray(guardian_id, e.guardian_id) !== -1) ? true : false;
                        let the_link = is_found ? `<a onclick='return modifyGuardianWard("${guardian_id}_${e.user_id}","remove");' href='javascript:void(0);' class='btn btn-outline-danger btn-sm'>Remove</a>` : `<a onclick='return modifyGuardianWard("${guardian_id}_${e.user_id}","append");' class='btn btn-outline-success btn-sm' href='javascript:void(0);'>Append Ward</a>`;
                        users_list += `
                    <div class="col-lg-6 mb-4">
                        <div class="d-flex justify-content-start">
                            <div class="mr-2">
                                <img src="${baseUrl}${e.image}" class="rounded-circle cursor author-box-picture" width="50px">
                            </div>
                            <div> 
                                <i class="fa fa-user"></i> ${e.name}
                                <br><i class="fa fa-home"></i> ${e.class_name}
                                <br><i class="fa fa-calendar-check"></i> ${e.dob_clean}
                            </div> 
                        </div>
                        <div class="mt-2 text-right">${the_link}</div>
                    </div>`;
                    });
                } else if (user_type == "guardian") {
                    let student_id = $(`div[id='user_search_list']`).attr("data-student_id");

                    $.each(response.data.result, function(i, e) {
                        let is_found = $.array_stream[`student_guardian_array_${student_id}`].length && ($.inArray(e.user_id, $.array_stream[`student_guardian_array_${student_id}`]) !== -1) ? true : false;
                        let the_link = is_found ? `<a onclick='return modifyWardGuardian("${e.user_id}_${student_id}","remove");' href='javascript:void(0);' class='btn btn-outline-danger btn-sm'>Remove</a>` : `<a onclick='return modifyWardGuardian("${e.user_id}_${student_id}","append");' class='btn btn-outline-success btn-sm' href='javascript:void(0);'>Append Guardian</a>`;
                        users_list += `
                    <div class="col-lg-6 mb-4">
                        <div class="d-flex justify-content-start">
                            <div class="mr-2">
                                <img src="${baseUrl}${e.image}" class="rounded-circle cursor author-box-picture" width="50px">
                            </div>
                            <div> 
                                <i class="fa fa-user"></i> ${e.name}
                                <br><i class="fa fa-phone"></i> ${e.phone_number}
                                <br><i class="fa fa-envelope"></i> ${e.email}
                            </div> 
                        </div>
                        <div class="mt-2 text-right">${the_link}</div>
                    </div>`;
                    });
                }
                users_list += "</div>";
                $(`div[id='user_search_list']`).html(users_list);
            }
        }).catch(() => {
            $(`div[id='user_search_list']`).html(`<div class='text-center text-danger font-italic'>Sorry! There was an error while processing the request</div>`);
        });
    } else {
        $(`div[id='user_search_list']`).html(`<div class='text-center text-danger font-italic'>Sorry! The search term must be at least 3 characters long.</div>`);
    }
}

var formatThreadComment = (rv) => {
        return `<div class="col-md-12 p-1 grid-margin" id="comment-listing" data-reply-container="${rv.item_id}">
        <div class="card rounded replies-item">
            <div class="card-header">
                <div class="col-lg-12 p-0">
                    <div class="d-flex justify-content-start">
                        <div>
                            <img width="50px" class="img-xs rounded-circle" src="${baseUrl}${rv.replied_by.image}" alt="">
                        </div>
                        <div class="ml-2">
                            <p class="cursor mb-0" data-id="${rv.user_id}">
                                ${rv.replied_by.fullname} <span class="ml-2 text-primary" data-username="@${rv.replied_by.username}">@${rv.replied_by.username}</span>
                            </p>
                            <p title="${rv.modified_date}" class="tx-11 mb-0 replies-timestamp text-muted">${rv.time_ago}</p>
                        </div>
                    </div>
                    ${rv.delete_button}
                </div>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="tx-14">${rv.message}</div>
                <div class="${rv.attachment.files.length ? `border-top mt-2 pt-2` : ""}">
                    <p>
                        <span ${rv.attachment.files.length ? `data-function="toggle-comments-files-attachment-list" data-reply-id="${rv.item_id}" class="cursor" data-toggle="tooltip" title="Hide Attachments"` : ""}>
                        ${rv.attachment.files.length ? `${rv.attachment.files.length} files (${rv.attachment.files_size})<span class="ml-2"><i class="fa fa-arrow-alt-circle-right"></i></span>` : ""}
                        </span>
                    </p>
                </div>
                <div class="attachments_list" style="display:none" data-reply-id="${rv.item_id}">${rv.attachment_html}</div>
            </div>
        </div>
    </div>`;
}

var share_Comment = (resource, item_id) => {

    let content = $(`trix-editor[id="leave_comment_content"]`),
        theLoader = $(`div[class="leave-comment-wrapper"] div[class~="absolute-content-loader"]`);

    let comment = {
        item_id: item_id,
        resource: resource,
        comment: htmlEntities(content.html())
    };

    $.pageoverlay.show();

    $.post(`${baseUrl}api/replies/comment`, comment).then((response) => {
        $.pageoverlay.hide();
        if (response.code == 200) {
            content.html("");
            $(`div[class="leave-comment-wrapper"] div[class~="file-preview"]`).html("");
            swal({
                text: response.data.result,
                icon: "success",
            });
            if (response.data.additional.record) {
                $.each(response.data.additional.record, function(ie, iv) {
                    $(`[data-record="${ie}"]`).html(iv);
                });
                let comment = formatThreadComment(response.data.additional.data);
                if ($(`div[id="comments-container"] div[id="comment-listing"]:first`).length) {
                    $(`div[id="comments-container"] div[id="comment-listing"]:first`).before(comment);
                } else {
                    $(`div[id="comments-container"]`).append(comment);
                }
            }
        } else {
            swal({
                text: response.data.result,
                icon: "error",
            });
        }
        apply_comment_click_handlers();
    }).catch(() => {
        $.pageoverlay.hide();
        swal({
            text: "Sorry! Error processing request.",
            icon: "error",
        });
    });
}

var delete_record = (record_id, resource) => {
    swal({
        title: "Delete Record",
        text: "Are you sure you want to delete this record? You cannot reverse the action once it has been confirmed.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/records/remove`, { resource, record_id }).then((response) => {
                let s_icon = "error";
                if (response.code == 200) {
                   s_icon = "success";
                    if (typeof initiateCalendar === "function") {
                        initiateCalendar();
                    }
                    $(`[data-row_id='${record_id}']`).remove();
                }
                swal({
                    position: "top",
                    text: response.data.result,
                    icon: s_icon,
                });
            });
        }
    });
}