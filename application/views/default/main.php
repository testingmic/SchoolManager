<?php
// set the title
$page_title = "Dashboard";

// require the headtags
require "headtags.php";

// global variable
global $isActiveAccount, $clientData, $clientId, $isSchool, $isChurch, $isBooking;
?>
<?= pageoverlay(); ?>
<?php if(!$isActiveAccount) { ?>
    <?php
    // if the current state is propagation
    if(($clientData->client_state === "Propagation")) { ?>
    <div class="main-content" id="pagecontent">
        <div class="card">
            <div class="card-body">
                <h1 class="text-center text-info text-uppercase"><?= $clientData->client_name ?></h1>
                <?= propagating_data($clientData); ?>
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
            <?php if(in_array($clientData->client_state, ["Pending"])) { ?>
                <div class="alert alert-danger text-center">
                    Sorry! Your Account has not yet been activated. Please check your
                    email for the verification link. You may not be able to perform certain functions if
                    not done.
                </div>
            <?php } ?>
            <div class="card">
                <div class="card-body mb-0 pb-0">
                    <h1 class="text-center text-info text-uppercase"><?= $clientData->client_name ?></h1>
                </div>
                <div class="col-lg-12">
                    <p class="text-center text-success font-18px">Thank you for creating an account with <strong><?= $appName ?></strong>.
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
    <div class="main-content" id="pagecontent"></div>
<?php } ?>
<?php require "foottags.php"; ?>
