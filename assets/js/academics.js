var $display_div = $(`div[id="academic_Term_Processing"]`);
var end_Academic_Term = (action = "begin") => {
    if (action === "begin") {
        $display_div.html(`
        <div class="card">
            <div class="card-body font-16">
                <div class="text-center">
                    You have opted to end this <strong>Academic ${academicSession}</strong>.
                    Once confirmed, you will be unable to use the system for not less than <strong>15 minutes</strong>.
                </div>
                <div class="mt-2 text-center">
                    This process is termed as <strong>propagation</strong> as the current term will be closed;
                    the fees owned by students will be taken into consideration and all outstanding
                    assessments will be closed and forwarded to the next academic year and term.
                </div>
                <div class="mt-2 text-center pt-2 border-top">
                    <button id="proceed_term_closure" onclick="return end_Academic_Term('proceed')" class="btn btn-outline-success">PROCEED TO END ${academicSession.toUpperCase()}</button>
                </div>
            </div>
        <div>`);
    } else if (action === "proceed") {
        swal({
            title: `End Academic ${academicSession}`,
            text: swalnotice["end_academic_term"],
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((proceed) => {
            if (proceed) {
                $.pageoverlay.show();
                let data_array = new Array();
                $.each($(`input[type="checkbox"][class~="data_to_import"]`), function() {
                    data_array.push($(this).val());
                });
                let data = data_array.join(",");
                $(`button[id="proceed_term_closure"]`).prop("disabled", true).html("Processing Request <i class='fa fa-spin fa-spinner'></i>");
                $.post(`${baseUrl}api/account/endacademicterm`, { data_to_import: data }).then((response) => {
                    $.pageoverlay.hide();
                    swal({
                        text: response.data.result,
                        icon: responseCode(response.code),
                    });
                    if (response.code == 200) {
                        setTimeout(() => {
                            window.location.href = `${baseUrl}dashboard`;
                        }, refresh_seconds);
                    }
                    $(`button[id="proceed_term_closure"]`).prop("disabled", false).html("PROCEED TO END TERM");
                }).catch(() => {
                    $(`button[id="proceed_term_closure"]`).prop("disabled", false).html("PROCEED TO END TERM");
                    swal({
                        text: swalnotice["ajax_error"],
                        icon: "error",
                    });
                    $.pageoverlay.hide();
                });
            }
        });
    }
}

var save_client_features = (client_id) => {
    swal({
        title: "Save Account Features",
        text: "Are you sure you want to save these settings?",
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then((proceed) => {
        if (proceed) {
            let features = {};
            $.each($(`div[id="account_features"] input:checked`), function(i, e) {
                let key = $(this).attr("data-menu_item");
                features[i] = key;
            });
            $.pageoverlay.show();
            $.post(`${baseUrl}api/account/features`, { features, client_id }).then((response) => {
                $.pageoverlay.hide();
                swal({
                    text: response.data.result,
                    icon: responseCode(response.code),
                });
                if (response.code == 200) {
                    setTimeout(() => {
                        window.location.href = `${current_url}`;
                    }, refresh_seconds);
                }
            }).catch(() => {
                swal({
                    text: swalnotice["ajax_error"],
                    icon: "error",
                });
                $.pageoverlay.hide();
            });
        }
    });
}