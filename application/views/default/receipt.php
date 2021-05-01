<?php 
// set the item id
$item_id = $SITEURL[1] ?? null;

global $defaultUser;

// base url
$receipt = "";
$baseUrl = config_item("base_url");

// if the item id was parsed
if(!empty($item_id) && $session->clientId) {
    
    // client id
    $clientId = $session->clientId;

    // set the parameters
    $item_param = (object) [
        "clientId" => $clientId,
        "item_id" => $item_id,
        "userData" => $defaultUser,
        "client_data" => $defaultUser->client,
    ];
    $data = load_class("fees", "controllers", $item_param)->list($item_param)["data"];

    // if the record was found
    if(!empty($data)) {

        // set the first key
        $data = $data[0];

        // get the client data
        $client = $myClass->client_data($clientId);
        $clientPrefs = $client->client_preferences;

        // append the data
        $receipt = '
        <link rel="stylesheet" href="'.$baseUrl.'assets/css/app.min.css">
        <link rel="stylesheet" href="'.$baseUrl.'assets/css/style.css">
        <div style="margin:auto auto; max-width:940px;">
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
                                        <h2>Invoice</h2>
                                        <div style="font-size:20px" class="text-right">Order #'.$data->id.'</div>
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
                                            Keith Johnson<br>
                                            197 N 2000th E<br>
                                            Rexburg, ID,<br>
                                            Springfield Center, USA
                                            </address>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <address>
                                            <strong>Payment Method:</strong><br>
                                            '.$data->payment_method.'<br>
                                            </address>
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <address>
                                            <strong>Order Date:</strong><br>
                                            '.$data->recorded_date.'<br><br>
                                            </address>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="section-title">Order Summary</div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover table-md">
                                            <tbody>
                                            <tr>
                                                <th data-width="40" style="width: 40px;">#</th>
                                                <th>Item</th>
                                                <th class="text-center">Price</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-right">Totals</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>'.$data->category_name.'</td>
                                                <td class="text-center">'.$data->amount.'</td>
                                                <td class="text-center">1</td>
                                                <td class="text-right">'.$data->amount.'</td>
                                            </tr>
                                        </tbody>
                                        </table>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-lg-8">
                                            <div class="section-title">Description</div>
                                            <p class="section-lead">'.$data->description.'</p>
                                        </div>
                                        <div class="col-lg-4 text-right">
                                            <div class="invoice-detail-item">
                                                <div class="invoice-detail-name">Subtotal</div>
                                                <div class="invoice-detail-value">'.$data->amount.'</div>
                                            </div>
                                            <hr class="mt-2 mb-2">
                                            <div class="invoice-detail-item">
                                                <div class="invoice-detail-name">Total</div>
                                                <div class="invoice-detail-value invoice-detail-value-lg">'.$data->amount.'</div>
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
}

print $receipt;
?>