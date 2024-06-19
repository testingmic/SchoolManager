<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $isTutor, $isParent, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Daily Student Reports";
$response->title = $pageTitle;

// get the data parsed
$filter = (object) array_map("xss_clean", $_POST);

// get the report id
$report_id = $SITEURL[1] ?? null;

// load the student reports list
$param = (object) [
    "userData" => $defaultUser,
    "clientId" => $clientId,
    "class_id" => $filter->class_id ?? null,
    "student_id" => $filter->student_id ?? null,
    "userId" => $defaultUser->user_id
];

// append the report id if not empty
if(!empty($report_id)) {
    $param->report_id = $report_id;
}

// get the reports list
$daily_reports = "";
$incidentObj = load_class("incidents", "controllers");
$reports = $incidentObj->report_list($param)["data"];

// end the query is the report_id is not empty and the reports is empty
if(!empty($report_id) && empty($reports)) {
    // permission denied log
    $response->html = page_not_found("permission_denied");
    // print out the response
    echo json_encode($response);
    exit;
}

// loop through the reports list
foreach($reports as $key => $each) {
    
    // set the url link
    $url_link = "students_daily_reports/{$each->item_id}";
    
    // generate the action buttons
    $action = "<button title='{$each->comments_count} comments' onclick='return load(\"{$url_link}\");' class=\"btn mb-1 btn-sm btn-outline-primary\"><i class=\"fa fa-comments\"></i> {$each->comments_count}</button>&nbsp;";

    // allow delete if not seen by the parent
    if($each->is_deletable && $isTutorAdmin) {
        $action .= "<button onclick='return delete_record(\"{$each->item_id}\", \"daily_report\")'; class=\"btn mb-1 btn-sm btn-outline-danger\"><i class=\"fa fa-trash\"></i></button>";
    }
    
    // append to the incidents list
    $daily_reports .= "<tr data-row_id=\"{$each->item_id}\">";
    $daily_reports .= "<td>".($key+1)."</td>";
    $daily_reports .= "
    <td>
        <div class='d-flex justify-content-start'>
            <div>
                <span class='user_name' onclick='return load(\"{$url_link}\");'>
                    {$each->student_name}
                </span><br>
                <div><strong>{$each->student_unique_id}</strong></div>
                <div>{$each->class_name}</div>
            </div>
        </div>
    </td>";
    $daily_reports .= "<td>".date("Y-m-d", strtotime($each->date_created))."</td>";
    $daily_reports .= "<td><div style='max-height:80px; overflow:hidden;'>{$each->description}</div></td>";
    $daily_reports .= "<td align='center'>{$action}</td>";
    $daily_reports .= "</tr>";
}

// if the class_id is not empty
$classes_param = (object) [
    "clientId" => $clientId,
    "columns" => "id, name, item_id",
    "limit" => 100,
    "client_data" => $defaultUser->client
];

// init students list
$students_list = [];

// load the classes list
$class_list = load_class("classes", "controllers")->list($classes_param)["data"];

// append it in the response array stream
$response->array_stream["reports"] = $reports;

// load the students id if the class id was parsed
if(!empty($filter->class_id)) {
    $students_list = $myClass->pushQuery(
        "a.item_id, a.name", 
        "users a LEFT JOIN classes b ON b.id = a.class_id", 
        "a.user_type='student' AND a.client_id='{$clientId}' AND a.user_status='Active'
        AND b.item_id = '{$filter->class_id}' LIMIT 300"
    );
}

// scripts to load
$response->scripts = ["assets/js/filters.js"];

