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

    $receipt = "";
    $date_range = "";
    $date_range .= isset($getObject->start_date) && !empty($getObject->start_date) ? $getObject->start_date : null;
    $date_range .= isset($getObject->end_date) && !empty($getObject->end_date) ? ":" . $getObject->end_date : null;

    // set the parameters
    $item_param = (object) [
        "clientId" => $clientId,
        "item_id" => $receipt_id,
        "userData" => $defaultUser,
        "date_range" => $date_range,
        "client_data" => $defaultUser->client,
        "student_id" => $getObject->student_id ?? null,
        "category_id" => $getObject->category_id ?? null,
        "payment_id" => $receipt_id ?? null,
    ];

    // create a new object
    $feesObject = load_class("fees", "controllers", $item_param);

    // load the receipt data
    $data = $feesObject->list($item_param)["data"];

    // if the record was found
    if(is_array($data)) {

        // create a new object
        $param = (object) [
            "data" => $data,
            "clientId" => $clientId,
            "getObject" => $getObject,
            "receipt_id" => $receipt_id,
        ];
        
        // load the receipt 
        $receipt = $feesObject->receipt($param);
    }
    print $receipt;
} else {
    no_file_log();
}
?>