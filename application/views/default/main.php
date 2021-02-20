<?php
// set the title
$page_title = "Dashboard";

// require the headtags
require "headtags.php";

// global variable
global $isActiveAccount, $clientData;
?>
<?= pageoverlay(); ?>
<?php if(!$isActiveAccount) {
// create a new object of the forms class
$formsObj = load_class("forms", "controllers");

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
                <p class="font-18px">Thank you for setting up an account with <strong><?= $appName ?></strong>.
                Please take a moment to complete the account setup process.
                </p>
            </div>
            <div class="col-lg-12">
                <?php  if($clientData->client_state === "Activated") { ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="padding-20">
                                <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="profile-tab2" data-toggle="tab" href="#profile" role="tab" aria-selected="true">Profile Setup</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" aria-disabled="true" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">General School Setup</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="import_students-tab2" data-toggle="tab" href="#import_students" role="tab" aria-selected="true">Import Students</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="staff-tab2" data-toggle="tab" href="#staff" role="tab" aria-selected="true">Import Staff</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="courses-tab2" data-toggle="tab" href="#courses" role="tab" aria-selected="true">Import Courses</a>
                                    </li>
                                </ul>
                                <div class="tab-content tab-bordered" id="myTab3Content">
                                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab2">
                                        <?= $formsObj->profile_form($baseUrl, $userData); ?>
                                    </div>
                                    <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab2">
                                        <?= $the_form["general"] ?? null; ?>
                                    </div>
                                    <div class="tab-pane fade" id="import_students" role="tabpanel" aria-labelledby="import_students-tab2">
                                        <?php if($notReady) { ?>
                                            <div class="alert alert-warning text-center">
                                                You must first set the Academic Year and Term to proceed.
                                            </div>
                                        <?php } else { ?>
                                            <?= $the_form["student"] ?? null; ?>
                                        <?php } ?>
                                    </div>
                                    <div class="tab-pane fade" id="staff" role="tabpanel" aria-labelledby="staff-tab2">
                                        <?php if($notReady) { ?>
                                            <div class="alert alert-warning text-center">
                                                You must first set the Academic Year and Term to proceed.
                                            </div>
                                        <?php } else { ?>
                                            <?= $the_form["staff"] ?? null; ?>
                                        <?php } ?>
                                    </div>
                                    <div class="tab-pane fade" id="courses" role="tabpanel" aria-labelledby="courses-tab2">
                                        <?php if($notReady) { ?>
                                            <div class="alert alert-warning text-center">
                                                You must first set the Academic Year and Term to proceed.
                                            </div>
                                        <?php } else { ?>
                                            <?= $the_form["course"] ?? null; ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<?php } else { ?>
<div class="main-content" id="pagecontent"></div>
<?php } ?>
<?php require "foottags.php"; ?>