<?php
// global variables
global $usersClass, $accessObject, $myClass, $isSchool, $defaultClientData, $clientPrefs;

// base url
$baseUrl = config_item("base_url");
$appName = config_item("site_name");

// confirm that user id has been parsed
$clientId = $session->clientId;
$loggedUserId = $session->userId;
$cur_user_id = $SITEURL[1] ?? $loggedUserId;

// get the user data
$userData = $defaultUser;

// if the user is not loggedin then show the login form
if(!loggedIn()) { require "login.php"; exit(-1); }

// clientdata
$clientData = $myClass->client_data($clientId);
$clientPrefs = $clientData->client_preferences;

$clientName = $clientData->client_name;

// confirm that the account is active
$isActiveAccount = (bool) ($clientData->client_state === "Active");

// get the variables for the accessobject
$accessObject->clientId = $clientId;
$accessObject->userId = $loggedUserId;
$accessObject->userPermits = $userData->user_permissions;

$userPrefs = $userData->preferences;
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
    <title><?= $page_title ?? "Dashboard" ?> : <?= $clientName ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/components.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/gallery.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/datatables/datatables.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/bootstrap-datepicker/datepicker.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendors/trix/trix.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/select2/select2.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/custom.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/table.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/chosen.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/calculator.css">    
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/bundles/fullcalendar/fullcalendar.min.css">
    <link rel='shortcut icon' type='image/x-icon' href='<?= $baseUrl ?>assets/img/favicon.ico' />
    <?php foreach($loadedCSS as $eachCSS) { ?>
        <link rel="stylesheet" href="<?= $baseUrl ?><?= $eachCSS ?>">
    <?php } ?>
    <link id="user_current_url" name="user_current_url" value="<?= $user_current_url ?>">
    <script>
        var myUName = "<?= $session->userName ?>",
            myPrefs = <?= json_encode($userData->client->client_preferences) ?>;
    </script>
