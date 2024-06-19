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
                        loadPage(`${baseUrl}book/${book_id}/view`);
                    }, 500);
                } else {
                    swal({
                        text: response.data.result,
                        icon: "error",
                    });
                }
            });
        }
    });
}

var book_quantity_Checker = () => {
    $(`div[id='library_form'] table tr[class~="each_book_item"] input[type="number"]`).on("input", function() {
        let input = $(this),
            value = parseInt(input.val()),
            max = parseInt(input.attr("max"));
        if (value < 1) {
            input.val(1);
            value = input;
        } else if (value > max) {
            input.val(max);
            value = max;
        }
        let book_id = input.attr("data-book_id");
        let label = {
            "todo": "update_quantity",
            "mode": selected_books.attr("data-mode"),
            "book_id": book_id,
            "quantity": value
        };
        if (value) {
            $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then(() => {}).catch(() => {
                swal({
                    text: "Sorry! An error was encountered while processing the request.",
                    icon: "error"
                });
            });
        }
    });
}

var request_quantity_Checker = () => {
    $(`div[id='books_request_details'] tr[class~="each_book_item"] input[type="number"]`).on("input", function() {
        var input = $(this);
        let value = parseInt(input.val()),
            max = parseInt(input.attr("max")),
            original = parseInt(input.attr("data-original")),
            book_id = input.attr("data-book_id");

        if (value < 1) {
            input.val(1);
        } else if (value > max) {
            input.val(max);
        }

        if (value !== original) {
            $(`button[id="save_book_${book_id}"]`).removeClass("hidden");
        } else if (value === original) {
            $(`button[id="save_book_${book_id}"]`).addClass("hidden");
        }
    });
    $(`div[id='books_request_details'] input[name="request_fine"]`).on("input", function() {
        var input = $(this);
        let value = parseInt(input.val()),
            max = parseInt(input.attr("max")),
            original = parseInt(input.attr("data-original"));

        if (value < 1) {
            input.val(1);
        } else if (value > max) {
            input.val(max);
        }

        if (value !== original) {
            $(`button[id="save_fine_"]`).removeClass("hidden");
        } else if (value === original) {
            $(`button[id="save_fine_"]`).addClass("hidden");
        }
    });
}

var issue_Request_Handler = (todo, book_id = "") => {
    let label = {
        "todo": todo,
        "mode": selected_books.attr("data-mode"),
        "book_id": book_id
    };
    if((todo !== "list") && (todo !== "update_quantity")) {
        $.pageoverlay.show();
    }
    $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
        $.pageoverlay.hide();
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
                                <p class="mb-0"><i class="fa fa-baby-carriage"></i> <strong>Stock Quantity:</strong>  ${book.info.books_stock}</p>
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
    }).catch(() => {
        swal({
            text: "Sorry! An error was encountered while processing the request.",
            icon: "error"
        });
        $.pageoverlay.hide();
    });
}

var remove_Book = (borrowed_id, book_id) => {
    swal({
        title: "Remove Book",
        text: "Are you sure you want remove this Book? \nOnce confirmed, it cannot be reversed.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let label = {
                "todo": "remove_book",
                "mode": "request",
                "data": {
                    "borrowed_id": borrowed_id,
                    "book_id": book_id
                }
            };
            $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
                if (response.code == 200) {
                    $(`tr[class~="each_book_item"][data-request_id="${borrowed_id}"][data-book_id="${book_id}"]`).remove();
                }
            }).catch(() => {
                swal({
                    text: "Sorry! An error was encountered while processing the request.",
                    icon: "error"
                });
            });
        }
    });
}

var save_Request_Fine = (borrowed_id) => {
    let fine = parseInt($(`input[name='request_fine']`).val());
    let label = {
        "todo": "save_book_fine",
        "mode": "request",
        "data": {
            "borrowed_id": borrowed_id,
            "fine": fine
        }
    };
    $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
        if (response.code == 200) {
            $(`button[id="save_fine_"]`).addClass("hidden");
            $(`input[name='request_fine']`).attr("data-original", fine);
            $(`span[data-each_fine='request']`).html(response.data.result);
        }
    });
}

var return_Requested_Book = (mode, borrowed_id, fine = 0) => {
    let f_note = fine > 1 ? "\nConfirming this indicates that the user has paid the fine." : "";
    let s_title = (mode === "entire_order") ? "Return Order" : "Return Book",
        s_message = (mode === "entire_order") ? `Are you sure you want to return the entire books? ${f_note}` : `Are you sure you want to return this book from the list? ${f_note}`;
    swal({
        title: s_title,
        text: s_message,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let label = {
                "todo": "return_books",
                "mode": "request",
                "data": {
                    "return_mode": mode,
                    "record_id": borrowed_id
                }
            };
            $.pageoverlay.show();
            $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
                $.pageoverlay.hide();
                if (response.code == 200) {
                    s_icon = "success";
                    if (mode == "entire_order") {
                        $(`td[id="return_book_column"]`).html(`<span class="badge badge-success">Returned</span>`);
                        $(`div[id=" id='return_all_container'"]`).html(``);
                    } else {
                        $(`td[class="return_book_column_${borrowed_id}"]`).html(`<span class="badge badge-success">Returned</span>`);
                    }
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code)
                });
            }).catch(() => {
                swal({
                    text: "Sorry! An error was encountered while processing the request.",
                    icon: "error"
                });
                $.pageoverlay.hide();
            });
        }
    });
}

