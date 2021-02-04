        <?php
        // load some global variables
        global $myClass, $baseUrl, $session, $userData, $current_url, $loadedJS, $auto_close_modal, $userPrefs, $announcementNotice;
        // init indexdb variable
        $idb_init = (bool) (isset($userPrefs->idb_init->init) && (strtotime($userPrefs->idb_init->idb_next_init) < time()));
        // if the force_cold_boot=1 query parameter has been parsed then set the init to true
        if(isset($_GET["force_cold_boot"]) && ($_GET["force_cold_boot"] == 1)) {
            $idb_init = true;
        }
        ?>
        <footer class="main-footer">
            <div class="footer-left">
            Copyright &copy; <?= date("Y") ?> <div class="bullet"></div> <a href="<?= $baseUrl; ?>"><?= $appName ?></a>. All rights reserved
            </div>
            <div class="footer-right"></div>
        </footer>
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
        baseUrl = "<?= $baseUrl ?>",current_url="<?= $user_current_url ?>",
        viewedAs = "<?= (bool) isset($_GET["viewas"]) ?>",
        this_user_unique_key = "persist:msgh-client-<?= $session->userId; ?>",
        form_modules = <?= json_encode($myClass->form_modules); ?>,
        <?= isset($companyData->awards) ? "company_awards_array=".json_encode($companyData->awards)."," : ""; ?>
        <?= isset($companyData->managers) ? "company_managers_array=".json_encode($companyData->managers)."," : ""; ?>
        <?php if(isset($myClass->formPreloader) && in_array($SITEURL[0], ["policy-view", "policy-claim-form"])) { ?>formPreloader = <?= json_encode($myClass->formPreloader); ?>,<?php } ?>
        $myPrefs = <?= json_encode($userPrefs); ?>;
    </script>

    <!-- General JS Scripts -->
    <script src="<?= $baseUrl; ?>assets/js/app.min.js"></script>
    <!-- JS Libraies -->
    <script src="<?= $baseUrl; ?>assets/bundles/chartjs/chart.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/apexcharts/apexcharts.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/select2/select2.js"></script>

    <script src="<?= $baseUrl; ?>assets/bundles/sweetalert/sweetalert.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/datatables/datatables.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/inputmask/jquery.inputmask.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/magnify.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/notify.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/trix/trix.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="<?= $baseUrl; ?>assets/bundles/fullcalendar/fullcalendar.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/js/scripts.js"></script>

    <script src="<?= $baseUrl; ?>assets/vendors/timetable/ui.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/ui-touch-punch.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/chosen.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/form.js"></script>
    <script src="<?= $baseUrl; ?>assets/vendors/timetable/grid.js"></script>
    
    <script src="<?= $baseUrl; ?>assets/js/myschoolgh.js"></script>
    <?php if($isAdminAccountant) { ?>
    <!-- <script src="<?= $baseUrl; ?>assets/js/calculator.js"></script> -->
    <?php } ?>
    <script src="<?= $baseUrl; ?>assets/js/app.js"></script>

    <?php if(isset($formToShow)) { ?>
    <script>fieldDefault = <?= json_encode($formToShow) ?>, thisSelectRow = <?= $formData["thisSelectRow"] ?>, thisRowId = <?= $formData["thisRowId"] ?>;</script>
    <?php } ?>
    <?php foreach($loadedJS as $eachJS) { ?>
        <script src="<?= $baseUrl; ?><?= $eachJS ?>"></script>
    <?php } ?>
    <script>
        $(() => {
            <?php if(isset($verify_payment)) { ?>
            verify_payment();
            <?php } ?>
            <?php if(!empty($session->tempProfilePicture)) { ?>
            save_profile_picture();
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