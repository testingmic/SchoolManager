var removeRow = () => {
    $(`button[class~="remove_guardian_row"]`).on('click', function() {
        let rowId = $(this).attr('data-row');
        $(`div[id="student_guardian_list"] [data-row="${rowId}"]`).remove();
    });
}

async function randomInt(length = 12) {
    var result = '',
        characters = '123456789',
        charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}

var generate_payment_report = (student_id) => {
    let category_id = $(`select[id="category_id"]`).val(),
        start_date = $(`input[name="group_start_date"]`).val(),
        end_date = $(`input[name="group_end_date"]`).val();
    window.open(`${baseUrl}receipt?category_id=${category_id}&start_date=${start_date}&end_date=${end_date}&student_id=${student_id}`);
}

$(`div[id="student_guardian_list"] button[class~="append-row"]`).on('click', async function(e) {

    let htmlData = $('div[id="student_guardian_list"] div[data-row]:last select').html(),
        lastRowId = $(`div[id="student_guardian_list"] div[data-row]`).length;
    let random_int = await randomInt(8);

    lastRowId++;

    let selectOptions = $('div[id="student_guardian_list"] div[data-row]:last select > option').length;

    if ((selectOptions + 3) == lastRowId) {
        return false;
    }

    $(`div[id="student_guardian_list"] div[data-row]:last`).after(`
        <div class="row mb-3 mt-3 pb-3" data-row="${lastRowId}">
            <div class="col-lg-4 col-md-4">
                <label for="guardian_info[guardian_fullname][${lastRowId}]">Fullname</label>
                <input type="hidden" name="guardian_info[guardian_id][${lastRowId}]" id="guardian_info[guardian_id][${lastRowId}]" value="${random_int}">
                <input type="text" name="guardian_info[guardian_fullname][${lastRowId}]" id="guardian_info[guardian_fullname][${lastRowId}]" class="form-control">
            </div>            
            <div class="col-lg-4 col-md-4">
                <label for="guardian_info[guardian_contact][${lastRowId}]">Contact Number</label>
                <input type="text" name="guardian_info[guardian_contact][${lastRowId}]" id="guardian_info[guardian_contact][${lastRowId}]" class="form-control">
            </div>
            <div class="col-lg-3 col-md-3">
                <label for="guardian_info[guardian_email][${lastRowId}]">Email Address</label>
                <input type="text" name="guardian_info[guardian_email][${lastRowId}]" id="guardian_info[guardian_email][${lastRowId}]" class="form-control">
            </div>
            <div class="col-lg-1 col-md-1 text-right">
                <div class="d-flex justify-content-end">
                    <div>
                        <br>
                        <button data-row="${lastRowId}" class="btn remove_guardian_row btn-danger" type="button"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 mt-2">
                <label for="guardian_info[guardian_relation][${lastRowId}]">Relationship</label>
                <select name="guardian_info[guardian_relation][${lastRowId}]" id="guardian_info[guardian_relation][${lastRowId}]" class="form-control selectpicker">
                    ${htmlData}
                </select>
            </div>
            <div class="col-lg-8 col-md-8 mt-2">
                <label for="guardian_info[guardian_address][${lastRowId}]">Address</label>
                <input type="text" name="guardian_info[guardian_address][${lastRowId}]" id="guardian_info[guardian_address][${lastRowId}]" class="form-control">
            </div>
        </div>
    `);

    $(`select[class~="selectpicker"]`).select2();
    removeRow();
});
removeRow();