<?php 
//: set the page header type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");

global $myClass, $SITEURL, $defaultUser;

// initial variables
$appName = $myClass->appName;
$baseUrl = $myClass->baseUrl;

// if no referer was parsed
jump_to_main($baseUrl);

// additional update
$clientId = $session->clientId;
$response = (object) ["current_user_url" => $session->user_current_url, "page_programming" => $myClass->menu_content_array];
$pageTitle = "Fees Payment Details";
$response->title = $pageTitle;

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

// item id
$item_id = $SITEURL[1] ?? null;
// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "payment_id" => $item_id,
        "client_data" => $defaultUser->client,
        "userData" => $defaultUser
    ];
    $data = load_class("fees", "controllers", $item_param)->list($item_param)["data"];

    // if no record was found
    if(empty($data)) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $record = $data[0];
        $hasUpdate = true;

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1><i class="fa fa-money-check"></i> '.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'fees-history">Payment History List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Student Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Fullname:</span>
                                <span class="float-right text-muted">'.($record->student_info->name ?? null).'</span>
                            </p>
                            '.(!empty($record->department_name) ? 
                            '<p class="clearfix">
                                <span class="float-left">Department:</span>
                                <span class="float-right text-muted">'.($record->department_name ?? null).'</span>
                            </p>' : '').'
                            '.(!empty($record->class_name) ? 
                            '<p class="clearfix">
                                <span class="float-left">Class:</span>
                                <span class="float-right text-muted">'.($record->class_name ?? null).'</span>
                            </p>' : '').'
                            '.(!empty($record->student_info->email) ? 
                            '<p class="clearfix">
                                <span class="float-left">Email:</span>
                                <span class="float-right text-muted">'.($record->student_info->email ?? null).'</span>
                            </p>' : '').'
                            '.(!empty($record->student_info->phone_number) ? 
                            '<p class="clearfix">
                                <span class="float-left">Contact:</span>
                                <span class="float-right text-muted">'.($record->student_info->phone_number ?? null).'</span>
                            </p>' : '').'
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Received By</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Fullname</span>
                                <span class="float-right text-muted">'.($record->created_by_info->name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Email</span>
                                <span class="float-right text-muted">'.($record->created_by_info->email ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Contact</span>
                                <span class="float-right text-muted">'.($record->created_by_info->phone_number ?? null).'</span>
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
                            <a class="nav-link active" id="profile-tab2" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Receipt</a>
                        </li>
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';

                        // set the items paid for
                        $response->html .= fees_receipt_data($data, $record, $item_id);
                        
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