<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $SITEURL, $isTutor;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$userId = $session->userId;
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Course Details";
$response->title = "{$pageTitle} : {$appName}";

// item id
$item_id = $SITEURL[1] ?? null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($item_id)) {

    // bypass the request
    $item_param = (object) [
        "clientId" => $clientId,
        "userId" => $userId,
        "course_id" => $item_id,
        "userData" => $defaultUser,
        "client_data" => $defaultUser->client,
        "full_attachments" => true,
        "full_details" => true,
        "limit" => 1
    ];

    // if user is a tutor
    if($isTutor) {
        $item_param->course_tutor = $userId;
    }

    // bypass check if the user is a student or parent
    if(!empty($session->student_id)) {
        $item_param->bypass = true;
    }

    $data = load_class("courses", "controllers", $item_param)->list($item_param);

    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        $response->scripts = ["assets/js/page/index.js"];

        // append is admin to the query string
        $isAdmin = (bool) ($defaultUser->user_type == "admin");
        $data->isAdmin = $isAdmin;

        // guardian information
        $the_form = load_class("forms", "controllers")->course_form($clientId, $baseUrl, $data);
        $hasUpdate = $accessObject->hasAccess("update", "course");
        $hasPlanner = $accessObject->hasAccess("lesson", "course");
        
        // parse the resources list 
        $response->client_auto_save = ["resources_list" => $data->resources_list];

        //links list
        $links_list = "";

        // confirm that the link is not empty
        if(!empty($data->resources_list) && isset($data->resources_list["link"])) {
            
            // loop through the list of links
            foreach($data->resources_list["link"] as $key => $link) {
                $key++;

                $link = (object) $link;

                $links_list .= "
                <div id=\"accordion\" data-row_id=\"{$link->item_id}\">
                    <div class=\"accordion\">
                        <div class=\"accordion-header collapsed\" role=\"button\" data-toggle=\"collapse\" data-target=\"#panel-body-{$key}\">
                            <div class=\"d-flex justify-content-between\">
                                <div><h4>{$key}. {$link->link_name}</h4></div>
                                <div><i class=\"fa fa-calendar-check\"></i> {$link->date_created}</div>
                            </div>
                        </div>
                        <div class=\"accordion-body collapse\" data-row_id=\"{$link->item_id}\" id=\"panel-body-{$key}\" data-parent=\"#accordion\">
                            <div class='d-flex justify-content-between'>
                                <div>
                                    <strong>{$link->link_url}</strong> <br>
                                    <a href='{$link->link_url}' class='anchor' target='_blank'>Visit Link</a>
                                </div>
                                ".($hasPlanner ? "
                                <div>
                                    <button onclick='return load_quick_form(\"course_link_upload\",\"{$link->course_id}_{$link->item_id}\");' class='btn btn-outline-success btn-sm' type='button'><i class='fa fa-edit'></i> Edit</button>
                                    <a href='#' onclick='return delete_record(\"{$link->item_id}\", \"resource_link\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>
                                </div>
                                " : null)."
                            </div>
                            <div class='mt-2 mb-3'>{$link->description}</div>
                        </div>
                    </div>
                </div>";
        
            }
        }

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // lesson planner display
        $lessons_list = "<div class='mb-2'>&nbsp;</div>";
        $attachments_list = "";

        // if the attachment parameter is not empty
        if(!empty($data->attachment)) {
            // create a new forms object
            $formsObj = load_class("forms", "controllers");
            // convert the attachment 
            $attachments = (array) $data->attachment;
            $attachments_list = $formsObj->list_attachments($attachments["files_list"], $session->userId, "col-lg-4 col-md-6", false);
        }

        // loop through the lesson plan
        foreach($data->lesson_plan as $key => $plan) {

            $key++;
            $unit_lessons = "";

            // if the lesson is not empty
            if(!empty($plan->lessons_list)) {

                // loop through the list of lessons
                foreach($plan->lessons_list as $ikey => $lesson) {
                    $ikey++;

                    // view button
                    $action = "<button onclick='return load_quick_form(\"course_lesson_form_view\",\"{$plan->course_id}_{$plan->id}_{$lesson->id}\");' class='btn  btn-sm btn-outline-primary' type='button'><i class='fa fa-eye'></i></button>";

                    // if the user has the permission
                    if($hasPlanner) {
                        // show the lesson content
                        $action .= "&nbsp;<button onclick='return load_quick_form(\"course_lesson_form\",\"{$plan->course_id}_{$plan->id}_{$lesson->id}\");' class='btn  btn-sm btn-outline-success' type='button'><i class='fa fa-edit'></i></button>";
                        $action .= "&nbsp;<a href='#' onclick='return delete_record(\"{$lesson->item_id}\", \"course_lesson\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>";
                    }

                    // list the actions
                    $unit_lessons .= "<tr data-row_id=\"{$lesson->item_id}\">";
                    $unit_lessons .= "<td>{$ikey}</td>";
                    $unit_lessons .= "<td><a title='Click to view lesson details' href='#' onclick='return load_quick_form(\"course_lesson_form_view\",\"{$plan->course_id}_{$plan->id}_{$lesson->id}\");'>{$lesson->name}</a></td>";
                    $unit_lessons .= "<td>{$lesson->start_date}</td>";
                    $unit_lessons .= "<td>{$lesson->end_date}</td>";
                    $unit_lessons .= "<td class='text-center'>{$action}</td>";
                    $unit_lessons .= "</tr>";
                }
            }

            $lessons_list .= "
                <div id=\"accordion\" data-row_id=\"{$plan->item_id}\">
                    <div class=\"accordion\">
                    <div class=\"accordion-header ".($plan->id == $session->thisLast_UnitId ? null : "collapsed")."\" role=\"button\" data-toggle=\"collapse\" data-target=\"#panel-body-{$key}\" ".($plan->id == $session->thisLast_UnitId ? "aria-expanded=\"true\"" : null)."\">
                        <div class=\"d-flex justify-content-between\">
                            <div><h4>{$key}. {$plan->name}</h4></div>
                            <div><i class=\"fa fa-calendar-check\"></i> {$plan->date_created}</div>
                        </div>
                    </div>
                    <div class=\"accordion-body ".($plan->id == $session->thisLast_UnitId ? "collapse show" : "collapse")."\" id=\"panel-body-{$key}\" data-parent=\"#accordion\">
                        <div class='d-flex justify-content-between'>
                            <div>
                                <span class=\"mr-3\"><strong>Start Date: </strong> {$plan->start_date}</span>
                                <span><strong>End Date: </strong> {$plan->end_date}</span>
                            </div>
                            ".($hasPlanner ? "
                            <div>
                                <button onclick='return load_quick_form(\"course_unit_form\",\"{$plan->course_id}_{$plan->id}\");' class='btn btn-outline-success btn-sm' type='button'><i class='fa fa-edit'></i> Edit</button>
                                <button onclick='return load_quick_form(\"course_lesson_form\",\"{$plan->course_id}_{$plan->id}\");' class='btn btn-outline-primary btn-sm' type='button'><i class='fa fa-plus'></i> New Lesson</button>
                                <a href='#' onclick='return delete_record(\"{$plan->item_id}\", \"course_unit\");' class='btn btn-sm btn-outline-danger'><i class='fa fa-trash'></i></a>
                            </div>
                            " : null)."
                        </div>
                        <div class='mt-2 mb-3'>{$plan->description}</div>

                        <div class='border-bottom mb-3'><h6>UNIT LESSONS</h6></div>
                        <div class='table-responsive trix-slim-scroll'>
                            <table class='table table-bordered datatable'>
                                <thead>
                                    <th>#</th>
                                    <th>Lesson Title</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th align='center'>Action</th>
                                </thead>
                                <tbody>{$unit_lessons}</tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>";
        }

        // confirm that the unit lessons has been set
        $unit_lessons = isset($unit_lessons) ? $unit_lessons : "";

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-courses">Course List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-3">
                <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center">
                        <div class="clearfix"></div>
                        <div class="author-box-name"><a href="#">'.$data->name.'</a></div>
                        <div class="author-box-job">'.$data->course_code.'</div>
                        <div class="author-box-job">('.$data->credit_hours.' Hours)</div>
                    </div>
                </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Description</h4>
                    </div>
                    <div class="card-body pt-0">
                        <div class="py-3 pt-0">
                            '.$data->description.'
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Course Tutor(s) Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">';
                    foreach($data->course_tutors as $tutor) {
                        $response->html .= '
                        <div class="pb-2 pt-3 border-bottom">
                            <p class="clearfix mb-2">
                                <span class="float-left">Fullname</span>
                                <span class="float-right text-muted"><a href="'.$baseUrl.'update-staff/'.$tutor->item_id.'/view">'.$tutor->name.'</a></span>
                            </p>
                            <p class="clearfix mb-2">
                                <span class="float-left">Email</span>
                                <span class="float-right text-muted">'.$tutor->email.'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Contact</span>
                                <span class="float-right text-muted">'.$tutor->phone_number.'</span>
                            </p>
                        </div>';
                    }
                    $response->html .= '</div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-9">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(!$updateItem ? "active" : null).'" id="classes-tab2" data-toggle="tab" href="#classes" role="tab" aria-selected="true">Classes List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="lessons-tab2" data-toggle="tab" href="#lessons" role="tab" aria-selected="true">Course Lesson Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="resources-tab2" data-toggle="tab" href="#resources" role="tab" aria-selected="true">Course Materials</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($updateItem ? "active" : null).'" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="classes" role="tabpanel" aria-labelledby="classes-tab2">
                            <div class="row">';

                            // if the class list is not empty
                            if(!empty($data->class_list)) {
                                
                                // loop throught the classes list
                                foreach($data->class_list as $class) {
                                    $response->html .= '
                                    <div class="col-lg-6 col-md-6">
                                        <div class="card">
                                            <div class="card-body pt-0 pb-0">
                                                <div class="pb-2 pt-3 border-bottom">
                                                    <p class="clearfix mb-2">
                                                        <span class="float-left">Name</span>
                                                        <span class="float-right text-muted"><a href="'.$baseUrl.'update-class/'.$class->id.'/view">'.$class->name.'</a></span>
                                                    </p>
                                                    <p class="clearfix">
                                                        <span class="float-left">Code</span>
                                                        <span class="float-right text-muted">'.$class->class_code.'</span>
                                                    </p>
                                                    <p class="clearfix">
                                                        <span class="float-left">Class Size</span>
                                                        <span class="float-right text-muted">'.$class->class_size.'</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }
                            } else {
                                $response->html .= '<div class="col-lg-12 font-italic">No class is currently offering this course.</div>';
                            }
                            
                            $response->html .= '        
                            </div>
                        </div>

                        <div class="tab-pane fade" id="lessons" role="tabpanel" aria-labelledby="lessons-tab2">
                            <div class="d-flex justify-content-between">
                                <div><h5>COURSE LESSONS</h5></div>
                                <div>
                                    '.($unit_lessons ? '<a target="_blank" class="btn btn-sm btn-outline-success" href="'.$baseUrl.'download?cs_mat='.base64_encode($data->id."_".$data->item_id."_".$data->client_id).'&dw=true"><i class="fa fa-download"></i> Download</a>' : '').'
                                    '.($hasPlanner ? '
                                        <button  onclick="return load_quick_form(\'course_unit_form\',\''.$data->id.'\');" class="btn btn-sm btn-outline-primary" type="button"><i class="fa fa-plus"></i> New Unit</button>'
                                    : null ).'
                                </div>
                            </div>
                            '.(!empty($lessons_list) ? $lessons_list : 
                                '<div class="text-left font-italic">No lessons have been uploaded under this course.</div>'
                            ).'
                        </div>
                        
                        <div class="tab-pane fade" id="resources" role="tabpanel" aria-labelledby="resources-tab2">
                            <div class="d-flex justify-content-between">
                                <div><h5>COURSE MATERIALS</h5></div>
                                '.($hasPlanner ? 
                                    add_new_item($data->item_id) 
                                : null ).'
                            </div>
                            <div class="slim-scroll p-0 m-0" style="max-height:400px; overflow-y:auto;">
                                '.($attachments_list ? $attachments_list : 
                                    '<div class="text-left font-italic">There are you attachments uploaded under this course</div>'
                                ).'
                            </div>
                            <div class="mt-4"><h5>RESOURCE LINKS</h5></div>
                            <div class="slim-scroll p-0 m-0" id="resource_link_list" style="max-height:400px; overflow-y:auto;">
                                '.($links_list ? $links_list : 
                                    '<div class="text-left font-italic">Course Tutor has not uploaded any resource links to this course.</div>'
                                ).'
                            </div>
                        </div>
                        <div class="tab-pane fade '.($updateItem ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
                        if($hasUpdate) {
                            $response->html .= $the_form;
                        }

                        $response->html .= '
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