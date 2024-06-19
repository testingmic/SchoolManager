<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultClientData, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// initial variables
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$response->title = "Application Forms";

// end the page if the user is not an admin
if(!$isAdminAccountant) {
    $response->html = page_not_found("permission_denied");
    echo json_encode($response);
    exit;
}

$response->scripts = [];
$createForm = confirm_url_id(1, "create");
$modifyForm = confirm_url_id(1, "modify");
$application_forms = "";
$applicationForm = "";
$applicationObj = load_class("applications", "controllers");

// set the parameters
$param = (object) [
    "clientId" => $session->clientId,
    "limit" => 200
];

// confirm if create a new form
if(!$createForm && !$modifyForm) {

    $item_list = $applicationObj->forms($param);

    $hasDelete = $accessObject->hasAccess("delete", "applications");
    $hasUpdate = $accessObject->hasAccess("update", "applications");

    foreach($item_list["data"] as $key => $each) {
        
        $action = "<a title='View Application Form record' href='#' onclick='return load(\"application_forms/modify/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></a>";
        
        $application_forms .= "<tr data-row_id=\"{$each->id}\">";
        $application_forms .= "<td>".($key+1)."</td>";
        $application_forms .= "<td><span class='user_name' onclick='return load(\"application_forms/modify/{$each->item_id}\");'>{$each->name}</span>
            <br><strong>FORM ID:</strong> <span class='font-16 text-info'>{$each->item_id}</span></td>";
        $application_forms .= "<td>{$each->year_enrolled}</td>";
        $application_forms .= "<td>{$each->applications_count}</td>";
        $application_forms .= "<td>".$myClass->the_status_label($each->state)."</td>";
        $application_forms .= "<td align='center'>{$action}</td>";
        $application_forms .= "</tr>";
    }

} elseif($createForm || $modifyForm) {

    // create forms object
    $formsObj = load_class("forms", "controllers");
    $recordData = null;

    // form attachment information
    $form_params = (object) [
        "module" => "application_forms",
        "userData" => $defaultUser,
        "accept" => ".pdf,.jpeg,.jpg,.png,.doc,.docx",
        "item_id" => null,
        "no_title" => true
    ];

    $itemFound = false;
    $formData = null;
    $application_id = $SITEURL[2] ?? null;

    // set the parameters
    if($modifyForm) {
        if(confirm_url_id(2)) {
            $param->application_id = $application_id;
            $param->limit = 1;

            // return the result
            $recordData = $applicationObj->forms($param);
            $itemFound = !empty($recordData["data"]) ? true : false;

            // if the complaint was found
            if($itemFound) {
                // create a new forms object
                // set the complaint data
                $recordData = $recordData["data"][0];
                $recordData->attachment = $recordData->attachment;
                $form_params->attachments_list = $recordData->attachment;

                // get the category
                $isEnrolled = (bool) (in_array($recordData->status, ["Pending", "In_Review", "Enrolled"]));

                // get the forms data
                $formContent = $formsObj->form_enlisting($recordData->form);

                $formData = $formContent["form"] ?? null;
                $formToShow = (isset($recordData->form->fields) && !empty($recordData->form->fields)) ? $recordData->form->fields : (object)[];

                $response->array_stream["form_input_fields"] = $formToShow;
                $response->array_stream["thisRowId"] = $formContent["thisRowId"] ?? 1;
            }
        }
    }

    // create a new files object
    $response->scripts = ["assets/js/forms.js", "assets/js/upload.js"];

    /** Set parameters for the data to attach */
    $applicationForm = '
        <form action="'.$baseUrl.'api/applications/'.($itemFound ? "update": "add").'" id="jsform-wrapper">
            <div class="row mb-2">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <div class="input-group-text">Form Title &nbsp;<span class="required">*</span></div>
                                    </div>
                                    <input type="text" value="'.($recordData->name ?? null).'" name="form_title" id="form_title" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="description">Application Description &nbsp;<span class="required">*</span></label>
                                <input id="description" type="hidden" hidden value="'.($recordData->description ?? null).'">
                                <trix-editor name="description" class="tiny-expand-height trix-slim-scroll" id="description" input="description"></trix-editor>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <div class="form-group">
                                <label for="requirements">Application Requirements</label>
                                <input id="requirements" type="hidden" hidden value="'.($recordData->requirements ?? null).'">
                                <trix-editor name="requirements" class="tiny-expand-height trix-slim-scroll" id="requirements" input="requirements"></trix-editor>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-7 col-md-7">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="allow_attachment">Allow upload of attachment</label>
                                                <select name="allow_attachment" id="allow_attachment" class="form-control selectpicker">
                                                    <option '.(!empty($recordData) && $recordData->form->allow_attachment == "yes" ? "selected" : null).' value="yes">Yes! Allow file attachments</option>
                                                    <option '.(!empty($recordData) && $recordData->form->allow_attachment == "no" ? "selected" : null).' value="no">No! Do not allow attachments</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="year_enrolled">Month/Year Published</label>
                                                <input value="'.($recordData->year_enrolled ?? null).'" type="text" class="form-control" id="year_enrolled" name="year_enrolled">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status">Current Status</label>
                                                <select name="status" id="status" class="form-control selectpicker">
                                                    <option '.(!empty($recordData) && $recordData->state == "Pending" ? "selected" : null).'  value="Pending">Pending</option>
                                                    <option '.(!empty($recordData) && $recordData->state == "In_Review" ? "selected" : null).' value="In_Review">In Review</option>
                                                    <option '.(!empty($recordData) && $recordData->state == "Enrolled" ? "selected" : null).' value="Enrolled">Enrolled</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="form_footnote">Form Footnote <small>(This will appear at the bottom of the form)</small></label>
                                        <textarea name="form_footnote" placeholder="Enter form footnote" id="form_footnote" cols="30" rows="5" class="form-control">'.($recordData->form->form_footnote ?? null).'</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-5">
                                    <div class="form-group">
                                        <label for="allow_attachment">Forms Documents <small class="font-italic"> - Attach additional documents (if any) to this form</small></label>';
                                        // load the attachment method
                                        $applicationForm .= $formsObj->form_attachment_placeholder($form_params);

                $applicationForm .= '</div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-12">
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="border-bottom pb-2">
                                <div class="col-lg-12 p-0 m-0 row justify-content-between">
                                    <div><h4>Form Content</h4></div>';
                                    $applicationForm .= form_manager_options();
            $applicationForm .= '</div>
                            </div>
                            '.(empty($formData) ? '<div id="form-pretext" class="text-center font-italic mt-2">The application form is empty. Add new fields to the set.</div>' : null).'
                            '.($itemFound ? "<input readonly type='hidden' name='application_id' value='{$application_id}'>" : null).'
                            <div class="pt-2 pl-0 mt-2" id="jsform-container">'.$formData.'</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="row mt-3">
                        <div class="col-md-6 text-left">
                            <button type=\'button\' style=\'padding:5px\' class=\'btn preview-form hidden btn-sm btn-secondary\'><i class=\'fa fa-eye\'></i> Preview Form</button>
                        </div>
                        <div class="col-md-6 text-right">
                            <button onclick="return save_application_form();" class="btn btn-outline-success" type="submit"><i class="fa fa-save"></i> Save Application Form </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>';
}

