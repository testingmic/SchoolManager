<?php 
// set the item id
$receipt_id = $SITEURL[1] ?? null;

global $defaultUser;

// base url
$receipt = null;
$baseUrl = config_item("base_url");
$getObject = (object) $_GET;

// if the item id was parsed
if($session->clientId) {
    
    // client id
    $clientId = $session->clientId;

    $date_range = "";
    $date_range .= isset($getObject->start_date) && !empty($getObject->start_date) ? $getObject->start_date : null;
    $date_range .= isset($getObject->end_date) && !empty($getObject->end_date) ? ":" . $getObject->end_date : null;

    // set the parameters
    $item_param = (object) [
        "clientId" => $clientId,
        "item_id" => $receipt_id,
        "userData" => $defaultUser,
        "client_data" => $defaultUser->client,
        "student_id" => $getObject->student_id ?? null,
        "category_id" => $getObject->category_id ?? null,
        "date_range" => $date_range,
    ];
    $data = load_class("fees", "controllers", $item_param)->list($item_param)["data"];

    // if the record was found
    if(is_array($data)) {

        // init variable
        $student_data = [];
        $studentIsset = (bool) isset($getObject->student_id) && !empty($getObject->student_id);

        // if the receipt id was parsed
        if(!empty($receipt_id)) {
            $student_data = $data[0];
        }

        if($studentIsset) {
            $student_data = $data[0];
        }
        
        // print_r($student_data);

        // get the client data
        $amount = 0;
        $client = $myClass->client_data($clientId);
        $clientPrefs = $client->client_preferences;

        // append the data
        $receipt = '
        <link rel="stylesheet" href="'.$baseUrl.'assets/css/app.min.css">
        <link rel="stylesheet" href="'.$baseUrl.'assets/css/style.css">
        <div style="margin:auto auto; max-width:1040px;">
            <div class="row mb-3">
                <div class="text-dark bg-white col-md-12 p-3">
                    <div class="text-center">
                        '.(!empty($client->client_logo) ? "<img width='70px' src='{$baseUrl}{$client->client_logo}'>" : "").'
                        <h3 class="mb-0 pb-0" style="color:#6777ef">'.$client->client_name.'</h3>
                        <div>'.$client->client_address.'</div>
                        <div>'.$client->client_contact.'
                            '.(!empty($client->client_secondary_contact) ? " | {$client->client_secondary_contact}" : "").'
                        </div>
                    </div>
                    <div class="border-bottom pb-1 mb-3 bg-blue"></div>
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="invoice-title">
                                        <h2>Official Receipt</h2>
                                        '.(!empty($receipt_id) ? '<div style="font-size:20px" class="text-right">Receipt ID #: '.($student_data->receipt_id ?? null).'</div>' : null).'
                                    </div>
                                    <hr class="pb-0 mb-2 mt-0">
                                    '.(!empty($student_data) ?
                                    '<div class="row">
                                        <div class="col-md-6">
                                            <address>
                                                <strong>Student Details:</strong><br>
                                                '.($student_data->student_info->name ?? null).'<br>
                                                '.($student_data->student_info->unique_id ?? null).'<br>
                                                '.($student_data->class_name ?? null).'<br>
                                                '.($student_data->department_name ?? null).'<br>
                                            </address>
                                        </div>
                                        '.(!empty($student_data->student_info->guardian_id) ?
                                        '<div class="col-md-6 text-md-right">
                                            <address>
                                            <strong>Billed To:</strong><br>
                                            '.(!empty($student_data->student_info->guardian_id[0]->fullname) ? $student_data->student_info->guardian_id[0]->fullname : null).'
                                            '.(!empty($student_data->student_info->guardian_id[0]->address) ? "<br>" . $student_data->student_info->guardian_id[0]->address : null).'
                                            '.(!empty($student_data->student_info->guardian_id[0]->contact) ? "<br>" . $student_data->student_info->guardian_id[0]->contact : null).'
                                            '.(!empty($student_data->student_info->guardian_id[0]->email) ? "<br>" . $student_data->student_info->guardian_id[0]->email : null).'
                                            </address>
                                        </div>': '').'
                                    </div>': '').'
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="section-title">Payment Details</div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-md">
                                            <tbody>
                                            <tr>
                                                <th data-width="40" style="width: 40px;">#</th>
                                                '.(empty($receipt_id) ? '<th>Item</th>' : '').'
                                                <th>Item</th>
                                                <th>Payment Method</th>
                                                <th>Description</th>
                                                <th>Record Date</th>
                                                <th class="text-right">Amount</th>
                                            </tr>';
                                            if(!empty($data)) {
                                                foreach($data as $key => $record) {
                                                    $amount += $record->amount;
                                                    $receipt .='<tr>
                                                        <td>'.($key+1).'</td>
                                                        '.(empty($receipt_id) ? '<td>
                                                            '.$record->student_info->name.'
                                                        </td>' : '').'
                                                        <td>'.$record->category_name.'</td>
                                                        <td>
                                                            <strong>'.$record->payment_method.'</strong>
                                                            '.(
                                                                $record->payment_method === "Cheque" ? 
                                                                "<br><strong>".explode("::", $record->cheque_bank)[0]."</strong><br>
                                                                <strong>#{$record->cheque_number}</strong>" : ""    
                                                            ).'
                                                        </td>
                                                        <td>'.(!$record->description ? $record->description : null).'</td>
                                                        <td>'.$record->recorded_date.'</td>
                                                        <td class="text-right"><strong>'.$record->amount.'</strong></td>
                                                    </tr>';
                                                }
                                            } else {
                                                $receipt .= '<tr><td align="center" colspan="'.(empty($receipt_id) ? 7 : 6).'">No Record Found</td></tr>';
                                            }
                                        $receipt .= '
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-lg-8"></div>
                                        <div class="col-lg-4 text-right">
                                            <hr class="mb-2">
                                            <div class="invoice-detail-item">
                                                <div class="invoice-detail-name">Total</div>
                                                <div class="invoice-detail-value invoice-detail-value-lg"><strong>'.(number_format($amount, 2)).'</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-bottom pb-1 mt-3 bg-blue"></div>
                    <div class="text-center pb-1 mt-1">
                        <strong>Location: </strong>'.$client->client_location.' | 
                        <strong>Contact:</strong> '.$client->client_contact.'
                        '.(!empty($client->client_secondary_contact) ? " | {$client->client_secondary_contact}" : "").'
                        | <strong>Address: </strong> '.(strip_tags($client->client_address)).'
                    </div>
                </div>
            </div>
        </div>
        <script>
            window.onload = (evt) => { window.print(); }
            window.onafterprint = (evt) => { window.close(); }
        </script>';
    }
    print $receipt;
} else {
    no_file_log();
}
?>