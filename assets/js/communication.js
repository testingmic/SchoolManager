var reset_communication_form = (form_url, title = "Create Template") => {
    swal({
        title: "Cancel Form",
        text: "Are you sure you want to cancel this form?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            $(`form[class="ajax-data-form"] input, form[class="ajax-data-form"] textarea`).val("");
            $(`div[id="communication_form"] [class="card-header"]`).html(title);
            $(`div[id="communication_form"] form[class="ajax-data-form"]`).attr("action", `${baseUrl}${form_url}`);
        }
    });
}

$(document).ready(function() {
    // SMS characters counter
    var $remaining = $(`span[class~="remaining_count"]`),
        $messages = $remaining.next();
    $(`textarea[name="message"]`).keyup(function() {
        var chars = this.value.length,
            messages = Math.ceil(chars / 160),
            remaining = messages * 160 - (chars % (messages * 160) || messages * 160);
        $remaining.text(remaining + ' characters remaining');
        $messages.text(messages + ' message');
    });
});