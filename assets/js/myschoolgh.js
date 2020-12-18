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

var search_usersList = (user_type = "") => {
    let user_name = $(`input[name='user_name_search']`).val();
    $(`div[id='user_search_list']`).html(`<div class='text-center'>Processing request <i class='fa fa-spinner fa-spin'></i></div>`);
    if (user_name.length > 2) {
        $.get(`${baseUrl}api/users/list?q=${user_name}&user_type=${user_type}&minified=simplied_load_withclass`).then((response) => {
            if (response.code !== 200) {
                $(`div[id='user_search_list']`).html(`<div class='text-center text-danger font-italic'>No ${user_type} found for the specified search term</div>`);
            } else {
                let users_list = "<div class='row'>",
                    guardian_id = $(`div[id='user_search_list']`).attr("data-guardian_id");
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
                                <br><strong>CLASS:</strong> <i class="fa fa-home"></i> ${e.class_name}
                                <br><strong>DOB:</strong> <i class="fa fa-calendar-check"></i> ${e.dob_clean}
                            </div> 
                        </div>
                        <div class="mt-2 text-right">${the_link}</div>
                    </div>`;
                });
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