$response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.(!empty($recordData) ? 
                    '<i class="fa fa-book-reader"></i> Update:' : ($createForm ? '<i class="fa fa-book-open"></i> Create:' : '<i class="fa fa-book"></i> List:')
                ).' '.$response->title.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                '.($createForm || $modifyForm ? '
                    <div class="breadcrumb-item active">
                        <a href="'.$baseUrl.'application_forms">
                            Application Forms
                        </a>
                    </div>' : null).'
                <div class="breadcrumb-item">Online Applciations</div>
            </div>
        </div>
        <div class="row">
            '.(!$createForm && !$modifyForm ? 
                '<div class="mb-2 col-lg-12 text-right">
                    <button onclick="return load(\'application_forms/create\')" class="btn btn-primary">
                        <i class="fa fa-book-open"></i> Create Form
                    </button>
                </div>' : null
            ).'
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                    '.(!$createForm && !$modifyForm ? 
                        '<div class="table-responsive">
                            <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Form Name</th>
                                        <th>Year Published</th>
                                        <th width="15%">No. of Applications</th>
                                        <th>Status</th>
                                        <th align="center" width="12%"></th>
                                    </tr>
                                </thead>
                                <tbody>'.$application_forms.'</tbody>
                            </table>
                        </div>' : $applicationForm
                    ).'
                    </div>
                </div>
            </div>
        </div>
    </section>';

// add the response
$response->html .= $createForm || $modifyForm ? select_field_modal() : null;

// print out the response
echo json_encode($response);
?>