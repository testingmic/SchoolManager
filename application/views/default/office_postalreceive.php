<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

// global 
global $myClass, $accessObject, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// set some important variables
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$filter = (object) array_map("xss_clean", $_POST);

// set the page tile
$pageTitle = "Postal Receive";
$response->title = $pageTitle;

// set additional parameters
$request_id = $SITEURL[1] ?? null;
$clientId = $session->clientId;

// if the user id is not empty
if(!$accessObject->hasAccess("view", "postal_receive")) {
    // parse error message
    $response->html = page_not_found("permission_denied");
} else {

    // set the first key
    $response->scripts = [
        "assets/js/frontoffice.js",
        "assets/js/upload.js"
    ];

    // results list
    $data = null;
    $request_data = null;
    $singleData = false;
    $results_list = null;

    // load the leave applications
    $param = (object) [
        "userData" => $defaultUser,
        "clientId" => $clientId,
        "section" => "postal_receive",
        "request_id" => strlen($request_id) > 6 ? $request_id : null,
        "user_id" => $defaultUser->user_id
    ];

    // create new object of the front office class
    $frontObj = load_class("frontoffice", "controllers");

    // load the results array list
    $results_array = $frontObj->list($param)["data"] ?? [];

    // if the request is not apply
    if($request_id && !empty($results_array)) {
        $singleData = true;
        $data = $results_array[0];
    }

    // load single record
    if(!$singleData) {

        // loop through the results array list
        foreach($results_array as $key => $each) {

            $action = "<button title='View Record Details' onclick='return load(\"office_postalreceive/{$each->item_id}\");' class='btn btn-sm mb-1 btn-outline-primary'><i class='fa fa-eye'></i></button>";
            
            $results_list .= "<tr data-row_id=\"{$each->id}\">";
            $results_list .= "<td>".($key+1)."</td>";
            $results_list .= "<td><span class='user_name' onclick='return load(\"office_postalreceive/{$each->item_id}\");'>{$each->content->from}</span></td>";
            $results_list .= "<td>{$each->content->reference}</td>";
            $results_list .= "<td>{$each->content->to}</td>";
            $results_list .= "<td>{$each->content->date}</td>";
            $results_list .= "<td align='center'>{$action}</td>";
            $results_list .= "</tr>";
        }

        // set the form
        $the_form = load_class("forms", "controllers")->postal_form('receive');
    }

    // if the data is not empty
    if(!empty($data)) {

        // uploads script
        $comment_form = null;   
        $response->scripts = ["assets/js/comments.js"];

        // comment form set
        $comment_form = leave_comments_builder("frontoffice", $request_id, false);

        // set the application data
        $request_data = '
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">

                    <div class="font-17 text-uppercase mb-2">
                        <i class="fa fa-user"></i> 
                        <span class="user_name">'.$data->content->to.'</span>
                    </div>
                    <div class="font-17 text-uppercase mb-2">
                        <i class="fa fa-reply-all"></i> 
                        <span>'.$data->content->from.'</span>
                    </div>
                    <div class="font-15 border-bottom pb-2 mb-2">
                        <i class="fa fa-calendar-check"></i> 
                        <strong>'.$data->content->date.'</strong>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">
                            <strong>Reference Number</strong> :
                            '.$data->content->reference.'
                        </div>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">
                            <strong>Address</strong> :
                            '.$data->content->address.'
                        </div>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        <div class="font-14">'.$data->content->note.'</div>
                    </div>
                    <div class="mb-2 border-bottom pb-2 mb-2">
                        '.$data->attachment_html.'
                    </div>
                    <div class="font-15  pb-2 mb-2">
                        <div>
                            <i class="fa fa-user"></i>
                            <span onclick="return load(\'staff/'.$data->created_by.'/documents\')" class="user_name">
                            '.$data->name.'
                            </span>
                        </div>
                        <div>
                            <i class="fa fa-phone"></i> 
                            '.$data->phone_number.'
                        </div>
                        <div>
                            <i class="fa fa-envelope"></i> 
                            '.$data->email.'
                        </div>
                        <div>
                            <i class="fa fa-calendar-check"></i> 
                            '.$data->date_created.'
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
                            '.$comment_form.'
                            <div id="comments-container" data-autoload="true" data-last-reply-id="0" data-id="'.$request_id.'" class="slim-scroll pt-3 mt-3 pr-2 pl-0" style="overflow-y:auto; max-height:850px"></div>
                            <div class="load-more mt-3 text-center"><button id="load-more-replies" type="button" class="btn btn-outline-secondary btn-sm">Loading comments <i class="fa fa-spin fa-spinner"></i></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    // set the html data
    $response->html = '
        <section class="section">

            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    '.($singleData || !empty($data) ? 
                        '<div class="breadcrumb-item">
                            <a href="'.$baseUrl.'office_postalreceive">Postal Receive List</a>
                        </div>' : null
                    ).'
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>

            <div class="row">

                '.($singleData && !empty($data) ? $request_data : null).'

                '.(!$singleData ?
                    '<div class="col-12 col-sm-12 col-md-4">
                        <div class="card">
                            <div class="card-header">Add Receive Dispatch</div>
                            <div class="card-body">
                                '.$the_form.'
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-8">
                        <div class="card">
                            <div class="card-header">Postal Receive List</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th width="5%" class="text-center">#</th>
                                                <th>From</th>
                                                <th>Ref. No.</th>
                                                <th>To</th>
                                                <th>Date</th>
                                                <th width="14%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>'.$results_list.'</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>' : null
                ).'

            </div>

        </section>';

}

// print out the response
echo json_encode($response);
?>