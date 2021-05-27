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

    // create a new object
    $feesObject = load_class("fees", "controllers", $item_param);

    // load the receipt data
    $data = $feesObject->list($item_param)["data"];

    // if the record was found
    if(is_array($data)) {

        // create a new object
        $param = (object) [
            "getObject" => $getObject,
            "data" => $data,
            "clientId" => $clientId
        ];

        // load the receipt 
        $receipt = $feesObject->receipt();
    }
    print $receipt;
} else {
    no_file_log();
}
?>