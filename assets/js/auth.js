var school_id = null, school_code = null;

$(`select[id="school_id"]`).on("change", function() {
    school_id = $(this).val();
});

$(`form[id="auth-form"]`).on("submit", function(evt) {
    evt.preventDefault();
    let form_data = $(this).serialize(),
        form_action = $(this).attr("action");
    $(`div[class~="form-results"]`).html("");
    $(`form[id="auth-form"] *`).prop("disabled", true);

    if(school_id) {
        form_data += `&school_id=${school_id}`;
    }
    
    school_code = school_code ? school_code : $(`input[name="school_code"]`).val();
    if(school_code !== $(`input[name="school_code"]`).val()) {
        school_code = $(`input[name="school_code"]`).val();
    }

    form_data += `&school_code=${school_code}`;

    $(`div[class="form-content-loader"]`).css("display", "flex");
    $.post(`${form_action}`, form_data, function(response) {
        if (response.code == 200) {
            $(`div[class~="form-results"]`).html(`<div class="alert mb-0 alert-success">${response.data?.result || ''}</div>`);
            if ($(`input[name="recover"]`).length) {
                $(`input[name="email"]`).val("");
                $(`form[id="auth-form"] *`).prop("disabled", false);
            } else {
                if ($(`link[name="current_url"]`).length) {
                    setTimeout(() => {
                        window.location.href = $(`link[name="current_url"]`).attr("value");
                    }, response.data?.refresh);
                }
                if (typeof response.data?.clear !== 'undefined') {
                    $(`form[id="auth-form"] *`).val("");
                    $(`form[id="auth-form"] *`).prop("disabled", false);
                    $(`form[id="auth-form"] input[name="plan"]`).val("basic");
                    $(`form[id="auth-form"] input[name="portal_registration"]`).val("true");
                }
            }
            if(typeof response.data?.proceed_signup !== 'undefined') {
                $(`div[class~="contact_number_group"]`).removeClass("hidden");
                $(`input[name="contact_number"]`).removeAttr("disabled");
                $(`button[id="validate_code"]`)
                    .prop("disabled", false)
                    .html("Proceed to Signup");
            }
        } else {
            $(`form[id="auth-form"] *`).prop("disabled", false);
            if (typeof response?.data?.result !== 'undefined') {
                $(`div[class~="form-results"]`).html(`<div class="alert mb-0 alert-danger">${response?.data?.result || 'An error occurred'}</div>`);
            } else {
                $(`div[class~="form-results"]`).html(`<div class="alert mb-0 alert-danger">${response?.result || 'An error occurred'}</div>`);
            }
        }
        $(`div[class="form-content-loader"]`).css("display", "none");
    }, "json").catch((error) => {
        let parsed_error = JSON.parse(error.responseText);
        let message = typeof parsed_error?.data === 'object' ? parsed_error?.data?.result : parsed_error?.data;
        $(`form[id="auth-form"] *`).prop("disabled", false);
        $(`div[class~="form-results"]`).html(`<div class="alert mb-0 alert-danger">${message || 'An error occurred'}</div>`);
        $(`div[class="form-content-loader"]`).css("display", "none");
    });
});

$('.selectpicker').each((index, el) => {
    let select = $(el),
        title = select.attr("data-select-title"),
        itemText = select.attr("data-itemtext"),
        itemsText = select.attr("data-itemstext"),
        width = select.attr("data-select-width"),
        maxOptions = select.attr("data-select-max");

    select.select2();
});