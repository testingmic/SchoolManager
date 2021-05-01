<?php
// set the title
$page_title = "Dashboard";

// require the headtags
require "headtags.php";

// global variable
global $isActiveAccount, $clientData, $clientId;
?>
<?= pageoverlay(); ?>
<?php if(!$isActiveAccount) {
// create a new object of the forms class
$formsObj = load_class("forms", "controllers");

// if the upload id is not empty
if(!empty($session->last_recordUpload)) {

    // check fi the setup_upload is not already set
    $clientPrefs->setup_upload = isset($clientPrefs->setup_upload) ? $clientPrefs->setup_upload : (object) [];

    // log the activity and check that the student record have been uploaded
    $clientPrefs->setup_upload->{$session->last_recordUpload} = true;
    
    // update the client information
    $myschoolgh->query("UPDATE clients_accounts SET client_preferences = '".json_encode($clientPrefs)."' WHERE client_id ='{$clientData->client_id}' LIMIT 1");
}

// get the user data
$staff_param = (object) [
    "clientId" => $clientId,
    "user_id" => $loggedUserId,
    "limit" => 1,
    "full_details" => true,
    "no_limit" => 1,
    "user_type" => $defaultUser->user_type
];
$data = load_class("users", "controllers")->list($staff_param)["data"];
$userData = $data[0];
$userData->disabled = true;

// not ready variable
$notReady = (bool) (empty($clientPrefs->academics->academic_year) || empty($clientPrefs->academics->academic_term));

// get the settings form
$the_form = $formsObj->settings_form($clientId, "ajax-account-form-content");

// remove the last upload session id
$session->remove(["last_recordUpload"]);
$session->remove(["course_csv_file", "staff_csv_file", "student_csv_file"]);

load_helpers(['setup_helper']);
?>
<div class="main-content" id="pagecontent">
    <section class="section">
        <div class="d-flex mt-3 justify-content-between">
            <div class="section-header">
                <h1>Hello <?= $clientData->client_name ?>,</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <p class="font-18px">Thank you for creating an account with <strong><?= $appName ?></strong>.
                Please take a moment to complete the account setup process.
                </p>
            </div>
            <div class="col-lg-12">
                <?php  if($clientData->client_state === "Activated") { ?>
                    <?= activated_form($the_form) ?>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<?php } else { ?>
<div class="main-content" id="pagecontent"></div>
<?php } ?>
<?php require "foottags.php"; ?>