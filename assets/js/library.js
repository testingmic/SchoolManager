var selected_books = $(`div[id='library_form'] div[id="selected_book_details"]`),
    selected_books_list = $(`div[id='library_form'] div[id="selected_book_list"]`);

var show_EResource_Modal = () => {
    $(`div[id="ebook_Resource_Modal_Content"]`).modal("show");
}

var upload_EBook_Resource = (book_id) => {
    swal({
        title: "Upload Resource",
        text: "Are you sure you want to upload these files for this resource?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $.post(`${baseUrl}api/library/upload_resource`, { book_id }).then((response) => {
                if (response.code == 200) {
                    $(`div[id="ebook_Resource_Modal_Content"]`).modal("hide");
                    $(`div[preview-id="ebook_${book_id}"]`).html("");
                    $(`div[data-ebook_resource_list="${book_id}"]`).html(response.data.additional.files_list);
                    swal({
                        position: "top",
                        text: response.data.result,
                        icon: "success",
                    });
                    setTimeout(() => {
                        loadPage(`${baseUrl}update-book/${book_id}/view`);
                    }, 500);
                } else {
                    swal({
                        position: "top",
                        text: response.data.result,
                        icon: "error",
                    });
                }
            });
        }
    });
}

var book_quantity_Checker = () => {
    $(`div[id='library_form'] table tr[class="each_book_item"] input[type="number"]`).on("keyup", function() {
        let input = $(this),
            value = parseInt(input.val()),
            max = parseInt(input.attr("max"));
        if (value < 1) {
            input.val(1);
        } else if (value > max) {
            input.val(max);
        }
    });
}

var issue_Request_Handler = (todo, book_id = "") => {
    let label = {
        "todo": todo,
        "mode": selected_books.attr("data-mode"),
        "book_id": book_id
    };
    $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
        if (response.code == 200) {
            selected_books.addClass("hidden");
            selected_books.html("");
            if (response.data.result.books_list !== undefined) {
                let books_list = `<div class="table-responsive"><table class="table table-bordered">`;
                $.each(response.data.result.books_list, function(i, book) {
                    i++;
                    books_list += `<tr data-row_id="${book.book_id}" class="each_book_item">
                        <td>
                            <div class="pt-2 pb-1">
                                <span class="text-primary text-uppercase">${book.info.title}</span><br>
                                <i class="fa fa-book-reader"></i> <strong>${book.info.isbn}</strong><br>
                                <i class="fa fa-user"></i> ${book.info.author}
                                <p class="mb-0">
                                    <span class="mr-4"><i class="fa fa-table"></i> <strong>Rack:</strong>  ${book.info.rack_no}</span> 
                                    <span><strong>Row:</strong>  ${book.info.row_no}</span>
                                </p>
                            </div>
                        </td>
                        <td><input type="number" min="1" max="${book.info.books_stock}" value="${book.quantity}" name="book_quantity" data-book_id="${book.book_id}" class="form-control" style="width:95px"></td>
                        <td><button onclick="return issue_Request_Handler('remove', '${book.book_id}');" class="btn-sm btn-outline-danger btn"><i class="fa fa-trash"></i></button></td>
                    </tr>`;
                });
                books_list += `</table></div>`;
                if (response.data.result.books_list) {
                    selected_books_list.html(books_list);
                }
                book_quantity_Checker();
                $(`option[data-item_id='${book_id}']`).remove();
            }
        }
    });
}