var save_Book_Quantity = (borrowed_id, book_id) => {
    let quantity = parseInt($(`input[data-request_id="${borrowed_id}"][data-book_id="${book_id}"]`).val());
    let label = {
        "todo": "save_book_quantity",
        "mode": "request",
        "data": {
            "borrowed_id": borrowed_id,
            "book_id": book_id,
            "quantity": quantity
        }
    };
    $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
        if (response.code == 200) {
            $(`button[id="save_book_${book_id}"]`).addClass("hidden");
            $(`input[data-request_id="${borrowed_id}"][data-book_id="${book_id}"]`).attr("data-original", quantity);
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

            if (request === "issued") {
                var user_role = $(`div[id='library_form'] select[name="user_role"]`),
                    user_id = $(`div[id='library_form'] select[name="user_id"]`),
                    overdue_rate = $(`div[id='library_form'] input[name="overdue_rate"]`),
                    overdue_apply = $(`div[id='library_form'] select[name="overdue_apply"]`).val();
            } else if (request === "request") {
                user_role = $(`div[id='library_form'] input[name="user_role"]`);
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

            if (request === "issued") {
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
                    "user_role": user_role.val(),
                    "return_date": return_date.val()
                };
            }
            $.pageoverlay.show();
            $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
                $.pageoverlay.hide();
                if (response.code == 200) {
                    selected_books.html("");
                    selected_books_list.html(`<div class="font-italic">No books has been selected yet.</div>`);
                    return_date.val("");
                    if (request === "issued") {
                        user_role.val("").change(),
                            user_id.val("").change(),
                            overdue_rate.val("");
                    }
                    setTimeout(() => {
                        loadPage(`${baseUrl}book_request/${response.data.additional.href}`);
                    },1000);
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
            }).catch(() => {
                $.pageoverlay.hide();
                swal({
                    text: "Sorry! An error was encountered while processing the request.",
                    icon: "error"
                });
            });
        }
    });
}

