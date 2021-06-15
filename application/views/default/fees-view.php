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
$pageTitle = "Fees Payment Details";
$response->title = "{$pageTitle} : {$appName}";

$accessObject->userId = $session->userId;
$accessObject->clientId = $session->clientId;

// item id
$item_id = confirm_url_id(1) ? xss_clean($SITEURL[1]) : null;
$pageTitle = confirm_url_id(2, "update") ? "Update {$pageTitle}" : "View {$pageTitle}";

// if the user id is not empty
if(!empty($item_id)) {

    $item_param = (object) [
        "clientId" => $clientId,
        "item_id" => $item_id,
        "client_data" => $defaultUser->client,
        "userData" => $defaultUser
    ];
    $data = load_class("fees", "controllers", $item_param)->list($item_param)["data"];

    // if no record was found
    if(empty($data)) {
        $response->html = page_not_found();
    } else {

        // set the first key
        $data = $data[0];
        $hasUpdate = true;

        // if the request is to view the student information
        $updateItem = confirm_url_id(2, "update") ? true : false;

        // append the html content
        $response->html = '
        <section class="section">
            <div class="section-header">
                <h1>'.$pageTitle.'</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'dashboard">Dashboard</a></div>
                    <div class="breadcrumb-item active"><a href="'.$baseUrl.'fees-history">Payment History List</a></div>
                    <div class="breadcrumb-item">'.$pageTitle.'</div>
                </div>
            </div>
            <div class="section-body">
            <div class="row mt-sm-4">
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Student Details</h4>
                    </div>
                    <div class="card-body pt-0 pb-0">
                        <div class="py-3 pt-0">
                            <p class="clearfix">
                                <span class="float-left">Fullname:</span>
                                <span class="float-right text-muted">'.($data->student_info->name ?? null).'</span>
                            </p>
                            '.(!empty($data->department_name) ? 
                            '<p class="clearfix">
                                <span class="float-left">Department:</span>
                                <span class="float-right text-muted">'.($data->department_name ?? null).'</span>
                            </p>' : '').'
                            '.(!empty($data->class_name) ? 
                            '<p class="clearfix">
                                <span class="float-left">Class:</span>
                                <span class="float-right text-muted">'.($data->class_name ?? null).'</span>
                            </p>' : '').'
                            '.(!empty($data->student_info->email) ? 
                            '<p class="clearfix">
                                <span class="float-left">Email:</span>
                                <span class="float-right text-muted">'.($data->student_info->email ?? null).'</span>
                            </p>' : '').'
                            '.(!empty($data->student_info->phone_number) ? 
                            '<p class="clearfix">
                                <span class="float-left">Contact:</span>
                                <span class="float-right text-muted">'.($data->student_info->phone_number ?? null).'</span>
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
                                <span class="float-right text-muted">'.($data->created_by_info->name ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Email</span>
                                <span class="float-right text-muted">'.($data->created_by_info->email ?? null).'</span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">Contact</span>
                                <span class="float-right text-muted">'.($data->created_by_info->phone_number ?? null).'</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-8">
                <div class="card">
                <div class="padding-20">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">';

                    if($hasUpdate) {
                        $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link active" id="profile-tab2" data-toggle="tab" href="#settings" role="tab" aria-selected="false">Receipt</a>
                        </li>';
                    }
                    
                    $response->html .= '
                        <li class="nav-item">
                            <a class="nav-link" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">Payment Info</a>
                        </li>
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade" id="students" role="tabpanel" aria-labelledby="students-tab2">
                            <div class="mb-3">
                                <div class="card-body p-2 pl-0">
                                    <div><h5>PAYMENT INFORMATION</h5></div>
                                    <table width="100%" class="table-bordered">
                                        <tr>
                                            <td class="p-2" width="20%">Amount Paid:</td>
                                            <td class="p-2">'.$data->amount.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Date:</td>
                                            <td class="p-2">'.$data->recorded_date.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Category:</td>
                                            <td class="p-2">'.$data->category_name.'</td>
                                        </tr>
                                        <tr>
                                            <td class="p-2" width="20%">Desription:</td>
                                            <td class="p-2">'.$data->description.'</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="profile-tab2">';
                        
                        if($hasUpdate) {
                            $response->html .= '
                            <div class="invoice">
                                <div class="invoice-print">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="invoice-title">
                                                <h2>Receipt</h2>
                                                <div class="invoice-number">#'.$data->receipt_id.'</div>
                                            </div>
                                            <hr class="pb-0 mb-2 mt-0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <address>
                                                        <strong>Student Details:</strong><br>
                                                        '.$data->student_info->name.'<br>
                                                        '.$data->student_info->unique_id.'<br>
                                                        '.$data->class_name.'<br>
                                                        '.$data->department_name.'<br>
                                                    </address>
                                                </div>
                                                <div class="col-md-6 text-md-right">
                                                    <address>
                                                    <strong>Billed To:</strong><br>
                                                    '.(!empty($data->student_info->guardian_id[0]->fullname) ? $data->student_info->guardian_id[0]->fullname : null).'
                                                    '.(!empty($data->student_info->guardian_id[0]->address) ? "<br>" . $data->student_info->guardian_id[0]->address : null).'
                                                    '.(!empty($data->student_info->guardian_id[0]->contact) ? "<br>" . $data->student_info->guardian_id[0]->contact : null).'
                                                    '.(!empty($data->student_info->guardian_id[0]->email) ? "<br>" . $data->student_info->guardian_id[0]->email : null).'
                                                    </address>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <address>
                                                    <strong>Payment Method:</strong><br>
                                                    <strong>'.$data->payment_method.'</strong><br>
                                                    '.(
                                                        $data->payment_method === "Cheque" ? 
                                                        "<strong>".explode("::", $data->cheque_bank)[0]."</strong><br>
                                                        <strong>#{$data->cheque_number}</strong>" : ""    
                                                    ).'
                                                    </address>
                                                </div>
                                                <div class="col-md-6 text-md-right">
                                                    <address>
                                                    <strong>Payment Date:</strong><br>
                                                    '.$data->recorded_date.'<br><br>
                                                    </address>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-md-12">
                                            <div class="section-title">Payment Summary</div>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover table-md">
                                                    <tbody>
                                                    <tr>
                                                        <th data-width="40" style="width: 40px;">#</th>
                                                        <th>Item</th>
                                                        <th class="text-right">Amount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>'.$data->category_name.'</td>
                                                        <td class="text-right">'.$data->amount.'</td>
                                                    </tr>
                                                </tbody>
                                                </table>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-lg-8">
                                                    <div class="section-title">Description</div>
                                                    <p class="section-lead">'.($data->description ? $data->description : null).'</p>
                                                </div>
                                                <div class="col-lg-4 text-right">
                                                    <div class="invoice-detail-item">
                                                        <div class="invoice-detail-name">Total</div>
                                                        <div class="invoice-detail-value invoice-detail-value-lg">'.$data->amount.'</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-md-right">
                                    <button onclick="return print_receipt(\''.$item_id.'\')" class="btn btn-warning btn-icon icon-left"><i class="fas fa-print"></i> Print</button>
                                </div>
                            </div>';
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