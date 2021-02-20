const DB_NAME = "myschool_gh-db";
const STORE_NAME = "persistent_store";
const DB_VERSION = 1;

var idb, $note = {
    "upf_": "User Preferences",
    "inc_": "Insurance Companies",
    "inp_": "Insurance Policies",
    "for_": "Forms Data"
};

const console_log = (message) => {
    var currentDate = '[' + new Date().toUTCString() + '] ';
    console.log(currentDate, message);
}

$(async() => {
    'use strict'

    if (!('indexedDB' in window)) {
        console.warn("IndexDB not supported");
        return;
    }

    function open_db() {
        var indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
        var req = indexedDB.open(DB_NAME, DB_VERSION);
        req.onsuccess = function() {
            idb = this.result;
        }
        req.onupgradeneeded = function(evt) {
            var objectStores = {
                persistent_store: this_user_unique_key
            };
            var store;
            $.each(objectStores, function(store_name, unique_id) {
                store = evt.currentTarget.result.createObjectStore(
                    store_name, { autoIncrement: true }
                );
            });
        };
    }

    function init(result) {
        return new Promise((resolve, reject) => {
            let $ul = result.users_list ? result.users_list : [];

            var object = {
                [this_user_unique_key]: {
                    "chats": []
                }
            };

            var store = object_store(STORE_NAME, "readwrite");
            var req;
            try {
                $.each(object, (ie, value) => {
                    req = store.put(value, ie);
                });
                resolve(200);
            } catch (e) {}
        });
    }

    function event_listeners() {
        $(`button[id="idb_init"]`).on("click", async function() {
            $(`div[id="populating-data"]`).removeClass("hidden");
            console_log("Forcing a cold boot to load information")
            await $.post(`${baseUrl}api/users/preference`, { label: "init_idb" }).then((response) => {
                if (response.code == 200) {
                    console_log("Information loaded successfully. Awaiting to populate local storage with the information")
                    init(response.data.result).then((resp) => {
                        if (resp == 200) {
                            var time = 500;
                            $.each($note, function(ii, ie) {
                                setTimeout(function() {
                                    $(`div[id="populating-data"] div[id="populating-notice"]`).html(`Populating ${ie}`);
                                }, time)
                                time += 500;
                            });
                            setTimeout(() => {
                                $(`div[id="populating-data"]`).addClass("hidden");
                                window.location.href = baseUrl;
                            }, time);
                        }
                    });
                }
            });
        });
    }

    open_db();
    event_listeners();
});

function object_store(store_name, mode) {
    var tx = idb.transaction(store_name, mode);
    return tx.objectStore(store_name);
}

function add(object) {
    var store = object_store(STORE_NAME, "readwrite");
    var req;
    try {
        $.each(object, function(ie, value) {
            req = store.put(value, ie);
        });
    } catch (e) {}
}

function read_idb(key) {
    return new Promise((resolve, reject) => {
        var store = object_store(STORE_NAME, "readwrite");
        var results = store.get(key).onsuccess = evt => {
            var itemList = evt.target.result;
            resolve(itemList)
        }
    })
}

async function save_preference($params) {
    let $data = await read_idb(this_user_unique_key);
    let upref = $data.user_information.preferences;
    $.each($params, (i, e) => {
        upref[i] = e;
    });
    $data.user_information.preferences = upref;
    let $_new = {
        [this_user_unique_key]: $data
    }
    add($_new);
}

async function update_idb_items(incoming) {
    console_log("Load the user local storage data");
    $data = await read_idb(this_user_unique_key);
    console_log("Looping through the information returned from the query");
    $.each(incoming, function(ikey, ivalue) {
        console_log(`Loading ${ikey} information from the local storage`);
        try {
            let upref = $data[ikey] ? $data[ikey] : [];
            console_log(`Set the key of the local storage with the new record`);

            let new_rec = {};
            $.each(ivalue, (i, e) => {
                new_rec[i] = e
            });
            console_log(`Replacing the local storage record with the new one`);
            upref = new_rec;
            $data[ikey] = upref;
            let $_new = {
                [this_user_unique_key]: $data
            }
            add($_new);
        } catch (err) {
            console_log(`Unable to update the local storage record set.`);
        }
    });
}

async function load_idb_record(resource, record_id) {
    let $data = await read_idb(this_user_unique_key);
    try {
        if ($data[resource] !== undefined) {
            let record = $data[resource][record_id] ? $data[resource][record_id] : $data[resource];
            return record;
        }
    } catch (err) {
        return [];
    }
}

async function replace_id_record(incoming_data, resource, record_ikey) {
    $data = await read_idb(this_user_unique_key);
    try {
        if ($data[resource] !== undefined) {
            $data[resource][record_ikey] = incoming_data;
            let $_new = {
                [this_user_unique_key]: $data
            }
            add($_new);
        }
    } catch (err) {}
}

async function update_idb_records(incoming, module_item_id) {
    console_log("Load the user local storage data");
    $data = await read_idb(this_user_unique_key);
    console_log("Looping through the information returned from the query");
    $.each(incoming, await
        function(ikey, ivalue) {
            console_log(`Loading ${ikey} information from the local storage`);
            try {
                let upref = $data[ikey] ? $data[ikey] : [];
                console_log(`Set the key of the local storage with the new record`);
                if (upref[module_item_id] === undefined) {
                    console_log(`Property replies not found in the local storage.`);
                    upref[module_item_id] = ivalue[module_item_id];
                    $data[ikey] = upref;
                    let $_new = {
                        [this_user_unique_key]: $data
                    }
                    add($_new);
                    console_log(`Property replies has been appended to the list.`);
                } else {
                    let new_rec = {};
                    console_log(`Updating the existing record information`);
                    $.each(ivalue[module_item_id], function(ii, iv) {
                        upref[module_item_id][ii] = iv;
                    });
                    $data[ikey] = upref;
                    let $_new = {
                        [this_user_unique_key]: $data
                    }
                    add($_new);
                    console_log(`Property replies has been updated successfully.`);
                }
            } catch (err) {
                console_log(`Unable to update the local storage record set.`);
            }
        });
}

async function save_chat(incoming, module_item_id) {
    $data = await read_idb(this_user_unique_key);
    $.each(incoming, await
        function(ikey, ivalue) {
            try {
                let upref = $data[ikey] ? $data[ikey] : [];
                if (upref[module_item_id] === undefined) {
                    upref[module_item_id] = ivalue[module_item_id];
                    $data[ikey] = upref;
                    let $_new = {
                        [this_user_unique_key]: $data
                    }
                    add($_new);
                } else {
                    let new_rec = {};
                    $.each(ivalue[module_item_id], function(ii, iv) {
                        upref[module_item_id][ii] = iv;
                    });
                    $data[ikey] = upref;
                    let $_new = {
                        [this_user_unique_key]: $data
                    }
                    add($_new);
                }
            } catch (err) {}
        });
}

async function update_idb(incoming) {
    console_log("Looping throgh the information returned from the query");
    $.each(incoming, async function(ikey, ivalue) {
        if (ivalue.section == "all") {
            let $sect = ivalue.field;
            console_log(`Loading ${$sect} information from the local storage`)
            let $data = await read_idb(this_user_unique_key);
            try {
                $data[$sect] = ivalue.data;
                let $_new = {
                    [this_user_unique_key]: $data
                }
                console_log(`Updating the record ${$sect} information`);
                add($_new);
            } catch (err) {
                console_log("Error populating local storage: Reason - User has cleared the local storage record.");
            }
        }
    })

}