// display the page content
$response->html = '
    <section class="section">
        <div class="section-header">
            <h1><i class="fa fa-book"></i> '.$pageTitle.'</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                '.(!empty($report_id) ? 
                    "<div class='breadcrumb-item'>
                        <a href='{$baseUrl}students_daily_reports'>{$pageTitle}</a>
                    </div>
                    <div class='breadcrumb-item'>{$reports[0]->student_name}</div>" : 
                    "<div class='breadcrumb-item'>{$pageTitle}</div>"
                ).'
            </div>
        </div>
        <div class="row" id="filter_Department_Class">';

            // show this section if the user is an admin or a teacher
            if($isTutorAdmin && empty($report_id)) {
                // append the content
                $response->html .='<div class="col-12 col-sm-12 col-md-4">
                    <div class="card">
                        <div class="card-header p-2">
                            <div class="w-100 d-flex justify-content-between">
                                <div>Log Form</div>
                                <div>
                                    <button onclick="return load_quick_form(\'daily_report_log_form\');" class="btn btn-outline-primary">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-2" id="report_container">

                            <div class="form-group mb-2">
                                <label>Select Class</label>
                                <select data-width="100%" class="form-control selectpicker" name="class_id">
                                    <option value="">Please Select Class</option>';
                                    foreach($class_list as $each) {
                                        $response->html .= "<option ".(!empty($filter->class_id) && ($filter->class_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";
                                    }
                                    $response->html .= '
                                </select>
                            </div>

                            <div class="form-group mb-2">
                                <label>Select Student <span class="required">*</span></label>
                                <select data-width="100%" class="form-control selectpicker" name="student_id">
                                    <option value="">Please Select Student</option>';
                                    foreach($students_list as $each) {
                                        $response->html .= "<option ".(!empty($filter->student_id) && ($filter->student_id == $each->item_id) ? "selected" : "")." value=\"{$each->item_id}\">".strtoupper($each->name)."</option>";
                                    }
                                    $response->html .= '
                                </select>
                            </div>

                            <div class="form-group mb-2" align="right">
                                <button id="filter_Daily_Reports_List" class="btn btn-outline-success">
                                    <i class="fa fa-filter"></i> Load Record
                                </button>
                            </div>

                        </div>
                    </div>
                </div>';
            }

            // show this content if the report_id is empty
            if(empty($report_id)) {
                // content to display
                $response->html .='
                <div class="'.($isTutorAdmin ? "col-12 col-md-8" : "col-12 col-md-12").'">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table data-empty="" class="table table-bordered table-sm table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="25%">Student Name</th>
                                            <th width="15%">Log Date</th>
                                            <th>Description</th>
                                            <th align="center" width="15%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>'.$daily_reports.'</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>';
            } else {

                // uploads script
                $response->scripts = ["assets/js/comments.js"];

                // set the seen status to 1
                if($isParent && !$reports[0]->is_seen) {
                    $incidentObj->report_is_seen($param);
                }

                // set the data
                $data = $reports[0];

                // append the content
                $response->html .= '
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="font-17 text-uppercase mb-2">
                                <i class="fa fa-user-graduate"></i> 
                                <span onclick="return load(\'student/'.$data->student_id.'\')" class="user_name">'.$data->student_name.'</span>
                            </div>
                            <div class="font-17 text-uppercase mb-2">
                                <i class="fa fa-home"></i> 
                                <span onclick="return load(\'class/'.$data->class_id.'\')" class="user_name">'.$data->class_name.'</span>
                            </div>
                            <div class="font-15 border-bottom pb-2 mb-2">
                                <i class="fa fa-calendar-check"></i> 
                                '.date("l, jS F Y", strtotime($data->date_created)).'
                            </div>
                            <div class="mb-2 border-bottom pb-2">
                                <div class="font-17" align="center"><strong>The Teacher\'s Report</strong></div>
                                <div class="font-14">'.$data->description.'</div>
                            </div>
                            <div class="font-15  pb-2 mb-2">
                                <div class="pb-2">
                                    <i class="fa fa-user-graduate"></i>
                                    <span onclick="return load(\'staff/'.$data->created_by.'/documents\')" class="user_name">
                                    '.$data->created_by_information->name.'
                                    </span>
                                </div>
                                <div class="pb-2">
                                    <i class="fa fa-phone"></i> 
                                    '.$data->created_by_information->phone_number.'
                                </div>
                                <div>
                                    <i class="fa fa-envelope"></i> 
                                    '.$data->created_by_information->email.'
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <div class="slim-scroll">
                                <div class="p-0 m-0">
                                    '.leave_comments_builder("daily_report", $report_id, false).'
                                    <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$report_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                                    <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }

        $response->html .='</div></section>';

// print out the response
echo json_encode($response);
?>