var approve_Cancel_Books_Request = (borrowed_id, todo) => {
    let t_title = (todo == "approve_request") ? "Approve Request" : "Cancel Request",
        t_text = (todo == "approve_request") ? "approve" : "cancel";
    swal({
        title: t_title,
        text: `Are you sure you want to ${t_text} the request? \nOnce confirmed, it cannot be reversed.`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let label = {
                "todo": todo,
                "mode": "request",
                "data": {
                    "borrowed_id": borrowed_id
                }
            };
            $.pageoverlay.show();
            $.post(`${baseUrl}api/library/issue_request_handler`, { label }).then((response) => {
                $.pageoverlay.hide();
                if (response.code == 200) {
                    if (response.data.additional.reload !== undefined) {
                        setTimeout(() => {
                            loadPage(`${baseUrl}book_request/${borrowed_id}`);
                        }, 700);
                    }
                }
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
            }).catch(() => {
                $.pageoverlay.hide();
                swal({
                    text: "Sorry! An error was encountered while processing the request.",
                    icon: "error"
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
        $.get(`${baseUrl}api/users/quick_list?user_type=${user_type}`).then((response) => {
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

var reset_book_stock = () => {
    swal({
        title: `Cancel`,
        text: `Are you sure you want cancel this update?`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            var lastRowId = $(`div[class~="books_stock_update"] div[data-row]`).length;
            if (lastRowId > 1) {
                $.each($(`div[class~="books_stock_update"] div[data-row]`), function(key, val) {
                    if (lastRowId != 1) {
                        $(`div[class~="books_stock_update"] div[data-row="${lastRowId}"]`).remove();
                        lastRowId--;
                    }
                });
            }
            $(`div[class~="books_stock_update"] select[name="book_id_1"]`).val(``).change();
            $(`input[name="quantity_1"], input[name="threshold_1"]`).val(0);
            loadPage(`${baseUrl}books_stock/list`);
        }
    });
}

var clear_stock_update_rows = () => {
    var lastRowId = $(`div[class~="books_stock_update"] div[data-row]`).length;
    if (lastRowId > 1) {
        $.each($(`div[class~="books_stock_update"] div[data-row]`), function(key, val) {
            if (lastRowId != 1) {
                $(`div[class~="books_stock_update"] div[data-row="${lastRowId}"]`).remove();
                lastRowId--;
            }
        });
    }
    $(`div[class~="books_stock_update"] select[name="book_id_1"]`).val(``).change();
    $(`input[name="quantity_1"], input[name="threshold_1"]`).val(0);
}

var update_book_stock = () => {
    var books_list = {},
        stock_quantity = "";
    $.each($(`div[class~="books_stock_update"] div[class~="books_content"]`), function(i, e) {
        var rowId = $(this).attr("data-row");
        if($(`select[id="book_id_${rowId}"]`).val() != 'null') {
            let _book_id = $(`select[id="book_id_${rowId}"]`).val(),
                _quantity = $(`input[name="quantity_${rowId}"]`).val(),
                _book_title = $(`input[name="book_title_${rowId}"]`).val();
            if(_book_id.length) {
                books_list[i] = {
                    book_id: _book_id,
                    quantity: _quantity,
                    treshold: $(`input[name="threshold_${rowId}"]`).val()
                }
                stock_quantity += `${_book_title}: ${_quantity}\n`;
            }
        }
    });

    if(Object.keys(books_list).length == 0) {
        swal({
            text: 'Please select at least one item to continue.',
            icon: 'error',
        });
        return false;
    }

    swal({
        title: `Update Books Stock`,
        text: `Are you sure you want to update the stock quantities of the following Books:\n${stock_quantity}`,
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.pageoverlay.show();
            $.post(`${baseUrl}api/library/update_stock`,{stock_quantity:books_list}).then((response) => {
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if(response.code == 200) {
                    clear_stock_update_rows();
                    setTimeout(() => {
                        loadPage(`${baseUrl}books_stock/list`);
                    }, refresh_seconds);
                }
                $.pageoverlay.hide();
            }).catch(() => {
                $.pageoverlay.hide();
                swal({
                    text: "Sorry! An error was encountered while processing the request.",
                    icon: "error"
                });
            });
        }
    });
}

var select_book_options = () => {
    $(`div[class~="books_stock_update"] div[data-row] select`).on("change", function() {
        let rowId = $(this).attr("data-row"),
            stock_quantity = $(`div[class~="books_stock_update"] select[data-row="${rowId}"] option:selected`).attr("data-books_stock"),
            book_title = $(`div[class~="books_stock_update"] select[data-row="${rowId}"] option:selected`).attr("data-book_title");
        $(`div[class~="books_stock_update"] input[name="stock_quantity_${rowId}"]`).val(stock_quantity);
        $(`div[class~="books_stock_update"] input[name="book_title_${rowId}"]`).val(book_title);
    });
    $(`div[class~="books_stock_update"] input[name^="quantity_"]`).on('input', function() {
        let rowId = $(this).attr("data-row");
        let _value = parseInt($(this).val()),
            _init = parseInt($(`div[class~="books_stock_update"] select[data-row="${rowId}"] option:selected`).attr("data-books_stock"));
        if(!isNaN(_value)) {
            let total = _value + _init;
            $(`div[class~="books_stock_update"] input[name="stock_quantity_${rowId}"]`).val(total);
        } else {
            $(`div[class~="books_stock_update"] input[name="stock_quantity_${rowId}"]`).val(_init);
        }
    });
}

var remove_book_row = (rowId) => {
    $(`div[class~="books_stock_update"] div[data-row="${rowId}"]`).remove();
}

var append_new_book_row = () => {

    var htmlData = $('div[class~="books_stock_update"] div[data-row]:last select').html(),
        lastRowId = $(`div[class~="books_stock_update"] div[data-row]`).length,
        selectOptions = $('div[class~="books_stock_update"] div[data-row]:last select > option').length;

    lastRowId++;

    if(selectOptions == lastRowId) {
        return false;
    }

    $(`div[class~="books_stock_update"] div[data-row]:last`).after(`
        <div class="row books_content" data-row="${lastRowId}">
            <div class="col-md-5 mb-3">
                <div>
                    <select data-row="${lastRowId}" name="book_id_${lastRowId}" id="book_id_${lastRowId}" class="form-control selectpicker">
                        ${htmlData}
                    </select>
                </div>
            </div>

            <div class="col-md-2 mb-3">
                <input type="number" data-row="${lastRowId}" step="1" value="0" class="form-control" min="1" name="quantity_${lastRowId}">
            </div>
            <div class="col-md-2 mb-3">
                <input data-row="${lastRowId}" name="book_title_${lastRowId}" type="hidden">
                <input type="number" data-row="${lastRowId}" step="1" value="0" class="form-control" min="1" name="threshold_${lastRowId}">
            </div>
            <div class="col-md-2 mb-3">
                <input type="number" data-row="${lastRowId}" disabled class="form-control" min="1" name="stock_quantity_${lastRowId}">
            </div>
            <div class="col-md-1 text-center">
                <span onclick="return remove_book_row(${lastRowId});" class="btn btn-outline-danger" data-value="${lastRowId}"><i class="fa fa-trash"></i></span>
            </div>                        
        </div>
    `);
    $(`select[class~="selectpicker"]`).select2();
    select_book_options();
}

if(selected_books.length) {
    book_quantity_Checker();
    issue_Request_Handler("list");
} else {
    select_book_options();
    request_quantity_Checker();
}