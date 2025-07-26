<?php
// global variables
global $usersClass, $accessObject, $myClass, $isSupport, $defaultClientData, $isSupportPreviewMode,
    $clientPrefs, $isParent, $defaultUser, $clientFeatures, $isReadOnly, $academicSession;

// base url
$baseUrl = $myClass->baseUrl;
$appName = $myClass->appName;

// confirm that user id has been parsed
$clientId = $session->clientId;
$loggedUserId = $session->userId;
$cur_user_id = $SITEURL[1] ?? $loggedUserId;

// get the user data
$userData = $defaultUser;

// if the user is not loggedin then show the login form
if(!loggedIn()) { require "login.php"; exit(-1); }

// clientdata
$clientData = $defaultClientData;
$clientPrefs = $clientData->client_preferences;

$clientName = $clientData->client_name;

// confirm that the account is active
$isActiveAccount = (bool) ($clientData->client_state === "Active");
$isSuspendedAccount = (bool) (in_array($clientData->client_state, ["Suspended", "Expired", "Propagation"]));

// get the variables for the accessobject
$accessObject->clientId = $clientId;
$accessObject->userId = $loggedUserId;
$accessObject->userPermits = $userData->user_permissions;

$userPrefs = (object) [];
$userPrefs->userId = $loggedUserId;
$userPrefs->user_image = $userData->image;

// user sidebar preference
$sidebar_pref = $userPrefs->sidebar_nav ?? null;
$theme_color = $userPrefs->theme_color ?? null;

// auto close modal options
$auto_close_modal = (!isset($userPrefs->auto_close_modal) || (isset($userPrefs->auto_close_modal) && ($userPrefs->auto_close_modal == "allow"))) ? false : true;

// user notifications
$userNotifications = [];

// set the current url in session
$user_current_url = $session->user_current_url;

// user payment preference
$userPrefs->payments = isset($userPrefs->payments) ? $userPrefs->payments : (object) [];
$userPrefs->payments->default_payment = isset($userPrefs->payments->default_payment) ? $userPrefs->payments->default_payment : null;

// chat preferences
if(!isset($userPrefs->messages)) {
    $userPrefs->messages = (object)[
        "enter_to_send" => 1,
        "hide_online" => 0,
        "status" => ""
    ];
}

// check if the current academic year and term has been logged as completed
$_academic_check = $myClass->pushQuery("academic_year, academic_term", "clients_terminal_log", "client_id='{$clientId}' LIMIT 50");

// if the user has the permission to end the academic term
$endPermission = $accessObject->hasAccess("close", "settings");
$changePassword = $accessObject->hasAccess("change_password", "permissions");

