var add_fees_category = () => {
    $(`div[id="feesCategoryModal"]`).modal("show");
    $(`div[class~="modal-backdrop"]`).addClass("hidden");
    $(`div[id="feesCategoryModal"] h5[class="modal-title"]`).html(`Add Fees Category`);
    $(`div[id="feesCategoryModal"] input, div[id="feesCategoryModal"] textarea`).val("");
}

var update_fees_category = (category_id) => {
    if ($.array_stream["fees_category_array_list"] !== undefined) {
        let activity_log = $.array_stream["fees_category_array_list"];
        if (activity_log[category_id] !== undefined) {
            let category = activity_log[category_id];
            $(`div[id="feesCategoryModal"] h5[class="modal-title"]`).html(`Update Category Record`);
            $(`div[id="feesCategoryModal"]`).modal("show");
            $(`div[id="feesCategoryModal"] input[name="name"]`).val(category.name);
            $(`div[id="feesCategoryModal"] input[name="code"]`).val(category.code);
            $(`div[id="feesCategoryModal"] input[name="category_id"]`).val(category_id);
            $(`div[id="feesCategoryModal"] textarea[name="description"]`).val(category.description);
            $(`div[id="feesCategoryModal"] input[name="amount"]`).val(category.amount);
            setTimeout(() => {
                $(`div[class~="modal-backdrop"]`).addClass("hidden");
            }, 200);
        }
    }
}

$(`div[class~="toggle-calculator"]`).addClass("hidden");