$(`form[id="auth-form"]`).on("submit", function(evt) {
    evt.preventDefault();
    let form_data = $(this).serialize(),
        form_action = $(this).attr("action");
    $(`div[class~="form-results"]`).html("");
    $(`form[id="auth-form"] *`).prop("disabled", true);

    $(`div[class="form-content-loader"]`).css("display", "flex");
    $.post(`${form_action}`, form_data, function(data) {
        if (data.result.code == 200) {
            $(`div[class~="form-results"]`).html(`<div class="alert mb-0 alert-success">${data.result.data}</div>`);
            if ($(`input[name="recover"]`).length) {
                $(`input[name="email"]`).val("");
                $(`form[id="auth-form"] *`).prop("disabled", false);
            } else {
                setTimeout(() => {
                    if ($(`link[name="current_url"]`).length) {
                        window.location.href = $(`link[name="current_url"]`).attr("value");
                    } else {
                        window.location.href = $(`link[name="current_url"]`).attr("value");
                    }
                }, data.result.refresh);
            }
        } else {
            $(`form[id="auth-form"] *`).prop("disabled", false);
            $(`div[class~="form-results"]`).html(`<div class="alert mb-0 alert-danger">${data.result.data}</div>`);
        }
        $(`div[class="form-content-loader"]`).css("display", "none");
    }, "json").catch(() => {
        $(`form[id="auth-form"] *`).prop("disabled", false);
        $(`div[class="form-content-loader"]`).css("display", "none");
    });
});