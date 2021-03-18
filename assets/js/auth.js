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
                if ($(`link[name="current_url"]`).length) {
                    setTimeout(() => {
                        window.location.href = $(`link[name="current_url"]`).attr("value");
                    }, data.result.refresh);
                }
                if (data.result.clear !== undefined) {
                    $(`form[id="auth-form"] *`).val("");
                    $(`form[id="auth-form"] *`).prop("disabled", false);
                    $(`form[id="auth-form"] input[name="plan"]`).val("basic");
                    $(`form[id="auth-form"] input[name="portal_registration"]`).val("true");
                }
            }
        } else {
            $(`form[id="auth-form"] *`).prop("disabled", false);
            if (data.result.data !== undefined) {
                $(`div[class~="form-results"]`).html(`<div class="alert mb-0 alert-danger">${data.result.data}</div>`);
            } else {
                $(`div[class~="form-results"]`).html(`<div class="alert mb-0 alert-danger">${data.result}</div>`);
            }
        }
        $(`div[class="form-content-loader"]`).css("display", "none");
    }, "json").catch(() => {
        $(`form[id="auth-form"] *`).prop("disabled", false);
        $(`div[class="form-content-loader"]`).css("display", "none");
    });
});