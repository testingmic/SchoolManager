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

$(`div[id="library_form"] select[name="category_id"]`).on("change", function() {
    let category_id = $(this).val(),
        mode = selected_books.attr("data-mode");
    if (category_id.length) {
        $.get(`${baseUrl}api/library/list?show_in_list=${mode}&minified=true`, { category_id }).then((response) => {
            if (response.code == 200) {
                $(`div[id='library_form'] select[name='book_id']`).find('option').remove().end();
                $(`div[id='library_form'] select[name='book_id']`).append(`<option value="null">Please Select</option>`);
                $.each(response.data.result, function(i, book) {
                    $(`div[id='library_form'] select[name='book_id']`).append(`<option data-row_no='${book.row_no}' data-rack_no='${book.rack_no}' data-item_id='${book.item_id}' data-isbn='${book.isbn}' data-books_stock='${book.books_stock}' data-book_image='${book.book_image}' data-book_author='${book.author}' data-book_title='${book.title}' value='${book.item_id}'>${book.title}</option>'`);
                });
            }
            selected_books.addClass("hidden");
            selected_books.html("");
        });
    }
});

var issue_Request_Handler = (todo, book_id) => {
    let label = {
        "todo": todo,
        "book_id": book_id
    };
    $.post(`${baseUrl}api/issue_request_handler`, { label }).then((response) => {
        if (response.code == 200) {

        }
    });
}

$(`div[id='library_form'] select[name='book_id']`).on("change", function() {
            let book = $(`div[id='library_form'] select[name='book_id'] > option:selected`).data();
            if (book.book_author === undefined) {
                selected_books.addClass("hidden");
                selected_books.html("");
            } else {
                selected_books.removeClass("hidden");
                selected_books.html(`
            <div class="card-body">
                <div class="d-flex justify-content-start">
                    ${book.book_image ? `<div class="mr-2"><img src="${book.book_image}" width="180px"></div>` : ""}
                    <div>
                        <p class="mb-0"><i class="fa fa-book"></i> <strong>Title:</strong> ${book.book_title}</p>
                        <p class="mb-0"><i class="fa fa-book-reader"></i> <strong>ISBN:</strong> ${book.isbn}</p>
                        <p class="mb-0"><i class="fa fa-user"></i> <strong>Author:</strong> ${book.book_author}</p>
                        <p class="mb-0"><i class="fa fa-baby-carriage"></i> <strong>Quantity:</strong>  ${book.books_stock}</p>
                        <p class="mb-3">
                            <span class="mr-4"><i class="fa fa-table"></i> <strong>Rack:</strong>  ${book.rack_no}</span> 
                            <span><strong>Row:</strong>  ${book.row_no}</span>
                        </p>
                        <p class="mb-0">
                            <button onclick="return issue_Request_Handler('add', '${book.item_id}');" class="btn-sm btn-outline-success btn"><i class="fa fa-plus"></i> Add</button>
                            <button onclick="return issue_Request_Handler('remove', '${book.item_id}');" class="btn-sm btn-outline-danger btn"><i class="fa fa-trash"></i> Remove</button>
                        </p>
                    </div>
                </div>
            </div>
        `);
    }
});