</head>
<body class="<?= $sidebar_pref ?> <?=  $theme_color ?> bg">
	<div class="loader"></div>
    <input type="hidden" id="todays_date" disabled value="<?= date("Y-m-d") ?>">
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
            <div class="navbar-bg"></div>
            <div class="progress-bar"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <div class="form-inline mr-auto">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" data-rel="tooltip" title="Hide/Display the Side Menubar" class="nav-link nav-link-lg collapse-btn"><i class="fas fa-bars"></i></a></li>
                    <li><a href="#" class="nav-link nav-link-lg fullscreen-btn" data-rel="tooltip" title="Maximize to Fullscreen Mode"><i class="fas fa-expand"></i></a></li>
                    <li><a href="#" class="nav-link nav-link-lg hidden" data-rel="tooltip" id="history-refresh" title="Reload Page"><i class="fas fa-redo-alt"></i></a></li>
                    <?php if($isActiveAccount) { ?>
                    <li class="border-left text-white d-none d-md-block">
                        <?php if(!$isSupport) { ?>
                        <a href="#" class="nav-link text-white nav-link-lg">
                            Academic Year/Term:
                            <strong class="font-18px">
                                <span><?= $clientPrefs->academics->academic_year ?></span> 
                                <span>|</span>
                                <span><?= $clientPrefs->academics->academic_term ?> Term</span>
                            </strong>
                            <?= ($endPermission && $defaultUser->appPrefs->termEnded ? "<span class='badge badge-danger notification'>Already Ended</span>" : "<span class='badge badge-success'>Active</span>"); ?>
                        </a>
                        <?php } ?>
                    </li>
                    <?php } ?>
                </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                <?php if(!empty($session->student_id)) { ?>
                <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" title="Wards List" data-toggle="tooltip" class="nav-link nav-link-lg"><i class="fa fa-users"></i></a>
                    <div class="dropdown-menu dropdown-list dropdown-menu-right" style="overflow-y:auto;">
                        <div class="dropdown-header">Wards List</div>
                        <div class="dropdown-list-content dropdown-list-message">
                            <?php if(!empty($userData->wards_list)) { ?>
                                <?php foreach($userData->wards_list as $ward) {
                                    $ward = (object) $ward;
                                    $isThis = (bool) ($session->student_id === $ward->student_guid);
                                    ?>
                                    <a href="javacript:void(0);" onclick="return set_default_Student('<?= $ward->student_guid ?>')"; class="<?= $isThis ? "bg-success text-white" : ""; ?> dropdown-item anchor">
                                        <span class="dropdown-item-avatar text-white">
                                            <img alt="image" src="<?= $baseUrl ?><?= $ward->image ?>" class="rounded-circle">
                                        </span>
                                        <span class="dropdown-item-desc">
                                            <span style="font-size: 14px;" class="message-user <?= $isThis ? "text-white" : ""; ?>"><?= $ward->name ?></span>
                                            <span style="font-size: 14px;" class="time  <?= $isThis ? "text-white" : ""; ?>"><strong><?= $ward->unique_id ?></strong></span>
                                            <span style="font-size: 14px;" class="time text-primary"><?= $ward->class_name ?></span>
                                        </span>
                                    </a>
                                <?php } ?>
                            <?php } else { ?>
                            <a href="#" class="anchor dropdown-item">
                                <span class="font-italic">Sorry! You currently do not have any ward in the school.</span>
                            </a>
                            <?php } ?>

                        </div>
                    </div>
                </li>
                <?php } ?>
                <?php if($isActiveAccount) { ?>
                <li class="dropdown dropdown-list-toggle">
                    <a href="#" data-toggle="dropdown" title="Email Messages" class="nav-link nav-link-lg message-toggle" data-notification="message"><i class="far fa-envelope"></i></a>
                    <div class="dropdown-menu dropdown-list dropdown-menu-right">
                    <div class="dropdown-header">Messages
                        <div class="float-right">
                        <a href="#" data-function="mark_as_read" data-item="messages">Mark All As Read</a>
                        </div>
                    </div>
                    <div id="messages_list" data-user_id="<?= $loggedUserId ?>" class="dropdown-list-content dropdown-list-message">
                        
                        <!-- <a href="#" class="dropdown-item">
                            <span class="dropdown-item-avatar text-white">
                                <img alt="image" src="assets/img/users/user-5.png" class="rounded-circle">
                            </span>
                            <span class="dropdown-item-desc">
                                <span class="message-user">Jacob Ryan</span>
                                <span class="time messege-text">Your payment invoice is generated.</span>
                                <span class="time text-primary">12 Min Ago</span>
                            </span>
                        </a> -->

                    </div>
                    <div class="dropdown-footer text-center">
                        <a href="<?= $baseUrl ?>chat">View All <i class="fas fa-chevron-right"></i></a>
                    </div>
                    </div>
                </li>
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
                <?php if($accessObject->hasAccess("support", "settings")) { ?>
                <li class="dropdown dropdown-list-toggle">
                    <a title="Support Tickets List" href="<?= $baseUrl ?>support" class="nav-link nav-link-lg"><i class="fa fa-phone-volume"></i> Support</a>
                </li>
                <?php } ?>
                <?php } ?>
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown"
                        class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                        <img alt="image" src="<?= $baseUrl ?><?= $userData->image ?>" class="user-img-radious-style">
                        <span class="d-sm-none d-lg-inline-block"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">Hello <?= !empty($userData->name) ? $userData->name : $clientData->client_name ?></div>
                    <?php if($isActiveAccount) { ?>
                        <a href="<?= $baseUrl ?>profile" class="dropdown-item has-icon">
                            <i class="far fa-user"></i> Profile
                        </a>
                        <?php if($accessObject->hasAccess("activities", "settings")) { ?>
                        <a href="<?= $baseUrl ?>timeline" class="dropdown-item has-icon">
                            <i class="fas fa-align-left"></i> Activities
                        </a>
                        <?php } ?>
                        <?php if($accessObject->hasAccess("login_history", "settings")) { ?>
                        <a href="<?= $baseUrl ?>login-history" class="dropdown-item has-icon">
                            <i class="fas fa-lock"></i> Login History
                        </a>
                        <?php } ?>
                        <?php if($accessObject->hasAccess("manage", "settings") && $isSchool) { ?>
                        <a href="<?= $baseUrl ?>settings" class="dropdown-item has-icon">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <?php } ?>
                        <?php if($changePassword) { ?>
                        <a href="<?= $baseUrl ?>password_manager" class="dropdown-item has-icon">
                            <i class="fas fa-key"></i> Password Manager
                        </a>
                        <?php } ?>
                        <?php if($accessObject->hasAccess("close", "settings") && $isSchool) { ?>
                        <a href="<?= $baseUrl ?>manager" class="dropdown-item has-icon">
                            <i class="fas fa-bolt"></i> <span class="mr-3">Manage Calendar</span> <?= $endPermission && $defaultUser->appPrefs->termEnded ? '<span class="notification beep"></span>' : null ?>
                        </a>
                        <?php } ?>
                        <a title="Knowledge Base" href="<?= $baseUrl ?>knowledgebase" class="dropdown-item has-icon">
                            <i class="fa fa-book-open"></i> Knowledge Base
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
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    
                    <div class="sidebar-brand">
                        <a href="<?= $baseUrl ?>">
                            <img alt="image" src="<?= $baseUrl ?>assets/img/logo.png" class="header-logo" />
                            <span class="logo-name"><?= $appName ?></span>
                        </a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Main</li>
                        <li><a href="<?= $baseUrl ?>dashboard" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
                        <?php 
                        // set the menu function 
                        $menu_function = $isSupport ? "support_menu" : $userData->user_type."_menu";
                        
                        // confirm that the function exists
                        if(function_exists($menu_function)) {
                            // load the function
                            $menu_function();
                        }
                        ?>
                        <?php if($isSchool) { ?>
                        <li class="mb-5"><a href="<?= $baseUrl ?>chat" class="nav-link"><i class="fas fa-envelope-open-text"></i><span>Live Chat</span></a></li>
                        <?php } ?>
                    </ul>
                </aside>
            </div>
