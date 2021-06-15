<?php function activated_form($the_form, $client_state) { global $notReady, $formsObj, $baseUrl, $userData; ?>
<div class="card">
    <div class="card-body">
        <div class="padding-20">
            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="profile-tab2" data-toggle="tab" href="#profile" role="tab" aria-selected="true">Account Profile Setup</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" aria-disabled="true" id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">General School Setup</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="examination-tab2" data-toggle="tab" href="#examination" role="tab" aria-selected="true">Examination</a>
                </li>
                <?php  if(in_array($client_state, ["Activated", "Pending"])) { ?>
                <li class="nav-item">
                    <a class="nav-link" id="courses-tab2" data-toggle="tab" href="#courses" role="tab" aria-selected="true">Import Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">Import Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="staff-tab2" data-toggle="tab" href="#staff" role="tab" aria-selected="true">Import Staff</a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" id="complete-tab2" data-toggle="tab" href="#complete" role="tab" aria-selected="true">Complete Process</a>
                </li>
            </ul>
            <div class="tab-content tab-bordered" id="myTab3Content">
                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab2">
                    <?= $formsObj->profile_form($baseUrl, $userData); ?>
                </div>
                <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab2">
                    <?= $the_form["general"] ?? null; ?>
                </div>
                <div class="tab-pane fade" id="examination" role="tabpanel" aria-labelledby="examination-tab2">
                    <?php if($notReady) { ?>
                        <div class="alert alert-warning text-center">
                            You must first set the Academic Year and Term to proceed.
                        </div>
                    <?php } else { ?>
                        <?= $the_form["examination"] ?? null; ?>
                    <?php } ?>
                </div>
                <?php  if(in_array($client_state, ["Activated", "Pending"])) { ?>
                    <div class="tab-pane fade" id="students" role="tabpanel" aria-labelledby="students-tab2">
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
                <?php } ?>
                <div class="tab-pane fade" id="complete" role="tabpanel" aria-labelledby="complete-tab2">
                    <?php if($notReady) { ?>
                        <div class="alert alert-warning text-center">
                            You must first set the Academic Year and Term to proceed.
                        </div>
                    <?php } else { ?>
                        <div>
                            <h3 class="text-success text-center">
                                <?= in_array($client_state, ["Activated", "Pending"]) ? 
                                    "You have successfully gone through the initial setup process, proceed to complete it and begin using the system." : 
                                    "The setup process for the academic year/term is completed, proceed to complete it and begin using the system." 
                                ?>
                            </h3>
                            <div class="text-center pt-3 mt-3 border-top">
                                <button onclick="return complete_setup_process()" class="btn btn-outline-success">
                                    <?= in_array($client_state, ["Activated", "Pending"]) ? "Complete Setup Process" : "Begin Academic Year" ?>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            
            </div>
        </div>
    </div>
</div>
<?php } ?>