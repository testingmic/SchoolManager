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
            $(`form[id="auth-form"] *`).prop("disabled", false);
            $(`div[class~="form-results"]`).html(`
                <div class="flex items-center p-3 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        <span class="font-medium">${response.data?.result || ''}</span>
                    </div>
                </div>
            `);
            if ($(`input[name="recover"]`).length) {
                $(`input[name="email"]`).val("");
            } else {
                if(typeof response.data?.access_token !== 'undefined') {
                    localStorage.setItem("mgh_access_token", response.data.access_token);
                }
                if ($(`link[name="current_url"]`).length) {
                    setTimeout(() => {
                        window.location.href = $(`link[name="current_url"]`).attr("value");
                    }, 2000);
                }
                if (typeof response.data?.clear !== 'undefined') {
                    $(`form[id="auth-form"] *`).val("");
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
            if(typeof response.data?.refresh_page !== 'undefined') {
                setTimeout(() => {
                    window.location.href = baseUrl;
                }, 2000);
            }
        } else {
            $(`form[id="auth-form"] *`).prop("disabled", false);
            if (typeof response?.data?.result !== 'undefined') {
                $(`div[class~="form-results"]`).html(`
                <div class="flex items-center p-3 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        <span class="font-medium">${response?.data?.result || 'An error occurred'}</span>
                    </div>
                </div>`);
            } else {
                $(`div[class~="form-results"]`).html(`
                <div class="flex items-center p-3 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        <span class="font-medium">${response?.result || 'An error occurred'}</span>
                    </div>
                </div>`);
            }
        }
        $(`div[class="form-content-loader"]`).css("display", "none");
    }, "json").catch((error) => {
        let parsed_error = JSON.parse(error.responseText);
        let message = typeof parsed_error?.data === 'object' ? parsed_error?.data?.result : parsed_error?.data;
        $(`form[id="auth-form"] *`).prop("disabled", false);
        $(`div[class~="form-results"]`).html(`
            <div class="flex items-center p-3 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    <span class="font-medium">${message || 'An error occurred'}</span>
                </div>
            </div>`);
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

// Password toggle functionality
$(document).ready(function() {
    $('#togglePassword').on('click', function() {
        const passwordInput = $('#password');
        const eyeIcon = $('#eyeIcon');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});

// check if the access token is valid
if(localStorage.getItem("mgh_access_token")) {
    if($(`button[id="togglePassword"]`).length) {
        $.post(`${baseUrl}api/auth/validate_token`, { 
            itoken: localStorage.getItem("mgh_access_token"),
            action: "validate_token"
         }, function(response) {
            if(response.code == 200) {
                localStorage.setItem("mgh_access_token", response.data.access_token);
                window.location.reload();
            }
        }).catch((error) => {
            let data = error.responseJSON.data;
            if(typeof data.reason !== 'undefined' && data.reason === "invalid_access_token") {
                localStorage.removeItem("mgh_access_token");
            }
        });
    }
}