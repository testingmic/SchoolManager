<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Student Details";
$response->title = "{$pageTitle} : {$appName}";

$response->scripts = ["assets/js/page/index.js"];

// student id
$user_id = $SITEURL[1] ?? null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($user_id)) {

    // set the student parameter
    $student_param = (object) [
        "clientId" => $clientId,
        "user_id" => $user_id,
        "limit" => 1,
        "full_details" => true,
        "no_limit" => 1,
        "user_type" => "student",
        "client_data" => $defaultUser->client
    ];

    $data = load_class("users", "controllers", $student_param)->list($student_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        // load the incidents
        $incidents = load_class("incidents", "controllers")->list($student_param);
        
        // user permissions
        $hasUpdate = $accessObject->hasAccess("update", "guardian");
        $addIncident = $accessObject->hasAccess("add", "incident");
        $updateIncident = $accessObject->hasAccess("update", "incident");
        $deleteIncident = $accessObject->hasAccess("delete", "incident");

        // can recieve
        $canReceive = $accessObject->hasAccess("receive", "fees");
        $viewAllocation = $accessObject->hasAccess("view_allocation", "fees");

        // receive fees payment 
        $receivePayment = !empty($canReceive) ? $canReceive : $isParent;

        // load fees allocation list for class
        $allocation_param = (object) ["clientId" => $clientId, "userData" => $defaultUser, "student_id" => $user_id, "receivePayment" => $receivePayment, "client_data" => $defaultUser->client, "parse_owning" => true];
        
        // load the class timetable
        $timetable = load_class("timetable", "controllers", $allocation_param)->class_timetable($data->class_guid, $clientId);

        $student_fees_payments = "";
        $student_fees_list = [];
        $amount = 0;

        // if the user has permissions to view fees allocation
        if($viewAllocation) {

            // create a new object
            $feesObject = load_class("fees", "controllers", $allocation_param);
                        
            // load fees allocation list for the students
            $fees_category_list = "";
            $student_allocation_list = $feesObject->student_allocation_array($allocation_param);
            $student_fees_list = $feesObject->list($allocation_param)["data"];
            $fees_category_array = $feesObject->category_list($allocation_param)["data"];

            // fees category
            foreach($fees_category_array as $category) {
                $fees_category_list .= "<option value=\"{$category->id}\">{$category->name}</option>";
            }

            // loop through the list of all fees payment
            foreach($student_fees_list as $key => $record) {

                // add up the amount to be paid
                $amount += $record->amount;

                // append to the fees allocation list
                $student_fees_payments .='
                <tr>
                    <td>'.($key+1).'</td>
                    <td>'.$record->category_name.'</td>
                    <td>'.$record->payment_method.'</td>
                    <td>'.(!$record->description ? $record->description : null).'</td>
                    <td>'.$record->recorded_date.'</td>
                    <td align="right">'.$record->amount.'</td>
                    <td><a href="'.$myClass->baseUrl.'receipt/'.$record->item_id.'" target="_blank" title="Click to print Receipt" class="btn btn-sm btn-outline-warning"><i class="fa fa-print"></i></a></td>
                </tr>';
            }
        }

        // populate the incidents
        $incidents_list = "";

        // list the user incidents
        if(!empty($incidents["data"])) {
            
            // begin the html contents
            $incidents_list = "<div class='row mb-3'>";

            // loop through the list of all incidents
            foreach($incidents["data"] as $each) {
                // generate the buttons
                $buttons = "<button onclick=\"return load_quick_form('incident_log_form_view','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-primary\" type=\"button\"><i class=\"fa fa-eye\"></i> View</button>&nbsp;";
                
                // is not active
                $isActive = !in_array($each->status, ["Solved", "Cancelled"]);

                // set the update button
                if($updateIncident && $isActive) {
                    $buttons .= "<button title='Click to update this record' onclick=\"return load_quick_form('incident_log_form','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-success\" type=\"button\"><i class=\"fa fa-edit\"></i> Update</button>";
                }

                if($deleteIncident && $isActive) {
                    $buttons .= "&nbsp;<a href='#' title='Click to delete this record' onclick='return delete_record(\"{$each->item_id}\", \"incident\");' class='btn mb-1 btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a>";
                }
                $buttons .= "&nbsp;<a href='{$baseUrl}download/incident?incident_id={$each->item_id}' target='_blank' title='Click to download this record' class='btn mb-1 btn-sm btn-outline-warning'><i class='fa fa-download'></i> Download</a>";

                //append to the list
                $incidents_list .= "
                <div class=\"col-12 col-md-6 load_incident_record col-lg-6\" data-id=\"{$each->item_id}\">
                    <div class=\"card card-success\">
                        <div class=\"card-header pr-2 pl-2\"><h4>{$each->subject}</h4></div>
                        <div class=\"card-body p-2\" style=\"height:150px;max-height:150px;overflow:hidden;\">{$each->description}</div>
                        <div class=\"pl-2 border-top mt-2\"><strong>Status: </strong> {$myClass->the_status_label($each->status)}</div>
                        <div class=\"pl-2\"><strong>Reported By: </strong> {$each->reported_by}</div>
                        ".(!empty($each->assigned_to_info->name) ? 
                            "<div class=\"pl-2\"><strong>Assigned To: </strong> 
                                {$each->assigned_to_info->name}, {$each->assigned_to_info->phone_number}
                            </div>" : null
                        )."
                        ".(($each->created_by === $session->userId) ? 
                            "<div class=\"pl-2\"><strong>Recorded By: </strong> 
                                {$each->created_by_information->name}, {$each->created_by_information->phone_number}
                            </div>" : null
                        )."
                        <div class=\"pl-2 mb-1 mt-2\">
                            <div class=\"d-flex p-2 justify-content-between\">
                                ".($updateIncident && $isActive ? "
                                    <div>
                                        <button onclick=\"return load_quick_form('incident_log_followup_form','{$each->user_id}_{$each->item_id}');\" class=\"btn mb-1 btn-sm btn-outline-warning\" type=\"button\"><i class=\"fa fa-list\"></i> Followups</button>
                                    </div>" : "<div>&nbsp;</div>"
                                )."
                                <div>{$buttons}</div>
                            </div>
                        </div>
                        <div class=\"card-footer pr-2 pb-2 pl-2 m-0 pt-1 border-top\">
                            <div class=\"d-flex justify-content-between\">
                                <div><i class=\"fa fa-home\"></i> {$each->location}</div>
                                <div><i class=\"fa fa-calendar-check\"></i> {$each->incident_date}</div>
                            </div>
                        </div>
                    </div>
                </div>";
            }

            $incidents_list .= "</div>";
            $response->client_auto_save = ["incidents_array" => $incidents["data"]];
        }

        // if the incident is empty
        if(empty($incidents_list)) {
            $incidents_list = "<div class='text-center font-italic'>No recorded incidents</div>";
        }

        // guardian information
        $user_form = load_class("forms", "controllers")->student_form($clientId, $baseUrl, $data);

        $guardian = "
            <div class='card-body p-2 pl-0' id='ward_guardian_information'>
                <div class='d-flex justify-content-between'>
                    <div><h5>GUARDIAN INFORMATION</h5></div>
                    ".($hasUpdate ? "<div><button onclick='return load_quick_form(\"modify_ward_guardian\",\"{$data->user_id}\");' class='btn btn-outline-primary btn-sm' type='button'><i class='fa fa-user'></i> Add Guardian</button></div>" : "")."
                </div>";

        // if the guardian information is not empty
        if(!empty($data->guardian_list)) {
            // loop through the guardian list
            foreach($data->guardian_list as $each) {
                $guardian .= "<div class='row mb-3 border-bottom pb-3' data-ward_guardian_id='{$each->user_id}'>";
                $guardian .= "<div class='col-lg-3'><strong>Fullname:</strong><br> {$each->fullname}</div>";
                $guardian .= "<div class='col-lg-2'><strong>Relation:</strong><br> {$each->relationship}</div>";
                $guardian .= "<div class='col-lg-3'><strong>Contact:</strong><br> {$each->contact}</div>";
                $guardian .= "<div class='col-lg-4'><strong>Email:</strong><br> {$each->email}</div>";
                $guardian .= !empty($each->address) ? "<div class='col-lg-12'><strong>Address:</strong><br> {$each->address}</div>" : null;

                // if the user has permissions to update the student record
                if($hasUpdate) {
                    $guardian .= "<div class='col-lg-12 mt-2 text-right'>
                        <a href=\"{$baseUrl}update-guardian/{$each->user_id}/view\" class=\"btn btn-sm btn-outline-success\" title=\"View guardian full details\"><i class=\"fa fa-eye\"></i> View</a>
                        <a onclick=\"return modifyWardGuardian('{$each->user_id}_{$data->user_id}','remove');\" href=\"javascript:void(0);\" class=\"btn btn-outline-danger anchor btn-sm\">Remove</a>
                    </div>";
                }
                $guardian .= "</div>";
            }
        } else {
            $guardian .= "<div class='font-italic'>No guardian has been attached to this Student</div>";
        }

        $guardian .= "</div>";

        // push the guardian ids into an array
        $response->array_stream["student_guardian_array_{$user_id}"] = $data->guardian_id;

        // if the request is to view the student information
        $updateItem = false;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-student">Students</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center bg-amber">
                        <div class="font-18 text-dark">REGISTRATION ID</div>
                        <div class="font-22 font-weight-bold text-uppercase text-dark">'.$data->unique_id.'</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center bg-info">
                        <div class="font-18 text-dark">CLASS</div>
                        <div class="font-22 font-weight-bold text-uppercase text-dark">'.$data->class_name.'</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center bg-pink">
                        <div class="font-18 text-dark">SECTION</div>
                        <div class="font-22 font-weight-bold text-uppercase text-dark">
                            '.($data->department_name ? $data->department_name : '-' ).'
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center bg-success">
                        <div class="font-18 text-dark">DEPARTMENT</div>
                        <div class="font-22 font-weight-bold text-uppercase text-dark">
                            '.($data->section_name ? $data->section_name : '-' ).'
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center m-0 p-0">
                        <img alt="image" src="'.$baseUrl.''.$data->image.'" class="profile-picture">
                    </div>
                    <div class="text-center">
                        <div class="author-box-description">'.$data->description.'</div>
                        <div class="w-100 mt-3">
                            <a class="btn btn-primary" href="'.$baseUrl.'modify-student/'.$user_id.'"><i class="fa fa-edit"></i> Edit Student</a>
                        </div>
                    </div>
                </div>
                </div>
                <div class="card">
                <div class="card-header">
                    <h4>Personal Details</h4>
                </div>
                <div class="card-body pt-0 pb-0">
                    <div class="py-4">
                        <p class="clearfix">
                            <span class="float-left">Enrollment Date</span>
                            <span class="float-right text-muted">'.$data->enrollment_date.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Gender</span>
                            <span class="float-right text-muted">'.$data->gender.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Section</span>
                            <span class="float-right text-muted">'.$data->section_name.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Birthday</span>
                            <span class="float-right text-muted">'.$data->date_of_birth.'</span>
                        </p>
                        '.($data->phone_number ?
                            '<p class="clearfix">
                                <span class="float-left">Phone</span>
                                <span class="float-right text-muted">'.$data->phone_number.'</span>
                            </p>' : ''
                        ).'
                        '.($data->email ?
                            '<p class="clearfix">
                                <span class="float-left">E-Mail</span>
                                <span class="float-right text-muted">'.$data->email.'</span>
                            </p>' : ''
                        ).'
                        <p class="clearfix">
                            <span class="float-left">Blood Group</span>
                            <span class="float-right text-muted">'.$data->blood_group_name.'</span>
                        </p>
                        <p class="clearfix">
                            <span class="float-left">Residence</span>
                            <span class="float-right text-muted">'.$data->residence.'</span>
                        </p>
                        '.($data->religion ? 
                            '<p class="clearfix">
                                <span class="float-left">Religion</span>
                                <span class="float-right text-muted">'.$data->religion.'</span>
                            </p>' : ''
                        ).'
                        <p class="clearfix">
                            <span class="float-left">Country</span>
                            <span class="float-right text-muted">'.$data->country_name.'</span>
                        </p>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(!$updateItem ? "active" : null).'" id="home-tab2" data-toggle="tab" href="#about" role="tab" aria-selected="true">Other Information</a>
                    </li>
                    '.($viewAllocation ? 
                    '<li class="nav-item">
                        <a class="nav-link" id="fees-tab2" data-toggle="tab" href="#fees" role="tab" aria-selected="true">Fees Allocation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="fees_payments-tab2" data-toggle="tab" href="#fees_payments" role="tab" aria-selected="true">Fees Payment</a>
                    </li>' : '').'
                    <li class="nav-item">
                        <a class="nav-link" id="calendar-tab2" data-toggle="tab" href="#calendar" role="tab" aria-selected="true">Timetable</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="incident-tab2" data-toggle="tab" href="#incident" role="tab" aria-selected="true">Incidents</a>
                    </li>';

                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="about" role="tabpanel" aria-labelledby="home-tab2">
                            '.($data->description ? "
                                <div class='mb-3'>
                                    <div class='card-body p-2 pl-0'>
                                        <div><h5>DESCRIPTION</h5></div>
                                        {$data->description}
                                    </div>
                                </div>
                            " : "").'
                            <div class="mb-3">
                                <div class="card-body p-2 pl-0">
                                    <div><h5>PREVIOUS SCHOOL DETAILS</h5></div>
                                    <table width="100%" class="table-bordered">
                                        <tr>
                                            <td class="p-2" width="20%">School Name</td>
                                            <td class="p-2">'.$data->previous_school.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Qualification</td>
                                            <td class="p-2">'.$data->previous_school_qualification.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Remarks</td>
                                            <td class="p-2">'.$data->previous_school_remarks.'</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            '.$guardian.'
                        </div>
                        '.($viewAllocation ? 
                        '<div class="tab-pane fade" id="fees" role="tabpanel" aria-labelledby="fees-tab2">
                            <div class="d-flex mb-3 pb-2 border-bottom justify-content-between">
                                <div><h5>FEES ALLOCATION</h5></div>
                                <div>
                                '.(
                                    !empty($student_allocation_list["owning"]) ? 
                                        '<div class="text-right">
                                            <a '.($isParent ? "target='_blank' href='{$myClass->baseUrl}pay/{$defaultUser->client_id}/fees/{$user_id}'" : 'href="'.$myClass->baseUrl.'fees-payment?student_id='.$user_id.'&class_id='.$data->class_id.'"').' class="btn btn-outline-primary"><i class="fa fa-adjust"></i> Make Fees Payment</a>
                                        </div>'
                                    : '<div class="badge badge-success">Fully Paid</div>'
                                ).'
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table data-empty="" class="table table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th>Student Name</th>
                                            <th>Category</th>
                                            <th>Due</th>
                                            <th>Paid</th>
                                            '.($receivePayment ? '<th width="10%" align="center"></th>' : '').'
                                        </tr>
                                    </thead>
                                    <tbody>'.$student_allocation_list["list"].'</tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="fees_payments" role="tabpanel" aria-labelledby="fees_payments-tab2">
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label>Filter by Category</label>
                                    <select data-width="100%" id="category_id" class="selectpicker form-control">
                                        <option value="">Select Category</option>
                                        '.$fees_category_list.'
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label>Start Date</label>                                
                                    <input value="'.date("Y-m-d", strtotime("first day of this month")).'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_start_date" id="group_start_date">
                                </div>
                                <div class="col-lg-3">
                                    <label>End Date</label>
                                    <input value="'.date("Y-m-d", strtotime("last day of this month")).'" type="text" class="datepicker form-control" style="border-radius:0px; height:42px;" name="group_end_date" id="group_end_date">
                                </div>
                                <div class="col-lg-2">
                                    <label>&nbsp;<br></label>
                                    <button type="button" onclick="return generate_payment_report(\''.$user_id.'\');" class="btn btn-block btn-primary">Generate</button>
                                </div>

                                <div class="border-top pt-3 col-lg-12 mt-3">
                                    <div class="table-responsive">
                                        <table width="100%" class="table table-striped table-bordered raw_datatable">
                                            <thead>
                                                <tr>
                                                    <th data-width="40" style="width: 40px;">#</th>
                                                    <th>Item</th>
                                                    <th>Payment Method</th>
                                                    <th>Description</th>
                                                    <th>Record Date</th>
                                                    <th align="right">Amount</th>
                                                    <th align="center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>'.$student_fees_payments.'</tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>':'').'
                        <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab2">
                            <div class="table-responsive trix-slim-scroll">
                                '.$timetable.'
                            </div>
                        </div>
                        <div class="tab-pane fade" id="incident" role="tabpanel" aria-labelledby="incident-tab2">
                            <div class="d-flex justify-content-between">
                                <div class="mb-2"><h5>INCIDENTS LOG</h5></div>
                                '.($addIncident ? '
                                    <div>
                                        <button type="button" onclick="return load_quick_form(\'incident_log_form\',\''.$user_id.'\');" class="btn btn-primary"><i class="fa fa-plus"></i> Log Incident</button>
                                    </div>' 
                                : null ).'
                            </div>
                            '.$incidents_list.'
                        </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </section>';
        
    }

}
// print out the response
echo json_encode($response);
?>