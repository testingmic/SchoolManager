var submit_ticket = () => {
    let department = $(`div[id="ticket_form"] select[name="department"]`).val(),
        section = $(`div[id="ticket_form"] select[name="section"]`).val(),
        subject = $(`div[id="ticket_form"] input[name="subject"]`).val(),
        content = $(`div[id="ticket_form"] textarea[name="content"]`).val();
    let string = String(content).replace(/\n/g, '<br>');
    $.post(`${baseUrl}api/support/create`, {department, subject, "content": htmlEntities(string), section}).then((response) => {
        if(response.code == 200) {
            notify(response.data.result, "success");
            $(`div[id="tickets"]`).modal("hide");
            $(`div[id="ticket_form"] input, div[id="ticket_form"] textarea`).val(``);
            setTimeout(() => {
                loadPage(`${baseUrl}support`);
            }, refresh_seconds);
        } else {
            notify(response.data.result);
        }
    }).catch(() => {
        notify(`Sorry! There was an error while processing the request.`);
    });
}

var reply_ticket = (ticket_id, section) => {
    let content = $(`div[id="ticket_form"] textarea[name="content"]`).val();
    let string = String(content).replace(/\n/g, '<br>');
    $.post(`${baseUrl}api/support/reply`, {ticket_id, "content": htmlEntities(string), section}).then((response) => {
        if(response.code == 200) {
            notify(response.data.result, "success");
            $(`div[id="ticket_form"] input, div[id="ticket_form"] textarea`).val(``);
            setTimeout(() => {
                let url_link = (section == "ticket") ? `${baseUrl}support/${section}/${ticket_id}` : `${baseUrl}${section}/item/${ticket_id}`;
                loadPage(url_link);
            }, refresh_seconds);
        } else {
            notify(response.data.result);
        }
    }).catch(() => {
        notify(`Sorry! There was an error while processing the request.`);
    });
}

var modify_ticket = (todo, ticket_id, section) => {
    swal({
        title: "Close Ticket",
        text: "Are you sure you want to close this ticket? Once closed, you cannot reply to it again.",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if(proceed) {
            $.post(`${baseUrl}api/support/${todo}`, {ticket_id, section}).then((response) => {
                if(response.code == 200) {
                    notify(response.data.result, "success");
                    setTimeout(() => {
                        let url_link = (section == "ticket") ? `${baseUrl}support/${section}/${ticket_id}` : `${baseUrl}${section}/item/${ticket_id}`;
                        loadPage(url_link);
                    }, refresh_seconds);
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