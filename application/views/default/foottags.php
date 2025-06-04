        <?php
        // load some global variables
        global $myClass, $baseUrl, $session, $isSupport, $userData, $current_url, $loadedJS, $auto_close_modal, $userPrefs, $announcementNotice, $defaultClientData, $clientName;
        // init indexdb variable
        $idb_init = (bool) (isset($userPrefs->idb_init->init) && (strtotime($userPrefs->idb_init->idb_next_init) < time()));
        // if the force_cold_boot=1 query parameter has been parsed then set the init to true
        if(isset($_GET["force_cold_boot"]) && ($_GET["force_cold_boot"] == 1)) {
            // $idb_init = true;
        }
        // create a new account object
        $accountObj = load_class("account", "controllers", (object) ["client_data" => $defaultUser->client]);
        ?>
        <footer class="main-footer">
            <div class="footer-left">
                Copyright &copy; <?= date("Y") ?> <div class="bullet"></div> <a href="<?= $myClass->baseUrl; ?>" target="_blank"><?= $appName ?></a>. All rights reserved
                | Powered By <strong><?= config_item("developer") ?></strong>
            </div>
            <div class="footer-right">
                <div class="network-notifier"></div>
            </div>
        </footer>

        <div class="settingSidebar">
          <a href="#" class="settingPanelToggle"> <i class="fa fa-book-reader"></i></a>
            <div class="settingSidebar-body ps ps-theme-default">
                <div class=" fade show active">
                    <div class="setting-panel-header">
                        <h6 class="font-medium m-b-0" data-item="title">
                            <?= $isEmployee || $isWardParent ? "Onboard Dictionary" : "Quick System Search" ?>
                        </h6>
                    </div>
                    <?php if($isEmployee || $isTutor || $isAdminAccountant) { ?>
                    <div class="quick_search">
                        <?php if(!$isEmployee) { ?>
                        <div data-content="system" class="col-sm-6 selected button">
                            Search
                        </div>
                        <?php } ?>
                        <div data-content="dictionary" class="<?= $isEmployee ? "col-sm-12" : "col-sm-6" ?> button">
                            Dictionary
                        </div>
                    </div>
                    <?php } ?>
                    <div class="p-15 border-bottom">
                        <?php if($isTutor || $isAdminAccountant) { ?>
                            <div class="system_content">
                                <div class="layout-color">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <label for="system">Search for a user account <small class="text-success">(hit enter to search)</small></label>
                                        </div>
                                        <div>
                                            <span onclick="return clear_quick_search_form('system')" class="cursor text-danger" title="Click to reset form">Clear</span>
                                        </div>
                                    </div>
                                    <input type="search" autocomplete="Off" name="system" id="system" class="form-control">
                                </div>
                            </div>
                        <?php } ?>
                        <div class="dictionary_content <?= $isEmployee || $isWardParent ? null : "hidden" ?>">
                            <div class="layout-color">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <label for="dictionary">Search for a Word <small class="text-success">(hit enter to search)</small></label>
                                    </div>
                                    <div>
                                        <span onclick="return clear_quick_search_form('dictionary')" class="cursor text-danger" title="Click to reset form">Clear</span>
                                    </div>
                                </div>
                                <input type="search" autocomplete="Off" name="dictionary" id="dictionary" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2 trix-slim-scroll" style="max-height:750px;overflow-y:auto;">
                        <div class="text-center hidden" id="quick_search_loader">
                            <i class="fa fa-spin fa-spinner text-primary"></i>
                        </div>
                        <div id="dictionary_query_results"></div>
                        <div id="system_query_results"></div>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </div>
    <div class="email-notification" style="display:none">
        <div class="d-flex row justify-content-between">
            <div class="content"></div>
            <div><i class="fa font-18px fa-times-circle"></i></div>
        </div>
    </div>
    <?php // below variables can be found in modal_helper.php ?>
    <?= ajax_forms_modal($auto_close_modal); ?>
    <?= replies_modal($auto_close_modal); ?>
    <?= general_modal(); ?>
    <?= save_form_data(); ?>
    <?= ajax_form_button(); ?>

    <div class="modal fade" id="viewOnlyModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-top modal-lg" style="width:100%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title pt-2">QUESTION DETAILS</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer mt-0 pt-0">
                    <button class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addMediaModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-top modal-xl" style="width:100%;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title pt-2">Pick Media Images / Document</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-1">

                    
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="upload_files-tab2" data-toggle="tab" href="#upload_files" role="tab" aria-selected="true">Upload Files</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="media_library-tab2" data-toggle="tab" href="#media_library" role="tab" aria-selected="true">Media Library</a>
                        </li>
                    </ul>
                    <div class="tab-content tab-bordered" id="myTab3Content">
                        <div class="tab-pane fade show active" id="upload_files" role="tabpanel" aria-labelledby="upload_files-tab2">
                            <div class="media-modal-content">
                                <div class="upload-content">
                                    <h3 class="mb-2"><span class="font-22">Click on the button below to upload the file(s).</span></h3>
                                    <button id="fileupload" class="btn btn-outline-primary pr-3 pl-3 pt-2 mb-2">Select Files</button>
                                    <input data-form_item_id="" data-form_module="documents_root" multiple accept=".<?= implode(",.", $myClass->accepted_attachment_file_types) ?>" type="file" hidden name="media_file_upload" id="media_file_upload" class="media-file-input">
                                    <p>Maximum upload file size: <?= $myClass->max_attachment_size ?>MB.</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade p-0" id="media_library" role="tabpanel" aria-labelledby="media_library-tab2">
                            <div class="media-modal-content">
                                <div class="row mr-0 ml-0">
                                    <div class="col-md-9 media-content">
                                        <div class="form-group" id="media_search_input">
                                            <label for="media-filter" class="pb-0 mb-0">Search:</label>
                                            <input type="search" name="media_name" class="form-control">
                                        </div>
                                        <div class="row"></div>
                                    </div>
                                    <div class="col-md-3 bg-light media-information">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer mt-0 pt-0">
                    <button class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="announcement_preview"></div>
