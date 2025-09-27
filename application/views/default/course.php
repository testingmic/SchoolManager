<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $defaultUser, $SITEURL, $isTutor, $isWardParent;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$userId = $session->userId;
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Subject Details";
$response->title = $pageTitle;

// item id
$item_id = $SITEURL[1] ?? null;

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

    // set the url
    $url_link = $SITEURL[2] ?? null;

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

        $labels = $myClass->defaultClientData->client_preferences ?? null;
        $labels = !empty($labels) ? ($labels->labels ?? []) : null;

        // set the first key
        $data = $data["data"][0];

        // set the page title
        $response->title = $data->name;

        $response->scripts = ["assets/js/index.js"];

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
        $class_ids_list = null;
        $subject_class_list = null;

        // if the class id is not empty
        if(!empty($data->class_list)) {
            // loop throught the classes list
            foreach($data->class_list as $class) {
                $class_ids_list .=  "{$class->item_id},";
                $subject_class_list .= '
                <div class="col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body pt-0 pb-0">
                            <div class="pb-2 pt-3 border-bottom">
                                <p class="clearfix mb-2">
                                    <span class="float-left">Name</span>
                                    <span class="float-right text-muted">
                                        <span class="user_name" '.(!$isWardParent || ($isTutor && in_array($class->item_id, $defaultUser->class_ids)) ? 'onclick="load(\'class/'.$class->item_id.'\');"' : null).'>'.$class->name.'
                                        </span>
                                    </span>
                                </p>
                                <p class="clearfix w-100">
                                    <span class="float-left">Code</span>
                                    <span class="float-right text-muted">'.$class->class_code.'</span>
                                </p>
                                <p class="clearfix w-100">
                                    <span class="float-left">Students Count</span>
                                    <span class="float-right text-muted">'.$class->students_count.'</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }

        // confirm that the link is not empty
        if(!empty($data->resources_list) && isset($data->resources_list["link"])) {
            
            // loop through the list of links
            foreach($data->resources_list["link"] as $key => $link) {
                $key++;

                $link = (object) $link;

                $links_list .= "
                <div id=\"accordion\" data-row_id=\"{$link->item_id}\">
                    <div class=\"accordion\">
                        <div class=\"accordion-header collapsed\" role=\"button\" data-toggle=\"collapse\" data-target=\"#panel-body-{$link->item_id}\">
                            <div class=\"d-flex justify-content-between w-100\">
                                <div><h4>{$key}. {$link->link_name}</h4></div>
                                <div class='text-right'><i class=\"fa fa-calendar-check\"></i> {$link->date_created}</div>
                            </div>
                        </div>
                        <div class=\"accordion-body collapse\" data-row_id=\"{$link->item_id}\" id=\"panel-body-{$link->item_id}\" data-parent=\"#accordion\">
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
        $lessonPlanner = confirm_url_id(2, "lesson") ? true : false;

        // lesson planner display
        $lessons_list = null;
        $attachments_list = "";

        // set the user_id id in the console
        $response->array_stream['url_link'] = "course/{$item_id}/";

        // if the attachment parameter is not empty
        if(!empty($data->attachment)) {
            // create a new forms object
            $formsObj = load_class("forms", "controllers");
            // convert the attachment 
            $attachments = (array) $data->attachment;
            $attachments_list = $formsObj->list_attachments($attachments["files_list"], $session->userId, "col-lg-4 col-md-6", false);
        }

        $uniId = $_GET["unit_id"] ?? 0;

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
            <details class='mb-2' ".(($uniId == $plan->id) ? "open" : null)." data-row_id=\"{$plan->item_id}\">
                <summary class='cursor cursor-pointer bg-primary font-16 text-white p-2 mb-2'>
                    {$key}. {$plan->name}
                    <span class='float-right'><i class=\"fa fa-calendar-check\"></i> ".date("d M Y", strtotime($plan->date_created))."</span>
                </summary>
                <div>
                    <div class='d-flex justify-content-between w-100'>
                        <div>
                            <span class=\"mr-3\"><strong>Start Date: </strong> {$plan->start_date}</span><br>
                            <span><strong>End Date: </strong> {$plan->end_date}</span>
                        </div>
                        ".($hasPlanner ? "
                        <div class='text-right'>
                            <button onclick='return load_quick_form(\"course_unit_form\",\"{$plan->course_id}_{$plan->id}\");' class='btn btn-outline-success btn-sm mb-2' type='button'>
                                <i class='fa fa-edit'></i> Edit
                            </button>
                            <button onclick='return load_quick_form(\"course_lesson_form\",\"{$plan->course_id}_{$plan->id}\");' class='btn btn-outline-primary btn-sm mb-2' type='button'>
                                <i class='fa fa-plus'></i> Add ".(!empty($labels->lesson_label) ? $labels->lesson_label : 'Lesson')."
                            </button>
                            <a href='#' onclick='return delete_record(\"{$plan->item_id}\", \"course_unit\");' class='btn btn-sm btn-outline-danger mb-2'><i class='fa fa-trash'></i> Delete</a>
                        </div>
                        " : null)."
                    </div>
                    <div class='mt-2 mb-3'>{$plan->description}</div>
                    <div class='border-bottom mb-3 bg-light-blue p-2 font-bold'>
                        <h6 class='pb-0 mb-0'>".strtoupper($labels->unit_label ?? 'Unit')." LESSONS</h6>
                    </div>
                    <div class='table-responsive trix-slim-scroll'>
                        <table data-order_item='asc' class='table table-bordered raw_datatable'>
                            <thead>
                                <th>#</th>
                                <th>Lesson Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th class='text-center'>Action</th>
                            </thead>
                            <tbody>{$unit_lessons}</tbody>
                        </table>
                    </div>
                </div>
            </details>";
        }

        // confirm that the unit lessons has been set
        $unit_lessons = isset($unit_lessons) ? $unit_lessons : "";

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-book"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'courses">Subjects</a></div>
                    <div class="breadcrumb-item">'.$data->name.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-3">
                <div class="sticky-top stick_to_top">
                    <div class="card rounded-2xl hover:scale-105 transition-all duration-300">
                        <div class="card-body p-3 text-center bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg text-white card-type-3">
                            <div class="text-uppercase font-25 font-weight-bolder text-white">'.$data->name.'</div>
                            <div class="font-18 font-weight-bold text-uppercase text-white">('.$data->course_code.')</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button onclick="load(\'gradebook/'.$data->item_id.'/grading?class_id='.trim($class_ids_list, ",").'\');" class="btn btn-block btn-outline-success">
                            <i class="fa fa-book-open"></i> GRADEBOOK
                            <span class="badge badge-success float-right">New</span>
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">CLASS NAME</h4>
                        </div>
                        <div class="card-body text-center">
                            '.$data->class_name.'
                        </div>
                    </div>
                ';

            $response->html .= '
                <div class="card d-none d-sm-block">
                    <div class="card-header">
                        <h4 class="mb-0">SUBJECT TUTOR DETAILS</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">';
                    if(!empty($data->course_tutors)) {
                        foreach($data->course_tutors as $tutor) {
                            $response->html .= '
                            <div class="pb-2 pt-3 border-bottom">
                                <p class="clearfix mb-2">
                                    <span class="float-left">Name</span>
                                    <span class="float-right text-muted"><a href="'.$baseUrl.'staff/'.$tutor->item_id.'/documents">'.ucwords(($tutor->name)).'</a></span>
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
                    }
                $response->html .= empty($data->course_tutors) ? "<div class='p-3 text-center'>Subject Tutors Not Set</div>" : null;
                $response->html .= '</div>
                </div>
            </div>';

            $response->html .= '
            </div>
            <div class="col-12 col-md-12 col-lg-9">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link '.(empty($url_link) || $url_link === "description" ? "active" : null).'" onclick="return appendToUrl(\'description\')" id="description-tab2" data-toggle="tab" href="#description" role="tab" aria-selected="true">Description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link '.($url_link === "classes" ? "active" : null).'" onclick="return appendToUrl(\'classes\')" id="classes-tab2" data-toggle="tab" href="#classes" role="tab" aria-selected="true">Classes List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link '.($url_link === "lessons" ? "active" : null).'" onclick="return appendToUrl(\'lessons\')" id="lessons-tab2" data-toggle="tab" href="#lessons" role="tab" aria-selected="true">Lesson Planner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link '.($url_link === "resources" ? "active" : null).'" onclick="return appendToUrl(\'resources\')" id="resources-tab2" data-toggle="tab" href="#resources" role="tab" aria-selected="true">Materials</a>
                    </li>';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link '.($url_link === "update" ? "active" : null).'" id="profile-tab2" onclick="return appendToUrl(\'update\')" data-toggle="tab" href="#settings" role="tab"
                            aria-selected="false">Update Details</a>
                        </li>';
                    }
                    
                    $response->html .= '
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade '.(empty($url_link) || $url_link === "description" ? "show active" : null).'" id="description" role="tabpanel" aria-labelledby="description-tab2">
                            <div class="card-body p-0">
                                <div class="card-header">
                                    <h4 class="mb-0">SUBJECT DESCRIPTION</h4>
                                </div>
                                <div class="p-3 pt-0">
                                '.(!empty($data->description) ? clean_html($data->description) : "<div class='text-center'>Description Not Set</div>").'
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade '.($url_link === "classes" ? "show active" : null).'" id="classes" role="tabpanel" aria-labelledby="classes-tab2">
                            <div class="row">';

                            // if the class list is not empty
                            if(!empty($data->class_list)) {
                                $response->html .= $subject_class_list;
                            } else {
                                $response->html .= '<div class="col-lg-12 font-italic">No class is currently offering this course.</div>';
                            }
                            
                            $response->html .= '        
                            </div>
                        </div>

                        <div class="tab-pane fade '.($url_link === "lessons" ? "show active" : null).'" id="lessons" role="tabpanel" aria-labelledby="lessons-tab2">
                            <div class="d-flex justify-content-between mb-2">
                                <div><h5>LESSON PLANNER</h5></div>
                                <div class="text-right">
                                    '.($lessons_list ? '<a target="_blank" class="btn btn-sm btn-outline-success mb-1" href="'.$baseUrl.'download/coursematerial?cs_mat='.base64_encode($data->id."_".$data->item_id."_".$data->client_id).'&ddw=true"><i class="fa fa-download"></i> Download</a>' : '').'
                                    '.($hasPlanner ? '
                                        <button  onclick="return load_quick_form(\'course_unit_form\',\''.$data->id.'\');" class="btn mb-1 btn-sm btn-outline-primary" type="button">
                                            <i class="fa fa-plus"></i> Create New '.(!empty($labels->unit_label) ? $labels->unit_label : 'Unit').'
                                        </button>'
                                    : null ).'
                                </div>
                            </div>
                            '.(!empty($lessons_list) ? $lessons_list : 
                                no_record_found("No lessons uploaded", "No lessons have been uploaded under this course.", null, "Lessons", false, "fa fa-book", false)
                            ).'
                        </div>
                        
                        <div class="tab-pane fade '.($url_link === "resources" ? "show active" : null).'" id="resources" role="tabpanel" aria-labelledby="resources-tab2">
                            <div class="d-flex justify-content-between">
                                <div><h5>MATERIALS</h5></div>
                                '.($hasPlanner ? 
                                    add_new_item($data->item_id, (!empty($labels->lesson_label) ? $labels->lesson_label : 'Lesson')) 
                                : null ).'
                            </div>
                            <div class="slim-scroll p-0 m-0">
                                '.($attachments_list ? $attachments_list : 
                                    '<div class="text-left font-italic">There are you attachments uploaded under this course</div>'
                                ).'
                            </div>
                            <div class="mt-4"><h5>RESOURCE LINKS</h5></div>
                            <div class="slim-scroll p-0 m-0" id="resource_link_list">
                                '.($links_list ? $links_list : 
                                    '<div class="text-left font-italic">Subject Tutor has not uploaded any resource links to this course.</div>'
                                ).'
                            </div>
                        </div>
                        <div class="tab-pane fade '.($url_link === "update" ? "show active" : null).'" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
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