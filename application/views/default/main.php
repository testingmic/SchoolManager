<?php
// set the title
$page_title = "Dashboard";

// require the headtags
require "headtags.php";

// global variable
global $isActiveAccount, $clientData, $clientId, $isSchool;

// set the current_url
$set_current_url = $session->user_current_url;
$set_current_url = !empty($set_current_url) ? $set_current_url : str_ireplace("/main", "/dashboard", current_url());

// get the url path
$urlParse = parse_url($set_current_url);
if(empty($urlParse['path']) || !empty($urlParse['path']) && strlen($urlParse['path']) == 1) {
    $set_current_url .= "dashboard";
}
?>
<?= pageoverlay(); ?>
<?php if(!$isActiveAccount) { ?>
    <?php
    // refresh the client data if the account has not yet been set up fully on every refresh
    $myClass->client_session_data($session->clientId, true);
    // if the current state is propagation
    if(($clientData->client_state === "Propagation")) { ?>
    <div class="main-content" id="pagecontent">
        <div class="card">
            <div class="card-body">
                <h1 class="text-center text-info text-uppercase"><?= $clientData->client_name ?></h1>
                <?= propagating_data($clientData, $session); ?>
            </div>
        </div>
    </div>
    <?php } elseif(in_array($clientData->client_state, ["Suspended", "Expired"])) {?>
        <div class="main-content" id="pagecontent"></div>
    <?php } else {
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
            "user_status" => ["Pending", "Active"],
            "full_details" => true,
            "no_limit" => 1,
            "user_type" => $defaultUser->user_type
        ];
        $data = load_class("users", "controllers")->list($staff_param)["data"];
        $userData = $data[0] ?? (object)[];
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
            <?= $myClass->async_notification(); ?>
            <div class="card bg-gradient-to-br from-blue-500 to-purple-600 pb-3">
                <div class="card-body mb-0 pb-0">
                    <h1 class="text-center font-25 text-white text-uppercase"><?= $clientData->client_name ?></h1>
                </div>
                <div class="col-lg-12">
                    <p class="text-center text-white font-20">Thank you for creating an account with <strong><?= $appName ?></strong>.
                        Please take a moment to complete the account setup process.
                    </p>
                </div>
            </div>
            <section class="section">
                
                <div class="row">
                    <div class="col-lg-12">
                        <?php  if(in_array($clientData->client_state, ["Activated", "Pending", "Complete"])) { ?>
                            <?= activated_form($the_form, $clientData->client_state) ?>
                        <?php } ?>
                    </div>
                </div>
            </section>
        </div>
    <?php } ?>
<?php } else { ?>
    <div id="load_dashboard_content" data-load-url="<?= $set_current_url; ?>"></div>
    <div id="content_menu_display"></div>
    <div class="main-content" id="pagecontent"></div>
<?php } ?>
<?php require "foottags.php"; ?>
