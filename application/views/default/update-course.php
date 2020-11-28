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
if(!isset($_SERVER["HTTP_REFERER"])) {
    header("location: {$baseUrl}main");
    exit;
}

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "Course Details";
$response->title = "{$pageTitle} : {$appName}";

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

$response->scripts = [
    "assets/js/page/index.js"
];

// item id
$item_id = confirm_url_id(2) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "userId" => $session->userId,
        "course_id" => $item_id,
        "limit" => 1
    ];

    $data = load_class("courses", "controllers")->list($item_param);
    
    // if no record was found
    if(empty($data["data"])) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data["data"][0];

        // guardian information
        $the_form = load_class("forms", "controllers")->course_form($clientId, $baseUrl, $data);
        $hasUpdate = $accessObject->hasAccess("update", "course");
        $hasPlanner = $accessObject->hasAccess("lesson", "course");
        
        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // lesson planner display
        $lessons_list = "<div class='mb-2'>&nbsp;</div>";
        foreach($data->lesson_plan as $key => $plan) {

            $unit_lessons = "";

            // if the lesson is not empty
            if(!empty($plan->lessons_list)) {
                // loop through the list of lessons
                foreach($plan->lessons_list as $ikey => $lesson) {
                    $ikey++;

                    // if the user has the permission
                    if($hasPlanner) {
                        // show the lesson content
                        $action = "<button onclick='return load_quick_form(\"course_lesson_form\",\"{$plan->id}_{$lesson->id}\");' class='btn  btn-sm btn-primary' type='button'><i class='fa fa-edit'></i></button>
                        <a href='#' data-record_id='{$lesson->id}' data-record_type='course_lesson' class='btn btn-sm delete_record btn-outline-danger'><i class='fa fa-trash'></i></a>";
                    }

                    // list the actions
                    $unit_lessons .= "<tr>";
                    $unit_lessons .= "<td>{$ikey}</td>";
                    $unit_lessons .= "<td>{$lesson->name}</td>";
                    $unit_lessons .= "<td>{$lesson->start_date}</td>";
                    $unit_lessons .= "<td>{$lesson->end_date}</td>";
                    if($hasPlanner) {
                        $unit_lessons .= "<td>{$action}</td>";
                    }
                    $unit_lessons .= "</tr>";
                }
            }

            $lessons_list .= "
                <div id=\"accordion\" data-unit_id=\"{$plan->id}\">
                    <div class=\"accordion\">
                    <div class=\"accordion-header ".($key !== 0 ? "collapsed" : null)."\" role=\"button\" data-toggle=\"collapse\" data-target=\"#panel-body-{$key}\" aria-expanded=\"".($key !== 0 ? "false" : "true")."\">
                        <h4>{$plan->name}</h4>
                    </div>
                    <div class=\"accordion-body ".($key !== 0 ? "collapse" : "collapse show")."\" id=\"panel-body-{$key}\" data-parent=\"#accordion\" style=\"\">
                        <div class='d-flex justify-content-between'>
                            <div>
                                <span class=\"mr-3\"><strong>Start Date: </strong> {$plan->start_date}</span>
                                <span><strong>End Date: </strong> {$plan->end_date}</span>
                            </div>
                            ".($hasPlanner ? "
                            <div>
                                <button onclick='return load_quick_form(\"course_unit_form\",\"{$plan->course_id}_{$plan->id}\");' class='btn btn-outline-success btn-sm' type='button'><i class='fa fa-edit'></i> Edit</button>
                                <button onclick='return load_quick_form(\"course_lesson_form\",\"{$plan->id}\");' class='btn btn-outline-primary btn-sm' type='button'><i class='fa fa-plus'></i> Add Lesson</button>
                                <a href='#' data-record_id='{$plan->id}' data-record_type='course_unit' class='btn btn-sm delete_record btn-outline-danger'><i class='fa fa-trash'></i></a>
                            </div>
                            " : null)."
                        </div>
                        <div class='mt-2 mb-3'>{$plan->description}</div>

                        <div class='border-bottom mb-3'><h6>UNIT LESSONS</h6></div>
                        <table class='table table-bordered datatable'>
                            <thead>
                                <th>#</th>
                                <th>Lesson Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                ".($hasPlanner ? "<th>Action</th>" : null)."
                            </thead>
                            <tbody>'.$unit_lessons.'</tbody>
                        </table>
                    </div>
                    </div>
                </div>";
        }

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'list-courses">Course List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
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
                        <h4>Course Tutor Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Fullname</span>
                                <span class="float-right text-muted">'.($data->course_tutor_info->name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Email</span>
                                <span class="float-right text-muted">'.($data->course_tutor_info->email ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Contact</span>
                                <span class="float-right text-muted">'.($data->course_tutor_info->phone_number ?? null).'</span>
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
                        <a class="nav-link '.(!$updateItem ? "active" : null).'" id="lessons-tab2" data-toggle="tab" href="#lessons" role="tab" aria-selected="true">Course Lesson Planner</a>
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
                        <div class="tab-pane fade '.(!$updateItem ? "show active" : null).'" id="lessons" role="tabpanel" aria-labelledby="lessons-tab2">
                            <div class="d-flex justify-content-between">
                                <div><h5>COURSE LESSONS</h5></div>
                                '.($hasPlanner ? '
                                    <div><button  onclick="return load_quick_form(\'course_unit_form\',\''.$item_id.'\');" class="btn  btn-sm btn-primary" type="button"><i class="fa fa-plus"></i> Add Unit</button></div>' 
                                : null ).'
                            </div>
                            '.$lessons_list.'
                        </div>
                        <div class="tab-pane fade" id="resources" role="tabpanel" aria-labelledby="resources-tab2">
                            <div class="col-lg-12 pl-0"><h5>COURSE MATERIALS</h5></div>
                            
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