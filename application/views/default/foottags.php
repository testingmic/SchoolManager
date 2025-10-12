        <?php
        // load some global variables
        global $myClass, $baseUrl, $session, $isSupport, $userData, $current_url, $loadedJS, $auto_close_modal, $userPrefs, $announcementNotice, $defaultClientData, $clientName;
        
        // init indexdb variable
        $idb_init = (bool) (isset($userPrefs->idb_init->init) && (strtotime($userPrefs->idb_init->idb_next_init) < time()));
        
        // set the class position
        $classPosition = "absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full animate-pulse";

        // create a new account object
        $accountObj = load_class("account", "controllers", (object) ["client_data" => $defaultUser->client]);

        $userQrCode = $myClass->qr_code_renderer($defaultUser->user_type, $defaultUser->user_row_id, $defaultUser->client_id, $defaultUser->name, true);
        
        // set the parameters
        $item_param = (object) [
            "baseUrl" => $baseUrl,
            "width" => "col-lg-12",
            "clientId" => $userPrefs->clientId ?? $defaultUser->client_id,
            "client_data" => $defaultUser->client
        ];
        // get the list of all the templates
        $knowledge_base_list = $myClass->tutorials_list($item_param);
        ?>
        <?php if($isAdminAccountant || $isSupport) { ?>
        <footer class="main-footer">
            <div class="footer-left">
                Copyright &copy; <?= 2023 ?> <div class="bullet"></div> <a href="<?= $myClass->baseUrl; ?>" target="_blank"><?= $appName ?></a>. All rights reserved
                | Powered By <strong><?= config_item("developer") ?></strong>
            </div>
            <div class="footer-right">
                <div class="network-notifier"></div>
            </div>
        </footer>
        <?php } else { ?>
        <nav id="footerBanner" class="footerBanner mt-4 bg-white dark:bg-gray-800 shadow-2xl border-t border-gray-200 dark:border-gray-700 fixed bottom-0 left-0 right-0 z-50 backdrop-blur-lg bg-white/95 dark:bg-gray-800/95">
            <div class="max-w-7xl mx-auto">
            <div class="flex justify-around">
                <!-- Home Navigation -->
                <a href="<?= $baseUrl ?>dashboard" class="flex flex-col items-center justify-center px-3 py-3 transition-all duration-300 group text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <div class="relative">
                    <svg class="h-6 w-6 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <?php if (!empty($favicon_color) && ($favicon_color == 'dashboard')): ?>
                        <div class="<?= $classPosition ?>"></div>
                    <?php endif; ?>
                    </div>
                    <span class="text-xs mt-1 font-medium">Dashboard</span>
                </a>
                <a href="<?= $baseUrl ?><?= $isWardParent ? "attendance_history" : "attendance" ?>" class="flex flex-col items-center justify-center px-3 py-3 transition-all duration-300 group text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <div class="relative">
                        <i class="fa fa-clock font-24"></i>
                    </div>
                    <span class="text-xs mt-1 font-medium">Attendance</span>
                </a>

                <?php if($isWardParent) { ?>
                <!-- Fees Navigation -->
                <a href="<?= $baseUrl ?>fees-history" class="flex flex-col items-center justify-center px-3 py-3 transition-all duration-300 group text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <div class="relative">
                        <svg class="h-6 w-6 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xs mt-1 font-medium">Fees</span>
                </a>
                <?php } ?>

                <!-- Assessments Navigation -->
                <a href="<?= $baseUrl ?>assessments" class="flex flex-col items-center justify-center px-3 py-3 transition-all duration-300 group text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <div class="relative">
                        <i class="fas fa-book-reader font-24"></i>
                    </div>
                    <span class="text-xs mt-1 font-medium">Assessments</span>
                </a>

                <?php if(!$isWardParent) { ?>
                <!-- Profile Navigation -->
                <a href="<?= $baseUrl ?>profile" class="flex flex-col items-center justify-center px-3 py-3 transition-all duration-300 group text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                    <div class="relative">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center transition-transform duration-300 group-hover:scale-110">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-xs mt-1 font-medium">Profile</span>
                </a>
                <?php } ?>

            </div>
            </div>

            <!-- Active Tab Indicator -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-green-500"></div>
        </nav>
        <?php } ?>

        <div class="bg-blur hidden"></div>
        <div class="settingSidebar">
            <a href="#" class="settingPanelToggle" onclick="return open_dictionary_modal()"> <i class="fa fa-book-reader"></i></a>
            <div class="settingSidebar-body ps ps-theme-default">
                <div class="fade show active">
                    <div class="setting-panel-header">
                        <h6 class="font-medium m-b-0" data-item="title">
                            <?= $isEmployee || $isWardParent ? "Onboard Dictionary" : "Quick System Search" ?>
                        </h6>
                    </div>
                    <?php if($isWardParent) { ?>
                        <div class="attendance_modal hidden">
                            <div class="card-body pt-2 trix-slim-scroll">
                                <div class="text-center font-18 my-3 font-bold text-primary">
                                    Show QR-Code to School staff for scanning.
                                </div>
                                <div class="text-center">
                                    <div class="grid grid-cols-2">
                                        <div class="col-12">
                                            <button onclick="return option_selected('bus')" id="bus_option" class="btn btn-outline-primary btn-block font-16">
                                                <i class="fa fa-bus"></i> Bus
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <button onclick="return option_selected('school')" id="school_option" class="btn btn-outline-primary btn-block font-16">
                                                <i class="fa fa-school"></i> School
                                            </button>
                                        </div>
                                    </div>
                                    <img id="qr_code_image" src="<?= $baseUrl ?><?= $userQrCode["qrcode"] ?>" alt="User QR Code" class="w-100 h-100">
                                    <div id="processing_qr_code" class="text-primary mb-2 hidden">
                                        <i class="fa fa-spin fa-spinner"></i> Confirming QR-Code...
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button disabled onclick="return confirm_qr_code_request()" class="btn btn-primary btn-block py-3" id="qr_code_scanned">
                                        <i class="fa fa-qrcode"></i> Yes, my QR-Code has been scanned.
                                    </button>
                                    <div class="text-center mt-2">
                                        <span class="text-danger cursor" onclick="return cancel_qr_code_request()">Cancel Request</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="search_dictionary">
                        <?php if($isEmployee || $isTutor || $isAdminAccountant) { ?>
                        <div class="quick_search">
                            <?php if(!$isEmployee) { ?>
                            <div data-content="system" class="<?= $isAdminAccountant ? "col-sm-4" : "col-sm-6" ?> border-right border-white selected button">
                                Search
                            </div>
                            <?php } ?>
                            <div data-content="dictionary" class="<?= $isEmployee ? "col-sm-12" : ($isAdminAccountant ? "col-sm-4" : "col-sm-6") ?> border-right border-white button">
                                Dictionary
                            </div>
                            <?php if($isAdminAccountant) { ?>
                                <div data-content="tutorials" class="col-sm-4 border-right border-white button">
                                    Tutorials
                                </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <div class="p-15 border-bottom">
                            <?php if($isTutor || $isAdminAccountant) { ?>
                                <div class="system_content">
                                    <div class="layout-color">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <label for="system">Search for a user or receipt <small class="text-success">(hit enter to search)</small></label>
                                            </div>
                                            <div>
                                                <span onclick="return clear_quick_search_form('system')" class="cursor text-danger" title="Click to reset form">Clear</span>
                                            </div>
                                        </div>
                                        <input type="search" placeholder="Enter search term here." autocomplete="Off" name="system" id="system" class="form-control">
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
                                    <input type="search" placeholder="Enter word to search for from dictionary." autocomplete="Off" name="dictionary" id="dictionary" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2 trix-slim-scroll" style="max-height:700px;overflow-y:auto;">
                            <div class="text-center hidden" id="quick_search_loader">
                                <i class="fa fa-spin fa-spinner text-primary"></i>
                            </div>
                            <div id="dictionary_query_results"></div>
                            <div id="system_query_results"></div>
                            <div id="tutorials_query_results">
                                <div class="tutorials_content hidden">
                                    <div class="row">
                                        <?= $knowledge_base_list ?>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    <script src="<?= $baseUrl; ?>assets/js/upload.js?v=<?= version() ?>"></script>
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