var client_auto_save, disabled_inputs = new Array(),
    foundArrayList = new Array(),
    csvContent = new Array(),
    dictionary_div = $(`div[id="dictionary_query_results"]`),
    quick_search_loader = $(`div[id="quick_search_loader"]`),
    current_focused_user_id, current_focused_msg_id, users_array_list = {};
var initDashboard = () => {}

var responseCode = (code) => {
    if (code == 200 || code == 201) {
        return "success";
    } else {
        return "error";
    }
}

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
                        loadPage(`${baseUrl}student/${response.data.result.user_id}`);
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
        return `<div class="col-md-12 mb-2 p-1 grid-margin" id="comment-listing" data-reply-container="${rv.item_id}">
        <div class="pb-2 mb-2 replies-item">
            <div class="card-header pb-0">
                <div class="col-lg-12 p-0">
                    <div class="d-flex justify-content-start">
                        <div>
                            <img width="50px" class="img-xs rounded-circle" src="${baseUrl}${rv.replied_by.image}" alt="">
                        </div>
                        <div class="ml-2">
                            <p class="cursor mb-0" data-id="${rv.user_id}">
                                <span class="font-bold">${rv.replied_by.fullname.toUpperCase()}</span>
                                <span class="ml-2 text-primary" data-username="@${rv.replied_by.username}">@${rv.replied_by.username}</span>
                            </p>
                            <p title="${rv.modified_date}" class="tx-11 mb-0 replies-timestamp text-muted">${rv.time_ago}</p>
                        </div>
                    </div>
                    ${rv.delete_button}
                </div>
            </div>
            <div class="card-body pt-2 pb-0">
                <div class="tx-14">${rv.message}</div>
                ${rv.attachment.files.length ? `
                <div class="border-top mt-2 pt-2>
                    <p>
                        <span data-function="toggle-comments-files-attachment-list" data-reply-id="${rv.item_id}" class="cursor" data-toggle="tooltip" title="Hide Attachments">
                        ${rv.attachment.files.length} files (${rv.attachment.files_size})<span class="ml-2"><i class="fa fa-arrow-alt-circle-right"></i></span>
                        </span>
                    </p>
                </div>` : ""}
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

    $(`div[class~="leave-comment-wrapper"] div[class~="form-content-loader"]`).css("display", "flex");

    $.post(`${baseUrl}api/replies/comment`, comment).then((response) => {
        $(`div[class~="leave-comment-wrapper"] div[class~="form-content-loader"]`).css("display", "none");
        if (response.code == 200) {
            content.html("");
            $(`div[class="leave-comment-wrapper"] div[class~="file-preview"]`).html("");
            notify(response.data.result, "success");
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
            notify(response.data.result);
        }
        apply_comment_click_handlers();
    }).catch(() => {
        $(`div[class~="leave-comment-wrapper"] div[class~="form-content-loader"]`).css("display", "none");
        notify("Sorry! Error processing request.");
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

var view_Event_Details = (event_group, event_id) => {
        $(`div[id="viewOnlyModal"] div[class="modal-body"], div[id="viewOnlyModal"] h5[class~="modal-title"]`).html("");
        if ($.array_stream["events_array_list"][event_group] !== undefined) {
            let event = $.array_stream["events_array_list"][event_group],
                the_key = "null";
            $.each(event, function(key, e) {
                if(e.item_id === event_id) {
                    the_key = key;
                    return;
                }
            });
            if (the_key !== "null") {
                let event_info = event[the_key];
                $(`div[id="viewOnlyModal"] div[class~="modal-dialog-top"]`).removeClass("modal-lg").addClass("modal-md");
                $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(event_info.title);
                $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html(`
                <div class="row">
                    <div class="col-md-12">
                        ${event_info.event_image ? `<div><img width="100%" src="${baseUrl}${event_info.event_image}"></div>` : ""}
                        <div>${event_info.description}</div>
                        <div class="mt-3">
                            <p class="p-0 m-0"><i class="fa fa-calendar"></i> <strong>Start Date:</strong> ${event_info.start_date}</p>
                            <p class="p-0 m-0"><i class="fa fa-calendar-check"></i> <strong>End Date:</strong> ${event_info.end_date}</p>
                            <p class="p-0 m-0"><i class="fa fa-users"></i>  <strong>Audience:</strong> ${event_info.audience.toUpperCase()}</p>
                            <p class="p-0 m-0"><i class="fa fa-home"></i> <strong>Type:</strong> ${event_info.event_type}</p>
                        </div>    
                    </div>
                </div>`);
            $(`div[id="viewOnlyModal"]`).modal("show");
        }
    }
}

var set_default_Student = (student_id) => {
    $.post(`${baseUrl}api/users/set_default_student`, {student_id}).then((response) => {
        if(response.code === 200) {
            swal({
                text: "Student ID successfully changed.",
                icon: "success",
            });
            setTimeout(() => {
                window.location.href= `${baseUrl}main`;
            }, refresh_seconds);
        }
    });
}

var set_academic_year_term = (academic_year, academic_term) => {
    $.post(`${baseUrl}api/account/set_default_year`, {academic_year, academic_term}).then((response) => {
        swal({
            text: response.data.result,
            icon: responseCode(response.code),
        });
        if(response.code == 200) {
            setTimeout(() => {
                window.location.href = `${baseUrl}dashboard`;
            }, refresh_seconds);
        }
    }).catch(() => {});
}

var validate_payslip = (record_id, redirect) => {
    let label = {record_id, record: "payslip"};
    swal({
        title: "Validate Payslip",
        text: "Are you sure you want to validate the payslip(s)",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.post(`${baseUrl}api/records/validate`, {label}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
                if(response.code === 200) {
                    if(redirect !== undefined) {
                        loadPage(`${redirect}`);
                    }
                }
            });
        }
    });
}

var validate_transaction = (record_id, redirect) => {
    let label = { record_id, record: "transaction" };
    swal({
        title: "Validate Transaction",
        text: "Are you sure you want to validate the transaction(s)",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.post(`${baseUrl}api/records/validate`, {label}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
                if(response.code === 200) {
                    if(redirect !== undefined) {
                        loadPage(`${redirect}`);
                    }
                }
            });
        }
    });
}

var view_AssessmentMarks = (assessment_id) => {
    if ($.array_stream["assessment_array"][assessment_id] !== undefined) {
        let assessment = $.array_stream["assessment_array"][assessment_id],
            students_list = `<div class="row">
                <div class="col-lg-12 mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">Subject:</td>
                                <td colspan="3">${assessment.assignment_title} 
                                    <strong class='badge p-1 pr-2 pl-2 badge-${assessment.assignment_type_label}'>
                                        ${assessment.assignment_type}
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Class:</td>
                                <td>${assessment.class_name}</td>
                                <td class="font-weight-bold">Course/Subject:</td>
                                <td>${assessment.course_name}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Average Score:</td>
                                <td>${assessment.class_average}</td>
                                <td class="font-weight-bold">Date Published:</td>
                                <td>${assessment.date_published}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Date Submitted</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>`;
        $.each(assessment.marks_list, function(i, e) {
            students_list += `
                <tr>
                    <td></td>
                    <td>
                    <div class="d-flex justify-content-start">
                        <div class="mr-2">
                            <img class="rounded-circle cursor author-box-picture" width="40px" src="${baseUrl}${e.student_image}">
                        </div>
                        <div>
                            <h6 class="p-0 m-0">${e.student_name}</h6>
                            <p>${e.unique_id}</p>
                        </div> 
                    </div>
                    </td>
                    <td>${e.date_submitted}</td>
                    <td>${e.score}</td>
                </tr>`;
        });
        students_list += `
                </tbody>
            </table>
        </div>`;

        $(`div[id="viewOnlyModal"] div[class="modal-body"]`).html(students_list);
        $(`div[id="viewOnlyModal"] h5[class~="modal-title"]`).html(`CLASS GRADE`);
        $(`div[id="viewOnlyModal"]`).modal("show");
    }
}

$(`div[class~="settingSidebar"] input[name="system"]`).on("keyup", function(evt) {
    let search_term = $(this).val();
    if (evt.keyCode == 13 && !evt.shiftKey) {
        quick_search_loader.removeClass("hidden");
        $.get(`${baseUrl}api/search/lookup?term=${search_term}`).then((response) => {
            if(response.code == 200) {
                let result = response.data.result,
                    html = "";
                if(result.users_list !== undefined) {
                    $.each(result.users_list, function(i, e) {
                        html += `<div class='border-bottom mb-2 font-bold'>${i.toUpperCase()} LIST <em class="text-warning">(${e.length} found)</em></div>`;
                        $.each(e, function(ii, ee) {
                            html += `
                            <div class="d-flex mb-2 justify-content-start">
                                <div class="mr-2"><img class="rounded-circle" width="50px" src="${baseUrl}${ee.image}"></div>
                                <div>
                                    <div class="user_name" onclick="load('${ee.user_type == "student" ? `student/${ee.user_id}` : (ee.user_type == "parent" ? `guardian/${ee.user_id}` : `staff/${ee.user_id}/documents`)}')">
                                        ${ee.name}
                                    </div>
                                    <div>${ee.unique_id}</div>
                                    ${ee.class_name !== null ? `<div>${ee.class_name}</div>` : ""}
                                    <div>${ee.the_status_label}</div>
                                </div>
                            </div>`;
                        });
                    });
                    
                }

                if(result.library_books_list !== undefined) {
                    html += `<div class='border-bottom mb-2 font-bold'>LIBRARY BOOKS LIST <em class="text-warning">(${result.library_books_list.length} found)</em></div>`;
                    $.each(result.library_books_list, function(i, book) {
                        html += `
                            <div class="pb-1">
                                <div class="d-flex justify-content-start">
                                    <div>
                                        <p class="mb-0"><i class="fa fa-book"></i> <strong>Title:</strong> 
                                            <span class="user_name" onclick="return load('book/${book.item_id}')">${book.title}</span>
                                        </p>
                                        <p class="mb-0"><i class="fa fa-book-reader"></i> <strong>ISBN:</strong> ${book.isbn}</p>
                                        <p class="mb-0"><i class="fa fa-user"></i> <strong>Author:</strong> ${book.author}</p>
                                        <p class="mb-0"><i class="fa fa-baby-carriage"></i> <strong>Quantity:</strong>  ${book.books_stock}</p>
                                        <p class="mb-2">
                                            <span class="mr-4"><i class="fa fa-table"></i> <strong>Rack:</strong>  ${book.rack_no}</span> 
                                            <span><strong>Row:</strong>  ${book.row_no}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                if(result.fees_payment_receipt !== undefined) {
                    html += `<div class='border-bottom mb-2 font-bold'>FEES RECEIPT LIST <em class="text-warning">(${result.fees_payment_receipt.length} found)</em></div>`;
                    $.each(result.fees_payment_receipt, function(i, receipt) {
                        html += `
                            <div class="pb-1 border-bottom mb-2">
                                <div class="d-flex justify-content-start">
                                    <div>
                                        <p class="mb-0"><i class="fa fa-user"></i> <strong>NAME:</strong> 
                                            <span class="user_name" onclick="return load('fees_view/${receipt.payment_id}')">${receipt.student_info.name}</span>
                                        </p>
                                        <p class="mb-0"><i class="fa fa-money-bill"></i> <strong>AMOUNT:</strong> ${receipt.amount}</p>
                                        <p class="mb-0"><i class="fa fa-book-reader"></i> ${receipt.description}</p>
                                        <p class="mb-0"><i class="fa fa-baby-carriage"></i>  ${receipt.category_name !== null ? receipt.category_name : receipt.category_id}</p>
                                        <p class="mb-0"><i class="fa fa-calendar"></i> ${receipt.recorded_date}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }

                $(`div[id="system_query_results"]`).html(html);

            } else {
                $(`div[id="system_query_results"]`).html(`<span class='text-danger'>Sorry! No results found for the search term</span>`);
            }
            quick_search_loader.addClass("hidden");
        }).catch(() => {
            quick_search_loader.addClass("hidden");
            $(`div[id="system_query_results"]`).html(`<span class='text-danger'>Sorry! An error occurred while processing the request</span>`);
        });
    }
});

$(`div[class~="settingSidebar"] input[name="dictionary"]`).on("keyup", function(evt) {
    let search_term = $(this).val();
    if (evt.keyCode == 13 && !evt.shiftKey) {
        quick_search_loader.removeClass("hidden");
        $.get(`${baseUrl}api/dictionary/search?term=${search_term}`).then((response) => {
            if(response.code == 200) {
                let result = response.data.result,
                    html = "";
                if(result.count) {
                    $.each(result.search_list, function(i, e) {
                        count = 0;
                        html += `<div><h6 class="mt-2 pb-0">${i.toUpperCase()}<h6></div>`;
                        $.each(e, function(ii, ee) {
                            count++;
                            let glossary = ee.glossary;
                            glossary = glossary.replace(`${search_term}`, `<strong class="text-danger">${search_term}</strong>`);
                            html += `<div class="mb-0">${count}. (${ee.tag_count}) ${glossary}</div>`;
                            if(ee.hyponym!== undefined) {
                                if(ee.hyponym.length) {
                                    html +=`<div><strong>Similar Meaning & Example:</strong></div>`;
                                    $.each(ee.hyponym, function(iv, ev) {
                                        let hyp_glossy = ev.glossary;
                                        hyp_glossy = hyp_glossy.replace(`${search_term}`, `<strong class="text-danger">${search_term}</strong>`);
                                        html += `<div class="mb-1 font-italic">${hyp_glossy}</div>`;
                                    });
                                }
                            }
                            html +=`<div class="mb-2"></div>`;
                        });
                    });
                    dictionary_div.html(html);
                } else {
                    dictionary_div.html(`<span class='text-danger'>Sorry! No results found for the search term</span>`);
                }
            } else {
                dictionary_div.html(`<span class='text-danger'>Sorry! No results found for the search term</span>`);
            }
            quick_search_loader.addClass("hidden");
        }).catch(() => {
            quick_search_loader.addClass("hidden");
            dictionary_div.html(`<span class='text-danger'>Sorry! An error occurred while processing the request</span>`);
        });
    }
});

$(`div[class~="settingSidebar"] div[class~="quick_search"] div`).on("click", function() {
    let item = $(this);
    let request = item.attr("data-content");
    if(request !== "dictionary") {
        $(`div[data-content="system"]`).addClass("selected");
        $(`div[data-content="dictionary"]`).removeClass("selected");
        $(`h6[data-item="title"]`).html("Quick System Search");
        $(`div[id="system_query_results"], div[class~="system_content"]`).removeClass("hidden");
        $(`div[id="dictionary_query_results"], div[class~="dictionary_content"]`).addClass("hidden");
    } else {
        $(`div[data-content="system"]`).removeClass("selected");
        $(`div[data-content="dictionary"]`).addClass("selected");
        $(`h6[data-item="title"]`).html("Onboard Dictionary");
        $(`div[id="system_query_results"], div[class~="system_content"]`).addClass("hidden");
        $(`div[id="dictionary_query_results"], div[class~="dictionary_content"]`).removeClass("hidden");
    }
    $(`div[class~="settingSidebar"] input[name="${request}"]`).val('').focus();
});

var close_modal_popup = (modal_id) => {
   $(`div[id="${modal_id}"]`).modal("hide"); 
}

var modal_popup = (modal_id) => {
    $(`div[id="${modal_id}"]`).modal("show");
}

var clear_quick_search_form = (div_id) => {
    $(`div[class~="settingSidebar"] input[name="${div_id}"]`).val('').focus();
    $(`div[id="${div_id}_query_results"]`).html(``);
}

var print_receipt = (receipt_id) => {
    window.open(`${baseUrl}receipt/${receipt_id}`, `Payment Receipt`, `menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes`);
}

var sms_characters_counter = (textarea_id) => {
    var $remaining = $(`span[class~="remaining_count"]`),
        $messages = $remaining.next();
    $(`textarea[name="message"][id="${textarea_id}"]`).on("input", function() {
        var chars = this.value.length,
            messages = Math.ceil(chars / sms_text_count),
            remaining = messages * sms_text_count - (chars % (messages * sms_text_count) || messages * sms_text_count);
        $remaining.text(`${remaining} characters remaining`);
        $messages.text(`${messages} message`);
    });
}

var modify_report_result = (action, report_id) => {
    let s_title = (action == "Submit") ? "Submit Results" : (action == "cancel" ? "Cancel Results" : "Approve Results");
    swal({
        title: s_title,
        text: `You have opted to ${action} this Results. Please note that you will not be able to update the record once it has been submitted. Do you want to proceed?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.pageoverlay.show();
            let label = {
                "action": action,
                "report_id": report_id
            };
            $.post(`${baseUrl}api/terminal_reports/modify`, { label }).then((response) => {
                let s_code = "error";
                if (response.code == 200) {
                    s_code = "success";
                }
                swal({
                    text: response.data.result,
                    icon: s_code,
                });
                if (response.data.additional.href !== undefined) {
                    setTimeout(() => {
                        loadPage(response.data.additional.href);
                    }, refresh_seconds);
                }
                $.pageoverlay.hide();
            }).catch(() => {
                $.pageoverlay.hide();
            });
        }
    });
}

