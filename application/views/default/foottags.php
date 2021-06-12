        <?php
        // load some global variables
        global $myClass, $baseUrl, $session, $userData, $current_url, $loadedJS, $auto_close_modal, $userPrefs, $announcementNotice;
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
                Copyright &copy; <?= date("Y") ?> <div class="bullet"></div> <a href="<?= $baseUrl; ?>"><?= $appName ?></a>. All rights reserved
            </div>
            <div class="footer-right">
                <div class="network-notifier"></div>
            </div>
        </footer>

        <div class="settingSidebar">
          <a href="#" class="settingPanelToggle"> <i class="fa fa-book-reader"></i></a>
            <div class="settingSidebar-body ps ps-theme-default">
                <div class=" fade show active">
                    <div class="setting-panel-header"><h6 class="font-medium m-b-0">Onboard Dictionary</h6></div>
                    <div class="p-15 border-bottom">
                        <div class="layout-color">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <label for="dictionary">Search for a Word <small class="text-success">(hit enter to search)</small></label>
                                </div>
                                <div>
                                    <span onclick="return clear_dictionary_form()" class="cursor text-danger" title="Click to reset form">Clear</span>
                                </div>
                            </div>
                            <input type="text" name="dictionary" id="dictionary" class="form-control">
                        </div>
                    </div>
                    <div class="card-body pt-2 trix-slim-scroll" style="max-height:500px;overflow-y:auto;">
                        <div class="text-center hidden" id="dictionary_loader"><i class="fa fa-spin fa-spinner text-primary"></i></div>
                        <div id="dictionary_search_term"></div>
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
                    <button class="btn btn-outline-danger" data-dismiss="modal">Close</button>
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
    var fieldDefault = {}, thisRowId = 1, thisSelectRow = 1, userAgent = "<?= $myClass->agent."||".$myClass->platform."||".$myClass->browser."||".ip_address(); ?>",
        baseUrl = "<?= $baseUrl ?>",current_url="<?= $user_current_url ?>", sms_text_count = <?= $myClass->sms_text_count; ?>,
        viewedAs = "<?= (bool) isset($_GET["viewas"]) ?>",
        this_user_unique_key = "persist:msgh-client-<?= $session->userId; ?>",
        form_modules = <?= json_encode($myClass->form_modules); ?>,
        swalnotice = <?= json_encode($myClass->swal_notification); ?>,
        $myPrefs = <?= json_encode($userPrefs); ?>;
        var acceptedArray = new Array();
            <?php foreach($accountObj->accepted_column as $key => $values) { ?>
            acceptedArray["<?= $key ?>"] = <?php $values = array_values($accountObj->accepted_column[$key]); print "[\"". implode("\",\"", $values) ."\"]"; ?>, 
            <?php } ?>
            current_column = "";
    </script>

    <script src="<?= $baseUrl; ?>assets/js/app.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/chartjs/chart.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/apexcharts/apexcharts.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/select2/select2.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/sweetalert/sweetalert.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/datatables/datatables.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/inputmask/jquery.inputmask.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/magnify.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/notify.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/trix/trix.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/bootstrap-datepicker/datepicker.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/fullcalendar/fullcalendar.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/scripts.js"></script>
    <script src="https://js.paystack.co/v2/inline.js"></script>
    <?php if($isActiveAccount) { ?>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/ui.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/ui-touch-punch.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/chosen.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/form.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/grid.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/myschoolgh.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/app.js"></script>
    <?php } else { ?>
    <script src="<?= $baseUrl; ?>assets/js/setup.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/grading.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/import.js"></script>
    <?php } ?>
    <?php if(isset($formToShow)) { ?>
    <script>fieldDefault = <?= json_encode($formToShow) ?>, thisSelectRow = <?= $formData["thisSelectRow"] ?>, thisRowId = <?= $formData["thisRowId"] ?>;</script>
    <?php } ?>
    <?php foreach($loadedJS as $eachJS) { ?>
        <script src="<?= $baseUrl; ?><?= $eachJS ?>"></script>
    <?php } ?>
    <script>
    <?php if(!$isActiveAccount) { ?>
        var logout = async() => {
            await $.post(`${baseUrl}api/auth/logout`).then((resp) => {
                if (resp.result.code == 200) {
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
            <?php if(isset($verify_payment)) { ?>
            verify_payment();
            <?php } ?>
            <?php if(!empty($announcementNotice)) { ?>
                <?= $announcementNotice->modal_function_script; ?>
                $(`div[class~="announcementModal_<?= $announcementNotice->item_id ?>"]`).modal("show");
                <?= "{$announcementNotice->modal_function}();" ?>
            <?php } ?>
            <?php if($idb_init) { ?>
            setTimeout(() => { $(`button[id="idb_init"]`).trigger("click"); }, 1000);
            <?php } ?>
            <?php if(isset($_GET["end_id"]) && preg_match("/^[a-z0-9]+$/", $_GET["end_id"])) { ?>
            $(`button[data-function="update"][data-item="<?= $_GET["end_id"] ?>"]`).trigger("click");
            <?php } ?>
        });
    </script>
</body>
</html>