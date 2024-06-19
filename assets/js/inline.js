var apiURL = `https://app.myschoolgh.com/api/applications/`;
window.onload = function(evt) {
    var mysgh_app_form = document.getElementById('mysgh_app_form');
    if((mysgh_app_form !== undefined) && (mysgh_app_form !== null)) {
        var mysgh_form_id = mysgh_app_form.getAttribute("data-mysgh_app_form-form_id"),
            mysgh_access_token = mysgh_app_form.getAttribute("data-access_token");
        if(((mysgh_form_id !== undefined) && (mysgh_form_id !== null)) && ((mysgh_access_token !== undefined) && (mysgh_access_token !== null))) {
            var xhr = new XMLHttpRequest(),
                data = JSON.stringify({form_id: mysgh_form_id});
            
            xhr.open("POST", `${apiURL}load_form?access_token=${mysgh_access_token}`);
            xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
            xhr.timeout = 10000;
            xhr.responseType = 'json';
            xhr.withCredentials = true;
            xhr.send(data);
            xhr.onload = function() {
                var info = xhr.response;
                if(info.description !== undefined) {
                    mysgh_app_form.innerHTML = info.description;
                } else if(info.data !== undefined) {
                    mysgh_app_form.innerHTML = info.data.result;
                }
            }
            xhr.onerror = function() {}
        }
    }
};

function mysgh_app_form_submit() {
    if(confirm("Are you sure you want to submit the form?")) {
        var mysgh_app_form = document.getElementById('mysgh_app_form'),
            mysgh_result_wrapper = document.getElementById('mysgh_app_form-submit_loader'),
            mysgh_form_content = document.getElementById('mysgh_app_form-form_content'),
            mysgh_form_id = mysgh_app_form.getAttribute("data-mysgh_app_form-form_id"),
            mysgh_access_token = mysgh_app_form.getAttribute("data-access_token");

        mysgh_result_wrapper.innerHTML = "";
        document.getElementById("mysgh_app_form-submit_botton").setAttribute("disabled", true);
        if(((mysgh_form_id !== undefined) && (mysgh_form_id !== null)) && ((mysgh_access_token !== undefined) && (mysgh_access_token !== null))) {
            if(document.forms.mysgh_app_form !== undefined) {
                let formData = new FormData(document.forms.mysgh_app_form),
                    xxhr = new XMLHttpRequest;
                formData.append("mysgh_app_form_id", mysgh_form_id);
                xxhr.open("POST", `${apiURL}apply?access_token=${mysgh_access_token}`);
                xxhr.responseType = 'json';
                xxhr.withCredentials = true;
                xxhr.timeout = 15000;
                xxhr.send(formData);

                xxhr.onload = function() {
                    var info = xxhr.response;
                    if((info.description !== undefined) && (info.code !== 203)) {
                        mysgh_result_wrapper.innerHTML = "<div class='error-container'>"+info.description+"</div>";
                        document.getElementById("mysgh_app_form-submit_botton").removeAttribute("disabled");
                    } else if((info.description !== undefined) && (info.code === 203)) {
                        mysgh_result_wrapper.innerHTML = "<div class='error-container'>"+info.data.result+"</div>";
                        document.getElementById("mysgh_app_form-submit_botton").removeAttribute("disabled");
                    } else {
                        mysgh_result_wrapper.innerHTML = "";
                        document.forms.mysgh_app_form.reset();
                        document.getElementById("requirements_container").remove();
                        mysgh_form_content.innerHTML = "<div class='success-container'>"+info.data.result+"</div>";
                    }
                }
                xxhr.onerror = function() {}
            }
        }
    }
}