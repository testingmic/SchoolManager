$(`button[type="refresh"]`).on("click", function() {
    let method = $(`div[class="filters"] select[name="method"]`).val(),
        resource = $(`div[class="filters"] select[name="resource"]`).val();
    window.location.href = `${baseUrl}endpoints?method=${method}&resource=${resource}`
});

var reset_form = () => {
    $(`div[class="form-results"]`).html("");
    $(`div[class~="endpoint-info"], a[class~="refresh-button"]`).addClass("hidden");
    $(`form[class="endpoint-form"] input, form[class="endpoint-form"] textarea`).val("");
    $(`form[class="endpoint-form"] textarea[name="parameter"]`).val(`{"limit":"The number of rows to limit the result"}`);
    $(`form[class="endpoint-form"] select`).val("").change();
    $(`form[class="endpoint-form"] input[name="request"]`).val("add");
    $(`form[class="endpoint-form"] select[name="status"]`).val("active").change();
}

$(`button[type="add"]`).on("click", function() {
    reset_form();
});

$(`button[data-function="update"]`).on("click", function() {
    let id = $(this).attr("data-item");
    $.post(`${baseUrl}endpoints`, { endpoint_id: id, label: "fetch" }, function(response) {
        if (response.date_created) {
            $(`form[class="endpoint-form"] input[name="endpoint"]`).val(response.endpoint);
            $(`form[class="endpoint-form"] input[name="resource"]`).val(response.resource);
            $(`form[class="endpoint-form"] input[name="endpoint_id"]`).val(response.item_id);
            $(`form[class="endpoint-form"] input[name="request"]`).val("update");
            $(`span[class="date_created"]`).html(response.date_created);
            $(`span[class="last_updated"]`).html(response.last_updated);
            $(`div[class~="endpoint-info"], a[class~="refresh-button"]`).removeClass("hidden");
            // $(`form[class="endpoint-form"] select[name="method"]`).val(response.method.toLowerCase()).change();
            $(`form[class="endpoint-form"] select[name="status"]`).val(response.status).change();
            $(`form[class="endpoint-form"] textarea[name="parameter"]`).val(response.parameter);
            $(`form[class="endpoint-form"] textarea[name="description"]`).val(response.description);
        }
    }, "json");
});

$(`button[data-function="delete"]`).on("click", function() {
    let id = $(this).attr("data-item"),
        msg = $(this).attr("data-msg"),
        label = $(this).attr("data-label");
    if (confirm(msg)) {
        $.post(`${baseUrl}endpoints`, { endpoint_id: id, label: label }, function(response) {
            if (response.code == 200) {
                if (id == parseInt($(`input[name="endpoint_id"]`).val())) {
                    reset_form();
                }
                if (label == "deprecate") {
                    $(`tr[data-item="${id}"] td:last`).html("");
                    let fc = $(`span[class="resource-${id}"]`).html();
                    $(`a[class~="refresh-button"]`).removeClass("hidden");
                    $(`tr[data-item="${id}"] span[class="resource-${id}"]`).html(`${fc}<br><span class="badge badge-danger">Deprecated</span>`);
                } else if (label == "restore") {
                    window.location.href = `${baseUrl}endpoints?end_id=${id}`;
                } else if (label == "delete") {
                    window.location.href = `${baseUrl}endpoints`;
                }
            }
        }, "json");
    }
});

$(`form[class="endpoint-form"]`).on("submit", function(evt) {
    evt.preventDefault();
    let form_data = $(this).serialize() + "&label=save";
    $(`div[class="form-results"]`).html("");
    $.post(`${baseUrl}endpoints`, form_data, function(response) {
        if (response.code == 200) {
            $(`div[class="form-results"]`).html(`<div class="alert alert-success">${response.data}</div>`);
            if (response.record_id) {
                reset_form();
                setTimeout(() => {
                    window.location.href = `${baseUrl}endpoints?end_id=${response.record_id}`;
                }, refresh_seconds);
            }

        } else {
            $(`div[class="form-results"]`).html(`<div class="alert alert-danger">${response.data}</div>`);
        }
    }, "json");
});