var save_Issue_Request = (issue_id, request) => {
    let t_title = (request === "issue") ? "Issue Books" : "Request for Books",
        t_message = (request === "issue") ? "Are you sure you want to issue these books to the selected user?" : "Are you sure you want to request for these Books.";

    swal({
        title: t_title,
        text: t_message,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {

        if (proceed) {

            var return_date = $(`div[id='library_form'] input[name="return_date"]`),
                books_list = {};

            if (request === "issue") {
                var user_role = $(`div[id='library_form'] select[name="user_role"]`),
                    user_id = $(`div[id='library_form'] select[name="user_id"]`),
                    overdue_rate = $(`div[id='library_form'] input[name="overdue_rate"]`),
                    overdue_apply = $(`div[id='library_form'] select[name="overdue_apply"]`).val();
            } else if (request === "request") {
                user_id = $(`div[id='library_form'] input[name="user_id"]`);
            }

            $.each($(`div[id='library_form'] table tr[class="each_book_item"] input`), function(i, e) {
                let book_id = $(this).attr("data-book_id"),
                    quantity = $(this).val();
                books_list[book_id] = parseInt(quantity);
            });

            let label = {
                "todo": request,
                "mode": selected_books.attr("data-mode")
            };

            if (request === "issue") {
                label["data"] = {
                    "books_list": books_list,
                    "user_id": user_id.val(),
                    "user_role": user_role.val(),
                    "return_date": return_date.val(),
                    "overdue_rate": overdue_rate.val(),
                    "overdue_apply": overdue_apply
                }
            } else if (request === "request") {
                label["data"] = {
                    "books_list": books_list,
                    "user_id": user_id.val(),
                    "return_date": return_date.val()
                };
            }

            $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
                let s_icon = "error";
                if (response.code == 200) {
                    selected_books.html("");
                    selected_books_list.html(`<div class="font-italic">No books has been selected yet.</div>`);
                    return_date.val("");
                    if (request === "issue") {
                        user_role.val("").change(),
                            user_id.val("").change(),
                            overdue_rate.val("");
                    }
                    s_icon = "success";
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

$(`div[id="library_form"] select[name="category_id"]`).on("change", function() {
    let category_id = $(this).val(),
        mode = selected_books.attr("data-mode");

    $(`div[id='library_form'] select[name='book_id']`).find('option').remove().end();
    $(`div[id='library_form'] select[name='book_id']`).append(`<option value="null">Please Select</option>`);

    if (category_id.length) {
        $.get(`${baseUrl}api/library/list?show_in_list=${mode}&minified=true`, { category_id }).then((response) => {
            if (response.code == 200) {
                $.each(response.data.result, function(i, book) {
                    $(`div[id='library_form'] select[name='book_id']`).append(`<option data-in_session='${book.in_session}' data-row_no='${book.row_no}' data-rack_no='${book.rack_no}' data-item_id='${book.item_id}' data-isbn='${book.isbn}' data-books_stock='${book.books_stock}' data-book_image='${book.book_image}' data-book_author='${book.author}' data-book_title='${book.title}' value='${book.item_id}'>${book.title}</option>'`);
                });
            }
            selected_books.addClass("hidden");
            selected_books.html("");
        });
    }
});

$(`div[id="library_form"] select[name="user_role"]`).on("change", function() {
    let user_type = $(this).val();

    $(`div[id='library_form'] select[name='user_id']`).find('option').remove().end();
    $(`div[id='library_form'] select[name='user_id']`).append(`<option value="null">Please Select</option>`);

    if (user_type.length) {
        $.get(`${baseUrl}api/users/list?minified=simplied_load_with&user_type=${user_type}`).then((response) => {
            if (response.code == 200) {
                $.each(response.data.result, function(i, user) {
                    $(`div[id='library_form'] select[name='user_id']`).append(`<option value='${user.user_id}'>${user.name} (${user.unique_id})</option>'`);
                });
            }
        });
    }
});

$(`div[id='library_form'] select[name='book_id']`).on("change", function() {
            let book = $(`div[id='library_form'] select[name='book_id'] > option:selected`).data();
            if (book.book_author === undefined) {
                selected_books.addClass("hidden");
                selected_books.html("");
            } else {
                selected_books.removeClass("hidden");
                selected_books.html(`
            <div class="card-body pb-1">
                <div class="d-flex justify-content-start">
                    ${book.book_image ? `<div class="mr-2"><img src="${book.book_image}" width="180px"></div>` : ""}
                    <div>
                        <p class="mb-0"><i class="fa fa-book"></i> <strong>Title:</strong> ${book.book_title}</p>
                        <p class="mb-0"><i class="fa fa-book-reader"></i> <strong>ISBN:</strong> ${book.isbn}</p>
                        <p class="mb-0"><i class="fa fa-user"></i> <strong>Author:</strong> ${book.book_author}</p>
                        <p class="mb-0"><i class="fa fa-baby-carriage"></i> <strong>Quantity:</strong>  ${book.books_stock}</p>
                        <p class="mb-2">
                            <span class="mr-4"><i class="fa fa-table"></i> <strong>Rack:</strong>  ${book.rack_no}</span> 
                            <span><strong>Row:</strong>  ${book.row_no}</span>
                        </p>
                        ${book.books_stock !== 0 ? `
                        <p class="mb-0">
                            ${book.in_session !== true ? `<button onclick="return issue_Request_Handler('add', '${book.item_id}');" class="btn-sm btn-outline-success btn"><i class="fa fa-plus"></i> Add</button>` 
                            : `<button onclick="return issue_Request_Handler('remove', '${book.item_id}');" class="btn-sm btn-outline-danger btn"><i class="fa fa-trash"></i> Remove</button>`}
                        </p>
                        ` : `<p class="text-danger font-weight-600 mb-0">Sorry! This book is out of Stock</p>`}
                    </div>
                </div>
            </div>
        `);
    }
});

issue_Request_Handler("list");