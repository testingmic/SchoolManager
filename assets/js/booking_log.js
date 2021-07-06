var remove_row = (rows_count) => {
    $(`div[class~="member_item"][data-row_id='${rows_count}']`).remove();
}

var delayTimer, membersSearchList;

var select_member = (user_id, row_id) => {
    if(membersSearchList[user_id] !== undefined) {
        let member = membersSearchList[user_id];
        $(`input[name="item_id[${row_id}]"]`).val(member.item_id);
        $(`input[name="fullname[${row_id}]"]`).val(member.fullname);
        $(`input[name="residence[${row_id}]"]`).val(member.residence);
        $(`input[name="contact[${row_id}]"]`).val(member.contact);
        $(`select[name="gender[${row_id}]"]`).val(member.gender).change();
    }
    $(`div[class~="picomplete-items"][data-row_id="${row_id}"]`).html(``);
}

var suggest_member = (search_term, row_id = 1) => {
    $(`div[class~="picomplete-items"]`).html(``);
    if(search_term.length > 1) {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(() => {
            let data = {
                request : "list",
                name: search_term
            };
            let members_list = "";
            $.post(`${baseUrl}api/booking/members`, {data}).then((response) => {
                if(response.code == 200) {
                    membersSearchList = response.data.result;
                    if(response.data.result) {
                        $.each(response.data.result, function(i, e) {
                            members_list += `
                            <div onclick='return select_member("${e.item_id}", ${row_id})' class='picomplete-item' data-value='${e.fullname}' data-index='${e.item_id}'>
                                ${e.fullname}
                            </div>`;
                        });
                        $(`div[class~="picomplete-items"][data-row_id="${row_id}"]`).html(members_list);
                    }
                }
            });
        }, 700);
    }
}

var add_sibling = () => {

    let members_list = $(`div[id="log_attendance_container"] div[class~="member_item"]:last`).attr("data-row_id"),
        rows_count = isNaN(members_list) ? 1 : (parseInt(members_list) + 1);
    let member_html = `
        <div class='row member_item mt-3 border-top pt-3' data-row_id='${rows_count}'>
            <div class='col-md-11 col-sm-10'>
                <div class='form-group'>
                    <label>Fullname <span class='required'>*</span></label>
                    <input oninput='return suggest_member(this.value, ${rows_count})' maxlength='64' type='text' name='fullname[${rows_count}]' id='fullname[${rows_count}]' class='form-control'>
                    <div data-row_id='${rows_count}' class='picomplete-items col-12 p-0'></div>
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
                    <select type='text' data-width='100%' name='gender[${rows_count}]' id='gender[${rows_count}]' class='selectpicker form-control'>
                        <option value=''>Please Select</option>
                        <option value='Male'>Male</option>
                        <option value='Female'>Female</option>
                    </select>
                </div>
            </div>
            <div class='col-md-4'>
                <div class='form-group'>
                    <label>Contact Number</label>
                    <input maxlength='12' type='number' min='0' name='contact[${rows_count}]' id='contact[${rows_count}]' class='form-control'>
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
    $('.selectpicker').select2();
}
form_submit_stopper();