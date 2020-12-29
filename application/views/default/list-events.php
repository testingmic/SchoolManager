<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Events Management";
$response->title = "{$pageTitle} : {$appName}";

// if the client information is not empty
if(!empty($session->clientId)) {
    // convert to lowercase
    $client_id = strtolower($session->clientId);
    
    // load user birthdays
    $birth_list = $myClass->pushQuery("name, phone_number, email, image, item_id, unique_id, DAY(date_of_birth) AS the_day, MONTH(date_of_birth) AS the_month", "users", "user_status='Active' AND status='1' AND deleted='0' LIMIT 200");
    $birthday_list = [];
    
    // loop through the users list
    // foreach($birth_list as $user) {
    //     $dob = date("Y")."-".$myClass->append_zeros($user->the_month,2)."-".$myClass->append_zeros($user->the_day,2);
    //     $birthday_list[] = [
    //         "title" => "{$user->name}",
    //         "start" => "{$dob}T06:00:00",
    //         "end" => "{$dob}T18:00:00",
    //         "description" => "
    //             <div class='row'>
    //                 <div class='col-md-10'>
    //                     <div>
    //                         This is the birthday of <strong>{$user->name}</strong>. 
    //                         <a href='javascript:void(0)' class='anchor' onclick='loadPage(\"{$baseUrl}compose?user_id={$user->item_id}&name={$user->name}\")'>Click Here</a> 
    //                         to send a Email or SMS message to the user.
    //                     </div>
    //                     <div class='mt-3'>
    //                         ".(!empty($user->phone_number) ? "<p class='p-0 m-0'><i class='fa fa-phone'></i> {$user->phone_number}</p>" : "")."
    //                         ".(!empty($user->email) ? "<p class='p-0 m-0'><i class='fa fa-envelope'></i> {$user->email}</p>" : "")."
    //                     </div>    
    //                 </div>
    //                 <div class='col-md-2'>
    //                     <img class='rounded-circle cursor author-box-picture' width='60px' src='{$baseUrl}{$user->image}'>
    //                 </div>
    //             </div>"
    //     ];
    // }

    // set the parameters
    $params = (object) [
        "container" => "events_management",
        "birthday_list" => json_encode($birthday_list),
        "baseUrl" => $baseUrl,
        "clientId" => $clientId,
        "event_Sources" => "birthdayEvents",
        "userId" => $session->userId
    ];

    // create new event class
    $eventClass = load_class("events", "controllers");

    // load the event types
    $event_types_list = "";
    $event_types_array = [];
    $event_types = $eventClass->types_list($params);

    // loop through the list
    foreach($event_types as $type) {
        $event_types_array[$type->item_id] = $type;
        $event_types_list .= "
            <div class='card mb-2'>
                <div class='card-header p-2 text-uppercase'>{$type->name}</div>
                ".(!empty($type->description) ? "<div class='card-body p-2'>{$type->description}</div>" : "")."
                <div class='card-footer p-2'>
                    <div class='d-flex justify-content-between'>
                        <div><button onclick='return update_Event_Type(\"{$type->item_id}\")' class='btn btn-sm btn-outline-success'><i class='fa fa-edit'></i> Edit</button></div>
                        <div><a href='#' onclick='return delete_record(\"{$type->item_id}\", \"event_type\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i> Delete</a></div>
                    </div>
                </div>
            </div>";
    }
    // append the questions list to the array to be returned
    $response->array_stream["event_types_array"] = $event_types_array;

    // generate a new script for this client
    // $filename = "assets/js/scripts/{$client_id}_events.js";
    
    // // get the data
    // $data = load_class("scripts", "controllers")->attendance($params);
    
    // // create a new file handler
    // $file = fopen($filename, "w");
    
    // // write the content to the file
    // fwrite($file, $data);
    
    // // close the opened file
    // fclose($file);

    // load the scripts
    $response->scripts = [
        "assets/js/scripts/{$client_id}_events.js",
        "assets/js/events.js",
    ];

    $response->html = '
        <div id="fullCalModal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modalTitle1" class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    </div>
                    <div id="modalBody1" class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Event Page</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="createEventModal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modalTitle2" class="modal-title">Add event</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    </div>
                    <div id="modalBody2" class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="formGroupExampleInput">Example label</label>
                                <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Example input">
                            </div>
                            <div class="form-group">
                                <label for="formGroupExampleInput2">Another label</label>
                                <input type="text" class="form-control" id="formGroupExampleInput2" placeholder="Another input">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="createEventTypeModal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 id="modalTitle2" class="modal-title">Add Event Type</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                    </div>
                    <div id="modalBody2" class="modal-body">
                        <form>
                            <div class="form-group">
                                <label>Event Type Name <span class="required">*</span></label>
                                <input type="text" class="form-control" name="name">
                                <input type="hidden" class="form-control" id="type_id" hidden name="type_id">
                            </div>
                            <div class="form-group">
                                <label for="formGroupExampleInput2">Description</label>
                                <textarea id="description" name="description" class="form-control"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button onclick="return save_Event_Type()" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
        </div>        
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item">Attendance Log</div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-lg-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="fc-overflow">
                                <div id="events_management"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-lg-3">
                    <h5>EVENT TYPES <span class="float-right"><button onclick="return add_Event_Type()" class="btn btn-sm btn-outline-primary"><i class="fa fa-plus"></i> Add New</button></span></h5>
                    <div class="mt-3" id="events_types_list">'.$event_types_list.'</div>
                </div>
            </div>
        </section>';
}

echo json_encode($response);