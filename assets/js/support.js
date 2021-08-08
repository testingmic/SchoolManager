var submit_ticket = () => {
    let department = $(`div[id="ticket_form"] select[name="department"]`).val(),
        subject = $(`div[id="ticket_form"] input[name="subject"]`).val(),
        content = $(`div[id="ticket_form"] textarea[name="content"]`).val();
    $.post(`${baseUrl}api/support/create`, {department, subject, content}).then((response) => {
        if(response.code == 200) {
            notify(response.data.result, "success");
            $(`div[id="tickets"]`).modal("hide");
            $(`div[id="ticket_form"] input, div[id="ticket_form"] textarea`).val(``);
            setTimeout(() => {
                loadPage(`${baseUrl}support`);
            }, 2000);
        } else {
            notify(response.data.result);
        }
    }).catch(() => {
        notify(`Sorry! There was an error while processing the request.`);
    });
}

var reply_ticket = () => {
    let ticket_id = $(`div[id="ticket_form"] input[name="ticket_id"]`).val(),
        content = $(`div[id="ticket_form"] textarea[name="content"]`).val();
    $.post(`${baseUrl}api/support/reply`, {ticket_id, content}).then((response) => {
        if(response.code == 200) {
            notify(response.data.result, "success");
            $(`div[id="ticket_form"] input, div[id="ticket_form"] textarea`).val(``);
            setTimeout(() => {
                loadPage(`${baseUrl}support/ticket/${ticket_id}`);
            }, 2000);
        } else {
            notify(response.data.result);
        }
    }).catch(() => {
        notify(`Sorry! There was an error while processing the request.`);
    });
}

var modify_ticket = (todo, ticket_id) => {
    swal({
        title: "Close Ticket",
        text: "Are you sure you want to close this ticket? Once closed, you cannot reply to it again.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.post(`${baseUrl}api/support/${todo}`, {ticket_id}).then((response) => {
                if(response.code == 200) {
                    notify(response.data.result, "success");
                    setTimeout(() => {
                        loadPage(`${baseUrl}support/ticket/${ticket_id}`);
                    }, 2000);
                } else {
                    notify(response.data.result);
                }
            }).catch(() => {
                notify(`Sorry! There was an error while processing the request.`);
            });
        }
    });
}

$(`div[id="ticket_form"] button[type="reset"]`).on("click", function() {
    $(`div[id="ticket_form"] input, div[id="ticket_form"] textarea`).val(``);
});

$(`table tr[class~="clickable-row"]`).on("click", function() {
    let row = $(this);
    let href = row.attr("data-href");
    loadPage(`${href}`);
});