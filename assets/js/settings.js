var update_card_preview = () => {
    
    let card_preview_front = $(`div[class="card-preview-front-body"]`);
    
    let front_color = $(`input[name="front_color"]`).val();
    let front_text_color = $(`input[name="front_text_color"]`).val();

    card_preview_front.css("background-color", front_color);
    card_preview_front.css("color", front_text_color);
    

    let card_preview_back = $(`div[class="card-preview-back"]`);
    let contact_numbers = $(`input[name="contact_numbers"]`).val();
    let back_found_message = $(`textarea[name="back_found_message"]`).val();
    let back_color = $(`input[name="back_color"]`).val();
    let back_text_color = $(`input[name="back_text_color"]`).val();
    
    card_preview_back.css("background-color", back_color);
    card_preview_back.css("color", back_text_color);
    card_preview_back.find("div[data-item='back_found_message']").html(back_found_message);
    card_preview_back.find("div[data-item='contact_numbers']").html(contact_numbers);

    return true;
}

var id_card_modal = () => {
    $(`div[id="idCardModal"]`).modal("show");
    $(`div[id="idCardModal"] h5[class="modal-title"]`).html(`Generate ID Cards`);
    $(`div[id="idCardModal"] input, div[id="idCardModal"] textarea`).val("");
}

var card_preview = (card_preview_id) => {

    $.get(`${baseUrl}api/cards/preview?card_preview_id=${card_preview_id}`).then((response) => {
        if (response.code == 200) {
            $(`div[id="previewCardModal"]`).modal("show");
            let result = response.data.result;
            $(`div[id="previewCardModal"] div[class="idcard-preview"]`).html(`
                ${result.idcard}
                <div class="text-center mt-3 border-top pt-2">
                    <a href="${baseUrl}download/idcard/?card_preview_id=${card_preview_id}" target="_blank" class="btn btn-primary">Download ID Card</a>
                </div>
            `);
            $(`div[id="previewCardModal"]`).modal("show");
        }
    }).catch((error) => {
        console.log(error);
    });
}

$(`select[id="user_category"]`).on("change", function() {
    let value = $(this).val();
    $(`div[id="attendance_log_list"]`).html(`<div class="text-center font-italic">${no_content_wrapper('Attendance', 'Sorry! The selected date is a weekend which has been excluded from recording attendance.')}</div>`);
    if (value == "null") {
        $(`div[class~="user_category_list"]`).addClass("hidden");
    } else if (value == "student") {
        $.get(`${baseUrl}api/classes/list?columns=a.id,a.item_id,a.name`).then((response) => {
            if (response.code == 200) {
                $(`div[class~="user_category_list"]`).removeClass("hidden");
                $(`select[name="user_category_list"]`).find('option').remove().end();
                $(`select[name="user_category_list"]`).append(`<option value="null" selected="selected">Select User</option>`);
                $.each(response.data.result, (_, e) => {
                    $(`select[name="user_category_list"]`).append(`<option data-item_id="${e.item_id}" value='${e.id}'>${e.name}</option>'`);
                });
            }
        });
    } else {
        $(`div[id="attendance"] div[class="form-content-loader"]`).css({ "display": "flex" });
        $(`div[class~="user_category_list"]`).addClass("hidden");
    }
});