var add_fees_category = () => {
    $(`div[id="feesCategoryModal"]`).modal("show");
    $(`div[id="feesCategoryModal"] h5[class="modal-title"]`).html(`Add Fees Category`);
    $(`div[id="feesCategoryModal"] input, div[id="feesCategoryModal"] textarea`).val("");
    $(`div[id="feesCategoryModal"] select[name="boarding_fees"]`).val("No").trigger("change");
    $(`div[id="feesCategoryModal"] select[name="frequency"]`).val("").trigger("change");
}

var update_fees_category = (category_id) => {
    if ($.array_stream["fees_category_array_list"] !== undefined) {
        let categories = $.array_stream["fees_category_array_list"];
        if (categories[category_id] !== undefined) {
            let category = categories[category_id];
            $(`div[id="feesCategoryModal"] h5[class="modal-title"]`).html(`Update Category Record`);
            $(`div[id="feesCategoryModal"]`).modal("show");
            $(`div[id="feesCategoryModal"] select[name="boarding_fees"]`).val(category.boarding_fees);
            $(`div[id="feesCategoryModal"] input[name="name"]`).val(category.name);
            $(`div[id="feesCategoryModal"] input[name="code"]`).val(category.code);
            $(`div[id="feesCategoryModal"] select[name="frequency"]`).val(category.frequency);
            $(`div[id="feesCategoryModal"] input[name="category_id"]`).val(category_id);
            $(`div[id="feesCategoryModal"] textarea[name="description"]`).val(category.description);
            $(`div[id="feesCategoryModal"] input[name="amount"]`).val(category.amount);
        }
    }
}

$(`div[class~="toggle-calculator"]`).addClass("hidden");