var convert_amount_to_Words = async (amount) => {
    return new Promise((resolve, reject) => {
        $.get(`${baseUrl}api/payment/convert_amount?amount=${amount}`).then((response) => {
            resolve(response.data.result);
        });
    });
}

var student_fullname_search = () => {
    $.expr[':'].Contains = function(a,i,m){
        return $(a).text().toUpperCase().indexOf(m[3].toUpperCase())>=0;
    };
    $(`div[id="student_search_input"] input[name="student_fullname"]`).on("input", function(event) {
        let input = $(this).val();
        $(`tr[data-row_search='name']`).addClass('hidden');
        $(`tr[data-row_search='name'][data-student_fullname]:Contains(${input}), tr[data-row_search='name'][data-student_unique_id]:Contains(${input})`).removeClass('hidden');
    });
}

var preview_pdf = (pdf_file) => {
    window.open(
        `${baseUrl}viewer?file=${baseUrl}${pdf_file}`, `Read PDF`, `width=${($(window).width())*0.90}px,height=750,left=200,resizable,scrollbars=yes,status=1`
    );
}

var preview_pdf_document = (pdf_file) => {
    $.get(`${baseUrl}api/documents/preview`, {file: pdf_file}).then((response) => {
        if(response.code == 200) {
            preview_pdf(response.data.result);
        }
    });
}

$(`div[id="viewOnlyModal"] button[class="close"], div[id="viewOnlyModal"] button[data-dismiss="modal"]`).on("click", function() {
    setTimeout(() => {
        $(`div[id="viewOnlyModal"] div[class~="modal-dialog"]`).removeClass("modal-xl").addClass("modal-lg");
    }, refresh_seconds);
});