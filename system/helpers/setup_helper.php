<?php function activated_form($the_form, $client_state, $isActiveAccount = false) { 
    global $notReady, $formsObj, $baseUrl, $userData, $SITEURL;

    $formsObj->isActiveAccount = $isActiveAccount;
    $urlLink = $SITEURL[1] ?? 'profile';
?>
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <!-- Modern Tab Navigation -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
        <div class="px-6 py-4">
            <ul class="nav nav-tabs setup-tab-nav border-0 bg-transparent" id="myTab2" role="tablist">
                <?php if(in_array($client_state, ["Activated", "Pending"])) { ?>
                <li class="nav-item">
                    <a onclick="return appendToUrl('profile')" class="<?= $urlLink == 'profile' ? 'active' : '' ?> nav-link border-0 px-6 py-3 text-gray-700 font-semibold text-sm transition-all duration-200 hover:shadow-md" 
                       id="profile-tab2" data-toggle="tab" href="#profile" role="tab" aria-selected="true">
                        <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                        <strong>User Profile</strong>
                    </a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a onclick="return appendToUrl('general')" class="<?= $urlLink == 'general' ? 'active' : '' ?> nav-link <?= !in_array($client_state, ["Activated", "Pending"]) ? "active bg-white shadow-sm" : "bg-transparent" ?> border-0 px-6 py-3 text-gray-700 font-semibold text-sm transition-all duration-200 hover:shadow-md" 
                       id="general-tab2" data-toggle="tab" href="#general" role="tab" aria-selected="true">
                        <i class="fas fa-school mr-2 text-green-600"></i>
                        <strong>School Profile</strong>
                    </a>
                </li>
                <li class="nav-item">
                    <a onclick="return appendToUrl('academic')" class="<?= $urlLink == 'academic' ? 'active' : '' ?> nav-link border-0 px-6 py-3 text-gray-700 font-semibold text-sm transition-all duration-200 hover:shadow-md" 
                       id="academic_calendar-tab2" data-toggle="tab" href="#academic_calendar" role="tab" aria-selected="true">
                        <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                        <strong>Academic Calendar</strong>
                    </a>
                </li>
                <?php if($isActiveAccount) { ?>
                    <li class="nav-item">
                        <a onclick="return appendToUrl('examination')" class="<?= $urlLink == 'examination' ? 'active' : '' ?> nav-link border-0 px-6 py-3 text-gray-700 font-semibold text-sm transition-all duration-200 hover:shadow-md" 
                           id="examination-tab2" data-toggle="tab" href="#examination" role="tab" aria-selected="true">
                            <i class="fas fa-chart-line mr-2 text-orange-600"></i>
                            <strong>Grading System</strong>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a onclick="return appendToUrl('results')" class="<?= $urlLink == 'results' ? 'active' : '' ?> nav-link border-0 px-6 py-3 text-gray-700 font-semibold text-sm transition-all duration-200 hover:shadow-md" 
                           id="results_structure-tab2" data-toggle="tab" href="#results_structure" role="tab" aria-selected="true">
                            <i class="fas fa-clipboard-list mr-2 text-indigo-600"></i>
                            <strong>Results Structure</strong>
                        </a>
                    </li>
                <?php } ?>
                <?php  if(in_array($client_state, ["Activated", "Pending"])) { ?>
                <li class="nav-item">
                    <a onclick="return appendToUrl('students')" class="<?= $urlLink == 'students' ? 'active' : '' ?> nav-link border-0 px-6 py-3 text-gray-700 font-semibold text-sm transition-all duration-200 hover:shadow-md" 
                       id="students-tab2" data-toggle="tab" href="#students" role="tab" aria-selected="true">
                        <i class="fas fa-users mr-2 text-teal-600"></i>
                        <strong>Import Students</strong>
                    </a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a onclick="return appendToUrl('complete')" class="<?= $urlLink == 'complete' ? 'active' : '' ?> nav-link border-0 px-6 py-3 text-gray-700 font-semibold text-sm transition-all duration-200 hover:shadow-md" 
                       id="complete-tab2" data-toggle="tab" href="#complete" role="tab" aria-selected="true">
                        <i class="fas fa-check-circle mr-2 text-green-600"></i>
                        <strong>Complete</strong>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="myTab3Content">
        <?php  if(in_array($client_state, ["Activated", "Pending"])) { ?>
        <div class="tab-pane fade <?= $urlLink == 'profile' ? 'show active' : '' ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab2">
            <div class="p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">User Profile Setup</h3>
                    <p class="text-gray-600">Let's start by setting up your personal profile information.</p>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 setup-info-card">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mr-4 setup-icon">
                            <i class="fas fa-info-circle text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Why this matters</h4>
                            <p class="text-sm text-gray-600">Your profile information helps personalize your experience and ensures proper account management.</p>
                        </div>
                    </div>
                </div>
                <?= $formsObj->profile_form($baseUrl, $userData); ?>
            </div>
        </div>
        <?php } ?>
        
        <div class="tab-pane fade <?= $urlLink == 'general' ? 'show active' : '' ?> <?= !in_array($client_state, ["Activated", "Pending"]) ? "show active" : null ?>" id="general" role="tabpanel" aria-labelledby="general-tab2">
            <div class="p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">School Profile Configuration</h3>
                    <p class="text-gray-600">Configure your school's basic information and settings.</p>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 mb-6 setup-info-card">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mr-4 setup-icon">
                            <i class="fas fa-lightbulb text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Pro tip</h4>
                            <p class="text-sm text-gray-600">This information will be used throughout the system for reports, communications, and official documents.</p>
                        </div>
                    </div>
                </div>
                <?= $the_form["general"] ?? null; ?>
            </div>
        </div>
        
        <div class="tab-pane fade <?= $urlLink == 'academic' ? 'show active' : '' ?>" id="academic_calendar" role="tabpanel" aria-labelledby="academic_calendar-tab2">
            <div class="p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Academic Calendar Setup</h3>
                    <p class="text-gray-600">Define your academic year, terms, and important dates.</p>
                </div>
                <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-6 mb-6 setup-info-card">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mr-4 setup-icon">
                            <i class="fas fa-calendar-check text-white text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Important</h4>
                            <p class="text-sm text-gray-600">This step is required before you can proceed with grading systems and student imports.</p>
                        </div>
                    </div>
                </div>
                <?= $the_form["calendar"] ?? null; ?>
            </div>
        </div>
        
        <?php if($isActiveAccount) { ?>
            <div class="tab-pane fade <?= $urlLink == 'examination' ? 'show active' : '' ?>" id="examination" role="tabpanel" aria-labelledby="examination-tab2">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Grading System Configuration</h3>
                        <p class="text-gray-600">Set up your school's grading scale and assessment criteria.</p>
                    </div>
                    <?php if($notReady) { ?>
                        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-6 border-l-4 border-yellow-400 setup-warning-card">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mr-4 setup-icon">
                                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Setup Required</h4>
                                    <p class="text-sm text-gray-600">You must first set the Academic Year and Term to proceed with grading system configuration.</p>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-6 mb-6 setup-info-card">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-orange-600 rounded-full flex items-center justify-center mr-4 setup-icon">
                                    <i class="fas fa-chart-bar text-white text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Grading System</h4>
                                    <p class="text-sm text-gray-600">Configure how grades are calculated and displayed for your students.</p>
                                </div>
                            </div>
                        </div>
                        <?= $the_form["examination"] ?? null; ?>
                    <?php } ?>
                </div>
            </div> 
            
            <div class="tab-pane fade <?= $urlLink == 'results' ? 'show active' : '' ?>" id="results_structure" role="tabpanel" aria-labelledby="results_structure-tab2">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Results Structure Setup</h3>
                        <p class="text-gray-600">Define how student results and reports are structured.</p>
                    </div>
                    <?php if($notReady) { ?>
                        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-6 border-l-4 border-yellow-400 setup-warning-card">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mr-4 setup-icon">
                                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Setup Required</h4>
                                    <p class="text-sm text-gray-600">You must first set the Academic Year and Term to proceed with results structure configuration.</p>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-xl p-6 mb-6 setup-info-card">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center mr-4 setup-icon">
                                    <i class="fas fa-clipboard-check text-white text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Results Structure</h4>
                                    <p class="text-sm text-gray-600">Configure how student results are organized and presented in reports.</p>
                                </div>
                            </div>
                        </div>
                        <?= $the_form["results_structure"] ?? null; ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        
        <?php  if(in_array($client_state, ["Activated", "Pending"])) { ?>
            <div class="tab-pane fade <?= $urlLink == 'students' ? 'show active' : '' ?>" id="students" role="tabpanel" aria-labelledby="students-tab2">
                <div class="p-8 pt-2">
                    <?php if($notReady) { ?>
                        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-6 border-l-4 border-yellow-400 setup-warning-card">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mr-4 setup-icon">
                                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Setup Required</h4>
                                    <p class="text-sm text-gray-600">You must first set the Academic Year and Term to proceed with student data import.</p>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <?= $the_form["student"] ?? null; ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div class="tab-pane fade <?= $urlLink == 'complete' ? 'show active' : '' ?>" id="complete" role="tabpanel" aria-labelledby="complete-tab2">
            <div class="p-8">
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Setup Complete</h3>
                    <p class="text-gray-600">You're almost ready to start using your school management system!</p>
                </div>
                
                <?php if($notReady) { ?>
                    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-6 border-l-4 border-yellow-400 setup-warning-card">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mr-4 setup-icon">
                                <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Setup Required</h4>
                                <p class="text-sm text-gray-600">You must first set the Academic Year and Term to proceed.</p>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-8 text-center setup-success-card">
                        <div class="mb-6">
                            <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4 setup-icon">
                                <i class="fas fa-check text-white text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-green-800 mb-2">
                                <?= in_array($client_state, ["Activated", "Pending"]) ? 
                                    "Setup Process Complete!" : 
                                    "Academic Setup Complete!" 
                                ?>
                            </h3>
                            <p class="text-green-700 text-lg">
                                <?= in_array($client_state, ["Activated", "Pending"]) ? 
                                    "You have successfully completed the initial setup process. You're now ready to begin using the system!" : 
                                    "The setup process for the academic year/term is completed. You're ready to start managing your school!" 
                                ?>
                            </p>
                        </div>
                        
                        <div class="bg-white rounded-xl p-6 mb-6">
                            <h4 class="font-semibold text-gray-800 mb-4">What's Next?</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-users text-blue-600"></i>
                                    </div>
                                    <p class="text-gray-600">Manage Students</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-chalkboard-teacher text-green-600"></i>
                                    </div>
                                    <p class="text-gray-600">Assign Teachers</p>
                                </div>
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-chart-line text-purple-600"></i>
                                    </div>
                                    <p class="text-gray-600">Track Progress</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button onclick="return complete_setup_process()" 
                                    class="setup-btn-success">
                                <i class="fas fa-rocket mr-2"></i>
                                <?= in_array($client_state, ["Activated", "Pending"]) ? "Complete Setup Process" : "Begin Academic Year & Term" ?>
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>