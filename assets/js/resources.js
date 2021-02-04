var course_resources_list = (search_term = "") => {
    let content = $(`div[id="courses_resources_list"]`);
    $.get(`${baseUrl}api/resources/courses_list?rq=${search_term}`).then((response) => {
        if (response.code === 200) {
            $(`div[id="total_count"]`).html(`<strong>${response.data.result.pagination.total_count} results</strong>`);
            content.html(response.data.result.html);
            init_image_popup();
        }
    });
}
if ($(`div[id="courses_resources_list"]`).length) {
    course_resources_list();
}

var search_Resource = () => {
    let term = $(`input[name="search_term"]`).val();
    course_resources_list(term);
}

$(`input[name="search_term"]`).on("keyup", function(evt) {
    if (evt.keyCode == 13 && !evt.shiftKey) {
        let term = $(`input[name="search_term"]`).val();
        course_resources_list(term);
    }
});