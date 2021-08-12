<?php 
// default variables
$appName = config_item("site_name");
$baseUrl = config_item("base_url");

// global variables
global $defaultClientData, $myClass;

// init variables
$payment_form = "";
$errorFound = false;
$clientPref = [];
$getObject = array_map("xss_clean", $_GET);

// accepted url
$accepted_url = [
    "fees" => [
        "params" => "checkout_url", "student_id", "class_id",
        "class" => "fees"
    ]
];

// load the payment helper
load_helpers(['payment_helper']);

// get the client data
$client_id = $SITEURL[1] ?? null;

// if the client id is not empty
if(!empty($client_id)) {
    
    // get the client data
    $client = isset($defaultClientData->client_name) ? $defaultClientData : $myClass->client_data($client_id);

    // check the url
    $errorFound = check_url($getObject);

    // convert the parameters into an object
    $getObject = (object) $getObject;

    // load class
    if(!$errorFound && isset($client->client_id)) {

        // parse the client data
        $client_info = (object) ["client_data" => $client];
        
        // append the client id to it
        $getObject->clientId = $client->client_id;

        // load the class
        $paymentObj = load_class($accepted_url[$SITEURL[2]]["class"], "controllers", $client_info);

        // remove the client preferences
        $clientPref = $client->client_preferences;
        
        // init parameters
        $getObject->item_specified = false;

        // assign new variables
        if(confirm_url_id(4, "checkout")) {
            $getObject->item_specified = true;
            $getObject->checkout_url = $SITEURL[3];
        }
        if(confirm_url_id(3) && !confirm_url_id(4)) {
            $getObject->student_id = $SITEURL[3];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= "Payment Checkout" ?? "Dashboard" ?> : <?= $appName ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/gallery.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
</head>
<body class="bg">
    <div class="d-flex justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <div class="mt-0">
                <div style="display: inline-block; width: 100%; text-align: center; height: 90px; line-height: 90px;">
                    <img style="vertical-align: middle;" height="60px" alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo" />
                    <span style="font-size:25px;font-weight:bold;"><?= $appName ?></span>
                </div>
            </div>
            <div class="card">
                <div class="card-block p-3">
                
                    <div class="row" id="make_fee_payment">
                        <?php if($errorFound || !isset($client->client_name)) { print "<p align='center' class='col-lg-12 text-danger'>{$myClass->permission_denied}</p>"; } else { ?>
                        <div class="col-md-6 mb-3">
                            <div class="client-logo text-center pt-2">
                                <img width="100%" style="max-width:250px" src="<?= $baseUrl ?><?= $client->client_logo ?>" alt="">
                            </div>
                            <div class="client-info border-bottom border-primary bg-whitesmoke p-2">
                                <div class="text-center client-name">
                                    <span class="font-18"><?= $client->client_name ?></span>
                                </div>
                                <div class="pay-info text-center">
                                    Make Payment for your <strong><?= ucfirst($SITEURL[2]) ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-content-loader" id="loader" style="display: none; position: absolute">
                                <div class="offline-content text-center">
                                    <p><i class="fa fa-spin fa-spinner fa-3x"></i></p>
                                </div>
                            </div>
                            <div class="form-content-loader" id="success_loader" style="display: none; position: absolute; border:solid 1px #ccc;">
                                <div class="offline-content text-center">
                                    <p class="m-0 p-0"><i class="fa fa-check text-success fa-3x"></i>Payment Successful</p>
                                    <p class="m-0 p-0"><small onclick="return close_payment_window();" class="text-danger underline">Close</small></p>
                                </div>
                            </div>
                            <div class="bg-whitesmoke p-3 border">
                                <?php
                                // get the payment form
                                if(!$errorFound && ($SITEURL[2] == "fees")) {
                                    $payment_form = pay_student_fees_checkout();
                                    print $payment_form;
                                }
                                ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php if(!$errorFound) { ?>
    <script>var baseUrl = "<?= $baseUrl ?>";</script>
    <script src="<?= $baseUrl; ?>assets/js/app.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/notify.js"></script>
    <script src="https://js.paystack.co/v2/inline.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/pay.js"></script>
<?php } ?>
</body>
</html>