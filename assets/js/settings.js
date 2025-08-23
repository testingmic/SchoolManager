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