</div>

<?php if($idb_init) { ?>
<button id="idb_init" class="btn btn-outline-success btn-sm" hidden></button>
<?php } ?>
<?php if(!empty($announcementNotice)) { ?>
<?= $announcementNotice->content ?>
<?php } ?>
<script>
var clientName = "<?= $clientName; ?>", fieldDefault = {}, refresh_seconds = 1000, thisRowId = 1, thisSelectRow = 1, userAgent = "<?= $myClass->agent."||".$myClass->platform."||".$myClass->browser."||".ip_address(); ?>",
baseUrl = "<?= $baseUrl ?>", apiURL = "<?= config_item("api_url"); ?>", current_url="<?= $user_current_url ?>", sms_text_count = <?= $myClass->sms_text_count; ?>,
viewedAs = "<?= (bool) isset($_GET["viewas"]) ?>", this_user_unique_key = "persist:msgh-client-<?= $session->userId; ?>", defaultPassword = "<?= DEFAULT_PASS ?>",
form_modules = <?= json_encode($myClass->form_modules); ?>,
swalnotice = <?= json_encode($myClass->swal_notification); ?>,
$myPrefs = <?= json_encode($userPrefs); ?>;
var acceptedArray = new Array();
<?php foreach($accountObj->accepted_column as $key => $values) { ?>
acceptedArray["<?= $key ?>"] = <?php $values = array_values($accountObj->accepted_column[$key]); print "[\"". implode("\",\"", $values) ."\"]"; ?>, 
<?php } ?>
current_column = "";
var appVersion = "<?= version() ?>";
</script>
<script src="<?= $baseUrl; ?>assets/js/app.min.js"></script>
<script src="<?= $baseUrl; ?>assets/bundles/chartjs/chart.min.js"></script>
<script src="<?= $baseUrl; ?>assets/bundles/apexcharts/apexcharts.min.js"></script>
<script src="<?= $baseUrl; ?>assets/bundles/select2/select2.js"></script>
<script src="<?= $baseUrl; ?>assets/bundles/sweetalert/sweetalert.js"></script>
<script src="<?= $baseUrl; ?>assets/bundles/datatables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= $baseUrl; ?>assets/bundles/datatables/export-tables/dataTables.buttons.min.js"></script>
<script src="<?= $baseUrl; ?>assets/bundles/datatables/export-tables/buttons.print.min.js"></script>
<script src="<?= $baseUrl; ?>assets/js/magnify.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/js/notify.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/vendors/trix/trix.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/bundles/bootstrap-daterangepicker/daterangepicker.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/bundles/bootstrap-datepicker/datepicker.min.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/bundles/fullcalendar/fullcalendar.min.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/js/scripts.js?v=<?= version() ?>"></script>
<?php if($isActiveAccount) { ?>
<script src="https://js.paystack.co/v2/inline.js"></script>
<script src="<?= $baseUrl; ?>assets/vendors/timetable/ui.min.js"></script>
<script src="<?= $baseUrl; ?>assets/vendors/timetable/ui-touch-punch.min.js"></script>
<script src="<?= $baseUrl; ?>assets/vendors/timetable/chosen.js"></script>
<script src="<?= $baseUrl; ?>assets/vendors/timetable/form.js"></script>
<script src="<?= $baseUrl; ?>assets/vendors/timetable/grid.js"></script>
<script src="<?= $baseUrl; ?>assets/js/myschoolgh.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/js/filemanager.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/js/app.js?v=<?= version() ?>"></script>
<?php } else { ?>
<script src="<?= $baseUrl; ?>assets/js/app.js?v=<?= version() ?>"></script>
<?php if(!in_array($defaultClientData->client_state, ["Expired", "Suspended"])) { ?>
    <script src="<?= $baseUrl; ?>assets/js/setup.js?v=<?= version() ?>"></script>
<?php } ?>
<script src="<?= $baseUrl; ?>assets/js/grading.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/js/import.js?v=<?= version() ?>"></script>
<?php } ?>
<?php if($isSupport) { ?>
<script src="<?= $baseUrl; ?>assets/js/schools.js?v=<?= version() ?>"></script>
<?php } ?>
<script src="<?= $baseUrl; ?>assets/js/notification.js?v=<?= version() ?>"></script>
<script src="<?= $baseUrl; ?>assets/js/media-uploader.js?v=<?= version() ?>"></script>
<?php if(isset($formToShow)) { ?>
<script>fieldDefault = <?= json_encode($formToShow) ?>, thisSelectRow = <?= $formData["thisSelectRow"] ?>, thisRowId = <?= $formData["thisRowId"] ?>;</script>
<?php } ?>
<?php foreach($loadedJS as $eachJS) { ?>
<script src="<?= $baseUrl; ?><?= $eachJS ?>?v=<?= version() ?>"></script>
<?php } ?>
<script>
<?php if(!$isActiveAccount) { ?>
    var logout = async() => {
        await $.post(`${baseUrl}api/auth/logout`).then((resp) => {
            if (resp.code == 200) {
                swal({
                    text: "You have successfully been logged out.",
                    icon: "success",
                });
                setTimeout(() => {
                    window.location.href = `${baseUrl}`
                }, 1500)
            } else {
                notify("Sorry! An unexpected error was encountered.");
            }
        });
    }
<?php } ?>
$(() => {
<?php if(isset($_GET["end_id"]) && preg_match("/^[a-zA-Z0-9]+$/", $_GET["end_id"])) { ?>
$(`button[data-function="update"][data-item="<?= $_GET["end_id"] ?>"]`).trigger("click");
<?php } ?>
});
</script>
</body>
</html>