// load the helper
load_helpers(['menu_helper']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $page_title ?? "Dashboard" ?> :: <?= $clientName ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css?v=<?= version() ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css?v=<?= version() ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/gallery.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/datatables/datatables.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-datepicker/datepicker.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendors/trix/trix.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/select2/select2.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css?v=<?= version() ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/calendar.css?v=<?= version() ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/table.css?v=<?= version() ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/chosen.css?v=<?= version() ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/calculator.css?v=<?= version() ?>">    
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/fullcalendar/fullcalendar.min.css">
    <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
    <link rel="apple-touch-icon" href="<?= $baseUrl ?>assets/img/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="theme-color" content="#2196F3">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="App - <?= $appName ?>">
    <?php foreach($loadedCSS as $eachCSS) { ?>
        <link rel="stylesheet" href="<?= $baseUrl ?><?= $eachCSS ?>">
    <?php } ?>
    <link id="user_current_url" name="user_current_url" value="<?= $user_current_url ?>">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/clients/<?= $clientData->client_id ?>.css">
    <script>
        var myUName = "<?= $session->userName ?>",
            academicSession = "<?= $academicSession; ?>",
            myPrefs = <?= json_encode($userData->client->client_preferences) ?>;
    </script>
    <?= $myClass->google_analytics_code ?>
</head>
<body class="bg-gradient-to-br from-slate-50 to-indigo-100 via-blue-50">
	<div class="loader"></div>
    <input name="minimum_date" hidden type="hidden" disabled value="<?= date("Y-m-d", strtotime("today -100 years")) ?>">
    <input type="hidden" hidden id="todays_date" disabled value="<?= date("Y-m-d") ?>">
    <div class="last_visited_page" value="<?= $userData->last_visited_page ?>"></div>
    <?php if(!empty($isAdminAccountant)) { ?>
    <section class="container-parent">
        <div class="container__child">
            <div class="calculator">
                <div class="hidden display">
                    <div class="calculator-display">
                        <output class="user-input" type="text" id="user-input">0</output>	
                        <output class="result" type="text" id="result">&nbsp;</output>	
                    </div>					
                </div>
                <div class="hidden all-buttons">
                    <button class="allclear" data-type="reset" id="allclear" >AC</button>
                    <button class="clear" data-type="backspace" id="clear" >C</button>
                    <button class="operators" data-type="operator" id="remainder" value="%" >&#37</button>
                    <button class="operators" data-type="operator" id="divide" value="/" >&#247</button>
                    <button class="digits" data-type="number" id="digit-7" value="7" >7</button>
                    <button class="digits" data-type="number" id="digit-8" value="8" >8</button>
                    <button class="digits" data-type="number" id="digit-9" value="9" >9</button>
                    <button class="operators" data-type="operator" id="plus" value="+" >&#43</button>
                    <button class="digits" data-type="number" id="digit-4" value="4" >4</button>
                    <button class="digits" data-type="number" id="digit-5" value="5" >5</button>
                    <button class="digits" data-type="number" id="digit-6" value="6" >6</button>
                    <button class="operators" data-type="operator" id="minus" value="-" >&#45</button>
                    <button class="digits" data-type="number" id="digit-1" value="1" >1</button>
                    <button class="digits" data-type="number" id="digit-2" value="2" >2</button>
                    <button class="digits" data-type="number" id="digit-3" value="3" >3</button>
                    <button class="operators" data-type="operator" id="multiply" value="x" >&#215</button>
                    <button class="digits" data-type="number" id="digit-0" value="0" >0</button>
                    <button class="decimal" data-type="decimal" id="decimal" value="." >.</button>
                    <button class="equals" data-type="equal" id="equals" value="=" >&#61</button>
                </div>
                <div class="toggle-calculator hidden">Show Calculator</div>
            </div>
        </div>
    </section>
    <?php } ?>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg bg-gradient-to-r fixed top-0 left-0 right-0 z-200 from-purple-900 via-blue-900 to-indigo-900 text-white">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-pink-400 to-purple-400 opacity-20 rounded-full -translate-y-16 translate-x-16 animate-pulse"></div>
            </div>
            <div class="progress-bar"></div>
            <nav class="navbar navbar-expand-lg main-navbar fixed">
                <div class="form-inline mr-auto">
                    <ul class="mb-3 navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" title="Hide/Display the Side Menubar" class="nav-link mt-2 nav-link-lg collapse-btn"><i class="fas fa-bars"></i></a></li>
                        <!-- <li><a href="#" class="nav-link nav-link-lg mt-2 fullscreen-btn" title="Maximize to Fullscreen Mode"><i class="fas fa-expand"></i></a></li> -->
                        <li><a href="#" class="nav-link nav-link-lg mt-2 hidden" id="history-refresh" title="Reload Page"><i class="fas fa-redo-alt"></i></a></li>
                        <?php if($isActiveAccount) { ?>
                        <li class="border-left text-white d-none d-md-block">
                            <?php if(!$isSupport) { ?>
                            <a class="nav-link text-white nav-link-lg mt-1">
                                <strong class="font-18px">
                                    <span><?= $clientPrefs->academics->academic_year ?></span> 
                                    <span>|</span>
                                    <span class="text-uppercase"><?= $clientPrefs->academics->academic_term ?> <?= $academicSession; ?></span>
                                    <?= ($endPermission && isset($defaultUser->appPrefs) && !empty($defaultUser->appPrefs->termEnded) ? 
                                        "<span class='badge badge-danger notification cursor' title='This academic year and term has been closed and forwarded to the next academic year and term.'>Term Ended</span>" : 
                                        ($endPermission ? "<span class='badge badge-success'>Active</span>" : null)); ?>
                                    <br><span class="font-weight-light font-17 text-uppercase"><?= $defaultUser->name; ?> / <?= ucwords($defaultUser->user_type); ?></span>
                                </strong>
                            </a>
                            <?php } else { ?>
                                <a href="#" class="nav-link mt-2 text-white nav-link-lg">
                                    <strong class="font-20px"><?= $clientName ?> - SUPPORT PANEL</strong>
                                </a>
                            <?php } ?>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <ul class="navbar-nav navbar-right items-center">
                <?php if($isActiveAccount) { ?>
                    <li class="dropdown dropdown-list-toggle"><a title="Notifications List" href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"><i class="far fa-bell"></i></a>
                        <div class="dropdown-menu dropdown-list dropdown-menu-right">
                            <div class="dropdown-header">Notifications
                                <div class="float-right mark_all_as_read">
                                    <span onclick="return mark_all_notification_as_read()" class="underline text-blue">Mark All As Read</span>
                                </div>
                            </div>
                            <div id="notifications_list" data-user_id="<?= $loggedUserId ?>" class="dropdown-list-content dropdown-list-icons"></div>
                        </div>
                    </li>
                    <?php
                    // show this section if the record is not empty
                    if(!empty($_academic_check) && $isAdmin) {
                    ?>
                    <li hidden class="dropdown switch-academic_year dropdown-list-toggle"><a href="#" title="Switch Academic Year/Term" data-toggle="dropdown" class="nav-link academic_years-toggle nav-link-lg"><i class="far fa-calendar"></i></a>
                        <div class="dropdown-menu dropdown-list dropdown-menu-right" style="width:250px">
                            <div class="dropdown-header mb-0 pb-0">Academic Years List</div>
                            <div class="dropdown-list-content pt-0 slim-scroll" style="overflow-y:auto">
                                <?php
                                // loop through the academic years and term
                                foreach($_academic_check as $_acc_years) {
                                ?>
                                    <div class="p-2 pl-3 border">
                                        <a href="#" onclick="return set_academic_year_term('<?= $_acc_years->academic_year; ?>','<?= $_acc_years->academic_term; ?>');" class="user_name">
                                            <?= $_acc_years->academic_year; ?>: <?= $_acc_years->academic_term; ?> <?= $academicSession; ?>
                                        </a>
                                        <?= ("{$_acc_years->academic_year}_{$_acc_years->academic_term}" == "{$session->is_readonly_academic_year}_{$session->is_readonly_academic_term}") ? "<i class='fa text-success fa-check-circle'></i>" : null; ?>
                                    </div>
                                <?php
                                }
                                ?>
                                <?php if(!empty($session->is_only_readable_app)) { ?>
                                    <div class="exit_review" onclick="return set_academic_year_term('revert','revert');">EXIT REVIEW MODE</div>
                                <?php } ?>
                            </div>
                        </div>
                    </li>
                    <?php } ?>
                    <?php if($accessObject->hasAccess("manage", "settings") && !$isSupport) { ?>
                        <li class="dropdown dropdown-list-toggle">
                            <a title="Account Settings" href="<?= $baseUrl ?>settings" class="nav-link nav-link-lg"><i class="fa fa-cog"></i></a>
                        </li>
                    <?php } ?>
                    <?php if($accessObject->hasAccess("support", "settings")) { ?>
                    <li class="dropdown dropdown-list-toggle">
                        <a title="Support Tickets List" href="<?= $baseUrl ?>support" class="nav-link nav-link-lg"><i class="fa fa-user-cog"></i></a>
                    </li>
                    <?php } ?>
                <?php } ?>
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown"
                        class="nav-link dropdown-toggle nav-link-lg nav-link-user flex items-center">
                        <img alt="image" src="<?= $baseUrl ?><?= $userData->image ?>" class="user-img-radious-style">
                        <span class="d-sm-none d-lg-inline-block"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right mt-1">
                    <div class="dropdown-title"><?= !empty($userData->name) ? $userData->name : $clientData->client_name ?></div>
                    <?php if($isActiveAccount) { ?>
                        <a href="<?= $baseUrl ?>profile" class="dropdown-item has-icon hover:bg-gradient-to-br hover:from-purple-500 hover:via-purple-600 hover:to-blue-600 hover:text-white">
                            <i class="far fa-user"></i> Profile
                        </a>
                        <?php if($accessObject->hasAccess("activities", "settings")) { ?>
                        <a href="<?= $baseUrl ?>timeline" class="dropdown-item has-icon hover:bg-gradient-to-br hover:from-purple-500 hover:via-purple-600 hover:to-blue-600 hover:text-white">
                            <i class="fas fa-align-left"></i> Activities
                        </a>
                        <?php } ?>
                        <?php if($accessObject->hasAccess("login_history", "settings")) { ?>
                        <a href="<?= $baseUrl ?>login-history" class="dropdown-item has-icon hover:bg-gradient-to-br hover:from-purple-500 hover:via-purple-600 hover:to-blue-600 hover:text-white">
                            <i class="fas fa-lock"></i> Login History
                        </a>
                        <?php } ?>
                        <?php if($accessObject->hasAccess("manage", "settings") && !$isSupport) { ?>
                        <a href="<?= $baseUrl ?>settings" class="dropdown-item has-icon hover:bg-gradient-to-br hover:from-purple-500 hover:via-purple-600 hover:to-blue-600 hover:text-white">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <?php } ?>
                        <?php if($changePassword) { ?>
                        <a href="<?= $baseUrl ?>password_manager" class="dropdown-item has-icon hover:bg-gradient-to-br hover:from-purple-500 hover:via-purple-600 hover:to-blue-600 hover:text-white">
                            <i class="fas fa-key"></i> Password Manager
                        </a>
                        <?php } ?>
                    <?php } ?>
                    <a title="Knowledge Base" href="<?= $baseUrl ?>knowledgebase" class="dropdown-item has-icon hover:bg-gradient-to-br hover:from-purple-500 hover:via-purple-600 hover:to-blue-600 hover:text-white">
                        <i class="fa fa-book-open"></i> Knowledge Base
                    </a>
                    <?php if($accessObject->hasAccess("manage", "settings") && !$isSupport) { ?>
                        <a href="<?= $baseUrl ?>schools" class="dropdown-item has-icon hover:bg-gradient-to-br hover:from-purple-500 hover:via-purple-600 hover:to-blue-600 hover:text-white">
                            <i class="fas fa-wrench"></i> <span class="mr-3">Account Setup</span> 
                            <?= $endPermission && isset($defaultUser->appPrefs) && !empty($defaultUser->appPrefs->termEnded) ? '<span class="notification beep"></span>' : null ?>
                        </a>
                    <?php } ?>
                    <div class="dropdown-divider"></div>
                    <a href="#" onclick="return logout()" class="dropdown-item anchor has-icon text-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    </div>
                </li>
                </ul>
            </nav>
            <?php if(!empty($session->is_readonly_academic_year)) { ?>
                <div class="review-note">
                    YOU ARE REVIEWING <strong><?= $session->is_readonly_academic_term; ?> <?= $academicSession; ?></strong> OF 
                    <strong><?= $session->is_readonly_academic_year; ?></strong>
                </div>
            <?php } ?>
            <div class="main-sidebar sidebar-style-2 sidebar-bg">
                <aside id="sidebar-wrapper">

                    <div class="sidebar-brand">
                        <a href="<?= $baseUrl ?>dashboard" class="anchor justify-content-center flex items-center">
                            <img alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo mr-2" />
                            <span class="logo-name"><?= $appName ?></span>
                        </a>
                    </div>
                    <ul class="sidebar-menu <?= !in_array("live_chat", $clientFeatures) ? "mb-5" : null; ?>">
                        <li class="menu-header">Main</li>
                        <li><a id="dashboard-menu" href="<?= $baseUrl ?>dashboard" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                        <?php 
                        // set the menu function 
                        $menu_function = $isSupport ? "support_menu" : ($isSuspendedAccount ? "help_menu" : $userData->user_type."_menu");
                        if(!$isSuspendedAccount) {
                            $menu_function = $isSupportPreviewMode ? "admin_menu" : $menu_function;
                        }

                        // confirm that the function exists
                        if(function_exists($menu_function)) {
                            // load the function
                            $menu_function();
                        }
                        ?>
                        <?php if($isSchool && $isActiveAccount && in_array("live_chat", $clientFeatures)) { ?>
                        <li class="mb-5"><a href="<?= $baseUrl ?>chat" class="nav-link"><i class="fas fa-envelope-open-text"></i><span>Live Chat</span></a></li>
                        <?php } ?>
                    </ul>
                </aside>
            </div>