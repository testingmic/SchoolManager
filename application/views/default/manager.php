<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser, $accessObject;

// initial variables
$appName = config_item("site_name");
$baseUrl = $config->base_url();

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) [];
$pageTitle = "App Manager";
$response->title = "{$pageTitle} : {$appName}";

// staff id
$user_id = $session->userId;
$response->scripts = ["assets/js/academics.js"];

// confirm the user permissions
if(empty($accessObject->hasAccess("close", "settings"))) {
    $response->html = page_not_found("permission_denied");
} else {
    // import list
    $import_list = [
        "students" => "Students",
        "courses" => "Courses / Subjects",
        "courses_plan" => "Course / Lesson Plan",
        "courses_resource" => "Course Resources",
        "fees_allocation" => "Fees Allocation"
    ];
    
    // import div
    $import_div = "";
    $count = 0;
    foreach($import_list as $key => $value) {
        $count++;
        $import_div .= "
            <div class=\"col-lg-4\">
                <div class=\"form-group\">
                    <label style=\"font-size:13px\" class=\"cursor\" for=\"{$key}_{$count}\">{$value}</label>
                    <input ".($key === "students" ? "checked disabled" : null)." id=\"{$key}_{$count}\" style=\"height:20px;width:20px;\" class=\"cursor data_to_import\" name=\"data_to_import[]\" value=\"{$key}\" type=\"checkbox\">
                </div>
            </div>";
    }
    // append the html content
    $response->html = '
    <section class="section">
        <div class="section-header">
            <h1>'.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                <div class="breadcrumb-item">'.$pageTitle.'</div>
            </div>
        </div>
        <div class="section-body">
        
            <div class="row">
                <div class="col-12 col-md-7">
                    <div class="card">
                        <div class="card-body font-16">
                            <div class="mb-2 row">
                                <div class="col-lg-6"><span class="font-weight-bold text-uppercase">Academic Year</span></div>
                                <div class="col-lg-6"><span>'.$defaultUser->appPrefs->academics->academic_year.'</span></div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-6"><span class="font-weight-bold text-uppercase">Academic Term</span></div>
                                <div class="col-lg-6"><span>'.$defaultUser->appPrefs->academics->academic_term.'</span></div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-6"><span class="font-weight-bold text-uppercase">This Term Began On</span></div>
                                <div class="col-lg-6"><span>'.date("jS F Y", strtotime($defaultUser->appPrefs->academics->term_starts)).'</span></div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-6"><span class="font-weight-bold text-uppercase">This Term '.($defaultUser->appPrefs->termEnded ? "Ended On" : "Ends On").'</span></div>
                                <div class="col-lg-6">
                                <span>
                                    '.date("jS F Y", strtotime($defaultUser->appPrefs->academics->term_ends)).'
                                    '.($defaultUser->appPrefs->termEnded ? "<span class='badge badge-danger'>Already Ended</span>" : "<span class='badge badge-success'>Active</span>").'
                                </span>
                                </div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-6"><span class="font-weight-bold text-uppercase">Next Term Begins</span></div>
                                <div class="col-lg-6"><span>'.date("jS F Y", strtotime($defaultUser->appPrefs->academics->next_term_starts)).'</span></div>
                            </div>
                            <div class="mb-2 row">
                                <div class="col-lg-6"><span class="font-weight-bold text-uppercase">Next Term Ends On</span></div>
                                <div class="col-lg-6"><span>'.date("jS F Y", strtotime($defaultUser->appPrefs->academics->next_term_ends)).'</span></div>
                            </div>
                            <div class="mb-2 mt-3 border-top pt-3"></div>
                            <div class="mb-2 row">
                                <div class="col-lg-12 text-primary mb-3"><strong>SELECT DATA TO IMPORT</strong></div>
                                '.$import_div.'
                            </div>
                            <div class="mb-2 mt-3 border-top pt-3"></div>
                            <div class="d-flex  justify-content-between mb-2">
                                <div>
                                    <a href="'.$baseUrl.'settings" class="btn btn-outline-primary"><i class="fa fa-edit"></i> Update</a>
                                </div>
                                <div>
                                    <button onclick="return end_Academic_Term(\'begin\');" class="btn btn-outline-danger"><i class="fa fa-american-sign-language-interpreting"></i> End Academic Term</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-5" id="academic_Term_Processing"></div>
            </div>

        </div>
    </section>';
}


// print out the response
echo json_encode($response);