var remove_row = (rows_count) => {
    $(`div[class~="member_item"][data-row_id='${rows_count}']`).remove();
}

var add_sibling = () => {

    let members_list = $(`div[id="log_attendance_container"] div[class~="member_item"]:last`).attr("data-row_id"),
        rows_count = isNaN(members_list) ? 1 : (parseInt(members_list) + 1);
    let member_html = `
        <div class='row member_item mt-3 border-top pt-3' data-row_id='${rows_count}'>
            <div class='col-md-11 col-sm-10'>
                <div class='form-group'>
                    <label>Fullname <span class='required'>*</span></label>
                    <input maxlength='64' type='text' name='fullname[${rows_count}]' id='fullname[${rows_count}]' class='form-control'>
                </div>
            </div>
            <div class='col-md-1 col-sm-2'>
                <div class='form-group'>
                    <label class='text-center'>.</label>
                    <button type='button' onclick='return remove_row(${rows_count})' class='btn btn-block btn-outline-danger'><i class='fa fa-trash'></i></button>
                </div>
            </div>
            <div class='col-md-3'>
                <div class='form-group'>
                    <label>Gender</label>
                    <select type='text' data-width='100%' name='gender[1]' id='gender[1]' class='selectpicker form-control'>
                        <option value=''>Please Select</option>
                        <option value='Male'>Male</option>
                        <option value='Female'>Female</option>
                    </select>
                </div>
            </div>
            <div class='col-md-4'>
                <div class='form-group'>
                    <label>Contact Number</label>
                    <input maxlength='15' type='number' name='contact[${rows_count}]' id='contact[${rows_count}]' class='form-control'>
                </div>
            </div>
            <div class='col-md-3'>
                <div class='form-group'>
                    <label>Place of Residence</label>
                    <input maxlength='32' type='text' name='residence[${rows_count}]' id='residence[${rows_count}]' class='form-control'>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='form-group'>
                    <label>Temperature <span class='required'>*</span></label>
                    <input maxlength='32' type='hidden' hidden name='item_id[${rows_count}]' id='item_id[${rows_count}]' class='form-control'>
                    <input maxlength='6' type='float' name='temperature[${rows_count}]' id='temperature[${rows_count}]' class='form-control'>
                </div>
            </div>
        </div>`;

    $(`div[id="log_attendance_container"]`).append(member_html);
}
form_submit_stopper();