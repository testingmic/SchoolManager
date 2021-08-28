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

        // print_r($data);
        // exit;

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
                        
                        // initial variables
                        $total_amount = 0;
                        $items_list = null;

                        // set the items paid for
                        $response->html .= '
                        <div class="invoice">
                            <div class="invoice-print">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="invoice-title">
                                            <h2>Receipt</h2>
                                            <div class="invoice-number">#'.$record->receipt_id.'</div>
                                        </div>
                                        <hr class="pb-0 mb-2 mt-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <address>
                                                    <strong>Student Details:</strong><br>
                                                    '.$record->student_info->name.'<br>
                                                    '.$record->student_info->unique_id.'<br>
                                                    '.$record->class_name.'<br>
                                                    '.$record->department_name.'<br>
                                                </address>
                                            </div>
                                            <div class="col-md-6 text-md-right">
                                                <address>
                                                <strong>Billed To:</strong><br>
                                                '.(!empty($record->student_info->guardian_id[0]->fullname) ? $record->student_info->guardian_id[0]->fullname : null).'
                                                '.(!empty($record->student_info->guardian_id[0]->address) ? "<br>" . $record->student_info->guardian_id[0]->address : null).'
                                                '.(!empty($record->student_info->guardian_id[0]->contact) ? "<br>" . $record->student_info->guardian_id[0]->contact : null).'
                                                '.(!empty($record->student_info->guardian_id[0]->email) ? "<br>" . $record->student_info->guardian_id[0]->email : null).'
                                                </address>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <address>
                                                <strong>Payment Method:</strong><br>
                                                <strong>'.$record->payment_method.'</strong><br>
                                                '.(
                                                    $record->payment_method === "Cheque" ? 
                                                    "<strong>".explode("::", $record->cheque_bank)[0]."</strong><br>
                                                    <strong>#{$record->cheque_number}</strong>" : ""    
                                                ).'
                                                </address>
                                            </div>
                                            <div class="col-md-6 text-md-right">
                                                <address>
                                                <strong>Payment Date:</strong><br>
                                                '.$record->recorded_date.'<br><br>
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
                                                    </tr>';
                                                    foreach($data as $key => $fee) {
                                                        $key++;
                                                        $total_amount += $fee->amount;

                                                        $response->html .= "
                                                        <tr>
                                                            <td data-width=\"40\" style=\"width: 40px;\">{$key}</td>
                                                            <td>{$fee->category_name}</td>
                                                            <td class=\"text-right\">{$fee->amount}</td>
                                                        </tr>";
                                                    }
                                                $response->html .= '
                                                    </tbody>
                                            </table>
                                        </div>
                                        <div class="row mt-4">
                                            <div class="col-lg-8"></div>
                                            <div class="col-lg-4 text-right">
                                                <div class="invoice-detail-item">
                                                    <div class="invoice-detail-name">Total</div>
                                                    <div class="invoice-detail-value invoice-detail-value-lg">'.number_format($total_amount, 2).'</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="text-md-right">
                                <button onclick="return print_receipt(\''.$item_id.'\')" class="btn btn-warning btn-icon icon-left"><i class="fas fa-print"></i> Print Receipt</button>
                            </div>
                        </div>';
                        
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